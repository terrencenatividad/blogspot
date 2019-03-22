<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->dashboard = new dashboard_model();
	}

	public function index() {
		$this->view->title			= ('Dashboard');
		$this->view->load('home');
	}

	public function ajax($task) {
		$ajax = $this->{$task}();
		if ($ajax) {
			header('Content-type: application/json');
			echo json_encode($ajax);
		}
	}

}