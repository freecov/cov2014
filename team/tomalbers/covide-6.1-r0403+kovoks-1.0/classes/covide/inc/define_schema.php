<?php

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
