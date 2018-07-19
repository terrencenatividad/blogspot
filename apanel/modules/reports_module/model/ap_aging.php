<?php
class ap_aging extends wc_model {

	public function getCustomerList() 
	{
		$result = $this->db->setTable('partners p')
						->setFields("p.partnercode ind, p.partnername val")
						->leftJoin("accountspayable ap ON p.partnercode = ap.vendor")
						->setWhere("p.partnercode != '' AND p.partnertype = 'supplier' AND p.stat = 'active' AND ap.stat = 'posted' ")
						->setGroupBy("val")
						->setOrderBy("val")
						->runSelect()
						->getResult();

		return $result;
	}

	function dateDiff($start, $end) 
	{
		$start_ts 	= strtotime($start);
		$end_ts 	= strtotime($end);
		$diff 		= $start_ts - $end_ts;
		return floor($diff / (60*60*24));
	}

	public function getApAging($date, $partnerfilter)
	{
		$query_table     = "";
		$query_condition = "";
		$query_groupby   = "";
		$query_sortby    = "";
		$query_fields   = array(" p.partnername partner", "ap.invoiceno as invoiceno",
								"ap.terms as terms","ap.duedate as duedate","ap.transactiondate as invoicedate","ap.amount as amount","ap.sourceno as sourceno","ap.voucherno as voucher","ap.source as source", "ap.referenceno as reference");
							  
		$query_table    = "accountspayable as ap";
		
		$query_condition .= " AND ((select COALESCE(SUM(app.amount),0) from pv_application app where app.apvoucherno = ap.voucherno AND app.stat = 'posted' AND app.entereddate <= '$date 23:59:59' ) < ap.amount) "; 
		$query_condition .= (!empty($date)) ? " AND  ap.transactiondate <= '$date'  " : "";

		$query_condition .= (!empty($partnerfilter) && $partnerfilter != 'none') ? " AND ap.vendor = '$partnerfilter' " : "";
		
		$query_groupby	 .=  " ap.voucherno ";
		$query_sortby    .= (!empty($sort) && !empty($sortBy))? " $sort $sortBy " : " partner, ap.invoiceno, ap.invoicedate ASC";
		$pagination 	=	$this->db->setTable($query_table)
								->setFields($query_fields)
								->leftJoin("partners as p ON p.partnercode = ap.vendor and p.companycode = '".COMPANYCODE."'")
								->setWhere("ap.stat='posted' $query_condition")
								->runPagination();			
					
		return $pagination;
	}

	public function fileExport($date, $partnerfilter)
	{
		$query_table     = "";
		$query_condition = "";
		$query_groupby   = "";
		$query_sortby    = "";
		$query_fields   = array(" p.partnername partner", "ap.invoiceno as invoiceno",
								"ap.terms as terms","ap.duedate as duedate","ap.transactiondate as invoicedate","ap.amount as amount","ap.sourceno as sourceno",
							    "ap.voucherno as voucher","ap.source as source");
							  
		$query_table    = "accountspayable as ap";
		
		$query_condition .= " AND ((select COALESCE(SUM(app.amount),0) from pv_application app where app.apvoucherno = ap.voucherno AND app.stat = 'posted' AND app.entereddate <= '$date 23:59:59' ) < ap.amount) ";
		$query_condition .= (!empty($date)) ? " AND ap.transactiondate <= '$date' " : "";
		
		$query_condition .= (!empty($partnerfilter) && $partnerfilter != 'none') ? " AND ap.vendor = '$partnerfilter' " : "";
		
		$query_groupby	 .=  " ap.voucherno ";
		$query_sortby    .= (!empty($sort) && !empty($sortBy))? " $sort $sortBy " : " partner, ap.invoiceno, ap.invoicedate ASC";
		$result =	$this->db->setTable($query_table)
					->setFields($query_fields)
					->leftJoin("partners as p ON p.partnercode = ap.vendor and p.companycode = '".COMPANYCODE."'")
					->setWhere("ap.stat='posted' $query_condition")
					->runSelect(false)
					->getResult();
					
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

	
}