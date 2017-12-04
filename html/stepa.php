<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
include_once 'includes/quest_acron.inc';
include_once '../libs/functions.class.inc.php';

$max_file_size = ini_get('post_max_size');


function make_upload_box($title, $file_id, $progress_bar_id, $progress_num_id) {
    global $max_file_size;
    return <<<HTML
                <div>
                    $title:
                    <input type='file' name='$file_id' id='$file_id' data-url='server/php/' class="input_file">
                    <label for="$file_id" class="file_upload"><img src="images/upload.svg" /> <span>Choose a file&hellip;</span></label>
                    <progress id='$progress_bar_id' max='100' value='0'></progress>
                </div>
                <br><div id="$progress_num_id"></div>
                Maximum size is $max_file_size.
HTML;
}

function make_pfam_size_box($parent_id, $table_id) {
    return <<<HTML
<center>
        <div style="width:80%;display:none" id="$parent_id">
            <table border="0" width="100%">
                <thead>
                    <th>Family</th>
                    <th>Family Name</th>
                    <th>Full Size</th>
                    <th>UniRef90 Size</th>
                    <th>UniRef50 Size</th>
                </thead>
                <tbody id="$table_id"></tbody>
            </table>
        </div>
</center>
HTML;
}


$neighbor_size_html = "";
$default_neighbor_size = functions::get_default_neighbor_size();
for ($i=3;$i<=20;$i++) {
	if ($i == $default_neighbor_size) {
		$neighbor_size_html .= "<option value='" . $i . "' selected='selected'>" . $i . "</option>";
	}
	else {
		$neighbor_size_html .= "<option value='" . $i . "'>" . $i . "</option>";
	}
}

$max_seq_num = functions::get_max_seq();
$max_seq_formatted = number_format($max_seq_num, 0);


?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-51157342-1', 'illinois.edu');
  ga('send', 'pageview');

</script>

<div class="update_message">
    The EST database has been updated to use UniProt
    <?php echo functions::get_uniprot_version(); ?> and InterPro
    <?php echo functions::get_interpro_version(); ?>.
</div>

<h3>First step of SSN generation: Input selection</h3>
<br>
Define the set of sequences to be used in the all-by-all BLAST. The similarity between the defined set of sequences will be calulated.
Four input methods are available. A utility for SSN coloring and analysis is also available.

<hr>
<img src="images/quest_stages_a.jpg" width="990" height="119" alt="stage 1">
<hr>
<h4>Input <a href="tutorial_startscreen.php" class="question" target="_blank">?</a></h4>
<form id='stepa' name="stepa" method="post" action="" enctype="multipart/form-data">
<input type="hidden" id='MAX_FILE_SIZE' name="MAX_FILE_SIZE" value="2147483648" />

<?php if (functions::option_a_enabled()) { ?>
<p class='align_left'>
    <input type='radio' id='option_selected_a' name='option_selected' value='A' onChange='disable_forms();'>
    <b>Option A: Single sequence</b><br>
    The provided sequence is used as the query for a BLAST search of the UniProt database and then, the
    similarities between the sequences are calculated and used to generate the SSN.  Submit only one
    protein sequence without FASTA header. The default maximum number of retrieved sequences is <?php echo number_format(functions::get_default_blast_seq(),0); ?>.
<fieldset id='option_a'>
<textarea class="blast_inputs" id='blast_input' name='blast_input'><?php if (isset($_POST['blast_input'])) { echo $_POST['blast_input']; } ?></textarea>
<p class='align_left'><a href='javascript:toggle_blast_advanced();'>Advanced Options<span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
<br><div id="blast_advanced" style="display: none;">
<p class='align_left'>E-Value: <input type='text' class='small' id='blast_evalue' name='blast_evalue' value='<?php if (isset($_POST['blast_evalue'])) { echo $_POST['blast_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge; 1; default: <?php echo functions::get_evalue(); ?>)</p>
<p class='align_left'>Maximum Blast Sequences: <input type='text' id='blast_max_seqs' class='small' name='blast_max_seqs' value='<?php if (isset($_POST['blast_max_seqs'])) { echo $_POST['blast_max_seqs']; } else { echo functions::get_default_blast_seq(); } ?>'> Maximum number of sequences retrieved (&le; <?php echo functions::get_max_blast_seq(); ?>; default: <?php echo functions::get_default_blast_seq(); ?>)</p>
</div>
</fieldset>
<?php } ?>


