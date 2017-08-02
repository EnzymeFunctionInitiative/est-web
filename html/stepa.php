<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
include_once 'includes/quest_acron.inc';
include_once '../libs/functions.class.inc.php';


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

$maxSeqNum = functions::get_max_seq();
$maxSeqFormatted = number_format($maxSeqNum, 0);

?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-51157342-1', 'illinois.edu');
  ga('send', 'pageview');

</script>

<!--<h3>Start With...    </h3>-->
<h3>First step of SSN generation: Input selection</h3>
<!--<h4>An Introduction</h4>
<p>Start here if you are new to the &quot;Enzyme Similarity Tool&quot;.</p>
<center><a href='index.php'><button type='button' class='css_btn_class'>Web Tutorial</button></a>
<a href='http://dx.doi.org/10.1016/j.bbapap.2015.04.015'><button type='button' class='css_btn_class'>Review Article</button></a></center>
-->
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
<!--    Generate data set of close relatives via BLAST.  Enter only protein sequence.  Do not enter any FASTA header information. (Maximum number sequences retrieved: <?php echo number_format(functions::get_max_blast_seq(),0); ?>).</p>-->
<fieldset id='option_a'>
<textarea class="blast_inputs" id='blast_input' name='blast_input'><?php if (isset($_POST['blast_input'])) { echo $_POST['blast_input']; } ?></textarea>
<p class='align_left'><a href='javascript:toggle_blast_advanced();'>Advanced Options<!-- (see tutorial)--> <span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
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
    format is IPRxxxxxx (six digits). The maximum number of retrieved sequences is <?php echo $maxSeqFormatted; ?>.
    <!--
        Generate data set with Pfam and/or InterPro numbers. For Pfam families, the format is a comma separated list of
        PFxxxxx (five digits); for InterPro families, the format is IPRxxxxxx (six digits).  The maximum number sequences
        retrieved is <?php echo $maxSeqFormatted; ?>. To identify the Pfam and/or InterPro number from a BLAST sequence, please go to <a href='<?php echo functions::get_interpro_website(); ?>' target='_blank'><?php echo functions::get_interpro_website(); ?></a>.
    -->
</p>
<fieldset id='option_b'>
<input type='text' id='families_input' name='families_input' class='blast_inputs'
    value='<?php if (isset($_POST['families_input'])) { echo $_POST['families_input']; } ?>'
    oninput="checkFamilyInput('families_input','family_size_container','family_count_table','families_input',<?php echo $maxSeqNum; ?>)"><br>

<center>
        <div style="width:50%;display:none" id="family_size_container">
            <table border="0" width="100%">
                <thead>
                    <th>Family</th>
                    <th>Size</th>
                </thead>
                <tbody id="family_count_table"></tbody>
            </table>
        </div>
</center>
<!--<center><div class="pfam_size" id="family_size_container">&nbsp;</div></center>-->

<p class='align_left'><a href='javascript:toggle_pfam_advanced();'>Advanced Options<!-- (see tutorial)--><span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
<br><div id="pfam_advanced" style="display: none;">
<p class='align_left'>E-Value: <input type='text' class='small' id='pfam_evalue' name='pfam_evalue' value='<?php if (isset($_POST['pfam_evalue'])) { echo $_POST['pfam_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default <?php echo functions::get_evalue(); ?>)</p>
<p class='align_left'>Fraction: <input type='text' class='small' id='pfam_fraction' name='pfam_fraction' value='<?php if (isset($_POST['pfam_fraction'])) { echo $_POST['pfam_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; (default: <?php echo functions::get_fraction(); ?>)</p>
<p class='align_left'>Enable Domain: <input type='checkbox' id='pfam_domain' name='pfam_domain' value='1' <?php if (isset($_POST['pfam_domain']) && ($_POST['pfam_domain'] == "1")) { echo "checked='checked'"; } ?>' > Check to generate SSN with Pfam-defined domains (default: off)</p>
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
    <!--Generate data set with custom FASTA file. Maximum size is-->
    
    <fieldset id='option_c'>
<!--
        <p>
            Input a list of protein sequences in the FASTA format with headers, and/or upload a FASTA file.
        </p>
