<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
include_once 'includes/quest_acron.inc';

if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    $obj = new colorssn($db,$_GET['id']);
    if ($obj->get_key() != $_GET['key']) {
        echo "No EFI-EST Selected. Please go back";
        exit;
    }

    if (time() > $obj->get_unixtime_completed() + functions::get_retention_secs()) {
        echo "<p class='center'><br>Your job results are only retained for a period of " . functions::get_retention_days(). " days";
        echo "<br>Your job was completed on " . $obj->get_time_completed();
        echo "<br>Please go back to the <a href='" . functions::get_server_name() . "'>homepage</a></p>";
        exit;
    }

    $jobNumber = $obj->get_id();
    $uploadedFilename = $obj->get_uploaded_filename();

    $url = $_SERVER['PHP_SELF'] . "?" . http_build_query(array('id'=>$obj->get_id(), 'key'=>$obj->get_key()));
    $baseUrl = functions::get_web_root() . "/results/" . $obj->get_output_dir();
    
    $ssnFile = $obj->get_colored_xgmml_filename_no_ext();
    $ssnFileZip = "$ssnFile.zip";
    
    $nodeFilesZip = "${ssnFile}_UniProt_IDs.zip";
    $fastaFilesZip = "${ssnFile}_FASTA.zip";
    $tableFile = $ssnFile . "_" . functions::get_colorssn_map_file_name();
    
    $ssnFile = "$ssnFile.xgmml";
}
else {
    echo "No EFI-EST Select. Please go back";
    exit;
}

?>	

<img src="images/quest_stages_e.jpg" width="990" height="119" alt="stage 1">
<hr>

<h3>Data set Completed</h3>
<p>&nbsp;</p>

<h4>Network Information</h4>
<table width="100%" border="1">
    <tr>
        <td>Input Option</td>
        <td>Color SSN</td>
    </tr>
    <tr>
        <td>Job Number</td>
        <td><?php echo $jobNumber ?></td>
    </tr>
    <tr>
        <td>Uploaded XGMML File</td>
        <td><?php echo $uploadedFilename ?></td>
    </tr>
</table>

<p>&nbsp;</p>

<hr>

<h4>Data File Download</h4>
<table width="100%" border="1">
<tr>
    <td>Colored SSN</td>
    <td>
        <a href="<?php echo "$baseUrl/$ssnFile"; ?>"><button>Download</button></a>
        <a href="<?php echo "$baseUrl/$ssnFileZip"; ?>"><button>Download ZIP</button></a>
    </td>
</tr>
<tr>
    <td>UniProt ID-Color-Cluster Number Mapping Table</td>
    <td>
        <a href="<?php echo "$baseUrl/$tableFile"; ?>"><button>Download</button></a>
    </td>
</tr>
<tr>
    <td>UniProt ID Lists per Cluster</td>
    <td>
        <a href="<?php echo "$baseUrl/$nodeFilesZip"; ?>"><button>Download All (ZIP)</button></a>
    </td>
</tr>
<tr>
    <td>FASTA Files per Cluster</td>
    <td>
        <a href="<?php echo "$baseUrl/$fastaFilesZip"; ?>"><button>Download All (ZIP)</button></a>
    </td>
</tr>
</table>


</div>

<?php include_once 'includes/footer.inc.php'; ?>

