<?php
/**
 * Covide Groupware-CRM metafields module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Metafields_output {
	/* constants */

	/* variables */

	/* methods */

	/* meta_add_field {{{ */
	/**
	 * show html to add a new metafield to a record
	 *
	 * @param string Table link identifier
	 * @param int The record id to link the metadata to
	 */
	public function meta_add_field($tablename, $record_id) {
		/* prepare som selectbox data arrays */
		$fieldtypes = array(
			1 => gettext("short text")."(".gettext("maximal 255 characters").")",
			2 => gettext("long text")."(".gettext("more then 255 characters possible").")",
			3 => gettext("date"),
			4 => gettext("yes/no")
		);
		$fieldorder = array();
		for ($i=-5;$i<=5;$i++) {
			$fieldorder[$i] = $i;
		}

		/* prepare meta_group select box.*/
		$address_data = new Address_data();
		$metagroups = $address_data->get_metagroups($id);
		foreach ($metagroups as $group) {
			$metagroup[$group['id']] = $group['name'];
		}

		/* convert the new tablenames to the oldones */
		if ($tablename == "relations") {
			$tablename = "adres";
		}

		/* start the output */
		$output = new Layout_output();
		$output->layout_page(gettext("extra"), 1);

		$venster_settings = array(
			"title"    => gettext("extra fields"),
			"subtitle" => gettext("add")
		);
		$venster = new Layout_venster($venster_settings);
		$venster->generateMenuItems();
		$venster->addVensterData();
			/* form */
			$venster->addTag("form", array(
				"id"     => "meta",
				"method" => "post",
				"action" => "index.php"
			));
			$venster->addHiddenField("mod", "metafields");
			$venster->addHiddenField("action", "save_add_field");
			$venster->addHiddenField("meta[tablename]", $tablename);
			$venster->addHiddenField("meta[record_id]", $record_id);
			/* put the form elements in a table so it looks ok */
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("name"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("meta[fieldname]", "", array("size"=>50));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("type"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("meta[fieldtype]", $fieldtypes, "");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("order"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("meta[fieldorder]", $fieldorder, "");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("group"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("meta[group]", $metagroup, "");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData("", "", "header");
				$table->addTableData("", "data");
					$table->insertAction("save", gettext("save"), "javascript: document.getElementById('meta').submit();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
			unset($table);
			$venster->endTag("form");
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* meta_format_field {{{ */
	/**
	 * Format metafield dataset to something you can use in a html document
	 *
	 * @param array the metafield information
	 * @return string the html you can include in a page
	 */
	public function meta_format_field($field_data) {
		if(is_array($field_data["value"])) {
			if ($_REQUEST["address_id"]) {
				$address_id = $_REQUEST["address_id"];
			} else {
				$address_id = $_REQUEST["id"];
			}
			/* check if relation id has an entry in meta_global */
			if($address_id != $field_data["value"]["relation_id"]) {
				$ret = "<input type=\"text\" style=\"width:90%;\" class=\"inputtext\" name=\"metafield[".$field_data["id"]."]\" value=\"\">\n";
				return $ret;
			}
			switch($field_data["fieldtype"]) {
				case 1: $ret = "<input type=\"text\" style=\"width:90%;\" class=\"inputtext\" name=\"metafield[".$field_data["id"]."]\" value=\"".$field_data["value"]["value"]."\">\n"; break;
				case 2: $ret = "<textarea name=\"metafield[".$field_data["id"]."]\" style=\"width:90%; height:90px;\" class=\"inputtextarea\">".$field_data["value"]["value"]."</textarea>\n"; break;
				case 3:
					$ret = "<select name=\"metafield[".$field_data["id"]."_day]\">\n";
					$ret .= "<option value=\"--\">--</option>";
					for ($i=1;$i<=31;$i++) {
						if (is_numeric($field_data["value"]["value"]) && date("d", $field_data["value"]["value"])==$i) {
							$ret .= "<option value=\"$i\" SELECTED>$i</option>\n";
						} else {
							$ret .= "<option value=\"$i\">$i</option>\n";
						}
					}
					$ret .= "</select>\n";
					$ret .= "<select name=\"metafield[".$field_data["id"]."_month]\">\n";
					$ret .= "<option value=\"--\">--</option>";
					for ($i=1;$i<=12;$i++) {
						if (is_numeric($field_data["value"]["value"]) && date("m", $field_data["value"]["value"])==$i) {
							$ret .= "<option value=\"$i\" SELECTED>$i</option>\n";
						} else {
							$ret .= "<option value=\"$i\">$i</option>\n";
						}
					}
					$ret .= "</select>\n";
					$ret .= "<select name=\"metafield[".$field_data["id"]."_year]\">\n";
					$ret .= "<option value=\"--\">--</option>";
					for ($i=date("Y",0)-50;$i<=date("Y")+5;$i++) {
						if (is_numeric($field_data["value"]["value"]) && date("Y", $field_data["value"]["value"])==$i) {
							$ret .= "<option value=\"$i\" SELECTED>$i</option>\n";
						} else {
							$ret .= "<option value=\"$i\">$i</option>\n";
						}
					}
					$ret .= "</select>\n";
					break;
				case 4:
					$ret = "<input type=\"radio\" name=\"metafield[".$field_data["id"]."]\" ";
					if ($field_data["value"]["value"] == "1")
						$ret .= "checked ";
					$ret .= "value=\"1\">".gettext("Yes");

					$ret .= "<input type=\"radio\" name=\"metafield[".$field_data["id"]."]\" ";
					if ($field_data["value"]["value"] != "1")
						$ret .= "checked ";
					$ret .= "value=\"0\">".gettext("No");
					break;
			}
		} else {
			switch($field_data["fieldtype"]) {
				case 1: $ret = "<input type=\"text\" style=\"width:90%;\" class=\"inputtext\" name=\"metafield[".$field_data["id"]."]\" value=\"".$field_data["value"]."\">\n"; break;
				case 2: $ret = "<textarea name=\"metafield[".$field_data["id"]."]\" style=\"width:90%; height:90px;\" class=\"inputtextarea\">".$field_data["value"]."</textarea>\n"; break;
				case 3:
					$ret = "<select name=\"metafield[".$field_data["id"]."_day]\">\n";
					$ret .= "<option value=\"--\">--</option>";
					for ($i=1;$i<=31;$i++) {
						$ret .= "<option value=\"$i\">$i</option>\n";
					}
					$ret .= "</select>\n";
					$ret .= "<select name=\"metafield[".$field_data["id"]."_month]\">\n";
					$ret .= "<option value=\"--\">--</option>";
					for ($i=1;$i<=12;$i++) {
						$ret .= "<option value=\"$i\">$i</option>\n";
					}
					$ret .= "</select>\n";
					$ret .= "<select name=\"metafield[".$field_data["id"]."_year]\">\n";
					$ret .= "<option value=\"--\">--</option>";
					for ($i=date("Y",0)-50;$i<=date("Y")+5;$i++) {
						$ret .= "<option value=\"$i\">$i</option>\n";
					}
					$ret .= "</select>\n";
					break;
				case 4:
					$ret = "<input type=\"radio\" name=\"metafield[".$field_data["id"]."]\" ";
					if ($field_data["value"] == "1")
						$ret .= "checked ";
					$ret .= "value=\"1\">".gettext("Yes");

					$ret .= "<input type=\"radio\" name=\"metafield[".$field_data["id"]."]\" ";
					if ($field_data["value"] != "1")
						$ret .= "checked ";
					$ret .= "value=\"0\">".gettext("No");
					break;
			}
		}
		return $ret;
	}
	/* }}} */
	/* meta_print_field {{{ */
	/**
	 * Make nice html output for displaying metafield content
	 *
	 * @param array The metafield information
	 * @return string The html to show
	 */
	public function meta_print_field($field_data) {
		if(is_array($field_data["value"])) {

		/* check if relation id has an entry in meta_global */
		if($_REQUEST["id"] != $field_data["value"]["relation_id"]) { return; }
			if ($field_data["value"]["value"]) {
				switch($field_data["fieldtype"]) {
					case 1: $ret = $field_data["value"]["value"];                break;
					case 2: $ret = nl2br($field_data["value"]["value"]);         break;
					case 3: $ret = date("d-m-Y", $field_data["value"]["value"]); break;
					case 4:
						if ($field_data["value"]["value"])
							$ret = gettext("Yes");
						else
							$ret = gettext("No");
						break;
				}
			}
				return $ret;
		}

		switch($field_data["fieldtype"]) {
			case 1: $ret = $field_data["value"];                break;
			case 2: $ret = nl2br($field_data["value"]);         break;
			case 3:
				if (empty($field_data["value"]) || (int)$field_data["value"] == 0)
					$ret = "";
				else
					$ret = date("d-m-Y", $field_data["value"]);
				break;
			case 4:
				if ($field_data["value"])
					$ret = gettext("Yes");
				else
					$ret = gettext("No");
				break;
		}
		return $ret;
	}
	/* }}} */
	/* meta_edit_field {{{ */
	/**
	 * edit meta field
	 *
	 * @param string The table link name
	 * @param int The record in the table that is linked
	 * @param int The metafield id
	 */
	public function meta_edit_field($tablename, $meta_id) {

	$address_data = new Address_data();
	$metafields = $address_data->get_global($meta_id);

	/* prepare som selectbox data arrays */
		$fieldtypes = array(
			1 => gettext("short text")."(".gettext("maximal 255 characters").")",
			2 => gettext("long text")."(".gettext("more then 255 characters possible").")",
			3 => gettext("date"),
			4 => gettext("yes/no")
		);
		$fieldorder = array();
		for ($i=-5;$i<=5;$i++) {
			$fieldorder[$i] = $i;
		}
		/* prepare meta_group select box.*/
		$address_data = new Address_data();
		$metagroups = $address_data->get_metagroups($id);
		foreach ($metagroups as $group) {
			$metagroup[$group['id']] = $group['name'];
		}

		/* convert the new tablenames to the oldones */
		if ($tablename == "relations") {
			$tablename = "adres";
		}
		/* start the output */
		$output = new Layout_output();
		$output->layout_page(gettext("extra"), 1);

		$venster_settings = array(
			"title"    => gettext("extra fields"),
			"subtitle" => gettext("edit")
		);
		$venster = new Layout_venster($venster_settings);
		$venster->generateMenuItems();
		$venster->addVensterData();
			/* form */
			$venster->addTag("form", array(
				"id"     => "meta",
				"method" => "post",
				"action" => "index.php"
			));
			$venster->addHiddenField("mod", "metafields");
			$venster->addHiddenField("action", "save_edit_field");
			$venster->addHiddenField("meta[tablename]", $tablename);
			$venster->addHiddenField("meta[record_id]", 0);
			$venster->addHiddenField("meta[id]", $meta_id);
			/* put the form elements in a table so it looks ok */
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("name"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("meta[fieldname]", $metafields[1]["fieldname"], array("size"=>50));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("type"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("meta[fieldtype]", $fieldtypes, array($metafields[1]["fieldtype"]));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("order"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("meta[fieldorder]", $fieldorder, array($metafields[1]["fieldorder"]));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("group"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("meta[group]", $metagroup, array($metafields[1]["group_id"]));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData("", "", "header");
				$table->addTableData("", "data");
					$table->insertAction("save", gettext("save"), "javascript: document.getElementById('meta').submit();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
			unset($table);
			$venster->endTag("form");
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */


	/* metagroup_add_field {{{ */
	/**
	 * show html to add a new group to a record
	 *
	 */
	public function metagroup_add_field() {
		$tablename = "meta_groups";
		/* start the output */
		$output = new Layout_output();
		$output->layout_page(gettext("extra"), 1);

		$venster_settings = array(
			"title"    => gettext("Meta Group"),
			"subtitle" => gettext("add")
		);
		$venster = new Layout_venster($venster_settings);
		$venster->addMenuItem(gettext("close"), "javascript: window.close();");
		$venster->generateMenuItems();
		$venster->addVensterData();
			/* form */
			$venster->addTag("form", array(
				"id"     => "metagroup",
				"method" => "post",
				"action" => "index.php"
			));
			$venster->addHiddenField("mod", "metafields");
			$venster->addHiddenField("action", "save_add_metagroup");
			$venster->addHiddenField("metagroup[tablename]", $tablename);
			$venster->addHiddenField("metagroup[record_id]", $record_id);
			/* put the form elements in a table so it looks ok */
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("name"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("metagroup[name]", "", array("size"=>50));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("description"), "", "header");
				$table->addTableData("", "data");
				$table->addTextarea("metagroup[description]", "", "");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData("", "", "header");
				$table->addTableData("", "data");
					$table->insertAction("save", gettext("save"), "javascript: document.getElementById('metagroup').submit();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();

			$venster->addCode($table->generate_output());
			unset($table);
			$venster->endTag("form");
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */

	/* metagroup_edit{{{ */
	/**
	 * show html to add a new group to a record
	 *
	 */
	public function metagroup_edit($id) {
		$address_data = new Address_data();
		$metagroups = $address_data->get_metagroups($id);

		$tablename == "meta_groups";
		/* start the output */
		$output = new Layout_output();
		$output->layout_page(gettext("extra"), 1);

		$venster_settings = array(
			"title"    => gettext("Meta Group"),
			"subtitle" => gettext("Edit")
		);
		$venster = new Layout_venster($venster_settings);
		$venster->addMenuItem(gettext("close"), "javascript: window.close();");
		$venster->generateMenuItems();
		$venster->addVensterData();
			/* form */
			$venster->addTag("form", array(
				"id"     => "metagroup",
				"method" => "post",
				"action" => "index.php"
			));
			$venster->addHiddenField("mod", "metafields");
			$venster->addHiddenField("action", "save_edit_metagroup");
			$venster->addHiddenField("metagroup[tablename]", $tablename);
			$venster->addHiddenField("metagroup[id]", $id);
			/* put the form elements in a table so it looks ok */
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("name"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("metagroup[name]", $metagroups[1]['name'], array("size"=>50));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("description"), "", "header");
				$table->addTableData("", "data");
				$table->addTextarea("metagroup[description]", $metagroups[1]['description'], "");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData("", "", "header");
				$table->addTableData("", "data");
					$table->insertAction("save", gettext("save"), "javascript: document.getElementById('metagroup').submit();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();

			$venster->addCode($table->generate_output());
			unset($table);
			$venster->endTag("form");
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
}
?>
