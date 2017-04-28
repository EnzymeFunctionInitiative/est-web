<?php

require_once 'family_shared.class.inc.php';

abstract class file_shared extends family_shared {


    protected $uploaded_filename;

    public function __construct($db,$id = 0) {
        parent::__construct($db, $id);
    }

    public function __destruct() {
    }

    protected function get_file_extension() { return ".txt"; }
    public function get_uploaded_filename() { return $this->uploaded_filename; }
    public function get_full_uploaded_path() { return functions::get_uploads_dir() . "/" . $this->get_uploaded_filename(); }
    public function get_results_input_file() {
        return functions::get_results_dir() . "/" . $this->get_id() . "/" . $this->get_id() . $this->get_file_extension();
    }
    
    protected function get_insert_array($data) {
        $insert_array = parent::get_insert_array($data);
        $insert_array['generate_fasta_file'] = $data->uploaded_filename;
        return $insert_array;
    }

    protected function load_generate($id) {
        $result = parent::load_generate($id);
        if (! $result) {
            return;
        }

        // This field is used for any file that is uploaded (e.g. Option C, D, and E), not just FASTA files.
        $this->uploaded_filename = $result[0]['generate_fasta_file'];

        return $result;
    }

    protected function post_insert_action($data, $insert_result) {
        $result = parent::post_insert_action($data, $insert_result);

        if (!$this->move_upload_file($data->tmp_file, $insert_result, $data->is_debug)) {
            $result->errors = true;
            $result->message = "Error moving file";
        }

        return $result;
    }

    
    protected function move_upload_file($tmp_file, $id) {
        $full_path = functions::get_uploads_dir() . "/" . $id . $this->get_file_extension();
        $result = move_uploaded_file($tmp_file, $full_path);
        return $result;
    }

    protected function copy_file_to_output() {
        $id = $this->get_id();
        $ext = $this->get_file_extension();
        $start_path = functions::get_uploads_dir() . "/" . $id . $ext;
        //$end_path = functions::get_results_dir() . "/" . $id . "/" . $id . $ext;
        $end_path = $this->get_results_input_file();
        functions::log_message("Copying $start_path to $end_path");
        return copy($start_path, $end_path);
    }

}

?>
