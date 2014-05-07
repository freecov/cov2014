<?php
/**
 * Covide Groupware-CRM Todo module dataobject
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 *
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Todo_data {

	/* methods */

	/* getTodoById {{{ */
	/**
	 * Get all the info about a specific todo
	 *
	 * @param int $todo_id The todo to fetch
	 * @return array the todo information
	 */
	public function getTodoById($todo_id) {
		$sql = sprintf("SELECT * FROM todo WHERE id=%d", $todo_id);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$row["timestamp"] = (int)$row["timestamp"];
		$row["timestamp_end"] = (int)$row["timestamp_end"];
		$address = new Address_data();
		$row["relation_name"] = $address->getAddressNameById($row["address_id"]);
		unset($address);
		if ($GLOBALS["covide"]->license["has_project"] || $GLOBALS["covide"]->license["has_project_declaration"]) {
			$project = new Project_data();
			$projectinfo          = $project->getProjectById($row["project_id"]);
			unset($project);
			$row["project_name"]  = $projectinfo[0]["name"];
		}

		$row["desktop_time"] = date("d/m/Y H:i", $row["timestamp"])."-".date("d/m/Y H:i", $row["timestamp_end"]);
		$row["humanstart"] = date("d-m-Y H:i", $row["timestamp"]);
		$row["humanend"]   = date("d-m-Y H:i", $row["timestamp_end"]);
		if ($row["status"]) {
			$row["is_active"]  = 0;
			$row["is_passive"] = 1;
		} else {
			$row["is_active"]  = 1;
			$row["is_passive"] = 0;
		}
		if (!$row["priority"])
			$row["priority"] = 5;
		if ($row["is_customercontact"])
			$row["is_no_custcont"] = 0;
		else
			$row["is_no_custcont"] = 1;

		return $row;
	}
	/* }}} */
	/* getTodosByUserId {{{ */
	/**
	 * Get all the todos belonging to a user with optional filters on done and today
	 *
	 * @param int $user_id The user we want to have the todos from
	 * @param int $done if set only show completed todos
	 * @param int $today if set only show todos with startdate before or at today
	 * @param array $options couple of options
	 * @return array The todo information
	 */
	public function getTodosByUserId($user_id, $done=0, $today=0, $options = array()) {
		$address_data = new Address_data();
		$user_data = new User_data();
		$access = $user_data->getUserDetailsById($_SESSION["user_id"]);
		$users = $user_data->getUserlist();
		unset($user_data);
		if ($today != 0) {
			$dateMonth = date("m", time());
			$dateDay   = date("d", time());
			$dateYear  = date("Y", time());
			$todaystart = mktime(0, 0, 0, $dateMonth, $dateDay, $dateYear);
			$todayend = mktime(0, 0, 0, $dateMonth, $dateDay+1, $dateYear);
			$dateToday = sprintf("AND (timestamp <= %2\$d OR timestamp_end >= %1\$d)", $todaystart, $todayend);
		} else {
			$dateToday = "";
		}
		if ($options["search"]) {
			$like = sql_syntax("like");
			$search = sprintf(" AND (subject $like '%%%1\$s%%' OR body $like '%%%1\$s%%')", $options["search"]);
		} else {
			$search = "";
		}
		if ($done == 1) {
			if ($access["xs_todomanage"]) {
				$sql = sprintf("SELECT * FROM todo WHERE is_done=%d %s %s ORDER BY timestamp DESC, timestamp_end DESC", $done, $search, $dateToday);
				$sql_count = sprintf("SELECT COUNT(*) FROM todo WHERE is_done=%d %s %s", $done, $search, $dateToday);
			} else {
				$sql = sprintf("SELECT * FROM todo WHERE is_done=%d AND user_add_id=%d OR user_id=%d %s %s ORDER BY timestamp DESC, timestamp_end DESC", $done, $user_id , $user_id, $search, $dateToday);
				$sql_count = sprintf("SELECT COUNT(*) FROM todo WHERE is_done=%d OR user_add_id=%d AND user_id=%d %s %s", $done, $user_id, $user_id, $search, $dateToday);
			}
		} else {
			$sql = sprintf("SELECT * FROM todo WHERE is_done=%d AND (user_add_id=%d OR user_id=%d) %s %s ORDER BY status ASC, priority ASC, timestamp_end ASC, timestamp ASC", $done, $user_id, $user_id, $search, $dateToday);
			$sql_count = sprintf("SELECT COUNT(*) FROM todo WHERE is_done=%d AND (user_add_id=%d OR user_id=%d) %s %s", $done, $user_id, $user_id, $search, $dateToday);
		}
		if (!$options["nolimit"]) {
			$result = sql_query($sql, "", $options["top"], $GLOBALS["covide"]->pagesize);
		} else {
			if ($options["max_hits"]) {
				$result = sql_query($sql, "", 0, $options["max_hits"]);
			} else {
				$result = sql_query($sql);
			}
		}
		$todolist = array();
		//grab total count
		if ($options["return_count"]) {
			$r_count = sql_query($sql_count);
			$todolist["total_count"] = sql_result($r_count, 0);
		}
		while ($row = sql_fetch_assoc($result)) {
			$row["desktop_time"] = date("d/m/y H:i", $row["timestamp"])."-".date("d/m/y H:i", $row["timestamp_end"]);
			$row["humanstart"] = date("d-m-Y H:i", $row["timestamp"]);
			$row["humanend"]   = date("d-m-Y H:i", $row["timestamp_end"]);
			$row["relname"]    = $address_data->getAddressNameByID($row["address_id"]);
			if ($GLOBALS["covide"]->license["has_project"] || $GLOBALS["covide"]->license["has_project_declaration"]) {
				if ($row["project_id"]) {
					if (!$project)
						$project = new Project_data();
					$projectinfo = $project->getProjectById($row["project_id"]);
					$row["project_name"]  = $projectinfo[0]["name"];
				}
			}
			if ($row["status"]) {
				$row["is_active"]  = 0;
				$row["is_passive"] = 1;
			} else {
				$row["is_active"]  = 1;
				$row["is_passive"] = 0;
			}
			if (!$row["priority"])
				$row["priority"] = 5;
			if ($row["is_customercontact"]) { $row["is_no_custcont"] = 0; } else { $row["is_no_custcont"] = 1;}
			if ($row["timestamp_end"] < time() && $row["is_done"] != 1)
				$row["overdue"] = 1;
			else
				$row["overdue"] = 0;
			if ($row["is_done"] == 1) {
				$row["is_current"] = 0;
			} else {
				$row["is_current"] = 1;
			}
			$row["username"] = $users[$row["user_id"]];
			$todolist[] = $row;
		}
		return $todolist;
	}
	/* }}} */
	/* getTodosByAddressId {{{ */
	/**
	 * Get todos linked to a relation
	 *
	 * @param int $address_id The relation to lookup
	 * @param int $done if set only get completed todos
	 * @return array the todo information
	 */
	public function getTodosByAddressId($address_id, $done=0) {
		$address_data = new Address_data();
		$user_data = new User_data();
		$sql = sprintf("SELECT * FROM todo WHERE is_done=%d AND address_id=%d ORDER BY timestamp ASC, priority ASC", $done, $address_id);
		$res = sql_query($sql);
		$todolist = array();
		while ($row = sql_fetch_assoc($res)) {
			$row["desktop_time"] = date("d/m/y H:i", $row["timestamp"])."-".date("d/m/y H:i", $row["timestamp_end"]);
			$row["humanstart"] = date("d-m-Y H:i", $row["timestamp"]);
			$row["humanend"]   = date("d-m-Y H:i", $row["timestamp_end"]);
			$row["relname"]    = $address_data->getAddressNameByID($row["address_id"]);
			if ($GLOBALS["covide"]->license["has_project"] || $GLOBALS["covide"]->license["has_project_declaration"]) {
				$project = new Project_data();
				$projectinfo          = $project->getProjectById($row["project_id"]);
				unset($project);
				$row["project_name"]  = $projectinfo[0]["name"];
			}
			$row["user_name"]  = $user_data->getUsernameById($row["user_id"]);
			if ($row["status"]) {
				$row["is_active"]  = 0;
				$row["is_passive"] = 1;
			} else {
				$row["is_active"]  = 1;
				$row["is_passive"] = 0;
			}
			if (!$row["priority"])
				$row["priority"] = 5;
			if ($row["is_customercontact"]) { $row["is_no_custcont"] = 0; } else { $row["is_no_custcont"] = 1;}
			if ($row["is_done"] == 1) {
				$row["is_current"] = 0;
			} else {
				$row["is_current"] = 1;
			}
			$todolist[] = $row;
		}
		return $todolist;
	}
	/* }}} */
	/* delete_todo {{{ */
	/**
	 * Delete a todo from the database
	 *
	 * @param int $todo_id the id of the todo to remove
	 * @param int $no_redir if set dont sent Location header
	 */
	public function delete_todo($todo_id, $no_redir=0) {
		if (!$todo_id)
			$todo_id = $_REQUEST["todoid"];

		if ($todo_id) {
			$sql = sprintf("UPDATE todo SET is_done = 1 WHERE id=%d AND user_id=%d", $todo_id, $_SESSION["user_id"]);
			$res = sql_query($sql);

			if ($GLOBALS["covide"]->license["has_funambol"]) {
				$funambol_data = new Funambol_data();
				$funambol_data->removeRecord("todo", $todo_id);
			}
		}
		if (!$no_redir)
			header("Location: index.php?mod=todo");
	}
	/* }}} */
	/* delete_multi_todo {{{ */
	/**
	 * Delete multiple todos in one run
	 *
	 * @param arary $postdata array with id's of todos to remove
	 */
	public function delete_multi_todo($postdata) {
		$todos = $postdata["checkbox_todo"];
		/* sanitize input */
		$ok = 0;
		$ids = array(0 => 0);
		foreach ($todos as $k=>$v) {
			//this looks weird but the todos array looks like:
			// array(
			//     todoid => 1/0
			// )
			// the 0 will never be here, but we check it anyways just to be sure.
			// so now you see why we use the key instead of the value of the array.
			if ((int)$v == 1) {
				$ok = 1;
				$ids[$k] = (int)$k;
			}
		}
		if (!$ok)
			die("FOUT!");

		foreach ($ids as $t=>$v) {
			if ($t)
				$this->delete_todo($t, 1);
		}
	}
	/* }}} */
	/* save_multi {{{ */
	/**
	 * Save multiple todos, only start and end dates
	 *
	 * The $postdata parameter should have this format:
	 * array(
	 *     [todo] => array(
	 *         [ids]         => (string)commaseperated list of affected ids,
	 *         [start_day]   => (int)new start day,
	 *         [start_month] => (int)new start month,
	 *         [start_year]  => (int)new start year 4 digit notation,
	 *         [end_day]     => (int)new end day,
	 *         [end_month]   => (int)new end month,
	 *         [end_year]    => (int)new end year 4 digit notation
	 *     )
	 * )
	 *
	 * @param array $postdata the id's and start and end information (see description for format)
	 */
	public function save_multi($postdata) {
		$todo_info = $postdata["todo"];
		$todos = explode(",", $todo_info["ids"]);
		/* sanitize input */
		$ok = 0;
		foreach ($todos as $k=>$v) {
			if ((int)$v) {
				$ok = 1;
			}
			$todos[$k] = (int)$v;
		}
		if (!$ok) {
			die("FOUT!");
		}
		$todo_ids = implode(",", $todos);
		unset($todos);
		/* generate date stamps */
		$start = mktime(0, 0, 0, $todo_info["start_month"], $todo_info["start_day"], $todo_info["start_year"]);
		$end   = mktime(0, 0, 0, $todo_info["end_month"],   $todo_info["end_day"],   $todo_info["end_year"]);
		$sql = sprintf("UPDATE todo SET timestamp = %d, timestamp_end = %d WHERE id IN (%s)", $start, $end, $todo_ids);
		$res = sql_query($sql);
	}
	/* }}} */
	/* save_todo {{{ */
	/**
	 * Store a todo in the database
	 *
	 * @param array $todoinfo The information to store in the database
	 * @param int $skip_funambol if set skip all the routines needed for sync with mobile devices
	 * @return array the new or modified ids
	 */
	public function save_todo($todoinfo="", $skip_funambol=0) {
		$modified_ids = array();

		if (!$todoinfo)
			$todoinfo = $_REQUEST["todo"];

		$fields = array();
		$values = array();

		if ($todoinfo["user_id"])
			$_user_id = $todoinfo["user_id"];
		else
			$_user_id = $_SESSION["user_id"];

		/* make arrays with info so we can store it in the database */
		$fields[] = "timestamp";          $values[] = sprintf("%d",   mktime($todoinfo["timestamp_hours"], $todoinfo["timestamp_minutes"], 0, $todoinfo["timestamp_month"], $todoinfo["timestamp_day"], $todoinfo["timestamp_year"]));
		$fields[] = "timestamp_end";      $values[] = sprintf("%d",   mktime($todoinfo["timestamp_end_hours"], $todoinfo["timestamp_end_minutes"], 0, $todoinfo["timestamp_end_month"], $todoinfo["timestamp_end_day"], $todoinfo["timestamp_end_year"]));
		$fields[] = "user_id";            $values[] = sprintf("%d",   $_user_id);
		$fields[] = "subject";            $values[] = sprintf("'%s'", $todoinfo["subject"]);
		$fields[] = "body";               $values[] = sprintf("'%s'", $todoinfo["body"]);
		$fields[] = "address_id";         $values[] = sprintf("%d",   $todoinfo["address_id"]);
		$fields[] = "project_id";         $values[] = sprintf("%d",   $todoinfo["project_id"]);
		$fields[] = "is_alert";           $values[] = sprintf("%d",   $todoinfo["is_alert"]);
		$fields[] = "is_customercontact"; $values[] = sprintf("%d",   $todoinfo["is_customercontact"]);
		$fields[] = "status";             $values[] = sprintf("%d",   $todoinfo["status"]);
		$fields[] = "priority";           $values[] = sprintf("%d",   $todoinfo["priority"]);
		if ($_user_id == $_SESSION["user_id"]) {
			$fields[] = "user_add_id";            $values[] = sprintf("%d",   '0');
		} else {
			$fields[] = "user_add_id";            $values[] = sprintf("%d",   $_SESSION["user_id"]);
		}
		if ($todoinfo["id"]) {
			/* grab old user_id */
			$q = sprintf("SELECT user_id FROM todo WHERE id = %d", $todoinfo["id"]);
			$r = sql_query($q);
			$old_userid = sql_result($r, 0);
			if ($old_userid != $_user_id) {
				if ($GLOBALS["covide"]->license["has_funambol"] && !$skip_funambol) {
					$funambol_data = new Funambol_data($old_userid);
					$funambol_data->removeRecord("todo", $todoinfo["id"]);
					unset($funambol_data);
				}
			}
			/* update a todo */
			$sql  = "UPDATE todo SET ";
			foreach ($fields as $k=>$v) {
				$sql .= $v."=".$values[$k].",";
			}
			$sql  = substr($sql, 0, strlen($sql)-1);
			$sql .= sprintf(" WHERE id=%d", $todoinfo["id"]);
		} else {
			/* insert a todo */
			$sql  = "INSERT INTO todo (";
			$sql .= implode(",", $fields);
			$sql .= ") VALUES (";
			$sql .= implode(",", $values);
			$sql .= ");";
		}
		$res = sql_query($sql);

		if ($todoinfo["id"])
			$modified_ids[0] = $todoinfo["id"];
		else
			$modified_ids[0] = sql_insert_id("todo");


		if ($GLOBALS["covide"]->license["has_funambol"] && !$skip_funambol) {
			$funambol_data = new Funambol_data($_user_id);
			$funambol_data->syncRecord("todo", "", $modified_ids[0]);
		}

		if (!$skip_funambol) {
			if ($todoinfo["noteid"]) {
				/* we are putting a note in the todo list */
				/* flag note as read */
				$sql = sprintf("UPDATE notes SET is_done=1, is_read=1 WHERE id=%d", $todoinfo["noteid"]);
				$res = sql_query($sql);
				/* jump back to notes screen */
				if ($todoinfo["noiface"] == 1) {
					/* we are in a popup, close this one */
					$output = new Layout_output();
					$output->start_javascript();
						if ($todoinfo["is_sales"]) {
							return true;
						} else {
							$output->addCode(
								"
								parent.location.href = parent.location.href;
								closepopup();
								"
							);
						}
					$output->end_javascript();
					$output->exit_buffer();
				} elseif ($todoinfo["noiface"] == 2) {
					/* we want a sales item */
					$sales_output = new Sales_output();
					$sales_output->salesEdit(array("note_id" => $todoinfo["noteid"], "noiface" => 1));
				} else {
					header("Location: index.php?mod=note");
				}
			} else {
				if ($todoinfo["noiface"] == 1) {
					$output = new Layout_output();
					$output->start_javascript();
					$output->addCode(
						"
						parent.location.href = parent.location.href;
						closepopup();
						"
					);
					$output->end_javascript();
					$output->exit_buffer();
				} else {
					/* jump back to todo list */
					header("Location: index.php?mod=todo");
				}
			}
		}
		return $modified_ids;
	}
	/* }}} */
	/* xml_check {{{ */
	/**
	 * Ajax check if dates are correct
	 *
	 * @param array $data array with start and end information
	 */
	public function xml_check($data) {
		$timestamp_start = mktime(0, 0, 0, $data["timestamp_month"], $data["timestamp_day"], $data["timestamp_year"]);
		$timestamp_end   = mktime(0, 0, 0, $data["timestamp_end_month"], $data["timestamp_end_day"], $data["timestamp_end_year"]);
		if ($timestamp_end < $timestamp_start) {
			echo "update_check(1);";
		} else {
			echo "update_check(0);";
		}
	}
	/* }}} */
	/* getTodosByProjectId {{{ */
	/**
	 * Get all the current todos for a project
	 *
	 * @param int $project_id The project to grab the todos for
	 *
	 * @return array Todo information
	 */
	public function getTodosByProjectId($project_id) {
		$ret = array();
		$user_data = new User_data();
		$sql = sprintf("SELECT * FROM todo WHERE project_id = %1\$d AND (timestamp >= %2\$d OR timestamp_end >= %2\$d)", $project_id, mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$row["human_date_from"] = date("d-m-Y H:i", $row["timestamp"]);
			$row["human_date_to"] = date("d-m-Y H:i", $row["timestamp_end"]);
			$row["username"] = $user_data->getUserNameById($row["user_id"]);
			$ret["todos"][] = $row;
		}
		return $ret;
	}
	/* }}} */
}
?>
