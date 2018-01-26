<?php
class uom_model extends wc_model {

	public function saveItem($data) {
		return $this->db->setTable('uom')
			->setValues($data)
			->runInsert();
	}

	public function updateItem($data, $dataid) {
		return $this->db->setTable('uom')
				->setValues($data)
				->setWhere("uomcode = '$dataid'")
				->setLimit(1)
				->runUpdate();
	}

	public function deleteItems($data) {
		$ids = "'" . implode("','", $data) . "'";
		return $this->db->setTable('uom')
				->setWhere("uomcode IN ($ids)")
				->setLimit(count($data))
				->runDelete();
	}

	public function getItemById($fields, $itemcode) {
		return $this->db->setTable('uom')
						->setFields($fields)
						->setWhere("uomcode = '$itemcode'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getItemList($fields, $search) {
		
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('uomcode','uomdesc','uomtype'));
		}
		$this->db->setTable("uom")
						->setFields($fields)
						->setWhere($condition);

		return $this->db->runSelect()
						->getResult();
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

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}