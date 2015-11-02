<?php

class analysis {

        ////////////////Private Variables//////////

        private $db; //mysql database object
        private $id;
	private $status;
	private $evalue;
	private $name;
	private $minimum;
	private $maximum;
	private $time_created;
	private $pbs_number;
	private $blast_id;
	private $generate_id;
	private $filter;
	private $time_started;
	private $time_completed;
	private $finish_file = "stats.tab.completed";
	private $sequence_file = "sequences.fa";
	protected $output_dir = "output";
	private $num_pbs_jobs = 16;
	private $filter_sequences;
        ///////////////Public Functions///////////

        public function __construct($db,$id = 0) {
                $this->db = $db;

                if ($id) {
                        $this->load_analysis($id);


                }
        }

        public function __destruct() {
        }

	public function get_status() { return $this->status; }
	public function get_id() { return $this->id; }
	public function get_generate_id() { return $this->generate_id; }
	public function get_evalue() { return $this->evalue; }
	public function get_min_length() { return $this->minimum; }
	public function get_max_length() { return $this->maximum; }
	public function get_time_created() { return $this->time_created; }
	public function get_pbs_number() { return $this->pbs_number; }
	public function get_name() { return $this->name; }
	public function get_filter() { return $this->filter; }
	public function get_time_started() { return $this->time_started; }
	public function get_time_completed() { return $this->time_completed; }
	public function get_unixtime_completed() { return strtotime($this->time_completed); }
	public function get_finish_file() { return $this->finish_file; }
	public function get_filter_sequences() { return $filter_sequences; }
        public function get_sequence_file() {
                return $this->sequence_file;
        }

        public function get_output_dir() {
                return $this->get_generate_id() . "/" . $this->output_dir;
        }

	public function set_num_sequences_post_filter() {
		$num_seq = $this->get_num_sequences_post_filter();

		$sql = "UPDATE analysis ";
		$sql .= "SET analysis_filter_sequences='" . $num_seq . "' ";
		$sql .= "WHERE analysis_id='" . $this->get_id() . " LIMIT 1";
		$result = $this->db->non_select_query($sql);
                if ($result) {
                        $this->filter_sequences = $num_seq;
                        return true;
                }
                return false;

	}		


	
        public function get_num_sequences_post_filter() {
		$root_dir = functions::get_results_dir();
		$directory = $root_dir . "/" . $this->get_output_dir() . "/" . $this->get_network_dir();
		$full_path = $directory . "/" . $this->get_sequence_file();
		$num_seq = 0;
                if (file_exists($full_path)) {

                        $exec = "cat " . $full_path . " | grep '>' | wc -l";
                        $output = exec($exec);
                        $output = trim(rtrim($output));
                        list($num_seq,) = explode(" ",$output);

                }
                return $num_seq;



        }

	public function create($generate_id,$evalue,$name,$minimum,$maximum) {
		$errors = false;
                $message = "";		
		$filter = "eval";
		if (!$this->verify_length($minimum,$maximum)) {
			$message = "<br><b>Please verify the minimum and maximum lengths.</b>";
			$errors = true;

		}
		if (!$this->verify_network_name($name)) {
			$message .= "<br><b>Please verify the network name.</b>";
			$message .= "<br><b>It can contain only letters, numbers, dash, and underscore.</b>";
			$errors = true;
		}
		if (!$this->verify_evalue($evalue)) {
			$message .= "<br><b>Please verify the alignment score.</b>";
			$errors = true;	
		}
		if (!$errors) {
			$insert_array = array('analysis_generate_id'=>$generate_id,
					'analysis_evalue'=>$evalue,
					'analysis_name'=>$name,
					'analysis_min_length'=>$minimum,
					'analysis_max_length'=>$maximum,
					'analysis_filter'=>$filter
			);
			$result = $this->db->build_insert("analysis",$insert_array);
                        if ($result) {
                                return array('RESULT'=>true,'id'=>$result);
                        }
                }
                return array('RESULT'=>false,'MESSAGE'=>$message);



	}
        public function set_pbs_number($pbs_number) {
                $sql = "UPDATE analysis SET analysis_pbs_number='" . $pbs_number . "' ";
                $sql .= "WHERE analysis_id='" . $this->get_id() . "'";
                $this->db->non_select_query($sql);
                $this->pbs_number = $pbs_number;


        }

	public function set_time_started() {
                $current_time = date("Y-m-d H:i:s",time());
                $sql = "UPDATE analysis SET analysis_time_started='" . $current_time . "' ";
                $sql .= "WHERE analysis_id='" . $this->get_id() . "' LIMIT 1";
                $this->db->non_select_query($sql);
                $this->time_started = $current_time;

        }

