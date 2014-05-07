<?php
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
$addressinfo[0] = $address_data->getAddressById($address_id, "user");
/* get the active notes for this user */
$notes_data     = new Note_data();
$notearr        = $notes_data->getNotes(array("user_id" => $user_id, "nocustcont" => 1));
$noteinfo       = $notearr["notes"];
/* get the active todos */
$todo_data = new Todo_data();
$todoinfo  = $todo_data->getTodosByUserId($user_id);
/* get the active customer contact items for this relation */
$custcontarr  = $notes_data->getNotes(array("user_id" => $user_id, "custcont" => 1));
$custcontinfo = $custcontarr["notes"];
/* get the appointments */
$calendar_data = new Calendar_data();
$calendarinfo  = $calendar_data->getAppointmentsByUser($user_id);
/* kilometers */
if ($GLOBALS["covide"]->license["has_project"]) {
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
	/* get projects */
	$projectdata = new Project_data();
	$projectoptions = array("user_id" => $user_id);
	$projectinfo = $projectdata->getProjectsBySearch($projectoptions);
	$proj_executor = array();
	$proj_manager  = array();
	if (is_array($projectinfo)) {
		foreach ($projectinfo as $k=>$v) {
			if ($v["executor"] == $user_id)
				$proj_executor[] = $v;
			else
				$proj_manager[] = $v;
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
	$projectinfo  = $project_data->getProjectsBySearch($id);
	$hoursinfo = $project_data->getOverviewData(mktime(0, 0, 0, date("m")-3, 1, date("Y")), mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
}
/* get the support data */
if ($GLOBALS["covide"]->license["has_issues"]) {
	$support_data = new Support_data();
	$supportinfo  = $support_data->getSupportItems(array("user_id" => $user_id, "active" => 1, "nolimit" => 1));
}
/* get the sales items */
if ($GLOBALS["covide"]->license["has_sales"]) {
	$sales_data = new Sales_data();
	$sales_info = $sales_data->getSalesBySearch(array("user_id" => $user_id));
}

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
	//$venster->addCode("<pre>".print_r($addressinfo, true)."</pre>");
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
	$view->addMapping(gettext("telephone nr"), "%%phone_nr");
	$view->addMapping(gettext("mobile phone nr"), "%%mobile_nr");
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

if ($visitor_perms["xs_hrmmanage"] || $user_id == $_SESSION["user_id"]) {
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
if ($GLOBALS["covide"]->license["has_project"] && ($visitor_perms["xs_projectmanage"] || $visitor_perms["xs_usermanage"] || $user_id == $_SESSION["user_id"])) {
	/* hours */
	$venster_settings = array(
		"title" => gettext("hours in the last 3 months")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData(array($hoursinfo["users"][$user_id]));
		$view->addMapping(gettext("billable hours"), "%total_fac");
		$view->addMapping(gettext("non-billable hours"), "%total_nofac");
		$view->addMapping(gettext("Holiday right"), "%total_hol");
		$view->addMapping(gettext("reported sick"), "%total_ill");
		$view->addMapping(gettext("special leave"), "%total_sl");
		$venster->addCode($view->generate_output());
		unset($view);
	$venster->endVensterData();
	$buf1->addCode($venster->generate_output());
	unset($venster);
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
}

/* support info */
if ($GLOBALS["covide"]->license["has_issues"] && ($visitor_perms["xs_issuemanage"] || $visitor_perms["xs_usermanage"] || $user_id == $_SESSION["user_id"])) {
	$venster_settings = array(
		"title"    => gettext("issues/support"),
		"subtitle" => gettext("current")
	);
	$venster = new Layout_venster($venster_settings);
	unset($venster_settings);
	$venster->addVensterData();
		$view = new Layout_view();
		$view->addData($supportinfo["items"]);
		$view->addMapping(gettext("date"), "%human_date");
		$view->addMapping(gettext("support request"), "%short_desc");
		$view->addMapping(gettext("dispatching"), "%short_sol");
		$view->addMapping(gettext("issuer"), "%sender_name");
		$view->addMapping(gettext("executor"), "%rcpt_name");
		$venster->addCode($view->generate_output());
		unset($view);
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
		$venster->insertLink(gettext("search")."/".gettext("history"), array("href"=>"index.php?mod=index&search[private]=0&search[notes]=1&search[phrase]=".$userinfo["username"]));
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
if ($GLOBALS["covide"]->license["has_sales"] && ($user_id == $_SESSION["user_id"] || $visitor_perms["xs_salesmanage"] || $visitor_perms["xs_usermanage"])) {
	$venster_settings = array(
		"title"    => gettext("sales"),
		"subtitle" => gettext("active items")
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
		$venster->insertLink(gettext("sales module"), array("href" => "index.php?mod=sales"));
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	$buf2counter++;
}

/* todo info */
if ($user_id == $_SESSION["user_id"] || $visitor_perms["xs_todomanage"] || $visitor_perms["xs_usermanage"]) {
	$venster_settings = array(
		"title"    => gettext("to do's"),
		"subtitle" => gettext("current")
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
				"type" => "link",
				"link" => array("javascript: loadXML('index.php?mod=todo&action=show_info&id=", "%id", "');"),
				"text" => "%subject"
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
	$venster->endVensterData();
	$buf2->addCode($venster->generate_output());
	unset($venster);
	$buf2counter++;
}
/* calendar info */
//FIXME: add calendar delegation access
if ($user_id == $_SESSION["user_id"] || $visitor_perms["xs_usermanage"]) {
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
					"link" => array("javascript: popup('index.php?mod=calendar&action=edit&id=", "%id", "', 'calendaredit', 0, 0, 1);"),
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

$output->layout_page_end();
echo $output->exit_buffer();
?>