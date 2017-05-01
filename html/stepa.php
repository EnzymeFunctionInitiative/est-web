<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
include_once 'includes/quest_acron.inc';

?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-51157342-1', 'illinois.edu');
  ga('send', 'pageview');

</script>

<h3>Start With...    </h3>
<h4>An Introduction</h4>
<p>Start here if you are new to the &quot;Enzyme Similarity Tool&quot;.</p>
<center><a href='index.php'><button type='button' class='css_btn_class'>Web Tutorial</button></a>
<a href='http://dx.doi.org/10.1016/j.bbapap.2015.04.015'><button type='button' class='css_btn_class'>Review Article</button></a></center>

<hr>
<img src="images/quest_stages_a.jpg" width="990" height="119" alt="stage 1">
<hr>
<h4>Input<a href="tutorial_startscreen.php" class="question" target="_blank">?</a></h4>
<form id='stepa' name="stepa" method="post" action="" enctype="multipart/form-data">
<input type="hidden" id='MAX_FILE_SIZE' name="MAX_FILE_SIZE" value="2147483648" />

<?php if (functions::option_a_enabled()) { ?>
<p class='align_left'><input type='radio' id='option_selected_a' name='option_selected' value='A' onChange='disable_forms();'><b>Option A:</b> Generate data set of close relatives via BLAST.  Enter only protein sequence.  Do not enter any FASTA header information. (Maximum number sequences retrieved: <?php echo number_format(functions::get_max_blast_seq(),0); ?>).</p>
<fieldset id='option_a'>
<textarea class="blast_inputs" id='blast_input' name='blast_input'><?php if (isset($_POST['blast_input'])) { echo $_POST['blast_input']; } ?></textarea>
<p class='align_left'><a href='javascript:toggle_blast_advanced();'>Advanced Options (see tutorial) <span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
<br><div id="blast_advanced" style="display: none;">
<p class='align_left'>E-Value: <input type='text' class='small' id='blast_evalue' name='blast_evalue' value='<?php if (isset($_POST['blast_evalue'])) { echo $_POST['blast_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge; 1; default: <?php echo functions::get_evalue(); ?>)</p>
<p class='align_left'>Maximum Blast Sequences: <input type='text' id='blast_max_seqs' class='small' name='blast_max_seqs' value='<?php if (isset($_POST['blast_max_seqs'])) { echo $_POST['blast_max_seqs']; } else { echo functions::get_default_blast_seq(); } ?>'> Maximum number of sequences retrieved (&le; <?php echo functions::get_max_blast_seq(); ?>; default: <?php echo functions::get_default_blast_seq(); ?>)</p>
</div>
</fieldset>
<?php } ?>
<hr>
<?php if (functions::option_b_enabled()) { ?>
<p class='align_left'><input type='radio' id='option_selected_b' name='option_selected' value='B' onChange='disable_forms();'><b>Option B:</b> Generate data set with Pfam and/or InterPro numbers. For Pfam families, the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is IPRxxxxxx (six digits).  The maximum number sequences retrieved is <?php echo number_format(functions::get_max_seq(),0); ?>. To identify the Pfam and/or InterPro number from a BLAST sequence, please go to <a href='<?php echo functions::get_interpro_website(); ?>' target='_blank'><?php echo functions::get_interpro_website(); ?></a>.
</p>
<fieldset id='option_b'>
<input type='text' id='families_input' name='families_input' class='blast_inputs' value='<?php if (isset($_POST['families_input'])) { echo $_POST['families_input']; } ?>'>
<p class='align_left'><a href='javascript:toggle_pfam_advanced();'>Advanced Options (see tutorial)<span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
<br><div id="pfam_advanced" style="display: none;">
<p class='align_left'>E-Value: <input type='text' class='small' id='pfam_evalue' name='pfam_evalue' value='<?php if (isset($_POST['pfam_evalue'])) { echo $_POST['pfam_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default <?php echo functions::get_evalue(); ?>)</p>
<p class='align_left'>Fraction: <input type='text' class='small' id='pfam_fraction' name='pfam_fraction' value='<?php if (isset($_POST['pfam_fraction'])) { echo $_POST['pfam_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; (default: <?php echo functions::get_fraction(); ?>)</p>
<p class='align_left'>Enable Domain: <input type='checkbox' id='pfam_domain' name='pfam_domain' value='1' <?php if (isset($_POST['pfam_domain']) && ($_POST['pfam_domain'] == "1")) { echo "checked='checked'"; } ?>'> Check to generate SSN with Pfam-defined domains (default: off)</p>
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
<p class='align_left'><input type='radio' id='option_selected_c' name='option_selected' value='C' onChange='disable_forms();'><b>Option C:</b> Generate data set with custom FASTA file with header information. Maximum size is <?php echo ini_get('post_max_size'); ?>.
<fieldset id='option_c'>
<p>FASTA File: <input type='file' name='fasta_file' id='fasta_file' data-url='server/php/'><progress id='progress_bar_fasta' max='100' value='0'></progress>
<br><div id="progressNumberFasta"></div> 
<p>If desired, include a Pfam and/or InterPro families, in the analysis of your FASTA file. For Pfam families, the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is IPRxxxxxx (six digits).</p>
<input type='text' id='families_input2' name='families_input2' class='blast_inputs' value='<?php if (isset($_POST['families_input2'])) { echo $_POST['families_input2']; } ?>'>
<p class='align_left'><a href='javascript:toggle_fasta_advanced();'>Advanced Options (see tutorial)<span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
<br><div id="fasta_advanced" style="display: none;">
<p class='align_left'>E-Value: <input type='text' class='small' id='fasta_evalue' name='fasta_evalue' value='<?php if (isset($_POST['fasta_evalue'])) { echo $_POST['fasta_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default: <?php echo functions::get_evalue(); ?>)</p>
<p class='align_left'>Fraction: <input type='text' class='small' id='fasta_fraction' name='fasta_fraction' value='<?php if (isset($_POST['fasta_fraction'])) { echo $_POST['fasta_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; default: <?php echo functions::get_fraction(); ?>)</p>
<?php    if (functions::get_program_selection_enabled()) { ?>
<p class='align_left'>Select Program to use:
<select name='option_c_program' id='option_c_program'>
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
<?php if (functions::option_d_enabled()) { ?>
<p class='align_left'><input type='radio' id='option_selected_d' name='option_selected' value='D' onChange='disable_forms();'><b>Option D:</b> Generate data set from a file with a list of Uniprot, NCBI, or Genbank sequence accession IDs. Maximum size is <?php echo ini_get('post_max_size'); ?>.
<fieldset id='option_d'>
<p>Accession ID File: <input type='file' name='accession_file' id='accession_file' data-url='server/php/'><progress id='progress_bar_accession' max='100' value='0'></progress>
<br><div id="progressNumberAccession"></div> 
<p class='align_left'><a href='javascript:toggle_accession_advanced();'>Advanced Options (see tutorial)<span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
<br><div id="accession_advanced" style="display: none;">
<p class='align_left'>E-Value: <input type='text' class='small' id='accession_evalue' name='accession_evalue' value='<?php if (isset($_POST['accession_evalue'])) { echo $_POST['accession_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default: <?php echo functions::get_evalue(); ?>)</p>
<p class='align_left'>Fraction: <input type='text' class='small' id='accession_fraction' name='accession_fraction' value='<?php if (isset($_POST['accession_fraction'])) { echo $_POST['accession_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; default: <?php echo functions::get_fraction(); ?>)</p>
<?php    if (functions::get_program_selection_enabled()) { ?>
<p class='align_left'>Select Program to use:
<select name='option_d_program' id='option_d_program'>
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
<?php if (functions::option_e_enabled()) { ?>
<p class='align_left'><input type='radio' id='option_selected_e' name='option_selected' value='E' onChange='disable_forms();'><b>Option E:</b> THIS DOESN'T WORK YET. Maximum size is <?php echo ini_get('post_max_size'); ?>.
<fieldset id='option_e'>
<p>FASTA File: <input type='file' name='fasta_id_file' id='fasta_id_file' data-url='server/php/'><progress id='progress_bar_fasta_id' max='100' value='0'></progress>
<br><div id="progressNumberFastaId"></div> 
<p>If desired, include a Pfam and/or InterPro families, in the analysis of your FASTA file. For Pfam families, the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is IPRxxxxxx (six digits).</p>
<input type='text' id='families_input3' name='families_input3' class='blast_inputs' value='<?php if (isset($_POST['families_input3'])) { echo $_POST['families_input3']; } ?>'>
<p class='align_left'><a href='javascript:toggle_fasta_id_advanced();'>Advanced Options (see tutorial)<span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a></p>
<br><div id="fasta_id_advanced" style="display: none;">
<p class='align_left'>E-Value: <input type='text' class='small' id='fasta_id_evalue' name='fasta_id_evalue' value='<?php if (isset($_POST['fasta_id_evalue'])) { echo $_POST['fasta_id_evalue']; } else { echo functions::get_evalue(); } ?>'> Negative log of e-value for all-by-all BLAST (&ge;1; default: <?php echo functions::get_evalue(); ?>)</p>
<p class='align_left'>Fraction: <input type='text' class='small' id='fasta_id_fraction' name='fasta_id_fraction' value='<?php if (isset($_POST['fasta_id_fraction'])) { echo $_POST['fasta_id_fraction']; } else { echo functions::get_fraction(); } ?>'>  Fraction of sequences in Pfam/Interpro family for network (&ge; 1; default: <?php echo functions::get_fraction(); ?>)</p>
<?php    if (functions::get_program_selection_enabled()) { ?>
<p class='align_left'>Select Program to use:
<select name='option_e_program' id='option_e_program'>
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
<p><br><input type="text" id='email' name='email' value='<?php if (isset($_POST['email'])) { echo $_POST['email']; } else { echo "Enter your email address"; } ?>' i
	class="blast_inputs email" id='email' onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
<span class="smalltext">Used for data retrieval only</span>
</p>
<div id='message'><?php if (isset($message)) { echo "<h4 class='center'>" . $message . "</h4>"; } ?></div>

<input type="button" id='submit' name="submit" value="GO" class="css_btn_class" onclick="uploadFile()">
        
</form>
<P>View Example - <a href='stepc_example.php'>Click Here</a></p>
<h4>InterPro Version: <b><?php echo functions::get_interpro_version(); ?></b></h4>
<h4>UniProt Version: <b><?php echo functions::get_uniprot_version(); ?></b></h4>
    
</div>
<script> 
disable_forms();
</script>

<?php include_once 'includes/footer.inc.php'; ?>
