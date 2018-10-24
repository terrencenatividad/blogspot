<?php
class costcenter extends wc_model {

		public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveCostCenter($data) {
		$result =  $this->db->setTable('cost_center')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$insert_id = $this->db->getInsertId();
			$this->log->saveActivity("Create Cost Center [$insert_id]");
		}

		return $result;
	}

	public function updateCostCenter($data, $id) {
		$result =  $this->db->setTable('cost_center')
							->setValues($data)
							->setWhere("id = '$id'")
							->setLimit(1)
							->runUpdate();
		
		if ($result) {
			$this->log->saveActivity("Update Cost Center [$id]");
		}

		return $result;
	}

	public function deleteCostCenter($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable('cost_center')
								->setWhere("id = '$id'")
								->setLimit(1)
								->runDelete();
		
			if ($result) {
				$this->log->saveActivity("Delete Cost Center [$id]");
			} else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}

	public function getCOA()
		{
			$result = $this->db->setTable('chartaccount')
							->setFields("id ind, CONCAT(segment5, ' - ', accountname) val, stat stat")
							->runSelect()
							->getResult();
                           
            return $result;
		}
	
	public function getUsers()
	{
		$result = $this->db->setTable('wc_users')
						->setFields("username ind, username val, stat stat")
						->runSelect()
						->getResult();
					
		return $result;
	}

	public function getCostCenterById($fields, $id) {
		return $this->db->setTable('cost_center')
						->setFields($fields)
						->setWhere("id = '$id'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getCostCenterListPagination($fields, $search = '', $sort) {

			$result = $this->db->setTable("cost_center c")
								->leftJoin('chartaccount ca ON c.costcenter_account = ca.id AND c.companycode = ca.companycode')
								->setFields("c.id,costcenter_code,costcenter_account,name,c.description,ca.accountname,approver,c.stat")
								->setWhere(1)
								->setOrderBy($sort)
								->runPagination();
	
			return $result;
		
	}

	public function getBudgetAccount($budget_account) {
		$result = $this->db->setTable('chartaccount')
					->setFields('id, accountname')
					->setWhere("id = '$budget_account'")
					->runSelect()
					->getRow();

		return $result;
	}

	public function getCostCenterList($fields, $search = '', $sort) {
		$result = $this->getCostCenterListQuery($fields, $search, $sort)
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

	public function checkExistingCostCenter($data) {
		$costcenters = "'" . implode("', '", $data) . "'";

		$result = $this->db->setTable('cost_center')
							->setFields('costcenter_code')
							->setWhere("costcenter_code IN ($costcenters)")
							->runSelect()
							->getResult();
		
		return $result;
	}

	private function getCostCenterListQuery($fields, $search = '', $sort) {
		$sort		= ($sort) ? $sort : 'costcenter_code';
		$condition = '';
		if ($search) {
			$condition = $this->generateSearch($search, array('id', 'costcenter_code'));
		}
		$query = $this->db->setTable('cost_center')
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

		$result 			   = $this->db->setTable('cost_center')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}

	public function getModuleAccessList($groupname = '',$asd) {
		$condition = '';
		$left_select = $this->db->setTable(PRE_TABLE . '_user_group ug')
								->setFields('wma.module_name module_name, mod_add, mod_view, mod_edit, mod_delete, mod_list, mod_print, mod_post, mod_unpost, mod_close')
								->innerJoin(PRE_TABLE . '_module_access wma ON ug.groupname = wma.groupname')
								->setWhere("ug.groupname = '$groupname'")
								->buildSelect();

		$this->db->setTable(PRE_TABLE . '_modules wm')
					->setFields('wm.module_name module_name, ug.mod_add mod_add, ug.mod_view mod_view, ug.mod_edit mod_edit, ug.mod_delete mod_delete, ug.mod_list mod_list, ug.mod_print mod_print, ug.mod_post mod_post, ug.mod_unpost mod_unpost, ug.mod_close mod_close, has_add, has_view, has_edit, has_delete, has_list, has_print, has_post, has_unpost, has_close')
					->leftJoin("($left_select) ug ON ug.module_name = wm.module_name")
					->setWhere("wm.active = 1  AND ug.module_name = '$asd'")
					->setGroupBy('wm.module_name');
	
		// echo $this->db->buildSelect(false);
		return $this->db->runSelect(false)->getResult();
	}

}