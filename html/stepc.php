
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
    exit;
}


$table_format = "html";
if (isset($_GET["as-table"])) {
    $table_format = "tab";
}
$table = new table_builder($table_format);


$web_address = dirname($_SERVER['PHP_SELF']);
$date_completed = $generate->get_time_completed_formatted();
$db_version = $generate->get_db_version();


$gen_type = $generate->get_type();
$formatted_gen_type = functions::format_job_type($gen_type);

$table->add_row("Date Completed", $date_completed);
if (!empty($db_version)) {
    $table->add_row("Database Version", $db_version);
}
$table->add_row("Input Option", $formatted_gen_type);
$table->add_row("Job Number", $gen_id);

$job_name = $gen_id . "_" . $gen_type;

$uploaded_file = "";
$included_family = "";
$num_family_nodes = $generate->get_num_family_sequences();
$total_num_nodes = $generate->get_num_sequences();
$extra_nodes_string = "";
$extra_nodes_ast = "";

if ($gen_type == "BLAST") {
    $generate = new blast($db,$_GET['id']);
    $code = $generate->get_blast_input();
    if ($table_format == "html") {
        $code = "<a href='blast.php?blast=$code' target='_blank'>View Sequence</a>";
    }
    $table->add_row("Blast Sequence", $code);
    $table->add_row("E-Value", $generate->get_evalue());
    $table->add_row("Maximum Blast Sequences", number_format($generate->get_submitted_max_sequences()));
}
elseif ($gen_type == "FAMILIES") {
    $generate = new generate($db,$_GET['id']);
    $included_family = $generate->get_families_comma();
    $table->add_row("PFam/Interpro Families", $included_family);
    $table->add_row("E-Value", $generate->get_evalue());
    $table->add_row("Fraction", $generate->get_fraction());
    $table->add_row("Domain", $generate->get_domain());
}
elseif ($gen_type == "ACCESSION") {
    $generate = new accession($db,$_GET['id']);
    $uploaded_file = $generate->get_uploaded_filename();
    if ($uploaded_file)
        $table->add_row("Uploaded Accession ID File", $uploaded_file);
    $table->add_row("No matches file", "<a href=\"" . $generate->get_no_matches_download_path() . "\"><button>Download</button></a>", true);
    $included_family = $generate->get_families_comma();
    if ($included_family != "")
        $table->add_row("PFam/Interpro Families", $included_family);
    $table->add_row("E-Value", $generate->get_evalue());
    $table->add_row("Fraction", $generate->get_fraction());

    $term = "IDs";
    $table->add_row("Number of $term in Uploaded File", number_format($generate->get_total_num_file_sequences()));
    $table->add_row("Number of $term in Uploaded File with UniProt Match", number_format($generate->get_num_matched_file_sequences()));
    $table->add_row("Number of $term in Uploaded File without UniProt Match", number_format($generate->get_num_unmatched_file_sequences()));
}
elseif ($gen_type == "FASTA" || $gen_type == "FASTA_ID") {
    $generate = new fasta($db,$_GET['id']);
    $uploaded_file = $generate->get_uploaded_filename();
    if ($uploaded_file)
        $table->add_row("Uploaded Fasta File", $uploaded_file);
    $included_family = $generate->get_families_comma();
    if ($included_family != "")
        $table->add_row("PFam/Interpro Families", $included_family);
    $table->add_row("E-Value", $generate->get_evalue());
    $table->add_row("Fraction", $generate->get_fraction());

    $num_file_seq = $generate->get_total_num_file_sequences();
    $num_matched = $generate->get_num_matched_file_sequences();
    $num_unmatched = $generate->get_num_unmatched_file_sequences();
    
    if (!empty($num_file_seq))
        $table->add_row("Number of Sequences in Uploaded File", number_format($num_file_seq));
    if (!empty($num_matched) && !empty($num_unmatched))
        $table->add_row("Number of FASTA Headers in Uploaded File", number_format($num_matched + $num_unmatched));
    if (!empty($num_matched))
        $table->add_row("Number of SSN Nodes with UniProt IDs from Uploaded File", number_format($num_matched));
    if (!empty($num_unmatched))
        $table->add_row("Number of SSN Nodes without UniProt IDs from Uploaded File", number_format($num_unmatched));

    if (!empty($num_family_nodes) && !empty($num_file_seq)) {
        $extra_num_nodes = $total_num_nodes - $num_family_nodes - $num_file_seq;
        if ($extra_num_nodes > 0) {
            $extra_nodes_string = "* $extra_num_nodes additional nodes have been added since multiple UniProt IDs were found for a single sequence with more than one header in one or more cases.";
            $extra_nodes_ast = "*";
        }
    }
}
elseif ($gen_type == "COLORSSN") {
    $generate = new colorssn($db, $_GET['id']);
    $table->add_row("Uploaded XGMML File", $generate->get_uploaded_filename());
    $table->add_row("Neighborhood Size", $generate->get_neighborhood_size());
    $table->add_row("Cooccurrence", $generate->get_cooccurrence());
}

