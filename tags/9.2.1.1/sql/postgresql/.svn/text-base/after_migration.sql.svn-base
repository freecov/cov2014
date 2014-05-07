ALTER TABLE adres RENAME TO address;
ALTER TABLE address ADD COLUMN address2 character varying(255);

ALTER TABLE adres_multivers RENAME TO address_multivers;
ALTER TABLE address_multivers ADD COLUMN address2 character varying(255);

ALTER TABLE adres_overig RENAME TO address_other;

ALTER TABLE adres_personen RENAME TO address_private;

ALTER TABLE adres_sync RENAME TO address_sync;

ALTER TABLE adres_sync_guid RENAME TO address_sync_guid;

ALTER TABLE adres_sync_records RENAME TO address_sync_records;

ALTER TABLE adresinfo RENAME TO address_info;

ALTER TABLE agenda RENAME TO calendar;
ALTER TABLE calendar ADD COLUMN subject character varying(255);

ALTER TABLE agenda_machtiging RENAME TO calendar_permissions;

ALTER TABLE akties RENAME TO finance_akties;

ALTER TABLE arbo RENAME TO arbo_arbo;

ALTER TABLE bcards RENAME TO address_businesscards;

ALTER TABLE bedrijfsclassifi RENAME TO address_classifications;

ALTER TABLE begin_standen_finance RENAME TO finance_begin_standen_finance;

ALTER TABLE boekingen RENAME TO finance_boekingen;

ALTER TABLE boekingen_20012003 RENAME TO finance_boekingen_20012003;

ALTER TABLE cms_bestanden RENAME TO cms_files;
ALTER TABLE cms_files DROP COLUMN dat;

ALTER TABLE filesys_bestanden RENAME TO filesys_files;
ALTER TABLE filesys_files DROP COLUMN data;

ALTER TABLE filesys_mappen RENAME TO filesys_folders;

ALTER TABLE filesys_rechten RENAME TO filesys_permissions;

ALTER TABLE functies RENAME TO functions;

ALTER TABLE gebruikers RENAME TO users;

ALTER TABLE gebruikers_log RENAME TO userchangelog;

ALTER TABLE groep RENAME TO user_groups;

ALTER TABLE grootboeknummers RENAME TO finance_grootboeknummers;

ALTER TABLE hoofd_project RENAME TO projects_master;

ALTER TABLE hypotheek RENAME TO morgage;

ALTER TABLE inkopen RENAME TO finance_inkopen;

ALTER TABLE jaar_afsluitingen RENAME TO finance_jaar_afsluitingen;

ALTER TABLE klachten RENAME TO issues;

ALTER TABLE klanten RENAME TO finance_klanten;

ALTER TABLE mail_attachments DROP COLUMN dat;

ALTER TABLE mail_berichten RENAME TO mail_messages;

ALTER TABLE mail_mappen RENAME TO mail_folders;

ALTER TABLE mail_templates_bestanden RENAME TO mail_templates_files;
ALTER TABLE mail_templates_files DROP COLUMN dat;

ALTER TABLE medewerker RENAME TO employees_info;

ALTER TABLE notitie RENAME TO notes;

ALTER TABLE offertes RENAME TO finance_offertes;

ALTER TABLE omzet_akties RENAME TO finance_omzet_akties;

ALTER TABLE omzet_totaal RENAME TO finance_omzet_totaal;

ALTER TABLE overige_posten RENAME TO finance_overige_posten;

ALTER TABLE poll_antwoord RENAME TO poll_answers;

ALTER TABLE poll_vraag RENAME TO polls;

DROP TABLE prefs;

ALTER TABLE prikbord RENAME TO announcements;

ALTER TABLE producten RENAME TO finance_producten;

ALTER TABLE producten_in_offertes RENAME TO finance_producten_in_offertes;

ALTER TABLE relatie_type RENAME TO finance_relatie_type;

ALTER TABLE snack_bestel RENAME TO snack_order;

ALTER TABLE snack_lijst RENAME TO snack_items;

ALTER TABLE soortbedrijf RENAME TO finance_soortbedrijf;

DROP TABLE talen;

DROP TABLE talen_backup;

DROP TABLE teksten;

ALTER TABLE templates_instellingen RENAME TO templates_settings;

ALTER TABLE uren_activ RENAME TO hours_activities;

ALTER TABLE urenreg RENAME TO hours_registration;

ALTER TABLE license ADD COLUMN mail_migrated smallint;
CREATE TABLE mail_messages_data (
	mail_id integer,
	body text,
	header text
);
ALTER TABLE mail_messages_data ADD PRIMARY KEY (mail_id);
CREATE INDEX flag_indexed ON mail_messages (indexed);
ALTER TABLE mail_messages ADD is_text smallint;
UPDATE mail_messages SET is_text = is_html;
ALTER TABLE mail_messages DROP is_html;
ALTER TABLE mail_signatures ADD realname character varying(255);
ALTER TABLE mail_signatures ADD companyname character varying(255);
ALTER TABLE mail_messages ADD options character varying(255);
ALTER TABLE mail_tracking ADD is_sent smallint;
