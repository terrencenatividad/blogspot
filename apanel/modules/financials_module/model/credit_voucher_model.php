<?php
class credit_voucher_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveCreditVoucher($data) {
		$result = $this->db->setTable('creditvoucher')
							->setValues($data)
							->runInsert();

		return $result;
	}

	public function updateCreditVoucher($data, $voucherno) {
		$result = $this->db->setTable('creditvoucher')
							->setValues($data)
							->setWhere("voucherno = '$voucherno'")
							->runUpdate();
		
		return $result;
	}

	
	public function getCustomerList() {
		$result = $this->db->setTable('partners')
							->setFields("partnercode ind, companycode, CONCAT( first_name, ' ', last_name ),  CONCAT(partnercode,' - ',partnername) val")
							->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
							->setOrderBy('partnername')
							->runSelect()
							->getResult();
		return $result;
	}

	public function getCreditVoucherPagination($fields, $search, $sort, $datefilter, $source, $filter) {
		$sort = ($sort) ? $sort : 'voucherno desc';
		$condition = "";
		if ($filter != 'all' && $filter != ''){
			$condition .= " AND c.stat = '$filter' ";
		}
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno','transactiondate','referenceno', 'partnername', 'invoiceno'));
		}
		$datefilter	= explode('-', $datefilter);
		foreach ($datefilter as $key => $date) {
			$datefilter[$key] = $this->date->dateDbFormat($date);
		}
		if (isset($datefilter[1])) {
			$condition .= " AND transactiondate >= '{$datefilter[0]}' AND transactiondate <= '{$datefilter[1]}'";
		}
		$result = $this->db->setTable("creditvoucher c")
						->leftJoin("partners p ON p.partnercode = c.partner AND p.partnertype = 'customer' AND p.companycode = c.companycode")
						->setFields($fields)
						->setWhere("voucherno != ''".$condition)
						->setOrderBy($sort)
						->runPagination();
						
		return $result;
	}

	public function getApplied($voucherno) {
		$result = $this->db->setTable("creditvoucher_applied")
						->setFields('SUM(amount) amount')
						->setWhere("cr_voucher = '$voucherno'")
						->runSelect()
						->getRow();

		return $result;
	}

	public function getCVById($voucherno) {
		$fields 				= array(
			'transactiondate',
			'voucherno',
			'partner',
            'invoiceno',
			'referenceno',
			'amount',
			'receivableno',
			'source',
			'stat'
		);
		$result = $this->db->setTable('creditvoucher')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();
		return $result;
	}

	public function getCVDetails($voucherno) {
		$fields = array(
			'transactiondate',
			'voucherno',
			'partnername',
            'invoiceno',
			'referenceno',
			'amount',
			'receivableno',
			'source',
			'c.stat'
		);
		$result = $this->db->setTable('creditvoucher c')
						->leftJoin('partners p ON p.partnercode = c.partner AND p.partnertype = "customer" AND p.companycode = c.companycode')
						->setFields($fields)
						->setWhere("voucherno = '$voucherno'")
						->runSelect()
						->getRow();
		return $result;
	}

	public function getRVDetails($voucherno) {
		$fields = array(
			'accountname',
			'detailparticulars',
			'debit',
            'credit'
		);
		$result = $this->db->setTable('creditvoucher c')
						->leftJoin('rv_details r ON r.voucherno = c.referenceno')
						->leftJoin('chartaccount ca ON ca.id = r.accountcode')
						->leftJoin('fintaxcode f ON f.salesAccount = r.accountcode')
						->setFields($fields)
						->setWhere("c.voucherno = '$voucherno' AND r.stat != 'deleted' AND r.credit != 0 AND f.fstaxcode = 'ADV'")
						->runSelect()
						->getResult();
		return $result;
	}

	public function getAppliedDetails($voucherno) {
		$fields = array(
			'rv_voucher',
			'r.credit credit',
			'ar.invoiceno invoiceno',
			'ca.entereddate date_applied'
		);
		$result = $this->db->setTable('creditvoucher c')
						->leftJoin('creditvoucher_applied ca ON ca.cr_voucher = c.voucherno')
						->leftJoin('rv_details r ON r.voucherno = ca.rv_voucher')
						->leftJoin('accountsreceivable ar ON ar.voucherno = r.arvoucherno')
						->setFields($fields)
						->setWhere("c.voucherno = '$voucherno' AND r.stat != 'deleted' AND r.credit != 0")
						->setOrderBy('date_applied DESC')
						->runSelect()
						->getResult();
		return $result;
	}

	public function getAccountsReceivablePagination($customer, $search) {
		$condition = '';
		if ($search) {
			$condition .= ' AND ' . $this->generateSearch($search, array('voucherno'));
		}
		$result		= $this->db->setTable('accountsreceivable')
								->setFields('voucherno, transactiondate, amount, invoiceno')
								->setWhere("customer = '$customer' AND stat NOT IN ('cancelled','temporary') AND excessamount > 0". $condition)
								->setOrderBy('voucherno')
								->runPagination();

		return $result;
	}

	public function deleteCreditVoucher($data) {
		$ids	= "'" . implode("','", $data) . "'";
		$result	= $this->db->setTable('creditvoucher')
							->setValues(array('stat'=>'inactive'))
							->setWhere("voucherno IN ($ids)")
							->setLimit(count($data))
							->runUpdate();
		if ($result) {
			$log_id = implode(', ', $data);
			$this->log->saveActivity("Cancel Credit Voucher [$log_id]");
		}

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