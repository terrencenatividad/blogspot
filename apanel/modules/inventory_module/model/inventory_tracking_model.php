<?php
class inventory_tracking_model extends wc_model {

	public function getInventoryTrackingPagination($itemcode, $datefilter, $warehouse, $sort) {
		$result = $this->getInventoryTrackingQuery($itemcode, $datefilter, $warehouse, $sort)
						->runPagination();
		return $result;
	}

	public function getInventoryTracking($itemcode, $datefilter, $warehouse, $sort) {
		$result = $this->getInventoryTrackingQuery($itemcode, $datefilter, $warehouse, $sort)
						->runSelect()
						->getResult();
		
		return $result;
	}

	public function getWarehouseDropdownList() {
		$result = $this->db->setTable('warehouse')
							->setFields('warehousecode ind, description val')
							->setWhere("stat = 'active'")
							->runSelect()
							->getResult();

		return $result;
	}

	private function getInventoryTrackingQuery($itemcode, $datefilter, $warehouse, $sort) {
		$condition = '';
		if ($itemcode && $itemcode != 'none') {
			$condition = "il.itemcode = '$itemcode'";
		}
		if ($warehouse && $warehouse != 'none') {
			$condition .= (empty($condition) ? '' : ' AND ') . "w.warehousecode = '$warehouse'";
		}
		$datefilter	= explode('-', $datefilter);
		foreach ($datefilter as $key => $date) {
			$datefilter[$key] = $this->date->dateDbFormat($date);
		}
		if (isset($datefilter[1])) {
			$condition .= (empty($condition) ? '' : ' AND ') . " il.entereddate >= '{$datefilter[0]}' AND il.entereddate <= '{$datefilter[1]} 23:59:59'";
		}
		$query = $this->db->setTable('inventorylogs il')
							->innerJoin('items i ON i.itemcode = il.itemcode AND i.companycode = il.companycode')
							->innerJoin('warehouse w ON w.warehousecode = il.warehouse AND w.companycode = il.companycode')
							->leftJoin('partners p ON p.partnercode = il.details AND p.companycode = il.companycode')
							->leftJoin('wc_users u ON u.username = il.enteredby AND u.companycode = il.companycode')
							->setFields("il.entereddate, itemname, description warehouse, reference, IF(p.partnername!='',p.partnername, il.details) partnername, prevqty, quantity, currentqty, activity, CONCAT(firstname, ' ', lastname) name")
							->setWhere($condition)
							->setOrderBy($sort);
		return $query;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}