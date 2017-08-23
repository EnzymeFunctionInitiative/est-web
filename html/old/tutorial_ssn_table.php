<?php 
include_once 'includes/main.inc.php';
include_once 'includes/header.inc.php'; 
include_once 'includes/quest_tutorial.inc';
?>

<h3>Tables</h3>


<table class="tutorial">
<thead>
<th>Node Attribute</th>
<th>Description - Options A, B, C with FASTA header reading, D</th>
</thead>
<tbody>
<tr>
    <td>Name</td>
    <td>Full network - UniProt accession; Rep Node network - UniProt accession for the longest sequence in the representative node (seed sequence for CD-Hit)</td>
</tr>
<tr>
    <td>Shared name</td>
    <td>Full network - UniProt accession; Rep Node network - UniProt accession for the longest sequence in the representative node (seed sequence for CD-Hit)</td>
</tr>
<tr>
    <td>Number of IDs in Rep Node<sup>1</sup></td>
    <td>Number of UniProt IDs in the representative node</td>
</tr>
<tr>
    <td>List of IDs in Rep Node<sup>1</sup></td>
    <td>List of UniProt IDs in the representative node</td>
</tr>
<tr>
    <td>Sequence Source</td>
    <td>Options B, C, and D, “USER” if from user-supplied file, “FAMILY” if from user-specified Pfam/InterPro family, “USER+FAMILY” if from both</td>
</tr>
<tr>
    <td>Query IDs</td>
    <td>Options C and D, Input Query ID(s) that identified a UniProt match in the idmapping file</td>
</tr>
<tr>
    <td>Other IDs</td>
    <td>Option C, headers for FASTA sequences that could not identify a UniProt match in the idmapping file</td>
</tr>
<tr>
    <td>Cluster Number</td>
    <td>Number assigned to cluster, in order of decreasing number of sequences in the clusters (“999999” for singletons)</td>
</tr>
<tr>
    <td>Cluster Sequence Count</td>
    <td>Number of sequences in the cluster</td>
</tr>
<tr>
    <td>Node.fillColor</td>
    <td>Unique color assigned to cluster, in hexadecimal</td>
</tr>
<tr>
    <td>Organism</td>
    <td>organism genus/genera and species, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Taxonomy ID</td>
    <td>NCBI taxonomy identifier(s), from UniProt </td>
</tr>
<tr>
    <td>UniProt Annotation Stastus</td>
    <td>SwissProt - manually annotated; TrEMBL - automatically annotated; from UniProt</td>
</tr>
<tr>
    <td>Description</td>
    <td>protein name(s)/annotation(s), from UniProtKB</td>
</tr>
<tr>
    <td>SwissProt Description</td>
    <td>protein name(s)/annotation(s), from UniProtKB for SwissProt reviewed entries</td>
</tr>
<tr>
    <td>Sequence Length</td>
    <td>number(s) of amino acid residues, from UniProt</td>
</tr>
<tr>
    <td>Gene name</td>
    <td>gene name(s)</td>
</tr>
<tr>
    <td>NCBI IDs</td>
    <td>RefSeq/GenBank IDs and GI numbers, from UniProt idmapping</td>
</tr>
<tr>
    <td>Superkingdom</td>
    <td>domain of life of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Kingdom</td>
    <td>kingdom of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Phylum</td>
    <td>Phylogenetic phylum of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Class</td>
    <td>Phylogenetic class of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Order</td>
    <td>Phylogenetic order of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Family</td>
    <td>Phylogenetic family of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Genus</td>
    <td>Phylogenetic genus of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>Species</td>
    <td>Phylogenetic species of the organism, from UniProt taxonomy.xml</td>
</tr>
<tr>
    <td>EC</td>
    <td>EC number, from UniProt</td>
</tr>
<tr>
    <td>PFAM</td>
    <td>Pfam family, from UniProt</td>
</tr>
<tr>
    <td>IPRO</td>
    <td>InterPro family, from UniProt</td>
</tr>
<tr>
    <td>PDB</td>
    <td>Protein Data Bank entry, from UniProt</td>
</tr>
<tr>
    <td>BRENDA ID</td>
    <td>BRENDA Database ID, from UniProt</td>
</tr>
<tr>
    <td>CAZY Name</td>
    <td>Carbohydrate-Active enZYmes (CAZy) family name(s), from UniProt</td>
</tr>
<tr>
    <td>GO Term</td>
    <td>Gene Ontology classification(s), from UniProt</td>
</tr>
<tr>
    <td>KEGG ID</td>
    <td>KEGG Database ID, from UniProt</td>
</tr>
<tr>
    <td>PATRIC ID</td>
    <td>PATRIC Database ID, from UniProt</td>
</tr>
<tr>
    <td>STRING ID</td>
    <td>STRING Database ID, from UniProt</td>
</tr>
<tr>
    <td>HMP Body Site</td>
    <td>location(s) of organism(s) in/on the body, if human microbiome organism, spreadsheet from HMP </td>
</tr>
<tr>
    <td>HMP Oxygen</td>
    <td>oxygen requirement(s), if human microbiome organism, spreadsheet from HMP</td>
</tr>
<tr>
    <td>P01 gDNA</td>
    <td>availability of gDNA(s) at EFI Protein Core, custom</td>
</tr>
</tbody>
</table>
<div>1 - Rep Node Networks</div>

<h4>Option C without FASTA header reading</h4>
<table>
<thead>
<th>Node Attribute</th>
<th>Description - Options A, B, C with FASTA header reading, D</th>
</thead>
<tbody>
<tr>
    <td>Name</td>
    <td>zzznnn, where nnn = number of the sequence in FASTA file</td>
</tr>
<tr>
    <td>Shared Name</td>
    <td>zzznnn, where nnn = number of the sequence in FASTA file</td>
</tr>
<tr>
    <td>Description</td>
    <td>FASTA Header </td>
</tr>
<tr>
    <td>Sequence Length</td>
    <td>Length of sequence in FASTA entry</td>
</tr>
</tbody>
</table>

<?php require('includes/gobackto_quest.php'); ?>
</div>

<?php include_once 'includes/footer.inc.php'; ?>

