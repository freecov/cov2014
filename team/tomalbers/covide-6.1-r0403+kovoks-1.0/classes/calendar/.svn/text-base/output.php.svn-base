<?php
/**
 * Covide Groupware-CRM Calendar output module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version 6.1
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Calendar_output {

	/* constants */
	const include_dir = "classes/calendar/inc/";
	const include_dir_main = "classes/html/inc/";

	/* variables */

	/* methods */
	public function show_main() {
		require_once(self::include_dir."show_main.php");
	}

	public function show_edit($id=0) {
		require_once(self::include_dir."show_edit.php");
	}

	public function show_info() {
		require_once(self::include_dir."show_info.php");
	}

	public function reg_input() {
		require_once(self::include_dir."reg_input.php");
	}

	public function pick_date() {
		require_once(self::include_dir."pick_date.php");
	}

	public function show_km() {
		require_once(self::include_dir."show_km.php");
	}

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
						"title"    => gettext("zoekresultaten")." ".gettext("agenda van")." ".$userdata->getUsernameById($user),
						"subtitle" => $_REQUEST["searchkey"]
					));
					$venster->addMenuItem(gettext("terug"), "javascript: history.go(-1)");
					$venster->generateMenuItems();
					$venster->addVensterData();
						$view = new Layout_view();
						$view->addData($calendar_items);
						$view->addMapping(gettext("begin"), "%human_start");
						$view->addMapping(gettext("eind"), "%human_end");
						$view->addMapping(gettext("onderwerp"), "%%complex_subject");
						$view->defineComplexMapping("complex_subject", array(
							array(
								"type" => "link",
								"link" => array("index.php?mod=calendar&day=", "%day", "&month=", "%month", "&year=", "%year"),
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
	 */
	function show_calendar($day, $month, $year) {
		$name = md5(rand()*session_id());
		/*
		name = unique name identifier
		day, month, year = javascript object id identifier of
		 the corresponding field

		 for example:
		 calendarInit("test", "document.velden.dag", "document.velden.maand", "document.getElementById('year')");
		*/
		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("
			function calendarPopUp_$name() {
				eval(\"var wx = window.open('".self::include_dir."calendar.php?ident=$name&start=".mktime(0,0,0,1,1,date("Y")-1)."&end=".mktime(0,0,0,1,1,date("Y")+5)."&sday='+$day.value+'&smonth='+$month.value+'&syear='+$year.value, 'wx', 'toolbar=no,scrollbars=no,location=no,statusbar=no,menubar=no,resizable=no,width=190,height=220,left = 100,top = 80');\");
			}

			function upd_$name(dday, dmonth, dyear){
				$day.value=parseInt(dday);
				$month.value=parseInt(dmonth);
				$year.value=parseInt(dyear);
				if ($year.value!=parseInt(dyear)){
					alert('".gettext("jaar")." '+dyear+' ".gettext("niet gevonden")."');
				}
			}
		");
		$output->end_javascript();
		$output->insertAction("calendar_today", gettext("Kalender"), "javascript: calendarPopUp_$name();");
		return $output->generate_output();
	}
	/* }}} */
	/* show_monthview {{{ */
	/**
	 * Show a month on one screen.
	 */
	public function show_monthview() {
		require_once(self::include_dir."show_monthview.php");
	}
	/* }}} */
}
?>
