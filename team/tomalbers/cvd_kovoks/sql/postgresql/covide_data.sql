--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'covide';

SET search_path = public, pg_catalog;

--
-- Data for TOC entry 85 (OID 3135928)
-- Name: adres; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY adres (id, achternaam, voornaam, bedrijfsnaam, adres, postcode, plaats, telnr, faxnr, email, gebruiker, bedrijf, link, publiek, mobielnr, debiteur_nr, land, soortbedrijf, "comment", website, relatie_type, tav, contactpersoon, klant, leverancier, partner, prospect, overig, letop, postbus, postcodepostbus, plaatspostbus, classificatie, accmanager, actief, rbriefkop, raanhef, rletter, rvoornaam, rtussen, rachternaam, e4lid, titel, relname, relpass, modified, sync_modified) FROM stdin;
8			Terrazur	A. Fokkerstraat 27-1	3772 MP	Barneveld	0342-490364	0342-423577	info@terrazur.nl	3	1	\N	1		14144	\N	\N	\N	http://www.terrazur.nl	0	Dhr. W.  Massier	Beste Willem	1	1	\N	\N	\N	\N				\N	0	1	1	1	W.	Willem		Massier		0	\N	\N	\N	\N
\.


--
-- Data for TOC entry 86 (OID 3135943)
-- Name: adres_multivers; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY adres_multivers (id, achternaam, voornaam, bedrijfsnaam, adres, postcode, plaats, telnr, faxnr, email, gebruiker, bedrijf, link, publiek, mobielnr, debiteur_nr, land, soortbedrijf, "comment", website, relatie_type, tav, contactpersoon, klant, leverancier, partner, prospect, overig, letop, postbus, postcodepostbus, plaatspostbus, classificatie, accmanager, actief, rbriefkop, raanhef, rletter, rvoornaam, rtussen, rachternaam) FROM stdin;
\.


--
-- Data for TOC entry 87 (OID 3135956)
-- Name: adres_overig; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY adres_overig (id, bedrijfsnaam, achternaam, voornaam, adres, postcode, plaats, telnr, faxnr, email, gebruiker, bedrijf, publiek, mobielnr, "comment", website, postbus, postcodepostbus, plaatspostbus, actief, bedrijfspand, arbo_kantoor, arbo_bedrijf, arbo_code, arbo_team, sync_modified) FROM stdin;
\.


--
-- Data for TOC entry 88 (OID 3135969)
-- Name: adres_personen; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY adres_personen (id, achternaam, voornaam, adres, postcode, plaats, telnr, faxnr, email, gebruiker, bedrijf, publiek, mobielnr, "comment", website, postbus, postcodepostbus, plaatspostbus, actief, land, e4lid, modified, sync_modified) FROM stdin;
1	test	test	teststraat	1111pp	barneveld	0342-490364		info@covide.net	1	0	1							1			\N	\N
\.


--
-- Data for TOC entry 89 (OID 3135979)
-- Name: adresinfo; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY adresinfo (id, bedrijfs_id, "comment", classificatie, letop, provisie_perc, md5, photo) FROM stdin;
8	8				\N	\N	\N
\.


--
-- Data for TOC entry 90 (OID 3135988)
-- Name: agenda; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY agenda (id, "begin", eind, omschrijving, gebruiker, debiteur_nr, project, prive, n_id, imp, geaccordeerd, notifytime, notified, locatie, "start", "end", groep, gpid, km, repeat, multirel, repeat_type, alert, vakantie, bverlof, ziek, e4l_id, dnd, deckm, modified, modified_by, sync_guid, sync_hash) FROM stdin;
1	1080824400	1080826200	aanmaken covide	1	8	3	0	0	0	1	0	0	\N	2004-04-01 15:00:00	2004-04-01 15:30:00	\N	\N	0	0	0	D	0	0	0	0		\N	\N	\N	\N	\N	\N
\.


--
-- Data for TOC entry 91 (OID 3136009)
-- Name: agenda_machtiging; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY agenda_machtiging (id, eigenaar, gemachtigde, rechten) FROM stdin;
\.


--
-- Data for TOC entry 92 (OID 3136017)
-- Name: akties; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY akties (id, debiteur_nr, omschrijving, datum, rekeningflow, factuur_nr, rekeningflow_btw, grootboeknummer_id) FROM stdin;
\.


--
-- Data for TOC entry 93 (OID 3136025)
-- Name: arbo; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY arbo (id, regiokantoor, regiofax, regiotel, regioteam, werkgever, adres, postcode, plaats, aansluitcode, contactpers, tel, fax) FROM stdin;
\.


