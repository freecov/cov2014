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
	die("no class definition found.");
}

set_time_limit(60*60*2);

$filename             = $data["filename"];
$extra_classification = (int)$data["classi"];
$cols                 = $data["col"];
unset($data);
/* first get the data to import from the file */
$ok = strpos($filename, $GLOBALS["covide"]->temppath);
if ($ok === false) {
	die("Access is denied.");
}
$fp = fopen($filename, "r");
$data = unserialize(file_get_contents($filename));
fclose($fp);

foreach ($data as $k=>$v) {
	if (count($v) == 0) {
		unset($data[$k]);
	}
}
/* remove some columns, for relations */
if ($_REQUEST["target"] == "bcard") {
	foreach ($cols as $k=>$v) {
		/* Companyname */
		if ($v == "companyname") {
			$get_companyname[$k] = $v;
			unset($cols[$k]);
		} elseif (!$v) {
			unset($cols[$k]);
		}
	}
} else {
	foreach ($cols as $k=>$v) {
		if (!$v) {
			unset($cols[$k]);
		} else {
			/* some columns should go in a secord table */
			switch ($v) {
				case "warning" :
				case "comment" :
					$cols_info[$v][$k] = $k;
					unset($cols[$k]);
					break;
				/* done */
			}
		}
	}
}

/* add classification field if not exists */
if (!in_array("classification", $cols))
	$cols[-1] = "classification";

/* reset array keys */
reset($cols);
$cols_unique = array_unique($cols);

/*
//FIXME: this goes wrong!
if (count($cols_unique) && array_key_exists("-1", $cols_unique)) {
	// remove the file
	@unlink($filename);
	$output = new Layout_output();
	$output->layout_page("", 1);
	$output->addTag("h2");
	$output->addCode(gettext("Because you did not select any column to import nothing was done."));
	$output->endTag("h2");
	$output->addTag("br");
	$output->addCode(gettext("This window will be closed in 5 seconds."));
	$output->start_javascript();
		$output->addCode("opener.location.href = opener.location.href;");
		$output->addCode("setTimeout('window.close();', 5000);");
	$output->end_javascript();
	$output->layout_page_end();
	$output->exit_buffer();
	return false;
}
*/

/* default query for every csv record */
if ($_REQUEST["target"] == "bcard") {
	/* bcard query */
	$prequery = "INSERT INTO address_businesscards (".implode(",", $cols_unique).", address_id, multirel, modified) VALUES(";
	$postquery = mktime().")";
} else {
	/* address query */
	$prequery = "INSERT INTO address (".implode(",", $cols_unique).", tav, contact_person, is_public, is_active, is_company, modified, user_id) VALUES(";
	$postquery = "1, 1, 1, ".mktime().", ".$_SESSION["user_id"].")";
}

