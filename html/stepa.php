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
<form name="stepa" method="post" action="" enctype="multipart/form-data">
<input type="hidden" id='MAX_FILE_SIZE' name="MAX_FILE_SIZE" value="2147483648" />

<?php if (functions::option_a_enabled()) { ?>
<p class='align_left'><input type='radio' id='option_selected_a' name='option_selected' value='A' onChange='disable_forms();'><b>Option A:</b> Generate data set of close relatives via BLAST.  Enter only protein sequence.  Do not enter any FASTA header information. (Maximum number sequences retrieved: <?php echo number_format(functions::get_blast_seq(),0); ?>).</p>
<fieldset id='option_a'>
<textarea class="blast_inputs" id='blast_input' name='blast_input'><?php if (isset($_POST['blast_input'])) { echo $_POST['blast_input']; } ?></textarea>
</fieldset>
<?php } ?>
<hr>
<?php if (functions::option_b_enabled()) { ?>
<p class='align_left'><input type='radio' id='option_selected_b' name='option_selected' value='B' onChange='disable_forms();'><b>Option B:</b> Generate data set with Pfam and/or InterPro numbers. For Pfam families, the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is IPRxxxxxx (six digits).  The maximum number sequences retrieved is <?php echo number_format(functions::get_max_seq(),0); ?>. To identify the Pfam and/or InterPro number from a BLAST sequence, please go to <a href='<?php echo functions::get_interpro_website(); ?>' target='_blank'><?php echo functions::get_interpro_website(); ?></a>.
</p>
<fieldset id='option_b'>
<input type='text' id='families_input' name='families_input' class='blast_inputs' value='<?php if (isset($_POST['families_input'])) { echo $_POST['families_input']; } ?>'>
</fieldset>
<?php } ?>

<hr>
<?php if (functions::option_c_enabled()) { ?>
<p class='align_left'><input type='radio' id='option_selected_c' name='option_selected' value='C' onChange='disable_forms();'><b>Option C:</b> Generate data set with custom FASTA file with header information. Maximum size is <?php echo ini_get('post_max_size'); ?>.
<fieldset id='option_c'>
<p>FASTA File: <input type='file' name='fasta_file' id='fasta_file' data-url='server/php/'><progress id='progress_bar' max='100' value='0'></progress>
<br><div id="progressNumber"></div> 
<p>If desired, include a Pfam and/or InterPro families, in the analysis of your FASTA file. For Pfam families, the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is IPRxxxxxx (six digits).</p>
<input type='text' id='families_input2' name='families_input2' class='blast_inputs' value='<?php if (isset($_POST['families_input'])) { echo $_POST['families_input']; } ?>'>
</fieldset>
<?php } ?>
<hr>
<p>
<input type="text" id='email' name='email' value='<?php if (isset($_POST['email'])) { echo $_POST['email']; } else { echo "Enter your email address"; } ?>' 
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
