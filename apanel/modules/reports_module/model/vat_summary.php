<?php
class vat_summary extends wc_model {

	public function getSales($range) 
	{
		$date 		= explode(' - ',$range);
		$startdate 	= date('Y-m-d',strtotime($date[0]));
		$enddate 	= date('Y-m-d',strtotime($date[1]));
		
		$result 	= $this->getRecords($startdate, $enddate,"Output");
		
		return $this->buildStructure(array($result),"transactions");
	}

	public function getPurchase($range) 
	{
		$date 		= explode(' - ',$range);
		$startdate 	= date('Y-m-d',strtotime($date[0]));
		$enddate 	= date('Y-m-d',strtotime($date[1]));
		
		$result 	= $this->getRecords($startdate, $enddate,"Input");

		return $this->buildStructure(array($result),"transactions");
	}

	public function getSummary($range) 
	{
		$date 		= explode(' - ',$range);
		$startdate 	= date('Y-m-d',strtotime($date[0]));
		$enddate 	= date('Y-m-d',strtotime($date[1]));
		
		$result 	= $this->getRecords($startdate, $enddate,"summary");

		return $this->buildStructure(array($result),"summary");
	}

	public function getRecords($start, $end, $type) {
		
		$filter 			= " AND (bt.transactiondate >= '$start' AND bt.transactiondate <= '$end') ";
		//$nature_condition 	= ($type == 'Input') ? " AND LOWER(ca.accountnature) = 'debit' " : " AND LOWER(ca.accountnature) = 'credit' ";
		if($type != 'summary'){
			$result =  $this->db->setTable('balance_table as bt')
							->setFields("ca.accountname as accountname, bt.voucherno as voucher, part.partnername as partner, bt.transtype as transtype, 
										bt.transactiondate as transactiondate, part.tinno as tin, SUM(bt.debit) as debit, SUM(bt.credit) as credit, part.address1 as address, bt.voucherno")
							->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
							->leftJoin('partners as part ON part.partnercode = bt.partnercode AND part.companycode = bt.companycode ')
							->setWhere("bt.transtype IN('AR','AP','PV','DV','RV','JV') AND ca.accountname LIKE '%$type%' $filter ")
							->setGroupBy("bt.voucherno")
							->setOrderBy("bt.accountcode, part.partnername ASC")
							->runPagination();
		}else{
			$result =  $this->db->setTable('balance_table as bt')
							->setFields("ca.accountname as accountname, SUM(bt.debit) as debit, SUM(bt.credit) as credit, bt.transtype as transtype, bt.voucherno")
							->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
							->leftJoin('partners as part ON part.partnercode = bt.partnercode AND part.companycode = bt.companycode ')
							->setWhere("bt.transtype IN('AR','AP','PV','DV','RV','PV','JV') AND (ca.accountname LIKE '%Output%' OR ca.accountname LIKE '%Input%') $filter")
							->setGroupBy("bt.accountcode")
							->setOrderBy("ca.accountname DESC")
							->runPagination();
		}
		

		return $result;
	}

	private function buildStructure($data, $type) {
		$maindata 	= $data[0]->result;
		$col 		= array();
		
		$i = 0;
		if($type == 'transactions'){
			foreach ($maindata as $key => $accounts) {
				
				$total = 0;
				$accountname 	= $accounts->accountname;
				$voucher 		= $accounts->voucher;
				$partner 		= $accounts->partner;
				$transtype 		= $accounts->transtype;
				$tin 			= $accounts->tin;
				$debit 			= $accounts->debit;
				$credit 		= $accounts->credit;
				$address 		= $accounts->address;
				$voucherno 		= $accounts->voucherno;

				if($transtype == 'AR'){
					$table 		= 'ar_details';
				}else if($transtype == 'AP'){
					$table 		= 'ap_details';
				}else if($transtype == 'PV' || $transtype == 'DV'){
					$table 		= 'pv_details';
				}else if($transtype == 'JV'){
					$table 		= 'journaldetails';
				}

				$gross 			= $this->getValue($table, array('SUM(debit) amount'), " voucherno = '$voucherno' ");
				$grossamount 	= ($gross) ? $gross[0]->amount : 0;
				$vatamount 		= ($credit > $debit) ? $credit - $debit : $debit - $credit; 
				
				$col[$i]['account'] 	= $accountname;
				$col[$i]['voucher'] 	= $voucher;
				$col[$i]['partner'] 	= $partner;
				$col[$i]['address'] 	= $address;
				$col[$i]['transtype'] 	= $transtype;
				$col[$i]['tin'] 		= $tin;
				$col[$i]['amount']		= $grossamount;
				$col[$i]['vatamount']	= $vatamount;
				$i++;
			}
		}else{
			foreach ($maindata as $key => $accounts) {
				
				$total = 0;
				$accountname 	= $accounts->accountname;
				$debit 			= $accounts->debit;
				$credit 		= $accounts->credit;
				$voucherno 		= $accounts->voucherno;
				$transtype 		= $accounts->transtype;

				if($transtype == 'AR'){
					$table 		= 'ar_details';
				}else if($transtype == 'AP'){
					$table 		= 'ap_details';
				}else if($transtype == 'PV' || $transtype == 'DV'){
					$table 		= 'pv_details';
				}else if($transtype == 'JV'){
					$table 		= 'journaldetails';
				}

				$gross 			= $this->getValue($table, array('SUM(debit) amount'), " voucherno = '$voucherno' ");
				$grossamount 	= ($gross) ? $gross[0]->amount : 0;
				$vatamount 		= ($debit > $credit) ? $debit : $credit; 
				
				$col[$i]['account'] 	= $accountname;
				$col[$i]['amount']		= $vatamount;
				$i++;
			}
		}
		
		$result 				= new stdClass();
		$result->rows  			= $col;
		$result->pagination  	= $data[0]->pagination;
		
		return $result;
	}

	public function getValue($table, $cols = array(), $cond, $orderby = "",$default = true)
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby)
					->runSelect($default)
					->getResult();
					
		return $result;
	}

}