-->
<?php    if (functions::option_e_enabled()) { ?>
            <p class='align_left'>
                <input type='checkbox' id='fasta_use_headers' name='fasta_use_headers' value='1'> <b>Read FASTA headers</b><br>
                 When selected, recognized UniProt or Genbank identifiers from FASTA headers are used to retrieve corresponding node attributes from the UniProt database.
<!--                Check to use IDs from FASTA headers to retrieve node attributes when possible (default: off).-->
            </p>
<?php    } ?>
        <textarea class="blast_inputs" id='fasta_input' name='fasta_input'><?php if (isset($_POST['fasta_input'])) { echo $_POST['fasta_input']; } ?></textarea>
        <p>
            FASTA File:
            <input type='file' name='fasta_file' id='fasta_file' data-url='server/php/'>
            <progress id='progress_bar_fasta' max='100' value='0'></progress>
            <br><div id="progressNumberFasta"></div> 
            Maximum size is <?php echo ini_get('post_max_size'); ?>b.
        </p>
        <p class='align_left'>
            If desired, include a Pfam and/or InterPro families, in the analysis of your FASTA file. For Pfam families,
            the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is
            IPRxxxxxx (six digits).
        </p>
        <input type='text' id='families_input2' name='families_input2' class='blast_inputs' value='<?php if (isset($_POST['families_input2'])) { echo $_POST['families_input2']; } ?>'>
        <p class='align_left'>
            <a href='javascript:toggle_fasta_advanced();'>Advanced Options<!-- (see
                tutorial)--><span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a>
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
    <!--Generate data set from a file with a list of Uniprot, NCBI, or Genbank sequence accession IDs.
    Maximum size is <?php echo ini_get('post_max_size'); ?>b.-->
    <fieldset id='option_d'>
        <p class='align_left'>
            Input a list of Uniprot, NCBI, or Genbank sequence accession IDs, and/or upload a text file containing
            the accession IDs.
        </p>
        <textarea class="blast_inputs" id='accession_input' name='accession_input'><?php if (isset($_POST['accession_input'])) { echo $_POST['accession_input']; } ?></textarea>
        <p>
            Accession ID File: <input type='file' name='accession_file' id='accession_file' data-url='server/php/'>
            <progress id='progress_bar_accession' max='100' value='0'></progress>
            <br><div id="progressNumberAccession"></div>
            Maximum size is <?php echo ini_get('post_max_size'); ?>b.
        </p>
        <p class='align_left'>
            If desired, include a Pfam and/or InterPro families, in the analysis of your file. For Pfam families,
            the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is
            IPRxxxxxx (six digits).
        </p>
        <input type='text' id='families_input4' name='families_input4' class='blast_inputs' value='<?php if (isset($_POST['families_input4'])) { echo $_POST['families_input4']; } ?>'>
        <p class='align_left'>
            <a href='javascript:toggle_accession_advanced();'>Advanced Options<!--(see 
                tutorial)--><span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a>
        </p>
        <br>
        <div id="accession_advanced" style="display: none;">
            <p class='align_left'>E-Value: <input type='text' class='small' id='accession_evalue' name='accession_evalue' value='<?php if (isset($_POST['accession_evalue'])) { echo $_POST['accession_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default: <?php echo functions::get_evalue(); ?>)</p>
            <p class='align_left'>Fraction: <input type='text' class='small' id='accession_fraction' name='accession_fraction' value='<?php if (isset($_POST['accession_fraction'])) { echo $_POST['accession_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; default: <?php echo functions::get_fraction(); ?>)</p>
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
    <!--Color an input SSN and return associated cluster data.-->
    <fieldset id='option_colorssn'>
        <p>
            SNN to color and analyze (uncompressed or zipped XGMML file):
            <input type='file' name='colorssn_file' id='colorssn_file' data-url='server/php/'>
            <progress id='progress_bar_colorssn' max='100' value='0'></progress>
            <br>
            <div id="progressNumberColorSsn"></div>
            Maximum size is <?php echo ini_get('post_max_size'); ?>b.
<?php /* ?>
            <p>
                Neighborhood Size (default: <?php echo $default_neighbor_size; ?>)
                <select name='neighbor_size' id='neighbor_size'>
                    <?php echo $neighbor_size_html; ?>
                </select>
            </p>
            <p>
                <label for='cooccurrence_input'>
                    Input % Co-Occurrence Lower Limit (Default: <?php echo functions::get_default_cooccurrence(); ?>, Valid 1-100):
                </label>
                <input type='text' id='cooccurrence' name='cooccurrence' maxlength='3' value='<?php echo functions::get_default_cooccurrence(); ?>'><br>
            </p>
            <p>
                If desired, include a Pfam and/or InterPro families, in the analysis of your file. For Pfam families,
                the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is 
                IPRxxxxxx (six digits).
            </p>
            <input type='text' id='families_input4' name='families_input4' class='blast_inputs' value='<?php if (isset($_POST['families_input4'])) { echo $_POST['families_input4']; } ?>'>
            <p class='align_left'>
                <a href='javascript:toggle_accession_advanced();'>Advanced Options (see
                tutorial)<span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a>
            </p>
            <br>
            <div id="accession_advanced" style="display: none;">
                <p class='align_left'>
                    E-Value: 
                    <input type='text' class='small' id='accession_evalue' name='accession_evalue' value='<?php if (isset($_POST['accession_evalue'])) { echo $_POST['accession_evalue']; } else { echo functions::get_evalue(); } ?>'>
                    Negative log of e-value for all-by-all BLAST (&ge;1; default: <?php echo functions::get_evalue(); ?>)
                </p>
                <p class='align_left'>
                    Fraction:
                    <input type='text' class='small' id='accession_fraction' name='accession_fraction' value='<?php if (isset($_POST['accession_fraction'])) { echo $_POST['accession_fraction']; } else { echo functions::get_fraction(); } ?>'>
                    Fraction of sequences in Pfam/Interpro family for network (&ge; 1; default: <?php echo functions::get_fraction(); ?>)
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
<?php */ ?>
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
<div id='message'><?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?></div>

<input type="button" id='submit' name="submit" value="Submit Analysis" class="css_btn_class_recalc" onclick="uploadFile()">
<hr>
       <h4><b><span style="color: blue">BETA</span></b></h4> 
</form>
<P>View Example - <a href='stepc_example.php'>Click Here</a></p>
<h4>InterPro Version: <b><?php echo functions::get_interpro_version(); ?></b></h4>
<h4>UniProt Version: <b><?php echo functions::get_uniprot_version(); ?></b></h4>
    
</div>
<script> 
disable_forms();
</script>

<script src="includes/family_counts.js" type="text/javascript"></script>

<?php include_once 'includes/footer.inc.php'; ?>
