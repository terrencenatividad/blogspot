<?php
class credit_memo_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}
	
	public function saveJournalVoucher($data, $data2) {
		
		$debit	= $this->removeComma($data2['debit']);
		$total	= 0;
		foreach ($debit as $entry) {
			$total += $entry;
		}
		// $debit						= $this->removeComma($data2['debit']);
		// $total						= 0;
		// $convertedamount			= $data['exchangerate']	= '1.00';
		$data['transtype']			= 'CM';
		$data['stat']				= 'posted';
		$data['period']				= date("n", strtotime($data['transactiondate']));
		$data['fiscalyear']			= date("Y", strtotime($data['transactiondate']));
		$data['currencycode']		= 'PHP';
		$exchangerate				= '1.00';
		$data['amount']				= $total;
		$data['convertedamount']	= $total * $exchangerate;

		$result = $this->db->setTable('journalvoucher')
							->setValues($data)
							->runInsert();
		// if ($result && $data2['debit'] != '') {
		// 	$result = $this->updateJournalVoucherDetails($data2, $data['voucherno']);
		// }
		if ($result) {
			$result = $this->updateJournalVoucherDetails($data2, $data['voucherno']);
		}
		return $result;
	}

	public function updateJournalVoucher($data, $data2, $voucherno, $log = false) {
		$debit	= $this->removeComma($data2['debit']);
		$sr_amount	= $this->removeComma($data['sr_amount']);
		$total	= 0;
		foreach ($debit as $entry) {
			$total += $entry;
		}
		$data['stat']				= 'posted';
		$data['period']				= date("n", strtotime($data['transactiondate']));
		$data['fiscalyear']			= date("Y", strtotime($data['transactiondate']));
		$data['amount']		= $total;
		$data['sr_amount']	= $sr_amount;
		$data['transtype']	= 'CM';
		
		$result = $this->db->setTable('journalvoucher')
							->setValues($data)
							->setWhere("voucherno = '$voucherno'")
							->setLimit(1)
							->runUpdate();
		if (isset($data['voucherno'])) {
			$voucherno =  $data['voucherno'];
		}
		if ($result) {
			if ($result && $log) {
				$this->log->saveActivity("$log Credit Memo [$voucherno}");
			}
			$result = $this->updateJournalVoucherDetails($data2, $voucherno);
		}
		return $result;
	}

	public function updateJournalVoucherDetails($data, $voucherno) {
		$result = $this->db->setTable('journaldetails')
							->setWhere("voucherno = '$voucherno'")
							->runDelete();
		
		$linenum = array();
		foreach ($data['debit'] as $key => $value) {
			$linenum[] = $key + 1;
		}
		if ($result) {
			$data['voucherno']			= $voucherno;
			$data['transtype']			= 'CM';
			$data['stat']				= 'posted';
			$data['debit']				= $this->removeComma($data['debit']);
			$data['credit']				= $this->removeComma($data['credit']);
			$data['converteddebit']		= $this->removeComma($data['debit']);
			$data['convertedcredit']	= $this->removeComma($data['credit']);
			$data['linenum']			= $linenum;
			$result = $this->db->setTable('journaldetails')
						->setValuesFromPost($data)
						->runInsert();
		}
		return $result;
	}

	public function deleteJournalVouchers($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('journalvoucher')
							->setValues(array('stat'=>'cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			if ($result) {
				$log_id = implode(', ', $data);
				$this->log->saveActivity("Delete Credit Memo [$log_id]");
			}
			$result = $this->deleteJournalVouchersDetails($data);
		}

		return $result;
	}

	public function deleteJournalVouchersDetails($data) {
		$ids 	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('journaldetails')
							->setValues(array('stat'=>'cancelled'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
							
		return $result;
	}

	public function reverseEntries($delete_id)
	{
		$voucherno = "'" . implode("','", $delete_id) . "'";
		$count = $this->db->setTable('journaldetails')
				->setFields('*')
				->setWhere("voucherno IN($voucherno)")
				->runSelect()
				->getResult();

		if(!empty($count))
		{
			$ctr = count($count) + 1;
			for($i = 0; $i < count($count); $i++)
			{
				$insert_info['voucherno']			= $count[$i]->voucherno;
				$insert_info['checkno']				= $count[$i]->checkno;
				$insert_info['transtype']			= $count[$i]->transtype;
				$insert_info['linenum']				= $ctr;
				$insert_info['slcode']				= $count[$i]->slcode;
				$insert_info['source']				= $count[$i]->source;
				$insert_info['costcentercode']		= $count[$i]->costcentercode;
				$insert_info['accountcode']			= $count[$i]->accountcode;
				$insert_info['debit']				= $count[$i]->credit;
				$insert_info['credit']				= $count[$i]->debit;
				$insert_info['currencycode']		= $count[$i]->currencycode;
				$insert_info['exchangerate']		= $count[$i]->exchangerate;
				$insert_info['converteddebit']		= $count[$i]->convertedcredit;
				$insert_info['convertedcredit']		= $count[$i]->converteddebit;
				$insert_info['taxcode']				= $count[$i]->taxcode;
				$insert_info['taxacctflg']			= $count[$i]->taxacctflg;
				$insert_info['taxline']				= $count[$i]->taxline;
				$insert_info['vatflg']				= $count[$i]->vatflg;
				$insert_info['detailparticulars']	= $count[$i]->detailparticulars;
				$insert_info['stat']				= $count[$i]->stat;

				$result = $this->db->setTable('journaldetails')
									->setValues($insert_info)
									->runInsert();
				$ctr++;
				var_dump($count);
			}
	}
	return $count;
		
}

	public function getJournalVoucherById($fields, $voucherno) {
		return $this->db->setTable('journalvoucher')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();
	}

	public function getJournalVoucherPagination($fields, $search, $typeid, $classid, $datefilter ,$partner, $limit, $sort) {
		$condition = "transtype = 'CM' and (jv.stat = 'posted' or jv.stat = 'cancelled') ";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno','transactiondate','referenceno','amount','partner'));
		}
		if($partner && $partner != 'none'){
			$condition .= "AND partner = '$partner' ";
		}
		// if ($startdate && $enddate) {
		// 	$condition .= " AND transactiondate >= '$startdate' AND transactiondate <= '$enddate'";
		// }
		$datefilter	= explode('-', $datefilter);
		foreach ($datefilter as $key => $date) {
			$datefilter[$key] = $this->date->dateDbFormat($date);
		}

		if (isset($datefilter[1])) {
			$condition .= " AND transactiondate >= '{$datefilter[0]}' AND transactiondate <= '{$datefilter[1]}'";
 		}
		$result = $this->db->setTable("journalvoucher jv")
						->setFields("pt.partnername, transactiondate,partner ,voucherno, referenceno, FORMAT(amount, 2) amount, jv.stat as stat, jv.source")
						->leftJoin("partners pt ON pt.partnercode = jv.partner")
						->setWhere($condition)
						->setOrderBy($sort)
						->runPagination();
						
		return $result;
	}

	public function getJournalVoucherDetails($fields, $voucherno) {
		$result = $this->db->setTable('journaldetails')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->setOrderBy('linenum')
							->runSelect()
							->getResult();
		return $result;
	}

	public function getProformaList($data)
	{
		$proformacode = $data['proformacode'];
		if($data['ajax_task'] == 'ajax_edit'){
			$cond = "transactiontype = 'Credit Memo' AND stat = 'active' OR proformacode = '$proformacode'";
		}else{
			$cond = "transactiontype = 'Credit Memo' AND stat = 'active'";
		}
		$result = $this->db->setTable('proforma')
					->setFields("proformacode ind, proformadesc val, stat stat")
					->setOrderBy("proformadesc")
					->setWhere($cond)
					->runSelect()
					->getResult();
		
		return $result;
	}

	public function getVendorList() {
		$result = $this->db->setTable('partners')
							->setFields("partnercode ind, partnername val")
							->setOrderBy("partnername")
							->setWhere("stat = 'active'")
							->runSelect()
							->getResult();
		return $result;
	}

	public function getChartOfAccountList() {
		$result = $this->db->setTable('chartaccount')
							->setFields("id ind, CONCAT(segment5, ' - ', accountname) val")
							->setWhere("stat = 'active'")
							->runSelect()
							->getResult();
		return $result;
	}

	public function getJournalVoucherTypeList() {
		return $this->db->setTable('wc_option')
						->setFields('code ind, value val')
						->setWhere("type = 'item_type'")
						->runSelect(false)
						->getResult();
	}

	public function getDocumentInfo($voucherno) {
		$result = $this->db->setTable('journalvoucher jv')
							->setFields('voucherno, transactiondate documentdate, amount, referenceno, remarks, partner')
							->innerJoin('partners pt on pt.partnercode = jv.partner')
							->setWhere("voucherno = '$voucherno' AND jv.stat = 'posted'")
							->setLimit(1)
							->runSelect()
							->getRow();
							
		return $result;
	}

	public function getDocumentDetails($voucherno) {
		$result = $this->db->setTable('journaldetails jd')
							->innerJoin('chartaccount ca ON jd.accountcode = ca.id AND ca.companycode = jd.companycode')
							->setFields("CONCAT(segment5, ' - ',accountname) accountname, debit, credit")
							->setWhere("voucherno = '$voucherno' AND stat = 'posted'")
							->runSelect()
							->getResult();
		return $result;
	}

	public function getProforma($proformacode) {
		$result = $this->db->setTable('proforma_details')
							->setFields("accountcodeid accountcode, accountname detailparticulars, '0.00' debit, '0.00' credit")
							->setWhere("proformacode = '$proformacode' AND stat = 'active'")
							->runSelect()
							->getResult();

		return $result;
	}

	public function getVendor($voucherno) {
		$result = $this->db->setTable('journalvoucher jv')
							->innerJoin('partners pt ON (jv.partner = pt.partnercode)')
							->setFields("partnername")
							->setWhere("voucherno = '$voucherno' ")
							->runSelect()
							->getResult();
							// echo $this->db->getQuery();
		return $result;
	}

	public function insertVendor($data) {
		$data["stat"]     	   = "active";
		$data['partnertype']   = "supplier";
		$result = $this->db->setTable('partners')
				->setValues($data)
				->runInsert();
				//echo $this->db->getQuery();
		return $result;
	}

	private function removeComma($data) {
		if (is_array($data)) {
			$temp = array();
			foreach ($data as $val) {
				$temp[] = $this->removeComma($val);
			}
			return $temp;
		} else {
			return str_replace(',', '', $data);
		}
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function getSalesOrderPagination($customer = '', $search = '') {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('main.voucherno', 'main.source_no','main.netamount','main.partner'));
		}
		if ($customer != '') {
			 $condition .= " AND main.partner = '$customer'";
		}
		$subquery = "SELECT companycode,voucherno, source_no, transactiondate, netamount , customer as partner FROM salesreturn UNION SELECT companycode,voucherno, source_no, transactiondate, netamount, vendor as partner FROM purchasereturn";

		$result		= $this->db->setTable("($subquery) main")
					->setFields('main.companycode,main.voucherno,main.source_no, main.transactiondate, main.netamount,main.partner')
					->leftJoin("journalvoucher jv ON main.voucherno = jv.referenceno AND main.companycode = jv.companycode AND jv.stat NOT IN ('cancelled','open','deleted') ")
					->setWhere("jv.voucherno IS NULL" .$condition)
					->runPagination();

		return $result;
	}

	public function getReturnDetails($sr_no){

		$sub = "SELECT companycode,itemcode, detailparticular, issueqty, issueuom, unitprice, amount
				FROM salesreturn_details
				WHERE voucherno = '$sr_no'
				UNION ALL 
				SELECT companycode,itemcode, detailparticular, receiptqty, receiptuom, unitprice, amount
				FROM purchasereturn_details
				WHERE voucherno = '$sr_no'";

		$result = 	$this->db->setTable("($sub) main")
					->setFields('main.itemcode,main.detailparticular,main.issueqty,main.issueuom,main.unitprice,main.amount')
					->runSelect()
					->getResult();
		return $result;

	}	

	public function getReturnHeader($sr_no){

		$sub = "SELECT companycode,voucherno, transactiondate, source_no, customer
				FROM salesreturn
				WHERE voucherno = '$sr_no'
				UNION ALL 
				SELECT companycode,voucherno, transactiondate, source_no, vendor
				FROM purchasereturn
				WHERE voucherno = '$sr_no'";

		$result = 	$this->db->setTable("($sub) main")
					->setFields('main.companycode,voucherno, transactiondate, source_no, customer, partnername')
					->leftJoin('partners ON customer = partnercode')
					->runSelect()
					->getResult();
		return $result;

	}	

}