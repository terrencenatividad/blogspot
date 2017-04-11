<?php
class wc_view {

	public $css = array();
	public $title = '';
	public $sub_title = '';
	public $header_active = '';

	public function addCss($css) {
		$this->css[] = 'assets/' . MODULE_FOLDER . '/' . PAGE_TYPE . '/assets/' . $css;
	}

	public function load($file, $data = array(), $enclosed = true) {
		if (is_array($data) || DEBUGGING) {
			extract($data);
		}
		$path = MODULE_PATH . '/' . PAGE_TYPE . '/view/' . $file . '.php';
		if (file_exists($path)) {
			// LOAD HEADER
			if ($enclosed) {
				$header_nav = $this->getNav();
				$header_active = $this->header_active;
				$include_css = $this->css;
				$page_title = $this->title;
				$page_subtitle = $this->enclose($this->sub_title, '<small>', '</small>');
				require_once (PAGE_TYPE == 'backend' ? '' : 'apanel/') . 'view/' . PAGE_TYPE . '_header.php';
			}
			// LOAD MODULE
			require_once $path;
			// LOAD FOOTER
			if ($enclosed) {
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
			$result = $db->setTable('wc_modules')
							->setFields('module_link, module_name, module_group, label')
							->setWhere('active AND show_nav')
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