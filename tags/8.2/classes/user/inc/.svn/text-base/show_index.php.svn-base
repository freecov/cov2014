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

$userdata = new User_data();
$userinfo = $userdata->getUserDetailsById($_SESSION["user_id"]);

if (!$userinfo["xs_usermanage"]) {
	if (!$userinfo["xs_limitusermanage"]) {
		/* we dont have access to other users. Redirect to edit screen for user */
		header("Location: index.php?mod=user&action=useredit&id=".$_SESSION["user_id"]);
		exit;
	}
}

/* ok, we have access to edit other users. show selection thingie */
$active_users = $userdata->getUserList(1);
$nonactive_users = $userdata->getUserList(0);

/* add admin to list if logged in as admin */
if ($userinfo["username"] == "administrator")
	$active_users[$_SESSION["user_id"]] = "administrator";

natcasesort($active_users);
natcasesort($nonactive_users);

$current_user = $userdata->getUserPermissionsById($_SESSION["user_id"]);

$output = new Layout_output();
$output->layout_page();
	/* put a form around it */
	$output->addTag("form", array(
		"id"     => "userselect",
		"method" => "get",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "user");
	$output->addHiddenField("action", "useredit");
	/* window around it all */
	$venster = new Layout_venster(array("title" => gettext("user settings")));
	$venster->addMenuItem(gettext("new user"), "?mod=user&action=useredit&id=0");
	$venster->addMenuItem(gettext("group policy"), "?mod=user&action=groupindex");
	if ($GLOBALS["covide"]->license["has_privoxy_config"] && !$GLOBALS["covide"]->license["disable_basics"])
		$venster->addMenuItem(gettext("Proxy"), "?mod=privoxyconf");

		/* module config is only available in mysql at the moment */
		if (preg_match("/^mysql(i){0,1}:\/\//si", $GLOBALS["covide"]->dsn)) {
			$currname = $userdata->getUserNameById($_SESSION["user_id"]);
			if ($currname == "administrator" || $current_user["xs_usermanage"]) {
				$venster->addMenuItem(gettext("Access"), "?mod=user&action=manageraccess");
				$venster->addMenuItem(gettext("Modules"), "?mod=user&action=moduleconf");
			}
		}
	$venster->generateMenuItems();
	$venster->addVensterData();
		$table = new Layout_table(array("cellspacing" => 1));
		$table->addTableRow();
			$table->insertTableData(gettext("active"), "", "header");
			$table->addTableData("", "header");
				if ($current_user["xs_usermanage"])
					$table->insertAction("new", gettext("new user"), "?mod=user&action=useredit&id=0");

			$table->endTableData();
			$table->insertTableData(gettext("inactive"), "", "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "top");
				$table->addSelectField("id", $active_users, $_SESSION["user_id"], 0, array("size" => 10), "act");
			$table->endTableData();
			$table->addTableData("", "top");
				$table->insertAction("back", gettext("activate"), "javascript: user_activate();");
				$table->addTag("br");
				$table->insertAction("forward", gettext("deactivate"), "javascript: user_deactivate();");
			$table->endTableData();
			$table->addTableData(array("valign" => "top"));
				$table->addSelectField("userid_nonactive", $nonactive_users, "", 0, array("size" => 10), "nonact");
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;");
			$table->addTableData();
				$table->insertAction("edit", gettext("change"), "javascript: user_edit();");
			$table->endTableData();
			$table->insertTableData("&nbsp;");
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."show_index.js");
$output->layout_page_end();
$output->exit_buffer();
?>