        public function set_time_completed() {
		$current_time = date("Y-m-d H:i:s",time());
                $sql = "UPDATE analysis SET analysis_time_completed='" . $current_time . "' ";
                $sql .= "WHERE analysis_id='" . $this->get_id() . "' LIMIT 1";
                $this->db->non_select_query($sql);
                $this->time_completed = $current_time;

        }

        public function check_pbs_running() {

                $output;
                $exec = "qstat " . $this->get_pbs_number() . " 2> /dev/null | grep " . $this->get_pbs_number();
                exec($exec,$output);
                if (count($output) == 1) {
                        return true;
                }
                else {
                        return false;
                }

        }
	
	public function check_finish_file() {
                $directory = functions::get_results_dir() . "/".  $this->get_output_dir();
		$directory .= "/" . $this->get_network_dir();
                $full_path = $directory . "/" . $this->get_finish_file();
                return file_exists($full_path);

        }

        public function set_status($status) {

                $sql = "UPDATE analysis ";
                $sql .= "SET analysis_status='" . $status . "' ";
                $sql .= "WHERE analysis_id='" . $this->get_id() . "' LIMIT 1";
                $result = $this->db->non_select_query($sql);
                if ($result) {
                        $this->status = $status;
                }

        }

	public function get_network_stats() {
		$results_dir = functions::get_results_dir();
		$file = $results_dir . "/" . $this->get_output_dir();
		$file .= "/" . $this->get_network_dir() . "/stats.tab";
		$file_handle = @fopen($file,"r") or die("Error opening stats.tab\n");
               	$i = 0; 
		$stats_array = array();
		$keys = array('File','Nodes','Edges','Size');
		$row = 0;
		while (($data = fgetcsv($file_handle,0,"\t")) !== FALSE) {

			if ($row == 1) {
				$result = array_splice($data,1,1);
				array_push($stats_array,array_combine($keys,$data));
			}
			elseif ($row > 1) {
				
				array_push($stats_array,array_combine($keys,$data));
			}
			$row++;
                }
		fclose($file_handle);
		return $stats_array;
	}


	public function download_network($file) {
		$root_dir = functions::get_results_dir();	
		$directory = $root_dir . "/" . $this->get_output_dir() . "/" . $this->get_network_dir();
		$full_path = $directory . "/" . $file;
		if (file_exists($full_path)) {
			header('Pragma: public');
	                header('Expires: 0');
        	        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Type: application-download');
			//header('COntent-Type: text/xml');
	                header('Content-Disposition: attachment; filename="' . $file . '"');
			header('Content-Length: ' . filesize($full_path));
			ob_clean();
        	        readfile($full_path);
		}
		else {
			return false;
		}


	}


