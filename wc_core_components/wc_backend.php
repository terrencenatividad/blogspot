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
					if (strpos($file, $class . '.php') !== false) {
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
		$login = $this->session->get('login');
		$companycode	= (isset($login['companycode']))	? $login['companycode']	: '';
		$username		= (isset($login['username']))		? $login['username']	: '';
		$groupname		= (isset($login['groupname']))		? $login['groupname']	: '';
		$name			= (isset($login['name']))			? $login['name']		: '';
		define('COMPANYCODE', $companycode);
		define('USERNAME', $username);
		define('GROUPNAME', $groupname);
		define('NAME', $name);
	}

	public function getAccess($module_name, $module_group, $default_function) {
		$db			= new db();
		$url		= new url();
		$function = ($this->getPage()) ? $this->getPage() : $default_function;
	
		if ($this->checkAccessType(array('add', 'create', 'insert', 'save'), $function)) {
			$type = 'mod_add';
		} else if ($this->checkAccessType(array('view', 'get', 'load', 'retrieve', 'check'), $function)) {
			$type = 'mod_view';
		} else if ($this->checkAccessType(array('set', 'update', 'edit', 'apply'), $function)) {
			$type = 'mod_edit';
		} else if ($this->checkAccessType(array('delete', 'remove', 'cancel'), $function)) {
			$type = 'mod_delete';
		} else if ($this->checkAccessType(array('list', 'export'), $function)) {
			$type = 'mod_list';
		} else if ($this->checkAccessType(array('print'), $function)) {
			$type = 'mod_print';
		} else if ($this->checkAccessType(array('unpost'), $function)) {
			$type = 'mod_unpost';
		} else if ($this->checkAccessType(array('post'), $function)) {
			$type = 'mod_post';
		} else if ($this->checkAccessType(array('close','save','update','delete'), $function)) {
			$type = 'mod_close';
		} 
		$result		= $db->setTable(PRE_TABLE . '_module_access')
							->setFields('mod_add, mod_view, mod_edit, mod_delete, mod_list, mod_print, mod_post, mod_unpost','mod_close')
							->setWhere("groupname = '" . GROUPNAME . "' AND module_name = '$module_name'")
							->runSelect()
							->getRow();

		if ( ! isset($type)) {
			if (DEBUGGING) {
				echo "Function Name Error: " . $function;
				exit();
			} else {
				$url->redirect(BASE_URL);
			}
		} else if ( ! $result || $result->$type !== '1') {
			if (DEBUGGING) {
				echo "No Permission to Access this Module Task: " . ucfirst(str_replace('mod_', '', $type));
				exit();
			} else {
				$url->redirect(BASE_URL);
			}
		}
		if ($result) {
			define('MOD_ADD', ($result->mod_add === '1'));
			define('MOD_VIEW', ($result->mod_view === '1'));
			define('MOD_EDIT', ($result->mod_edit === '1'));
			define('MOD_DELETE', ($result->mod_delete === '1'));
			define('MOD_LIST', ($result->mod_list === '1'));
			define('MOD_PRINT', ($result->mod_print === '1'));
			define('MOD_POST', ($result->mod_post === '1'));
			define('MOD_UNPOST', ($result->mod_unpost === '1'));
			define('MOD_CLOSE', ($result->mod_unpost === '1'));
		}
		if (isset($type)) {
			define('MODULE_NAME', $module_name);
			define('MODULE_GROUP', $module_group);
			define('MODULE_TASK', $type);
		}

		$db->close();
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


		$locktime = $db->setTable(PRE_TABLE . '_users')
						->setFields('locktime')
						->setWhere("username = '" . USERNAME . "' AND locktime >= NOW()")
						->setLimit(1)
						->runSelect()
						->getRow();

		if ($locktime && $subfolder != 'login') {
			$date = new date();
			$fdate		= strtotime($locktime->locktime);
			$sdate		= strtotime($date->datetimeDBFormat());
			$locksec 	= $fdate - $sdate;
			if ($input->isPost) {
				header('Content-type: application/json');
				echo json_encode(array('locked' => true, 'locktime' => $date->datetimeFormat($locktime->locktime), 'locksec' => $locksec, 'baseurl' => BASE_URL));
				exit();
			} else {
				define('LOCKED', $date->datetimeFormat($locktime->locktime));
				define('LOCKED_SEC', $locksec);
			}
		}

		if (in_array($subfolder,array('login','register'))) {
			$this->module_folder = 'wc_core';
			$this->module_file = $subfolder;
			$this->module_function = 'index';
			define('MODULE_URL', BASE_URL . $subfolder);
			define('MODULE_TASK', $subfolder);
			define('MODULE_NAME', ucfirst($subfolder));
		} else if ($subfolder != '' && $subfolder != 'ajax') {
			$paths = $db->setTable(PRE_TABLE . '_modules')
						->setFields('module_name, module_group, module_link, folder, file, default_function')
						->setWhere("'" . SUB_FOLDER . "/' LIKE module_link AND active")
						->setOrderBy('length(module_link) DESC')
						->runSelect(false)
						->getRow();
			
			if ($paths) {
				$this->module_link = $paths->module_link;
				$this->getAccess($paths->module_name, $paths->module_group, $paths->default_function);
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
		$onload = new onload();
		$onload->init();
		define('MODULE_PATH', 'modules/' . $this->module_folder);
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
				if ($this->module_function == 'ajax') {
					if ($_SERVER['REQUEST_METHOD'] != 'POST') {
						echo 'show 404';
						exit();
					}
				}
				call_user_func_array(array($controller, $this->module_function), $this->args);
			} else if (DEBUGGING) {
				echo '<p><b>Unable to find Controller Function:</b> ' . $this->module_function . '()</p>';
				exit();
			} else {
				echo 'show 404';
				exit();
			}
		} else if (DEBUGGING) {
			echo '<p><b>Unable to find Controller File:</b> ' . $path . '</p>';
			exit();
		} else {
			echo 'show 404';
			exit();
		}
	}

}
$backend	= new backend();
$url		= new url();
$access		= new access();
$input		= new input();

define('MODAL', $input->post('modal'));

if (AUTO_LOGIN && ! function_exists('password_verify') && ! $access->isApanelUser() && $_SERVER['HTTP_HOST'] == 'localhost') {
	$session	= new session();
	$session->set('login', array('name' => 'Super 12 Admin', 'username' => 'superadmin', 'apanel_user' => true, 'companycode' => 'CID', 'groupname' => 'superadmin')); // Disable Login
	$access->loginUser();
	$url->redirect(FULL_URL);
}
if (SUB_FOLDER == 'logout') {
	$access->logoutUser();
	$url->redirect(BASE_URL);
} else if ($access->isApanelUser() || SUB_FOLDER == 'login' || SUB_FOLDER == 'register') {
	$backend->loadModule();
} else {
	if ($input->isPost) {
		header('Content-type: application/json');
		echo json_encode(
				array(
					'show_login_form' => true
				)
			);
	} else {
		$access->logoutUser();
		$redirect = (BASE_URL == FULL_URL) ? '' : '?redirect=' . base64_encode(FULL_URL);
		$url->redirect(BASE_URL . 'login' . $redirect);
	}
}