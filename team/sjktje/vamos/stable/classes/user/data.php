<?php
	/**
	 * Covide Groupware-CRM User_data
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version 7.1
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2006 Covide BV
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
				$sql = sprintf("SELECT username FROM users WHERE id=%d", (int)$id);
				$res = sql_query($sql);
				$row = sql_fetch_assoc($res);
				return $row["username"];
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
		public function getGroupList($userSelectionMode=0, $str="") {
			if ($str) {
				$sql = sprintf("SELECT * FROM user_groups WHERE name like '%s%%' OR description like '%s%%' ORDER BY name", $str, $str);
			} else {
				$sql = "SELECT * FROM user_groups ORDER BY name";
			}
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
		public function getUserList($active=1, $search="", $archiveuser = 0) {
			$like = sql_syntax("like");
			if ($search) {
				$sq = sprintf(" AND username %s '%s%%' ", $like, $search);
			} else {
				$sq = "";
			}
			if ((int)$archiveuser == 1) {
				$exclude = "'administrator'";
			} else {
				$exclude = "'administrator', 'archiefgebruiker'";
			}
			$sql = sprintf("SELECT id, username FROM users WHERE is_active=%d %s AND username NOT IN (%s) ORDER BY username", $active, $sq, $exclude);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$userlist[$row["id"]] = $row["username"];
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
			$sql = "SELECT id, username FROM users WHERE username = 'archiefgebruiker'";
			$res = sql_query($sql);
			return sql_result($res,0);
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
			$sql = sprintf("SELECT * FROM users WHERE id=%d", (int)$id);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			$permissions = Array();
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
			if ($this->permissions[$permission] == 1) {
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
			$sql = sprintf("SELECT * FROM users WHERE id=%d", (int)$id);
			$res = sql_query($sql);
			$userinfo = sql_fetch_assoc($res);
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
				"mail_num_items"   => $GLOBALS["covide"]->pagesize_default
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
				$sql = sprintf("SELECT givenname,surname FROM address_private WHERE id=%d", $userinfo["address_id"]);
				$r = sql_query($sql);
				$addressdata = sql_fetch_assoc($r);
				if ($addressdata["givenname"] && $addressdata["surname"]) {
					$return["realname"] = $addressdata["givenname"]." ".$addressdata["surname"];
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
		/* validate_login {{{ */
		/**
		 * check given username and password against database.
		 * On succuss, set the session
		 *
		 * @param array $userinfo [username], [password]
		 * @return void
		 */
		public function validate_login($userinfo) {
			/* check if remembers password was set */
			if (!$userinfo["save_password"] || $userinfo["remember_type"]!="covide") {
				$expire = mktime(0,0,0,date("m"),date("d")-2,date("Y")); //expire now
				setcookie("covideuser", "", $expire, dirname($_SERVER["SCRIPT_NAME"]), $_SERVER["HTTP_HOST"]);
			}

			$sql = sprintf("SELECT id, password, style, mail_num_items as pagesize FROM users WHERE is_active = 1 AND username='%s'", $userinfo["username"]);
			$res = sql_query($sql);
			if (sql_num_rows($res)>0) {
				$row = sql_fetch_assoc($res);

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
					$_SESSION["ssl_enable"] = $_SERVER["HTTPS"];
					$_SESSION["user_id"]    = $row["id"];
					$_SESSION["theme"]      = $row["style"];
					$_SESSION["pagesize"]   = $row["pagesize"];
					/* log to database */
					$logininfo["time"]    = mktime();
					$logininfo["user_id"] = $row["id"];
					$logininfo["ip"]      = $_SERVER["REMOTE_ADDR"];
					$this->login_log($logininfo);
					header("Location: index.php?mod=desktop");

					exit();
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
			#sleep(1);
			header("Location: index.php?mod=desktop");
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
		 * @return array the users from db
		 */
		public function getNestedUserList($showArchiveUser, $showInActive, $showGroups=0) {
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
	}
?>
