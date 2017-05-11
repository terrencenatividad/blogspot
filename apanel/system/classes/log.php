<?php
class log extends db {

	public function __construct() {
		parent::__construct();
		$ipaddress = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) : (isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR'] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '-'));
		$browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '-';
		$this->data = array(
			'companycode' => COMPANYCODE,
			'username' => USERNAME,
			'timestamps' => date('Y-m-d H:i:s'),
			'activitydone' => '',
			'ip_address' => $ipaddress,
			'browser' => $browser,
			'module' => MODULE_NAME,
			'task' => MODULE_TASK
		);
	}

	public function saveActivity($activity) {
		$this->data['activitydone'] = $activity;
		return $this->setTable('wc_admin_logs')
					->setValues($this->data)
					->runInsert(false);
	}

}