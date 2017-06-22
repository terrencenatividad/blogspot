<?php
class access {
	
	public function checkAccess($usergroup_id, $module_link) {
		$db = new db();
		$exist = '';
		$db->close();
	}

	public function isApanelUser() {
		$session = new session();
		$login = $session->get('login');
		if (isset($login['apanel_user']) && $login['apanel_user']) {
			$db = new db();
			$result = $db->setTable(PRE_TABLE . '_users')
						->setFields('username')
						->setWhere("username = '{$login['username']}'")
						->setLimit(1)
						->runSelect(false)
						->getRow();

			$db->close();
			return $result;
		} else {
			return false;
		}
	}

}