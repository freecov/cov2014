ALTER TABLE `address` DROP COLUMN `sync_modified`;
ALTER TABLE `address_businesscards` DROP COLUMN `sync_modified`;
ALTER TABLE `address_other` DROP COLUMN `sync_modified`;
ALTER TABLE `address_private` DROP COLUMN `sync_modified`;

DROP TABLE `address_sync`;
DROP TABLE `address_sync_guid`;
DROP TABLE `address_sync_records`;
DROP TABLE `agenda_sync`;
DROP TABLE `todo_sync`;

ALTER TABLE `calendar` DROP COLUMN `sync_guid`, DROP COLUMN `sync_hash`;
ALTER TABLE `todo` DROP COLUMN `sync_guid`, DROP COLUMN `sync_hash`;
ALTER TABLE `license` DROP COLUMN `has_sync4j`;

ALTER TABLE `users` DROP COLUMN `sync4j_source`, DROP COLUMN `sync4j_path`, DROP COLUMN `sync4j_source_adres`, DROP COLUMN `sync4j_source_todo`;

