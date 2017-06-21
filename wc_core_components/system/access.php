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
			return true;
		} else {
			return false;
		}
	}

}