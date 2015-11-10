<?php

class stepa {

        ////////////////Private Variables//////////

        protected $db; //mysql database object
        protected $id;
	protected $email;
	protected $key;
	protected $status;
	protected $evalue;
	protected $time_created;
	protected $pbs_number;
	protected $time_completed;
	protected $time_started;
	protected $finish_file = "1.out.completed";
	protected $output_dir = "output";
	protected $type;
	protected $num_sequences;
	protected $accession_file = "allsequences.fa";
	private $num_pbs_jobs = 1;

	//private $alignment_length = "r_quartile_align.png";
	//private $length_histogram = "r_hist_length.png";
	//private $percent_identity = "r_quartile_perid.png";
	//private $number_of_edges = "r_hist_edges.png";
	
	private $alignment_length = "alignment_length.png";
	private $length_histogram = "length_histogram.png";
	private $percent_identity = "percent_identity.png";
	private $number_of_edges = "number_of_edges.png";
        ///////////////Public Functions///////////

        public function __construct($db,$id = 0) {
                $this->db = $db;

                if ($id) {
                        $this->load_generate($id);


                }
        }

        public function __destruct() {
        }
	public function get_type() { return $this->type; }
	public function get_status() { return $this->status; }
	public function get_id() { return $this->id; }
	public function get_email() { return $this->email; }
	public function get_key() { return $this->key; }
	public function get_evalue() { return $this->evalue; }
	public function get_time_created() { return $this->time_created; }
	public function get_pbs_number() { return $this->pbs_number; }
	public function get_time_started() { return $this->time_started; }
	public function get_time_completed() { return $this->time_completed; }
	public function get_unixtime_completed() { return strtotime($this->time_completed); }
	public function get_num_sequences() { return $this->num_sequences; }
	public function get_finish_file() { 
		return $this->get_output_dir() . "/" . $this->finish_file; 
	}
	public function get_accession_file() {
		return $this->get_output_dir() . "/".  $this->accession_file;
	}
	public function get_output_dir() {
		return $this->get_id() . "/" . $this->output_dir;
	}


        public function set_pbs_number($pbs_number) {
                $sql = "UPDATE generate SET generate_pbs_number='" . $pbs_number . "' ";
                $sql .= "WHERE generate_id='" . $this->get_id() . "' LIMIT 1";
                $this->db->non_select_query($sql);
                $this->pbs_number = $pbs_number;


        }

	public function set_time_started() {
		$current_time = date("Y-m-d H:i:s",time());
		$sql = "UPDATE generate SET generate_time_started='" . $current_time . "' ";
                $sql .= "WHERE generate_id='" . $this->get_id() . "' LIMIT 1";
                $this->db->non_select_query($sql);
                $this->time_started = $current_time;


	}
	public function set_time_completed() {
		$current_time = date("Y-m-d H:i:s",time());
		$sql = "UPDATE generate SET generate_time_completed='" . $current_time . "' ";
		$sql .= "WHERE generate_id='" . $this->get_id() . "' LIMIT 1";
		$this->db->non_select_query($sql);
		$this->time_completed = $current_time;

	}
	public function check_pbs_running() {
		$output;
		$exit_status;
		$exec = "qstat " . $this->get_pbs_number() . " 2> /dev/null | grep " . $this->get_pbs_number();
		exec($exec,$output,$exit_status);
		if (count($output) == 1) {
			return true;
		}
		else {
			return false;
		}

	}
	
        public function check_finish_file() {
                $results_path = functions::get_results_dir();
                $full_path = $results_path . "/" . $this->get_finish_file();
                return file_exists($full_path);

        }

        public function set_status($status) {

                $sql = "UPDATE generate ";
                $sql .= "SET generate_status='" . $status . "' ";
                $sql .= "WHERE generate_id='" . $this->get_id() . "' LIMIT 1";
                $result = $this->db->non_select_query($sql);
                if ($result) {
                        $this->status = $status;
                }

        }

	public function get_num_sequence_from_file() {
                $results_path = functions::get_results_dir();
                $full_path = $results_path . "/" . $this->get_accession_file();
		$num_seq = 0;
                if (file_exists($full_path)) {

                        $exec = "grep '>' " . $full_path . " | wc -l ";
                        $output = exec($exec);
                        $output = trim(rtrim($output));
                        list($num_seq,) = explode(" ",$output);

                }
                return $num_seq;
        }

        public function set_num_sequences($num_seq) {
                $sql = "UPDATE generate ";
                $sql .= "SET generate_num_sequences='" . $num_seq . "' ";
                $sql .= "WHERE generate_id='" . $this->get_id() . "' LIMIT 1";
                $result = $this->db->non_select_query($sql);
                if ($result) {
                        $this->num_sequences = $num_seq;
                        return true;
                }
                return false;
        }
	
