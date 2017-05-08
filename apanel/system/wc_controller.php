<?php
class wc_controller {

	public function __construct() {
		$this->view = new wc_view();
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


}