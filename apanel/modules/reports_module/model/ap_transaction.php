<?php
class ap_transaction extends wc_model {

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
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$supplier     	 = (isset($data['supplier']) && !empty($data['supplier'])) ? htmlentities($data['supplier']) : ""; 

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
	
		$addCondition = "";
		$addCondition .= (!empty($supplier) && $supplier != 'none')? " AND ap.vendor = '$supplier' ":"";
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND ap.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('partners p')
						->setFields("p.partnercode suppliercode, p.partnername suppliername, ap.voucherno, pv.voucherno pvoucherno, ap.transactiondate, ap.invoiceno, ap.amount, ap.amountpaid,ap.balance,ap.particulars,ap.terms,ap.stat")
						->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
						->leftJoin("pv_application pv ON ap.voucherno = pv.apvoucherno")
						->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat IN('open','posted') $addCondition ")
						->setOrderBy('p.partnercode')
						->runSelect()
						->getResult();
		
		return $result;
	}

	public function getInvoiceList($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$supplier     	 = (isset($data['supplier']) && !empty($data['supplier'])) ? htmlentities($data['supplier']) : ""; 
		
		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($supplier) && $supplier != 'none')? " AND ap.vendor = '$supplier' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND ap.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('partners p')
							->setFields("p.partnercode suppliercode, p.partnername suppliername, ap.voucherno, pv.voucherno pvoucherno, ap.transactiondate, ap.invoiceno,ap.amount,ap.amountpaid,ap.balance,ap.particulars,ap.terms,ap.stat")
							->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
							->leftJoin("pv_application pv ON ap.voucherno = pv.apvoucherno")
							->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat IN('open','posted') $addCondition ")
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
		return $result;
	}
}