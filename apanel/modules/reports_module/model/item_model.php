<?php
class item_model extends wc_model {

	public function saveItem($data) {
		return $this->db->setTable('items')
			->setValues($data)
			->runInsert();
	}

	public function updateItem($data, $itemcode) {
		return $this->db->setTable('items')
				->setValues($data)
				->setWhere("itemcode = '$itemcode'")
				->setLimit(1)
				->runUpdate();
	}

	public function deleteItems($data) {
		$ids = "'" . implode("','", $data) . "'";
		return $this->db->setTable('items')
				->setWhere("itemcode IN ($ids)")
				->setLimit(count($data))
				->runDelete();
	}

	public function getItemById($fields, $itemcode) {
		return $this->db->setTable('items')
						->setFields($fields)
						->setWhere("itemcode = '$itemcode'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getItemList($fields, $search, $typeid, $classid) {
		$fields = array_merge($fields, array('ic.label itemclass', 'it.value itemtype'));
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('itemcode' , 'ic.label' , 'it.value'));
		}
		if ($typeid && $typeid != 'null') {
			$condition .= (($condition) ? " AND " : " ") . "i.typeid = '$typeid'";
		}
		if ($classid && $classid != 'null') {
			$condition .= (($condition) ? " AND " : " ") . "i.classid = '$classid'";
		}
		$this->db->setTable("items i")
						->innerJoin("itemclass ic ON ic.id = i.classid AND i.companycode = ic.companycode")
						->innerJoin("wc_option it ON it.code = i.typeid AND it.type = 'item_type'")
						->setFields($fields)
						->setWhere($condition);

		return $this->db->runSelect()
						->getResult();
	}

	public function getItemTypeList() {
		return $this->db->setTable('wc_option')
						->setFields('code ind, value val')
						->setWhere("type = 'item_type'")
						->runSelect(false)
						->getResult();
	}

	public function getItemDropdownList() {
		return $this->db->setTable('items')
						->setFields('itemcode ind, itemname val')
						->setWhere("stat = 'active'")
						->runSelect()
						->getResult();
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}