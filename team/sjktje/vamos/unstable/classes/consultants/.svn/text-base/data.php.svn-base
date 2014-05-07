<?php
/*
 *  Copyright (C) 2006 Svante Kvarnstrom
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307
 *  USA
 */

class Consultants_data {
    const include_dir = "classes/consultants/inc";
	/* {{{ getConsultants */
	/** 
	 * Retrieves consultant information from database.
	 *
	 * This function takes an array with options with the following structure:
	 *
	 * Array(
	 *    "id"                => $id,
	 *    "sort"              => $sort,
	 *    "top"               => $top
	 *    "search"            => Array(
	 *        "firstname"         => $firstname,
	 *        "surname"           => $surname,
	 *        "email"             => $email,
	 *        "prescription_code" => $prescription_code,
	 *        "city"              => $city
	 *     )
	 * ); 
	 *
	 * @param array $options Array with options
	 * @return array Array with consultant information.
	 */
	public function getConsultants($options=array()) {
		if ($options["id"] >= 1) {
			/* Fetch info about consultant $id */
			$consultant = Array();
			//$sql = sprintf("SELECT * FROM nrd_consultant WHERE id=%s", $options["id"]);
			$sql = sprintf("SELECT id,title,surname,firstname,ssn,employee_nr,prescription_code,".
				"address,address2,zipcode,city,country,email,phone_nr,mobile_nr,fax_nr,clearing_nr,".
				"account_nr,iban_nr,tax,is_interested_in_oncall_duty,is_beingchecked,is_blacklisted,warning,other ".
				"FROM nrd_consultant WHERE id=%s", $options["id"]);
			$res = sql_query($sql);
			$consultant = sql_fetch_assoc($res);
			return $consultant;
		} else {
			/* Else just display the whole list of consultants */
			$consultants = Array();
			/*
			 * We must honor the search requests. The database wont like us if we give it 
			 * lots of "LIKE '%%'"'s, so lets filter those out. Some databases use weird
			 * LIKE syntax, so let's use sql_syntax() to be sure we're doing it right.
			 */
			$like_syntax = sql_syntax("like");

			$firstname         = $options["search"]["firstname"]         ? " AND firstname $like_syntax '%".$options["search"]["firstname"]."%'" : "";
			$surname           = $options["search"]["surname"]           ? " AND surname $like_syntax '%".$options["search"]["surname"]."%'" : "";
			$email             = $options["search"]["email"]             ? " AND email $like_syntax '%".$options["search"]["email"]."%'" : "";
			$prescription_code = $options["search"]["prescription_code"] ? " AND prescription_code $like_syntax '%".$options["search"]["prescription_code"]."%'" : "";
			$city              = $options["search"]["city"]              ? " AND city $like_syntax '%".$options["search"]["city"]."%'" : "";
			$competence        = $options["search"]["competence"]        ? " AND t2.consultant_id = t1.id AND t3.id = t2.competence_id AND t3.name $like_syntax '%".$options["search"]["competence"]."%'" : "";
			$extra_from        = $options["search"]["competence"]        ? ", nrd_consultant_competence as t2, nrd_consultant_competence_type as t3" : "";
			$is_beingchecked   = $options["search"]["is_beingchecked"]   ? " AND is_beingchecked = '1'" : " AND is_beingchecked != '1'";

			$filter = "$firstname $surname $email $prescription_code $city $is_beingchecked $competence";

			$sql = "SELECT DISTINCT(t1.id) as id,firstname,surname,city,email,phone_nr,mobile_nr,prescription_code FROM nrd_consultant as t1 $extra_from WHERE 1=1 $filter";

			$sql_count = "SELECT COUNT(DISTINCT(t1.id)) FROM nrd_consultant as t1 $extra_from WHERE 1=1 $filter";
			$sql_count .= "$firstname $surname $email $prescription_code $city $is_beingchecked";
			if (!empty($options["sort"])) { 
				$order = sql_filter_col($options["sort"]);
				$sql .= " ORDER BY $order";
			}
			/* get the total amount of matches to our search */
			$res_count = sql_query($sql_count);
			$count = sql_result($res_count, 0);
			$return["total_count"] = $count;

			/* We only want to return a certain subset of data (from x .. y) */
			$res = sql_query($sql, "", $options["top"], $GLOBALS["covide"]->pagesize);
			while ($row = sql_fetch_assoc($res)) {
				$consultants[] = $row;
			}
			$return["data"] = $consultants;
			return $return;
		}
	} 
	/* }}} */
	/* {{{ getCategories */
	/**
	 * 
	 * Returns full category list or selected categories.
	 *
	 * If given a consultant id, this function will look up the categories selected
	 * for that particular consultant. If no id was given, the whole list of available
	 * categories will be returned. 
	 *
	 * @param int id - id of consultant
	 * @return array Array of either selected categories, or all categories.
	 */
	public function getCategories($id=0) {
		if ($id > 0) {
			$selected = array();
			$sql = sprintf("SELECT category_id FROM nrd_consultant_category WHERE consultant_id='%d'", $id);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) 
				$selected[] = $row["category_id"];
			return $selected; 
		} else {
			$categories = array();
			$sql = "SELECT id,name FROM nrd_consultant_category_type";
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res))
				$categories[$row["id"]] = $row["name"];
			return $categories;
		}
	} 
	/* }}} */
	/* {{{ getCompetence */
	/**
	 * 
	 * Returns full competence list or selected competence areas.
	 *
	 * If given a consultant id, this function will look up the competences 
	 * selected for that particular consultant. If no id was given, the whole 
	 * list of available competence areas will be returned.
	 *
	 * @param int id - id of consultant
	 * @return array Array of either selected competence areas or all of them.
	 */
	public function getCompetence($id=0) {
		if ($id > 0) {
			$selected = array();
			$sql = sprintf("SELECT competence_id FROM nrd_consultant_competence WHERE ".
				"consultant_id='%d'", $id);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) 
				$selected[] = $row["competence_id"];
			return $selected;
		} else {
			$competence = array();
			$sql = "SELECT id,name,code FROM nrd_consultant_competence_type";
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$competence[$row["id"]] = $row["code"]." - ".$row["name"];
			}
			return $competence;
		}
	} 
	/* }}} */
	/* {{{ getConsultantCompanies() */
	/**
	 * 
	 * Returns list of companies/customers connected to specified consultant id.
	 *
	 * @param int id Id of consultant.
	 * @return array List of companies linked to consultant
	 */
	public function getConsultantCompanies($id) {
		$companies = array();
		$sql = sprintf("SELECT nrd_customer.company_name,nrd_customer.id FROM nrd_consultant_company,nrd_customer ".
			"WHERE nrd_consultant_company.consultant_id=%d AND nrd_consultant_company.customer_id=nrd_customer.id",
			$id);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res))
			$companies[] = $row;
		return $companies;
	} 
	/* }}} */
	/* {{{ saveConsultant 
	 *
	 * This function will save or update a consultant entry. We use hidden fields
	 * in the forms to determine if we should UPDATE or INSERT.
	 *
	 * in: $_POST from consultant form.
	 * out: no return value.
	 */
	public function saveConsultant($data) {
		if ($data["consultants"]["firstname"] != '' && $data["consultants"]["surname"] != '') {
			/* If $data["consultants"]["id"], update the table row with that id. */
			if ($data["consultants"]["id"]) {
				/* {{{ update consultant */
				$sql = sprintf("UPDATE nrd_consultant SET surname='%s', firstname='%s', ssn='%s',".
					"employee_nr='%s', prescription_code='%s', address='%s', address2='%s', zipcode='%s', city='%s',".
					"country='%s', email='%s', phone_nr='%s', mobile_nr='%s', fax_nr='%s', clearing_nr='%s',".
					"account_nr='%s', iban_nr='%s', tax='%s', is_interested_in_oncall_duty='%s', is_beingchecked='%s', is_blacklisted='%s',".
					"other='%s' WHERE id='%s'", $data["consultants"]["surname"],
					$data["consultants"]["firstname"], $data["consultants"]["ssn"], 
					$data["consultants"]["employee_nr"], $data["consultants"]["prescription_code"],
					$data["consultants"]["address"], $data["consultants"]["address2"], $data["consultants"]["zipcode"], 
					$data["consultants"]["city"], $data["consultants"]["country"],
					$data["consultants"]["email"], $data["consultants"]["phone_nr"], 
					$data["consultants"]["mobile_nr"], $data["consultants"]["fax_nr"],
					$data["consultants"]["clearing_nr"], $data["consultants"]["account_nr"],
					$data["consultants"]["iban_nr"], $data["consultants"]["tax"], 
					$data["consultants"]["is_interested_in_oncall_duty"], $data["consultants"]["is_beingchecked"], 
					$data["consultants"]["is_blacklisted"],
					$data["consultants"]["other"], $data["consultants"]["id"]);
				$res = sql_query($sql);
				/* 
				 * Clear consultants added categories and readd them (in case there's
				 * been a change.) 
				 *
				 * TODO: Check if there's actually been any change to the category
				 * selection - these INSERTs may be unnecessary.
				 */
				$sql = sprintf("DELETE FROM nrd_consultant_category WHERE consultant_id='%d'", 
					$data["consultants"]["id"]);
				sql_query($sql);

				if (is_array($data["consultants"]["categories"])) {
					foreach ($data["consultants"]["categories"] as $cat) {
						$sql = sprintf("INSERT INTO nrd_consultant_category (consultant_id,category_id) ".
							 "VALUES ('%s','%s')", $data["consultants"]["id"], $cat);
						sql_query($sql);
					}
				}

				/* 
				 * Clear consultants added competence areas and readd them (in case
				 * there's been a change.)
				 *
				 * TODO: Check if there's actually been any change - these INSERTs
				 * can in many cases be unnecessary.
				 */
				$sql = sprintf("DELETE FROM nrd_consultant_competence WHERE consultant_id='%d'",
					 $data["consultants"]["id"]);
				sql_query($sql);

				if (is_array($data["consultants"]["competence"])) {
					foreach ($data["consultants"]["competence"] as $comp) {
						$sql = sprintf("INSERT INTO nrd_consultant_competence (consultant_id,competence_id) ".
							"VALUES ('%s','%s')", $data["consultants"]["id"], $comp);
						sql_query($sql);
					}
				}
				/*
				 * If user's filled in the log text field, submit the log entry. 
				 */
				if ($data["consultants"]["log"]) {
					$logbook_data = new Logbook_data();
					$logparams = Array(
						"module"    => "consultants", /* Why are we not using $data["consultants"]["mod"] here? */
						"record_id" => $data["consultants"]["id"],
						"message"   => $data["consultants"]["log"]
					);
					$logbook_data->addLogEntry($logparams);
				}
			/* }}} */
			} else {
				/* {{{ add new consultant */
				$sql = sprintf("INSERT INTO nrd_consultant (surname,firstname,ssn,".
					"employee_nr,prescription_code,address,address2,zipcode,city,country,email,phone_nr,".
					"mobile_nr,fax_nr,pincode,clearing_nr,account_nr,iban_nr,tax,is_interested_in_oncall_duty,".
					"is_beingchecked, is_blacklisted,other) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s',".
					"'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
					$data["consultants"]["surname"], 
					$data["consultants"]["firstname"], $data["consultants"]["ssn"], 
					$data["consultants"]["employee_nr"], $data["consultants"]["prescription_code"],
					$data["consultants"]["address"], $data["consultants"]["address2"], $data["consultants"]["zipcode"], 
					$data["consultants"]["city"], $data["consultants"]["country"],
					$data["consultants"]["email"], $data["consultants"]["phone_nr"], 
					$data["consultants"]["mobile_nr"], $data["consultants"]["fax_nr"], 
					$data["consultants"]["pincode"], $data["consultants"]["clearing_nr"], $data["consultants"]["account_nr"],
					$data["consultants"]["iban_nr"], $data["consultants"]["tax"],
					$data["consultants"]["is_interested_in_oncall_duty"], $data["consultants"]["is_beingchecked"],
					$data["consultants"]["is_blacklisted"], $data["consultants"]["other"]);
				$res = sql_query($sql);

				/* 
				 * Since we're adding a new consultant we don't know of the id (yet). Let's 
				 * fetch the id by using sql_insert_id();
				 */
				$data["consultants"]["id"] = sql_insert_id("nrd_consultant");

				/* Add categories */
				if (is_array($data["consultants"]["categories"])) {
					foreach ($data["consultants"]["categories"] as $cat) {
						$sql = sprintf("INSERT INTO nrd_consultant_category (consultant_id,category_id) ".
							"VALUES ('%s','%s')", $data["consultants"]["id"], $cat);
						sql_query($sql);
					}
				}

				/* Add competence list */
				if (is_array($data["consultants"]["competence"])) {
					foreach ($data["consultants"]["competence"] as $comp) {
						$sql = sprintf("INSERT INTO nrd_consultant_competence (consultant_id,competence_id) ".
							"VALUES ('%s','%s')", $data["consultants"]["id"], $comp);
						sql_query($sql);
					}
				}

				if ($data["consultants"]["log"]) {
					$logbook_data = new Logbook_data();
					$logbook_data->addLogEntry(Array(
						"module"    => $data["consultants"]["mod"],
						"record_id" => $data["consultants"]["id"],
						"message"   => $data["consultants"]["log"]
					));
				}
				/* }}} */
			}
			/* {{{ PEAR::isError test */
			if (PEAR::isError($res))
				return false;
			else
				return true;
			/* }}} */
		} else {
			return true;
		}
	} /* }}} */
}
?>
