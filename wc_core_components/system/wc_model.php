<?php
class wc_model {

	public function __construct() {
		$this->db = new db();
		$this->date = new date();
	}

	public function cleanNumber(&$data, $list = array()) {
		foreach ($list as $index) {
			if (is_array($data[$index])) {
				$temp = array();
				foreach ($data[$index] as $value) {
					$temp[] = str_replace(',', '', $value);
				}
				$data[$index] = $temp;
			} else {
				$data[$index] = str_replace(',', '', $data[$index]);
			}
		}
	}

}