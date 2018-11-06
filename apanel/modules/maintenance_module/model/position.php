<?php
class position extends wc_model {

		public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function savePosition($data) {
		$result =  $this->db->setTable('position')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$insert_id = $this->db->getInsertId();
			$this->log->saveActivity("Create Position [$insert_id]");
		}

		return $result;
	}

	public function updatePosition($data, $id) {
		$result =  $this->db->setTable('position')
							->setValues($data)
							->setWhere("id = '$id'")
							->setLimit(1)
							->runUpdate();
		
		if ($result) {
			$this->log->saveActivity("Update Position [$id]");
		}

		return $result;
	}

	public function deletePosition($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('position')
								->setWhere("id = '$id'")
								->setLimit(1)
								->runDelete();
		
			if ($result) {
				$this->log->saveActivity("Delete Position [$id]");
			} else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}


	public function getPositionById($fields, $id) {
		return $this->db->setTable('position')
						->setFields($fields)
						->setWhere("id = '$id'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getPositionListPagination($fields, $search = '', $sort) {

			$result = $this->db->setTable("position")
								->setFields("id,position,description,stat")
								->setWhere(1)
								->setOrderBy($sort)
								->runPagination();
	
			return $result;
		
	}

	public function getPositionList($fields, $search = '', $sort) {
		$result = $this->getPositionListQuery($fields, $search, $sort)
						->runSelect()
						->getResult();

		return $result;
	}

	public function checkExistingPosition($data) {
		$positions = "'" . implode("', '", $data) . "'";

		$result = $this->db->setTable('position')
							->setFields('position')
							->setWhere("position IN ($positions)")
							->runSelect()
							->getResult();
		
		return $result;
	}

	private function getPositionListQuery($fields, $search = '', $sort) {
		$sort		= ($sort) ? $sort : 'position';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('id', 'position'));
		}
		$query = $this->db->setTable('position')
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

	public function updateStat($data,$id)
	{
		$condition 			   = " id = '$id' ";

		$result 			   = $this->db->setTable('position')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}
}