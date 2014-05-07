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
$addresses[0] = gettext("none");
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
	1 => gettext("daily view"),
	2 => gettext("monthly calendar")
);
/* yes/no dropdowns */
$showyesno = array(
	0 => gettext("no"),
	1 => gettext("yes")
);
/* themes */
$themes = array(
	0 => "Oud Look",
	1 => "Covide II",
	2 => "Neo-Atlantica",
	3 => "Terrazur",
	4 => "New Mexico",
	5 => "Smooth Winter",
	6 => "Venetus",
	7 => "Venetus II"
);
/* mailprotocols */
$mailprotocols = array(
	0 => "POP3",
	1 => "IMAP"
);
$mail_mode = array(
	0 => gettext("text"),
	1 => gettext("HTML")
);
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
/*
$htmleditors = array(
	2 => gettext("MSIE/Gecko/DHTML (standaard)"),
	3 => gettext("Java based (alternatief)")
);
*/
$htmleditors = array(
	2 => gettext("Auto detection")
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
/* build our windows and views etc */
/* window with basic user info like password and address settings */
$venster_basic = new Layout_venster(array(
	"title" => gettext("common settings")
));
$venster_basic->addVensterData();
	$table = new Layout_table(array("cellspacing"=>1));
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
		$table->insertTableData(gettext("last login"), "", "header");
		$table->addTableData("", "data");
			$table->addCode($userinfo["last_login_host"]." @ ");
			$table->addCode($userinfo["last_login_time_h"]);
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
	if ($adminmode) {
		$table->addTableRow();
			$table->insertTableData(gettext("active"), "", "header");
			$table->addTableData("", "data");
				$table->addCheckBox("user[is_active]", "1", $userinfo["is_active"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("address information"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[address_id]", $addresses, $userinfo["address_id"]);
				unset($addresses);
			$table->endTableData();
		$table->endTableRow();
		if ($GLOBALS["covide"]->license["has_hrm"]) {
			$table->addTableRow();
				$table->insertTableData(gettext("location"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("user[employer_id]", $companylocations, $userinfo["employer_id"]);
					unset($addresses);
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
	if (!$GLOBALS["covide"]->license["disable_basics"]) {
		$table->addTableRow();
			$table->insertTableData(gettext("default calendar view"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[calendarmode]", $calendarmodi, $userinfo["calendarmode"]);
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
		} else {
			$table->addHiddenField("user[addressaccountmanage]", $userinfo["addressaccountmanage"]);
			$table->addHiddenField("user[addresssyncmanage]", $userinfo["addresssyncmanage"]);
		}
	}
	$table->addTableRow();
		$table->insertTableData(gettext("logout automatically"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[automatic_logout]", $showyesno, $userinfo["automatic_logout"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			if ($_REQUEST["id"] && $adminmode) {
				$table->insertAction("delete", gettext("deactivate"), "javascript: user_deactivate();");
				$table->addSpace();
			}
			$table->addTag("div", array("id" => "action_save"));
				$table->insertAction("save", gettext("save"), "javascript: user_save();");
			$table->endTag("div");
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();
	$venster_basic->addCode($table->generate_output());
	unset($table);
$venster_basic->endVensterData();

/* window with manager settings */
$venster_access = new Layout_venster(array(
	"title" => gettext("permissions")
));
$venster_access->addVensterData();
	$table = new Layout_table(array("cellspacing" => 1));
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
	if ($GLOBALS["covide"]->license["has_finance"] && !$GLOBALS["covide"]->license["disable_basics"]) {
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
	}
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
	$table->addTableRow(array("style" => "display: none;"));
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
	if ($GLOBALS["covide"]->license["has_cms"]) {
		$table->addTableRow();
			$table->addTableData("", "data");
				$table->insertAction("module_cms", gettext("cms access level"), "");
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addCode(gettext("cms access level"));
			$table->endTableData();
			$table->addTableData("", "data");
				$cms_data = new cms_data();
				$sel = $cms_data->cms_xs_levels;
				$table->addSelectField("user[xs_cms_level]", $sel, $userinfo["xs_cms_level"]);
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

if (!$GLOBALS["covide"]->license["disable_basics"]) {
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
			$table->insertTableData(gettext("items per folder"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[mail_num_items]", $pagesizeitems, $userinfo["mail_num_items"]);
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
		$venster_email->addCode($table->generate_output());
		unset($table);
	$venster_email->endVensterData();
}

/* window with additional misc settings */
$venster_misc = new Layout_venster(array(
	"title" => gettext("remaining")
));
$venster_misc->addVensterData();
	$table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableData(gettext("show help with buttons"), "", "header");
		$table->addTableData("", "data");
			$table->addSelectField("user[showhelp]", $showyesno, $userinfo["showhelp"]);
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->insertTableData(gettext("show pop-up with alerts"), "", "header");
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
			$table->insertTableData(gettext("show popups for voip"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("user[showvoip]", $showyesno, $userinfo["showvoip"]);
			$table->endTableData();
		$table->endTableRow();
	}
	if (!$GLOBALS["covide"]->license["disable_basics"]) {
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
		$table->insertTableData("", "", "header");
		$table->addTableData("", "data");
			$table->addTag("div", array("id" => "action_save_bottom"));
				$table->insertAction("save", gettext("save"), "javascript: user_save();");
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
$output->addTag("div", array("style" => "visibility: hidden;", "id" => "errordiv"));
$output->endTag("div");
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
		if (!$GLOBALS["covide"]->license["disable_basics"]) {
			/* window with email settings */
			$table->addCode($venster_email->generate_output());
			unset($venster_email);
		}
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
