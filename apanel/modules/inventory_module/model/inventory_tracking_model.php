<?php
class inventory_tracking_model extends wc_model {

	public function getInventoryTrackingPagination($itemcode, $datefilter) {
		$result = $this->getInventoryTrackingQuery($itemcode, $datefilter)
						->runPagination();
		
		return $result;
	}

	public function getInventoryTracking($itemcode, $datefilter) {
		$result = $this->getInventoryTrackingQuery($itemcode, $datefilter)
						->runSelect()
						->getResult();
		
		return $result;
	}

	private function getInventoryTrackingQuery($itemcode, $datefilter) {
		$condition = '';
		if ($itemcode && $itemcode != 'none') {
			$condition = "il.itemcode = '$itemcode'";
		}
		$datefilter	= explode('-', $datefilter);
		foreach ($datefilter as $key => $date) {
			$datefilter[$key] = $this->date->dateDbFormat($date);
		}
		if (isset($datefilter[1])) {
			$condition .= (empty($condition) ? '' : ' AND ') . " il.entereddate >= '{$datefilter[0]}' AND il.entereddate <= '{$datefilter[1]}'";
		}
		$query = $this->db->setTable('inventorylogs il')
							->innerJoin('items i ON i.itemcode = il.itemcode AND i.companycode = il.companycode')
							->innerJoin('warehouse w ON w.warehousecode = il.warehouse AND w.companycode = il.companycode')
							->leftJoin('partners p ON p.partnercode = il.details AND p.companycode = il.companycode')
							->leftJoin('wc_users u ON u.username = il.enteredby AND p.companycode = il.companycode')
							->setFields("il.entereddate, itemname, description warehouse, reference, partnername, prevqty, quantity, currentqty, activity, CONCAT(firstname, ' ', lastname) name")
							->setWhere($condition)
							->setOrderBy('il.id DESC');

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