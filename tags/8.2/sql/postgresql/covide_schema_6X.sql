CREATE TABLE active_calls (
    "name" character varying(255),
    address_id integer,
    "timestamp" integer
);

CREATE TABLE adres (
    id serial NOT NULL,
    surname character varying(255),
    givenname character varying(255),
    companyname character varying(255),
    address character varying(255),
    zipcode character varying(255),
    city character varying(255),
    phone_nr character varying(255),
    fax_nr character varying(255),
    email character varying(255),
    user_id integer,
    is_company smallint,
    link integer,
    is_public smallint,
    mobile_nr character varying(255),
    debtor_nr integer,
    country character varying(255),
    company_type smallint,
    "comment" text,
    website character varying(255),
    relation_type smallint,
    tav character varying(255),
    contact_person character varying(255),
    is_customer smallint,
    is_supplier smallint,
    is_partner smallint,
    is_prospect smallint,
    is_other smallint,
    "warning" character varying(255),
    pobox character varying(255),
    pobox_zipcode character varying(255),
    pobox_city character varying(255),
    classification character varying(255),
    account_manager integer,
    is_active smallint DEFAULT 1,
    contact_letterhead smallint DEFAULT 2,
    contact_commencement smallint DEFAULT 2,
    contact_initials character varying(255),
    contact_givenname character varying(255),
    contact_infix character varying(255),
    contact_surname character varying(255),
    e4lid character varying(255),
    title integer,
    relname character varying(255),
    relpass character varying(255),
    modified integer,
    sync_modified integer
);

CREATE TABLE adres_multivers (
    id serial NOT NULL,
    surname character varying(255),
    givenname character varying(255),
    companyname character varying(255),
    address character varying(255),
    zipcode character varying(255),
    city character varying(255),
    phone_nr character varying(255),
    fax_nr character varying(255),
    email character varying(255),
    user_id integer,
    is_company smallint,
    link integer,
    is_public smallint,
    mobile_nr character varying(255),
    debtor_nr integer,
    country character varying(255),
    company_type smallint,
    "comment" text,
    website character varying(255),
    relation_type smallint,
    tav character varying(255),
    contact_person character varying(255),
    is_customer smallint,
    is_supplier smallint,
    is_partner smallint,
    is_prospect smallint,
    is_other smallint,
    "warning" character varying(255),
    pobox character varying(255),
    pobox_zipcode character varying(255),
    pobox_city character varying(255),
    classification character varying(255),
    account_manager integer,
    is_active smallint DEFAULT 1,
    contact_letterhead smallint DEFAULT 2,
    contact_commencement smallint DEFAULT 2,
    contact_initials character varying(255),
    contact_givenname character varying(255),
    contact_infix character varying(255),
    contact_surname character varying(255),
    e4lid character varying(255),
    title integer,
    relname character varying(255),
    relpass character varying(255),
    modified integer,
    sync_modified integer
);

CREATE TABLE adres_overig (
    id serial NOT NULL,
    companyname character varying(255),
    surname character varying(255),
    givenname character varying(255),
    address character varying(255),
    zipcode character varying(255),
    city character varying(255),
    phone_nr character varying(255),
    fax_nr character varying(255),
    email character varying(255),
    user_id integer,
    is_company smallint,
    is_public smallint,
    mobile_nr character varying(255),
    "comment" text,
    website character varying(255),
    pobox character varying(255),
    pobox_zipcode character varying(255),
    pobox_city character varying(255),
    is_active smallint DEFAULT 1,
    is_companylocation smallint DEFAULT 0,
    arbo_kantoor smallint DEFAULT 0,
    arbo_bedrijf integer,
    arbo_code character varying(255),
    arbo_team character varying(255),
    sync_modified integer
);

CREATE TABLE adres_personen (
    id serial NOT NULL,
    surname character varying(255),
    givenname character varying(255),
    address character varying(255),
    zipcode character varying(255),
    city character varying(255),
    phone_nr character varying(255),
    fax_nr character varying(255),
    email character varying(255),
    user_id integer,
    is_company smallint,
    is_public smallint,
    mobile_nr character varying(255),
    "comment" text,
    website character varying(255),
    pobox character varying(255),
    pobox_zipcode character varying(255),
    pobox_city character varying(255),
    is_active smallint DEFAULT 1,
    e4lid character varying(255),
    country character varying(255),
    modified integer,
    sync_modified integer
);

