<?php

class input_data {

    public $email;
    public $name;
    public $evalue;
    public $fraction;
    public $max_seqs;

    // For option A, C, D, and E
    public $field_input;

    // For option B
    public $domain;
    public $program;

    // For option B+C
    public $families;

    // For option C+D+E
    public $tmp_file;
    public $uploaded_filename;

    public $minimum;
    public $maximum;

    // This flag is set to true if the script is called from the command line.
    public $is_debug;

    //public $cooccurrence;
    //public $neighborhood_size;
}

class validation_result {
    public $message;
    public $errors;

    public function __construct() {
        $this->message = "";
        $this->errors = false;
    }
}

?>

