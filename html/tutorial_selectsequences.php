<?php 

include_once 'includes/header.inc.php'; 
include_once 'includes/quest_tutorial.inc';
?>

	<h3>Select Sequences for Data Set</h3>
        <p><strong>You have two options for building your data set.  </strong></p>
        <ul>
          <li><em>Option A.</em>  Build a relatively small data set with just the most closely related sequences retrieved from the UniProtKB database using a BLAST alignment score of 10<sup>-5</sup>; the UniProtKB database contains all nonredundant protein sequences  A maximum of 2000 sequences is used, but the data set may be smaller if &lt;2000 sequences are found using a BLAST alignment score of 10<sup>-5</sup>.  A  maximum of 2000 was chosen because in most cases a full network will be viewable without having to collapse nodes into representative nodes (see <a href="#anchor1">below</a>). A BLAST alignment score of 10<sup>-5</sup> was chosen because in most searches (for the purposes of functional assignment) sequences will be retrieved that are divergent enough to be informative but won&rsquo;t litter the networks with outliers.  Use this option if you are only interested in those proteins which are most similar to your protein of interest.  <br>
            <br>
            or</li></ul>
        <ul>
          <li><em>Option B.</em> Build a larger data set by including entire groups related to your protein of interest that are located in the Pfam and InterPro databases.  In this option, your input sequence will be used as the query for an <a href="http://www.ebi.ac.uk/Tools/pfa/iprscan/help/">InterProScan</a> search.  We have replicated the ability to view search results as found in web version of InterProScan, which allows you to visualize how your protein of interest &ldquo;maps&rdquo; to domains and families in the InterPro and <a href="http://pfam.sanger.ac.uk/">Pfam</a> databases (Figure 4).  Each group is linked to the source database so you can easily gather more information about the group. From examining this output you can determine which groups are best to select (via check box) for inclusion in the network data set. Because gathering entire families and/or domains results in a larger sequence set, it is likely that you'll need to view representative node SSNs instead of full SSNs (see below). </li>
          </ul>
        <p><img src="images/tutorial_figure_4.jpg" width="659" height="465" alt="Figure 4"><br>Figure 4. Option B of data set selection showing the layout and features of the group selection page. (Use actual screen shot when available.)
</p>
        <p><a name="anchor1"></a>Once you have made your selection(s), EFI-EST begins assembling the dataset and performing the all-by-all BLAST. Since this may take anywhere from a minute to several hours (depending on the size and complexity of the dataset), you may close the running window. When the dataset is complete, you&rsquo;ll receive an e-mail with a link to analyze the dataset. This link will be active for <span class="red">7 days</span> so that you may return at your convenience.</p>
        <p>&nbsp;</p>
<div>
</div>
<p>&nbsp;</p>
<div>
  <div></div>
</div>
    <?php require('includes/gobackto_quest.php'); ?>

    </div>

<?php include_once 'includes/footer.inc.php'; ?>

