<?php
class sales_customer extends wc_model {

	public function customer_list($startdate, $enddate,$customer,$search) {
		$condition = '';
		if ($startdate && $enddate) {
			$condition .= " AND inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate'";
		}
		if($customer && $customer != 'none'){
			$condition .= " AND cust.partnercode = '$customer'";
		}
		// if($search){
		// 	$condition .= " AND cust.partnername LIKE '%$search%'";
		// }	

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
			'inv.referenceno as ref'
		);

		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setOrderBy("SUM(inv.amount) DESC ")
						->setGroupBy("cust.partnercode ")
						->runPagination();
						// echo $this->db->getQuery();
		return $result;
	}

	public function fileExport($data) {
		$condition = '';
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
		// if($search){
		// 	$condition .= " AND cust.partnername LIKE '%$search%'";
		// }	

		$fields = array(
			"partnername name",
			'SUM(inv.amount) amount'
		);

		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setGroupBy("cust.partnercode ")
						->setOrderBy("SUM(inv.amount) DESC ")
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
			'SUM(inv.amount) amount',
			'inv.stat stat',
			'inv.referenceno as ref'
		);

		if ($start && $end){
			$condition .=  "AND inv.transactiondate >= '$start' AND inv.transactiondate <= '$end' ";
		}

		if ($cust_code){
			$condition .=  "AND inv.customer = '$cust_code' ";
		}
		
		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setGroupBy("inv.voucherno ")
						->setOrderBy(" inv.voucherno DESC ")
						->runSelect()
						->getResult();
		return $result;
	}

	public function customerInvoices($cust_code,$datefilter){
		$condition = '';
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
			'SUM(inv.amount) amount',
			'inv.stat stat',
			'inv.referenceno as ref'
		);

		if ($start && $end){
			$condition .=  "AND inv.transactiondate >= '$start' AND inv.transactiondate <= '$end' ";
		}

		if ($cust_code){
			$condition .=  "AND inv.customer = '$cust_code' ";
		}
		
		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->setWhere(" inv.stat = 'posted'" .$condition)
						->setGroupBy("inv.voucherno ")
						->setOrderBy(" inv.voucherno DESC ")
						->runPagination();
		return $result;
	}

	public function retrieveCustomerList(){
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
						->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
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

}