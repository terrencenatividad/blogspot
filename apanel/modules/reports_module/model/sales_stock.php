<?php
class sales_stock extends wc_model {

	public function getWarehouseList() {
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, description val")
						->setWhere("stat = 'active'")
						->runSelect()
						->getResult();
		return $result;
	}

	public function getSalesPersonList() {
		$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT(first_name,' ',last_name) val")
						->setWhere("stat = 'active' AND partnertype = 'sales'")
						->runSelect()
						->getResult();
		return $result;
	}

	public function stockList($warehouse, $sort, $category, $startdate, $enddate){
		$result	= $this->getQueryDetails($warehouse, $sort, $category, $startdate, $enddate)
						->runPagination();
		return $result;
		
	}

	public function fileExport($warehouse, $sort, $category, $startdate, $enddate){
		$result	= $this->getQueryDetails($warehouse, $sort, $category, $startdate, $enddate)
						->runSelect()
						->getResult();
		return $result;
	}

	public function retrieveData($table, $fields = array(), $cond = "", $join = "", $orderby = "", $groupby = ""){
		$result = $this->db->setTable($table)
					->setFields($fields)
					->leftJoin($join)
					->setGroupBy($groupby)
					->setWhere($cond)
					->setOrderBy($orderby)
					->runSelect()
					->getResult();
		return $result;
	}

	public function getValue($table, $cols = array(), $cond, $orderby = "", $bool = ""){
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
		return $result;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function getSalesTotal($warehouse, $sort, $category, $startdate, $enddate) {
		$result	= $this->getQueryDetails($warehouse, $sort, $category, $startdate, $enddate)
						->setGroupBy('')
						->setOrderBy('')
						->runSelect()
						->getRow();

		return $result;
	}

	private function getQueryDetails($warehouse, $sort, $category, $startdate, $enddate){
		$condition = "";
		$condition .= (!empty($warehouse) && $warehouse != 'none' )? " AND warehouse = '$warehouse' ":"";
		$condition .= (!empty($startdate) && !empty($enddate)) ? " AND iq.transactiondate BETWEEN '$startdate' AND '$enddate' " : "";
		$condition .= (!empty($category) && $category != 'none') ? "AND ic.id = '$category' " : "";
		$sort = (!empty($sort)) ? str_replace('+', ' ', $sort) : 'sorter, parent, classid';
		
		if ($category == '' || $category == 'none') {
			$category = 0;
		}

		$si = $this->db->setTable('salesinvoice a')
						->innerJoin('salesinvoice_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("itemcode, transactiondate, a.companycode, a.customer, b.warehouse, issueqty sales, 0 returns, b.amount + b.taxamount - b.itemdiscount amount, 0 return_amount, b.unitprice price, issueuom uom")
						->setWhere("a.stat = 'open' OR a.stat = 'posted'")
						->buildSelect();

		$sr = $this->db->setTable('salesreturn a')
						->innerJoin('salesreturn_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("itemcode, transactiondate, a.companycode, a.customer, b.warehouse, 0 sales, issueqty returns, 0 amount, b.amount + b.taxamount return_amount, b.unitprice price, issueuom uom")
						->setWhere("a.stat = 'Returned'")
						->buildSelect();

		$inner_query = $si . ' UNION ALL ' . $sr;

		$classes = $this->getItemClassList($category);

		$query = $this->db->setTable("($classes) ic")
							->leftJoin('items i ON i.classid = ic.id AND i.companycode = ic.companycode')
							->leftJoin("($inner_query) iq ON iq.itemcode = i.itemcode AND iq.companycode = i.companycode")
							->setFields('ic.id classid, i.itemcode, i.itemname detailparticular, warehouse, label, FORMAT((SUM(sales) - SUM(returns)),0) issueqty, SUM(price) unitprice,  (SUM(amount)-SUM(return_amount)) amount, uom')
							->setWhere("warehouse != '' AND ((transactiondate >= '$startdate' AND transactiondate <= '$enddate'))  AND iq.itemcode IS NOT NULL" . $condition)
							->setGroupBy('i.itemcode')
							->setOrderBy($sort);

		return $query;
	}

	public function getItemClassList($category) {
		$result = $this->db->setTable('itemclass ic')
							->leftJoin('itemclass p ON ic.parentid = p.id AND ic.companycode = p.companycode')
							->setFields('ic.id id, ic.label label, ic.parentid parentid, ic.companycode companycode')
							->runSelect()
							->getResult();

		return implode(' UNION ALL ', $this->buildTree($result, $category));
	}

	private function buildTree($list, $pid = 0, $level_temp = 0, &$linenum = 0, $addself = true) {
		$op = array();
		$level = $level_temp;
		$level++;
		foreach($list as $item) {
		  if ($item->parentid == $pid || ($addself && $item->id == $pid)) {
			$linenum++;
			$sorter = ($item->parentid == 0) ? $item->id : $item->parentid;
			$op[] = "SELECT {$item->id} id, '{$item->label}' label, $level level, '{$item->companycode}' companycode, $linenum linenum, '$item->parentid' parent, '$sorter' sorter";
			if ( ! ($addself && $item->id == $pid)) {
			  $children = $this->buildTree($list, $item->id, $level, $linenum, false);
			  $op = array_merge($op, $children);
			}
		  }
		}
		return $op;
	}
}