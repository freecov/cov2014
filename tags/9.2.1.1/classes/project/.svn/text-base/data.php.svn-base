<?php
/**
 * Covide Groupware-CRM Project_data
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */
Class Project_data {
	/* constants */
	const include_dir = "classes/project/inc/";
	const class_name  = "project";

	/* variables */
	/**
	 * @var int The pagesize, defaults to the paging objects pagesize
	 */
	private $pagesize;
	/**
	 * @var array User permissions used in the dataCheckPermissions method
	 */
	private $user_cache;

	/* methods */
	/* __construct {{{ */
	/**
	 * Constructor that sets the pagesize variable
	 */
	public function __construct() {
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}
	/* }}} */
	/* updateRel($project_id, $master, $address_id) {{{ */
	/**
	 * Updates the relation that is linked to a project.
	 *
	 * @param int the project to alter
	 * @param int wether it's a master project or not
	 * @param int the address id to link
	 *
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
	/* setLfact {{{ */
	/**
	 * Sets last invoice date on a project
	 */
	public function setLfact() {
		require(self::include_dir."dataSetLfact.php");
	}
	/* }}} */
	/* getProjectsBySearch {{{ */
	/**
	 * Get a list of projects based on the search options
	 *
	 * @todo Document $options and return format
	 *
	 * @param array $options Searchoptions
	 * @param int $start The start in the recordset
	 * @param int $limit The ammount of projects to return
	 *
	 * @return array Projects matched
	 */
	public function getProjectsBySearch($options = array(), $start = 0, $limit = 0) {
		require(self::include_dir."dataGetProjectsBySearch.php");
		return $sorted_arr;
	}
	/* }}} */
	/* getProjectById {{{ */
	/**
	 * Get projectinformation from specified id
	 *
	 * @param int $projectid The project id from the database
	 * @param int $master If set to 1 only fetch masterproject information
	 *
	 * @return array Project information
	 */
	public function getProjectById($projectid, $master=0) {
		require(self::include_dir."dataGetProjectById.php");
		return $projectinfo;
	}
	/* }}} */
	/* getProjectNameById {{{ */
	/**
	 * Get the projectname for specified projectid
	 *
	 * @param int $projectid The id from the database
	 * @param int $master if set to 1 only look at masterprojects
	 *
	 * @return string The projectname
	 */
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
	/* }}} */
	/* getExceededProjects {{{ */
	/**
	 * Get projects that exceeded either budget or timelimit set
	 *
	 * @param int $user_id Get the project where this user is the manager
	 *
	 * @return array Projects that exceeded budget including the set and real values for budget and hours
	 */
	public function getExceededProjects($user_id) {
		$sql = sprintf("
			SELECT
				project_id,
				project.name,
				project.hours*3600*0.75 AS hours,
				project.budget*0.75 AS budget,
				SUM(hours_registration.timestamp_end - hours_registration.timestamp_start) + SUM(IF(hours_registration.hours IS NULL, 0, hours_registration.hours)*3600) AS total_hours_in_sec,
				SUM(IF(hours_registration.tarif IS NULL and price IS NULL, ((timestamp_end-timestamp_start)/3600), 0) * hours_activities.tarif) + SUM(IF(hours_registration.tarif IS NULL, 0, hours_registration.tarif)) + SUM(IF(price IS NULL, 0, price)) AS total_budget
			FROM hours_registration
			LEFT JOIN hours_activities ON hours_activities.id = hours_registration.activity_id
			LEFT JOIN project ON project.id = hours_registration.project_id
			WHERE
				hours_registration.is_billable = 1
				AND project.is_active = 1
				AND project.manager = %d
			GROUP BY hours_registration.project_id
			HAVING
				(hours > 0 AND hours < total_hours_in_sec)
				OR (budget > 0 AND budget < total_budget)
			", $user_id);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$return[] = $row;
		}
		return $return;
	}
	/* }}} */
	/* getSubprojectsById {{{ */
	/**
	 * Get all subprojects for a given master project
	 *
	 * @param int $projectid The master project database id
	 * @param int $top Where to start in the recordset
	 * @param string $sort How to sort the result
	 *
	 * @return array Subprojects
	 */
	public function getSubprojectsById($projectid, $top=0, $sort="") {
		require(self::include_dir."dataGetSubprojectsById.php");
		return $return;
	}
	/* }}} */
	/* getMasterProjectArray {{{ */
	/**
	 * Get all masterprojects in an array
	 *
	 * @return array Projects
	 */
	public function getMasterProjectArray() {
		require(self::include_dir."dataGetMasterProjectArray.php");
		return $projectlist;
	}
	/* }}} */
	/* saveProject {{{ */
	/**
	 * Save projectinformation into the database
	 *
	 * @return void
	 */
	public function saveProject() {
		require(self::include_dir."dataSaveProject.php");
	}
	/* }}} */
	/* getHoursList {{{ */
	/**
	 * Get hours for a specific project based on optional settings
	 *
	 * @todo Document $settings structure and return structure
	 *
	 * @param array $settings Limit hours returned based on parameters
	 *
	 * @return array Hourslist
	 */
	public function getHoursList($settings) {
		require(self::include_dir."dataGetHoursList.php");
		return $hoursinfo;
	}
	/* }}} */
	/* toggleHours {{{ */
	/**
	 * Toggle hours from billable to service and viceversa
	 *
	 * @todo Move the id from a $_REQUEST to a function parameter
	 *
	 * @return void
	 */
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
	/* }}} */
	/* toggleActive {{{ */
	/**
	 * Toggle a project from non-active to active and viceversa
	 *
	 * @todo Move the project id from a $_REQUEST to a function parameter
	 *
	 * @return void
	 */
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
			$output = new Layout_output();
			if ($row["is_active"]) {
				$is_active = 0;
				$output->insertAction("disabled", gettext("no"), '');
			} else {
				$is_active = 1;
				$output->insertAction("enabled", gettext("yes"), '');
			}
			$html = $output->generate_output();
			/* now update the database */
			$q = sprintf("UPDATE $table SET is_active=%d WHERE id=%d", $is_active, $row["id"]);
			$r = sql_query($q);
			echo "print_active('".addslashes($html)."');";
		} else {
			echo "alert('".addslashes(gettext("No valid id"))."');";
		}
	}
	/* }}} */
	/* 	getActivities {{{ */
	/**
	 * 	return all the activities in the database
	 *
	 * @param int $with_groups if set to 1 will order the activities by group
	 *
	 * @return array the activities sorted by name
	 */
	public function getActivities($with_groups = 0) {
		if ($with_groups) {
			$sql = "SELECT hours_activities.*, hours_activities_groups.name AS group_name FROM hours_activities LEFT JOIN hours_activities_groups ON hours_activities.group_id = hours_activities_groups.id ORDER BY group_id, UPPER(activity)";
		} else {
			$sql = "SELECT * FROM hours_activities ORDER BY group_id,UPPER(activity)";
		}
		$res = sql_query($sql);
		$activities = array();
		while ($row = sql_fetch_assoc($res)) {
			if ($with_groups) {
				$activities[$row["group_name"]][] = $row;
			} else {
				$activities[] = $row;
			}
		}
		return $activities;
	}
	/* }}} */
	/* 	saveActivity {{{ */
	/**
	 * 	Store activity in db
	 *
	 * @param array The activity info. Name and costs/hour and optional the department_id if project_ext is activated
	 * @return bool true on succes, false on failure
	 */
	public function saveActivity($data) {
		if ($data["id"]) {
			/* update */
			$sql = sprintf("
				UPDATE hours_activities SET activity = '%s', tarif = %01.2F, department_id = %d, user_id = %d, group_id = %d WHERE id = %d",
				$data["activity"], $data["tarif"], $data["department_id"], $data["user_id"], $data["group_id"], $data["id"]);
		} else {
			$sql = sprintf("
				INSERT INTO hours_activities (activity, tarif, department_id, user_id, group_id) VALUES('%s', %01.2F, %d, %d, %d)",
				$data["activity"], $data["tarif"], $data["department_id"], $data["user_id"], $data["group_id"]);
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
	/* getActivityGroups {{{ */
	/**
	 * Return the activity groups
	 *
	 * @return array Activitygroups ordered by name
	 */
	public function getActivityGroups() {
		$activitygroups = array(0 => "---");
		$sql = "SELECT * FROM hours_activities_groups ORDER BY UPPER(name);";
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$activitygroups[$row["id"]] = $row["name"];
		}
		return $activitygroups;
	}
	/* }}} */
	/* 	saveActivityGroup {{{ */
	/**
	 * 	Store activitygroup in db
	 *
	 * @param array The activitygroup info. Id and Name
	 * @return bool true on succes, false on failure
	 */
	public function saveActivityGroup($data) {
		if ($data["id"]) {
			/* update */
			$sql = sprintf("
				UPDATE hours_activities_groups SET name = '%s' WHERE id = %d",
				$data["name"], $data["id"]);
		} else {
			$sql = sprintf("
				INSERT INTO hours_activities_groups (name) VALUES('%s')",
				$data["name"]);
		}
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* 	removeActivityGroup {{{ */
	/**
	 * 	Delete activityGroup item from db
	 *
	 * @param int The database id of the record to remove.
	 * @return bool true on succes, false on fail
	 */
	public function removeActivityGroup($id) {
		$sql = sprintf("DELETE FROM hours_activities_groups WHERE id = %d", $id);
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* 	getCosts {{{ */
	/**
	 * 	return all the costs in the database
	 *
	 * @return array the costs sorted by name
	 */
	public function getCosts() {
		$sql = "SELECT * FROM project_costs ORDER BY UPPER(cost)";
		$res = sql_query($sql);
		$costs = array();
		while ($row = sql_fetch_assoc($res)) {
			$costs[] = $row;
		}
		return $costs;
	}
	/* }}} */
	/* 	saveCost {{{ */
	/**
	 * 	Store cost in db
	 *
	 * @param array The cost info. Name and costs/hour and optional the department_id if project_ext is activated
	 * @return bool true on succes, false on failure
	 */
	public function saveCost($data) {
		if ($data["id"]) {
			/* update */
			$sql = sprintf("UPDATE project_costs SET cost = '%s', tarif = %01.2F, department_id = %d, purchase = %01.2F, marge = %01.2F WHERE id = %d", $data["cost"], $data["tarif"], $data["department_id"], $data["purchase"], $data["marge"], $data["id"]);
		} else {
			$sql = sprintf("INSERT INTO project_costs (cost, tarif, department_id, purchase, marge) VALUES('%s', %01.2F, %d, %01.2F, %01.2F)", $data["cost"], $data["tarif"], $data["department_id"], $data["purchase"], $data["marge"]);
		}
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* 	removeCost {{{ */
	/**
	 * 	Delete Cost item from db
	 *
	 * This is a dangerous action. It means hour registration items that
	 * use this cost will have cost 0 right now.
	 *
	 * @param int The database id of the record to remove.
	 * @return bool true on succes, false on fail
	 */
	public function removeCost($id) {
		$sql = sprintf("DELETE FROM project_costs WHERE id = %d", $id);
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
	public function getOverviewData($start, $end, $user_id=0) {
		$data["total_fac"] = 0;
		$data["total_nofac"] = 0;
		$data["total_hol"] = 0;
		if ($user_id) {
			$users = array($user_id => $user_id);
		} else {
			$user_data = new User_data();
			$users = $user_data->getUserList();
		}
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
		$regex = sql_syntax("regex");
		if ($_REQUEST["deb"] && !$deb) {
			$regexmultirel = $regex." '(^|\\\\,)".$_REQUEST["deb"]."(\\\\,|$)'";
			$debiteur = sprintf(" AND address_id = %1\$s OR multirel %2\$s", $_REQUEST["deb"], $regexmultirel);
		}
		if ($deb) {
			$regexmultirel = $regex." '(^|\\\\,)".$deb."(\\\\,|$)'";
			$debiteur = sprintf(" AND address_id = %1\$s OR multirel %2\$s", $deb, $regexmultirel);
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
		//$data["count"] = count($data["data"]);
		return $data;
	}
	/* }}} */
	/* dataCheckPermissions {{{ */
	/**
	 * Check the permissions on a project for a user
	 *
	 * @param array $row Projectdata
	 * @param int $user_id The userid to check this for
	 * @param int $check_edit see if we can edit or if we have global view access
	 *
	 * @return bool true on access, false on no-access
	 */
	public function dataCheckPermissions($row, $user_id = 0, $check_edit = 0) {
		if (!$user_id) {
			$user_id = $_SESSION["user_id"];
		}
		if (!$this->user_cache["user_object"]) {
			$this->user_cache["user_object"] = new User_data();
			$this->user_cache["user_perm"]   = $this->user_cache["user_object"]->getUserdetailsById($user_id);
			$this->user_cache["user_groups"] = $this->user_cache["user_object"]->getUserGroups($user_id);
		}
		if ($check_edit) {
			if ($this->user_cache["user_perm"]["xs_projectmanage"] ||
				$this->user_cache["user_perm"]["xs_limited_projectmanage"]
			) {
				return true;
			} else {
				return false;
			}
		} else {
			if ($this->user_cache["user_perm"]["xs_projectmanage"] ||
				$this->user_cache["user_perm"]["xs_limited_projectmanage"] ||
				$row["manager"] == $user_id ||
				$row["executor"] == $user_id) {
					/* allow access */
					return true;
				} else {
					$arr =& $this->user_cache["user_groups"];
					$users = explode(",", $row["users"]);
					foreach ($arr as $v) {
						if (in_array("G".$v, $users)) {
							return true;
						} elseif (in_array($user_id, $users)) {
							return true;
						}
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
	/** autocomplete_project_name {{{ */
	/**
	 * Find a projectname matching the given string and echo it back, to be used in AJAX autocomplete layer
	 *
	 * @param string $str beginning of the projectname
	 *
	 * @return void
	 */
	public function autocomplete_project_name($str) {
		$like = sql_syntax("like");
		$data = array();
		$q = sprintf("select name from project where name %s '%s%%' ORDER BY name desc", $like, $str);
		$res = sql_query($q, "", 0, 1);
		if (sql_num_rows($res)) {
			/* get currval */
			$last = str_replace($str, "", sql_result($res,0));
			/* some left padding adjustments */
			$last = $str.sprintf("%0".strlen($last)."s", ($last+1));
			echo $last;
		}
	}
	/* }}} */
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
	/* getProjectHoursByUserId() {{{*/
	/**
	 * Returns array of hour registration by user and group by project
	 *
	 * @return array projects hours
	 */
	public function getProjectHoursByUserId($user_id, $begin_timestamp=0, $end_timestamp=0) {
		if (!$begin_timestamp)
			$begin_timestamp = mktime(0,0,0,date("m")-3,date("d"),date("Y"));
		if (!$end_timestamp)
			$end_timestamp = time();
		$sql = sprintf ("SELECT COUNT(project_id) AS count_project_hours, SUM(timestamp_end - timestamp_start) AS total_hours, project_id FROM hours_registration WHERE is_billable = 1 AND timestamp_start  BETWEEN ".$begin_timestamp." AND ".$end_timestamp." AND user_id=%d GROUP BY project_id ORDER BY total_hours DESC", $user_id);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			/* get non-billable hours */
			$sql2 = sprintf ("SELECT SUM(timestamp_end - timestamp_start) AS total_hours, project_id FROM hours_registration WHERE is_billable = 0 AND timestamp_start  BETWEEN ".$begin_timestamp." AND ".$end_timestamp." AND project_id=%d GROUP BY project_id ORDER BY total_hours ASC", $row["project_id"]);
			$res2 = sql_query($sql2);
			$data = sql_fetch_assoc($res2);
			$row["hours_nonbillable"] = $data["total_hours"];
			/* project name */
			$row["project_name"] = $this->getProjectNameById($row["project_id"]);
			$conversion = new Layout_conversion();
			$row["project_hours"] = $conversion->seconds_to_hours($row["total_hours"]);
			$return[] = $row;
		}
		return $return;
	}
	/* }}} */
	/* getProjectHoursByProjectId($id) {{{*/
	/**
	 * Returns array of hour registration by project and user
	 *
	 * @param int $id - project ID
	 * @param int $user_id optional filter on a specific user
	 * @param int $ts_start optional start timestamp for the list
	 * @param int $ts_end optional end timestamp for the list
	 * @return array projects hours
	 */
	public function getProjectHoursByProjectId($id, $user_id = 0, $ts_start = 0, $ts_end = 0) {
		$activities = $this->getActivities();
		foreach($activities AS $act) {
			$activity[$act["id"]] = $act;
		}
		if ($ts_start && $ts_end) {
			$timequery = sprintf("AND timestamp_start => %d AND timestamp_end <= %d", $ts_start, $ts_end);
		} else {
			$last_3_months = time()-(92*(24*3600));
			$timequery = sprintf("AND timestamp_start > %d", $last_3_months);
		}

		if ($user_id) {
			$sql = sprintf("SELECT activity_id, project_id, timestamp_start, timestamp_end, description FROM hours_registration WHERE project_id = %d %s AND user_id = %d", $id, $timequery, $user_id);
		} else {
			$sql = sprintf("SELECT activity_id, project_id, timestamp_start, timestamp_end, description FROM hours_registration WHERE project_id = %d %s", $id, $timequery);
		}
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$row["activity_name"] = $activity[$row["activity_id"]]["activity"];
			$row["project_name"] = $this->getProjectNameById($row["project_id"]);
			$row["date"] = date("d-m-Y", $row["timestamp_start"]);
			$time = $row["timestamp_end"] - $row["timestamp_start"];
			$hours = floor($time / 3600);
			$hours_remainder = $time - $hours*3600;
			$minutes = floor($hours_remainder / 60);
			$row["project_hours"] = $hours."&nbsp;".gettext("hours")."&nbsp;".$minutes."&nbsp;".gettext("minutes");
			$return[] = $row;
		}
		return $return;
	}
	/* }}} */
	/* getProjectAccessByRelation($relation_id, $user_id) {{{*/
	/**
	 * Returns array of project IDs from a relation to which a specific user has access to (manager, executor or access/users)
	 *
	 * @param int $relation_id - The ID of the relation
	 * @param int $user_id - The user_id of the currently logged in user
	 */
	public function getProjectAccessByRelation($relation_id, $user_id) {
		$sql = sprintf("SELECT id, name FROM project WHERE address_id=%1\$d AND (manager = %2\$d OR executor = %2\$d OR users IN (%2\$d))", $relation_id, $user_id);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$data[$row["id"]] = $row["name"];
		}
		return $data;
	}
	/* }}} */
	/* hasHours {{{ */
	/**
	 * Check if a project has unbilled hours
	 *
	 * @param int $project_id The project to check
	 * @param int $lfact Timestamp of last billing. Hours before this timestamp will be ignored.
	 * @param int $non_billable If set, will also take non-billable (service) hours in account
	 *
	 * @return bool True if there are hours, false otherwise
	 */
	public function hasHours($project_id, $lfact = 0, $non_billable = 0) {
		$project_id = sprintf("%d", $project_id);
		$non_billable = sprintf("%d", $non_billable);
		if (!$project_id) {
			return false;
		}
		if ($non_billable) {
			$sql = sprintf("SELECT COUNT(*) FROM hours_registration WHERE project_id = %d AND timestamp_start >= %d", $project_id, $lfact);
		} else {
			$sql = sprintf("SELECT COUNT(*) FROM hours_registration WHERE project_id = %d AND is_billable = 1 AND timestamp_start >= %d", $project_id, $lfact);
		}
		$res = sql_query($sql);
		$result = sql_result($res, 0);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	/* }}} */

}
?>
