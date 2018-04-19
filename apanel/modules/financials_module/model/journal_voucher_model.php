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

	public function getJournalVoucherById($fields, $voucherno) {
		return $this->db->setTable('journalvoucher')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();
	}

	public function getJournalVoucherPagination($fields, $search, $sort, $datefilter) {
		$sort = ($sort) ? $sort : 'transactiondate desc';
		$condition = "transtype = 'JV' and stat = 'posted' ";
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno','transactiondate','referenceno','amount'));
		}
		$datefilter	= explode('-', $datefilter);
		foreach ($datefilter as $key => $date) {
			$datefilter[$key] = $this->date->dateDbFormat($date);
		}
		if (isset($datefilter[1])) {
			$condition .= " AND transactiondate >= '{$datefilter[0]}' AND transactiondate <= '{$datefilter[1]}'";
		}
		$result = $this->db->setTable("journalvoucher")
						->setFields("transactiondate, voucherno, referenceno, amount")
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

	public function getProformaList() {
		$result = $this->db->setTable('proforma')
							->setFields("proformacode ind, proformadesc val")
							->setWhere("transactiontype = 'Journal Voucher'")
							->setOrderBy("proformadesc")
							->runSelect()
							->getResult();
		return $result;
	}

	public function getChartOfAccountList() {
		$result = $this->db->setTable('chartaccount')
							->setFields("id ind, CONCAT(segment5, ' - ', accountname) val")
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
							->setWhere("voucherno = '$voucherno' AND stat = 'posted'")
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

}