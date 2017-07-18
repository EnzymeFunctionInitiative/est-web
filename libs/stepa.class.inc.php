<?php

require_once('Mail.php');
require_once('Mail/mime.php');

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
    protected $total_num_file_sequences;
    protected $num_matched_file_sequences;
    protected $num_unmatched_file_sequences;
    protected $num_family_sequences;
    protected $accession_file = "allsequences.fa";
    protected $counts_file;
    protected $eol = PHP_EOL;
    protected $num_pbs_jobs = 1;
    protected $program;
    protected $fraction;
    protected $db_version;
    protected $beta;

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

        $this->counts_file = functions::get_accession_counts_filename();
        $this->beta = functions::get_release_status();
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
    public function get_time_completed_formatted() {
        return functions::format_datetime(functions::parse_datetime($this->time_completed));
    }
    public function get_unixtime_completed() { return strtotime($this->time_completed); }
    public function get_num_sequences() { return $this->num_sequences; }
    public function get_total_num_file_sequences() { return $this->total_num_file_sequences; }
    public function get_num_matched_file_sequences() { return $this->num_matched_file_sequences; }
    public function get_num_unmatched_file_sequences() { return $this->num_unmatched_file_sequences; }
    public function get_num_family_sequences() { return $this->num_family_sequences; }
    public function get_program() { return $this->program; }
    public function get_fraction() { return $this->fraction; }
    public function get_finish_file() { 
        return $this->get_output_dir() . "/" . $this->finish_file; 
    }
    public function get_accession_file() {
        return $this->get_output_dir() . "/".  $this->accession_file;
    }
    public function get_accession_counts_file() {
        return $this->get_output_dir() . "/".  $this->counts_file;
    }
    public function get_accession_counts_file_full_path() {
        return functions::get_results_dir() . "/" . $this->get_output_dir() . "/".  $this->counts_file;
    }
    public function get_output_dir() {
        return $this->get_id() . "/" . $this->output_dir;
    }
    public function get_blast_input() { return ""; }
    public function get_families() { return array(); }
    public function get_db_version() { return $this->db_version; }


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
        $full_count_path = $results_path . "/" . $this->get_accession_counts_file();
        $full_path = $results_path . "/" . $this->get_accession_file();

        if (file_exists($full_count_path)) {
            $num_seq = array(0, 0, 0, 0, 0);
            $lines = file($full_count_path);
            foreach ($lines as $line) {
                list($key, $val) = explode("\t", rtrim($line));
                if (!$val)
                    $num_seq[0] = intval($key);
                else if ($key == "Total")
                    $num_seq[0] = intval($val);
                else if($key == "FileTotal")
                    $num_seq[1] = intval($val);
                else if($key == "FileMatched")
                    $num_seq[2] = intval($val);
                else if($key == "FileUnmatched")
                    $num_seq[3] = intval($val);
                else if ($key == "Family")
                    $num_seq[4] = intval($val);
            }
        } else if (file_exists($full_path)) {
            $exec = "grep '>' " . $full_path . " | sort | uniq | wc -l ";
            $output = exec($exec);
            $output = trim(rtrim($output));
            list($num_seq,) = explode(" ",$output);
        } else {
            $num_seq = 0;
        }

        return $num_seq;
    }

    public function set_num_sequences($num_seq) {
        $sql = "UPDATE generate SET ";
        
        if (is_array($num_seq)) {
            $sql .= "generate_num_seq='" . $num_seq[0] . "', ";
            $sql .= "generate_total_num_file_seq='" . $num_seq[1] . "', ";
            $sql .= "generate_num_matched_file_seq='" . $num_seq[2] . "', ";
            $sql .= "generate_num_unmatched_file_seq='" . $num_seq[3] . "', ";
            $sql .= "generate_num_family_seq='" . $num_seq[4] . "' ";
        } else {
            $sql .= "SET generate_num_seq='" . $num_seq . "' ";
        }
        
        $sql .= "WHERE generate_id='" . $this->get_id() . "' LIMIT 1";

        print "SQL: $sql\n";
        
        $result = $this->db->non_select_query($sql);

        if ($result) {
            if (is_array($num_seq)) {
                $this->num_sequences = $num_seq[0];
                $this->total_num_file_sequences = $num_seq[1];
                $this->num_matched_file_sequences = $num_seq[2];
                $this->num_unmatched_file_sequences = $num_seq[3];
                $this->num_family_sequences = $num_seq[4];
            }
            else {
                $this->num_sequences = $num_seq;
                $this->total_num_file_sequences = 0;
                $this->num_matched_file_sequences = 0;
                $this->num_unmatched_file_sequences = 0;
                $this->num_family_sequences = 0;
            }
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
        $subject = $this->beta . "EFI-EST - Initial submission received";
        $to = $this->get_email();
        $from = "EFI EST <" . functions::get_admin_email() . ">";

        $plain_email = "";

        if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-EST." . $this->eol;

        //plain text email
        $plain_email .= $this->get_started_email_body();
        $plain_email .= "You will receive an email once the job has been completed." . $this->eol . $this->eol;
        $plain_email .= "Submission Summary:" . $this->eol . $this->eol;
        $plain_email .= $this->get_job_info() . $this->eol . $this->eol;
        $plain_email .= "If no new email is received after 48 hours, please contact us and mention the EFI-EST ";
        $plain_email .= "Job ID that corresponds to this email." . $this->eol . $this->eol;
        $plain_email .= functions::get_email_footer();

        $html_email = nl2br($plain_email, false);

        $message = new Mail_mime(array("eol"=>$this->eol));
        $message->setTXTBody($plain_email);
        $message->setHTMLBody($html_email);
        $body = $message->get();
        $extraheaders = array("From"=>$from,
            "Subject"=>$subject
        );
        $headers = $message->headers($extraheaders);

        $mail = Mail::factory("mail");
        $mail->send($to,$headers,$body);
    }

    protected function get_started_email_body() {
        $plain_email = "The initial information needed for the generation of the data set is being fetched and ";
        $plain_email .= "processed. The similarity between sequences is being calculated." . $this->eol . $this->eol;
        return $plain_email;
    }

    // This can be overridden.
    protected function get_generate_results_script() {
        return "stepc.php";
    }

    protected function get_completion_email_subject_line() {
        return "Initial calculation complete";
    }

    protected function get_completion_email_body() {
        $plain_email = "The initial information needed for the generation of the data set has been fetched and ";
        $plain_email .= "processed. The similarity between sequences has been calculated." . $this->eol . $this->eol;
        $plain_email .= "To finalize your SSN, please go to THE_URL" . $this->eol . $this->eol;
        return $plain_email;
    }


    public function email_complete() {
        $subject = $this->beta . "EFI-EST - " . $this->get_completion_email_subject_line();
        $to = $this->get_email();
        $from = "EFI-EST <" .functions::get_admin_email() . ">";

        $full_url = functions::get_web_root() . "/" . $this->get_generate_results_script();
        $full_url = $full_url . "?" . http_build_query(array('id'=>$this->get_id(),
            'key'=>$this->get_key()));

        if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-EST." . $this->eol;

        //plain text email
        $plain_email .= $this->get_completion_email_body();
        $plain_email .= "Submission Summary:" . $this->eol . $this->eol;
        $plain_email .= $this->get_job_info() . $this->eol . $this->eol;
        $plain_email .= "These data will only be retained for " . functions::get_retention_days() . " days." . $this->eol . $this->eol;
        $plain_email .= functions::get_email_footer() . $this->eol;

        $html_email = nl2br($plain_email, false);

        $plain_email = str_replace("THE_URL", $full_url, $plain_email);
        $html_email = str_replace("THE_URL", "<a href=\"" . htmlentities($full_url) . "\">" . $full_url . "</a>", $html_email);

        $message = new Mail_mime(array("eol"=>$this->eol));
        $message->setTXTBody($plain_email);
        $message->setHTMLBody($html_email);
        $body = $message->get();
        $extraheaders = array("From"=>$from,
            "Subject"=>$subject
        );
        $headers = $message->headers($extraheaders);

        $mail = Mail::factory("mail");
        $mail->send($to,$headers,$body);


    }

    // Analysis only
    public function email_failed() {

        $subject = $this->beta . "EFI-EST - Analysis computation failed";
        $to = $this->get_email();
        $full_url = functions::get_web_root();
        $from = "EFI-EST <" .functions::get_admin_email() . ">";

        $plain_email = "";

        if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-EST." . $this->eol;

        //plain text email
        $plain_email .= "The analysis computation failed." . $this->eol;
        $plain_email .= "Please restart by going to THE_URL" . $this->eol . $this->eol;
        $plain_email .= "Submission Summary:" . $this->eol . $this->eol;
        $plain_email .= $this->get_job_info() . $this->eol . $this->eol;
        $plain_email .= functions::get_email_footer() . $this->eol;

        $html_email = nl2br($plain_email, false);

        $plain_email = str_replace("THE_URL", $full_url, $plain_email);
        $html_email = str_replace("THE_URL", "<a href=\"" . htmlentities($full_url) . "\">" . $full_url . "</a>", $html_email);

        $message = new Mail_mime(array("eol"=>$this->eol));
        $message->setTXTBody($plain_email);
        $message->setHTMLBody($html_email);
        $body = $message->get();
        $extraheaders = array("From"=>$from,
            "Subject"=>$subject
        );
        $headers = $message->headers($extraheaders);

        $mail = Mail::factory("mail");
        $mail->send($to,$headers,$body);


    }


    public function email_number_seq() {
        $subject = $this->beta . "EFI-EST - Too many sequences for initial computation";
        $to = $this->get_email();
        $full_url = functions::get_web_root();
        $from = "EFI-EST <" .functions::get_admin_email() . ">";
        $max_seq = functions::get_max_seq();

        $plain_email = "";

        if ($this->beta) $plain_email = "Thank you for using the beta site of EFI-EST." . $this->eol;

        //plain text
        $plain_email .= "This computation will use " . number_format($this->get_num_sequences()) . "." . $this->eol;
        $plain_email .= "This number is too large--you are limited to ";
        $plain_email .=  number_format($max_seq) . " sequences." . $this->eol . $this->eol;
        $plain_email .= "Return to THE_URL" . $this->eol;
        $plain_email .= "to start a new job with a different set of Pfam/InterPro families.";
        $plain_email .= "Or, if you would like to generate a network with the Pfam/InterPro";
        $plain_email .= " families you have chosen, send an e-mail to efi@enzymefunction.org and";
        $plain_email .= " request an account on Biocluster.  We will provide you with instructions";
        $plain_email .= " to use our Unix scripts for network generation.  These scripts allow you";
        $plain_email .= " to use a larger number of processors and, also, provide more options for";
        $plain_email .= " generating the network files.  Your e-mail should provide a brief ";
        $plain_email .= "description of your project so that the EFI can assist you." . $this->eol . $this->eol;
        $plain_email .= "Submission Summary:" . $this->eol . $this->eol;
        $plain_email .= $this->get_job_info() . $this->eol . $this->eol;
        $plain_email .= functions::get_email_footer() . $this->eol . $this->eol;

        $html_email = nl2br($plain_email, false);
        $plain_email = str_replace("THE_URL", $full_url, $plain_email);
        $html_email = str_replace("THE_URL", "<a href=\"" . htmlentities($full_url) . "\">" . $full_url . "</a>", $html_email);

        $message = new Mail_mime(array("eol"=>$this->eol));
        $message->setTXTBody($plain_email);
        $message->setHTMLBody($html_email);
        $body = $message->get();
        $extraheaders = array("From"=>$from,
            "Subject"=>$subject
        );
        $headers = $message->headers($extraheaders);

        $mail = Mail::factory("mail");
        $mail->send($to,$headers,$body);
    }


    ///////////////Protected Functions///////////


    protected function load_generate($id) {
        $sql = "SELECT * FROM generate WHERE generate_id='" . $id . "' ";
        $sql .= "LIMIT 1";
        $result = $this->db->query($sql);
        if ($result) {
            $this->id = $id;
            $this->key = $result[0]['generate_key'];
            $this->pbs_number = $result[0]['generate_pbs_number'];
            $this->evalue = $result[0]['generate_evalue'];
            $this->fraction = $result[0]['generate_fraction'];
            $this->time_created = $result[0]['generate_time_created'];
            $this->status = $result[0]['generate_status'];
            $this->time_started = $result[0]['generate_time_started'];
            $this->time_completed = $result[0]['generate_time_completed'];
            $this->type = $result[0]['generate_type'];
            $this->num_sequences = $result[0]['generate_num_seq'];
            $this->total_num_file_sequences = $result[0]['generate_total_num_file_seq'];
            $this->num_matched_file_sequences = $result[0]['generate_num_matched_file_seq'];
            $this->num_unmatched_file_sequences = $result[0]['generate_num_unmatched_file_seq'];
            $this->num_family_sequences = $result[0]['generate_num_family_seq'];
            $this->email = $result[0]['generate_email'];
            $this->program = $result[0]['generate_program'];
            $this->db_version = functions::decode_db_version($result[0]['generate_db_version']);
        }

        return $result;
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

    //	private function available_pbs_slots() {
    //                $queue = new queue(functions::get_generate_queue());
    //                $num_queued = $queue->get_num_queued();
    //                $max_queuable = $queue->get_max_queuable();
    //                $num_user_queued = $queue->get_num_queued(functions::get_cluster_user());
    //                $max_user_queuable = $queue-> get_max_user_queuable();
    //
    //               	$result = false; 
    //		if ($max_queuable - $num_queued < $this->num_pbs_jobs) {
    //			$result = false;
    //			$msg = "ERROR: Queue " . functions::get_generate_queue() . " is full.  Number in the queue: " . $num_queued;
    //		}
    //		elseif ($max_user_queuable - $num_user_queued < $this->num_pbs_jobs) {
    //			$result = false;
    //			$msg = "ERROR: Number of Queued Jobs for user " . functions::get_cluster_user() . " is full.  Number in the queue: " . $num_user_queued;	
    //                }
    //		else {
    //			$result = true;
    //			$msg = "Number of queued jobs in queue " . functions::get_generate_queue() . ": " . $num_queued . ", Number of queued user jobs: " . $num_user_queued;
    //		}
    //		functions::log_message($msg);
    //		return $result;
    //        }


    //private function get_job_info() {
    //
    //	$message = "EFI-EST ID: " . $this->get_id() . "\r\n";
    //	$message .= "E-Value: " . $this->get_evalue() . "\r\n";	
    //            return $message;
    //}
}

?>
