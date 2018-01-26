<?php
class item_type_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveItemType($data) {
		$result =  $this->db->setTable('itemtype')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$insert_id = $this->db->getInsertId();
			$this->log->saveActivity("Create Item Type [$insert_id]");
		}

		return $result;
	}

	public function updateItemType($data, $id) {
		$result =  $this->db->setTable('itemtype')
							->setValues($data)
							->setWhere("id = '$id'")
							->setLimit(1)
							->runUpdate();
		
		if ($result) {
			$this->log->saveActivity("Update Item Type [$id]");
		}

		return $result;
	}

	public function deleteItemType($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('itemtype')
								->setWhere("id = '$id'")
								->setLimit(1)
								->runDelete();
		
			if ($result) {
				$this->log->saveActivity("Delete Item Type [$id]");
			} else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}

	public function getItemTypeById($fields, $id) {
		return $this->db->setTable('itemtype')
						->setFields($fields)
						->setWhere("id = '$id'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getItemTypeListPagination($fields, $search = '', $sort) {
		$result = $this->getItemTypeListQuery($fields, $search, $sort)
						->runPagination();

		return $result;
	}

	public function getItemTypeList($fields, $search = '', $sort) {
		$result = $this->getItemTypeListQuery($fields, $search, $sort)
						->runSelect()
						->getResult();

		return $result;
	}

	public function saveItemTypeCSV($data) {
		$result = $this->db->setTable('itemtype')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$this->log->saveActivity("Upload Item Type CSV");
		}

		return $result;
	}

	public function checkExistingItemType($data) {
		$item_types = "'" . implode("', '", $data) . "'";

		$result = $this->db->setTable('itemtype')
							->setFields('label')
							->setWhere("label IN ($item_types)")
							->runSelect()
							->getResult();
		
		return $result;
	}

	private function getItemTypeListQuery($fields, $search = '', $sort) {
		$sort		= ($sort) ? $sort : 'label';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('id', 'label'));
		}
		$query = $this->db->setTable('itemtype')
							->setFields($fields)
							->setOrderBy($sort)
							->setWhere($condition);

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