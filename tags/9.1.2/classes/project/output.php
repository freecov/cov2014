<?php
Class Project_output {
	/* constants */
	const include_dir = "classes/project/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "project";

	/* variables */
	public $has_declaration;

	/* methods */
	public function __construct() {
		$this->has_declaration = $GLOBALS["covide"]->license["has_project_declaration"];
	}
	public function show_overview() {
		require(self::include_dir."show_overview.php");
	}

	public function show_info($projectid, $master=0) {
		require(self::include_dir."show_info.php");
	}

	public function pick_project() {
		require(self::include_dir."pick_project.php");
	}

	public function edit_project($projectid, $master, $sub_of=0) {
		require(self::include_dir."edit_project.php");
	}

	public function showHours() {
		require(self::include_dir."showHours.php");
	}
	
	public function showProjectHours() {
		require(self::include_dir."showProjectHours.php");
	}

    /* 	showNotes {{{ */
    /**
     * 	showNotes. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function showNotes() {
		$note_data = new Note_data();
		$note_info = $note_data->getNotes(array(
			"no_limit" => 1,
			"note_type" => "all",
			"user_id"   => "all",
			"project_id" => $_REQUEST["project_id"]
		));
		$output = new Layout_output();
		$output->layout_page("", 1);
		$venster = new Layout_venster(array(
			"title" => gettext("notes")
		));
		$venster->addMenuItem(gettext("new note"), sprintf("javascript: popup('index.php?mod=note&action=edit&id=0&project_id=%d')", $_REQUEST["project_id"]), 0, 0);
		$venster->generateMenuItems();
		$venster->addVensterData();
			$view = new Layout_view();
			$view->addData($note_info["notes"]);
			$view->addMapping(gettext("date"), "%human_date");
			$view->addMapping(gettext("from"), "%from_name");
			$view->addMapping(gettext("to"), "%to_name");
			$view->addMapping(gettext("subject"), "%subject");
			$view->addMapping(gettext("content"), "%body");
			$venster->addCode($view->generate_output());
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* showTodos {{{ */
	/**
	 * Show all todos on a project
	 */
	public function showTodos() {
		$todo_data = new Todo_data();
		$todoinfo  = $todo_data->getTodosByProjectId($_REQUEST["project_id"]);

		$output = new Layout_output();
		$output->layout_page("", 1);
		$venster = new Layout_venster(array(
			"title" => gettext("todos")
		));
		$venster->addMenuItem(gettext("create todo"), sprintf("javascript: popup('?mod=todo&action=edit_todo&&hide=1&project_id=%d');", $_REQUEST["project_id"]), 0, 0);
		$venster->generateMenuItems();
		$venster->addVensterData();
			$view = new Layout_view();
			$view->addData($todoinfo["todos"]);
			$view->addMapping(gettext("start date"), "%human_date_from");
			$view->addMapping(gettext("end date"), "%human_date_to");
			$view->addMapping(gettext("user"), "%username");
			$view->addMapping(gettext("subject"), "%subject");
			$view->addMapping(gettext("content"), "%body");
			$view->setHTMLField(gettext("content"));
			$venster->addCode($view->generate_output());
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
    /* }}} */

    /* 	showCal {{{ */
    /**
     * 	showCal. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function showCal() {
		$calendar_data = new Calendar_data();
		$calendar_info = $calendar_data->getAppointmentsBySearch(array("all" => 1, "project_id" => $_REQUEST["project_id"], "sortorder" => "DESC", "max_hits" => 100));
		$output = new Layout_output();
		$output->layout_page("", 1);
		$venster = new Layout_venster(array(
			"title" => gettext("calendar")
		));
		$venster->addMenuItem(gettext("new item"), sprintf("javascript: popup('?mod=calendar&action=edit&id=0&project_id=%d');", $_REQUEST["project_id"]), 0, 0);
		$venster->generateMenuItems();
		$venster->addVensterData();
			$view = new Layout_view();
			$view->addData($calendar_info);
			$view->addMapping(gettext("from"), "%human_start");
			$view->addMapping(gettext("till"), "%human_end");
			$view->addMapping(gettext("subject"), "%subject");
			$view->addMapping(gettext("user"), "%user_name");
			$venster->addCode($view->generate_output());
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
    /* }}} */

    /* 	showActivities {{{ */
    /**
     * 	showActivities. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function showActivities() {
		require(self::include_dir."show_activities.php");
	}
    /* }}} */

    /* 	hourOverview {{{ */
    /**
     * 	show total hours
     *
     */
	public function hourOverview() {
		require(self::include_dir."hourOverview.php");
	}
    /* }}} */
	
	/* exportHours {{{ */
	/**
	 * creates a .csv file and returns a download
	 *
	 * @param array $data The project data used to select what to export
	 *   This variable looks like this:
	 *   array (
	 *		"id" => projectid,
	 *		"start" => optional start time for the export,
	 *		"end"   => optional end time for the export
	 *   )
	 */
	public function exportHours($data) {
		$project_id = $data["id"];
		$projectdata = new Project_data();
		$conversion = new Layout_conversion();
		$projectinfo = $projectdata->getProjectById($project_id);
		if ($data["start"] && $data["end"]) {
			$hourlist = $projectdata->getHoursList(array("projectid"=>$project_id, "start" => $data["start"], "end" => $data["end"]));
		} else {
			$hourlist = $projectdata->getHoursList(array("projectid"=>$project_id, "lfact"=>$projectinfo[0]["lfact"]));
		}
		
		$csv = array();
		$csv[] = gettext("startdate");
		$csv[] = gettext("enddate");
		$csv[] = gettext("hours");
		$csv[] = gettext("username");
		$csv[] = gettext("activity");
		$csv[] = gettext("description");
		$csv[] = gettext("costs");
		$data = $conversion->generateCSVRecord($csv);
		unset($csv);
		
		foreach ($hourlist["items"] as $item) {
			$csv = array();
			$csv[] = $item["human_start_date"]. " " .$item["human_start_time"];
			$csv[] = $item["human_end_date"]. " " .$item["human_end_time"];
			$csv[] = $item["hours_bill"];
			$csv[] = $item["user_name"];
			$csv[] = $item["activityname"];
			$csv[] = html_entity_decode(strip_tags($item["description"]), null, "UTF-8");
			$csv[] = $item["costs"];
			$data .= $conversion->generateCSVRecord($csv);
			unset($csv);
		}
		header("Content-Transfer-Encoding: binary");
		header("Content-Type: text/plain; charset=UTF-8");

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header("Content-Disposition: filename=covide_hourlist.csv"); //msie 5.5 header bug
		}else{
			header("Content-Disposition: attachment; filename=covide_hourlist.csv");
		}
		echo $data;
		exit();
		
	}
	/* }}} */
	public function hourStats() {
		require(self::include_dir."hourStats.php");
	}
	
	/* exportHoursXML {{{ */
	/**
	 * creates a .xml file and returns a download
	 *
	 * @param array $data The project data used to select what to export
	 *   This variable looks like this:
	 *   array (
	 *		"id" => projectid,
	 *		"start" => optional start time for the export,
	 *		"end"   => optional end time for the export
	 *   )
	 */
	public function exportHoursXML($data) {
		$project_id = $data["id"];
		$projectdata = new Project_data();
		$conversion = new Layout_conversion();
		$projectinfo = $projectdata->getProjectById($project_id);
		if ($data["start"] && $data["end"]) {
			$hourlist = $projectdata->getHoursList(array("projectid"=>$project_id, "start" => $data["start"], "end" => $data["end"]));
		} else {
			$hourlist = $projectdata->getHoursList(array("projectid"=>$project_id, "lfact"=>$projectinfo[0]["lfact"]));
		}
		
		$string = "<export>\n"; 
		foreach ($hourlist["items"] as $item) {
			$string .= "<item>\n";
			$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "startdate", $item["human_start_date"]. " " .$item["human_start_time"]);
			$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "enddate", $item["human_end_date"]. " " .$item["human_end_time"]);
			$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "hours", $item["hours_bill"]);
			$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "username", htmlentities($item["user_name"]));
			$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "activity", htmlentities($item["activityname"]));
			$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "description", htmlentities(strip_tags($item["description"]), null, "UTF-8"));
			$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "costs", $item["costs"]);
			$string .= "</item>\n";
		}
		$string .= "</export>"; 
		
		$xml = new SimpleXMLElement($string);
		$xmldata = $xml->asXML();

		header("Content-Transfer-Encoding: binary");
		header("Content-Type: text/xml; charset=UTF-8");

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header("Content-Disposition: filename=covide_hourlist.xml"); //msie 5.5 header bug
		}else{
			header("Content-Disposition: attachment; filename=covide_hourlist.xml");
		}
		echo $xmldata;
		exit();
		
	}
	/* }}} */
}