--
-- Data for TOC entry 94 (OID 3136042)
-- Name: arbo_ziek; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY arbo_ziek (id, user_id, werkgever_id, arbo_id, datum, datum_ziek, datum_melding, datum_herstel, herstel, herstel_loon, zwanger, zwanger_ziek, orgaandonatie, ongeval, ontvangt_wao, wao_perc, herintr_wao, herintr_perc, bijzonderheden, ziekmelding) FROM stdin;
\.


--
-- Data for TOC entry 95 (OID 3136058)
-- Name: bcards; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY bcards (id, bedrijfs_id, voornaam, voorletters, tussenvoegsel, achternaam, geboortedatum, mobielnr, telefoonnr, email, memo, raanhef, classificatie, rbriefkop, zadres, zpostcode, zplaats, zmobielnr, ztelefoonnr, zemail, zfaxnr, padres, ppostcode, pplaats, pmobielnr, ptelefoonnr, pemail, pfaxnr, e4lid, titel, eigen, modified, photo, sync_modified) FROM stdin;
\.


--
-- Data for TOC entry 96 (OID 3136075)
-- Name: bedrijfsclassifi; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY bedrijfsclassifi (id, omschr, actief, locked, subtype) FROM stdin;
\.


--
-- Data for TOC entry 97 (OID 3136081)
-- Name: begin_standen_finance; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY begin_standen_finance (id, grootboek_id, stand) FROM stdin;
1	1000	0.00
2	1100	0.00
\.


--
-- Data for TOC entry 98 (OID 3136088)
-- Name: boekingen; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY boekingen (id, credit, factuur, grootboek_id, status, datum, koppel_id, bedrag, product, inkoop, deb_nr, betaald, locked) FROM stdin;
\.


--
-- Data for TOC entry 99 (OID 3136099)
-- Name: boekingen_20012003; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY boekingen_20012003 (id, credit, factuur, grootboek_id, status, datum, koppel_id, bedrag, product, inkoop, deb_nr, betaald) FROM stdin;
\.


--
-- Data for TOC entry 100 (OID 3136109)
-- Name: bugs; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY bugs (id, gebruiker, module, onderwerp, omschrijving) FROM stdin;
\.


--
-- Data for TOC entry 101 (OID 3136117)
-- Name: chat_rooms; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY chat_rooms (id, naam, users, topic, aktief) FROM stdin;
\.


--
-- Data for TOC entry 102 (OID 3136125)
-- Name: chat_text; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY chat_text (id, room, "user", tekst, date) FROM stdin;
\.


--
-- Data for TOC entry 103 (OID 3136130)
-- Name: cms_bestanden; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY cms_bestanden (id, naam, dat, "type", size) FROM stdin;
\.


--
-- Data for TOC entry 104 (OID 3136137)
-- Name: cms_data; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY cms_data (id, gebruikersnaam, parentpage, paginatype, paginatitel, datumpublicatie, paginadata, pageredirect, ispublic, isactive, ismenuitem) FROM stdin;
\.


--
-- Data for TOC entry 105 (OID 3136154)
-- Name: cms_images; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY cms_images (id, paginaid, "path", omschrijving) FROM stdin;
\.


--
-- Data for TOC entry 106 (OID 3136162)
-- Name: faq; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY faq (id, category, vraag, antwoord) FROM stdin;
\.


--
-- Data for TOC entry 107 (OID 3136170)
-- Name: faq_cat; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY faq_cat (id, naam) FROM stdin;
\.


--
-- Data for TOC entry 108 (OID 3136175)
-- Name: filesys_bestanden; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY filesys_bestanden (id, naam, map_id, project, relatie, datum, user_id, data, "type", size, omschrijving) FROM stdin;
\.


--
-- Data for TOC entry 109 (OID 3136191)
-- Name: filesys_mappen; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY filesys_mappen (id, naam, openbaar, relatie, relatie_id, user_id, hoofdmap, gedeeld, omschrijving, hrm_id, sticky, project_id) FROM stdin;
3	openbare mappen	1	0	\N	\N	0	\N	\N	\N	0	0
12	Terrazur	1	1	8	\N	4	\N	\N	\N	0	0
23	mijn documenten	0	0	\N	1	0	\N	\N	\N	0	0
20	medewerkers	1	0	\N	\N	19	\N	\N	\N	1	0
21	oud-medewerkers	1	0	\N	\N	19	\N	\N	\N	1	0
25	mijn documenten	0	0	\N	2	0	\N	\N	\N	0	0
24	projecten	1	0	\N	\N	0	\N	\N	\N	1	0
19	hrm	1	0	\N	\N	0	\N	\N	\N	1	0
4	relaties	1	0	\N	\N	0	\N	\N	\N	1	0
22	covide	1	0	\N	\N	20	\N	\N	2	0	0
\.


