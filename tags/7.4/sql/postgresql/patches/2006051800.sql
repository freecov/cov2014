CREATE TABLE templates_files (
	id serial,
	template_id integer,
	name character varying(255),
	temp_id integer,
	"type" character varying(255),
	"size" character varying(255)
);
ALTER TABLE templates_files ADD PRIMARY KEY (id);
