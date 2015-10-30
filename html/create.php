<?php
require_once 'includes/main.inc.php';
$result['id'] = 0;
$result['MESSAGE'] = "";
$result['RESULT'] = 0;

if (isset($_POST['submit'])) {
        foreach ($_POST as &$var) {
                $var = trim(rtrim($var));
        }
        $message = "";
        switch($_POST['option_selected']) {
                //Option A - Blast Input
                case 'A':
                        $blast = new blast($db);
                        $result = $blast->create($_POST['email'],$_POST['blast_input'],$_POST['blast_evalue'],$_POST['blast_max_seqs']);
                        break;

                //Option B - PFam/Interpro
                case 'B':

                        $generate = new generate($db);
                        $result = $generate->create($_POST['email'],$_POST['pfam_evalue'],$_POST['families_input'],$_POST['pfam_fraction'],$_POST['pfam_domain']);
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
        	                $result = $fasta->create($_POST['email'],$_POST['fasta_evalue'],$_POST['families_input2'],
					$_FILES['fasta_file']['tmp_name'],$_FILES['fasta_file']['name'],$_POST['fasta_fraction']);
				
			}
			break;
                default:
                        $result['RESULT'] = false;
                        $result['MESSAGE'] = "You need to select on of the above options.";

        }


}


echo json_encode(array('valid'=>$result['RESULT'],
                        'id'=>$result['id'],
                        'message'=>$result['MESSAGE']));

?>