--
-- Data for TOC entry 110 (OID 3136204)
-- Name: forum; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY forum (id, ref, gebruiker, project, datum, onderwerp, inhoud, gelezen) FROM stdin;
\.


--
-- Data for TOC entry 111 (OID 3136215)
-- Name: functies; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY functies (id, naam, omschr) FROM stdin;
\.


--
-- Data for TOC entry 112 (OID 3136223)
-- Name: gebruikers; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY gebruikers (id, naam, wachtwoord, persnr, xs_usermanage, xs_adresmanage, xs_projectmanage, xs_forummanage, adres_id, xs_pollmanage, xs_faqmanage, xs_klachtmanage, xs_chatmanage, xs_omzetmanage, xs_notemanage, xs_todomanage, "comment", actief, style, mail_server, mail_gebruiker, mail_wachtwoord, mail_email, mail_email1, mail_html, mail_handtekening, mail_toonaantal, mail_deltime, days, htmleditor, adresaccmanage, agendasel, toonhelp, toonpopup, xs_salarismanage, server_deltime, xs_bedrijfsinfomanage, xs_hrmmanage, taal, werkgever, a_uitl, view_textmail_only, e4l_update, dayquote, infowin_altmethod, xs_e4l, xs_filemanage, xs_limitusermanage, change_theme, xs_relatiemanage, xs_nieuwsbriefmanage, renderstatus, mail_forward, toonvoip, handtekening, voip_device, xs_salesmanage, e4l_username, e4l_password, agendamode, xs_arbo, xs_arbo_validated, eigen_aantekeningen_alt, rssnews, mail_showbcc, mail_imap, mail_num_items, xs_hypo, sync4j_source, sync4j_path, sync4j_source_adres, sync4j_source_todo) FROM stdin;
3	archiefgebruiker	3c604b2e43ca806536d70f781a13a65e	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0	\N	1	\N	\N	\N	\N	\N	\N	\N	\N	0	0	0	2	\N	\N	0	1	0	1	0	0	NL	0	0	\N	0	\N	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
1	administrator	3c604b2e43ca806536d70f781a13a65e	\N	1	1	\N	\N	\N	\N	\N	\N	\N	\N	\N	0	\N	1	0	\N	\N	\N	\N	\N	\N	\N	0	0	17	2	\N	\N	0	1	0	1	0	0	NL	0	0	\N	0	\N	1	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
2	covide	1a529f44b9575060c370478b01e6674c	1	1	1	1	1	1	1	1	1	\N	0	1	1	<p></p>	1	0					demo@covide.net	0		1	1206001	162	2			1	1	0	1	1	0	NL	0	0	0	1092811595	1	1	0	1	1	1	1	1		0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for TOC entry 113 (OID 3136249)
-- Name: groep; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY groep (id, gnaam, descr, members, manager) FROM stdin;
\.


--
-- Data for TOC entry 114 (OID 3136255)
-- Name: grootboeknummers; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY grootboeknummers (id, nr, titel, debiteur) FROM stdin;
\.


--
-- Data for TOC entry 115 (OID 3136260)
-- Name: hoofd_project; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY hoofd_project (id, naam, omschrijving, beheerder, actief, status, debiteur, bcards) FROM stdin;
\.


--
-- Data for TOC entry 116 (OID 3136270)
-- Name: inkopen; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY inkopen (id, datum, balanspost, boekstuknr, descr, leverancier_nr, bedrag_ex, bedrag_inc, bedrag_btw, betaald) FROM stdin;
\.


--
-- Data for TOC entry 117 (OID 3136278)
-- Name: jaar_afsluitingen; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY jaar_afsluitingen (jaar, datum_afgesloten) FROM stdin;
\.


--
-- Data for TOC entry 118 (OID 3136284)
-- Name: klachten; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY klachten (id, tijd, klacht, afhandeling, project, registrant, rcpt, "prior", afgehandeld, debiteur_nr, email, referentie_nr) FROM stdin;
\.


--
-- Data for TOC entry 119 (OID 3136296)
-- Name: klanten; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY klanten (id, naam, adres, postcode, plaats, land, telefoonnummer, faxnummer, email, soortbedrijf_id, aantalwerknemers, debiteur_nr, contactpersoon, contactpersoon_voorletters, totaal_flow, totaal_flow_12) FROM stdin;
\.


