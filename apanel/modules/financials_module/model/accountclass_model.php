<?php
class accountclass_model extends wc_model {

	public function getAccountClassPagination($fields, $search, $sort) {
		$sort = ($sort) ? $sort : 'accountclass asc';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('accountclasscode','accountclass'));
		}
		$result = $this->db->setTable("accountclass")
						->setFields($fields)
						->setWhere($condition)
						->setOrderBy($sort)
						->runPagination();
						
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