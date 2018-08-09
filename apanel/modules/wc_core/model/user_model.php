<?php
class user_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveUser($data) {
		$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		$result = $this->db->setTable(PRE_TABLE . '_users')
							->setValues($data)
							->runInsert();
		
		if ($result) {
			$this->log->saveActivity("Create User [{$data['username']}]");
		}

		return $result;
	}

	public function updateUser($data, $username) {
		if ($data['password']) {
			$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		} else {
			unset($data['password']);
		}
		$result = $this->db->setTable(PRE_TABLE . '_users')
							->setValues($data)
							->setWhere("username = '$username'")
							->setLimit(1)
							->runUpdate();

		if ($result) {
			$this->log->saveActivity("Update User [$username]");
		}

		return $result;
	}

	public function deleteUsers($data) {
		$error_id = array();
		foreach ($data as $id) {
			$result =  $this->db->setTable(PRE_TABLE . '_users')
								->setWhere("username = '$id'")
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

	public function checkUsername($username, $reference) {
		$result = $this->db->setTable(PRE_TABLE . '_users')
							->setFields('username')
							->setWhere("username = '$username' AND username != '$reference'")
							->setLimit(1)
							->runSelect(false)
							->getRow();

		if ($result) {
			return false;
		} else {
			return true;
		}
	}

	public function getUserById($fields, $username) {
		return $this->db->setTable(PRE_TABLE . '_users')
						->setFields($fields)
						->setWhere("username = '$username'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function checkExistingUser($data) {
		$item_types = "'" . implode("', '", $data) . "'";

		$result = $this->db->setTable(PRE_TABLE . '_users')
							->setFields('username')
							->setWhere("username IN ($item_types)")
							->runSelect()
							->getResult();
		
		return $result;
	}

	public function getUserPagination($fields, $search, $sort) {
		$sort		= ($sort) ? $sort : 'username';
		$fields = array(
			'username',
			'password',
			'email',
			'stat',
			'is_login',
			'useragent',
			'wu.groupname groupname',
			'firstname',
			'lastname',
			'middleinitial',
			'phone',
			'mobile'
		);
		$condition = '';
		if ($search) {
			$condition .= $this->generateSearch($search, array('username' , 'wu.groupname', 'firstname', 'lastname'));
		}
		$result = $this->db->setTable(PRE_TABLE . "_users wu")
							->innerJoin(PRE_TABLE . "_user_group wug ON wug.groupname = wu.groupname AND wug.companycode = wu.companycode")
							->setFields($fields)
							->setWhere($condition)
							->setOrderBy($sort)
							->runPagination();

		return $result;
	}

	public function getGroupList($search = '') {
		$condition = "status = 'active'";
		if ($search) {
			$condition = " groupname = '$search'";
		}
		$result = $this->db->setTable(PRE_TABLE . '_user_group')
						->setFields('groupname ind, groupname val')
						->setWhere($condition)
						->setOrderBy('groupname')
						->runSelect()
						->getResult();

		return $result;
	}

	public function saveUserCSV($values) {
		foreach ($values as $key => $row) {
			$values[$key]['password'] = password_hash($row['password'], PASSWORD_BCRYPT);
		}

		$result = $this->db->setTable(PRE_TABLE . '_users')
							->setValues($values)
							->runInsert();

		return $result;
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

	public function updateStat($data,$code)
	{
		$condition 			   = " username = '$code' ";

		$result 			   = $this->db->setTable('wc_users')
											->setValues($data)
											->setWhere($condition)
											->setLimit(1)
											->runUpdate();

		return $result;
	}

}