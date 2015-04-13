<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
include_once 'includes/quest_acron.inc';

?>	

<img src="images/quest_stages_c.jpg" width="990" height="119" alt="stage 1">
<hr>

	<h3>Data set Completed</h3>
	<p>&nbsp;</p>
	        <h4>Network Information</h4>
            <table width="100%" border="1">
        <tr>
                <td>PFam/Interpro Families</td><td>PF11817</td>        </tr>
        <tr>
                <td>Total Number of Sequences</td>
                <td>767        </tr>
    </table>
<p>&nbsp;</p>
<hr>
<h4>1: Analyze your data set<a href="tutorial_analysis.php" class="question" target="_blank">?</a></h4>
        <p><strong>Important! </strong>View plots and histogram to determine the appropriate lengths and alignment score before continuing.</p>
    <table>
                <tr>
        <td><p>Number of Edges Histogram</p></td>
        <td><a href='example/output/number_of_edges.png' class="view_download" target='_blank'>View</a></td>
        <td><a href='example/output/number_of_edges.png' download target='_blank' class='view_download'>Download</button></td>

        </tr>
    <tr>
        <td><p>Length Histogram</p></td>
        <td><a href="example/output/length_histogram.png" class="view_download" target='_blank'>View</a></td>
        <td><a href='example/output/length_histogram.png' class='view_download' target='_blank' download>Download</a></td>


    </tr>
        <tr>
        <td><p>Alignment Length Quartile Plot</p></td>
        <td><a href="example/output/alignment_length.png" class="view_download" target='_blank'>View</a></td>
        <td><a href='example/output/alignment_length.png' class='view_download' target='_blank' download>Download</a></td>

    </tr>
        <tr>
        <td><p>Percent Identity Quartile Plot</p></td>
        <td><a href="example/output/percent_identity.png" class="view_download" target='_blank'>View</a></td>
        <td><a href='example/output/percent_identity.png' class='view_download' target='_blank' download>Download</a></td>

    </tr>
    </table>


    <hr><p><br></p>
    <h4>2: Choose alignment score for output<a href="tutorial_analysis.php" class="question" target="_blank">?</a>
        <span style='color:red'>Required</span></h4>
    <p>Select a lower limit for the aligment score for the output files. You will input an integer which represents the exponent of 10<sup>-X</sup> where X is the integer.</p>
  <form name="define_length" method="post" action="/efi-est/stepc.php?id=2465&key=db46f417fbb6ed8d02b1d4e3631f200ac7f2a690" class="align_left">

       <p><input type="text" name="evalue" value='200' readonly='readonly'> alignment score</p>
<hr><p><br></p>
    <h4>3: Define length range<a href="tutorial_analysis.php" class="question" target="_blank">?</a>
        <span style='color:red'>Optional</span></h4>
    <p>If protein length needs to be restricted.</p>

             <p><input type="text" name="minimum" maxlength='20' value='0' readonly='readonly'> Min (Defaults: <?php echo __MINIMUM__; ?>)<br>
       <input type="text" name="maximum" maxlength='20' value='50000' readonly='readonly'> Max (Defaults: <?php echo __MAXIMUM__; ?>) </p>



      <hr>
    <h4>4: Provide Network Name <span style='color:red'>Required</span></h4>

      <p><input type="text" name="network_name" value='FoieGras_e200' readonly='readonly'> Name

        <p><input type='button' class='css_btn_class_recalc' onClick="location.href='stepe_example.php'" value='Analyze Data'>

        <p>    </form>
</div>

<div class="clear"></div>
<p class="suggestions" style='font-size:14px'><a href="http://enzymefunction.org/content/sequence-similarity-networks-tool-feedback" target="_blank">Need help or have suggestions or comments?   Please click here to submit »</a></p>
</div>


<div class="clear"></div>


</div>

</div>
<div class="clear"></div>
</div>
<?php include_once 'includes/footer.inc.php'; ?>