	public function email_complete() {
	
		$stepa = new stepa($this->db,$this->get_generate_id());	
		$boundary = uniqid('np');
                $subject = "EFI-EST PFAM/Interpro Analysis Complete";
		$from = functions::get_admin_email();
                $to = $stepa->get_email();
                $url = functions::get_web_root() . "/stepe.php";
                $full_url = $url . "?" . http_build_query(array('id'=>$this->get_generate_id(),
                                'key'=>$stepa->get_key(),'analysis_id'=>$this->get_id()));

		//html email
		$message = "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/html;charset='iso-8859-1'\r\n\r\n";
                $message .= "<br>Your EFI-EST PFAM/Interpro Analysis is Complete\r\n";
		$message .= "<br>To view results, please go to\r\n";
                $message .= "<a href='" . $full_url . "'>" . $full_url . "</a>\r\n";
		$message .= "<br><br>" . nl2br($this->get_stepa_job_info());
		$message .= "<br>" . nl2br($this->get_job_info());
		$message .= "<br>\r\n";
		$message .= "<br>This data will only be retained for " . functions::get_retention_days() . " days.\r\n";
                $message .= "<br>" . nl2br(functions::get_email_footer());

		//plain text email
		$message .= "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/plain;charset='iso-8859-1'\r\n\r\n";
		$message .= "Your EFI-EST PFAM/Interpro Analysis is Complete\r\n";
                $message .= "To view results, please go to\r\n";
                $message .= $full_url . "\r\n";
                $message .= $this->get_stepa_job_info();
		$message .= $this->get_job_info();
                $message .= "\r\n";
                $message .= "This data will only be retained for " . functions::get_retention_days() . " days.\r\n";
                $message .= "\r\n";
                $message .= functions::get_email_footer() . "\r\n";
		$message .= "\r\n\r\n--" . $boundary . "--\r\n";

		//headers
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "From: " . $from . "\r\n";
                $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from);

        }

        public function email_failed($from_email,$web_root,$footer) {
		$generate = new stepa($this->db,$this->get_generate_id());
                $subject = "EFI-EST PFAM/Interpro Analysis Failed";
                $to = $generate->get_email();
                $url = $web_root . "/stepa.php";

		
                $message = "<br>Your EFI-EST PFAM/Interpro Generation Failed\r\n";
                $message .= "<br>Sorry it failed.\r\n";
                $message .= "Please restart by going to <a href='" . $url . "'>" . $url . "</a>\r\n";

                if ($generate->get_blast_input() != "") {
                        $message .= "<br>Blast Input: \r\n";
                        $message .= $generate->get_formatted_blast();

                }
                elseif (count($generate->get_families())) {
                        $message .= "<br>PFAM/Interpro Families: ";
                        $message .= implode(", ",$generate->get_families());

                }
		$message .= nl2br($this->get_job_info());
		$message .= "<br><br>";
                $message .= $footer;

                $headers = "From: " . $from_email . "\r\n";
                $headers .= "Content-Type: text/html; charset=iso-8859-1" . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from_email);



        }

	public function email_started() {

                $stepa = new stepa($this->db,$this->get_generate_id());
		$boundary = uniqid('np');
                $subject = "EFI-EST PFAM/Interpro Analysis Started";
                $from = functions::get_admin_email();
                $to = $stepa->get_email();
		//html email
		$message = "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/html;charset='iso-8859-1'\r\n\r\n";
                $message .= "<br>Your EFI-EST PFAM/Interpro Analysis has started\r\n";
                $message .= "<br>You will receive an email once it is completed.\r\n";
		$message .= "<br><br>" . nl2br($this->get_stepa_job_info());
		$message .= nl2br($this->get_job_info());
                $message .= "<br>" . nl2br(functions::get_email_footer()) . "\r\n";

		//plain text email
		$message .= "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/plain;charset='iso-8859-1'\r\n\r\n";
		$message .= "Your EFI-EST PFAM/Interpro Analysis has started\r\n";
                $message .= "You will receive an email once it is completed.\r\n\r\n";
                $message .= $this->get_stepa_job_info();
                $message .= $this->get_job_info();
                $message .= "\r\n";
                $message .= functions::get_email_footer() . "\r\n";
		$message .= "\r\n\r\n--" . $boundary . "--\r\n";

		//headers
		$headers = "MIME-Version: 1.0\r\n";
                $headers .= "From: " . $from . "\r\n";
                $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from);


        }

	public function run_job() {
                if ($this->available_pbs_slots()) {

		        //Setup Directories
        		$job_dir = functions::get_results_dir() . "/" . $this->get_generate_id();
	        	$relative_output_dir = "output";
	        	$full_output_dir = $job_dir . "/output/" .  $this->get_network_dir();
		        if (@file_exists($full_output_dir)) {
        		        functions::rrmdir($full_output_dir);
		        }
			$current_dir = getcwd();
			if (file_exists($job_dir)) {
				chdir($job_dir);
		        	$exec = "source /etc/profile\n";
			        $exec .= "module load " . functions::get_efi_module() . "\n";
				$exec .= "module load " . functions::get_efidb_module() . "\n";
        			$exec .= "analyzedata.pl ";
			        $exec .= "-maxlen " . $this->get_max_length() . " ";
        			$exec .= "-minlen " . $this->get_min_length() . " ";
	        		$exec .= "-filter " . $this->get_filter() . " ";
	        		$exec .= "-title " . $this->get_name() . " ";
			        $exec .= "-minval " . $this->get_evalue() . " ";
        			$exec .= "-tmp " . $relative_output_dir . " ";
			        $exec .= "-queue " . functions::get_analyse_queue() . " 2>&1 ";
        			$exit_status = 1;
		        	$output_array = array();
		        	$output = exec($exec,$output_array,$exit_status);
				chdir($current_dir);
		        	$output = trim(rtrim($output));
	        		$pbs_job_number = substr($output,0,strpos($output,"."));
		        	if ($pbs_job_number && !$exit_status) {
        			        $this->set_pbs_number($pbs_job_number);
	                		$this->set_time_started();
	                		$this->set_status(__RUNNING__);
					$this->email_started();
					return array('RESULT'=>true,
						'PBS_NUMBER'=>$pbs_job_number,
						'EXIT_STATUS'=>$exit_status,
						'MESSAGE'=>'Job Successfully Submitted');
		                }
        	        	else {
        		                return array('RESULT'=>false,
						'EXIT_STATUS'=>$exit_status,
						'MESSAGE'=>$output_array[7]);
		                }

			}
			else {
				return array('RESULT'=>false,'EXIT_STATUS'=>1,'MESSAGE'=>'Directory ' . $job_dir . ' does not exist');
			}
	
			}
		else {
			return array('RESULT'=>false,'EXIT_STATUS'=>1,'MESSAGE'=>'Queue is full');
		}
	}

	///////////////Private Functions///////////


	private function load_analysis($id) {
		$sql = "SELECT * FROM analysis WHERE analysis_id='" . $id . "' ";
		$sql .= "LIMIT 1";
		$result = $this->db->query($sql);
		if ($result) {
			$this->id = $id;
			$this->generate_id = $result[0]['analysis_generate_id'];
			$this->pbs_number = $result[0]['analysis_pbs_number'];
			$this->evalue = $result[0]['analysis_evalue'];
			$this->name = $result[0]['analysis_name'];
			$this->minimum = $result[0]['analysis_min_length'];
			$this->maximum = $result[0]['analysis_max_length'];
			$this->time_created = $result[0]['analysis_time_created'];
			$this->status = $result[0]['analysis_status'];
			$this->filter = $result[0]['analysis_filter'];
			$this->time_started = $result[0]['analysis_time_started'];
			$this->time_completed = $result[0]['analysis_time_completed'];
			$this->filter_sequences = $result[0]['analysis_filter_sequences'];
		}

	}

	public function get_network_dir() {
		$path = $this->get_filter() . "-" . $this->get_evalue();
		$path .= "-" . $this->get_min_length() . "-" . $this->get_max_length();
		return $path;

	}

	private function verify_length($min,$max) {

		$result = true;
		if (!$this->is_integer($min)) {
			$result = false;
		}
		elseif (!$this->is_integer($max)) {
			$result = false;
		}
		elseif ($min >= $max) {
			$result = false;
		}
		return $result;

	}

        private function verify_network_name($name) {
                $result = true;
                if ($name == "") {
                        $result = false;
                }
                if (!preg_match('/^[A-Za-z0-9_-]+$/',$name)) {
                        $result = false;
                }
                return $result;

        }
	
	private function verify_evalue($evalue) {
		$result = true;
		if ($evalue == "") {
			$result = false;
		}
		elseif (!$this->is_integer($evalue)) {
			$result = false;
		}
		return $result;

	}

	private function is_integer($value) {
		if (preg_match('/^\d+$/',$value)) {
			return true;
		}
		return false;

	}

	private function available_pbs_slots() {
                $queue = new queue(functions::get_analyse_queue());
                $num_queued = $queue->get_num_queued();
                $max_queuable = $queue->get_max_queuable();
                $num_user_queued = $queue->get_num_queued(functions::get_cluster_user());
                $max_user_queuable = $queue-> get_max_user_queuable();

                $result = false;
                if ($max_queuable - $num_queued < $this->num_pbs_jobs) {
                        $result = false;
                        $msg = "Analysis ID: " . $this->get_id() . " - ERROR: Queue " . functions::get_analyse_queue() . " is full.  Number in the queue: " . $num_queued;
                }
                elseif ($max_user_queuable - $num_user_queued < $this->num_pbs_jobs) {
                        $result = false;
                        $msg = "Analysis ID: " . $this->get_id() . " - ERROR: Number of Queued Jobs for user " . functions::get_cluster_user() . " is full.  Number in the queue: " . $num_user_queued;
                }
                else {
                        $result = true;
                        $msg = "Analysis ID: " . $this->get_id() . " - Number of queued jobs in queue " . functions::get_analyse_queue() . ": " . $num_queued . ", Number of queued user jobs: " . $num_user_queued;
                }
                functions::log_message($msg);
                return $result;
        }

	private function get_job_info() {
		$message = "Minimum Length: " . $this->get_min_length() . "\r\n";
                $message .= "Maximum Length: " . $this->get_max_length() . "\r\n";
                $message .= "Alignment Score: " . $this->get_evalue() . "\r\n";
                $message .= "Network Name: " . $this->get_name() . "\r\n";
		return $message;

	}

	private function get_stepa_job_info() {
		$stepa = new stepa($this->db,$this->get_generate_id());
		$job_type = $stepa->get_type();
		switch ($job_type) {
		
			case "BLAST":
                	        $stepa = new blast($this->db,$this->get_generate_id());
				break;
                
                	case "FAMILIES": 
	                        $stepa = new generate($this->db,$this->get_generate_id());
				break;			

                	case "FASTA":
	                        $stepa = new fasta($this->db,$this->get_generate_id());
				break;
		}
		$message = $stepa->get_job_info();
		return $message;
	}
}

?>
