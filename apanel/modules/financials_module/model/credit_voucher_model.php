<?php
class credit_voucher_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function getCreditVoucherPagination($fields, $search, $sort, $datefilter, $source, $filter) {
		$sort = ($sort) ? $sort : 'transactiondate desc';
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
						->leftJoin("partners p ON p.partnercode = c.partner")
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