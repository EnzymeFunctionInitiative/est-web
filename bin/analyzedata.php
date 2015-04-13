<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
	//functions::log_message("Running " . basename($argv[0]));
	$new_analysis = functions::get_analysis($db,__NEW__);
	if (count($new_analysis)) {
		foreach ($new_analysis as $analysis) {
			sleep(1);
			$analysis_obj = new analysis($db,$analysis['analysis_id']);
			$result = $analysis_obj->run_job();
			if ($result['RESULT']) {
        	                $msg = "Analysis ID: " . $analysis['analysis_id'] .  " - PBS Number: " . $result['PBS_NUMBER'] . " - " . $result['MESSAGE'];
                	}
	                else {
        	                $msg = "Analysis ID: " . $analysis['analysis_id'] .  " - Error: " . $result['MESSAGE'];
                	}
	                functions::log_message($msg);

		}
	}
	else {
		$msg = "No New Analaysis";
		//functions::log_message($msg);
	}

}






?>
