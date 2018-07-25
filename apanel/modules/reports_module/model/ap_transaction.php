<?php
class ap_transaction extends wc_model {

	public function getsupplierList($data) 
	{
		$supplier     	 = (isset($data['supplier']) && !empty($data['supplier'])) ? htmlentities($data['supplier']) : ""; 
		$searchkey 		 = (isset($data['search'])  && !empty($data['search'])) ? htmlentities($data['search']) : "";
		$status    		 = (isset($data['status']) && !empty($data['status']))?  $data['status'] : "posted";
		
		$addCondition = "";
		$addCondition .= (!empty($supplier))? " AND ap.suppliercode = '$supplier' ":"";
		$addCondition 	.= (!empty($searchkey)) ? " AND (p.partnercode LIKE '%$searchkey%' OR p.first_name LIKE '%$searchkey%' OR p.last_name LIKE '%$searchkey%' OR p.address1 LIKE '%$searchkey%') " : "";

		$result = $this->db->setTable('partners p')
						->setFields("DISTINCT p.partnercode suppliercode, p.partnername suppliername, p.address1  supplieraddress")
						->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
						->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active'  AND ap.stat = '".$status."' $addCondition ")
						->setOrderBy("p.partnercode")
						->runPagination();

		return $result;
	}

	public function getVoucherList($data)
	{
		$supplier     	 = (isset($data['supplier']) && !empty($data['supplier'])) ? htmlentities($data['supplier']) : ""; 
		$voucherno       = (isset($data['voucher']) && !empty($data['voucher'])) ? htmlentities($data['voucher']) : "";
		$searchkey 		 = (isset($data['search'])  && !empty($data['search'])) ? htmlentities($data['search']) : "";
		$status    		 = (isset($data['status']) && !empty($data['status']))?  $data['status'] : "posted";

		$addCondition = "";
		$addCondition .= (!empty($supplier))? " AND ap.vendor = '$supplier' ":"";
		$addCondition .= (!empty($voucherno))? " AND ap.voucherno = '$voucherno' ":"";
		$addCondition 	.= (!empty($searchkey)) ? " AND (p.partnercode LIKE '%$searchkey%' OR p.first_name LIKE '%$searchkey%' OR p.last_name LIKE '%$searchkey%' OR ap.voucherno LIKE '%$searchkey%' ) " : "";

		$result = $this->db->setTable('partners p')
						->setFields("p.partnercode suppliercode, p.partnername suppliername, ap.voucherno, ap.referenceno, ap.transactiondate, ap.invoiceno, ap.invoicedate, ap.duedate")
						->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
						->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat = '".$status."' $addCondition ")
						->setOrderBy('p.partnercode')
						// ->buildSelect();
						->runPagination();
	
		// var_dump($result);
		
		return $result;
	}

	// public function getInvoiceList($data)
	// {
	// 	$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
	// 	$supplier     	 = (isset($data['supplier']) && !empty($data['supplier'])) ? htmlentities($data['supplier']) : ""; 
	// 	$voucherno       = (isset($data['voucher']) && !empty($data['voucher'])) ? htmlentities($data['voucher']) : "";
	// 	$status    		 = (isset($data['status']) && !empty($data['status']))?  $data['status'] : "posted";
		
	// 	$datefilterArr		= explode(' - ',$daterangefilter);
	// 	$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
	// 	$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";

	// 	$addCondition = "";
	// 	$addCondition .= (!empty($supplier))? " AND ap.vendor = '$supplier' ":"";
	// 	$addCondition .= (!empty($voucherno))? " AND ap.voucherno = '$voucherno' ":"";
	// 	$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND ap.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

	// 	$result = $this->db->setTable('partners p')
	// 					->setFields("p.partnercode suppliercode, p.partnername suppliername, ap.voucherno, pv.voucherno pvoucherno, ap.transactiondate, ap.invoiceno,ap.amount,ap.amountpaid,ap.balance,ap.particulars,ap.terms,ap.stat, ap.transtype AS aptranstype, pv.transtype as pvtranstype")
	// 					->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
	// 					->leftJoin("pv_application pv ON ap.voucherno = pv.apvoucherno")
	// 					->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat = '".$status."' $addCondition ")
	// 					->setOrderBy('p.partnercode')
	// 					->runPagination();

	// 	return $result;
	// }

	public function fileExport($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$supplier     	 = (isset($data['supplier']) && !empty($data['supplier'])) ? htmlentities($data['supplier']) : ""; 
		$voucherno       = (isset($data['voucher']) && !empty($data['voucher'])) ? htmlentities($data['voucher']) : "";
		$status    		 = (isset($data['status']) && !empty($data['status']))?  $data['status'] : "posted";
		
		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
	
		$addCondition = "";
		$addCondition .= (!empty($supplier))? " AND ap.vendor = '$supplier' ":"";
		$addCondition .= (!empty($voucherno))? " AND ap.voucherno = '$voucherno' ":"";
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND ap.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('partners p')
						->setFields("p.partnercode suppliercode, p.partnername suppliername, ap.voucherno, pv.voucherno pvoucherno, ap.transactiondate, ap.invoiceno, ap.amount, ap.amountpaid,ap.balance,ap.particulars,ap.terms,ap.stat")
						->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
						->leftJoin("pv_application pv ON ap.voucherno = pv.apvoucherno")
						->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat = '".$status."' $addCondition ")
						->setOrderBy('p.partnercode')
						->runSelect()
						->getResult();
		
		return $result;
	}

	public function getInvoiceList($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$supplier     	 = (isset($data['supplier']) && !empty($data['supplier'])) ? htmlentities($data['supplier']) : ""; 
		$voucherno       = (isset($data['voucher']) && !empty($data['voucher'])) ? htmlentities($data['voucher']) : "";
		$status    		 = (isset($data['status']) && !empty($data['status']))?  $data['status'] : "posted";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($supplier))? " AND ap.vendor = '$supplier' ":"";
		$addCondition .= (!empty($voucherno))? " AND ap.voucherno = '$voucherno' ":"";
		

		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND ap.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('partners p')
							->setFields("p.partnercode suppliercode, p.partnername suppliername, ap.voucherno, pv.voucherno pvoucherno, ap.transactiondate, ap.invoiceno,ap.amount,ap.amountpaid,ap.balance,ap.particulars,ap.terms,ap.stat")
							->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
							->leftJoin("pv_application pv ON ap.voucherno = pv.apvoucherno")
							->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat = '".$status."' $addCondition ")
							->setOrderBy('p.partnercode')
							->runPagination();
		return $result;
	}

	public function getValue($table, $cols = array(), $cond, $orderby = "", $addon = true,$limit = false)
	{
		 $this->db->setTable($table)
					->setFields($cols)
					->setWhere($cond)
					->setOrderBy($orderby);
					if($limit){
						$this->db->setLimit('1');
					}
		$result =   $this->db->runSelect($addon)
					->getResult();
					//->buildSelect();

		return $result;
	}

	public function retrieveData($table, $fields = array(), $cond = "", $join = "", $orderby = "", $groupby = "")
	{
		$result = $this->db->setTable($table)
					->setFields($fields)
					->leftJoin($join)
					->setGroupBy($groupby)
					->setWhere($cond)
					->setOrderBy($orderby)
					->runSelect(false)
					->getResult();
		
		//var_dump($this->db->getQuery());	

		return $result;
	}

	
}