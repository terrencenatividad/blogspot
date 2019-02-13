<?php
class after_sales extends wc_model {

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

		$sq = $this->db->setTable('servicequotation a')
							->setFields("a.transactiondate, a.voucherno service_quotation, '' job_order_no, '' si_goods, '' si_service,'' serialno, uom,a.customer,a.stat")
							->leftJoin("servicequotation_details b ON a.voucherno = b.voucherno")
							->setWhere('a.stat IN ("Approved","Partial")')
							->buildSelect();

		$jo = $this->db->setTable('job_order a')
							->setFields("a.transactiondate, service_quotation, a.job_order_no, '' si_goods, '' si_service, '' serialno, uom,a.customer,a.stat")
							->leftJoin("job_order_details b ON a.job_order_no = b.job_order_no")
							->setWhere('a.stat = "completed"')
							->buildSelect();

		$jr = $this->db->setTable('job_release a')
							->setFields("a.transactiondate, service_quotation, a.job_order_no, '' si_goods, '' si_service, '' serialno, uom,b.customer,a.stat")
							->leftJoin("job_order b ON a.job_order_no = b.job_order_no")
							->leftJoin("job_order_details c ON c.job_order_no = b.job_order_no")
							->leftJoin("salesinvoice d ON d.sourceno = a.job_order_no")
							->leftJoin("billing e ON e.job_orderno = a.job_order_no")
							->setWhere('a.stat = "released" AND ((d.stat IS NULL OR d.stat != "posted") AND (e.stat IS NULL OR e.stat != "Paid")) AND b.stat != "completed"')
							->buildSelect();

		$si = $this->db->setTable('salesinvoice a')
							->setFields("a.transactiondate, service_quotation, sourceno job_order_no,  b.voucherno si_goods, '' si_service, b.serialno serialno, convuom uom,a.customer,a.stat")
							->leftJoin("salesinvoice_details b ON b.voucherno = a.voucherno")
							->leftJoin("job_order c ON c.job_order_no = a.sourceno")
							->setWhere('b.stat = "posted" AND srctranstype = "jo"')
							->buildSelect();

		$billing = $this->db->setTable('billing a')
							->setFields("a.transactiondate, service_quotation, a.job_orderno, '' si_goods, b.voucherno si_service, '' serialno, convuom uom,a.customer,a.stat")
							->leftJoin("billing_details b ON b.voucherno = a.voucherno")
							->leftJoin("job_order c ON c.job_order_no = a.job_orderno")
							->setWhere('a.stat = "Paid" AND a.job_orderno != ""')
							->buildSelect();

		$query = $sq . ' UNION ALL ' . $jo. ' UNION ALL ' . $jr.' UNION ALL ' . $si. ' UNION ALL ' . $billing;
		
		$result = 	$this->db->setTable("($query) main")
							->setFields('transactiondate,service_quotation, job_order_no, si_goods, si_service, serialno, uom, partnername, main.stat')
							->leftJoin('partners p ON p.partnercode = main.customer')
							->setWhere("main.stat != ''".$addCondition)	
							->setOrderBy($sort)						
							->runPagination(false);
		return $result;
	}

	public function fileExport($data)
	{
		$daterangefilter	= isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$customer     	 	= (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 
		$sort     	 		= (isset($data['sort']) && !empty($data['sort'])) ? htmlentities($data['sort']) : ""; 
		$sort 				= ($sort) ? $sort : 'transactiondate desc';

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($customer) && $customer != 'none')? " AND customer = '$customer' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$sq = $this->db->setTable('servicequotation a')
							->setFields("a.transactiondate, a.voucherno service_quotation, '' job_order_no, '' si_goods, '' si_service,'' serialno, uom,a.customer,a.stat")
							->leftJoin("servicequotation_details b ON a.voucherno = b.voucherno")
							->setWhere('a.stat IN ("Approved","Partial")')
							->buildSelect();

		$jo = $this->db->setTable('job_order a')
							->setFields("a.transactiondate, service_quotation, a.job_order_no, '' si_goods, '' si_service, '' serialno, uom,a.customer,a.stat")
							->leftJoin("job_order_details b ON a.job_order_no = b.job_order_no")
							->setWhere('a.stat = "completed"')
							->buildSelect();

		$jr = $this->db->setTable('job_release a')
							->setFields("a.transactiondate, service_quotation, a.job_order_no, '' si_goods, '' si_service, '' serialno, uom,b.customer,a.stat")
							->leftJoin("job_order b ON a.job_order_no = b.job_order_no")
							->leftJoin("job_order_details c ON c.job_order_no = b.job_order_no")
							->leftJoin("salesinvoice d ON d.sourceno = a.job_order_no")
							->leftJoin("billing e ON e.job_orderno = a.job_order_no")
							->setWhere('a.stat = "released" AND ((d.stat IS NULL OR d.stat != "posted") AND (e.stat IS NULL OR e.stat != "Paid")) AND b.stat != "completed"')
							->buildSelect();

		$si = $this->db->setTable('salesinvoice a')
							->setFields("a.transactiondate, service_quotation, sourceno job_order_no,  b.voucherno si_goods, '' si_service, b.serialno serialno, convuom uom,a.customer,a.stat")
							->leftJoin("salesinvoice_details b ON b.voucherno = a.voucherno")
							->leftJoin("job_order c ON c.job_order_no = a.sourceno")
							->setWhere('b.stat = "posted" AND srctranstype = "jo"')
							->buildSelect();

		$billing = $this->db->setTable('billing a')
							->setFields("a.transactiondate, service_quotation, a.job_orderno, '' si_goods, b.voucherno si_service, '' serialno, convuom uom,a.customer,a.stat")
							->leftJoin("billing_details b ON b.voucherno = a.voucherno")
							->leftJoin("job_order c ON c.job_order_no = a.job_orderno")
							->setWhere('a.stat = "Paid" AND a.job_orderno != ""')
							->buildSelect();

		$query = $sq . ' UNION ALL ' . $jo. ' UNION ALL ' . $jr.' UNION ALL ' . $si. ' UNION ALL ' . $billing;

		$result = 	$this->db->setTable("($query) main")
							->setFields('transactiondate,service_quotation, job_order_no, si_goods, si_service, serialno, uom, partnername, main.stat')
							->leftJoin('partners p ON p.partnercode = main.customer')
							->setWhere("main.stat != ''".$addCondition)	
							->setOrderBy($sort)						
							->runSelect(false)
							->getResult();
		return $result;
	}
}