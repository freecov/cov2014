<?php
/**
 * Covide Groupware-CRM Project module output.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */
Class Project_output {
	/* constants */
	const include_dir = "classes/project/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "project";

	/* variables */
	/**
	 * @var bool set to 1 if declaration module is enabled, 0 otherwise
	 */
	public $has_declaration;

	/* methods */
	/* __construct {{{ */
	/**
	 * Class contstructor that sets the class variable $has_declaration
	 *
	 * @return void
	 */
	public function __construct() {
		$this->has_declaration = $GLOBALS["covide"]->license["has_project_declaration"];
	}
	/* }}} */
	/* show_overview {{{ */
	/**
	 * Show list of projects based on search and permissions
	 *
	 * @return void
	 */
	public function show_overview() {
		require(self::include_dir."show_overview.php");
	}
	/* }}} */
	/* show_info {{{ */
	/**
	 * Show projectinformation
	 *
	 * @param int $projectid The project to show
	 * @param int $master if set to 1 will grab masterproject information
	 *
	 * @return void
	 */
	public function show_info($projectid, $master=0) {
		require(self::include_dir."show_info.php");
	}
	/* }}} */
	/* pick_project {{{ */
	/**
	 * show screen to pick a project, used by various modules to link a project
	 *
	 * @return void
	 */
	public function pick_project() {
		require(self::include_dir."pick_project.php");
	}
	/* }}} */
	/* edit_project {{{ */
	/**
	 * Show screen to input/edit a project
	 *
	 * @param int $projectid The project to edit or 0 for a new project
	 * @param int $master 1 if it's a master project, 0 for normal projects
	 * @param int $sub_of if set the new/edited project is a child of this master project id
	 *
	 * @return void
	 */
	public function edit_project($projectid, $master, $sub_of=0) {
		require(self::include_dir."edit_project.php");
	}
	/* }}} */
	/* showHours {{{ */
	/**
	 * Show project card including hours and extra information
	 *
	 * @return void
	 */
	public function showHours() {
		require(self::include_dir."showHours.php");
	}
	/* }}} */
	/* showProjectHours {{{ */
	/**
	 * Show all hours for a project
	 *
	 * @return void
	 */
	public function showProjectHours() {
		require(self::include_dir."showProjectHours.php");
	}
	/* }}} */
	/* 	showNotes {{{ */
	/**
	 * Show notes linked to a project
	 *
	 * @return void
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
			$view->addMapping(gettext("actions"), "%%complex_actions");
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src" => "info",
					"link" => array("javascript: popup('index.php?mod=note&action=message&hidenav=1&msg_id=", "%id", "');"),
				),
			));
			$view->setHTMLField("body");
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
	 *
	 * @return void
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
			$view->addMapping(gettext("actions"), "%%complex_actions");
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src" => "info",
					"link" => array("javascript: popup('index.php?mod=todo&action=edit_todo&hide=1&todoid=", "%id", "');")
				),
			));
			$view->setHTMLField("body");
			$venster->addCode($view->generate_output());
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* 	showCal {{{ */
	/**
	 * Show calendaritems linked to a project
	 *
	 * @return void
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
			$view->addMapping(gettext("actions"), "%%complex_actions");
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src" => "info",
					"link" => array("javascript: popup('index.php?mod=calendar&action=edit&hidenav=1&id=", "%id", "&user=", "%user_id", "');")
				),
			));
			$venster->addCode($view->generate_output());
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* showSales {{{ */
	/**
	 * Show all salesitems linked to a project
	 *
	 * @return void
	 */
	public function showSales() {
		$sales_data = new Sales_data();
		$sales_info = $sales_data->getSalesBySearch(array("no_limit" => 1, "project_id" => $_REQUEST["project_id"]));
		$output = new Layout_output();
		$output->layout_page("", 1);
		$venster = new Layout_venster(array(
			"title" => gettext("Sales")
		));
		$venster->addMenuItem(gettext("new item"), sprintf("javascript: popup('?mod=sales&action=edit&noiface=1&id=0&project_id=%d');", $_REQUEST["project_id"]), 0, 0);
		$venster->generateMenuItems();
		$venster->addVensterData();
			$view = new Layout_view();
			$view->addData($sales_info["data"]);
			$view->addMapping(gettext("salesitem"), "%subject");
			$view->addMapping(gettext("description"), "%description");
			$view->addMapping(gettext("contact"), "%%complex_address");
			$view->addMapping(gettext("project"), "%%complex_project");
			$view->addMapping(gettext("prospect"), "%h_timestamp_prospect");
			$view->addMapping(gettext("quote"), "%h_timestamp_proposal");
			$view->addMapping(gettext("order/commission"), "%h_timestamp_order");
			$view->addMapping(gettext("invoice"), "%h_timestamp_invoice");
			$view->addMapping(gettext("user"), "%username");
			$view->addMapping(gettext("score"), array("%expected_score", "&#037;"), "right");
			$view->addMapping(gettext("price"), "%total_sum", "right");
			$view->addMapping(gettext("actions"), "%%complex_actions");
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src" => "info",
					"link" => array("javascript: popup('index.php?mod=sales&action=edit&noiface=1&id=", "%id", "');")
				),
			));
			$view->setHTMLField(gettext("description"));
			$view->defineComplexMapping("complex_project", array(
				array(
					"type"  => "text",
					"text"  => "%h_project",
					"check" => "%has_project"
				)
			));

			$view->defineComplexMapping("complex_address", array(
				array(
					"type" => "multilink",
					"link" => array("index.php?mod=address&action=relcard&id=", "%all_address_ids"),
					"text" => "%all_address_names",
					"check" => "%all_address_ids"
				),
			));
			$venster->addCode($view->generate_output());
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* 	showActivities {{{ */
	/**
	 * Show configured activities and possibility to edit
	 *
	 * @return void
	 */
	public function showActivities() {
		require(self::include_dir."show_activities.php");
	}
	/* }}} */
	/* showCosts {{{ */
	/**
	 * Show other project costs and ability to edit them
	 *
	 * @return void
	 */
	public function showCosts() {
		require(self::include_dir."show_costs.php");
	}
	/* }}} */
	/* hourOverview {{{ */
	/**
	 * show total hours
	 *
	 * @return void
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
			$hourlist = $projectdata->getHoursList(array("projectid" => $project_id, "start" => $data["start"], "end" => $data["end"]));
			$bulklist = $projectdata->getHoursList(array("projectid" => $project_id, "start" => $data["start"], "end" => $data["end"], "bulk" => 1));
			$misclist = $projectdata->getHoursList(array("projectid" => $project_id, "start" => $data["start"], "end" => $data["end"], "misc" => 1));
		} else {
			$hourlist = $projectdata->getHoursList(array("projectid" => $project_id, "lfact" => $projectinfo[0]["lfact"]));
			$bulklist = $projectdata->getHoursList(array("projectid" => $project_id, "lfact" => $projectinfo[0]["lfact"], "bulk" => 1));
			$misclist = $projectdata->getHoursList(array("projectid" => $project_id, "misc" => 1));
		}
		if (!is_array($hourlist["items"])) { $hourlist["items"] = array(); }
		if (!is_array($bulklist["items"])) { $bulklist["items"] = array(); }
		if (!is_array($misclist["items"])) { $misclist["items"] = array(); }

		$csv = array();
		$csv[] = gettext("normal hours");
		$csv[] = gettext("bulk hours");
		$csv[] = gettext("other project costs");
		$csv[] = gettext("startdate");
		$csv[] = gettext("enddate");
		$csv[] = gettext("hours");
		$csv[] = gettext("username");
		$csv[] = gettext("activity");
		$csv[] = gettext("description");
		$csv[] = gettext("purchase");
		$csv[] = gettext("marge");
		$csv[] = gettext("tarif");
		$data = $conversion->generateCSVRecord($csv);
		unset($csv);

		foreach ($hourlist["items"] as $item) {
			$csv = array();
			$csv[] = "1";
			$csv[] = "0";
			$csv[] = "0";
			$csv[] = $item["human_start_date"]. " " .$item["human_start_time"];
			$csv[] = $item["human_end_date"]. " " .$item["human_end_time"];
			$csv[] = $item["hours_bill"];
			$csv[] = $item["user_name"];
			$csv[] = $item["activityname"];
			$csv[] = html_entity_decode(strip_tags($item["description"]), null, "UTF-8");
			$csv[] = $item["purchase"];
			$csv[] = $item["marge"];
			$csv[] = $item["costs"];
			$data .= $conversion->generateCSVRecord($csv);
			unset($csv);
		}
		foreach ($bulklist["items"] as $item) {
			$csv = array();
			$csv[] = "0";
			$csv[] = "1";
			$csv[] = "0";
			$csv[] = $item["human_date"]. " " .$item["human_start_time"];
			$csv[] = $item["human_end_date"]. " " .$item["human_end_time"];
			$csv[] = $item["hours_bill"];
			$csv[] = $item["user_name"];
			$csv[] = $item["activityname"];
			$csv[] = html_entity_decode(strip_tags($item["description"]), null, "UTF-8");
			$csv[] = $item["purchase"];
			$csv[] = $item["marge"];
			$csv[] = $item["costs"];
			$data .= $conversion->generateCSVRecord($csv);
			unset($csv);
		}
		foreach ($misclist["items"] as $item) {
			$csv = array();
			$csv[] = "0";
			$csv[] = "0";
			$csv[] = "1";
			$csv[] = $item["human_start_date"]. " " .$item["human_start_time"];
			$csv[] = $item["human_end_date"]. " " .$item["human_end_time"];
			$csv[] = $item["hours_bill"];
			$csv[] = $item["user_name"];
			$csv[] = $item["activityname"];
			$csv[] = html_entity_decode(strip_tags($item["description"]), null, "UTF-8");
			$csv[] = $item["purchase"];
			$csv[] = $item["marge"];
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
	/* hourStats {{{ */
	/**
	 * What does this do ?
	 *
	 * @return void
	 */
	public function hourStats() {
		require(self::include_dir."hourStats.php");
	}
	/* }}} */
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
			$hourlist = $projectdata->getHoursList(array("projectid" => $project_id, "start" => $data["start"], "end" => $data["end"]));
			$bulklist = $projectdata->getHoursList(array("projectid" => $project_id, "start" => $data["start"], "end" => $data["end"], "bulk" => 1));
			$misclist = $projectdata->getHoursList(array("projectid" => $project_id, "start" => $data["start"], "end" => $data["end"], "misc" => 1));
		} else {
			$hourlist = $projectdata->getHoursList(array("projectid" => $project_id, "lfact" => $projectinfo[0]["lfact"]));
			$bulklist = $projectdata->getHoursList(array("projectid" => $project_id, "lfact" => $projectinfo[0]["lfact"], "bulk" => 1));
			$misclist = $projectdata->getHoursList(array("projectid" => $project_id, "misc" => 1));
		}
		if (!is_array($hourlist["items"])) { $hourlist["items"] = array(); }
		if (!is_array($bulklist["items"])) { $bulklist["items"] = array(); }
		if (!is_array($misclist["items"])) { $misclist["items"] = array(); }

		$string = "<export>\n\t<items>\n";
		foreach ($hourlist["items"] as $item) {
			$string .= "\t\t<item type=\"normal_hours\">\n";
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "startdate", $item["human_start_date"]. " " .$item["human_start_time"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "enddate", $item["human_end_date"]. " " .$item["human_end_time"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "hours", $item["hours_bill"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "username", htmlentities($item["user_name"]));
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "activity", htmlentities($item["activityname"]));
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "description", htmlentities(strip_tags($item["description"]), null, "UTF-8"));
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "purchase", $item["purchase"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "marge", $item["marge"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "costs", $item["costs"]);
			$string .= "\t\t</item>\n";
		}
		foreach ($bulklist["items"] as $item) {
			$string .= "\t\t<item type=\"bulk_hours\">\n";
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "startdate", $item["human_date"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "enddate", $item["human_end_date"]. " " .$item["human_end_time"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "hours", $item["hours_bill"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "username", htmlentities($item["user_name"]));
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "activity", htmlentities($item["activityname"]));
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "description", htmlentities(strip_tags($item["description"]), null, "UTF-8"));
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "purchase", $item["purchase"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "marge", $item["marge"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "costs", $item["costs"]);
			$string .= "\t\t</item>\n";
		}
		foreach ($misclist["items"] as $item) {
			$string .= "\t\t<item type=\"misc_items\">\n";
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "startdate", $item["human_start_date"]. " " .$item["human_start_time"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "enddate", $item["human_end_date"]. " " .$item["human_end_time"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "hours", $item["hours_bill"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "username", htmlentities($item["user_name"]));
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "activity", htmlentities($item["activityname"]));
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "description", htmlentities(strip_tags($item["description"]), null, "UTF-8"));
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "purchase", $item["purchase"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "marge", $item["marge"]);
			$string .= sprintf("\t\t\t<%1\$s>%2\$s</%1\$s>\n", "costs", $item["costs"]);
			$string .= "\t\t</item>\n";
		}
		$string .= "\t</items>\n</export>";

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
	/* hoursTable {{{ */
	/**
	 * Helper function to create table with registered hours
	 *
	 * @param array $options user_id, timestamp_start and timestamp_end
	 *
	 * @return string HTML for the table
	 */
	public function hoursTable($options) {
			$user_id = $options["user_id"];
			$project_timestamp_start = $options["timestamp_start"];
			$month = $options["month"];
			$day = $options["day"];
			$year = $options["year"];
			$e_month = $options["e_month"];
			$e_day = $options["e_day"];
			$e_year = $options["e_year"];
			$project_data = new Project_data();
			$table = new Layout_table();
			$table = new Layout_table(array("cellspacing" => 1, "width" => "100%", "style" => "border: 0px solid #CCC"));
			$table->addTableRow();
				$table->insertTableData(date("F", $project_timestamp_start), array("colspan" => 8, "width" => "1%", "style" => "border: 0px solid #CCC"), "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("week"), array("width" => "1%", "style" => "border: 0px solid #CCC"), "header");
				$table->insertTableData(gettext("sun"), array("width" => "14%", "style" => "border: 0px solid #CCC"), "header");
				$table->insertTableData(gettext("mon"), array("width" => "14%", "style" => "border: 0px solid #CCC"), "header");
				$table->insertTableData(gettext("tue"), array("width" => "14%", "style" => "border: 0px solid #CCC"), "header");
				$table->insertTableData(gettext("wed"), array("width" => "14%", "style" => "border: 0px solid #CCC"), "header");
				$table->insertTableData(gettext("thu"), array("width" => "14%", "style" => "border: 0px solid #CCC"), "header");
				$table->insertTableData(gettext("fri"), array("width" => "14%", "style" => "border: 0px solid #CCC"), "header");
				$table->insertTableData(gettext("sat"), array("width" => "14%", "style" => "border: 0px solid #CCC"), "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(date("W", mktime(0, 0, 0, $month, $day, $year)), "", "header");
				$daystoskip = date("w", mktime(0, 0, 0, $month, $day, $year));
				$dow = $daystoskip;
				for ($i = 0; $i < $daystoskip; $i++) {
					$table->insertTableData("&nbsp;", array("style" => "border: 0px solid #CCC"));
				}
				if ($month < $e_month) {
					$until = date("t", mktime(0, 0, 0, $month, $day, $year));
				} else {
					$until = $e_day;
				}
				for ($i = $day; $i <= $until; $i++) {

					$proj_info = $project_data->getProjectHoursByUserId($user_id, mktime(0, 0, 0, $month, ($i), $year), mktime(23, 59, 59, $month, ($i), $year));
					$table->addTableData(array("style" => "vertical-align: top"), "data");
						$table->addTag("span", array("style" => "text-decoration: underline;"));
						$table->addCode(($i)."-$month-$year");
						$table->endTag("span");
						$table->addTag("br");
						if (is_array($proj_info)) {
							foreach ($proj_info as $v) {
								$table->addCode(str_replace(" ", "&nbsp;", "<b>".$v["project_name"]."</b><br>".$v["project_hours"]));
								$table->addTag("br");
							}
						}
					$table->endTableData();
					$dow++;
					if ($dow==7) {
						if (!($i == date("t", mktime(0, 0, 0, date("m"), 1, date("Y"))))) {
							$table->endTableRow();
							$table->addTableRow();
							$table->insertTableData(date("W", mktime(0, 0, 0, $month, ($i+7), $year)), "", "header");
						}
						$dow=0;
					}
				}
				// Complete fields of calander when month does not end on saturday
				if ($dow != 0) {
					for ($i = 0; $i != 7 - $dow; $i++) {
						$table->insertTableData("&nbsp;", array("style" => "border: 0px solid #CCC"));
					}
				}
			$table->endTableRow();
		$table->endTable();
		return $table->generate_output();
	}
	/* }}} */
	/* hoursUserPerDay {{{ */
	/**
	 * Show hours made by a user in a calendar
	 *
	 * @return void
	 */
	public function hoursUserPerDay() {
		if ($_REQUEST["regitems"]["start_day"]) {
			$regitem_start_day = $_REQUEST["regitems"]["start_day"];
		} else {
			$regitem_start_day = date("d");
		}
		if ($_REQUEST["regitems"]["start_month"]) {
			$regitem_start_month = $_REQUEST["regitems"]["start_month"];
		} else {
			$regitem_start_month = date("m")-1;
		}
		if ($_REQUEST["regitems"]["start_year"]) {
			$regitem_start_year = $_REQUEST["regitems"]["start_year"];
		} else {
			$regitem_start_year = date("Y");
		}
		if ($_REQUEST["regitems"]["end_day"]) {
			$regitem_end_day = $_REQUEST["regitems"]["end_day"];
		} else {
			$regitem_end_day = date("d");
		}
		if ($_REQUEST["regitems"]["end_month"]) {
			$regitem_end_month = $_REQUEST["regitems"]["end_month"];
		} else {
			$regitem_end_month = date("m");
		}
		if ($_REQUEST["regitems"]["end_year"]) {
			$regitem_end_year = $_REQUEST["regitems"]["end_year"];
		} else {
			$regitem_end_year = date("Y");
		}
		$user_id = $_REQUEST["user_id"];
		$project_timestamp_start = mktime(0,0,0,$regitem_start_month,$regitem_start_day,$regitem_start_year);
		$project_timestamp_end = mktime(0,0,0,$regitem_end_month,$regitem_end_day,$regitem_end_year);
		$project_data = new Project_data();
		$month = date("m", $project_timestamp_start);
		$day   = date("d", $project_timestamp_start);
		$year  = date("Y", $project_timestamp_start);

		$e_month = date("m", $project_timestamp_end);
		$e_day   = date("d", $project_timestamp_end);
		$e_year  = date("Y", $project_timestamp_end);

		$days = array();
		$months = array();
		$years = array();
		for ($i = 1; $i <= 31; $i++) {
			$days[$i] = $i;
		}
		for ($i = 1; $i <= 12; $i++) {
			$months[$i] = $i;
		}
		for ($i = date("Y")-5; $i <= date("Y")+1; $i++) {
			$years[$i] = $i;
		}

		$output = new Layout_output();
		$output->layout_page("hours", 1);
		$venster_settings = array(
			"title" => gettext("hours"),
			"subtitle" => date("d-m-Y", $project_timestamp_start)." - ".date("d-m-Y", $project_timestamp_end)
		);
		$venster = new Layout_venster($venster_settings);
		$venster->addVensterData();
			$venster->addTag("form", array("method" => "get", "action" => "index.php", "id" => "regitemsfrm"));
			$venster->addHiddenField("mod", "project");
			$venster->addHiddenField("action", "hoursuserperday");
			$venster->addHiddenField("user_id", $user_id);
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("start"), "", "");
				$table->addTableData();
					$table->addSelectField("regitems[start_day]", $days, $regitem_start_day);
					$table->addSelectField("regitems[start_month]", $months, $regitem_start_month);
					$table->addSelectField("regitems[start_year]", $years, $regitem_start_year);
				$table->endTableData();
				$table->insertTableData("", "", "");
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("end"), "", "");
				$table->addTableData();
					$table->addSelectField("regitems[end_day]", $days, $regitem_end_day);
					$table->addSelectField("regitems[end_month]", $months, $regitem_end_month);
					$table->addSelectField("regitems[end_year]", $years, $regitem_end_year);
				$table->endTableData();
				$table->addTableData();
					$table->insertAction("forward", gettext("submit"), "javascript:document.getElementById('regitemsfrm').submit();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTableData();
			$table->endTable();
			$venster->addCode($table->generate_output());
			$venster->endTag("form");

		$options = array(
			"user_id" => $user_id,
			"e_month" => $e_month,
			"e_day"   => $e_day,
			"e_year"  => $e_year
		);
		if ($month < $e_month) {
			for ($i = $month; $i <= $e_month; $i++) {
				$options["month"] = $i;
				$options["year"] = $year;
				if ($i == $month) {
					$options["day"] = $day;
				} else {
					$options["day"] = 1;
				}
				$options["timestamp_start"] = mktime(0, 0, 0, $options["month"], 1, $year);
				$venster->addCode($this->hoursTable($options));
			}
		} else {
			$options["timestamp_start"] = $project_timestamp_start;
			$options["month"] = $month;
			$options["day"] = $day;
			$options["year"] = $year;
			$venster->addCode($this->hoursTable($options));
		}

		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* hoursUserPerProject {{{ */
	/**
	 * Show hours per user per project
	 *
	 * @return void
	 */
	public function hoursUserPerProject() {
		if ($_REQUEST["regitems"]["start_day"]) {
			$regitem_start_day = $_REQUEST["regitems"]["start_day"];
		} else {
			$regitem_start_day = date("d");
		}
		if ($_REQUEST["regitems"]["start_month"]) {
			$regitem_start_month = $_REQUEST["regitems"]["start_month"];
		} else {
			$regitem_start_month = date("m")-1;
		}
		if ($_REQUEST["regitems"]["start_year"]) {
			$regitem_start_year = $_REQUEST["regitems"]["start_year"];
		} else {
			$regitem_start_year = date("Y");
		}
		if ($_REQUEST["regitems"]["end_day"]) {
			$regitem_end_day = $_REQUEST["regitems"]["end_day"];
		} else {
			$regitem_end_day = date("d");
		}
		if ($_REQUEST["regitems"]["end_month"]) {
			$regitem_end_month = $_REQUEST["regitems"]["end_month"];
		} else {
			$regitem_end_month = date("m");
		}
		if ($_REQUEST["regitems"]["end_year"]) {
			$regitem_end_year = $_REQUEST["regitems"]["end_year"];
		} else {
			$regitem_end_year = date("Y");
		}
		$user_id = $_REQUEST["user_id"];
		$project_timestamp_start = mktime(0,0,0,$regitem_start_month,$regitem_start_day,$regitem_start_year);
		$project_timestamp_end = mktime(0,0,0,$regitem_end_month,$regitem_end_day,$regitem_end_year);
		$project_data = new Project_data();
		$proj_info = $project_data->getProjectHoursByUserId($user_id, $project_timestamp_start, $project_timestamp_end);

		$days = array();
		$months = array();
		$years = array();
		for ($i = 1; $i <= 31; $i++) {
			$days[$i] = $i;
		}
		for ($i = 1; $i <= 12; $i++) {
			$months[$i] = $i;
		}
		for ($i = date("Y")-5; $i <= date("Y")+1; $i++) {
			$years[$i] = $i;
		}
		$venster_settings = array(
			"title" => gettext("hours"),
			"subtitle" => date("d-m-Y", $project_timestamp_start)." - ".date("d-m-Y", $project_timestamp_end)
		);
		$venster = new Layout_venster($venster_settings);
		unset($venster_settings);
		$venster->addVensterData();
			$venster->addTag("form", array("method" => "get", "action" => "index.php", "id" => "regitemsfrm"));
			$venster->addHiddenField("mod", "project");
			$venster->addHiddenField("action", "hoursuserperproject");
			$venster->addHiddenField("user_id", $user_id);
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("start"), "", "");
				$table->addTableData();
					$table->addSelectField("regitems[start_day]", $days, $regitem_start_day);
					$table->addSelectField("regitems[start_month]", $months, $regitem_start_month);
					$table->addSelectField("regitems[start_year]", $years, $regitem_start_year);
				$table->endTableData();
				$table->insertTableData("", "", "");
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("end"), "", "");
				$table->addTableData();
					$table->addSelectField("regitems[end_day]", $days, $regitem_end_day);
					$table->addSelectField("regitems[end_month]", $months, $regitem_end_month);
					$table->addSelectField("regitems[end_year]", $years, $regitem_end_year);
				$table->endTableData();
				$table->addTableData();
					$table->insertAction("forward", gettext("submit"), "javascript:document.getElementById('regitemsfrm').submit();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTableData();
			$table->endTable();
			$venster->addCode($table->generate_output());
			$venster->endTag("form");
			$view = new Layout_view();
			$view->addData($proj_info);
			$view->addMapping(gettext("project name"), "%%proj_name");
			$view->addMapping(gettext("hours"), "%project_hours");
			$view->addMapping("", "%%project_link");
			$view->defineComplexMapping("proj_name", array(
				array(
					"type" => "link",
					"text" => "%project_name",
					"link" => array("index.php?mod=project&action=showinfo&id=", "%project_id")
				)
			));
			$view->defineComplexMapping("project_link", array(
				array(
					"type" => "link",
					"text" => gettext("hour overview"),
					"link" => array("javascript: popup('index.php?mod=project&action=showprojecthours&id=", "%project_id", "&user_id=", "$user_id", "', 'hour_overview', 750, 600, 1);")
				)
			));
			$venster->addCode($view->generate_output());
			unset($view);
		$venster->endVensterData();
		$output = new Layout_output();
		$output->layout_page("hours", 1);
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* showActivityGroups {{{ */
	/**
	 * Show activity groups
	 */
	public function showActivityGroups() {
		include(self::include_dir."show_activitygroups.php");
	}
	/* }}} */
	/* payrollExport {{{ */
	/**
	 * Export for payroll company
	 *
	 * @param int $start Timestamp to start
	 * @param int $end Timestamp to end
	 * @param int $export_id If given, will give the specific export csv again, if not it will create a new export.
	 *
	 * @return void
	 */
	public function payrollExport($start = 0, $end = 0, $export_id = 0) {
		$calendar_data = new Calendar_data();
		$project_data = new Project_data();
		$address_data = new Address_data();
		$user_data = new User_data();
		$conversion = new Layout_conversion();
		// If export_id is given we want to redownload that csv so grab it from the database
		// If not, we want a new export without the already exported items in that timeframe
		if ($export_id) {
			$sql = sprintf("SELECT csv_data FROM payroll_exports WHERE id = %d", $export_id);
			$res = sql_query($sql);
			$data = sql_fetch_assoc($res);
			$data = stripslashes($data["csv_data"]);
		} else {
			// Get the already exported items so we can exclude them.
			$sql = sprintf("SELECT km_items, registration_items FROM payroll_exports WHERE timestamp_start BETWEEN %1\$d AND %2\$d OR timestamp_end BETWEEN %1\$d AND %2\$d OR (timestamp_start >= %1\$d AND timestamp_end <= %2\$d)",
				$start, $end);
			$res = sql_query($sql);
			$exclude_kmitems = array();
			$exclude_regitems = array();
			while ($row = sql_fetch_assoc($res)) {
				$exclude_regitems = array_merge($exclude_regitems, explode(",", $row["registration_items"]));
				$exclude_kmitems = array_merge($exclude_kmitems, explode(",", $row["km_items"]));
			}
			$data = "";
			$csv = array();
			$csv[] = gettext("employee");
			$csv[] = gettext("SSN");
			$csv[] = gettext("date");
			$csv[] = gettext("number of hours");
			$csv[] = gettext("average hour tarif");
			$csv[] = gettext("number of kilometers");
			$csv[] = gettext("km-price");
			//$csv[] = gettext("surcharge");
			$data = $conversion->generateCSVRecord($csv);
			unset($csv);
			// get kilometers for this timeframe
			$kmitems = $calendar_data->getKmItems(array("allusers" => 1, "start" => $start, "end" => $end, "noreg" => 1));
			// get hours for this timeframe
			$hourslist = array();
			$reghourslist = $project_data->getHoursList(array("start" => $start, "end" => $end));
			$bulkhourslist = $project_data->getHoursList(array("start" => $start, "end" => $end, "bulk" => 1));

      //Apparently, array_merge returns NULL if any of its arguments are NULL, so this is the solution:
      if (!isset($reghourslist["items"])) {
        $hourslist["items"] = $bulkhourslist["items"];
      } else if (!isset($bulkhourslist["items"])) {
        $hourslist["items"] = $reghourslist["items"];
      } else {
        $hourslist["items"] = array_merge($reghourslist["items"], $bulkhourslist["items"]);
			}

			$items = array();
			$_hoursitems = array();
			$_kmitems = array();
			if ($hourslist["items"]) {
				foreach ($hourslist["items"] as $item) {
					if (in_array($item["id"], $exclude_regitems)) {
						continue;
					}
					$_hoursitems[] = $item["id"];
					if ($item["date"]) {
						$hours = $item["hours"];
						$start_date = $item["date"];
					} else {
						$hours = ($item["timestamp_end"] - $item["timestamp_start"]) / 3600;
						$start_date = $item["timestamp_start"];
					}
					//add hours to day counter of the user
					if ($items[$item["user_id"]][date("d/m/y", $start_date)]["hours"]) {
						$items[$item["user_id"]][date("d/m/y", $start_date)]["hours"] += $hours;
					} else {
						$items[$item["user_id"]][date("d/m/y", $start_date)]["hours"] = $hours;
					}
				}
			}

			foreach ($kmitems as $userid => $kmitemarr) {
				if ($kmitemarr["items"]) {
					foreach ($kmitemarr["items"] as $item) {
						if (in_array($item["id"], $exclude_kmitems)) {
							continue;
						}
						$_kmitems[] = $item["id"];
						if ($items[$userid][date("d/m/y", $item["timestamp_start"])]["kilometers"]) {
							$items[$userid][date("d/m/y", $item["timestamp_start"])]["kilometers"] += $item["kilometers"];
						} else {
							$items[$userid][date("d/m/y", $item["timestamp_start"])]["kilometers"] += $item["kilometers"];
						}
					}
				}
			}
			$i = 0;
			foreach ($items as $user_id => $payrollitems) {
				//grab the hrm info for this user
				$userdata = $user_data->getUserdetailsById($user_id);
				$hrminfo = $address_data->getHRMinfo($user_id);
				foreach ($payrollitems as $day => $info) {
					$i++;
					unset($csv);
					$cvs = array();
					$csv[] = $userdata["username"];
					$csv[] = $hrminfo[0]["social_security_nr"];
					$csv[] = $day;
					$csv[] = number_format($info["hours"], 2, ",", "");
					$csv[] = number_format($hrminfo[0]["gross_wage"], 2, ",", "");
					$csv[] = round($info["kilometers"]);
					$csv[] = ((int)$hrminfo[0]["kilometer_allowance"])/100;
					//$csv[] = $hrminfo[0]["overhead"];
					$data .= $conversion->generateCSVRecord($csv);
				}
			}
			if ($i) {
				// If there are any items, store this export in the database
				$sql = sprintf("INSERT INTO payroll_exports (timestamp, timestamp_start, timestamp_end, km_items, registration_items, csv_data) VALUES (%d, %d, %d, '%s', '%s', '%s')",
					time(), $start, $end, implode(",", $_kmitems), implode(",", $_hoursitems), sql_escape_string($data));
				sql_query($sql);
			}
		}
		header("Content-Transfer-Encoding: binary");
		header("Content-Type: text/plain; charset=UTF-8");

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header("Content-Disposition: filename=covide_hourlist_payroll.csv"); //msie 5.5 header bug
		}else{
			header("Content-Disposition: attachment; filename=covide_hourlist_payroll.csv");
		}
		echo $data;
		exit();
	}
	/* }}} */
	/* list_payrollexports {{{ */
	/**
	 * Show a list of payrollexports and delete function
	 *
	 * @since 9.2.2.1
	 * @todo move sql stuff to data object
	 * @param array $options Optional array with key "action" that can have value "delete" and a key "id" with the payroll id to delete
	 *
	 * @return void
	 */
	public function list_payrollexports($options = array()) {
		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
		if (!$user_info["xs_projectmanage"]) {
			die("no access");
		}
		if (!is_array($options)) {
			$options = array();
		}
		if (array_key_exists("action", $options) && array_key_exists("id", $options)) {
			if ($options["action"] == "delete") {
				$sql = sprintf("DELETE FROM payroll_exports WHERE id = %d", $options["id"]);
				$res = sql_query($sql);
			}
		}
		/* grab all payroll exports */
		$sql = sprintf("SELECT * FROM payroll_exports ORDER BY timestamp");
		$res = sql_query($sql);
		$exports = array();
		while ($row = sql_fetch_assoc($res)) {
			$row["h_date"] = date("d-m-Y", $row["timestamp"]);
			$row["h_date_start"] = date("d-m-Y", $row["timestamp_start"]);
			$row["h_date_end"] = date("d-m-Y", $row["timestamp_end"]);
			$exports[$row["id"]] = $row;
		}
		$output = new Layout_output();
		$output->layout_page(gettext("Payroll Exports"));
		$venster = new Layout_venster(array("title" => gettext("Payroll export"), "subtitle" => gettext("list")));
		$venster->addMenuItem(gettext("back"), "index.php?mod=project&action=hour_overview");
		$venster->generateMenuItems();
		$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($exports);
		$view->addMapping(gettext("date export"), "%h_date");
		$view->addMapping(gettext("start date"), "%h_date_start");
		$view->addMapping(gettext("end date"), "%h_date_end");
		$view->addMapping(gettext("actions"), "%%complex_actions");
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src" => "delete",
				"link" => array("javascript: if (confirm(gettext('are you sure you want to delete this export?'))) { document.location.href='index.php?mod=project&action=list_payrollexports&pr_options[action]=delete&pr_options[id]=", "%id", "'; }"),
			),
			array(
				"type" => "action",
				"src" => "file_export",
				"link" => array("index.php?mod=project&action=payrollexport&export_id=", "%id"),
			),

		));
		$venster->addCode($view->generate_output());
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
}
