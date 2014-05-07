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
 * @copyright Copyright 2000-2006 Covide BV
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
	private $_hasmenuitems = 0;

	/* methods   */

  public function __construct($settings="") {
		$this->output     = "";
		$this->addComment("begin venster object");

		$this->menu_items = array();
		if (!is_array($settings)) {
			$settings = array();
		}
		$table = new Layout_table( array("class"=>"window_header") );

		if ($GLOBALS["covide"]->venster > 0)
			$settings["skip_id"] = 1;

		if ($settings["title"]) {
			$table->addTableRow();
			$table->addTableData( array("colspan"=>2) );
				if ($settings["skip_id"]) {
					$table->addTag("span", array("class"=>"onderdeel") );
				} else {
					$table->addTag("span", array("class"=>"onderdeel", "id" => "venster_onderdeel"));
					$GLOBALS["covide"]->venster++;
				}
				if (!$GLOBALS["covide"]->mobile) {
					$table->addCode($settings["title"]);
				} else {
					$table->insertTag("b", $settings["title"]);
				}
				$table->endTag("span");
				$table->addSpace(2);
				if ($settings["skip_id"]) {
					$table->addTag("span", array("class"=>"titel") );
				} else {
					$table->addTag("span", array("class"=>"titel", "id"=>"venster_titel") );
				}
				$table->addCode($settings["subtitle"]);
				$table->endTag("span");
			$table->endTableData();
			$table->endTableRow();
		}
		$this->_table = $table;
  }

	public function addMenuItem($name, $link, $new_window=0) {
		$this->menu_items[$name] = array($link, (int)$new_window);
	}

	public function generateMenuItems() {
		$this->_hasmenuitems = 1;
		$table = $this->_table;

		$extra_menuitems = $GLOBALS["covide"]->contrib["USE_CONTRIB_MENU_ITEMS"];
		if ($extra_menuitems) {
			if (!is_array($extra_menuitems))
				$extra_menuitems = array($extra_menuitems);

			foreach ($extra_menuitems as $m) {
				$this->addMenuItem(
					$m["name"],
					$m["link"],
					(int)$m["popup"]
				);
			}
		}

		$table->addTableRow();
		$table->addTableData( array(
			"style"=>"vertical-align: top;"
		));

		$table->insertImage("menu_top.gif","","",1);
		if (!$GLOBALS["covide"]->mobile) {
			$menu = new Layout_table();
			foreach ($this->menu_items as $n=>$l) {
				$menu->addTableRow();
					$menu->addTableData();
						$menu->insertImage("menu_link_l.gif", "", "", 1);
					$menu->endTableData();
					$menu->addTableData( array(
						"class"    => "menuLnk",
						"width"    => 114,
						"height"   => 18,
						"style"    => "text-align: center;"
						), "nowrap");
						$menu->insertLink($n, array(
							"href"   => $l[0],
							"class"  => "menu",
							"target" => ($l[1]) ? "_blank":""
						));
					$menu->endTableData();
					$menu->addTableData();
						$menu->insertImage("menu_link_r.gif", "", "", 1);
					$menu->endTableData();
				/* end menu item */
				$menu->endTableRow();
			}
			$menu->endTable();
			$table->addCode ($menu->generate_output() );
		} else {
			/* mobile */
			foreach ($this->menu_items as $n=>$l) {
				$table->insertLink("[".$n."]", array(
					"href"   => $l[0],
					"class"  => "menu",
					"target" => ($l[1]) ? "_blank":""
				));
				$table->addCode(" ");
			}
		}
		$table->insertImage("menu_onder.gif","","",1);
		$this->_table = $table;
	}

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
		$table->addCode(ucfirst(strftime("%B %Y", mktime(0, 0, 0, $month, 1, $year))));
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

	public function generateCalendar($month, $day, $year, $userlist) {
		if ($GLOBALS["covide"]->mobile) {
			$this->generateCalendarMobile($month, $day, $year, $userlist);
			return;
		}

		$table = $this->_table;
		$table->addTag("br");
		$table->addTag("br");
		$baseurl = "index.php?mod=calendar&day=1&extrauser=".implode(",", $userlist);
		$urlnext = $baseurl."&month=".($month+1)."&year=$year";
		if ($month == 1) {
			$urlprev = $baseurl."&year=".($year-1)."&month=12";
		} else {
			$urlprev = $baseurl."&year=$year&month=".($month-1);
		}
		$table->addTag("div", array("align" => "center"));
		$table->insertAction("back", gettext("previous month"), $urlprev);
		$table->addSpace(1);
		$table->addCode(ucfirst(strftime("%B %Y", mktime(0, 0, 0, $month, 1, $year))));
		$table->addSpace(1);
		$table->insertAction("forward", gettext("next month"), $urlnext);
		$table->endTag("div");
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

	public function addVensterData() {
		//flush
		$table = $this->_table;
		$this->addCode( $table->generate_output() );
		if ($this->_hasmenuitems) {
			$class = "venster_data";
			$this->endTag("td");
		} else {
			$this->addTag("tr");
			$class = "venster_data venster_left_nomenu";
		}
		$this->addTag("td", array(
			"class" => $class
		));

		unset($this->_table);
	}

	public function endVensterData() {
		$this->endTag("td");
		$this->addTag("td", array("class"=>"venster_right") );
			$this->insertTag("div", "", array(
				"class" => "venster_right_spacer"
			));
			$this->addSpace();
		$this->endTag("td");
		$this->endTag("tr");
		$this->endTag("table");
		$this->addComment("end venster object");
	}

	public function generate_output($no_mobile=0) {
		if (!$GLOBALS["covide"]->mobile || !$no_mobile) {
			return $this->output;
		}
	}

}
?>
