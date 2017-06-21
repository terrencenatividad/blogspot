<?php
class wc_model {

	public function __construct() {
		$this->db = new db();
		$this->date = new date();
	}

}