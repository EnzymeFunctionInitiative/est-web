<?php 

include_once 'includes/header.inc.php'; 

include_once 'includes/quest_tutorial.inc';
?>

  
	<h3>What is a Sequence Similarity Network?</h3>
        <p><a href="http://babbittlab.ucsf.edu/"><strong>Prof. Patricia Babbitt&rsquo;s</strong></a> group at UCSF first developed sequence similarity networks (SSNs) as a way to deal with the ever-increasing deluge of sequences deposited in public databases. A general tutorial is provided here, but please see their seminal papers for a thorough description of the SSN technique <a href="tutorial_references.php">(<strong>1</strong>)</a> and their program Pythoscape <a href="tutorial_references.php">(<strong>2</strong>)</a>, which were the inspiration for the EFI&rsquo;s EFI-EST web server.</p>
        <p>Sequence similarity networks (SSNs) are a quick and easy way to visualize sequence relationships within groups of proteins, especially large numbers of proteins. In the simplest form, each protein sequence is represented as a square circle (referred to as a &ldquo;node&rdquo;). A line (referred to as an &ldquo;edge&rdquo;) connecting one sequence to another is an indication of relatedness <a id='ex1'></a>(Figure 1).
<p><img src="images/tutorial_figure_1.jpg" width="659" height="362" alt="Figure 1"><br>
          Figure 1. Example of a very simple sequence similarity network as a function of e-value.</p>

<p>For our purposes, the relatedness is described by sequence similarity. Users choose a threshold at which they’d like to examine the similarity within a set of protein sequences. The sequence set is subjected to an all-by-all <a href='http://www.ncbi.nlm.nih.gov/books/NBK21097/'>BLAST</a>, and the resulting pairwise scores are used to determine which protein sequences should or should not be connected in a network at the selected threshold metric.

<p>NOTE: EFI-EST calculates an alignment score based on bit score, that is similar to, but not the same as <a href='http://www.ncbi.nlm.nih.gov/blast/Blast.cgi?CMD=Web&PAGE_TYPE=BlastDocs&DOC_TYPE=FAQ#expect'>e-value</a>.
        
<p>          For example, if the alignment score threshold is specified as 28, then edges are only drawn between nodes (protein sequences) that share that level of similarity (or greater). If two proteins are not connected, that means their sequences are less similar than described by the 28 threshold value. If the network is recalculated at a more permissive (smaller) alignment score, relationships may become apparent that were not evident at more stringent (larger) alignment score. Groups of highly similar proteins display a high degree of interconnectivity even as the aligment score is increased. These &ldquo;clusters&rdquo; are often very useful for the interrogation of enzyme function. </p>
<p>Although not as rigorous as traditional phylogenetic trees, SSNs typically display the same topology (Figure 2). However, SSNs offer an advantage over trees in that large sequence sets (e.g. many thousands of proteins) can be analyzed much more quickly and visualized easily using the network visualization program <a href="http://www.cytoscape.org/">Cytoscape</a> (<strong><a href='tutorial_references.php'>3</a></strong>).</p>
<p><img src="images/tutorial_figure_2.jpg" width="580" height="373" alt="Figure 2">

<br>Figure 2.  Rooted phylogenetic tree (UPGMA) created with <a href="http://www.genome.jp/tools/clustalw/">ClustalW</a> (A) using the same sequence set as shown in the network in Figure 1 (B).  Proteins in the tree are identified by their six character UniProt accession numbers.</p>
        <p>Besides speed, another major advantage of SSNs is the ability to include pertinent information for each individual protein (such as species, annotation, length, PDB deposition, etc.).  This information is included as &ldquo;node attributes&rdquo; which are searchable and sortable within a sequence similarity network displayed in Cytoscape (Figure 3). For a complete list of node attributes available via this tool, click <a href='tutorial_node_attributes.php'>here</a>. </p>
        <p><a id='ex3'></a><img src="images/tutorial_figure_3.jpg" alt="Figure 3" width="518" height="570"></p>
<br>Figure 3. Representative node attributes for the example data set as seen in a Cytoscape session.
        <div>
          
        </div>
<div>
  <div> </div>
      </div>
	<form action='tutorial_why_use_networks.php' method='post'>
        <button type='submit' class='css_btn_class'>CONTINUE WITH THE TUTORIAL</button>
</form>

    <?php require('includes/gobackto_quest.php'); ?>
    </div>

<?php include_once 'includes/footer.inc.php'; ?>

