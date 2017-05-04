<?php
class user_model extends wc_model {

	public function saveUser($data) {
		$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		return $this->db->setTable('wc_users')
			->setValues($data)
			->runInsert();
	}

	public function updateUser($data, $username) {
		if ($data['password']) {
			$data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		} else {
			unset($data['password']);
		}
		return $this->db->setTable('wc_users')
				->setValues($data)
				->setWhere("username = '$username'")
				->setLimit(1)
				->runUpdate();
	}

	public function deleteUsers($data) {
		$ids = "'" . implode("','", $data) . "'";
		return $this->db->setTable('wc_users')
				->setWhere("username IN ($ids)")
				->setLimit(count($data))
				->runDelete();
	}

	public function getUserById($fields, $username) {
		return $this->db->setTable('wc_users')
						->setFields($fields)
						->setWhere("username = '$username'")
						->setLimit(1)
						->runSelect()
						->getRow();
	}

	public function getUserList($fields, $search, $typeid, $classid) {
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
		$this->db->setTable("wc_users wu")
						->innerJoin("wc_user_group wug ON wug.groupname = wu.groupname AND wug.companycode = wu.companycode")
						->setFields($fields)
						->setWhere($condition);

		return $this->db->runSelect()
						->getResult();
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