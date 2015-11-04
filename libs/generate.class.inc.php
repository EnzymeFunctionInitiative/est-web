<?php

class generate extends stepa {

        ////////////////Private Variables//////////

	private $families = array();
	private $sequence_max;
	private $max_blast_failed = "accession.txt.failed";
	private $fraction;
	private $domain;
	public $subject = "EFI-EST PFAM/Interpro";
        //number of pbs jobs, not including blast jobs.
        private $num_pbs_jobs = 8;
        ///////////////Public Functions///////////

        public function __construct($db,$id = 0) {
                $this->db = $db;

                if ($id) {
                        $this->load_generate($id);


                }
        }

        public function __destruct() {
        }

	public function get_fraction() { return $this->fraction; }
	public function get_domain() { 
		if ($this->domain) {
			return "on";
		}
		return "off";
	
	}
	public function get_families() { return $this->families; }
	public function get_families_comma() { return implode(",", $this->get_families()); }
	public function get_sequence_max() { return $this->sequence_max; }

        public function get_max_blast_failed_file() {
                return $this->get_output_dir() . "/" . $this->max_blast_failed;
        }

	public function get_finish_file() { 
		return $this->get_output_dir() . "/" . $this->finish_file; 
	}

	
	//get_pfam_families()
	//returns an array of the pfam families or empty array otherwise
	public function get_pfam_families() {
		$pfam_families = array();
		foreach ($this->families as $family) {
			if (substr($family,0,2) == "PF") {
				array_push($pfam_families,$family);
			}

		}
		return $pfam_families;

	}

	//get_interpro_families()
	//returns an array of the interpro families or empty array otherwise
	public function get_interpro_families() {
		$interpro_families = array();
		foreach ($this->families as $family) {
			if (substr($family,0,3) == "IPR") {
				array_push($interpro_families,$family);
			}

		}
		return $interpro_families;

	}

        public function check_max_blast_failed_file() {
                $results_path = functions::get_results_dir();
                $full_path = $results_path . "/" . $this->get_max_blast_failed_file();
                return file_exists($full_path);

        }

        public function set_num_blast() {
                $results_path = functions::get_results_dir();
                if ($this->check_max_blast_failed_file()) {
                        $full_path = $results_path . "/" . $this->get_max_blast_failed_file();
                        $handle = fopen($full_path,"r");
                        $result = fgetcsv($handle,0," ");
                        $number_sequences = $result[3];
                        fclose($handle);
                        $this->set_sequence_max($number_sequences);
                }


        }

	public function create($email,$evalue,$families,$fraction,$domain) {
		$errors = false;
                $message = "";
		$type = "FAMILIES";
		if (!$this->verify_email($email)) {
                        $errors = true;
                        $message .= "<br><b>Please enter a valid email address</b></br>";
                }

		if (($families == "") || (!$this->verify_families($families))) {
			$errors = true;
			$message .= "<br><b>Please enter valid Interpro and PFam numbers</b></br>";
		}
		if (!$this->verify_evalue($evalue)) {
                        $errors = true;
                        $message .= "<br><b>Please enter a valid E-Value.</b></br>";

                }

		if (!$this->verify_fraction($fraction)) {
			$errors = true;
			$message .= "<br><b>Please enter an integer for the fraction option.</b></br>";
		}
		if (!$errors) {
			$domain_bool = 0;
			if ($domain == 'true') {
				$domain_bool = 1;
			}
			$key = $key = $this->generate_key();
			$formatted_families = $this->format_families($families);
			$insert_array = array('generate_key'=>$key,
					'generate_email'=>$email,
					'generate_type'=>$type,
					'generate_families'=>$formatted_families,
					'generate_evalue'=>$evalue,
					'generate_fraction'=>$fraction,
					'generate_domain'=>$domain_bool
			);
			$result = $this->db->build_insert("generate",$insert_array);
                        if ($result) {
                                return array('RESULT'=>true,'id'=>$result,'MESSAGE'=>'Job successfully created');
                        }
                }
                return array('RESULT'=>false,'MESSAGE'=>$message,'id'=>0);



	}

	
	

	public function set_sequence_max($num_seq) {
		$sql ="UPDATE generate ";
		$sql .= "SET generate_sequence_max='1',generate_num_sequences='" . $num_seq . "' ";
		$sql .= "WHERE generate_id='" . $this->get_id() . "' LIMIT 1";
		$result = $this->db->non_select_query($sql);
		if ($result) {
			$this->sequence_max = 1;
			$this->num_sequences = $num_seq;
		}

	}

