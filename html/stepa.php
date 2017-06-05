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
<p class='align_left'>
    <input type='radio' id='option_selected_c' name='option_selected' value='C' onChange='disable_forms();'>
    <b>Option C:</b> Generate data set with custom FASTA file with header information. Maximum size is
    <?php echo ini_get('post_max_size'); ?>.
    <fieldset id='option_c'>
        <p>
            Input a list of protein sequences in the FASTA format with headers, and/or upload a FASTA file.
        </p>
        <textarea class="blast_inputs" id='fasta_input' name='fasta_input'><?php if (isset($_POST['fasta_input'])) { echo $_POST['fasta_input']; } ?></textarea>
        <p>
            FASTA File:
            <input type='file' name='fasta_file' id='fasta_file' data-url='server/php/'>
            <progress id='progress_bar_fasta' max='100' value='0'></progress>
            <br><div id="progressNumberFasta"></div> 
        </p>
        <p>
            If desired, include a Pfam and/or InterPro families, in the analysis of your FASTA file. For Pfam families,
            the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is
            IPRxxxxxx (six digits).
        </p>
        <input type='text' id='families_input2' name='families_input2' class='blast_inputs' value='<?php if (isset($_POST['families_input2'])) { echo $_POST['families_input2']; } ?>'>
<?php    if (functions::option_e_enabled()) { ?>
            <p class='align_left'>
                Read FASTA headers: <input type='checkbox' id='fasta_use_headers' name='fasta_use_headers' value='1' checked='checked'>
                Check to use IDs from FASTA headers to retrieve node attributes (default: on)
            </p>
<?php    } ?>
        <p class='align_left'>
            <a href='javascript:toggle_fasta_advanced();'>Advanced Options (see
                tutorial)<span class="ui-icon ui-icon-triangle-1-e" style='display: inline-block;'></span></a>
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
    <b>Option D:</b> Generate data set from a file with a list of Uniprot, NCBI, or Genbank sequence accession IDs.
    Maximum size is <?php echo ini_get('post_max_size'); ?>.
    <fieldset id='option_d'>
        <p>
            Input a list of Uniprot, NCBI, or Genbank sequence accession IDs, and/or upload a text file containing
            the accession IDs.
        </p>
        <textarea class="blast_inputs" id='accession_input' name='accession_input'><?php if (isset($_POST['accession_input'])) { echo $_POST['accession_input']; } ?></textarea>
        <p>
            Accession ID File: <input type='file' name='accession_file' id='accession_file' data-url='server/php/'>
            <progress id='progress_bar_accession' max='100' value='0'></progress>
            <br><div id="progressNumberAccession"></div>
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

<hr>
<p class='align_left'>
    <input type='radio' id='option_selected_colorssn' name='option_selected' value='colorssn' onChange='disable_forms();'>
    <b>Color SSN:</b> Color an input SSN and return associated cluster data.
    Maximum size is <?php echo ini_get('post_max_size'); ?>.
    <fieldset id='option_colorssn'>
        <p>
            XGMML File:
            <input type='file' name='colorssn_file' id='colorssn_file' data-url='server/php/'>
            <progress id='progress_bar_colorssn' max='100' value='0'></progress>
            <br>
            <div id="progressNumberColorSsn"></div>
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
