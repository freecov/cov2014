<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}

	/* define weekdays */
	$weekdays = array(
		0 => gettext("sun"),
		1 => gettext("mon"),
		2 => gettext("tue"),
		3 => gettext("wed"),
		4 => gettext("thu"),
		5 => gettext("fri"),
		6 => gettext("sat")
	);
	/* get start date */
	if (!$start) {
		$init  = 1;
		$start = time();
	}

	/* define start month and year */
	$month = date("m", $start);
	$year  = date("Y", $start);

	$start_prev = mktime(
		0,0,0,
		date("m", $start)-$num_months,
		date("d", $start),
		date("Y", $start)
	);
	$start_next = mktime(
		0,0,0,
		date("m", $start)+$num_months,
		date("d", $start),
		date("Y", $start)
	);
	$this->has_abbr[] = true;

?>
<a name="calendar_mark" class='anchor'></a>
<form id="calform" action="/calendarpage/<?php echo $this->pageid ?>" method="get">
<input type="hidden" name="start" id="calstart" value="<?php echo $start ?>" <?php echo ($this->doctype['xhtml']) ? '/':'' ?>>
<div id="calendar_layer">
	<table>
		<tr>
			<?php for ($m=0; $m<$num_months; $m++) { ?>
				<?php
					/* get start timestamp */
					$ts = mktime(0,0,0,$month+$m,1,$year);

					/* get start weekday */
					$wd = date("w", $ts);

					/* get negative month offset */
					$offset = 1-$wd;
				?>
				<td style="vertical-align: top;">
					<table class="calendar_table" cellspacing="0" cellpadding="2">
						<tr>
							<td class="calendar_month" colspan="2">
								<?php
									if ($m == 0) {
										$output = new Layout_output();
										$output->insertAction("calendar_today", gettext("go to today"), sprintf("javascript: initCalOffset(%d, 0);", $id));
										$output->addSpace();
										$output->insertAction("back", gettext("previous months"), sprintf("javascript: initCalOffset(%d, %d);", $id, $start_prev));
										echo $output->generate_output();
									} else {
										echo "<img src='/img/spacer.gif' height='22px;' alt=''>";
									}
								?>
							</td>
							<td class="calendar_month" colspan="5" style="text-align: center;"><b><?php echo date("F Y", mktime(0,0,0,$month+$m, 1, $year)); ?></b></td>
							<td class="calendar_month">
								<?php
									if ($m == $num_months - 1) {
										$output = new Layout_output();
										$output->insertAction("forward", gettext("next months"), sprintf("javascript: initCalOffset(%d, %d);", $id, $start_next));
										echo $output->generate_output();
									} else {
										echo "<img src='/img/spacer.gif' height='22px;' alt=''>";
									}
								?>
							</td>
						</tr>
						<tr>
							<?php for ($i=0; $i<7; $i++) { ?>
								<td class="calendar_weekday"><?php echo $weekdays[$i] ?></td>
							<?php } ?>
								<td class="calendar_week"><i>w</i></td>
						</tr>
						<tr>
							<?php
								for ($i=$offset; $i<=date("t", $ts); $i++) {
									/* current ts */
									$curts = mktime(0,0,0,$month+$m,$i,$year);
									$nextts = mktime(0,0,0,$month+$m,$i+1,$year);

									/* get items */
									$q = sprintf("select dateid from cms_date_index where datetime between %d and %d",
										$curts, $nextts - 1);
									// if we are on a page, show only the page, else, show all items
									if ($id > 0) {
										$q.= sprintf(' and pageid = %d', $id);
									}
									$res = sql_query($q);
									$items = array();
									while ($row = sql_fetch_assoc($res)) {
										$items[]=$row["dateid"];
									}


									if ($i > 0)
										if (count($items) > 0) {
											$txt = $this->getCalendarInfo(0, $items);
											echo sprintf("<td class='calendar_day' style='text-align: right'>
												<b><em class=\"tt_tooltip\" onmouseover=\"Tip('%s', CLOSEBTN, true, SHADOW, true, FOLLOWMOUSE, false);\">%d</em></b></td>",
												$txt, $i);

										} else {
											echo "<td class='calendar_day' style='text-align: right'>$i</td>";
										}
									else
										echo "<td class='calendar_day'>&nbsp;</td>";

									if (date("w", $curts) == 6) {
										echo "<td class='calendar_week'><i>".date("W", mktime(0,0,0,$month+$m, $i, $year))."</i></td>";
										echo "</tr><tr>";
									}
								}
								if (date("w", $curts) != 6) {
									$nomatch = 1;
									while ($nomatch && $i <= (31+7+1)) { //i = is safety break point
										$curts = mktime(0,0,0,$month+$m,$i,$year);
										if (date("w", $curts) == 0) {
											echo "<td class='calendar_week'><i>".date("W", mktime(0,0,0,$month+$m, $i, $year))."</i></td>";
											$nomatch = 0;
										} else {
											echo "<td class='calendar_day'>&nbsp;</td>";
										}
										$i++;
									}
								}
							?>
						</tr>
					</table>
				</td>
			<?php } ?>
		</tr>
	</table>
</div>
</form>
