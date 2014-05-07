ALTER TABLE `license` ADD `has_project_declaration` TINYINT(3);
CREATE TABLE `projects_declaration_extrainfo` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`project_id` INT( 11 ) NOT NULL ,
`task_date` INT( 11 ) NOT NULL ,
`damage_date` INT( 11 ) NOT NULL ,
`accident_type` INT( 11 ) NOT NULL ,
`perc_liabilities_wished` FLOAT( 16, 2 ) NOT NULL ,
`perc_liabilities_recognised` FLOAT( 16, 2 ) NOT NULL ,
`constituent` INT( 11 ) NOT NULL ,
`tarif` INT( 11 ) NOT NULL ,
`is_NCNP` INT( 11 ) NOT NULL ,
`perc_NCNP` FLOAT( 16, 2 ) NOT NULL ,
`client` INT( 11 ) NOT NULL ,
`adversary` VARCHAR( 255 ) NOT NULL ,
`expertise` VARCHAR( 255 ) NOT NULL ,
`lesion` INT( 11 ) NOT NULL ,
`lesion_description` TEXT NOT NULL ,
`hospitalisation` INT( 11 ) NOT NULL ,
`incapacity_for_work` INT NOT NULL ,
`profession` INT( 11 ) NOT NULL ,
`employment` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM ;

