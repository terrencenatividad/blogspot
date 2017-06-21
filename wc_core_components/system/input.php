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
			$return = $this->clean($_POST);
		}
		return $return;
	}

	public function get($get = '') {
		$return = array();
		if (is_array($get)) {
			foreach ($get as $key => $value) {
				if (is_numeric($key)) {
					$return[$value] = isset($_GET[$value]) ? $this->clean($_GET[$value]) : '';
				} else {
					$return[$value] = isset($_GET[$key]) ? $this->clean($_GET[$key]) : '';
				}
			}
		} else if ( ! empty($get)) {
			$return = isset($_GET[$get]) ? $this->clean($_GET[$get]) : '';
		} else {
			$return = $this->clean($_GET);
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