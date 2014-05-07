<?php
if (!class_exists("User_output")) {
	die("no class definition found");
}
$_user_id = (int)$_REQUEST["id"];
$userdata = new User_data();
$editperms = $userdata->getUserPermissionsById($_SESSION["user_id"]);

if ($_user_id) {
	$userinfo = $userdata->getUserDetailsById($_REQUEST["id"]);
} else {
	$userinfo = $userdata->getNewUser();
}
if ($editperms["xs_usermanage"] || $editperms["xs_limitusermanage"]) { $adminmode = 1; }
/* gather data */
/* private addresses for address linking */
$addressdata = new Address_data();
$addresses = $addressdata->getRelationsList(array("addresstype" => "private", "privuseredit" => 1, "nolimit" => 1));
$addresslist = $addresses["address"];
unset($addresses);
foreach ($addresslist as $v) {
	$addresses[$v["id"]] = $v["surname"].", ".$v["givenname"];
}
unset($addresslist);
$addresses[0] = gettext("geen");
/* users */
$users = $userdata->getUserList();
$users[0] = "---";
/* shared calendars */
$calendar_data = new Calendar_data();
$calinfo = $calendar_data->getDelegationByVisitor($_user_id);
$calaccess = array();
if (!is_array($calinfo)) $calinfo = array();
foreach ($calinfo as $k=>$v) {
	$calaccess[$v["user_id"]] = $userdata->getUsernameById($v["user_id"]);
}
$calaccess = array_unique($calaccess);
asort($calaccess);
$userinfo["selected_cals"] = explode(",", $userinfo["calendarselection"]);
if (!is_array($userinfo["selected_cals"])) $userinfo["selected_cals"] = array();
/* language array */
$languages = array(
	"NL" => "NL",
	"EN" => "EN",
	"DE" => "DE",
	"ES" => "ES"
);
/* calendar modi */
$calendarmodi = array(
	1 => gettext("dagoverzicht"),
	2 => gettext("maandoverzicht")
);
/* yes/no dropdowns */
$showyesno = array(
	0 => gettext("nee"),
	1 => gettext("ja")
);
/* themes */
$themes = array(
	0 => "Oud Look",
	1 => "Covide II",
	2 => "Neo-Atlantica",
	3 => "Terrazur",
	4 => "New Mexico"
);
/* mailprotocols */
$mailprotocols = array(
	0 => "POP3",
	1 => "IMAP"
);
$mail_mode = array(
	0 => gettext("tekst"),
	1 => gettext("html")
);
/* mail server keep */
$mailserverdeltimes = array(
	1                       => gettext("direct"),
	mktime(0,0,1,1,2,1970)  => gettext("1 dag"),
	mktime(0,0,1,1,3,1970)  => gettext("2 dagen"),
	mktime(0,0,1,1,6,1970)  => gettext("5 dagen"),
	mktime(0,0,1,1,8,1970)  => gettext("1 week"),
	mktime(0,0,1,1,15,1970) => gettext("2 weken"),
	mktime(0,0,1,1,22,1970) => gettext("3 weken *"),
	mktime(0,0,1,2,1,1970)  => gettext("1 maand *"),
	mktime(0,0,1,3,1,1970)  => gettext("2 maanden *"),
	mktime(0,0,1,7,1,1970)  => gettext("3 maanden *"),
	mktime(0,0,1,1,1,1971)  => gettext("1 jaar *"),
	-1                      => gettext("nooit")." *"
);
/* items per page */
$paging = new Layout_paging();
$pagesizeitems = $paging->_pagesizearray;

