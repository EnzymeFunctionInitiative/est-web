<?php

chdir(dirname(__FILE__));
set_include_path(get_include_path() . ':../libs');
function __autoload($class_name) {
        if(file_exists("../libs/" . $class_name . ".class.inc.php")) {
                require_once $class_name . '.class.inc.php';
        }
}


include_once '../conf/settings.inc.php';
date_default_timezone_set(__TIMEZONE__);
$db = new db(__MYSQL_HOST__,__MYSQL_DATABASE__,__MYSQL_USER__,__MYSQL_PASSWORD__);

$sapi_type = php_sapi_name();
//If run from command line
if ($sapi_type != 'cli') {
        echo "Error: This script can only be run from the command line.\n";
}
else {
//	$queue = new queue("default");
	$queue = new queue("blacklight");
	echo "Jobs in Queue: " . $queue->get_num_queued() . "\n";
	echo "User Jobs in Queue: " . $queue->get_num_queued("efi_est") . "\n";
	echo "Max Queuable: " . $queue->get_max_queuable() . "\n";
	echo "Max User Queuable: " . $queue->get_max_user_queuable() . "\n";
}






?>