<hr>


<?php if (functions::option_b_enabled()) { ?>
<p class='align_left'>
    <input type='radio' id='option_selected_b' name='option_selected' value='B' onChange='disable_forms();'>
    <b>Option B: Pfam and/or InterPro families</b><br>
     The sequences from the Pfam and/or InterPro families are retrieved, and then, the similarities between the
    sequences are calculated and used to generate the SSN.
    For Pfam families, the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the
    format is IPRxxxxxx (six digits). The maximum number of retrieved sequences is <?php echo $max_seq_formatted; ?>.
</p>
<fieldset id='option_b'>
<input type='text' id='families_input' name='families_input' class='blast_inputs'
    value='<?php if (isset($_POST['families_input'])) { echo $_POST['families_input']; } ?>'
    oninput="checkFamilyInput('families_input','family_size_container','family_count_table','families_input',<?php echo $max_seq_num; ?>)"><br>
<?php echo make_pfam_size_box('family_size_container', 'family_count_table'); ?> 

<p class='align_left'><a href='javascript:toggle_pfam_advanced();'>Advanced Options<span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
<br><div id="pfam_advanced" style="display: none;">
<p class='align_left'>E-Value: <input type='text' class='small' id='pfam_evalue' name='pfam_evalue' value='<?php if (isset($_POST['pfam_evalue'])) { echo $_POST['pfam_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default <?php echo functions::get_evalue(); ?>))</p>
<p class='align_left'>Fraction: <input type='text' class='small' id='pfam_fraction' name='pfam_fraction' value='<?php if (isset($_POST['pfam_fraction'])) { echo $_POST['pfam_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; (default: <?php echo functions::get_fraction(); ?>))</p>
<p class='align_left'>Enable Domain: <input type='checkbox' id='pfam_domain' name='pfam_domain' value='1' <?php if (isset($_POST['pfam_domain']) && ($_POST['pfam_domain'] == "1")) { echo "checked='checked'"; } ?> > Check to generate SSN with Pfam-defined domains (default: off)</p>
<p class='align_left'>Sequence Identity: <input type='text' class='small' id='pfam_seqid' name='pfam_seqid' value='<?php if (isset($_POST['pfam_seqid'])) { echo $_POST['pfam_seqid']; } else { echo "1"; } ?>'> Sequence identity (&le; 1; (default: 1))</p>
<p class='align_left'>Sequence Length Overlap: <input type='text' class='small' id='pfam_length_overlap' name='pfam_length_overlap' value='<?php if (isset($_POST['pfam_length_overlap'])) { echo $_POST['pfam_length_overlap']; } else { echo "1"; } ?>'> Sequence length overlap (&le; 1; (default: 1))</p>
<p class='align_left'>Randomize fractions: <input type='checkbox' id='pfam_random_fraction' name='pfam_random_fraction' value='1' <?php if (isset($_POST['pfam_random_fraction']) && ($_POST['pfam_random_fraction'] == "1")) { echo "checked='checked'"; } ?> > Check to randomize fractions</p>
<!--<p class='align_left'>UniRef Version: 
    <select name="pfam_uniref_version" id="pfam_uniref_version">
        <option value="None" selected='selected'>None</option>
        <option value="50">UniRef50</option>
        <option value="90">UniRef90</option>
    </select>
