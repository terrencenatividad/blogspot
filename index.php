<?php
// OB START
if ( ! ob_start('ob_gzhandler')) {
	ob_start();
}
define('PRE_PATH', '');
define('PAGE_TYPE', 'frontend');
require_once PRE_PATH . 'settings.php';
require_once PRE_PATH . CORE_COMPONENTS . 'wc_site.php';

// OB END
while (ob_get_level() > 1 && ob_end_flush());
?>