<?php
/**
 * Covide Groupware-CRM Dimdim data module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
Class Dimdim_data {
	/* constants */
	const include_dir = "classes/dimdim/inc/";

	/* methods */
	/* save_meeting {{{ */
	/**
	 * Saves a meeting into the db
	 *
	 * @param array $data - Contains all $_REQUEST data
	 */
	public function save_meeting($data) {
		$meeting = $data["dimdim"];
		$meeting["external_attendees"] = $data["appointment"]["relmail"];
		$sql = sprintf("INSERT INTO dimdim (id, name, description, room, attendees, external_attendees, startdate, enddate) 
						VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						$meeting["name"], $meeting["description"], $meeting["room"], $meeting["attendees"],
						serialize($meeting["external_attendees"]), $meeting["startdate"], $meeting["enddate"]
		);
		$query = sql_query($sql);
		$meeting_id = sql_insert_id($query);


		$output = new Layout_output();
		$output->load_javascript(self::include_dir."save_meeting.js");
		$output->start_javascript();
		$output->addCode("setTimeout('save_meeting(".$meeting_id.", \"".$meeting["name"]."\")');\n");
		$output->end_javascript();
		$output->layout_page_end();
		echo $output->generate_output();
	}
	/* }}} */
	
	/* getMeetingByID {{{ */
	/**
	 * Gets all meeting data by its ID
	 *
	 * @param int $id - The ID of the meeting
	 */
	public function getMeetingById($id) {
		$sql = sprintf("SELECT * FROM dimdim WHERE id = %d", $id);
		$result = sql_query($sql);
		while ($row = sql_fetch_assoc($result)) {
			$data = $row;
		}
		return $data;
	}
	/* }}} */
	
	/* deleteMeeting {{{ */
	/**
	 * Deletes a meeting by its ID
	 *
	 * @param int $id - The ID of the meeting
	 */
	public function deleteMeeting($id) {
		/* Delete meeting-connection from appointment */
		$sql = sprintf("UPDATE calendar SET dimdim_meeting = NULL WHERE dimdim_meeting = %d", $id);
		$result = sql_query($sql);
		/* Delete meeting in general */
		$sql = sprintf("DELETE FROM dimdim WHERE id = %d", $id);
		return sql_query($sql);
	}
	/* }}} */
}
?>