<?php
	/**
	 * Covide Groupware-CRM calendar module
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
	Class Calendar_data {
		/* constants */
		const include_dir = "classes/calendar/inc/";
		const class_name  = "calendar";

		/* variables */

		/**
		 * @var array dataplaceholder for all items
		 */
		public $calendar_items = array();
		/**
		 * @var array userdetails cache variable
		 */
		public $userdetails_cache = array();
		/* methods */
		public function __construct($sync = 0) {
			//check if we have to run the first migration
			// This is the migration to the new repeat etc functionality introduced in Covide 8.2
			if (!$GLOBALS["covide"]->license["calendar_migrated"] || $GLOBALS["covide"]->license["calendar_migrated"] < 1) {
				$this->migrate_calendar01(1);
				$sql = "UPDATE license SET calendar_migrated=1";
				$res = sql_query($sql);
				$GLOBALS["covide"]->license["calendar_migrated"] = 1;
			}
			if ($_SESSION["user_id"] && $sync == 1) {
				$user_data = new User_data();
				$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
				if ($user_info["google_username"] && $user_info["google_password"]) {
					$google_data = new Google_data();
					$gClient = $google_data->getGoogleClientLogin($_user_info["google_username"], $_user_info["google_password"], "calendar");
					$google_data->syncGoogleCalendarClient($_SESSION["user_id"], $gClient);
				}
			}
		}
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
			$sql  = sprintf("SELECT calendar.*, calendar_user.user_id, calendar_user.status FROM calendar, calendar_user WHERE calendar.id = calendar_user.calendar_id AND (address_id = %1\$d OR multirel LIKE '%%,%1\$d,%%'", $address_id);
			$sql .= sprintf(" OR multirel LIKE '%1\$d,%%' OR multirel LIKE '%%,%1\$d' OR multirel = '%1\$d')", $address_id);
			//TODO: fix the following line
			//$sql .= sprintf(" AND (is_private = 0 OR user_id = %d)", $_SESSION["user_id"]);
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

				/* wipe out info on private appointments that are not for logged in user */
				if ($_SESSION["user_id"] != $row["user_id"] && (($row["status"] != 1 && $row["status"] != 4) || $row["is_private"] == 1)) {
					$calendaritems[$i]["subject"] = gettext("private appointment");
					$calendaritems[$i]["body"]    = gettext("private appointment");
				} else {
					if ($row["subject"]) {
						$calendaritems[$i]["subject"] = $row["subject"];
					} else {
						$calendaritems[$i]["subject"] = substr($row["body"], 0, 80);
					}
					$calendaritems[$i]["body"] = $row["body"];
				}
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
			$sql  = sprintf("SELECT calendar.*, calendar_user.user_id, calendar_user.status FROM calendar, calendar_user WHERE calendar.id = calendar_user.calendar_id AND user_id = %d", $user_id);
			if (!$history) {
				$sql .= sprintf(" AND timestamp_start >= %d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
			} else {
				$sql .= sprintf(" AND timestamp_end < %d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
			}
			//$sql .= sprintf(" AND (is_private != 1 OR user_id = %d)", $_SESSION["user_id"]);
			$sql .= " ORDER BY timestamp_start ASC";
			$res = sql_query($sql);
			$i = 0;
			while ($row = sql_fetch_assoc($res)) {
				$calendaritems[$i] = $row;
				$calendaritems[$i]["human_start"] = date("d-m-Y H:i", $row["timestamp_start"]);
				$calendaritems[$i]["human_end"]   = date("d-m-Y H:i", $row["timestamp_end"]);
				$calendaritems[$i]["user_name"]   = $userinfo[$row["user_id"]];
				/* wipe out info on private appointments that are not for logged in user */
				if ($_SESSION["user_id"] != $row["user_id"] && (($row["status"] != 1 && $row["status"] != 4) || $row["is_private"] == 1)) {
					$calendaritems[$i]["subject"] = gettext("private appointment");
					$calendaritems[$i]["body"]    = gettext("private appointment");
				} else {
					if ($row["subject"]) {
						$calendaritems[$i]["subject"] = $row["subject"];
					} else {
						$calendaritems[$i]["subject"] = substr($row["body"], 0, 80);
					}
					$calendaritems[$i]["body"] = $row["body"];
				}
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

			//FIXME: get access permissions if session user_id is set. I'm not sure wether funambol or something uses this function so builtin a failsafe for no session set
			if (array_key_exists("user_id", $_SESSION)) {
				$permissions = $this->getDelegationByVisitor($_SESSION["user_id"]);
				if (!is_array($permissions)) {
					$permissions = array(
						0 => array(
							"id"              => 1,
							"user_id"         => $_SESSION["user_id"],
							"user_id_visitor" => $_SESSION["user_id"],
							"permission"      => "RW"
						)
					);
				}
			} else {
				$permissions = 1;
			}

			$buf = sql_syntax("buffer");
			$sql = "SELECT ".$buf." calendar.*,calendar_user.user_id FROM calendar, calendar_user WHERE calendar.id = calendar_user.calendar_id AND (isrecurring = 0 OR isrecurring IS NULL)";
			if (is_array($permissions)) {
				$user_ids = array($_SESSION["user_id"]);
				foreach ($permissions as $k=>$v) {
					$user_ids[] = $v["user_id"];
				}
				$sql .= sprintf(" AND user_id IN (%s)", implode(",", $user_ids));
			}
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
				$sql .= sprintf(" AND address_id IN (%s)", $options["address_id"]);
			}
			if ($options["project_id"]) {
				$sql .= sprintf(" AND project_id = %d", $options["project_id"]);
			}
			if ($options["searchkey"]) {
				$like_syntax = sql_syntax("like");
				$sql .= sprintf(" AND (subject $like_syntax '%%%1\$s%%' OR body $like_syntax '%%%1\$s%%')", $options["searchkey"]);
			}
			if ($options["date"]) {
				$sql .= sprintf(" AND timestamp_start BETWEEN %d AND %d", $options["date"]["start"], $options["date"]["end"]);
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
				if ($calendaritems[$i]["subject"] == "") { $calendaritems[$i]["subject"] = substr($calendaritems[$i]["body"], 0, 40); }
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
		/* {{{ sendDeleteNotificationMail($id, $timestamp) */
		/**
		 * notify users with mail upon appointment deletion
		 *
		 * @param int calendar item id
		 * @return bool true on succes
		 */
		public function sendDeleteNotificationMail($id, $timestamp="") {
			$sql = sprintf("SELECT * FROM calendar WHERE id = %d", $id);
			$result = sql_query($sql);
			$r = sql_fetch_assoc($result);

			// get users realname
			$user_data = new User_data();
			$user_info = $user_data->getEmployeedetailsById($_SESSION["user_id"]);

			$mail_subject = gettext("Deletion of appointment")." ".strftime("%A %d %B %Y %H:%M", $r["timestamp_start"]);
			$mail_body  = $user_info["realname"]." ".gettext("of company")." ".$GLOBALS["covide"]->license["name"]." has cancelled the appointment";
			if ($timestamp != "") {
				$mail_body .= " ".gettext("on date")." ".strftime("%A %d %B %Y", $timestamp).".";
			}
			$mail_body .= ".\n\n";
			$mail_body .= gettext("starttime").": ".strftime("%H:%M", $r["timestamp_start"])."\n";
			$mail_body .= gettext("endtime").": ".strftime("%H:%M", $r["timestamp_end"])."\n";
			$mail_body .= gettext("subject").": ".$r["subject"]."\n\n\n";
			$mail_body .= gettext("Regards")."\n\n".$user_info["realname"]."\n".$GLOBALS["covide"]->license["name"];

			$from = $user_info["mail_email"];
			if (!trim($from)) {
				$from = "support@terrazur.nl";
			}

			$mail_data = new Email_data();
			$concept_id = $mail_data->save_concept();

			$sql = sprintf("SELECT * FROM calendar_user INNER JOIN users ON users.id = calendar_user.user_id WHERE calendar_id = %d", $id);
			$rs = sql_query($sql);
			while ($row = sql_fetch_assoc($rs)) {
				if ($from != $row["mail_email"]) {
					$to_email.= $row["mail_email"]." , ";
				}
			}

			$mailreq = array(
				"view_mode" => "html",
				"mail" => array(
					"from" => $from,
					"subject" => $mail_subject,
					"to" => $to_email,
					"rcpt" => $to_email,
				),
				"contents" => nl2br($mail_body),
			);

			if ($to_email != "") {
				$tmp_buf[1] = $mail_data->save_concept($concept_id, $mailreq);
				$tmp_buf[2] = $mail_data->sendMailComplex($concept_id, 1, 1);
			}
			return true;
		}
		/* }}} */
		/* {{{ delete($id) */
		/**
		 * delete item from db
		 *
		 * @todo change function name, delete is a keyword in PHP
		 * @todo remove calendar_repeat and calendar_exceptions data as well
		 *
		 * @param int calendar item id
		 * @param int $user_id The user to delete this item for
		 *
		 * @return bool true on succes
		 */
		public function delete($id, $user_id) {
			// remove the item from google if needed
			$_user_data = new User_data();
			$_user_info = $_user_data->getUserDetailsById($user_id);
			$google_data = new Google_data();
			if ($_user_info["google_username"] && $_user_info["google_password"]) {
				$gClient = $google_data->getGoogleClientLogin($_user_info["google_username"], $_user_info["google_password"], "calendar");
			}
			
			$sql = sprintf("SELECT user_id, google_id FROM calendar_user WHERE calendar_id = %d AND user_id = %d", $id, $user_id);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				if ($row["google_id"]) {
					if ($_user_info["google_username"] && $_user_info["google_password"]) {
						$google_data->deleteGoogleCalendarItemClient($gClient, $row["google_id"]);
					}
				}
			}

			/* send notification mail to other users */
			if ($_REQUEST["notifyuser"] == "send") {
				$this->sendDeleteNotificationMail($id);
			}

			// find out if we end up with a calendar item without linked users
			$sql = sprintf("SELECT COUNT(*) FROM calendar_user WHERE calendar_id = %d AND user_id != %d", $id, $user_id);
			$res = sql_query($sql);
			$usercount = sql_result($res, 0);

			if ($usercount == 0) {
				$campaign_data = new Campaign_data();
				$is_app = $campaign_data->campaignHasAppointment($id);
				if ($is_app) {
					$q = sprintf("UPDATE campaign_records SET appointment_id = NULL WHERE appointment_id = %d", $id);
					$r = sql_query($q);
				}

				/* delete dim dim if exists */
				$sql = sprintf("SELECT dimdim_meeting FROM calendar WHERE id = %d", $id);
				$result = sql_query($sql);
				if ($result) {
					$dimdim_data = new Dimdim_data();
					$dimdim_data->deleteMeeting(sql_result($result, 0));
				}
				$sql = sprintf("DELETE FROM calendar WHERE id = %d", $id);
				sql_query($sql);
			}
			$sql = sprintf("DELETE FROM calendar_user WHERE calendar_id = %d AND user_id = %d", $id, $user_id);
			sql_query($sql);
			if ($GLOBALS["covide"]->license["has_funambol"]) {
				$funambol_data = new Funambol_data();
				$funambol_data->removeRecord("calendar", $id);
			}
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
		public function save2db($data, $funambol_input=0) {
			require(self::include_dir."dataSave2db.php");
			return $modified_ids;
		}
		/* }}} */
		/* getCalendarItemById {{{ */
		/**
		 * Get a calendaritem by Id. Returns empty array when id = 0
		 *
		 * @param integer $id the id of the calendaritem
		 * @param integer $user_id The user to grab this item for.
		 * @param integer $funambol TODO:document
		 * @return array the calendaritem so we can use it in a view
		 */
		public function getCalendarItemById($id, $user_id, $funambol=0) {
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
		 * @param int $user_id
		 * @param int $show_placeholder if set to 1 shows 'none' element used in form dropdowns
		 *
		 * @return array the activities
		 */
		public function getActivityNames($user_id = 0, $show_placeholder = 0) {
			$projadmin = 1;
			$user_data = new User_data();
			$activities = array();
			if ($show_placeholder == 1) {
				$activities[0] = gettext("geen");
			}

			if ($GLOBALS["covide"]->license["has_project_ext"]) {
				$projext_data = new Projectext_data();
				$departments = $projext_data->extGetDepartments();
			}
			$sql = "SELECT id, activity, department_id, user_id FROM hours_activities ORDER BY UPPER(activity)";
			if ($user_id) {
				$userdetails = $user_data->getUserDetailsById($user_id);
				if (!$userdetails["xs_projectmanage"] && !$userdetails["xs_limited_projectmanage"]) {
					$sql = sprintf("SELECT id, activity, department_id FROM hours_activities WHERE user_id = 0 OR user_id IS NULL OR user_id = %d ORDER BY user_id, UPPER(activity)", $user_id);
					$projadmin = 0;
				}
			}
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				if ($row["department_id"]) {
					$activities[$departments[$row["department_id"]]["department"]][$row["id"]] = $row["activity"];
				} else {
					if ($projadmin && $row["user_id"]) {
						$activities[$user_data->getUsernameById($row["user_id"])][$row["id"]] = $row["activity"];
					} else if ($projadmin) {
						$activities[gettext("global")][$row["id"]] = $row["activity"];
					} else {
						$activities[$row["id"]] = $row["activity"];
					}
				}
			}
			return $activities;
		}
		/* }}} */
		/* {{{ getActivityTarifs() */
		/**
		 * Get the activity costs
		 *
		 * @param int $type 1 for hourtarif, 2 for purchase tarif, 3 for marge tarif, -1 for all types in an array
		 * @return array the hour tarifs
		 */
		public function getActivityTarifs($type = 1) {
			switch($type) {
			case -1: $column = "*";           break;
			case 2:  $column = "id,purchase"; break;
			case 3:  $column = "id,marge";    break;
			default: $column = "id,tarif";    break;
			}
			$sql = sprintf("SELECT %s FROM hours_activities", $column);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				if ($type == -1) {
					$activitycosts[$row["id"]] = array(
						"purchase" => $row["purchase"],
						"marge"    => $row["marge"],
						"tarif"    => $row["tarif"]
					);
				} else {
					$activitycosts[$row["id"]] = $row[str_replace("id,", "", $column)];
				}
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
		/* xml_check() {{{ */
		/**
		 * Do some checking for conflicts and call javascript
		 */
		public function xml_check() {
			/* if this is an event, then no checks are needed. */
			if ($_REQUEST["is_event"] == "true") {
				echo "update_conflict(4);";
				exit;
			}
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
			$tstamp_to1   = $tstamp_to-1;
			$tstamp_from1 = $tstamp_from+1;
			if ($_REQUEST["user_id"]) {
				//add $_REQUEST["user"] and sanitize the array
				$_users = explode(",", $_REQUEST["user_id"]);
				$_users[] = $_REQUEST["user"];
				$_users = array_unique($_users);
				foreach ($_users as $k=>$v) {
					if ((int)$v <= 0)
						unset($_users[$k]);
				}
				$users_to_check = implode(",", $_users);
			} else {
				$_users = array($_REQUEST["user"]);
				$users_to_check = sprintf("%d", $_REQUEST["user"]);
			}
			foreach ($_users as $v) {
				$items = $this->_get_appointments($v, date("m", $tstamp_from), date("d", $tstamp_from), date("Y", $tstamp_from), 1);
				if (count($items) > 0) {
					$test_start = date("Hi", $tstamp_from1);
					$test_end   = date("Hi", $tstamp_to1);
					foreach ($items as $item) {
						if ($item > 0) {
							if ($_REQUEST["id"] && $_REQUEST["id"] == $this->calendar_items[$item]["id"]) {
								continue;
							}
							$item_start = date("Hi", $this->calendar_items[$item]["ststamp"]);
							$item_end   = date("Hi", $this->calendar_items[$item]["etstamp"]);
							if (($test_start >= $item_start && $test_start <= $item_end) ||
								($test_end >= $item_start && $test_end <= $item_end)) {
									echo "update_conflict(3);";
									exit;
							}
						}
					}
				}
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
		 * if options[noreg] is set it will return all kilometers, no matter if the item is registered or not.
		 * @return array Data per user in an array field (containing arrays with individual items)
		 */
		public function getKmItems($options) {
			$user_data = new User_data();
			$project_data = new Project_data();
			$address_data = new Address_data();
			/* set some defaults if no options are given */
			if (!$options["start"]) {
				$options["start"] = mktime(0, 0, 0, date("m")-1, 1, date("Y"));
				$options["end"]   = mktime(0, 0, 0, date("m"), 0, date("Y"));
			}
			if (!$options["users"][0] && !$options["allusers"]) {
				$options["users"][0] = $_SESSION["user_id"];
			} else {
				$userlist = $user_data->getUserList();
				$options["users"] = array();
				foreach ($userlist as $userid => $username) {
					$options["users"][] = $userid;
				}
			}
			if ($options["noreg"]) {
				$status = " != 2";
			} else {
				$status = " IN (1,3,4)";
			}
			if ($options["project_id"]) {
				$sq_project = sprintf("AND calendar.project_id = %d ", $options["project_id"]);
			} else {
				$sq_project = "";
			}
			/* initialize empty return val */
			$return = array();
			/* gather data from db */
			foreach ($options["users"] as $user) {
				/* set total counts to 0 at the start */
				$return[$user]["total_dec"] = $return[$user]["total_non_dec"] = 0;
				$return[$user]["username"] = $user_data->getUsernameById($user);
				/* grab kilometer allowance for this user */
				$user_info = $address_data->getHRMInfo($user);
				if ($user_info[0]["kilometer_allowance"]) {
					$kmall = $user_info[0]["kilometer_allowance"];
				} else {
					$kmall = 0;
				}
				/* construct sql */
				$sql  = "
					SELECT
						calendar.id,
						calendar.timestamp_start,
						calendar.subject,
						calendar.body as description,
						calendar.location,
						calendar.kilometers,
						calendar.deckm,
						calendar_user.user_id,
						calendar_user.status,
						calendar.project_id
					FROM
						calendar,calendar_user ";
				$sql .= sprintf("WHERE calendar.id = calendar_user.calendar_id AND kilometers > 0 AND status %s AND user_id = %d AND timestamp_start BETWEEN %d AND %d ", $status, $user, $options["start"], $options["end"]);
				$sql .= $sq_project;
				$sql .= "ORDER BY timestamp_start";
				$res = sql_query($sql);

				while ($row = sql_fetch_assoc($res)) {
					if (!trim($row["subject"])) {
						$row["subject"] = substr($row["description"], 0, 50);
					}
					$row["human_date"] = date("d-m-Y", $row["timestamp_start"]);
					$row["user_name"] = $user_data->getUsernameById($row["user_id"]);
					$row["project_name"] = $project_data->getProjectnameById($row["project_id"]);
					if ($row["deckm"]) {
						$costs = $row["kilometers"]*$kmall/100;
						$row["costs"] = $costs;
						$return[$user]["total_costs"] += $costs;
						$return[$user]["total_dec"] += $row["kilometers"];
					} else {
						$return[$user]["total_non_dec"] += $row["kilometers"];
					}
					$return[$user]["items"][] = $row;
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
		/* getDelegationByVisitor {{{ */
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
		public function getDelegationByVisitor($user_id) {
			$sql = sprintf("SELECT * FROM calendar_permissions WHERE user_id_visitor = %d", $user_id);
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
							if (!opener) {
								history.go(-2)
							}
							"
						);
					$output->end_javascript();
				$output->layout_page_end();
				$output->exit_buffer();
			}
		}
		/* }}} */
		/* detectCestSwitch {{{ */
		/**
		 * Find out if DST switch falls in this week
		 *
		 * @param int $weekstart timestamp of the first second in the week where $ts is in
		 * @param int $ts timestamp we want to check
		 * @return int difference in seconds if DST switched
		 */
		public function detectCestSwitch($weekstart, $ts) {
			//detect offset zone for weekstart
			if (strftime("%Z", $weekstart) == "CET")
				$offset_weekstart = 2;
			else
				$offset_weekstart = 1;

			if (strftime("%Z", $ts) == "CET")
				$offset_ts = 2;
			else
				$offset_ts = 1;

			$diff = ($offset_ts - $offset_weekstart) * 60 * 60;
			return $diff;
		}
		/* }}} */
		/* checkRegistrationState {{{ */
		/**
		 * Check if a calendar item is already registered
		 *
		 * @param int $item_id The calendaritem id
		 * @param int $user_id The userid to check for the calendaritem
		 * @return bool true if registered, false if not
		 */
		public function checkRegistrationState($item_id, $user_id) {
			$q = sprintf("select status from calendar_user  where calendar_id = %d AND user_id = %d", $item_id, $user_id);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);
			if ($row["status"] == 4) {
				return true;
			} else {
				return false;
			}
		}
		/* }}} */
		/* {{{ getAppointmentsByPrivate($private_id) */
		/**
		 * Get the appointments linked to a private contact
		 *
		 * @param int private id
		 * @return array the actual appointments
		 */
		public function getAppointmentsByPrivate($private_id) {
			/* get users in an array */
			$userdata = new User_data();
			$userinfo = $userdata->getUserList();
			$today = mktime(0,0,0,date("m"),date("d"),date("Y"));
			$sql  = sprintf("SELECT * FROM calendar WHERE private_id = %d AND timestamp_start >= %d", $private_id, $today);
			$sql .= " ORDER BY timestamp_start ASC";
			$res = sql_query($sql);
			$i = 0;
			while ($row = sql_fetch_assoc($res)) {
				$calendaritems[$i] = $row;
				$calendaritems[$i]["human_start"] = date("d-m-Y H:i", $row["timestamp_start"]);
				$calendaritems[$i]["human_end"]   = date("d-m-Y H:i", $row["timestamp_end"]);
				$calendaritems[$i]["user_name"]   = $userinfo[$row["user_id"]];
				/* wipe out info on private appointments that are not for logged in user */
				if ($_SESSION["user_id"] != $row["user_id"] && ($row["status"] != 1 || $row["is_private"] == 1)) {
					$calendaritems[$i]["subject"] = gettext("private appointment");
					$calendaritems[$i]["body"]    = gettext("private appointment");
				} else {
					if ($row["subject"]) {
						$calendaritems[$i]["subject"] = $row["subject"];
					} else {
						$calendaritems[$i]["subject"] = substr($row["body"], 0, 80);
					}
					$calendaritems[$i]["body"] = $row["body"];
				}
				$i++;
			}
			return $calendaritems;
		}
		/* }}} */
		/* getNotifyTemplate {{{ */
		/**
		 * Get the calendar notification template for a user
		 *
		 * @param int $user_id The userid
		 * @return string Notification message
		 */
		public function getNotifyTemplate($user_id) {
			$sql = sprintf("SELECT * FROM calendar_notifications WHERE user_id = %d", $user_id);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res, 0);
			return trim($row["template"]);
		}
		/* }}} */
		/* save_notification {{{ */
		/**
		 * Save notification template in the database
		 *
		 * @param array $data array with user_id and description as key. More data will be ignored.
		 */
		public function save_notification($data) {
			// find out if we already have an entry for this user
			$sql = sprintf("SELECT COUNT(*) FROM calendar_notifications WHERE user_id = %d", $data["user_id"]);
			$res = sql_query($sql);
			$count = sql_result($res, 0);
			if ($count)
				$sql = sprintf("UPDATE calendar_notifications SET template = '%s' WHERE user_id = %d", $data["description"], $data["user_id"]);
			else
				$sql = sprintf("INSERT INTO calendar_notifications (user_id, template) VALUES (%d, '%s')", $data["user_id"], $data["description"]);
			$res = sql_query($sql);
		}
		/* }}} */
		/* getCalendarUsersByCalendarId {{{ */
		/**
		 * Fetch all user_id's for a specific appointment.
		 *
		 * @param int $calendar_id The calendar id of the appointment in question.
		 * @param int $user_id If given, remove this user_id from the result set. This is usefull if you call this function for the 'extra_users' field
		 * @return array The user_ids that are linked to the specified appointment.
		 */
		public function getCalendarUsersByCalendarId($calendar_id, $user_id = 0) {
			$users = array();
			$sql = sprintf("SELECT user_id FROM calendar_user WHERE calendar_id = %d", $calendar_id);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				if ($row["user_id"] != $user_id)
					$users[] = $row["user_id"];
			}
			return $users;
		}
		/* }}} */
		/* getDaysPerMonth {{{ */
		/**
		 * Return the total days of given month in given year
		 *
		 * @param int $month The month in question
		 * @param int $year The year. Needed for leapyears
		 *
		 * @return int Number of days in the month
		 */
		function getDaysPerMonth($month, $year) {
			return date("t", mktime(0, 0, 0, $month, 1, $year));
		}
		/* }}} */
		/* query_items {{{ */
		/**
		 * Read calendar items visible to the user.
		 *
		 * @param int $user_id Userid
		 * @param bool $repeating Get repeating items when true
		 * @param int timestamp_start Start of view
		 * @param int timestamp_end End of the view
		 *
		 * @return array The calendar items sorted by date
		 */
		public function query_items($user_id, $repeating, $timestamp_start, $timestamp_end) {
			/**
			 * Some variables and classes we need.
			 * $counter is used to give each item a unique position in the array
			 * $result will be the array we are going ot return
			 */
			$counter      = 0;
			$result       = array();

			//create filter and column and table strings to use in the query
			if ($repeating) {
				$datefilter    = sprintf("AND calendar.id = calendar_repeats.calendar_id
				AND (calendar_repeats.timestamp_end >= %d OR calendar_repeats.timestamp_end IS NULL OR calendar_repeats.timestamp_end = '')
				AND calendar.isrecurring = 1",
				$timestamp_start);
				$repeatcolumns = ",calendar_repeats.repeat_type, calendar_repeats.timestamp_end as repeat_end, calendar_repeats.repeat_frequency as repeat_freq, calendar_repeats.repeat_days";
				$repeattables  = "calendar_repeats, ";
			} else {
				$datefilter    = sprintf("AND (calendar.timestamp_start >= %d AND calendar.timestamp_end <= %d)
				AND calendar.isrecurring = 0", $timestamp_start, $timestamp_end);
				$repeatcolumns = "";
				$repeattables  = "";
			}

			//construct query
			$q = sprintf("SELECT
				DISTINCT(calendar.id), calendar.timestamp_start, calendar.timestamp_end,
				calendar.alldayevent, calendar.is_private, calendar.isrecurring as is_repeat,
				calendar_user.status
				%s
			FROM
				calendar, %s calendar_user
			WHERE
				calendar.id = calendar_user.calendar_id
				%s
				AND calendar_user.user_id = %d",
				$repeatcolumns, $repeattables, $datefilter, $user_id);

			//process results
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				//skip rejected items
				if ($row["status"] == 2) {
					continue;
				}
				$b_time = (date("H",$row["timestamp_start"])*60)+date("i",$row["timestamp_start"]);
				$e_time = (date("H",$row["timestamp_end"])*60)+date("i",$row["timestamp_end"]);
				//$item = $this->getCalendarItemById($row["id"], $user_id);
				$item = $row;
				//add exceptions to repeating items
				$item["exceptions"] = $this->getCalendarExceptionsById($row["id"], $user_id);
				$result[$counter] = $item;
				//increase the counter
				$counter++;
			}
			return $result;
		}
		/* }}} */
		/* getCalendarExceptionsById {{{ */
		/**
		 * Get the exceptions for a specific calendar item
		 *
		 * @param int $calendar_id The item id
		 * @param int $user_id The userid we are looking for
		 *
		 * @return array timestamps of days the exception is set for
		 */
		public function getCalendarExceptionsById($calendar_id, $user_id) {
			// $result is the array we are returning
			$result = array();
			$sql = sprintf("SELECT * FROM calendar_exceptions WHERE calendar_id = %d AND user_id = %d", $calendar_id, $user_id);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$return[] = $row;
			}
			return $return;
		}
		/* }}} */
		/* getRepeatingItemsByDate {{{ */
		/**
		 * return the repeating items for given date in given itemcollection
		 *
		 * use {@link query_items} to grab all repeating items for a user and use this
		 * function to find items in there for given date
		 *
		 * @param int $user_id The userid in question
		 * @param array $repeating_items The collection of raw repeating items data
		 * @param int $timestamp Timestamp for repeating items (rounded to a full day by this function)
		 * @param bool $get_waiting If true it will also return items with status 'waiting for ok by user'
		 *
		 * @return array only the items in $repeating_items that match
		 */
		public function getRepeatingItemsByDate($user_id, $repeating_items, $timestamp, $get_waiting = False) {
			/**
			 * initialize some variables we will need
			 *
			 * $n - counter for return array keys so they get nice increasing number
			 * $ret - Return array
			 */
			$n = 0;
			$ret = array();

			for ($i = 0; $i < count($repeating_items); $i++) {
				//Only get active items, or waiting if $get_waiting is set. Other stati will be ignored
				if ($repeating_items[$i]["status"] == 1 || $get_waiting) {
					//check if this item applies for the timestamp given
					if ($this->checkRepeatedItemByTimestamp($repeating_items[$i], $timestamp)) {
						// filter out exceptions
						$skipthisone = false;
						if (is_array($repeating_items[$i]["exceptions"])) {
							foreach ($repeating_items[$i]["exceptions"] as $k=>$v) {
								if ($v["user_id"] == $user_id && $v["timestamp_exception"] == $timestamp)
									$skipthisone = true;

							}
						}
						if (!$skipthisone) {
							$ret[$n] = $repeating_items[$i];
							$n++;
						}
					}
				}
			}
			return $ret;
		}
		/* }}} */
		/* checkRepeatedItemByTimestamp {{{ */
		/**
		 * Determines wether the item will fall on the day where the timestamp is
		 *
		 * @param array $calendar_item The calendar item
		 * @param int $timestamp The timestamp to check
		 *
		 * @return bool true when timestamp and item match
		 */
		public function checkRepeatedItemByTimestamp($calendar_item, $timestamp) {
			// init a var to hold seconds for one day
			$oneday = 24*60*60;
			//normalize timestamp to today's start
			$ts = mktime(0, 0, 0, date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp));
			//normalize start of item to today's start
			$ts_item = mktime(0, 0, 0, date("m", $calendar_item["timestamp_start"]), date("d", $calendar_item["timestamp_start"]), date("Y", $calendar_item["timestamp_start"]));
			// only repeat from the start and to the end if endtime is set
			if ($ts < $ts_item) {
				return false;
			}
			if ($calendar_item["repeat_end"] && $ts > $calendar_item["repeat_end"]) {
				return false;
			}
			switch ($calendar_item["repeat_type"]) {
			case 1:
				// check daily repeating item
				if (floor(($ts - $calendar_item["timestamp_start"])/$oneday%$calendar_item["repeat_freq"])) {
					return false;
				}
				return true;
				break;
			case 2:
				// check weekly repeating
				// get day of the week for the timestamp we are checking
				$dow    = date("w", $ts);
				// get day of the week for the appointment
				$dow1   = date("w", $calendar_item["timestamp_start"]);
				// grab the status flag in the repeat_days item on the location of weekday.
				// this data part looks like this for every day: yyyyyyy and this for no day: nnnnnnn or any combination
				$is_day = substr($calendar_item["repeat_days"], $dow, 1);
				// get start of week
				$wstart = $calendar_item["timestamp_start"] - ($dow1 * $oneday);

				// check if this appointment falls in this week
				if (floor(($ts - $wstart)/604800)%$calendar_item["repeat_freq"]) {
					return false;
				}
				// check if we are looking at a day that this item should occur
				return (strcmp($is_day, "y") == 0);
				break;
			case 3:
				// check monthly by day of month
				$dow_start = date("w", $calendar_item["timestamp_start"]);
				$dow_ts    = date("w", $ts);
				// if current day is not the same as day of item, bail out here.
				// This will prevet the whole logic to be run on an item that wont match anyways
				if ($dow_ts != $dow_start) {
					return false;
				}
				// prepare checking data from calendar item
				$day_start   = floor(date("d", $calendar_item["timestamp_start"]));
				$month_start = date("m", $calendar_item["timestamp_start"]);
				$year_start  = date("Y", $calendar_item["timestamp_start"]);
				$dow1_start  = (date("w", $calendar_item["timestamp_start"] - ($one_day * ($day_start - 1))) + 35) % 7;
				$days_in_first_week_start = (7 - $dow1_start) % 7;
				$whichweek_start = round(($day_start - $days_in_first_week_start) / 7);
				if ($dow_start >= $dow1_start && $days_in_first_week_start) {
					$whichweek_start++;
				}
				// prepare the same data for the $ts
				$day   = date("d", $ts);
				$month = date("m", $ts);
				$year  = date("Y", $ts);
				$dow1  = (date("w", $ts - ($one_day * ($day - 1))) + 35) % 7;
				$days_in_first_week = (7 - $dow1) % 7;
				$whichweek = round(($day - $days_in_first_week) / 7);
				if ($dow_ts >= $dow1 && $days_in_first_week) {
					$whichweek++;
				}

				if ((($year - $year_start) * 12 + $month - $month_start) % $calendar_item["repeat_freq"])
					return false;

				return ($whichweek == $whichweek_start);
				break;
			case 4:
				// check monthly by day of month (from end)
				$dow_start = date("w", $calendar_item["timestamp_start"]);
				$dow_ts    = date("w", $ts);
				// if current day is not the same as day of item, bail out here.
				// This will prevet the whole logic to be run on an item that wont match anyways
				if ($dow_ts != $dow_start) {
					return false;
				}
				// prepare checking data from calendar item
				$day_start = ceil(date("d", $calendar_item["timestamp_start"]));
				$month_start = ceil(date("m", $calendar_item["timestamp_start"]));
				$year_start  = date("Y", $calendar_item["timestamp_start"]);
				$daysthismonth_start = $this->getDaysPerMonth($month_start, $year_start);
				$whichweek_start = floor(($daysthismonth_start - $day_start) / 7);
				// prepare the same data for the $ts
				$day = ceil(date("d", $ts));
				$month = ceil(date("m", $ts));
				$year  = date("Y", $ts);
				$daysthismonth = $this->getDaysPerMonth($month, $year);
				$whichweek = floor(($daysthismonth - $day) / 7);

				if ((($year - $year_start) * 12 + $month - $month_start) % $calendar_item["repeat_freq"]) {
					return false;
				}
				return ($whichweek_start == $whichweek);
				break;
			case 5:
				// check monthly by date
				$month_start = date("m", $calendar_item["timestamp_start"]);
				$year_start  = date("Y", $calendar_item["timestamp_start"]);

				$month_ts    = date("m", $ts);
				$year_ts     = date("Y", $ts);

				if ((($year - $year_start) * 12 + $month - $month_start) % $calendar_item["repeat_freq"]) {
					return false;
				}
				return (date("d", $ts) == date("d", $calendar_item["timestamp_start"]));
				break;
			case 6:
				// check yearly
				$year_start = date("Y", $calendar_item["timestamp_start"]);

				$year = date("Y", $ts);

				if (($year - $year_start) % $calendar_item["repeat_freq"]) {
					return false;
				}
				return (date("dm", $ts) == date("dm", $calendar_item["timestamp_start"]));
				break;
			case 7:
				// check workday repeating
				// get day of the week for the timestamp we are checking
				$dow    = date("w", $ts);
				// get day of the week for the appointment
				$dow1   = date("w", $calendar_item["timestamp_start"]);
				// grab the status flag in the repeat_days item on the location of workdays.
				// this data part looks like this for every day: nyyyyyn or any combination
				$is_day = substr($calendar_item["repeat_days"], $dow, 1);
				// get start of week
				$wstart = $calendar_item["timestamp_start"] - ($dow1 * $oneday);

				// check if this appointment falls in this week and its a workday
				if (floor(($ts - $wstart)/604800)%$calendar_item["repeat_freq"] || (floor(($ts - $calendar_item["timestamp_start"])/$oneday%$calendar_item["repeat_freq"]))) {
					return false;
				}
				// check if we are looking at a day that this item should occur
				return (strcmp($is_day, "y") == 0);
				break;
			default :
				// unknown repeat type. Should never be reached
				return false;
				break;
			}
		}
		/* }}} */
		/* deleteallrep {{{ */
		/**
		 * Delete all occurences of a repeating item for a user
		 *
		 * @param int id Calendar id
		 * @param int user_id The user to remove the items from
		 */
		public function deleteallrep($id, $user_id) {
			/* send notification mail to other users */
			if ($_REQUEST["notifyuser"] == "send") {
				$this->sendDeleteNotificationMail($id);
			}
			// remove the item from google if needed
			$_user_data = new User_data();
			$google_data = new Google_data();
			if ($_user_info["google_username"] && $_user_info["google_password"]) {
				$gClient = $google_data->getGoogleClientLogin($_user_info["google_username"], $_user_info["google_password"], "calendar");
			}
			
			$sql = sprintf("SELECT user_id, google_id FROM calendar_user WHERE user_id = %d AND calendar_id = %d", $user_id, $id);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				if ($row["google_id"]) {
					$_user_info = $_user_data->getUserDetailsById($row["user_id"]);
					if ($_user_info["google_username"] && $_user_info["google_password"]) {
						$google_data->deleteGoogleCalendarItemClient($gClient, $row["google_id"]);
					}
				}
			}
			$sql = sprintf("DELETE FROM calendar_user WHERE calendar_id = %d AND user_id = %d", $id, $user_id);
			sql_query($sql);
			// find out if other users link to this appointment, if not remove the item from the calendar table and calendar_repeats table
			// and also from the calendar_exceptions table
			$sql = sprintf("SELECT COUNT(*) FROM calendar_user WHERE calendar_id = %d", $id);
			$res = sql_query($sql);
			$count = sql_result($res, 0);
			if (!$count) {
				$sql = sprintf("DELETE FROM calendar WHERE id = %d", $id);
				sql_query($sql);
				$sql = sprintf("DELETE FROM calendar_repeats WHERE calendar_id = %d", $id);
				sql_query($sql);
				$sql = sprintf("DELETE FROM calendar_exceptions WHERE calendar_id = %d", $id);
				sql_query($sql);
			}
		}
		/* }}} */
		/* deleteonrep {{{ */
		/**
		 * Delete one occurrence of a repeating item
		 *
		 * @param int $id The calendar item in question
		 * @param int $user_id The user to make this exception for
		 * @param int $timestamp The timestamp for the exception
		 */
		public function deleteonerep($id, $user_id, $timestamp) {
			/* send notification mail to other users */
			if ($_REQUEST["notifyuser"] == "send") {
				$this->sendDeleteNotificationMail($id, $timestamp);
			}
			$sql = sprintf("INSERT INTO calendar_exceptions VALUES (%d, %d, %d)", $id, $user_id, $timestamp);
			$res = sql_query($sql);
		}
		/* }}} */
		/* migrate_calendar01 {{{ */
		/**
		 * Migrate the calendar from single table to multiple table so the repeat stuff gets done
		 * This should be run only once
		 */
		public function migrate_calendar01($no_output = 0) {
			//dont rely on magic_quotes_sybase
			$magic_sybase = ini_set("magic_quotes_sybase", false);
			//run forever if needed
			set_time_limit(60*60*60*24);
			// first, move the old calendar table to calendar_old
			$sql = "RENAME TABLE calendar TO calendar_old";
			$res = sql_query($sql);
			// now create the new tables
			$sql = "CREATE TABLE IF NOT EXISTS `calendar` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT 'unique id for an appointment.',
  `timestamp_start` int(11) unsigned NOT NULL COMMENT 'UNIX timestamp of start date and time for the appointment',
  `timestamp_end` int(11) unsigned NOT NULL COMMENT 'UNIX timestamp of end date and time for the appointment',
  `alldayevent` tinyint(2) NOT NULL COMMENT '1 if this is an all-day event',
  `subject` varchar(255) NOT NULL COMMENT 'Short description for the appointment',
  `body` text NOT NULL COMMENT 'Extended description for the appointment. Be sure to remove HTML tags when sending this to funambol.',
  `location` varchar(255) NOT NULL COMMENT 'The location for the appointment',
  `kilometers` int(11) unsigned default NULL COMMENT 'Distance to the location of the appointment',
  `reminderset` tinyint(2) NOT NULL COMMENT '1 if a reminder should be sent to the user',
  `reminderminutesbeforestart` int(11) unsigned NOT NULL COMMENT 'Number of minutes before the start of the appointment a reminder should be sent',
  `busystatus` tinyint(4) NOT NULL COMMENT '0 for free, 1 for tentative, 2 for busy, 3 for outofoffice',
  `importance` tinyint(4) NOT NULL COMMENT '0 for low, 1 for normal, 2 for high',
  `address_id` int(11) unsigned NOT NULL COMMENT 'main contact id from address table for this appointment',
  `multirel` varchar(255) NOT NULL COMMENT 'pipe seperated list of additional contacts for this appointment',
  `project_id` int(11) unsigned default NULL COMMENT 'main project id from project table for this appointment',
  `private_id` int(11) NOT NULL default '0',
  `is_private` tinyint(2) NOT NULL COMMENT '1 if this is a private appointment that other users are not allowed to view/alter',
  `isrecurring` tinyint(2) NOT NULL default '0' COMMENT 'true if the appointment is a recurring appointment',
  `modified_by` int(11) unsigned NOT NULL COMMENT 'user id of the user that last modified this appointment',
  `modified` int(11) unsigned NOT NULL COMMENT 'UNIX timestamp when this appointment was last modified',
  `is_ill` tinyint(2) NOT NULL,
  `is_specialleave` tinyint(2) NOT NULL,
  `is_holiday` tinyint(2) NOT NULL,
  `is_dnd` tinyint(2) NOT NULL COMMENT 'With the voip module this will mean the phone wont ring',
  `multiprivate` varchar(255) NOT NULL,
  `deckm` smallint(3),
  `note_id` int(11),
  `external_id` INT( 11 ),
  PRIMARY KEY  (`id`),
  KEY `address_id` (`address_id`)
);";
			$res = sql_query($sql);
			$sql = "CREATE TABLE IF NOT EXISTS `calendar_exceptions` (
  `calendar_id` int(11) unsigned NOT NULL COMMENT 'id from calendar table',
  `user_id` int(11) unsigned NOT NULL COMMENT 'id from userstable',
  `timestamp_exception` int(11) unsigned NOT NULL COMMENT 'UNIX TIMESTAMP of exception date'
);";
			$res = sql_query($sql);
			$sql = "CREATE TABLE IF NOT EXISTS `calendar_repeats` (
  `calendar_id` int(11) unsigned NOT NULL,
  `repeat_type` int(3) unsigned default NULL,
  `timestamp_end` int(11) default NULL,
  `repeat_frequency` int(11) unsigned default NULL,
  `repeat_days` char(7) default NULL
);";
			$res = sql_query($sql);
			$sql = "CREATE TABLE IF NOT EXISTS `calendar_user` (
  `calendar_id` int(11) unsigned NOT NULL COMMENT 'appointment id',
  `user_id` int(11) unsigned NOT NULL COMMENT 'user id as found in the users table',
  `status` int(11) NOT NULL COMMENT 'status of evenvt for this user: 1 for accepted, 2 for rejected, 3 for waiting',
  KEY `user_id` (`user_id`)
);";
			$res = sql_query($sql);
			//$sql = "ALTER TABLE `calendar_user` ADD INDEX ( `calendar_id` );";
			//$res = sql_query($sql);
			// now we have to convert everything
			$sql_loop = "SELECT * FROM calendar_old";
			$res_loop = sql_query($sql_loop);
			while ($row = sql_fetch_assoc($res_loop)) {
				$q_cal = sprintf("INSERT INTO calendar (id, timestamp_start, timestamp_end, alldayevent, subject, body, location, kilometers, address_id,
					multirel, project_id, private_id, is_private, modified_by, modified, is_ill, is_specialleave, is_holiday, is_dnd, multiprivate) VALUES (
					%d, %d, %d, %d, '%s', '%s', '%s', %d, %d, '%s', %d, %d, %d, %d, %d, %d, %d, %d, %d, '%s')",
					$row["id"], $row["timestamp_start"], $row["timestamp_end"], $row["is_event"], addslashes($row["subject"]), addslashes($row["description"]), addslashes($row["location"]),
					$row["kilometers"], $row["address_id"], $row["multirel"], $row["project_id"], $row["private_id"], $row["is_private"], $row["modified_by"],
					$row["modified"], $row["is_ill"], $row["is_specialleave"], $row["is_holiday"], $row["is_dnd"], $row["multiprivate"]);
				$res = sql_query($q_cal);
				if ($row["is_registered"]) {
					$status = 4;
				} else {
					$status = 1;
				}
				$q_user = sprintf("INSERT INTO calendar_user (calendar_id, user_id, status) VALUES (%d, %d, %d)", $row["id"], $row["user_id"], $status);
				$res = sql_query($q_user);
				// if more then one user is involved
				/* XXX: removed because covide 8.1 made a new appointment in all the calendars
				$extra = explode(",", $row["extra_users"]);
				if (is_array($extra)) {
					foreach ($extra as $userid) {
						if ($userid) {
							$q_user = sprintf("INSERT INTO calendar_user (calendar_id, user_id, status) VALUES (%d, %d, %d)", $row["id"], $userid, 1);
							$res = sql_query($q_user);
						}
					}
				}
				 */
				//repeating items
				if ($row["repeat_type"]) {
					switch ($row["repeat_type"]) {
					case "D":
						if ($row["is_repeat"] == 1) {
							//daily
							$sql = sprintf("INSERT INTO calendar_repeats (calendar_id, repeat_type, repeat_frequency) VALUES (%d, 1, 1)", $row["id"]);
						} elseif ($row["is_repeat"] == 7) {
							//weekly
							$dow = date("w", $row["timestamp_start"]);
							$days = array();
							for ($i = 0; $i <= 6; $i++) {
								if ($dow == $i) {
									$days[$i] = "y";
								} else {
									$days[$i] = "n";
								}
							}
							$repeat_days = implode("", $days);
							$sql = sprintf("INSERT INTO calendar_repeats (calendar_id, repeat_type, repeat_frequency, repeat_days) VALUES (%d, 2, 1, '%s')", $row["id"], $repeat_days);
						}
						break;
					case "M": //monthly
						$sql = sprintf("INSERT INTO calendar_repeats (calendar_id, repeat_type, repeat_frequency) VALUES (%d, 5, 1)", $row["id"]);
						break;
					case "Y": //yearly
						$sql = sprintf("INSERT INTO calendar_repeats (calendar_id, repeat_type, repeat_frequency) VALUES (%d, 6, 1)", $row["id"]);
						break;
					}
					$res = sql_query($sql);
					$q = sprintf("UPDATE calendar SET isrecurring=1 WHERE id=%d", $row["id"]);
					$res = sql_query($q);
				}
			}
			ini_set("magic_quotes_sybase", $magic_sybase);
			if ($no_output) {
				return true;
			} else {
				echo "done";
			}
		}
		/* }}} */
		/* getRegistrationItemById {{{ */
		/**
		 * Get a specific hourregistration item
		 *
		 * @param int $id The database id of the item to grab
		 *
		 * @return array The item information
		 */
		public function getRegistrationItemById($id) {
			$sql = sprintf("SELECT * FROM hours_registration WHERE id = %d", $id);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			return $row;
		}
		/* }}} */
		public function externalCalendarSync( $userid ) {
			echo "Userid: ".$userid;
			require(self::include_dir."externalcalendars.php");
			$sql = sprintf("SELECT id FROM calendar_external WHERE userid = %d", $userid);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				echo "Syncing: ". $row["id"];
				$external = new externalCalendar( $row["id"] );
				$external->setUser( $userid );
				$external->sync();
			}
		}
		/* reg_delete_xml {{{ */
		/**
		 * Delete a registered item and return javascript call
		 * This function should only be used on ajax situations
		 *
		 * @param int $id The database id of the item to remove
		 */
		public function reg_delete_xml($id) {
			if ($id != sprintf("%d", $id)) {
				return false;
			}
			$sql = sprintf("DELETE FROM hours_registration WHERE id = %d", $id);
			$res = sql_query($sql);
			echo "hours_refresh_page();";
		}
		/* }}} */
	}
?>
