<?php
/**
 * Covide CMS module
 *
 * @author Michiel van Baak <michiel@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
if (!class_exists("Cms_output")) {
	die("no class definition found");
}

/**
 * Possible form settings for now:
 *
 * default_classifications - always attach these classifications to the new address
 * user_classifications_N - classifications a submitter can select from where N is the number
 * user_cla_desc_N - description for on the form.
 * address_type - relations/bcard
 */
// get settings
$cms_data = new Cms_data();
$formsettings = $cms_data->getFormSettings($id);
$userselections = array();
foreach($formsettings as $v) {
	if ($v["settingsname"] == "default_classifications" || strpos($v["settingsname"], "user_classifications") === 0) {
		$val = explode(",", $v["settingsvalue"]);
		if (strpos($v["settingsname"], "user_classifications") === 0) {
			$userselections[] = $v["settingsname"];
		}
	} else {
		$val = $v["settingsvalue"];
	}
	$formsetting[$v["settingsname"]] = $val;
}
// init some variables with the setting value possibilities
$addresstypes = array(
	"relations" => gettext("Relations"),
	"bcard"     => gettext("Business Card")
);
$crmcla_selectmode = array(
	"select" => gettext("Selectbox"),
	"check"  => gettext("Checkboxen")
);
$classification_data = new Classification_data();
$crm_classifications = $classification_data->getClassifications("", "", "", 1);
$crmcla = array();
foreach($crm_classifications as $v) {
	$crmcla[$v["id"]] = $v["description"];
}
$output = new Layout_output();
$output->layout_page("", 1);
$output->addTag("form", array(
	"id" => "cmsFormSettings",
	"method" => "post",
	"action" => "index.php",
));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveFormSettings");
	$output->addHiddenField("id", $id);
	$frame = new Layout_venster(array(
		"title"    => gettext("CMS"),
		"subtitle" => gettext("crm field settings")
	));
	$frame->addVensterData();
		$table = new Layout_table();
		$table->addTableRow();
			$table->insertTableData(gettext("Address type"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("formsettings[address_type]", $addresstypes, $formsetting["address_type"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Default classifications"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("formsettings[default_classifications][]", $crmcla, $formsetting["default_classifications"], 1);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			if (count($userselections)) {
				$table->insertTableData(gettext("User selectable classifications"), array("colspan" => 2), "header");
			} else {
				$table->insertTableData(gettext("User selectable classifications"), "", "header");
				$table->addTableData("", "data");
					$table->insertAction("file_add", gettext("add"), "javascript:add_usercla(1, ".$id.");");
				$table->endTableData();
			}
		$table->endTableRow();
		$i = 1;
		natcasesort($userselections);
		$count = count($userselections);
		foreach ($userselections as $v) {
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addTextField("formsettings[name_".$v."]", $formsetting["name_".$v]);
				$table->endTableData();
				$table->addTableData("", "data");
					$table->addSelectField("formsettings[$v][]", $crmcla, $formsetting[$v], 1);
					$table->insertAction("delete", gettext("remove"), "javascript:remove_usercla('$v', $id)");
					if ($count == $i) {
						$last_number = substr($v, 21);
						$next_number = $last_number + 1;
						$table->addTag("br");
						$table->insertAction("file_add", gettext("add"), "javascript:add_usercla(".$next_number.", ".$id.");");
					}
				$table->endTableData();
			$table->endTableRow();
			$i++;
		}
		$table->addTableRow();
			$table->insertTableData(gettext("User selection mode"), "", "header");
			$table->addTableData("", "data");
				$table->addSelectField("formsettings[user_selectmode]", $crmcla_selectmode, $formsetting["user_selectmode"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData();
				$table->insertAction("close", gettext("close window"), "javascript: window.close();");
				$table->insertAction("save", gettext("save"), "javascript: save_formsettings();");
			$table->endTableData();
		$table->endTableRow();
		$table->endtable();
		$frame->addCode($table->generate_output());
		unset($table);
	$frame->endVensterData();
	$output->addCode($frame->generate_output());
	unset($frame);
	$output->endTag("form");
	$output->load_javascript(self::include_dir."cmsFormSettings.js");
$output->layout_page_end();
$output->exit_buffer();
?>
