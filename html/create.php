<?php
require_once 'includes/main.inc.php';
require_once '../libs/input.class.inc.php';

$result['id'] = 0;
$result['MESSAGE'] = "";
$result['RESULT'] = 0;

$input = new input_data;
$input->is_debug = !isset($_SERVER["HTTP_HOST"]);

// If this is being run from the command line then we parse the command line parameters and put them into _POST so we can use
// that below.
if ($input->is_debug) {
    parse_str($argv[1], $_POST);
    if (isset($argv[2])) {
        $file_array = array();
        parse_str($argv[2], $file_array);
        foreach ($file_array as $parm => $file) {
            $fname = basename($file);
            $_FILES[$parm]['tmp_name'] = $file;
            $_FILES[$parm]['name'] = $fname;
            $_FILES[$parm]['error'] = 0;
        }
    }
}




$test = "";
foreach($_POST as $var) {
    $test .= " " . $var;
}


if (isset($_POST['submit'])) {
    foreach ($_POST as &$var) {
        $var = trim(rtrim($var));
    }
    $message = "";
    $option = $_POST['option_selected'];

    $input->email = $_POST['email'];
    if (array_key_exists('evalue', $_POST))
        $input->evalue = $_POST['evalue'];
    if (array_key_exists('program', $_POST))
        $input->program = isset($_POST['program']) ? $_POST['program'] : "";
    if (array_key_exists('fraction', $_POST))
        $input->fraction = $_POST['fraction'];
    
    switch($option) {
        //Option A - Blast Input
        case 'A':
            $blast = new blast($db);
            
            $input->field_input = $_POST['blast_input'];
            $input->max_seqs = $_POST['blast_max_seqs'];
            
            $result = $blast->create($input);
            break;

        //Option B - PFam/Interpro
        case 'B':
            $generate = new generate($db);
            
            $input->families = $_POST['families_input'];
            $input->domain = $_POST['pfam_domain'];

            $result = $generate->create($input);
            break;

        //Option C - Fasta Input
        case 'C':
        case 'E':
        //Option D - accession list
        case 'D':
        //Option color SSN
        case 'colorssn':
            if ($_FILES['file']['error'] === "") { $_FILES['file']['error'] = 4; }
    
            if ((isset($_FILES['file']['error'])) && ($_FILES['file']['error'] !== 0)) {
                $result['MESSAGE'] = "Error Uploading File: " . functions::get_upload_error($_FILES['file']['error']);
                $result['RESULT'] = false;
            }
            else {
                if ($option == "C" || $option == "E") {
                    $useFastaHeaders = $_POST['use_fasta_headers'];
                    $obj = new fasta($db, 0, $useFastaHeaders == "true" ? "E" : "C");
                    $input->field_input = $_POST['fasta_input'];
                    $input->families = $_POST['families_input'];
                } else if ($option == "D") {
                    $obj = new accession($db);
                    $input->field_input = $_POST['accession_input'];
                    $input->families = $_POST['families_input'];
                } else if ($option == "colorssn") {
                    $obj = new colorssn($db);
                    //$input->cooccurrence = $_POST['cooccurrence'];
                    //$input->neighborhood_size = $_POST['neighborhood_size'];
                }
 
                $input->tmp_file = $_FILES['file']['tmp_name'];
                $input->uploaded_filename = $_FILES['file']['name'];
                $result = $obj->create($input);
            }

            break;
            
        default:
            $result['RESULT'] = false;
            $result['MESSAGE'] = "You need to select one of the above options.";
    
    }

}


if ($input->is_debug) {
    print "JSON: ";
}

echo json_encode(array('valid'=>$result['RESULT'],
    'id'=>$result['id'],
    'message'=>$result['MESSAGE']));

if ($input->is_debug) {
    print "\n\n";
}

?>
