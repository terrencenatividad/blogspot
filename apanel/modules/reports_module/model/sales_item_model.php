<?php
class sales_item_model extends wc_model {

	public function getSalesPagination($category, $itemcode, $sort, $start, $end) {
		$result	= $this->getQueryDetails($category, $itemcode, $sort, $start, $end)
						->runPagination();

		return $result;
	}

	public function getSales($category, $itemcode, $sort, $start, $end) {
		$result	= $this->getQueryDetails($category, $itemcode, $sort, $start, $end)
						->runSelect()
						->getResult();

		return $result;
	}

	public function getSalesTotal($category, $itemcode, $start, $end) {
		$result	= $this->getQueryDetails($category, $itemcode, '', $start, $end)
						->setGroupBy('')
						->runSelect()
						->getRow();

		return $result;
	}

	private function getQueryDetails($category, $itemcode, $sort, $start, $end) {
		$condition = '';
		if ($itemcode && $itemcode != 'none') {
			$condition = " AND i.itemcode = '$itemcode'";
		}
		$sort = ($sort) ? str_replace('+', ' ', $sort) : '';
		if ($category == '' || $category == 'none') {
			$category = 0;
		}

		$si = $this->db->setTable('salesinvoice a')
						->innerJoin('salesinvoice_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("itemcode, transactiondate, a.companycode, b.warehouse, issueqty sales, 0 returns, b.amount + b.taxamount - b.itemdiscount amount, issueuom uom")
						->setWhere("a.stat = 'open' OR a.stat = 'posted'")
						->buildSelect();

		$sr = $this->db->setTable('salesreturn a')
						->innerJoin('salesreturn_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("itemcode, transactiondate, a.companycode, b.warehouse, 0 sales, issueqty returns, 0 amount, issueuom uom")
						->setWhere("a.stat = 'open' OR a.stat = 'posted'")
						->buildSelect();

		$inner_query = $si . ' UNION ALL ' . $sr;

		$classes = $this->getItemClassList($category);

		$query = $this->db->setTable("($classes) ic")
							->leftJoin('items i ON i.classid = ic.id AND i.companycode = ic.companycode')
							->leftJoin("($inner_query) iq ON iq.itemcode = i.itemcode AND iq.companycode = i.companycode")
							->setFields('itemname, warehouse, label category, SUM(sales) sales, SUM(returns) returns, SUM(amount) total_amount, uom')
							->setWhere("warehouse != '' AND ((transactiondate >= '$start' AND transactiondate <= '$end') OR transactiondate IS NULL) AND amount > 0" . $condition)
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
	
	public function getItemList() {
		$result = $this->db->setTable('items')
						->setFields("itemcode ind, itemname val")
						->runSelect()
						->getResult();

		return $result;
	}

	private function buildTree($list, $pid = 0, $level_temp = 0, &$linenum = 0, $addself = true) {
		$op = array();
		$level = $level_temp;
		$level++;
		foreach($list as $item) {
			if ($item->parentid == $pid || ($addself && $item->id == $pid)) {
				$linenum++;
				$op[] = "SELECT {$item->id} id, '{$item->label}' label, $level level, '{$item->companycode}' companycode, $linenum linenum, '$item->parentid' aaaa";
				if ( ! ($addself && $item->id == $pid)) {
					$children = $this->buildTree($list, $item->id, $level, $linenum, false);
					$op = array_merge($op, $children);
				}
			}
		}
		return $op;
	}

}