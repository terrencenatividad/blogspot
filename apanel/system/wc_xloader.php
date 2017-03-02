<?php
class load {

	public function system($file) {
		require_once 'system/' . $file . '.php';
	}

	public function model($file) {
		if (defined('MODULE_PATH')) {
			require_once MODULE_PATH . '/model/' . $file . '.php';
		}
	}

}