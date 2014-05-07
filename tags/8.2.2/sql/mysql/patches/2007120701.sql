ALTER TABLE `active_calls` ADD `ident` VARCHAR(255);
ALTER TABLE `active_calls` ADD INDEX `ident_index` (`ident`);