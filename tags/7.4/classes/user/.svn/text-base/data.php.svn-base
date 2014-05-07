<?php
	/**
	 * Covide Groupware-CRM User_data
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
	Class User_data {
		/* constants */
		const include_dir = "classes/user/inc/";
		/* variables */
		/**
		 * @var array holds user permissions
		 */
		public $permissions = Array();
		public $calendarString = "";
		private $_cache;

		/**
		 * @var array users with their id.
		 */
		public $userlist = Array();
		/* methods */
		/* 	getUsernameByID {{{ */
	    /**
	     * 	getUsernameById return the name of user id
	     *
		 * @param int $id the database id of the user record
	     * @return string the username of userid
	     */
		public function getUsernameById($id) {
			if (preg_match("/^G\d{1,}/s", $id)) {
				$group = $this->getGroupInfo( (int)preg_replace("/^G/s", "", $id) );
				return $group["name"];
			} else {
				$id = (int)$id;
				if (!$this->_cache["username"][$id]) {
					if ($this->_cache["userdetails"][$id]) {
						$this->_cache["username"][$id] = $this->_cache["userdetails"][$id]["username"];
					} else {
						$sql = sprintf("SELECT username FROM users WHERE id=%d", $id);
						$res = sql_query($sql);
						$row = sql_fetch_assoc($res);
						$this->_cache["username"][$id] = $row["username"];
					}
				}
				return $this->_cache["username"][$id];
			}
		}
		/* }}} */
		/* 	getGroupList {{{ */
	    /**
	     * 	return array of groups
	     *
		 * @param int $userSelectionMode if 1 return array with G.$id=>name, if 0 or unset return array with id=>array(databaserow)
		 * @param string $str If set use this to limit the results based on the text (search)
	     * @return array key = id, value = name
	     */
		public function getGroupList($userSelectionMode=0, $str="", $nonempty = 0) {
			$sql = "SELECT * FROM user_groups";
			if ($str) {
				$sql .= sprintf(" WHERE name like '%s%%' OR description like '%s%%'", $str, $str);
				if ($nonempty == 1)
					$sql .= " AND members != ''";
			} else {
				if ($nonempty == 1)
					$sql .= " WHERE members != ''";
			}
			$sql .= " ORDER BY name";
			$res = sql_query($sql);
			$group = array();
			while ($row = sql_fetch_assoc($res)) {
				if ($userSelectionMode) {
					$group["G".$row["id"]] = $row["name"];
				} else {
					$group[$row["id"]] = $row;
				}
			}
			return $group;
		}
		/* }}} */
		/* getGroupInfo {{{ */
		/**
		 * Return all info on a group
		 *
		 * @param int $group_id The groupid to lookup
		 * @return array content of table user_groups for the given group_id
		 */
		public function getGroupInfo($group_id) {
			if ((int)$group_id == 0) {
				die("invalid groupid");
			}
			if ((int)$group_id >=1) {
				$sql = sprintf("SELECT * FROM user_groups WHERE id = %d", $group_id);
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
			} else {
				$row = array();
			}
			return $row;
		}
		/* }}} */
		/* getUserGroups {{{ */
		/**
		 * Return the groups a user is in
		 *
		 * @param int $user_id The userid to find the groups for
		 * @return array the groupids this user is in
		 */
		public function getUserGroups($user_id) {
			$data = array();

			$regex_syntax = sql_syntax("regex");
			$regex = $regex_syntax." '(^|\\\\,)". (int)$user_id ."(\\\\,|$)' ";
			$q = sprintf("select id from user_groups where members %s", $regex);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$data[]=$row["id"];
			}
			if (!count($data))
				$data = array("-1");
			return $data;
		}
		/* }}} */
		/* save_group {{{ */
		/**
		 * Save modified/new group info into the database
		 *
		 * @param array $data The groupinformation
		 * @return bool true on success
		 */
		public function save_group($data) {
			$groupinfo = $data["group"];
			if ($groupinfo["id"]) {
				/* update */
				$sql = sprintf("UPDATE user_groups SET name = '%s', description = '%s', members = '%s', manager = %d WHERE id = %d",
					$groupinfo["name"], $groupinfo["description"], $groupinfo["members"], $groupinfo["manager"], $groupinfo["id"]);
			} else {
				/* insert */
				$sql = sprintf("INSERT INTO user_groups (name, description, members, manager) VALUES('%s', '%s', '%s', %d)",
					$groupinfo["name"], $groupinfo["description"], $groupinfo["members"], $groupinfo["manager"], $groupinfo["id"]);
			}
			$res = sql_query($sql);
			return true;
		}
		/* }}} */
		/* delete_group {{{ */
		/**
		 * Remove a group from the database
		 *
		 * @param int $group_id The group to remove
		 * @return bool true on succes.
		 */
		public function delete_group($group_id) {
			if ($group_id) {
				$sql = sprintf("DELETE FROM user_groups WHERE id = %d", $group_id);
				$res = sql_query($sql);
				return true;
			} else {
				return false;
			}
		}
		/* }}} */
		/* 	getUserList {{{ */
	    /**
	     * 	getUserList return array with users and there id.
	     *
		 * @param int $active 1 for active addresses
		 * @param string $search if set use this as search in username
		 * @param int $archiveuser if set inclue archiveuser as well
	     * @return array keys are the user_id's and values are the usernames
	     */
		public function getUserList($active=1, $search="", $archiveuser = 0, $calendar=0) {
			$like = sql_syntax("like");
			if ($search) {
				$sq = sprintf(" AND username %s '%s%%' ", $like, $search);
			} else {
				$sq = "";
			}
			if ($calendar > 0) {
				$sql = "SELECT user_id FROM calendar_permissions WHERE user_id_visitor = '$calendar' AND permissions='RW'";
				$res = sql_query($sql);
				while ($row = sql_fetch_assoc($res)) {
					$calendarArr[$row["user_id"]] = $row["user_id"];
				}
			}
			if ((int)$archiveuser == 1) {
				$exclude = "'administrator'";
			} else {
				$exclude = "'administrator', 'archiefgebruiker'";
			}

			$sql = sprintf("SELECT id, username FROM users WHERE is_active=%d %s AND username NOT IN (%s) ORDER BY username", $active, $sq, $exclude);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				if (is_array($calendarArr)) {
					if (in_array($row["id"], $calendarArr))
						$userlist[$row["id"]] = $row["username"];
				} else {
					$userlist[$row["id"]] = $row["username"];
				}
			}
			if (!is_array($userlist))
				$userlist = array();
			$this->userlist = $userlist;
			return $userlist;
		}
		/* }}} */
		/* 	getArchiveUserId {{{ */
		/**
		 * Find the user_id of the archiveuser
		 *
		 * @return int the user_id of the user
		 */
		public function getArchiveUserId() {
			$name = "archiefgebruiker";
			$id   = false;
			if (is_array($this->_cache["username"])) {
				$id = array_search($name, $this->_cache["username"]);
			}
			if (!$id) {
				$sql = sprintf("SELECT id, username FROM users WHERE username = '%s'", $name);
				$res = sql_query($sql);
				$id  = sql_result($res,0);
				$this->_cache["username"][$id] = $name;
			}
			return $id;
		}
		/* }}} */
		/* 	getUserPermissionsById {{{ */
	    /**
	     * Return the global permissions for a user and also put this info into the object variable permissions
	     *
		 * @param int $id user id
	     * @return array permissions to modules. key is the module name, value is 1 or 0
	     */
		public function getUserPermissionsById($id) {
			$id = (int)$id;

			if (!$this->_cache["userdetails"][$id]) {
				$sql = sprintf("SELECT * FROM users WHERE id=%d", $id);
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
				$this->_cache["userdetails"][$id] = $row;
			} else {
				$row = $this->_cache["userdetails"][$id];
			}

			$permissions = Array();
			if (!is_array($row))
				$row = array();

			foreach ($row as $k=>$v) {
				if (strstr($k, "xs_")) {
					$permissions[$k] = (int)$v;
				}
				if ($k == "addressaccountmanage" || $k == "calendarmode" || $k == "change_theme") {
					$permissions[$k] = $v;
				}
			}
			$this->permissions = $permissions;
			return $permissions;
		}
		/* }}} */
		/* 	checkPermission {{{ */
	    /**
	     * 	check if user has permission set to 1
	     *
		 * @param string $permission permission to check
	     * @return bool true if permission is granted, false if not.
	     */
		public function checkPermission($permission) {
			if (!strstr($permission, "xs_")) {
				$permission = "xs_".$permission;
			}
			if ($this->permissions[$permission] > 0) {
				return true;
			} else {
				return false;
			}
		}
		/* }}} */
		/* 	saveUserSettings {{{ */
	    /**
	     * 	store userinfo in database
	     *
		 * @param array $userdata
	     * @return bool true if succes, false if not.
	     */
		public function saveUserSettings($userdata) {
			require(self::include_dir."dataSaveUserSettings.php");
		}
		/* }}} */
		/* 	getUserdetailsById {{{ */
	    /**
	     * 	get all the userfields from db for a user
	     *
		 * @param int $id the user to fetch
	     * @return array content of usertable in database.
	     */
		public function getUserdetailsById($id) {
			$id = (int)$id;
			if (!$this->_cache["userdetails"][$id]) {
				$sql = sprintf("SELECT * FROM users WHERE id=%d", $id);
				$res = sql_query($sql);
				$userinfo = sql_fetch_assoc($res);
				$this->_cache["userdetails"][$id] = $userinfo;
			} else {
				$userinfo = $this->_cache["userdetails"][$id];
			}

			if (!$userinfo["mail_num_items"]) {
				$userinfo["mail_num_items"]=$GLOBALS["covide"]->pagesize_default;
			}
			/* get last login entry from loginlog */
			$sql = sprintf("SELECT * FROM login_log WHERE user_id=%d ORDER BY time DESC LIMIT 1", $id);
			$res = sql_query($sql);
			$logininfo = sql_fetch_assoc($res);
			$userinfo["last_login_time_h"] = @date("d-m-Y H:i", $logininfo["time"]);
			$userinfo["last_login_ip"]     = $logininfo["ip"];
			$userinfo["last_login_host"]   = @gethostbyaddr($logininfo["ip"]); 
			if (!$userinfo["authmethod"])
				$userinfo["authmethod"] = "database";
			if (!$userinfo["calendarinterval"])
				$userinfo["calendarinterval"] = 5;
			return $userinfo;
		}
		/* }}} */
		/* getNewUser {{{ */
		/**
		 * Get all the info we need for a new user
		 *
		 * @return array The info we want to preset for a new user
		 */
		public function getNewUser() {
			$userinfo = array(
				"style"            => 0,
				"pers_nr"          => $this->getNextPersNR(),
				"is_active"        => 1,
				"language"         => "EN",
				"automatic_logout" => 1,
				"showhelp"         => 1,
				"mail_num_items"   => $GLOBALS["covide"]->pagesize_default,
				"authmethod"       => "database"
			);
			return $userinfo;
		}
		/* }}} */
		/* getNextPersNR {{{ */
		/**
		 * Get the next available employee number.
		 *
		 * @return int The new employee number
		 */
		public function getNextPersNR() {
			$sql = "SELECT MAX(pers_nr)+1 as newnr FROM users";
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			return $row["newnr"];
		}
		/* }}} */
		/* 	getEmployeedetailsById {{{ */
	    /**
	     * 	get all the employeefields from db for a user
	     *
		 * @param int $id the user to fetch
	     * @return array the data
	     */
		public function getEmployeedetailsById($id) {
			$sql = sprintf("SELECT id,username,address_id,employer_id,mail_email,mail_email1 FROM users WHERE id=%d", (int)$id);
			$res = sql_query($sql);
			$userinfo = sql_fetch_assoc($res);
			if ($userinfo["address_id"]) {
				/* fetch users realname */
				$sql = sprintf("SELECT givenname,surname,infix FROM address_private WHERE id=%d", $userinfo["address_id"]);
				$r = sql_query($sql);
				$addressdata = sql_fetch_assoc($r);
				if ($addressdata["givenname"] && $addressdata["surname"]) {
					$return["realname"] = trim($addressdata["givenname"])." ".trim($addressdata["infix"])." ".trim($addressdata["surname"]);
					$return["realname"] = preg_replace("/ {2,}/", " ", $return["realname"]);
				} else {
					$return["realname"] = $userinfo["username"];
				}
			} else {
				/* no realname, put username as realname */
				$return["realname"] = $userinfo["username"];
			}
			if ($userinfo["employer_id"]) {
				$sql = sprintf("SELECT companyname FROM address_other WHERE id=%d", $userinfo["employer_id"]);
				$r = sql_query($sql);
				$empdata = sql_fetch_assoc($r);
				if ($empdata["companyname"]) {
					$return["companyname"] = $empdata["companyname"];
				} else {
					$return["companyname"] = "";
				}
			} else {
				$return["companyname"] = "";
			}

			$return["mail_email"]  = $userinfo["mail_email"];
			$return["mail_email1"] = $userinfo["mail_email1"];
			$return["user_id"]  = $userinfo["id"];
			$return["username"] = $userinfo["username"];

			return $return;
		}
		/* }}} */
	    /* 	check_login {{{ */
	    /**
	     * Find out if a user is logged in
	     *
	     * @return int 1 if user is logged in, otherwise 0
	     */
		public function check_login() {
			if ($_SESSION["user_id"]) {
				return 1;
			} else {
				return 0;
			}
		}
		/* }}} */
		/* getUserIdByAddressId {{{ */
		/**
		 * Get the userid associated with given address id
		 *
		 * @param int $address_id The address id to lookup
		 * @return int The userid
		 */
		public function getUserIdByAddressId($address_id) {
			$sql = sprintf("SELECT id FROM users WHERE address_id = %d", $address_id);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			return $row["id"];
		}
		/* }}} */
		/* getAddressIdByUserId {{{ */
		/**
		 * Get the addressid associated with given user id
		 *
		 * @param int $user_id The user id to lookup
		 * @return int The addressid
		 */
		public function getAddressIdByUserId($user_id) {
			$sql = sprintf("SELECT address_id FROM users WHERE id = %d", $user_id);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			return $row["address_id"];
		}
		/* }}} */
		/* getUserinfoXML {{{ */
		/**
		 * Get some user information we need in the login screen
		 *
		 * @param string $username The username supplied in the login screen
		 * @return array if user is found an array with id, authmethod and is_active is returned. Otherwise empty array
		 */
		public function getUserinfoXML($username) {
			$userinfo = array();
			$sql = sprintf("SELECT id, authmethod, is_active FROM users WHERE username='%s'", preg_quote($username));
			$res = sql_query($sql);
			if (sql_num_rows($res)>0) {
				$row = sql_fetch_assoc($res);
				$userinfo["id"] = $row["id"];
				$userinfo["authmethod"] = ($row["authmethod"]?$row["authmethod"]:"database");
				$userinfo["is_active"] = ($row["is_active"]?1:0);
			}
			if ($userinfo["authmethod"] == "radius") {
				echo "radius_auth = 1;";
			} else {
				echo "radius_auth = 0;";
			}
		}
		/* }}} */
		/* validate_login {{{ */
		/**
		 * check given username and password against database.
		 * On succuss, set the session
		 *
		 * @param array $userinfo [username], [password]
		 * @return void
		 */
		public function validate_login($userinfo) {
			/* check if the username is in the database. If not, we dont need to authenticate */
			$sql = sprintf("SELECT id, password, style, mail_num_items as pagesize, authmethod FROM users WHERE is_active = 1 AND username='%s'", $userinfo["username"]);
			$res = sql_query($sql);
			if (sql_num_rows($res)>0) {
				/* it is, so lets authenticate */
				/* get the userdata */
				$row = sql_fetch_assoc($res);

				/* check for radius license */
				if ($GLOBALS["covide"]->license["has_radius"] && strtolower($row["authmethod"]) == "radius") {
					/* get radius settings from db */
					$sql_radius = "SELECT * FROM radius_settings";
					$res_radius = sql_query($sql_radius);
					$radius_settings = sql_fetch_assoc($res_radius);
					/* create radius authentication handle */
					$radius = radius_auth_open();
					/* add authentication server with shared secret, 10 second timeout and 2 retries */
					if (!radius_add_server($radius, $radius_settings["radius_server"], $radius_settings["radius_port"], $radius_settings["shared_secret"], 5, 1))
						die(radius_strerror($radius));
					/* create radius authentication request structure in the radius authentication handle */
					if (!radius_create_request($radius, RADIUS_ACCESS_REQUEST))
						die(radius_strerror($radius));
					/* set some parameters */
					/* the NAS-IP */
					if (!radius_put_addr($radius, RADIUS_NAS_IP_ADDRESS, $radius_settings["nas_ip"]))
						die(radius_strerror($radius));
					/* username */
					if (!radius_put_string($radius, RADIUS_USER_NAME, $userinfo["username"]))
						die(radius_strerror($radius));

					if ($radius_settings["auth_type"] == "CHAP") {
						/* create challenge */
						mt_srand(time());
						$chall = mt_rand();
						// FYI: CHAP = md5(ident + plaintextpass + challenge)
						$chapval = pack("H*", md5(pack("Ca*", 1, $userinfo["password"])));
						$pass = pack("C", 1).$chapval;
						/* add the password to the request */

						/* This is for CHAP_MD5 */
						if (!radius_put_attr($radius, RADIUS_CHAP_PASSWORD, $pass))
							die(radius_strerror($radius));
						// add the challenge to the request
						if (!radius_put_attr($radius, RADIUS_CHAP_CHALLENGE, $chall))
							die(radius_strerror($radius));
					} elseif ($radius_settings["auth_type"] == "PAP") {
						/* This is for normal passwords */
						radius_put_attr($radius, RADIUS_USER_PASSWORD, $userinfo["password"]);
					}
					/* send the request */
					if (!$auth = radius_send_request($radius))
						die(radius_strerror($radius));
					switch ($auth) {
					case RADIUS_ACCESS_ACCEPT:
						/* login is granted */
						if (strtolower($_SERVER["HTTPS"]) == "on" || strtolower($_SERVER["HTTP_X_FORWARDED_PROTOCOL"] == "https"))
							$_SESSION["ssl_enable"] = "on";
						else
							$_SESSION["ssl_enable"] = 0;
						$_SESSION["user_id"]    = $row["id"];
						$_SESSION["theme"]      = $row["style"];
						$_SESSION["pagesize"]   = $row["pagesize"];
						/* log to database */
						$logininfo["time"]    = mktime();
						$logininfo["user_id"] = $row["id"];
						if ($_SERVER["REMOTE_ADDR"] == "127.0.0.1" && array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER))
							$logininfo["ip"]      = $_SERVER["HTTP_X_FORWARDED_FOR"];
						else
							$logininfo["ip"]      = $_SERVER["REMOTE_ADDR"];
						$this->login_log($logininfo);

						if ($_REQUEST["uri"]) {
							$output = new Layout_output();
							$output->start_javascript();
								$output->addCode(sprintf("
									if (opener) {
										opener.location.href = '%1\$s'; setTimeout('window.close();', 100);
									} else {
										location.href = '%1\$s';
									}
								", str_replace("'", "", $_REQUEST["uri"])));
							$output->end_javascript();
							$output->exit_buffer();
						} else {
							header("Location: index.php?mod=desktop&uri=".$_REQUEST["uri"]);
							exit();
						}
						break;
					case RADIUS_ACCESS_REJECT:
						break;
					default:
						$user_output = new User_output();
						$user_output->show_login(99);
						exit();
						break;
					}
					radius_close($radius);

				} else {
					/* check if remembers password was set */
					if (!$userinfo["save_password"] || $userinfo["remember_type"]!="covide") {
						$expire = mktime(0,0,0,date("m"),date("d")-2,date("Y")); //expire now
						setcookie("covideuser", "", $expire, dirname($_SERVER["SCRIPT_NAME"]), $_SERVER["HTTP_HOST"]);
					}

					/* generate server side md5 hash with md5(md5-db-pass + challenge) */
					$serverpw = md5($row["password"].$_SESSION["challenge"]);
					$clientpw = $userinfo["password"];

					if ($serverpw == $clientpw) {
						/* if password save is requested */
						if ($userinfo["save_password"] && $userinfo["remember_type"]=="covide") {
							$stream = base64_encode($userinfo["username"]."|".$row["password"]."|".$_REQUEST["use_ssl"]);
							$expire = mktime(0,0,0,date("m"),date("d"),date("Y")+1); //expire in one year
							setcookie("covideuser", $stream, $expire, dirname($_SERVER["SCRIPT_NAME"]), $_SERVER["HTTP_HOST"]);
						}

						/* login is granted */
						if (strtolower($_SERVER["HTTPS"]) == "on" || strtolower($_SERVER["HTTP_X_FORWARDED_PROTOCOL"] == "https"))
							$_SESSION["ssl_enable"] = "on";
						else
							$_SESSION["ssl_enable"] = 0;
						$_SESSION["user_id"]    = $row["id"];
						$_SESSION["theme"]      = $row["style"];
						$_SESSION["pagesize"]   = $row["pagesize"];
						/* log to database */
						$logininfo["time"]    = mktime();
						$logininfo["user_id"] = $row["id"];
						if ($_SERVER["REMOTE_ADDR"] == "127.0.0.1" && array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER))
							$logininfo["ip"]      = $_SERVER["HTTP_X_FORWARDED_FOR"];
						else
							$logininfo["ip"]      = $_SERVER["REMOTE_ADDR"];
						$this->login_log($logininfo);

						if ($_REQUEST["uri"]) {
							$output = new Layout_output();
							$output->start_javascript();
								$output->addCode(sprintf("
									if (opener) {
										opener.location.href = '%1\$s'; setTimeout('window.close();', 100);
									} else {
										location.href = '%1\$s';
									}
								", str_replace("'", "", $_REQUEST["uri"])));
							$output->end_javascript();
							$output->exit_buffer();
						} else {
							header("Location: index.php?mod=desktop&uri=".$_REQUEST["uri"]);
							exit();
						}
					}
				}
			}
			$user_output = new User_output();
			$user_output->show_login(1);
			exit();
		}
		/* }}} */
		/* login_log {{{ */
		/**
		 * log succesfull login entries in database
		 *
		 * @param array $logininfo time and ip of login item.
		 * @return bool true on success
		 */
		public function login_log($logininfo) {
			/* look if this is the first login today */
			$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			$sql = sprintf("SELECT COUNT(*) FROM login_log WHERE user_id=%d AND day=%d", $logininfo["user_id"], $today);
			$res = sql_query($sql);
			$count = sql_result($res, 0);
			if (!$count) {
				/* increment days of the user */
				$sql = sprintf("SELECT days FROM users WHERE id=%d", $logininfo["user_id"]);
				$res = sql_query($sql);
				$days = sql_result($res, 0);
				$days = (int)$days+1;
				$sql = sprintf("UPDATE users SET days=%d WHERE id=%d", $days, $logininfo["user_id"]);
				$res = sql_query($sql);
			}
			/* write to loginlog table */
			$sql = sprintf("INSERT INTO login_log (user_id, ip, time, day) VALUES (%d, '%s', %d, %d)", $logininfo["user_id"], $logininfo["ip"], $logininfo["time"], $today);
			$res = sql_query($sql);
		}
		/* }}} */
		/* logout {{{ */
		/**
		 * Destroy session so user is no longer logged in
		 */
		public function logout() {
			@session_unset();
			@session_destroy();

			if ($_REQUEST["redir"] == "close")
				echo "<script>window.close();</script>";
			elseif ($_REQUEST["redir"])
				header("Location: ".$_REQUEST["redir"]);
			else
				header("Location: index.php?mod=desktop&uri=".$_REQUEST["uri"]);

			exit();
		}
		/* }}} */
		/* getNestedUserList {{{ */
		/**
		 * get array with all users. Array keys are usertypes
		 *
		 * @param int $showArchiveUser 1 to put archive user in array
		 * @param int $showInActive 1 to put inactive users in array
		 * @param int $showGroups 1 to put groups in array
		 * @param int $showCalendar 1 to put (allowed) calendar users in array
		 * @return array the users from db
		 */
		public function getNestedUserList($showArchiveUser, $showInActive, $showGroups=0, $showCalendar=0) {
			if($showCalendar > 0) {
				$list[gettext("active users")] = $this->getUserList(1,0,0,$_SESSION["user_id"]);
			} else {
				if ($showGroups) {
					$list[gettext("groups")] = $this->getGroupList(1);
				}
				if ($showArchiveUser) {
					$archive = $this->getArchiveUserId();
					$list[gettext("special users")] = array($archive=>gettext("archiveuser"));
				}
				$list[gettext("active users")] = $this->getUserList();

				if ($showInActive) {
					$list[gettext("non-active users")] = $this->getUserList(0);
				}
			}
			foreach ($list as $k=>$v) {
				if (is_array($list[$k]))
					natcasesort($list[$k]);
			}
			return $list;
		}
		/* }}} */
		/* fundUserInList {{{ */
		/**
		 * check if user_id is in userlist and return the username if it is
		 *
		 * @param array $list the list of userids=>usernames to search in
		 * @param int $user_id the user_id to lookup
		 * @return string The matching username
		 */
		public function findUserInList($list, $user_id) {
			$username = "??";
			foreach ($list as $k=>$l) {
				if ($l[$user_id]) {
					$username = $l[$user_id];
				}
			}
			return $username;
		}
		/* }}} */
		/* autocomplete {{{ */
		/**
		 * Autocomplete usernames.
		 */
		public function autocomplete() {
			require(self::include_dir."autocomplete.php");
		}
		/* }}} */
		/* activate_user {{{ */
		/**
		 * Activate non-active user.
		 *
		 * @param int $user_id The userid to activate
		 * @return bool True if ok, false if userid is not in database
		 */
		public function activate_user($user_id) {
			/* check if user_id is set */
			if ((int)$user_id > 0) {
				/* check if user is in the database */
				$sql = sprintf("SELECT COUNT(*) as count FROM users WHERE id = %d", $user_id);
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
				if ($row["count"] == 1) {
					/* set to active, no matter whether user is active or not */
					$sql = sprintf("UPDATE users SET is_active = 1 WHERE id = %d", $user_id);
					$res = sql_query($sql);
					return true;
				}
			}
			return false;
		}
		/* }}} */
		/* deactivate_user {{{ */
		/**
		 * Deactivate non-active user.
		 *
		 * @param int $user_id The userid to deactivate
		 * @return bool True if ok, false if userid is not in database
		 */
		public function deactivate_user($user_id) {
			/* check if user_id is set */
			if ((int)$user_id > 0) {
				/* check if user is in the database */
				$sql = sprintf("SELECT COUNT(*) as count FROM users WHERE id = %d", $user_id);
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
				if ($row["count"] == 1) {
					/* set to non-active, no matter whether user is active or not */
					$sql = sprintf("UPDATE users SET is_active = 0 WHERE id = %d", $user_id);
					$res = sql_query($sql);
					return true;
				}
			}
			return false;
		}
		/* }}} */
		/* checkUserXML {{{ */
		/**
		 * Check if the username is already in the database
		 *
		 * @param string $username The username to check
		 * @param string $currentuser the username to ignore in the test for already existing users
		 */
		public function checkUserXML($username, $currentuser = 0) {
			$sql = sprintf("SELECT COUNT(*) FROM users WHERE id != %d AND username='%s'", $currentuser, $username);
			$res = sql_query($sql);
			$count = sql_result($res,0);
			if ($count) {
				echo "update_conflict(1);";
			} else {
				echo "update_conflict(0);";
			}
		}
		/* }}} */
		/* useredit_check {{{ */
		/**
		 * Checks useredit form fields before saving to db
		 *
		 * @param array $checkdata The data to check
		 */
		public function useredit_check($checkdata) {
			/* extract and sanitize the input data */
			$username = sprintf("%s", $checkdata["username"]);
			$passwd1  = sprintf("%s", $checkdata["passwd1"]);
			$passwd2  = sprintf("%s", $checkdata["passwd2"]);
			$pers_nr  = sprintf("%d", $checkdata["empnum"]);
			$user_id  = sprintf("%d", $checkdata["userid"]);
			/* check employee number */
			if (!$pers_nr) {
				echo "handle_error(3);";
				return true;
			} else {
				$sql = sprintf("SELECT COUNT(*) FROM users WHERE pers_nr=%d AND id != %d", $pers_nr, $user_id);
				$res = sql_query($sql);
				$count = sql_result($res, 0);
				if ($count > 0) {
					echo "handle_error(2);";
					return true;
				}
			}
			/* check passwords */
			if (strlen($passwd1)) {
				if ($passwd1 != $passwd2) {
					echo "handle_error(1);";
					return true;
				}
				if (strlen($passwd1) < 6) {
					echo "handle_error(4);";
					return true;
				}
				if (!preg_match("/[a-z]/si", $passwd1) || !preg_match("/[^a-z]/si", $passwd1)) {
					echo "handle_error(5);";
					return true;
				}
			}

			/* all went ok, lets run the save */
			echo "user_save_exec();";
			return true;
		}
		/* }}} */
		/* updatePagesize {{{ */
		/**
		 * update default pagesize for paging object
		 *
		 * @param int $num The new number of items on a page
		 */
		public function updatePagesize($num) {
			if ($num < 5) {
				$num = 5;
			} elseif ($num > 1000) {
				$num = 1000;
			}
			$_SESSION["pagesize"] = (int)$num;
			echo "updated to ".$num;
		}
		/* }}} */
		public function getLicenseTable() {
			/* this part is only available in mysql at the moment */
			$cols = array();
			$q = "describe license";
			$result = $GLOBALS["covide"]->db->query($q);
			while ($row = $result->fetchRow()) {
				if (preg_match("/^varchar/si", $row["Type"]))
					$type = "s";
				elseif (preg_match("/^text/si", $row["Type"]))
					$type = "t";
				else
					$type = "d";
				$cols[$row["Field"]] = $type;
			}
			$q = "select * from license";
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);
			foreach ($row as $k=>$v) {
				$data[] = array(
					"name"  => $k,
					"value" => ($cols[$k]=="d" && !$v) ? 0 : $v,
					"type"  => $cols[$k]
				);
			}
			return $data;
		}
		public function saveLicense($req) {
			/* check permissions */
			$currname = $this->getUserNameById($_SESSION["user_id"]);
			if ($currname != "administrator")
				die("access is denied!");

			$q = "update license set ";
			$cols = $this->getLicenseTable();
			foreach ($cols as $v) {
				$i++;
				if ($i > 1)
					$q.= ", ";

				$q.= sprintf("`%s` = ", $v["name"]);
				if ($v["type"] == "d")
					$q.= sprintf("%d ", $req["lic"][$v["name"]]);
				else
					$q.= sprintf("'%s' ", $req["lic"][$v["name"]]);
			}
			sql_query($q);

			session_write_close();
			$useroutput = new User_output();
			$useroutput->usersaved();
			exit();
		}

		public function cleanUp() {
			header("Content-type: text/plain");

			$filesys_data = new Filesys_data();
			$filesys_data->cleanOrphanedItems();
			$email_data = new Email_data();
			$email_data->cleanOrphanedItems();
			$address_data = new Address_data();
			$address_data->cleanOrphanedItems();
		}

		public function cron() {
			ob_clean();
			ob_start();

			/* we output plain text */
			header("Content-Type: text/plain");

			echo sprintf("started at %s\n", date("r"));
			echo sprintf("[host] %s\n", $_SERVER["SERVER_NAME"]);
			/* select all active users */
			$q = sprintf("select * from users where is_active = 1 order by id");
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				/* a flush */
				ob_flush();
				flush();

				/* some output */
				$out = sprintf("[user] id: %d, username: %s\n", $row["id"], $row["username"]);

				if ($row["mail_user_id"] && $row["mail_server"] && $row["mail_password"]) {
					/* only output if something needs to be done */
					echo $out;
					$out = "";
					echo sprintf("- fetching email");
					$email = new Email_retrieve($row["id"], 0);
					$r = $email->retrieve();
					echo sprintf(" [%s]\n", $r);
					unset($email);
				}
				if ($row["xs_funambol"] && $row["xs_funambol_version"] == 6) {
					/* only output if something needs to be done */
					echo $out;
					$out = "";

					if ($GLOBALS["covide"]->license["funambol_server_version"] < 600) {
						echo "[entering funambol recover sync!]\n";
						$recover = 1;
					}

					echo sprintf("- funambol sync ");
					$fnbl = new Funambol_data($row["id"], 1);
					$r = $fnbl->syncUser();

					/* if recover mode, delete all appointments in the device */
					if ($recover)
						$r = $fnbl->syncUser(1);

					echo sprintf("[server sync]");

					echo " [calendar]";
					$fnbl->checkRecords("calendar");

					echo " [todo]";
					$fnbl->checkRecords("todo");

					echo " [address]";
					$fnbl->checkRecords("address");

					#$fnbl->checkRecords("files");
					unset($fnbl);
					echo " [done]\n";
				}
			}
			echo sprintf("archiving old email");
			$email = new Email_data();
			$email->archiveOldEmails();
			unset($email );
			echo " [done]\n";

			echo sprintf("fetching rss feed items");
			$rss = new Rss();
			unset($rss);
			echo " [done]\n";
			if ($GLOBALS["covide"]->license["has_multivers"]) {
				echo sprintf("syncing multivers addresses\n");
				$address_data = new Address_data();
				$address_data->syncMultivers();
				echo "[done]\n";
			}
			echo sprintf("finished at %s\n", date("r"));
		}
	}
?>
