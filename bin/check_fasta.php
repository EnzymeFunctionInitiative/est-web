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
	$running_jobs = functions::get_fastas($db,__RUNNING__);
	if (count($running_jobs)) {
	        foreach ($running_jobs as $job) {

			$fasta = new fasta($db,$job['generate_id']);
			print_r($fasta);
			$blast_failed_exists = $fasta->check_max_blast_failed_file();
                        $finish_file_exists = $fasta->check_finish_file();
                        $job_running = $fasta->check_pbs_running();

                        if ((!$job_running) && ($finish_file_exists)) {
                                $fasta->set_status(__FINISH__);
                                $fasta->set_time_completed();
                                $num_seq = $fasta->get_num_sequence_from_file();
                                $fasta->set_num_sequences($num_seq);
                                $fasta->email_complete();
                                $msg = "Generate ID: " . $job['generate_id'] . " - Job Completed Successfully";
                                functions::log_message($msg);
                        }
                        elseif ((!$job_running) && ($blast_failed_exists)) {
                                $fasta->set_status(__FAILED__);
                                $fasta->set_num_blast();
                                $fasta->set_time_completed();
                                $fasta->email_number_seq();
                                $msg = "Generate ID: " . $job['generate_id'] . " - Job Failed - Max Number of Sequences";
                                functions::log_message($msg);
                        }

	        }
	}
	else {
		//functions::log_message('No Running Fasta Jobs');	
	}

}






?>