--
-- Data for TOC entry 120 (OID 3136299)
-- Name: license; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY license (bedrijfsnaam, code, datum, project, faq, forum, klachten, chat, prikbord, enquete, emagazine, factuur, poort, snack, email, snelstart, plain, latest_version, multivers, multivers_path, mail_interval, salaris, multivers_update, hrm, exact, finance_start_date, max_upload_size, e4l, dayquote, dayquote_nr, mail_shell, voip, sales, filesyspath, arbo, disable_basics, privoxy_config, hypo, sync4j) FROM stdin;
covide	covide	1045522860	1	1	1	1	1	1	1	1	0	\N	\N	\N	\N	\N	2.4.1	\N	\N	0	1	\N	0	0	\N	4M	0	1112133600	4094	0	0	0	\N	\N	\N	\N	\N	\N
\.


--
-- Data for TOC entry 121 (OID 3136314)
-- Name: login_log; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY login_log (id, user_id, ip, "time", dag) FROM stdin;
\.


--
-- Data for TOC entry 122 (OID 3136322)
-- Name: mail_attachments; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY mail_attachments (id, bericht_id, naam, temp_id, dat, "type", size, cid) FROM stdin;
\.


--
-- Data for TOC entry 123 (OID 3136331)
-- Name: mail_berichten; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY mail_berichten (id, message_id, map_id, gebruiker_id, debiteur_nr, project_nr, van, onderwerp, header, body, datum, ishtml, publiek, van_email, aan, cc, omschr, nieuw, replyto, status_pop, bcc, datum_ontvangen, template_id, askwichrel, indexed) FROM stdin;
\.


--
-- Data for TOC entry 124 (OID 3136347)
-- Name: mail_filters; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY mail_filters (id, gebruiker_id, afzender, ontvanger, onderwerp, naar_mapid) FROM stdin;
\.


--
-- Data for TOC entry 125 (OID 3136354)
-- Name: mail_mappen; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY mail_mappen (id, naam, gebruiker_id, parentmap_id) FROM stdin;
4	Archief	\N	\N
5	Postvak-IN	1	\N
6	Verzonden-Items	1	\N
7	Verwijderde-Items	1	\N
8	Postvak-IN	2	\N
9	Verzonden-Items	2	\N
10	Verwijderde-Items	2	\N
11	Concepten	2	\N
12	Bounced berichten	2	\N
\.


--
-- Data for TOC entry 126 (OID 3136359)
-- Name: mail_templates; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY mail_templates (id, koptekst, omschrijving, width, repeat, voettekst) FROM stdin;
\.


--
-- Data for TOC entry 127 (OID 3136370)
-- Name: mail_templates_bestanden; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY mail_templates_bestanden (id, template_id, naam, temp_id, dat, "type", size, pos) FROM stdin;
\.


--
-- Data for TOC entry 128 (OID 3136380)
-- Name: medewerker; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY medewerker (id, user_id, sofinr, datum_start, geboortedatum, geslacht, dienstverband, functie, functieniveau, cont_uren, cont_vak, datum_stop, eindevaluatie) FROM stdin;
1	2	\N	\N	\N	0	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for TOC entry 129 (OID 3136390)
-- Name: notitie; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY notitie (id, datum, onderwerp, inhoud, schrijver, gelezen, ontvanger, gedaan, delstatus, project, debiteur_nr, support, extra_ont) FROM stdin;
\.


--
-- Data for TOC entry 130 (OID 3136402)
-- Name: offertes; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY offertes (id, debiteur_nr, titel, status, uitvoerder, producten_id_0, producten_id_1, producten_id_2, producten_id_3, html_0, html_1, html_2, html_3, datum_0, datum_1, datum_2, datum_3, bedrijfsnaam, prec_betaald_0, prec_betaald_1, prec_betaald_2, prec_betaald_3, factuur_nr_0, factuur_nr_1, factuur_nr_2, factuur_nr_3, btw_tonen, btw_prec, factuur_nr, datum, definitief_2, definitief_3) FROM stdin;
\.


--
-- Data for TOC entry 131 (OID 3136412)
-- Name: omzet_akties; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY omzet_akties (id, debiteur_nr, omschrijving, datum, datum_betaald, rekeningflow, rekeningflow_btw, rekeningflow_ex, factuur_nr, grootboeknummer_id, bedrag_betaald) FROM stdin;
\.


--
-- Data for TOC entry 132 (OID 3136421)
-- Name: omzet_totaal; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY omzet_totaal (id, debiteur_nr, totaal_flow, totaal_flow_btw, totaal_flow_ex, totaal_flow_12) FROM stdin;
\.


--
-- Data for TOC entry 133 (OID 3136427)
-- Name: overige_posten; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY overige_posten (id, grootboek_id, omschrijving, debiteur, datum, bedrag, tegenrekening) FROM stdin;
\.


--
-- Data for TOC entry 134 (OID 3136440)
-- Name: poll_antwoord; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY poll_antwoord (id, vraag_id, user_id, antwoord) FROM stdin;
\.


