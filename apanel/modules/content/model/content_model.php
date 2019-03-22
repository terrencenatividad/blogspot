<?php
class content_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function getContentList($fields, $search, $sort) {
		$sort		= ($sort) ? $sort : 'id';
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('content'));
		}
		$result = $this->db->setTable('content')
		->setFields($fields)
		->setWhere($condition)
		->setOrderBy($sort)
		->setResultLimit(5)
		->runPagination();
		return $result;
	}

	public function saveContent($fields) {
		return $this->db->setTable('content')
		->setValues($fields)
		->runInsert();
	}

	public function getContentById($fields, $id) {
		return $this->db->setTable('content')
		->setFields($fields)
		->setWhere("id = '$id'")
		->runSelect()
		->getRow();
	}

	public function updateContent($fields, $id) {
		return $this->db->setTable('content')
		->setValues($fields)
		->setWhere("id = '$id'")
		->setLimit(1)
		->runUpdate();
	}

	public function deleteContent($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('content')
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

	public function updateStatus($fields, $id) {
		return $this->db->setTable('content')
		->setValues($fields)
		->setWhere("id = '$id'")
		->setLimit(1)
		->runUpdate();
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}
}