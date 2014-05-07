ALTER TABLE license ADD COLUMN mail_migrated smallint;
CREATE TABLE mail_messages_data (
	mail_id integer,
	body text,
	header text
);
ALTER TABLE mail_messages_data ADD PRIMARY KEY (mail_id);
CREATE INDEX flag_indexed ON mail_messages (indexed);
/* TODO: RENAME COLUMN IS_HTML TO IS_TEXT */
ALTER TABLE mail_messages ADD is_text smallint;
UPDATE mail_messages SET is_text = is_html;
ALTER TABLE mail_messages DROP is_html;
ALTER TABLE license ADD COLUMN mail_migrated smallint;
