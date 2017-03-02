<?php
// OB START
if ( ! ob_start('ob_gzhandler')) {
	ob_start();
}
define('PAGE_TYPE', 'frontend');
require_once 'apanel/wc_site.php';

// OB END
while (ob_get_level() > 1 && ob_end_flush());
?>