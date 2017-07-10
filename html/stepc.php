
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
$dateCompleted = $generate->get_time_completed_formatted();
$dbVersion = $generate->get_db_version();


$gen_type = $generate->get_type();
$formatted_gen_type = functions::format_job_type($gen_type);

$table->add_row("Date Completed", $dateCompleted);
if (!empty($dbVersion)) {
    $table->add_row("Database Version", $dbVersion);
}
$table->add_row("Input Option", $formatted_gen_type);
$table->add_row("Job Number", $gen_id);

$job_name = $gen_id . "_" . $gen_type;

$uploaded_file = "";
$included_family = "";

if ($gen_type == "BLAST") {
    $generate = new blast($db,$_GET['id']);
    $code = $generate->get_blast_input();
    if ($table_format == "html") {
        $code = "<a href='blast.php?blast=$code' target='_blank'>View Sequence</a>";
    }
    $table->add_row("Blast Sequence", $code);
    $table->add_row("E-Value", $generate->get_evalue());
    $table->add_row("Maximum Blast Sequences", number_format($generate->get_submitted_max_sequences()));
    if (functions::get_program_selection_enabled()) { $table->add_row("Program Used", $generate->get_program()); }
}
elseif ($gen_type == "FAMILIES" || $gen_type == "ACCESSION" || $gen_type == "FASTA" || $gen_type == "FASTA_ID") {
    if ($gen_type == "FASTA" || $gen_type == "FASTA_ID") {
        $generate = new fasta($db,$_GET['id']);
        $uploaded_file = $generate->get_uploaded_filename();
        if ($uploaded_file) $table->add_row("Uploaded Fasta File", $uploaded_file);
    } else if ($gen_type == "ACCESSION") {
        $generate = new accession($db,$_GET['id']);
        $uploaded_file = $generate->get_uploaded_filename();
        if ($uploaded_file) $table->add_row("Uploaded Accession ID File", $uploaded_file);
    } else {
        $generate = new generate($db,$_GET['id']);
    }

    $included_family = $generate->get_families_comma();
    if ($included_family != "") $table->add_row("PFam/Interpro Families", $included_family);
    
    $table->add_row("E-Value", $generate->get_evalue());
    $table->add_row("Fraction", $generate->get_fraction());
    
    if ($gen_type == "FAMILIES") { $table->add_row("Domain", $generate->get_domain()); }
    if (functions::get_program_selection_enabled()) { $table->add_row("Program Used", $generate->get_program()); }
}
elseif ($gen_type == "COLORSSN") {
    $generate = new colorssn($db, $_GET['id']);
    $table->add_row("Uploaded XGMML File", $generate->get_uploaded_filename());
    $table->add_row("Neighborhood Size", $generate->get_neighborhood_size());
    $table->add_row("Cooccurrence", $generate->get_cooccurrence());
}

if ($uploaded_file) $table->add_row("Number of Sequences in Uploaded File", number_format($generate->get_num_file_sequences()));
if ($included_family) $table->add_row("Number of Sequences in PFAM/InterPro Family", number_format($generate->get_num_family_sequences()));
$table->add_row("Final Number of Sequences", number_format($generate->get_num_sequences()));


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


    $dateCompleted = $generate->get_time_completed_formatted();
    $dbVersion = $generate->get_db_version();

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
<p>&nbsp;</p>

<hr>



<h4>1: Analyze your data set<a href="tutorial_analysis.php" class="question" target="_blank">?</a></h4>
<p><strong>Important! </strong>View plots and histogram to determine the appropriate lengths and alignment score before continuing.</p>
<table>
    <tr>
        <td><p>Number of Edges Histogram</p></td>
        <td><a href='<?php echo "results/" . $generate->get_output_dir() . "/" . $generate->get_number_edges_plot(); ?>'
            class="view_download" target='_blank'>View</a></td>
        <td><form method='post' action='graphs.php'>
            <input type='hidden' name='id' value='<?php echo $generate->get_id(); ?>'>
            <input type='hidden' name='type' value='EDGES'>
            <input type='hidden' name='key' value='<?php echo $generate->get_key(); ?>'>
            <input type='submit' name='download_plot' value='Download' class='view_download'>
            </form>
        </td>
    </tr>
    <tr>
        <td><p>Length Histogram</p></td>
        <td><a href="<?php echo "results/" . $generate->get_output_dir() . "/" . $generate->get_length_histogram_plot(); ?>" class="view_download" target='_blank'>View</a></td>
        <td><form method='post' action='graphs.php'>
                <input type='hidden' name='id' value='<?php echo $generate->get_id(); ?>'>
                <input type='hidden' name='type' value='HISTOGRAM'>
                <input type='hidden' name='key' value='<?php echo $generate->get_key(); ?>'>
                <input type='submit' name='download_plot' value='Download' class='view_download'>
                </form>
        </td>
    </tr>
    <tr>
        <td><p>Alignment Length Quartile Plot</p></td>
        <td><a href="<?php echo "results/" . $generate->get_output_dir() . "/" . $generate->get_alignment_plot(); ?>" class="view_download" target='_blank'>View</a></td>
        <td><form method='post' action='graphs.php'>
                <input type='hidden' name='id' value='<?php echo $generate->get_id(); ?>'>
                <input type='hidden' name='type' value='ALIGNMENT'>
                <input type='hidden' name='key' value='<?php echo $generate->get_key(); ?>'>
                <input type='submit' name='download_plot' value='Download' class='view_download'>
                </form>
        </td>
    </tr>
    <tr>
        <td><p>Percent Identity Quartile Plot</p></td>
        <td><a href="<?php echo "results/" . $generate->get_output_dir() . "/" . $generate->get_percent_identity_plot(); ?>" class="view_download" target='_blank'>View</a></td>
        <td><form method='post' action='graphs.php'>
                <input type='hidden' name='id' value='<?php echo $generate->get_id(); ?>'>
                <input type='hidden' name='type' value='IDENTITY'>
                <input type='hidden' name='key' value='<?php echo $generate->get_key(); ?>'>
                <input type='submit' name='download_plot' value='Download' class='view_download'>
                </form>
        </td>    
    </tr>
</table>


<hr><p><br></p>
<h4>2: Choose alignment score for output<a href="tutorial_analysis.php" class="question" target="_blank">?</a>
<span style='color:red'>Required</span></h4>
<p>Select a lower limit for the aligment score for the output files. You will input an integer which represents the exponent of 10<sup>-X</sup> where X is the integer.</p>

<form name="define_length" method="post" action="<?php echo $url; ?>" class="align_left">

    <p><input type="text" name="evalue" 
<?php if (isset($_POST['evalue'])) { 
    echo "value='" . $_POST['evalue'] ."'"; }
?>
        > alignment score</p>
<hr><p><br></p>
    <h4>3: Define length range<a href="tutorial_analysis.php" class="question" target="_blank">?</a>
    <span style='color:red'>Optional</span></h4>
    <p>If protein length needs to be restricted.</p>

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
    <h4>4: Provide Network Name <span style='color:red'>Required</span></h4>


      <p><input type="text" name="network_name" 
<?php if (isset($_POST['network_name'])) {
    echo "value='" . $_POST['network_name'] . "'";
}
?>
        > Name


        <p>
        <input type='hidden' name='id' value='<?php echo $generate->get_id(); ?>'>
      <input type="submit" name="analyze_data" value="Analyze Data" class="css_btn_class_recalc">

        </p>
    <p><?php if (isset($result['MESSAGE'])) { echo $result['MESSAGE']; } ?>
    </form>



  </div>

<?php

    include_once 'includes/footer.inc.php';
}

?>