</p>-->
<?php    if (functions::get_program_selection_enabled()) { ?>
<p class='align_left'>Select Program to use: 
<select name='option_b_program' id='option_b_program'>
        <option value='BLAST'>Blast</option>
        <option value='BLAST+'>Blast+</option>
        <option selected='selected' value='DIAMOND'>Diamond</option>
	<option value='DIAMONDSENSITIVE'>Diamond Sensitive</option>
</select></p>
<?php    } ?>

</div>

</fieldset>
<?php } ?>

<hr>
<?php if (functions::option_c_enabled()) { ?>
<p class='align_left'>
    <input type='radio' id='option_selected_c' name='option_selected' value='C' onChange='disable_forms();'>
    <b>Option C: User-supplied set of sequences</b><br>
    The similarities between the provided sequences will be calculated and used to generate the SSN.
    Input a list of protein sequences in FASTA format with headers, or upload a FASTA file.
    
    <fieldset id='option_c'>
<?php    if (functions::option_e_enabled()) { ?>
            <p class='align_left'>
                <input type='checkbox' id='fasta_use_headers' name='fasta_use_headers' value='1'> <b>Read FASTA headers</b><br>
                 When selected, recognized UniProt or Genbank identifiers from FASTA headers are used to retrieve corresponding node attributes from the UniProt database.
            </p>
<?php    } ?>
        <textarea class="blast_inputs" id='fasta_input' name='fasta_input'><?php if (isset($_POST['fasta_input'])) { echo $_POST['fasta_input']; } ?></textarea>
        <p>
            <?php echo make_upload_box("FASTA File", "fasta_file", "progress_bar_fasta", "progressNumberFasta"); ?>
        </p>
        <p class='align_left'>
            If desired, include a Pfam and/or InterPro families, in the analysis of your FASTA file. For Pfam families,
            the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is
            IPRxxxxxx (six digits).
        </p>
        <input type='text' id='families_input2' name='families_input2' class='blast_inputs' value='<?php if (isset($_POST['families_input2'])) { echo $_POST['families_input2']; } ?>'
            oninput="checkFamilyInput('families_input2','family_size_container_optc','family_count_table_optc','families_input2',<?php echo $max_seq_num; ?>)"><br>
<?php echo make_pfam_size_box('family_size_container_optc', 'family_count_table_optc'); ?> 
        <p class='align_left'>
            <a href='javascript:toggle_fasta_advanced();'>Advanced Options
                <span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a>
        </p>
        <br>
        <div id="fasta_advanced" style="display: none;">
            <p class='align_left'>
                E-Value: <input type='text' class='small' id='fasta_evalue' name='fasta_evalue' value='<?php if (isset($_POST['fasta_evalue'])) { echo $_POST['fasta_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default: <?php echo functions::get_evalue(); ?>)
            </p>
            <p class='align_left'>
                Fraction: <input type='text' class='small' id='fasta_fraction' name='fasta_fraction' value='<?php if (isset($_POST['fasta_fraction'])) { echo $_POST['fasta_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; default: <?php echo functions::get_fraction(); ?>)
            </p>
<?php    if (functions::get_program_selection_enabled()) { ?>
            <p class='align_left'>
                Select Program to use:
                <select name='option_c_program' id='option_c_program'>
                    <option value='BLAST'>Blast</option>
                    <option value='BLAST+'>Blast+</option>
                    <option selected='selected' value='DIAMOND'>Diamond</option>
                	<option value='DIAMONDSENSITIVE'>Diamond Sensitive</option>
                </select>
            </p>
<?php    } ?>
        </div>
    </fieldset>
</p>
<?php } ?>

