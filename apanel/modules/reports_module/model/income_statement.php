<?php
class income_statement extends wc_model {

	// public function getMonthly($year, $month) {
	// 	$startdate 	= date('Y-m-1',strtotime($month.' '.$year));
	// 	$enddate 	= date('Y-m-t',strtotime($month.' '.$year));
		
	// 	$result 	= $this->getRecords($startdate, $enddate);
	
	// 	return $this->buildStructure(array($result));
	// }

	public function getMonthly($year = false) {
		$year = ($year) ? $year : date('Y');
		$monthly1	= $this->getRecords("{$year}-01-01", $this->getMonthEnd("{$year}-01-1"));
		$monthly2	= $this->getRecords("{$year}-02-01", $this->getMonthEnd("{$year}-02-1"));
		$monthly3	= $this->getRecords("{$year}-03-01", $this->getMonthEnd("{$year}-03-1"));
		$monthly4	= $this->getRecords("{$year}-04-01", $this->getMonthEnd("{$year}-04-1"));
		$monthly5	= $this->getRecords("{$year}-05-01", $this->getMonthEnd("{$year}-05-1"));
		$monthly6	= $this->getRecords("{$year}-06-01", $this->getMonthEnd("{$year}-06-1"));
		$monthly7	= $this->getRecords("{$year}-07-01", $this->getMonthEnd("{$year}-07-1"));
		$monthly8	= $this->getRecords("{$year}-08-01", $this->getMonthEnd("{$year}-08-1"));
		$monthly9	= $this->getRecords("{$year}-09-01", $this->getMonthEnd("{$year}-09-1"));
		$monthly10	= $this->getRecords("{$year}-10-01", $this->getMonthEnd("{$year}-10-1"));
		$monthly11	= $this->getRecords("{$year}-11-01", $this->getMonthEnd("{$year}-11-1"));
		$monthly12	= $this->getRecords("{$year}-12-01", $this->getMonthEnd("{$year}-12-1"));

		$return = $this->buildStructure(array($monthly1,$monthly2,$monthly3,$monthly4,$monthly5,$monthly6,$monthly7,$monthly8,$monthly9,$monthly10,$monthly11,$monthly12));
		// var_dump($return);
		return $return;
	}

	public function getQuarterly($year = false) {
		$year 		= ($year) ? $year : date('Y');
		$quarter1 	= $this->getRecords("{$year}-01-01", "{$year}-03-31");
		$quarter2 	= $this->getRecords("{$year}-04-01", "{$year}-06-31");
		$quarter3 	= $this->getRecords("{$year}-07-01", "{$year}-09-30");
		$quarter4 	= $this->getRecords("{$year}-10-01", "{$year}-12-31");
		
		return $this->buildStructure(array($quarter1, $quarter2, $quarter3, $quarter4));
	}

	public function getYearly($year = false) {
		$year = ($year) ? $year : date('Y');
		$year1 = $this->getRecords("{$year}-01-01", "{$year}-12-31");
		$year2 = $this->getRecords(($year - 1) . "-01-01", ($year - 1) . "-12-31");
		return $this->buildStructure(array($year1, $year2));
	}

	public function getRecords($start, $end) {
		
		$filter = " AND (bt.transactiondate >= '$start' AND bt.transactiondate <= '$end') ";
		$result =  $this->db->setTable('balance_table as bt')
							->setFields("ca.accountclasscode as accountclasscode, bt.accountcode as accountcode, ca.accountname as accountname, bt.transactiondate as transactiondate, SUM(bt.debit) as debit, SUM(bt.credit) as credit, ca.parentaccountcode as parent, ca.accountnature as accountnature")
							->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
							->setWhere("ca.fspresentation = 'IS' AND ca.accountclasscode IN('REV','REVENU') $filter")
							->setGroupBy('bt.accountcode')
							->setOrderBy("bt.accountcode")
							->runSelect()
							->getResult();

		$result1 =  $this->db->setTable('balance_table bt')
							->setFields("ca.accountclasscode as accountclasscode, bt.accountcode as accountcode,ca.accountname as accountname, bt.transactiondate as transactiondate, SUM(bt.debit) as debit, SUM(bt.credit) as credit, ca.parentaccountcode as parent, ca.accountnature as accountnature")
							->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
							->setWhere("ca.fspresentation = 'IS' AND ca.accountclasscode IN('OTHINC','OTRINC') $filter")
							->setGroupBy('bt.accountcode')
							->setOrderBy("bt.accountcode")
							->runSelect()
							->getResult();

		$result2 =  $this->db->setTable('balance_table bt')
							->setFields("ca.accountclasscode as accountclasscode, bt.accountcode as accountcode,ca.accountname as accountname, bt.transactiondate as transactiondate, SUM(bt.debit) as debit, SUM(bt.credit) as credit, ca.parentaccountcode as parent, ca.accountnature as accountnature")
							->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
							->setWhere("ca.fspresentation = 'IS' AND ca.accountclasscode IN('COST','COSTSA') $filter")
							->setGroupBy('bt.accountcode')
							->setOrderBy("bt.accountcode")
							->runSelect()
							->getResult();

		$result3 =  $this->db->setTable('balance_table bt')
							->setFields("ca.accountclasscode as accountclasscode, bt.accountcode as accountcode,ca.accountname as accountname, bt.transactiondate as transactiondate, SUM(bt.debit) as debit, SUM(bt.credit) as credit, ca.parentaccountcode as parent, ca.accountnature as accountnature")
							->leftJoin('chartaccount as ca ON ca.id = bt.accountcode AND ca.companycode = bt.companycode ')
							->setWhere("ca.fspresentation = 'IS' AND ca.accountclasscode IN('EXP','OPSEXP','OTREXP') $filter")
							->setGroupBy('bt.accountcode')
							->setOrderBy("bt.accountcode")
							->runSelect()
							->getResult();
							
							
		return array_merge($result, $result1, $result2, $result3);
	}

