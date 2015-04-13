ALTER TABLE generate ADD COLUMN generate_email VARCHAR(255) AFTER generate_id;
ALTER TABLE generate ADD COLUMN generate_key VARCHAR(255) AFTER generate_email;
ALTER TABLE generate ADD COLUMN generate_time_started DATETIME AFTER generate_time_created;

ALTER TABLE analysis ADD COLUMN analysis_time_started DATETIME AFTER analysis_time_created;

UPDATE generate 
	LEFT JOIN quest 
ON
	generate.generate_quest_id = quest.quest_id
SET
	generate.generate_email = quest.quest_email,
	generate.generate_key = quest.quest_key;

ALTER TABLE generate DROP COLUMN generate_quest_id;
ALTER TABLE analysis DROP COLUMN analysis_quest_id;

DROP TABLE quest;


UPDATE generate SET generate_time_started = generate_time_created;
UPDATE analysis SET analysis_time_started = analysis_time_created;


