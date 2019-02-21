<?php
class purchase_journal extends wc_model {

	public function purchase_journalList($startdate, $enddate,$vendor, $sort) {
		$orderby	= '';
		$condition = '';
		if ($startdate && $enddate) {
			$condition .= " AND pr.transactiondate >= '$startdate' AND pr.transactiondate <= '$enddate'";
		}
		
		if($vendor && ! in_array('none',$vendor)){
			$condition .= " AND pr.vendor IN ('" . implode("', '", $vendor) . "')";
		}

		if($sort){
			$orderby .= $sort;
		}

		$fields = array('
			pr.vendor code, pr.transactiondate transactiondate, pr.remarks remarks, pr.voucherno ref_no, pr.discountamount discount, pr.total_tax taxamount, pr.netamount netamount, pt.partnername vendor, pt.tinno tinno, pr.amount amount'
		);

		$result = $this->db->setTable('purchasereceipt pr')
						->setFields($fields)
						->leftJoin('partners pt ON pr.vendor = pt.partnercode')
						->setWhere(" pr.total_tax > 0 AND pr.stat NOT IN ('temporary','cancelled') " .$condition)
						->setOrderBy($orderby)
						->runPagination();
		return $result;
	}

	public function fileExport($startdate, $enddate,$vendor, $sort) {
		$orderby	= '';
		$condition = '';
		if ($startdate && $enddate) {
			$condition .= " AND pr.transactiondate >= '$startdate' AND pr.transactiondate <= '$enddate'";
		}
		
		if($vendor && ! in_array('none',$vendor)){
			$condition .= " AND pr.vendor IN ('" . implode("', '", $vendor) . "')";
		}

		if($sort){
			$orderby .= $sort;
		}

		$fields = array('
			pr.vendor code, pr.transactiondate transactiondate, pr.remarks remarks, pr.voucherno ref_no, pr.discountamount discount, pr.total_tax taxamount, pr.netamount netamount, pt.partnername vendor, pt.tinno tinno, pr.amount amount'
		);

		$result = $this->db->setTable('purchasereceipt pr')
						->setFields($fields)
						->leftJoin('partners pt ON pr.vendor = pt.partnercode')
						->setWhere(" pr.total_tax > 0 AND pr.stat NOT IN ('temporary','cancelled')" .$condition)
						->setOrderBy($orderby)
						->runSelect()
						->getResult();
		return $result;
	}

	public function getVendorList() {
		$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
						->setWhere("stat = 'active' AND partnertype = 'supplier'")
						->runSelect()
						->getResult();
		return $result;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function vendorInfo($code){
		if ($code == ''){
			$wh = "('')";
		} else {
			$wh = "'". implode("','" ,$code)."'";
			$wh = "($wh)";
		}
		$result = $this->db->setTable('partners')
				->setFields("partnername val")
				->setWhere("stat = 'active' AND partnertype = 'supplier' AND partnercode IN $wh")
				->runSelect()
				->getResult();
				// echo $this->db->getQuery();
		return $result;

	}

}