<?php
// DEFINE MODULE VARIABLES
define('MOD1', (isset($request_dir[0]) ? $request_dir[0] : ''));
define('MOD2', (isset($request_dir[1]) ? $request_dir[1] : ''));
define('MOD3', (isset($request_dir[2]) ? $request_dir[2] : ''));
define('MOD4', (isset($request_dir[3]) ? $request_dir[3] : ''));
define('MOD5', (isset($request_dir[4]) ? $request_dir[4] : ''));
define('MODULE_PATH', (PAGE_TYPE == 'backend' ? '' : 'apanel/') . 'modules/');

class module {

	private $module_link = '';
	private $module_folder = '';
	private $module_file = '';
	private $module_function = '';
	private $args = array();
	private $header_active = '';

	public function getModulePath() {
		$db = new db();
		$path = '';
		$link = $this->getLinkRequest();
		$paths = $db->retrieveRow('module_link, folder, file, default_function', 'wc_modules', "'$link' LIKE module_link AND active");
		$db->close();
		var_dump($paths);
		if ($paths) {
			$this->module_link = $paths->module_link;
			$this->module_folder = $paths->folder;
			$this->module_file = $paths->file;
			$this->module_function = $paths->default_function;
		} else if (DEBUGGING) {
			echo 'Module Not Found in Database';
		} else {
			$this->show404();
		}
		if (MOD1 == '') {
			$this->module_folder = 'wc_core';
			$this->module_file = 'home';
			$this->module_function = 'index';
		}


		return MODULE_PATH . $this->module_folder . '/' . PAGE_TYPE . '/controller/' . $this->module_file . '.php';
	}

	public function getLinkRequest() {
		$link = '';
		for ($x = 1; $x <= 5; $x++) {
			$mod = constant('MOD' . $x);
			if ( ! empty($mod)) {
				$link .= '/' . $mod;
			}
		}
		return ltrim($link, '/');
	}

	public function loadModel() {
		$path = MODULE_PATH . $this->module . '/model.php';
		if (is_file($path) || DEBUGGING) {
			include $path;
		}
	}

	public function loadModule() {
		$path = $this->getModulePath();
		if (MOD1 == 'assets') {
			$this->loadAsset();
		} else if (is_file($path) || DEBUGGING) {
			include $path;
			$this->loadModel();
			$this->initializeModule();
		} else {
			$this->show404();
		}
	}

	public function loadAsset() {
		$asset_type = '';
		if (MOD2 == 'css' || MOD2 == 'fonts' || MOD2 == 'js' || MOD2 == 'img') {
			$asset_path = (PAGE_TYPE == 'backend' ? '' : 'apanel/') . 'view/assets/' . MOD2  . '/' . MOD3;
			$asset_type = MOD2;
		} else {

		}
		if ($asset_type == 'css') {
			header('Content-Type: text/css');
		}
		if (file_exists($asset_path) || DEBUGGING) {
			readfile($asset_path);
			exit();
		} else {
			$this->show404();
		}
	}

	public function show404() {
		header('HTTP/1.0 404 Not Found');
		echo '404';
		exit();
	}

	public function initializeModule() {
		define('MODULE_LINK', $this->module_link);
		$controller = new controller();
		$controller->model = new model();
		$controller->model->db = new db();
		$controller->session = new session();
		$controller->input = new input();
		$controller->url = new url();
		$controller->access = new access();
		$controller->view = new view($this->module);
		$controller->view->title = $controller->title;
		$controller->view->header = $this->getHeaders();
		$controller->view->header_active = $this->header_active;
		$controller->slashes = $this->args;
		$this->page = (empty($this->page)) ? 'index' : $this->page;
		call_user_func_array(array($controller, $this->page), $this->args);
	}

	public function getHeaders() {
		$db = new db();
		$header = array();
		$result = $db->retreiveRecord('navigation, module_link, nav_group', 'wc_modules', "location = '" . PAGE_TYPE . "' AND nav_group != ''");
		foreach ($result as $row) {
			$nav = explode('|', $row->navigation);
			$nav_count = count($nav);
			if ($nav_count == 1) {
				$header[$row->nav_group][$nav[0]] = $row->module_link;
			} else if ($nav_count == 2) {
				$header[$row->nav_group][$nav[0]][$nav[1]] = $row->module_link;
			} else if ($nav_count == 3) {
				$header[$row->nav_group][$nav[0]][$nav[1]][$nav[2]] = $row->module_link;
			}
		}
		return $header;
		$db->close();
	}

}
$module = new module;
$module->loadModule();