CREATE TABLE adres_sync (
    id serial NOT NULL,
    address_id integer,
    address_table character varying(255),
    is_private integer,
    account_manager integer,
    sync_modified integer,
    sync_hash character varying(255),
    "parent_id" integer
);

CREATE TABLE adres_sync_guid (
    id serial NOT NULL,
    sync_id integer,
    sync_guid integer,
    user_id integer
);

CREATE TABLE adres_sync_records (
    id serial NOT NULL,
    address_table character varying(255),
    address_id integer,
    user_id integer
);

CREATE TABLE adresinfo (
    id serial NOT NULL,
    address_id integer NOT NULL DEFAULT 0,
    "comment" text,
    classification character varying(255),
    "warning" character varying(255),
    photo character varying(255),
    provision_perc numeric(8,2),
    md5 character varying(255)
);

CREATE TABLE agenda (
    id serial NOT NULL,
    timestamp_start integer,
    timestamp_end integer,
    description text,
    user_id integer,
    address_id integer,
    project_id integer,
    is_private smallint,
    note_id integer NOT NULL DEFAULT 0,
    is_important smallint DEFAULT 0,
    is_registered smallint DEFAULT 0,
    notifytime integer,
    notified smallint DEFAULT 0,
    location character varying(255),
    human_start timestamp without time zone,
    human_end timestamp without time zone,
    is_group integer,
    group_id integer,
    kilometers integer,
    is_repeat integer,
    multirel character varying(255),
    repeat_type character(1),
    is_alert smallint DEFAULT 0,
    is_holiday smallint DEFAULT 0,
    is_specialleave smallint DEFAULT 0,
    is_ill smallint DEFAULT 0,
    e4l_id character varying(255),
    is_dnd smallint DEFAULT 0,
    deckm smallint DEFAULT 0,
    modified integer,
    modified_by integer,
    sync_guid integer,
    sync_hash character varying(255)
);

CREATE TABLE agenda_machtiging (
    id serial NOT NULL,
    user_id integer,
    user_id_visitor integer,
    permissions character varying(255) DEFAULT 'RO'
);

CREATE TABLE agenda_sync (
    id serial NOT NULL,
    user_id integer,
    sync_guid integer,
    "action" character varying(255)
);

CREATE TABLE akties (
    id serial NOT NULL,
    address_id integer,
    omschrijving text,
    datum integer,
    rekeningflow numeric(16,2),
    factuur_nr integer,
    rekeningflow_btw numeric(16,2),
    grootboeknummer_id integer
);

CREATE TABLE arbo (
    id serial NOT NULL,
    regiokantoor character varying(255),
    regiofax character varying(255),
    regiotel character varying(255),
    regioteam character varying(255),
    werkgever character varying(255),
    adres character varying(255),
    postcode character varying(255),
    city character varying(255),
    aansluitcode character varying(255),
    contactpers character varying(255),
    tel character varying(255),
    fax character varying(255)
);

CREATE TABLE arbo_verslag (
    id serial NOT NULL,
    user_id integer,
    manager integer,
    soort integer,
    datum integer,
    omschrijving text,
    acties text,
    betrokkenen text,
    datum_invoer integer,
    ziekmelding integer
);

CREATE TABLE arbo_ziek (
    id serial NOT NULL,
    user_id integer,
    werkgever_id integer,
    arbo_id integer,
    datum integer,
    datum_ziek integer,
    datum_melding integer,
    datum_herstel integer,
    herstel integer,
    herstel_loon integer,
    zwanger smallint DEFAULT 0,
    zwanger_ziek smallint DEFAULT 0,
    orgaandonatie smallint DEFAULT 0,
    ongeval smallint DEFAULT 0,
    ontvangt_wao smallint DEFAULT 0,
    wao_perc integer,
    herintr_wao smallint DEFAULT 0,
    herintr_perc integer,
    bijzonderheden text,
    ziekmelding integer
);

