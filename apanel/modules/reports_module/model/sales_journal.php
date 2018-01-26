<?php
class sales_journal extends wc_model {

	public function sales_journalList($startdate, $enddate,$customer, $sort) {
		$condition = '';
		if ($startdate && $enddate) {
			$condition .= " AND si.transactiondate >= '$startdate' AND si.transactiondate <= '$enddate'";
		}
		
		if($customer && ! in_array('none',$customer)){
			$condition .= " AND si.customer IN ('" . implode("', '", $customer) . "')";
		}

		$fields = array('
			si.customer code, si.transactiondate transactiondate, si.remarks remarks, si.voucherno ref_no, si.discountamount discount, si.taxamount taxamount, si.netamount netamount, si.vat_sales vat_sales, si.vat_exempt vat_exempt, pt.partnername customer, pt.tinno tinno, si.amount amount,discounttype,(amount * discountamount)/100 as d_amount'
			
		);

		$result = $this->db->setTable('salesinvoice si')
						->setFields($fields)
						->leftJoin('partners pt ON si.customer = pt.partnercode')
						->setWhere("si.taxamount > 0 AND si.stat NOT IN ('temporary','cancelled')" .$condition)
						->setOrderBy($sort)
						->runPagination();
		return $result;
	}

	public function fileExport($startdate, $enddate,$customer, $sort) {
		$condition = '';
		if ($startdate && $enddate) {
			$condition .= " AND si.transactiondate >= '$startdate' AND si.transactiondate <= '$enddate'";
		}
		
		if($customer && ! in_array('none',$customer)){
			$condition .= " AND si.customer IN ('" . implode("', '", $customer) . "')";
		}

		$fields = array('
			si.customer code, si.transactiondate transactiondate, si.remarks remarks, si.voucherno ref_no, si.discountamount discount, si.taxamount taxamount, si.netamount netamount, si.vat_sales vat_sales, si.vat_exempt vat_exempt, pt.partnername customer, pt.tinno tinno, si.amount amount'
		);

		$result = $this->db->setTable('salesinvoice si')
						->setFields($fields)
						->leftJoin('partners pt ON si.customer = pt.partnercode')
						->setWhere("si.taxamount > 0 AND si.stat NOT IN ('temporary','cancelled')" .$condition)
						->setOrderBy($sort)
						->runSelect()
						->getResult();
		return $result;
	}

	public function getCustomerList() {
		$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
						->setWhere("stat = 'active' AND partnertype = 'customer'")
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

}