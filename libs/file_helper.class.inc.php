<?php

require_once('functions.class.inc.php');

class file_helper {

    private $uploaded_filename;
    private $file_extension;
    private $id;

    public function __construct($file_extension, $id = 0) {
        $this->id = $id;
        $this->file_extension = $file_extension;
    }

    public function get_file_extension() { return $this->file_extension; }
    public function get_uploaded_filename() { return $this->uploaded_filename; }
    public function get_full_uploaded_path() { return functions::get_uploads_dir() . "/" . $this->get_uploaded_filename(); }
    public function get_results_input_file() {
        return functions::get_results_dir() . "/" . $this->id . "/" . $this->id . $this->file_extension;
    }
    
    public function on_append_insert_array($data, $insert_array) {
        $insert_array['generate_fasta_file'] = $data->uploaded_filename;
        return $insert_array;
    }

    public function on_load_generate($id, $result) {
        // This field is used for any file that is uploaded (e.g. Option C, D, and E), not just FASTA files.
        $this->uploaded_filename = $result[0]['generate_fasta_file'];
    }

    public function on_post_insert_action($data, $insert_result, $parent_result) {

        if (!$this->move_upload_file($data->tmp_file, $insert_result, $data->is_debug)) {
            $parent_result->errors = true;
            $parent_result->message = "Error moving file";
        }

        return $parent_result;
    }

    public function copy_file_to_output() {
        $ext = $this->get_file_extension();
        $start_path = functions::get_uploads_dir() . "/" . $this->id . $ext;
        //$end_path = functions::get_results_dir() . "/" . $this->id . "/" . $this->id . $ext;
        $end_path = $this->get_results_input_file();
        functions::log_message("Copying $start_path to $end_path");
        return copy($start_path, $end_path);
    }

    protected function move_upload_file($tmp_file, $id) {
        $full_path = functions::get_uploads_dir() . "/" . $id . $this->get_file_extension();
        $result = move_uploaded_file($tmp_file, $full_path);
        return $result;
    }

}

?>
