<?php
class bom extends wc_model
{
	public function getBOMListing($data, $sort, $search, $filter)
	{
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('itemcode', 'description'));
		}

		$result = $this->db->setTable('bom')
		->setFields($data)
		->setWhere("status = 'active' AND '$condition'")
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

	public function fileExport($data, $sort, $search)
	{
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('itemcode', 'description'));
		}

		$result = $this->db->setTable('bom')
		->setFields($data)
		->setWhere($condition)
		->setOrderBy($sort)
		->runSelect()
		->getResult();
		
		return $result;
	}
}
?>