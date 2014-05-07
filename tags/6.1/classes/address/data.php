<?php
	/**
	 * Covide Groupware-CRM Address_data
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
	Class Address_data {
		/* constants */
		/**
		 * @const string the class include dir
		 */
		const include_dir = "classes/address/inc/";

		/* variables */
		/**
		 * @var array holds address information
		 */
		public $adresinfo = Array();

		/* methods */

	/* show_phonenr($phonenr) {{{ */
	/**
	 * Show a phone nr
	 *
	 * When the office has voip license, show link.
	 * else show text
	 *
	 * @param string the phonenumber to parse
	 * @return string the link or text to show
	 */
	public function show_phonenr($phonenr) {
		if ($GLOBALS["covide"]->license["has_voip"]) {
			$output = new Layout_output();
			$output->insertLink($phonenr, array(
				"href" => "javascript: loadXML('index.php?mod=voip&action=call&number=".preg_replace("/[\(|\)]/si","",str_replace("-","",str_replace(" ","",str_replace("+","00",$phonenr))))."');"
			));
			return $output->generate_output();
		} else {
			return $phonenr;
		}
	}
	/* }}} */
		/* generate_letterinfo($data) {{{*/
		/**
		 * generate 2 fields based on title, commencement etc
		 *
		 * @param array the address data
		 * @return array tav and contactperson
		 */
		public function generate_letterinfo($data) {
			require(self::include_dir."dataGenerateLetterinfo.php");
			return $return;
		}
		/* }}} */
		/* store2db {{{ */
	    /**
	     * 	put addressinfo in db
	     *
		 * @param array the address record
		 * @param array Metafield data (optional)
	     */
		public function store2db($address, $metafields = array()) {
			require(self::include_dir."dataStore2db.php");
		}
		/* }}} */
		/* delete {{{ */
	    /**
	     * 	delete addressinfo in db
	     *
		 * @param int the id of the address
		 * @param string the addresstype
	     */
		public function delete($address_id, $addresstype) {
			require_once(self::include_dir."dataDelete.php");
		}
		/* }}} */
		/* 	getAddressByID {{{ */
	    /**
	     * 	getAddressById Find an address based on address id
	     *
		 * @param int the database id
		 * @param int type of address. Can be relation/bcard etc
	     * @return int 1 if user is logged in, otherwise 0
	     */
		public function getAddressByID($addressid, $type="relations", $sub="kantoor") {
			if ($type == "bcards") {
				$sql = sprintf("SELECT address_businesscards.*, address.companyname FROM address_businesscards left join address on address.id = address_businesscards.address_id WHERE address_businesscards.id=%d", $addressid);
				$res = sql_query($sql);
				$adresinfo = sql_fetch_assoc($res);
				/* make photo an array with the seperate parts of info */
				$photo = explode("|", $adresinfo["photo"]);
				unset($adresinfo["photo"]);
				if (count($photo) == 3) {
					/* valid photo */
					$adresinfo["photo"]["id"]   = $adresinfo["id"];
					$adresinfo["photo"]["size"] = $photo[0];
					$adresinfo["photo"]["type"] = $photo[1];
					$adresinfo["photo"]["name"] = $photo[2];
				} else {
					$adresinfo["photo"] = array(
						"id"   => 0,
						"size" => 0,
						"type" => "unknown",
						"name" => "unknown"
					);
				}


				$adresinfo["business_phone_nr_link"]  = $this->show_phonenr($adresinfo["business_phone_nr"]);
				$adresinfo["business_mobile_nr_link"] = $this->show_phonenr($adresinfo["business_mobile_nr"]);
				$adresinfo["personal_phone_nr_link"]  = $this->show_phonenr($adresinfo["personal_phone_nr"]);
				$adresinfo["personal_mobile_nr_link"] = $this->show_phonenr($adresinfo["personal_mobile_nr"]);

				$adresinfo["fullname"] = $adresinfo["givenname"]." ".$adresinfo["infix"]." ".$adresinfo["surname"];
				$adresinfo["fullname"] = preg_replace("/\W{2,}/si", " ", $adresinfo["fullname"]);
			} elseif($type == "overig") {
				$sql = sprintf("SELECT * FROM address_other WHERE id = %d", $addressid);
				$res = sql_query($sql);
				$adresinfo = sql_fetch_assoc($res);
			} elseif ($type != "relations" && $type != "nonactive" && $type != "address") {
				$sql = sprintf("SELECT * FROM address_private WHERE id=%d", $addressid);
				$res = sql_query($sql);
				$adresinfo = sql_fetch_assoc($res);
				$adresinfo["phone_nr_link"] = $this->show_phonenr($adresinfo["phone_nr"]);
				$adresinfo["mobile_nr_link"] = $this->show_phonenr($adresinfo["mobile_nr"]);

			} else {
				$sql = sprintf("SELECT address.*, address_info.provision_perc, address_info.warning as letop, address_info.comment as memo, address_info.classification as classifi, address_info.photo as photo FROM address,address_info WHERE address_info.address_id=address.id AND address.id=%d", $addressid);
				$res = sql_query($sql);
				$adresinfo = sql_fetch_assoc($res);
				$adresinfo["warning"] = $adresinfo["letop"];
				/* make photo an array with the seperate parts of info */
				$photo = explode("|", $adresinfo["photo"]);
				unset($adresinfo["photo"]);
				if (count($photo) == 3) {
					/* valid photo */
					$adresinfo["photo"]["id"]   = $adresinfo["id"];
					$adresinfo["photo"]["size"] = $photo[0];
					$adresinfo["photo"]["type"] = $photo[1];
					$adresinfo["photo"]["name"] = $photo[2];
				} else {
					$adresinfo["photo"] = array(
						"id"   => 0,
						"size" => 0,
						"type" => "unknown",
						"name" => "unknown"
					);
				}
				/* generate classification names */
				$classifications = explode("|", $adresinfo["classifi"]);
				foreach ($classifications as $k=>$v) {
					if (!$v) {
						unset($classifications[$k]);
					}
				}
				$classifications[] = 0;
				$classifications = implode(",", $classifications);
				$query = sprintf("SELECT * FROM address_classifications WHERE id IN (%s) ORDER BY upper(description)", $classifications);
				$result = sql_query($query);
				$classification_names = "";
				while ($row = sql_fetch_assoc($result)) {
					$classification_names .= $row["description"]."\n";
				}
				/* if address is a relation, we can have an account manager. init user object and get name */
				if ($type == "relations" || $type == "nonactive") {
					$user_data = new User_data();
					$adresinfo["account_manager_name"] = $user_data->getUsernameById($adresinfo["account_manager"]);
				}
				/* fix website */
				if (strpos($adresinfo["website"], "http://") !== 0) {
					$adresinfo["website"] = "http://".$adresinfo["website"];
				}
				$adresinfo["classification_names"] = $classification_names;
				$adresinfo["phone_nr_link"] = $this->show_phonenr($adresinfo["phone_nr"]);
				$adresinfo["mobile_nr_link"] = $this->show_phonenr($adresinfo["mobile_nr"]);
			}

			if (!trim($adresinfo["fullname"])) $adresinfo["fullname"] = "--";
			/* return the info */
			return $adresinfo;
		}
		/* }}} */
		/* 	getAddressNameByID {{{ */
	    /**
	     * 	Find an addressname based on address id
	     *
		 * @param int the database id
		 * @param int type of address. Can be relation/bcard etc
	     * @return array the name
	     */
		public function getAddressNameByID($addressid, $type=0) {
			if (!$addressid) {
				return gettext("geen");
			} else {
				if ($type == 0) {
					$sql = sprintf("SELECT companyname FROM address WHERE id=%d", $addressid);
				}
				$res = sql_query($sql);
				$adresinfo = sql_fetch_assoc($res);
				/* return the info */
				return $adresinfo["companyname"];
			}
		}
		/* }}} */
		/* 	getAddressByName {{{ */
	    /**
	     * 	getAddressByName Find an address based on address name
	     *
		 * @param string the database field 'bedrijfsnaam'
		 * @param int type of address. Can be relation/bcard etc
	     * @return int 1 if user is logged in, otherwise 0
	     */
		public function getAddressByName($addressname, $type=0) {

		}
		/* }}} */
		/* 	getRelationsArray {{{ */
	    /**
	     * 	getRelationsArray Put all relationnames and ids in an array
	     *
		 * @param int active or non-active addresses
	     * @return array all relations with ids
	     */
		public function getRelationsArray($active=1) {
			if ($active==1) {
				$sql = "SELECT id, companyname FROM address WHERE is_active=1";
			} else {
				$sql = "SELECT id, companyname FROM address WHERE is_active!=1";
			}
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$companies[$row["id"]] = $row["companyname"];
			}
			unset($row);
			$companies[0] = gettext("geen");
			return $companies;
		}
		/* }}} */
		/* 	lookupRelationEmail {{{ */
	    /**
	     * 	lookupRelationEmail Put all relationemails + bcards and ids in an array
	     * 	if mail address is double, replace the id with -1
	     */
		public function lookupRelationEmail($email) {
			$filter = $email;
			$mails = array();

			$q = "select companyname, address_businesscards.email, business_email, address_id as id from address_businesscards left join address on address_businesscards.address_id = address.id where lower(address_businesscards.email) like '$filter' or lower(business_email) like '$filter' ";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$row["email"]          = strtolower($row["email"]);
				$row["business_email"] = strtolower($row["business_email"]);

				if (preg_match("/.*@.*\..*/s", $row["email"])) {
					$mails[$row["id"]] = $row["companyname"];
				}
				if (preg_match("/.*@.*\..*/s", $row["business_email"])) {
					$mails[$row["id"]] = $row["companyname"];
				}
			}
			$q = "select companyname, email, id from address where lower(email) like '$filter' ";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$mails[$row["id"]] = $row["companyname"];
			}
			return $mails;
		}
		/* }}} */
				/* 	lookupRelationEmail {{{ */
	    /**
	     * 	lookupRelationEmail Put all relationemails + bcards and ids in an array
	     * 	if mail address is double, replace the id with -1
	     */
		public function lookupRelationEmailCommencement($email, $address_type) {
			$filter = $email;

			$like = sql_syntax("like");
			if ($address_type == "bcards" || !$address_type) {
				$q = "select * from address_businesscards where business_email ".$like." '%".$email."%' OR personal_email like '%".$email."%'";
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res)) {
					$row["contact_letterhead"]   = $row["letterhead"];
					$row["contact_commencement"] = $row["commencement"];
					$row["contact_infix"]        = $row["infix"];
					$row["contact_surname"]      = $row["surname"];
					$row["contact_givenname"]    = $row["givenname"];
					$row["contact_initials"]     = $row["initials"];

					$row = $this->generate_letterinfo($row);
					if ($row["contact_person"]) {
						return trim(preg_replace("/ {2,}/s", " ", $row["contact_person"]));
					}
				}
			}
			if ($address_type == "relations" || !$address_type) {
				$q = "select * from address where email ".$like." '%".$email."%'";
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res)) {
					$row = $this->generate_letterinfo($row);
					if ($row["contact_person"]) {
						return trim(preg_replace("/ {2,}/s", " ", $row["contact_person"]));
					}
				}
			}

		}
		/* }}} */
		/* getMailAddressesById {{{ */
		/**
		 * find all email addresses associated to an address record
		 *
		 * @param int the address table id
		 * @return array All email addresses found
		 */
		public function getMailAddressesById($address_id) {
			require(self::include_dir."dataGetMailAddressesById.php");
			return $return;
		}
		/* }}} */
		/* 	getRelationsEmailArray {{{ */
	    /**
	     * 	getRelationsEmailArray Put all relationemails + bcards and ids in an array
	     * 	if mail address is double, replace the id with -1
	     */
		public function getRelationsEmailArray() {
			$filter = "%@%.%";
			$mails = array();
			$q = "select email, business_email, personal_email, address_id as id from address_businesscards where lower(email) like '$filter' or lower(business_email) like '$filter' ";

			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$row["email"]          = trim(strtolower($row["email"]));
				$row["business_email"] = trim(strtolower($row["business_email"]));
				$row["personal_email"] = trim(strtolower($row["personal_email"]));

				if (preg_match("/.*@.*\..*/s", $row["email"])) {
					if ($mails[$row["email"]] && $mails[$row["email"]]!=$row["id"]) {
						$mails[$row["email"]] = -1;
					} else {
						$mails[$row["email"]] = $row["id"];
					}
				}
				if (preg_match("/.*@.*\..*/s", $row["business_email"])) {
					if ($mails[$row["business_email"]] && $mails[$row["business_email"]]!=$row["id"]) {
						$mails[$row["business_email"]] = -1;
					} else {
						$mails[$row["business_email"]] = $row["id"];
					}
				}
				if (preg_match("/.*@.*\..*/s", $row["personal_email"])) {
					if ($mails[$row["personal_email"]] && $mails[$row["personal_email"]]!=$row["id"]) {
						$mails[$row["personal_email"]] = -1;
					} else {
						$mails[$row["personal_email"]] = $row["id"];
					}
				}
			}
			$q = "select email, id from address where lower(email) like '$filter' ";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$row["email"] = trim(strtolower($row["email"]));
				if ($mails[$row["email"]] && $mails[$row["email"]]!=$row["id"]) {
					$mails[$row["email"]] = -1;
				} else {
					$mails[$row["email"]] = $row["id"];
				}
			}
			return $mails;
		}
		/* }}} */
		/* 	getRelationsList {{{ */
	    /**
	     * 	getRelationsList generate big array with all addresses.
	     *
		 * @param array options for search and addresstype etc.
		 * $options["addresstype"]: users/private/overig/bcards/relations
		 * $options["l"]: First letter of relations to show.
		 * $options["search"]: search words to match addresses.
	     * @return array addresses that match the options
	     */
		public function getRelationsList($options) {
			if ($options["addresstype"] == "both") {
				$options["addresstype"] = "relations";
				$address1 = $this->getRelationsList($options);
				$options["addresstype"] = "bcards";
				$address2 = $this->getRelationsList($options);

				foreach ($address1["address"] as $k=>$v) {
					$result["address"]["R".$k] = $v;
				}
				foreach ($address2["address"] as $k=>$v) {
					$result["address"]["B".$k] = $v;
				}
				unset($address1["address"]);
				unset($address2["address"]);

				$result["count"] = count($result["data"]);
				return $result;

			} else {
				require(self::include_dir."dataGetRelationsList.php");
				return $addressinfo;
			}
		}

		/* }}} */
		/* 	getBcardsByRelationID {{{ */
	    /**
	     * 	put all bcards for a relation in an array
	     *
		 * @param int relation to fetch for
	     * @return array all relevant info
	     */
		public function getBcardsByRelationID($address_id) {
			$address_id = (int)$address_id;
			if ($GLOBALS["covide"]->license["has_sync4j"]) {
				/* build list with items the user is already syncing. We use this for the bullet */
				$sql = sprintf("SELECT * FROM address_sync_records WHERE user_id = %d AND address_table = 'address_businesscards'", $_SESSION["user_id"]);
				$res = sql_query($sql);
				while ($row = sql_fetch_assoc($res)) {
					$_sync[$row["address_id"]] = $row["id"];
				}
			}
			/* get bcards */
			$ret = array();
			$regex_syntax = sql_syntax("regex");
			$query = "SELECT * FROM address_businesscards WHERE address_id=".$address_id." OR multirel $regex_syntax '(^|\\\\,)".$address_id."(\\\\,|$)' ORDER BY surname,givenname";
			$res = sql_query($query);
			while($row = sql_fetch_assoc($res)) {
				if (trim($row["business_phone_nr"])) { $row["business_phone_nr_link"]  = $this->show_phonenr($row["business_phone_nr"]); }
				if (trim($row["business_mobile_nr"])) { $row["business_mobile_nr_link"] = $this->show_phonenr($row["business_mobile_nr"]); }
				if (trim($row["personal_phone_nr"])) { $row["personal_phone_nr_link"]  = $this->show_phonenr($row["personal_phone_nr"]); }
				if (trim($row["personal_mobile_nr"])) { $row["personal_mobile_nr_link"] = $this->show_phonenr($row["personal_mobile_nr"]); }
				$row["fullname"] = $row["givenname"]." ".$row["infix"]." ".$row["surname"];
				$row["fullname"] = preg_replace("/\W{2,}/si", " ", $row["fullname"]);
				if ($row["alternative_name"]) {
					$row["has_no_alt"] = 0;
				} else {
					$row["has_no_alt"] = 1;
				}
				/* sync4j check to see if this bcard is prepared to be synced */

				if ($GLOBALS["covide"]->license["has_sync4j"]) {
					if (isset($_sync[$row["id"]])) {
						$row["sync_yes"] = 1;
						$row["sync_no"]  = 0;
					} else {
						$row["sync_yes"] = 0;
						$row["sync_no"]  = 1;
					}
				}
				/* put prepared data in return array */
				$ret[] = $row;
			}
			return $ret;
		}
		/* }}} */
		/* getTitles {{{*/
	    /**
	     * 	generate array with titles
		 *
		 * This is used in a lot of places.
		 * Hence the function
	     *
	     * @return array all relevant info
	     */
		public function getTitles() {
			$return = array(
	            0 => " ",
				1 => "Dr.",
				2 => "Drs.",
				3 => "Ing.",
				4 => "Ir.",
				5 => "Mr.",
				6 => "Prof.",
				7 => "Prof. Dr.",
				8 => "BSc.",
				9 => "MSc.",
				10 => "Drs. Ing."
			);
			return $return;
		}
		/* }}} */
		/* getLetterheads {{{*/
	    /**
	     * 	generate array with letterheads
		 *
		 * This is used in a lot of places.
		 * Hence the function
	     *
	     * @return array all relevant info
	     */
		public function getLetterheads() {
			$return = array(
				0 => " ",
				1 => "Beste",
				2 => "Geachte",
				3 => "Dear"
			);
			return $return;
		}
		/* }}} */
		/* getCommencements {{{*/
	    /**
	     * 	generate array with commencements
		 *
		 * This is used in a lot of places.
		 * Hence the function
	     *
	     * @return array all relevant info
	     */
		public function getCommencements() {
			$return = array(
				0 => " ",
				1 => "Dhr.",
				2 => "Mevr.",
				3 => "---",
				4 => "Mr.",
				5 => "Mrs.",
				6 => "Ms."
			);
			return $return;
		}
		/* }}} */
		/* save_bcard {{{ */
		/**
		 * store modified/created bcard in db
		 */
		public function save_bcard() {
			require(self::include_dir."dataSaveBcard.php");
		}
		/* }}} */
		/* remove_bcard {{{ */
		/**
		 * Remove a businesscard
		 *
		 * @param int The businesscard id to remove
		 */
		public function remove_bcard($cardid) {
			$sql = sprintf("DELETE FROM address_businesscards WHERE id=%d", $cardid);
			$res = sql_query($sql);
		}
		/* }}} */
		/* gendebtornr {{{ */
		/**
		 * Generate debtor number based on data already in database
		 * Outputs javascript function update_debtor_nr(newnumber)
		 * To be used in AJAX calls
		 */
		public function gendebtornr() {
			$sql = "SELECT MAX(debtor_nr) as currentnr FROM address";
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			$nextnr = $row["currentnr"]+1;
			echo "update_debtor_nr(".$nextnr.");";
		}
		/* }}} */
		/* toggleSync {{{ */
		/**
		 * Enable/disable sync of specified record for specified user.
		 *
		 * echo 3 javascript lines to browser so it can alter the images.
		 * It will alter the DOM elements with id: toggle_sync_$address_id.
		 * To be used in AJAX calls
		 *
		 * @param int The address id to toggle
		 * @param string The address table to get the id/record from
		 * @param string activate or deactivate
		 * @param int The userid to toggle the sync for
		 */
		public function toggleSync($address_id, $address_table, $toggleaction, $user) {
			$output = new Layout_output();
			/* check if record is in the db. */
			/* if it is, remove, if not, insert */
			$sql = sprintf("SELECT COUNT(*) as count FROM address_sync_records WHERE user_id = %d AND address_id = %d AND address_table = '%s'", $user, $address_id, $address_table);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["count"]) {
				$toggleaction = "deactivate";
			} else {
				$toggleaction = "activate";
			}
			if ($toggleaction == "deactivate") {
				$sql   = sprintf("DELETE FROM address_sync_records WHERE user_id = %d AND address_id = %d AND address_table = '%s'", $user, $address_id, $address_table);
				$res   = sql_query($sql);
				$image = $output->replaceImage("f_oud.gif");
				$alt   = gettext("sync");
			} elseif ($toggleaction == "activate") {
				$sql = sprintf("INSERT INTO address_sync_records (user_id, address_id, address_table) VALUES (%d, %d, '%s')", $user, $address_id, $address_table);
				$res = sql_query($sql);
				$image = $output->replaceImage("f_nieuw.gif");
				$alt   = gettext("sync");
			}
			unset($output);
			echo "document.getElementById('toggle_sync_".$address_id."').src='$image';";
			echo "document.getElementById('toggle_sync_".$address_id."').alt='$alt';";
			echo "document.getElementById('toggle_sync_".$address_id."').title='$alt';";
		}
		/* }}} */
		/* getBirthDays {{{ */
		/**
		 * Get an array with people who celebrate their birthday today.
		 *
		 * @return array name, companyname, timestamp of birth and age
		 */
		public function getBirthDays() {
			$q = "select id, address_id, timestamp_birthday, givenname, infix, surname from address_businesscards where timestamp_birthday > 0";
			$res = sql_query($q);
			$bd = array();
			while ($row = sql_fetch_assoc($res)) {
				if (date("d", $row["timestamp_birthday"])==date("d") && date("m", $row["timestamp_birthday"])==date("m")) {

					$companyname = $this->getAddressNameByID((int)$row["address_id"]);
					$fullname = $row["givenname"]." ".$row["infix"]." ".$row["surname"];
					$fullname = preg_replace("/\W{2,}/si", " ", $fullname);

					$age = (int)(date("Y") - date("Y", $row["timestamp_birthday"]));

					$bd[$row["id"]] = array(
						"id"           => $row["id"],
						"company_id"   => $row["address_id"],
						"company_name" => $companyname,
						"timestamp"    => $row["timestamp_birthday"],
						"name"         => $fullname,
						"age"          => $age
					);
				}
			}
			return $bd;
		}
		/* }}} */
		/* import_save {{{ */
		/**
		 * Save prepared import data into database.
		 * This function will output mini page to refresh the opener and close the popup
		 *
		 * @param array The prepared import data
		 */
		public function import_save($data) {
			require(self::include_dir."dataImportSave.php");
			/* small output to close window */
			$output = new Layout_output();
			$output->layout_page("", 1);
			$output->start_javascript();
				$output->addCode("opener.location.href = opener.location.href;");
				$output->addCode("window.close();");
			$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* limit_import_field {{{ */
		/**
		 * Truncate too long values produced during import preperation
		 *
		 * @param string The value to check
		 * @param int max lenght of the data
		 * @return string Original string if it's in the limit, truncated otherwise
		 */
		public function limit_import_field($data, $length) {
			if (strlen($data) > $length) {
				$data = substr($data, 0, $lenght);
			}
			return addslashes($data);
		}
		/* }}} */
		/* getHRMinfo {{{ */
		/**
		 * get employee info from db
		 *
		 * @param int The user to fetch the extra data for
		 * @return array The extra info, or array (0 => none) if nothing found
		 */
		public function getHRMinfo($user_id) {
			if ((int)$user_id > 0) {
				$sql = sprintf("SELECT * FROM employees_info WHERE user_id = %d", $user_id);
				$res = sql_query($sql);
				while($row = sql_fetch_assoc($res)) {
					$row["human_gender"] = ($row["gender"] == 1?gettext("man"):gettext("vrouw"));
					if ($row["timestamp_started"] > 0)
						$row["human_start"]  = date("d-m-Y", $row["timestamp_started"]);
					if ($row["timestamp_stop"])
						$row["human_end"]    = date("d-m-Y", $row["timestamp_stop"]);
					if ($row["timestamp_birthday"])
						$row["human_bday"]   = date("d-m-Y", $row["timestamp_birthday"]);
					$hrminfo[] = $row;
				}
			} else {
				$hrminfo = array("0" => gettext("geen"));
			}
			return $hrminfo;
		}
		/* }}} */
		/* check_double {{{ */
		/**
		 * Check if an address is already in the database.
		 *
		 * Right now we use the zipcode and phone number field for the check
		 * If this is not enough it's not hard to add more fields in the check.
		 *
		 * @param array Fields to use in check. [fieldname] => fieldvalue, ....
		 */
		public function check_double($checkdata) {
			if (strlen($checkdata["zipcode"]) || strlen($checkdata["phone_nr"])) {
				$sql = sprintf("SELECT COUNT(*) as count FROM address WHERE zipcode = '%s' AND phone_nr = '%s'", $checkdata["zipcode"], $checkdata["phone_nr"]);
				if ($checkdata["id"] > 0) {
					$sql .= sprintf(" AND id != %d", $checkdata["id"]);
				}
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
			} else {
				$row["count"] == 0;
			}
			echo "update_double(".$row["count"].");";
			exit;
		}
		/* }}} */
		/* checkcla_xml {{{ */
		/**
		 * Check if classification already is in the database
		 *
		 * @param array Fields to use in check
		 */
		public function checkcla_xml($data) {
			if (strlen($data["name"])) {
				$sql = sprintf("SELECT COUNT(*) as count FROM address_classifications WHERE UPPER(description) = '%s'", strtoupper($data["name"]));
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
				if ($row["count"] > 0) {
					echo "update_check(1);";
				} else {
					echo "update_check(0);";
				}
			} else {
				echo "update_check(2);";
			}
		}
		/* }}} */
		/* savecla_multi {{{ */
		/**
		 * Add new classification to selection of addresses
		 *
		 * @param array new classification name and options array to get the relevant addresses from addressbook
		 */
		public function savecla_multi($data) {
			/* create new classification */
			$sql = sprintf("INSERT INTO address_classifications (description) VALUES ('%s')", $data["addcla"]["name"]);
			$res = sql_query($sql);
			$new_cla_id = sql_insert_id("address_classifications");
			$addresses = $this->getRelationsList($data["options"]);
			if (is_array($addresses["address"])) {
				foreach ($addresses["address"] as $address) {
					$address_id = $address["id"];
					$classifications = explode("|", $address["classification"]);
					/* sanitize */
					foreach ($classifications as $k=>$v) {
						if (!$v) {
							unset($classifications[$k]);
						}
					}
					$classifications[] = $new_cla_id;
					$cla = "|".implode("|", $classifications)."|";
					$sql = "UPDATE address_info SET classification = '$cla' WHERE address_id = $address_id";
					$res = sql_query($sql);
				}
			}
			/* close window */
			$output = new Layout_output();
			$output->layout_page("", 1);
			$output->start_javascript();
				$output->addCode("window.close();");
			$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* storeRelIMG {{{ */
		/**
		 * Store a new image in db
		 *
		 * @param int The address id to link the image to
		 * @param string The address table name (relations/bcards)
		 * @param array Content of $_FILES upload info
		 * @return bool true on success, false on error
		 */
		public function storeRelIMG($address_id, $addresstype, $filedata) {
			$base = $GLOBALS["covide"]->filesyspath;
			if ($addresstype == "relations") {
				$photopath = $base."/relphotos/".$address_id.".dat";
				$tmp = $filedata["address"]["tmp_name"]["binphoto"];
				$photodata = $filedata["address"]["size"]["binphoto"]."|".$filedata["address"]["type"]["binphoto"]."|".$filedata["address"]["name"]["binphoto"];
				$sql = sprintf("UPDATE address_info SET photo = '%s' WHERE address_id = %d", $photodata, $address_id);
			} else {
				$photopath = $base."/relphotos/bcards/".$address_id.".dat";
				$tmp = $filedata["bcard"]["tmp_name"]["binphoto"];
				$photodata = $filedata["bcard"]["size"]["binphoto"]."|".$filedata["bcard"]["type"]["binphoto"]."|".$filedata["bcard"]["name"]["binphoto"];
				$sql = sprintf("UPDATE address_businesscards SET photo = '%s' WHERE id = %d", $photodata, $address_id);
			}
			/* move the file */
			if (move_uploaded_file($tmp, $photopath)) {
				/* store record in database */
				$res = sql_query($sql);
			} else {
				return false;
			}
			print_r($filedata);
			return true;
		}
		/* }}} */
		/* removeRelIMG {{{ */
		/**
		 * Remove a relation image from database
		 *
		 * @param int The address id to remove image for
		 * @param string The address table for the address_id
		 */
		public function removeRelIMG($address_id, $addresstype) {
			$imagepath = $GLOBALS["covide"]->filesyspath."/relphotos/";
			if ($addresstype == "relations") {
				$imagepath .= $address_id.".dat";
				$sql = sprintf("UPDATE address_info SET photo = '' WHERE address_id = %d", $address_id);
			} elseif ($addresstype == "bcards") {
				$imagepath .= "bcards/".$address_id.".dat";
				$sql = sprintf("UPDATE address_businesscards SET photo = '' WHERE id = %d", $address_id);
			}
			/* remove from database */
			$res = sql_query($sql);
			/* remove file from filesys */
			@unlink($imagepath);
		}
		/* }}} */
		/* save_hrminfo {{{ */
		/**
		 * Stores hrm info in the database
		 *
		 * @param array The hrminfo. See description for format
		 * @todo Describe expected input array
		 */
		public function save_hrminfo($hrmdata) {
			if (!is_array($hrmdata)) {
				die("invalid input");
			}
			/* generate dates */
			$contract_start = mktime(1, 0, 0, $hrmdata["start_month"], $hrmdata["start_day"], $hrmdata["start_year"]);
			$contract_stop  = mktime(1, 0, 0, $hrmdata["end_month"]  , $hrmdata["end_day"]  , $hrmdata["end_year"]);
			$birthday       = mktime(1, 0, 0, $hrmdata["bday_month"] , $hrmdata["bday_day"] , $hrmdata["bday_year"]);
			/* first look if we have info for this user */
			$sql = sprintf("SELECT COUNT(*) as count FROM employees_info WHERE user_id = %d", $hrmdata["user_id"]);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["count"]) {
				/* we have a record, update it */
				$sql  = sprintf("UPDATE employees_info SET social_security_nr = '%s', timestamp_started = %d, timestamp_birthday = %d, evaluation = '%s', ",
					$hrmdata["social_security_nr"], $contract_start, $birthday, $hrmdata["evaluation"]);
				$sql .= sprintf("gender = %d, contract_type = '%s', contract_hours = %d, contract_holidayhours = %d, timestamp_stop = %d WHERE user_id = %d",
					$hrmdata["gender"], $hrmdata["contract_type"], $hrmdata["contract_hours"], $hrmdata["contract_holidayhours"], $contract_stop, $hrmdata["user_id"]);
			} else {
				/* no record found, so insert this info */
				$sql  = "INSERT INTO employees_info (user_id, social_security_nr, timestamp_started, timestamp_birthday, gender, contract_type, contract_hours, contract_holidayhours, timestamp_stop, evaluation) ";
				$sql .= sprintf("VALUES (%d, '%s', %d, %d, %d, '%s', %d, %d, %d, '%s')",
					$hrmdata["user_id"], $hrmdata["social_security_nr"], $contract_start, $birthday, $hrmdata["gender"], $hrmdata["contract_type"],
					$hrmdata["contract_hours"], $hrmdata["contract_holidayhours"], $contract_stop, $hrmdata["evaluation"]);
			}
			$res = sql_query($sql);
			/* small set of output to close the window */
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("
					opener.location.href=opener.location.href;
					window.close();
				");
			$output->end_javascript();
			$output->exit_buffer();
		}
		/* }}} */
		/* get_metafields {{{ */
		/**
		 * Get the metafields from db
		 */
		public function get_metafields() {
			$sql = "SELECT * FROM address_metafields ORDER BY fieldorder";
			$res = sql_query($sql);
			$fields = array();
			$i = 0;
			while ($row = sql_fetch_assoc($res)) {
				$i++;
				$fields[$i] = $row;
				/* friendly type name */
				switch ($row["fieldtype"]) {
					case 1: $fields[$i]["h_fieldtype"] = gettext("korte tekst"); break;
					case 2: $fields[$i]["h_fieldtype"] = gettext("lange tekst"); break;
					case 3: $fields[$i]["h_fieldtype"] = gettext("datum");       break;
					case 4: $fields[$i]["h_fieldtype"] = gettext("ja/nee");      break;
				}
			}
			return $fields;
		}
		/* }}} */

	}
?>
