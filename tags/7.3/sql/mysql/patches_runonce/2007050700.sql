DROP TABLE IF EXISTS `funambol_address_sync_v3`;
CREATE TABLE `funambol_address_sync_v3` (
`id` int( 11 ) NOT NULL AUTO_INCREMENT ,
`guid` varchar( 255 ) NOT NULL ,
`address_table` varchar( 255 ) NOT NULL ,
`address_id` int( 11 ) NOT NULL ,
`user_id` int( 11 ) NOT NULL ,
`file_hash` varchar( 255 ) NOT NULL ,
`datetime` int( 11 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
KEY `user_id` ( `user_id` ) ,
KEY `user_id_2` ( `user_id` ) ,
KEY `user_id_3` ( `user_id` )
) ENGINE = MYISAM DEFAULT CHARSET = latin1;

INSERT INTO `funambol_address_sync_v3`
SELECT *
FROM `funambol_address_sync`;

TRUNCATE `funambol_address_sync`;
TRUNCATE `funambol_calendar_sync`;
TRUNCATE `funambol_file_sync`;
TRUNCATE `funambol_stats`;
TRUNCATE `funambol_todo_sync`;
UPDATE `license` SET `funambol_server_version` = 600;
