#database update script version 1.1
#Add FASTA option to generate_type
ALTER TABLE generate MODIFY COLUMN generate_type ENUM('BLAST','FAMILIES','FASTA');

#Add FASTA filename column to generate table
ALTER TABLE generate ADD COLUMN generate_fasta_file VARCHAR(255);

#Add total number of filter sequences in analysis table
ALTER TABLE analysis ADD COLUMN analysis_filter_sequences INT;

