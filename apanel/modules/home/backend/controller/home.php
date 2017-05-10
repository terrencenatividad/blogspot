<?php
class controller extends wc_controller {

	public function index() {
		$this->view->title = ('Dashboard (Layout Only)');
		$this->view->load('home');
	}

}