CREATE TABLE bcards (
    id serial NOT NULL,
    address_id integer NOT NULL DEFAULT 0,
    givenname character varying(255),
    initials character varying(255),
    infix character varying(255),
    surname character varying(255),
    timestamp_birthday integer,
    mobile_nr character varying(255),
    phone_nr character varying(255),
    email character varying(255),
    memo text,
    commencement smallint DEFAULT 3,
    classification character varying(255),
    letterhead smallint DEFAULT 2,
    business_address character varying(255),
    business_zipcode character varying(255),
    business_city character varying(255),
    business_mobile_nr character varying(255),
    business_phone_nr character varying(255),
    business_email character varying(255),
    business_fax_nr character varying(255),
    personal_address character varying(255),
    personal_zipcode character varying(255),
    personal_city character varying(255),
    personal_mobile_nr character varying(255),
    personal_phone_nr character varying(255),
    personal_email character varying(255),
    personal_fax_nr character varying(255),
    e4lid character varying(255),
    title integer,
    alternative_name character varying(255),
    modified integer,
    photo character varying(255),
    sync_modified integer
);

CREATE TABLE bedrijfsclassifi (
    id serial NOT NULL,
    description character varying(255),
    is_active smallint DEFAULT 0,
    is_locked smallint,
    subtype smallint
);

CREATE TABLE begin_standen_finance (
    id serial NOT NULL,
    grootboek_id integer NOT NULL DEFAULT 0,
    stand numeric(16,2) NOT NULL DEFAULT 0
);

CREATE TABLE boekingen (
    id serial NOT NULL,
    credit smallint NOT NULL DEFAULT 0,
    factuur integer NOT NULL DEFAULT 0,
    grootboek_id integer,
    status smallint,
    datum integer,
    koppel_id integer,
    bedrag numeric(16,2),
    product integer,
    inkoop smallint NOT NULL DEFAULT 0,
    deb_nr integer NOT NULL DEFAULT 0,
    betaald smallint NOT NULL DEFAULT 0,
    locked smallint NOT NULL DEFAULT 0
);

CREATE TABLE boekingen_20012003 (
    id serial NOT NULL,
    credit smallint NOT NULL DEFAULT 0,
    factuur integer NOT NULL DEFAULT 0,
    grootboek_id integer,
    status smallint,
    datum integer,
    koppel_id integer,
    bedrag numeric(16,2),
    product integer,
    inkoop smallint NOT NULL DEFAULT 0,
    deb_nr integer NOT NULL  DEFAULT 0,
    betaald smallint NOT NULL DEFAULT 0
);

CREATE TABLE bugs (
    id serial NOT NULL,
    user_id integer,
    module character varying(255),
    subject character varying(255),
    description text
);

CREATE TABLE cdr (
    calldate timestamp with time zone NOT NULL DEFAULT now(),
    clid character varying(255),
    src character varying(255),
    dst character varying(255),
    dcontext character varying(255),
    channel character varying(255),
    dstchannel character varying(255),
    lastapp character varying(255),
    lastdata character varying(255),
    duration bigint,
    billsec bigint,
    disposition character varying(255),
    amaflags bigint,
    accountcode character varying(255),
    uniqueid character varying(255),
    userfield character varying(255)
);

CREATE TABLE chat_rooms (
    id serial NOT NULL,
    name character varying(255),
    users text,
    topic character varying(255),
    is_active smallint
);

CREATE TABLE chat_text (
    id serial NOT NULL,
    room integer,
    "user" integer,
    text character varying(255),
    "timestamp" integer
);

CREATE TABLE cms_bestanden (
    id serial NOT NULL,
    name character varying(255),
    dat character varying(255),
    "type" character varying(255) DEFAULT 'application/octet-stream',
    size character varying(255) DEFAULT '0'
);

CREATE TABLE cms_data (
    id serial NOT NULL,
    user_id integer NOT NULL DEFAULT 0,
    page_parent integer NOT NULL DEFAULT 0,
    page_type integer NOT NULL DEFAULT 0,
    page_titel character varying(255),
    publicationdate integer NOT NULL DEFAULT 0,
    page_data text,
    page_redirect character varying(255)  DEFAULT '0',
    is_public smallint NOT NULL DEFAULT 0,
    is_active smallint NOT NULL DEFAULT 0,
    is_menuitem smallint NOT NULL DEFAULT 0
);

