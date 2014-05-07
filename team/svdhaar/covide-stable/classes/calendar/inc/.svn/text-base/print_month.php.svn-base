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
$month = $_REQUEST["month"];
$day   = $_REQUEST["day"];
$year  = $_REQUEST["year"];
$userid = $_REQUEST["userid"];
if (!$userid) $userid = $_SESSION["user_id"];
if ($_REQUEST["timestamp"]) {
	$month = date("m", $_REQUEST["timestamp"]);
	$day   = date("d", $_REQUEST["timestamp"]);
	$year  = date("Y", $_REQUEST["timestamp"]);
}
if (!$month) { $month = date("m"); }
if (!$day)   { $day   = date("d"); }
if (!$year)  { $year  = date("Y"); }
$datestamp = mktime(0, 0, 0, $month, $day, $year);

$calendar_data = new Calendar_data();
$table = new Layout_table(array("width" => "100%"));
	$table->addTableRow();
		$table->addTableData();
			$table_cal = new Layout_table(array("cellspacing" => 1, "width" => "100%", "style" => "border: 1px solid #CCC"));
			$table_cal->addTableRow();
				$table_cal->insertTableData(gettext("sun"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("mon"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("tue"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("wed"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("thu"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("fri"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
				$table_cal->insertTableData(gettext("sat"), array("width" => "14%", "style" => "border: 1px solid #CCC"), "header");
			$table_cal->endTableRow();
			$table_cal->addTableRow();
				/* skip the weekdays before first of the month */
				$daystoskip = date("w", mktime(0, 0, 0, $month, 1, $year));
				$dow = $daystoskip;
				for ($i = 0; $i < $daystoskip; $i++)
					$table_cal->insertTableData("&nbsp;", array("style" => "border: 1px solid #CCC"));
				for ($i=0; $i<date("t",mktime(0,0,0,$month,1,$year)); $i++) {
					if (($i+1) == date("d") && $month == date("m")) {
						$table_cal->addTableData(array("align" => "left", "style" => "border: 1px solid #CCC"), "header");
					} else {
						$table_cal->addTableData(array("align" => "left", "style" => "border: 1px solid #CCC"), "data");
					}
						$table_day = new Layout_table(array("width" => "100%"));
						$table_day->addTableRow();
							$table_day->addTableData(array("width" => "100%", "align" => "right"));
								$datemask = mktime(0, 0, 0, $month, ($i+1), $year);
								/* grab calendar items for this day */
								$items_arr = $calendar_data->_get_appointments($_SESSION["user_id"], $month, ($i+1), $year, 1);
								$days_header[$days[$i]]["longdate"] = $days[$i]." (".($i+1)."/".$month.")";
								$days_header[$days[$i]]["linkdate"] = "&day=".($i+1)."&month=$month&year=$year";

								$table_day->addSpace(2);
								$table_day->insertLink($i+1, array("href"=>"index.php?mod=calendar&day=".($i+1)."&month=".$month."&year=".$year));
								$table_day->addTag("br");
								$table_day->addTag("br");
							$table_day->endTableData();
						$table_day->endTableRow();
						$table_day->addTableRow();
							$table_day->addTableData(array("NOWRAP" => "NOWRAP"));
								if (count($calendar_data->calendar_items)) {
									foreach ($calendar_data->calendar_items as $v) {
										if ($v["is_event"] != 1) {
											$table_day->addCode($v["shuman"]."&nbsp;");
										}
										$table_day->addCode(str_replace(" ", "&nbsp;", substr(strip_tags($v["subject"]), 0, 25)));
										$table_day->addTag("br");
									}
								}
								unset($calendar_data->calendar_items);
								unset($items_arr);
							$table_day->endTableData();
						$table_day->endTable();
						$table_cal->addCode($table_day->generate_output());
						unset($table_day);
					$table_cal->endTableData();
					$dow++;
					if ($dow==7) {
						/* End of the week. Start new row in calander */
						if (!(($i+1)==date("t",mktime(0,0,0,date("m"),1,date("Y"))))) {
							$table_cal->endTableRow();
							$table_cal->addTableRow();
						}
						$dow=0;
					}
				}
				// Complete fields of calander when month does not end on saturday
				if ($dow!=0) {
					for ($i=0; $i!=7-$dow; $i++) {
						$table_cal->insertTableData("&nbsp;", array("style" => "border: 1px solid #CCC"), "header");
					}
				}
			$table_cal->endTableRow();
			$table_cal->endTable();
			$table->addCode($table_cal->generate_output());
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();

$output = new Layout_output();
$output->layout_page("", 1);
$output->addCode($table->generate_output());
$output->start_javascript();
	$output->addCode("
		function printit() {
			this.print();
		}
		addLoadEvent(printit);
	");
$output->end_javascript();
$output->layout_page_end();
$output->exit_buffer();
?>
