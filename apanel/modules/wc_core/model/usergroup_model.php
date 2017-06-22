<?php
class usergroup_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveGroup($data, $module_access) {
		$result = $this->db->setTable(PRE_TABLE . '_user_group')
							->setValues($data)
							->runInsert();
		if ($result) {
			if ($result) {
				$this->log->saveActivity("Create User Group [{$data['groupname']}]");
			}
			$result = $this->setGroupAccess($module_access, $data['groupname']);
		}
		return $result;
	}

	private function getValue($array, $index) {
		return (isset($array[$index])) ? $array[$index] : 0;
	}

	private function setGroupAccess($module_access, $groupname) {
		$values = array();
		$this->db->setTable(PRE_TABLE . '_module_access')
				->setWhere("groupname = '$groupname'")
				->runDelete(false);

		foreach ($module_access as $key => $value) {
			$mod = array(
				'module_name' => $key,
				'companycode' => COMPANYCODE,
				'groupname' => $groupname,
				'mod_add' => $this->getValue($value, 'mod_add'),
				'mod_edit' => $this->getValue($value, 'mod_edit'),
				'mod_view' => $this->getValue($value, 'mod_view'),
				'mod_delete' => $this->getValue($value, 'mod_delete'),
				'mod_list' => $this->getValue($value, 'mod_list'),
				'mod_print' => $this->getValue($value, 'mod_print')
			);
			$values[] = $mod;
		}
		$result = $this->db->setTable(PRE_TABLE . '_module_access')
							->setValues($values)
							->runInsert(false);
		return $result;
	}

	public function updateGroup($data, $group_id, $module_access) {
		$result = $this->db->setTable(PRE_TABLE . '_user_group')
				->setValues($data)
				->setWhere("groupname = '$group_id'")
				->setLimit(1)
				->runUpdate();

		if ($result) {
			$this->log->saveActivity("Update User Group [$group_id]");
		}
				
		return $this->setGroupAccess($module_access, $group_id);
	}

	public function deleteGroup($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable(PRE_TABLE . '_user_group')
								->setWhere("groupname = '$id'")
								->setLimit(1)
								->runDelete();
		
			if ($result) {
				$this->log->saveActivity("Delete User Group [$id]");
			} else {
				if ($this->db->getError() == 'locked') {
					$error_id[] = $id;
				}
			}
		}

		return $error_id;
	}

	public function checkGroupName($groupname, $reference) {
		$result = $this->db->setTable(PRE_TABLE . '_user_group')
							->setFields('groupname')
							->setWhere("groupname = '$groupname' AND groupname != '$reference'")
							->setLimit(1)
							->runSelect()
							->getRow();

		if ($result) {
			return false;
		} else {
			return true;
		}
	}

	public function getGroupByName($fields, $groupname) {
		return $this->db->setTable(PRE_TABLE . '_user_group')
						->setFields($fields)
						->setWhere("groupname = '$groupname'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getGroupPagination($fields, $search, $sort) {
		$condition	= '';
		$sort		= ($sort) ? $sort : 'groupname';
		if ($search) {
			$condition = $this->generateSearch($search, $fields);
		}
		$result =  $this->db->setTable(PRE_TABLE . '_user_group')
							->setFields($fields)
							->setWhere($condition)
							->setOrderBy($sort)
							->runPagination();

		return $result;
	}

	public function getGroupListDropdown() {
		return $this->db->setTable(PRE_TABLE . '_user_group')
						->setFields('groupname ind, groupname val')
						->runSelect()
						->getResult();
	}

	public function getModuleAccessList($groupname = '') {
		$condition = '';
		$left_select = $this->db->setTable(PRE_TABLE . '_user_group ug')
								->setFields('wma.module_name module_name, mod_add, mod_view, mod_edit, mod_delete, mod_list, mod_print')
								->innerJoin(PRE_TABLE . '_module_access wma ON ug.groupname = wma.groupname')
								->setWhere("ug.groupname = '$groupname'")
								->buildSelect();

		$this->db->setTable(PRE_TABLE . '_modules wm')
					->setFields('wm.module_name module_name, ug.mod_add mod_add, ug.mod_view mod_view, ug.mod_edit mod_edit, ug.mod_delete mod_delete, ug.mod_list mod_list, ug.mod_print mod_print, has_add, has_view, has_edit, has_delete, has_list, has_print')
					->leftJoin("($left_select) ug ON ug.module_name = wm.module_name")
					->setGroupBy('wm.module_name');
	
		// echo $this->db->buildSelect(false);
		return $this->db->runSelect(false)->getResult();
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}