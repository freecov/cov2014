<?php
/**
 * Covide Groupware-CRM Desktop data class
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Desktop_data {
	/* constants */
	/* variables */
	/* methods */

	/* save_notes() {{{ */
	/**
	 * Store a users personal notes to desktop.
	 *
	 * If the function is called because the user
	 * was working in the webinterface, we are in a popup
	 * window, so we call a little output handling to close the window
	 *
	 * @param array the form values. To construct this yourself: (array)data => (int)user_id=id, (string)contents=the notes
	 * @return bool true on success, false on failure
	 */
	public function save_notes($data) {
		/* sanitize the input */
		$comment = preg_replace("'([\r\n])[\s]+'", " ", $data["contents"]);
		/* put in db */
		$sql = sprintf("UPDATE users SET comment = '%s' WHERE id = %d", $comment, $data["user_id"]);
		$res = sql_query($sql);
		/* lil output to close the window */
		$output = new Layout_output();
		$output->layout_page("", 1);
		$output->start_javascript();
			$output->addCode(
				"
				if (parent) {
					parent.location.href = parent.location.href;
					closepopup();
				}
				"
			);
		$output->end_javascript();
		$output->exit_buffer();
		return true;
	}
	/* }}} */
	/* getOwnNotes() {{{ */
	/**
	 * Get the personal notes from database
	 *
	 * @param int The user_id of the user we want to fetch the notes
	 * @return string The contents of the database field comment
	 */
	public function getOwnNotes($user_id) {
		$sql = sprintf("SELECT comment FROM users WHERE id=%d", $user_id);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		return $row["comment"];
	}
	/* }}} */
	/* getRSSfeeds() {{{ */
	/**
	 * Get all subscribed RSS feeds from db
	 *
	 * @param array the id, name, url and user_id of the feeds
	 */
	public function getRSSfeeds() {
		$sql = "SELECT * from rssfeeds";
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$return[] = $row;
		}
		return $return;
	}
	/* }}} */
	/* getRSSitems() {{{ */
	/**
	 * Retrieve rss items for a specific feed, limited by some count
	 *
	 * @param int The id of the feed.
	 * @param int The ammount of items to return. Newest first
	 * @return array The complete items as they are in the database
	 */
	public function getRSSitems($feedid, $count) {
		$rss_data = new Rss_data();
		return ($rss_data->getRSSitems($feedid, $count));
	}
	/* }}} */
	/* getMailInfo {{{ */
	/**
	 * Get the information so we can show what kind of mail there is for the user
	 *
	 * @param int The user_id we want to know something about
	 * @return array total count of new mail, and new mail count per folder
	 */
	public function getMailInfo($user_id) {
		$blocked_folders = array(
			"Verzonden-Items",
			"Verwijderde-Items",
			"Concepten",
			"Draft messages",
			"Deleted messages",
			"Sent messages"
		);
		$email_data = new Email_data();
		$email_info = $email_data->getFolders(array("user_id" => $user_id, "count" => 1), 0);

		$email_shared = $email_data->getSharedFolderAccess($user_id);

		$count = 0;
		$desktopinfo = array("count" => 0);
		foreach ($email_info as $val) {
			if ($val["unread"] && !in_array($val["name"], $blocked_folders)) {
				$desktopinfo["count"]+= $val["unread"];
				$desktopinfo["folders"][] = $val;
			}
		}
		foreach ($email_shared as $f) {
			$val = $email_data->getFolder($f["folder_id"], 1);
			$val["name"].= " (".$f["username"].")";
			if ($val["unread"] && !in_array($val["name"], $blocked_folders)) {
				$desktopinfo["count"]+= $val["unread"];
				$desktopinfo["folders"][] = $val;
			}
		}
		return $desktopinfo;
	}
	/* }}} */
	/* getAlertInfo {{{ */
	/**
	 * Get all the counts for modules we want to alert for
	 *
	 * @param int return_always if set, always return the alert array data
	 */
	public function getAlertInfo($return_always = 0) {
		/* get user settings */
		$user_data = new User_data();
		$userinfo = $user_data->getUserdetailsById($_SESSION["user_id"]);
		/* get note data */
		$note_data = new Note_data();
		$note_count = $note_data->getNotecountByUserId($_SESSION["user_id"]);
		if ($note_count["new"]) {
			$alertinfo["notes"] = $note_count["new"];
		}
		unset($note_data, $note_count);
		/* get calendar data */
		$calendar_data = new Calendar_data();
		$items_arr = $calendar_data->_get_appointments($_SESSION["user_id"], date("m"), date("d"), date("Y"));
		$calendarcount = 0;
		if (count($calendar_data->calendar_items)) {
			foreach($calendar_data->calendar_items as $v) {
				if ($v["important"] && (mktime() > $v["ststamp"]-$v["notifytime"])) {
					$calendarcount++;
				}
			}
		}
		if ($calendarcount) {
			$alertinfo["calendar"] = $calendarcount;
		}
		unset($items_arr, $calendar_data);
		/* todo data */
		$todo_data = new Todo_data();
		$todo_arr = $todo_data->getTodosByUserId($_SESSION["user_id"]);
		$todoalertcount = 0;
		foreach ($todo_arr as $v) {
			if ($v["is_alert"]) {
				$alertcount++;
				$todoalertcount++;
			}
		}
		if ($todoalertcount) {
			$alertinfo["todo"] = $todoalertcount;
		}
		unset($todo_arr, $todo_data);
		/* rss and email need desktop data object */
		$desktop_data = new Desktop_data();
		$email_info = $desktop_data->getMailInfo($_SESSION["user_id"]);
		if (is_array($email_info["folders"])) {
			foreach ($email_info["folders"] as $v) {
				if ($v["unread"]) {
					$alertinfo["email"][$v["name"]] = $v["unread"];
				}
			}
		}
		unset ($email_info);
		if ($GLOBALS["covide"]->license["has_issues"] && $userinfo["xs_issuemanage"]) {
			$support_data = new Support_data();

			/* support issues */
			$supportinfo = $support_data->getSupportItems(array("user_id" => $_SESSION["user_id"], "active" => 1, "nolimit" => 1));
			if ($supportinfo["count"]) {
				$alertinfo["supportitems"] = $supportinfo["count"];
			}
			/* external support calls */
			$supportcalls = count($support_data->getExternalIssues());
			if ($supportcalls) {
				$alertinfo["ext_supportcalls"] = $supportcalls;
			}

			unset($supportinfo, $support_data);
		}
		if ($GLOBALS["covide"]->license["has_cms"] && $userinfo["xs_addressmanage"]) {
			$address_data = new Address_data();
			$address_list = $address_data->getRelationsList(array("addresstype" => "relations", "cmsforms" => 1));
			if ($address_list["total_count"]) {
				$alertinfo["crmforms"] = array("count" => $address_list["total_count"]);
			}
		}
		if ($GLOBALS["covide"]->license["has_project"]) {
			$project_data = new Project_data();
			$project_info = $project_data->getExceededProjects($_SESSION["user_id"]);
			$exceeded = count($project_info);
			if ($exceeded) {
				$alertinfo["project"] = $exceeded;
			}
		}
		if ((md5(serialize($alertinfo)) != $_SESSION["alertmd5"]) || $return_always == 1) {
			return $alertinfo;
		}
	}
	/* }}} */
	/* saveLayout {{{ */
	/**
	 * Save block layout to users settings so we can reproduce it
	 *
	 * @param array $request contents of $_REQUEST
	 *
	 * @return void
	 */
	public function saveLayout($request) {
		$user_id = $_SESSION["user_id"];
		$user_data = new User_data();
		$current_settings = $user_data->getUserdetailsById($user_id);
		$cur_dash = $current_settings["dashboardlayout"];
		$cur_dash = unserialize($cur_dash);
		$cur_dash[$request["col"]] = $request["block"];
		$cur_dash = serialize($cur_dash);
		$sql = sprintf("UPDATE users SET dashboardlayout = '%s' WHERE id = %d", $cur_dash, $user_id);
		$res = sql_query($sql);
	}
	/* }}} */
	/* saveAddBlock {{{ */
	/**
	 * Save new block settings to db.
	 * This function is called from the 'addblock' form
	 *
	 * @param array $data The request data
	 */
	public function saveAddBlock($data) {
		/* for what user ? */
		$user_id = sprintf("%d", $data["dashboard"]["user_id"]);
		/* grab their current settings */
		$user_data = new User_data();
		$current_settings = $user_data->getUserdetailsById($user_id);
		$cur_dash = $current_settings["dashboardlayout"];
		$cur_dash = unserialize($cur_dash);
		$used_blocks = array();
		/* put all the used blocks in a flat array so we can easily search it */
		foreach ($cur_dash as $col => $blocks) {
			$used_blocks = array_merge($used_blocks, $blocks);
		}
		if (!in_array($data["dashboard"]["addblock"], $used_blocks)) {
			//add it to the first column
			$col1 = array_reverse($cur_dash["column1"]);
			$col1[] = $data["dashboard"]["addblock"];
			$cur_dash["column1"] = array_reverse($col1);
			$cur_dash = serialize($cur_dash);
			$sql = sprintf("UPDATE users SET dashboardlayout = '%s' WHERE id = %d", $cur_dash, $user_id);
			$res = sql_query($sql);
		}
		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("
			parent.document.location.href='index.php?mod=desktop';
		");
		$output->end_javascript();
		$output->exit_buffer();
	}
	/* }}} */
}
?>
