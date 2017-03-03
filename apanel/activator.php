<?php
if (isset($_GET['folder'])) {
	$folder = $_GET['folder'];
	require_once 'system/db.php';
	$db = new db();
	require_once "modules/$folder/activate.php";
}
?>