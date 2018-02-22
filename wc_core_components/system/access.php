<?php
class access {

	public function __construct() {
		$this->date		= new date();
	}
	
	public function checkAccess($usergroup_id, $module_link) {
		$exist = '';
		$this->db->close();
	}

	public function checkLockAccess($module_page = '', $data_spec = '') {
		$db				= new db();
		$date_now		= $this->date->datetimeDbFormat();
		$username		= USERNAME;
		$module_name	= defined('MODULE_NAME') ? MODULE_NAME : '';

		if ($module_page == '') {
			return true;
		}

		$result			= $db->setTable(PRE_TABLE . '_lock_access')
								->setFields('module_name')
								->setWhere("username != '$username' AND module_name = '$module_name' AND data_spec = '$data_spec' AND DATE_ADD(access_time, INTERVAL 20 SECOND) >= '$date_now'")
								->setLimit(1)
								->runSelect()
								->getRow();

		if ($result) {
			return false;
		}

		$result			= $db->setTable(PRE_TABLE . '_lock_access')
								->setFields('module_name')
								->setWhere("username = '$username' AND module_name = '$module_name'")
								->setLimit(1)
								->runSelect()
								->getRow();

		$db->setTable(PRE_TABLE . '_lock_access')
			->setValues(array(
				'username'		=> $username,
				'module_name'	=> $module_name,
				'module_page'	=> $module_page,
				'data_spec'		=> $data_spec,
				'access_time'	=> $date_now
			));
		
		if ($result) {
			$result = $db->setWhere("username = '$username' AND module_name = '$module_name'")
						->runUpdate();
		} else {
			$result = $db->runInsert();
		}

		$db->close();
		return $result;
	}

	public function isApanelUser() {
		$db			= new db();
		$session	= new session();
		$login		= $session->get('login');
		$date_now	= $this->date->datetimeDbFormat();
		if (isset($login['apanel_user']) && $login['apanel_user']) {
			$result = $db->setTable(PRE_TABLE . '_users')
								->setFields('username')
								->setWhere("username = '{$login['username']}' AND checktime >= '$date_now'")
								->setLimit(1)
								->runSelect(false)
								->getRow();

			if ($result) {
				$this->loginUser();
			}

			$db->close();
			return $result;
		} else {
			return false;
		}
	}

	public function loginUser() {
		$db			= new db();
		$session	= new session();
		$login		= $session->get('login');
		$username	= isset($login['username']) ? $login['username'] : '';

		$checktime = $this->date->datetimeDbFormat('', '+30 minutes');
		$db->setTable(PRE_TABLE . '_users')
			->setValues(array('checktime' => $checktime))
			->setWhere("username = '{$login['username']}'")
			->setLimit(1)
			->runUpdate(false);
	}

	public function logoutUser() {
		$db			= new db();
		$session	= new session();
		$login		= $session->get('login');
		$username	= isset($login['username']) ? $login['username'] : '';

		$session->clean('login');

		$checktime = $this->date->datetimeDbFormat();
		$db->setTable(PRE_TABLE . '_users')
			->setValues(array('checktime' => $checktime))
			->setWhere("username = '$username'")
			->setLimit(1)
			->runUpdate(false);
	}

}