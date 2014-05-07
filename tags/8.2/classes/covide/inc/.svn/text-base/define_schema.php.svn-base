<?php
	/**
	 * Covide Groupware-CRM Core database module
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

	if (!class_exists("Covide_db")) {
		exit("no class definition found");
	}

	/* table_versions table */
	$this->addField("table_versions", array(
		"column"     => "id",
		"type"       => "int",
		"length"     => 11,
		"not_null"   => 1,
		"is_primary" => 1
	));
	$this->addField("table_versions", array(
		"column"     => "table",
		"type"       => "varchar",
		"length"     => 255,
		"not_null"   => 1
	));
	$this->addField("table_versions", array(
		"column"     => "hash",
		"type"       => "varchar",
		"length"     => 255,
		"not_null"   => 1
	));
	$this->addField("table_versions", array(
		"column"     => "updated",
		"type"       => "varchar",
		"length"     => 255,
		"not_null"   => 1
	));

?>
