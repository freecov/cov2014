--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = true;

--
-- Name: active_calls; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE active_calls (
    bedrijfsnaam character varying(255),
    bedrijfsid integer,
    tijd integer
);


ALTER TABLE public.active_calls OWNER TO covide;

--
-- Name: adres; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE adres (
    id serial NOT NULL,
    achternaam character varying(40),
    voornaam character varying(40),
    bedrijfsnaam character varying(60),
    adres character varying(60),
    postcode character varying(8),
    plaats character varying(30),
    telnr character varying(15),
    faxnr character varying(15),
    email character varying(40),
    gebruiker integer DEFAULT 0,
    bedrijf smallint,
    link integer,
    publiek smallint,
    mobielnr character varying(15),
    debiteur_nr integer,
    land character varying(40),
    soortbedrijf smallint,
    "comment" text,
    website character varying(250),
    relatie_type smallint,
    tav character varying(100),
    contactpersoon character varying(100),
    klant smallint,
    leverancier smallint,
    partner smallint,
    prospect smallint,
    overig smallint,
    letop character varying(255),
    postbus character varying(8),
    postcodepostbus character varying(8),
    plaatspostbus character varying(100),
    classificatie character varying(100),
    accmanager integer DEFAULT 0,
    actief smallint DEFAULT (1)::smallint,
    rbriefkop smallint DEFAULT (2)::smallint,
    raanhef smallint DEFAULT (2)::smallint,
    rletter character varying(200),
    rvoornaam character varying(200),
    rtussen character varying(200),
    rachternaam character varying(200),
    e4lid character varying(255) DEFAULT ''::character varying,
    titel integer DEFAULT 0,
    relname character varying(255) DEFAULT ''::character varying,
    relpass character varying(255) DEFAULT ''::character varying,
    modified integer DEFAULT 0,
    sync_modified integer
);


ALTER TABLE public.adres OWNER TO covide;

--
-- Name: adres_multivers; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE adres_multivers (
    id serial NOT NULL,
    achternaam character varying(40),
    voornaam character varying(40),
    bedrijfsnaam character varying(60),
    adres character varying(60),
    postcode character varying(8),
    plaats character varying(30),
    telnr character varying(15),
    faxnr character varying(15),
    email character varying(40),
    gebruiker integer DEFAULT 0,
    bedrijf smallint,
    link integer,
    publiek smallint,
    mobielnr character varying(15),
    debiteur_nr integer,
    land character varying(40),
    soortbedrijf smallint,
    "comment" text,
    website character varying(250),
    relatie_type smallint,
    tav character varying(100),
    contactpersoon character varying(100),
    klant smallint,
    leverancier smallint,
    partner smallint,
    prospect smallint,
    overig smallint,
    letop character varying(255),
    postbus character varying(8),
    postcodepostbus character varying(8),
    plaatspostbus character varying(100),
    classificatie character varying(100),
    accmanager integer DEFAULT 0,
    actief smallint DEFAULT (1)::smallint,
    rbriefkop smallint DEFAULT (2)::smallint,
    raanhef smallint DEFAULT (2)::smallint,
    rletter character varying(200),
    rvoornaam character varying(200),
    rtussen character varying(200),
    rachternaam character varying(200)
);


ALTER TABLE public.adres_multivers OWNER TO covide;

--
-- Name: adres_overig; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE adres_overig (
    id serial NOT NULL,
    bedrijfsnaam character varying(255),
    achternaam character varying(40),
    voornaam character varying(40),
    adres character varying(60),
    postcode character varying(8),
    plaats character varying(30),
    telnr character varying(15),
    faxnr character varying(15),
    email character varying(40),
    gebruiker integer DEFAULT 0,
    bedrijf smallint,
    publiek smallint,
    mobielnr character varying(15),
    "comment" text,
    website character varying(250),
    postbus character varying(8),
    postcodepostbus character varying(8),
    plaatspostbus character varying(100),
    actief smallint DEFAULT (1)::smallint,
    bedrijfspand smallint DEFAULT (0)::smallint,
    arbo_kantoor smallint DEFAULT (0)::smallint,
    arbo_bedrijf integer DEFAULT 0,
    arbo_code character varying(100),
    arbo_team character varying(100),
    sync_modified integer
);


ALTER TABLE public.adres_overig OWNER TO covide;

--
-- Name: adres_personen; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE adres_personen (
    id serial NOT NULL,
    achternaam character varying(40),
    voornaam character varying(40),
    adres character varying(60),
    postcode character varying(8),
    plaats character varying(30),
    telnr character varying(15),
    faxnr character varying(15),
    email character varying(40),
    gebruiker integer DEFAULT 0,
    bedrijf smallint,
    publiek smallint,
    mobielnr character varying(15),
    "comment" text,
    website character varying(250),
    postbus character varying(8),
    postcodepostbus character varying(8),
    plaatspostbus character varying(100),
    actief smallint DEFAULT (1)::smallint,
    land character varying(255),
    e4lid character varying(255),
    modified integer DEFAULT 0,
    sync_modified integer
);


ALTER TABLE public.adres_personen OWNER TO covide;

--
-- Name: adres_sync; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE adres_sync (
    id serial NOT NULL,
    adres_id integer,
    adres_table character varying(50),
    private integer,
    acc_manager integer,
    sync_modified serial NOT NULL,
    sync_hash character varying(255),
    parent_id integer
);


ALTER TABLE public.adres_sync OWNER TO covide;

--
-- Name: adres_sync_guid; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE adres_sync_guid (
    id serial NOT NULL,
    sync_id integer,
    sync_guid integer,
    gebruiker integer
);


ALTER TABLE public.adres_sync_guid OWNER TO covide;

--
-- Name: adres_sync_records; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE adres_sync_records (
    id serial NOT NULL,
    adres_table character varying(50),
    adres_id integer,
    gebruiker_id integer
);


ALTER TABLE public.adres_sync_records OWNER TO covide;

--
-- Name: adresinfo; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE adresinfo (
    id serial NOT NULL,
    bedrijfs_id integer DEFAULT 0 NOT NULL,
    "comment" text,
    classificatie character varying(255),
    letop character varying(255),
    photo character varying(255),
    provisie_perc numeric(8,2),
    md5 character varying(255)
);


ALTER TABLE public.adresinfo OWNER TO covide;

--
-- Name: agenda; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE agenda (
    id serial NOT NULL,
    "begin" integer,
    eind integer,
    omschrijving text,
    gebruiker integer DEFAULT 0,
    debiteur_nr integer DEFAULT 0,
    project integer DEFAULT 0,
    prive smallint,
    n_id integer DEFAULT 0 NOT NULL,
    imp smallint DEFAULT (0)::smallint,
    geaccordeerd smallint DEFAULT (0)::smallint,
    notifytime integer DEFAULT 0,
    notified smallint DEFAULT (0)::smallint,
    locatie character varying(255),
    "start" timestamp without time zone,
    "end" timestamp without time zone,
    groep integer,
    gpid integer,
    km integer,
    repeat integer,
    multirel character varying(255),
    repeat_type character(1),
    alert smallint DEFAULT (0)::smallint,
    vakantie smallint DEFAULT (0)::smallint,
    bverlof smallint DEFAULT (0)::smallint,
    ziek smallint DEFAULT (0)::smallint,
    e4l_id character varying(255) DEFAULT ''::character varying,
    dnd smallint DEFAULT (0)::smallint,
    deckm smallint DEFAULT (0)::smallint,
    modified integer DEFAULT 0,
    modified_by integer,
    sync_guid integer,
    sync_hash character varying(255)
);


ALTER TABLE public.agenda OWNER TO covide;

--
-- Name: agenda_machtiging; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE agenda_machtiging (
    id serial NOT NULL,
    eigenaar integer DEFAULT 0,
    gemachtigde integer DEFAULT 0,
    rechten character varying(20) DEFAULT 'RO'::character varying
);


ALTER TABLE public.agenda_machtiging OWNER TO covide;

--
-- Name: agenda_sync; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE agenda_sync (
    id serial NOT NULL,
    gebruiker integer,
    sync_guid integer,
    "action" character varying(2)
);


ALTER TABLE public.agenda_sync OWNER TO covide;

--
-- Name: akties; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE akties (
    id serial NOT NULL,
    debiteur_nr integer,
    omschrijving text,
    datum integer,
    rekeningflow numeric(16,2),
    factuur_nr integer,
    rekeningflow_btw numeric(16,2),
    grootboeknummer_id integer
);


ALTER TABLE public.akties OWNER TO covide;

