<?php

require_once '../includes/main.inc.php';

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
                	elseif ((!$pbs_running) && (!$analysis->check_finish_file())) {
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