	public function get_alignment_plot() {
		return $this->alignment_length;
	}
	public function get_length_histogram_plot() {
		return $this->length_histogram;

	}
	public function get_percent_identity_plot() {
		return $this->percent_identity;
	}
	public function get_number_edges_plot() {
		return $this->number_of_edges;
	}

	public function download_graph($type) {
		$results_dir = functions::get_results_dir();
		$directory = $results_dir . "/" . $this->get_output_dir();
		$filename = "";
		if ($type == "ALIGNMENT") {
			$full_path = $directory . "/" . $this->get_alignment_plot();
			$filename = $this->get_alignment_plot();
		}

		elseif ($type == "HISTOGRAM") {
			$full_path = $directory . "/" . $this->get_length_histogram_plot();
			$filename = $this->get_length_histogram_plot();
		}
		elseif ($type == "IDENTITY") {
			$full_path = $directory . "/" . $this->get_percent_identity_plot();
			$filename = $this->get_percent_identity_plot();
		}
		elseif ($type == "EDGES") {
			$full_path = $directory . "/".  $this->get_number_edges_plot();
			$filename = $this->get_number_edges_plot();
		}
		if (file_exists($full_path)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($full_path).'"');
			header('Content-Transfer-Encoding: binary');
			header('Connection: Keep-Alive');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($full_path));
			ob_clean();
                        readfile($full_path);
                }
                else {
                        return false;
                }


	}

        public function email_started() {

		$boundary = uniqid('np');
                $subject = $this->subject . " Generation Started";
                $to = $this->get_email();
		$from = "EFI-EST <" .functions::get_admin_email() . ">";
                $url = functions::get_web_root() . "/stepc.php";
                $full_url = $url . "?" . http_build_query(array('id'=>$this->get_id(),
                                'key'=>$this->get_key()));

		//html email
		$message = "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/html;charset='utf-8'\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$message .= "<html><body>";
                $message .= "<br>Your " . $this->subject . " Generation has started running.\r\n";
                $message .= "<br>You will receive an email once the job has been completed.\r\n";
		$message .= "<br>" .nl2br($this->get_job_info(),false); 
                $message .= "<br><br>" . nl2br(functions::get_email_footer(),false);
		$message .= "</body></html>";


		//plain text email
                $message .= "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/plain;charset='utf-8'\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$message .= "Your " . $this->subject . " Generation has started running.\r\n";
                $message .= "You will receive an email once the job has been completed.\r\n";
                $message .= $this->get_job_info();
                $message .= "\r\n" . functions::get_email_footer() . "\r\n";
		$message .= "\r\n\r\n--" . $boundary . "--\r\n";

                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "From: " . $from . "\r\n";
                $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from);
        }


        public function email_complete() {

                $boundary = uniqid('np');
                $subject = $this->subject . " Generation Complete";
                $to = $this->get_email();
                $from = "EFI-EST <" .functions::get_admin_email() . ">";
                $url = functions::get_web_root() . "/stepc.php";
                $full_url = $url . "?" . http_build_query(array('id'=>$this->get_id(),
                                'key'=>$this->get_key()));

                //html email
                $message = "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/html;charset='utf-8'\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $message .= "<br>Your " . $this->subject . " Generation is Complete\r\n";
                $message .= "<br>To view results, please go to\r\n";
                $message .= "<a href=\"" . $full_url . "\">" . $full_url . "</a>\r\n";
		$message .= "<br>test";
                $message .= "<br>" . nl2br($this->get_job_info(),false);
                $message .= "<br>This data will only be retained for " . functions::get_retention_days() . " days.\r\n";
                $message .= "<br>" . nl2br(functions::get_email_footer(),false);


                //plain text email
                $message .= "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/plain;charset='utf-8'\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $message .= "Your " . $this->subject . " Generation is Complete\r\n";
                $message .= "To view results, please go to\r\n";
                $message .= $full_url . "\r\n\r\n";
                $message .= $this->get_job_info();
                $message .= "This data will only be retained for " . functions::get_retention_days() . " days.\r\n";
                $message .= "\r\n" . functions::get_email_footer() . "\r\n";
                $message .= "\r\n\r\n--" . $boundary . "--\r\n";

                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "From: " . $from . "\r\n";
                $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from);


        }


        public function email_failed() {

                $boundary = uniqid('np');
                $subject = $this->subject . " Generation Failed";
                $to = $this->get_email();
                $url = functions::get_web_root();
		$from = "EFI-EST <" .functions::get_admin_email() . ">";

                //html email
                $message = "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/html;charset='utf-8'\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $message .= "<br>Your " . $this->subject . " Generation Failed\r\n";
                $message .= "<br>Sorry it failed.\r\n";
                $message .= "<br>Please restart by going to <a href='" . $url . "'>" . $url . "</a>\r\n";
                $message .= "<br>" . nl2br($this->get_job_info(),false);
                $message .= "<br><br>";
                $message .= nl2br(functions::get_email_footer(),false);

                //plain text email
                $message .= "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/plain;charset='utf-8'\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $message .= "Your " . $this->subject . " Generation Failed\r\n";
                $message .= "Sorry it failed.\r\n";
                $message .= "Please restart by going to " . $url . "\r\n";
                $message .= $this->get_job_info();
                $message .= "\r\n" . functions::get_email_footer() . "\r\n";
                $message .= "\r\n\r\n--" . $boundary . "--\r\n";


                $headers = "MIME-Version: 1.0\r\n";
                $headers = "From: " . $from . "\r\n";
                $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
                mail($to,$subject,$message,$headers," -f " . $from);



        }


	public function email_number_seq() {
                $boundary = uniqid('np');
                $subject = "EFI-EST " . $this->subject . " Number of Sequences too large";
                $to = $this->get_email();
                $url = functions::get_web_root();
		$from = "EFI-EST <" .functions::get_admin_email() . ">";
                $max_seq = functions::get_max_seq();

                //html email
                $message = "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/html;charset='utf-8'\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $message .= "<br>Your EFI-EST " . $this->subject . " Data Set\n";
                $message .= nl2br($this->get_job_info(),false);
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
                $message .= nl2br(functions::get_email_footer(),false);

                //plain text
                $message .= "\r\n\r\n--" . $boundary . "\r\n";
                $message .= "Content-type:text/plain;charset='utf-8'\r\n";
		$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $message .= "Your EFI-EST " . $this->subject . " Data Set\n";
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


	///////////////Protected Functions///////////


        private function load_generate($id) {
                $sql = "SELECT * FROM generate WHERE generate_id='" . $id . "' ";
                $sql .= "LIMIT 1";
                $result = $this->db->query($sql);
                if ($result) {
                        $this->id = $id;
			$this->key = $result[0]['generate_key'];
                        $this->pbs_number = $result[0]['generate_pbs_number'];
                        $this->evalue = $result[0]['generate_evalue'];
                        $this->time_created = $result[0]['generate_time_created'];
                        $this->status = $result[0]['generate_status'];
			$this->time_started = $result[0]['generate_time_started'];
                        $this->time_completed = $result[0]['generate_time_completed'];
			$this->type = $result[0]['generate_type'];
			$this->num_sequences = $result[0]['generate_num_sequences'];
			$this->email = $result[0]['generate_email'];
                }

        }

        protected function verify_email($email) {
                $email = strtolower($email);
		$hostname = "";
                if (strpos($email,"@")) {
                        list($prefix,$hostname) = explode("@",$email);
                }

                $valid = 1;
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$valid = 0;
		}
		elseif (($hostname != "") && (!checkdnsrr($hostname,"ANY"))) {
			$valid = 0;
		}
		return $valid;

        }

	protected function verify_evalue($evalue) {
		$max_evalue = 100;
		$valid = 1;
		if ($evalue == "") {
			$valid = 0;
		}
		if (!preg_match("/^\d+$/",$evalue)) {
			$valid = 0;
			
		}
		if ($evalue > $max_evalue) {
			$valid = 0;
		}
		return $valid;


	}

        protected function verify_fraction($fraction) {
                $valid = 1;
                if ($fraction == "") {
                        $valid = 0;
                }
                if (!preg_match("/^\d+$/",$fraction)) {
                        $valid = 0;
                }
                return $valid;

        }

        protected function generate_key() {
                $key = uniqid (rand (),true);
                $hash = sha1($key);
                return $hash;

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
			$msg = "ERROR: Queue " . functions::get_generate_queue() . " is full.  Number in the queue: " . $num_queued;
		}
		elseif ($max_user_queuable - $num_user_queued < $this->num_pbs_jobs) {
			$result = false;
			$msg = "ERROR: Number of Queued Jobs for user " . functions::get_cluster_user() . " is full.  Number in the queue: " . $num_user_queued;	
                }
		else {
			$result = true;
			$msg = "Number of queued jobs in queue " . functions::get_generate_queue() . ": " . $num_queued . ", Number of queued user jobs: " . $num_user_queued;
		}
		functions::log_message($msg);
		return $result;
        }

//	private function get_job_info() {
//
//		$message = "EFI-EST ID: " . $this->get_id() . "\r\n";
//		$message .= "E-Value: " . $this->get_evalue() . "\r\n";	
//              return $message;
//	}

}

?>
