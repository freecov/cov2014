<?php
	/**
	 * Covide Groupware-CRM calendar module
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
	if (!class_exists("Calendar_output")) {
		die("no class definition found");
	}
	$user_data = new User_data();
	$calendar_data = new Calendar_data();

	$venster_settings = array(
		"title"    => gettext("weekly overview")." ".$user_data->getUsernameById($useridtolookup),
		"subtitle" => gettext("week")." ".strftime("%V", mktime(0,0,0,$month,$day,$year))
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$table = new Layout_table(array("width" => "100%"));
		$table->addTableRow();
			$table->addTableData();

				/* weekview */
				$weekstart = mktime(0,0,0,$month,$day-date("w",mktime(0,0,0,$month,$day,$year)),$year);
				$buffer = array();

				$days = array(
					gettext("sun"),
					gettext("mon"),
					gettext("tue"),
					gettext("wed"),
					gettext("thu"),
					gettext("fri"),
					gettext("sat")
				);
				for ($i=0;$i<7;$i++) {

					/* detect and get offset according to summer / winter time */
					$offset = $calendar_data->detectCestSwitch($weekstart, $weekstart+(24*$i*60*60));

					/* weekday */
					$start_day   = date("d", ($weekstart+$offset+(24*$i*60*60)));
					$start_month = date("m", ($weekstart+$offset+(24*$i*60*60)));
					$start_year  = date("Y", ($weekstart+$offset+(24*$i*60*60)));


					$items_arr = $calendar_data->_get_appointments($useridtolookup, $start_month, $start_day, $start_year, 1);
					$days_header[$days[$i]]["longdate"] = $days[$i]." (".$start_day."/".$start_month.")";
					$days_header[$days[$i]]["linkdate"] = "&day=$start_day&month=$start_month&year=$start_year";
					if ($extrausers) {
						$days_header[$days[$i]]["linkdate"] .= "&extrauser=".$extrausers;
					}
					if (count($calendar_data->calendar_items)) {
						foreach ($calendar_data->calendar_items as $v) {
							$buffer[$days[$i]][] = array(
								"now"           => $days[$i],
								"id"            => $v["id"],
								"human_span"    => $v["human_span_short"],
								"subject"       => substr(strip_tags($v["subject"]),0,25),
								"show_actions"  => $v["show_actions"],
								"is_registered" => $v["is_registered"]
							);
						}
					} else {
						$buffer[$days[$i]][] = array();
					}
					unset($item_arr);
					unset($calendar_data->calendar_items);
					/* weekday */
				}
				$weekdata = array($buffer);

				$weekview = new Layout_view();
				foreach ($days as $wd) {
					$weekview->addMapping("%%complex_weekday_header_".$wd, "%%complex_weekday_".$wd);
					$weekview->defineComplexMapping("complex_weekday_header_".$wd, array(
						array(
							"type" => "link",
							"text" => $days_header[$wd]["longdate"],
							"link" => "index.php?mod=calendar".$days_header[$wd]["linkdate"]
						)
					));
					$weekview->defineComplexMapping("complex_weekday_".$wd, array(
						array(
							"type"   => "array",
							"array"  => $wd,
							"mapping" => "%%complex_appointment"
						)
					), array("class" => "weekviewweekday"));
				}
				$weekview->defineComplexMapping("complex_appointment", array(
					array(
						"type"  => "action",
						"src"   => "info",
						"alt"   => gettext("show"),
						"link"  => array("javascript: toonInfo(", "%id", ",", $useridtolookup,");"),
						"check" => "%id"
					),
					array(
						"type"  => "link",
						"text"  =>
							array(
								"%human_span",
								"%subject",
								"</span>",
								"\n"
							),
						"link"  => array("javascript: calendaritem_edit(", "%id", ", ".$useridtolookup.");"),
						"check" => "%show_actions"
					),
					array(
						"type"  => "text",
						"text"  =>
							array(
								"%human_span",
								"%subject",
								"</span>",
								"\n"
							),
						"check" => "%is_registered"
					)
				));
				$weekview->addData($weekdata);

				$table->addCode( $weekview->generate_output() );

				/* end view */

			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		if (!$print) {
			$venster->insertAction("print", gettext("print"), "javascript: popup('index.php?mod=calendar&action=print_week&userid=$useridtolookup&day=$day&month=$month&year=$year', 'printweek', 300, 300, 1);");
		}
		$venster->insertAction("back", gettext("last week"), sprintf("?mod=calendar&extrauser=%s&day=%d&month=%d&year=%d", $_REQUEST["extrauser"], $day-7, $month, $year));
		$venster->insertAction("forward", gettext("next week"), sprintf("?mod=calendar&extrauser=%s&day=%d&month=%d&year=%d", $_REQUEST["extrauser"], $day+7, $month, $year));
	$venster->endVensterData();
?>
