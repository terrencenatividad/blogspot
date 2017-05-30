<?php
class controller extends wc_controller {

	public function index() {
		$access				= new access();
		$input				= new input();
		$login_model		= new login();
		$url				= new url();
		$session			= new session();
		$log				= new log();
		$data = array('error_msg' => '');
		$data = $input->post(array(
			'username',
			'password'
		));
		$locktime = base64_decode($input->get('locktime'));
		if ($locktime) {
			$locktime = $this->date->datetimeFormat($locktime);
		}
		$data['locktime'] = $locktime;
		if ($access->isApanelUser()) {
			$redirect = base64_decode($input->get('redirect'));
			$redirect = ( ! empty($redirect) && strpos($redirect, BASE_URL) !== false) ? $redirect : BASE_URL;
			$url->redirect($redirect);
		}
		if ($input->isPost) {
			extract($data);
			$result = $login_model->getUserAccess($username, $password);
			if ($result) {
				$session->set('login', $result);
				$log->saveActivity('Login');
				$url->redirect(FULL_URL);
			} else {
				$data['error_msg'] = 'Invalid Username or Password';
			}
		}

		$this->view->load('login', $data, false);
	}

}