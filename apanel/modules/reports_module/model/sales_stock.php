<?php
class sales_stock extends wc_model {

	public function getWarehouseList() {
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val")
						->setWhere("stat = 'active'")
						->runSelect()
						->getResult();
				//var_dump($this->db->getQuery());
		return $result;
	}

	public function stockList($data)
	{
		$daterangefilter = (isset($data['daterangefilter'])) ? htmlentities($data['daterangefilter']) : ""; 
		$warehouse       = (isset($data['warehouse']) && !empty($data['warehouse'])) ? htmlentities($data['warehouse']) : "";
		$items 		 	 = (isset($data['items'])  && !empty($data['items'])) ? htmlentities($data['items']) : "";
		$sort       	= (isset($data['sort']) && !empty($data['sort'])) ? htmlentities($data['sort']) : "";
		
		$datefilterArr		= explode(' - ',$daterangefilter);
		$datefilterFrom		= (!empty($datefilterArr[0])) ? date("Y-m-d",strtotime($datefilterArr[0])) : "";
		$datefilterTo		= (!empty($datefilterArr[1])) ? date("Y-m-d",strtotime($datefilterArr[1])) : "";
		
		$addCondition = "";
		$addCondition .= (!empty($warehouse) && $warehouse != 'none' )? " AND invdtl.warehouse = '$warehouse' ":"";
		
		$addCondition .= (!empty($daterangefilter) && !is_null($datefilterArr)) ? "AND inv.transactiondate BETWEEN '$datefilterFrom' AND '$datefilterTo' " : "";

		$result = $this->db->setTable('salesinvoice inv')
						->setFields("ic.label,invdtl.itemcode,invdtl.detailparticular,FORMAT(SUM(invdtl.issueqty),0) as issueqty ,w.description warehouse,w.description,SUM(invdtl.unitprice) as unitprice ,SUM(invdtl.amount) as amount")
						->leftJoin('salesinvoice_details invdtl ON inv.voucherno= invdtl.voucherno')
						->innerJoin("items i ON invdtl.itemcode = i.itemcode AND invdtl.companycode = i.companycode")
						->innerJoin("itemclass ic ON i.classid = ic.id AND i.companycode = ic.companycode")
						->leftJoin("warehouse w ON invdtl.warehouse = w.warehousecode AND invdtl.companycode = w.companycode")
						->setWhere("inv.stat = 'posted' $addCondition ")
						->setOrderBy($sort)
						->setLimit($items)
						->setGroupBy('invdtl.itemcode')
						->runPagination();

		return $result;
		
	}

	public function fileExport($start, $end, $warehouse, $limit, $sort)
	{
		$addCondition = '';

		if ($start && $end){
			$addCondition .= "AND inv.transactiondate BETWEEN '$start' AND '$end' " ;
		}

		if ($warehouse && $warehouse != 'none'){
			$addCondition .= "AND invdtl.warehouse = '$warehouse' ";
		}

		$result = $this->db->setTable('salesinvoice inv')
							->setFields("ic.label,invdtl.itemcode,invdtl.detailparticular,SUM(invdtl.issueqty) as issueqty ,w.description warehouse,w.description,SUM(invdtl.unitprice) as unitprice ,SUM(invdtl.amount) as amount")
							->leftJoin('salesinvoice_details invdtl ON inv.voucherno= invdtl.voucherno')
							->innerJoin("items i ON invdtl.itemcode = i.itemcode AND invdtl.companycode = i.companycode")
							->innerJoin("itemclass ic ON i.classid = ic.id AND i.companycode = ic.companycode")
							->leftJoin("warehouse w ON invdtl.warehouse = w.warehousecode AND invdtl.companycode = w.companycode")
							->setWhere("inv.stat = 'posted' $addCondition ")
							->setOrderBy($sort)
							->setGroupBy('invdtl.itemcode')
							->runSelect()
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
						->runSelect()
						->getResult();
			
			// var_dump($this->db->buildSelect());

			return $result;
		}

		public function getValue($table, $cols = array(), $cond, $orderby = "", $bool = "")
		{
			$result = $this->db->setTable($table)
						->setFields($cols)
						->setWhere($cond)
						->setOrderBy($orderby)
						->runSelect($bool)
						->getResult();

			return $result;
		}

	public function customer_list($itemcode, $startdate, $enddate) {
		$condition = '';
		if ($itemcode && $itemcode != 'none') {
			$condition = "itemcode = '$itemcode'";
		}
		if ($startdate && $enddate) {
			$condition .= (empty($condition) ? 'AND' : ' AND ') . " inv.transactiondate >= '$startdate' AND inv.transactiondate <= '$enddate'";
		}

		$fields = array(
			'inv.transactiondate date',
			'inv.voucherno voucherno',
			'cust.partnercode partnercode',
			"cust.partnername name",
			'SUM(inv.amount) amount',
			'app.balance balance',
			'inv.stat stat'
		);

		$result = $this->db->setTable('salesinvoice as inv')
						->setFields($fields)
						->leftJoin('partners cust ON cust.partnercode = inv.customer AND cust.companycode = inv.companycode')
						->leftJoin("accountsreceivable app ON app.sourceno = inv.voucherno AND app.companycode = inv.companycode ")
						->setWhere(" inv.stat NOT IN ('temporary','cancelled')" .$condition)
						->setGroupBy(" cust.partnercode ")
						->setOrderBy(" inv.voucherno DESC ")
						->runPagination();
						// echo $this->db->getQuery();
		return $result;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}