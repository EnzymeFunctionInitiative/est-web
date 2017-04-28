<?php

require_once('family_shared.class.inc.php');
require_once('file_helper.class.inc.php');

class fasta extends family_shared {


    private $change_fasta_exec = "formatcustomfasta.pl";
    private $userdat_file = "output.dat";
    private $file_helper;
    public $subject = "EFI-EST FASTA";

    
    public function __construct($db, $id = 0) {
        $this->file_helper = new file_helper(".fasta", $id);
        parent::__construct($db, $id);
    }

    public function __destruct() {
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ACCESSOR FUNCTIONS SPECIFIC TO FASTA

    public function get_fasta_file() { return $this->get_id() . $this->file_helper->get_file_extension(); }
    public function get_change_fasta_exec() { return $this->change_fasta_exec; }
    public function get_uploaded_filename() { return $this->file_helper->get_uploaded_filename(); }
    public function get_userdat_file() { return $this->userdat_file; }
    public function get_userdat_file_path() {
        return functions::get_results_dir() . "/" . $this->get_id() . "/output/" . $this->get_userdat_file();
    }
    public function get_full_fasta_file_path() {
        return functions::get_results_dir() . "/" . $this->get_id() . "/" . $this->get_fasta_file();
    }

    // END ACCESSOR FUNCTIONS SPECIFIC TO FASTA
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // OVERLOADS

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

    protected function post_insert_action($data, $insert_result) {
        $result = parent::post_insert_action($data, $insert_result);
        return $this->file_helper->on_post_insert_action($data, $insert_result, $result);
    }
    
    public function get_insert_array($data) {
        $insert_array = parent::get_insert_array($data);
        $insert_array = $this->file_helper->on_append_insert_array($data, $insert_array);
        return $insert_array;
    }

    // End for file_helper
    //////////////////////////////
    
    protected function get_create_type() {
        return "FASTA";
    }

    protected function validate($data) {
        $result = parent::validate($data);

        if (!$this->verify_fraction($data->fraction)) {
            $result->errors = true;
            $result->message .= "<br><b>Please enter a valid fraction</b></br>";
        }
        if (!$this->verify_fasta($data->uploaded_filename)) {
            $result->errors = true;
            $result->message .= "<br><b>Please upload a valid fasta file.  The file extension must be .txt, .fasta, or .fa</b></br>";
        }

        return $result;
    }

    protected function post_output_structure_create() {
        if (!$this->copy_fasta_to_output()) {
            $this->set_status(__FAILED__);
            return 'Fasta file did not copy';
        } else {
            return '';
        }
    }

    protected function get_run_script_args($out) {
        $parms = parent::get_run_script_args($out);

        // This works because as a part of the import process the fasta file is copied to the results directory.
        $parms["-userfasta"] = $this->get_full_fasta_file_path();
        $parms["-userdat"] = $out->relative_output_dir . "/" . $this->get_userdat_file();

        return $parms;
    }

    public function get_job_info($eol = "\r\n") {
        $message = "EFI-EST ID: " . $this->get_id() . $eol;
        $message .= "Uploaded Fasta File: " . $this->file_helper->get_uploaded_filename() . $eol;
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
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // FUNCTIONS SPECIFIC TO FASTA
    
    public function view_fasta_file() {
        $filename = $this->get_id() . $this->file_helper->get_file_extension();
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
        $filename = $this->get_id() . $this->file_helper->get_file_extension();
        $full_path = functions::get_uploads_dir() . "/" . $filename;
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($full_path) . "\""); 
        readfile($full_path);

    }

    private function copy_fasta_to_output() {
        $filename = $this->get_id() . $this->file_helper->get_file_extension();
        print "$filename\n";
        $start_path = functions::get_uploads_dir() . "/" . $filename;
        print "$start_path\n";
        $end_path = functions::get_results_dir() . "/" . $this->get_id() . "/" . $filename;
        print "$end_path\n";
        $dat_path = $this->get_userdat_file_path();
        print "$dat_path\n";
        if ((file_exists($start_path)) && (file_exists(dirname($end_path)))) {
            $result = $this->fix_fasta($start_path,$end_path,$dat_path);
            print "Result: $result\n";
            return $result;
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
        print "$exec\n";
        $output = exec($exec,$output_array,$exit_status);
        print "$output\n";
        if (!$exit_status) {
            return true;
        }
        return false;

    }

    private function verify_fasta($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $valid = true;
        if (!in_array($ext,functions::get_valid_fasta_filetypes())) {
            $valid = false;	
        }
        return $valid;
    }

    // END FUNCTIONS SPECIFIC TO FASTA
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

?>
