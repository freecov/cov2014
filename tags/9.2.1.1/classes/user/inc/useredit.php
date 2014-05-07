<?php
/**
 * Covide Groupware-CRM user module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
if (!class_exists("User_output")) {
	die("no class definition found");
}
$_user_id = (int)$_REQUEST["id"];
$userdata = new User_data();
$editperms = $userdata->getUserPermissionsById($_SESSION["user_id"]);


if ($_user_id) {
	$userinfo = $userdata->getUserDetailsById($_REQUEST["id"], 1);
} else {
	$userinfo = $userdata->getNewUser();
}
if ($editperms["xs_usermanage"] || $editperms["xs_limitusermanage"]) { $adminmode = 1; }
/* gather data */
/* private addresses for address linking */
$addressdata = new Address_data();
$addresses = $addressdata->getRelationsList(array("addresstype" => "private", "privuseredit" => 1, "privuseredituser" => $_user_id, "nolimit" => 1));
$addresslist = $addresses["address"];
unset($addresses);
$addresses[0] = gettext("none");
foreach ($addresslist as $v) {
	$addresses[$v["id"]] = $v["infix"]." ".$v["surname"].", ".$v["givenname"];
}
unset($addresslist);
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
	"ES" => "ES",
	"DA" => "DA",
	"NO" => "NO"
);
/* lookup language names */
$conversion = new Layout_conversion();
foreach ($languages as $k=>$v) {
	$languages[$k] = $conversion->getLangName($v);
}

/* address modi */
$addressmodi = array(
	0 => gettext("relationcards"),
	1 => gettext("bussinesscards")
);
/* calendar modi */
$calendarmodi = array(
	1 => gettext("daily view"),
	2 => gettext("monthly calendar"),
	3 => gettext("weekly view"),
	4 => gettext("daily view with weekview"),
);
/* intervalmodi */
$intervalmodi = array(
	5 => 5,
	15 => 15,
	30 => 30,
	60 => 60
);
/* hour_format */
$hour_format = array(
	0 => "24 ".gettext("hour"),
	1 => "12 ".gettext("hour")
);
/* yes/no dropdowns */
$showyesno = array(
	0 => gettext("no"),
	1 => gettext("yes")
);
/* themes */
$themes = array(
	0 => "Covide IX",
	1 => "Covide white",
	2 => "SilverLightning",
	3 => "SunSet",
);

/* mailprotocols */
$mailprotocols = array(
	0 => "POP3 or POP3/start-TLS",
	1 => "IMAP or IMAP/start-TLS",
	2 => "IMAP/Secure Sockets Layer",
	3 => "POP3 Without SSL support",
);
$mail_mode = array(
	0 => gettext("text"),
	1 => gettext("HTML")
);
/* email templates */
$mail_data = new Email_data();
$templates = $mail_data->get_template_list();
$mail_templates_html[0] = gettext("none");
foreach ($templates as $template) {
	$mail_templates_html[$template["id"]] = $template["description"];
}



/* mail server keep */
$mailserverdeltimes = array(
	1                       => gettext("direct"),
	mktime(0,0,1,1,2,1970)  => gettext("1 day"),
	mktime(0,0,1,1,3,1970)  => gettext("2 days"),
	mktime(0,0,1,1,6,1970)  => gettext("5 days"),
	mktime(0,0,1,1,8,1970)  => gettext("1 week"),
	mktime(0,0,1,1,15,1970) => gettext("2 weeks"),
	mktime(0,0,1,1,22,1970) => gettext("3 weeks *"),
	mktime(0,0,1,2,1,1970)  => gettext("1 month *"),
	mktime(0,0,1,3,1,1970)  => gettext("2 months *"),
	mktime(0,0,1,7,1,1970)  => gettext("3 months *"),
	mktime(0,0,1,1,1,1971)  => gettext("1 year *"),
	-1                      => gettext("never")." *"
);
/* authentication methods */
$authmethods = array(
	"radius"   => gettext("radius"),
	"database" => gettext("covide database")
);
/* items per page */
$paging = new Layout_paging();
$pagesizeitems = $paging->_pagesizearray;

