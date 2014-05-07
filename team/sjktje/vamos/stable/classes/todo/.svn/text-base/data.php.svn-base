<?php
Class Todo_data {

	public function getTodoById($todo_id) {
		$sql = sprintf("SELECT * FROM todo WHERE id=%d", $todo_id);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$row["desktop_time"] = date("d/m/y", $row["timestamp"])."-".date("d/m/y", $row["timestamp_end"]);
		$row["humanstart"] = date("d-m-Y", $row["timestamp"]);
		$row["humanend"]   = date("d-m-Y", $row["timestamp_end"]);
		if ($row["is_customercontact"]) { $row["is_no_custcont"] = 0; } else { $row["is_no_custcont"] = 1;}
		return $row;
	}

	public function getTodosByUserId($user_id, $done=0) {
		$address_data = new Address_data();
		$sql = sprintf("SELECT * FROM todo WHERE is_done=%d AND user_id=%d ORDER BY timestamp ASC", $done, $user_id);
		$res = sql_query($sql);
		$todolist = array();
		while ($row = sql_fetch_assoc($res)) {
			$row["desktop_time"] = date("d/m/y", $row["timestamp"])."-".date("d/m/y", $row["timestamp_end"]);
			$row["humanstart"] = date("d-m-Y", $row["timestamp"]);
			$row["humanend"]   = date("d-m-Y", $row["timestamp_end"]);
			$row["relname"]    = $address_data->getAddressNameByID($row["address_id"]);
			if ($row["is_customercontact"]) { $row["is_no_custcont"] = 0; } else { $row["is_no_custcont"] = 1;}
			$todolist[] = $row;
		}
		return $todolist;
	}

	public function getTodosByAddressId($address_id, $done=0) {
		$address_data = new Address_data();
		$user_data = new User_data();
		$sql = sprintf("SELECT * FROM todo WHERE is_done=%d AND address_id=%d ORDER BY timestamp ASC", $done, $address_id);
		$res = sql_query($sql);
		$todolist = array();
		while ($row = sql_fetch_assoc($res)) {
			$row["desktop_time"] = date("d/m/y", $row["timestamp"])."-".date("d/m/y", $row["timestamp_end"]);
			$row["humanstart"] = date("d-m-Y", $row["timestamp"]);
			$row["humanend"]   = date("d-m-Y", $row["timestamp_end"]);
			$row["relname"]    = $address_data->getAddressNameByID($row["address_id"]);
			$row["user_name"]  = $user_data->getUsernameById($row["user_id"]);
			if ($row["is_customercontact"]) { $row["is_no_custcont"] = 0; } else { $row["is_no_custcont"] = 1;}
			$todolist[] = $row;
		}
		return $todolist;
	}

	public function delete_todo() {
		if ($_REQUEST["todoid"]) {
			if ($GLOBALS["covide"]->license["has_sync4j"]) {
				//Sync4j code
				$q = sprintf("SELECT sync4j_source_todo FROM users WHERE id = %d", $_SESSION["user_id"]);
				$resx2 = sql_query($q);
				$rowx2 = sql_fetch_assoc($resx2);
				if ($rowx2["sync4j_source_todo"]) {
					//get todo
					$q = sprintf("SELECT id, user_id, sync_guid FROM todo WHERE id = %d", $_REQUEST["todoid"]);
					$resx = sql_query($q);
					$rowx = sql_fetch_array($resx);
					if ($rowx["sync_guid"] && $rowx["user_id"] == $_SESSION["user_id"]) {
						$q = sprintf("INSERT INTO todo_sync (user_id, sync_guid, action) VALUES (%d, %d, 'D')", $_SESSION["user_id"], $rowx["sync_guid"]);
						sql_query($q);
					}
				}
			}
			//end sync4j code
			$sql = sprintf("DELETE FROM todo WHERE id=%d AND user_id=%d", $_REQUEST["todoid"], $_SESSION["user_id"]);
			$res = sql_query($sql);
		}
		header("Location: index.php?mod=todo");
	}

	public function delete_multi_todo($postdata) {
		$todos = $postdata["checkbox_todo"];
		/* sanitize input */
		$ok = 0;
		$ids = array(0 => 0);
		foreach ($todos as $k=>$v) {
			if ((int)$v) {
				$ok = 1;
			}
			$ids[$k] = (int)$k;
		}
		if (!$ok) {
			die("FOUT!");
		}
		$todo_ids = implode(",", $ids);
		unset($todos);
		$sql = sprintf("DELETE FROM todo WHERE id IN (%s)", $todo_ids);
		$res = sql_query($sql);
	}

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

	public function save_todo() {
		$todoinfo = $_REQUEST["todo"];
		$fields = array();
		$values = array();
		/* make arrays with info so we can store it in the database */
		$fields[] = "timestamp";          $values[] = sprintf("%d",   mktime(0, 0, 0, $todoinfo["timestamp_month"], $todoinfo["timestamp_day"], $todoinfo["timestamp_year"]));
		$fields[] = "timestamp_end";      $values[] = sprintf("%d",   mktime(0, 0, 0, $todoinfo["timestamp_end_month"], $todoinfo["timestamp_end_day"], $todoinfo["timestamp_end_year"]));
		$fields[] = "user_id";            $values[] = sprintf("%d",   $_SESSION["user_id"]);
		$fields[] = "subject";            $values[] = sprintf("'%s'", $todoinfo["subject"]);
		$fields[] = "body";               $values[] = sprintf("'%s'", $todoinfo["body"]);
		$fields[] = "address_id";         $values[] = sprintf("%d",   $todoinfo["address_id"]);
		$fields[] = "project_id";         $values[] = sprintf("%d",   $todoinfo["project_id"]);
		$fields[] = "is_alert";           $values[] = sprintf("%d",   $todoinfo["is_alert"]);
		$fields[] = "is_customercontact"; $values[] = sprintf("%d",   $todoinfo["is_customercontact"]);
		if ($todoinfo["id"]) {
			/* update a todo */
			$sql  = "UPDATE todo SET ";
			foreach ($fields as $k=>$v) {
				$sql .= $v."=".$values[$k].",";
			}
			$sql  = substr($sql, 0, strlen($sql)-1);
			$sql .= sprintf(" WHERE id=%d", $todoinfo["id"]);
			/* if we have sync4j, we need to update some tables */
			if ($GLOBALS["covide"]->license["has_sync4j"]) {
				$q = sprintf("select sync4j_source_todo from users where id = %d", $_SESSION["user_id"]);
				$res = sql_query($q);
				$row = sql_fetch_assoc($res);
				if ($row["sync4j_source_todo"]) {
					//get todo
					$q = sprintf("select id, user_id, sync_guid from todo where id = %d", $todoinfo["id"]);
					$resx = sql_query($q);
					$rowx = sql_fetch_array($resx);
					if ($rowx["sync_guid"]) {
						$q = sprintf("insert into todo_sync (user_id, sync_guid, action) values (%d, %d, 'U')", $_SESSION["user_id"], $rowx["sync_guid"]);
						sql_query($q);
					}
				}
				unset($res);
				unset($q);
				unset($row);
			}
		} else {
			/* insert a todo */
			$sql  = "INSERT INTO todo (";
			$sql .= implode(",", $fields);
			$sql .= ") VALUES (";
			$sql .= implode(",", $values);
			$sql .= ");";
		}
		$res = sql_query($sql);
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
							opener.location.href = opener.location.href;
							window.close();
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
			/* jump back to todo list */
			header("Location: index.php?mod=todo");
		}
	}

	public function xml_check($data) {
		$timestamp_start = mktime(0, 0, 0, $data["timestamp_month"], $data["timestamp_day"], $data["timestamp_year"]);
		$timestamp_end   = mktime(0, 0, 0, $data["timestamp_end_month"], $data["timestamp_end_day"], $data["timestamp_end_year"]);
		if ($timestamp_end < $timestamp_start) {
			echo "update_check(1);";
		} else {
			echo "update_check(0);";
		}
	}
}
?>