/* html editors */
$htmleditors = array(
	2 => gettext("MSIE/Gecko/DHTML (standaard)"),
	3 => gettext("Java based (alternatief)")
);
/* hrm, link user to company location */
if ($GLOBALS["covide"]->license["has_hrm"]) {
	$companylocations = array(0 => gettext("geen"));
	$addresses_hrm = $addressdata->getRelationsList(array("addresstype" => "overig", "sub" => "kantoor", "nolimit" => 1));
	foreach ($addresses_hrm["address"] as $v) {
		$companylocations[$v["id"]] = $v["companyname"];
	}
	unset($addresses_hrm);
}
/* build our windows and views etc */
/* window with basic user info like password and address settings */
$venster_basic = new Layout_venster(array(
	"title" => gettext("algemene instellingen")
));
$venster_basic->addVensterData();
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("naam"), "", "header");
		$table->addTableData("", "data");
			if ($adminmode) {
				$table->addTextField("user[username]", $userinfo["username"]);
			} else {
				$table->addCode($userinfo["username"]);
			}
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("wachtwoord"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("user[password]", "");
			$table->addPasswordField("user[vpassword]", "");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("wachtwoord"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("user[password1]", "");
			$table->addPasswordField("user[vpassword1]", "");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("personeels nr"), "", "header");
		$table->addTableData("", "data");
			if ($adminmode) {
				$table->addTextField("user[pers_nr]", $userinfo["pers_nr"]);
			} else {
				$table->addCode($userinfo["pers_nr"]);
			}
		$table->endTableData();
	$table->endTableRow();
	if ($adminmode) {
		$table->addTableRow();
			$table->insertTableData(gettext("actief"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("user[is_active]", "1", $userinfo["is_active"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("adres informatie"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[address_id]", $addresses, $userinfo["address_id"]);
				unset($addresses);
			$table->endTableData();
		$table->endTableRow();
		if ($GLOBALS["covide"]->license["has_hrm"]) {
			$table->addTableRow();
				$table->insertTableData(gettext("locatie"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("user[employer_id]", $companylocations, $userinfo["employer_id"]);
					unset($addresses);
				$table->endTableData();
			$table->endTableRow();
		}
	}
	$table->addTableRow();
		$table->insertTableData(gettext("taal"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[language]", $languages, $userinfo["language"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("standaard agenda modus"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[calendarmode]", $calendarmodi, $userinfo["calendarmode"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("standaard agenda selectie"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[calendarselection][]", $calaccess, $userinfo["selected_cals"], 1);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("automatisch uitloggen"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[automatic_logout]", $showyesno, $userinfo["automatic_logout"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			if ($_REQUEST["id"] && $adminmode) {
				$table->insertAction("delete", gettext("deactiveren"), "javascript: user_deactivate();");
				$table->addSpace();
			}
			$table->addTag("div", array("id" => "action_save"));
				$table->insertAction("save", gettext("opslaan"), "javascript: user_save();");
			$table->endTag("div");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster_basic->addCode($table->generate_output());
	unset($table);
$venster_basic->endVensterData();

/* window with manager settings */
$venster_access = new Layout_venster(array(
	"title" => gettext("rechten")
));
$venster_access->addVensterData();
	$table = new Layout_table(array("cellspacing" => 1));
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->addSpace();
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("alle rechten toekennen / afnemen"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("usercheckall", 1, 0);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertImage("icoon_loginstats.gif", gettext("administrator"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("administrator"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_usermanage]", "1", $userinfo["xs_usermanage"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertImage("icoon_instellingen.gif", gettext("gebruiker manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("gebruiker manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_limitusermanage]", "1", $userinfo["xs_limitusermanage"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertImage("icoon_adressen.gif", gettext("globaal adressen manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("globaal adressen manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_addressmanage]", "1", $userinfo["xs_addressmanage"]);
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_finance"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_omzet.gif", gettext("omzet manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("omzet manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_turnovermanage]", "1", $userinfo["xs_turnovermanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_salaris"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_salaris.gif", gettext("salaris manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("salaris manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_salariscommanage]", "1", $userinfo["xs_salariscommanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_project"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_projecten.gif", gettext("globaal project manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("globaal project manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_projectmanage]", "1", $userinfo["xs_projectmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_forum"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_forum.gif", gettext("forum manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("forum manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_forummanage]", "1", $userinfo["xs_forummanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_faq"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_faq.gif", gettext("faq manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("faq manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_faqmanage]", "1", $userinfo["xs_faqmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_issues"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_klachten.gif", gettext("support manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("support manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_issuemanage]", "1", $userinfo["xs_issuemanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_announcements"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_prikbord.gif", gettext("prikbord manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("prikbord manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_pollmanage]", "1", $userinfo["xs_pollmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertImage("icoon_bestandsbeheer.gif", gettext("bestandsbeheer manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("bestandsbeheer manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_filemanage]", "1", $userinfo["xs_filemanage"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertImage("icoon_notities.gif", gettext("notitie manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("notitie manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_notemanage]", "1", $userinfo["xs_notemanage"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertImage("icoon_agenda.gif", gettext("todo manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("todo manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_todomanage]", "1", $userinfo["xs_todomanage"]);
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_hrm"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_instellingen.gif", gettext("bedrijfsinfo manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("bedrijfsinfo manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_companyinfomanage]", "1", $userinfo["xs_companyinfomanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertImage("icoon_nieuwsbrief.gif", gettext("nieuwsbrief manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("nieuwsbrief manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_newslettermanage]", "1", $userinfo["xs_newslettermanage"]);
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_sales"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_sales.gif", gettext("sales manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("sales manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_salesmanage]", "1", $userinfo["xs_salesmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_hrm"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_hrm.gif", gettext("hrm manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("hrm manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_hrmmanage]", "1", $userinfo["xs_hrmmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_hypo"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_facturen.gif", gettext("hypotheek manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("hypotheek manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_hypo]", "1", $userinfo["xs_hypo"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertImage("icoon_uitloggen.gif", gettext("relatiekaarten manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("relatiekaarten manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_relationmanage]", "1", $userinfo["xs_relationmanage"]);
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_arbo"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_arbo.gif", gettext("arbo dienst / bedrijfsarts"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("arbo dienst / bedrijfsarts")." *");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_arbo]", "1", $userinfo["xs_arbo"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->endTable();
	$venster_access->addCode($table->generate_output());
	unset($table);
$venster_access->endVensterData();

/* window with theme info */
$venster_themes = new Layout_venster(array(
	"title" => gettext("themes")
));
$venster_themes->addVensterData();
	$theme_table = new Layout_table(array("width" => "100%"));
	$theme_table->addTableRow();
		$theme_table->addTableData(array("width" => "1%"), "data");
			$table = new Layout_table(array("cellspacing"=>1));
			foreach ($themes as $k=>$v) {
				$table->addTableRow();
					$table->addTableData("", "data");
						$table->addCode($v);
					$table->endTableData();
					$table->addTableData("", "data");
						$table->addRadioField("user[style]", "", $k, $userinfo["style"], "", "update_preview('$k')");
					$table->endTableData();
				$table->endTableRow();
			}
			$table->endTable();
			$theme_table->addCode($table->generate_output());
			unset($table);
		$theme_table->endTableData();
		$theme_table->addTableData(array("align" => "center"), "data");
			$theme_table->addCode($this->theme_preview($userinfo["style"])->generate_output());
		$theme_table->endTableData();
	$theme_table->endTableRow();
	$theme_table->endTable();
	$venster_themes->addCode($theme_table->generate_output());
	unset($theme_table);
$venster_themes->endVensterData();

/* window with email settings */
$venster_email = new Layout_venster(array(
	"title" => gettext("email")
));
$venster_email->addVensterData();
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("e-mail"), "", "header");
		$table->addTableData("", "data");
			if ($adminmode) {
				$table->addTextField("user[mail_email]", $userinfo["mail_email"]);
			} else {
				$table->addCode($userinfo["mail_email"]);
			}
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("e-mail (alternatief)"), "", "header");
		$table->addTableData("", "data");
			if ($adminmode) {
				$table->addTextField("user[mail_email1]", $userinfo["mail_email1"]);
			} else {
				$table->addCode($userinfo["mail_email1"]);
			}
		$table->endTableData();
	$table->endTableRow();
	if ($adminmode) {
		$table->addTableRow();
			$table->insertTableData(gettext("mailprotocol"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[mail_imap]", $mailprotocols, $userinfo["mail_imap"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("mailserver"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("user[mail_server]", $userinfo["mail_server"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("gebruikersnaam"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("user[mail_user_id]", $userinfo["mail_user_id"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("wachtwoord"), "", "header");
			$table->addTableData("", "data");
				$table->addHiddenField("user[mail_password]", "");
				$table->addPasswordField("user[vmail_password]", $userinfo["mail_password"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("mail van de server verwijderen na"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[mail_server_deltime]", $mailserverdeltimes, $userinfo["mail_server_deltime"]);
				$table->addCode("* = ".gettext("alleen onder imap mogelijk"));
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData(gettext("mail doorsturen naar"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[mail_forward]", $users, $userinfo["mail_forward"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("mail opmaak"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[mail_html]", $mail_mode, $userinfo["mail_html"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("toon standaard cc en bcc"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[mail_showbcc]", $showyesno, $userinfo["mail_showbcc"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("toon HTML mail als text"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[mail_view_textmail_only]", $showyesno, $userinfo["mail_view_textmail_only"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("voorkeur html editor"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[htmleditor]", $htmleditors, $userinfo["htmleditor"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("handtekening"), "", "header");
		$table->addTableData("", "data");
			$table->addTextArea("user[mail_signature]", $userinfo["mail_signature"], array("style"=>"width: 300px; height: 100px;"));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			$table->insertLink(gettext("meer email adressen/handtekeningen"), array("href"=>"index.php?mod=email&action=signatures&user_id=".$_user_id));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("verwijderde/verzonden items weggooien na"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[mail_deltime]", $mailserverdeltimes, $userinfo["mail_deltime"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("aantal items per map"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[mail_num_items]", $pagesizeitems, $userinfo["mail_num_items"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			$table->insertLink(gettext("email ophalen voor deze gebruiker"), array("href"=>"javascript: user_mailfetch(".$userinfo["id"].");"));
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster_email->addCode($table->generate_output());
	unset($table);
$venster_email->endVensterData();

/* window with additional misc settings */
$venster_misc = new Layout_venster(array(
	"title" => gettext("overig")
));
$venster_misc->addVensterData();
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("hulp tonen bij knoppen"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[showhelp]", $showyesno, $userinfo["showhelp"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("popup tonen bij alerts"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[showpopup]", $showyesno, $userinfo["showpopup"]);
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_voip"]) {
		$table->addTableRow();
			$table->insertTableData(gettext("voip device"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("user[voip_device]", $userinfo["voip_device"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("popup tonen bij voip"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[showvoip]", $showyesno, $userinfo["showvoip"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData(gettext("eigen aantekeningen alternatieve weergave"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[alternative_note_view_desktop]", $showyesno, $userinfo["alternative_note_view_desktop"]);
		$table->endTableData();
	$table->endTableRow();
	/* TODO: make this part of functionality again */
	/*
	$table->addTableRow();
		$table->insertTableData(gettext("quote van de dag"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[dayquote]", $showyesno, $userinfo["dayquote"]);
		$table->endTableData();
	$table->endTableRow();
	*/
	$table->addTableRow();
		$table->insertTableData(gettext("rss feeds"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[rssnews]", $showyesno, $userinfo["rssnews"]);
		$table->endTableData();
	$table->endTableRow();
	if ($adminmode && $GLOBALS["covide"]->license["has_sync4j"]) {
		$table->addTableRow();
			$table->insertTableData(gettext("Sync4j Agenda SyncSource"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("user[sync4j_source]", $userinfo["sync4j_source"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Sync4j Adresboek SyncSource"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("user[sync4j_source_adres]", $userinfo["sync4j_source_adres"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Sync4j Todo SyncSource"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("user[sync4j_source_todo]", $userinfo["sync4j_source_todo"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Sync4j Gebruikerspad"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("user[sync4j_path]", $userinfo["sync4j_path"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			$table->addTag("div", array("id" => "action_save_bottom"));
				$table->insertAction("save", gettext("opslaan"), "javascript: user_save();");
			$table->endTag("div");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster_misc->addCode($table->generate_output());
	unset($table);
$venster_misc->endVensterData();

$output = new Layout_output();
$output->layout_page();
/* form */
$output->addTag("form", array(
	"id"     => "useredit",
	"method" => "post",
	"action" => "index.php"
));
$output->addHiddenField("mod", "user");
$output->addHiddenField("action", "usersave");
$output->addHiddenField("user[id]", $_REQUEST["id"]);
/* table for page layout */
$table = new Layout_table(array("cellspacing"=>2));
$table->addTableRow();
	$table->addTableData(array("style" => "vertical-align: top;"));
		/* window with basic user info like password and address settings */
		$table->addCode($venster_basic->generate_output());
		unset($venster_basic);
		if ($adminmode) {
			/* window with manager settings */
			$table->addCode($venster_access->generate_output());
		unset($venster_access);
	$table->endTableData();
	$table->addTableData(array("style" => "vertical-align: top;"));
	}
		/* window with theme info */
		$table->addCode($venster_themes->generate_output());
		unset($venster_themes);
		/* window with email settings */
		$table->addCode($venster_email->generate_output());
		unset($venster_email);
		/* window with additional misc settings */
		$table->addCode($venster_misc->generate_output());
		unset($venster_misc);
	$table->endTableData();
$table->endTableRow();
$table->endTable();
$output->addCode($table->generate_output());
unset($table);
$output->endTag("form");
$output->load_javascript(self::include_dir."usereditcheck.js");
$output->load_javascript(self::include_dir."useredit.js");
$output->layout_page_end();
$output->exit_buffer();
?>
