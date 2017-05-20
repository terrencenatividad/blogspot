<?php
// SETUP CONFIG
define('HTTPS', true);
define('DEBUGGING', true);
define('AUTO_LOGIN', true);
// PHP CONFIG
error_reporting(E_ALL);
ini_set('display_errors', (DEBUGGING ? 1 : 0));
ini_set('memory_limit', '64M');
// TIMEZONE CONFIG
$timezone = "Asia/Manila";
date_default_timezone_set($timezone);
// SESSION CONFIG
session_start();
session_cache_limiter('private');
session_cache_expire(480);
// DATABASE CONFIG
define('WC_HOSTNAME', '192.168.100.235');
define('WC_USERNAME', 'root');
define('WC_PASSWORD', '123456');
define('WC_DATABASE', 'webcore');