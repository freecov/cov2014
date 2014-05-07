CREATE TABLE mail_autoreply (
	id int(11) auto_increment,
	user_id int(11),
	timestamp_start int(11),
	timestamp_end int(11),
	subject varchar(255),
	body varchar(255),
	notified text,
	is_active tinyint(2),
	primary key (id)
);
