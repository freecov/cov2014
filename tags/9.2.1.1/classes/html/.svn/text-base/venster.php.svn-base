<?php
/**
 * Covide Venster object
 *
 * Window/Venster interface class.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
class Layout_venster Extends Layout_output {
	/* constants */
	const include_dir =  "classes/html/inc/";

	/* variables */
	private $_output = "";
	private $menu_items = array();
	private $settings = array();
	private $_table;
	private $_menuitemscode = "";
	private $_maindiv = "maincontent_fullwidth";
	private $_nofullwidth = 0;

	/* methods   */

	/* __construct {{{ */
	/**
	 * Class constructor.
	 * If you provide a settings array a title and subtitle can be set
	 *
	 * @param array $settings Array with the keys 'title' and 'subtitle'
	 */
	public function __construct($settings="") {
		$this->output     = "";
		$this->addComment("begin venster object");
		if ($settings["nofullwidth"] == 1) {
			$this->_nofullwidth = 1;
			$this->_maindiv = "maincontent";
		}

		$this->menu_items = array();
		if (!is_array($settings)) {
			$settings = array();
		}
		$_output = new Layout_output();
		$_output->addTag("div", array("id" => "__maindiv__"));

		if ($GLOBALS["covide"]->venster > 0)
			$settings["skip_id"] = 1;

		if ($settings["title"]) {
			$_output->addTag("div", array("id" => "mod_header"));
				$_output->insertTag("h1", $settings["title"], array("class" => "fl_left"));
				$_output->addTag("div", array("id" => "mod_header_options", "class" => "fl_left"));
					$_output->addSpace(2);
					$_output->addCode($settings["subtitle"]);
				$_output->endTag("div");
				$_output->addTag("div", array("id" => "mod_header_menu", "class" => "fl_right"));
					$_output->addCode("<!-- __menuitems__ -->");
				$_output->endTag("div");
			$_output->endTag("div");
		}
		$this->_table = new Layout_output();
		$this->_output = $_output;
	}
	/* }}} */
	/* addMenuItem {{{ */
	/**
	 * Add a menuitem to the in-memory array of menuitems
	 *
	 * @param string $name The name of the menuitem
	 * @param string $link The url to use when the menuitem is clicked
	 * @param int $new_window if set the link will be opened in a new window
	 * @param int $hidden if set the link will be in a 'more' collapsed menuitem, if set no 0 will be shown on top of a frame
	 */
	public function addMenuItem($name, $link, $new_window=0, $hidden=1) {
		$this->menu_items[$name] = array($link, (int)$new_window, (int)$hidden);
	}
	/* }}} */
	/* generateMenuItems {{{ */
	/**
	 * Generate the html code for the menu
	 */
	public function generateMenuItems() {
		$hiddencount = 0;
		$output = new Layout_output();

		$extra_menuitems = $GLOBALS["covide"]->contrib["USE_CONTRIB_MENU_ITEMS"];
		if ($extra_menuitems) {
			if (!is_array($extra_menuitems)) {
				$extra_menuitems = array($extra_menuitems);
			}

			foreach ($extra_menuitems as $m) {
				$this->addMenuItem(
					$m["name"],
					$m["link"],
					(int)$m["popup"]
				);
			}
		}
		// find out if we need 'hidden' items is a 'more' menu
		foreach ($this->menu_items as $v) {
			if ($v[2]) {
				$hiddencount++;
			}
		}
		if ($hiddencount) {
			$output_hidden = new Layout_output();
			$output_hidden->addTag("br");
			$output_hidden->addTag("br");
			$output_hidden->addTag("div", array("id" => "mod_header_menu_moreactions_top", "class" => "mod_header_menu_moreactions", "style" => "display: none;"));
		}
		$output->addTag("ul");
			foreach ($this->menu_items as $n => $l) {
				if ($l[2]) {
					$output_hidden->insertLink($n, array(
						"href"   => $l[0],
						"target" => ($l[1]) ? "_blank":""
					));
					$output_hidden->addTag("br");
				} else {
					$output->addTag("li");
					$output->insertLink($n, array(
						"href"   => $l[0],
						"target" => ($l[1]) ? "_blank":""
					));
					$output->endTag("li");
				}
			}
			if ($hiddencount) {
				$output->addTag("li");
					$output->insertLink(gettext("more actions")." ▼", array(
						"href" => "javascript: header_menu_toggleMoreActions('top');"
					));
				$output->endTag("li");
			}
		$output->endTag("ul");
		if ($hiddencount) {
			$output_hidden->endTag("div");
			$output->addCode($output_hidden->generate_output());
		}
		$this->_menuitemscode = $output->generate_output();
	}
	/* }}} */
	/* generateCalendarMobile {{{ */
	public function generateCalendarMobile($month, $day, $year, $userlist) {
		$table = $this->_table;
		$table->addTag("br");
		$table->addTag("br");
		$baseurl = "index.php?mod=calendar&day=1&extrauser=".implode(",", $userlist);
		$urlnext = $baseurl."&month=".($month+1);
		if ($month == 1) {
			$urlprev = $baseurl."&year=".($year-1)."&month=12";
		} else {
			$urlprev = $baseurl."&year=$year&month=".($month-1);
		}
		$table->addTag("div", array("align" => "center"), array("class" => "calendar_mobile"));
		$table->insertAction("back", gettext("previous month"), $urlprev);
		$table->addSpace(1);
		$table->addCode(utf8_encode(ucfirst(strftime("%B %Y", mktime(0, 0, 0, $month, 1, $year)))));
		$table->addSpace(1);
		$table->insertAction("forward", gettext("next month"), $urlnext);
		$table->endTag("div");
		/* table for calendar */
		$caltable = new Layout_table(array("cellspacing"=>1, "class"=>"calendar_table"));
		$caltable->addTableRow();
			$caltable->addTableData();
				$caltable->insertTag("div", gettext("sun")." ", array("class" => "calendar_mobile"));
				$caltable->insertTag("div", gettext("mon")." ", array("class" => "calendar_mobile"));
				$caltable->insertTag("div", gettext("tue")." ", array("class" => "calendar_mobile"));
				$caltable->insertTag("div", gettext("wed")." ", array("class" => "calendar_mobile"));
				$caltable->insertTag("div", gettext("thu")." ", array("class" => "calendar_mobile"));
				$caltable->insertTag("div", gettext("fri")." ", array("class" => "calendar_mobile"));
				$caltable->insertTag("div", gettext("sat")." ", array("class" => "calendar_mobile"));
				$caltable->insertTag("div", "w", array("class" => "calendar_mobile"));
			$caltable->endTableData();
		$caltable->endTableRow();
		$caltable->addTableRow();
			$caltable->addTableData();
			/* Skip weekdays to first day of the month */
			$skipdays = date("w",mktime(0,0,0,$month,1,$year));
			$dow=$skipdays;
			for ($i=0; $i!=$skipdays; $i++) {
				$caltable->insertTag("div", "<font color='#eeeeee'>00</font>"." ", array("class" => "calendar_mobile"));
			}
			/* display days of the month */
			for ($i=0; $i!=date("t",mktime(0,0,0,$month,1,$year)); $i++) {
				$url = "index.php?mod=calendar&day=".($i+1)."&month=$month&year=$year&extrauser=".implode(",", $userlist);
				$caltable->addTag("div", array("class" => "calendar_mobile"));
					$caltable->insertLink(($i+1<=10) ? "0".$i:$i, array("href" => $url));
					$caltable->addCode(" ");
				$caltable->endTag("div");
				$dow++;
				if ($dow == 7) {
					/* end of the week, start new row, but first print week number */
					if (!(($i+1)==date("t",mktime(0,0,0,$month,1,$year)))) {
						$caltable->insertTag("div", "<i>".date("W",mktime(0,0,0,$month,$i,$year))."</i>", array("class" => "calendar_mobile"));
						$caltable->addTag("br");
						$lastweek = date("W",mktime(0,0,0,$month,$i+7,$year));
					}
					$dow = 0;
				}
			}
			/* complete the fields so table looks better */
			if ($dow) {
				for ($i=0; $i!=7-$dow; $i++) {
					$caltable->insertTag("div", "&nbsp;", array("class" => "calendar_mobile"));
				}
			}
			$caltable->insertTag("div", "<i>".$lastweek."</i>", array("class" => "calendar_mobile"));
			$caltable->endTableData();
		$caltable->endTableRow();
		$caltable->endTable();
		$table->addCode($caltable->generate_output());
		unset($caltable);
		/* lil table and form for direct jumping in the calendar */
		$searchtable = new Layout_table();
		$searchtable->addTableRow();
			$searchtable->addTableData();
				$searchtable->addTextField("search_day", $day, array("style"=>"width: 25px;"));
				$searchtable->addTextField("search_month", $month, array("style"=>"width: 25px;"));
				$searchtable->addTextField("search_year", $year, array("style"=>"width: 50px;"));
				$searchtable->insertAction("forward", gettext("search"), "javascript: date_jump();");
			$searchtable->endTableData();
		$searchtable->endTableRow();
		$searchtable->addTableRow();
			$searchtable->addTableData();
				$searchtable->addTextField("search_term", gettext("search for"), array("style"=>"width: 150px;"), "", 1);
				$searchtable->insertAction("forward", gettext("search"), "javascript: search_jump();");
			$searchtable->endTableData();
		$searchtable->endTableRow();
		$searchtable->endTable();
		$table->addCode($searchtable->generate_output());
		$this->_table = $table;

	}
	/* }}} */
	/* generateCalendar {{{ */
	public function generateCalendar($month, $day, $year, $userlist) {
		//return false;
		if ($GLOBALS["covide"]->mobile) {
			$this->generateCalendarMobile($month, $day, $year, $userlist);
			return;
		}

		$table = $this->_table;
		$baseurl = "index.php?mod=calendar&day=1&extrauser=".implode(",", $userlist);
		$urlnext = $baseurl."&month=".($month+1)."&year=$year";
		if ($month == 1) {
			$urlprev = $baseurl."&year=".($year-1)."&month=12";
		} else {
			$urlprev = $baseurl."&year=$year&month=".($month-1);
		}
		$table->addTag("div", array("id" => "sidebar"));
		$table->addTag("div", array("id" => "menucalendar", "style" => ""));
		$table->insertAction("back", gettext("previous month"), $urlprev);
		$table->addSpace(1);
		$table->addCode(utf8_encode(ucfirst(strftime("%B %Y", mktime(0, 0, 0, $month, 1, $year)))));
		$table->addSpace(1);
		$table->insertAction("forward", gettext("next month"), $urlnext);
		/* table for calendar */

		$caltable = new Layout_table(array("cellspacing"=>1,
			"class"=>"calendar_table"
		));
		$caltable->addTableRow();
			$caltable->insertTableData(gettext("sun"), "", "header");
			$caltable->insertTableData(gettext("mon"), "", "header");
			$caltable->insertTableData(gettext("tue"), "", "header");
			$caltable->insertTableData(gettext("wed"), "", "header");
			$caltable->insertTableData(gettext("thu"), "", "header");
			$caltable->insertTableData(gettext("fri"), "", "header");
			$caltable->insertTableData(gettext("sat"), "", "header");
			$caltable->insertTableData("w", "", "header");
		$caltable->endTableRow();
		$caltable->addTableRow();
			/* Skip weekdays to first day of the month */
			$skipdays = date("w",mktime(0,0,0,$month,1,$year));
			$dow=$skipdays;
			for ($i=0; $i!=$skipdays; $i++) {
				$caltable->insertTableData("&nbsp;", "", "data");
			}
			/* display days of the month */
			for ($i=0; $i!=date("t",mktime(0,0,0,$month,1,$year)); $i++) {
				$url = "index.php?mod=calendar&day=".($i+1)."&month=$month&year=$year&extrauser=".implode(",", $userlist);
				if (($i+1) == date("d") && $month == date("m") && $year == date("Y")) {
					$caltable->addTableData("", "header");
				} else {
					$caltable->addTableData("", "data");
				}
					$caltable->insertLink($i+1, array("href" => $url));
				$caltable->endTableData();
				$dow++;
				if ($dow == 7) {
					/* end of the week, start new row, but first print week number */
					if (!(($i+1)==date("t",mktime(0,0,0,$month,1,$year)))) {
						$caltable->insertTableData("<i>".date("W",mktime(0,0,0,$month,$i,$year))."</i>", "", "data");
						$caltable->endTableRow();
						$caltable->addTableRow();
						$lastweek = date("W",mktime(0,0,0,$month,$i+7,$year));
					}
					$dow = 0;
				}
			}
			/* complete the fields so table looks better */
			if ($dow) {
				for ($i=0; $i!=7-$dow; $i++) {
					$caltable->insertTableData("&nbsp;", "", "data");
				}
			}
			$caltable->insertTableData("<i>".$lastweek."</i>", "", "data");
		$caltable->endTableRow();
		$caltable->endTable();
		$table->addCode($caltable->generate_output());
		unset($caltable);
		/* lil table and form for direct jumping in the calendar */
		$searchtable = new Layout_table();
		$searchtable->addTableRow();
			$searchtable->addTableData(array("align" => "left"));
				$searchtable->addTextField("search_day", $day, array("style"=>"width: 25px;"));
				$searchtable->addTextField("search_month", $month, array("style"=>"width: 25px;"));
				$searchtable->addTextField("search_year", $year, array("style"=>"width: 50px;"));
				$searchtable->insertAction("forward", gettext("search"), "javascript: date_jump();");
			$searchtable->endTableData();
		$searchtable->endTableRow();
		$searchtable->endTable();
		$table->addCode($searchtable->generate_output());

		$rcpt = implode(",", $userlist);
		$table_user = new Layout_table(array("cellspacing"=>1));
		$table_user->addTableRow();
			$table_user->addTag("br");
			$table_user->insertTableData("<p>".gettext("different users' calendar:")."</p>", array(
				"class" => "valign_top",
				"align" => "left",
			), "");
		$table_user->endTableRow();
		$table_user->addTableRow();
			$table_user->addTableData(array("align" => "left"), "");
				$table_user->addHiddenField("extrauser", $rcpt);
				$useroutput = new User_output();
				$table_user->addCode( $useroutput->user_selection("extrauser", $rcpt, 1, 0, 0, 0, 1) );
				$table_user->insertAction("forward", gettext("apply"), "javascript: document.getElementById('calendarform').submit();");
			$table_user->endTableData();
		$table_user->endTableRow();
		$table_user->endTable();
		$table->addCode($table_user->generate_output());

		$table->endTag("div");
		$this->_table = $table;
	}
	/* }}} */
	/* addVensterData {{{ */
	/**
	 * adds header and menu to output buffer and starts maincontent
	 */
	public function addVensterData() {
		//flush
		$table = $this->_table;
		$_output = $this->_output;
		$this->addCode($table->generate_output());
		if ($this->_nofullwidth == 1) {
			$this->endTag("div");
		}
		$this->addCode(str_replace("__maindiv__", $this->_maindiv, $_output->generate_output()));
		unset($this->_output);
		$this->addCode("\n\n");
		$this->addTag("div", array("class" => "mod_content"));
		$this->addCode("\n\n");
	}
	/* }}} */
	/* endVensterData {{{ */
	/**
	 * Ends the maincontent and adds comment that the frame ends here
	 */
	public function endVensterData() {
		$this->addCode("\n\n");
		$this->endTag("div");
		$this->addCode("\n\n");
		$this->addTag("div", array("id" => "mod_footer"));
		$this->addTag("div", array("id" => "mod_footer_menu", "class" => "fl_right"));
		$this->addCode(str_replace("header_menu_toggleMoreActions('top')", "header_menu_toggleMoreActions('bottom')", str_replace("mod_header_menu_moreactions_top", "mod_header_menu_moreactions_bottom", $this->_menuitemscode)));
		$this->endTag("div");
		$this->endTag("div");
		$this->addCode("\n\n");
		$this->endTag("div");
		$this->addComment("end venster object");
		$this->start_javascript();
			$this->addCode("
				function header_menu_toggleMoreActions(whichone) {
					if (document.getElementById('mod_header_menu_moreactions_'+whichone)) {
						var el_moreactions = document.getElementById('mod_header_menu_moreactions_'+whichone);
						if (el_moreactions.style.display == 'none') {
							el_moreactions.style.display='block';
						} else {
							el_moreactions.style.display='none';
						}
					}
				}
			");
		$this->end_javascript();
	}
	/* }}} */
	/* generate_output {{{ */
	/**
	 * Overload layout_output function to generate the final html code
	 *
	 * @param int $no_mobile if set and we are in mobile output generate output anyways
	 *
	 * @return string The html for the frame and it's contents
	 */
	public function generate_output($no_mobile=0) {
		if (!$GLOBALS["covide"]->mobile || !$no_mobile) {
			return str_replace("<!-- __menuitems__ -->", $this->_menuitemscode, $this->output);
		}
	}
	/* }}} */
}
?>