CREATE TABLE cms_images (
    id serial NOT NULL,
    page_id integer NOT NULL DEFAULT 0,
    path character varying(255),
    description character varying(255)
);

CREATE TABLE faq (
    id serial NOT NULL,
    category_id integer,
    question text,
    answer text
);

CREATE TABLE faq_cat (
    id serial NOT NULL,
    name character varying(255)
);

CREATE TABLE faxes (
    id serial NOT NULL,
    date integer,
    sender character varying(255),
    receiver character varying(255),
    relation_id integer
);

CREATE TABLE filesys_bestanden (
    id serial NOT NULL,
    name character varying(255),
    folder_id integer NOT NULL DEFAULT 0,
    project_id integer NOT NULL DEFAULT 0,
    address_id integer NOT NULL DEFAULT 0,
    "timestamp" integer NOT NULL DEFAULT 0,
    user_id integer NOT NULL DEFAULT 0,
    data character varying(255),
    "type" character varying(255),
    size character varying(255),
    description text
);

CREATE TABLE filesys_mappen (
    id serial NOT NULL,
    name character varying(255) ,
    is_public smallint NOT NULL DEFAULT 0,
    is_relation smallint NOT NULL DEFAULT 0,
    address_id integer,
    user_id integer,
    parent_id integer,
    is_shared character varying(255),
    description text,
    hrm_id integer,
    sticky smallint DEFAULT 0,
    project_id integer
);

CREATE TABLE filesys_rechten (
    id serial NOT NULL,
    folder_id integer,
    user_id character varying(255),
    permissions character varying(255)
);

CREATE TABLE forum (
    id serial NOT NULL,
    ref integer,
    user_id integer NOT NULL DEFAULT 0,
    project_id integer NOT NULL DEFAULT 0,
    "timestamp" integer,
    subject character varying(255),
    body text,
    "read" text
);

CREATE TABLE functies (
    id serial NOT NULL,
    name character varying(255),
    description text
);

CREATE TABLE gebruikers (
    id serial NOT NULL,
    "username" character varying(255),
    "password" character varying(255),
    pers_nr integer,
    xs_usermanage smallint DEFAULT 0,
    xs_addressmanage smallint,
    xs_projectmanage smallint,
    xs_forummanage smallint,
    address_id integer,
    xs_pollmanage smallint,
    xs_faqmanage smallint,
    xs_issuemanage smallint,
    xs_chatmanage smallint,
    xs_turnovermanage smallint,
    xs_notemanage smallint,
    xs_todomanage smallint DEFAULT 0,
    "comment" text,
    is_active smallint NOT NULL DEFAULT 1,
    style smallint,
    mail_server character varying(255),
    mail_user_id character varying(255),
    mail_password character varying(255),
    mail_email character varying(255),
    mail_email1 character varying(255),
    mail_html smallint,
    mail_signature text,
    mail_showcount smallint DEFAULT 0,
    mail_deltime integer,
    days smallint DEFAULT 0,
    htmleditor smallint DEFAULT 2 NOT NULL,
    addressaccountmanage character varying(255),
    calendarselection character varying(255),
    showhelp integer NOT NULL DEFAULT 0,
    showpopup smallint DEFAULT 1,
    xs_salariscommanage smallint DEFAULT 0,
    mail_server_deltime integer DEFAULT 1,
    xs_companyinfomanage smallint DEFAULT 0,
    xs_hrmmanage smallint DEFAULT 0,
    language character(2) NOT NULL DEFAULT 'NL',
    employer_id integer,
    automatic_logout smallint DEFAULT 0,
    mail_view_textmail_only smallint,
    e4l_update integer,
    dayquote integer,
    infowin_altmethod integer,
    xs_e4l smallint DEFAULT 0,
    xs_filemanage integer,
    xs_limitusermanage integer,
    change_theme integer,
    xs_relationmanage integer,
    xs_newslettermanage integer,
    renderstatus text,
    mail_forward integer,
    showvoip smallint DEFAULT 0,
    signature character varying(255),
    voip_device character varying(255),
    xs_salesmanage smallint,
    e4l_username character varying(255),
    e4l_password character varying(255),
    mail_imap integer,
    xs_hypo smallint,
    mail_num_items integer,
    xs_arbo integer,
    xs_arbo_validated integer,
    alternative_note_view_desktop integer,
    rssnews integer,
    mail_showbcc integer,
    calendarmode integer DEFAULT 1,
    sync4j_source character varying(255),
    sync4j_path character varying(255),
    sync4j_source_adres character varying(255),
    sync4j_source_todo character varying(255)
);

