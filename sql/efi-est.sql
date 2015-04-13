CREATE TABLE generate (
	generate_id INT NOT NULL AUTO_INCREMENT,
	generate_email VARCHAR(255),
	generate_key VARCHAR(255),
	generate_type ENUM('BLAST','FAMILIES'),
	generate_blast TEXT,
	generate_status ENUM('NEW','RUNNING','FINISH','FAILED') DEFAULT 'NEW',
	generate_families TINYTEXT,
	generate_evalue INT,
	generate_pbs_number INT,
	generate_time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	generate_time_started DATETIME,
	generate_time_completed DATETIME,
	generate_sequence_max BOOLEAN DEFAULT 0,
	generate_num_sequences INT DEFAULT 0,
	PRIMARY KEY(generate_id)
);

CREATE TABLE analysis (
	analysis_id INT NOT NULL AUTO_INCREMENT,
	analysis_generate_id INT REFERENCES generate(generate_id),
	analysis_quest_id INT REFERENCES quest(quest_id),
	analysis_status ENUM('NEW','RUNNING','FINISH','FAILED') DEFAULT 'NEW',
	analysis_min_length INT,
	analysis_max_length INT,
	analysis_filter ENUM('eval') DEFAULT 'eval',
	analysis_evalue INT,
	analysis_name VARCHAR(255),
	analysis_pbs_number INT,
	analysis_time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	analysis_time_started DATETIME,
	analysis_time_completed DATETIME,
	PRIMARY KEY(analysis_id)

);

ALTER TABLE generate AUTO_INCREMENT = 1000;
ALTER TABLE analysis AUTO_INCREMENT = 1000;
