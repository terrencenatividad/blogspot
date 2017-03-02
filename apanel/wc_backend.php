<?php
class backend {

	private $module_link = '';
	private $module_folder = '';
	private $module_file = '';
	private $module_function = '';
	private $args = array();

	public function __construct() {
		$this->load = new load();
	}

	public function getModulePath() {
		$this->load->system('db');
		$db = new db();
		if (SUB_FOLDER != '') {
			$paths = $db->retrieveRow('module_link, folder, file, default_function', 'wc_modules', "'" . SUB_FOLDER . "/' LIKE module_link AND active");
			if ($paths) {
				$this->module_link = $paths->module_link;
				$this->module_folder = $paths->folder;
				$this->module_file = $paths->file;
				$this->module_function = ($this->getPage()) ? $this->getPage() : $paths->default_function;
			} else if (DEBUGGING) {
				echo '<p><b>Unable to find Path in Database:</b> ' . SUB_FOLDER . '</p>';
				exit();
			}
		} else {
			$this->module_folder = 'home';
			$this->module_file = 'home';
			$this->module_function = 'index';
		}
		define('MODULE_PATH', 'modules/' . $this->module_folder);
		return MODULE_PATH . '/' . PAGE_TYPE . '/controller/' . $this->module_file . '.php';
	}

	public function getPage() {
		$page = explode('/', str_replace(str_replace('%', '', $this->module_link), '', SUB_FOLDER));
		if (in_array($page[0], array('add', 'view', 'edit', 'delete', 'listing'))) {
			return $page[0];
		} else {
			return false;
		}
	}

	public function loadModule() {
		$path = $this->getModulePath();
		if (file_exists($path)) {
			require_once $path;
			$controller = new controller;
			$controller->{$this->module_function}();
		} else if (DEBUGGING) {
			echo '<p><b>Unable to find Controller File:</b> ' . $path . '</p>';
			exit();
		} else {
			echo 'show 404';
		}
	}

}

$backend = new backend();
$backend->loadModule();