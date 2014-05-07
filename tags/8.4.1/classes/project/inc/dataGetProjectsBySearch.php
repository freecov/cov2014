<?php
	if (!class_exists("Project_data")) {
		exit("no class definition found");
	}
	/* get the user data */
	$user_data = new User_data();
	$user_perm = $user_data->getUserdetailsById($_SESSION["user_id"]);

	/* prepare a basic permissions subquery if this user is not a global project manager */
	if (!$user_perm["xs_projectmanage"]) {
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
		$sql = sprintf("SELECT id,name,description,is_active,manager,executor,budget,hours,lfact FROM project WHERE (manager=%1\$d OR executor=%1\$d) %2\$s ORDER BY UPPER(name)", $options["user_id"], $activeOnly);
		$res = sql_query($sql);
		$sorted_arr = array();
		while ($row = sql_fetch_assoc($res)) {
			if ($row["is_active"]) {
				$row["is_nonactive"] = 0;
			} else {
				$row["is_nonactive"] = 1;
			}
			if ($user_perm["xs_projectmanage"] || $row["manager"] == $_SESSION["user_id"]
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
			$query_sub  = sprintf("select * from project WHERE group_id = 0 ".$sq." AND
				(name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%') ORDER BY lower(name)", $options["searchkey"]);

			/* master projects */
			$query_main = sprintf("select * from projects_master WHERE (1=1 ".$sq."
				AND (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%')
				) OR (
				id IN (select group_id from project WHERE group_id > 0 ".$sq.")
				AND (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%'))
				order by lower(name)", $options["searchkey"]);

			/* normal projects with master project */
			$query_sub_master = sprintf("select * from project WHERE group_id > 0 AND (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%') $sq GROUP BY group_id", $options["searchkey"]);

			$query_sub_count  = sprintf("select count(*) from project WHERE group_id = 0 ".$sq." AND
				(name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%') ORDER BY lower(name)", $options["searchkey"]);

			/* master projects */
			$query_main_count = sprintf("select count(*) from projects_master WHERE (1=1 ".$sq."
				AND (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%')
				) OR (
				id IN (select group_id from project WHERE group_id > 0 ".$sq.")
				AND (name $like_syntax '%%%1\$s%%' OR description $like_syntax '%%%1\$s%%'))
				order by lower(name)", $options["searchkey"]);

		} elseif (!$GLOBALS["covide"]->license["has_project_declaration"] 
			&& !$GLOBALS["covide"]->license["has_project_ext_samba"]) { //fixme
			$query_sub  = "select * from project WHERE is_active ".$is_active." AND group_id = 0 ".$sq;
			$query_sub_count  = "select COUNT(*) from project WHERE is_active ".$is_active." AND group_id = 0 ".$sq;
			$query_main = "select * from projects_master WHERE is_active ".$is_active." AND (1=1 ".$sq.") OR id IN (select group_id from project WHERE is_active ".$is_active." AND group_id > 0 ".$sq.") order by lower(name)";
			$query_main_count = "select COUNT(*) from projects_master WHERE is_active ".$is_active." AND (1=1 ".$sq.") OR id IN (select group_id from project WHERE is_active ".$is_active." AND group_id > 0 ".$sq.") order by lower(name)";
		} else {
			if (!$options["sort"]) {
				$query_sort = "ORDER BY lower(name)";
			} else {
				$query_sort = "ORDER BY ".sql_filter_col($options["sort"]);
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
			$pr[$i]["id"]           = $row["id"];
			$pr[$i]["master"]       = 0;
			$pr[$i]["nonmaster"]    = 1;
			$pr[$i]["group_id"]     = $row["group_id"];
			$pr[$i]["name"]         = $row["name"];
			$pr[$i]["description"]  = $row["description"];
			$pr[$i]["executor"]     = $user_data->getUsernameById($row["executor"]);
			$pr[$i]["lfact"]        = $row["lfact"];
			$pr[$i]["has_hours"]    = $this->hasHours($row["id"], $row["lfact"]);
			if ($this->dataCheckPermissions($row)) {
				$pr[$i]["allow_edit"] = 1;
			}
			$i++;
		}
		/* master projects */
		$q = $query_main;
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$pr[$i]["id"]           = $row["id"];
			$pr[$i]["master"]       = 1;
			$pr[$i]["nonmaster"]    = 0;
			$pr[$i]["group_id"]     = 0;
			$pr[$i]["name"]         = $row["name"];
			$pr[$i]["description"]  = $row["description"];
			$pr[$i]["executor"]     = $user_data->getUsernameById($row["executor"]);
			$pr[$i]["lfact"]        = $row["lfact"];
			$pr[$i]["has_hours"]    = false;
			if ($this->dataCheckPermissions($row)) {
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
					$pr[$i]["id"]           = $row["id"];
					$pr[$i]["master"]       = 1;
					$pr[$i]["nonmaster"]    = 0;
					$pr[$i]["group_id"]     = 0;
					$pr[$i]["name"]         = $row["name"];
					$pr[$i]["description"]  = $row["description"];
					$pr[$i]["executor"]     = $user_data->getUsernameById($row["executor"]);
					$pr[$i]["lfact"]        = $row["lfact"];
					$pr[$i]["has_hours"]    = false;
					if ($this->dataCheckPermissions($row)) {
						$pr[$i]["allow_edit"] = 1;
					}
					$found_masters[$i] = $row["id"];
					$i++;
				}
			}
		}
		/* sort stuff */
		foreach ($pr as $j=>$v) {
			$sort_values[$j] = strtolower($pr[$j]["name"]);
		}

		if (count($sort_values)) {
			if (!$options["sort"]) {
				asort ($sort_values);
				reset ($sort_values);
			}
			while (list ($arr_key, $arr_val) = each ($sort_values)) {
				$sorted_arr[] = $pr[$arr_key];
			}
		}
		$sorted_arr["total_count"] = $total_count;
	}
?>
