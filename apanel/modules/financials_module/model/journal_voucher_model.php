<?php
class journal_voucher_model extends wc_model {

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
		$exchangerate				= '1.00';
		$data['transtype']			= 'JV';
		$data['stat']				= 'posted';
		$data['period']				= date("n", strtotime($data['transactiondate']));
		$data['fiscalyear']			= date("Y", strtotime($data['transactiondate']));
		$data['currencycode']		= 'PHP';
		$data['exchangerate']		= $exchangerate;
		$data['amount']				= $total;
		$data['convertedamount']	= $total * $exchangerate;

		$result = $this->db->setTable('journalvoucher')
							->setValues($data)
							->runInsert();

		if ($result) {
			$result = $this->updateJournalVoucherDetails($data2, $data['voucherno']);
		}

		return $result;
	}

	public function updateJournalVoucher($data, $data2, $voucherno, $log = false) {
		$debit	= $this->removeComma($data2['debit']);
		$total	= 0;
		foreach ($debit as $entry) {
			$total += $entry;
		}
		$data['stat']		= 'posted';
		$data['period']		= date("n", strtotime($data['transactiondate']));
		$data['fiscalyear']	= date("Y", strtotime($data['transactiondate']));
		$data['amount']		= $total;
		$data['transtype']	= 'JV';
		
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
				$this->log->saveActivity("$log Journal Voucher [$voucherno}");
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
			$data['transtype']			= 'JV';
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
				$this->log->saveActivity("Delete Journal Voucher [$log_id]");
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

	public function getJournalVoucherBySourceNo($fields, $voucherno) {
		$ids	= "'" . implode("','", $voucherno) . "'";
		return $this->db->setTable('journalvoucher')
						->setFields($fields)
						->setWhere("sourceno IN ($ids)")
						->runSelect()
						->getResult();
	}

	public function getJournalVoucherPagination($fields, $search, $sort, $datefilter, $source, $filter) {
		$sort = ($sort) ? $sort : 'transactiondate desc';
		$condition = "transtype = 'JV' and (stat = 'posted' or stat = 'cancelled')";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno','transactiondate','referenceno','amount','source'));
		}
		$datefilter	= explode('-', $datefilter);
		foreach ($datefilter as $key => $date) {
			$datefilter[$key] = $this->date->dateDbFormat($date);
		}
		if (isset($datefilter[1])) {
			$condition .= " AND transactiondate >= '{$datefilter[0]}' AND transactiondate <= '{$datefilter[1]}'";
		}
		if ($source && $source != "none") {
			if($source=='closed'){
				$condition .= " AND source = 'closing'";
 			} else if($source=='manual'){
				$condition .= " AND source = ''";
			} else if($source=='import'){
				$condition .= " AND source = 'import'";
			}
		}
		if ($filter && $filter != "all") {
			if($filter=='cancelled'){
				$condition .= " AND stat = 'cancelled'";
 			} 
		}
		$result = $this->db->setTable("journalvoucher")
						->setFields("transactiondate, voucherno, referenceno, amount, source as checker, stat")
						->setWhere($condition)
						->setOrderBy($sort)
						->runPagination();
						
		return $result;
	}

	public function getJournalVoucherDetails($fields, $voucherno,$status) {
		if($status != 'cancelled'){
			$fields = array('voucherno','accountcode','detailparticulars','debit','credit','linenum');
			$groupby = '';
		}else{
			$fields = array('voucherno','accountcode','detailparticulars','debit','credit','linenum');
			$groupby = '';
		}

		$result = $this->db->setTable('journaldetails')
							->setFields($fields)
							->setWhere("voucherno = '$voucherno'")
							->setGroupBy($groupby)
							->setOrderBy('linenum')
							->runSelect()
							->getResult();
		return $result;
	}

	public function getProformaList($data)
	{
		$proformacode = $data['proformacode'];
		if($data['ajax_task'] == 'ajax_edit'){
			$cond = "transactiontype = 'Journal Voucher' AND stat = 'active' OR proformacode = '$proformacode'";
		}else{
			$cond = "transactiontype = 'Journal Voucher' AND stat = 'active'";
		}
		$result = $this->db->setTable('proforma')
					->setFields("proformacode ind, proformadesc val, stat stat")
					->setOrderBy("proformadesc")
					->setWhere($cond)
					->runSelect()
					->getResult();
		
		return $result;
	}

	public function getChartOfAccountList() {
		$result = $this->db->setTable('chartaccount coa')
							->setFields("coa.id ind, CONCAT(coa.segment5, ' - ', coa.accountname) val")
							->leftJoin('chartaccount coa2 ON coa2.parentaccountcode = coa.id')
							->setWhere("coa.accounttype != '' AND coa.stat = 'active'")
							->setOrderBy('coa.segment5, coa2.segment5')
							->runSelect()
							->getResult();
		return $result;
	}

	public function getEditChartOfAccountList($coa_array) {
		$condition = ($coa_array) ? " OR id IN ('".implode("','",$coa_array)."')" : "";	
		$result = $this->db->setTable('chartaccount')
							->setFields("id ind, CONCAT(segment5, ' - ', accountname) val")
							->setWhere("stat = 'active' $condition")
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
		$result = $this->db->setTable('journalvoucher')
							->setFields('voucherno, transactiondate documentdate, amount, referenceno, remarks')
							->setWhere("voucherno = '$voucherno' AND stat = 'posted'")
							->setLimit(1)
							->runSelect()
							->getRow();
		return $result;
	}

	public function getDocumentDetails($voucherno) {
		$result = $this->db->setTable('journaldetails jd')
							->innerJoin('chartaccount ca ON jd.accountcode = ca.id AND ca.companycode = jd.companycode')
							->setFields("CONCAT(segment5, ' - ',accountname) accountname, debit, credit")
							->setWhere("voucherno = '$voucherno' AND jd.stat = 'posted'")
							->runSelect()
							->getResult();
		return $result;
	}

	public function getProforma($proformacode) {
		$result = $this->db->setTable('proforma_details')
							->setFields("accountcodeid accountcode, accountname detailparticulars, '0.00' debit, '0.00' credit")
							->setWhere("proformacode = '$proformacode'")
							->runSelect()
							->getResult();

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

	public function getImportList(){
		return	$this->db->setTable('items')
						->setFields('items.itemcode as itemcode, items.itemname as name, w.description as warehouse')
						->leftJoin("warehouse w ON w.companycode = items.companycode")
						->leftJoin("invfile inv ON inv.itemcode = items.itemcode AND w.warehousecode = inv.warehouse AND w.companycode = inv.companycode")
						//->setWhere("inv.onhandQty IS NULL")
						->setOrderBy('items.itemcode')
						->runSelect()
						->getResult();
	}

	public function check_if_exists($column, $table, $condition)
	{
		return $this->db->setTable($table)
						->setFields("COUNT(".$column.") count")
						->setWhere($condition)
						->runSelect()
						->getResult();
	}

	public function save_import($table, $posted_data){
		$result = $this->db->setTable($table)
				->setValuesFromPost($posted_data)
				->runInsert();
		return $result;
	}

	public function getAccountId($accountname) {
		$result = $this->db->setTable('chartaccount')
							->setFields("id")
							->setWhere("accountname = '$accountname'")
							->runSelect()
							->getResult();
							
		return $result[0]->id;
	}

}