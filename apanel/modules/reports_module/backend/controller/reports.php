<?php
class controller extends wc_controller {

	public function __construct() {
		parent::__construct();
		$this->ui = new ui();
		$this->view->header_active = 'report/';
	}

	public function listing() {
		$this->view->title = 'Reports';
		$data['ui'] = $this->ui;
		$this->view->load('report_list', $data);
	}

}