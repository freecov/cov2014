<?
Class Project_output {
	/* constants */
	const include_dir = "classes/project/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "project";

	/* variables */

	/* methods */
	public function show_overview() {
		require(self::include_dir."show_overview.php");
	}

	public function show_info($projectid, $master=0) {
		require(self::include_dir."show_info.php");
	}

	public function pick_project() {
		require(self::include_dir."pick_project.php");
	}

	public function edit_project($projectid, $master) {
		require(self::include_dir."edit_project.php");
	}

	public function showHours() {
		require(self::include_dir."showHours.php");
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
			"project_id" => $_REQUEST["project_id"]
		));
		$output = new Layout_output();
		$output->layout_page("", 1);
		$venster = new Layout_venster(array(
			"title" => gettext("notities")
		));
		$venster->addVensterData();
			$view = new Layout_view();
			$view->addData($note_info["notes"]);
			$view->addMapping(gettext("datum"), "%human_date");
			$view->addMapping(gettext("van"), "%from_name");
			$view->addMapping(gettext("naar"), "%to_name");
			$view->addMapping(gettext("onderwerp"), "%subject");
			$view->addMapping(gettext("inhoud"), "%body");
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
		$calendar_info = $calendar_data->getAppointmentsBySearch(array("all" => 1, "project_id" => $_REQUEST["project_id"]));
		$output = new Layout_output();
		$output->layout_page("", 1);
		$venster = new Layout_venster(array(
			"title" => gettext("agenda")
		));
		$venster->addVensterData();
			$view = new Layout_view();
			$view->addData($calendar_info);
			$view->addMapping(gettext("van"), "%human_start");
			$view->addMapping(gettext("tot"), "%human_end");
			$view->addMapping(gettext("onderwerp"), "%subject");
			$view->addMapping(gettext("gebruiker"), "%user_name");
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

}
