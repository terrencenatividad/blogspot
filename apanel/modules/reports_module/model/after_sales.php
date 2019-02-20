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

	public function getSerialNumber($serial) 
	{
		$result = $this->db->setTable('items_serialized i')
							->setFields("serialno")
							->setWhere("id = '$serial'")
							->runSelect()
							->getRow();
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
		$addCondition .= (!empty($customer) && $customer != 'none')? " AND b.customer = '$customer' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND a.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('job_release a')
						->setFields("a.transactiondate, service_quotation, a.job_order_no, d.voucherno si_goods, e.voucherno si_service, a.unit uom , a.serialnumbers,partnername,a.stat jr_stat, b.stat jo_stat, c.stat sq_stat, d.stat si_stat, f.balance")
						->leftJoin("job_order b ON a.job_order_no = b.job_order_no")
						->leftJoin("servicequotation c ON c.voucherno = b.service_quotation")
						->leftJoin("salesinvoice d ON d.sourceno = a.job_order_no")
						->leftJoin("billing e ON e.job_orderno = a.job_order_no")
						->leftJoin("accountsreceivable f ON f.invoiceno = e.voucherno")
						->leftJoin('partners p ON p.partnercode = b.customer')
						->setWhere("a.stat = 'released' AND b.stat IN ('partial','completed') AND (d.stat IS NULL OR d.stat NOT IN('cancelled','temporary'))" .$addCondition)
						->setOrderBy('a.transactiondate DESC')
						->runPagination();
						// echo $this->db->getQuery();
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
		$addCondition .= (!empty($customer) && $customer != 'none')? " AND b.customer = '$customer' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? " AND a.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('job_release a')
						->setFields("a.transactiondate, service_quotation, a.job_order_no, d.voucherno si_goods, e.voucherno si_service, a.unit uom , a.serialnumbers,partnername,a.stat jr_stat, b.stat jo_stat, c.stat sq_stat, d.stat si_stat, f.balance")
						->leftJoin("job_order b ON a.job_order_no = b.job_order_no")
						->leftJoin("servicequotation c ON c.voucherno = b.service_quotation")
						->leftJoin("salesinvoice d ON d.sourceno = a.job_order_no")
						->leftJoin("billing e ON e.job_orderno = a.job_order_no")
						->leftJoin("accountsreceivable f ON f.invoiceno = e.voucherno")
						->leftJoin('partners p ON p.partnercode = b.customer')
						->setWhere("a.stat = 'released' AND b.stat != 'cancelled' AND (d.stat IS NULL OR d.stat NOT IN('cancelled','temporary'))" .$addCondition)
						->setOrderBy('a.transactiondate DESC')
						->runSelect()
						->getResult();
		return $result;
	}
}