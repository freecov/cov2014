ALTER TABLE `mail_messages` ADD `private_id` INT NOT NULL DEFAULT '0' AFTER `project_id` ;
ALTER TABLE `calendar` ADD `private_id` INT NOT NULL DEFAULT '0' AFTER `project_id` ;