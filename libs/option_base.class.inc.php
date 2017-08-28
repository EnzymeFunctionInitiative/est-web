<?php

require_once 'stepa.class.inc.php';
require_once 'input.class.inc.php';
require_once 'output.class.inc.php';

abstract class option_base extends stepa {

    protected $sequence_max;
    protected $max_blast_failed = "accession.txt.failed";


    public function __construct($db, $id = 0) {
        parent::__construct($db, $id);
    }

    public function __destruct() {
    }

    public function get_sequence_max() { return $this->sequence_max; }
    public function get_max_blast_failed_file() {
        return $this->get_output_dir() . "/" . $this->max_blast_failed;
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

    public function create($data) { //$email, $evalue, $families, $tmp_file, $uploaded_filename, $fraction, $program) {
        $type = $this->get_create_type();

        $result = $this->validate($data);
        
        if (!$result->errors) {
            $table_name = "generate";
            $insert_array = $this->get_insert_array($data);
            if ($data->is_debug) {
                foreach ($insert_array as $k => $v) {
                    print "'$k' = $v\n";
                }
                $insert_result = 1;
            }
            else {
                $insert_result = $this->db->build_insert($table_name, $insert_array);
            }

            $result = $this->post_insert_action($data, $insert_result);
            if (!$result->errors && $insert_result) {
                return array('RESULT' => true, 'id' => $insert_result, 'MESSAGE' => 'Job successfully created');
            }
        }

        return array('RESULT' => false, 'MESSAGE' => $result->message, 'id' => 0);
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // OVERLOADABLE FUNCTIONS
    // These functions can (some must) be overloaded to alter functionality.  If a child needs to add a new
    // validation, it should override validate but call the parent before doing any internal validation.

    // This is stored into the generate database so that it can be picked up by run_job below later.
    abstract protected function get_create_type();

    protected function get_insert_array($data) {
        $key = $key = $this->generate_key();
        $insert_array = array('generate_key' => $key,
            'generate_email' => $data->email,
            'generate_type' => $this->get_create_type(),
            'generate_evalue' => $data->evalue,
            'generate_fraction' => $data->fraction,
            'generate_program' => $data->program,
            'generate_db_version' => functions::get_encoded_db_version(),
        );
        return $insert_array;
    }

    // This can be overridden, but for any file action parent::post_insert_action should be called from the child
    // class in addition to whatever actions the child performs.
    protected function post_insert_action($data, $insert_result) {
        $result = new validation_result;
        return $result;
    }

    // This can be overridden, but for any file action parent::validate should be called from the child
    // class in addition to whatever actions the child performs.
    protected function validate($data) {
        $result = new validation_result;
        $result->errors = false;

        if (!$this->verify_email($data->email)) {
            $result->errors = true;
            $result->message .= "<br>Please enter a valid email address</br>";
        }
        if (!$this->verify_evalue($data->evalue)) {
            $result->errors = true;
            $result->message .= "<br><b>Please enter a valid E-Value</b></br>";
        }

        return $result;
    }

    // Not typically overloaded but can be used to create an alternate directory structure.
    protected function get_output_structure() {
        $out = new output_structure;
        //Setup Directories
        $out->job_dir = functions::get_results_dir() . "/" . $this->get_id();
        $out->relative_output_dir = "output";
        $out->full_output_dir = $out->job_dir . "/" . $out->relative_output_dir;
        return $out;
    }

    // This is used by classes that take uploaded files and process them.
    protected function post_output_structure_create() { return ''; }

    // This is used to get the name of the script that goes into the batch script.
    protected abstract function get_run_script();

    // This gets the arguments for the script.
    protected abstract function get_run_script_args($out);

    protected function additional_exec_modules() { return ""; }

    // END OVERLOADABLE FUNCTIONS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    // This function calls the overloaded functions above to construct a script for submitting to the cluster.
    public function run_job($is_debug = false) {
        if (! $this->available_pbs_slots()) {
            return array('RESULT' => false, 'EXIT_STATUS' => 1, 'MESSAGE' => 'Queue is full');
        }

        $out = $this->get_output_structure();

        if (@file_exists($out->job_dir)) {
            functions::rrmdir($out->job_dir);
        }
        
        if (!file_exists($out->job_dir))
            mkdir($out->job_dir);
        if (!file_exists($out->full_output_dir))
            mkdir($out->full_output_dir);

        $msg = $this->post_output_structure_create();
        if ($msg) {
            return array('RESULT' => false, 'MESSAGE' => $msg);
        }

        chdir($out->job_dir);

        $parms = $this->get_run_script_args($out);

        $exec = "source /etc/profile\n";
        $exec .= "module load " . functions::get_efi_module() . "\n";
        $exec .= "module load " . functions::get_efidb_module() . "\n";
        $exec .= $this->additional_exec_modules();
        $exec .= $this->get_run_script() . " ";
        foreach ($parms as $key => $value) {
            $exec .= " " . $key . " " . $value;
        }
        $exec .= " 2>&1";

        $exit_status = 1;
        $output_array = array();

        functions::log_message($exec);
        if (!$is_debug) {
            $output = exec($exec, $output_array, $exit_status);
        }
        else {
            functions::log_message("DEBUG: " . $exec);
            $output = "1.1";
            $exit_status = 0;
        }

        print $output . "\n";
        $output = trim(rtrim($output));
        $pbs_job_number = substr($output, 0, strpos($output, "."));

        functions::log_message($output);
        functions::log_message($pbs_job_number);

        if ($pbs_job_number && !$exit_status) {
            functions::log_message("no error");
            if (!$is_debug) {
                $this->set_pbs_number($pbs_job_number);
                $this->set_time_started();
                $this->email_started();
                $this->set_status(__RUNNING__);
            }
            return array('RESULT' => true, 'PBS_NUMBER' => $pbs_job_number, 'EXIT_STATUS' => $exit_status, 'MESSAGE' => 'Job Successfully Submitted');
        }
        else {
            functions::log_message("There was an error: " . $output . "  exit status: $exit_status" . "  " . join(',', $output_array));
            return array('RESULT' => false, 'EXIT_STATUS' => $exit_status, 'MESSAGE' => $output_array[18]);
        }
    }

    public function set_sequence_max($num_seq) {
        $sql ="UPDATE generate ";
        $sql .= "SET generate_sequence_max='1',generate_num_seq='" . $num_seq . "' ";
        $sql .= "WHERE generate_id='" . $this->get_id() . "' LIMIT 1";
        $result = $this->db->non_select_query($sql);
        if ($result) {
            $this->sequence_max = 1;
            $this->num_sequences = $num_seq;
        }
    }

    protected function available_pbs_slots() {
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

}

?>
