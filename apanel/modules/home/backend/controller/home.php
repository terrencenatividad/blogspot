<?php
class controller extends wc_controller {

	public function index() {
		$this->view->title = ('Dashboard (Test New Docker)');
		$this->view->load('home');
	}

}