/* html editors */
$htmleditors = array(
	2 => gettext("tinyMCE (advanced)"),
	3 => gettext("Wyzz (simple)")
);
/* hrm, link user to company location */
if ($GLOBALS["covide"]->license["has_hrm"]) {
	$companylocations = array(0 => gettext("none"));
	$addresses_hrm = $addressdata->getRelationsList(array("addresstype" => "overig", "sub" => "kantoor", "nolimit" => 1));
	foreach ($addresses_hrm["address"] as $v) {
		$companylocations[$v["id"]] = $v["companyname"];
	}
	unset($addresses_hrm);
}

/* basic user info like password and address settings */
	$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
	$table->addTableRow();
		$table->insertTableData(gettext("common settings"), array("colspan" => 2), "header");
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("last login"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($userinfo["last_login_host"]." @ ");
			$table->addCode($userinfo["last_login_time_h"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("name"), "", "header");
		$table->addTableData("", "data");
			if ($adminmode) {
				$table->addTextField("user[username]", $userinfo["username"]);
			} else {
				$table->addCode($userinfo["username"]);
				$table->addHiddenField("user[username]", $userinfo["username"]);
			}
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("password"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("user[password]", "");
			$table->addPasswordField("user[vpassword]", "");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("password"), "", "header");
		$table->addTableData("", "data");
			$table->addHiddenField("user[password1]", "");
			$table->addPasswordField("user[vpassword1]", "");
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("employee number"), "", "header");
		$table->addTableData("", "data");
			if ($adminmode) {
				$table->addTextField("user[pers_nr]", $userinfo["pers_nr"]);
			} else {
				$table->addCode($userinfo["pers_nr"]);
				$table->addHiddenField("user[pers_nr]", $userinfo["pers_nr"]);
			}
		$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_radius"] && $adminmode) {
		$table->addTableRow();
			$table->insertTableData(gettext("authentication method"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[authmethod]", $authmethods, $userinfo["authmethod"]);
			$table->endTableData();
		$table->endTableRow();
	} else {
		$table->addHiddenField("user[authmethod]", $userinfo["authmethod"]);
	}
	if ($adminmode) {
		$table->addTableRow();
			$table->insertTableData(gettext("active"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("user[is_active]", "1", $userinfo["is_active"]);
			$table->endTableData();
		$table->endTableRow();
		if (1 == 0 && $GLOBALS["covide"]->license["has_hrm"]) {
			$table->addTableRow();
				$table->insertTableData(gettext("location"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("user[employer_id]", $companylocations, $userinfo["employer_id"]);
				$table->endTableData();
			$table->endTableRow();
		}
	}
	$table->addTableRow();
		$table->insertTableData(gettext("language"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[language]", $languages, $userinfo["language"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addHiddenField("user[automatic_logout]", 1);

	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			if ($_REQUEST["id"] && $adminmode && $editperms["xs_usermanage"]) {
				$table->insertAction("delete", gettext("deactivate"), "javascript: user_deactivate();");
				$table->addSpace();
			}
			$table->addTag("div", array("id" => "action_save"));
				$table->insertAction("save", gettext("save"), "javascript: user_save();");
			$table->endTag("div");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$settings_common = $table->generate_output();
	unset($table);

/* calendar settings */
if (!$GLOBALS["covide"]->license["disable_basics"]) {
		$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
			$table->addTableRow();
				$table->insertTableData(gettext("calendar"), array("colspan" => 2), "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("default calendar view"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("user[calendarmode]", $calendarmodi, $userinfo["calendarmode"]);
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow();
				$table->insertTableData(gettext("default calendar interval"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("user[calendarinterval]", $intervalmodi, $userinfo["calendarinterval"]);
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow();
				$table->insertTableData(gettext("default calendar timeformat"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("user[hour_format]", $hour_format, $userinfo["hour_format"]);
				$table->endTableData();
			$table->endTableRow();

			/* default calendar selection */
			$table->addTableRow();
				$table->insertTableData(gettext("default calendar selection"), "", "header");
				$table->addTableData("", "data");
					//$table->addSelectField("user[calendarselection][]", $calaccess, $userinfo["selected_cals"], 1, array("size" => 5));

					$table->addHiddenField("user[calendarselection]", $userinfo["calendarselection"]);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("usercalendarselection", $userinfo["calendarselection"], 1, 0, 0, 0, 0, 1) );

				$table->endTableData();
			$table->endTableRow();
		
			for ( $i = 0 ; $i <= count( $userinfo["externalcalendar"] ) /* that's 1 extra for a new one */; ++$i ) {
				$table->addTableRow();
					$table->insertTableData(gettext("external calendar"), "", "header");
					$table->addTableData("", "data");
					$table->addTextField("user[externalcalendar][$i]", $userinfo["externalcalendar"][$i]);
					$table->endTableData();
				$table->endTableRow();
			}
		$table->endTable();
		$settings_calendar = $table->generate_output();
		unset($table);
}

if ($adminmode || !$GLOBALS["covide"]->disable_basics) {
	/* address settings */
		$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
		$table->addTableRow();
			$table->insertTableData(gettext("address"), array("colspan" => 2), "header");
		$table->endTableRow();
		if ($adminmode) {
			$table->addTableRow();
				$table->insertTableData(gettext("address information"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("user[address_id]", $addresses, $userinfo["address_id"]);
					unset($addresses);
				$table->endTableData();
			$table->endTableRow();
		}
		if (!$GLOBALS["covide"]->disable_basics) {
			$table->addTableRow();
				$table->insertTableData(gettext("default address view"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("user[addressmode]", $addressmodi, $userinfo["addressmode"]);
				$table->endTableData();
			$table->endTableRow();
		}
		/* address manager of */
		if ($adminmode) {
			$table->addTableRow();
				$table->insertTableData(gettext("address managers of"), "", "header");
				$table->addTableData("", "data");
					$table->addHiddenField("user[addressaccountmanage]", $userinfo["addressaccountmanage"]);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("useraddressaccountmanage", $userinfo["addressaccountmanage"], 1, 0, 0, 0, 0, 1) );

				$table->endTableData();
			$table->endTableRow();

			/* sync manager of */
			$table->addTableRow();
				$table->insertTableData(gettext("sync manager of"), "", "header");
				$table->addTableData("", "data");
					$table->addHiddenField("user[addresssyncmanage]", $userinfo["addresssyncmanage"]);
					$useroutput = new User_output();
					$table->addCode( $useroutput->user_selection("useraddresssyncmanage", $userinfo["addresssyncmanage"], 1, 0, 0, 0, 0, 1) );

				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("restrict addressselection to classification"), "", "header");
				$table->addTableData("", "data");
					$table->addHiddenField("user[classificationrestriction]", $userinfo["classificationrestriction"]);
					$table->endTag("span");
					$classification = new Classification_output();
					$table->addCode( $classification->classification_selection("userclassificationrestriction", $userinfo["classificationrestriction"], "enabled", 0, 1) );
				$table->endTableData();
			$table->endTableRow();
		} else {
			$table->addHiddenField("user[addressaccountmanage]", $userinfo["addressaccountmanage"]);
			$table->addHiddenField("user[addresssyncmanage]", $userinfo["addresssyncmanage"]);
			$table->addHiddenField("user[classificationrestriction]", $userinfo["classificationrestriction"]);
		}


		$table->endTable();
	$settings_address = $table->generate_output();
	unset($table);
}


/* window with manager settings */
	$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
	$table->addTableRow();
		$table->insertTableData(gettext("permissions"), array("colspan" => 3), "header");
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->addSpace();
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("grant/revoke all permissions"));
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
			$table->insertAction("module_settings", gettext("user manager"), "");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("user manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_limitusermanage]", "1", $userinfo["xs_limitusermanage"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertAction("module_addressbook", gettext("global address manager"), "");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("global address manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_addressmanage]", "1", $userinfo["xs_addressmanage"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertAction("module_classification", gettext("global classification manager"), "");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("global classification manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_classmanage]", "1", $userinfo["xs_classmanage"]);
		$table->endTableData();
	$table->endTableRow();
	if (($GLOBALS["covide"]->license["has_finance"] || $GLOBALS["covide"]->license["has_factuur"]) && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_finance", gettext("turnover manager"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("turnover manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_turnovermanage]", "1", $userinfo["xs_turnovermanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_campaign"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_campaign", gettext("campaign manager"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("campaign manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_campaignmanage]", "1", $userinfo["xs_campaignmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_salaris"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_salaris.gif", gettext("salary manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("salary manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_salariscommanage]", "1", $userinfo["xs_salariscommanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_project"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_project", gettext("global project manager"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("global project manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_projectmanage]", "1", $userinfo["xs_projectmanage"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_project", gettext("limited project manager"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("limited project manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_limited_projectmanage]", "1", $userinfo["xs_limited_projectmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	/* XXX: mvb: We dont have those anymore. They were hidden. comment them out for now. This can be removed soon
	if ($GLOBALS["covide"]->license["has_forum"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow(array("style" => "display: none;"));
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
	if ($GLOBALS["covide"]->license["has_faq"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow(array("style" => "display: none;"));
			$table->addTableData("", "data");
				$table->insertImage("icoon_faq.gif", gettext("FAQ manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("FAQ manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_faqmanage]", "1", $userinfo["xs_faqmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	*/
	if ($GLOBALS["covide"]->license["has_issues"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_support", gettext("support manager"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("support manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_issuemanage]", "1", $userinfo["xs_issuemanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	/* XXX: mvb: We dont have those anymore. They were hidden. comment them out for now. This can be removed soon
	if ($GLOBALS["covide"]->license["has_announcements"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow(array("style" => "display: none;"));
			$table->addTableData("", "data");
				$table->insertImage("icoon_prikbord.gif", gettext("clipboard manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("clipboard manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_pollmanage]", "1", $userinfo["xs_pollmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	*/
	if (!$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_filesys", gettext("filesystem manager"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("filesystem manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_filemanage]", "1", $userinfo["xs_filemanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertAction("module_notes", gettext("note manager"), "");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("note manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_notemanage]", "1", $userinfo["xs_notemanage"]);
		$table->endTableData();
	$table->endTableRow();
	if (!$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_calendar", gettext("to do manager"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("to do manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_todomanage]", "1", $userinfo["xs_todomanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	/* XXX: mvb: We dont have those anymore. They were hidden. comment them out for now. This can be removed soon
	if ($GLOBALS["covide"]->license["has_hrm"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow(array("style" => "display: none;"));
			$table->addTableData("", "data");
				$table->insertImage("icoon_instellingen.gif", gettext("company info manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("company info manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_companyinfomanage]", "1", $userinfo["xs_companyinfomanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if (!$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_nieuwsbrief.gif", gettext("newsbulletin manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("newsbulletin manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_newslettermanage]", "1", $userinfo["xs_newslettermanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	*/
	if ($GLOBALS["covide"]->license["has_sales"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_sales", gettext("sales manager"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("sales manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_salesmanage]", "1", $userinfo["xs_salesmanage"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if ($GLOBALS["covide"]->license["has_hrm"] && !$GLOBALS["covide"]->license["disable_basics"]) {
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
	if ($GLOBALS["covide"]->license["has_hypo"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_facturen.gif", gettext("mortgage manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("mortgage manager"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_hypo]", "1", $userinfo["xs_hypo"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->addTableData("", "data");
			$table->insertAction("module_logout", gettext("contact manager"), "");
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCode(gettext("contact manager"));
		$table->endTableData();
		$table->addTableData("", "data");
			$table->addCheckBox("user[xs_relationmanage]", "1", $userinfo["xs_relationmanage"]);
		$table->endTableData();
	$table->endTableRow();

	/*
	if ($GLOBALS["covide"]->license["has_arbo"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertImage("icoon_arbo.gif", gettext("arbo office/ company doctor"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("arbo office/ company doctor")." *");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_arbo]", "1", $userinfo["xs_arbo"]);
			$table->endTableData();
		$table->endTableRow();
	}
	*/
	if ($GLOBALS["covide"]->license["has_funambol"] && !$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_funambol", gettext("funambol enable mobile device sync"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("funambol enable mobile device sync"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_funambol]", "1", $userinfo["xs_funambol"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_funambol_ex", gettext("remove deleted mailitems from mail server"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("do not push deleted mailitems to sync server"));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCheckBox("user[xs_funambol_expunge]", "1", $userinfo["xs_funambol_expunge"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->endTable();
	if ($editperms["xs_usermanage"])
		$settings_access = $table->generate_output();
	else
		$settings_access = gettext("No permission to access permissions");
	unset($table);

if ($GLOBALS["covide"]->license["has_cms"]) {
	/* window with cms access */
	$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
	$table->addTableRow();
		$table->insertTableData(gettext("CMS"), array("colspan" => 2), "header");
	$table->endTableRow();
	$table->addTableRow();
	$table->addTableData(array("valign" => "top"), "data");
	$table->insertAction("module_cms", gettext("cms access level"), "");
	$table->endTableData();
	$table->addTableData("", "data");
	$table->addCode(gettext("cms access level"));
	$table->addTag("br");
	$cms_data = new cms_data();
	$sel = $cms_data->cms_xs_levels;
	foreach ($sel as $k=>$v) {
		$table->addRadioField("user[xs_cms_level]", $v, $k, (int)$userinfo["xs_cms_level"]);
	}
	//$table->addSelectField("user[xs_cms_level]", $sel, $userinfo["xs_cms_level"]);
	$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$settings_cmsaccess = $table->generate_output();
}
/* window with theme info */
$theme_table = new Layout_table(array("width" => "100%"));
$theme_table->addTableRow();
	$theme_table->insertTableData(gettext("themes"), array("colspan" => 2), "header");
$theme_table->endTableRow();
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
$settings_themes = $theme_table->generate_output();
unset($theme_table);

if (!$GLOBALS["covide"]->license["disable_basics"] || $GLOBALS["covide"]->license["has_cms"]) {
	/* window with email settings */
	$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
	$table->addTableRow();
		$table->insertTableData(gettext("email"), array("colspan" => 2), "header");
	$table->endTableRow();
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
	$table->addTableRow();
	$table->insertTableData(gettext("send copy to address (bcc)"), "", "header");
	$table->addTableData("", "data");
	$table->addTextField("user[mail_default_bcc]", $userinfo["mail_default_bcc"]);
	$table->endTableData();
	$table->endTableRow();

	/* if mail lock is active in license table, only admins can change mail settings */
	$cur_username = $userdata->getUsernameById($_SESSION["user_id"]);
	$mail_lock    = $GLOBALS["covide"]->license["mail_lock_settings"];
	if ($mail_lock) {
		if ($cur_username == "administrator") {
			$mail_lock = 0;
		}
	}

	if ($adminmode) {
		$table->addTableRow();
		$table->insertTableData(gettext("mail protocol"), "", "header");
		$table->addTableData("", "data");
		if ($mail_lock) {
			$table->addReadonlyTextField("user[h_mail_imap]", ($userinfo["mail_imap"]) ? "imap":"pop3");
			$table->addHiddenField("user[mail_imap]", $userinfo["mail_imap"]);
		} else {
			$table->addSelectField("user[mail_imap]", $mailprotocols, $userinfo["mail_imap"]);
		}
		$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
		$table->insertTableData(gettext("mailserver"), "", "header");
		$table->addTableData("", "data");
		if ($mail_lock) {
			$table->addReadonlyTextField("user[mail_server]", $userinfo["mail_server"]);
		} else {
			$table->addTextField("user[mail_server]", $userinfo["mail_server"]);
		}
		$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
		$table->insertTableData(gettext("username"), "", "header");
		$table->addTableData("", "data");
		/* funambol causes permanent mail username lock */
		if ($mail_lock || $userinfo["xs_funambol"]) {
			$table->addReadonlyTextField("user[mail_user_id]", $userinfo["mail_user_id"]);
		} else {
			$table->addTextField("user[mail_user_id]", $userinfo["mail_user_id"]);
		}
		$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
		$table->insertTableData(gettext("password"), "", "header");
		$table->addTableData("", "data");
		$table->addHiddenField("user[mail_password]", "");
		if ($mail_lock) {
			$table->addHiddenField("user[vmail_password]", $userinfo["mail_password"]);
			$table->addReadonlyTextField("user[h_vmail_password]", preg_replace("/./s", "*", $userinfo["mail_password"]));
		} else {
			$table->addPasswordField("user[vmail_password]", $userinfo["mail_password"]);
		}
		$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
		$table->insertTableData(gettext("remove mail from server after"), "", "header");
		$table->addTableData("", "data");
		if ($mail_lock) {
			$table->addHiddenField("user[mail_server_deltime]", $userinfo["mail_server_deltime"]);
			$table->addReadonlyTextField("user[h_mail_server_deltime]", $mailserverdeltimes[$userinfo["mail_server_deltime"]]);
		} else {
			$table->addSelectField("user[mail_server_deltime]", $mailserverdeltimes, $userinfo["mail_server_deltime"]);
			$table->addCode("* = ".gettext("only with IMAP"));
		}
		$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
	$table->insertTableData(gettext("mail layout"), "", "header");
	$table->addTableData("", "data");
	$table->addSelectField("user[mail_html]", $mail_mode, $userinfo["mail_html"]);
	$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
	$table->insertTableData(gettext("show HTML email as plain text"), "", "header");
	$table->addTableData("", "data");
	$table->addSelectField("user[mail_view_textmail_only]", $showyesno, $userinfo["mail_view_textmail_only"]);
	$table->endTableData();
	$table->endTableRow();
	
	$table->addTableRow();
	$table->insertTableData(gettext("default template"), "", "header");
	$table->addTableData("", "data");
	$table->addSelectField("user[mail_default_template]", $mail_templates_html, $userinfo["mail_default_template"]);
	$table->endTableData();
	$table->endTableRow();
	
	$table->addTableRow();
	$table->insertTableData(gettext("editor preference"), "", "header");
	$table->addTableData("", "data");
	$table->addSelectField("user[htmleditor]", $htmleditors, $userinfo["htmleditor"]);
	$table->endTableData();
	$table->endTableRow();

	$table->addTableRow();
	$table->insertTableData("", "", "header");
	$table->addTableData("", "data");
	$table->insertLink(gettext("email addresses/signatures"), array("href"=>"index.php?mod=email&action=signatures&user_id=".$_user_id));
	$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
	$table->insertTableData(gettext("delete deleted/sent items after"), "", "header");
	$table->addTableData("", "data");
	$table->addSelectField("user[mail_deltime]", $mailserverdeltimes, $userinfo["mail_deltime"]);
	$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
	$table->insertTableData(gettext("use short list view"), "", "header");
	$table->addTableData("", "data");
	$table->addSelectField("user[mail_shortview]", array(0 => gettext("no (default)"), 1 => gettext("yes")), $userinfo["mail_shortview"]);
	$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
	$table->insertTableData(gettext("mail is default private"), "", "header");
	$table->addTableData("", "data");
	$table->addSelectField("user[mail_default_private]", array(0 => gettext("no (default)"), 1 => gettext("yes")), $userinfo["mail_default_private"]);
	$table->endTableData();
	$table->endTableRow();
	if ($GLOBALS["covide"]->license["has_cms"]) {
		$table->addTableRow();
			$table->insertTableData(gettext("hide cmsforms folders"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[mail_hide_cmsforms]", array(0 => gettext("no (default)"), 1 => gettext("yes")), $userinfo["mail_hide_cmsforms"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
	$table->insertTableData("", "", "header");
	$table->addTableData("", "data");
	$table->insertLink(gettext("fetch e-mail for this user"), array("href"=>"javascript: user_mailfetch(".$userinfo["id"].");"));
	$table->endTableData();
	$table->endTableRow();
	if ($userinfo["xs_funambol"]) {
		$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
		$table->insertTag("a", gettext("download sync client software"), array(
					"href" => "javascript: popup('classes/funambol/client/index.php', 'clients', 600, 500, 1);"
					));
		$currname = $userdata->getUserNameById($_SESSION["user_id"]);
		if ($currname == "administrator") {
			$table->addTag("br");
			$table->insertTag("a", gettext("init sync user data (use with care)"), array(
						"href" => "javascript: reset_sync_user();"
						));
			$table->start_javascript();
			$table->addCode("
					function reset_sync_user() {
					if (confirm(gettext('Do you really want to init the sync data for this user?')) == true) {
					if (confirm(gettext('Please make sure the device is empty, continue?')) == true) {
					popup('?mod=funambol&action=rebuild&user_id=".$_REQUEST["id"]."');
					}
					}
					}
					");
					$table->end_javascript();

		}


		$table->endTableData();
		$table->endTableRow();
	}

	$table->endTable();
	$settings_email = $table->generate_output();
	unset($table);
}

/* window with additional misc settings */
$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
$table->addTableRow();
	$table->insertTableData(gettext("remaining"), array("colspan" => 2), "header");
$table->endTableRow();
/*
   $table->addTableRow();
   $table->insertTableData(gettext("show help with buttons"), "", "header");
   $table->addTableData("", "data");
   $table->addSelectField("user[showhelp]", $showyesno, $userinfo["showhelp"]);
   $table->endTableData();
   $table->endTableRow();
 */
$table->addTableRow();
$table->insertTableData(gettext("play sound with alerts"), "", "header");
$table->addTableData("", "data");
$table->addSelectField("user[showpopup]", $showyesno, $userinfo["showpopup"]);
$table->endTableData();
$table->endTableRow();
if ($GLOBALS["covide"]->license["has_voip"]) {
	$table->addTableRow();
	$table->insertTableData(gettext("voip login/number"), "", "header");
	$table->addTableData("", "data");
	$table->addTextField("user[voip_device]", $userinfo["voip_device"]);
	$table->addCode("/");
	$table->addTextField("user[voip_number]", $userinfo["voip_number"], array(
				"style" => "width: 50px;"
				));
	$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
	$table->insertTableData(gettext("show popups for voip"), "", "header");
	$table->addTableData("", "data");
	$table->addSelectField("user[showvoip]", $showyesno, $userinfo["showvoip"]);
	$table->endTableData();
	$table->endTableRow();
}
if (1==0 && !$GLOBALS["covide"]->license["disable_basics"]) {
	$table->addTableRow();
	$table->insertTableData(gettext("show own notes in alternative view"), "", "header");
	$table->addTableData("", "data");
	$table->addSelectField("user[alternative_note_view_desktop]", $showyesno, $userinfo["alternative_note_view_desktop"]);
	$table->endTableData();
	$table->endTableRow();
}
$table->addTableRow();
$table->insertTableData(gettext("quote of the day"), "", "header");
$table->addTableData("", "data");
$table->addSelectField("user[dayquote]", $showyesno, $userinfo["dayquote"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("rss feeds"), "", "header");
$table->addTableData("", "data");
$table->addSelectField("user[rssnews]", $showyesno, $userinfo["rssnews"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("show birthdays"), "", "header");
$table->addTableData("", "data");
$table->addSelectField("user[showbdays]", $showyesno, $userinfo["showbdays"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("items per folder"), "", "header");
$table->addTableData("", "data");
$table->addSelectField("user[mail_num_items]", $pagesizeitems, $userinfo["mail_num_items"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("popups as new window"), "", "header");
$table->addTableData("", "data");
$table->addSelectField("user[popup_newwindow]", $showyesno, $userinfo["popup_newwindow"]);
$table->endTableData();
$table->endTableRow();
$conversion = new Layout_conversion();
$fonts = $conversion->getFonts(2);
$table->addTableRow();
$table->insertTableData(gettext("override font / font size"), "", "header");
$table->addTableData("", "data");
$table->addSelectField("user[font]", $fonts["fonts"], $userinfo["font"]);
$table->addSelectField("user[fontsize]", $fonts["sizes"], $userinfo["fontsize"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData("", "", "header");
$table->addTableData("", "data");
$table->addTag("div", array("id" => "action_save_bottom"));
$table->insertAction("save", gettext("save"), "javascript: user_save();");
$table->endTag("div");
$table->endTableData();
$table->endTableRow();
$table->endTable();
$settings_misc = $table->generate_output();
unset($table);

/* google settings */
$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
$table->addTableRow();
	$table->insertTableData(gettext("google"), array("colspan" => 2), "header");
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("google docs username"), "", "header");
$table->addTableData("", "data");
$table->addTextField("user[google_username]", $userinfo["google_username"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("google docs password"), "", "header");
$table->addTableData("", "data");
$table->addHiddenField("user[google_password]", "");
$table->addPasswordField("user[vgoogle_password]", $userinfo["google_password"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("google maps default starting point"), "", "header");
$table->addTableData("", "data");
$table->addTextField("user[google_startingpoint]", $userinfo["google_startingpoint"]);
$table->endTableData();
$table->endTableRow();
$table->endTable();
$settings_google = $table->generate_output();
unset($table);

/* dimdim settings */
$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
$table->addTableRow();
	$table->insertTableData(gettext("dimdim"), array("colspan" => 2), "header");
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("dimdim username"), "", "header");
$table->addTableData("", "data");
$table->addTextField("user[dimdim_username]", $userinfo["dimdim_username"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("dim dim password"), "", "header");
$table->addTableData("", "data");
$table->addPasswordField("user[dimdim_password]", $userinfo["dimdim_password"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData("", "", "header");
$table->addTableData("", "data");
$table->addTag("div", array("id" => "action_save_bottom"));
$table->insertAction("save", gettext("save"), "javascript: user_save();");
$table->endTag("div");
$table->endTableData();
$table->endTableRow();
$table->endTable();
$settings_dimdim = $table->generate_output();
unset($table);
/* twitter settings */
$table = new Layout_table(array("cellspacing"=>1, "width" => "100%"));
$table->addTableRow();
	$table->insertTableData(gettext("Twitter"), array("colspan" => 2), "header");
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("Twitter username"), "", "header");
$table->addTableData("", "data");
$table->addTextField("user[twitter_username]", $userinfo["twitter_username"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData(gettext("Twitter password"), "", "header");
$table->addTableData("", "data");
$table->addPasswordField("user[twitter_password]", $userinfo["twitter_password"]);
$table->endTableData();
$table->endTableRow();
$table->addTableRow();
$table->insertTableData("", "", "header");
$table->addTableData("", "data");
$table->addTag("div", array("id" => "action_save_bottom"));
$table->insertAction("save", gettext("save"), "javascript: user_save();");
$table->endTag("div");
$table->endTableData();
$table->endTableRow();
$table->endTable();
$settings_twitter = $table->generate_output();
unset($table);

$output = new Layout_output();
$output->layout_page(gettext("user settings"));
/* form */
$output->addTag("form", array(
			"id"     => "useredit",
			"method" => "post",
			"action" => "index.php"
			));
$output->addHiddenField("mod", "user");
$output->addHiddenField("action", "usersave");
$output->addHiddenField("user[id]", $_REQUEST["id"]);
$output->addTag("div", array("style" => "visibility: hidden;", "id" => "errordiv"));
$output->endTag("div");
/* table for page layout */
$table = new Layout_table(array("cellspacing"=>2, "width" => "100%"));
$table->addTableRow();
$table->addTableData(array("style" => "vertical-align: top;"));
/* window with basic user info like password and address settings */
$table->addCode($settings_common);
unset($venster_basic);

if ($settings_address) {
	$table->addCode($settings_address);
	unset($settings_address);
}

if ($settings_calendar) {
	$table->addCode($settings_calendar);
	unset($settings_calendar);
}

if ($adminmode) {
	/* window with manager settings */
	$table->addCode($settings_access);
	unset($settings_access);
	if ($GLOBALS["covide"]->license["has_cms"]) {
		$table->addCode($settings_cmsaccess);
		unset($settings_cmsaccess);
	}
			$table->endTableData();
			$table->addTableData(array("style" => "vertical-align: top;"));
}
		/* window with theme info */
		$table->addCode($settings_themes);
		unset($settings_themes);
		if (!$GLOBALS["covide"]->license["disable_basics"] || $GLOBALS["covide"]->license["has_cms"]) {
			/* window with email settings */
			$table->addCode($settings_email);
			unset($settings_email);
		}
		/* window with additional misc settings */
		$table->addCode($settings_misc);
		unset($settings_misc);
		/* window with additional google settings */
		$table->addCode($settings_google);
		unset($settings_google);
		/* window with additional dimdim settings */
		$table->addCode($settings_dimdim);
		unset($settings_dimdim);
		/* window with additional twitter settings */
		$table->addCode($settings_twitter);
		unset($settings_twitter);
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