--
-- Data for TOC entry 135 (OID 3136445)
-- Name: poll_vraag; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY poll_vraag (id, vraag, actief) FROM stdin;
\.


--
-- Data for TOC entry 136 (OID 3136453)
-- Name: prefs; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY prefs (id, gebruiker, bgcolor, style, mail_server, mail_gebruiker, mail_wachtwoord, mail_email, mail_html, mail_handtekening) FROM stdin;
\.


--
-- Data for TOC entry 137 (OID 3136462)
-- Name: prikbord; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY prikbord (id, titel, tekst, popup, actief) FROM stdin;
\.


--
-- Data for TOC entry 138 (OID 3136470)
-- Name: producten; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY producten (id, titel, html, prijsperjaar, categorie, grootboeknummer_id, debiteur_nr, prijs, btw_prec) FROM stdin;
\.


--
-- Data for TOC entry 139 (OID 3136478)
-- Name: producten_in_offertes; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY producten_in_offertes (id, producten_id, omschrijving, link_id, aantal, btw, prijs) FROM stdin;
\.


--
-- Data for TOC entry 140 (OID 3136487)
-- Name: project; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY project (id, naam, omschrijving, beheerder, groep, actief, status, debiteur, lfact, budget, uren, bcards) FROM stdin;
3	covide	\N	2	1	1	0	\N	\N	\N	\N	\N
\.


--
-- Data for TOC entry 141 (OID 3136498)
-- Name: relatie_type; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY relatie_type (id, "type") FROM stdin;
\.


--
-- Data for TOC entry 142 (OID 3136503)
-- Name: snack_bestel; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY snack_bestel (id, aantal) FROM stdin;
\.


--
-- Data for TOC entry 143 (OID 3136508)
-- Name: snack_lijst; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY snack_lijst (id, naam) FROM stdin;
\.


--
-- Data for TOC entry 144 (OID 3136513)
-- Name: soortbedrijf; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY soortbedrijf (id, omschrijving) FROM stdin;
\.


--
-- Data for TOC entry 145 (OID 3136518)
-- Name: status_list; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY status_list (id, msg_id, mail_id, datum, gebruiker_id, mark_delete) FROM stdin;
\.


--
-- Data for TOC entry 146 (OID 3136528)
-- Name: support; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY support (id, tijd, opmerking, "type", klantnaam, email, referentie_nr, customer_id) FROM stdin;
\.


--
-- Data for TOC entry 147 (OID 3136536)
-- Name: teksten; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY teksten (id, html, omschrijving, "type") FROM stdin;
\.


--
-- Data for TOC entry 148 (OID 3136544)
-- Name: templates; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY templates (id, bcards, font, fontsize, inhoud, slotzin, afzender, relatie, omschrijving, classificaties, ids, betreft, gebruiker, instelling_id, datum, plaats, classificaties_niet, multirel, save_date, and_or, faxnr, handtekening) FROM stdin;
\.


--
-- Data for TOC entry 149 (OID 3136559)
-- Name: templates_instellingen; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY templates_instellingen (id, page_left, page_top, page_right, adres_left, adres_width, adres_top, adres_positie, omschrijving) FROM stdin;
\.


--
-- Data for TOC entry 150 (OID 3136571)
-- Name: todo; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY todo (id, datum, user_id, acc, titel, omschrijving, relatie_id, eind, project_id, alert, klantcontact, sync_guid, sync_hash) FROM stdin;
\.


--
-- Data for TOC entry 151 (OID 3136584)
-- Name: uren_activ; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY uren_activ (id, activiteit, uurtarief) FROM stdin;
1	niets	0.00
\.


--
-- Data for TOC entry 152 (OID 3136589)
-- Name: urenreg; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY urenreg (id, gebruiker, project, tijd_begin, tijd_eind, activiteit, omschrijving, factureren, "type") FROM stdin;
1	1	3	1080824400	1080826200	1	aanmaken covide	0	0
\.


--
-- Data for TOC entry 153 (OID 3136599)
-- Name: status_conn; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY status_conn (gebruiker_id, datetime) FROM stdin;
\.


--
-- Data for TOC entry 154 (OID 3136603)
-- Name: statistics; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY "statistics" (tabel, updates, "vacuum") FROM stdin;
\.


--
-- Data for TOC entry 155 (OID 3136607)
-- Name: filesys_rechten; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY filesys_rechten (id, map_id, gebruiker_id, rechten) FROM stdin;
\.


--
-- Data for TOC entry 156 (OID 3136612)
-- Name: meta_table; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY meta_table (id, tablename, fieldname, fieldtype, fieldorder, record_id, value) FROM stdin;
\.


