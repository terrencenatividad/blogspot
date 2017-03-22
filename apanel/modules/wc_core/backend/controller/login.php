<?php
class controller extends wc_controller {

	public function index() {
		$access				= new access();
		$input				= new input();
		$login_model		= new login();
		$url				= new url();
		$session			= new session();
		$data = array('error_msg' => '');
		if ($access->isApanelUser()) {
			$redirect = base64_decode($input->get('redirect'));
			$redirect = ( ! empty($redirect)) ? $redirect : BASE_URL;
			$url->redirect($redirect);
		}
		if ($input->isPost) {
			$data = $input->post(array(
				'username',
				'password'
			));
			extract($data);
			$result = $login_model->getUserAccess($username, $password);
			if ($result) {
				$session->set('login', $result);
				$url->redirect(FULL_URL);
			} else {
				$data['error_msg'] = 'Invalid Username or Password';
			}
		}

		$this->view->load('login', $data, false);
	}

}