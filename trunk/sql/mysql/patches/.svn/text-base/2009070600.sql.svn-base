CREATE TABLE sessions (
  session_id varchar(255) NOT NULL default '0',
  session_data longtext,
  expire int(1) unsigned NOT NULL default '0',
  user_agent varchar(255) default NULL,
  KEY session_id USING BTREE (session_id),
  KEY expire USING BTREE (expire)
);
