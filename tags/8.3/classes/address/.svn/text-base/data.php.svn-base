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
	 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
	 * @copyright Copyright 2000-2008 Covide BV
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
		/**
		 * @var array holds cacheable address data
		 */
	
		private $_cache = array();

		/* methods */
		/* show_phonenr($phonenr) {{{ */
		/**
		 * Show a phone nr
		 *
		 * When the office has voip license, show link.
		 * else show text
		 *
		 * @param string $phonenr the phonenumber to parse
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
		 * @param array $data the address data
		 * @return array tav and contactperson
		 */
		public function generate_letterinfo($data) {
			require(self::include_dir."dataGenerateLetterinfo.php");
			return $return;
		}
		/* }}} */
		/* store2db {{{ */
		/**
		 * put addressinfo in db
		 *
		 * @param array $address the address record
		 * @param array $metafields Metafield data (optional)
		 * @param $skip_funambol if set, dont run the funambol routines
		 */
		public function store2db($address, $metafields = array(), $skip_funambol = 0) {
			require(self::include_dir."dataStore2db.php");
			if ($skip_funambol)
				return $new_id;
		}
		/* }}} */
		/* delete {{{ */
		/**
		 * delete addressinfo in db
		 *
		 * @param int $address_id the id of the address
		 * @param string $addresstype the addresstype
		 * @param $skip_funambol if set, dont run the funambol routines
		 */
		public function delete($address_id, $addresstype, $skip_funambol=0) {
			require(self::include_dir."dataDelete.php");
		}
		/* }}} */
		/* getAddressByID {{{ */
		/**
		 * Find an address based on address id
		 *
		 * @param int $addressid the database id
		 * @param string $type type of address. Can be relation/bcard etc
		 * @param string $sub some addresstypes have subtypes.
		 * @return array The address data
		 */
		public function getAddressByID($addressid, $type="relations", $sub="kantoor", $migrate=0) {
			require(self::include_dir."dataGetAddressById.php");
			/* return the info */
			return $adresinfo;
		}
		/* }}} */
		/* getAddressNameByID {{{ */
		/**
		 * Find an addressname based on address id
		 *
		 * @param int $addressid the database id
		 * @param int type of address. Can be relation/bcard etc
		 * @return array the name
		 * @todo the type parameter is useless. We should get rid of it
		 */
		public function getAddressNameByID($addressid, $type=0) {
			//TODO: return is already the end of a function. remove the else
			if (!$addressid) {
				return gettext("none");
			} else {
				if ($type == 0) {
					$sql = sprintf("SELECT companyname,is_person,contact_surname,contact_givenname,contact_infix FROM address WHERE id=%d", $addressid);
				}
				$res = sql_query($sql);
				$addressinfo = sql_fetch_assoc($res);
				/* fix companyname for persons in relationlist */
				if ($addressinfo["is_person"]) {
					$companyname_temp = $addressinfo["contact_surname"].", ".$addressinfo["contact_givenname"]." ".$addressinfo["contact_infix"];
					$companyname = preg_replace("/ {2,}/", " ", $companyname_temp);
				} else {
					$companyname = $addressinfo["companyname"];
				}

				/* return the info */
				return $companyname;
			}
		}
		/* }}} */
		/* getRelationsArray {{{ */
		/**
		 * Put all relationnames and ids in an array
		 *
		 * @param int $active active or non-active addresses
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
			$companies[0] = gettext("none");
			return $companies;
		}
		/* }}} */
		/* lookupRelationEmail {{{ */
		/**
		 * Put all relationemails + bcards and ids in an array
		 * if mail address is double, replace the id with -1
		 *
		 * @param string $email The email address to match
		 * @return array keys are address ids, values are relation names
		 */
		public function lookupRelationEmail($email) {
			$filter = trim(strtolower($email));
			$mails = array();
			$like = sql_syntax("like");

			$q = sprintf("SELECT address_id, multirel FROM address_businesscards WHERE lower(email) $like '%%%1\$s%%' OR lower(business_email) $like '%%%1\$s%%'
				OR lower(personal_email) $like '%%%1\$s%%' OR lower(other_email) $like '%%%1\$s%%'", $filter);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				//put address_id in the array with id's
				$address_ids[] = $row["address_id"];
				//put multirel ids into array
				if (strlen($row["multirel"])) {
					$multirel = explode(",", $row["multirel"]);
					foreach($multirel as $address_id) {
						$address_ids[] = $address_id;
					}
				}
			}
			$q = sprintf("select id from address where lower(email) $like '%%%1\$s%%'", $filter);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$address_ids[] = $row["id"];
			}
			//unique the array
			$address_ids = array_unique($address_ids);
			//get companynames
			foreach ($address_ids as $id) {
				$mails[$id] = $this->getAddressNameByID($id);
			}
			return $mails;
		}
		/* }}} */
		/* lookupRelationEmailCommencement {{{ */
		/**
		 * Return contactperson for a specific email address.
		 *
		 * @param string $email email address to lookup
		 * @param string $address_type Type of address to lookup
		 * @return string The contact person information for an email address
		 */
		public function lookupRelationEmailCommencement($email, $address_type) {
			$filter = $email;

			$like = sql_syntax("like");
			if ($address_type == "bcards" || !$address_type) {
				//TODO: use sprintf here
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
						//TODO: why are we calling return in a while loop ?
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
						//TODO: why are we calling return in a while loop ?
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
		 * @param int $address_id the address table id
		 * @return array All email addresses found
		 */
		public function getMailAddressesById($address_id) {
			require(self::include_dir."dataGetMailAddressesById.php");
			return $return;
		}
		/* }}} */
		/* getRelationsEmailArray {{{ */
		/**
		 * Put all relationemails + bcards and ids in an array
		 * if mail address is double, replace the id with -1
		 *
		 * @return array key is the email address, value is the address_id or -1
		 */
		public function getRelationsEmailArray() {
			require(self::include_dir."dataGetRelationsEmailArray.php");
			return $mails;
		}
		/* }}} */
		/* getRelationsList {{{ */
		/**
		 * generate big array with all addresses.
		 *
		 * @param array $options options for search and addresstype etc.
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
		/* getBcardsByRelationID {{{ */
		/**
		 * put all bcards for a relation in an array
		 *
		 * @param int $address_id relation to fetch for
		 * @param string $search searchstring to limit the results
		 * @param mixed $classification if an array, limit the result to businesscards that have at least one of those classifications
		 * @return array all relevant info
		 */
		public function getBcardsByRelationID($address_id, $search="", $classification = "") {
			$address_id = (int)$address_id;
			/* get bcards */
			if ($GLOBALS["covide"]->license["has_funambol"]) {
				$sync_identifier = "address_businesscards";
				$sql = sprintf("SELECT * FROM funambol_address_sync WHERE user_id = %d AND address_table = '%s'", $_SESSION["user_id"], $sync_identifier);
				$res = sql_query($sql);
				while ($row = sql_fetch_assoc($res)) {
					$_sync[$row["address_id"]] = $row["id"];
				}
			}
			$ret = array();
			$regex_syntax = sql_syntax("regex");
			if (is_array($classification)) {
				$regex_field = "classification";
				$i = 1;
				foreach ($classification as $cla) {
					if ($i == 1) {
						$sql_cla = sprintf("AND ((%s %s '(^|\\\\|)%d(\\\\||$)')", $regex_field, $regex_syntax, $cla);
					} else {
						$sql_cla .= sprintf("OR (%s %s '(^|\\\\|)%d(\\\\||$)')", $regex_field, $regex_syntax, $cla);
					}
					$i++;
				}
				$sql_cla .= ")";
			} else {
				$sql_cla = "";
			}
			$query = sprintf("SELECT * FROM address_businesscards WHERE (address_id = %1\$d OR multirel %2\$s '(^|\\\\,)%1\$d(\\\\,|$)') %3\$s ORDER BY surname,givenname", $address_id, $regex_syntax, $sql_cla);
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
				/* sync check to see if this bcard is prepared to be synced */
				if ($GLOBALS["covide"]->license["has_funambol"] && !$GLOBALS["covide"]->license["disable_basics"]) {
					if (isset($_sync[$row["id"]])) {
						$row["sync_yes"] = 1;
						$row["sync_no"]  = 0;
					} else {
						$row["sync_yes"] = 0;
						$row["sync_no"]  = 1;
					}
				}
				/* rcbc field. We need a 'non-rcbc' field as well in various lists */
				if ($row["rcbc"]) {
					$row["norcbc"] = 0;
				} else {
					$row["norcbc"] = 1;
				}
				/* if we need to filter the data */
				$search = trim($search);
				if (($search && stristr(implode(" ", $row), $search)) || !$search)
					$ret[] = $row;
			}
			return $ret;
		}
		/* }}} */
		/* getTitles {{{*/
		/**
		 * generate array with titles
		 *
		 * This is used in a lot of places.
		 * Hence the function
		 *
		 * @param int $id if set, only return the title for that id
		 * @param int $justTitles not used anymore
		 * @return array all relevant info
		 * @todo remove the $justTitles parameter because it's not used
		 */
		public function getTitles($id = -1, $justTitles=0) {
			// TODO: what does justTitles do?
			// justTitles was made for output purposes, if you didn't want the IDs. Not sure if it's still used..

			// check for cache
			if ($id == -1 && $this->_cache["titles"])
				return $this->_cache["titles"];

			$sql = "SELECT id, title FROM address_titles";
			if ($id > -1) {
				$sql .= sprintf(" WHERE id = %d", $id);
			} else {
				/* We want all, so the first value should be empty
				so you're able to choose no title AND only if no specific ID is selected*/
				$return[0] = " ";
			}
			$res = sql_query($sql);

			while ($row = sql_fetch_assoc($res))
				$return[$row["id"]] = $row;
			//make sure we return an array
			if (!is_array($return))
				$return = array();

			// fill cache
			if ($id == -1)
				$this->_cache["titles"] = $return;
			
			return $return;
		}
		/* }}} */
		/* saveTitles {{{ */
		/**
		 * Saves the titles in the DB
		 *
		 * @param array $data new title data
		 */
		public function saveTitles($data) {
			$table_array = array(
				1=>"address_commencement",
				2=>"address_titles",
				3=>"address_letterhead",
				4=>"address_suffix"
			);
			$table = $table_array[$data["cid"]];

			$titles = $data["titles"];
			if($data["method"] == "edit") {
				$q = sprintf("UPDATE %s SET title = '%s' WHERE id = %d", $table, $titles[0]["title"], $titles[0]["id"]);
				sql_query($q);
			}
			if($data["method"] == "new") {
				$q = sprintf("INSERT INTO %s (title) VALUES ('%s')", $table, $titles[0]["title"]);
				sql_query($q);
			}
			$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("
						opener.location.href = opener.location.href;
						window.close();
					");
				$output->end_javascript();
			$output->exit_buffer();
		}
		/* }}} */
		/* removeTitles {{{ */
		/**
		 * Remove a specific ID from the database
		 *
		 * @param int $id ID number of title
		 * @return bool true on success
		 */
		public function removeTitles($id) {
			$table_array = array(
				1=>"address_commencement",
				2=>"address_titles",
				3=>"address_letterhead",
				4=>"address_suffix"
			);
			$table = $table_array[$id["cid"]];
			if ($id["id"]) {
				$sql = sprintf("DELETE FROM %s WHERE id = %d", $table, $id["id"]);
				$res = sql_query($sql);
				return true;
			}
		}
		/* }}} */
		/* getSuffix {{{ */
		/**
		 * Get a list of specifie suffixes from the database
		 *
		 * @param int $id If given fetch only this suffix
		 * @return array array key is the database id and the value is the complete address_suffix table record
		 */
		public function getSuffix($id="-1") {
			$sql = "SELECT id, title FROM address_suffix";
			if($id > -1) {
				$sql .= sprintf(" WHERE id = %d", $id);
			} else {
				/* We want all, so the first value should be empty
				so you're able to choose no title AND only if no specific ID is selected*/
				$return[0] = " ";
			}
			$res = sql_query($sql);

			while ($row = sql_fetch_assoc($res)) {
				$return[$row["id"]] = $row;
			}

			return $return;
		}
		/* }}} */
		/* getLetterheads {{{*/
		/**
		 * generate array with letterheads
		 *
		 * This is used in a lot of places.
		 * Hence the function
		 *
		 * @return array all relevant info
		 */
		public function getLetterheads($id=-1) {
			// check cache
			if ($id == -1 && $this->_cache["letterheads"])
				return $this->_cache["letterheads"];

			$sql = "SELECT id, title FROM address_letterhead";
			if($id > -1) {
				$sql .= sprintf(" WHERE id = %d", $id);
			} else {
				/* We want all, so the first value should be empty
				so you're able to choose no title AND only if no specific ID is selected*/
				$return[0] = " ";
			}
			$res = sql_query($sql);

			while ($row = sql_fetch_assoc($res)) {
				$return[$row["id"]] = $row;
			}
			if (!is_array($return))
				$return = array();

			// set cache
			if ($id == -1)
				$this->_cache["letterheads"] = $return;

			return $return;
		}
		/* }}} */
		/* getCommencements {{{*/
		/**
		 * generate array with commencements
		 *
		 * This is used in a lot of places.
		 * Hence the function
		 *
		 * @return array all relevant info
		 */
		public function getCommencements($id=-1) {
			// check cache
			if ($id == -1 && $this->_cache["commencement"]) 
				return $this->_cache["commencement"];
				
			$sql = "SELECT id, title FROM address_commencement";
			if($id > -1) {
				$sql .= sprintf(" WHERE id = %d", $id);
			} else {
				/* We want all, so the first value should be empty
				so you're able to choose no title AND only if no specific ID is selected*/
				$return[0] = " ";
			}
			$res = sql_query($sql);

			while ($row = sql_fetch_assoc($res)) {
				$return[$row["id"]] = $row;
			}
			if (!is_array($return))
				$return = array();

			// fill cache
			if ($id == -1)
				$this->_cache["commencement"] = $return;

			return $return;
		}
		/* }}} */
		/* save_bcard {{{ */
		/**
		 * store modified/created bcard in db
		 */
		public function save_bcard($bcardinfo, $metafields = "", $returnid = 0, $update_modified_field = 1) {
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
			if ($GLOBALS["covide"]->license["has_funambol"]) {
				$funambol = new Funambol_data();
				$funambol->deleteAddressById($cardid, "address_businesscards");
			}
			$sql = sprintf("DELETE FROM address_businesscards WHERE id=%d", $cardid);
			sql_query($sql);

			$sql = sprintf("DELETE FROM address_birthdays WHERE bcard_id=%d", $cardid);
			sql_query($sql);
		}
		/* }}} */
		/* gendebtornr {{{ */
		/**
		 * Generate debtor number based on data already in database
		 * Outputs javascript function update_debtor_nr(newnumber)
		 * To be used in AJAX calls
		 */
		public function gendebtornr($prefix="") {
			if (!$prefix) {
				/* the easy way */
				/* just lookup the number and do +1 */

				$sql = "SELECT MAX(debtor_nr) as currentnr FROM address";
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
				$nextnr = $row["currentnr"]+1;
				echo "update_debtor_nr(".$nextnr.");";
			}	else {
				/* the hard way */
				/* lookup by prefix and try to lookup the next number and preserve
				   the padding of previous numbers */

				$like = sql_syntax("like");
				$sql = sprintf("select debtor_nr from address where debtor_nr %s '%s%%'",
					$like, $prefix);
				$res = sql_query($sql);
				$debs = array();
				while ($row = sql_fetch_assoc($res)) {
					$reg = "/^".$prefix."/si";
					$curlen = strlen($row["debtor_nr"]);
					$debs[] = (int)preg_replace($reg, "", $row["debtor_nr"]);
				}
				asort($debs);
				$nextnr = sprintf("%s%d", $prefix, end($debs)+1);
				if (strlen($nextnr) < $curlen) {
					$nextnr = sprintf("%s%0".($curlen-strlen($prefix))."s",
						$prefix, end($debs)+1);
				}
				echo "update_debtor_nr('".$nextnr."');";
			}
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

			if ($_REQUEST["subact"] == "notify") {
				$div = new Layout_output();
				$div->addTag("img", array(
					"src" => "img/bar.png"
				));

				$output->layout_page("sync", 1);
				$output->insertTag("b", gettext("Updating selections... this operation could take some time."));

				$output->addTag("br");
				$output->addTag("br");
				$output->insertTag("marquee", $div->generate_output(), array(
					"id"           => "marquee_progressbar",
					"behavoir"     => "scroll",
					"style"        => "width: 300px; background-color: #f1f1f1; height: 10px; border: 1px solid #b0b2c6; margin-top: 10px;",
					"scrollamount" => 3,
					"direction"    => "right",
					"scrolldelay"  => 60
				));

				$output->layout_page_end();
				$output->exit_buffer();
			}
			/* check if record is in the db. */
			/* if it is, remove, if not, insert */

			/* Funambol module */
			/* ===================================== */
			if ($GLOBALS["covide"]->license["has_funambol"]) {
				set_time_limit(60*60);
				/* create funambol object */
				$funambol = new Funambol_data();
				$image = $funambol->toggleAddressSync($user, $address_id, $address_table);
				$alt = gettext("sync");
			}
			/* ===================================== */

			unset($output);
			echo "document.getElementById('toggle_sync_".$address_id."').src='$image';";
			echo "document.getElementById('toggle_sync_".$address_id."').alt='$alt';";
			echo "document.getElementById('toggle_sync_".$address_id."').title='$alt';";
		}
		/* }}} */
		/* getEmployeeBirthdays {{{ */
		/**
		 * Fetch birthdays of the employees
		 */
		public function getEmployeeBirthdays() {
			$q = "SELECT id, givenname, surname, timestamp_birthday FROM address_private WHERE timestamp_birthday != 0 AND timestamp_birthday IS NOT NULL AND id IN (select address_id from users where is_active = 1 AND address_id IS NOT NULL)";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$day = date("d", $row["timestamp_birthday"]);
				$month = date("m", $row["timestamp_birthday"]);
				if($month == date("m") && ($day >= date("d") && $day <= date("d")+3)) {
					$diff = mktime(
						date("H"),
						date("i"),
						date("s"),
						date("m", $row["timestamp_birthday"]),
						date("d", $row["timestamp_birthday"]),
						date("Y")) - mktime();

					$days = ceil($diff/24/60/60);
					$field[$row["id"]] = $row;
					$field[$row["id"]]["age"] = (int)(date("Y") - date("Y", $row["timestamp_birthday"]));
					$field[$row["id"]]["days"] = $days;
				}
			}
			return $field;
		}
		/* }}} */
		/* getBirthDays {{{ */
		/**
		 * Get an array with people who celebrate their birthday today.
		 *
		 * @return array name, companyname, timestamp of birth and age
		 */
		public function getBirthDays() {
			require(self::include_dir."dataGetBirthdays.php");
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
				$output->addCode("setTimeout('window.close();', 100);");
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
					$row["human_gender"] = ($row["gender"] == 1?gettext("male"):gettext("female"));
					if ($row["timestamp_started"] > 0)
						$row["human_start"]  = date("d-m-Y", $row["timestamp_started"]);
					if ($row["timestamp_stop"])
						$row["human_end"]    = date("d-m-Y", $row["timestamp_stop"]);
					if ($row["timestamp_birthday"])
						$row["human_bday"]   = date("d-m-Y", $row["timestamp_birthday"]);
					$hrminfo[] = $row;
				}
			} else {
				$hrminfo = array("0" => gettext("none"));
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
		/* savecla_multi {{{ */
		/**
		 * Add new classification to selection of addresses
		 *
		 * @param array new classification name and options array to get the relevant addresses from addressbook
		 */
		public function savecla_multi($data) {
			if (!$data["inline"]) {
				$new_cla_id = $data["addcla"]["classification_id"];
				$data["options"]["nolimit"] = 1;
				$addresses = $this->getRelationsList($data["options"]);
				if (is_array($addresses["address"])) {
					foreach ($addresses["address"] as $address) {
						$address_id = $address["id"];
						$classifications = explode("|", $address["classification"]);
						/* sanitize */
						$classifications[] = $new_cla_id;
						foreach ($classifications as $k=>$v) {
							if (!$v) {
								unset($classifications[$k]);
							}
						}
						$classifications = array_unique($classifications);
						$cla = "|".implode("|", $classifications)."|";
						$id_field = ($data["options"]["addresstype"] == "relations") ? "address_id" : "id";
						$sql = sprintf("UPDATE address_businesscards SET classification = '%s' WHERE %s = %d AND rcbc = 1", $cla, $id_field, $address_id);
						$res = sql_query($sql);
					}
				}
			} else {
				$new_cla_id = $data["addcla"]["classification_id"];
				//get current classifications
				if ($data["addresstype"] == "relation") {
					$upd = "UPDATE address_businesscards SET classification = '%s' WHERE address_id = %d AND rcbc = 1";
					$sql = sprintf("SELECT classification from address_businesscards WHERE address_id = %d AND rcbc = 1", $data["addressid"]);
					$res = sql_query($sql);
				} else {
					$upd = "UPDATE address_businesscards SET classification = '%s' WHERE id = %d";
					$sql = sprintf("SELECT classification from address_businesscards WHERE id = %d", $data["addressid"]);
					$res = sql_query($sql);
				}
				$row = sql_fetch_assoc($res);
				$classifications = explode("|", $row["classification"]);
				/* sanitize */
				$classifications[] = $new_cla_id;
				foreach ($classifications as $k=>$v) {
					if (!$v) {
						unset($classifications[$k]);
					}
				}
				$classifications = array_unique($classifications);
				$cla = "|".implode("|", $classifications)."|";
				$sql = sprintf($upd, $cla, $data["addressid"]);
				$res = sql_query($sql);
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
			$sql = "SELECT * FROM meta_table ORDER BY fieldorder";
			$res = sql_query($sql);
			$fields = array();
			$i = 0;
			while ($row = sql_fetch_assoc($res)) {
				$i++;
				$fields[$i] = $row;
				/* friendly type name */
				switch ($row["fieldtype"]) {
					case 1: $fields[$i]["h_fieldtype"] = gettext("short text"); break;
					case 2: $fields[$i]["h_fieldtype"] = gettext("long text"); break;
					case 3: $fields[$i]["h_fieldtype"] = gettext("date");       break;
					case 4: $fields[$i]["h_fieldtype"] = gettext("yes/no");      break;
				}
			}
			return $fields;
		}
		/* }}} */
		/* get_global{{{ */
		/**
		 * Get the global metafields from db
		 * @param ID if looking for a specific global field
		 */
		public function get_global($id=0) {
			if($id != 0) { $meta_id = "AND id = $id"; } else { $meta_id = ""; }
			$sql = "SELECT * FROM meta_table WHERE record_id = 0 $meta_id ORDER BY id";
			$res = sql_query($sql);
			$fields = array();
			$i = 0;
			while ($row = sql_fetch_assoc($res)) {
				$i++;
				$fields[$i] = $row;
				/* friendly type name */
				switch ($row["fieldtype"]) {
					case 1: $fields[$i]["h_fieldtype"] = gettext("short text"); break;
					case 2: $fields[$i]["h_fieldtype"] = gettext("long text"); break;
					case 3: $fields[$i]["h_fieldtype"] = gettext("date");       break;
					case 4: $fields[$i]["h_fieldtype"] = gettext("yes/no");      break;
				}
			}
			return $fields;
		}
		/* }}} */
		/* saveExportInfo {{{ */
		public function saveExportInfo($exportoptions) {
			$this->cleanupExportInfo();

			$q = sprintf("insert into address_selections (user_id, datetime, info) values (%d, %d, '%s')",
				$_SESSION["user_id"], mktime(), serialize($exportoptions));
			sql_query($q);
			$return = sql_insert_id("address_selections");
			return $return;
		}
		/* }}} */
		/* getExportInfo {{{ */
		public function getExportInfo($id) {
			$q = sprintf("select info from address_selections where user_id = %d and id = %d",
				$_SESSION["user_id"], $id);
			$res = sql_query($q);
			if (sql_num_rows($res)>0) {
				return unserialize(sql_result($res, 0));
			}
		}
		/* }}} */
		/* cleanupExportInfo {{{ */
		public function cleanupExportInfo() {
			$ts = mktime() - (60 * 60 * 24); //24 hours
			$q = sprintf("delete from address_selections where datetime < %d", $ts);
			sql_query($q);
		}
		/* }}} */
		/* updateBcardRelations {{{ */
		/**
		 * Update the relations on a businesscard
		 *
		 * @param int $id the businesscard in question
		 * @param int $address_id The main address id
		 * @param string $multirel comma seperated string with extra address_ids
		 */
		public function updateBcardRelations($id, $address_id, $multirel) {
			$q = sprintf("delete from address_businesscards_info where bcard_id = %d", $id);
			sql_query($q);

			$ids = array();
			if ($multirel)
				$ids = explode(",", $ids);

			$ids[] = $address_id;
			$ids = array_unique($ids);
			foreach ($ids as $v) {
				if ($v) {
					$q = sprintf("insert into address_businesscards_info (address_id, bcard_id)
						values (%d, %d)", $v, $id);
					sql_query($q);
				}
			}
		}
		/* }}} */
		/* cleanOrphanedItems {{{ */
		public function cleanOrphanedItems() {
			/* address */
			echo "\ntable: address_businesscards_info - address_id\n";
			$q = "select * from address_businesscards_info where address_id NOT IN (
				select id from address)";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				echo sprintf("[%d] could not find address id: %d\n", $row["id"], $row["address_id"]);
			}
			/* bcards */
			echo "\ntable: address_businesscards_info - bcard_id\n";
			$q = "select * from address_businesscards_info where bcard_id NOT IN (
				select id from address_businesscards)";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				echo sprintf("[%d] could not find bcard_id: %d\n", $row["id"], $row["bcard_id"]);
			}
			/* address_info */
			echo "\ntable: address_info\n";
			$q = "select * from address_info where address_id NOT IN (select id from address)";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				echo sprintf("[%d] could not find address_id: %d\n", $row["id"], $row["address_id"]);
			}
			/* address_businesscard */
			echo "\ntable: address_businesscards - address_id\n";
			$q = "select * from address_businesscards where address_id NOT IN (select id from address)";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				echo sprintf("[%d] could not find address_id: %d\n", $row["id"], $row["address_id"]);
			}
			/* address_businesscard */
			echo "\ntable: address_businesscards - multirel\n";
			$keys = array();
			$q = "select id, multirel from address_businesscards";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				if ($row["multirel"]) {
					$keys = explode(",", $row["multirel"]);
					foreach ($keys as $k=>$v) {
						if (!$v) {
							unset($keys[$k]);
							echo sprintf("[%d] empty id not allowed: %d\n", $row["id"], $row["address_id"]);
						} else {
							$q = sprintf("select count(*) from address where id = %d", $v);
							$res2 = sql_query($q);
							if (sql_num_rows($res2,0) == 0) {
								unset($keys[$k]);
								echo sprintf("[%d] could not find address_id: %d\n", $row["id"], $row["address_id"]);
							}
						}
					}
				}
			}
		}
		/* }}} */
		/* getRecord {{{ */
		public function getRecord($options = array()) {
			if (!isset($options["id"]) || !$options["id"])
				die("no id given");
			switch ($options["type"]) {
			case "relation":
				$table = "address";
				break;
			case "user":
				$table = "address_private";
				break;
			}
			$sql = sprintf("SELECT * FROM %s WHERE id = %d", $table, $options["id"]);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			return $row;
		}
		/* }}} */
		/* keepPrivate {{{ */
		public function keepPrivate($id) {
			$q = sprintf("update address_private set sync_added = 0 where id = %d", $id);
			sql_query($q);
		}
		/* }}} */
		/* deactivateSelection{{{*/
		/**
		* Sets a addressbook selection on non-active
		*
		* @param array with options for getRelationlist
		*/
		public function deactivateSelection($options) {
			if (is_array($options)) {
				$addressdata = new Address_data();
				$addresses = $addressdata->getRelationsList($options["options"]);
				foreach ($addresses["address"] AS $k) {
					$q = sprintf("update address set is_active = 0 where id = %d", $k["id"]);
					sql_query($q);
				}
			}
			/* small output to close window */
			$output = new Layout_output();
			$output->layout_page("", 1);
			$output->start_javascript();
				$output->addCode("opener.location.href = opener.location.href;");
				$output->addCode("setTimeout('window.close();', 100);");
			$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* deleteSelectionExec{{{*/
		/**
		* Delete a selection of non-active addresses
		*
		* @param array with options for getRelationlist
		*/
		public function deleteSelectionExec($options) {
			if (is_array($options)) {
				$addressdata = new Address_data();
				$addresses = $addressdata->getRelationsList($options["options"]);
				foreach ($addresses["address"] AS $k) {
					$q = sprintf("DELETE FROM address_info WHERE address_id = %d", $k["id"]);
					sql_query($q);
					$q = sprintf("DELETE FROM address WHERE is_active = 0 AND id = %d", $k["id"]);
					sql_query($q);
				}
			}
			/* small output to close window */
			$output = new Layout_output();
			$output->layout_page("", 1);
			$output->start_javascript();
				$output->addCode("opener.location.href = opener.location.href;");
				$output->addCode("setTimeout('window.close();', 100);");
			$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* listCountries {{{ */
		public function listCountries($only_db=0) {
			require(self::include_dir."dataListCountries.php");
			return $countryArray;
		}
		/* }}} */
		/* syncMultivers {{{ */
		public function syncMultivers() {
			require(self::include_dir."dataSyncMultivers.php");
		}
		/* }}} */
		/* readMultivers {{{ */
		public function readMultivers($database_file) {
			$data = file_get_contents($database_file);
			$parser = xml_parser_create();
				xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
				xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,0);
				xml_parse_into_struct($parser,$data,$values,$tags);
				xml_parser_free($parser);
				unset($parser);
				unset($data);
				// loop through the structures
				foreach ($tags as $key=>$val) {
					if ($key != "Organization")
						continue;
					$multiversranges = $val;
					for ($i=0; $i < count($multiversranges); $i++) {
						$offset = $multiversranges[$i] + 1;
						$len = $multiversranges[$i + 1] - $offset;
						$tdb[] = $this->parseMultivers(array_slice($values, $offset, $len));
						//TODO: update/insert address record here and unset the object $tdb
					}
				}
				return $tdb;
		}
		/* }}} */
		/* parseMultivers {{{ */
		public function parseMultivers($mvalues) {
			for ($i=0; $i < count($mvalues); $i++)
				$mval[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];
			$a = new multivers_addies($mval);
			return $a;
		}
		/* }}} */
		/* multiversAccManager {{{ */
		public function multiversAccManager($multivers_vert) {
			$accountmanagers = $this->getMultiversAccManagers();
			if (!$multivers_vert)
				return 0;
			if (array_key_exists($multivers_vert, $accountmanagers))
				return $accountmanagers[$multivers_vert];
			else
				return $multivers_vert;
		}
		/* }}} */
		/* getMultiversAccManagers {{{ */
		public function getMultiversAccManagers() {
			$managers = array(
				8 => 16
			);
			return $managers;
		}
		/* }}} */
		/* getAddressIdByDebtor {{{ */
		public function getAddressIdByDebtor($deb) {
			$q = sprintf("select * from address where debtor_nr = '%s'",
				$deb);
			$res = sql_query($q);
			if (sql_num_rows($res) > 0)
				return sql_result($res,0);
			else
				return 0;
		}
		/* }}} */
		/* checkrcbc {{{ */
		/**
		 * Check if address already has a rcbc, and if not creates it
		 *
		 * @param array $addressdata The data as returned by getAddressById
		 *
		 * @return int The rcbc id
		 */
		public function checkrcbc($addressdata) {
			require(self::include_dir."dataCheckRcBc.php");
			return $rcbc_id;
		}
		/* }}} */
		/* getRCBCByAddressId {{{ */
		/**
		 * Grab the RCBC for a specific relation
		 *
		 * @param int $address_id The relations id
		 *
		 * @return array RCBC info, false if none exists
		 */
		public function getRCBCByAddressId($address_id) {
			$sql = sprintf("SELECT b.*, a.companyname FROM address_businesscards b, address a WHERE a.id = b.address_id AND b.rcbc = 1 AND b.address_id = %d", $address_id);
			$res = sql_query($sql);
			if (sql_num_rows($res)) {
				return sql_fetch_assoc($res);
			} else {
				return false;
			}
		}
		/* }}} */
		/* getBcardById {{{ */
		/**
		 * Gets the entire bussinesscard row by an id
		 */
		public function getBcardById($id) {
			$sql = sprintf("SELECT * FROM address_businesscards WHERE id = %d", $id);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$data = $row;
			}
			return $data;
		}
		/* }}} */
		/* importVcard_save {{{ */
		/**
		 * Save prepared import data into database.
		 * This function will output mini page to refresh the opener and close the popup
		 *
		 * @param array The prepared import data
		 */
		public function importVcard_save($data) {
			require(self::include_dir."dataImportVcardSave.php");
			/* small output to close window */
			$output = new Layout_output();
			$output->layout_page("", 1);
			$output->start_javascript();
				$output->addCode("opener.location.href = opener.location.href;");
				$output->addCode("setTimeout('window.close();', 100);");
			$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
		}
		/* }}} */
		/* getRelationIdByName {{{ */
		/**
		 * Find an id by company name.
		 *
		 * @param name Name of the company
		 */
		public function getRelationIdByName($name) {
			$sql = sprintf("SELECT id FROM address WHERE companyname = '%s'", $name);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$id = $row["id"];
			}
			return $id;
		}
		/* }}} */
		/* saveSortAndSelect {{{ */
		/**
		 * Saves the fields and sort order array into the db
		 *
		 * @param array $data field & sort array	
		 */
		public function saveSortAndSelect($data, $type) {
			/* update the right field */
			switch ($type) {
				case "bcards":
					$sql_field = "default_address_fields_bcard";
				break;
				case "relations":
					$sql_field = "default_address_fields";
				break;
			}
			/* strip empty ones */
			foreach ($data as $field=>$sort) {
				if ($sort) {
					$sortdata[$field] = $sort;
				}
			}
			/* order them by sort order and maintain indices */
			krsort($sortdata);  // order by key (name)
			asort($sortdata);   // order by value (sort order)
			/* update users settings */
			$sql = sprintf("UPDATE users SET %s = '%s' WHERE id = %d",
				$sql_field, serialize($sortdata), $_SESSION["user_id"]
			);
			$result = sql_query($sql);
			
			/* close the popup */
			$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("
						opener.location.href = opener.location.href;
						window.close();
					");
				$output->end_javascript();
			$output->exit_buffer();
		}
		/* }}} */
		/* getAddressIdByClassification {{{ */
		/**
		 * Get all address ID's that contain a specific classification
		 *
		 * @param int $id The classification ID
		 *
		 * @return array Address IDs and company names in multirel format. (id = companyname)
		 */
		public function getAddressIdByClassification($id) {
			$regex_syntax = sql_syntax("regex");
			$q = sprintf("SELECT b.address_id, a.companyname FROM address_businesscards b LEFT JOIN address a ON a.id = b.address_id WHERE b.rcbc = 1");
			$q .= sprintf(" AND (b.classification %s '(^|\\\\|)%d(\\\\||$)')", $regex_syntax, $id);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$data[$row["address_id"]] = $row["companyname"];
			}
			return $data;
		}
		/* }}} */
	}

	class multivers_addies {
		function __construct($aa) {
			if (!is_array($aa)) {
				$aa = Array();
			}
			foreach ($aa as $k=>$v)
				$this->$k = $aa[$k];
		}
	}
?>
