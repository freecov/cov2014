<?php
/**
 * Covide Table object
 *
 * Table interface class.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
class Layout_table Extends Layout_output {
	/* variables */
	var $_output = "";

	/* methods   */

	/* __construct {{{ */
	/**
	 * __construct. Constructor with default settings for this class
	 *
	 * Set defaults for the table object
	 *
	 * @param settings Array with custom settings for this table
	 */
	public function __construct($settings="", $border=0) {
		if (!is_array($settings)) {
			$settings = array();
		}

		if (!$settings["cellpadding"]) {
			$settings["cellpadding"] = 0;
		}
		if (!$settings["cellspacing"]) {
			$settings["cellspacing"] = 0;
		}
		if (!$settings["border"]) {
			$settings["border"] = 0;
		}
		if ($border) {
			$settings["class"] .= " table_data";
			$settings["class"] = trim($settings["class"]);
		}

		$this->addComment("begin table object");
		$this->addTag("table", $settings);
	}
	/* }}} */
	/* createEmptyTable {{{ */
	/**
	 * Create tablerow, one tablefield with content, close table and return output
	 *
	 * @param string The content to put in the tablecell
	 * @return string HTML code to attach to output buffer
	 */
	public function createEmptyTable($data) {
		$this->addTableRow();
		$this->insertTableData($data);
		$this->endTableRow();
		$this->endTable();
		return $this->generate_output();
	}
	/* }}} */
	/* addTableRow {{{ */
	/**
	 * Add a <tr> with optional settings
	 *
	 * @param array tr tag attributes
	 * @param int Hide in mobile modus if 1
	 */
	public function addTableRow($settings="", $no_mobile=0) {
		if ($GLOBALS["covide"]->mobile && $no_mobile) {
			$this->block_output = 1;
		}
		$this->addTag("tr", $settings);
	}
	/* }}} */
	/* endTableRow {{{ */
	/**
	 * Add a </tr>
	 */
	public function endTableRow() {
		$this->block_output = 0;
		$this->endTag("tr");
	}
	/* }}} */
	/* addTableHeader {{{ */
	/**
	 * Add a <th> with optional settings
	 *
	 * @param array th tag attributes
	 */
	public function addTableHeader($settings="") {
		$this->addTag("th", $settings);
	}
	/* }}} */
	/* endTableHeader {{{ */
	/**
	 * Add a </th>
	 */
	public function endTableHeader() {
		$this->endTag("th", $settings);
	}
	/* }}} */
	/* insertTableHeader {{{ */
	/**
	 * Add a <th>, put content in it and add </th>
	 *
	 * @param string The content to put in the table header
	 * @param array th tag attributes
	 */
	public function insertTableHeader($code, $settings="") {
		$this->addTableHeader($settings);
		$this->addCode($code);
		$this->endTableHeader();
	}
	/* }}} */
	/* addTableData {{{ */
	/**
	 * Add a <td> with optional settings and style.
	 * style is simply another style class, but we use them mainly in data tables with header/data
	 *
	 * @param array td tag attributes
	 * @param string extra style class
	 */
	public function addTableData($settings="", $style=0) {
		if ($style) {
			if (!is_array($settings)) {
				$settings = array();
			}
			if ($settings["class"]) {
				$class = explode(" ", $settings["class"]);
			}
			if (!is_array($style)) {
				$style = explode(" ", $style);
			}
			foreach ($style as $s) {
				if ($s == "header") {
					$class[]= "list_header";
				}
				if ($s == "data") {
					$class[]= "list_data";
				}
				if ($s == "datatop") {
					$class[]= "list_data_top";
				}
				if ($s == "nowrap") {
					$class[]="nowrap";
				}
				if ($s == "bold") {
					$class[]="bold";
				}
				if ($s == "top") {
					$class[]="valign_top";
				}
			}
			$class = array_unique($class);
			$settings["class"] = implode(" ", $class);
		}
		$this->addTag("td", $settings);
	}
	/* }}} */
	/* endTableData {{{ */
	/**
	 * Add a </td>
	 */
	public function endTableData() {
		$this->endTag("td");
	}
	/* }}} */
	/* insertTableData {{{ */
	/**
	 * Add a <td>, put some content in it, add a </td>
	 *
	 * @param string The content to use
	 * @param array td tag attributes
	 * @param string additional style class to use
	 */
	public function insertTableData($code, $settings="", $style=0) {
		$this->addTableData($settings, $style);
		$this->addCode($code);
		$this->endTableData();
	}
	/* }}} */
	/* endTable {{{ */
	/**
	 * Add a </table>
	 */
	public function endTable() {
		$this->endTag("table");
		$this->addComment("end table object");
	}
	/* }}} */
}