<?php if (functions::option_d_enabled()) { ?>
<hr>
<p class='align_left'>
    <input type='radio' id='option_selected_d' name='option_selected' value='D' onChange='disable_forms();'>
    <b>Option D: List of UniProt and/or NCBI IDs</b><br>
    The sequences and attributes corresponding to the recognized identifiers are retreived, and then, the similarities
    between the sequences are calculated and used to generate the SSN. Input a list of Uniprot, NCBI, or Genbank
    sequence accession IDs, or upload a text file containing the accession IDs. 
    <fieldset id='option_d'>
        <p class='align_left'>
            Input a list of Uniprot, NCBI, or Genbank sequence accession IDs, and/or upload a text file containing
            the accession IDs.
        </p>
        <textarea class="blast_inputs" id='accession_input' name='accession_input'><?php if (isset($_POST['accession_input'])) { echo $_POST['accession_input']; } ?></textarea>
        <p>
            <?php echo make_upload_box("Accession ID File", "accession_file", "progress_bar_accession", "progressNumberAccession"); ?>
        </p>
        <p class='align_left'>
            If desired, include a Pfam and/or InterPro families, in the analysis of your file. For Pfam families,
            the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is
            IPRxxxxxx (six digits).
        </p>
        <input type='text' id='families_input4' name='families_input4' class='blast_inputs' value='<?php if (isset($_POST['families_input4'])) { echo $_POST['families_input4']; } ?>'
            oninput="checkFamilyInput('families_input4','family_size_container_optd','family_count_table_optd','families_input4',<?php echo $max_seq_num; ?>)"><br>
<?php echo make_pfam_size_box('family_size_container_optd', 'family_count_table_optd'); ?> 
        <p class='align_left'>
            <a href='javascript:toggle_accession_advanced();'>Advanced Options
                <span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a>
        </p>
        <br>
        <div id="accession_advanced" style="display: none;">
            <p class='align_left'>E-Value: <input type='text' class='small' id='accession_evalue' name='accession_evalue' value='<?php if (isset($_POST['accession_evalue'])) { echo $_POST['accession_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default: <?php echo functions::get_evalue(); ?>)</p>
            <p class='align_left'>Fraction: <input type='text' class='small' id='accession_fraction' name='accession_fraction' value='<?php if (isset($_POST['accession_fraction'])) { echo $_POST['accession_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; default: <?php echo functions::get_fraction(); ?>)</p>
            <p class='align_left'>Expand UniRef homologs: 
                <input type='checkbox' id='accession_use_uniref' name='accession_use_uniref'
                        <?php if (isset($_POST['accession_use_uniref']) && ($_POST['accession_use_uniref'] == "1")) { echo "checked='checked'"; } ?>
                        onchange="toggleUniref('accession_uniref_version', this)"
                >
                Check to expand the homologs for any input sequences that are UniRef seed sequences (default: off)
            </p>
            <p class='align_left'>UniRef Version: 
                <select name="accession_uniref_version" id="accession_uniref_version" disabled="disabled">
                    <option value="50">UniRef50</option>
                    <option value="90">UniRef90</option>
                </select>
            </p>
<?php    if (functions::get_program_selection_enabled()) { ?>
            <p class='align_left'>Select Program to use:
                <select name='option_d_program' id='option_d_program'>
                    <option value='BLAST'>Blast</option>
                    <option value='BLAST+'>Blast+</option>
                    <option selected='selected' value='DIAMOND'>Diamond</option>
                	<option value='DIAMONDSENSITIVE'>Diamond Sensitive</option>
                </select>
            </p>
<?php    } ?>
        </div>
    </fieldset>
</p>
<?php } ?>

<hr>