	public function email_number_seq() {
		$boundary = uniqid('np');
                $subject = "EFI-EST PFAM/Interpro Number of Sequences too large";
                $to = $this->get_email();
                $url = functions::get_web_root();
                $from = functions::get_admin_email();
		$max_seq = functions::get_max_seq();

		//html email
		$message = "\r\n\r\n--" . $boundary . "\r\n"; 
                $message .= "Content-type:text/html;charset='iso-8859-1'\r\n\r\n";
		$message .= "<br>Your EFI_EST Pfam/InterPro Data Set\n";
		$message .= nl2br($this->get_job_info());
		$message .= "<br>This job will use " . number_format($this->get_num_sequences()) . ".";
		$message .= "This number is too large--you are limited to ";
		$message .=  number_format($max_seq) . " sequences.";
		$message .= "<br>Return to <a href='" . $url . "'>" . $url. "</a> ";
		$message .= "to start a new job with a different set of Pfam/InterPro families.";
		$message .= "<br>Or, if you would like to generate a network with the Pfam/InterPro";
		$message .= " families you have chosen, send an e-mail to efi@enzymefunction.org and";
		$message .= " request an account on Biocluster.  We will provide you with instructions";
		$message .= " to use our Unix scripts for network generation.  These scripts allow you";
		$message .= " to use a larger number of processors and, also, provide more options for";
		$message .= " generating the network files.  Your e-mail should provide a brief ";
		$message .= "description of your project so that the EFI can assist you.";
		$message .= "<br>";	
		$message .= nl2br(functions::get_email_footer());

		//plain text
		$message .= "\r\n\r\n--" . $boundary . "\r\n"; 
                $message .= "Content-type:text/plain;charset='iso-8859-1'\r\n\r\n";
		$message .= "Your EFI_EST Pfam/InterPro Data Set\n";
                $message .= $this->get_job_info();
                $message .= "This job will use " . number_format($this->get_num_sequences()) . ".";
                $message .= "This number is too large--you are limited to ";
                $message .=  number_format($max_seq) . " sequences.\r\n";
                $message .= "Return to " . $url;
                $message .= "to start a new job with a different set of Pfam/InterPro families.\r\n";
                $message .= "Or, if you would like to generate a network with the Pfam/InterPro";
                $message .= " families you have chosen, send an e-mail to efi@enzymefunction.org and";
                $message .= " request an account on Biocluster.  We will provide you with instructions";
                $message .= " to use our Unix scripts for network generation.  These scripts allow you";
                $message .= " to use a larger number of processors and, also, provide more options for";
                $message .= " generating the network files.  Your e-mail should provide a brief ";
                $message .= "description of your project so that the EFI can assist you.\r\n";
                $message .= "\r\n";    
                $message .= "\r\n" . functions::get_email_footer() . "\r\n";
		$message .= "\r\n\r\n--" . $boundary . "--\r\n";


                $headers = "MIME-Version: 1.0\r\n";
                $headers = "From: " . $from . "\r\n";
                $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from);

        }

	public function run_job() {
                if ($this->available_pbs_slots()) {

		        //Setup Directories
        		$job_dir = functions::get_results_dir() . "/" . $this->get_id();
	        	$relative_output_dir = "output";
	        	$full_output_dir = $job_dir . "/" . $relative_output_dir;

		        if (@file_exists($job_dir)) {
        		        functions::rrmdir($job_dir);
		        }
        		mkdir($job_dir);
	        	mkdir($full_output_dir);


	        	chdir($job_dir);
		        $pfam_families = implode(",",$this->get_pfam_families());
        		$interpro_families = implode(",",$this->get_interpro_families());
		        $exec = "source /etc/profile\n";
        		$exec .= "module load " . functions::get_efi_module() . "\n";
			$exec .= "module load " . functions::get_efidb_module() . "\n";
	        	$exec .= "generatedata.pl ";
	        	$exec .= "-np " . functions::get_cluster_procs() . " ";
		        $exec .= "-evalue " . functions::get_evalue() . " ";

        		if (strlen($interpro_families)) {
	                	$exec .= "-ipro " . $interpro_families . " ";
		        }
	        	if (strlen($pfam_families)) {
        	        	$exec .= "-pfam " . $pfam_families . " ";
	        	}
		        $exec .= "-tmp " . $relative_output_dir . " ";
        		$exec .= "-maxsequence " . functions::get_max_seq() . " ";
			$exec .= "-fraction " . $this->get_fraction() . " ";
			$exec .= "-evalue " . $this->get_evalue() . " ";
			$exec .= "-domain " . $this->get_domain() . " ";
		        $exec .= "-queue " . functions::get_generate_queue() . " ";
        		$exec .= "-memqueue " . functions::get_generate_queue() . " 2>&1";
        		$exit_status = 1;
		        $output_array = array();
        		$output = exec($exec,$output_array,$exit_status);
	        	$output = trim(rtrim($output));
	        	$pbs_job_number = substr($output,0,strpos($output,"."));
	
		        if ($pbs_job_number && !$exit_status) {
        		        $this->set_pbs_number($pbs_job_number);
                		$this->set_time_started();
				$this->email_started();
		                $this->set_status(__RUNNING__);
				return array('RESULT'=>true,'PBS_NUMBER'=>$pbs_job_number,'EXIT_STATUS'=>$exit_status,'MESSAGE'=>'Job Successfully Submitted');
	                }
        	        else {
                	        return array('RESULT'=>false,'EXIT_STATUS'=>$exit_status,'MESSAGE'=>$output_array[18]);
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
                        $this->key = $result[0]['generate_key'];
                        $this->email = $result[0]['generate_email'];
                        $this->pbs_number = $result[0]['generate_pbs_number'];
                        $this->evalue = $result[0]['generate_evalue'];
                        $this->time_created = $result[0]['generate_time_created'];
                        $this->status = $result[0]['generate_status'];
			$this->time_started = $result[0]['generate_time_started'];
                        $this->time_completed = $result[0]['generate_time_completed'];
                        $families = explode(",",$result[0]['generate_families']);
                        $this->families = $families;
                        $this->sequence_max = $result[0]['generate_sequence_max'];
                        $this->num_sequences = $result[0]['generate_num_sequences'];
			$this->fraction = $result[0]['generate_fraction'];
			$this->domain = $result[0]['generate_domain'];
                }

        }

	private function verify_families($families) {
		$family_array = explode(",",$families);
		$valid = 0;
		foreach ($family_array as $family) {
			$family = trim(rtrim($family));
			$family = strtolower($family);
			//Test if Interpro Number
			if ((substr($family,0,3) == "ipr") && (is_numeric(substr($family,3))) && (strlen(substr($family,3)) == 6)) {
				$valid = 1;
				
			}
			//Test if PFam Number
			elseif ((substr($family,0,2) == "pf") && (is_numeric(substr($family,2))) && (strlen(substr($family,2)) == 5)) {
				$valid = 1;
			}
			else {
				$valid = 0;
				break;
			}
		}
		return $valid;

	}

	private function format_families($families) {
		$search = array(" ");
		$replace = "";
		$formatted_families = str_ireplace($search,$replace,$families);
		$formatted_families = strtoupper($formatted_families);
		return $formatted_families;

	}

	private function available_pbs_slots() {
		$queue = new queue(functions::get_generate_queue());
                $num_queued = $queue->get_num_queued();
                $max_queuable = $queue->get_max_queuable();
                $num_user_queued = $queue->get_num_queued(functions::get_cluster_user());
                $max_user_queuable = $queue-> get_max_user_queuable();

                $result = false;
                if ($max_queuable - $num_queued < $this->num_pbs_jobs + functions::get_cluster_procs()) {
                        $result = false;
                        $msg = "Generate ID: " . $this->get_id() . " - ERROR: Queue " . functions::get_generate_queue() . " is full.  Number in the queue: " . $num_queued;
                }
                elseif ($max_user_queuable - $num_user_queued < $this->num_pbs_jobs + functions::get_cluster_procs()) {
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

	public function get_job_info() {

		$message = "EFI-EST ID: " . $this->get_id() . "\r\n";
                $message .= "PFAM/Interpro Families: \r\n";
                $message .= $this->get_families_comma() . "\r\n";
                $message .= "E-Value: " . $this->get_evalue() . "\r\n";
                $message .= "Fraction: " . $this->get_fraction() . "\r\n";
                $message .= "Enable Domain: " . $this->get_domain() . "\r\n";
		return $message;

	}


}
?>
