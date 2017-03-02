<?php
class controller extends wc_controller {

	public function index() {
		$this->view->title = ('Dashboard');
		$this->view->load('home');
	}

}