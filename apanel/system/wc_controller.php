<?php
class wc_controller {

	public function __construct() {
		$this->view = new wc_view();
		$this->load = new load();
	}


}