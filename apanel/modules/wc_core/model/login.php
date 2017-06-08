<?php
class login extends wc_model {

	private $validation = array();

	public function getUserAccess($username, $password) {
		$result = $this->db->setTable('wc_users')
							->setFields("username, password, companycode, groupname, CONCAT(firstname, ' ', middleinitial, ' ', lastname) name")
							->setWhere("username = '$username'")
							->setLimit(1)
							->runSelect(false)
							->getRow();
		if ($result) {
			if (password_verify($password, $result->password)) {
				return array('username' => $result->username, 'apanel_user' => true, 'companycode' => $result->companycode, 'groupname' => $result->groupname, 'name' => $result->name);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function checkLockedAccount($username) {
		$result = $this->db->setTable('wc_users')
							->setFields('locktime')
							->setWhere("username = '$username' AND locktime >= NOW()")
							->setLimit(1)
							->runSelect(false)
							->getRow();

		return $result;
	}
	
}