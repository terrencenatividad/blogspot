<?php
class model {

	private $validation = array();

	public function getUserAccess($email, $password) {
		$result = $this->db->retrieveRow('email, password, group_id, apanel_access', 'wc_users', "email = '$email' AND apanel_access");
		if ($result) {
			if (password_verify($password, $result->password)) {
				return array('email' => $result->email, 'group_id' => $result->group_id, 'apanel_user' => $result->apanel_access);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function getTitle() {
		
	}
	
}