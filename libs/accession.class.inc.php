<?php

require_once('family_shared.class.inc.php');
require_once('option_base.class.inc.php');
require_once('generate_helper.class.inc.php');
require_once('file_helper.class.inc.php');

class accession extends family_shared {


    private $change_fasta_exec = "formatcustomfasta.pl";
    private $file_helper;
    public $subject = "EFI-EST FASTA";


    public function __construct($db,$id = 0) {
        $this->file_helper = new file_helper(".txt", $id);
        parent::__construct($db, $id);
    }

    public function __destruct() {
    }

    public function get_uploaded_filename() { return $this->file_helper->get_uploaded_filename(); }
    public function get_no_matches_download_path() {
        return functions::get_web_root() . "/" .
            functions::get_results_dirname() . "/" . 
            $this->get_output_dir() . "/" . 
            functions::get_no_matches_filename(); 
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // OVERLOADS

    protected function get_create_type() {
        return "ACCESSION";
    }

    protected function validate($data) {
        $result = parent::validate($data);

        //if (!$this->verify_fraction($data->fraction)) {
        //    $result->errors = true;
        //    $result->message .= "<br><b>Please enter a valid fraction</b></br>";
        //}
        if ($data->uploaded_filename && !$this->verify_accession_file($data->uploaded_filename)) {
            $result->errors = true;
            $result->message .= "<br><b>Please upload a valid accession ID file.  The file extension must be .txt</b></br>";
        } else if (!$data->field_input && !$data->uploaded_filename) {
            $result->errors = true;
            $result->message .= "<br><b>Please specify a list of accession IDs and/or upload a valid accession ID file.  The file extension must be .txt.</b></br>";
        }

        return $result;
    }

    //////////////////////////////
    // For file_helper
    protected function load_generate($id) {
        $result = parent::load_generate($id);
        if (! $result) {
            return;
        }

        $this->file_helper->on_load_generate($id, $result);

        return $result;
    }

    protected function post_insert_action($data, $insert_result_id) {
        $result = parent::post_insert_action($data, $insert_result_id);
        $result = $this->file_helper->on_post_insert_action($data, $insert_result_id, $result);

        if ($data->field_input) {
            $file = $this->file_helper->get_full_uploaded_path();
            file_put_contents($file, "\n" . $data->field_input . "\n", FILE_APPEND);
        }

        return $result;
    }
    
    public function get_insert_array($data) {
        $insert_array = parent::get_insert_array($data);
        $insert_array = $this->file_helper->on_append_insert_array($data, $insert_array);
        return $insert_array;
    }

    // End for file_helper
    //////////////////////////////
    
    protected function post_output_structure_create() {
        if (!$this->file_helper->copy_file_to_results_dir()) {
            $this->set_status(__FAILED__);
            return 'Accession file did not copy';
        } else {
            return '';
        }
    }

    protected function get_run_script() {
        return "generatedata.pl";
    }

    protected function get_run_script_args($out) {
        $parms = parent::get_run_script_args($out);
        //$parms = array();
        //$parms = generate_helper::get_run_script_args($out, $parms);
        //$parms["-blast"] = strtolower($this->get_program());
        $parms["-useraccession"] = $this->file_helper->get_results_input_file();
        //$parms["-fraction"] = $this->get_fraction();
        return $parms;
    }
    
    public function get_job_info($eol = "\r\n") {
        $message = "EFI-EST ID: " . $this->get_id() . $eol;
        $message .= "Uploaded Accession File: " . $this->file_helper->get_uploaded_filename() . $eol;
        if (count($this->get_families())) {
            $message .= "PFAM/Interpro Families: ";
            $message .= $this->get_families_comma() . $eol;
        }
        $message .= "E-Value: " . $this->get_evalue() . $eol;
        $message .= "Fraction: " . $this->get_fraction() . $eol;
        $message .= "Selected Program: " . $this->get_program() . $eol;
        return $message;
    }

    // END OVERLOADS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function verify_accession_file($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $valid = true;
        if (!in_array($ext, functions::get_valid_accession_filetypes())) {
            $valid = false;
        }
        return $valid;
    }
}

?>