--
-- Name: arbo; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE arbo (
    id serial NOT NULL,
    regiokantoor character varying(100) DEFAULT ''::character varying NOT NULL,
    regiofax character varying(100) DEFAULT ''::character varying NOT NULL,
    regiotel character varying(100) DEFAULT ''::character varying NOT NULL,
    regioteam character varying(100) DEFAULT ''::character varying NOT NULL,
    werkgever character varying(100) DEFAULT ''::character varying NOT NULL,
    adres character varying(100) DEFAULT ''::character varying NOT NULL,
    postcode character varying(100) DEFAULT ''::character varying NOT NULL,
    plaats character varying(100) DEFAULT ''::character varying NOT NULL,
    aansluitcode character varying(100) DEFAULT ''::character varying NOT NULL,
    contactpers character varying(100) DEFAULT ''::character varying NOT NULL,
    tel character varying(100) DEFAULT ''::character varying NOT NULL,
    fax character varying(100) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE public.arbo OWNER TO covide;

--
-- Name: arbo_verslag; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE arbo_verslag (
    id serial NOT NULL,
    gebruiker integer,
    manager integer,
    soort integer,
    datum integer,
    omschrijving text,
    acties text,
    betrokkenen text,
    datum_invoer integer,
    ziekmelding integer
);


ALTER TABLE public.arbo_verslag OWNER TO covide;

--
-- Name: arbo_ziek; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

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
    zwanger smallint DEFAULT (0)::smallint,
    zwanger_ziek smallint DEFAULT (0)::smallint,
    orgaandonatie smallint DEFAULT (0)::smallint,
    ongeval smallint DEFAULT (0)::smallint,
    ontvangt_wao smallint DEFAULT (0)::smallint,
    wao_perc integer DEFAULT 0,
    herintr_wao smallint DEFAULT (0)::smallint,
    herintr_perc integer DEFAULT 0,
    bijzonderheden text,
    ziekmelding integer
);


ALTER TABLE public.arbo_ziek OWNER TO covide;

--
-- Name: bcards; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE bcards (
    id serial NOT NULL,
    bedrijfs_id integer DEFAULT 0 NOT NULL,
    voornaam character varying(25) DEFAULT ''::character varying NOT NULL,
    voorletters character varying(255),
    tussenvoegsel character varying(10) DEFAULT ''::character varying NOT NULL,
    achternaam character varying(50) DEFAULT ''::character varying NOT NULL,
    geboortedatum integer,
    mobielnr character varying(15),
    telefoonnr character varying(15),
    email character varying(100),
    memo text,
    raanhef smallint DEFAULT (3)::smallint,
    classificatie character varying(100),
    rbriefkop smallint DEFAULT (2)::smallint,
    zadres character varying(255),
    zpostcode character varying(25),
    zplaats character varying(255),
    zmobielnr character varying(25),
    ztelefoonnr character varying(25),
    zemail character varying(255),
    zfaxnr character varying(25),
    padres character varying(255),
    ppostcode character varying(25),
    pplaats character varying(255),
    pmobielnr character varying(25),
    ptelefoonnr character varying(25),
    pemail character varying(255),
    pfaxnr character varying(25),
    e4lid character varying(255) DEFAULT ''::character varying,
    titel integer DEFAULT 0,
    eigen character varying(255) DEFAULT ''::character varying,
    modified integer DEFAULT 0,
    photo character varying(255),
    sync_modified integer
);


ALTER TABLE public.bcards OWNER TO covide;

--
-- Name: bedrijfsclassifi; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE bedrijfsclassifi (
    id serial NOT NULL,
    omschr character varying(100),
    actief smallint DEFAULT (0)::smallint,
    locked smallint,
    subtype smallint
);


ALTER TABLE public.bedrijfsclassifi OWNER TO covide;

--
-- Name: begin_standen_finance; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE begin_standen_finance (
    id serial NOT NULL,
    grootboek_id integer DEFAULT 0 NOT NULL,
    stand numeric(16,2) DEFAULT 0.00 NOT NULL
);


ALTER TABLE public.begin_standen_finance OWNER TO covide;

--
-- Name: boekingen; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE boekingen (
    id serial NOT NULL,
    credit smallint DEFAULT (0)::smallint NOT NULL,
    factuur integer DEFAULT 0 NOT NULL,
    grootboek_id integer,
    status smallint,
    datum integer,
    koppel_id integer,
    bedrag numeric(16,2),
    product integer,
    inkoop smallint DEFAULT (0)::smallint NOT NULL,
    deb_nr integer DEFAULT 0 NOT NULL,
    betaald smallint DEFAULT (0)::smallint NOT NULL,
    locked smallint DEFAULT (0)::smallint NOT NULL
);


ALTER TABLE public.boekingen OWNER TO covide;

--
-- Name: boekingen_20012003; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE boekingen_20012003 (
    id serial NOT NULL,
    credit smallint DEFAULT (0)::smallint NOT NULL,
    factuur integer DEFAULT 0 NOT NULL,
    grootboek_id integer,
    status smallint,
    datum integer,
    koppel_id integer,
    bedrag numeric(16,2),
    product integer,
    inkoop smallint DEFAULT (0)::smallint NOT NULL,
    deb_nr integer DEFAULT 0 NOT NULL,
    betaald smallint DEFAULT (0)::smallint NOT NULL
);


ALTER TABLE public.boekingen_20012003 OWNER TO covide;

--
-- Name: bugs; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE bugs (
    id serial NOT NULL,
    gebruiker integer,
    module character varying(30),
    onderwerp character varying(255),
    omschrijving text
);


ALTER TABLE public.bugs OWNER TO covide;

