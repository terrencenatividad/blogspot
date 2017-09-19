<?php
class log extends db {

	private $data = '';

	public function __construct() {
		parent::__construct();
		$ipaddress = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) : (isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '-'));
		$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '-';
		$this->data = array(
			'companycode' => defined('COMPANYCODE') ? COMPANYCODE : '',
			'username' => defined('USERNAME') ? USERNAME : '',
			'timestamps' => date('Y-m-d H:i:s'),
			'activitydone' => '',
			'ip_address' => $ipaddress,
			'browser' => $browser,
			'module' => defined('MODULE_NAME') ? MODULE_NAME : '',
			'task' => defined('MODULE_TASK') ? MODULE_TASK : ''
		);
	}

	public function saveActivity($activity) {
		if ($activity == 'Login') {
			$session					= new session();
			$login						= $session->get('login');
			$this->data['companycode']	= (isset($login['companycode']))	? $login['companycode']	: '';
			$this->data['username']		= (isset($login['username']))		? $login['username']	: '';
		}
		$this->data['activitydone'] = $activity;
		return $this->setTable(PRE_TABLE . '_admin_logs')
					->setValues($this->data)
					->runInsert(false);
	}

}