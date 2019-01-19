<?php
class sales_customer extends wc_model {

	public function customer_list($startdate, $enddate,$customer,$search) {
		$condition = '';
		$condition2 = '';
		if ($startdate && $enddate) {
			$condition .= " AND inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate'";
		}
		if($customer && $customer != 'none'){
			$condition .= " AND cust.partnercode = '$customer'";
		}

		if ($startdate && $enddate) {
			$condition2 .= " AND sr.transactiondate >= '$startdate' AND sr.transactiondate <= '$enddate'";
		}
		if($customer && $customer != 'none'){
			$condition2 .= " AND cust.partnercode = '$customer'";
		}

		$sub_query = "(SELECT sr.warehouse warehouse ,sr.companycode,sr.voucherno,sr.source_no,SUM(amount) ramount FROM salesreturn sr LEFT JOIN partners cust ON cust.partnercode = sr.customer AND cust.companycode = sr.companycode  WHERE sr.stat = 'Returned'  GROUP BY source_no )";
		// var_dump($sub_query);	

		$fields = array(
			'inv.transactiondate date',
			'inv.voucherno voucherno',
			'inv.warehouse',
			'cust.partnercode partnercode',
			'cust.email email',
			'cust.address1 address1',
			'cust.mobile mobile',
			"partnername name",
			'SUM(inv.amount) amount',
			'inv.stat stat',
			'inv.referenceno as ref',
			'SUM(sra.ramount) as ramount',
			'sra.warehouse srwarehouse'
		);

		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->leftJoin($sub_query .'as sra ON sra.companycode = inv.companycode AND inv.voucherno = sra.source_no' )
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setOrderBy("SUM(inv.amount) DESC ")
						->setGroupBy("cust.partnercode ")
						->runPagination();
						// echo $this->db->getQuery();
		return $result;
	}

	public function fileExport($data) {
		$condition = '';
		$condition2 = '';
		$datefilter	= $data['daterangefilter'];
		$customer 	= $data['customer'];
		$search 	= $data['search'];
		$datefilter = explode('-', $datefilter);
		foreach ($datefilter as $date) {
			$dates[] = date('Y-m-d', strtotime($date));
		}
		$startdate  = 	$dates[0] ;
		$enddate	= 	$dates[1] ;
		if ($startdate && $enddate) {
			$condition .= "AND inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate'";
		}
		if($customer && $customer != 'none'){
			$condition .= " AND cust.partnercode = '$customer'";
		}
		if ($startdate && $enddate) {
			$condition2 .= " AND sr.transactiondate >= '$startdate' AND sr.transactiondate <= '$enddate'";
		}
		if($customer && $customer != 'none'){
			$condition2 .= " AND cust.partnercode = '$customer'";
		}

		$sub_query = "(SELECT sr.warehouse warehouse ,sr.companycode,sr.voucherno,sr.source_no,SUM(amount) ramount FROM salesreturn sr LEFT JOIN partners cust ON cust.partnercode = sr.customer AND cust.companycode = sr.companycode  WHERE sr.stat = 'Returned'  GROUP BY source_no )";

		$fields = array(
			'inv.transactiondate date',
			'inv.voucherno voucherno',
			'inv.warehouse',
			'cust.partnercode partnercode',
			'cust.email email',
			'cust.address1 address1',
			'cust.mobile mobile',
			"partnername name",
			'SUM(inv.amount) amount',
			'inv.stat stat',
			'inv.referenceno as ref',
			'sra.ramount as ramount'
		);

						$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->leftJoin($sub_query .'as sra ON sra.companycode = inv.companycode AND inv.voucherno = sra.source_no' )
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setOrderBy("SUM(inv.amount) DESC ")
						->setGroupBy("cust.partnercode ")
						->runSelect()
						->getResult();
		return $result;
	}

	public function customerDetails($cust_code){
		$fields = array(
			'partnercode',
			"partnername name",
			'email',
			'address1',
			'mobile'
		);
		
		$result = $this->db->setTable('partners')
						->setFields($fields)
						->setWhere("partnertype = 'customer' AND partnercode = '$cust_code'" )
						->setLimit(1)
						->runSelect()
						->getRow();
		
		return $result;
	}

