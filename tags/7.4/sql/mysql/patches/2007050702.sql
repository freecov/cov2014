ALTER TABLE `funambol_stats` ADD `synchash` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `funambol_address_sync` CHANGE `guid` `guid` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `funambol_calendar_sync` CHANGE `guid` `guid` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `funambol_file_sync` CHANGE `guid` `guid` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `funambol_todo_sync` CHANGE `guid` `guid` VARCHAR( 255 ) NOT NULL;
