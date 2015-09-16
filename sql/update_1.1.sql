#database update script version 1.1
#Add FASTA option to generate_type
ALTER TABLE generate MODIFY COLUMN generate_type ENUM('BLAST','FAMILIES','FASTA');

#Add FASTA filename column to generate table
ALTER TABLE generate ADD COLUMN generate_fasta_file VARCHAR(255);

#Add max blast sequences to generate table
ALTER TABLE generate ADD COLUMN generate_blast_max_sequence INT;

#Add fraction 
ALTER TABLE generate ADD COLUMN generate_fraction INT DEFAULT 1;

#Add domain
ALTER TABLE generate ADD COLUMN generate_domain BOOLEAN DEFAULT FALSE;

#Add total number of filter sequences in analysis table
ALTER TABLE analysis ADD COLUMN analysis_filter_sequences INT;

#Table that lists the database versions
CREATE TABLE db_version (
        db_version_id INT NOT NULL AUTO_INCREMENT,
        db_version_date VARCHAR(255),
        db_version_interpro VARCHAR(255),
	db_version_unipro VARCHAR(255),
	db_version_default BOOLEAN DEFAULT 0,
	db_version_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(db_version_id)
);

#add reference id to db_version for generate table
ALTER TABLE generate ADD COLUMN generate_db_version INT REFERENCES db_version(db_version_id);