--
-- Data for TOC entry 157 (OID 3136618)
-- Name: active_calls; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY active_calls (bedrijfsnaam, bedrijfsid, tijd) FROM stdin;
\.


--
-- Data for TOC entry 158 (OID 3136830)
-- Name: mail_tracking; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY mail_tracking (id, mail_id, email, datum_first, datum_last, count, mail_id_2, clients, agents, mailcode, hyperlinks) FROM stdin;
\.


--
-- Data for TOC entry 159 (OID 3136849)
-- Name: faxes; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY faxes (id, date, sender, receiver, relation_id) FROM stdin;
\.


--
-- Data for TOC entry 160 (OID 3136854)
-- Name: rssitems; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY rssitems (id, feed, title, body, link, date) FROM stdin;
\.


--
-- Data for TOC entry 161 (OID 3136862)
-- Name: rssfeeds; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY rssfeeds (id, name, homepage, url, user_id, count) FROM stdin;
1	Covide	http://www.covide.nl	http://www.covide.nl/rss.php	0	5
\.


--
-- Data for TOC entry 162 (OID 3136868)
-- Name: sales; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY sales (id, titel, score_verwacht, totaalbedrag, datum_offerte, datum_opdracht, datum_factuur, klant_id, descr, actief, datum_prospect, user_id_modified, user_sales_id) FROM stdin;
\.


--
-- Data for TOC entry 163 (OID 3136882)
-- Name: arbo_verslag; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY arbo_verslag (id, gebruiker, manager, soort, datum, omschrijving, acties, betrokkenen, datum_invoer, ziekmelding) FROM stdin;
\.


--
-- Data for TOC entry 164 (OID 3136890)
-- Name: mail_signatures; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY mail_signatures (id, gebruiker_id, email, signature, title) FROM stdin;
\.


--
-- Data for TOC entry 165 (OID 3136898)
-- Name: gebruikers_log; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY gebruikers_log (id, manager, user_id, datum, change) FROM stdin;
\.


--
-- Data for TOC entry 166 (OID 3136907)
-- Name: hypotheek; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY hypotheek (id, datum, bedrag, geldverstrekker, verzekeraar, jaarpremie, user_id, user_src, titel, descr, soort, klant_id, actief) FROM stdin;
\.


--
-- Data for TOC entry 167 (OID 3136917)
-- Name: agenda_sync; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY agenda_sync (id, gebruiker, sync_guid, "action") FROM stdin;
\.


--
-- Data for TOC entry 168 (OID 3136924)
-- Name: adres_sync; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY adres_sync (id, adres_id, adres_table, private, acc_manager, sync_modified, sync_hash, parent_id) FROM stdin;
\.


--
-- Data for TOC entry 169 (OID 3136930)
-- Name: adres_sync_guid; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY adres_sync_guid (id, sync_id, sync_guid, gebruiker) FROM stdin;
\.


--
-- Data for TOC entry 170 (OID 3136935)
-- Name: adres_sync_records; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY adres_sync_records (id, adres_table, adres_id, gebruiker_id) FROM stdin;
\.


--
-- Data for TOC entry 171 (OID 3136940)
-- Name: todo_sync; Type: TABLE DATA; Schema: public; Owner: covide
--

COPY todo_sync (id, gebruiker, sync_guid, "action") FROM stdin;
\.


--
-- TOC entry 2 (OID 3135926)
-- Name: adres_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('adres_id_seq', 8, true);


--
-- TOC entry 3 (OID 3135941)
-- Name: adres_multivers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('adres_multivers_id_seq', 1, false);


--
-- TOC entry 4 (OID 3135954)
-- Name: adres_overig_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('adres_overig_id_seq', 1, false);


--
-- TOC entry 5 (OID 3135967)
-- Name: adres_personen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('adres_personen_id_seq', 1, true);


--
-- TOC entry 6 (OID 3135977)
-- Name: adresinfo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('adresinfo_id_seq', 8, true);


--
-- TOC entry 7 (OID 3135986)
-- Name: agenda_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('agenda_id_seq', 1, true);


--
-- TOC entry 8 (OID 3136007)
-- Name: agenda_machtiging_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('agenda_machtiging_id_seq', 1, false);


--
-- TOC entry 9 (OID 3136015)
-- Name: akties_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('akties_id_seq', 1, false);


--
-- TOC entry 10 (OID 3136023)
-- Name: arbo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('arbo_id_seq', 1, false);


--
-- TOC entry 11 (OID 3136040)
-- Name: arbo_ziek_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('arbo_ziek_id_seq', 1, false);


