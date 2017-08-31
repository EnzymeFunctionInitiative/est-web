<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
?>

<div class="content_holder">
<div class="bottom">
<div class="content_wide">
<div class="content_2ndlevel">

<h2>EFI - Enzyme Similarity Tool</h2>

<p>
A sequence similarity network (SSN) allows researchers to visualize relationships among 
protein sequences. In SSNs, the most related proteins are grouped together in 
clusters.  The Enzyme Similarity Tool (EFI-EST) is a web-tool that allows researchers to
easily generate SSNs that can be visualized in 
<a href="http://www.cytoscape.org/">Cytoscape</a>
(<a href="http://efi.igb.illinois.edu/efi-est-beta/tutorial_references.php">3</a>).
</p>

<div><center><a href="stepa.php"><button type="button" class='css_btn_class_recalc'>Begin EFI-EST</button></a></center></div>

<h3>Overview of possible inputs for EFI-EST</h3>

<p>
The EFI - ENZYME SIMILARITY TOOL (EFI-EST) is a webserver for the generation of 
SSNs. Four options for user-initiated generation of a SSN are available. In 
addition, a utility to enhance SSNs interpretation is available.
</p>

<ul>
<li><b>Option A: Single sequence query</b>.  The provided sequence is used as 
the query for a BLAST search of the UniProt database. The retrieved sequences 
are used to generate the SSN.
<p class="indentall">Option A allows the user to explore local sequence-function space for the query 
sequence. Homologs are collected and used to generate the SSN. By default, 
<?php echo functions::get_default_blast_seq(1); ?> sequences are collected
as this number often allows a “full” SSN to be generated and viewed with Cytoscape.</p>
</li>

<li><b>Option B: Pfam and/or InterPro families</b>. Defined protein families are used to generate the SSN.
<p class="indentall">
Option B allows the user to explore sequence-function space from defined 
protein families. A limit of <?php echo functions::get_max_seq(1); ?> 
sequences is imposed. Generation of a SSN for more than one family is allowed.
</p>
</li>

<li><b>Option C: User-supplied FASTA file.</b>
A SSN is generated from a set of defined sequences.

<p class="indentall">
Option C allows the user to generate a SSN for a provided set of FASTA 
formatted sequences. By default, the provided sequences cannot be associated 
with sequences in the UniProt database, and only two node attributes are 
provided for the SSNs generated: the number of residues as the “Sequence 
Length”, and the FASTA header as the “Description”. 
</p>

<p class="indentall">An option allows the FASTA headers to be read and if Uniprot or NCBI 
identifiers are recognized, the corresponding Uniprot information will be 
presented as node attributes.
</p>
</li>

<li><b>Option D: List of UniProt and/or NCBI IDs.</b>
The SSN is generated after 
fetching the information from the corresponding databases.

<p class="indentall">
Option D allows the user to provide a list of UniProt IDs, NCBI IDs, and/or 
NCBI GI numbers (now “retired”). UniProt IDs are used to retrieve sequences and 
annotation information from the UniProt database. When recognized, NCBI IDs and 
GI numbers are used to retrieve the “equivalent” UniProt IDs and information. 
Sequences with NCBI IDs that cannot be recognized will not be included in the 
SSN and a “nomatch” file listing these IDs is available for download.
</p>
</li>

<li><b>Utility for the identification and coloring of independent clusters within a 
SSN.</b>

<p class="indentall">
Independent clusters in the uploaded SSN are identified, numbered and colored. 
Summary tables, sets of IDs and sequences for specific clusters and are 
provided. A manually edited SNN can serve as input for this utility.
</p>
</li>
</ul>

<p><a href='http://dx.doi.org/10.1016/j.bbapap.2015.04.015'>Please see our recent
review in BBA Proteins for examples of EFI-EST use.</a></p>


<!--

<h3>OPTIONS FOR GENERATING SEQUENCE SIMLARITY NETWORKS</h3>

<p style='line-height:120%'>Sequence similarity networks (SSNs) are a
powerful tool for analyzing relationships among sequences in protein
(super)families and that these will be useful for enhancing functional
discovery/annotation using strategies developed by the Enzyme Function
Initiative (EFI) as well as developing hypotheses about structure‑function
relationships in families and superfamilies.  As a result, this web tool
provides "open access" to the ability to generate SSNs.  Five different options
for user-initiated generation of SSNs, four with this web tool and one with
Unix terminal scripts:</p>

