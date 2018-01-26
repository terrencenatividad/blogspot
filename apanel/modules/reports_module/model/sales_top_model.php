<?php
class sales_top_model extends wc_model {

	public function getWarehouseList() {
		$result = $this->db->setTable('warehouse')
							->setFields('warehousecode ind, description val')
							->runSelect()
							->getResult();

		return $result;
	}

	public function getSalesPagination($warehouse, $sort, $start, $end) {
		$query	= $this->getQueryDetails($warehouse, $sort, $start, $end); 
		$result	= $query->runPagination();

		return $result;
	}

	public function getSales($warehouse, $sort, $start, $end) {
		$query	= $this->getQueryDetails($warehouse, $sort, $start, $end); 
		$result	= $query->runSelect()
						->getResult();

		return $result;
	}

	private function getQueryDetails($warehouse, $sort, $start, $end) {
		$sort = ($sort) ? $sort : 'SUM(amount) desc';
		$condition = '';
		if ($warehouse != 'none' && $warehouse != '') {
			$condition = " AND warehouse = '$warehouse'";
		}

		$si = $this->db->setTable('salesinvoice a')
						->innerJoin('salesinvoice_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("itemcode, transactiondate, a.companycode, b.warehouse, issueqty sales, 0 returns, a.amount, issueuom uom")
						->setWhere("a.stat = 'open' OR a.stat = 'posted'")
						->buildSelect();

		$sr = $this->db->setTable('salesreturn a')
						->innerJoin('salesreturn_details b ON a.companycode = b.companycode AND a.voucherno = b.voucherno')
						->setFields("itemcode, transactiondate, a.companycode, b.warehouse, 0 sales, issueqty returns, 0 amount, issueuom uom")
						->setWhere("a.stat = 'open' OR a.stat = 'posted'")
						->buildSelect();

		$inner_query = $si . ' UNION ALL ' . $sr;

		$query = $this->db->setTable("($inner_query) iq")
							->innerJoin('items i ON i.itemcode = iq.itemcode AND i.companycode = iq.companycode')
							->innerJoin('itemclass ic ON ic.id = i.classid AND ic.companycode = i.companycode')
							->setFields('itemname, warehouse, label category, SUM(sales) sales, SUM(returns) returns, SUM(amount) total_amount, uom')
							->setWhere("warehouse != '' AND transactiondate >= '$start' AND transactiondate <= '$end'" . $condition)
							->setGroupBy('i.itemcode')
							->setOrderBy($sort);

		return $query;
	}

}