<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Cms_output")) {
		die("no class definition found");
	}

	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => ($id==-1) ? gettext("banners"):gettext("photo gallery")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getGalleryData($id, $_REQUEST["cmsfilter"]);

	if ($id > -1) {
		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
	}
	$venster->addVensterData();

	$cmsfilter = $_REQUEST["cmsfilter"];
	if (!$cmsfilter["s_timestamp_year"]) {
		$cmsfilter["date_start"] = mktime();
	} else {
		$cmsfilter["date_start"] = mktime(0,0,0,
			$cmsfilter["s_timestamp_month"],
			$cmsfilter["s_timestamp_day"],
			$cmsfilter["s_timestamp_year"]
		);
	}
	if (!$cmsfilter["e_timestamp_year"]) {
		$cmsfilter["date_end"] = mktime();
	} else {
		$cmsfilter["date_end"] = mktime(0,0,0,
			$cmsfilter["e_timestamp_month"],
			$cmsfilter["e_timestamp_day"],
			$cmsfilter["e_timestamp_year"]
		);
	}

	$venster->addTag("form", array(
		"id"     => "date_range",
		"method" => "get",
		"action" => "index.php"
	));
	$venster->addHiddenField("mod", "cms");
	$venster->addHiddenField("action", "cmsgallery");
	$venster->addHiddenField("id", $id);

	$tbl = new Layout_table();
	if ($id == -1) {
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->insertAction("calendar_reg_hour", "", "");
				$tbl->addSpace();
				$tbl->addCode(gettext("select date range"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("start date"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				for ($i=1; $i<=31; $i++) {
					$days[$i] = $i;
				}
				for ($i=1; $i<=12; $i++) {
					$months[$i] = $i;
				}
				for ($i=2000; $i<=date("Y")+5; $i++) {
					$years[$i] = $i;
				}
				for ($i=0; $i<=23; $i++) {
					$hour[$i] = $i;
				}
				for ($i=0; $i<60; $i+=15) {
					$min[$i] = sprintf("%02s", $i);
				}

				$tbl->addSelectField("cmsfilter[s_timestamp_day]",   $days,   date("d", $cmsfilter["date_start"]));
				$tbl->addSelectField("cmsfilter[s_timestamp_month]", $months, date("m", $cmsfilter["date_start"]));
				$tbl->addSelectField("cmsfilter[s_timestamp_year]",  $years,  date("Y", $cmsfilter["date_start"]));
				$calendar = new Calendar_output();
				$tbl->addCode( $calendar->show_calendar("document.getElementById('cmsfilters_timestamp_day')", "document.getElementById('cmsfilters_timestamp_month')", "document.getElementById('cmsfilters_timestamp_year')" ));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("end date"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");

				$tbl->addSelectField("cmsfilter[e_timestamp_day]",   $days,   date("d", $cmsfilter["date_end"]));
				$tbl->addSelectField("cmsfilter[e_timestamp_month]", $months, date("m", $cmsfilter["date_end"]));
				$tbl->addSelectField("cmsfilter[e_timestamp_year]",  $years,  date("Y", $cmsfilter["date_end"]));
				$calendar = new Calendar_output();
				$tbl->addCode( $calendar->show_calendar("document.getElementById('cmsfiltere_timestamp_day')", "document.getElementById('cmsfiltere_timestamp_month')", "document.getElementById('cmsfiltere_timestamp_year')" ));
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode(gettext("search"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addTextField("cmsfilter[search]", $cmsfilter["search"]);
			$tbl->insertAction("forward", gettext("filter"), "javascript: document.getElementById('date_range').submit();");
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();
	$venster->addCode($tbl->generate_output());

	$venster->endTag("form");

	$view = new Layout_view();
	$view->addData($cms);
	$view->addMapping(gettext("order"), "%order");
	if ($id > 0)
		$view->addMapping(gettext("change"), "%%complex_order");

	$view->addMapping(gettext("name"), "%file_short");
	if ($id == -1) {
		$view->addMapping(gettext("rating"), "%rating_h");
		$view->addMapping(gettext("website"), "%url");
		$view->addMapping(gettext("views"), "%views");
		$view->addMapping(gettext("visits"), "%visits");
	} else {
		$view->addMapping(gettext("description"), "%description_h");
		$view->addMapping(gettext('resolution'), '%resolution');
	}
	$view->addMapping("%%complex_action_header", "%%complex_actions", "", "nowrap");

	$view->defineComplexMapping("complex_action_header", array(
		array(
			"type" => "action",
			"src"  => "delete",
			"alt"  => gettext("delete all"),
			"link" => array("javascript: if (confirm(gettext('Are you sure you want to delete ALL items in this gallery?'))) document.location.href='index.php?mod=cms&action=cmsgalleryitemdelete&item=-1&id=$id&pageid=$id&itemid=-1';")
		)
	));

	$view->defineComplexMapping("complex_actions", array(
		array(
			"type" => "action",
			"src"  => "edit",
			"alt"  => gettext("edit"),
			"link" => array("javascript: popup('?mod=cms&action=cmsgalleryitemedit&id=", "%pageid", "&itemid=", "%id", "', 'gallery', 780, 550, 1);")
		),
		array(
			"type" => "action",
			"src"  => "delete",
			"alt"  => gettext("delete"),
			"link" => array("javascript: if (confirm(gettext('Are you sure you want to delete this entry?'))) document.location.href='index.php?mod=cms&action=cmsgalleryitemdelete&item=", "%id" ,"&id=", "%pageid", "&itemid=", "%id", "';")
		)
	));
	$view->defineComplexMapping("complex_order", array(
		array(
			"type" => "action",
			"src"  => "up",
			"alt"  => gettext("up"),
			"link" => array("?mod=cms&action=galleryitemswitch&direction=up&id=", "%pageid", "&itemid=", "%id")
		),
		array(
			"type" => "action",
			"src"  => "down",
			"alt"  => gettext("down"),
			"link" => array("?mod=cms&action=galleryitemswitch&direction=down&id=", "%pageid", "&itemid=", "%id")
		),
	));

	if (count($cms) >= 6) {
		$limit_height = "height: 300px; overflow:auto;";
	} else {
		$limit_height = "";
	}

	$venster->addTag("div", array(
		"class"  => "limit_height",
		"style" => $limit_height
	));
		$venster->addCode($view->generate_output());
	$venster->endTag("div");

	$fs_output = new Filesys_output();
	$venster->addTag("hr");
	$venster->addTag("form", array(
		"id"     => "file_upload",
		"method" => "post",
		"action" => "index.php",
		"enctype" => "multipart/form-data"
	));
	$venster->addCode($fs_output->show_fileupload()->generate_output());
	$venster->endTag("form");
	$venster->start_javascript();
		$venster->addCode("
			function filesys_upload_files() {
				document.getElementById('file_upload').submit();
			}
		");
	$venster->end_javascript();

	$venster->addTag("br");
	$venster->addTag("br");

	$cms = $cms_data->getGallerySettings($id);

	$table = new Layout_table(array(
		"cellspacing" => 1,
		"cellpadding" => 1
	));
	/* layout settings */
	if ($id > -1) {
		$table->addTableRow();
			$table->insertTableData(gettext("Font settings"), "", "header");
			$table->addTableData();
				$conversion = new Layout_conversion();
				$fonts = $conversion->getFonts();

				$table->addSelectField("cms[font]", $fonts["fonts"], $cms["font"]);
				$table->addSelectField("cms[fontsize]", $fonts["sizes"], $cms["font_size"]);
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->insertTableData(gettext("Layout"), "", "header");
			$table->addTableData("", "data");
				$sel = array(
					0 => gettext("table"),
					1 => gettext("slideshow"),
					//2 => gettext("slideshow (horizontal)"),
					3 => gettext("listing")
				);
				$table->addSelectField("cms[gallerytype]", $sel, $cms["gallerytype"]);
			$table->endTableData();
		$table->endTableRow();
		/* cols */
		$table->addTableRow(array(
			"id" => "layer_cols"
		));
			$table->addTableData("", "header");
				$table->addCode(gettext("Number of columns"));
				$table->addTag("br");
				$table->addSpace();
				$table->addCode(gettext("(if table layout)"));
			$table->endTableData();
			$table->addTableData("", "data");
				$sel = array();
				for ($i=1;$i<=10;$i++) {
					$sel[$i]=$i;
				}
				$table->addSelectField("cms[cols]", $sel, $cms["cols"]);
			$table->endTableData();
		$table->endTableRow();
		/* cols */
		$table->addTableRow(array(
			"id" => "layer_rows"
		));
			$table->addTableData("", "header");
				$table->addCode(gettext("Number of rows per page"));
				$table->addTag("br");
				$table->addSpace();
				$table->addCode(gettext("(if table or list layout)"));
			$table->endTableData();
			$table->addTableData("", "data");
				$sel = array();
				for ($i=1;$i<=10;$i++) {
					$sel[$i]=$i;
				}
				$table->addSelectField("cms[rows]", $sel, $cms["rows"]);
			$table->endTableData();
		$table->endTableRow();
		/* allow max size */
		$table->addTableRow();
			$table->insertTableData(gettext("Allow viewing full size"), "", "header");
			$table->addTableData("", "data");
				$sel = array(
					0 => gettext("no"),
					1 => gettext("yes")
				);
				$table->addSelectField("cms[fullsize]", $sel, $cms["fullsize"]);
			$table->endTableData();
		$table->endTableRow();
		/* thumbnail size medium */
		$table->addTableRow();
			$table->insertTableData(gettext("Max thumbnail size (medium)"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("cms[bigsize]", $cms["bigsize"]);
			$table->endTableData();
		$table->endTableRow();
	} else {
		$table->addHiddenField("cms[thumbsize]", 0);
		$table->addHiddenField("cms[gallerytype]", 0);
		$table->insertTag("span", "", array(
			"style" => "display: none;",
			"id"    => "layer_cols"
		));
		$table->insertTag("span", "", array(
			"style" => "display: none;",
			"id"    => "layer_rows"
		));
	}
	/* thumbnail size small */
	$table->addTableRow();
		$table->insertTableData(gettext("Max thumbnail size (small)"), "", "header");
		$table->addTableData("", "data");
			$table->addTextField("cms[thumbsize]", $cms["thumbsize"]);
		$table->endTableData();
	$table->endTableRow();

	$div = new Layout_output();
	$div->addTag("img", array(
		"src" => "img/bar.png"
	));
	$table->addTableRow(array(
		"id" => "layer_busy",
		"style" => "xdisplay: none;"
	));
		$table->addTableData("", "data");
			$table->insertTag("marquee", $div->generate_output(), array(
				"id"           => "marquee_progressbar",
				"behavoir"     => "scroll",
				"style"        => "width: 300px; background-color: #f1f1f1; height: 10px; border: 1px solid #b0b2c6; visibility:hidden; margin-top: 10px;",
				"scrollamount" => 3,
				"direction"    => "right",
				"scrolldelay"  => 60
			));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow(array(
		"id" => "layer_actions"
	));
		$table->addTableData("", "data");
			$table->insertAction("save", gettext("save"), "javascript: saveSettingsGallery();");
			$table->insertAction("close", gettext("close"), "javascript: window.close();");
		$table->endTableData();
	$table->endTableRow();

	$table->endTable();
	$venster->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$venster->addHiddenField("mod", "cms");
	$venster->addHiddenField("action", "saveGallerySettings");
	$venster->addHiddenField("id", $id);
	$venster->addCode($table->generate_output());
	$venster->endTag("form");


	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	$output->load_javascript(self::include_dir."script_cms.js");
	$output->start_javascript();
		$output->addCode("
			function updateLayerCols() {
				return true;
				document.getElementById('layer_cols').style.visibility = 'hidden';
				document.getElementById('layer_rows').style.visibility = 'hidden';
				if (document.getElementById('cmsgallerytype').value == 0) {
					document.getElementById('layer_cols').style.visibility = 'visible';
					document.getElementById('layer_rows').style.visibility = 'visible';
				}
				if (document.getElementById('cmsgallerytype').value == 3) {
					document.getElementById('layer_cols').style.visibility = 'visible';
				}
			}
			document.getElementById('cmsgallerytype').onchange = function() {
				updateLayerCols();
			}
			addLoadEvent(updateLayerCols());
		");
	$output->end_javascript();

	/* add a container for multiple downloads */
	$output->insertTag("div", "", array(
		"id"    => "download_container",
		"style" => "display: none; width: 0px; height: 0px;"
	));

	$output->exit_buffer();
?>
