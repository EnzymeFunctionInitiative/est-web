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
	if ($generate->get_type() == "BLAST") {
		$generate = new blast($db,$_GET['id']);
		$net_info_html = "<td>Blast Sequence</td>";
		$net_info_html .= "<td><a href='blast.php?blast=" . $generate->get_blast_input() . "' target='_blank'>View Sequence</a></td>";
		$net_info_html .= "<tr><td>E-Value</td><td>" . $generate->get_evalue() . "</td></tr>";
                $net_info_html .= "<tr><td>Maximum Blast Sequences</td><td>" . number_format($generate->get_submitted_max_sequences()) . "</td></tr>";

	}
        elseif ($generate->get_type() == "FAMILIES") {
 		$generate = new generate($db,$_GET['id']);
		$net_info_html = "<td>PFam/Interpro Families</td>";
		$net_info_html .= "<td>" . $generate->get_families_comma() . "</td>";
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

	$analysis_id = $_GET['analysis_id'];
        $analysis = new analysis($db,$analysis_id);

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
			$full_network_html .= "<td style='text-align:center;'><a href='" . $path . "'><button>Download</button></a></td>\n";
                	$full_network_html .= "<td style='text-align:center;'>" . number_format($stats[$i]['Nodes'],0) . "</td>\n";
	                $full_network_html .= "<td style='text-align:center;'>" . number_format($stats[$i]['Edges'],0) . "</td>\n";
        	        $full_network_html .= "<td style='text-align:center;'>" . functions::bytes_to_megabytes($stats[$i]['Size'],0) . " MB</td>\n";
                	$full_network_html .= "</tr>";
		}
		else {
			$percent_identity = substr($stats[$i]['File'],strpos($stats[$i]['File'],'-')+1);
			$percent_identity = substr($percent_identity,0,strrpos($percent_identity,'.'));
			$percent_identity = str_replace(".","",$percent_identity);
			$path = functions::get_web_root() . "/results/" . $analysis->get_output_dir() . "/" . $analysis->get_network_dir() . "/" . $stats[$i]['File'];
			$rep_network_html .= "<tr>";
			$rep_network_html .= "<td style='text-align:center;'><a href='" . $path . "'><button>Download</button></a></td>\n";
			$rep_network_html .= "<td style='text-align:center;'>" . $percent_identity . "</td>\n";
			$rep_network_html .= "<td style='text-align:center;'>" . number_format($stats[$i]['Nodes'],0) . "</td>\n";
			$rep_network_html .= "<td style='text-align:center;'>" . number_format($stats[$i]['Edges'],0) . "</td>\n";
			$rep_network_html .= "<td style='text-align:center;'>" . functions::bytes_to_megabytes($stats[$i]['Size'],0) . " MB</td>\n";
			$rep_network_html .= "</tr>";
		}
	}


}

else {

        echo "No EFI-EST Select.  Please go back";
        exit;

}


?>	

<img src="images/quest_stages_e.jpg" width="990" height="119" alt="stage 1">
   <hr>

		
<h3>Download Network Files</h3>
	<p>&nbsp;</p>
	<p><b>If you use an SSN from EFI-EST, please cite <a href='tutorial_references.php'>Reference #6 Gerlt <i>et al.</i></a></b></p>
	<p>&nbsp;</p>
	<h4>Network Information</h4>
	    <table width="100%" border="1">
	<tr>
		<?php echo $net_info_html; ?>
	</tr>
	<tr>
		<td>Network Name</td>
		<td><?php echo $analysis->get_name(); ?></td>
	</tr>
	<tr>
		<td>Alignment Score</td>
		<td><?php echo $analysis->get_evalue(); ?></td>
	</tr>
	<tr>
		<td>Minimum Length</td>
		<td><?php echo number_format($analysis->get_min_length()); ?></td>
	</tr>
	<tr>
		<td>Maximum Length</td>
		<td><?php echo number_format($analysis->get_max_length()); ?></td>
	</tr>
	<tr>
		
		<td>Number of Filtered Sequences</td>
		<td><?php echo number_format($analysis->get_num_sequences_post_filter()); ?></td>
		
	</tr>
	<tr>
		<td>Total Number of Sequences</td>
		<td><?php echo number_format($generate->get_num_sequences()); ?></td>
	</tr>
	<tr>
		<td>Download Statistics</td>
		<td><a href='<?php echo $analysis->get_stats_full_path(); ?>'><button>Download</button></a></td> 
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
<?php include_once 'includes/footer.inc.php'; ?>

