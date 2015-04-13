<?php 

include_once 'includes/header.inc.php'; 
include_once 'includes/quest_tutorial.inc';
?>

	<h3>Node Attributes</h3>
<p>A major advantage of sequence similarity networks is the ability to include pertinent information for each individual protein (such as species, annotation, length, PDB deposition, etc.).  This information is included as “Node Attributes” which are searchable and sortable within the data panel in a sequence similarity network displayed in Cytoscape (see Figure 3 <a href='tutorial.php#ex3'>here</a>).</p>

<p>Also notice that if you right click (control+click on Mac) on any node in a network open in Cytoscape, you will get a sub-menu to carry out node-specific actions or access external links via LinkOut, such as to UniProtKB.</p>
<p><img src='images/node_attributes_Figure_1.png' width='700'></p>
<p>Note that the EFI-EST web server uses data available in the UniProtKB database to populate node attribute fields.  Therefore, only information that is stored in UniProtKB is included in EFI-EST networks.  To load and map your own node attributes, click <a href='http://enzymefunction.org/system/files/upload/EFI_Quick_Help_Cytoscape-Apply_Node_Attribute.pdf' target='_blank'>here</a> to view a tutorial.  Adding your own node attributes is useful for mapping annotations or other information that you have at hand, which is otherwise not available in UniProtKB. </p>
<p>An introduction on how to use Cytoscape can be found <a href='tutorial_cytoscape.php'>here</a>.</p>
<p><b>Rep Node Network Node Attributes</b></p>

<p><b>ACC (variable, list)</b> – UniProt accession(s) for the protein(s) 
<br><b>CAZY  (variable, list)</b> – CAZy family name(s) for the protein(s) 
<br><b>CLASS  (variable, list)</b> – Phylogenetic class(es) of the organism(s)
<br><b>Cluster Size (variable)</b> – number of proteins in the rep node
<br><b>Description  (variable, list)</b> – protein name(s)/annotation(s) in UniProtKB
<br><b>Domain  (variable, list)</b> – domain of life to which the organism(s) belong(s)
<br><b>EC (variable, list)</b> – the EC number(s) for the protein(s)
<br><b>EFI_ID  (6 digit, starting with 5, list)</b> – target ID(s) for the protein(s) from EFI-DB
<br><b>FAMILY  (variable, list)</b> – Phylogenetic family(ies) of the organism(s)
<br><b>GDNA  (true or false, list)</b> – availability of gDNA(s) from the AECOM Protein Core
<br><b>GENUS  (variable, list)</b> – Phylogenetic genus(i) of the organism(s)
<br><b>GI  (variable, list)</b> – GI numbers mapped to the protein(s)
<br><b>GN (variable, list)</b> – gene name(s) for the protein(s) 
<br><b>GO (variable, list)</b> – Gene Ontology classification(s) for the protein(s)
<br><b>HMP_Body_Site  (body site, list)</b> – if human microbiome species, the location(s) of the species in/on the body 
<br><b>HMP_Oxygen  (oxygen requirement, list)</b> – if human microbiome species, the oxygen requirement(s)
<br><b>IPRO (variable, list)</b> – InterPro family(ies) into which the protein(s) has been classified
<br><b>name (variable, list)</b> – UniProt accession for the longest sequence in the rep node
<br><b>ORDER  (variable, list)</b> – Phylogenetic order of the organism(s)
<br><b>Organism  (variable, list)</b> – organism genus(i) and species 
<br><b>PDB  (4 character, list)</b> – deposition code(s) for structures deposited in the Protein Data Bank
<br><b>PFAM  (variable, list)</b> – Pfam family(ies) into which the protein(s) has(have) been classified
<br><b>PHYLUM  (variable, list)</b> – Phylogenetic phylum(a) of the organism(s)
<br><b>Sequence_Length (variable, list)</b> – number(s) of amino acid residues in the protein(s)
<br><b>Shared name</b> – UniProt accession for the longest sequence in the rep node
<br><b>SPECIES  (variable, list)</b> – Phylogenetic species of the organism(s)
<br><b>STATUS  (unreviewed or reviewed, list)</b> – indicates if the annotation(s) were generated automatically and are found in TrEMBL (unreviewed) or manually annotated and are found in Swiss-Prot (reviewed)
<br><b>Taxonomy_ID  (variable, list)</b> – NCBI taxonomic identifier(s) for the organism(s)
<br><b>Uniprot_ID (variable, list)</b> – UniProt ID(s) for the protein(s)
<br><b>Swis-Prot reviewed entries (variable, list)</b> - Protein name/annotation in UniProtKB for SwissProt reviewed entries.

<p><b>Full Network Node Attributes</b></p>
<p><b>ACC (variable)</b> – UniProt accession for the protein
<br><b>CAZY  (variable)</b> – CAZy family name(s) for the protein
<br><b>CLASS  (variable)</b> – Phylogenetic class of the organism
<br><b>Description  (variable)</b> – protein name (annotation) in UniProtKB
<br><b>Domain  (variable)</b> – domain of life to which the organism belongs
<br><b>EC (variable)</b> – the EC number for the protein
<br><b>EFI_ID  (6 digit, starting with 5)</b> – target ID from EFI-DB
<br><b>FAMILY  (variable)</b> – Phylogenetic family of the organism
<br><b>GDNA  (true or false)</b> – availability of gDNA from the AECOM Protein Core
<br><b>GENUS  (variable)</b> – Phylogenetic genus of the organism
<br><b>GI  (variable)</b> – GI numbers mapped to the protein
<br><b>GN (variable)</b> – gene name for the protein
<br><b>GO (variable)</b> – Gene Ontology classification for the protein
<br><b>HMP_Body_Site  (body site)</b> – if a human microbiome species, the location of the species in/on the body 
<br><b>HMP_Oxygen  (oxygen requirement)</b> – if a human microbiome species, the oxygen requirement
<br><b>IPRO (variable)</b> – InterPro family(ies) into which the protein has been classified
<br><b>name (variable)</b> – UniProt accession
<br><b>ORDER  (variable)</b> – Phylogenetic order of the organism
<br><b>Organism  (variable)</b> – organism genus and species 
<br><b>PDB  (4 character)</b> – deposition code(s) for structures deposited in the Protein Data Bank
<br><b>PFAM  (variable)</b> – Pfam family(ies) into which the protein has been classified
<br><b>PHYLUM  (variable)</b> – Phylogenetic phylum of the organism
<br><b>SEQ  (variable)</b> – amino acid sequence of the protein
<br><b>Sequence_Length (variable)</b> – number of amino acid residues in the protein
<br><b>Shared name</b> – UniProt accession for the protein
<br><b>SPECIES  (variable)</b> – Phylogenetic species of the organism
<br><b>STATUS  (unreviewed or reviewed) </b> - indicates if the annotation was generated automatically and was found in TrEMBL (unreviewed) or manually annotated and found in Swiss-Prot (reviewed)
<br><b>Taxonomy_ID  (variable)</b> – NCBI taxonomic identifier for the organism
<br><b>Uniprot_ID (variable)</b> – UniProt ID for the protein
<br><b>Swis-Prot reviewed entries (variable)</b> - Protein name/annotation in UniProtKB for SwissProt reviewed entries.
<p>&nbsp;</p>
<div>
  <div></div>
</div>
<form action='tutorial_cytoscape.php' method='post'>
        <button type='submit' class='css_btn_class'>CONTINUE WITH THE TUTORIAL</button>
</form>

    <?php require('includes/gobackto_quest.php'); ?>

    </div>

<?php include_once 'includes/footer.inc.php'; ?>
