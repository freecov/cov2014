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
		public function store2db($address, $metafields = array(), $skip_funambol = 0) {
			require(self::include_dir."dataStore2db.php");
			if ($skip_funambol)
				return $new_id;
		}
		/* }}} */
		/* delete {{{ */
	    /**
	     * 	delete addressinfo in db
	     *
		 * @param int the id of the address
		 * @param string the addresstype
	     */
		public function delete($address_id, $addresstype, $skip_funambol=0) {
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
			$user = new User_data();
			$user->getUserPermissionsById($_SESSION["user_id"]);
			$accmanager_arr = explode(",", $user->permissions["addressaccountmanage"]);
			if (!$accmanager_arr[0])
				unset($accmanager_arr[0]);
			$accmanager_arr[] = $_SESSION["user_id"];

			if ($type == "bcards") {
				$sql = sprintf("SELECT address_businesscards.*, address.companyname FROM address_businesscards left join address on address.id = address_businesscards.address_id WHERE address_businesscards.id=%d", $addressid);
				$res = sql_query($sql);
				$adresinfo = sql_fetch_assoc($res);
				if (!is_array($adresinfo))
					$adresinfo = array();
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
				$adresinfo["fullname"] = $adresinfo["givenname"]." ".$adresinfo["infix"]." ".$adresinfo["surname"];
				$adresinfo["fullname"] = preg_replace("/\W{2,}/si", " ", $adresinfo["fullname"]);
			} elseif ($type != "relations" && $type != "nonactive" && $type != "address") {
				$sql = sprintf("SELECT * FROM address_private WHERE id=%d", $addressid);
				$res = sql_query($sql);
				$adresinfo = sql_fetch_assoc($res);
				$adresinfo["phone_nr_link"] = $this->show_phonenr($adresinfo["phone_nr"]);
				$adresinfo["mobile_nr_link"] = $this->show_phonenr($adresinfo["mobile_nr"]);
				$adresinfo["fullname"] = $adresinfo["givenname"]." ".$adresinfo["infix"]." ".$adresinfo["surname"];
				$adresinfo["fullname"] = preg_replace("/\W{2,}/si", " ", $adresinfo["fullname"]);
			} else {
				$sql = sprintf("SELECT address.*, address_info.classification as classifi, address_info.provision_perc, address_info.warning as letop, address_info.comment as memo, address_info.classification as classifi, address_info.photo as photo FROM address,address_info WHERE address_info.address_id=address.id AND address.id=%d", $addressid);
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

				$adresinfo["letterinfo"] = $this->generate_letterinfo(array(
					"contact_initials"     => $adresinfo["contact_initials"],
					"contact_letterhead"   => $adresinfo["contact_letterhead"],
					"contact_commencement" => $adresinfo["contact_commencement"],
					"contact_givenname"    => $adresinfo["contact_givenname"],
					"contact_infix"        => $adresinfo["contact_infix"],
					"contact_surname"      => $adresinfo["contact_surname"],
					"title"                => $adresinfo["contact_title"]
				));
				$adresinfo["tav"] = $adresinfo["letterinfo"]["tav"];
				$adresinfo["contact_person"] = $adresinfo["letterinfo"]["contact_person"];

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
					if ($GLOBALS["covide"]->license["has_twinfield"]) {
						/* get the twinfield office this address is in */
						$q = sprintf("SELECT office_id FROM address_info_twinfield WHERE address_id = %d", $addressid);
						$r = sql_query($q);
						$adresinfo["twinfield_office"] = sql_result($r, 0);
					}
				}
				/* fix website */
				if (strpos($adresinfo["website"], "http://") !== 0) {
					$adresinfo["website"] = "http://".$adresinfo["website"];
				}
				$adresinfo["classification_names"] = $classification_names;
				$adresinfo["phone_nr_link"] = $this->show_phonenr($adresinfo["phone_nr"]);
				$adresinfo["mobile_nr_link"] = $this->show_phonenr($adresinfo["mobile_nr"]);
				$adresinfo["fullname"] = $adresinfo["givenname"]." ".$adresinfo["infix"]." ".$adresinfo["surname"];
				$adresinfo["fullname"] = preg_replace("/\W{2,}/si", " ", $adresinfo["fullname"]);
			}

			if ($user->checkPermission("xs_addressmanage")) {
				$adresinfo["addressmanage"] = 1;
				$adresinfo["addressacc"]    = 1;
			} else {
				$adresinfo["addressmanage"] = 0;
				if ($adresinfo["account_manager"] && in_array($adresinfo["account_manager"], $accmanager_arr)) {
					$adresinfo["addressacc"] = 1;
				} else {
					$adresinfo["addressacc"] = 0;
					$adresinfo["noaccess"]   = 1;
				}
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
			$companies[0] = gettext("none");
			return $companies;
		}
		/* }}} */
		/* 	lookupRelationEmail {{{ */
	    /**
	     * 	lookupRelationEmail Put all relationemails + bcards and ids in an array
	     * 	if mail address is double, replace the id with -1
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
			$q = sprintf("select id from address where lower(email) $like '%%%1\$s%%$'", $filter);
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
		/* 	lookupRelationEmailCommencement {{{ */
	    /**
	     * 	Put all relationemails + bcards and ids in an array
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
			$q = "select email, business_email, personal_email, other_email, address_id as id, multirel from address_businesscards where lower(email) like '$filter' or lower(business_email) like '$filter' or lower(personal_email) like '$filter'";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$row["email"]          = trim(strtolower($row["email"]));
				$row["business_email"] = trim(strtolower($row["business_email"]));
				$row["personal_email"] = trim(strtolower($row["personal_email"]));
				$row["other_email"]    = trim(strtolower($row["other_email"]));
				$address_ids = preg_replace("/^,/", "", preg_replace("/,$/", "", $row["id"].",".$row["multirel"]));
				$address_ids = explode(",", $address_ids);
				$address_ids = array_unique($address_ids);

				if (preg_match("/.*@.*\..*/s", $row["email"])) {
					if (count($address_ids)>1) {
						$mails[$row["email"]] = -1;
					} else {
						if ($mails[$row["email"]] && !in_array($mails[$row["email"]], $address_ids)) {
							$mails[$row["email"]] = -1;
						} else {
							$mails[$row["email"]] = $row["id"];
						}
					}
				}
				if (preg_match("/.*@.*\..*/s", $row["business_email"])) {
					if (count($address_ids)>1) {
						$mails[$row["business_email"]] = -1;
					} else {			
						if ($mails[$row["business_email"]] && !in_array($mails[$row["business_email"]], $address_ids)) {
							$mails[$row["business_email"]] = -1;
						} else {
							$mails[$row["business_email"]] = $row["id"];
						}
					}
				}
				if (preg_match("/.*@.*\..*/s", $row["personal_email"])) {
					if (count($address_ids)>1) {
						$mails[$row["personal_email"]] = -1;
					} else {
						if ($mails[$row["personal_email"]] && !in_array($mails[$row["personal_email"]], $address_ids)) {
							$mails[$row["personal_email"]] = -1;
						} else {
							$mails[$row["personal_email"]] = $row["id"];
						}
					}
				}
				if (preg_match("/.*@.*\..*/s", $row["other_email"])) {
					if (count($address_ids)>1) {
						$mails[$row["other_email"]] = -1;
					} else {
						if ($mails[$row["other_email"]] && !in_array($mails[$row["other_email"]], $address_ids)) {
							$mails[$row["other_email"]] = -1;
						} else {
							$mails[$row["other_email"]] = $row["id"];
						}
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
		public function getBcardsByRelationID($address_id, $search="") {
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
	     * 	generate array with titles
		 *
		 * This is used in a lot of places.
		 * Hence the function
	     *
	     * @return array all relevant info
	     */
		public function getTitles($id = 0, $justTitles=0) {
		/* Only use this list if no database population is specified */
			$returnBackUp = array(
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
			if($id!=0) { $whereClause = sprintf("WHERE id =%d" ,$id); } else { $whereClause = ""; }
			if($justTitles!=0) { $selectClause = "title"; } else { $selectClause = "*"; }
			$sql = "SELECT $selectClause FROM address_titles $whereClause";
			$res = sql_query($sql);
			/* Make sure the first entry will be a space so you're able to choose no title AND only if no specific ID is selected*/
			if($id==0) {
				$return[0] = " ";
			}
			while ($row = sql_fetch_assoc($res)) {
				$return[] = $row;
			}

			/* If database population is not specified, use the backup list. */
			if(!is_array($return)) { $return = $returnBackUp; }
			return $return;
		}
		/* }}} */

		/* saveTitles {{{*/
	    /**
	     * 	Saves the titles in the DB
	    *
	     * @param array new title data
	     * @return true
	     */
			public function saveTitles($data) {
			$titles = $data["titles"];
			if($data["method"] == "edit") {
				$q = sprintf("UPDATE address_titles SET title = '%s' WHERE id = %d", $titles[0]["title"], $titles[0]["id"]);
				sql_query($q);
			} 
			if($data["method"] == "new") {
				$q = sprintf("INSERT INTO address_titles (title) VALUES ('%s')", $titles[0]["title"]);
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
		/* removeTitles {{{*/
	    /**
	     * 	Remove a specific ID from the database
		 *
	     * @param int ID number of title
	     * @return array all relevant info
	     */
		public function removeTitles($id) {
		if($id["id"]) {
			$sql = sprintf("DELETE FROM address_titles WHERE id = %d", $id["id"]);
			$res = sql_query($sql);
			return true;
		 }
		}
		/* }}} */
		
		public function getSuffix() {
			$return = array(
        0 => " ",
				1 => "I",
				2 => "II",
				3 => "III",
				4 => "Jr.",
				5 => "Sr."
			);
			return $return;
		}

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
		/* getBirthDays {{{ */
		/**
		 * Get an array with people who celebrate their birthday today.
		 *
		 * @return array name, companyname, timestamp of birth and age
		 */
		public function getBirthDays() {

			$esc = sql_syntax("escape_char");

			$q = "select id, timestamp_birthday from address_businesscards where timestamp_birthday != 0 AND id NOT IN (select bcard_id from address_birthdays)";
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
			}

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
			$sql = sprintf("INSERT INTO address_classifications (description, is_active) VALUES ('%s', 1)", $data["addcla"]["name"]);
			$res = sql_query($sql);
			$new_cla_id = sql_insert_id("address_classifications");
			$data["options"]["nolimit"] = 1;
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


		public function saveExportInfo($exportoptions) {
			$this->cleanupExportInfo();

			$q = sprintf("insert into address_selections (user_id, datetime, info) values (%d, %d, '%s')",
				$_SESSION["user_id"], mktime(), serialize($exportoptions));
			sql_query($q);
			$return = sql_insert_id("address_selections");
			return $return;
		}
		public function getExportInfo($id) {
			$q = sprintf("select info from address_selections where user_id = %d and id = %d",
				$_SESSION["user_id"], $id);
			$res = sql_query($q);
			if (sql_num_rows($res)>0) {
				return unserialize(sql_result($res, 0));
			}
		}
		public function cleanupExportInfo() {
			$ts = mktime() - (60 * 60 * 24); //24 hours
			$q = sprintf("delete from address_selections where datetime < %d", $ts);
			sql_query($q);
		}

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

		public function keepPrivate($id) {
			$q = sprintf("update address_private set sync_added = 0 where id = %d", $id);
			sql_query($q);
		}
		public function listCountries() {
			$countryArray = array( 
			'XX' => "",
			'AF' => gettext('Afghanistan'),
			'AL' => gettext('Albania'),
			'DZ' => gettext('Algeria'),
			'AS' => gettext('American Samoa'),
			'AD' => gettext('Andorra'),
			'AO' => gettext('Angola'),
			'AI' => gettext('Anguilla'),
			'AQ' => gettext('Antarctica'),
			'AG' => gettext('Antigua And Barbuda'),
			'AR' => gettext('Argentina'),
			'AM' => gettext('Armenia'),
			'AW' => gettext('Aruba'),
			'AU' => gettext('Australia'),
			'AT' => gettext('Austria'),
			'AZ' => gettext('Azerbaijan'),
			'BS' => gettext('Bahamas'),
			'BH' => gettext('Bahrain'),
			'BD' => gettext('Bangladesh'),
			'BB' => gettext('Barbados'),
			'BY' => gettext('Belarus'),
			'BE' => gettext('Belgium'),
			'BZ' => gettext('Belize'),
			'BJ' => gettext('Benin'),
			'BM' => gettext('Bermuda'),
			'BT' => gettext('Bhutan'),
			'BO' => gettext('Bolivia'),
			'BA' => gettext('Bosnia And Herzegowina'),
			'BW' => gettext('Botswana'),
			'BV' => gettext('Bouvet Island'),
			'BR' => gettext('Brazil'),
			'IO' => gettext('British Indian Ocean Territory'),
			'BN' => gettext('Brunei Darussalam'),
			'BG' => gettext('Bulgaria'),
			'BF' => gettext('Burkina Faso'),
			'BI' => gettext('Burundi'),
			'KH' => gettext('Cambodia'),
			'CM' => gettext('Cameroon'),
			'CA' => gettext('Canada'),
			'CV' => gettext('Cape Verde'),
			'KY' => gettext('Cayman Islands'),
			'CF' => gettext('Central African Republic'),
			'TD' => gettext('Chad'),
			'CL' => gettext('Chile'),
			'CN' => gettext('China'),
			'CX' => gettext('Christmas Island'),
			'CC' => gettext('Cocos (Keeling) Islands'),
			'CO' => gettext('Colombia'),
			'KM' => gettext('Comoros'),
			'CG' => gettext('Congo'),
			'CD' => gettext('Congo, The Democratic Republic Of The'),
			'CK' => gettext('Cook Islands'),
			'CR' => gettext('Costa Rica'),
			'CI' => gettext('Cote D\'Ivoire'),
			'HR' => gettext('Croatia (Local Name: Hrvatska)'),
			'CU' => gettext('Cuba'),
			'CY' => gettext('Cyprus'),
			'CZ' => gettext('Czech Republic'),
			'DK' => gettext('Denmark'),
			'DJ' => gettext('Djibouti'),
			'DM' => gettext('Dominica'),
			'DO' => gettext('Dominican Republic'),
			'TP' => gettext('East Timor'),
			'EC' => gettext('Ecuador'),
			'EG' => gettext('Egypt'),
			'SV' => gettext('El Salvador'),
			'GQ' => gettext('Equatorial Guinea'),
			'ER' => gettext('Eritrea'),
			'EE' => gettext('Estonia'),
			'ET' => gettext('Ethiopia'),
			'FK' => gettext('Falkland Islands (Malvinas)'),
			'FO' => gettext('Faroe Islands'),
			'FJ' => gettext('Fiji'),
			'FI' => gettext('Finland'),
			'FR' => gettext('France'),
			'FX' => gettext('France, Metropolitan'),
			'GF' => gettext('French Guiana'),
			'PF' => gettext('French Polynesia'),
			'TF' => gettext('French Southern Territories'),
			'GA' => gettext('Gabon'),
			'GM' => gettext('Gambia'),
			'GE' => gettext('Georgia'),
			'DE' => gettext('Germany'),
			'GH' => gettext('Ghana'),
			'GI' => gettext('Gibraltar'),
			'GR' => gettext('Greece'),
			'GL' => gettext('Greenland'),
			'GD' => gettext('Grenada'),
			'GP' => gettext('Guadeloupe'),
			'GU' => gettext('Guam'),
			'GT' => gettext('Guatemala'),
			'GN' => gettext('Guinea'),
			'GW' => gettext('Guinea-Bissau'),
			'GY' => gettext('Guyana'),
			'HT' => gettext('Haiti'),
			'HM' => gettext('Heard And Mc Donald Islands'),
			'VA' => gettext('Holy See (Vatican City State)'),
			'HN' => gettext('Honduras'),
			'HK' => gettext('Hong Kong'),
			'HU' => gettext('Hungary'),
			'IS' => gettext('Iceland'),
			'IN' => gettext('India'),
			'ID' => gettext('Indonesia'),
			'IR' => gettext('Iran (Islamic Republic Of)'),
			'IQ' => gettext('Iraq'),
			'IE' => gettext('Ireland'),
			'IL' => gettext('Israel'),
			'IT' => gettext('Italy'),
			'JM' => gettext('Jamaica'),
			'JP' => gettext('Japan'),
			'JO' => gettext('Jordan'),
			'KZ' => gettext('Kazakhstan'),
			'KE' => gettext('Kenya'),
			'KI' => gettext('Kiribati'),
			'KP' => gettext('Korea, Democratic People\'S Republic Of'),
			'KR' => gettext('Korea, Republic Of'),
			'KW' => gettext('Kuwait'),
			'KG' => gettext('Kyrgyzstan'),
			'LA' => gettext('Lao People\'S Democratic Republic'),
			'LV' => gettext('Latvia'),
			'LB' => gettext('Lebanon'),
			'LS' => gettext('Lesotho'),
			'LR' => gettext('Liberia'),
			'LY' => gettext('Libyan Arab Jamahiriya'),
			'LI' => gettext('Liechtenstein'),
			'LT' => gettext('Lithuania'),
			'LU' => gettext('Luxembourg'),
			'MO' => gettext('Macau'),
			'MK' => gettext('Macedonia, Former Yugoslav Republic Of'),
			'MG' => gettext('Madagascar'),
			'MW' => gettext('Malawi'),
			'MY' => gettext('Malaysia'),
			'MV' => gettext('Maldives'),
			'ML' => gettext('Mali'),
			'MT' => gettext('Malta'),
			'MH' => gettext('Marshall Islands'),
			'MQ' => gettext('Martinique'),
			'MR' => gettext('Mauritania'),
			'MU' => gettext('Mauritius'),
			'YT' => gettext('Mayotte'),
			'MX' => gettext('Mexico'),
			'FM' => gettext('Micronesia, Federated States Of'),
			'MD' => gettext('Moldova, Republic Of'),
			'MC' => gettext('Monaco'),
			'MN' => gettext('Mongolia'),
			'MS' => gettext('Montserrat'),
			'MA' => gettext('Morocco'),
			'MZ' => gettext('Mozambique'),
			'MM' => gettext('Myanmar'),
			'NA' => gettext('Namibia'),
			'NR' => gettext('Nauru'),
			'NP' => gettext('Nepal'),
			'NL' => gettext('Netherlands'),
			'AN' => gettext('Netherlands Antilles'),
			'NC' => gettext('New Caledonia'),
			'NZ' => gettext('New Zealand'),
			'NI' => gettext('Nicaragua'),
			'NE' => gettext('Niger'),
			'NG' => gettext('Nigeria'),
			'NU' => gettext('Niue'),
			'NF' => gettext('Norfolk Island'),
			'MP' => gettext('Northern Mariana Islands'),
			'NO' => gettext('Norway'),
			'OM' => gettext('Oman'),
			'PK' => gettext('Pakistan'),
			'PW' => gettext('Palau'),
			'PA' => gettext('Panama'),
			'PG' => gettext('Papua New Guinea'),
			'PY' => gettext('Paraguay'),
			'PE' => gettext('Peru'),
			'PH' => gettext('Philippines'),
			'PN' => gettext('Pitcairn'),
			'PL' => gettext('Poland'),
			'PT' => gettext('Portugal'),
			'PR' => gettext('Puerto Rico'),
			'QA' => gettext('Qatar'),
			'RE' => gettext('Reunion'),
			'RO' => gettext('Romania'),
			'RU' => gettext('Russian Federation'),
			'RW' => gettext('Rwanda'),
			'KN' => gettext('Saint Kitts And Nevis'),
			'LC' => gettext('Saint Lucia'),
			'VC' => gettext('Saint Vincent And The Grenadines'),
			'WS' => gettext('Samoa'),
			'SM' => gettext('San Marino'),
			'ST' => gettext('Sao Tome And Principe'),
			'SA' => gettext('Saudi Arabia'),
			'SN' => gettext('Senegal'),
			'SC' => gettext('Seychelles'),
			'SL' => gettext('Sierra Leone'),
			'SG' => gettext('Singapore'),
			'SK' => gettext('Slovakia (Slovak Republic)'),
			'SI' => gettext('Slovenia'),
			'SB' => gettext('Solomon Islands'),
			'SO' => gettext('Somalia'),
			'ZA' => gettext('South Africa'),
			'GS' => gettext('South Georgia, South Sandwich Islands'),
			'ES' => gettext('Spain'),
			'LK' => gettext('Sri Lanka'),
			'SH' => gettext('St. Helena'),
			'PM' => gettext('St. Pierre And Miquelon'),
			'SD' => gettext('Sudan'),
			'SR' => gettext('Suriname'),
			'SJ' => gettext('Svalbard And Jan Mayen Islands'),
			'SZ' => gettext('Swaziland'),
			'SE' => gettext('Sweden'),
			'CH' => gettext('Switzerland'),
			'SY' => gettext('Syrian Arab Republic'),
			'TW' => gettext('Taiwan'),
			'TJ' => gettext('Tajikistan'),
			'TZ' => gettext('Tanzania, United Republic Of'),
			'TH' => gettext('Thailand'),
			'TG' => gettext('Togo'),
			'TK' => gettext('Tokelau'),
			'TO' => gettext('Tonga'),
			'TT' => gettext('Trinidad And Tobago'),
			'TN' => gettext('Tunisia'),
			'TR' => gettext('Turkey'),
			'TM' => gettext('Turkmenistan'),
			'TC' => gettext('Turks And Caicos Islands'),
			'TV' => gettext('Tuvalu'),
			'UG' => gettext('Uganda'),
			'UA' => gettext('Ukraine'),
			'AE' => gettext('United Arab Emirates'),
			'GB' => gettext('United Kingdom'),
			'US' => gettext('United States of America'),
			'UM' => gettext('United States Minor Outlying Islands'),
			'UY' => gettext('Uruguay'),
			'UZ' => gettext('Uzbekistan'),
			'VU' => gettext('Vanuatu'),
			'VE' => gettext('Venezuela'),
			'VN' => gettext('Viet Nam'),
			'VG' => gettext('Virgin Islands (British)'),
			'VI' => gettext('Virgin Islands (U.S.)'),
			'WF' => gettext('Wallis And Futuna Islands'),
			'EH' => gettext('Western Sahara'),
			'YE' => gettext('Yemen'),
			'YU' => gettext('Yugoslavia'),
			'ZM' => gettext('Zambia'),
			'ZW' => gettext('Zimbabwe') 
			);
			return $countryArray;
		}
	}
?>
