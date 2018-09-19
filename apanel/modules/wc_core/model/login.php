<?php
class login extends wc_model {

	private $validation = array();

	public function getUserAccess($username, $password) {
		$result = $this->db->setTable(PRE_TABLE . '_users')
							->setFields("username, password, companycode, groupname, CONCAT(firstname, ' ', middleinitial, ' ', lastname) name, stat")
							->setWhere("username = '$username'")
							->setLimit(1)
							->runSelect(false)
							->getRow();
		if ($result) {
			if (password_verify($password, $result->password)) {
				return array('username' => $result->username, 'apanel_user' => true, 'companycode' => $result->companycode, 'groupname' => $result->groupname, 'name' => $result->name, 'stat' => $result->stat);
			} else {
				return false;
			}
		} else {
				return false;
		}
	}

	public function checkLockedAccount($username) {
		$result = $this->db->setTable(PRE_TABLE . '_users')
							->setFields('locktime')
							->setWhere("username = '$username' AND locktime >= NOW()")
							->setLimit(1)
							->runSelect(false)
							->getRow();

		return $result;
	}
	
}