CREATE TABLE IF NOT EXISTS `meta_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
);

ALTER TABLE  `meta_table` ADD  `group_id` INT( 11) NULL;
