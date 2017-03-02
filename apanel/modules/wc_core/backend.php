<?php
class controller {

	public function __construct() {
		$this->title = 'Sslkdjflksjdljfk';
	}

	public function index() {
		$this->view->load('home');
	}

	public function login() {
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
			$result = $this->model->getUserAccess($email, $password);
			if ($result) {
				$this->session->set('login', $result);
				$this->url->redirect(BASE_URL . 'login');
			} else {
				var_dump('Error');
			}
		}
		$this->view->load('login', array(), false);
	}

	public function logs() {
		$this->view->load('home');
	}

	public function users() {
		$this->view->load('home');
	}

	public function user_group($type = '') {
		var_dump($type);
		$this->view->load('home');
	}

}