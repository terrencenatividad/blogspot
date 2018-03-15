<?php
class ar_aging extends wc_model {

	public function getCustomerList() 
	{
		$result = $this->db->setTable('partners p')
						->setFields("DISTINCT p.partnercode ind, p.partnername val")
						->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
						->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat = 'posted'")
						->setGroupBy("val")
						->setOrderBy("val")
						->runSelect()
						->getResult();
				//var_dump($this->db->getQuery());
		return $result;
	}

	function dateDiff($start, $end) 
	{
		$start_ts 	= strtotime($start);
		$end_ts 	= strtotime($end);
		$diff 		= $start_ts - $end_ts;
		return floor($diff / (60*60*24));
	}

	public function getArAging($date, $partnerfilter)
	{
		$query_table     = "";
		$query_condition = "";
		$query_groupby   = "";
		$query_sortby    = "";
		$query_fields   = array("p.partnername partner", "ar.invoiceno as invoiceno",
								"ar.terms as terms","ar.duedate as duedate","ar.transactiondate as invoicedate","ar.amount as amount","ar.sourceno as sourceno",
							    "ar.voucherno as voucher","ar.source as source");
							  
		$query_table    = "accountsreceivable as ar";

		$query_condition .= " AND ((select COALESCE(SUM(app.amount),0) from rv_application app where app.arvoucherno = ar.voucherno AND app.stat = 'posted' AND app.entereddate <= '$date 11:59:59' ) < ar.amount) ";					 
		$query_condition .= (!empty($date)) ? " AND ar.transactiondate <= '$date'  " : "";
	
		$query_condition .= (!empty($partnerfilter) && $partnerfilter != 'none') ? " AND ar.customer = '$partnerfilter' " : "";
		
		$query_groupby	 .=  " ar.voucherno ";
		$query_sortby    .= (!empty($sort) && !empty($sortBy))? " $sort $sortBy " : " partner, ar.invoiceno, ar.invoicedate ASC";
		$pagination 	=	$this->db->setTable($query_table)
									->setFields($query_fields)
									->leftJoin("partners as p ON p.partnercode = ar.customer and p.companycode = '".COMPANYCODE."'")
									->setWhere("ar.companycode = '".COMPANYCODE."' AND ar.stat='posted' $query_condition ")
									->runPagination();
		// echo $this->db->getQuery();
		return $pagination;
	}

	public function fileExport($date,$customer)
	{
		$query_table     = "";
		$query_condition = "";
		$query_groupby   = "";
		$query_sortby    = "";
		$query_fields   = array(" p.partnername partner", "ar.invoiceno as invoiceno",
								"ar.terms as terms","ar.duedate as duedate","ar.transactiondate as invoicedate","ar.amount as amount","ar.sourceno as sourceno",
							    "ar.voucherno as voucher","ar.source as source");
							  
		$query_table    = "accountsreceivable as ar";
		
		$query_condition .= " AND ((select COALESCE(SUM(app.amount),0) from rv_application app where app.arvoucherno = ar.voucherno AND app.stat = 'posted' AND app.entereddate <= '$date 11:59:59' ) < ar.amount) ";					 
		$query_condition .= (!empty($date)) ? " AND ar.transactiondate <= '$date' " : "";
		
		$query_condition .= (!empty($customer) && $customer != 'none') ? " AND ar.customer = '$customer' " : "";
		
		$query_groupby	 .=  " ar.voucherno ";
		$query_sortby    .= (!empty($sort) && !empty($sortBy))? " $sort $sortBy " : " partner, ar.invoiceno, ar.invoicedate ASC";
		$result =	$this->db->setTable($query_table)
					->setFields($query_fields)
					->leftJoin("partners as p ON p.partnercode = ar.customer and p.companycode = '".COMPANYCODE."'")
					->setWhere("ar.companycode = '".COMPANYCODE."' AND ar.stat='posted' $query_condition")
					->runSelect(false)
					->getResult();
					//->buildSelect(false);
		// echo $this->db->getQuery();			
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

	private function removeComma($data) 
	{
		if (is_array($data)) {
			$temp = array();
			foreach ($data as $val) {
				$temp[] = $this->removeComma($val);
			}
			return $temp;
		} else {
			return str_replace(',', '', $data);
		}
	}

	public function getOption($type, $orderby = "")
	{
		$result = $this->db->setTable('wc_option')
					->setFields("code ind, value val")
					->setWhere("type = '$type'")
					->setOrderBy($orderby)
					->runSelect(false)
					->getResult();

		return $result;
	}

	public function buildQuery($table, $fields = array(), $cond = "")
	{	
		$sub_select = $this->db->setTable($table)
							   ->setFields($fields)
							   ->setWhere($cond)
							   ->buildSelect();
		// var_dump($this->db->buildSelect());
		return $sub_select;
	}


	private function getList($array, $id) {
		$list = array();
		foreach ($array as $key => $value) {
			if ($key != $id) {
				$list[] = (object) array('ind' => $key, 'val' => $value['label']);
				if (isset($value['children'])) {
					$list = array_merge($list, $this->getList($value['children'], $id));
				}
			}
		}
		return $list;
	}

	private function buildTree($list, $pid = 0) {
		$op = array();
		foreach($list as $item) {
			if ($item->parentid == $pid) {
				$op[$item->id] = array(
					'label' => $item->label
				);
				$children = $this->buildTree($list, $item->id);
				if ($children) {
					$op[$item->id]['children'] = $children;
				}
			}
		}
		return $op;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}