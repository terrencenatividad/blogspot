<?php
class sales_item_model extends wc_model {

	public function getSalesPagination($category, $itemcode, $customer, $warehouse, $sort, $start, $end) {
		$result	= $this->getQueryDetails($category, $itemcode, $customer, $warehouse, $sort, $start, $end)
						->runPagination();

		return $result;
	}

	public function getSales($category, $itemcode, $customer, $warehouse, $sort, $start, $end) {
		$result	= $this->getQueryDetails($category, $itemcode, $customer, $warehouse, $sort, $start, $end)
						->setOrderBy('label ASC, itemname ASC')
						->runSelect()
						->getResult();

		return $result;
	}

	public function getSalesTotal($category, $itemcode, $customer, $warehouse, $sort, $start, $end) {
		$result	= $this->getQueryDetails($category, $itemcode, $customer, $warehouse, '', $start, $end)
						// ->setFields('i.itemname, si.warehouse, ic.label category, SUM(IFNULL(si.sales,0)) sales, SUM(IFNULL(sr.returns,0)) returns, (SUM(IFNULL(si.amount,0))-SUM(IFNULL(sr.amount,0))) total_amount, si.uom')
						->setGroupBy('')
						->runSelect()
						->getRow();

		return $result;
	}

	private function getQueryDetails($category, $itemcode, $customer, $warehouse, $sort, $start, $end) {
		$condition = '';
		if ($itemcode && $itemcode != 'none') {
			$condition .= " AND i.itemcode = '$itemcode'";
		}
		if ($customer && $customer != 'none') {
			$condition .= " AND iq.customer = '$customer'";
		}
		if ($warehouse && $warehouse != 'none') {
			$condition .= " AND iq.warehouse = '$warehouse'";
		}
		$sort = ($sort) ? str_replace('+', ' ', $sort) : '';
		if ($category == '' || $category == 'none') {
			$category = 0;
		}

		$si = $this->db->setTable('salesinvoice a')
						->innerJoin('salesinvoice_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("itemcode, transactiondate, a.companycode, a.customer, b.warehouse, SUM(issueqty) sales, 0 returns, SUM(b.amount + b.taxamount - b.itemdiscount) amount, issueuom uom")
						->setWhere("(a.stat = 'open' OR a.stat = 'posted') ")
						->setGroupBy('b.itemcode, a.transactiondate, a.customer, a.warehouse ')
						->buildSelect();

		$sr = $this->db->setTable('salesreturn a')
						->innerJoin('salesreturn_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("itemcode, transactiondate, a.companycode, a.customer, b.warehouse, 0 sales, SUM(issueqty) returns, -(SUM(b.amount)) amount, issueuom uom")
						->setWhere("a.stat = 'Returned'")
						->setGroupBy('b.itemcode, a.transactiondate, a.customer, a.warehouse')
						->buildSelect();
		$inner_query = $si . ' UNION ALL ' . $sr;

		$classes = $this->getItemClassList($category);

		$query = $this->db->setTable("($classes) ic")
							->leftJoin('items i ON i.classid = ic.id AND i.companycode = ic.companycode')
							->leftJoin("($inner_query) iq ON iq.itemcode = i.itemcode AND iq.companycode = i.companycode")
							->leftJoin("partners p ON p.partnercode = iq.customer AND iq.companycode = p.companycode")
							->setFields('itemname, warehouse, label category, SUM(sales) sales, SUM(returns) returns, SUM(amount) total_amount, uom')
							->setWhere("warehouse != '' AND iq.itemcode IS NOT NULL  AND (iq.transactiondate >= '$start' AND iq.transactiondate <= '$end')" . $condition)
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
						->setFields("itemcode ind, CONCAT(itemcode,' - ',itemname) val")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getCustomerList() {
		$si = $this->db->setTable('salesinvoice a')
						->innerJoin('salesinvoice_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("a.customer, itemcode, b.warehouse, a.companycode")
						->setWhere("a.stat = 'open' OR a.stat = 'posted'")
						->setGroupBy('b.itemcode, a.customer, a.warehouse')
						->buildSelect();

		$sr = $this->db->setTable('salesreturn a')
						->innerJoin('salesreturn_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("a.customer, itemcode, b.warehouse, a.companycode")
						->setWhere("a.stat = 'Returned'")
						->setGroupBy('b.itemcode, a.customer, a.warehouse')
						->buildSelect();

		$inner_query = $si . ' UNION ALL ' . $sr;
		
		$query = $this->db->setTable("itemclass ic")
							->leftJoin('items i ON i.classid = ic.id AND i.companycode = ic.companycode')
							->leftJoin("($inner_query) iq ON iq.itemcode = i.itemcode AND iq.companycode = i.companycode")
							->setFields("GROUP_CONCAT(customer SEPARATOR ',') as customers")
							->setWhere("iq.warehouse != '' AND iq.itemcode IS NOT NULL")
							->runSelect()
							->getRow();

		$ids = preg_split("/[\s,]+/", $query->customers);
		$customers	= "'" . implode("','", $ids) . "'";

		$result = $this->db->setTable('partners')
						->setFields("partnercode ind, CONCAT(partnercode,' - ',partnername) val")
						->setWhere("partnertype = 'customer' AND partnercode IN ($customers)")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getWarehouseList() {
		$result = $this->db->setTable('warehouse')
						->setFields("warehousecode ind, CONCAT(warehousecode,' - ',description) val")
						->runSelect()
						->getResult();

		return $result;
	}

	public function getName($code, $type) {
		if($type == "customer") {
			$result = $this->db->setTable('partners')
								->setFields("partnercode ind, partnername val")
								->setWhere('partnertype = "customer" AND partnercode = "'.$code.'"')
								->runSelect()
								->getRow();
		} else {
			$result = $this->db->setTable('partners')
								->setFields("partnercode ind, CONCAT(first_name,' ',last_name) val")
								->setWhere('partnertype = "sales" AND partnercode = "'.$code.'"')
								->runSelect()
								->getRow();
		}

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