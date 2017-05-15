<?php
class backend {

	private $module_link = '';
	private $module_folder = '';
	private $module_file = '';
	private $module_function = '';
	private $args = array();

	public function __construct() {
		// AUTOLOADER
		spl_autoload_register(function ($class) {
			if (file_exists("system/$class.php")) {
				require_once "system/$class.php";
			} else if (file_exists(MODULE_PATH . "/model/$class.php")) {
				require_once MODULE_PATH . "/model/$class.php";
			} else {
				$dir = new RecursiveDirectoryIterator('system');
				foreach (new RecursiveIteratorIterator($dir) as $file){
					if (strpos($file , $class . '.php') !== false) {
						include_once $file;
						break;
					}
				}
			}
		});
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

	public function getAccess($module_name, $default_function) {
		$db		= new db();
		$url	= new url();
		$function = ($this->getPage()) ? $this->getPage() : $default_function;

		if ($this->checkAccessType(array('add', 'create', 'insert', 'save', 'apply', 'retrieve'), $function)) {
			$type = 'mod_add';
		} else if ($this->checkAccessType(array('view', 'get', 'load'), $function)) {
			$type = 'mod_view';
		} else if ($this->checkAccessType(array('update', 'edit'), $function)) {
			$type = 'mod_edit';
		} else if ($this->checkAccessType(array('delete', 'remove', 'cancel'), $function)) {
			$type = 'mod_delete';
		} else if ($this->checkAccessType(array('list', 'export'), $function)) {
			$type = 'mod_list';
		} else if ($this->checkAccessType(array('print'), $function)) {
			$type = 'mod_print';
		}

		$result = $db->setTable('wc_module_access')
					->setFields('mod_add, mod_view, mod_edit, mod_delete, mod_list, mod_print')
					->setWhere("groupname = '" . GROUPNAME . "' AND module_name = '$module_name'")
					->runSelect()
					->getRow();

		if ( ! isset($type) || ($result && $result->$type !== '1')) {
			if (DEBUGGING) {
				echo "Function Name Error: " . $function;
				exit();
			} else {
				$url->redirect(BASE_URL);
			}
		} else if ($result) {
			define('MOD_ADD', ($result->mod_add === '1'));
			define('MOD_VIEW', ($result->mod_view === '1'));
			define('MOD_EDIT', ($result->mod_edit === '1'));
			define('MOD_DELETE', ($result->mod_delete === '1'));
			define('MOD_LIST', ($result->mod_list === '1'));
			define('MOD_PRINT', ($result->mod_print === '1'));
		}
		
		if (isset($type)) {
			define('MODULE_NAME', $module_name);
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
		$this->getSession();
		$db = new db();
		if (SUB_FOLDER == 'login') {
			$this->module_folder = 'wc_core';
			$this->module_file = 'login';
			$this->module_function = 'index';
		} else if (SUB_FOLDER != '') {
			$paths = $db->setTable('wc_modules')
						->setFields('module_name, module_link, folder, file, default_function', 'wc_modules')
						->setWhere("'" . SUB_FOLDER . "/' LIKE module_link AND active")
						->runSelect(false)
						->getRow();
			
			if ($paths) {
				$this->module_link = $paths->module_link;
				$this->getAccess($paths->module_name, $paths->default_function);
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
			$this->module_function = 'index';
		}
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
$session	= new session();
$url		= new url();
$access		= new access();
if (AUTO_LOGIN && ! $access->isApanelUser()) {
	$session->set('login', array('name' => 'Super 12 Admin', 'username' => 'superadmin', 'apanel_user' => true, 'companycode' => 'CID', 'groupname' => 'superadmin')); // Disable Login
	$url->redirect(FULL_URL);
}
if (SUB_FOLDER == 'logout') {
	$session->clean('login');
	$url->redirect(BASE_URL);
} else if ($access->isApanelUser() || SUB_FOLDER == 'login') {
	$backend->loadModule();
} else {
	$redirect = (BASE_URL == FULL_URL) ? '' : '?redirect=' . base64_encode(FULL_URL);
	$url->redirect(BASE_URL . 'login' . $redirect);
}