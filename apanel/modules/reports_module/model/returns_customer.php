<?php
class returns_customer extends wc_model 
{
	public function customer_list($startdate, $enddate,$customer, $warehouse, $search) 
	{
		$condition = '';
		if ($startdate && $enddate) 
		{
			$condition .= " AND sr.transactiondate >= '$startdate' AND sr.transactiondate <= '$enddate'";
		}
		// if($customer && ! in_array('none', $customer))
		// {
		// 	$condition .= " AND cust.partnercode IN ('" . implode("', '", $customer) . "')";
		// }
		if($customer && $customer != 'none'){
			$condition .= " AND cust.partnercode = '$customer'";
		}
		if($warehouse && ! in_array('none',$warehouse))
		{
			$condition .= " AND sr.warehouse IN ('" . implode("', '", $warehouse) . "')";
		}
		// if($search){
		// 	$condition .= " AND cust.partnername LIKE '%$search%'";
		// }	

		$fields = array(
			'sr.transactiondate date',
			'sr.voucherno voucherno',
			'cust.partnercode partnercode',
			'cust.email email',
			'cust.address1 address1',
			'cust.mobile mobile',
			"partnername name",
			'sr.warehouse',
			'SUM(sr.amount) amount',
			'sr.stat stat'
		);

		$result = $this->db->setTable('salesreturn as sr')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = sr.customer AND cust.companycode = sr.companycode')
						->setWhere(" sr.stat NOT IN ('temporary','cancelled')" .$condition)
						->setGroupBy("cust.partnercode ")
						->setOrderBy("SUM(sr.amount) DESC ")
						// ->buildSelect();
						->runPagination();
		
		// var_dump($result);

		return $result;
	}

	public function fileExport($data) 
	{
		$condition 	= '';
		$datefilter	= $data['daterangefilter'];
		$customer 	= $data['customer'];
		$datefilter = explode('-', $datefilter);
		
		foreach ($datefilter as $date) 
		{
			$dates[] = date('Y-m-d', strtotime($date));
		}
		
		$startdate  = 	$dates[0] ;
		$enddate	= 	$dates[1] ;
		
		if ($startdate && $enddate) 
		{
			$condition .= "AND sr.transactiondate >= '$startdate' AND sr.transactiondate <= '$enddate'";
		}
		
		if($customer && $customer != 'none'){
			$condition .= " AND cust.partnercode = '$customer'";
		}
		
		$fields = array(
			"partnername name",
			'SUM(sr.amount) amount'
		);

		$result = $this->db->setTable('salesreturn as sr')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = sr.customer AND cust.companycode = sr.companycode')
						->setWhere(" sr.stat NOT IN ('temporary','cancelled')" .$condition)
						->setGroupBy("cust.partnercode ")
						->setOrderBy("SUM(sr.amount) DESC ")
						->runSelect()
						->getResult();
		return $result;
	}

	public function customerDetails($cust_code)
	{
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

	public function customerInvoices($cust_code,$datefilter, $warehouse)
	{
		$condition = '';
		$filter = explode('-',$datefilter);
		
		foreach($filter as $date)
		{
			$dates[] = date('Y-m-d',strtotime($date));
		}
		$start = $dates[0];
		$end   = $dates[1];

		$fields = array(
			'sr.transactiondate date',
			'sr.voucherno voucherno',
			'sr.warehouse',
			"cust.partnername name",
			"srd.itemcode",
			"srd.detailparticular",
			"FORMAT(srd.issueqty,0) issueqty",
			"srd.issueuom as uom",
			"srd.unitprice",
			'(srd.unitprice * srd.issueqty) amount',
			'sr.stat stat'
		);

		if ($start && $end)
		{
			$condition .=  "AND sr.transactiondate >= '$start' AND sr.transactiondate <= '$end' ";
		}
		// if($warehouse)
		// {
		// 	$condition .= " AND sr.warehouse = '$warehouse'";
		// }

		$result = $this->db->setTable('salesreturn as sr')
						->setFields($fields)
						->leftJoin("salesreturn_details as srd ON sr.voucherno = srd.voucherno AND sr.companycode = srd.companycode")
						->leftJoin('partners cust ON cust.partnercode = sr.customer AND cust.companycode = sr.companycode')
						->setWhere(" sr.stat NOT IN ('temporary','cancelled') AND partnercode = '$cust_code' " .$condition)
						->setOrderBy(" sr.voucherno DESC ")
						->runPagination();
		
		return $result;
	}

	public function export_details($data) {
		// var_dump($data);
		$condition 	= '';
		$datefilter	= $data['datefilter'];
		$customer 	= $data['customer'];
		$datefilter = explode('-', $datefilter);
		
		foreach ($datefilter as $date) 
		{
			$dates[] = date('Y-m-d', strtotime($date));
		}

		$startdate  = 	$dates[0];
		$enddate	= 	$dates[1];
		
		if ( isset($startdate) && isset($enddate)) 
		{
			$condition .= " AND sr.transactiondate >= '$startdate' AND sr.transactiondate <= '$enddate'";
		}
		
		if(!empty($customer) && isset($customer))
		{
			$condition .= " AND cust.partnercode = '$customer'";
		}

		$fields = array(
			'sr.transactiondate date',
			'sr.voucherno voucherno',
			'sr.warehouse',
			"cust.partnername name",
			"srd.itemcode",
			"srd.detailparticular",
			"FORMAT(srd.issueqty,0) issueqty",
			"srd.issueuom as uom",
			"srd.unitprice",
			'(srd.unitprice * srd.issueqty) amount',
			'sr.stat stat'
		);

		
		$result = $this->db->setTable('salesreturn as sr')
						->setFields($fields)
						->leftJoin("salesreturn_details as srd ON sr.voucherno = srd.voucherno AND sr.companycode = srd.companycode")
						->leftJoin('partners cust ON cust.partnercode = sr.customer AND cust.companycode = sr.companycode')
						->setWhere(" sr.stat NOT IN ('temporary','cancelled') AND partnercode = '$customer' " .$condition)
						->setOrderBy(" sr.voucherno DESC ")
						->runSelect()
						->getResult();
		
		// var_dump($result);
						
		return $result;
	}

	public function retrieveCustomerList()
	{
			$result = $this->db->setTable('partners')
						->setFields("partnercode ind, partnername val")
						->setWhere("partnercode != '' AND partnertype = 'customer' AND stat = 'active'")
						->setOrderBy("val")
						->runSelect()
						->getResult();
			return $result;
	}

	public function getWarehouseList() 
	{
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val")
						->setWhere("stat = 'active'")
						->runSelect()
						->getResult();
		return $result;
	}

	// ---------- NOT USE ----------
	private function generateSearch($search, $array) 
	{
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}