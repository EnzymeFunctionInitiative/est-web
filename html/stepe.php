<?php 
include_once 'includes/main.inc.php';
include_once '../libs/table_builder.class.inc.php';


if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    exit;
}

$generate = new stepa($db,$_GET['id']);
$gen_id = $generate->get_id();

if ($generate->get_key() != $_GET['key']) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    //echo "No EFI-EST Selected. Please go back";
    exit;
}

$analysis_id = $_GET['analysis_id'];
$analysis = new analysis($db, $analysis_id);


$table_format = "html";
if (isset($_GET["as-table"])) {
    $table_format = "tab";
}
$table = new table_builder($table_format);


$web_address = dirname($_SERVER['PHP_SELF']);
$dateCompleted = $generate->get_time_completed_formatted();
$dbVersion = $generate->get_db_version();


$gen_type = $generate->get_type();
$gen_type = functions::format_job_type();

$table->add_row("Date Completed", $dateCompleted);
if (!empty($dbVersion)) {
    $table->add_row("Database Version", $dbVersion);
}
$table->add_row("Input Option", $gen_type);
$table->add_row("Job Number", $gen_id);


if ($generate->get_type() == "BLAST") {
    $generate = new blast($db,$_GET['id']);
    $code = $generate->get_blast_input();
    if ($table_format == "html") {
        $code = "<a href='blast.php?blast=$code' target='_blank'>View Sequence</a>";
    }
    $table->add_row("Blast Sequence", $code);
    $table->add_row("E-Value", $generate->get_evalue());
    $table->add_row("Maximum Blast Sequences", number_format($generate->get_submitted_max_sequences()));
}
elseif ($generate->get_type() == "FAMILIES" || $generate->get_type() == "ACCESSION") {
    $generate = new generate($db,$_GET['id']);
    $table->add_row("PFam/Interpro Families", $generate->get_families_comma());
    $table->add_row("E-Value", $generate->get_evalue());
    $table->add_row("Fraction", $generate->get_fraction());
    $table->add_row("Domain", $generate->get_domain());
}
elseif ($generate->get_type() == "FASTA" || $generate->get_type() == "FASTA_ID") {
    $generate = new fasta($db,$_GET['id']);
    $table->add_row("Uploaded Fasta File", $generate->get_uploaded_filename());
    if ($generate->get_families_comma() != "") {
        $table->add_row("PFam/Interpro Families", $generate->get_families_comma());
    }
    $table->add_row("E-Value", $generate->get_evalue());
    $table->add_row("Fraction", $generate->get_fraction());
}

$table->add_row("Network Name", $analysis->get_name());
$table->add_row("Alignment Score", $analysis->get_evalue());
$table->add_row("Minimum Length", number_format($analysis->get_min_length()));
$table->add_row("Maximum Length", number_format($analysis->get_max_length()));
$table->add_row("Number of Filtered Sequences", number_format($analysis->get_num_sequences_post_filter()));
$table->add_row("Total Number of Sequences", number_format($generate->get_num_sequences()));


$table_string = $table->as_string();