	public function getYearList() {
		$year_list = array();
		$year_now = date('Y');
		for ($year = $year_now; $year > $year_now - 5; $year--) {
			$year_list[$year] = $year;
		}
		return $year_list;
	}

	public function getMonthList() {
		$month_list = array();
		$months = 12;
		for($month=1; $month<=$months; ++$month){
			 $month_list[date('F', mktime(0, 0, 0, $month, 1))] = date('F', mktime(0, 0, 0, $month, 1));
		}
		return $month_list;
	}

	private function getMonthEnd($date) {
		return date("Y-m-t", strtotime($date));
	}

	private function buildStructure($data) {
		$y = array();
		$revenue_array 	= array('REV','REVENU');
		$otherinc_array = array('OTHINC','OTRINC');
		$cost_array 	= array('COST','COSTSA');
		$exp_array 		= array('EXP','OPSEXP','OTREXP');
		$inc_array 		= array('INCTAX');
		$maindata 		= array();

		$data_key = 0;
	
		for($i=0;$i<13;$i++){
			if(!empty($data[$i])){
				$maindata 	= $data[$i];
			}
		}
	
		// if(!empty($data[0])){
		// 	$maindata 	= $data[0];
		// }
		// if(!empty($data[1])){
		// 	$maindata 	= $data[1];
		// }
		// if(!empty($data[2])){
		// 	$maindata 	= $data[2];
		// }
		// if(!empty($data[3])){
		// 	$maindata 	= $data[3];
		// }
		

		if($maindata){

			foreach ($maindata as $key => $accounts) {
				$col 				= array();
				$total 				= 0;
				$accountcode		= $accounts->accountcode;
				$accountname 		= $accounts->accountname;
				$accountclasscode 	= $accounts->accountclasscode;
				$accountnature 		= $accounts->accountnature;
				$parent 			= $accounts->parent;
				$parentnature 		= $this->getValue("chartaccount", array("accountnature"), " segment5 = '$parent' ");
				$parentnature 		= ($parentnature) ? $parentnature[0]->accountnature : $accountnature;
				$accounttype 		= '';
				$accountclass 		= '';
				
				foreach ($data as $x) {
					if(!empty($x[$key])){
						$account 	= $x[$key];
						$tot 		= ($account->debit > $account->credit) ? $account->debit - $account->credit : $account->credit - $account->debit;
						if($accountnature == $parentnature)
						{
							$total 		+= $tot;
							$col[] 		= $tot;
						}else{
							$total 		-= $tot;
							$col[] 		= -$tot;
						}
					}else{
						$total 		+= 0;
						$col[] 		= 0;
					}
				}

				if (in_array($accountclasscode, $revenue_array)) {
					$accounttype = 'Revenue';
				} else if (in_array($accountclasscode, $otherinc_array)) {
					$accounttype = 'Other Income';
				} else if (in_array($accountclasscode, $cost_array)) {
					$accounttype = 'Cost';
				} else if (in_array($accountclasscode, $exp_array)) {
					$accounttype = 'Expense';
				} else if (in_array($accountclasscode, $inc_array)) {
					$accounttype = 'Income Tax';
				}else {
					$total = 0;
				}

				if ($total !== 0 && ! empty($accounttype)) {
					$y[$accounttype][$accountname]	= $col;
				}
			}
		}
		return $y;
	}

	private function getValue($table, $cols = array(), $cond = "", $orderby = "", $bool = "")
	{
		$result = $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby)
					->runSelect($bool)
					->getResult();

		return $result;
	}

}