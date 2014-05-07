ALTER TABLE `notes` ADD `campaign_id` INT NOT NULL AFTER `address_id` ;
ALTER TABLE `campaign_records` ADD `note_id` INT NOT NULL ;
ALTER TABLE `campaign_records` ADD `email_id` INT NOT NULL ;
ALTER TABLE `campaign_records` ADD `appointment_id` INT NOT NULL ;
ALTER TABLE `address` ADD `do_not_contact` INT NOT NULL ;
ALTER TABLE `campaign` ADD `tracker_id` INT NOT NULL ;