	public function fileExport2($data) {
		$cust_code = $data['partnercode'];
		$res = $this->customerDetails($cust_code);
		$condition = '';
		$condition2 = '';
		$filter = explode('-',$data['datefilter']);
		foreach($filter as $date){
			$dates[] = date('Y-m-d',strtotime($date));
		}

		$start = $dates[0];
		$end   = $dates[1];

		$fields = array(
			'inv.transactiondate date',
			'inv.voucherno voucherno',
			'inv.warehouse',
			"partnername name",
			'(inv.amount) amount',
			'inv.stat stat',
			'inv.referenceno as ref',
			'sra.voucherno as srno',
			'SUM(sra.ramount) sr_amount' 
		);

		if ($start && $end){
			$condition .=  "AND inv.transactiondate >= '$start' AND inv.transactiondate <= '$end' ";
		}

		if ($cust_code){
			$condition .=  "AND inv.customer = '$cust_code' ";
		}

		if ($start && $end){
			$condition2 .= " AND sr.transactiondate >= '$start' AND sr.transactiondate <= '$end'";
		}

		if ($cust_code){
			$condition2 .= " AND cust.partnercode = '$cust_code'";
		}

		$sub_query = "(SELECT sr.companycode,sr.voucherno,sr.source_no,amount ramount FROM salesreturn sr LEFT JOIN partners cust ON cust.partnercode = sr.customer AND cust.companycode = sr.companycode  WHERE sr.stat = 'Returned' )";
		
		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->leftJoin($sub_query .'as sra ON sra.companycode = inv.companycode AND inv.voucherno = sra.source_no'  )
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setGroupBy("inv.voucherno")
						->setOrderBy(" inv.voucherno DESC ")
						->runSelect()
						->getResult();
		return $result;
	}

	public function customerInvoices($cust_code,$datefilter){
		$condition = '';
		$condition2 = '';
		$filter = explode('-',$datefilter);
		foreach($filter as $date){
			$dates[] = date('Y-m-d',strtotime($date));
		}
		$start = $dates[0];
		$end   = $dates[1];

		$fields = array(
			'inv.transactiondate date',
			'inv.voucherno voucherno',
			'inv.warehouse',
			"partnername name",
			'(inv.amount) amount',
			'inv.stat stat',
			'inv.referenceno as ref',
			'sra.voucherno as srno',
			'SUM(sra.ramount) sr_amount' 
		);

		if ($start && $end){
			$condition .=  "AND inv.transactiondate >= '$start' AND inv.transactiondate <= '$end' ";
		}

		if ($cust_code){
			$condition .=  "AND inv.customer = '$cust_code' ";
		}

		if ($start && $end){
			$condition2 .= " AND sr.transactiondate >= '$start' AND sr.transactiondate <= '$end'";
		}

		if ($cust_code){
			$condition2 .= " AND cust.partnercode = '$cust_code'";
		}

		$sub_query = "(SELECT sr.companycode,sr.voucherno,sr.source_no,amount ramount FROM salesreturn sr LEFT JOIN partners cust ON cust.partnercode = sr.customer AND cust.companycode = sr.companycode  WHERE sr.stat = 'Returned'  )";
		
		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->leftJoin($sub_query .'as sra ON sra.companycode = inv.companycode AND inv.voucherno = sra.source_no'  )
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setGroupBy("inv.voucherno")
						->setOrderBy(" inv.voucherno DESC ")
						->runPagination();
		return $result;
	}

	public function retrieveCustomerList() {
			$result = $this->db->setTable('salesinvoice as inv')
					->setFields("GROUP_CONCAT(customer SEPARATOR ',') as customers")
					->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
					->setWhere("inv.stat = 'posted'")
					->runSelect()
					->getRow();

			$ids = preg_split("/[\s,]+/", $result->customers);
			$customers	= "'" . implode("','", $ids) . "'";

			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
						->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'  AND partnercode IN ($customers)")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			return $result;
	}

	public function getWarehouseList() {
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val")
						->setWhere("stat = 'active'")
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

	public function customer_name($customer){
		$result = $this->db->setTable('partners')
					->setFields("partnername name")
					->setWhere("partnertype = 'customer' AND stat = 'active' AND partnercode = '$customer'")
					->runSelect()
					->getResult();
		return $result;
}

}