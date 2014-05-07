<?php
/**
 * Covide Groupware-CRM Calendar output module
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
Class Calendar_output {

	/* constants */
	const include_dir = "classes/calendar/inc/";
	const include_dir_main = "classes/html/inc/";

	/* variables */

	/* methods */
	/* show_main {{{ */
	/**
	 * Show calendar
	 */
	public function show_main($date=0) {
		require(self::include_dir."show_main.php");
	}
	/* }}} */
	/* print_main {{{ */
	/**
	 * Print day calendar
	 */
	public function print_main() {
		require(self::include_dir."print_main.php");
	}
	/* }}} */
	/* show_edit {{{ */
	/**
	 * Show interface to edit/create a calendar item
	 *
	 * @param int Optional calendaritem id to edit. if empty or 0 create new item
	 */
	public function show_edit($id=0) {
		require(self::include_dir."show_edit.php");
	}
	/* }}} */
	/* show_info {{{ */
	/**
	 * Show a calendaritem using the infolayer and AJAX
	 */
	public function show_info() {
		require(self::include_dir."show_info.php");
	}
	/* }}} */
	/* reg_input {{{ */
	/**
	 * Show screen to register hours.
	 */
	public function reg_input() {
		require(self::include_dir."reg_input.php");
	}
	/* }}} */
	/* show_km {{{ */
	/**
	 * Show overview of registered kilometers
	 */
	public function show_km() {
		require(self::include_dir."show_km.php");
	}
	/* }}} */
	/* show_searchres {{{ */
	/**
	 * Show search results triggered by the search box beneath the month in side of window
	 */
	public function show_searchres() {
		if ($_REQUEST["searchkey"]) {
			$calendar_data = new Calendar_data();
			$userdata = new User_data();
			/* make new page */
			$output = new Layout_output();
			$output->layout_page();
				/* loop through all the users */
				$users = explode(",", $_REQUEST["extrauser"]);
				foreach ($users as $user) {
					/* get all the calendar items for user */
					$search_options = array(
						"all"       => 1,
						"user_id"   => $user,
						"searchkey" => $_REQUEST["searchkey"]
					);
					$calendar_items = $calendar_data->getAppointmentsBySearch($search_options);

					/* window widget containing view object */
					$venster = new Layout_venster(array(
						"title"    => gettext("search results")." ".gettext("calendar of")." ".$userdata->getUsernameById($user),
						"subtitle" => $_REQUEST["searchkey"]
					));
					$venster->addMenuItem(gettext("back"), "javascript: history.go(-1)");
					$venster->generateMenuItems();
					$venster->addVensterData();
						$view = new Layout_view();
						$view->addData($calendar_items);
						$view->addMapping(gettext("start"), "%human_start");
						$view->addMapping(gettext("end"), "%human_end");
						$view->addMapping(gettext("subject"), "%%complex_subject");
						$view->defineComplexMapping("complex_subject", array(
							array(
								"type" => "link",
								"link" => array("index.php?mod=calendar&day=", "%day", "&month=", "%month", "&year=", "%year", "&extrauser=".$_REQUEST["extrauser"]),
								"text" => "%subject"
							)
						));
						$venster->addCode($view->generate_output());
						unset($view);
					$venster->endVensterData();
					/* end window, append to output buffer */
					$output->addCode($venster->generate_output());
					unset($venster);
				}
			$output->layout_page_end();
			$output->exit_buffer();
		} else {
			die("nothing to search for");
		}
	}
	/* }}} */
	/* show_emails {{{ */
	/**
	 * Show email boxes in edit view when a relation is picked
	 */
	public function show_emails() {
		$address_data = new Address_data();
		$address_id = $_REQUEST["address_id"];

		$email_arr = array();
		$ids = explode(",", $address_id);
		if (is_array($ids)) {
			foreach ($ids as $v) {
				if ($v) {
					$email_arr[] = $address_data->getMailAddressesById($v);
				}
			}
		}
		$output = new Layout_output();
		foreach($email_arr as $v) {
			foreach ($v["emails"] as $email) {
				$emails["emails"][] = $email;
			}
		}
		if (is_array($emails["emails"])) {
			foreach($emails["emails"] as $k=>$v) {
				$output->addCheckBox("appointment[relmail][$v]", $v);
				$output->addCode($v);
				$output->addTag("br");
			}
		}
		$output->exit_buffer();
	}
	/* }}} */
	/* show_permissions {{{ */
	/**
	 * Show permissions on your calendar
	 */
	public function show_permissions() {
		/* use logged in user as default */
		$user_id = $_SESSION["user_id"];
		/* supervisors are able to manage others calendar permissions. */
		if ($_REQUEST["user_id"]) {
			$user_id = $_REQUEST["user_id"];
		}
		/* all logic is in this include file */
		require(self::include_dir."showPermissions.php");
	}
	/* }}} */
	/* show_calendar {{{ */
	/**
	 * Show a small calendar to pick a date
	 *
	 * You can us this function like this:
	 * <code>
	 * calendarInit("test", "document.velden.day", "document.velden.month", "document.velden.year");
	 * </code>
	 *
	 * @param string Javascript object id of the dayfield in the dom layout
	 * @param string Javascript object id of the monthfield in the dom layout
	 * @param string Javascript object id of the yearfield in the dom layout
	 * @param string from_cms Optional field. when set will use an absolute path to classes/calendar/inc/calendra.php
	 */
	function show_calendar($day, $month, $year, $from_cms = 0) {
		$name = md5($day.$month.$year.rand());
		/*
		name = unique name identifier
		day, month, year = javascript object id identifier of
		 the corresponding field

		 for example:
		 calendarInit("test", "document.velden.dag", "document.velden.maand", "document.getElementById('year')");
		 */
		if ($from_cms) {
			$calendar_php = "/".self::include_dir."calendar.php";
		} else {
			$calendar_php = self::include_dir."calendar.php";
		}
		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("
			function calendarPopUp_$name() { eval(\"var wx = window.open('".$calendar_php."?ident=$name&start=".mktime(0,0,0,1,1,date("Y")-1)."&end=".mktime(0,0,0,1,1,date("Y")+5)."&sday='+$day.value+'&smonth='+$month.value+'&syear='+$year.value, 'wx', 'toolbar=no,scrollbars=no,location=no,statusbar=no,menubar=no,resizable=no,width=190,height=220,left = 100,top = 80');\"); }

			function upd_$name(dday, dmonth, dyear){ $day.value=parseInt(dday); $month.value=parseInt(dmonth); $year.value=parseInt(dyear);
				if ($year.value!=parseInt(dyear)){ alert('".gettext("year")." '+dyear+' ".gettext("not found")."'); }
			}
		");
		$output->end_javascript();
		$output->insertAction("calendar_today", gettext("Calendar"), "javascript: calendarPopUp_$name();");
		return $output->generate_output();
	}
	/* }}} */
	/* show_monthview {{{ */
	/**
	 * Show a month on one screen.
	 */
	public function show_monthview() {
		require(self::include_dir."show_monthview.php");
	}
	/* }}} */
	/* show_monthview_mu {{{ */
	/**
	 * Show a month on one screen with all selected users.
	 */
	public function show_monthview_mu($user_data) {
		require(self::include_dir."show_monthview_mu.php");
	}
	/* }}} */
	/* show_planning {{{ */
	/**
	 * Show planning.
	 * This will show week with coloured blocks much like the planning screens you see in production rooms.
	 * Options to select users and times can be altered by the visiting user
	 *
	 * @todo It's only this week with user selection. Implement more options like time selection and toggle between vertical and horizontal view
	 */
	public function show_planning() {
		require(self::include_dir."show_planning.php");
	}
	/* }}} */
	/* show_day {{{ */
	/**
	 * Function to create view of day
	 *
	 * @param array $data The data as fetched by $Calendar_output->_get_appointments
	 * @param int $datestamp The current date as mktime int
	 * @param int $print If 1 some icons and elements wont be added to the object so it's nicer to print
	 * @return object A Layout_view object with a view of the current day included.
	 */
	 public function show_day($data, $datestamp=0, $print=0) {

	/* repeat_type indicates a monthlt/yearly repeating item, meaning it should get
		an flag for  a repeating icon as well. */
	foreach ($data as $key => $item) {
		if ($item["repeat_type"])
			$data[$key]["is_repeat"]++;
	}
	$view = new Layout_view();
		$view->addData($data);

		/* map our columns */
		if ($print) {
			$view->addMapping(gettext("time"), array(
				"%human_span_long"
			));
			$view->addMapping(gettext("calendar item"), array(
				"%%complex_important",
				"%%complex_apptype",
				"%%complex_location",
				"%subject",
				"\n",
				"%body",
				"\n",
				"%%complex_extrainfo"
			));
		} else {
			$view->addMapping(gettext("time"), array(
				"%human_span_long",
				"%%complex_time"
			));
			$view->addMapping(gettext("calendar item"), array(
				"%%complex_important",
				"%%complex_apptype",
				"%%complex_location",
				"%subject",
				"\n",
				"%body",
				"\n",
				"%%complex_extrainfo"
			));
		}
		//alow html inside body field
		$view->setHtmlField("body");

		/* define the mappings */
		$view->defineComplexMapping("complex_time", array(
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: calendaritem_remove(", "%id", ", ", "%user_id", ", ", "%is_repeat", ", ", $datestamp, ");"),
				"check" => "%show_actions"
			),
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("javascript: calendaritem_edit(", "%id", ",", "%user_id", ",$datestamp)"),
				"check" => "%show_actions"
			),
			array(
				"type" => "action",
				"src"  => "calendar_reg_hour",
				"alt"  => gettext("register hours"),
				"link" => array("javascript: calendaritem_reg(", "%id", ",", "%user_id", ",$datestamp)"),
				"check" => "%show_reg"
			),
			array(
				"type" => "action",
				"src"  => "info",
				"alt"  => gettext("show"),
				"link" => array("javascript: toonInfo(", "%id", ",", "%user_id", ");")
			),
			array(
				"type" => "action",
				"src"  => "calendar_repeat",
				"alt"  => gettext("repeating"),
				"check" => "%is_repeat"
			)
		));
		$view->defineComplexMapping("complex_important", array(
			array(
				"type"  => "action",
				"src"   => "important",
				"alt"   => gettext("important meeting"),
				"check" => "%important"
			),
			array(
				"text"  => "<b>".gettext("important meeting")."</b>\n",
				"check" => "%important"
			)
		));
		$view->defineComplexMapping("complex_apptype", array(
			array(
				"type"  => "action",
				"src"   => "state_private",
				"alt"   => gettext("private appointment"),
				"check" => "%is_private"
			),
			array(
				"text"  => "<b>".gettext("private appointment")."</b>\n",
				"check" => "%is_private"
			)
		));
		$view->defineComplexMapping("complex_location", array(
			array(
				"type"  => "text",
				"text"  => array(gettext("location"), ": ", "%location", ", ", gettext("kilometers"), ": ", "%kilometers", "\n"),
				"check" => "%location"
			)
		));
		$view->defineComplexMapping("complex_modified", array(
			array(
				"type" => "text",
				"text" => array(gettext("last changed by"), ": ", "%h_modified_by", " ", gettext("on"), ": ", "%h_modified")
			)
		));
		$view->defineComplexMapping("complex_extrainfo", array(
			array(
				"type" => "text",
				"text" => array(gettext("contact"), ": "),
				"check" => "%relation"
			),
			array(
				"type" => "multilink",
				"link" => array("index.php?mod=address&action=relcard&id=", "%all_address_ids"),
				"text" => "%all_address_names",
				"check" => "%all_address_ids"
			),
			array(
				"type" => "text",
				"text" => array(" ", ($GLOBALS["covide"]->license["has_project_declaration"]) ? gettext("declaration") : gettext("project"), ": "),
				"check" => "%project_id"
			),
			array(
				"type" => "link",
				"link" => array("index.php?mod=project&action=showhours&id=", "%project_id"),
				"text" => "%project_name",
				"check" => "%project_id"
			),
			array(
				"type" => "text",
				"text" => array(" ", gettext("note"), ": ")
			),
			array(
				"type"  => "link",
				"link"  => array("index.php?mod=note&action=message&msg_id=", "%note_id"),
				"text"  => "%note_title",
				"check" => "%note_id"
			),
			array(
				"type"  => "link",
				"link"  => array("javascript: popup('index.php?mod=note&action=edit&calendar_id=", "%id", "&address_id=", "%relation", "&project_id=", "%project_id", "');"),
				"text"  => gettext("make note"),
				"check" => "%no_note"
			)
		));
		return $view->generate_output();
	 }
	 /* }}} */
	/* show_week {{{ */
	/**
	 * Function to create view of week
	 *
	 * @param int $useridtolookup The userid to create weekoverview for
	 * @param int $month The month to use
	 * @param int $day The day to use. If this day is not sunday the function will figure out the start of the week itself
	 * @param int $year The year to use
	 * @param int $print if 1, some images wont be included for a cleaner print page
	 * @param string $extrausers comma seperated list of users that should be included in the edit links (creating calendar items for multiple users)
	 * @return object A Layout_view object with a view of the current week included.
	 */
	 public function show_week($useridtolookup, $month, $day, $year, $extrausers=0, $print=0) {
		require(self::include_dir."show_week.php");
		return $venster;
	 }
	 /* }}} */
	 /* print_week {{{ */
	 /**
	  * Print selected week calendar
	  */
	 public function print_week() {
		require(self::include_dir."print_week.php");
	 }
	 /* }}} */
	/* show_notificationtemplate {{{ */
	/**
	 * Let user edit notification template
	 *
	 * @param int $user_id The userid this template is for
	 */
	public function show_notificationtemplate($user_id) {
		// get template for this user
		$calendar_data = new Calendar_data();
		$nottemp = $calendar_data->getNotifyTemplate($user_id);
		$output = new Layout_output();
		$output->layout_page();
		$frame = new Layout_venster(array("title" => gettext("Calendar"), "subtitle" => gettext("notification template")));
		$frame->addMenuItem(gettext("show calendar"), "index.php?mod=calendar");
		$frame->generateMenuItems();
		$frame->addVensterData();
		$frame->addCode(gettext("Here you can create your own notification template. This template will be used when you select one or more relation email addresses when creating an appointment."));
		$frame->addTag("br");
		$frame->addTag("br");
		$frame->addCode(gettext("You can use some special strings that will be replaced when the email is sent. Here's a list of them with a description of how you can use them."));
		$frame->addTag("br");
		$frame->addTag("br");
		$frame->addcode("{{starttime}} - ".gettext("will be replaced with the calendar start time and date"));
		$frame->addTag("br");
		$frame->addcode("{{endtime}} - ".gettext("will be replaced with the calendar end time and date"));
		$frame->addTag("br");
		$frame->addcode("{{subject}} - ".gettext("will be replaced with the calendar subject"));
		$frame->addTag("br");
		$frame->addcode("{{location}} - ".gettext("will be replaced with the calendar location"));
		$frame->addTag("br");
		$frame->addcode("{{description}} - ".gettext("will be replaced with the calendar description. Plain text version only."));
		$frame->addTag("br");
		$frame->addTag("br");
		$frame->addCode(gettext("See this example (english only) for how you can use it."));
		$frame->addTag("br");
		$frame->addTag("br");
		$frame->addCode("
		Mr. M. van Baak has planned a meeting with you.<br>
		Here are the details about the meeting:<br>
		Starts at {{starttime}}<br>
		Ends at {{endtime}}<br>
		Takes place at {{location}}<br>
		The meeting is about {{subject}}<br>
		Additional information:<br>
		{{description}}<br>
		If you, for some reason, are not able to attend this meeting please send a mail to info@example.com.<br><br>

		Thank you,<br>
		M. van Baak<br>
		Covide project manager and core developer.
		");
		$frame->addTag("br");
		$frame->addTag("br");
		$frame->addTag("form", array("id" => "nottemp", "action" => "index.php", "method" => "post"));
		$frame->addHiddenField("mod", "calendar");
		$frame->addHiddenField("action", "savenotification");
		$frame->addHiddenField("user_id", $user_id);
		$frame->addTextArea("description", $nottemp, array("style"=>"width: 570px; height: 300px;"), "contents");
		$frame->insertAction("save", gettext("save"), "javascript: document.getElementById('nottemp').submit();");
		$frame->endTag("form");
		$frame->endVensterData();
		$output->addCode($frame->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* ask_repeat {{{ */
	/**
	 * Ask questions about repeating item like: remove only this one, alter only this one
	 *
	 * @param array $data Request data that holds at least user, itemid
	 */
	public function ask_repeat($data) {
		switch($data["repeataction"]) {
		case "delete" :
			$output = new Layout_output();
			$output->layout_page("", 1);
				$frame = new Layout_venster(array("title" => gettext("Calendar"), "subtitle" => gettext("repeat")));
				$frame->addMenuItem(gettext("close"), "javascript: window.close();");
				$frame->generateMenuItems();
				$frame->addVensterData();
					$frame->addCode(gettext("What do you want to do?"));
					$frame->addTag("br");
					$frame->insertLink("Remove all occurences", array("href" => "javascript: remove_all();"));
					$frame->addTag("br");
					$frame->insertLink("Remove only this item", array("href" => "javascript: remove_one();"));
				$frame->endVensterData();
				$output->addCode($frame->generate_output());
				$output->start_javascript();
				$output->addCode("
					function remove_all() {
						opener.document.getElementById('action').value = 'deleteallrep';
						opener.document.getElementById('id').value = ".$_REQUEST["id"].";
						opener.document.getElementById('user_id').value = ".$_REQUEST["user"].";
						if (confirm(gettext('remove calenderitem')+' ?')) {
							opener.document.getElementById('calendarform').submit();
						}
						window.close();
					}
					function remove_one() {
						opener.document.getElementById('action').value = 'deleteonerep';
						opener.document.getElementById('id').value = ".$_REQUEST["id"].";
						opener.document.getElementById('user_id').value = ".$_REQUEST["user"].";
						opener.document.getElementById('datestamp').value = ".$_REQUEST["timestamp"].";
						if (confirm(gettext('remove calenderitem')+' ?')) {
							opener.document.getElementById('calendarform').submit();
						}
						window.close();
					}
				");
				$output->end_javascript();
			$output->layout_page_end();
			$output->exit_buffer();
			break;
		default:
			echo "nothing";
			break;
		}
	}
	/* }}} */
	/* print_month {{{ */
	/**
	 * Print month calendar
	 */
	public function print_month() {
		require(self::include_dir."print_month.php");
	}
	/* }}} */
}
?>
