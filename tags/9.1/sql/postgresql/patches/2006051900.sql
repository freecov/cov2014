CREATE TABLE mailfolder_permissions (
    id serial NOT NULL,
    user_id integer,
    user_id_visitor integer,
	folder_id integer,
    permissions character varying(255) DEFAULT 'RW'::character varying
);
CREATE INDEX mailfolder_permissions_user_id ON mailfolder_permissions USING btree (user_id);
CREATE INDEX mailfolder_permissions_user_id_visitor ON mailfolder_permissions USING btree (user_id_visitor);