<?php if (functions::option_e_enabled()) { ?>
<p class='align_left'>
    <input type='radio' id='option_selected_e' name='option_selected' value='E' onChange='disable_forms();'>
    <b>Option E: Pfam and/or InterPro families with advanced options</b><br>
     The sequences from the Pfam and/or InterPro families are retrieved, and then, the similarities between the
    sequences are calculated and used to generate the SSN. Advanced options, including specifying the use of
    UniRef, sequence identity, and sequence length are provided.
    For Pfam families, the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the
    format is IPRxxxxxx (six digits). The maximum number of retrieved sequences is <?php echo $max_seq_formatted; ?>.
</p>
<fieldset id='option_e'>
<input type='text' id='pfam_plus_families' name='pfam_plus_families' class='blast_inputs'
    value='<?php if (isset($_POST['pfam_plus_families'])) { echo $_POST['pfam_plus_families']; } ?>'
    oninput="checkFamilyInput('pfam_plus_families','family_size_container_plus','family_count_table_plus','pfam_plus_families',<?php echo $max_seq_num; ?>)"><br>
<?php echo make_pfam_size_box('family_size_container_plus', 'family_count_table_plus'); ?> 

<p class='align_left'><a href='javascript:toggle_pfam_plus_advanced();'>Advanced Options<span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
<br><div id="pfam_plus_advanced" style="display: none;">
<p class='align_left'>E-Value: <input type='text' class='small' id='pfam_plus_evalue' name='pfam_plus_evalue' value='<?php if (isset($_POST['pfam_plus_evalue'])) { echo $_POST['pfam_plus_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default <?php echo functions::get_evalue(); ?>))</p>
<p class='align_left'>Fraction: <input type='text' class='small' id='pfam_plus_fraction' name='pfam_plus_fraction' value='<?php if (isset($_POST['pfam_plus_fraction'])) { echo $_POST['pfam_plus_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; (default: <?php echo functions::get_fraction(); ?>))</p>
<p class='align_left'>Enable Domain: <input type='checkbox' id='pfam_plus_domain' name='pfam_plus_domain' value='1' <?php if (isset($_POST['pfam_plus_domain']) && ($_POST['pfam_plus_domain'] == "1")) { echo "checked='checked'"; } ?> > Check to generate SSN with Pfam-defined domains (default: off)</p>
<p class='align_left'>Sequence Identity: <input type='text' class='small' id='pfam_plus_seqid' name='pfam_plus_seqid' value='<?php if (isset($_POST['pfam_plus_seqid'])) { echo $_POST['pfam_plus_seqid']; } else { echo "1"; } ?>'> Sequence identity (&le; 1; (default: 1))</p>
<p class='align_left'>Sequence Length Overlap: <input type='text' class='small' id='pfam_plus_length_overlap' name='pfam_plus_length_overlap' value='<?php if (isset($_POST['pfam_plus_length_overlap'])) { echo $_POST['pfam_plus_length_overlap']; } else { echo "1"; } ?>'> Sequence length overlap (&le; 1; (default: 1))</p>
<p class='align_left'>Do not demultiplex: <input type='checkbox' id='pfam_plus_demux' name='pfam_plus_demux' value='1' <?php if (isset($_POST['pfam_plus_demux']) && ($_POST['pfam_plus_demux'] == "1")) { echo "checked='checked'"; } ?> > Check to prevent a demultiplex to expand cd-hit clusters (default: demultiplex)</p>
<p class='align_left'>Randomize fractions: <input type='checkbox' id='pfam_plus_random_fraction' name='pfam_plus_random_fraction' value='1' <?php if (isset($_POST['pfam_plus_random_fraction']) && ($_POST['pfam_plus_random_fraction'] == "1")) { echo "checked='checked'"; } ?> > Check to randomize fractions</p>
<p class='align_left'>UniRef Version: 
    <select name="pfam_plus_uniref_version" id="pfam_plus_uniref_version">
        <option value="None" selected='selected'>None</option>
        <option value="50">UniRef50</option>
        <option value="90">UniRef90</option>
    </select>
</p>
<?php    if (functions::get_program_selection_enabled()) { ?>
<p class='align_left'>Select Program to use: 
<select name='option_b_program' id='option_b_program'>
        <option value='BLAST'>Blast</option>
        <option value='BLAST+'>Blast+</option>
        <option selected='selected' value='DIAMOND'>Diamond</option>
	<option value='DIAMONDSENSITIVE'>Diamond Sensitive</option>
</select></p>
<?php    } ?>