--
-- TOC entry 12 (OID 3136056)
-- Name: bcards_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('bcards_id_seq', 1, false);


--
-- TOC entry 13 (OID 3136073)
-- Name: bedrijfsclassifi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('bedrijfsclassifi_id_seq', 1, false);


--
-- TOC entry 14 (OID 3136079)
-- Name: begin_standen_finance_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('begin_standen_finance_id_seq', 2, true);


--
-- TOC entry 15 (OID 3136086)
-- Name: boekingen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('boekingen_id_seq', 1, false);


--
-- TOC entry 16 (OID 3136097)
-- Name: boekingen_20012003_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('boekingen_20012003_id_seq', 1, false);


--
-- TOC entry 17 (OID 3136107)
-- Name: bugs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('bugs_id_seq', 1, false);


--
-- TOC entry 18 (OID 3136115)
-- Name: chat_rooms_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('chat_rooms_id_seq', 1, false);


--
-- TOC entry 19 (OID 3136123)
-- Name: chat_text_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('chat_text_id_seq', 1, false);


--
-- TOC entry 20 (OID 3136128)
-- Name: cms_bestanden_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('cms_bestanden_id_seq', 1, false);


--
-- TOC entry 21 (OID 3136135)
-- Name: cms_data_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('cms_data_id_seq', 1, false);


--
-- TOC entry 22 (OID 3136152)
-- Name: cms_images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('cms_images_id_seq', 1, false);


--
-- TOC entry 23 (OID 3136160)
-- Name: faq_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('faq_id_seq', 1, false);


--
-- TOC entry 24 (OID 3136168)
-- Name: faq_cat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('faq_cat_id_seq', 1, false);


--
-- TOC entry 25 (OID 3136173)
-- Name: filesys_bestanden_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('filesys_bestanden_id_seq', 1, false);


--
-- TOC entry 26 (OID 3136189)
-- Name: filesys_mappen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('filesys_mappen_id_seq', 24, true);


--
-- TOC entry 27 (OID 3136202)
-- Name: forum_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('forum_id_seq', 1, false);


--
-- TOC entry 28 (OID 3136213)
-- Name: functies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('functies_id_seq', 1, false);


--
-- TOC entry 29 (OID 3136221)
-- Name: gebruikers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('gebruikers_id_seq', 3, true);


--
-- TOC entry 30 (OID 3136247)
-- Name: groep_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('groep_id_seq', 1, false);


--
-- TOC entry 31 (OID 3136253)
-- Name: grootboeknummers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('grootboeknummers_id_seq', 1, false);


--
-- TOC entry 32 (OID 3136258)
-- Name: hoofd_project_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('hoofd_project_id_seq', 1, false);


--
-- TOC entry 33 (OID 3136268)
-- Name: inkopen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('inkopen_id_seq', 1, false);


--
-- TOC entry 34 (OID 3136282)
-- Name: klachten_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('klachten_id_seq', 1, false);


--
-- TOC entry 35 (OID 3136294)
-- Name: klanten_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('klanten_id_seq', 1, false);


--
-- TOC entry 36 (OID 3136312)
-- Name: login_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('login_log_id_seq', 1, true);


--
-- TOC entry 37 (OID 3136320)
-- Name: mail_attachments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('mail_attachments_id_seq', 1, false);


--
-- TOC entry 38 (OID 3136329)
-- Name: mail_berichten_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('mail_berichten_id_seq', 1, true);


--
-- TOC entry 39 (OID 3136345)
-- Name: mail_filters_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('mail_filters_id_seq', 1, false);


--
-- TOC entry 40 (OID 3136352)
-- Name: mail_mappen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('mail_mappen_id_seq', 11, true);


--
-- TOC entry 41 (OID 3136357)
-- Name: mail_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('mail_templates_id_seq', 1, false);


--
-- TOC entry 42 (OID 3136368)
-- Name: mail_templates_bestanden_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('mail_templates_bestanden_id_seq', 1, false);


--
-- TOC entry 43 (OID 3136378)
-- Name: medewerker_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('medewerker_id_seq', 1, true);


--
-- TOC entry 44 (OID 3136388)
-- Name: notitie_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('notitie_id_seq', 1, false);


--
-- TOC entry 45 (OID 3136400)
-- Name: offertes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('offertes_id_seq', 1, false);


--
-- TOC entry 46 (OID 3136410)
-- Name: omzet_akties_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('omzet_akties_id_seq', 1, false);


--
-- TOC entry 47 (OID 3136419)
-- Name: omzet_totaal_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('omzet_totaal_id_seq', 1, false);


--
-- TOC entry 48 (OID 3136425)
-- Name: overige_posten_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('overige_posten_id_seq', 1, false);


