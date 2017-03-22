<?php
class login extends wc_model {

	private $validation = array();

	public function getUserAccess($username, $password) {
		$result = $this->db->setTable('wc_users')
							->setFields('username, password, companycode')
							->setWhere("username = '$username'")
							->setLimit(1)
							->runSelect(false)
							->getRow();
		if ($result) {
			if (password_verify($password, $result->password)) {
				return array('username' => $result->username, 'apanel_user' => true, 'companycode' => $result->companycode);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
}