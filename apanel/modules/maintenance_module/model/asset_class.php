<?php
class asset_class extends wc_model {

		public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveAssetClass($data) {
		$result =  $this->db->setTable('asset_class')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$insert_id = $this->db->getInsertId();
			$this->log->saveActivity("Create Asset Class [$insert_id]");
		}

		return $result;
	}

	public function updateAssetClass($data, $id) {
		$result =  $this->db->setTable('asset_class')
							->setValues($data)
							->setWhere("id = '$id'")
							->setLimit(1)
							->runUpdate();
		
		if ($result) {
			$this->log->saveActivity("Update Asset Class [$id]");
		}

		return $result;
	}

	public function deleteAssetClass($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('asset_class')
								->setWhere("id = '$id'")
								->setLimit(1)
								->runDelete();
		
			if ($result) {
				$this->log->saveActivity("Delete Asset Class [$id]");
			} else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}

	public function getValue($table, $cols = array(), $leftJoin, $cond, $orderby = "", $addon = true,$limit = false)
	{
		 $this->db->setTable($table)
					->setFields($cols)
					->leftJoin($leftJoin)
					->setWhere($cond)
					->setOrderBy($orderby);
					if($limit){
						$this->db->setLimit('1');
					}
		$result =   $this->db->runSelect($addon)
					->getRow();
					//->buildSelect();

		return $result;
	}

	public function getCOA()
	{
		$result = $this->db->setTable('chartaccount')
						->setFields("id ind, CONCAT(segment5, ' - ', accountname) val, stat stat")
						->runSelect()
						->getResult();
						
		return $result;
	}
	
	public function getAssetClassById($fields, $id) {
		return $this->db->setTable('asset_class')
						->setFields($fields)
						->setWhere("id = '$id'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getAssetClassListPagination($fields, $search = '', $sort) {
		if(!$sort){
			$sort = 'code ASC';
		}
		if ($search) {
			$condition = $this->generateSearch($search, array('id','code', 'assetclass'));
		}else{
			$condition = "";
		}
			$result = $this->db->setTable("asset_class")
								->setFields($fields)
								->setWhere($condition)
								->setOrderBy($sort)
								->runPagination();
	
			return $result;
		
	}

	public function getAssetClassList($fields, $search = '', $sort) {
		$result = $this->getAssetClassListQuery($fields, $search, $sort)
						->runSelect()
						->getResult();

		return $result;
	}

	public function checkExistingAssetClass($data) {
		$codes = "'" . implode("', '", $data) . "'";

		$result = $this->db->setTable('asset_class')
							->setFields('code')
							->setWhere("code IN ($codes)")
							->runSelect()
							->getResult();
		
		return $result;
	}

	public function check_duplicate($current)
		{
			return $this->db->setTable('code')
							->setFields('COUNT(code) count')
							->setWhere(" code = '$current'")
							->runSelect()
							->getResult();
		}

	private function getAssetClassListQuery($fields, $search = '', $sort) {
		$fields = array('a.id',
		'code',
		'assetclass',
		'depreciate',
		'useful_life',
		'salvage_value',
		'gl_asset',
		'gl_accdep',
		'gl_depexpense',
		'c.accountname asset',
		'o.accountname accdep',
		'a.accountname depexp',
		'c.segment5 a_asset',
		'o.segment5 a_accdep',
		'a.segment5 a_depexp',
		'ac.stat');
		$sort		= ($sort) ? $sort : 'assetclass';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('id', 'assetclass'));
		}
		$query = $this->db->setTable('asset_class ac')
							->setFields($fields)
							->leftJoin("chartaccount c ON c.id = ac.gl_asset")
							->leftJoin("chartaccount o ON o.id = ac.gl_accdep")
							->leftJoin("chartaccount a ON a.id = ac.gl_depexpense")
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

		$result 			   = $this->db->setTable('asset_class')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}
}