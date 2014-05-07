<?php
		if (!class_exists("Project_data")) {
			exit("no class definition found");
		}
		/* get the user data */
		$user_data = new User_data();
		$user_perm = $user_data->getUserdetailsById($_SESSION["user_id"]);

		if ($options["searchkey"]) {
			if ($options["searchkey"] == "*") {
				$options["searchkey"] = "%";
			}
			//first the easy part. get the main projects and stand alone projects
			$i = 0;
			$found_ids = Array();
			$like_syntax = sql_syntax("like");
			/* get all the master projects that match the search terms */
			$sql = sprintf("SELECT * FROM projects_master WHERE name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%' ORDER BY UPPER(name)", $options["searchkey"]);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$pr[$i]["id"]           = $row["id"];
				$pr[$i]["master"]       = 1;
				$pr[$i]["nonmaster"]    = 0;
				$pr[$i]["name"]         = $row["name"];
				$pr[$i]["description"]  = $row["description"];
				if ($user_perm["xs_projectmanage"] || $row["manager"] == $_SESSION["user_id"])
					$pr[$i]["allow_edit"] = 1;
				$found_ids[$i]          = $row["id"];
				$i++;
			}
			/* get the normal projects */
			$sql = sprintf("SELECT * FROM project WHERE (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%') AND group_id=0 ORDER BY UPPER(name)", $options["searchkey"]);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$pr[$i]["id"]           = $row["id"];
				$pr[$i]["master"]       = 0;
				$pr[$i]["nonmaster"]    = 1;
				$pr[$i]["name"]         = $row["name"];
				$pr[$i]["description"]  = $row["description"];
				if ($user_perm["xs_projectmanage"] || $row["manager"] == $_SESSION["user_id"]) {
					$pr[$i]["allow_edit"] = 1;
				}

				$i++;
			}
			//now the hard part. get the main projects with sub projects that match the search string
			$sql = sprintf("SELECT group_id FROM project WHERE group_id !=0 AND (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%') GROUP BY group_id", $options["searchkey"]);
			$res = sql_query($sql);
			$get_h = "0";
			while ($row = sql_fetch_assoc($res)) {
				$get_h .= ",".$row["group_id"];
			}
			$sql = "SELECT * FROM projects_master WHERE id IN (".$get_h.")";
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				if (!in_array($row["id"], $found_ids)) {
					$pr[$i]["id"]           = $row["id"];
					$pr[$i]["master"]       = 1;
					$pr[$i]["nonmaster"]    = 0;
					$pr[$i]["name"]         = $row["name"];
					$pr[$i]["description"]  = $row["description"];
					if ($user_perm["xs_projectmanage"] || $row["manager"] == $_SESSION["user_id"])
						$pr[$i]["allow_edit"] = 1;
					$found_ids[$i]          = $row["id"];
					$i++;
				}
			}

			for ($j = 0; $j < sizeof($pr); $j++) {
				$sort_values[$j] = strtolower($pr[$j]["name"]);
			}
			if (count($sort_values)) {
				asort ($sort_values);
				reset ($sort_values);
				while (list ($arr_key, $arr_val) = each ($sort_values)) {
					$sorted_arr[] = $pr[$arr_key];
				}
				if (!$user_perm["xs_projectmanage"]) {
					/* not global project manager, filter on manager */
					//subproject
					$proj_access["sub"] = array();
					$proj_access["master"] = array();
					$proj_access["parent"] = array();
					$q = "select id,group_id from project where manager = ".$_SESSION["user_id"];
					$resx = sql_query($q);
					while ($rowx = sql_fetch_array($resx)) {
						$proj_access["sub"][]    =$rowx["id"];
						$proj_access["parent"][] =$rowx["group_id"];
					}
					$proj_access["parent"] = array_unique($proj_access["parent"]);
					$q = "select id from projects_master where manager = ".$_SESSION["user_id"];
					$resx = sql_query($q);
					while ($rowx = sql_fetch_array($resx)) {
						$proj_access["master"][]=$rowx["id"];
					}
					foreach ($sorted_arr as $key=>$val) {
						if ($val["master"]) {
							if (!in_array($val["id"], $proj_access["master"])) {
								if (!in_array($val["id"], $proj_access["parent"])) {
									unset($sorted_arr[$key]);
								}
							}
						} else {
							if (!in_array($val["id"], $proj_access["sub"])) {
								unset($sorted_arr[$key]);
							}
						}
					}
				}
			}
		} elseif ($options["address_id"]) {
			$like = sql_syntax("like");
			$sql = sprintf("SELECT id,name,description,is_active FROM project WHERE address_id=%1\$d OR multirel $like '%%%1\$d%%' ORDER BY UPPER(name)", $options["address_id"]);
			$res = sql_query($sql);
			$sorted_arr = array();
			while ($row = sql_fetch_assoc($res)) {
				if ($row["is_active"]) {
					$row["is_nonactive"] = 0;
				} else {
					$row["is_nonactive"] = 1;
				}
				if ($user_perm["xs_projectmanage"] || $row["manager"] == $_SESSION["user_id"])
					$row["allow_edit"] = 1;
				$sorted_arr[] = $row;
			}
		} else {
			$hours = Array();
			$q1 = "SELECT project_id, MAX(timestamp_start) AS tijd FROM hours_registration GROUP BY project_id";
			$res1 = sql_query($q1);
			while ($row = sql_fetch_array($res1)) {
				if (!array_key_exists($row["project_id"],$hours)) {
					$hours[$row["project_id"]] = $row["tijd"];
				}
			}
			ksort($hours);
			reset($hours);

			// aray maken met alle te tonen projecten.
			$query = "SELECT COUNT(id) FROM projects_master";
			$query.=" WHERE is_active=1";
			$result = sql_query($query);
			$totaal = sql_result($result,0);
			$query = "SELECT COUNT(id) FROM project";
			$query .= " WHERE is_active=1 AND group_id=0";
			$result = sql_query($query);
			$tot1 = sql_result($result,0);
			$totaal = $totaal + $tot1;
			if ($totaal!=0) {
				// Haal informatie over elk project op
				$query = "SELECT * FROM project";
				$query.=" WHERE is_active=1 AND group_id=0";
				$query .= " ORDER BY name ASC";
				$result = sql_query($query);
				$i = 0;
				while ($row = sql_fetch_array($result)) {
					/* search for hours not yet paid for. */
					if ($row["lfact"]=="") {
						$lfact_time=0;
					} else {
						$lfact_time = $row["lfact"];
					}
					if ($hours[$row["id"]] > $lfact_time) {
						$fac[$i]["id"] = $row["id"];
						$fac[$i]["master"] = 0;
						$fac[$i]["nonmaster"] = 1;
						$fac[$i]["name"] = $row["name"];
						$fac[$i]["description"] = $row["description"];
						if ($user_perm["xs_projectmanage"] || $row["manager"] == $_SESSION["user_id"])
							$fac[$i]["allow_edit"] = 1;
						$i++;
					}
				}
				$sql = "SELECT * FROM projects_master ORDER BY name";
				$res = sql_query($sql);
				while ($row = sql_fetch_array($res)) {
					$aantal_subs = 0;
					$r = sql_query("SELECT * FROM project WHERE group_id=".$row["id"]." AND is_active=1");
					while ($row1 = sql_fetch_array($r)) {
						if ($row1["lfact"]=="") {
							$lfact_time=0;
						} else {
							$lfact_time = $row1["lfact"];
						}
						if ($hours[$row1["id"]] > $lfact_time) {
							$aantal_subs++;
						}
					}
					if ($aantal_subs) {
						$fac[$i]["id"] = $row["id"];
						$fac[$i]["nonmaster"] = 0;
						$fac[$i]["master"] = 1;
						$fac[$i]["name"] = $row["name"];
						$fac[$i]["description"] = $row["description"];
						if ($user_perm["xs_projectmanage"] || $row["manager"] == $_SESSION["user_id"]) {
							$fac[$i]["allow_edit"] = 1;
						}
						$i++;
					}
				}
				for ($j = 0; $j < sizeof($fac); $j++) {
					$sort_values[$j] = strtolower($fac[$j]["name"]);
				}
				if (count($sort_values)) {
					asort ($sort_values);
					reset ($sort_values);
					while (list ($arr_key, $arr_val) = each ($sort_values)) {
						$sorted_arr[] = $fac[$arr_key];
					}
					//subprojects
					$proj_access["sub"] = array();
					$proj_access["master"] = array();
					$proj_access["parent"] = array();
					$q = "select id, group_id from project where manager = ".$_SESSION["user_id"];
					$resx = sql_query($q);
					while ($rowx = sql_fetch_array($resx)) {
						$proj_access["sub"][]=$rowx["id"];
						$proj_access["parent"][]=$rowx["group_id"];
					}
					$proj_access["parent"] = array_unique($proj_access["parent"]);
					$q = "select id from projects_master where manager = ".$_SESSION["user_id"];
					$resx = sql_query($q);
					while ($rowx = sql_fetch_array($resx)) {
						$proj_access["master"][]=$rowx["id"];
					}
					$user_data = new User_data();
					$userperms = $user_data->getUserPermissionsById($_SESSION["user_id"]);
					//new permission code check
					if (!$userperms["xs_projectmanage"]) {
						foreach ($sorted_arr as $key=>$val) {
							if ($val["master"]) {
								if (!in_array($val["id"],$proj_access["master"])) {
									if (!in_array($val["id"],$proj_access["parent"]))
										unset($sorted_arr[$key]);
								}
							} else {
								if (!in_array($val["id"],$proj_access["sub"]))
									unset($sorted_arr[$key]);
							}
						}
					}
				}
			}
		}
?>