<p>
<strong>1.</strong>  Option A in which the user provides a sequence that is
used as the query for a BLASTP search of the UniProtKB database.  A maximum of
<?php echo functions::get_max_blast_seq(1); ?> "best" hits (rank ordered
according to decreasing e value; this default limit can be modified by the
user) are collected and used to generate the SSN.  This allows the user to
explore the "local" sequence-function space occupied by the query.  The limit
of <?php echo functions::get_max_blast_seq(1); ?> sequences is imposed to allow
a "full" network to be generated in which each node represents a single
sequence (although the user may find that a smaller number is necessary; this
limit also can be modified by the user).  In a "full" SSN, the node attributes
that are associated with each sequence (node) provide a "basis set" of
annotation information.  More detailed annotation information for each sequence
can be obtained by right‑clicking on each node and following the links to the
PIR database where the complete UniProtKB annotation information for that
sequence is available.
</p>

<p>
<strong>2.</strong>  Option B in which the user provides the ID numbers for one
or more Pfam and/or InterPro families.  Because of the computational time that
is required to generate the all-by-all BLAST dataset and, also, output the full
and representative node SSNs, a limit of <?php echo number_format(__MAX_SEQ__);
?> sequences is imposed (the BLAST and network output steps each may take
several hours).  As described in this tutorial, it is unlikely that "full" SSNs
will be small enough to be opened and viewed in Cytoscape.  Instead,
representative ("rep") node networks are provided for viewing in Cytoscape in
which each "metanode" represents several sequences that share specified levels
of sequence identity.  The node attributes for rep node networks provide lists
of the annotation information for all of the sequences within each metanode,
i.e., annotation information for specific sequences cannot be obtained.
Despite this limitation, the rep node networks should allow a useful overview
of sequence-function space in the user‑specified families/superfamilies.
</p>

<p>
<strong>3.</strong> Option C in which the user can generate a SSN with a
user-supplied FASTA file.  The sequences in the FASTA file need not be in the
public databases; for example, the user can generate SSNs for sequences from
in-house genome projects.  Option C provides two suboptions:  1) generation of
the SSN for the user-supplied FASTA file; and 2) generation of the SSN for the
user-supplied FASTA file in combination with user-specified Pfam- and/or
InterPro-defined families.  The first suboption provides the user with sequence
relationships in the FASTA file; the second suboption allows the user to place
the sequences in the FASTA file in the context of a complete protein family.
Because the sequences in the user-supplied FASTA file need not be in the
UniProt database, the node attributes for these sequences will not be those
provided for those in Option B described above.  Instead, the FASTA header
information for each sequence will be provided as the "Description" so the user
should make sure the headers contain sufficient information to identify the
sequences.  In addition, the user can select an option that will cause EST
to parse the FASTA headers for UniProt and NCBI IDs and retrieve matching
UniProt annotations and store those as node attributes in the output SSN.
Selecting this option also outputs taxonomy information in the output SSN
for any identified sequences.
</p>

<p>
<strong>4.</strong> Option D in which the user inputs a list of UniProt or
NCBI IDs.  The IDs are matched to the UniProt database and if IDs are
recognized, the sequences are retrieved for generation of the SSN.  In
addition, annotations are retrieved and stored as node attributes in the
output SSN.
</p>

<p>
<strong>5.</strong>  For those users interested in detailed analyses of
sequence-function space in large (super)families that require rep node networks
for viewing with Cytoscape, we will provide accounts on the computer cluster
(Biocluster) at the Institute for Genomic Biology at the University of
Illinois, Urbana‑Champaign, so that they can utilize straightforward Unix
scripts for generating and outputting SSNs; we will pay the costs for using
Biocluster.  The Unix scripts allow "subgroups" of sequences in rep node
networks to be expanded to "full" networks so that the node attributes and
UniProtKB annotations for each sequence can be accessed.  Basic knowledge of
Unix is recommended for using these scripts.  If you are interested in
obtaining an account on Biocluster and generating networks using the scripts,
please send an e-mail to efi@enzymefunction.org and provide a brief description
of your interests and goals so that we can provide guidance in your use of the
scripts.  We will reply with information on how to register for an account.
</p>
-->

<hr>

<div><center><a href="tutorial.php"><button type='button' class='css_btn_class'>Proceed to the tutorial</button></a></center></div>

<div><center><a href="stepa.php"><button type='button' class='css_btn_class_recalc'>Begin EFI-EST</button></a></center></div>

<?php include_once 'includes/footer.inc.php'; ?>