foreach ($data as $fld) {
	$nameinfo = array();
	$sql = "";
	unset($vals);
	$vals = array();

	/* foreach chosen column */
	foreach ($cols as $k=>$colname) {
		/* key position = field name data from array */
		$v = $fld[$k];
		/* do some sanity checks */
		if ($colname != "memo") {
			$v = $this->limit_import_field($v, 255);
		}
		$v = addslashes($v);
		switch ($colname) {
			case "debtor_nr":
				$v = (int)$v;
				break;
			case "is_supplier":
			case "is_customer":
				$v = ($v==1) ? 1:0;
				break;
			case "timestamp_birthday":
				$v = strtotime($v);
				break;
		}
		if (!$vals[$colname])
			$vals[$colname] = array();

		if (trim($v))
			$vals[$colname][] = trim($v);

		switch ($colname) {
			case "contact_initials" :
				$nameinfo["contact_initials"] = $v;
				break;
			case "contact_givenname" :
				$nameinfo["contact_givenname"] = $v;
				break;
			case "contact_infix" :
				$nameinfo["contact_infix"] = $v;
				break;
			case "contact_surname" :
				$nameinfo["contact_surname"] = $v;
				break;
			case "contact_letterhead" :
				$nameinfo["contact_letterhead"] = $v;
				break;
			case "contact_commencement" :
				$nameinfo["contact_commencement"] = $v;
				break;
			case "title" :
				$nameinfo["title"] = $v;
				break;
			case "suffix" :
				$nameinfo["suffix"] = $v;
				break;
		}
	}

	if ($_REQUEST["target"] == "bcard") {
		$q = $prequery;
	} else {
		$address_letterinfo = $this->generate_letterinfo($nameinfo);
		$vals["tav"] = $address_letterinfo["tav"];
		$vals["contact_person"] = $address_letterinfo["contact_person"];
		unset($address_letterinfo);
		/* build insert statement */
		$q = $prequery;
	}

	$classification_data = new Classification_data();
	$classifications = $classification_data->getClassifications("", 1);
	$cla_list = array();

	foreach ($classifications as $c) {
		$cla_list[$c["id"]] = trim(strtolower($c["description"]));
	}

	foreach ($vals as $col=>$x) {
		if (!is_array($x))
			$x = array($x);

		switch ($col) {
			case "classification":
				/* lookup this classification */
				$zz = array();
				foreach ($x as $z) {
					if (!in_array(trim(strtolower($z)), $cla_list)) {
						$cdata["description"] = trim($z);
						$cdata["is_active"] = 1;
						$z = $classification_data->store2db($cdata);
					} else {
						$z = array_search(trim(strtolower($z)), $cla_list);
					}
					/* add to list */
					$zz[] = $z;
				}
				//add the extra classification (if any)
				if ($extra_classification)
					$zz[] = $extra_classification;

				$x = implode("|", $zz);

				//save for later use
				$classification_ids = $x;
				break;
			default:
				/* implode by semicolon ; */
				$x = implode("; ", $x);
				break;
		}
		$q .= sprintf("'%s', ", $x);
	}

	if ($_REQUEST["target"] == "bcard" && is_array($get_companyname)) {
		$company_ids = array();
		$first = "";
		foreach ($get_companyname as $g=>$v) {
			$name = strtolower($fld[$g]);
			$cp = sprintf("select id from address where lower(companyname) like '%s'", addslashes($name));
			$res = sql_query($cp);
			while ($row = sql_fetch_assoc($res)) {
				$company_ids[] = $row["id"];
			}
		}
		if (count($company_ids) > 0)
			$first = array_shift($company_ids);
		else
			$first = 0;

		$q.= sprintf("%d, ", $first);
		$q.= sprintf("'%s', ", implode(",", $company_ids));
	}	elseif ($_REQUEST["target"] == "bcard") {
		$q.= "0, '', ";
	}

	$q .= $postquery;
	sql_query($q);

	if ($_REQUEST["target"] == "bcard" && $first) {
		$this->updateBcardRelations(sql_insert_id("address_businesscards"), $first, $company_ids);
	}

	/* prepare stuff for addressinfo table */
	if ($_REQUEST["target"] != "bcard") {
		$inserted_id = sql_insert_id("address");
		$q_info  = "INSERT INTO address_info (address_id, classification, warning, comment) VALUES ";
		$warning = array();
		$comment = array();

		if (is_array($cols_info)) {
			foreach ($cols_info as $a=>$b) {
				if ($a == "warning") {
					foreach ($b as $aa) {
						if ($fld[$aa])
							$warning[] = $fld[$aa];
					}
				}
				if ($a == "comment") {
					foreach ($b as $aa) {
						if ($fld[$aa])
							$comment[] = $fld[$aa];
					}
				}
			}
		}
		$warning = implode("; ", $warning);
		$comment = implode("\n---\n", $comment);

		$warning = $this->limit_import_field($warning, 255);
		$comment = addslashes($comment);

		$q_info .= sprintf("(%d, '%s', '%s', '%s')",
			$inserted_id, $classification_ids, $warning, $comment);

		sql_query($q_info);
		// generate RCBC
		$tmpaddressinfo = $this->getAddressByID($inserted_id, "relations", "kantoor", 1);
		$this->checkrcbc($tmpaddressinfo);
	}
}

/* remove the file */
@unlink($filename);
?>
