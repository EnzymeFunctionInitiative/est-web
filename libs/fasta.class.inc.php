<?php

class fasta extends stepa {

        ////////////////Private Variables//////////

	private $families = array();
	private $sequence_max;
	private $max_blast_failed = "accession.txt.failed";
        //number of pbs jobs, not including blast jobs.
        private $num_pbs_jobs = 8;
	private $change_fasta_exec = "formatcustomfasta.pl";
	private $userdat_file = "output.dat";
	private $uploaded_filename;
	private $fraction;
	public $subject = "EFI-EST FASTA";

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
	public function get_families() { return $this->families; }
	public function get_families_comma() { return implode(",", $this->get_families()); }
	public function get_sequence_max() { return $this->sequence_max; }
	public function get_fasta_file() { return $this->get_id() . ".fasta"; }
	public function get_change_fasta_exec() { return $this->change_fasta_exec; }
	public function get_userdat_file() { return $this->userdat_file; }
	public function get_userdat_file_path() {
		return functions::get_results_dir() . "/" . $this->get_id() . "/output/" . $this->get_userdat_file();
	}
	public function get_uploaded_filename() { return $this->uploaded_filename; }
	public function get_full_fasta_file_path() {
		return functions::get_results_dir() . "/" . $this->get_id() . "/" . $this->get_fasta_file();
	}
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