--
-- TOC entry 49 (OID 3136438)
-- Name: poll_antwoord_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('poll_antwoord_id_seq', 1, false);


--
-- TOC entry 50 (OID 3136443)
-- Name: poll_vraag_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('poll_vraag_id_seq', 1, false);


--
-- TOC entry 51 (OID 3136451)
-- Name: prefs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('prefs_id_seq', 1, false);


--
-- TOC entry 52 (OID 3136460)
-- Name: prikbord_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('prikbord_id_seq', 8, false);


--
-- TOC entry 53 (OID 3136468)
-- Name: producten_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('producten_id_seq', 1, false);


--
-- TOC entry 54 (OID 3136476)
-- Name: producten_in_offertes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('producten_in_offertes_id_seq', 1, false);


--
-- TOC entry 55 (OID 3136485)
-- Name: project_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('project_id_seq', 3, true);


--
-- TOC entry 56 (OID 3136496)
-- Name: relatie_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('relatie_type_id_seq', 1, false);


--
-- TOC entry 57 (OID 3136501)
-- Name: snack_bestel_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('snack_bestel_id_seq', 1, false);


--
-- TOC entry 58 (OID 3136506)
-- Name: snack_lijst_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('snack_lijst_id_seq', 1, false);


--
-- TOC entry 59 (OID 3136511)
-- Name: soortbedrijf_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('soortbedrijf_id_seq', 1, false);


--
-- TOC entry 60 (OID 3136516)
-- Name: status_list_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('status_list_id_seq', 1, true);


--
-- TOC entry 61 (OID 3136526)
-- Name: support_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('support_id_seq', 1, false);


--
-- TOC entry 62 (OID 3136534)
-- Name: teksten_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('teksten_id_seq', 1, false);


--
-- TOC entry 63 (OID 3136542)
-- Name: templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('templates_id_seq', 1, false);


--
-- TOC entry 64 (OID 3136557)
-- Name: templates_instellingen_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('templates_instellingen_id_seq', 1, false);


--
-- TOC entry 65 (OID 3136569)
-- Name: todo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('todo_id_seq', 1, false);


--
-- TOC entry 66 (OID 3136582)
-- Name: uren_activ_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('uren_activ_id_seq', 1, true);


--
-- TOC entry 67 (OID 3136587)
-- Name: urenreg_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('urenreg_id_seq', 1, true);


--
-- TOC entry 68 (OID 3136605)
-- Name: filesys_rechten_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('filesys_rechten_id_seq', 1, false);


--
-- TOC entry 69 (OID 3136610)
-- Name: meta_table_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('meta_table_id_seq', 1, false);


--
-- TOC entry 70 (OID 3136828)
-- Name: mail_tracking_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('mail_tracking_id_seq', 1, false);


--
-- TOC entry 71 (OID 3136847)
-- Name: faxes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('faxes_id_seq', 1, false);


--
-- TOC entry 72 (OID 3136852)
-- Name: rssitems_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('rssitems_id_seq', 1, false);


--
-- TOC entry 73 (OID 3136860)
-- Name: rssfeeds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('rssfeeds_id_seq', 1, false);


--
-- TOC entry 74 (OID 3136866)
-- Name: sales_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('sales_id_seq', 1, false);


--
-- TOC entry 75 (OID 3136880)
-- Name: arbo_verslag_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('arbo_verslag_id_seq', 1, false);


--
-- TOC entry 76 (OID 3136888)
-- Name: mail_signatures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('mail_signatures_id_seq', 1, false);


--
-- TOC entry 77 (OID 3136896)
-- Name: gebruikers_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('gebruikers_log_id_seq', 1, false);


--
-- TOC entry 78 (OID 3136905)
-- Name: hypotheek_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('hypotheek_id_seq', 1, false);


--
-- TOC entry 79 (OID 3136915)
-- Name: agenda_sync_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('agenda_sync_id_seq', 1, false);


--
-- TOC entry 80 (OID 3136920)
-- Name: adres_sync_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('adres_sync_id_seq', 1, false);


--
-- TOC entry 81 (OID 3136922)
-- Name: adres_sync_sync_modified_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('adres_sync_sync_modified_seq', 1, false);


--
-- TOC entry 82 (OID 3136928)
-- Name: adres_sync_guid_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('adres_sync_guid_id_seq', 1, false);


--
-- TOC entry 83 (OID 3136933)
-- Name: adres_sync_records_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('adres_sync_records_id_seq', 1, false);


--
-- TOC entry 84 (OID 3136938)
-- Name: todo_sync_id_seq; Type: SEQUENCE SET; Schema: public; Owner: covide
--

SELECT pg_catalog.setval('todo_sync_id_seq', 1, false);


