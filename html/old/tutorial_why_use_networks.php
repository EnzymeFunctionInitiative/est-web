<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
include_once 'includes/quest_tutorial.inc';
?>

	<h3>Why Use Sequence Similarity Networks?</h3>
        <p>SSNs are useful for examining the sequence relationships between proteins. For functional assignment, we generally use them in two ways:</p>
        <ol>
          <li><strong>To view a large set of proteins holistically</strong><br>
            For example, examining the number of clusters found in a SSN as the alignment score increases allows an approximation of how many distinct families are found within the sequence set, especially when known functions are mapped onto the network.  From this analysis, it may be apparent that distinct families have different functions or they have the same function but evolved from different ancestors to form discrete groups.  In <a href='tutorial.php#ex1'>Figure 1</a>, it appears that there are 6 possible families, 2 grey families (&ldquo;singletons&rdquo;) and an individual family for each of the remaining colors. This information is useful for understanding the diversity within a group of proteins and for identifying regions of sequence space for which little to no functional information is available, indicating an excellent area for the discovery of new functions.<br>&nbsp;
          </li>
          <li><strong>To view a particular sequence&rsquo;s relation within a larger set of sequences</strong><br>
          For example, if a cluster contains a protein of known function, it is possible that the entire cluster, including any unknown proteins of interest, also shares that function. This association is stronger if it remains intact at less stringent alignment scores. Likewise, if an unknown is found to be associated with a cluster of known function, but fractures at more stringent alignment scores (e.g. red and green nodes in <a href='tutorial.php#ex1'>Figure 1</a>), it is possible that the unknown and known functions have a common partial reaction but differ in specificity. This scenario is common among functionally diverse superfamilies. In the simple network above, if the proteins in the red cluster were known to be isoprenoid synthases that catalyze elongation of shorter chain length products, then a plausible hypothesis could be that the proteins in the green cluster may catalyze elongation of medium or longer chain length products.</li>
        </ol>
        <p>          In either case, SSNs allow the user to quickly and easily view sequence relationships and gather information about proteins of known and unknown function. As sequence databases and needs to concatenate disparate information into a single visual aid grow, SSNs will become increasingly more valuable for developing hypotheses. This was our motivation for creating EFI-EST. </p>
<div>
  <div></div>
</div>
	<form action='tutorial_startscreen.php' method='post'>
        <button type='submit' class='css_btn_class'>CONTINUE WITH THE TUTORIAL</button>
</form>

    <?php require('includes/gobackto_quest.php'); ?>

    </div>

<?php include_once 'includes/footer.inc.php'; ?>