	public function create($email,$evalue,$families,$tmp_fastafile,$uploaded_filename,$fraction,$program) {
		$errors = false;
                $message = "";
		$type = "FASTA";
		if (!$this->verify_email($email)) {
                        $errors = true;
                        //$message .= "<br>Please enter a valid email address</br>";
                }

		if (($families != "") && (!$this->verify_families($families))) {
			$errors = true;
			$message .= "<br><b>Please enter valid Interpro and PFam numbers</b></br>";
		}
		if (!$this->verify_fraction($fraction)) {
			$errors = true;
			$message .= "<br><b>Please enter a valid fraction</b></br>";
		}
		if (!$this->verify_evalue($evalue)) {
			$errors = true;
			$message .= "<br><b>Please enter a valid E-Value</b></br>";
	
		}
		if (!$this->verify_fasta($uploaded_filename)) {
			$errors = true;
			$message .= "<br><b>Please upload a valid fasta file.  The file extension must be .txt, .fasta, or .fa</b></br>";
		}
		if (!$errors) {

			$key = $key = $this->generate_key();
			$formatted_families = $this->format_families($families);
			$insert_array = array('generate_key'=>$key,
					'generate_email'=>$email,
					'generate_type'=>$type,
					'generate_families'=>$formatted_families,
					'generate_evalue'=>$evalue,
					'generate_fasta_file'=>$uploaded_filename,
					'generate_fraction'=>$fraction,
					'generate_program'=>$program
			);
			$result = $this->db->build_insert("generate",$insert_array);
			
			if (!$this->move_upload_file($tmp_fastafile,$result)) {
				$errors = true;
				$message = "Error moving file";
			}
                        elseif ($result) {
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
			if (!$this->copy_fasta_to_output()) {
				$this->set_status(__FAILED__);
				return array('RESULT'=>false,'MESSAGE'=>'Fasta file did not copy');
				
			}

	        	chdir($job_dir);
		        
			$pfam_families = implode(",",$this->get_pfam_families());
        		$interpro_families = implode(",",$this->get_interpro_families());
		        $exec = "source /etc/profile\n";
        		$exec .= "module load " . functions::get_efi_module() . "\n";
			$exec .= "module load " . functions::get_efidb_module() . "\n";
	        	$exec .= "generatedata.pl ";
	        	$exec .= "-np " . functions::get_cluster_procs() . " ";
		        $exec .= "-evalue " . functions::get_evalue() . " ";
			$exec .= "-blast " . strtolower($this->get_program()) . " ";
        		if (strlen($interpro_families)) {
	                	$exec .= "-ipro " . $interpro_families . " ";
		        }
	        	if (strlen($pfam_families)) {
        	        	$exec .= "-pfam " . $pfam_families . " ";
	        	}
			$exec .= "-userfasta " . $this->get_full_fasta_file_path() . " ";
		        $exec .= "-tmp " . $relative_output_dir . " ";
        		$exec .= "-maxsequence " . functions::get_max_seq() . " ";
			$exec .= "-evalue " . $this->get_evalue() . " ";
			$exec .= "-fraction " . $this->get_fraction() . " ";
		        $exec .= "-queue " . functions::get_generate_queue() . " ";
        		$exec .= "-memqueue " . functions::get_generate_queue() . " ";
			$exec .= "-userdat " . $relative_output_dir . "/" . $this->get_userdat_file() . " 2>&1";
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

	public function view_fasta_file() {
                $filename = $this->get_id() . ".fasta";
                $full_path = functions::get_uploads_dir() . "/" . $filename;
                $data = file_get_contents($full_path);
                $data_array = explode("\n", $data);
                $output = "";
                while (list($var, $val) = each($data_array)) {
                        ++$var;
                        $val = trim($val);
                        $output .= "<br>" . $val;
                }
                return $output;

	}
	public function download_fasta_file() {
                $filename = $this->get_id() . ".fasta";
                $full_path = functions::get_uploads_dir() . "/" . $filename;
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($full_path) . "\""); 
		readfile($full_path);

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
	                if ($result[0]['generate_families'] != "") {
				$families = explode(",",$result[0]['generate_families']);
        		        $this->families = $families;
			}
                        $this->sequence_max = $result[0]['generate_sequence_max'];
                        $this->num_sequences = $result[0]['generate_num_sequences'];
			$this->uploaded_filename = $result[0]['generate_fasta_file'];
			$this->fraction = $result[0]['generate_fraction'];
			$this->program = $result[0]['generate_program'];
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


	private function move_upload_file($tmp_fastafile,$id) {
		$full_path = functions::get_uploads_dir() . "/" . $id . ".fasta";
		$result = move_uploaded_file($tmp_fastafile,$full_path);
		return $result;

	}

	private function copy_fasta_to_output() {
		$filename = $this->get_id() . ".fasta";
		$start_path = functions::get_uploads_dir() . "/" . $filename;
		$end_path = functions::get_results_dir() . "/" . $this->get_id() . "/" . $filename;
		$dat_path = $this->get_userdat_file_path();
		if ((file_exists($start_path)) && (file_exists(dirname($end_path)))) {
			return $this->fix_fasta($start_path,$end_path,$dat_path);
		}
		return false;
	}

	private function fix_fasta($start_path,$end_path,$dat_path) {
		$exec = "source /etc/profile\n";
		$exec .= "module load " . functions::get_efi_module() . "\n";
		$exec .= $this->get_change_fasta_exec() . " -in " .  $start_path;
		$exec .= " -out " . $end_path;
		$exec .= " -dat " . $dat_path;
		$exit_status = 1;
		$output_array = array();
		$output = exec($exec,$output_array,$exit_status);
		if (!$exit_status) {
			return true;
		}
		return false;

	}

	public function get_job_info($eol = "\r\n") {
		$message = "EFI-EST ID: " . $this->get_id() . $eol;
                $message .= "Uploaded Fasta File: " . $this->get_uploaded_filename() . $eol;
		if (count($this->get_families())) {
			 $message .= "PFAM/Interpro Families: ";
                         $message .= $this->get_families_comma() . $eol;
		}
                $message .= "E-Value: " . $this->get_evalue() . $eol;
                $message .= "Fraction: " . $this->get_fraction() . $eol;
		$message .= "Selected Program: " . $this->get_program() . $eol;
                return $message;

        }

	private function verify_fasta($filename) {
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$valid = true;
		if (!in_array($ext,functions::get_valid_fasta_filetypes())) {
			$valid = false;	

		}
		return $valid;


	}
}

?>
