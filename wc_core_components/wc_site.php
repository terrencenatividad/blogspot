<?php
// CONFIG FILE
require_once 'config.php';
// HTTP -> HTTPS
$request_scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
if (defined('HTTPS') && HTTPS && $request_scheme == 'http' && $_SERVER['HTTP_HOST'] != 'localhost' && ! filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_IP)) {
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit();
}
define('FULL_URL', $request_scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
// GET SUB FOLDER
$sub_folder = str_replace('index.php', '', $_SERVER['PHP_SELF']);
if ($sub_folder == '/') {
	$sub_folder = '';
}
$request_uri = explode('?', str_replace(str_replace('index.php', '', $_SERVER['PHP_SELF']), '', $_SERVER['REQUEST_URI']));
$request_dir = explode('/', $request_uri[0]);
if (isset($_GET['/' . $request_uri[0]])) {
	unset($_GET['/' . $request_uri[0]]);
}
// ASSET LOADER
if ($request_dir[0] == 'assets' && isset($request_dir[1]) && isset($request_dir[2])) {
	$temp_dir = $request_dir;
	unset($temp_dir[0]);
	if (in_array($temp_dir[1], array('css', 'js', 'fonts', 'images'))) {
		$asset_path = PRE_PATH . CORE_COMPONENTS . 'assets/' . implode('/', $temp_dir);
		$asset_type = $temp_dir[1];
	} else {
		$asset_path = 'apanel/modules/' . $temp_dir[1] . '/' . PAGE_TYPE . '/assets/';
		unset($temp_dir[1]);
		$asset_path .= implode('/', $temp_dir);
		$asset_type = $temp_dir[2];
	}
	if ($asset_type == 'css') {
		header('Content-Type: text/css');
	} else if ($asset_type == 'js') {
		header('Content-Type: application/javascript');
	}
	if ( ! in_array($request_dir[count($request_dir) - 1], array('style.css', 'custom.css', 'script.js', 'site.js', 'global.js'))) {
		header('Cache-Control: public, max-age=31536000');
		header('Pragma: public, max-age=31536000');
	}
	if (file_exists($asset_path) || DEBUGGING) {
		readfile($asset_path);
		exit();
	} else {
		$this->show404();
	}
}
define('SUB_FOLDER', trim(implode('/' , $request_dir), '/'));
foreach ((isset($request_uri[1]) ? explode('&', $request_uri[1]) : array()) as $row) {
	$temp_ = explode('=', $row);
	$_GET[$temp_[0]] = isset($temp_[1]) ? $temp_[1] : '';
}
// DEFINE BASE URL
define('BASE_URL', $request_scheme . '://' . $_SERVER['HTTP_HOST'] . $sub_folder);
require_once PRE_PATH . CORE_COMPONENTS . 'system/wc_controller.php';
require_once PRE_PATH . CORE_COMPONENTS . 'system/wc_model.php';
require_once PRE_PATH . CORE_COMPONENTS . 'system/wc_view.php';
require_once 'wc_' . PAGE_TYPE . '.php';