--
-- Name: cdr; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE cdr (
    calldate timestamp with time zone DEFAULT now() NOT NULL,
    clid character varying(80) DEFAULT ''::character varying NOT NULL,
    src character varying(80) DEFAULT ''::character varying NOT NULL,
    dst character varying(80) DEFAULT ''::character varying NOT NULL,
    dcontext character varying(80) DEFAULT ''::character varying NOT NULL,
    channel character varying(80) DEFAULT ''::character varying NOT NULL,
    dstchannel character varying(80) DEFAULT ''::character varying NOT NULL,
    lastapp character varying(80) DEFAULT ''::character varying NOT NULL,
    lastdata character varying(80) DEFAULT ''::character varying NOT NULL,
    duration bigint DEFAULT 0::bigint NOT NULL,
    billsec bigint DEFAULT 0::bigint NOT NULL,
    disposition character varying(45) DEFAULT ''::character varying NOT NULL,
    amaflags bigint DEFAULT 0::bigint NOT NULL,
    accountcode character varying(20) DEFAULT ''::character varying NOT NULL,
    uniqueid character varying(32) DEFAULT ''::character varying NOT NULL,
    userfield character varying(255) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE public.cdr OWNER TO covide;

--
-- Name: chat_rooms; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE chat_rooms (
    id serial NOT NULL,
    naam character varying(100),
    users text,
    topic character varying(100),
    aktief smallint
);


ALTER TABLE public.chat_rooms OWNER TO covide;

--
-- Name: chat_text; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE chat_text (
    id serial NOT NULL,
    room integer,
    "user" integer,
    tekst character varying(255),
    date integer
);


ALTER TABLE public.chat_text OWNER TO covide;

--
-- Name: cms_bestanden; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE cms_bestanden (
    id serial NOT NULL,
    naam character varying(255),
    dat character varying(10),
    "type" character varying(255) DEFAULT 'application/octet-stream'::character varying,
    size character varying(255) DEFAULT '0'::character varying
);


ALTER TABLE public.cms_bestanden OWNER TO covide;

--
-- Name: cms_data; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE cms_data (
    id serial NOT NULL,
    gebruikersnaam integer DEFAULT 0 NOT NULL,
    parentpage integer DEFAULT 0 NOT NULL,
    paginatype integer DEFAULT 0 NOT NULL,
    paginatitel character varying(255) DEFAULT ''::character varying NOT NULL,
    datumpublicatie integer DEFAULT 0 NOT NULL,
    paginadata text NOT NULL,
    pageredirect character varying(255) DEFAULT '0'::character varying NOT NULL,
    ispublic smallint DEFAULT (0)::smallint NOT NULL,
    isactive smallint DEFAULT (0)::smallint NOT NULL,
    ismenuitem smallint DEFAULT (0)::smallint NOT NULL
);


ALTER TABLE public.cms_data OWNER TO covide;

--
-- Name: cms_images; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE cms_images (
    id serial NOT NULL,
    paginaid integer DEFAULT 0 NOT NULL,
    path character varying(255) DEFAULT ''::character varying NOT NULL,
    omschrijving character varying(255) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE public.cms_images OWNER TO covide;

--
-- Name: faq; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE faq (
    id serial NOT NULL,
    category integer,
    vraag text,
    antwoord text
);


ALTER TABLE public.faq OWNER TO covide;

--
-- Name: faq_cat; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE faq_cat (
    id serial NOT NULL,
    naam character varying(50)
);


ALTER TABLE public.faq_cat OWNER TO covide;

--
-- Name: faxes; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE faxes (
    id serial NOT NULL,
    date integer,
    sender character varying(100),
    receiver character varying(100),
    relation_id integer
);


ALTER TABLE public.faxes OWNER TO covide;

--
-- Name: filesys_bestanden; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE filesys_bestanden (
    id serial NOT NULL,
    naam character varying(255) DEFAULT ''::character varying NOT NULL,
    map_id integer DEFAULT 0 NOT NULL,
    project integer DEFAULT 0 NOT NULL,
    relatie integer DEFAULT 0 NOT NULL,
    datum integer DEFAULT 0 NOT NULL,
    user_id integer DEFAULT 0 NOT NULL,
    data character varying(10),
    "type" character varying(50) DEFAULT ''::character varying NOT NULL,
    size character varying(50) DEFAULT ''::character varying NOT NULL,
    omschrijving text
);


ALTER TABLE public.filesys_bestanden OWNER TO covide;

--
-- Name: filesys_mappen; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE filesys_mappen (
    id serial NOT NULL,
    naam character varying(255) DEFAULT ''::character varying NOT NULL,
    openbaar smallint DEFAULT (0)::smallint NOT NULL,
    relatie smallint DEFAULT (0)::smallint NOT NULL,
    relatie_id integer,
    user_id integer,
    hoofdmap integer,
    gedeeld character varying(255),
    omschrijving text,
    hrm_id integer,
    sticky smallint DEFAULT (0)::smallint,
    project_id integer DEFAULT 0
);


ALTER TABLE public.filesys_mappen OWNER TO covide;

--
-- Name: filesys_rechten; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE filesys_rechten (
    id serial NOT NULL,
    map_id integer,
    gebruiker_id character varying(255),
    rechten character varying(255)
);


ALTER TABLE public.filesys_rechten OWNER TO covide;

--
-- Name: forum; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE forum (
    id serial NOT NULL,
    ref integer DEFAULT 0,
    gebruiker integer DEFAULT 0 NOT NULL,
    project integer DEFAULT 0 NOT NULL,
    datum integer,
    onderwerp character varying(40),
    inhoud text,
    gelezen text
);


ALTER TABLE public.forum OWNER TO covide;

--
-- Name: functies; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE functies (
    id serial NOT NULL,
    naam character varying(100),
    omschr text
);


ALTER TABLE public.functies OWNER TO covide;

--
-- Name: gebruikers; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE gebruikers (
    id serial NOT NULL,
    naam character varying(20),
    wachtwoord character varying(32),
    persnr integer,
    xs_usermanage smallint DEFAULT (0)::smallint,
    xs_adresmanage smallint,
    xs_projectmanage smallint,
    xs_forummanage smallint,
    adres_id integer,
    xs_pollmanage smallint,
    xs_faqmanage smallint,
    xs_klachtmanage smallint,
    xs_chatmanage smallint,
    xs_omzetmanage smallint,
    xs_notemanage smallint,
    xs_todomanage smallint DEFAULT (0)::smallint,
    "comment" text,
    actief smallint DEFAULT (1)::smallint NOT NULL,
    style smallint,
    mail_server character varying(255),
    mail_gebruiker character varying(255),
    mail_wachtwoord character varying(255),
    mail_email character varying(255),
    mail_email1 character varying(255),
    mail_html smallint,
    mail_handtekening text,
    mail_toonaantal smallint DEFAULT (0)::smallint,
    mail_deltime integer DEFAULT 0,
    days smallint DEFAULT (0)::smallint,
    htmleditor smallint DEFAULT (2)::smallint NOT NULL,
    adresaccmanage character varying(255),
    agendasel character varying(255),
    toonhelp integer DEFAULT 0 NOT NULL,
    toonpopup smallint DEFAULT (1)::smallint,
    xs_salarismanage smallint DEFAULT (0)::smallint,
    server_deltime integer DEFAULT 1,
    xs_bedrijfsinfomanage smallint DEFAULT (0)::smallint,
    xs_hrmmanage smallint DEFAULT (0)::smallint,
    taal character(2) DEFAULT 'NL'::bpchar NOT NULL,
    werkgever integer DEFAULT 0,
    a_uitl smallint DEFAULT (0)::smallint,
    view_textmail_only smallint,
    e4l_update integer DEFAULT 0,
    dayquote integer,
    infowin_altmethod integer,
    xs_e4l smallint DEFAULT (0)::smallint,
    xs_filemanage integer,
    xs_limitusermanage integer,
    change_theme integer,
    xs_relatiemanage integer,
    xs_nieuwsbriefmanage integer,
    renderstatus text,
    mail_forward integer,
    toonvoip smallint DEFAULT (0)::smallint,
    handtekening character varying(255),
    voip_device character varying(255) DEFAULT ''::character varying,
    xs_salesmanage smallint,
    e4l_username character varying(100),
    e4l_password character varying(100),
    xs_arbo integer,
    xs_arbo_validated integer,
    eigen_aantekeningen_alt integer,
    agendamode integer DEFAULT 1,
    rssnews integer,
    mail_showbcc integer,
    mail_imap integer,
    xs_hypo smallint,
    mail_num_items integer,
    sync4j_source character varying(255),
    sync4j_path character varying(255),
    sync4j_source_adres character varying(255),
    sync4j_source_todo character varying(255)
);


ALTER TABLE public.gebruikers OWNER TO covide;

--
-- Name: gebruikers_log; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE gebruikers_log (
    id serial NOT NULL,
    manager integer,
    user_id integer,
    datum integer,
    change text
);


ALTER TABLE public.gebruikers_log OWNER TO covide;

--
-- Name: groep; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE groep (
    id serial NOT NULL,
    gnaam character varying(50),
    descr character varying(255),
    members character varying(50),
    manager integer DEFAULT 0
);


ALTER TABLE public.groep OWNER TO covide;

--
-- Name: grootboeknummers; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE grootboeknummers (
    id serial NOT NULL,
    nr integer,
    titel character varying(255),
    debiteur smallint
);


ALTER TABLE public.grootboeknummers OWNER TO covide;

--
-- Name: hoofd_project; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE hoofd_project (
    id serial NOT NULL,
    naam character varying(30),
    omschrijving text,
    beheerder integer DEFAULT 0,
    actief smallint,
    status smallint DEFAULT (0)::smallint,
    debiteur integer,
    bcards integer
);


ALTER TABLE public.hoofd_project OWNER TO covide;

--
-- Name: hypotheek; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE hypotheek (
    id serial NOT NULL,
    datum integer,
    bedrag numeric(16,2),
    geldverstrekker integer,
    verzekeraar integer,
    jaarpremie integer,
    user_id integer,
    user_src integer,
    titel character varying(255),
    descr text,
    soort integer,
    klant_id integer,
    actief smallint
);


ALTER TABLE public.hypotheek OWNER TO covide;

--
-- Name: inkopen; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE inkopen (
    id serial NOT NULL,
    datum integer DEFAULT 0 NOT NULL,
    balanspost integer DEFAULT 0 NOT NULL,
    boekstuknr integer DEFAULT 1 NOT NULL,
    descr character varying(255),
    leverancier_nr integer DEFAULT 0 NOT NULL,
    bedrag_ex numeric(16,2),
    bedrag_inc numeric(16,2),
    bedrag_btw numeric(16,2),
    betaald numeric(16,2) DEFAULT 0.00 NOT NULL
);


ALTER TABLE public.inkopen OWNER TO covide;

--
-- Name: jaar_afsluitingen; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE jaar_afsluitingen (
    jaar integer DEFAULT 0 NOT NULL,
    datum_afgesloten integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.jaar_afsluitingen OWNER TO covide;

--
-- Name: klachten; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE klachten (
    id serial NOT NULL,
    tijd integer,
    klacht text,
    afhandeling text,
    project integer DEFAULT 0,
    registrant integer DEFAULT 0,
    rcpt integer DEFAULT 0,
    "prior" smallint DEFAULT (0)::smallint,
    afgehandeld smallint,
    debiteur_nr integer,
    email text,
    referentie_nr integer
);


ALTER TABLE public.klachten OWNER TO covide;

--
-- Name: klanten; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE klanten (
    id serial NOT NULL,
    naam character varying(255),
    adres character varying(255),
    postcode character varying(8),
    plaats character varying(255),
    land character varying(255),
    telefoonnummer character varying(64),
    faxnummer character varying(64),
    email character varying(255),
    soortbedrijf_id integer,
    aantalwerknemers integer,
    debiteur_nr integer,
    contactpersoon character varying(255),
    contactpersoon_voorletters character varying(255),
    totaal_flow integer,
    totaal_flow_12 integer
);


ALTER TABLE public.klanten OWNER TO covide;

--
-- Name: license; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE license (
    bedrijfsnaam character varying(200) DEFAULT ''::character varying NOT NULL,
    code character varying(20) DEFAULT ''::character varying NOT NULL,
    datum integer DEFAULT 0 NOT NULL,
    project smallint,
    faq smallint,
    forum smallint,
    klachten smallint,
    chat smallint,
    prikbord smallint,
    enquete smallint,
    emagazine smallint,
    factuur smallint,
    poort smallint,
    snack smallint,
    email character varying(255),
    snelstart smallint,
    plain smallint,
    latest_version character varying(200) DEFAULT '0'::character varying,
    multivers smallint,
    multivers_path character varying(255),
    mail_interval integer DEFAULT 0,
    salaris smallint DEFAULT (1)::smallint,
    multivers_update integer,
    hrm smallint,
    exact smallint DEFAULT (0)::smallint,
    finance_start_date integer,
    max_upload_size character varying(150) DEFAULT '24M'::character varying,
    e4l smallint DEFAULT (0)::smallint,
    dayquote integer,
    dayquote_nr integer,
    mail_shell smallint DEFAULT (0)::smallint,
    voip smallint DEFAULT (0)::smallint,
    sales smallint,
    filesyspath character varying(255) DEFAULT ''::character varying,
    arbo integer,
    disable_basics integer,
    privoxy_config integer,
    hypo smallint,
    sync4j integer
);


ALTER TABLE public.license OWNER TO covide;

--
-- Name: login_log; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE login_log (
    id serial NOT NULL,
    user_id integer DEFAULT 0 NOT NULL,
    ip character varying(50),
    "time" integer DEFAULT 0 NOT NULL,
    dag integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.login_log OWNER TO covide;

--
-- Name: mail_attachments; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE mail_attachments (
    id serial NOT NULL,
    bericht_id integer DEFAULT 0 NOT NULL,
    naam character varying(255),
    temp_id integer DEFAULT 0 NOT NULL,
    dat character varying(10),
    "type" character varying(255) DEFAULT 'application/octet-stream'::character varying,
    size character varying(255) DEFAULT '0'::character varying,
    cid character varying(255)
);


ALTER TABLE public.mail_attachments OWNER TO covide;

--
-- Name: mail_berichten; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE mail_berichten (
    id serial NOT NULL,
    message_id character varying(255),
    map_id integer DEFAULT 0 NOT NULL,
    gebruiker_id integer DEFAULT 0 NOT NULL,
    debiteur_nr integer DEFAULT 0 NOT NULL,
    project_nr integer,
    van character varying(255),
    onderwerp text,
    header text,
    body text,
    datum character varying(255),
    ishtml smallint,
    publiek smallint,
    van_email character varying(255),
    aan text,
    cc text,
    omschr character varying(255),
    nieuw smallint DEFAULT (0)::smallint NOT NULL,
    replyto character varying(255),
    status_pop smallint DEFAULT (0)::smallint NOT NULL,
    bcc text,
    datum_ontvangen integer DEFAULT 0 NOT NULL,
    template_id integer DEFAULT 0 NOT NULL,
    askwichrel smallint DEFAULT (0)::smallint,
    indexed integer
);


ALTER TABLE public.mail_berichten OWNER TO covide;

--
-- Name: mail_filters; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE mail_filters (
    id serial NOT NULL,
    gebruiker_id integer DEFAULT 0 NOT NULL,
    afzender character varying(255),
    ontvanger character varying(255),
    onderwerp character varying(255),
    naar_mapid integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.mail_filters OWNER TO covide;

--
-- Name: mail_mappen; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE mail_mappen (
    id serial NOT NULL,
    naam character varying(255),
    gebruiker_id integer,
    parentmap_id integer
);


ALTER TABLE public.mail_mappen OWNER TO covide;

--
-- Name: mail_signatures; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE mail_signatures (
    id serial NOT NULL,
    gebruiker_id integer,
    email character varying,
    signature text,
    title character varying
);


ALTER TABLE public.mail_signatures OWNER TO covide;

--
-- Name: mail_templates; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE mail_templates (
    id serial NOT NULL,
    koptekst text NOT NULL,
    omschrijving character varying(255) DEFAULT ''::character varying NOT NULL,
    width character varying(255) DEFAULT '800'::character varying NOT NULL,
    repeat smallint DEFAULT (1)::smallint NOT NULL,
    voettekst text
);


ALTER TABLE public.mail_templates OWNER TO covide;

--
-- Name: mail_templates_bestanden; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE mail_templates_bestanden (
    id serial NOT NULL,
    template_id integer DEFAULT 0 NOT NULL,
    naam character varying(255),
    temp_id integer DEFAULT 0 NOT NULL,
    dat character varying(10),
    "type" character varying(255) DEFAULT 'application/octet-stream'::character varying,
    size character varying(255) DEFAULT '0'::character varying,
    pos character(1) DEFAULT ''::bpchar NOT NULL
);


ALTER TABLE public.mail_templates_bestanden OWNER TO covide;

--
-- Name: mail_tracking; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE mail_tracking (
    id serial NOT NULL,
    mail_id integer,
    email character varying(255),
    datum_first integer,
    datum_last integer,
    count integer,
    mail_id_2 integer,
    clients text,
    agents text,
    mailcode character varying(255),
    hyperlinks text
);


ALTER TABLE public.mail_tracking OWNER TO covide;

--
-- Name: medewerker; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE medewerker (
    id serial NOT NULL,
    user_id integer DEFAULT 0 NOT NULL,
    sofinr character varying(255),
    datum_start integer,
    geboortedatum integer,
    geslacht smallint DEFAULT (0)::smallint NOT NULL,
    dienstverband text,
    functie character varying(100),
    functieniveau character varying(255),
    cont_uren integer,
    cont_vak integer,
    datum_stop integer,
    eindevaluatie text
);


ALTER TABLE public.medewerker OWNER TO covide;

--
-- Name: meta_table; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE meta_table (
    id serial NOT NULL,
    tablename character varying(255),
    fieldname character varying(255),
    fieldtype integer,
    fieldorder integer,
    record_id integer,
    value text
);


ALTER TABLE public.meta_table OWNER TO covide;

--
-- Name: notitie; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE notitie (
    id serial NOT NULL,
    datum integer,
    onderwerp character varying(30),
    inhoud text,
    schrijver integer DEFAULT 0,
    gelezen smallint,
    ontvanger integer DEFAULT 0,
    gedaan smallint,
    delstatus smallint DEFAULT (0)::smallint NOT NULL,
    project smallint,
    debiteur_nr integer,
    support smallint DEFAULT (0)::smallint,
    extra_ont character varying(255)
);


ALTER TABLE public.notitie OWNER TO covide;

--
-- Name: offertes; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE offertes (
    id serial NOT NULL,
    debiteur_nr integer,
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
    datum_0 character varying(8),
    datum_1 character varying(8),
    datum_2 character varying(8),
    datum_3 character varying(8),
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
    datum character varying(8),
    definitief_2 smallint DEFAULT (0)::smallint NOT NULL,
    definitief_3 smallint DEFAULT (0)::smallint NOT NULL
);


ALTER TABLE public.offertes OWNER TO covide;

--
-- Name: omzet_akties; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE omzet_akties (
    id serial NOT NULL,
    debiteur_nr integer,
    omschrijving text,
    datum integer,
    datum_betaald integer,
    rekeningflow numeric(16,2),
    rekeningflow_btw numeric(16,2),
    rekeningflow_ex numeric(16,2),
    factuur_nr integer,
    grootboeknummer_id integer,
    bedrag_betaald numeric(16,2) DEFAULT 0.00 NOT NULL
);


ALTER TABLE public.omzet_akties OWNER TO covide;

--
-- Name: omzet_totaal; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE omzet_totaal (
    id serial NOT NULL,
    debiteur_nr integer DEFAULT 0 NOT NULL,
    totaal_flow integer,
    totaal_flow_btw numeric(16,2),
    totaal_flow_ex numeric(16,2),
    totaal_flow_12 integer
);


ALTER TABLE public.omzet_totaal OWNER TO covide;

--
-- Name: overige_posten; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE overige_posten (
    id serial NOT NULL,
    grootboek_id integer DEFAULT 0 NOT NULL,
    omschrijving text NOT NULL,
    debiteur integer DEFAULT 0 NOT NULL,
    datum integer DEFAULT 0 NOT NULL,
    bedrag numeric(16,2) DEFAULT 0.00 NOT NULL,
    tegenrekening integer DEFAULT 59 NOT NULL
);


ALTER TABLE public.overige_posten OWNER TO covide;

--
-- Name: poll_antwoord; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE poll_antwoord (
    id serial NOT NULL,
    vraag_id integer,
    user_id integer,
    antwoord smallint
);


ALTER TABLE public.poll_antwoord OWNER TO covide;

--
-- Name: poll_vraag; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE poll_vraag (
    id serial NOT NULL,
    vraag text,
    actief smallint
);


ALTER TABLE public.poll_vraag OWNER TO covide;

--
-- Name: prefs; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE prefs (
    id serial NOT NULL,
    gebruiker integer DEFAULT 0,
    bgcolor character varying(6),
    style smallint,
    mail_server character varying(255),
    mail_gebruiker character varying(128),
    mail_wachtwoord character varying(64),
    mail_email character varying(255),
    mail_html smallint,
    mail_handtekening text
);


ALTER TABLE public.prefs OWNER TO covide;

--
-- Name: prikbord; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE prikbord (
    id serial NOT NULL,
    titel character varying(40),
    tekst text,
    popup smallint,
    actief smallint
);


ALTER TABLE public.prikbord OWNER TO covide;

--
-- Name: producten; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE producten (
    id serial NOT NULL,
    titel character varying(255),
    html text,
    prijsperjaar smallint,
    categorie character varying(255),
    grootboeknummer_id integer,
    debiteur_nr integer,
    prijs numeric(10,2),
    btw_prec numeric(10,2)
);


ALTER TABLE public.producten OWNER TO covide;

--
-- Name: producten_in_offertes; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE producten_in_offertes (
    id serial NOT NULL,
    producten_id integer,
    omschrijving text,
    link_id integer,
    aantal integer,
    btw numeric,
    prijs numeric(16,2) DEFAULT 0.00
);


ALTER TABLE public.producten_in_offertes OWNER TO covide;

--
-- Name: project; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE project (
    id serial NOT NULL,
    naam character varying(30),
    omschrijving text,
    beheerder integer DEFAULT 0,
    groep smallint DEFAULT (0)::smallint NOT NULL,
    actief smallint,
    status smallint DEFAULT (0)::smallint,
    debiteur integer,
    lfact integer,
    budget integer,
    uren integer,
    bcards integer
);


ALTER TABLE public.project OWNER TO covide;

--
-- Name: relatie_type; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE relatie_type (
    id serial NOT NULL,
    "type" character varying(100)
);


ALTER TABLE public.relatie_type OWNER TO covide;

--
-- Name: rssfeeds; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE rssfeeds (
    id serial NOT NULL,
    name character varying(255),
    homepage character varying(255),
    url character varying(255),
    user_id integer DEFAULT 0,
    count integer DEFAULT 5
);


ALTER TABLE public.rssfeeds OWNER TO covide;

--
-- Name: rssitems; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE rssitems (
    id serial NOT NULL,
    feed integer,
    title character varying(255),
    body text,
    link character varying(255),
    date integer
);


ALTER TABLE public.rssitems OWNER TO covide;

--
-- Name: sales; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE sales (
    id serial NOT NULL,
    titel character varying(255),
    score_verwacht integer,
    totaalbedrag numeric(16,2),
    datum_offerte integer,
    datum_opdracht integer,
    datum_factuur integer,
    klant_id integer,
    descr text,
    actief integer,
    datum_prospect integer,
    user_id_modified integer,
    user_sales_id integer,
    user_id_create integer
);


ALTER TABLE public.sales OWNER TO covide;

--
-- Name: snack_bestel; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE snack_bestel (
    id serial NOT NULL,
    aantal smallint
);


ALTER TABLE public.snack_bestel OWNER TO covide;

--
-- Name: snack_lijst; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE snack_lijst (
    id serial NOT NULL,
    naam character varying(30)
);


ALTER TABLE public.snack_lijst OWNER TO covide;

--
-- Name: soortbedrijf; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE soortbedrijf (
    id serial NOT NULL,
    omschrijving character varying(255)
);


ALTER TABLE public.soortbedrijf OWNER TO covide;

--
-- Name: statistics; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE "statistics" (
    tabel character varying(255),
    updates integer,
    "vacuum" integer
);


ALTER TABLE public."statistics" OWNER TO covide;

--
-- Name: status_conn; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE status_conn (
    gebruiker_id integer DEFAULT 0 NOT NULL,
    datetime integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.status_conn OWNER TO covide;

--
-- Name: status_list; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE status_list (
    id serial NOT NULL,
    msg_id character varying(255) DEFAULT '0'::character varying NOT NULL,
    mail_id integer DEFAULT 0 NOT NULL,
    datum integer DEFAULT 0 NOT NULL,
    gebruiker_id integer DEFAULT 0 NOT NULL,
    mark_delete smallint DEFAULT (0)::smallint NOT NULL
);


ALTER TABLE public.status_list OWNER TO covide;

--
-- Name: support; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE support (
    id serial NOT NULL,
    tijd integer,
    opmerking text,
    "type" integer,
    klantnaam text,
    email text,
    referentie_nr integer,
    customer_id integer DEFAULT 0
);


ALTER TABLE public.support OWNER TO covide;

--
-- Name: talen; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE talen (
    id serial NOT NULL,
    nl text,
    de text,
    en text,
    es text,
    it text
);


ALTER TABLE public.talen OWNER TO covide;

--
-- Name: talen_backup; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE talen_backup (
    id serial NOT NULL,
    nl text,
    de text,
    en text,
    es text,
    it text
);


ALTER TABLE public.talen_backup OWNER TO covide;

--
-- Name: teksten; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE teksten (
    id serial NOT NULL,
    html text,
    omschrijving character varying(255),
    "type" integer
);


ALTER TABLE public.teksten OWNER TO covide;

--
-- Name: templates; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE templates (
    id serial NOT NULL,
    bcards smallint DEFAULT (0)::smallint NOT NULL,
    font character varying(50) DEFAULT ''::character varying NOT NULL,
    fontsize integer DEFAULT 0 NOT NULL,
    inhoud text,
    slotzin text,
    afzender text,
    relatie integer DEFAULT 0 NOT NULL,
    omschrijving character varying(255),
    classificaties character varying(255),
    ids text,
    betreft character varying(255),
    gebruiker integer DEFAULT 0 NOT NULL,
    instelling_id integer DEFAULT 0 NOT NULL,
    datum character varying(255),
    plaats character varying(255),
    classificaties_niet character varying(255),
    multirel character varying(255),
    save_date timestamp without time zone NOT NULL,
    and_or character varying(255) DEFAULT 'AND'::character varying,
    faxnr smallint,
    handtekening smallint
);


ALTER TABLE public.templates OWNER TO covide;

--
-- Name: templates_instellingen; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE templates_instellingen (
    id serial NOT NULL,
    page_left numeric(16,2) DEFAULT 0.00 NOT NULL,
    page_top numeric(16,2) DEFAULT 0.00 NOT NULL,
    page_right numeric(16,2) DEFAULT 0.00 NOT NULL,
    adres_left numeric(16,2) DEFAULT 0.00 NOT NULL,
    adres_width numeric(16,2) DEFAULT 0.00 NOT NULL,
    adres_top numeric(16,2) DEFAULT 0.00 NOT NULL,
    adres_positie smallint DEFAULT (0)::smallint NOT NULL,
    omschrijving character varying(255)
);


ALTER TABLE public.templates_instellingen OWNER TO covide;

--
-- Name: todo; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE todo (
    id serial NOT NULL,
    datum integer DEFAULT 0 NOT NULL,
    user_id integer DEFAULT 0 NOT NULL,
    acc smallint DEFAULT (0)::smallint,
    titel character varying(255),
    omschrijving text,
    relatie_id integer DEFAULT 0,
    eind integer,
    project_id integer,
    alert smallint DEFAULT (0)::smallint,
    klantcontact integer,
    sync_guid integer,
    sync_hash character varying(255)
);


ALTER TABLE public.todo OWNER TO covide;

--
-- Name: todo_sync; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE todo_sync (
    id serial NOT NULL,
    gebruiker integer,
    sync_guid integer,
    "action" character varying(2)
);


ALTER TABLE public.todo_sync OWNER TO covide;

--
-- Name: uren_activ; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE uren_activ (
    id serial NOT NULL,
    activiteit character varying(30),
    uurtarief numeric(16,2)
);


ALTER TABLE public.uren_activ OWNER TO covide;

--
-- Name: urenreg; Type: TABLE; Schema: public; Owner: covide; Tablespace: 
--

CREATE TABLE urenreg (
    id serial NOT NULL,
    gebruiker integer DEFAULT 0 NOT NULL,
    project integer DEFAULT 0 NOT NULL,
    tijd_begin integer,
    tijd_eind integer,
    activiteit character varying(30),
    omschrijving text,
    factureren smallint DEFAULT (0)::smallint,
    "type" smallint DEFAULT (0)::smallint
);


ALTER TABLE public.urenreg OWNER TO covide;

--
-- Name: adres_multivers_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY adres_multivers
    ADD CONSTRAINT adres_multivers_pkey PRIMARY KEY (id);


ALTER INDEX public.adres_multivers_pkey OWNER TO covide;

--
-- Name: adres_overig_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY adres_overig
    ADD CONSTRAINT adres_overig_pkey PRIMARY KEY (id);


ALTER INDEX public.adres_overig_pkey OWNER TO covide;

--
-- Name: adres_personen_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY adres_personen
    ADD CONSTRAINT adres_personen_pkey PRIMARY KEY (id);


ALTER INDEX public.adres_personen_pkey OWNER TO covide;

--
-- Name: adres_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY adres
    ADD CONSTRAINT adres_pkey PRIMARY KEY (id);


ALTER INDEX public.adres_pkey OWNER TO covide;

--
-- Name: adresinfo_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY adresinfo
    ADD CONSTRAINT adresinfo_pkey PRIMARY KEY (id);


ALTER INDEX public.adresinfo_pkey OWNER TO covide;

--
-- Name: agenda_machtiging_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY agenda_machtiging
    ADD CONSTRAINT agenda_machtiging_pkey PRIMARY KEY (id);


ALTER INDEX public.agenda_machtiging_pkey OWNER TO covide;

--
-- Name: agenda_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY agenda
    ADD CONSTRAINT agenda_pkey PRIMARY KEY (id);


ALTER INDEX public.agenda_pkey OWNER TO covide;

--
-- Name: akties_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY akties
    ADD CONSTRAINT akties_pkey PRIMARY KEY (id);


ALTER INDEX public.akties_pkey OWNER TO covide;

--
-- Name: arbo_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY arbo
    ADD CONSTRAINT arbo_pkey PRIMARY KEY (id);


ALTER INDEX public.arbo_pkey OWNER TO covide;

--
-- Name: arbo_ziek_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY arbo_ziek
    ADD CONSTRAINT arbo_ziek_pkey PRIMARY KEY (id);


ALTER INDEX public.arbo_ziek_pkey OWNER TO covide;

--
-- Name: bcards_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY bcards
    ADD CONSTRAINT bcards_pkey PRIMARY KEY (id);


ALTER INDEX public.bcards_pkey OWNER TO covide;

--
-- Name: bedrijfsclassifi_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY bedrijfsclassifi
    ADD CONSTRAINT bedrijfsclassifi_pkey PRIMARY KEY (id);


ALTER INDEX public.bedrijfsclassifi_pkey OWNER TO covide;

--
-- Name: begin_standen_finance_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY begin_standen_finance
    ADD CONSTRAINT begin_standen_finance_pkey PRIMARY KEY (id);


ALTER INDEX public.begin_standen_finance_pkey OWNER TO covide;

--
-- Name: boekingen_20012003_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY boekingen_20012003
    ADD CONSTRAINT boekingen_20012003_pkey PRIMARY KEY (id);


ALTER INDEX public.boekingen_20012003_pkey OWNER TO covide;

--
-- Name: boekingen_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY boekingen
    ADD CONSTRAINT boekingen_pkey PRIMARY KEY (id);


ALTER INDEX public.boekingen_pkey OWNER TO covide;

--
-- Name: bugs_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY bugs
    ADD CONSTRAINT bugs_pkey PRIMARY KEY (id);


ALTER INDEX public.bugs_pkey OWNER TO covide;

--
-- Name: chat_rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY chat_rooms
    ADD CONSTRAINT chat_rooms_pkey PRIMARY KEY (id);


ALTER INDEX public.chat_rooms_pkey OWNER TO covide;

--
-- Name: chat_text_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY chat_text
    ADD CONSTRAINT chat_text_pkey PRIMARY KEY (id);


ALTER INDEX public.chat_text_pkey OWNER TO covide;

--
-- Name: cms_bestanden_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY cms_bestanden
    ADD CONSTRAINT cms_bestanden_pkey PRIMARY KEY (id);


ALTER INDEX public.cms_bestanden_pkey OWNER TO covide;

--
-- Name: cms_data_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY cms_data
    ADD CONSTRAINT cms_data_pkey PRIMARY KEY (id);


ALTER INDEX public.cms_data_pkey OWNER TO covide;

--
-- Name: cms_images_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY cms_images
    ADD CONSTRAINT cms_images_pkey PRIMARY KEY (id);


ALTER INDEX public.cms_images_pkey OWNER TO covide;

--
-- Name: faq_cat_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY faq_cat
    ADD CONSTRAINT faq_cat_pkey PRIMARY KEY (id);


ALTER INDEX public.faq_cat_pkey OWNER TO covide;

--
-- Name: faq_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY faq
    ADD CONSTRAINT faq_pkey PRIMARY KEY (id);


ALTER INDEX public.faq_pkey OWNER TO covide;

--
-- Name: filesys_bestanden_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY filesys_bestanden
    ADD CONSTRAINT filesys_bestanden_pkey PRIMARY KEY (id);


ALTER INDEX public.filesys_bestanden_pkey OWNER TO covide;

--
-- Name: filesys_mappen_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY filesys_mappen
    ADD CONSTRAINT filesys_mappen_pkey PRIMARY KEY (id);


ALTER INDEX public.filesys_mappen_pkey OWNER TO covide;

--
-- Name: forum_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY forum
    ADD CONSTRAINT forum_pkey PRIMARY KEY (id);


ALTER INDEX public.forum_pkey OWNER TO covide;

--
-- Name: functies_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY functies
    ADD CONSTRAINT functies_pkey PRIMARY KEY (id);


ALTER INDEX public.functies_pkey OWNER TO covide;

--
-- Name: gebruikers_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY gebruikers
    ADD CONSTRAINT gebruikers_pkey PRIMARY KEY (id);


ALTER INDEX public.gebruikers_pkey OWNER TO covide;

--
-- Name: groep_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY groep
    ADD CONSTRAINT groep_pkey PRIMARY KEY (id);


ALTER INDEX public.groep_pkey OWNER TO covide;

--
-- Name: grootboeknummers_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY grootboeknummers
    ADD CONSTRAINT grootboeknummers_pkey PRIMARY KEY (id);


ALTER INDEX public.grootboeknummers_pkey OWNER TO covide;

--
-- Name: hoofd_project_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY hoofd_project
    ADD CONSTRAINT hoofd_project_pkey PRIMARY KEY (id);


ALTER INDEX public.hoofd_project_pkey OWNER TO covide;

--
-- Name: hypotheek_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY hypotheek
    ADD CONSTRAINT hypotheek_pkey PRIMARY KEY (id);


ALTER INDEX public.hypotheek_pkey OWNER TO covide;

--
-- Name: inkopen_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY inkopen
    ADD CONSTRAINT inkopen_pkey PRIMARY KEY (id);


ALTER INDEX public.inkopen_pkey OWNER TO covide;

--
-- Name: klachten_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY klachten
    ADD CONSTRAINT klachten_pkey PRIMARY KEY (id);


ALTER INDEX public.klachten_pkey OWNER TO covide;

--
-- Name: klanten_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY klanten
    ADD CONSTRAINT klanten_pkey PRIMARY KEY (id);


ALTER INDEX public.klanten_pkey OWNER TO covide;

--
-- Name: login_log_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY login_log
    ADD CONSTRAINT login_log_pkey PRIMARY KEY (id);


ALTER INDEX public.login_log_pkey OWNER TO covide;

--
-- Name: mail_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY mail_attachments
    ADD CONSTRAINT mail_attachments_pkey PRIMARY KEY (id);


ALTER INDEX public.mail_attachments_pkey OWNER TO covide;

--
-- Name: mail_berichten_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY mail_berichten
    ADD CONSTRAINT mail_berichten_pkey PRIMARY KEY (id);


ALTER INDEX public.mail_berichten_pkey OWNER TO covide;

--
-- Name: mail_filters_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY mail_filters
    ADD CONSTRAINT mail_filters_pkey PRIMARY KEY (id);


ALTER INDEX public.mail_filters_pkey OWNER TO covide;

--
-- Name: mail_mappen_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY mail_mappen
    ADD CONSTRAINT mail_mappen_pkey PRIMARY KEY (id);


ALTER INDEX public.mail_mappen_pkey OWNER TO covide;

--
-- Name: mail_templates_bestanden_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY mail_templates_bestanden
    ADD CONSTRAINT mail_templates_bestanden_pkey PRIMARY KEY (id);


ALTER INDEX public.mail_templates_bestanden_pkey OWNER TO covide;

--
-- Name: mail_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY mail_templates
    ADD CONSTRAINT mail_templates_pkey PRIMARY KEY (id);


ALTER INDEX public.mail_templates_pkey OWNER TO covide;

--
-- Name: medewerker_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY medewerker
    ADD CONSTRAINT medewerker_pkey PRIMARY KEY (id);


ALTER INDEX public.medewerker_pkey OWNER TO covide;

--
-- Name: meta_table_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY meta_table
    ADD CONSTRAINT meta_table_pkey PRIMARY KEY (id);


ALTER INDEX public.meta_table_pkey OWNER TO covide;

--
-- Name: notitie_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY notitie
    ADD CONSTRAINT notitie_pkey PRIMARY KEY (id);


ALTER INDEX public.notitie_pkey OWNER TO covide;

--
-- Name: offertes_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY offertes
    ADD CONSTRAINT offertes_pkey PRIMARY KEY (id);


ALTER INDEX public.offertes_pkey OWNER TO covide;

--
-- Name: omzet_akties_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY omzet_akties
    ADD CONSTRAINT omzet_akties_pkey PRIMARY KEY (id);


ALTER INDEX public.omzet_akties_pkey OWNER TO covide;

--
-- Name: omzet_totaal_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY omzet_totaal
    ADD CONSTRAINT omzet_totaal_pkey PRIMARY KEY (id);


ALTER INDEX public.omzet_totaal_pkey OWNER TO covide;

--
-- Name: overige_posten_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY overige_posten
    ADD CONSTRAINT overige_posten_pkey PRIMARY KEY (id);


ALTER INDEX public.overige_posten_pkey OWNER TO covide;

--
-- Name: poll_antwoord_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY poll_antwoord
    ADD CONSTRAINT poll_antwoord_pkey PRIMARY KEY (id);


ALTER INDEX public.poll_antwoord_pkey OWNER TO covide;

--
-- Name: poll_vraag_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY poll_vraag
    ADD CONSTRAINT poll_vraag_pkey PRIMARY KEY (id);


ALTER INDEX public.poll_vraag_pkey OWNER TO covide;

--
-- Name: prefs_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY prefs
    ADD CONSTRAINT prefs_pkey PRIMARY KEY (id);


ALTER INDEX public.prefs_pkey OWNER TO covide;

--
-- Name: prikbord_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY prikbord
    ADD CONSTRAINT prikbord_pkey PRIMARY KEY (id);


ALTER INDEX public.prikbord_pkey OWNER TO covide;

--
-- Name: producten_in_offertes_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY producten_in_offertes
    ADD CONSTRAINT producten_in_offertes_pkey PRIMARY KEY (id);


ALTER INDEX public.producten_in_offertes_pkey OWNER TO covide;

--
-- Name: producten_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY producten
    ADD CONSTRAINT producten_pkey PRIMARY KEY (id);


ALTER INDEX public.producten_pkey OWNER TO covide;

--
-- Name: project_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY project
    ADD CONSTRAINT project_pkey PRIMARY KEY (id);


ALTER INDEX public.project_pkey OWNER TO covide;

--
-- Name: relatie_type_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY relatie_type
    ADD CONSTRAINT relatie_type_pkey PRIMARY KEY (id);


ALTER INDEX public.relatie_type_pkey OWNER TO covide;

--
-- Name: snack_bestel_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY snack_bestel
    ADD CONSTRAINT snack_bestel_pkey PRIMARY KEY (id);


ALTER INDEX public.snack_bestel_pkey OWNER TO covide;

--
-- Name: snack_lijst_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY snack_lijst
    ADD CONSTRAINT snack_lijst_pkey PRIMARY KEY (id);


ALTER INDEX public.snack_lijst_pkey OWNER TO covide;

--
-- Name: soortbedrijf_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY soortbedrijf
    ADD CONSTRAINT soortbedrijf_pkey PRIMARY KEY (id);


ALTER INDEX public.soortbedrijf_pkey OWNER TO covide;

--
-- Name: status_list_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY status_list
    ADD CONSTRAINT status_list_pkey PRIMARY KEY (id);


ALTER INDEX public.status_list_pkey OWNER TO covide;

--
-- Name: support_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY support
    ADD CONSTRAINT support_pkey PRIMARY KEY (id);


ALTER INDEX public.support_pkey OWNER TO covide;

--
-- Name: talen_backup_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY talen_backup
    ADD CONSTRAINT talen_backup_pkey PRIMARY KEY (id);


ALTER INDEX public.talen_backup_pkey OWNER TO covide;

--
-- Name: talen_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY talen
    ADD CONSTRAINT talen_pkey PRIMARY KEY (id);


ALTER INDEX public.talen_pkey OWNER TO covide;

--
-- Name: teksten_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY teksten
    ADD CONSTRAINT teksten_pkey PRIMARY KEY (id);


ALTER INDEX public.teksten_pkey OWNER TO covide;

--
-- Name: templates_instellingen_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY templates_instellingen
    ADD CONSTRAINT templates_instellingen_pkey PRIMARY KEY (id);


ALTER INDEX public.templates_instellingen_pkey OWNER TO covide;

--
-- Name: templates_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY templates
    ADD CONSTRAINT templates_pkey PRIMARY KEY (id);


ALTER INDEX public.templates_pkey OWNER TO covide;

--
-- Name: todo_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY todo
    ADD CONSTRAINT todo_pkey PRIMARY KEY (id);


ALTER INDEX public.todo_pkey OWNER TO covide;

--
-- Name: uren_activ_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY uren_activ
    ADD CONSTRAINT uren_activ_pkey PRIMARY KEY (id);


ALTER INDEX public.uren_activ_pkey OWNER TO covide;

--
-- Name: urenreg_pkey; Type: CONSTRAINT; Schema: public; Owner: covide; Tablespace: 
--

ALTER TABLE ONLY urenreg
    ADD CONSTRAINT urenreg_pkey PRIMARY KEY (id);


ALTER INDEX public.urenreg_pkey OWNER TO covide;

--
-- Name: adres_debnr; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX adres_debnr ON adres USING btree (debiteur_nr) WHERE (debiteur_nr > 0);


ALTER INDEX public.adres_debnr OWNER TO covide;

--
-- Name: agenda_begin; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX agenda_begin ON agenda USING btree ("begin");


ALTER INDEX public.agenda_begin OWNER TO covide;

--
-- Name: agenda_debnr; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX agenda_debnr ON agenda USING btree (debiteur_nr);


ALTER INDEX public.agenda_debnr OWNER TO covide;

--
-- Name: agenda_eind; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX agenda_eind ON agenda USING btree (eind);


ALTER INDEX public.agenda_eind OWNER TO covide;

--
-- Name: agenda_end; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX agenda_end ON agenda USING btree ("end");


ALTER INDEX public.agenda_end OWNER TO covide;

--
-- Name: agenda_gebruiker; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX agenda_gebruiker ON agenda USING btree (gebruiker);

ALTER TABLE agenda CLUSTER ON agenda_gebruiker;


ALTER INDEX public.agenda_gebruiker OWNER TO covide;

--
-- Name: agenda_repeat; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX agenda_repeat ON agenda USING btree (repeat);


ALTER INDEX public.agenda_repeat OWNER TO covide;

--
-- Name: agenda_repeat_type; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX agenda_repeat_type ON agenda USING btree (repeat_type);


ALTER INDEX public.agenda_repeat_type OWNER TO covide;

--
-- Name: agenda_start; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX agenda_start ON agenda USING btree ("start");


ALTER INDEX public.agenda_start OWNER TO covide;

--
-- Name: bcards_bedrijfs_id; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX bcards_bedrijfs_id ON bcards USING btree (bedrijfs_id);


ALTER INDEX public.bcards_bedrijfs_id OWNER TO covide;

--
-- Name: boek_datum; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX boek_datum ON boekingen USING btree (datum);


ALTER INDEX public.boek_datum OWNER TO covide;

--
-- Name: boek_factuur; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX boek_factuur ON boekingen USING btree (factuur);


ALTER INDEX public.boek_factuur OWNER TO covide;

--
-- Name: boek_koppel; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX boek_koppel ON boekingen USING btree (koppel_id);


ALTER INDEX public.boek_koppel OWNER TO covide;

--
-- Name: boek_status; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX boek_status ON boekingen USING btree (status);


ALTER INDEX public.boek_status OWNER TO covide;

--
-- Name: fact_def_2; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fact_def_2 ON offertes USING btree (definitief_2);


ALTER INDEX public.fact_def_2 OWNER TO covide;

--
-- Name: fact_def_3; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fact_def_3 ON offertes USING btree (definitief_3);


ALTER INDEX public.fact_def_3 OWNER TO covide;

--
-- Name: fact_factuur_2; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fact_factuur_2 ON offertes USING btree (factuur_nr_2);


ALTER INDEX public.fact_factuur_2 OWNER TO covide;

--
-- Name: fact_factuur_3; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fact_factuur_3 ON offertes USING btree (factuur_nr_3);


ALTER INDEX public.fact_factuur_3 OWNER TO covide;

--
-- Name: fsysb_id; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fsysb_id ON filesys_bestanden USING btree (id);


ALTER INDEX public.fsysb_id OWNER TO covide;

--
-- Name: fsysb_map; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fsysb_map ON filesys_bestanden USING btree (map_id);


ALTER INDEX public.fsysb_map OWNER TO covide;

--
-- Name: fsysb_naam; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fsysb_naam ON filesys_bestanden USING btree (naam);


ALTER INDEX public.fsysb_naam OWNER TO covide;

--
-- Name: fsysb_naamoms; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fsysb_naamoms ON filesys_bestanden USING btree (naam, omschrijving);


ALTER INDEX public.fsysb_naamoms OWNER TO covide;

--
-- Name: fsysm_id; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE UNIQUE INDEX fsysm_id ON filesys_mappen USING btree (id);


ALTER INDEX public.fsysm_id OWNER TO covide;

--
-- Name: fsysm_naam; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fsysm_naam ON filesys_mappen USING btree (naam);


ALTER INDEX public.fsysm_naam OWNER TO covide;

--
-- Name: fsysm_omschrijving; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX fsysm_omschrijving ON filesys_mappen USING btree (omschrijving);


ALTER INDEX public.fsysm_omschrijving OWNER TO covide;

--
-- Name: klachten_deb; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX klachten_deb ON klachten USING btree (debiteur_nr);


ALTER INDEX public.klachten_deb OWNER TO covide;

--
-- Name: mail_berichten_askwrel; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mail_berichten_askwrel ON mail_berichten USING btree (askwichrel) WHERE (askwichrel = 1);


ALTER INDEX public.mail_berichten_askwrel OWNER TO covide;

--
-- Name: mail_berichten_indexed; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mail_berichten_indexed ON mail_berichten USING btree (indexed);


ALTER INDEX public.mail_berichten_indexed OWNER TO covide;

--
-- Name: mail_berichten_msg_id; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mail_berichten_msg_id ON mail_berichten USING btree (message_id);


ALTER INDEX public.mail_berichten_msg_id OWNER TO covide;

--
-- Name: mail_berichten_stpop; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mail_berichten_stpop ON mail_berichten USING btree (status_pop) WHERE (status_pop = 1);


ALTER INDEX public.mail_berichten_stpop OWNER TO covide;

--
-- Name: mail_datum; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mail_datum ON mail_berichten USING btree (datum);


ALTER INDEX public.mail_datum OWNER TO covide;

--
-- Name: mail_debnr; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mail_debnr ON mail_berichten USING btree (debiteur_nr) WHERE (debiteur_nr > 0);


ALTER INDEX public.mail_debnr OWNER TO covide;

--
-- Name: mailatt_berichtid; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailatt_berichtid ON mail_attachments USING btree (bericht_id);


ALTER INDEX public.mailatt_berichtid OWNER TO covide;

--
-- Name: mailatt_id; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailatt_id ON mail_attachments USING btree (id);


ALTER INDEX public.mailatt_id OWNER TO covide;

--
-- Name: mailatt_tempid; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailatt_tempid ON mail_attachments USING btree (temp_id);


ALTER INDEX public.mailatt_tempid OWNER TO covide;

--
-- Name: mailber_gebid; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailber_gebid ON mail_berichten USING btree (gebruiker_id);


ALTER INDEX public.mailber_gebid OWNER TO covide;

--
-- Name: mailber_mapid; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailber_mapid ON mail_berichten USING btree (map_id);


ALTER INDEX public.mailber_mapid OWNER TO covide;

--
-- Name: mailberichten_nieuw; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailberichten_nieuw ON mail_berichten USING btree (nieuw) WHERE (nieuw = 1);


ALTER INDEX public.mailberichten_nieuw OWNER TO covide;

--
-- Name: mailmappen_geb; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailmappen_geb ON mail_mappen USING btree (gebruiker_id);


ALTER INDEX public.mailmappen_geb OWNER TO covide;

--
-- Name: mailmappen_id; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailmappen_id ON mail_mappen USING btree (id);


ALTER INDEX public.mailmappen_id OWNER TO covide;

--
-- Name: mailmappen_naam; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailmappen_naam ON mail_mappen USING btree (naam);


ALTER INDEX public.mailmappen_naam OWNER TO covide;

--
-- Name: mailmappen_parent; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX mailmappen_parent ON mail_mappen USING btree (parentmap_id);


ALTER INDEX public.mailmappen_parent OWNER TO covide;

--
-- Name: note_deb; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX note_deb ON notitie USING btree (debiteur_nr);


ALTER INDEX public.note_deb OWNER TO covide;

--
-- Name: note_delstatus; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX note_delstatus ON notitie USING btree (delstatus);


ALTER INDEX public.note_delstatus OWNER TO covide;

--
-- Name: note_gel; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX note_gel ON notitie USING btree (gelezen) WHERE (gelezen <> 1);


ALTER INDEX public.note_gel OWNER TO covide;

--
-- Name: note_ontvanger; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX note_ontvanger ON notitie USING btree (ontvanger);


ALTER INDEX public.note_ontvanger OWNER TO covide;

--
-- Name: note_part_delstatus; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX note_part_delstatus ON notitie USING btree (delstatus) WHERE (delstatus <> 2);


ALTER INDEX public.note_part_delstatus OWNER TO covide;

--
-- Name: note_part_ged; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX note_part_ged ON notitie USING btree (gedaan) WHERE (gedaan IS NULL);


ALTER INDEX public.note_part_ged OWNER TO covide;

--
-- Name: note_sender; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX note_sender ON notitie USING btree (schrijver);


ALTER INDEX public.note_sender OWNER TO covide;

--
-- Name: note_support; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX note_support ON notitie USING btree (support);


ALTER INDEX public.note_support OWNER TO covide;

--
-- Name: off_bedrijfsnaam; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX off_bedrijfsnaam ON offertes USING btree (bedrijfsnaam);


ALTER INDEX public.off_bedrijfsnaam OWNER TO covide;

--
-- Name: off_factuur_nr; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX off_factuur_nr ON offertes USING btree (factuur_nr);


ALTER INDEX public.off_factuur_nr OWNER TO covide;

--
-- Name: omz_debnr; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX omz_debnr ON omzet_akties USING btree (debiteur_nr);


ALTER INDEX public.omz_debnr OWNER TO covide;

--
-- Name: omz_factuur; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX omz_factuur ON omzet_akties USING btree (factuur_nr);


ALTER INDEX public.omz_factuur OWNER TO covide;

--
-- Name: statuslist_gebid; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX statuslist_gebid ON status_list USING btree (gebruiker_id);


ALTER INDEX public.statuslist_gebid OWNER TO covide;

--
-- Name: statuslist_mark_delete; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX statuslist_mark_delete ON status_list USING btree (mark_delete);


ALTER INDEX public.statuslist_mark_delete OWNER TO covide;

--
-- Name: statuslist_msgid; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE INDEX statuslist_msgid ON status_list USING btree (msg_id);


ALTER INDEX public.statuslist_msgid OWNER TO covide;

--
-- Name: tableindex; Type: INDEX; Schema: public; Owner: covide; Tablespace: 
--

CREATE UNIQUE INDEX tableindex ON "statistics" USING btree (tabel);


ALTER INDEX public.tableindex OWNER TO covide;

--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

