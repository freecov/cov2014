<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

/* make sure we cannot be called statically */
if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* date specs for dropdown boxes */
$days   = array(0 => gettext("na"));
$months = array(0 => gettext("na"));
$years  = array(0 => gettext("na"));
for ($i=1; $i<=31; $i++) {
	if ($i < 10) { $i = "0".$i; }
	$days[$i] = $i;
}
for ($i=1; $i<=12; $i++) {
	if ($i < 10) { $i = "0".$i; }
	$months[$i] = $i;
}
for ($i=1900; $i<=date("Y")+2; $i++) {
	$years[$i] = $i;
}
/* gender specs for dropdown box */
$genders = array(
	0 => gettext("none"),
	1 => gettext("male"),
	2 => gettext("female")
);
/* get the hrm info for this address record */
$address_data = new Address_data();
$hrminfo_arr = $address_data->getHRMinfo($user_id);
$hrminfo = $hrminfo_arr[0];
unset($hrminfo_arr);

/* start output */
$output = new Layout_output();
$output->layout_page("edit hrm", 1);
	$output->addTag("form", array(
		"id"     => "hrmform",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "address");
	$output->addHiddenField("action", "save_hrminfo");
	$output->addHiddenField("hrm[user_id]", $user_id);
	$venster = new Layout_venster();
	$venster->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			$table->insertTableData(gettext("birth date"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("hrm[bday_day]",   $days,   date("d", $hrminfo["timestamp_birthday"]));
				$table->addSelectField("hrm[bday_month]", $months, date("m", $hrminfo["timestamp_birthday"]));
				$table->addSelectField("hrm[bday_year]",  $years,  date("Y", $hrminfo["timestamp_birthday"]));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("sex"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("hrm[gender]", $genders, $hrminfo["gender"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Social Security Number"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("hrm[social_security_nr]", $hrminfo["social_security_nr"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Start of the contract"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("hrm[start_day]",   $days,   date("d", (int)$hrminfo["timestamp_started"]));
				$table->addSelectField("hrm[start_month]", $months, date("m", (int)$hrminfo["timestamp_started"]));
				$table->addSelectField("hrm[start_year]",  $years,  date("Y", (int)$hrminfo["timestamp_started"]));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Expiration of the contract"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("hrm[end_day]",   $days,   date("d", (int)$hrminfo["timestamp_stop"]));
				$table->addSelectField("hrm[end_month]", $months, date("m", (int)$hrminfo["timestamp_stop"]));
				$table->addSelectField("hrm[end_year]",  $years,  date("Y", (int)$hrminfo["timestamp_stop"]));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("contract"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("hrm[contract_type]", $hrminfo["contract_type"], array("rows" => 10, "cols" => 50));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("work hours"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("hrm[contract_hours]", $hrminfo["contract_hours"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Holiday right"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("hrm[contract_holidayhours]", $hrminfo["contract_holidayhours"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("hourly gross wage"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("hrm[gross_wage]", $hrminfo["gross_wage"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Kilometer allowance"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("hrm[kilometer_allowance]", $hrminfo["kilometer_allowance"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("overhead")." %", "", "header");
			$perc = array();
			for ($i = 1; $i <= 100; $i++) {
				$perc[$i] = $i;
			}
			$table->addTableData("", "data");
				$table->addSelectField("hrm[overhead]", $perc, $hrminfo["overhead"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("evaluation"), "", "header");
			$table->addTableData("", "data");
				$table->addTextArea("hrm[evaluation]", $hrminfo["evaluation"], array("rows" => 10, "cols" => 50));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData("&nbsp;", "", "header");
			$table->addTableData("", "data");
				$table->insertAction("save", gettext("save"), "javascript: document.getElementById('hrmform').submit();");
				$table->insertAction("cancel", gettext("cancel"), "javascript: window.close();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
	$output->endTag("form");
/* end output and flush to client */
$output->layout_page_end();
$output->exit_buffer();
?>
