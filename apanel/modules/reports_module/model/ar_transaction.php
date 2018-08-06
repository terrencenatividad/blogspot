<?php
class ar_transaction extends wc_model {

	public function getCustomers() 
	{
		$result = $this->db->setTable('partners p')
							->setFields("p.partnercode ind, CONCAT( p.partnercode, ' - ', p.partnername ) val")
							->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat IN('posted') ")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();
		return $result;
	}

	public function getInvoiceList($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$customer     	 = (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 
		
		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($customer) && $customer != 'none')? " AND ar.customer = '$customer' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND ar.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('partners p')
							->setFields("p.partnercode customercode, p.partnername customername, ar.voucherno, rv.voucherno rvoucherno, ar.transactiondate, ar.invoiceno,ar.amount,ar.amountreceived,ar.balance,ar.particulars,ar.terms,ar.stat")
							->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
							->leftJoin("rv_application rv ON ar.voucherno = rv.arvoucherno")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat IN('open','posted') $addCondition ")
							->setOrderBy('p.partnercode')
							->runPagination();
		return $result;
	}

	public function fileExport($data)
	{

		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$customer     	 = (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($customer) && $customer != 'none')? " AND ar.customer = '$customer' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND ar.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('partners p')
							->setFields("p.partnercode customercode, CONCAT( p.first_name, ' ', p.last_name ) customername, ar.voucherno, rv.voucherno rvoucherno, ar.transactiondate, ar.invoiceno,ar.amount,ar.amountreceived,ar.balance,ar.particulars,ar.terms,ar.stat")
							->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
							->leftJoin("rv_application rv ON ar.voucherno = rv.arvoucherno")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat IN('open','posted') $addCondition ")
							->setOrderBy('p.partnercode')
							->runSelect()
							->getResult();
		return $result;
	}
}