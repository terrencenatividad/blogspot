<?php
class ar_transaction extends wc_model {

	public function getCustomerList($data) 
	{
		$customer     	 = (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 
		$searchkey 		 = (isset($data['searchkey'])  && !empty($data['searchkey'])) ? htmlentities($data['searchkey']) : "";
		$status    		 = (isset($data['status']) && !empty($data['status']))?  $data['status'] : "posted";

		$addCondition = "";
		$addCondition .= (!empty($customer))? " AND ar.customercode = '$customer' ":"";
		$addCondition 	.= (!empty($searchkey)) ? " AND (p.partnercode LIKE '%$searchkey%' 
		OR p.partnername LIKE '%$searchkey%' OR p.address1 LIKE '%$searchkey%') " : "";

		$result = $this->db->setTable('partners p')
						->setFields("DISTINCT p.partnercode customercode, 
						p.partnername customername, p.address1  customeraddress")
						->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
						->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active'  AND ar.stat = '".$status."' $addCondition ")
						->setOrderBy("p.partnercode")
						->runPagination();
						//->getResult();
				//var_dump($this->db->getQuery());
		return $result;
	}

	public function getVoucherList($data)
	{
		
		$customer     	 = (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 
		$voucherno       = (isset($data['voucher']) && !empty($data['voucher'])) ? htmlentities($data['voucher']) : "";
		$searchkey 		 = (isset($data['searchkey'])  && !empty($data['searchkey'])) ? htmlentities($data['searchkey']) : "";
		$status    		 = (isset($data['status']) && !empty($data['status']))?  $data['status'] : "posted";

		$addCondition = "";
		$addCondition .= (!empty($customer))? " AND ar.customer = '$customer' ":"";
		$addCondition .= (!empty($voucherno))? " AND ar.voucherno = '$voucherno' ":"";

		$addCondition 	.= (!empty($searchkey)) ? " AND (p.partnercode LIKE '%$searchkey%' OR CONCAT( p.first_name, ' ', p.last_name ) customername LIKE '%$searchkey%' OR ar.voucherno LIKE '%$searchkey%' ) " : "";

		$result = $this->db->setTable('partners p')
						->setFields("p.partnercode customercode, CONCAT( p.first_name, ' ', p.last_name ) customername, ar.voucherno, ar.referenceno,ar.transactiondate, ar.invoiceno,ar.invoicedate,ar.duedate")
						->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
						->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat = '".$status."' $addCondition ")
						->setOrderBy('p.partnercode')
						->runPagination();
				//var_dump($this->db->getQuery());
		return $result;
	}

	public function getInvoiceList($data)
	{
		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$customer     	 = (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 
		$voucherno       = (isset($data['voucher']) && !empty($data['voucher'])) ? htmlentities($data['voucher']) : "";
		$status    		 = (isset($data['status']) && !empty($data['status']))?  $data['status'] : "posted";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($customer))? " AND ar.customer = '$customer' ":"";
		$addCondition .= (!empty($voucherno))? " AND ar.voucherno = '$voucherno' ":"";
		

		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND ar.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('partners p')
							->setFields("p.partnercode customercode, p.partnername customername, ar.voucherno, rv.voucherno rvoucherno, ar.transactiondate, ar.invoiceno,ar.amount,ar.amountreceived,ar.balance,ar.particulars,ar.terms,ar.stat")
							->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
							->leftJoin("rv_application rv ON ar.voucherno = rv.arvoucherno")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat = '".$status."' $addCondition ")
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

	public function fileExport($data)
	{

		$daterangefilter = isset($data['daterangefilter']) ? htmlentities($data['daterangefilter']) : ""; 
		$customer     	 = (isset($data['customer']) && !empty($data['customer'])) ? htmlentities($data['customer']) : ""; 
		$voucherno       = (isset($data['voucher']) && !empty($data['voucher'])) ? htmlentities($data['voucher']) : "";
		$status    		 = (isset($data['status']) && !empty($data['status']))?  $data['status'] : "posted";

		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($customer))? " AND ar.customer = '$customer' ":"";
		$addCondition .= (!empty($voucherno))? " AND ar.voucherno = '$voucherno' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND ar.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('partners p')
							->setFields("p.partnercode customercode, CONCAT( p.first_name, ' ', p.last_name ) customername, ar.voucherno, rv.voucherno rvoucherno, ar.transactiondate, ar.invoiceno,ar.amount,ar.amountreceived,ar.balance,ar.particulars,ar.terms,ar.stat")
							->leftJoin("accountsreceivable ar ON p.partnercode = ar.customer")
							->leftJoin("rv_application rv ON ar.voucherno = rv.arvoucherno")
							->setWhere("p.partnercode != '' AND p.partnertype = 'customer' AND p.stat = 'active' AND ar.stat = '".$status."' $addCondition ")
							->setOrderBy('p.partnercode')
							->runSelect()
							->getResult();
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