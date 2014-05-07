CREATE TABLE projects_ext_activities (
  id serial,
  department_id integer,
  activity character varying(255),
  description text
);
ALTER TABLE projects_ext_activities ADD PRIMARY KEY(id);

CREATE TABLE projects_ext_departments (
  id serial,
  department character varying(255),
  description text,
  address_id integer
);
ALTER TABLE projects_ext_departments ADD PRIMARY KEY(id);

CREATE TABLE projects_ext_extrainfo (
  id serial,
  project_id integer,
  activity_id integer
);
ALTER TABLE projects_ext_extrainfo ADD PRIMARY KEY(id);

CREATE TABLE projects_ext_metafields (
  id serial,
  field_name character varying(255),
  field_type integer,
  field_order integer,
  activity integer,
  show_list integer,
  default_value character varying(255)
);
ALTER TABLE projects_ext_metafields ADD PRIMARY KEY(id);

CREATE TABLE projects_ext_metavalues (
  id serial,
  project_id integer,
  meta_id integer,
  meta_value text
);
ALTER TABLE projects_ext_metavalues ADD PRIMARY KEY(id);
