<?php
	if (!class_exists("Calendar_output")) {
		die("no class definition found");
	}
	$user_data = new User_data();
	$calendar_data = new Calendar_data();

	$venster_settings = array(
		"title"    => gettext("weekplanning")." ".$user_data->getUsernameById($useridtolookup),
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
					gettext("zo"),
					gettext("ma"),
					gettext("di"),
					gettext("wo"),
					gettext("do"),
					gettext("vr"),
					gettext("za")
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
								"shuman"        => $v["shuman"],
								"ehuman"        => $v["ehuman"],
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
					));
				}
				$weekview->defineComplexMapping("complex_appointment", array(
					array(
						"type"  => "action",
						"src"   => "info",
						"alt"   => gettext("tonen"),
						"link"  => array("javascript: toonInfo(", "%id", ");"),
						"check" => "%id"
					),
					array(
						"type"  => "link",
						"text"  =>
							array(
								"%shuman",
								" - ",
								"%ehuman",
								"\n",
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
								"%shuman",
								" - ",
								"%ehuman",
								"\n",
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
	$venster->endVensterData();
?>
