ALTER TABLE mail_messages ADD COLUMN description_new text;
UPDATE mail_messages SET description_new = description;
ALTER TABLE mail_messages DROP COLUMN description;
ALTER TABLE mail_messages RENAME COLUMN description_new TO description;
