<?php
class ar_detailed extends wc_model {

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

	public function fileExport($data)
	{
		$datefilter 	= isset($data['datefilter']) ? htmlentities($data['datefilter']) : ""; 
		$customer		= (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 

		$datefilter		= date("Y-m-d",strtotime($datefilter));
	
		$addCondition = "";
		$addCondition .= (!empty($customer) && $customer != 'none')? " AND ar.customer = '$customer' ":"";
		$addCondition .= (!empty($datefilter) && !is_null($datefilter)) ? "AND ar.transactiondate <= '$datefilter' " : "";
		//$balanceCondition = " AND ((select (SUM(app.amount) + SUM(app.discount)) from rv_application app left join receiptvoucher pay ON pay.voucherno = app.voucherno where app.arvoucherno = ar.voucherno and pay.transactiondate <= '$datefilter') < ar.amount) ";
		$balanceCondition = 'AND (ar.balance > 0 OR ar.balance > 0.00)';
		$result = $this->db->setTable('partners p')
							->setFields("
								p.partnercode customercode, p.partnername customername, 
								ar.voucherno, ar.transactiondate, ar.invoiceno, 
								ar.amount, ar.particulars
							")
							->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat IN('open','posted') $addCondition $balanceCondition ")
							->setOrderBy('p.partnercode')
							->runSelect()
							->getResult();
		return $result;
	}

	public function getInvoiceList($data)
	{
		$datefilter 	= isset($data['datefilter']) ? htmlentities($data['datefilter']) : ""; 
		$customer		= (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 

		$datefilter		= date("Y-m-d",strtotime($datefilter));
		
		$addCondition = "";
		$addCondition .= (!empty($customer) && $customer != 'none')? " AND ar.customer = '$customer' ":"";
		
		$addCondition .= (!empty($datefilter) && !is_null($datefilter)) ? "AND ar.transactiondate <= '$datefilter' " : "";
		$balanceCondition = " AND (COALESCE((select (SUM(app.amount) + SUM(app.discount)) from rv_application app left join receiptvoucher pay ON pay.voucherno = app.voucherno where app.arvoucherno = ar.voucherno and pay.transactiondate <= '$datefilter' and pay.stat IN('open','posted')),0) < ar.amount) ";
		$result = $this->db->setTable('partners p')
							->setFields("
								p.partnercode customercode, p.partnername customername, 
								ar.voucherno, ar.transactiondate, ar.invoiceno, 
								ar.amount, ar.particulars
							")
							->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND ar.stat IN('open','posted') $addCondition $balanceCondition ")
							->setOrderBy('p.partnercode')
							->runPagination();
		return $result;
	}

	public function getPayments($voucher, $datefilter){
		$datefilter		= date("Y-m-d",strtotime($datefilter));
		$dateRange 		= (!empty($datefilter) && !is_null($datefilter)) ? " AND rv.transactiondate <= '$datefilter' " : "";

		$result			= $this->db->setTable('rv_application app')
							->setFields("SUM(app.convertedamount) paymentamount, SUM(app.discount) paymentdiscount")
							->leftJoin("receiptvoucher rv ON rv.voucherno = app.voucherno")
							->setWhere("app.arvoucherno = '$voucher' AND rv.stat IN('open','posted') $dateRange ")
							->setOrderBy('rv.transactiondate')
							->runSelect()
							->getResult();
		return $result;
	}
}