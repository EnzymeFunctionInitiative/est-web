#!/bin/bash


if [[ $1 == *"A" ]]; then
php ../html/create.php \
"email=noberg@illinois.edu"\
"&blast_input=APMNHIFEQDTCSVSDNYPTVNSAKLPDPFTTASGEKVTTKDQFECRRAEINKILQQYELGEYPGPPDSVEASLSGNSITVRVTVGSKSISFSASIRKPSGAGPFPAIIGIGGASIPIPSNVATITFNNDEFGAQMGSGSRGQGKFYDLFGRDHSAGSLTAWAWGVDRLIDGLEQVGAQASGIDTKRLGVTGCSRNGKGAFITGALVDRIALTIPQESGAGGAACWRISDQQKAAGANIQTAAQIITENPWFSRNFDPHVNSITSVPQDHHLLAALIVPRGLAVFENNIDWLGPVSTTGCMAAGRLIYKAYGVPNNMGFSLVGGHNHCQFPSSQNQDLNSYINYFLLGQGSPSGVEHSDVNVNVAEWAPWGAGAPTLA"\
"&blast_evalue=5"\
"&blast_max_seqs=5000"\
"&option_selected=A"\
"&submit=1"
fi


if [[ $1 == *"B" ]]; then
php ../html/create.php \
"email=noberg@illinois.edu"\
"&pfam_evalue=5"\
"&families_input=pf05544"\
"&pfam_fraction=5"\
"&pfam_domain="\
"&program=DIAMOND"\
"&option_selected=B"\
"&submit=1"
fi


if [[ $1 == *"C" ]]; then
    cp option_c_source.txt option_c.txt
    php ../html/create.php \
"email=noberg@illinois.edu"\
"&fasta_evalue=5"\
"&families_input2="\
"&fasta_fraction=5"\
"&program=DIAMOND"\
"&option_selected=C"\
"&submit=1" \
"fasta_file=option_c.txt"
fi


if [[ $1 == *"D" ]]; then
    cp accessions_source.txt accessions.txt
    php ../html/create.php \
"email=noberg@illinois.edu"\
"&accession_evalue=5"\
"&accession_fraction=5"\
"&option_selected=D"\
"&submit=1" \
"accession_file=accessions.txt"
fi




