<?php
class user_model extends wc_model {

	public function __construct() {
		parent::__construct();
		$this->log = new log();
	}

	public function saveUser($data) {
		$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		$result = $this->db->setTable('wc_users')
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
		$result = $this->db->setTable('wc_users')
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
			$result =  $this->db->setTable('wc_users')
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
		$result = $this->db->setTable('wc_users')
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
		return $this->db->setTable('wc_users')
						->setFields($fields)
						->setWhere("username = '$username'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getUserPagination($fields, $search, $typeid, $classid) {
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
		if ($typeid && $typeid != 'null') {
			$condition .= (($condition) ? " AND " : " ") . "i.typeid = '$typeid'";
		}
		if ($classid && $classid != 'null') {
			$condition .= (($condition) ? " AND " : " ") . "i.classid = '$classid'";
		}
		$result = $this->db->setTable("wc_users wu")
							->innerJoin("wc_user_group wug ON wug.groupname = wu.groupname AND wug.companycode = wu.companycode")
							->setFields($fields)
							->setWhere($condition)
							->runPagination();

		return $result;
	}

	public function getGroupList() {
		return $this->db->setTable('wc_user_group')
						->setFields('groupname ind, groupname val')
						->runSelect()
						->getResult();
	}

	private function generateSearch($search, $array) {
		$temp = array();
		foreach ($array as $arr) {
			$temp[] = $arr . " LIKE '%" . str_replace(' ', '%', $search) . "%'";
		}
		return '(' . implode(' OR ', $temp) . ')';
	}

}