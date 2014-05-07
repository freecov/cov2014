<?php

		$str        = $options["phrase"];
		$max_hits   = $options["max_hits"];
		if ($options["address_id"])
			$address_id = explode(",", $options["address_id"]);
		else
			$address_id = array();

		/* prepare return variable */
		$data = array(
			"files"   => array(),
			"folders" => array()
		);

		/* cache */
		$folder_permissions = array();

		/* extract permissions for this user */
		$user_data = new User_data();
		$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);
		/* get syntax */
		$like = sql_syntax("like");

		/* search all folders */
		$q = sprintf("select * from filesys_folders where name %1\$s '%%%2\$s%%' or description %1\$s '%%%2\$s%%' order by name", $like, $str);
		if ($max_hits) {
			$res = sql_query($q, "", 0, $max_hits);
		} else {
			$res = sql_query($q);
		}
		while ($row = sql_fetch_assoc($res)) {
			// Count the subfolders
			$qs = sprintf("select count(*) as folder_count from filesys_folders where parent_id = %d", $row["id"]);
			$resSub = sql_query($qs);
			$folder_count = sql_result($resSub, 0);
			// Count the files
			$qf = sprintf("select count(*) as file_count from filesys_files where folder_id = %d", $row["id"]);
			$resFile = sql_query($qf);
			$file_count = sql_result($resFile, 0);

			$row = $this->extractPermissions($row, &$user_info, 1);
			if ($row["parent_id"] == 0 && $row["name"] == "mijn documenten") {
				if ($row["user_id"] == $_SESSION["user_id"]) {
					$data["folders"][$row["id"]] = $row;
					$data["folders"][$row["id"]]["foldercount"] = $folder_count;
					$data["folders"][$row["id"]]["filecount"] = $file_count;
					$folder_permissions[$row["id"]]["access"] = "R";
					$folder_permissions[$row["id"]]["name"]   = $row["name"];
				} else {
					$folder_permissions[$row["id"]]["access"] = "D";
					$folder_permissions[$row["id"]]["name"]   = $row["name"];
				}
			} elseif (in_array($row["xs"], array("R", "W", "S", "X"))) {
				$data["folders"][$row["id"]] = $row;
					$data["folders"][$row["id"]]["foldercount"] = $folder_count;
					$data["folders"][$row["id"]]["filecount"] = $file_count;
				$folder_permissions[$row["id"]]["access"] = "R";
				$folder_permissions[$row["id"]]["name"]   = $row["name"];
			} else {
				$folder_permissions[$row["id"]]["access"] = "D";
				$folder_permissions[$row["id"]]["name"]   = $row["name"];
			}

			if ($folder_permissions[$row["id"]])
				$folder_permissions[$row["id"]]["address_id"] = $this->checkForRelation($row["id"]);
		}

		$user_data = new User_data();
		$users = $user_data->getUserlist();

		/* search all files */
		$q = sprintf("select $buf * from filesys_files where name %1\$s '%%%2\$s%%' or description %1\$s '%%%2\$s%%' order by folder_id, name", $like, $str);
		if ($max_hits) {
			$res = sql_query($q, "", 0, $max_hits);
		} else {
			$res = sql_query($q);
		}
		while ($row = sql_fetch_assoc($res)) {
			/* check against cache */
			if (!$folder_permissions[$row["folder_id"]]) {
				$q2 = "select * from filesys_folders where id = ".$row["folder_id"];
				$res2 = sql_query($q2);
				$row2 = sql_fetch_assoc($res2);
				$row2 = $this->extractPermissions($row2, &$user_info, 1);
				/* add to cache */
				if ($row2["parent_id"] == 0 && $row2["name"] == "mijn documenten") {
					if ($row2["user_id"] == $_SESSION["user_id"]) {
						$folder_permissions[$row["folder_id"]]["access"] = "R";
						$folder_permissions[$row["folder_id"]]["name"]   = $row2["name"];
					} else {
						$folder_permissions[$row["folder_id"]]["access"] = "D";
						$folder_permissions[$row["folder_id"]]["name"]   = $row2["name"];
					}
				} elseif ($row2["xs"] == "R" || $row2["xs"] == "W" || $row2["xs"] == "S" || $row2["xs"] == "X") {
					$folder_permissions[$row["folder_id"]]["access"] = "R";
					$folder_permissions[$row["folder_id"]]["name"]   = $row2["name"];
				} else {
					$folder_permissions[$row["folder_id"]]["access"] = "D";
					$folder_permissions[$row["folder_id"]]["name"]   = $row2["name"];
				}
				if ($folder_permissions[$row["folder_id"]] && !$folder_permissions[$row["folder_id"]]["address_id"])
					$row["address_id"] = $this->checkForRelation($row["folder_id"]);
			}
			/* lookup in cache */
			if ($folder_permissions[$row["folder_id"]]["access"] == "R") {
				$row["size_human"] = $this->parseSize($row["size"]);
				if ($row["timestamp"]) {
					$row["date_human"] = date("d-m-Y H:i", $row["timestamp"]);
				} else {
					$row["date_human"] = "---";
				}
				$row["user_name"]   = $users[$row["user_id"]];
				$row["folder_name"] = $folder_permissions[$row["folder_id"]]["name"];

				$row["fileicon"] = $this->getFileType($row["name"]);
				if ($_REQUEST["subaction"] == "add_attachment") {
					$row["attachment"] = 1;
				}
				$data["files"][$row["id"]] = $row;
			}
		}
		if (count($address_id)) {
			/* filter folders */
			foreach ($data["folders"] as $k=>$v) {
				if (!in_array($v["address_id"], $address_id))
					unset($data["folders"][$k]);
			}
			/* filter files */
			foreach ($data["files"] as $k=>$v) {
				if (!in_array($v["address_id"], $address_id))
					unset($data["files"][$k]);
			}
		}
?>
