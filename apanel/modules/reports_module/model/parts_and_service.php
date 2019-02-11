<?php
class parts_and_service extends wc_model {

	public function getCustomers() 
	{
		$result = $this->db->setTable('partners p')
							->setFields("p.partnercode ind, CONCAT( p.partnercode, ' - ', p.partnername ) val")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' ")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();
		return $result;
	}

	public function getJOList($data)
	{
		$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$customer     	 	= (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 
		$sort     	 		= (isset($data['sort']) && !empty($data['sort'])) ? htmlentities($data['sort']) : ""; 
		$sort 				= ($sort) ? $sort : 'transactiondate desc';

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($customer) && $customer != 'none')? " AND customer = '$customer' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$si = $this->db->setTable('salesinvoice a')
							->setFields("a.transactiondate, service_quotation, po_number, b.voucherno si, discountedamount parts, '' service ,a.customer,a.stat")
							->leftJoin("salesinvoice_details b ON b.voucherno = a.voucherno")
							->leftJoin("job_order c ON c.job_order_no = a.sourceno")
							->setWhere('b.stat = "posted" AND srctranstype = "jo"')
							->buildSelect();

		$billing = $this->db->setTable('billing a')
							->setFields("a.transactiondate, service_quotation, po_number, b.voucherno si, '' parts, discountedamount service,a.customer,a.stat")
							->leftJoin("billing_details b ON b.voucherno = a.voucherno")
							->leftJoin("job_order c ON c.job_order_no = a.job_orderno")
							->setWhere('a.stat != "Cancelled" AND a.job_orderno != ""')
							->buildSelect();

		$query =  $si. ' UNION ALL ' . $billing;
		
		$result = 	$this->db->setTable("($query) main")
							->setFields('transactiondate,service_quotation, po_number, si, parts, service, partnername, main.stat')
							->leftJoin('partners p ON p.partnercode = main.customer')
							->setWhere("main.stat != ''".$addCondition)	
							->setOrderBy($sort)						
							->runPagination(false);
		return $result;
	}

	public function fileExport($data)
	{
		$daterangefilter 	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$customer     	 	= (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 
		$sort     	 		= (isset($data['sort']) && !empty($data['sort'])) ? htmlentities($data['sort']) : ""; 
		$sort 				= ($sort) ? $sort : 'transactiondate desc';

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($customer) && $customer != 'none')? " AND customer = '$customer' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$si = $this->db->setTable('salesinvoice a')
						->setFields("a.transactiondate, service_quotation, po_number, b.voucherno si, discountedamount parts, '' service ,a.customer,a.stat")
						->leftJoin("salesinvoice_details b ON b.voucherno = a.voucherno")
						->leftJoin("job_order c ON c.job_order_no = a.sourceno")
						->setWhere('b.stat = "posted" AND srctranstype = "jo"')
						->buildSelect();

		$billing = $this->db->setTable('billing a')
						->setFields("a.transactiondate, service_quotation, po_number, b.voucherno si, '' parts, discountedamount service,a.customer,a.stat")
						->leftJoin("billing_details b ON b.voucherno = a.voucherno")
						->leftJoin("job_order c ON c.job_order_no = a.job_orderno")
						->setWhere('a.stat != "Cancelled" AND a.job_orderno != ""')
						->buildSelect();

		$query =  $si. ' UNION ALL ' . $billing;

		$result = 	$this->db->setTable("($query) main")
							->setFields('transactiondate,service_quotation, po_number, si, parts, service, partnername, main.stat')
							->leftJoin('partners p ON p.partnercode = main.customer')
							->setWhere("main.stat != ''".$addCondition)	
							->setOrderBy($sort)	
							->runSelect(false)
							->getResult();
		return $result;
	}
}