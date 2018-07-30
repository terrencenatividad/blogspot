<?php
class ap_detailed extends wc_model {

	public function getSuppliers() 
	{
		$result = $this->db->setTable('partners p')
							->setFields("p.partnercode ind, CONCAT( p.partnercode, ' - ', p.partnername ) val")
							->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
							->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat IN('posted') ")
							->setGroupBy("val")
							->setOrderBy("val")
							->runSelect()
							->getResult();
		return $result;
	}

	public function fileExport($data)
	{
		$datefilter 	= isset($data['datefilter']) ? htmlentities($data['datefilter']) : ""; 
		$supplier		= (isset($data['supplier']) && !empty($data['supplier'])) ? htmlentities($data['supplier']) : ""; 

		$datefilter		= date("Y-m-d",strtotime($datefilter));
	
		$addCondition = "";
		$addCondition .= (!empty($supplier) && $supplier != 'none')? " AND ap.vendor = '$supplier' ":"";
		$addCondition .= (!empty($datefilter) && !is_null($datefilter)) ? "AND ap.transactiondate <= '$datefilter' " : "";
		$balanceCondition = " AND (COALESCE((select (SUM(app.amount) + SUM(app.discount)) from pv_application app left join paymentvoucher pay ON pay.voucherno = app.voucherno where app.apvoucherno = ap.voucherno and pay.transactiondate <= '$datefilter'),0) < ap.amount) ";
		$result = $this->db->setTable('partners p')
							->setFields("
								p.partnercode suppliercode, p.partnername suppliername, 
								ap.voucherno, ap.transactiondate, ap.invoiceno, 
								ap.amount, ap.particulars
							")
							->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
							->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat IN('open','posted') $addCondition $balanceCondition ")
							->setOrderBy('p.partnercode')
							->runSelect()
							->getResult();
		return $result;
	}

	public function getInvoiceList($data)
	{
		$datefilter 	= isset($data['datefilter']) ? htmlentities($data['datefilter']) : ""; 
		$supplier		= (isset($data['supplier']) && !empty($data['supplier'])) ? htmlentities($data['supplier']) : ""; 

		$datefilter		= date("Y-m-d",strtotime($datefilter));
		
		$addCondition = "";
		$addCondition .= (!empty($supplier) && $supplier != 'none')? " AND ap.vendor = '$supplier' ":"";
		
		$addCondition .= (!empty($datefilter) && !is_null($datefilter)) ? "AND ap.transactiondate <= '$datefilter' " : "";
		$balanceCondition = " AND (COALESCE((select (SUM(app.amount) + SUM(app.discount)) from pv_application app left join paymentvoucher pay ON pay.voucherno = app.voucherno where app.apvoucherno = ap.voucherno and pay.transactiondate <= '$datefilter'),0) < ap.amount) ";
		$result = $this->db->setTable('partners p')
							->setFields("
								p.partnercode suppliercode, p.partnername suppliername, 
								ap.voucherno, ap.transactiondate, ap.invoiceno, 
								ap.amount, ap.particulars
							")
							->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
							->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat IN('open','posted') $addCondition $balanceCondition ")
							->setOrderBy('p.partnercode')
							->runPagination();
		return $result;
	}

	public function getPayments($voucher, $datefilter){
		$datefilter		= date("Y-m-d",strtotime($datefilter));
		$dateRange 		= (!empty($datefilter) && !is_null($datefilter)) ? "AND pv.transactiondate <= '$datefilter' " : "";

		$result			= $this->db->setTable('pv_application app')
							->setFields("SUM(app.convertedamount) paymentamount, SUM(app.discount) paymentdiscount")
							->leftJoin("paymentvoucher pv ON pv.voucherno = app.voucherno")
							->setWhere("app.apvoucherno = '$voucher' AND pv.stat IN('open','posted') $dateRange ")
							->setOrderBy('pv.transactiondate')
							->runSelect()
							->getResult();
		return $result;
	}
}