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

	public function updateStat($data,$code)
	{
		$condition 			   = " atcId = '$code' ";

		$result 			   = $this->db->setTable('atccode')
		->setValues($data)
		->setWhere($condition)
		->setLimit(1)
		->runUpdate();

		return $result;
	}

	public function saveBOM($bom, $bom_details) {
		$result = $this->db->setTable('bom')
		->setValues($disburse)
		->runInsert();

		$bom_details['bom_code'] = $bom['bom_code'];

		$result = $this->db->setTable('bom_details')
		->setValuesFromPost($disburse_comp)
		->runInsert();

		return $result;
	}
}
?>