CREATE TABLE gebruikers_log (
    id serial NOT NULL,
    manager integer,
    user_id integer,
    "timestamp" integer,
    change text
);

CREATE TABLE groep (
    id serial NOT NULL,
    name character varying(255),
    description character varying(255),
    members character varying(255),
    manager integer
);

CREATE TABLE grootboeknummers (
    id serial NOT NULL,
    nr integer,
    titel character varying(255),
    debiteur smallint
);

CREATE TABLE hoofd_project (
    id serial NOT NULL,
    name character varying(255),
    description text,
    manager integer,
    is_active smallint,
    status smallint DEFAULT 0,
    address_id integer,
    address_businesscard_id integer
);

CREATE TABLE hypotheek (
    id serial NOT NULL,
    timestamp integer,
    total_sum numeric(16,2),
    investor integer,
    insurancer integer,
    year_payement integer,
    user_id integer,
    user_src integer,
    subject character varying(255),
    description text,
    "type" integer,
    address_id integer,
    is_active smallint
);

CREATE TABLE inkopen (
    id serial NOT NULL,
    datum integer NOT NULL DEFAULT 0,
    balanspost integer NOT NULL DEFAULT 0,
    boekstuknr integer NOT NULL DEFAULT 1,
    descr character varying(255),
    leverancier_nr integer NOT NULL DEFAULT 0,
    bedrag_ex numeric(16,2),
    bedrag_inc numeric(16,2),
    bedrag_btw numeric(16,2),
    betaald numeric(16,2) NOT NULL
);

CREATE TABLE jaar_afsluitingen (
    jaar integer NOT NULL DEFAULT 0,
    datum_afgesloten integer NOT NULL DEFAULT 0
);

CREATE TABLE klachten (
    id serial NOT NULL,
    "timestamp" integer,
    description text,
    solution text,
    project_id integer,
    registering_id integer,
    user_id integer,
    "priority" smallint DEFAULT 0,
    is_solved smallint,
    address_id integer,
    email text,
    reference_nr integer
);

CREATE TABLE klanten (
    id serial NOT NULL,
    naam character varying(255),
    adres character varying(255),
    postcode character varying(255),
    city character varying(255),
    land character varying(255),
    telefoonnummer character varying(255),
    faxnummer character varying(255),
    email character varying(255),
    soortbedrijf_id integer,
    aantalwerknemers integer,
    address_id integer,
    contactpersoon character varying(255),
    contactpersoon_voorletters character varying(255),
    totaal_flow integer,
    totaal_flow_12 integer
);

CREATE TABLE license (
    name character varying(255) ,
    code character varying(255) ,
    "timestamp" integer NOT NULL DEFAULT 0,
    has_project smallint,
    has_faq smallint,
    has_forum smallint,
    has_issues smallint,
    has_chat smallint,
    has_announcements smallint,
    has_enquete smallint,
    has_emagazine smallint,
    has_finance smallint,
    has_external smallint,
    has_snack smallint,
    email character varying(255),
    has_snelstart smallint,
    plain smallint,
    latest_version character varying(255) DEFAULT '0',
    has_multivers smallint,
    multivers_path character varying(255),
    mail_interval integer,
    has_salariscom smallint DEFAULT 1,
    multivers_update integer,
    has_hrm smallint,
    has_exact smallint DEFAULT 0,
    finance_start_date integer,
    max_upload_size character varying(255) DEFAULT '24M',
    has_e4l smallint DEFAULT 0,
    dayquote integer,
    dayquote_nr integer,
    mail_shell smallint DEFAULT 0,
    has_voip smallint DEFAULT 0,
    has_sales smallint,
    filesyspath character varying(255),
    has_hypo smallint,
    has_arbo integer,
    disable_basics integer,
    has_privoxy_config integer,
    has_sync4j integer
);

