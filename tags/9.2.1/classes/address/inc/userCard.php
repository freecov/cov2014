<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* FIXME: quick and dirty way to solve usercard for user 'none' */
if ($id == 0) {
	echo "<script language=\"javascript\">history.go(-1);</script>";
}
$address_id = $id;
/* get the userinfo */
$userdata = new User_data();
$user_id = $userdata->getUserIdByAddressId($id);
$userinfo = $userdata->getUserDetailsById($user_id);
$visitor_perms = $userdata->getUserPermissionsById($_SESSION["user_id"]);
/* get the address */
$address_data   = new Address_data();
$addressinfo[0] = $address_data->getAddressByID($address_id, "user");
/* get the active notes for this user */
$notes_data     = new Note_data();
$notearr        = $notes_data->getNotes(array("user_id" => $user_id, "nocustcont" => 1));
$noteinfo       = $notearr["notes"];
if ($GLOBALS["covide"]->license["has_project"] || $GLOBALS["covide"]->license["has_project_declaration"]) {
	/* get project info */
	$project_data = new Project_data();
	if ($_REQUEST["regitems"]["start_day"]) {
		$regitem_start_day = $_REQUEST["regitems"]["start_day"];
	} else {
		$regitem_start_day = date("d");
	}
	if ($_REQUEST["regitems"]["start_month"]) {
		$regitem_start_month = $_REQUEST["regitems"]["start_month"];
	} else {
		$regitem_start_month = date("m")-1;
	}
	if ($_REQUEST["regitems"]["start_year"]) {
		$regitem_start_year = $_REQUEST["regitems"]["start_year"];
	} else {
		$regitem_start_year = date("Y");
	}
	if ($_REQUEST["regitems"]["end_day"]) {
		$regitem_end_day = $_REQUEST["regitems"]["end_day"];
	} else {
		$regitem_end_day = date("d");
	}
	if ($_REQUEST["regitems"]["end_month"]) {
		$regitem_end_month = $_REQUEST["regitems"]["end_month"];
	} else {
		$regitem_end_month = date("m");
	}
	if ($_REQUEST["regitems"]["end_year"]) {
		$regitem_end_year = $_REQUEST["regitems"]["end_year"];
	} else {
		$regitem_end_year = date("Y");
	}
	$project_timestamp_start = mktime(0,0,0,$regitem_start_month,$regitem_start_day,$regitem_start_year);
	$project_timestamp_end = mktime(0,0,0,$regitem_end_month,$regitem_end_day,$regitem_end_year);
	$proj_info = $project_data->getProjectHoursByUserId($user_id, $project_timestamp_start, $project_timestamp_end);
}
if (!$GLOBALS["covide"]->license["disable_basics"]) {
	/* get the active todos */
	$todo_data = new Todo_data();
	if ($_REQUEST["history"] == "todos") {
		$todoinfo       = $todo_data->getTodosByUserId($user_id, 1);
		$subtitle_todo  = gettext("history");
	} else {
		$todoinfo       = $todo_data->getTodosByUserId($user_id);
		$subtitle_todo  = gettext("current");
	}
	/* get the appointments */
	$calendar_data = new Calendar_data();
	$calendarinfo  = $calendar_data->getAppointmentsByUser($user_id);
	/* kilometers */
	if ($GLOBALS["covide"]->license["has_project"]) {
		if ($GLOBALS["covide"]->license["has_project_declaration"]) {
			if ($_REQUEST["regitems"]["day"]) {
				$regitem_day = $_REQUEST["regitems"]["day"];
			} else {
				$regitem_day = date("d");
			}
			if ($_REQUEST["regitems"]["month"]) {
				$regitem_month = $_REQUEST["regitems"]["month"];
			} else {
				$regitem_month = date("m");
			}
			if ($_REQUEST["regitems"]["year"]) {
				$regitem_year = $_REQUEST["regitems"]["year"];
			} else {
				$regitem_year = date("Y");
			}
			$proj_dec = new ProjectDeclaration_data();
			$proj_data = new Project_data();
			$regitems = $proj_dec->getRegistrationItems(0, -1, 0, $user_id, mktime(0, 0, 0, $regitem_month, $regitem_day, $regitem_year));
			$reguren = 0;
			$regmoney = 0;
			foreach ($regitems as $k=>$v) {
				$regitems[$k]["project_name"] = $proj_data->getProjectNameById($v["project_id"]);
				$reguren+= $v["time_units"];
				$regmoney+= $v["total_price"];
			}
			$reguren = sprintf("%d:%d", $reguren/60, $reguren % 60);
			$regmoney = number_format($regmoney, 2, ".", ",");
			$regitems[] = array("description" => gettext("total"), "time_units" => $reguren, "total_price" => $regmoney);
		} else {
			/* 1 month ago */
			$kmarr1 = $calendar_data->getKmItems(array(
				"users" => array($user_id),
				"start" => mktime(0, 0, 0, date("m")-1, 1, date("Y")),
				"end"   => mktime(0, 0, 0, date("m"), 1, date("Y"))
			));
			$kminfo["prev"] = $kmarr1[$user_id];
			unset($kmarr1);
			/* this month */
			$kmarr1 = $calendar_data->getKmItems(array(
				"users" => array($user_id),
				"start" => mktime(0, 0, 0, date("m"), 1, date("Y")),
				"end"   => mktime(0, 0, 0, date("m")+1, 1, date("Y"))
			));
			$kminfo["now"] = $kmarr1[$user_id];
		}
		/* get projects */
		$projectdata = new Project_data();
		$projectoptions = array("user_id" => $user_id, "active" => 1);
		$projectinfo = $projectdata->getProjectsBySearch($projectoptions);
		$proj_executor = array();
		$proj_manager  = array();
		$proj_other    = array();
		if (is_array($projectinfo)) {
			foreach ($projectinfo as $k=>$v) {
				if ($v["executor"] == $user_id) {
					$proj_executor[] = $v;
				} elseif ($v["manager"] == $user_id) {
					$proj_manager[] = $v;
				} else {
					$proj_other[] = $v;
				}
			}
		}
		unset($projectinfo);
		unset($projectoptions);
		unset($projectdata);
	}
	/* get the filesys data */
	if ($GLOBALS["covide"]->license["has_hrm"]) {
		$filesys_data = new Filesys_data();
		$filesysinfo = $filesys_data->getUserFolder($user_id);
	}
	/* get the project data */
	if ($GLOBALS["covide"]->license["has_project"]) {
		$project_data = new Project_data();
		$projectinfo  = $project_data->getProjectsBySearch(array("user_id" => $user_id));
		$hoursinfo = $project_data->getOverviewData(mktime(0, 0, 0, date("m")-3, 1, date("Y")), mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
	}
	/* get the support data */
	if ($GLOBALS["covide"]->license["has_issues"]) {
		$support_data = new Support_data();
		if ($_REQUEST["history"] == "support") {
			$supportinfo = $support_data->getSupportItems(array("user_id" => $user_id, "active" => 0, "nolimit" => 1));
			$subtitle_support = gettext("history");
		} else {
			$supportinfo = $support_data->getSupportItems(array("user_id" => $user_id, "active" => 1, "nolimit" => 1));
			$subtitle_support = gettext("current");
		}
	}
	/* get the sales items */
	if ($GLOBALS["covide"]->license["has_sales"]) {
		$sales_data = new Sales_data();
		if ($_REQUEST["history"] == "sales") {
			$sales_info = $sales_data->getSalesBySearch( array("user_id" => $user_id, "in_active" => 1) );
			$sales_subtitle = gettext("inactive items");
		} else {
			$sales_info = $sales_data->getSalesBySearch( array("user_id" => $user_id) );
			$sales_subtitle = gettext("active items");
		}
	}
}
/* get the active customer contact items for this relation */
$custcontarr  = $notes_data->getNotes(array("user_id" => $user_id, "custcont" => 1));
$custcontinfo = $custcontarr["notes"];

/* init the output object */
$output = new Layout_output();
$output->layout_page("usercard");

//dual column rendering
$buf1 = new Layout_output();
$buf2 = new Layout_output();
$buf2counter = 0;
/* address record */
$venster_settings = array(
	"title"    => gettext("information")
);
$venster = new Layout_venster($venster_settings);
unset($venster_settings);
$venster->addVensterData();
	$view = new Layout_view();
	$view->addData($addressinfo);
	/* specify layout */
	$view->addMapping(gettext("name"), array("%givenname", " ", "%surname"));
	$view->addMapping(gettext("picture"), "%photo");
	$view->addMapping(gettext("debtor nr"), "%debtor_nr");
	$view->addMapping(gettext("contact"), "%tav");
	$view->addMapping(gettext("address"), array("%address","\n","%address2"));
	$view->addMapping(gettext("zip code"), "%zipcode");
	$view->addMapping(gettext("city"), "%city");
	$view->addMapping(gettext("po box"), "%pobox");
	$view->addMapping(gettext("zip code po box"), "%pobox_zipcode");
	$view->addMapping(gettext("city po box"), "%pobox_city");
	$view->addMapping(gettext("telephone nr"), "%%phone_nr", array(
		"allow_html" => 1
	));
	$view->addMapping(gettext("mobile phone nr"), "%%mobile_nr", array(
		"allow_html" => 1
	));
	$view->addMapping(gettext("fax nr"), "%fax_nr");
	$view->addMapping(gettext("email"), "%%email");
	$view->addMapping(gettext("website"), "%%website");
	$view->addMapping(gettext("account manager"), "%account_manager_name");
	$view->addMapping(gettext("classification(s)"), "%classification_names");
	$view->addMapping(gettext("search for communication items"), "%companyname");
	$view->defineComplexMapping("phone_nr", array(
		array(
			"type" => "text",
			"text" => "%phone_nr_link"
		)
	));
	$view->defineComplexMapping("mobile_nr", array(
		array(
			"type" => "text",
			"text" => "%mobile_nr_link"
		)
	));
	$view->defineComplexMapping("email", array(
		array(
			"type"    => "link",
			"text"    => "%email",
			"link"    => array("javascript: emailSelectFrom('", "%email", "','", "$address_id", "');")
		)
	));
	$view->defineComplexMapping("website", array(
		array(
			"type"   => "link",
			"text"   => "%website",
			"link"   => "%website",
			"target" => "_blank"
		)
	));
	$venster->addCode($view->generate_output_vertical());
	unset($view);
$venster->endVensterData();


$buf1->addCode($venster->generate_output());
unset($venster);

if (($visitor_perms["xs_hrmmanage"] || $user_id == $_SESSION["user_id"]) && !$GLOBALS["covide"]->license["disable_basics"]) {
	/* hrm info */
	$hrminfo = $address_data->getHRMinfo($user_id);
	$venster_settings = array(
		"title"    => gettext("HRM"),
		"subtitle" => gettext("information")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($hrminfo);
		$view->addMapping(gettext("files"), "%%complex_files");
		$view->addMapping(gettext("sex"), "%human_gender");
		$view->addMapping(gettext("birthday"), "%human_bday");
		$view->addMapping(gettext("social security number"), "%social_security_nr");
		$view->addMapping(gettext("Start of the contract"), "%human_start");
		$view->addMapping(gettext("Expiration of the contract"), "%human_end");
		$view->addMapping(gettext("contract"), "%contract_type");
		$view->addMapping(gettext("work hours"), "%contract_hours");
		$view->addMapping(gettext("Holiday right"), "%contract_holidayhours");
		$view->addMapping(gettext("hourly gross wage"), "%gross_wage");
		$view->addMapping(gettext("kilometer allowance"), "%kilometer_allowance");
		$view->addMapping(gettext("overhead")." %", "%overhead");
		$view->addMapping(gettext("evaluation"), "%evaluation");
		$view->defineComplexMapping("complex_files", array(
			array(
				"type" => "link",
				"text" => gettext("open folder"),
				"link" => array("index.php?mod=filesys&action=opendir&id=", $filesysinfo)
			)
		));
		$venster->addCode($view->generate_output_vertical());
		$venster->insertAction("edit", gettext("change"), "javascript: popup('index.php?mod=address&action=edithrm&user_id=$user_id', 'hrmedit', 550, 600, 1);");
	$venster->endVensterData();
	$buf1->addCode($venster->generate_output());
	unset($venster);
}
if (($GLOBALS["covide"]->license["has_project"] && ($visitor_perms["xs_projectmanage"] || $visitor_perms["xs_usermanage"] || $user_id == $_SESSION["user_id"])) && !$GLOBALS["covide"]->license["disable_basics"]) {
	if ($GLOBALS["covide"]->license["has_project_declaration"]) {
		$days = array();
		$months = array();
		$years = array();
		for ($i = 1; $i <= 31; $i++) {
			$days[$i] = $i;
		}
		for ($i = 1; $i <= 12; $i++) {
			$months[$i] = $i;
		}
		for ($i = date("Y")-5; $i <= date("Y")+1; $i++) {
			$years[$i] = $i;
		}
		$venster_settings = array(
			"title" => gettext("registered items per day")
		);
		$venster = new Layout_venster($venster_settings);
		unset($venster_settings);
		$venster->addVensterData();
			$venster->addTag("form", array("method" => "get", "action" => "index.php", "id" => "regitemsfrm"));
			$venster->addHiddenField("mod", "address");
			$venster->addHiddenField("action", "usercard");
			$venster->addHiddenField("id", $address_id);
			$venster->addSelectField("regitems[day]", $days, $regitem_day);
			$venster->addSelectField("regitems[month]", $months, $regitem_month);
			$venster->addSelectField("regitems[year]", $years, $regitem_year);
			$venster->insertAction("forward", gettext("submit"), "javascript:document.getElementById('regitemsfrm').submit();");
			$venster->endTag("form");

			$view_hourlist = new Layout_view();
			$view_hourlist->addData($regitems);
			$view_hourlist->addMapping(gettext("date"), "%human_date");
			$view_hourlist->addMapping(gettext("name"), "%%complex_name");
			$view_hourlist->addMapping(gettext("declaration type"), "%declaration_type");
			$view_hourlist->addMapping(gettext("description"), "%description");
			$view_hourlist->addMapping(gettext("kilometers"), "%kilometers");
			$view_hourlist->addMapping(gettext("minutes"), "%time_units");
			$view_hourlist->addMapping(gettext("total price"), "%total_price");
			$view_hourlist->defineComplexMapping("complex_name", array(
				array(
					"type" => "link",
					"link" => array("index.php?mod=project&action=showinfo&master=0&id=", "%project_id"),
					"text" => "%project_name"
				)
			));
			$venster->addCode($view_hourlist->generate_output());
		$venster->endVensterData();
		$buf1->addCode($venster->generate_output());
		unset($venster);
	} else {
		$venster_settings = array(
			"title" => gettext("hours"),
		);
		$venster = new Layout_venster($venster_settings);
		$venster->addVensterData();
			$venster->insertLink(gettext("hours per project"), array("href" => "javascript: popup('index.php?mod=project&action=hoursuserperproject&user_id=$user_id', 'perproject', 550, 600, 1);"));
			$venster->addSpace(3);
			$venster->insertLink(gettext("hours per day"), array("href" => "javascript: popup('index.php?mod=project&action=hoursuserperday&user_id=$user_id', 'perday', 550, 600, 1);"));
		$venster->endVensterData();
		$buf1->addCode($venster->generate_output());

		/* kilometers */
		$venster_settings = array(
			"title"    => gettext("kilometers"),
			"subtitle" => gettext("information")
		);
		$venster = new Layout_venster($venster_settings);
		unset($venster_settings);
		$venster->addVensterData();
			$table = new Layout_table(array("width" => "100%"));
			$table->addTableRow();
				$table->insertTableData(gettext("previous month"), array("colspan" => 2), "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData("", "data");
					$view = new Layout_view();
					$view->addData(array($kminfo["prev"]));
					$view->addMapping(gettext("billable"), "%total_dec");
					$view->addMapping(gettext("not")." ".gettext("billable"), "%total_non_dec");
					$table->addCode($view->generate_output());
					unset($view);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("this month"), array("colspan" => 2), "header");
			$table->endTableRow();
			$table->addTableRow();
				$table->addTableData("", "data");
					$view = new Layout_view();
					$view->addData(array($kminfo["now"]));
					$view->addMapping(gettext("billable"), "%total_dec");
					$view->addMapping(gettext("not")." ".gettext("billable"), "%total_non_dec");
					$table->addCode($view->generate_output());
					unset($view);
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
			unset($table);

			$venster->addTag("br");
			$venster->insertLink(gettext("search"), array("href" => "index.php?mod=calendar&action=km"));
		$venster->endVensterData();
		$buf1->addCode($venster->generate_output());
		unset($venster);
	}
	/* projects where user is executor */
	$frame_settings = array(
		"title"    => gettext("projects"),
		"subtitle" => gettext("executor")
	);
	$frame = new Layout_venster($frame_settings);
	unset($frame_settings);
	$frame->addVensterData();
		$view = new Layout_view();
		$view->addData($proj_executor);
		$view->addMapping(gettext("name"), "%%complex_name");
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping(gettext("active"), "%%complex_active");
		$view->defineComplexMapping("complex_name", array(
			array(
				"type" => "link",
				"link" => array("index.php?mod=project&action=showinfo&master=0&id=", "%id"),
				"text" => "%name"
			)
		));
		$view->defineComplexMapping("complex_active", array(
			array(
				"type"  => "action",
				"src"   => "enabled",
				"check" => "%is_active"
			),
			array(
				"type"  => "action",
				"src"   => "disabled",
				"check" => "%is_nonactive"
			)
		));
		$frame->addCode($view->generate_output());
		unset($view);
	$frame->endVensterData();
	$buf1->addCode($frame->generate_output());
	unset($frame);
	/* projects where user is manager */
	$frame_settings = array(
		"title"    => gettext("projects"),
		"subtitle" => gettext("manager")
	);
	$frame = new Layout_venster($frame_settings);
	unset($frame_settings);
	$frame->addVensterData();
		$view = new Layout_view();
		$view->addData($proj_manager);
		$view->addMapping(gettext("name"), "%%complex_name");
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping(gettext("active"), "%%complex_active");
		$view->defineComplexMapping("complex_name", array(
			array(
				"type" => "link",
				"link" => array("index.php?mod=project&action=showinfo&master=0&id=", "%id"),
				"text" => "%name"
			)
		));
		$view->defineComplexMapping("complex_active", array(
			array(
				"type"  => "action",
				"src"   => "enabled",
				"check" => "%is_active"
			),
			array(
				"type"  => "action",
				"src"   => "disabled",
				"check" => "%is_nonactive"
			)
		));
		$frame->addCode($view->generate_output());
		unset($view);
	$frame->endVensterData();
	$buf1->addCode($frame->generate_output());
	unset($frame);
	/* projects where user is in users field */
	$frame_settings = array(
		"title"    => gettext("projects"),
		"subtitle" => gettext("users")
	);
	$frame = new Layout_venster($frame_settings);
	unset($frame_settings);
	$frame->addVensterData();
		$view = new Layout_view();
		$view->addData($proj_other);
		$view->addMapping(gettext("name"), "%%complex_name");
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping(gettext("active"), "%%complex_active");
		$view->defineComplexMapping("complex_name", array(
			array(
				"type" => "link",
				"link" => array("index.php?mod=project&action=showinfo&master=0&id=", "%id"),
				"text" => "%name"
			)
		));
		$view->defineComplexMapping("complex_active", array(
			array(
				"type"  => "action",
				"src"   => "enabled",
				"check" => "%is_active"
			),
			array(
				"type"  => "action",
				"src"   => "disabled",
				"check" => "%is_nonactive"
			)
		));
		$frame->addCode($view->generate_output());
		unset($view);
	$frame->endVensterData();
	$buf1->addCode($frame->generate_output());
	unset($frame);
}

/* support info */
if (($GLOBALS["covide"]->license["has_issues"] && ($visitor_perms["xs_issuemanage"] || $visitor_perms["xs_usermanage"] || $user_id == $_SESSION["user_id"])) && !$GLOBALS["covide"]->license["disable_basics"]) {
	$venster_settings = array(
		"title"    => gettext("issues/support"),
		"subtitle" => $subtitle_support
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($supportinfo["items"]);
		$view->addMapping("&nbsp;", "%%complex_actions");
		$view->addMapping(gettext("date"), "%human_date");
		$view->addMapping(gettext("support request"), "%short_desc");
		$view->addMapping(gettext("dispatching"), "%short_sol");
		$view->addMapping(gettext("issuer"), "%sender_name");
		$view->addMapping(gettext("executor"), "%rcpt_name");
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "info",
				"link" => array("javascript: popup('?mod=support&action=showitem&id=", "%id", "', 'support', 640, 500, 1);")
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
		if ($_REQUEST["history"] == "support") {
			$venster->insertLink(gettext("current"), array(
				"href" => "index.php?mod=address&action=usercard&id=$address_id&history=nothing"
			));
			$venster->addSpace(3);
		} else {
			$venster->insertLink(gettext("history"), array(
				"href" => "index.php?mod=address&action=usercard&id=$address_id&history=support"
			));
			$venster->addSpace(3);
		}
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	$buf2counter++;
}

/* note info */
if ($visitor_perms["xs_notemanage"] || $visitor_perms["xs_usermanage"] || $user_id == $_SESSION["user_id"]) {
	$venster_settings = array(
		"title"    => gettext("notes"),
		"subtitle" => gettext("current")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($noteinfo);
		$view->addMapping(gettext("date"), "%human_date");
		$view->addMapping(gettext("from"), "%from_name");
		$view->addMapping(gettext("to"), "%to_name");
		$view->addMapping(gettext("subject"), "%%subject");
		$view->defineComplexMapping("subject", array(
			array(
				"type" => "link",
				"link" => array("javascript: popup('index.php?mod=note&action=message&hidenav=1&msg_id=", "%id", "', 'shownote', 0, 0, 1);"),
				"text" => "%subject"
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
		$venster->addTag("br");
		$venster->insertLink(gettext("search")."/".gettext("history"), array("href"=>"index.php?mod=index&search[private]=0&search[notes]=1&search[note_user_id]=".$userinfo["id"]));
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	$buf2counter++;

	/* custcont info */
	$venster_settings = array(
		"title"    => gettext("customer contact items")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($custcontinfo);
		$view->addMapping(gettext("date"), "%human_date");
		$view->addMapping(gettext("from"), "%from_name");
		$view->addMapping(gettext("to"), "%to_name");
		$view->addMapping(gettext("subject"), "%%subject");
		$view->defineComplexMapping("subject", array(
			array(
				"type" => "link",
				"link" => array("javascript: popup('index.php?mod=note&action=message&hidenav=1&msg_id=", "%id", "', 'shownote', 0, 0, 1);"),
				"text" => "%subject"
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
		$venster->addTag("br");
		$venster->insertLink(gettext("search")."/".gettext("history"), array("href"=>"index.php?mod=index&search[private]=0&search[notes]=1&search[phrase]=".$userinfo["username"]));
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	$buf2counter++;
}

/* sales info */
if (($GLOBALS["covide"]->license["has_sales"] && ($user_id == $_SESSION["user_id"] || $visitor_perms["xs_salesmanage"] || $visitor_perms["xs_usermanage"])) && !$GLOBALS["covide"]->license["disable_basics"]) {
	$venster_settings = array(
		"title"    => gettext("sales"),
		"subtitle" => $sales_subtitle
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($sales_info["data"]);
		$view->addMapping(gettext("subject"), "%subject");
		$view->addMapping(gettext("contact"), "%%complex_address");
		$view->addMapping(gettext("score"), "%%complex_score");
		$view->addMapping(gettext("price"), "%total_sum");
		$view->defineComplexMapping("complex_address", array(
			array(
				"type" => "link",
				"link" => array("index.php?mod=address&action=relcard&id=", "%address_id"),
				"text" => "%h_address"
			)
		));
		$view->defineComplexMapping("complex_score", array(
			array(
				"type" => "text",
				"text" => array("%expected_score", "&#37;") /* &#37; is a % */
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
		$venster->addTag("br");
		if ($_REQUEST["history"] == "sales") {
			$venster->insertLink(gettext("current"), array(
				"href" => "index.php?mod=address&action=usercard&id=$id&history=nothing"
			));
			$venster->addSpace(3);
		} else {
			$venster->insertLink(gettext("history"), array(
				"href" => "index.php?mod=address&action=usercard&id=$id&history=sales"
			));
			$venster->addSpace(3);
		}
		$venster->insertLink(gettext("sales module"), array("href" => "index.php?mod=sales"));
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	$buf2counter++;
}

/* todo info */
if (($user_id == $_SESSION["user_id"] || $visitor_perms["xs_todomanage"] || $visitor_perms["xs_usermanage"]) && !$GLOBALS["covide"]->license["disable_basics"]) {
	$venster_settings = array(
		"title"    => gettext("to do's"),
		"subtitle" => $subtitle_todo
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($todoinfo);
		$view->addMapping(gettext("date"), "%desktop_time");
		$view->addMapping(gettext("subject"), "%%subject");
		$view->defineComplexMapping("subject", array(
			array(
				"type" => "text",
				"text" => " [A] ",
				"check" => "%is_active"
			),
			array(
				"type" => "text",
				"text" => " [P] ",
				"check" => "%is_passive"
			),
			array(
				"type" => "text",
				"text" => array(" (", "%priority", ") ")
			),
			array(
				"type" => "link",
				"link" => array("javascript: loadXML('index.php?mod=todo&action=show_info&id=", "%id", "');"),
				"text" => "%subject"
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
		if ($_REQUEST["history"] == "todos") {
			$venster->insertLink(gettext("current"), array(
				"href" => "index.php?mod=address&action=usercard&id=$id&history=nothing"
			));
			$venster->addSpace(3);
		} else {
			$venster->insertLink(gettext("history"), array(
				"href" => "index.php?mod=address&action=usercard&id=$id&history=todos"
			));
			$venster->addSpace(3);
		}
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	$buf2counter++;
}
/* calendar info */
//FIXME: add calendar delegation access
if (($user_id == $_SESSION["user_id"] || $visitor_perms["xs_usermanage"]) && !$GLOBALS["covide"]->license["disable_basics"]) {
	$venster_settings = array(
		"title" => gettext("calendar")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
		$venster->addVensterData();
			$view = new Layout_view();
			$view->addData($calendarinfo);
			$view->addMapping(gettext("from"), "%%human_start");
			$view->addMapping(gettext("till"), "%%human_end");
			$view->addMapping(gettext("subject"), "%%subject");
			$view->addMapping(gettext("user"), "%user_name");
			$view->defineComplexMapping("human_start", array(
				array(
					"type" => "link",
					"link" => array("index.php?mod=calendar&extra_user=", "%user_id", "&timestamp=", "%timestamp_start"),
					"text" => "%human_start"
				)
			));
			$view->defineComplexMapping("human_end", array(
				array(
					"type" => "link",
					"link" => array("index.php?mod=calendar&extra_user=", "%user_id", "&timestamp=", "%timestamp_end"),
					"text" => "%human_end"
				)
			));
			$view->defineComplexMapping("subject", array(
				array(
					"type" => "link",
					"link" => array("javascript: showcalitem(", "%id", ", ".$user_id.");"),
					"text" => "%subject"
				)
			));
			$venster->addCode($view->generate_output());
			unset($view);
		$venster->addTag("br");
		$venster->insertLink(gettext("search")."/".gettext("history"), array("href"=>"index.php?mod=index&search[private]=0&search[calendar]=1&search[phrase]=".$userinfo["username"]));
		$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	$buf2counter++;
}

$tbl = new Layout_table( array("width"=>"100%") );
$tbl->addTableRow();
	$tbl->insertTableData($buf1->generate_output(), array("width"=>"50%", "style"=>"padding-right: 5px; vertical-align: top;") );
	if ($buf2counter)
		$tbl->insertTableData($buf2->generate_output(), array("width"=>"50%", "style"=>"padding-left: 5px; vertical-align: top;") );
$tbl->endTableRow();
$tbl->endTable();

$output->addCode($tbl->generate_output());

$email = new Email_output();
$output->addCode( $email->emailSelectFromPrepare() );
$output->load_javascript(self::include_dir."address_actions.js");
$output->load_javascript(self::include_dir."relcard_actions.js");
$output->layout_page_end();
echo $output->exit_buffer();
?>
