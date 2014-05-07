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
	if (!class_exists("Address_data")) {
		die("no class definition found");
	}

	$esc = sql_syntax("escape_char");

	// process businesscards into address_birthdays
	$q = "select id, timestamp_birthday from address_businesscards where (timestamp_birthday != 0 AND timestamp_birthday IS NOT NULL) AND id NOT IN (select bcard_id from address_birthdays where bcard_id IS NOT NULL)";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$field["timestamp"] = (int)$row["timestamp_birthday"];
		$field["day"]       = date("d", $row["timestamp_birthday"]);
		$field["month"]     = date("m", $row["timestamp_birthday"]);
		$field["year"]      = date("Y", $row["timestamp_birthday"]);
		$field["bcard_id"]  = $row["id"];

		$values = array();
		$keys = array();

		foreach ($field as $k=>$v) {
			$keys[] = $esc.$k.$esc;
			$values[] = $v;
		}
		$q = sprintf("insert into address_birthdays (%s) values (%s)",
			implode(", ", $keys), implode(", ", $values));
		sql_query($q);
		unset($field);
		unset($keys);
		unset($values);
	}
	// process relationcards into address_birthdays
	$q = "select id, contact_birthday from address where (contact_birthday != 0 AND contact_birthday is not NULL) AND id NOT IN (select address_id from address_birthdays WHERE address_id IS NOT NULL)";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$field["timestamp"]  = (int)$row["contact_birthday"];
		$field["day"]        = date("d", $row["contact_birthday"]);
		$field["month"]      = date("m", $row["contact_birthday"]);
		$field["year"]       = date("Y", $row["contact_birthday"]);
		$field["address_id"] = $row["id"];

		$values = array();
		$keys = array();

		foreach ($field as $k=>$v) {
			$keys[] = $esc.$k.$esc;
			$values[] = $v;
		}
		$q = sprintf("insert into address_birthdays (%s) values (%s)",
			implode(", ", $keys), implode(", ", $values));
		sql_query($q);
		unset($field);
		unset($keys);
		unset($values);
	}


	// grab businesscard birthdays
	$q = sprintf("select id, address_id, timestamp_birthday, givenname, infix, surname from address_businesscards where id IN (
		select bcard_id from address_birthdays where (
				%1\$sday%1\$s = %2\$d AND
				%1\$smonth%1\$s = %3\$d
			) OR (
				%1\$sday%1\$s = %4\$d AND
				%1\$smonth%1\$s = %5\$d
			) OR (
				%1\$sday%1\$s = %6\$d AND
				%1\$smonth%1\$s = %7\$d
			) OR (
				%1\$sday%1\$s = %8\$d AND
				%1\$smonth%1\$s = %9\$d
			) )
		", $esc, date("d"), date("m"),
			date("d", mktime(0,0,0,date("m"),date("d")+1,date("Y"))),
			date("m", mktime(0,0,0,date("m"),date("d")+1,date("Y"))),
			date("d", mktime(0,0,0,date("m"),date("d")+2,date("Y"))),
			date("m", mktime(0,0,0,date("m"),date("d")+2,date("Y"))),
			date("d", mktime(0,0,0,date("m"),date("d")+3,date("Y"))),
			date("m", mktime(0,0,0,date("m"),date("d")+3,date("Y")))
		);
	$res = sql_query($q);
	$bd = array();
	while ($row = sql_fetch_assoc($res)) {
		$companyname = $this->getAddressNameByID((int)$row["address_id"]);
		$fullname = $row["givenname"]." ".$row["infix"]." ".$row["surname"];
		$fullname = preg_replace("/ {2,}/si", " ", $fullname);
		#echo $fullname;

		$age = (int)(date("Y") - date("Y", $row["timestamp_birthday"]));

		$diff = mktime(
			date("H"),
			date("i"),
			date("s"),
			date("m", $row["timestamp_birthday"]),
			date("d", $row["timestamp_birthday"]),
			date("Y")) - mktime();

		$days = ceil($diff/24/60/60);

		$bd[$row["id"]] = array(
			"id"           => $row["id"],
			"company_id"   => $row["address_id"],
			"company_name" => $companyname,
			"timestamp"    => $row["timestamp_birthday"],
			"name"         => $fullname,
			"age"          => $age,
			"days"         => $days
		);
	}
	// grab relation birthdays
	$q = sprintf("select id, companyname, contact_birthday, contact_givenname, contact_infix, contact_surname from address where id IN (
		select address_id from address_birthdays where (
				%1\$sday%1\$s = %2\$d AND
				%1\$smonth%1\$s = %3\$d
			) OR (
				%1\$sday%1\$s = %4\$d AND
				%1\$smonth%1\$s = %5\$d
			) OR (
				%1\$sday%1\$s = %6\$d AND
				%1\$smonth%1\$s = %7\$d
			) OR (
				%1\$sday%1\$s = %8\$d AND
				%1\$smonth%1\$s = %9\$d
			) )
		", $esc, date("d"), date("m"),
			date("d", mktime(0,0,0,date("m"),date("d")+1,date("Y"))),
			date("m", mktime(0,0,0,date("m"),date("d")+1,date("Y"))),
			date("d", mktime(0,0,0,date("m"),date("d")+2,date("Y"))),
			date("m", mktime(0,0,0,date("m"),date("d")+2,date("Y"))),
			date("d", mktime(0,0,0,date("m"),date("d")+3,date("Y"))),
			date("m", mktime(0,0,0,date("m"),date("d")+3,date("Y")))
		);
	$res = sql_query($q);
	$bd1 = array();
	while ($row = sql_fetch_assoc($res)) {
		$companyname = $row["companyname"];
		$fullname = $row["contact_givenname"]." ".$row["contact_infix"]." ".$row["contact_surname"];
		$fullname = preg_replace("/ {2,}/si", " ", $fullname);

		$age = (int)(date("Y") - date("Y", $row["timestamp_birthday"]));

		$diff = mktime(
			date("H"),
			date("i"),
			date("s"),
			date("m", $row["contact_birthday"]),
			date("d", $row["contact_birthday"]),
			date("Y")) - mktime();

		$days = ceil($diff/24/60/60);

		$bd1[$row["id"]] = array(
			"id"           => $row["id"],
			"company_id"   => $row["id"],
			"company_name" => $companyname,
			"timestamp"    => $row["contact_birthday"],
			"name"         => $fullname,
			"age"          => $age,
			"days"         => $days
		);
	}
	$bd = $bd+$bd1;
?>