CREATE TABLE login_log (
    id serial NOT NULL,
    user_id integer NOT NULL DEFAULT 0,
    ip character varying(255),
    "time" integer NOT NULL DEFAULT 0,
    "day" integer NOT NULL DEFAULT 0
);

CREATE TABLE mail_attachments (
    id serial NOT NULL,
    message_id integer NOT NULL DEFAULT 0,
    name character varying(255),
    temp_id integer NOT NULL DEFAULT 0,
    dat character varying(255),
    "type" character varying(255) DEFAULT 'application/octet-stream',
    size character varying(255) DEFAULT '0',
    cid character varying(255)
);

CREATE TABLE mail_berichten (
    id serial NOT NULL,
    message_id character varying(255),
    folder_id integer NOT NULL DEFAULT 0,
    user_id integer NOT NULL DEFAULT 0,
    address_id integer NOT NULL DEFAULT 0,
    project_id integer,
    sender character varying(255),
    subject text,
    header text,
    body text,
    "date" character varying(255),
    is_html smallint,
    is_public smallint,
    sender_emailaddress character varying(255),
    "to" text,
    cc text,
    description text,
    is_new smallint NOT NULL DEFAULT 0,
    replyto character varying(255),
    status_pop smallint NOT NULL DEFAULT 0,
    bcc text,
    date_received integer NOT NULL DEFAULT 0,
    template_id integer NOT NULL DEFAULT 0,
    askwichrel smallint DEFAULT 0,
    indexed integer
);

CREATE TABLE mail_filters (
    id serial NOT NULL,
    user_id integer NOT NULL DEFAULT 0,
    sender character varying(255),
    receipient character varying(255),
    subject character varying(255),
    to_mapid integer NOT NULL DEFAULT 0
);

CREATE TABLE mail_mappen (
    id serial NOT NULL,
    name character varying(255),
    user_id integer,
    parent_id integer
);

CREATE TABLE mail_signatures (
    id serial NOT NULL,
    user_id integer,
    email character varying(255),
    signature text,
    subject character varying(255)
);

CREATE TABLE mail_templates (
    id serial NOT NULL,
    header text NOT NULL,
    description character varying(255) ,
    width character varying(255) NOT NULL DEFAULT '800',
    repeat smallint NOT NULL DEFAULT 1,
    footer text
);

CREATE TABLE mail_templates_bestanden (
    id serial NOT NULL,
    template_id integer NOT NULL DEFAULT 0,
    name character varying(255),
    temp_id integer NOT NULL DEFAULT 0,
    dat character varying(255),
    "type" character varying(255) DEFAULT 'application/octet-stream',
    size character varying(255) DEFAULT '0',
    position character(1) NOT NULL DEFAULT ' '
);

CREATE TABLE mail_tracking (
    id serial NOT NULL,
    mail_id integer,
    email character varying(255),
    timestamp_first integer,
    timestamp_last integer,
    "count" integer,
    mail_id_2 integer,
    clients text,
    agents text,
    mailcode character varying(255),
    hyperlinks text
);

CREATE TABLE medewerker (
    id serial NOT NULL,
    user_id integer NOT NULL DEFAULT 0,
    social_security_nr character varying(255),
    timestamp_started integer,
    timestamp_birthday integer,
    gender smallint NOT NULL DEFAULT 0,
    contract_type text,
    "function" character varying(255),
    functionlevel character varying(255),
    contract_hours integer,
    contract_holidayhours integer,
    timestamp_stop integer,
    evaluation text
);

CREATE TABLE meta_table (
    id serial NOT NULL,
    tablename character varying(255),
    fieldname character varying(255),
    fieldtype integer,
    fieldorder integer,
    record_id integer,
    "value" text
);

CREATE TABLE notitie (
    id serial NOT NULL,
    "timestamp" integer,
    subject character varying(255),
    body text,
    sender integer,
    is_read smallint,
    user_id integer,
    is_done smallint,
    delstatus smallint NOT NULL DEFAULT 0,
    project_id smallint,
    address_id integer,
    is_support smallint DEFAULT 0,
    extra_receipients character varying(255)
);

