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
    switch($_POST['option_selected']) {
        //Option A - Blast Input
        case 'A':
            $blast = new blast($db);
            
            $input->email = $_POST['email'];
            $input->blast_input = $_POST['blast_input'];
            $input->evalue = $_POST['blast_evalue'];
            $input->max_seqs = $_POST['blast_max_seqs'];
            
            $result = $blast->create($input);
            break;

        //Option B - PFam/Interpro
        case 'B':
            $generate = new generate($db);
            
            $input->email = $_POST['email'];
            $input->evalue = $_POST['pfam_evalue'];
            $input->families = $_POST['families_input'];
            $input->fraction = $_POST['pfam_fraction'];
            $input->domain = $_POST['pfam_domain'];
            $input->program = $_POST['program'];

            $result = $generate->create($input);
            break;

        //Option C - Fasta Input
        case 'C':
            if ($_FILES['fasta_file']['error'] === "") { $_FILES['fasta_file']['error'] = 4; }
    
            if ((isset($_FILES['fasta_file']['error'])) && ($_FILES['fasta_file']['error'] !== 0)) {
                $result['MESSAGE'] = "Error Uploading File: " . functions::get_upload_error($_FILES['fasta_file']['error']);
                $result['RESULT'] = false;
            }
            else {
                $fasta = new fasta($db);
 
                $input->email = $_POST['email'];
                $input->evalue = $_POST['fasta_evalue'];
                $input->families = $_POST['families_input2'];
                $input->fraction = $_POST['fasta_fraction'];
                $input->program = $_POST['program'];
                $input->tmp_file = $_FILES['fasta_file']['tmp_name'];
                $input->uploaded_filename = $_FILES['fasta_file']['name'];
 
                $result = $fasta->create($input);
            }

            break;
            
        //Option D - accession list
        case 'D':
            if ($_FILES['accession_file']['error'] === "") { $_FILES['accession_file']['error'] = 4; }
    
            if ((isset($_FILES['accession_file']['error'])) && ($_FILES['accession_file']['error'] !== 0)) {
                $result['MESSAGE'] = "Error Uploading File: " . functions::get_upload_error($_FILES['accession_file']['error']);
                $result['RESULT'] = false;
            }
            else {
                $accession = new accession($db);
 
                $input->email = $_POST['email'];
                $input->evalue = $_POST['accession_evalue'];
                $input->fraction = $_POST['accession_fraction'];
                $input->program = $_POST['program'];
                $input->tmp_file = $_FILES['accession_file']['tmp_name'];
                $input->uploaded_filename = $_FILES['accession_file']['name'];
 
                $result = $accession->create($input);
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
