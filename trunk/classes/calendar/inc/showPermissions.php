<?php
/**
 * Covide Groupware-CRM calendar module
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
/* only allow this file to be included in the class scope */
if (!class_exists("Calendar_output")) {
	die("no class definition found");
}
/* get username */
$user_data = new User_data();
$username = $user_data->getUsernameById($user_id);
/* get current permission delegation records for this user */
$calendar_delegation = array();
$calendar_data = new Calendar_data();
$calendar_delegation = $calendar_data->getDelegationByUser($user_id);
if (!is_array($calendar_delegation))
	$calendar_delegation = array();
$permusers = array();
/* make array with RO users and an array with RW users */
foreach ($calendar_delegation as $k=>$v) {
	if ($v["permission"] == "RO") {
		$ro_users[] = $v["user_id_visitor"];
		//= $user_data->getUsernameById($v["user_id_visitor"]);
		$permusers[] = $v["user_id_visitor"];
	} elseif ($v["permission"] == "RW") {
		$rw_users[] = $v["user_id_visitor"];
		//= $user_data->getUsernameById($v["user_id_visitor"]);
		$permusers[] = $v["user_id_visitor"];
	}
}
/* make arrays hold one item if they are empty */
if (!count($ro_users)) {
	$ro_users = array();
}
if (!count($rw_users)) {
	$rw_users = array();
}
/* get users from db */
$userlist = $user_data->getUserList();
/* filter users so only users without permissions are here */
foreach ($userlist as $a=>$b) {
	if (!in_array($a, $permusers)) {
		$d_users[] = $a;
		//= $b;
	}
}
$useroutput = new User_output();
/* output buffer */
$output = new Layout_output();
$output->layout_page("", 1);
	$output->addTag("form", array(
		"id"     => "permform",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "calendar");
	$output->addHiddenField("action", "permissionsave");
	$output->addHiddenField("calperm[user_id]", $user_id);
	$output->addHiddenField("calperm[closewin]", "1");
	/* window widget */
	$venster = new Layout_venster(array(
		"title"    => gettext("calendar"),
		"subtitle" => gettext("share")
	));
	$venster->addVensterData();
		/* table for 3 parts */
		$table = new Layout_table(array("cellspacing"=>1));
		$table->addTableRow();
			$table->insertTableData(gettext("user").": ".$username, array("colspan"=>5));
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("read-only"), "", "header");
			$table->insertTableData("", "", "header");
			$table->insertTableData(gettext("read/write"), "", "header");
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("style"=>"vertical-align: bottom;"), "data");
				$table->addHiddenField("calperm[ro]", implode(",", $ro_users));
				$table->addCode($useroutput->user_selection("calpermro", implode(",", $ro_users), 1, 0, 0, 0));
			$table->endTableData();
			$table->addTableData("", "data");
				$table->addSpace(3);
			$table->endTableData();
			$table->addTableData(array("style"=>"vertical-align: bottom;"), "data");
				$table->addHiddenField("calperm[rw]", implode(",", $rw_users));
				$table->addCode($useroutput->user_selection("calpermrw", implode(",", $rw_users), 1, 0, 0, 0));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData(array("colspan"=>3), "data");
				$table->insertAction("save", gettext("save"), "javascript: perm_save();");
			$table->endTableData();
		$table->endTableRow();
		/* add table to window and destroy object */
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
		
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");
	$output->start_javascript();
		$output->addCode(
			"
			function perm_save() {
				document.getElementById('permform').submit();
			}
			"
		);
	$output->end_javascript();
/* end page and flush buffer to client */
$output->layout_page_end();
$output->exit_buffer();
?>