CREATE TABLE offertes (
    id serial NOT NULL,
    address_id integer,
    titel character varying(255),
    status integer,
    uitvoerder character varying(255),
    producten_id_0 character varying(255),
    producten_id_1 character varying(255),
    producten_id_2 character varying(255),
    producten_id_3 character varying(255),
    html_0 text,
    html_1 text,
    html_2 text,
    html_3 text,
    datum_0 character varying(255),
    datum_1 character varying(255),
    datum_2 character varying(255),
    datum_3 character varying(255),
    bedrijfsnaam character varying(255),
    prec_betaald_0 integer,
    prec_betaald_1 integer,
    prec_betaald_2 integer,
    prec_betaald_3 integer,
    factuur_nr_0 integer,
    factuur_nr_1 integer,
    factuur_nr_2 integer,
    factuur_nr_3 integer,
    btw_tonen smallint,
    btw_prec numeric,
    factuur_nr integer,
    datum character varying(255),
    definitief_2 smallint NOT NULL DEFAULT 0,
    definitief_3 smallint NOT NULL DEFAULT 0
);

CREATE TABLE omzet_akties (
    id serial NOT NULL,
    address_id integer,
    omschrijving text,
    datum integer,
    datum_betaald integer,
    rekeningflow numeric(16,2),
    rekeningflow_btw numeric(16,2),
    rekeningflow_ex numeric(16,2),
    factuur_nr integer,
    grootboeknummer_id integer,
    bedrag_betaald numeric(16,2) NOT NULL
);

CREATE TABLE omzet_totaal (
    id serial NOT NULL,
    address_id integer NOT NULL DEFAULT 0,
    totaal_flow integer,
    totaal_flow_btw numeric(16,2),
    totaal_flow_ex numeric(16,2),
    totaal_flow_12 integer
);

CREATE TABLE overige_posten (
    id serial NOT NULL,
    grootboek_id integer NOT NULL DEFAULT 0,
    omschrijving text NOT NULL,
    debiteur integer NOT NULL DEFAULT 0,
    datum integer NOT NULL DEFAULT 0,
    bedrag numeric(16,2) NOT NULL,
    tegenrekening integer NOT NULL DEFAULT 59
);

CREATE TABLE poll_antwoord (
    id serial NOT NULL,
    poll_id integer,
    user_id integer,
    answer smallint
);

CREATE TABLE poll_vraag (
    id serial NOT NULL,
    question text,
    is_active smallint
);

CREATE TABLE prefs (
    id serial NOT NULL,
    user_id integer,
    bgcolor character varying(255),
    style smallint,
    mail_server character varying(255),
    mail_user_id character varying(255),
    mail_wachtwoord character varying(255),
    mail_email character varying(255),
    mail_html smallint,
    mail_handtekening text
);

CREATE TABLE prikbord (
    id serial NOT NULL,
    subject character varying(255),
    body text,
    is_popup smallint,
    is_active smallint
);

CREATE TABLE producten (
    id serial NOT NULL,
    titel character varying(255),
    html text,
    prijsperjaar smallint,
    categorie character varying(255),
    grootboeknummer_id integer,
    address_id integer,
    prijs numeric(10,2),
    btw_prec numeric(10,2)
);

CREATE TABLE producten_in_offertes (
    id serial NOT NULL,
    producten_id integer,
    omschrijving text,
    link_id integer,
    aantal integer,
    btw numeric,
    prijs numeric(16,2)
);

CREATE TABLE project (
    id serial NOT NULL,
    name character varying(255),
    description text,
    manager integer,
    group_id smallint NOT NULL DEFAULT 0,
    is_active smallint,
    status smallint DEFAULT 0,
    address_id integer,
    lfact integer,
    budget integer,
    hours integer,
    address_businesscard_id integer
);

CREATE TABLE relatie_type (
    id serial NOT NULL,
    "type" character varying(255)
);

CREATE TABLE rssfeeds (
    id serial NOT NULL,
    "name" character varying(255),
    homepage character varying(255),
    url character varying(255),
    user_id integer,
    "count" integer DEFAULT 5
);

CREATE TABLE rssitems (
    id serial NOT NULL,
    feed integer,
    subject character varying(255),
    body text,
    link character varying(255),
    date integer
);

