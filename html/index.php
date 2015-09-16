<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 

?>
<div class="content_holder">


        <div class="bottom">

    <div class="content_wide">

    <div class="content_2ndlevel">
<!--<h2>Sequence Similarity Networks Tool</h2>-->
<h2>EFI - Enzyme Similarity Tool</h2>
<div class="content_nav">

<ul>
	  <li><a href='index.php'>OPTIONS FOR GENERATING SEQUENCE SIMLARITY NETWORKS</a></li>
          <li><a href="tutorial.php">What is a Sequence Similarity Network?</a></li>
          <li><a href="tutorial_why_use_networks.php">Why use Sequence Similarity Networks?</a></li>
          <li><a href="tutorial_startscreen.php">EFI-EST Start Screen</a></li>
          <li><a href="tutorial_analysis.php">Data Set Analysis</a>
          <ul>
                <li><a href="tutorial_analysis.php#ex1">Example 1: a &quot;simple&quot; case</a></li>
            <li><a href="tutorial_analysis.php#ex3">Example 2: multidomain proteins</a></li>
          </ul>
          </li>
          <li><a href="tutorial_download.php">Network File Download</a></li>
          <li><a href="tutorial_node_attributes.php">Node Attributes</a></li>
          <li><a href='tutorial_cytoscape.php'>Introduction to Cytoscape</a>
                <ul>
                        <li><a href='tutorial_cytoscape.php#ex1'>Download Cytoscape</a></li>
                        <li><a href='tutorial_cytoscape.php#ex2'>Initial Steps</li>
                        <li><a href='tutorial_cytoscape.php#ex3'>Selecting Nodes</li>
                        <li><a href='tutorial_cytoscape.php#ex4'>Node Attributes Data Panel</li>
                        <li><a href='tutorial_cytoscape.php#ex5'>Searching</li>
                        <li><a href='tutorial_cytoscape.php#ex6'>Changing Visual Styles</li>
                        <li><a href='tutorial_cytoscape.php#ex7'>Filtering Network</li>
                        <li><a href='tutorial_cytoscape.php#ex8'>Saving Sessions</li>
                        <li><a href='tutorial_cytoscape.php#ex9'>Opening a Session File</li>
                </ul>
                </li>
          <li><a href="tutorial_references.php">References</a></li>
</ul>
</div>
  <div class="content_content">
<h3>OPTIONS FOR GENERATING SEQUENCE SIMLARITY NETWORKS</h3>
<p style='line-height:120%'><br>Sequence similarity networks (SSNs) are a powerful tool for analyzing relationships among sequences in protein (super)families and that these will be useful for enhancing functional discovery/annotation using strategies developed by the Enzyme Function Initiative (EFI) as well as developing hypotheses about structure‑function relationships in families and superfamilies.  As a result, this web tool provides “open access” to the ability to generate SSNs.  
Four different options for user-initiated generation of SSNs, three with this web tool and one with Unix terminal scripts:</p>

<p><strong>1.</strong>  Option A in which the user provides a sequence that is used as the query for a BLASTP search of the UniProtKB database.  A maximum of <?php echo functions::get_max_blast_seq(1); ?> “best” hits 
(rank ordered according to decreasing e‑value; this default limit can be modified by the user) are collected and used to generate the SSN.  This allows the user to explore the “local” sequence-function space occupied by the query.  
The limit of <?php echo functions::get_max_blast_seq(1); ?> sequences is imposed to allow a “full” network to be generated in which each node represents a single sequence (although the user may find that a smaller number is necessary; this limit also can be modified by the user).  In a “full” SSN, the node attributes that are associated with each sequence (node) provide a “basis set” of annotation information.  More detailed annotation information for each sequence can be obtained by right‑clicking on each node and following the links to the PIR database where the complete UniProtKB annotation information for that sequence is available.</p>

<p style='line-height:120%'><br><strong>2.</strong>  Option B in which the user provides the ID numbers for one or more Pfam and/or InterPro families.  Because of the computational time that is required to generate the all-by-all BLAST dataset and, also, output the full and representative node SSNs, a limit of <?php echo number_format(__MAX_SEQ__); ?> sequences is imposed (the BLAST and network output steps each may take several hours).  As described in this tutorial, it is unlikely that “full” SSNs will be small enough to be opened and viewed in Cytoscape.  Instead, representative (“rep”) node networks are provided for viewing in Cytoscape in which each “metanode” represents several sequences that share specified levels of sequence identity.  The node attributes for rep node networks provide lists of the annotation information for all of the sequences within each metanode, i.e., annotation information for specific sequences cannot be obtained.  Despite this limitation, the rep node networks should allow a useful overview of sequence-function space in the user‑specified families/superfamilies.</p>

<p style='line-height:120%'><br><strong>3.</strong> Option C in which the user can generate a SSN with a user-supplied FASTA file.  The sequences in the FASTA file need not be in the public databases; for example, the user can generate SSNs for sequences from in-house genome projects.  Option C provides two suboptions:  1) generation of the SSN for the user-supplied FASTA file; and 2) generation of the SSN for the user-supplied FASTA file in combination with user-specified Pfam- and/or InterPro-defined families.  The first suboption provides the user with sequence relationships in the FASTA file; the second suboption allows the user to place the sequences in the FASTA file in the context of a complete protein family.  Because the sequences in the user-supplied FASTA file need not be in the UniProt database, the node attributes for these sequences will not be those provided for those in Option B described above.  Instead, the FASTA header information for each sequence will be provided as the “Description” so the user should make sure the headers contain sufficient information to identify the sequences.</p>

<p style='line-height:120%'><br><strong>4.</strong>  For those users interested in detailed analyses of sequence-function space in large (super)families that require rep node networks for viewing with Cytoscape, we will provide accounts on the computer cluster (Biocluster) at the Institute for Genomic Biology at the University of Illinois, Urbana‑Champaign, so that they can utilize straightforward Unix scripts for generating and outputting SSNs; we will pay the costs for using Biocluster.  The Unix scripts allow “subgroups” of sequences in rep node networks to be expanded to “full” networks so that the node attributes and UniProtKB annotations for each sequence can be accessed.  Basic knowledge of Unix is recommended for using these scripts.  If you are interested in obtaining an account on Biocluster and generating networks using the scripts, please send an e-mail to efi@enzymefunction.org and provide a brief description of your interests and goals so that we can provide guidance in your use of the scripts.  We will reply with information on how to register for an account.</p>

<p style='line-height:120%'><br>Because each of these four options necessarily involves a time delay between wanting and having a network, the we are is working toward precomputing a library of SSNs for all Pfam families so that users can “immediately” download the network file(s) for the families that contain their sequence(s) of interest.  Our plan is to provide access to this library via a webtool that will be available on the EFI’s website.</p>

<p><a href='http://dx.doi.org/10.1016/j.bbapap.2015.04.015'>Please see our recent review in BBA Proteins for examples of EFI-EST use.</a></p>

<hr>
<form action='tutorial.php' method='post'>
	<button type='submit' class='css_btn_class'>Please proceed to the tutorial</button>
</form>
<form action="stepa.php" method="post">
	<button type='submit' class='css_btn_class'>Begin EFI-EST</button>
</form>
        
    
    
</div>
  


<?php include_once 'includes/footer.inc.php'; ?>
