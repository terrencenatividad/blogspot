<?php
class input {

	public $isPost = false;

	public function __construct() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->isPost = true;
		}
	}

	public function post($post = array()) {
		$return = array();
		foreach ($post as $key => $value) {
			if (is_numeric($key)) {
				$return[$value] = isset($_POST[$value]) ? $_POST[$value] : 'SS';
			} else {
				var_dump($key);
				$return[$value] = isset($_POST[$key]) ? $_POST[$key] : 'AA';
			}
		}
		return $return;
	}

}