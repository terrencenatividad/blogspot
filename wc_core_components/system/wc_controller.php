<?php
class wc_controller {

	public function __construct() {
		$this->view = new wc_view();
		$this->date = new date();
	}

	public function checkOutModel($model) {
		$temp = explode('/', $model);
		if (isset($temp[1])) {
			$path = "modules/{$temp[0]}/model/{$temp[1]}.php";
			if (is_file($path)) {
				require_once($path);
				return new $temp[1]();
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function cleanData($data) {
		if (isset($this->clean_number)) {
			foreach ($this->clean_number as $index) {
				$temp = $data[$index];
				if (is_array($temp)) {
					$array = array();
					foreach ($temp as $key => $value) {
						$array[$key] = str_replace(',', '', $value);
					}
					$data[$index] = $array;
				} else {
					$data[$index] = str_replace(',', '', $temp);
				}
			}
		}
		return $data;
	}

}