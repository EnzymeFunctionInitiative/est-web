<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
include_once 'includes/quest_acron.inc';

if (isset($_POST['submit'])) {
	foreach ($_POST as &$var) {
		$var = trim(rtrim($var));
	}
	$message = "";
	
	//If you entered both blast and pfam/interpro, fail
	if ((strlen($_POST['blast_input'])) && (strlen($_POST['families_input']))) {
		$message = "<br><b>You can only select Option A or Option B</b></br>";

	}

	//If you entered only blast, do option A.
	elseif (strlen($_POST['blast_input'])) {
		$blast = new blast($db);
		$result = $blast->create($_POST['email'],functions::get_evalue(),$_POST['blast_input']);
                
		if ($result['RESULT']) {
                              header('Location: stepb.php');
		}
		else {
			$message = $result['MESSAGE'];
		}
	}

	//If you entered only pfam/interpro, do option B.
	elseif (strlen($_POST['families_input'])) {
		$generate = new generate($db);
		$result = $generate->create($_POST['email'],functions::get_evalue(),$_POST['families_input']);

       	        if ($result['RESULT']) {
                	      header('Location: stepb.php');
        	}
		else {
			$message = $result['MESSAGE']; 
		}
	}

	//If you entered nothing, fail
	else {
		$message = "You need to select Option A or Option B";

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
<form name="blast" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<p>Option A: Generate data set of close relatives via BLAST.  Enter only protein sequence.  Do not enter any fasta header information. (Maximum number sequences retrieved: <?php echo number_format(functions::get_blast_seq(),0); ?>).</p>
<textarea class="blast_inputs" name='blast_input'><?php if (isset($_POST['blast_input'])) { echo $_POST['blast_input']; } ?></textarea>
<p>Option B: Generate data set with Pfam and/or InterPro numbers. For Pfam families, the format is a comma separated list of PFxxxxx (five digits); for InterPro families, the format is IPRxxxxxx (six digits).  The maximum number sequences retrieved is <?php echo number_format(functions::get_max_seq(),0); ?>. To convert your blast search into an InterPro number, please go to <a href='<?php echo functions::get_interpro_website(); ?>' target='_blank'><?php echo functions::get_interpro_website(); ?></a>.

</p>
<input type='text' name='families_input' class='blast_inputs' value='<?php if (isset($_POST['families_input'])) { echo $_POST['families_input']; } ?>'>
<p>
<input type="text" name='email' value='<?php if (isset($_POST['email'])) { echo $_POST['email']; } else { echo "Enter your email address"; } ?>' 
	class="blast_inputs email" id='email' onfocus="if(!this._haschanged){this.value=''};this._haschanged=true;"><br>
<span class="smalltext">Used for data retrieval only</span>
</p>
<?php if (isset($message)) { echo "<b style='color: red;'>" . $message . "</b>"; } ?>       
<hr>

<input type="submit" name="submit" value="GO" class="css_btn_class">
        
</form>
<P>View Example - <a href='stepc_example.php'>Click Here</a></p>
<h4>InterPro Version: <b><?php echo functions::get_interpro_version(); ?></b></h4>
<h4>UniProt Version: <b><?php echo functions::get_uniprot_version(); ?></b></h4>
    
</div>
  


<?php include_once 'includes/footer.inc.php'; ?>