if (isset($_GET["as-table"])) {
    $table_filename = functions::safe_filename($analysis->get_name()) . "_settings.txt";

    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $table_filename . '"');
    header('Content-Length: ' . strlen($table_string));
    ob_clean();
    echo $table_string;
}
else {

    if (time() > $analysis->get_unixtime_completed() + functions::get_retention_secs()) {
        echo "<p class='center'><br>Your job results are only retained for a period of " . functions::get_retention_days() . " days.";
        echo "<br>Your job was completed on " . $analysis->get_time_completed();
        echo "<br>Please go back to the <a href='" . functions::get_server_name() . "'>homepage</a></p>";
        exit;
    }

    $stats = $analysis->get_network_stats();
    $rep_network_html = "";
    $full_network_html = "";

    for ($i=0;$i<count($stats);$i++) {
        if ($i == 0) {
            $path = functions::get_web_root() . "/results/" . $analysis->get_output_dir() . "/" . $analysis->get_network_dir() . "/" . $stats[$i]['File'];
            $full_network_html = "<tr>";
            $full_network_html .= "<td style='text-align:center;'><a href='$path'><button>Download</button></a>  <a href='$path.zip'><button>Download ZIP</button></a></td>\n";
            $full_network_html .= "<td style='text-align:center;'>" . number_format($stats[$i]['Nodes'],0) . "</td>\n";
            $full_network_html .= "<td style='text-align:center;'>" . number_format($stats[$i]['Edges'],0) . "</td>\n";
            $full_network_html .= "<td style='text-align:center;'>" . functions::bytes_to_megabytes($stats[$i]['Size'],0) . " MB</td>\n";
            $full_network_html .= "</tr>";
        }
        else {
            $percent_identity = substr($stats[$i]['File'],strpos($stats[$i]['File'],'-')+1);
            $percent_identity = substr($percent_identity,0,strrpos($percent_identity,'_'));
            $percent_identity = str_replace(".","",$percent_identity);
            $path = functions::get_web_root() . "/results/" . $analysis->get_output_dir() . "/" . $analysis->get_network_dir() . "/" . $stats[$i]['File'];
            $rep_network_html .= "<tr>";
            $rep_network_html .= "<td style='text-align:center;'><a href='$path'><button>Download</button></a>   <a href='$path.zip'><button>Download ZIP</button></a></td>\n";
            $rep_network_html .= "<td style='text-align:center;'>" . $percent_identity . "</td>\n";
            $rep_network_html .= "<td style='text-align:center;'>" . number_format($stats[$i]['Nodes'],0) . "</td>\n";
            $rep_network_html .= "<td style='text-align:center;'>" . number_format($stats[$i]['Edges'],0) . "</td>\n";
            $rep_network_html .= "<td style='text-align:center;'>" . functions::bytes_to_megabytes($stats[$i]['Size'],0) . " MB</td>\n";
            $rep_network_html .= "</tr>";
        }
    }

    include_once 'includes/header.inc.php'; 
    include_once 'includes/quest_acron.inc';
    

?>	

<img src="images/quest_stages_e.jpg" width="990" height="119" alt="stage 1">
   <hr>


<h3>Download Network Files</h3>
    <p>&nbsp;</p>
    <p><b>If you use an SSN from EFI-EST, please cite <a href='tutorial_references.php'>Reference #6 Gerlt <i>et al.</i></a></b></p>
    <p>&nbsp;</p>
    <h4>Network Information</h4>
    <p>
        Generation and Analysis Summary Table
        <a href='<?php echo $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&key=" . $_GET['key'] . "&analysis_id=" . $_GET['analysis_id'] . "&as-table=1" ?>'><button>Download</button></a>
    </p>

    <table width="100%" border="1">
        <?php echo $table_string; ?>
    </table>

    <h4>Full Network <a href="tutorial_download.php" class="question" target="_blank">?</a></h4>
    <p>Each node in the network is a single protein from the data set. Large files (&gt;500MB) may not open.</p>

    <table width="100%" border="1">
    <tr>
        <th></th>
        <th># Nodes</th>
        <th># Edges</th>
        <th>File Size (MB)</th>
    </tr>
    <?php echo $full_network_html; ?>
    </table>

    <p>&nbsp;</p>
    <div class="align_left">
    <h4>Representative Node Networks <a href="tutorial_download.php" class="question" target="_blank">?</a></h4>
    <p>Each node in the network represents a collection of proteins grouped according to percent identity.</p>
    </div>
        <table width="100%" border="1">
    <tr>
    <th></th>
    <th>% ID</th>
    <th># Nodes</th>
    <th># Edges</th>
    <th>File Size (MB)</th>
    </tr>

    <?php echo $rep_network_html; ?>
    </table>

    <hr>

  </div>
<center><p><a href='http://enzymefunction.org/resources/tutorials/efi-and-cytoscape3'>New to Cytoscape</a></p></center>
<?php

    include_once 'includes/footer.inc.php';

} // as-table block

?>

