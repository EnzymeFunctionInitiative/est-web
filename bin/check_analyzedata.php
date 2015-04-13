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
	//functions::log_message("Running " . basename($argv[0]));
	$running_jobs = functions::get_analysis($db,__RUNNING__);
	if (count($running_jobs)) {
	        foreach ($running_jobs as $job) {
			$analysis = new analysis($db,$job['analysis_id']);
			$pbs_running = $analysis->check_pbs_running();
        	        if ((!$pbs_running) && ($analysis->check_finish_file())) {
                	        $analysis->set_status(__FINISH__);
                        	$analysis->set_time_completed();
	                        $analysis->email_complete();
				$msg = "Analaysis ID: " . $job['analysis_id'] . " - Job Completed Successfully";
				functions::log_message($msg);	
	
        	        }
                	if ((!$pbs_running) && (!$analysis->check_finish_file())) {
                        	$analysis->set_status(__FAILED__);
	                        $analysis->set_time_completed();
        	                $analysis->email_failed();
				$msg = "Analaysis ID: " . $job['analysis_id'] . " - Job Failed";
				functions::log_message($msg);
	                }
	        }
	}
	else {
		//$msg = "No Running Analysis";
		//functions::log_message($msg);
	}

}






?>