</div>

</fieldset>
<?php } ?>

<?php //////////////////////////////////////////////////////////////////////////////////////////////////// ?>
<?php if (functions::colorssn_enabled()) { ?>

<a name="colorssn"></a>

<hr>

<h4>Utility for SSN Coloring and Analysis</h4>

<hr>
<p class='align_left'>
    <input type='radio' id='option_selected_colorssn' name='option_selected' value='colorssn' onChange='disable_forms();'>
    <b>Color SSN Utility: Color a previously generated SSN and return associated cluster data.</b><br>
    Independent sequence clusters in the uploaded SSN are identified, numbered and colored. Summary tables,
    sets of IDs and sequences for specific clusters are provided. A Cytoscape-edited SNN can serve as input for this utility.
    In order for all of the new features to work correctly, SSNs generated by EFI-EST <?php echo functions::get_est_version(); ?>
    (released 8/17/2017) should be used. 
    <fieldset id='option_colorssn'>
        <p>
            <?php echo make_upload_box("SNN to color and analyze (uncompressed or zipped XGMML file)", "colorssn_file", "progress_bar_colorssn", "progressNumberColorSsn"); ?>
        </p>
    </fieldset>
</p>

<?php } ?>


<?php //////////////////////////////////////////////////////////////////////////////////////////////////// ?>
<?php /* ?>
<hr>

<h4>Family Count Inquiry Tool</h4>

<hr>
<p class='align_left'>
    This tool returns the size of the families for the Interpro, Pfam, Gene3D, or SSF databases.
</p>
<p class='align_left'>
    Input a list of families:
    <div>
        <div style="float: left; width: 50%">
            <textarea rows="10" cols="50" id="familyCountInput"></textarea>
        </div>
        <div style="float: right; width: 50%">
            <table border="0" width="100%">
                <thead>
                    <th>Family</th>
                    <th>Size</th>
                </thead>
                <tbody id="family_count_table"></tbody>
            </table>
        </div>
    </div>
    <div style="clear: both; padding-top: 20px; height: 50px">
        <button onclick="getFamilyCounts('familyCountInput','family_count_table');" class="family_count_btn" type="button">Query</button>
    </div>
</p>
<?php */ ?>

<hr>
<p><br><input type="text" id='email' name='email' value='<?php if (isset($_POST['email'])) { echo $_POST['email']; } else { echo "Enter your email address"; } ?>' i
	class="blast_inputs email" id='email' onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
<span class="smalltext">Used for data retrieval only</span>
</p>
<div id='message' style='font-weight:bold;font-size:130%;color:red'><?php if (isset($message)) { echo "<h4 class='center' style='color:red'>" . $message . "</h4>"; } ?></div>

<input type="button" id='submit' name="submit" value="Submit Analysis" class="css_btn_class_recalc" onclick="uploadFile()">

<div class="update_message">
    The EST database has been updated to use UniProt
    <?php echo functions::get_uniprot_version(); ?> and InterPro
    <?php echo functions::get_interpro_version(); ?>.
</div>

<hr>
<?php if (functions::is_beta_release()) { ?><h4><b><span style="color: blue">BETA</span></b></h4><?php } ?>
</form>
<P>View Example - <a href='stepc_example.php'>Click Here</a></p>
<p><center>InterPro Version: <b><?php echo functions::get_interpro_version(); ?></b></center></p>
<p><center>UniProt Version: <b><?php echo functions::get_uniprot_version(); ?></b></center></p>
<p><center>EFI-EST Version: <b><?php echo functions::get_est_version(); ?></b></center></p>
    
</div>
<script> 
disable_forms();
</script>

<script src="includes/family_counts.js" type="text/javascript"></script>
<script src="includes/custom-file-input.js" type="text/javascript"></script>

<?php include_once 'includes/footer.inc.php'; ?>
