<?php

class blast extends stepa {

        ////////////////Private Variables//////////

	private $blast_input;
	private $sequence_max;
	private $blast_sequence_max;
	public $fail_file = "1.out.failed";
	private $num_pbs_jobs = 1;
	///////////////Public Functions///////////

        public function __construct($db,$id = 0) {
                $this->db = $db;

                if ($id) {
                        $this->load_generate($id);


                }
        }

        public function __destruct() {
        }

	public function get_submitted_max_sequences() { return $this->blast_sequence_max; }
	public function get_blast_input() { return $this->blast_input; }
	public function get_finish_file() { 
		return $this->get_output_dir() . "/" . $this->finish_file; 
	}
	public function get_fail_file() {
		return $this->get_output_dir() . "/" . $this->fail_file;
	}
	public function check_fail_file() {
		$results_path = functions::get_results_dir();
                $full_path = $results_path . "/" . $this->get_fail_file();
		return file_exists($full_path);

	}
	public function get_formatted_blast() {
		$width = 70;
		$break = "\r\n";
		$cut = true;
		return wordwrap($this->get_blast_input(),$width,$break,$cut);
	}
	
	public function create($email,$blast_input,$evalue,$max_seqs) {
		$errors = false;
                $message = "";
		$type = "BLAST";
		if (!$this->verify_email($email)) {
                        $errors = true;
                        $message .= "<br><b>Please enter a valid email address</b></br>";
                }

		if (($blast_input != "") && (!$this->verify_blast_input($blast_input))) {
			$errors = true;
                        $message .= "<br><b>Please enter a valid blast input</b></br>";


		}
		if (!$this->verify_evalue($evalue)) {
			$errors = true;
			$message .= "<br><b>Please enter a valid E-Value</b></br>";
		}
		if (!$this->verify_max_seqs($max_seqs)) {
			$errors = true;	
			$message .= "<br><b>Please enter a valid maximum number of sequences</b></br>";
		}
		if (!$errors) {
			$key = $key = $this->generate_key();
			$formatted_blast = $this->format_blast($blast_input);
			$insert_array = array('generate_key'=>$key,
					'generate_email'=>$email,
					'generate_type'=>$type,
					'generate_evalue'=>$evalue,
					'generate_blast_max_sequence'=>$max_seqs,
					'generate_blast'=>$formatted_blast
			);
			$result = $this->db->build_insert("generate",$insert_array);
                        if ($result) {
                                return array('RESULT'=>true,'id'=>$result,'MESSAGE'=>'Job successfully created');
                        }
                }
                return array('RESULT'=>false,'MESSAGE'=>$message);



	}


	
	public function email_complete() {
                $subject = "EFI-EST PFAM/Interpro Generation Complete";
                $to = $this->get_email();
		$from = functions::get_admin_email();
                $url = functions::get_web_root() . "/stepc.php";
                $full_url = $url . "?" . http_build_query(array('id'=>$this->get_id(),
                                'key'=>$this->get_key()));
                $message = "<br>Your EFI-EST PFAM/Interpro Generation is Complete\r\n";
                $message .= "<br>To view results, please go to\r\n";
		$message .= "<a href='" . $full_url . "'>" . $full_url . "</a>\r\n";
		$message .= "<br>EFI-EST ID: " . $this->get_id() . "\r\n";	
		$message .= "<br>Blast Sequence: \r\n";
		$message .= "<br>" . $this->get_formatted_blast() . "\r\n";
		$message .= "<br>E-Value: " . $this->get_evalue() . "\r\n";
		$message .= "<br>Maximum Blast Sequences: " . $this->get_submitted_max_sequences() . "\r\n";
		$message .= "<br><br>";
		$message .= "<br>This data will only be retained for " . functions::get_retention_days() . " days.\r\n";
		$message .= functions::get_email_footer();
                $headers = "From: " . $from . "\r\n";
                $headers .= "Content-Type: text/html; charset=iso-8859-1" . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from);
		

        }

	public function email_failed() {
                $subject = "EFI-EST PFAM/Interpro Generation Failed";
                $to = $this->get_email();
		$from = functions::get_admin_email();
                $url = functions::get_web_root();
                $message = "<br>Your EFI-EST PFAM/Interpro Generation Failed\r\n";
                $message .= "<br>Sorry it failed.\r\n";
                $message .= "<br>Please restart by going to <a href='" . $url . "'>" . $url . "</a>\r\n";
		$message .= "<br>EFI-EST ID: " . $this->get_id() . "\r\n";
                $message .= "<br>Blast Sequence: \r\n";
                $message .= "<br>" . $this->get_formatted_blast() . "\r\n";
                $message .= "<br>E-Value: " . $this->get_evalue() . "\r\n";
                $message .= "<br>Maximum Blast Sequences: " . $this->get_submitted_max_sequences() . "\r\n";
		$message .= "<br><br>";
		$message .= functions::get_email_footer();
                $headers = "From: " . $from . "\r\n";
                $headers .= "Content-Type: text/html; charset=iso-8859-1" . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from);
	


	}
	

	 public function email_started() {

                $subject = "EFI-EST PFAM/Interpro Generation Started";
                $to = $this->get_email();
                $from = functions::get_admin_email();
                $url = functions::get_web_root() . "/stepc.php";
                $full_url = $url . "?" . http_build_query(array('id'=>$this->get_id(),
                                'key'=>$this->get_key()));
                $message = "<br>Your EFI-EST PFAM/Interpro Generation has started running.\r\n";
                $message .= "<br>You will receive an email once the job has been completed.\r\n";
                $message .= "<br>EFI-EST ID: " . $this->get_id() . "\r\n";
		$message .= "<br>Blast Sequence: \r\n";
                $message .= "<br>" . $this->get_formatted_blast() . "\r\n";
                $message .= "<br>E-Value: " . $this->get_evalue() . "\r\n";
                $message .= "<br>Maximum Blast Sequences: " . $this->get_submitted_max_sequences() . "\r\n";

                $message .= "<br>" . functions::get_email_footer();
                $headers = "From: " . $from . "\r\n";
                $headers .= "Content-Type: text/html; charset=iso-8859-1" . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from);

        }


	public function run_job() {
		if ($this->available_pbs_slots()) {
			//Setup Directories
			//$job_dir = functions::get_results_dir() . "/" . $this->get_id();
			//$relative_output_dir = "output";
			//if (@file_exists($job_dir)) {
			//	functions::rrmdir($job_dir);
			//}
			//mkdir($job_dir);
		
                        //Setup Directories
                        $job_dir = functions::get_results_dir() . "/" . $this->get_id();
                        $relative_output_dir = "output";
                        $full_output_dir = $job_dir . "/" . $relative_output_dir;

                        if (@file_exists($job_dir)) {
                                functions::rrmdir($job_dir);
                        }
                        mkdir($job_dir);
                        //mkdir($full_output_dir);

                        chdir($job_dir);	
			$exec = "source /etc/profile\n";
                        $exec .= "module load " . functions::get_efi_module() . "\n";
                        $exec .= "module load " . functions::get_efidb_module() . "\n";
			$exec .= "blasthits-new.pl ";
			$exec .= "-seq  '" . $this->get_blast_input() . "' ";
			$exec .= "-evalue " . $this->get_evalue() . " ";
			$exec .= "-np " . functions::get_blasthits_processors() . " ";
			$exec .= "-queue " . functions::get_generate_queue() . " ";
			$exec .= "-memqueue " . functions::get_generate_queue() . " ";
			if ($this->get_submitted_max_sequences() != "") {
				$exec .= "-nresults " . $this->get_submitted_max_sequences() . " ";
			}
			else {
				$exec .= "-nresults " . functions::get_default_blast_seq() . " ";
			}
			$exec .= "-tmpdir " . $relative_output_dir;
        		$exit_status = 1;
	        	$output_array = array();
	        	$output = exec($exec,$output_array,$exit_status);
		        $output = trim(rtrim($output));
        		$pbs_job_number = substr($output,0,strpos($output,"."));
		        if ($pbs_job_number && !$exit_status) {
        		        $this->set_pbs_number($pbs_job_number);
                		$this->set_time_started();
	                	$this->set_status(__RUNNING__);
				$this->email_started();
        		        return array('RESULT'=>true,'PBS_NUMBER'=>$pbs_job_number,'EXIT_STATUS'=>$exit_status,'MESSAGE'=>'Job Successfully Submitted');
	        	}
	        	else {
        	        	return array('RESULT'=>false,'EXIT_STATUS'=>$exit_status,'MESSAGE'=>$output);
	        	}
		}
		else {
			return array('RESULT'=>false,'EXIT_STATUS'=>1,'MESSAGE'=>'Queue is full');
		}

}

	///////////////Private Functions///////////

        private function load_generate($id) {
                $sql = "SELECT * FROM generate WHERE generate_id='" . $id . "' ";
                $sql .= "LIMIT 1";
                $result = $this->db->query($sql);
                if ($result) {
                        $this->id = $id;
			$this->email = $result[0]['generate_email'];
			$this->key = $result[0]['generate_key'];
			$this->status = $result[0]['generate_status'];
			$this->evalue = $result[0]['generate_evalue'];
                        $this->time_created = $result[0]['generate_time_created'];
			$this->pbs_number = $result[0]['generate_pbs_number'];
			$this->time_started = $result[0]['generate_time_started'];
			$this->time_completed = $result[0]['generate_time_completed'];
                        

			$this->blast_input = $result[0]['generate_blast'];
                        $this->sequence_max = $result[0]['generate_sequence_max'];
                        $this->num_sequences = $result[0]['generate_num_sequences'];
			$this->blast_sequence_max = $result[0]['generate_blast_max_sequence'];
                }

        }

	private function verify_blast_input($blast_input) {
                $blast_input = strtolower($blast_input);
                $valid = 1;

                if (!strlen($blast_input)) {
                        $valid = 0;
                }
		if (strlen($blast_input) > 65534) {
			$valid = 0;
		}
		if (preg_match('/[^a-z-* \n\t\r]/',$blast_input)) {
                        $valid = 0;

                }
                return $valid;


        }


        private function format_blast($blast_input) {
                //$search = array(" ","\n","\r\n","\r","\t");
		$search = array("\r\n","\r");
                $replace = "\n";
                $formatted_blast = str_ireplace($search,$replace,$blast_input);
                return $formatted_blast;


        }

	protected function verify_max_seqs($max_seqs) {
		$valid = 0;
		if ($max_seqs == "") {
			$valid = 0;
		}
		elseif (!preg_match("/^[1-9][0-9]*$/",$max_seqs)) {
			$valid = 0;
		}
		elseif ($max_seqs > functions::get_max_blast_seq()) {
			$valid = 0;
		}
		else {
			$valid = 1;
		}
		return $valid;
	}

	private function available_pbs_slots() {
                $queue = new queue(functions::get_generate_queue());
                $num_queued = $queue->get_num_queued();
                $max_queuable = $queue->get_max_queuable();
                $num_user_queued = $queue->get_num_queued(functions::get_cluster_user());
                $max_user_queuable = $queue-> get_max_user_queuable();

                $result = false;
                if ($max_queuable - $num_queued < $this->num_pbs_jobs) {
                        $result = false;
                        $msg = "Generate ID: " . $this->get_id() . " - ERROR: Queue " . functions::get_generate_queue() . " is full.  Number in the queue: " . $num_queued;
                }
                elseif ($max_user_queuable - $num_user_queued < $this->num_pbs_jobs) {
                        $result = false;
                        $msg = "Generate ID: " . $this->get_id() . " - ERROR: Number of Queued Jobs for user " . functions::get_cluster_user() . " is full.  Number in the queue: " . $num_user_queued;
                }
                else {
                        $result = true;
                        $msg = "Generate ID: " . $this->get_id() . " - Number of queued jobs in queue " . functions::get_generate_queue() . ": " . $num_queued . ", Number of queued user jobs: " . $num_user_queued;
                }
                functions::log_message($msg);
                return $result;
        }


	private function get_email_info_txt() {
		return strip_tags($this->get_email_info_html());

	}

	private function get_email_info_html() {
                $message .= "<br>EFI-EST ID: " . $this->get_id() . "\r\n";
                $message .= "<br>Blast Sequence: \r\n";
                $message .= "<br>" . $this->get_formatted_blast() . "\r\n";
                $message .= "<br>E-Value: " . $this->get_evalue() . "\r\n";
                $message .= "<br>Maximum Blast Sequences: " . $this->get_submitted_max_sequences() . "\r\n";
                $message .= "<br><br>";
                $message .= "<br>This data will only be retained for " . functions::get_retention_days() . " days.\r\n";
                $message .= functions::get_email_footer();
		return $message;


	}
}

?>
