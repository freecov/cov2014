<?php
	/**
	 * Covide Groupware-CRM calendar module
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version 6.1
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2006 Covide BV
	 * @package Covide
	 */
	Class Calendar_data {
		/* constants */
		const include_dir = "classes/calendar/inc/";
		const class_name  = "calendar";

		/* variables */

		/**
		 * @var array dataplaceholder for all items
		 */
		public $calendar_items = array();

		/* methods */
		/* {{{ getAppointmentsByAddress($address_id, $history) */
		/**
		 * Get the appointments linked to an address
		 *
		 * @param int address id
		 * @param int if 1 return items from the past
		 * @return array the actual appointments
		 */
		public function getAppointmentsByAddress($address_id, $history=0) {
			/* get users in an array */
			$userdata = new User_data();
			$userinfo = $userdata->getUserList();
			$sql  = sprintf("SELECT * FROM calendar WHERE (address_id = %1\$d OR multirel LIKE '%%,%1\$d,%%'", $address_id);
			$sql .= sprintf(" OR multirel LIKE '%1\$d,%%' OR multirel LIKE '%%,%1\$d' OR multirel = '%1\$d')", $address_id);
			$sql .= sprintf(" AND (is_private = 0 OR user_id = %d)", $_SESSION["user_id"]);
			if (!$history) {
				$sql .= sprintf(" AND timestamp_start >= %d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
			} else {
				$sql .= sprintf(" AND timestamp_end < %d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
			}
			$sql .= " ORDER BY timestamp_start DESC";
			$res = sql_query($sql);
			$i = 0;
			while ($row = sql_fetch_assoc($res)) {
				$calendaritems[$i] = $row;
				$calendaritems[$i]["human_start"] = date("d-m-Y H:i", $row["timestamp_start"]);
				$calendaritems[$i]["human_end"]   = date("d-m-Y H:i", $row["timestamp_end"]);
				$calendaritems[$i]["user_name"]   = $userinfo[$row["user_id"]];
				$i++;
			}
			return $calendaritems;
		}
		/* }}} */
		/* {{{ getAppointmentsByUser($user_id, $history) */
		/**
		 * Get the appointments linked to a user
		 *
		 * @param int user id
		 * @param int if 1 return items from the past
		 * @return array the actual appointments
		 */
		public function getAppointmentsByUser($user_id, $history=0) {
			/* get users in an array */
			$userdata = new User_data();
			$userinfo = $userdata->getUserList();
			$sql  = sprintf("SELECT * FROM calendar WHERE user_id = %d", $user_id);
			if (!$history) {
				$sql .= sprintf(" AND timestamp_start >= %d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
			} else {
				$sql .= sprintf(" AND timestamp_end < %d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
			}
			$sql .= sprintf(" AND (is_private != 1 OR user_id = %d)", $_SESSION["user_id"]);
			$sql .= " ORDER BY timestamp_start ASC";
			$res = sql_query($sql);
			$i = 0;
			while ($row = sql_fetch_assoc($res)) {
				$calendaritems[$i] = $row;
				$calendaritems[$i]["human_start"] = date("d-m-Y H:i", $row["timestamp_start"]);
				$calendaritems[$i]["human_end"]   = date("d-m-Y H:i", $row["timestamp_end"]);
				$calendaritems[$i]["user_name"]   = $userinfo[$row["user_id"]];
				$i++;
			}
			return $calendaritems;
		}
		/* }}} */
		/* getAppointmentsBySearch($options) {{{ */
		/**
		 * Get appointments by search options.
		 *
		 * This is not using the _get_appointments because we don't need
		 * all the stuff that's in there. Repeated items wont be included.
		 * This is used for relationcard and employeecard.
		 *
		 * @param array search options like user_id, address_id, searchkey
		 * @return array the items that match
		 */
		public function getAppointmentsBySearch($options) {
			$userdata = new User_data();
			$userinfo = $userdata->getUserList();

			$buf = sql_syntax("buffer");
			$sql = "SELECT ".$buf." * FROM calendar WHERE (is_repeat = 0 OR is_repeat IS NULL)";
			if (!$options["all"]) {
				if (!$options["history"]) {
					$sql .= sprintf(" AND timestamp_start >= %d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
				} else {
					$sql .= sprintf(" AND timestamp_end < %d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
				}
			}
			if ($options["user_id"]) {
				$sql .= sprintf(" AND user_id = %d", $options["user_id"]);
			}
			if ($options["address_id"]) {
				$sql .= sprintf(" AND address_id = %d", $options["address_id"]);
			}
			if ($options["project_id"]) {
				$sql .= sprintf(" AND project_id = %d", $options["project_id"]);
			}
			if ($options["searchkey"]) {
				$like_syntax = sql_syntax("like");
				$sql .= sprintf(" AND (subject $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%')", $options["searchkey"]);
			}
			if ($options["sortorder"]) {
				$sql .= sprintf(" ORDER BY timestamp_start %s", $options["sortorder"]);
			} else {
				$sql .= " ORDER BY timestamp_start ASC";
			}
			if ($options["max_hits"]) {
				$res = sql_query($sql, "", 0, $options["max_hits"]);
			} else {
				$res = sql_query($sql);
			}
			$i = 0;
			while ($row = sql_fetch_assoc($res)) {
				$i = $row["id"];
				$calendaritems[$i] = $row;
				if ($calendaritems[$i]["subject"] == "") { $calendaritems[$i]["subject"] = substr($calendaritems[$i]["description"], 0, 40); }
				$calendaritems[$i]["human_start"] = date("d-m-Y H:i", $row["timestamp_start"]);
				$calendaritems[$i]["human_end"]   = date("d-m-Y H:i", $row["timestamp_end"]);
				$calendaritems[$i]["day"]         = date("d", $row["timestamp_start"]);
				$calendaritems[$i]["month"]       = date("m", $row["timestamp_start"]);
				$calendaritems[$i]["year"]        = date("Y", $row["timestamp_start"]);
				$calendaritems[$i]["user_name"]   = $userinfo[$row["user_id"]];

				$calendaritems[$i]["subject"] = preg_replace("/(\t)|(\n)|(\r)/s", "", $calendaritems[$i]["subject"]);
				$i++;
			}
			return $calendaritems;
		}
		/* }}} */
		/* {{{ delete($id) */
		/**
		 * delete item from db
		 *
		 * @param int calendar item id
		 * @return bool true on succes
		 */
		public function delete($id) {
			/* sync4j snippet. put delete action in the database */
			$q = sprintf("select user_id, sync_guid from calendar where id = %d", $id);
			$result = sql_query($q);
			if ($result) {
				$rowx = sql_fetch_array($result);
				if ($rowx["sync_guid"]>0) {
					$q = "select sync4j_source from users where id = ".$rowx["user_id"];
					$resx2 = sql_query($q);
					if (sql_result($resx2,0)) {
						$q = "insert into agenda_sync (user_id, sync_guid, action) values (".$rowx["user_id"].",".$rowx["sync_guid"].",'D')";
						sql_query($q);
					}
				}
			}


			$sql = sprintf("DELETE FROM calendar WHERE id=%d", $id);
			$res = sql_query($sql);
			return true;
		}
		/* }}} */
		/* {{{ save2db($data) */
		/**
		 * Store the calendar item in the db.
		 *
		 * @param array All form fields used in the edit/create calendar item
		 * @return bool nothing. it refreshes the opener (if one)
		 */
		public function save2db($data) {
			require_once(self::include_dir."dataSave2db.php");
		}
		/* }}} */
		/* {{{ getCalendarItemById($id) */
		/**
		 * Get a calendaritem by Id. Returns empty array when id = 0
		 *
		 * @param integer the id of the calendaritem
		 * @return array the calendaritem so we can use it in a view
		 */
		public function getCalendarItemById($id) {
			require(self::include_dir."dataGetCalendarItemById.php");
			return $appointment;
		}
		/* }}} */
		/* {{{ _get_appointments($_user, $_month, $_day, $_year) */
		/**
		 * Get all appointments that meet params
		 *
		 * @param integer the userid
		 * @param integer the month
		 * @param integer the day
		 * @param integer the year
		 * @return array the calendaritems
		 */
		public function _get_appointments($_user, $_month, $_day, $_year, $compress=0) {
			require(self::include_dir."dataGetAppointments.php");
			return $arr;
		}
		/* }}} */
		/* {{{ getActivityNames() */
		/**
		 * Get the activities for hour registration
		 *
		 * @return array the activities
		 */
		public function getActivityNames() {
			$sql = "SELECT id,activity FROM hours_activities ORDER BY UPPER(activity)";
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$activities[$row["id"]] = $row["activity"];
			}
			return $activities;
		}
		/* }}} */
		/* {{{ getActivityTarifs() */
		/**
		 * Get the activity costs
		 *
		 * @return array the hour tarifs
		 */
		public function getActivityTarifs() {
			$sql = "SELECT id,tarif FROM hours_activities";
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$activitycosts[$row["id"]] = $row["tarif"];
			}
			return $activitycosts;
		}
		/* }}} */
		/* {{{ reg_save() */
		/**
		 * store registered hours in db
		 *
		 * @return bool true on succes
		 */
		public function reg_save() {
			require(self::include_dir."dataRegSave.php");
		}
		/* }}} */
		/* {{{ convertTodoToCalendarItem($todoid) */
		/**
		 * convert a todo to calendar_item format for input
		 *
		 * @param int The todo id.
		 * @return array The data structure of a calendar item
		 */
		public function convertTodoToCalendarItem($todoid) {
			require(self::include_dir."dataConvertTodoToCalendarItem.php");
			return $item;
		}
		/* }}} */
		/* xml_chec() {{{ */
		/**
		 * Do some checking for conflicts and call javascript
		 */
		public function xml_check() {
			/* make timestamps */
			$tstamp_from = mktime($_REQUEST["from_hour"], $_REQUEST["from_min"], 0, $_REQUEST["from_month"], $_REQUEST["from_day"], $_REQUEST["from_year"]);
			$tstamp_to   = mktime($_REQUEST["to_hour"], $_REQUEST["to_minute"], 0, $_REQUEST["from_month"], $_REQUEST["from_day"], $_REQUEST["from_year"]);
			/* check for starttime after endtime */
			if ($tstamp_from > $tstamp_to) {
				echo "update_conflict(1);";
				exit;
			}
			/* check for starttime same as endtime */
			if ($tstamp_from == $tstamp_to) {
				echo "update_conflict(2);";
				exit;
			}
			/* check for double appointments */
			$tstamp_to1 = $tstamp_to-1;
			$tstamp_from1 = $tstamp_from+1;
			$sql = "SELECT COUNT(*) as counter FROM calendar WHERE (((timestamp_start BETWEEN $tstamp_from AND $tstamp_to1) OR (timestamp_end BETWEEN $tstamp_from1 AND $tstamp_to)) or (timestamp_start<=$tstamp_from and timestamp_end>=$tstamp_to))";
			if ($_REQUEST["id"]) {
				$sql .= sprintf("AND id != %d", $_REQUEST["id"]);
			}
			$sql .= sprintf(" AND user_id=%d", $_REQUEST["user"]);
			//TODO: this should be added to the above query: AND gebruiker IN ($users_check)";
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["counter"]>0) {
				echo "update_conflict(3);";
				exit;
			}
			echo "update_conflict(0);";
			exit;
		}
		/* }}} */
		/* getKmItems {{{ */
		/**
		 * Get all the travel distances from hourreg database
		 *
		 * @param array Search options: start date, users
		 * @return array Data per user in an array field (containing arrays with individual items)
		 */
		public function getKmItems($options) {
			/* set some defaults if no options are given */
			if (!$options["start"]) {
				$options["start"] = mktime(0, 0, 0, date("m")-1, 1, date("Y"));
				$options["end"]   = mktime(0, 0, 0, date("m"), 0, date("Y"));
			}
			if (!$options["users"][0]) {
				$options["users"][0] = $_SESSION["user_id"];
			}
			/* initialize empty return val */
			$return = array();
			/* gather data from db */
			foreach ($options["users"] as $user) {
				/* set total counts to 0 at the start */
				$return[$user]["total_dec"] = $return[$user]["total_non_dec"] = 0;
				/* construct sql */
				$sql  = "SELECT timestamp_start, subject, description, location, kilometers, deckm FROM calendar ";
				$sql .= sprintf("WHERE kilometers > 0 AND is_registered = 1 AND user_id = %d AND timestamp_start BETWEEN %d AND %d ", $user, $options["start"], $options["end"]);
				$sql .= "ORDER BY timestamp_start";
				$res = sql_query($sql);

				while ($row = sql_fetch_assoc($res)) {
					if (!trim($row["subject"])) {
						$row["subject"] = substr($row["description"], 0, 50);
					}
					$row["human_date"] = date("d-m-Y", $row["timestamp_start"]);
					$return[$user]["items"][] = $row;
					if ($row["deckm"]) {
						$return[$user]["total_dec"] += $row["kilometers"];
					} else {
						$return[$user]["total_non_dec"] += $row["kilometers"];
					}
				}
			}
			return $return;
		}
		/* }}} */
		/* checkPermission {{{ */
		/**
		 * Checks visitor of calendar's access to requested users calendar
		 *
		 * @param int Userid of the requested calendar
		 * @param int Userid of the visitor
		 * @return mixed integer 0 if no access, string RO for readonly and string RW for full access
		 */
		public function checkPermission($calendarowner, $visitor) {
			$permissions = 0;
			$sql = sprintf("SELECT * FROM calendar_permissions WHERE user_id = %d AND user_id_visitor = %d", $calendarowner, $visitor);
			$res = sql_query($sql);
			if (sql_num_rows($res)) {
				$row = sql_fetch_assoc($res);
				$permissions = $row["permissions"];
			}
			return $permissions;
		}
		/* }}} */
		/* getDelegationByUser {{{ */
		/**
		 * Delegation info for a specific user.
		 *
		 * The return array looks like:
		 * [id] => database record id,
		 * [user_id] => user who owns the calendar,
		 * [user_id_visitor] => visitor of calendar,
		 * [permissions] => RO or RW
		 *
		 * @param int The userid to get the delegation/share info for
		 * @return array database records for the userid
		 */
		public function getDelegationByUser($user_id) {
			$sql = sprintf("SELECT * FROM calendar_permissions WHERE user_id = %d", $user_id);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$delegation[] = array(
					"id"              => $row["id"],
					"user_id"         => $row["user_id"],
					"user_id_visitor" => $row["user_id_visitor"],
					"permission"      => $row["permissions"]
				);
			}
			return $delegation;
		}
		/* }}} */
		/* save_permissions {{{ */
		/**
		 * Store delegation to database
		 *
		 * This function will output some javascript to close page, if param array has key 'close_win' set to value other then 0.
		 * the data array has the following format:
		 * [user_id] => (int)N: the userid who ownz the calendar
		 * [ro] => (string)a,b,c: comma seperated user_id's of users who have readonly access to the calendar
		 * [rw] => (string)a,b,c: comma seperated user_id's of users who have readwrite access to the calendar
		 *
		 * @param array The data to be saved
		 */
		public function save_permissions($data) {
			/* sanitize data */
			$data["ro"] = preg_replace("/,{1,}$/", "", preg_replace("/^,{1,}/", "", $data["ro"]));
			$data["rw"] = preg_replace("/,{1,}$/", "", preg_replace("/^,{1,}/", "", $data["rw"]));
			/* cleanup permission table */
			$sql = sprintf("DELETE FROM calendar_permissions WHERE user_id = %d", $data["user_id"]);
			$res = sql_query($sql);
			/* put ro users in the database */
			$users_ro = explode(",", $data["ro"]);
			foreach ($users_ro as $v) {
				$sql = sprintf("INSERT INTO calendar_permissions (user_id, user_id_visitor, permissions) VALUES (%d, %d, '%s')", $data["user_id"], $v, "RO");
				$res = sql_query($sql);
			}
			$users_rw = explode(",", $data["rw"]);
			foreach ($users_rw as $v) {
				$sql = sprintf("INSERT INTO calendar_permissions (user_id, user_id_visitor, permissions) VALUES (%d, %d, '%s')", $data["user_id"], $v, "RW");
				$res = sql_query($sql);
			}
			/* little trick to close the window if this is used in the webapp */
			if ($data["closewin"]) {
				$output = new Layout_output();
				$output->layout_page("", 1);
					$output->start_javascript();
						$output->addCode(
							"
							if (opener) {
								window.close();
							}
							"
						);
					$output->end_javascript();
				$output->layout_page_end();
				$output->exit_buffer();
			}
		}
		/* }}} */
	}
?>
