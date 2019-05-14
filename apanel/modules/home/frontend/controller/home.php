<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->dashboard = new dashboard_model();
	}

	public function index() {
		$this->view->title = 'Dashboard';
		$data						= array();
		$this->view->load('home', $data);
	}

}