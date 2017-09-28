<?php
class wc_view {

	private $css			= array();
	private $js				= array();
	public $title			= '';
	public $sub_title		= '';
	public $header_active	= '';

	public function __construct() {
		$this->date = new date();
	}
	
	public function addCSS($css) {
		if (is_array($css)) {
			$this->css = array_merge($this->css, $css);
		} else {
			$this->css[] = $css;
		}
	}
	
	public function addJS($js) {
		if (is_array($js)) {
			$this->js = array_merge($this->js, $js);
		} else {
			$this->js[] = $js;
		}
	}

	public function load($file, $data = array(), $enclosed = true) {
		$this->header_active = ($this->header_active) ? BASE_URL . $this->header_active : MODULE_URL;
		if (is_array($data) || DEBUGGING) {
			extract($data);
		}
		$path = MODULE_PATH . '/' . PAGE_TYPE . '/view/' . $file . '.php';
		if (file_exists($path)) {
			// LOAD HEADER
			if ($enclosed && ( ! defined('MODAL') || (defined('MODAL') && ! MODAL))) {
				$header_nav		= $this->getNav();
				$header_active	= $this->header_active;
				$include_css	= $this->css;
				$include_js		= $this->js;
				$page_title		= $this->title;
				$page_subtitle	= $this->enclose($this->sub_title, '<small>', '</small>');
				require_once (PAGE_TYPE == 'backend' ? '' : 'apanel/') . 'view/' . PAGE_TYPE . '_header.php';
			}
			// LOAD MODULE
			require_once $path;
			// LOAD FOOTER
			if ($enclosed && ( ! defined('MODAL') || (defined('MODAL') && ! MODAL))) {
				require_once (PAGE_TYPE == 'backend' ? '' : 'apanel/') . 'view/' . PAGE_TYPE . '_footer.php';
			}
		} else if (DEBUGGING) {
			echo '<p><b>Unable to find View File:</b> ' . $path . '</p>';
		} else {
			echo 'show 404';
		}
	}

	public function getNav() {
		$nav = array();
		if (PAGE_TYPE == 'backend') {
			$db = new db();
			$result = $db->setTable(PRE_TABLE . '_modules m')
							->innerJoin(PRE_TABLE . '_module_access ma ON ma.module_name = m.module_name')
							->setFields('module_link, m.module_name, module_group, label')
							->setWhere('active AND show_nav AND (mod_add + mod_view + mod_edit + mod_delete + mod_list + mod_print) > 0')
							->setGroupBy('module_name')
							->setOrderBy('group_order, module_order, module_name')
							->runSelect(false)
							->getResult();
			foreach($result as $row) {
				$nav[$row->label][$row->module_group][$row->module_name] = $row->module_link;
			}
			$db->close();
		}
		return $nav;
	}

	public function enclose($val, $pre, $suf) {
		return (( ! empty($val)) ? $pre . $val . $suf : '');
	}

}