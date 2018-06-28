<?php
class backend {

	private $module_link = '';
	private $module_folder = '';
	private $module_file = '';
	private $module_function = '';
	private $args = array();

	public function __construct() {
		// AUTOLOADER
		function autoload_function($class) {
			if (file_exists("system/$class.php")) {
				require_once "system/$class.php";
			} else if (defined('MODULE_PATH') && file_exists(MODULE_PATH . "/model/$class.php")) {
				require_once MODULE_PATH . "/model/$class.php";
			} else {
				$dir = new RecursiveDirectoryIterator(PRE_PATH . CORE_COMPONENTS . 'system');
				foreach (new RecursiveIteratorIterator($dir) as $file){
					if (strpos($file, '/' . $class . '.php') !== false) {
						include_once $file;
						break;
					}
				}
			}
		}
		spl_autoload_register('autoload_function');
		$this->session = new session();
	}

	public function getSession() {
		define('COMPANYCODE', 'CID');
	}

	public function checkAccessType($array, $access) {
		foreach ($array as $value) {
			if (strpos($access, $value) !== false) {
				return true;
			}
		}
		return false;
	}

	public function getModulePath() {
		$subfolders	= explode('/', SUB_FOLDER);
		$subfolder	= $subfolders[0];
		$this->getSession();
		$db			= new db();
		$url		= new url();
		$session	= new session();
		$input		= new input();

		if ($subfolder == 'login') {
			$this->module_folder = 'wc_core';
			$this->module_file = 'login';
			$this->module_function = 'index';
			define('MODULE_URL', BASE_URL . 'login');
			define('MODULE_TASK', 'login');
			define('MODULE_NAME', 'Login');
		} else if ($subfolder != '' && $subfolder != 'ajax') {
			$paths = $db->setTable(PRE_TABLE . '_modules')
						->setFields('module_name, module_group, module_link, folder, file, default_function')
						->setWhere("'" . SUB_FOLDER . "/' LIKE module_link AND active")
						->runSelect(false)
						->getRow();
			
			if ($paths) {
				$this->module_link = $paths->module_link;
				$this->module_folder = $paths->folder;
				$this->module_file = $paths->file;
				$link_args = explode('/', rtrim($paths->module_link, '/'));
				$args = explode('/', rtrim(SUB_FOLDER, '/'));
				$module_url = array();
				$this->module_function = $paths->default_function;
				foreach ($link_args as $key => $value) {
					if ($value == '%' && isset($args[$key])) {
						$this->module_function = $args[$key];
					}
					unset($args[$key]);
				}
				$this->args = $args;
				define('MODULE_URL', BASE_URL . str_replace('%', '', $paths->module_link));
			} else if (DEBUGGING) {
				echo '<p><b>Unable to find Path in Database:</b> ' . SUB_FOLDER . '</p>';
				exit();
			}
		} else {
			$this->module_folder = 'home';
			$this->module_file = 'home';
			$this->module_function = ($subfolder == 'ajax') ? 'ajax' : 'index';
			unset($subfolders[0]);
			$this->args = $subfolders;
			define('MODULE_URL', BASE_URL);
		}
		define('MODULE_PATH', 'apanel/modules/' . $this->module_folder);
		return MODULE_PATH . '/' . PAGE_TYPE . '/controller/' . $this->module_file . '.php';
	}

	public function getPage() {
		$page = explode('/', str_replace(str_replace('%', '', $this->module_link), '', SUB_FOLDER));
		if (in_array($page[0], array('add', 'view', 'edit', 'delete', 'listing', 'print'))) {
			return $page[0];
		} else if ($page[0] == 'ajax') {
			return (isset($page[1])) ? $page[1] : false;
		} else {
			return false;
		}
	}

	public function loadModule() {
		$path = $this->getModulePath();
		if (file_exists($path)) {
			require_once $path;
			$controller = new controller;
			if (method_exists($controller,$this->module_function)) {
				call_user_func_array(array($controller, $this->module_function), $this->args);
			} else if (DEBUGGING) {
				echo '<p><b>Unable to find Controller Function:</b> ' . $this->module_function . '()</p>';
			} else {
				echo 'show 404';
			}
		} else if (DEBUGGING) {
			echo '<p><b>Unable to find Controller File:</b> ' . $path . '</p>';
			exit();
		} else {
			echo 'show 404';
		}
	}

}
$backend	= new backend();
$backend->loadModule();