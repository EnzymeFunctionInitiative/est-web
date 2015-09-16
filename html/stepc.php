<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
include_once 'includes/quest_acron.inc';

if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
	$generate = new stepa($db,$_GET['id']);
	if ($generate->get_key() != $_GET['key']) {
                echo "No EFI-EST Selected. Please go back";
		exit;
        }
	$net_info_html = "";
	if ($generate->get_type() == "BLAST") {
                $generate = new blast($db,$_GET['id']);
                $net_info_html = "<tr><td>Blast Sequence</td>";
                $net_info_html .= "<td><a href='blast.php?blast=" . $generate->get_blast_input() . "' target='_blank'>View Sequence</a></td></tr>";
		$net_info_html .= "<tr><td>E-Value</td><td>" . $generate->get_evalue() . "</td></tr>";
		$net_info_html .= "<tr><td>Maximum Blast Sequences</td><td>" . number_format($generate->get_submitted_max_sequences()) . "</td></tr>";
		
        }
        elseif ($generate->get_type() == "FAMILIES") {
                $generate = new generate($db,$_GET['id']);
                $net_info_html = "<tr><td>PFam/Interpro Families</td>";
                $net_info_html .= "<td>" . $generate->get_families_comma() . "</td></tr>";
		$net_info_html .= "<tr><td>E-Value</td><td>" . $generate->get_evalue() . "</td></tr>";
		$net_info_html .= "<tr><td>Fraction</td><td>" . $generate->get_fraction() . "</td</tr>";
		$net_info_html .= "<tr><td>Domain</td><td>" . $generate->get_domain() . "</td></tr>";
        }
	elseif ($generate->get_type() == "FASTA") {
                $generate = new fasta($db,$_GET['id']);
                $net_info_html = "<td>Uploaded Fasta File</td>";
                $net_info_html .= "<td>" . $generate->get_uploaded_filename() . "</td>";
		if ($generate->get_families_comma() != "") {
			$net_info_html .= "<tr><td>PFam/Interpro Families</td>";
	                $net_info_html .= "<td>" . $generate->get_families_comma() . "</td></tr>";
			

		}
		$net_info_html .= "<tr><td>E-Value</td><td>" . $generate->get_evalue() . "</td></tr>";
                $net_info_html .= "<tr><td>Fraction</td><td>" . $generate->get_fraction() . "</td</tr>";


        }

	if (time() > $generate->get_unixtime_completed() + functions::get_retention_secs()) {
		echo "<p class='center'><br>Your job results are only retained for a period of " . functions::get_retention_days(). " days";
		echo "<br>Your job was completed on " . $generate->get_time_completed();
		echo "<br>Please go back to the <a href='" . functions::get_server_name() . "'>homepage</a></p>";
		exit;
	}

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

}
else {

        echo "No EFI-EST Select. Please go back";
	exit;
}


?>	

<img src="images/quest_stages_c.jpg" width="990" height="119" alt="stage 1">
<hr>

	<h3>Data set Completed</h3>
	<p>&nbsp;</p>
	        <h4>Network Information</h4>
            <table width="100%" border="1">
                <?php echo $net_info_html; ?>
	<tr>
		<td>Total Number of Sequences</td>
		<td><?php echo number_format($generate->get_num_sequences()); ?>
	</tr>
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
  
<?php include_once 'includes/footer.inc.php'; ?>
