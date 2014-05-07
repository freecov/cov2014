<?php

class Customers_data {
	const include_dir = "classes/customers/inc";

	/* {{{ deleteContact() */
	/**
	 * Delete contact
	 *
	 * Removes contact from database.
	 *
	 * @param int $id Id of contact to remove.
	 * @return true if no error occured, false otherwise.
	 */
	public function deleteContact($id) {
		if ($id > 0) {
			$sql = sprintf("DELETE FROM nrd_contact WHERE id=%d", $id);
			sql_query($sql);
		}
		if (PEAR::isError($res))
			return false;
		else
			return true;
	} /* }}} */
	/* {{{ getContacts() */
	/**
	 * Retrieves contact information from database.
	 *
	 * This function takes an array with options with the following structure:
	 *
	 * Array(
	 *   "id"          => $id,
	 *   "customer_id" => $customer_id
	 * )
	 *
	 * @param array $options Array with options
	 * @return array Array with contact information.
	 */
	public function getContacts($options=array()) {
		if ($options["id"] >= 1) {
			/* Fetch info about contact $id */
			$contact = Array();
			$sql = sprintf("SELECT * FROM nrd_contact WHERE id=%d", $options["id"]);
			$res = sql_query($sql);
			$contact = sql_fetch_assoc($res);
			return $contact;
		} else {
			/* Else just display the whole list of customers */
			$contacts = Array();
			$sql = sprintf("SELECT firstname,surname,id,title,email FROM nrd_contact WHERE customer_id=%d", 
				$options["customer_id"]);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) 
				$contacts[] = $row;
			return $contacts;
		}
	} /* }}} */
	/* {{{ getCustomersByType() */
	/**
	 * Retrieves customer information from database.
	 * 
	 * This function will return a list of customers that have a specific type 
	 * id (and optionally a specific consultant id -- yes, consultant id!)
	 *
	 * @param array $options Array of options.
	 * @return array Array with customer information.
	 */
	public function getCustomersByType($options=array()) {
		$sql = "SELECT * FROM nrd_customer WHERE 1";
		$sql .= $options["type"] ? sprintf(" AND type=%d", $options["type"]) : "";
		$sql .= $options["consultant_id"] ? sprintf(" AND consultant_id=%d", $options["consultant_id"]) : "";
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res))
			$companies[] = $row;
		return $companies;
	} /* }}} */
	/* {{{ getCustomers() */
	/**
	 * Retrieves customer information from database.
	 *
	 * This function takes an array with option with the following structure:
	 *
	 * Array(
	 *   "id"   => $id,
	 *   "sort" => $sort,
	 *   "top"  => $list_from,
	 *   "search" => Array(
	 *       "company_name"    => $company_name,
	 *       "organisation_nr" => $organisation_nr,
	 *       "customer_nr"     => $customer_nr
	 *       "city"            => $city,
	 *   )
	 * );
	 *
	 * @param array $options Array with options
	 * @return array Array with customer information.
	 */
	public function getCustomers($options=array()) {
		if ($options["id"] >= 1) {
			/* Fetch info about customer $id */
			$customer = Array();
			$sql = sprintf("SELECT * FROM nrd_customer WHERE id=%d", $options["id"]);
			$res = sql_query($sql);
			$customer = sql_fetch_assoc($res);
			return $customer;
		} else {
			/* Else just display the whole list of customers */
			$customers = Array();
			/*
			 * We must honor the search requests. The database wont like us if we give it 
			 * lots of "LIKE '%%'"'s, so lets filter those out. Some databases use weird
			 * LIKE syntax, so let's use sql_syntax() to be sure we're doing it right.
			 */
			$like_syntax = sql_syntax("like");

			$company_name = $options["search"]["company_name"] ? " AND company_name $like_syntax '%".$options["search"]["company_name"]."%'" : "";
			$organisation_nr = $options["search"]["organisation_nr"] ? " AND organisation_nr $like_syntax '%".$options["search"]["organisation_nr"]."%'" : "";
			$city = $options["search"]["city"] ? " AND city $like_syntax '%".$options["search"]["city"]."%'" : "";
			$customer_nr = $options["search"]["customer_nr"] ? " AND customer_nr $like_syntax '%".$options["search"]["customer_nr"]."%'" : "";

			$filter = "type=1 $company_name $organisation_nr $city $customer_nr";

			$sql = "SELECT * FROM nrd_customer WHERE $filter";

			$sql_count = "SELECT COUNT(*) FROM nrd_customer WHERE $filter";
			if (!empty($options["sort"])) {
				$order = sql_filter_col($options["sort"]);
				$sql .= " ORDER BY $order";
			}

			/* Get the total amount of matches to our search */
			$res_count = sql_query($sql_count);
			$count = sql_result($res_count, 0);
			$return["total_count"] = $count;

			/* We only want to return a certain subset of data (from x .. y) */
			$res = sql_query($sql, "", $options["list_from"], $GLOBALS["covide"]->pagesize);

			while ($row = sql_fetch_assoc($res)) 
				$customers[] = $row;
			$return["data"] = $customers;
			return $return;
		}
	} /* }}} */
	/* {{{ saveContact() */
	/**
	 * Saves contact information.
	 *
	 * This function will save or update a contact entry. We use hidden fields
	 * in the forms to determine if we should UPDATE or INSERT.
	 *
	 * @param array $data $_POST of the customer form.
	 * @return no return value
	 */
	 /* TODO: We might want to set modified col with a unix timestamp here. */
	public function saveContact($data) {
		/* If id is set, update the table row with that id. */
		if ($data["contact"]["id"]) {
		
			$sql = sprintf("UPDATE nrd_contact SET firstname='%s', surname='%s', ".
				"title='%s', email='%s', address='%s', address2='%s', zipcode='%s', ".
				"city='%s', country='%s', phone_nr='%s', mobile_nr='%s', other='%s', ".
				"pobox='%s', pobox_zipcode='%s', pobox_city='%s' WHERE id='%d'", 
				$data["contact"]["firstname"], $data["contact"]["surname"], 
				$data["contact"]["title"], $data["contact"]["email"], 
				$data["contact"]["address"], $data["contact"]["address2"],
				$data["contact"]["zipcode"], $data["contact"]["city"],
				$data["contact"]["country"], $data["contact"]["phone_nr"], 
				$data["contact"]["mobile_nr"], $data["contact"]["other"],
				$data["contact"]["pobox"], $data["contact"]["pobox_zipcode"],
				$data["contact"]["pobox_city"], $data["contact"]["id"]);
			sql_query($sql);
		} else {
			/*
			 * If not, add new contact. 
			 * We depend on $data["contact"]["customer_id"] here.
			 */
			$sql = sprintf("INSERT INTO nrd_contact SET firstname='%s', surname='%s', ".
				"title='%s', email='%s', address='%s', address2='%s', zipcode='%s', ".
				"city='%s', country='%s', phone_nr='%s', mobile_nr='%s', other='%s', ".
				"pobox='%s', pobox_zipcode='%s', pobox_city='%s', customer_id='%d'", 
				$data["contact"]["firstname"], $data["contact"]["surname"], 
				$data["contact"]["title"], $data["contact"]["email"], 
				$data["contact"]["address"], $data["contact"]["address2"],
				$data["contact"]["zipcode"], $data["contact"]["city"],
				$data["contact"]["country"], $data["contact"]["phone_nr"], 
				$data["contact"]["mobile_nr"], $data["contact"]["other"],
				$data["contact"]["pobox"], $data["contact"]["pobox_zipcode"],
				$data["contact"]["pobox_city"], $data["customers"]["id"]);
			sql_query($sql);
		}

		if (PEAR::isError($res))
			return false;
		else
			return true;
	} /* }}} */
	/* {{{ saveCustomer() */
	/**
	 * Saves customer information.
	 *
	 * This function will save or update a consultant entry. We use hidden fields
	 * in the forms to determine if we should UPDATE or INSERT.
	 *
	 * @param array $data $_POST of the customer form
	 * @return no return value
	 */
	public function saveCustomer($data) {
		/* If $data["customers"]["id"] is set, update the table row with that id. */
		if ($data["customers"]["id"] && $data["customers"]["company_name"]) {
			/* update */
			$sql = sprintf("UPDATE nrd_customer SET company_name='%s', expense_nr='%s', ".
				"organisation_nr='%s', customer_nr='%s', address='%s', address2='%s', ".
				"zipcode='%s', city='%s', country='%s', quickfacts='%s', other='%s', ".
				"fax_nr='%s', telephone_nr='%s', website='%s', billing_address='%s' ".
				"WHERE id='%d'",
				$data["customers"]["company_name"], $data["customers"]["expense_nr"],
				$data["customers"]["organisation_nr"], $data["customers"]["customer_nr"],
				$data["customers"]["address"], $data["customers"]["address2"],
				$data["customers"]["zipcode"], $data["customers"]["city"],
				$data["customers"]["country"], $data["customers"]["quickfacts"], 
				$data["customers"]["other"], $data["customers"]["fax_nr"],
				$data["customers"]["telephone_nr"], $data["customers"]["website"],
				$data["customers"]["billing_address"], $data["customers"]["id"]);
			sql_query($sql);

			/*
			 * If user's filled in the log text field, submit the log entry. We should filter
			 * out spaces etc before doing this check so that we don't submit log entries only
			 * containing a couple of spaces
			 */
			if ($data["customers"]["log"]) {
				$logbook_data = new Logbook_data();
				$logparams = Array(
					"module"    => "customers", 
					"record_id" => $data["customers"]["id"],
					"message"   => $data["customers"]["log"]
				);
				$logbook_data->addLogEntry($logparams);
			}
		} elseif (!$data["customers"]["id"] && $data["customers"]["company_name"]) { 
			$sql = sprintf("INSERT INTO nrd_customer SET company_name='%s', expense_nr='%s', ".
				"organisation_nr='%s', customer_nr='%s', address='%s', address2='%s', ".
				"zipcode='%s', city='%s', country='%s', quickfacts='%s', other='%s', ".
				"billing_address='%s', telephone_nr='%s', fax_nr='%s', website='%s', ".
				"type=%d",
				$data["customers"]["company_name"], $data["customers"]["expense_nr"],
				$data["customers"]["organisation_nr"], $data["customers"]["customer_nr"],
				$data["customers"]["address"], $data["customers"]["address2"],
				$data["customers"]["zipcode"], $data["customers"]["city"],
				$data["customers"]["country"], $data["customers"]["quickfacts"], 
				$data["customers"]["other"], $data["customers"]["billing_address"],
				$data["customers"]["telephone_nr"], $data["customers"]["fax_nr"],
				$data["customers"]["website"], $data["customers"]["type"]);
			$res = sql_query($sql);

			if ($data["customers"]["consultant_id"]) {
				$last_insert_id = sql_insert_id("nrd_customer");
				$this->linkConsultantCompany($data["customers"]["consultant_id"],$last_insert_id);
			}

			/*
			 * If user's filled in the log text field, submit the log entry. We should filter
			 * out spaces etc before doing this check so that we don't submit log entries only
			 * containing a couple of spaces
			 */
			if ($data["customers"]["log"]) {
				$logbook_data = new Logbook_data();
				$logparams = Array(
					"module"    => $data["customers"]["mod"],
					"record_id" => $data["customers"]["id"],
					"message"   => $data["customers"]["log"]
				);
				$logbook_data->addLogEntry($logparams);
			}
		}
		if (PEAR::isError($res))
			return false;
		else
			return true;
	} /* }}} */
	/* {{{ searchCustomer() /*
	/**
	 * Search customer and return customer name and id. 
	 *
	 * @param string $name String to search for
	 * @param int $type search only customers of type $type
	 */
	public function searchCustomer($name, $type=0) {
		$like = sql_syntax("like");
		$sql = sprintf("SELECT id,company_name FROM nrd_customer WHERE company_name $like '%s'", "%".$name."%");
		if ($type > 0) 
			$sql .= sprintf(" AND type=%d", $type);
			
		$res = sql_query($sql);

		while ($row = sql_fetch_assoc($res))
			$companies[$row["id"]] = $row["company_name"];

		return $companies;
	} /* }}} */
	/* {{{ linkConsultantCompany() */
	/**
	 * Link consultant to some company.
	 *
	 * This functions adds a consultant id and a customer id to the 
	 * nrd_customer_company table. 
	 *
	 * @param int $consultant_id Id of consultant.
	 * @param int $customer_id Id of customer/company to add.
	 * @return no return value
	 */
	public function linkConsultantCompany($consultant_id,$customer_id) {
		$sql = sprintf("INSERT INTO nrd_consultant_company (consultant_id,customer_id) ".
			"VALUES (%d,%d)", $consultant_id, $customer_id);
		sql_query($sql);
	} /* }}} */
}
?>