if ($gen_type != "COLORSSN") {
    if (functions::get_program_selection_enabled())
        $table->add_row("Program Used", $generate->get_program());
}

if ($included_family && !empty($num_family_nodes))
    $table->add_row("Number of IDs in PFAM/InterPro Family", number_format($num_family_nodes));
$table->add_row("Total Number of Nodes $extra_nodes_ast", number_format($total_num_nodes));


$table_string = $table->as_string();

if (isset($_GET["as-table"])) {
    $table_filename = functions::safe_filename($job_name) . "_settings.txt";

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

    include_once 'includes/header.inc.php'; 
    include_once 'includes/quest_acron.inc';


    if (time() > $generate->get_unixtime_completed() + functions::get_retention_secs()) {
        echo "<p class='center'><br>Your job results are only retained for a period of " . functions::get_retention_days(). " days";
        echo "<br>Your job was completed on " . $generate->get_time_completed();
        echo "<br>Please go back to the <a href='" . functions::get_server_name() . "'>homepage</a></p>";
        exit;
    }


    $date_completed = $generate->get_time_completed_formatted();
    $db_version = $generate->get_db_version();

    $url = $_SERVER['PHP_SELF'] . "?" . http_build_query(array('id'=>$generate->get_id(),
        'key'=>$generate->get_key()));

    if (isset($_POST['analyze_data'])) {
        foreach ($_POST as $var) {
            $var = trim(rtrim($var));
        }
        $min = $_POST['minimum'];
        if ($_POST['minimum'] == "") {
            $min = __MINIMUM__;
        }
        $max = $_POST['maximum'];
        if ($_POST['maximum'] == "") {
            $max = __MAXIMUM__;
        }
        $analysis = new analysis($db);
        $result = $analysis->create($_POST['id'],
            $_POST['evalue'],
            $_POST['network_name'],
            $min,
            $max);

        if ($result['RESULT']) {
            header('Location: stepd.php');
        }
    }

    function make_plot_download($gen, $hdr, $type, $preview_img, $download_img) {
        $html = "<span class='plot_header'>$hdr</span> \n";
        $html .= "<a href='graphs.php?id=" . $gen->get_id() . "&type=" . $type . "&key=" . $gen->get_key() . "'><button class='file_download'>Download <img src='images/download.svg' /></button></a>\n";
        if ($preview_img) {
            $html .= "<button class='accordion'>Preview</button>\n";
            $html .= "<div class='acpanel'>\n";
            $html .= "<img src='$preview_img' />\n";
            $html .= "</div>\n";
        } else {
            $html .= "<a href='$download_img'><button class='file_download'>Preview</button></a>\n";
            $html .= "<div></div>\n";
        }

        return $html;
    }

?>	

<img src="images/quest_stages_c.jpg" width="990" height="119" alt="stage 1">
<hr>

<h3>Data set Completed</h3>
<p>&nbsp;</p>
<h4>Network Information</h4>

<p>
    Generation Summary Table
    <a href='<?php echo $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&key=" . $_GET['key'] . "&as-table=1" ?>'><button>Download</button></a>
</p>

<table width="100%" border="1">
    <?php echo $table_string; ?>
</table>
<?php echo $extra_nodes_string; ?>
<p>&nbsp;</p>

<hr>

<h4><b>Parameters for SSN Finalization</b></h4>

To finalize the generation of an SSN, a similarity threshold that defines which protein sequences
should or should not be connected in a network is needed. This will determine the segregation of proteins into clusters.

<h4>Analyze your data set<a href="tutorial_analysis.php" class="question" target="_blank">?</a></h4>
<p>View plots and histogram to determine the appropriate lengths and alignment score before continuing.</p>

<?php echo make_plot_download($generate, "Number of Edges Histogram", "EDGES", $generate->get_number_edges_plot_sm(), $generate->get_number_edges_plot(1)); ?>

<?php echo make_plot_download($generate, "Length Histogram", "HISTOGRAM", $generate->get_length_histogram_plot_sm(), $generate->get_length_histogram_plot(1)); ?>

<?php echo make_plot_download($generate, "Alignment Length Quartile Plot", "ALIGNMENT", $generate->get_alignment_plot_sm(), $generate->get_alignment_plot(1)); ?>

<?php echo make_plot_download($generate, "Percent Identity Quartile Plot", "IDENTITY", $generate->get_percent_identity_plot_sm(), $generate->get_percent_identity_plot(1)); ?>


<!--
<span class="plot_header">Number of Edges Histogram</span> 
<a href="graphs.php?id=<?php echo $generate->get_id(); ?>&type=EDGES&key=<?php echo $generate->get_key(); ?>"><button class="file_download">Download <img src="images/download.svg" /></button></a>
<button class="accordion">Preview</button>
<div class="acpanel">
<img src='<?php echo "results/" . $generate->get_output_dir() . "/" . $generate->get_number_edges_plot_sm(); ?>' />
</div>

<span class="plot_header">Length Histograpm</span> 
<a href="graphs.php?id=<?php echo $generate->get_id(); ?>&type=HISTOGRAM&key=<?php echo $generate->get_key(); ?>"><button class="file_download">Download <img src="images/download.svg" /></button></a>
<button class="accordion">Preview</button>
<div class="acpanel">
<img src='<?php echo "results/" . $generate->get_output_dir() . "/" . $generate->get_length_histogram_plot_sm(); ?>' />
</div>

<span class="plot_header">Alignment Length Quartile Plot</span> 
<a href="graphs.php?id=<?php echo $generate->get_id(); ?>&type=ALIGNMENT&key=<?php echo $generate->get_key(); ?>"><button class="file_download">Download <img src="images/download.svg" /></button></a>
<button class="accordion">Preview</button>
<div class="acpanel">
<img src='<?php echo "results/" . $generate->get_output_dir() . "/" . $generate->get_alignment_plot_sm(); ?>' />
</div>

<span class="plot_header">Percent Identity Quartile Plot</span> 
<a href="graphs.php?id=<?php echo $generate->get_id(); ?>&type=IDENTITY&key=<?php echo $generate->get_key(); ?>"><button class="file_download">Download <img src="images/download.svg" /></button></a>
<button class="accordion">Preview</button>
<div class="acpanel">
<img src='<?php echo "results/" . $generate->get_output_dir() . "/" . $generate->get_percent_identity_plot_sm(); ?>' />
</div>
-->

<hr><p><br></p>
<h4><b>Finalization Parameters</b></h4>
<h4>1: Alignment score for output <a href="tutorial_analysis.php" class="question" target="_blank">?</a></h4>
<p>Select a lower limit for the aligment score for the output files. You will input an integer which represents the exponent of 10<sup>-X</sup> where X is the integer.</p>

<form name="define_length" method="post" action="<?php echo $url; ?>" class="align_left">

    <p><input type="text" name="evalue" 
<?php if (isset($_POST['evalue'])) { 
    echo "value='" . $_POST['evalue'] ."'"; }
?>
        > alignment score</p>

This score is the similarity threshold which determine the connection of proteins with each other. All pairs of proteins with a similarity score below this number will not be connected. Sets of connected proteins will form clusters.

<hr>
    <h4>2: Sequence length restriction  <a href="tutorial_analysis.php" class="question" target="_blank">?</a>
    <span style='color:red'>Optional</span></h4>
    <p> This option can be used to restrict sequences used based on their length.</p>

       <p><input type="text" name="minimum" maxlength='20' 
<?php if (isset($_POST['minimum'])) { 
    echo "value='" . $_POST['minimum'] . "'"; }
?>
        > Min (Defaults: <?php echo __MINIMUM__; ?>)<br>
       <input type="text" name="maximum" maxlength='20'
<?php if (isset($_POST['maximum'])) { 
    echo "value='" . $_POST['maximum'] . "'"; }
?>
        > Max (Defaults: <?php echo __MAXIMUM__; ?>) </p>



      <hr>
    <h4>3: Provide Network Name</h4>


      <p><input type="text" name="network_name" 
<?php if (isset($_POST['network_name'])) {
    echo "value='" . $_POST['network_name'] . "'";
}
?>
        > Name
</p>

This name will be displayed in Cytoscape.

        <p>
        <input type='hidden' name='id' value='<?php echo $generate->get_id(); ?>'>
</p>

<hr>

<center>
      <input type="submit" name="analyze_data" value="Create SSN" class="css_btn_class_recalc">

    <p><?php if (isset($result['MESSAGE'])) { echo $result['MESSAGE']; } ?>

<?php if (functions::is_beta_release()) { ?>
<h4><b><span style="color: blue">BETA</span></b></h4>
<?php } ?>
</center>
    </form>


  </div>

<?php

    include_once 'includes/footer.inc.php';
}

?>

