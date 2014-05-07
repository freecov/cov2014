<?php
	if (!class_exists("Project_data")) {
		exit("no class definition found");
	}
	/* get the user data */
	$user_data = new User_data();
	$user_perm = $user_data->getUserdetailsById($_SESSION["user_id"]);
	$addressdata = new Address_data();
	$i = 0;

	/* prepare a basic permissions subquery if this user is not a global project manager */
	if (!$user_perm["xs_projectmanage"] && !$user_perm["xs_limited_projectmanage"]) {
		$groups = $user_data->getUserGroups($_SESSION["user_id"]);
		if (count($groups) > 0) {
			$regex_syntax = sql_syntax("regex");
			$sq = " AND ( 1=0 ";
			foreach ($groups as $g) {
				$g = "G".$g;
				$sq.= " OR users ".$regex_syntax." '(^|\\\\,)". $g."(\\\\,|$)' ";
			}
			$sq.= " OR manager = ".$_SESSION["user_id"]." OR executor = ".$_SESSION["user_id"]." OR users ".$regex_syntax." '(^|\\\\,)". (int)$_SESSION["user_id"]."(\\\\,|$)' ";
			$sq.= ") ";
		}
	}

	if ($options["address_id"]) {
		/* search for projects related to a relation */
		$like = sql_syntax("like");
		$regex = sql_syntax("regex");
		$regexmultirel = $regex." '(^|\\\\,)".$options["address_id"]."(\\\\,|$)'";
		$sql = sprintf("SELECT id,name,description,is_active,manager,executor,users FROM project WHERE address_id=%1\$d OR multirel %2\$s ORDER BY UPPER(name)", $options["address_id"], $regexmultirel);
		$res = sql_query($sql);
		$sorted_arr = array();
		while ($row = sql_fetch_assoc($res)) {
			if ($row["is_active"]) {
				$row["is_nonactive"] = 0;
			} else {
				$row["is_nonactive"] = 1;
			}
			if ($this->dataCheckPermissions($row, $_SESSION["user_id"])) {
				$row["allow_edit"] = 1;
			}
			$sorted_arr[] = $row;
		}
	} elseif ($options["user_id"]) {
		/* search for projects related to a user */
		$like = sql_syntax("like");
		if($options["active"]) { $activeOnly = "AND is_active > 0"; } else { $activeOnly = ""; }
		$sql = sprintf("SELECT id,name,description,is_active,manager,executor,budget,hours,lfact FROM project WHERE (manager=%1\$d OR executor=%1\$d OR users LIKE '%%,%1\$d' OR users LIKE '%1\$d,%%' OR users = %1\$d OR users LIKE '%%,%1\$d,%%') %2\$s ORDER BY UPPER(name)", $options["user_id"], $activeOnly);
		$res = sql_query($sql);
		$sorted_arr = array();
		while ($row = sql_fetch_assoc($res)) {
			if ($row["is_active"]) {
				$row["is_nonactive"] = 0;
			} else {
				$row["is_nonactive"] = 1;
			}
			if ($user_perm["xs_projectmanage"] || $user_perm["xs_limited_projectmanage"] || $row["manager"] == $_SESSION["user_id"]
				|| $row["executor"] == $_SESSION["user_id"])
				$row["allow_edit"] = 1;
			$sorted_arr[] = $row;
		}
	} else {
		/* main project view */
		/** quick description:
		 * - without searchkey:
		 *    display all active (master)projects
		 * - with searchkey:
		 *    display all projects matching the search key, with projectext: filter on the selected meta field
		 */
		if ($options["inactive"] == 1) {
			$is_active = "!= 1";
		} else {
			$is_active = "= 1";
		}

		if ($options["searchkey"] == "*")
			$options["searchkey"] = "%";

		if ($options["searchkey"]) {
			$like_syntax = sql_syntax("like");
			/* normal standalone projects */
			$query_sub  = sprintf("select * from project WHERE is_active ".$is_active." AND group_id = 0 ".$sq." AND
				(name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%') ORDER BY lower(name)", $options["searchkey"]);

			/* master projects */
			$query_main = sprintf("select * from projects_master WHERE (is_active ".$is_active." ".$sq."
				AND (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%')
				) OR (
				id IN (select group_id from project WHERE is_active ".$is_active." AND group_id > 0 ".$sq.")
				AND (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%'))
				order by lower(name)", $options["searchkey"]);

			/* normal projects with master project */
			$query_sub_master = sprintf("select * from project WHERE group_id > 0 AND (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%') $sq GROUP BY group_id", $options["searchkey"]);

			$query_sub_count = str_replace("select *", "select count(*)", $query_sub);
			$query_main_count = str_replace("select *", "select count(*)", $query_main);
		} elseif (!$GLOBALS["covide"]->license["has_project_declaration"]
			&& !$GLOBALS["covide"]->license["has_project_ext_samba"]) { //fixme
			$query_sub  = "select * from project WHERE is_active ".$is_active." AND group_id = 0 ".$sq;
			$query_sub_count  = "select COUNT(*) from project WHERE is_active ".$is_active." AND group_id = 0 ".$sq;
			$query_main = "select * from projects_master WHERE is_active ".$is_active." AND (1=1 ".$sq.") OR id IN (select group_id from project WHERE is_active ".$is_active." AND group_id > 0 ".$sq.") order by lower(name)";
			$query_main_count = "select COUNT(*) from projects_master WHERE is_active ".$is_active." AND (1=1 ".$sq.") OR id IN (select group_id from project WHERE is_active ".$is_active." AND group_id > 0 ".$sq.") order by lower(name)";
		} else {
			if (!$options["sort"]) {
				$query_sort = " ORDER BY lower(name)";
			} else {
				$query_sort = " ORDER BY ".sql_filter_col($options["sort"]);
			}
			$query_sub  = "select project.*, users.username from project left join users on users.id=project.executor WHERE project.is_active ".$is_active." AND group_id = 0 ".$sq.$query_sort;
			$query_sub_count  = "select COUNT(*) from project WHERE is_active ".$is_active." AND group_id = 0 ".$sq;
			$query_main = "select projects_master.*, users.username from projects_master left join users on users.id=projects_master.executor WHERE projects_master.is_active ".$is_active." AND (1=1 ".$sq.") OR projects_master.id IN (select group_id from project WHERE is_active ".$is_active." AND group_id > 0 ".$sq.")".$query_sort;
			$query_main_count = "select COUNT(*) from projects_master WHERE is_active ".$is_active." AND (1=1 ".$sq.") OR id IN (select group_id from project WHERE is_active ".$is_active." AND group_id > 0 ".$sq.") order by lower(name)";
		}
		$pr = array();
		$_use_hour_reg = 1;
		$projects      = array();
		$found_masters = array();

		/* normal standalone projects */
		$q = $query_sub;
		$r_c_1 = sql_query($query_main_count);
		$count1 = sql_result($r_c_1, 0);
		$r_c_2 = sql_query($query_sub_count);
		$count2 = sql_result($r_c_2, 0);
		$total_count = $count1+$count2;
		$res = sql_query($q, "", $start, $limit);
		while ($row = sql_fetch_assoc($res)) {
			$row["relname"] = "";
			$address_id = $row["address_id"];
			$multirel = $row["multirel"];
			$relations = array($address_id);
			$multirel = explode(",", $multirel);
			$relations = array_merge($multirel, $relations);
			$relations = array_unique($relations);
			foreach ($relations as $k=>$v) {
				if (!$v) {
					unset($relations[$k]);
				} else {
					$row["relname"] .= ", ".$addressdata->getAddressNameById($v);
				}
			}
			$row["relname"] = preg_replace("/^, /", "", $row["relname"]);
			$pr[$i]["id"]           = $row["id"];
			$pr[$i]["master"]       = 0;
			$pr[$i]["nonmaster"]    = 1;
			$pr[$i]["group_id"]     = $row["group_id"];
			$pr[$i]["name"]         = $row["name"];
			$pr[$i]["description"]  = $row["description"];
			$pr[$i]["executor"]     = $user_data->getUsernameById($row["executor"]);
			$pr[$i]["lfact"]        = $row["lfact"];
			$pr[$i]["has_hours"]    = $this->hasHours($row["id"], $row["lfact"]);
			if ($this->dataCheckPermissions($row, 0, 1)) {
				$pr[$i]["allow_edit"] = 1;
			}
			$pr[$i]["relations"] = implode(",", $relations);
			$pr[$i]["relation_names"] = $row["relname"];
			$i++;
		}
		/* master projects */
		$q = $query_main;
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$has_hours = false;
			// grab subprojects for their hours
			$subs = $this->getSubprojectsById($row["id"]);
			if (!is_array($subs)) { $subs = array("data" => array()); }
			if (!is_array($subs["data"])) { $subs["data"] = array(); }
			foreach ($subs["data"] as $v) {
				if ($v["has_hours"]) {
					$has_hours = true;
				}
			}
			$pr[$i]["id"]           = $row["id"];
			$pr[$i]["master"]       = 1;
			$pr[$i]["nonmaster"]    = 0;
			$pr[$i]["group_id"]     = 0;
			$pr[$i]["name"]         = $row["name"];
			$pr[$i]["description"]  = $row["description"];
			$pr[$i]["executor"]     = $user_data->getUsernameById($row["executor"]);
			$pr[$i]["lfact"]        = $row["lfact"];
			$pr[$i]["has_hours"]    = $has_hours;
			if ($this->dataCheckPermissions($row, 0, 1)) {
				$pr[$i]["allow_edit"] = 1;
			}
			$found_masters[$i] = $row["id"];
			$i++;
		}
		/* now lets have some fun
			We are searching on subprojects, but we only want to add the master projects
			to the list. Also make sure to check permissions and stuff
		*/
		if ($query_sub_master) {
			$q = $query_sub_master;
			$res = sql_query($q);
			$get_masters = array(0);
			while ($row = sql_fetch_assoc($res)) {
				$get_masters[] = $row["group_id"];
			}
			$q = sprintf("select * from projects_master where id in (%s)", implode(",", $get_masters));
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				if (!in_array($row["id"], $found_masters)) {
					$has_hours = false;
					// grab subprojects for their hours
					$subs = $this->getSubprojectsById($row["id"]);
					if (!is_array($subs)) { $subs = array(); }
					if (!is_array($subs["data"])) { $subs["data"] = array(); }
					foreach ($subs["data"] as $v) {
						if ($v["has_hours"]) {
							$has_hours = true;
						}
					}
					$row["relnames"] = "";
					$address_id = $row["address_id"];
					$multirel = $row["multirel"];
					$relations = array($address_id);
					$multirel = explode(",", $multirel);
					$relations = array_merge($multirel, $relations);
					$relations = array_unique($relations);
					foreach ($relations as $k=>$v) {
						if (!$v) {
							unset($relations[$k]);
						} else {
							$row["relname"] .= ", ".$addressdata->getAddressNameById($v);
						}
					}
					$row["relname"] = preg_replace("/^, /", "", $row["relname"]);
					$pr[$i]["id"]           = $row["id"];
					$pr[$i]["master"]       = 1;
					$pr[$i]["nonmaster"]    = 0;
					$pr[$i]["group_id"]     = 0;
					$pr[$i]["name"]         = $row["name"];
					$pr[$i]["description"]  = $row["description"];
					$pr[$i]["executor"]     = $user_data->getUsernameById($row["executor"]);
					$pr[$i]["lfact"]        = $row["lfact"];
					$pr[$i]["has_hours"]    = $has_hours;
					if ($this->dataCheckPermissions($row, 0, 1)) {
						$pr[$i]["allow_edit"] = 1;
					}
					$pr[$i]["relations"] = implode(",", $relations);
					$pr[$i]["relation_names"] = $row["relname"];
					$found_masters[$i] = $row["id"];
					$i++;
				}
			}
		}
		/* sort stuff */
		foreach ($pr as $j=>$v) {
			$sort_values_name[$j] = strtolower($pr[$j]["name"]);
			$sort_values_description[$j] = strtolower($pr[$j]["description"]);
			$sort_values_executor[$j] = strtolower($pr[$j]["executor"]);
		}

		if (count($pr)) {
			asort ($sort_values_name);
			reset ($sort_values_name);
			$sort_values = $sort_values_name;
			if ($options["sort"]) {
				if (strpos($options["sort"], "|") !== false) {
					$sortparams = explode("|", $options["sort"]);
					switch ($sortparams[0]) {
					case "name":
						$sort_values = $sort_values_name;
						break;
					case "description":
						$sort_values = $sort_values_description;
						break;
					case "executor":
						$sort_values = $sort_values_executor;
						break;
					default:
						$sort_values = $sort_values_name;
						break;
					}
					if ($sortparams[1] == "asc") {
						asort($sort_values);
						reset($sort_values);
					} else {
						arsort($sort_values);
						reset($sort_values);
					}
				}
			}
		}
		$sorted_arr = array();
		$i = 0;
		if (!is_array($sort_values)) {
			$sort_values = array();
		}
		foreach ($sort_values as $k => $v) {
			$sorted_arr[$i] = $pr[$k];
			$i++;
		}
		$sorted_arr["total_count"] = $total_count;
	}
?>
