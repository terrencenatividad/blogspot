<?php
class controller extends wc_controller {

	public function index() {
		$this->access = new access();
		$this->input = new input();
		$this->login_model = new login();
		$this->view->title = 'Login Page';
		if ($this->access->isApanelUser()) {
			$this->url->redirect(BASE_URL);
		}
		if ($this->input->isPost) {
			$data = $this->input->post(array(
				'email',
				'password'
			));
			extract($data);
			$result = $this->login_model->getUserAccess($email, $password);
			if ($result) {
				$this->session->set('login', $result);
				$this->url->redirect(BASE_URL . 'login');
			} else {
				var_dump('Error');
			}
		}
		$this->view->load('login', array(), false);
	}

}