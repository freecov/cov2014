<?php
Class Project_data {
	/* constants */
	const include_dir = "classes/project/inc/";
	const class_name  = "project";

	/* variables */
	private $pagesize;
	private $user_cache;

	/* methods */
	public function __construct() {
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}

	/* updateRel($project_id, $master, $address_id) {{{ */
	/**
	 * Updates the relation that is linked to a project.
	 *
	 * @param int the project to alter
	 * @param int wether it's a master project or not
	 * @param int the address id to link
	 * @return string a javascript function, because we use xmlhttp to call this
	 */
	public function updateRel($project_id, $master, $address_id) {
		if ($master == 1) {
			$table = "projects_master";
		} else {
			$table = "project";
		}
		$sql = sprintf("UPDATE $table SET address_id = %d WHERE id = %d", $address_id, $project_id);
		$res = sql_query($sql);
		echo "reload_page();";
	}
	/* }}} */

	public function setLfact() {
		require(self::include_dir."dataSetLfact.php");
	}

	public function getProjectsBySearch($options = array()) {
		require(self::include_dir."dataGetProjectsBySearch.php");
		return $sorted_arr;
	}

	public function getProjectById($projectid, $master=0) {
		require(self::include_dir."dataGetProjectById.php");
		return $projectinfo;
	}

	public function getProjectNameById($projectid, $master=0) {
		if ($master) {
			$sql = sprintf("SELECT name FROM projects_master WHERE id=%d", $projectid);
		} else {
			$sql = sprintf("SELECT name FROM project WHERE id=%d", $projectid);
		}
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		if (!$row["name"]) { $row["name"] = "[deleted]"; }
		if ($projectid == 0) {
			$row["name"] = gettext("none");
		}
		return $row["name"];
	}

	public function getExceededProjects($user_id) {
		$sql = sprintf("SELECT p.id, p.name, p.hours, p.budget, SUM(h.timestamp_end - h.timestamp_start) AS uren, SUM(tarif) AS bedrag  FROM project p, hours_registration h, hours_activities a WHERE p.id = h.project_id AND p.is_active = 1 AND (p.budget > 0 OR p.hours > 0) AND p.manager = %d AND h.timestamp_end > p.lfact AND h.is_billable > 0 AND h.activity_id = a.id GROUP BY h.project_id HAVING (p.hours*60*60) < uren OR ((uren/60/60)*bedrag) > p.budget", $user_id);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$return[] = $row;
		}
		return $return;
	}
	
	public function getSubprojectsById($projectid, $top=0, $sort="") {
		require(self::include_dir."dataGetSubprojectsById.php");
		return $return;
	}

	public function getMasterProjectArray() {
		require(self::include_dir."dataGetMasterProjectArray.php");
		return $projectlist;
	}

	public function saveProject() {
		require(self::include_dir."dataSaveProject.php");
	}

	public function getHoursList($settings) {
		require(self::include_dir."dataGetHoursList.php");
		return $hoursinfo;
	}

	public function toggleHours() {
		if ($_REQUEST["id"]) {
			$sql = sprintf("SELECT id,is_billable FROM hours_registration WHERE id=%d", $_REQUEST["id"]);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["is_billable"] == 1) { $is_billable = 0; } else { $is_billable = 1; }
			$sql = sprintf("UPDATE hours_registration SET is_billable = %d WHERE id=%d", $is_billable, $_REQUEST["id"]);
			$res = sql_query($sql);
			echo "hours_refresh_page();";
		} else {
			echo "alert('".addslashes(gettext("No valid id"))."')";
		}
	}

	public function toggleActive() {
		if ($_REQUEST["master"]) {
			$table = "projects_master";
		} else {
			$table = "project";
		}
		if ($_REQUEST["id"]) {
			/* fetch current is_active flag */
			$sql = sprintf("SELECT id,is_active FROM $table WHERE id=%d", $_REQUEST["id"]);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["is_active"]) {
				$is_active = 0;
				$html = gettext("no");
			} else {
				$is_active = 1;
				$html = gettext("yes");
			}
			/* now update the database */
			$q = sprintf("UPDATE $table SET is_active=%d WHERE id=%d", $is_active, $row["id"]);
			$r = sql_query($q);
			echo "print_active('".addslashes($html)."');";
		} else {
			echo "alert('".addslashes(gettext("No valid id"))."');";
		}
	}

    /* 	getActivities {{{ */
    /**
     * 	return all the activities in the database
     *
     * @return array the activities sorted by name
     */
	public function getActivities() {
		$sql = "SELECT * FROM hours_activities ORDER BY UPPER(activity)";
		$res = sql_query($sql);
		$activities = array();
		while ($row = sql_fetch_assoc($res)) {
			$activities[] = $row;
		}
		return $activities;
	}
    /* }}} */

    /* 	saveActivity {{{ */
    /**
     * 	Store activity in db
     *
     * @param array The activity info. Name and costs/hour
	 * @return bool true on succes, false on failure
     */
	public function saveActivity($data) {
		if ($data["id"]) {
			/* update */
			$sql = sprintf("UPDATE hours_activities SET activity = '%s', tarif = %01.2F WHERE id = %d", $data["activity"], $data["tarif"], $data["id"]);
		} else {
			$sql = sprintf("INSERT INTO hours_activities (activity, tarif) VALUES('%s', %01.2F)", $data["activity"], $data["tarif"]);
		}
		$res = sql_query($sql);
		return true;
	}
    /* }}} */

    /* 	removeActivity {{{ */
    /**
     * 	Delete activity item from db
     *
     * This is a dangerous action. It means hour registration items that
	 * use this activity will have cost 0 right now.
     *
     * @param int The database id of the record to remove.
     * @return bool true on succes, false on fail
     */
	public function removeActivity($id) {
		$sql = sprintf("DELETE FROM hours_activities WHERE id = %d", $id);
		$res = sql_query($sql);
		return true;
	}
    /* }}} */

    /* 	getOverviewData {{{ */
    /**
     * 	getOverviewData. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function getOverviewData($start, $end) {
		$data["total_fac"] = 0;
		$data["total_nofac"] = 0;
		$data["total_hol"] = 0;
		$user_data = new User_data();
		$users = $user_data->getUserList();
		foreach ($users as $userid=>$username) {
			$data["users"][$userid]["total_fac"] = 0;
			$data["users"][$userid]["total_nofac"] = 0;
			$data["users"][$userid]["total_hol"] = 0;
			$data["users"][$userid]["total_ill"] = 0;
			$data["users"][$userid]["total_sl"] = 0;
			$sql  = sprintf("SELECT * FROM hours_registration WHERE user_id = %d", $userid);
			$sql .= sprintf(" AND timestamp_start > %d AND timestamp_end < %d", $start, $end);
			$sql .= " ORDER BY timestamp_start";
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$timespan = $row["timestamp_end"] - $row["timestamp_start"];
				switch ($row["type"]) {
					case 3 :
						$data["users"][$userid]["total_hol"] += $timespan;
						break;
					case 5 :
						$data["users"][$userid]["total_ill"] += $timespan;
						break;
					case 4 :
						$data["users"][$userid]["total_sl"] += $timespan;
						break;
				}
				if ($row["is_billable"]) {
					$data["users"][$userid]["total_fac"] += $timespan;
				} else {
					$data["users"][$userid]["total_nofac"] += $timespan;
				}
			}
			$data["users"][$userid]["total_fac"] = round($data["users"][$userid]["total_fac"]/3600);
			$data["users"][$userid]["total_nofac"] = round($data["users"][$userid]["total_nofac"]/3600);
			$data["users"][$userid]["total_hol"] = round($data["users"][$userid]["total_hol"]/3600);
			$data["users"][$userid]["total_ill"] = round($data["users"][$userid]["total_ill"]/3600);
			$data["users"][$userid]["total_sl"] = round($data["users"][$userid]["total_sl"]/3600);
			$data["total_fac"] += $data["users"][$userid]["total_fac"];
			$data["total_nofac"] += $data["users"][$userid]["total_nofac"];
			$data["total_hol"] += $data["users"][$userid]["total_hol"];
		}
		return $data;
	}
   /* }}} */

	/* {{{ function dataPickProject() */
	public function dataPickProject($deb = 0) {
		$user_data = new User_data();
		$user_data->getUserdetailsById($_SESSION["user_id"]);
		if ($_REQUEST["searchinfo"]) {
			$like_syntax = sql_syntax("like");
			$sq = sprintf(" AND (name $like_syntax '%%%s%%' OR description $like_syntax '%%%s%%') ", $_REQUEST["searchinfo"], $_REQUEST["searchinfo"]);
		}
		switch ($_REQUEST["actief"]) {
			case "0" : $actief = " is_active=0 "; break;
			case "1" : $actief = " is_active=1 "; break;
			case "2" : $actief = " 1=1 "; break;
			default  : $actief= " is_active=1 "; break;
		}
		$like = sql_syntax("like");
		if ($_REQUEST["deb"] && !$deb) {
			$debiteur = sprintf(" AND address_id = %1\$s OR (multirel $like '%%,%1\$s%%' OR multirel $like '%%%1\$s,%%' OR multirel = '%1\$s')", $_REQUEST["deb"]);
		}
		if ($deb) {
			$debiteur = sprintf(" AND address_id = %1\$s OR (multirel $like '%%,%1\$s%%' OR multirel $like '%%%1\$s,%%' OR multirel = '%1\$s')", $deb);
		}
		$q = "select * from project where $actief $debiteur $sq order by UPPER(name)";
		$q_count = "select count(*) from project where $actief $debiteur $sq";

		$res = sql_query($q, "", (int)$_REQUEST["start"], $GLOBALS["covide"]->pagesize);
		$res_count = sql_query($q_count);
		$data["count"] = sql_result($res_count,0);

		while ($row = sql_fetch_array($res)) {
			if ($row["group_id"]) {
				$row2 = $this->getProjectById($row["group_id"], 1);
			} else {
				$row2 = array();
			}
			if ($GLOBALS["covide"]->license["use_project_global_reghour"] || $this->dataCheckPermissions($row) || $this->dataCheckPermissions($row2[0]))
				$data["data"][$row["id"]] = $row;
		}
		$data["count"] = count($data["data"]);
		return $data;
  }
  /* }}} */
  /* {{{ function dataCheckPermissions() */
  public function dataCheckPermissions($row) {
		if (!$this->user_cache["user_object"]) {
	  	$this->user_cache["user_object"] = new User_data();
			$this->user_cache["user_perm"]   = $this->user_cache["user_object"]->getUserdetailsById($_SESSION["user_id"]);
			$this->user_cache["user_groups"] = $this->user_cache["user_object"]->getUserGroups($_SESSION["user_id"]);
		}
		if ($this->user_cache["user_perm"]["xs_projectmanage"] ||
			$row["manager"] == $_SESSION["user_id"] ||
			$row["executor"] == $_SESSION["user_id"]) {
			/* allow access */
			return true;
		} else {
			$arr =& $this->user_cache["user_groups"];
			$users = explode(",", $row["users"]);
			foreach ($arr as $v) {
				if (in_array("G".$v, $users)) {
					return true;
				} elseif (in_array($_SESSION["user_id"], $users)) {
					return true;
				}
			}
		}
		return false;
  }
  /* }}} */
	/* deleteProject {{{ */
	/**
	 * Deletes a project and all linked files and hourregistration items
	 *
	 * @param int $projectid The projectid to remove
	 * @param int 0 if normal project, 1 if master project
	 * @return bool true on success.
	 */
	public function deleteProject($projectid, $master = 0) {
		/* only allow if user has access */
		$user_data    = new User_data();
		$filesys_data = new Filesys_data();
		$user_perm = $user_data->getUserdetailsById($_SESSION["user_id"]);

		if ($user_perm["xs_usermanage"] || $user_perm["xs_limitusermanage"]) {
			if ($master) {
				/* master projects */

				/* remove project */
				$sql = sprintf("DELETE FROM projects_master WHERE id = %d", $projectid);
				$res = sql_query($sql);
				/* update all children */
				$sql = sprintf("UPDATE project set group_id = 0 WHERE group_id = %d", $projectid);
				$res = sql_query($sql);
				$output = new Layout_output();
				$output->start_javascript();
				$output->addCode("
					opener.document.getElementById('velden').submit(); window.close();
				");
				$output->end_javascript();
				$output->exit_buffer();
				return true;
			} else {
				/* sub or standalone projects */

				/* remove hours registration items */
				$sql = sprintf("DELETE FROM hours_registration WHERE project_id = %d", $projectid);
				$res = sql_query($sql);
				/* remove the project */
				$sql = sprintf("DELETE FROM project WHERE id = %d", $projectid);
				$res = sql_query($sql);
				/* get the filesys folder and remove it */
				$folder = $filesys_data->getProjectFolder($projectid);
				$filesys_data->deleteFolderExec($folder);
				return true;
			}

		} else {
			return false;
		}
	}
	/* }}} */

	public function autocomplete_project_name($str) {
		$like = sql_syntax("like");
		$data = array();
		$q = sprintf("select name from project where name %2\$s '%1\$s%%' ORDER BY name desc", $str, $like);
		$res = sql_query($q, "", 0, 1);
		if (sql_num_rows($res)) {
			/* get currval */
			$last = str_replace($str, "", sql_result($res,0));
			/* some left padding adjustments */
			$last = $str.sprintf("%0".strlen($last)."s", ($last+1));
			echo $last;
		}
	}
	/* getRecord {{{ */
	/**
	 * Get a project record from the database without checking permissions
	 *
	 * We need this for the filesystem. Some checks there need all the information
	 * The normal getProjectById returns nothing when the user has no permissions thus
	 * filesystem folder duplication will showup
	 */
	public function getRecord($options = array()) {
		if (!isset($options["id"]) || !$options["id"])
			die("error - no id specified");
		if ($options["master"])
			$table = "projects_master";
		else
			$table = "project";
		$sql = sprintf("SELECT * FROM %s WHERE id = %d", $table, $options["id"]);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		return $row;
	}
	/* }}} */
}
?>
