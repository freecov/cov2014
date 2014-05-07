CREATE TABLE `meta_global` (
	`id` int(11) NOT NULL auto_increment,
	`meta_id` int(11) NOT NULL,
	`relation_id` int(11) NOT NULL,
	`value` mediumtext NOT NULL,
	PRIMARY KEY  (`id`)
);
