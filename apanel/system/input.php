<?php
class input {

	public $isPost = false;

	public function __construct() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->isPost = true;
		}
	}

	public function post($post = '') {
		$return = array();
		if (is_array($post)) {
			foreach ($post as $key => $value) {
				if (is_numeric($key)) {
					$return[$value] = isset($_POST[$value]) ? $this->clean($_POST[$value]) : '';
				} else {
					$return[$value] = isset($_POST[$key]) ? $this->clean($_POST[$key]) : '';
				}
			}
		} else if ( ! empty($post)) {
			$return = isset($_POST[$post]) ? $this->clean($_POST[$post]) : '';
		} else {
			var_dump('lksjdlfkjsdf');
			$return = $this->clean($_POST);
		}
		return $return;
	}

	private function clean($value) {
		if (is_array($value)) {
			$temp = array();
			foreach ($value as $key => $val) {
				$temp[$this->clean($key)] = $this->clean($val);
			}
			return $temp;
		} else {
			return addslashes(implode('', explode("\\", trim($value))));
		}
	}

}