CREATE TABLE sales (
    id serial NOT NULL,
    subject character varying(255),
    expected_score integer,
    total_sum numeric(16,2),
    timestamp_proposal integer,
    timestamp_order integer,
    timestamp_invoice integer,
    address_id integer,
    description text,
    is_active integer,
    timestamp_prospect integer,
    user_id_modified integer,
    user_sales_id integer,
    user_id_create integer
);

CREATE TABLE snack_bestel (
    id serial NOT NULL,
    ammount smallint
);

CREATE TABLE snack_lijst (
    id serial NOT NULL,
    name character varying(255)
);

CREATE TABLE soortbedrijf (
    id serial NOT NULL,
    omschrijving character varying(255)
);

CREATE TABLE "statistics" (
    "table" character varying(255),
    updates integer,
    "vacuum" integer
);

CREATE TABLE status_conn (
    user_id integer NOT NULL DEFAULT 0,
    timestamp integer NOT NULL DEFAULT 0
);

CREATE TABLE status_list (
    id serial NOT NULL,
    msg_id character varying(255) DEFAULT '0' NOT NULL,
    mail_id integer NOT NULL DEFAULT 0,
    timestamp integer NOT NULL DEFAULT 0,
    user_id integer NOT NULL DEFAULT 0,
    mark_delete smallint NOT NULL DEFAULT 0
);

CREATE TABLE support (
    id serial NOT NULL,
    timestamp integer,
    body text,
    "type" integer,
    relation_name text,
    email text,
    reference_nr integer,
    customer_id integer
);

CREATE TABLE talen (
    id serial NOT NULL,
    nl text,
    de text,
    en text,
    es text,
    it text
);

CREATE TABLE talen_backup (
    id serial NOT NULL,
    nl text,
    de text,
    en text,
    es text,
    it text
);

CREATE TABLE teksten (
    id serial NOT NULL,
    html text,
    description character varying(255),
    "type" integer
);

CREATE TABLE templates (
    id serial NOT NULL,
    address_businesscard_id smallint NOT NULL DEFAULT 0,
    font character varying(255) ,
    fontsize integer NOT NULL DEFAULT 0,
    body text,
    footer text,
    sender text,
    address_id integer NOT NULL DEFAULT 0,
    description character varying(255),
    classification character varying(255),
    ids text,
    header character varying(255),
    user_id integer NOT NULL DEFAULT 0,
    settings_id integer NOT NULL DEFAULT 0,
    "date" character varying(255),
    city character varying(255),
    negative_classification character varying(255),
    multirel character varying(255),
    save_date timestamp without time zone,
    and_or character varying(255) DEFAULT 'AND',
    fax_nr smallint,
    signature smallint
);

CREATE TABLE templates_instellingen (
    id serial NOT NULL,
    page_left numeric(16,2) NOT NULL DEFAULT 0,
    page_top numeric(16,2) NOT NULL DEFAULT 0,
    page_right numeric(16,2) NOT NULL DEFAULT 0,
    address_left numeric(16,2) NOT NULL DEFAULT 0,
    address_width numeric(16,2) NOT NULL DEFAULT 0,
    address_top numeric(16,2) NOT NULL DEFAULT 0,
    address_position smallint NOT NULL DEFAULT 0,
    description character varying(255)
);

CREATE TABLE todo (
    id serial NOT NULL,
    timestamp integer NOT NULL DEFAULT 0,
    user_id integer NOT NULL DEFAULT 0,
    is_done smallint DEFAULT 0,
    subject character varying(255),
    body text,
    address_id integer,
    timestamp_end integer,
    project_id integer,
    is_alert smallint DEFAULT 0,
    is_customercontact integer,
    sync_guid integer,
    sync_hash character varying(255)
);

CREATE TABLE todo_sync (
    id serial NOT NULL,
    user_id integer,
    sync_guid integer,
    "action" character varying(255)
);

CREATE TABLE uren_activ (
    id serial NOT NULL,
    activity character varying(255),
    tarif numeric(16,2)
);

CREATE TABLE urenreg (
    id serial NOT NULL,
    user_id integer NOT NULL DEFAULT 0,
    project_id integer NOT NULL DEFAULT 0,
    timestamp_start integer,
    timestamp_end integer,
    activity_id character varying(255),
    description text,
    is_billable smallint DEFAULT 0,
    "type" smallint DEFAULT 0
);
