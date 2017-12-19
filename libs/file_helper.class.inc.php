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
    public function get_results_input_file($id = 0) {
        if ($id == 0) {
            $id = $this->id;
        }
        return functions::get_results_dir() . "/" . $id . "/" . $id . $this->file_extension;
    }
    
    public function on_append_insert_array($data, $insert_array) {
        $data->uploaded_filename = preg_replace("([^A-Za-z0-9_\-\.])", "_", $data->uploaded_filename);
        $insert_array['generate_fasta_file'] = $data->uploaded_filename;
        return $insert_array;
    }

    public function on_load_generate($id, $result) {
        // This field is used for any file that is uploaded (e.g. Option C, D, and E), not just FASTA files.
        $this->uploaded_filename = $result['generate_fasta_file'];
        $this->file_extension = "." . pathinfo($this->uploaded_filename, PATHINFO_EXTENSION);
    }

    public function on_post_insert_action($data, $id, $parent_result) {
        // Retain the zip extension, if any.
        $this->uploaded_filename = $id . "." . pathinfo($data->uploaded_filename, PATHINFO_EXTENSION);

        if ($data->tmp_file) {
            if (!$this->move_upload_file($data->tmp_file, $id, $data->is_debug)) {
                $parent_result->errors = true;
                $parent_result->message = "Error moving file " . $data->tmp_file . "     " . $this->uploaded_filename;
            }
        }

        return $parent_result;
    }

    // This must happen after the output structure has been created.
    public function copy_file_to_results_dir() {
        $ext = $this->get_file_extension();
        $start_path = functions::get_uploads_dir() . "/" . $this->id . $ext;
        //$end_path = functions::get_results_dir() . "/" . $this->id . "/" . $this->id . $ext;
        $end_path = $this->get_results_input_file($this->id);
        error_log("Copying $start_path to $end_path");
        return copy($start_path, $end_path);
    }

    protected function move_upload_file($tmp_file, $id, $is_debug) {
        $target_file = $this->get_full_uploaded_path(); 
        if ($is_debug) {
            print "If this was not run through the console, we would move $tmp_file to $target_file. But we aren't going to try that because it will likely fail.\n";
            $result = true;
        } else {
            $result = move_uploaded_file($tmp_file, $target_file);
            if (!$result) {
                error_log("Unable to move $tmp_file to $target_file");
            }
        }
        return $result;
    }

}

?>
