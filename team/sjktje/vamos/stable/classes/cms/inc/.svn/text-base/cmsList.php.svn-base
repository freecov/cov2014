<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("list")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getListData($id);

	$this->addMenuItems(&$venster);
	$venster->generateMenuItems();
	$venster->addVensterData();

	$tbl = new Layout_table(array(
		"cellspacing" => 1
	));
	/* criteria */
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>3), "header");
			$tbl->insertAction("view_all", "", "");
			$tbl->addSpace();
			$tbl->addCode(gettext("Specify what results to show on the page."));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>3), "data");

			/* create a new view and add the data */
			$view = new Layout_view();
			$view->addData($cms["_query"]);
			$view->addMapping(gettext("order"), "%3");
			$view->addMapping(gettext("and/or"), "%4");
			$view->addMapping(gettext("fieldname"), "%0");
			$view->addMapping(gettext("expression"), "%1");
			$view->addMapping(gettext("value"), "%2");
			$view->addMapping("", "%%complex_actions");

			/* define complex flags fromto */
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type"  => "action",
					"src"   => "delete",
					"alt"   => gettext("to remove this condition"),
					"link" => array("?mod=cms&action=cmsDeleteListItem&id=".$_REQUEST["id"]."&item=", "%5")
				)
			));
			$tbl->addCode($view->generate_output());

		$tbl->endTableData();
	$tbl->endTableRow();
	/* new criteria */
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>3), "header");
			$tbl->insertAction("new", "", "");
			$tbl->addSpace();
			$tbl->addCode(gettext("new condition"));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("and/or"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$sel = array(
				"0"   => sprintf("- %s -", gettext("select")),
				"en" => gettext("and"),
				"of" => gettext("or")
			);
			$tbl->addSelectField("new[andor]", $sel, "");
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addCode( gettext("search for both conditions or search for one of the conditions") );
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("field"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$sel = array(
				"0"   => sprintf("- %s -", gettext("select")),
				gettext("default fields") => array(
					"parentpage"  => gettext("parent page"),
					"paginaTitel" => gettext("title"),
					"pageLabel"   => gettext("label"),
					"keywords"    => gettext("keywords")
				),
				gettext("datum velden (optionele datum module)") => array(
					"daterange" => gettext("date - range"),
					"dateday"   => gettext("date - day of week")
				),
				gettext("metadata velden (optionele metadata module)") => array()
			);
			$meta = $cms_data->getMetadataDefinitions();
			foreach ($meta as $name=>$group) {
				foreach ($group as $k=>$v) {
					$sel[gettext("metadata velden (optionele metadata module)")]["meta".$v["id"]] = sprintf("[%s] %s", ($v["group"]) ? $v["group"]:gettext("default group"), $v["field_name"]);
				}
			}
			$tbl->addSelectField("new[newfield]", $sel, "");
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("what field to filter on"));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow(array(
		"id" => "dtext1",
		"style" => "display: auto;"
	));
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("expression"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$sel = array(
				"0"         => sprintf("- %s -", gettext("select")),
				"is"        => gettext("[ field = 'value' ] fieldcontent is the same as fieldvalue"),
				"islike"    => gettext("[ field = '*value*' ] fieldcontent matches fieldvalue"),
				"isnot"     => gettext("[ field != 'value' ] fieldcontent is not fieldvalue"),
				"isnotlike" => gettext("[ field != '*value*' ] fieldcontent does not match fieldvalue")
			);
			$tbl->addSelectField("new[operator]", $sel, "");
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("match on exact word or only partual"));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow(array(
		"id" => "dtext2",
		"style" => "display: auto;"
	));
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("value"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addTextField("new[value]", "", array("style" => "width: 300px;"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("welk woord moet wel (of niet) in de kolom voorkomen. U kunt een lijst van gebruikte woorden opvragen door op het icoon links te klikken"));
		$tbl->endTableData();
	$tbl->endTableRow();
	/* date range selections */
	for ($i=1; $i<=31; $i++) {
		$days[$i] = $i;
	}
	for ($i=1; $i<=12; $i++) {
		$months[$i] = $i;
	}
	for ($i=2003; $i<=date("Y")+5; $i++) {
		$years[$i] = $i;
	}
	$calendar = new Calendar_output();
	$tbl->addTableRow(array(
		"id" => "ddate1",
		"style" => "display: none;"
	));
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("start date"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addSelectField("new[start_day]", $days, "");
			$tbl->addSelectField("new[start_month]", $months, "");
			$tbl->addSelectField("new[start_year]", $years, "");
			$tbl->addCode( $calendar->show_calendar("document.getElementById('newstart_day')", "document.getElementById('newstart_month')", "document.getElementById('newstart_year')" ));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow(array(
		"id" => "ddate2",
		"style" => "display: none;"
	));
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("end date"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addSelectField("new[end_day]", $days, "");
			$tbl->addSelectField("new[end_month]", $months, "");
			$tbl->addSelectField("new[end_year]", $years, "");
			$tbl->addCode( $calendar->show_calendar("document.getElementById('newend_day')", "document.getElementById('newend_month')", "document.getElementById('newend_year')" ));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow(array(
		"id" => "dday1",
		"style" => "display: none;"
	));
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("day of week"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			foreach ($cms_data->weekdays as $k=>$v) {
				$tbl->addCheckBox("repeat[$k]", 1, 0);
				$tbl->addSpace();
				$tbl->addCode($v);
				$tbl->addTag("br");
			}
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData("", "data");
			$tbl->addCode(gettext("position"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$sel = array();
			for ($i=1;$i<=25;$i++) {
				$sel[$i] = $i;
			}
			$tbl->addSelectField("new[position]", $sel, 1);
			$tbl->addSpace();
			$tbl->insertAction("save", gettext("add"), "javascript: saveSettings();");
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addSpace();
		$tbl->endTableData();
	$tbl->endTableRow();

	/* which fields */
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>3), "header");
			$tbl->insertAction("view", "", "");
			$tbl->addSpace();
			$tbl->addCode(gettext("pick fields to show"));
		$tbl->endTableData();
	$tbl->endTableRow();

	$fields["def"] = array(
		"paginaTitel" => gettext("page title"),
		"pageLabel"   => gettext("page label"),
		"keywords"    => gettext("keywords"),
		"datumPublicatie" => gettext("publication date")
	);
	$fields["date"] = array(
		"datefull"    => gettext("volledige datum(s) (optionele datum module)"),
		"dateweekday" => gettext("weekdag(en) (optionele datum module)"),
		"datemonth"   => gettext("maand(en) (optionele datum module)")
	);
	$sel = array(
		"0" => sprintf("- %s -", gettext("dont show"))
	);
	for ($i=1; $i<=15; $i++) {
		$sel[gettext("show on position")][$i] = sprintf("%s: %s", gettext("position"), ($i==1) ? $i." + ".gettext("link"):$i);
	}
	foreach ($fields["def"] as $k=>$v) {
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "data nowrap");
				$tbl->addCode(sprintf("%s: %s", gettext("field"), $v));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSelectField("fields[$k]", $sel, $cms["_fields"][$k]);
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan" => 2), "header");
		$tbl->endTableData();
	$tbl->endTableRow();
	foreach ($fields["date"] as $k=>$v) {
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "data nowrap");
				$tbl->addCode(sprintf("%s: %s", gettext("date field"), $v));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSelectField("fields[$k]", $sel, $cms["_fields"][$k]);
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan" => 2), "header");
		$tbl->endTableData();
	$tbl->endTableRow();
	foreach ($meta as $name=>$group) {
		foreach ($group as $k=>$v) {
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan"=>2), "data nowrap");
					$tbl->addCode(sprintf("%s: [%s] %s", gettext("meta field"), ($name) ? $name:gettext("default group"), $v["field_name"]));
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					$tbl->addSelectField("fields[meta".$v["id"]."]", $sel, $cms["_fields"]["meta".$k]);
				$tbl->endTableData();
			$tbl->endTableRow();
		}
	}


	/* sort order */
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>3), "header");
			$tbl->insertAction("toggle", "", "");
			$tbl->addSpace();
			$tbl->addCode(gettext("Way to sort"));
		$tbl->endTableData();
	$tbl->endTableRow();

	$sel = array(
		"0"   => sprintf("- %s -", gettext("select")),
		gettext("default fields") => array(
			"parentpage"  => gettext("parent page"),
			"paginaTitel" => gettext("title"),
			"pageLabel"   => gettext("label"),
			"keywords"    => gettext("keywords")
		),
		gettext("metadata velden (optionele metadata module)") => array()
	);
	$sel2 = array(
		"asc"  => "+ ".gettext("ascending"),
		"desc" => "-&nbsp; ".gettext("descending")
	);
	foreach ($meta as $name=>$group) {
		foreach ($group as $k=>$v) {
			if ($v["field_type"] != "checkbox")
				$sel[gettext("metadata velden (optionele metadata module)")]["meta".$v["id"]] = sprintf("[%s] %s*", ($v["group"]) ? $v["group"]:gettext("default group"), $v["field_name"]);
		}
	}

	for ($i=0;$i<3;$i++) {
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "data nowrap");
				$tbl->addCode(sprintf("%d. %s", $i+1, gettext("sort")));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSelectField("order[".$i."_sort]", $sel, $cms["_order"][$i]["sort"]);
				$tbl->addSelectField("order[".$i."_asc]", $sel2, $cms["_order"][$i]["asc"]);
			$tbl->endTableData();
		$tbl->endTableRow();
	}


	/* placement */
	$sel = array(
		"onder" => gettext("below page data"),
		"boven" => gettext("above page data")
	);
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>3), "header");
			$tbl->insertAction("up", "", "");
			$tbl->addSpace();
			$tbl->addCode(gettext("Where to show the list"));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "data nowrap");
			$tbl->addCode(sprintf("%s", gettext("show list on position")));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addSelectField("position", $sel, $cms["_position"]);
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>3), "header");
			$tbl->insertAction("save", gettext("save options"), "javascript: saveSettings();");
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();


	$venster->addCode($tbl->generate_output());
	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "cmsSaveList");
	$output->addHiddenField("id", $_REQUEST["id"]);

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->load_javascript(self::include_dir."script_cms.js");
	$output->start_javascript();
		$output->addCode("
			document.getElementById('newnewfield').onchange = function() {
				redrawopts(document.getElementById('newnewfield').value);
			}
		");
	$output->end_javascript();

	$output->layout_page_end();
	$output->exit_buffer();
?>
