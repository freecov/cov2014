<?php
/**
 * Covide Groupware-CRM Campaign module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Campaign_output {

	/* constants */
	const include_dir = "classes/campaign/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name = "campaign";

	/* methods */

	/* show_list {{{*/
    /**
     * 	generate list on welcome screen
     */
	public function show_list() {
		$active = !$_REQUEST["history"];
		$output = new Layout_output();
		$output->layout_page(gettext("campaigns"));

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", $_REQUEST["mod"]);
		$output->addHiddenField("start", (int)$_REQUEST["start"]);
		$output->addHiddenField("address_id", $_REQUEST["address_id"]);
		$output->addHiddenField("sort", $_REQUEST["sort"]);

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("campaigns"),
			"subtitle" => gettext("overview")
		));

		/* menu items */
		$venster->addMenuItem(gettext("new campaign"), "javascript: popup('?mod=campaign&action=new', 'campaignnew', 640, 480, 1);");
		$venster->addMenuItem(gettext("current campaigns"), "index.php?mod=campaign");
		$venster->addMenuItem(gettext("history"), "index.php?mod=campaign&history=1");
		$venster->generateMenuItems();
		$venster->addVensterData();

		$campaign_data = new Campaign_data();
		$data = $campaign_data->getCampaignsBySearch(array(
			"search"    => $_REQUEST["search"],
			"start"     => $_REQUEST["start"],
			"is_active" => $active
		));

		$venster->addCode(gettext("search").": ");
		$venster->addTextField("search", $_REQUEST["search"]);
		$venster->insertAction("toggle", gettext("show all"), "javascript: document.getElementById('search').value = ''; document.getElementById('velden').submit();");
		$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('start').value = ''; document.getElementById('velden').submit();");

		$view = new Layout_view();
		$view->addData($data["data"]);

		/* add the mappings (columns) we needed */
		$view->addMapping(gettext("date"), "%datetime_h");
		$view->addMapping(gettext("name"), "%name");
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping(gettext("classifications"), "%%complex_cla");
		$view->addMapping(gettext("type"), "%type_h");
		$view->addMapping(gettext("count"), "%count");
		$view->addMapping("", "%%complex_actions");

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "data_business_telephone",
				"alt"     => gettext("callscript"),
				"link"    => array("javascript: popup('index.php?mod=campaign&action=callscript&id=", "%id", "', 'campaignedit', 0, 600, 1);"),
				"check"   => "%is_callscript"
			),
			array(
				"type"    => "action",
				"src"     => "data_private_telephone",
				"alt"     => gettext("recallscript"),
				"link"    => array("javascript: popup('index.php?mod=campaign&action=recallscript&id=", "%id", "', 'campaignedit', 600, 600, 1);"),
				"check"   => "%is_recallable"
			),
			array(
				"type"    => "action",
				"src"     => "view",
				"alt"     => gettext("open"),
				"link"    => array("javascript: popup('index.php?mod=campaign&action=open&id=", "%id", "', 'campaignopen', 0, 0, 1);")
			),
			array(
				"type"    => "action",
				"src"     => "mail_new",
				"alt"     => gettext("mail"),
				"link"    => array("javascript: popup('index.php?mod=email&action=compose&id=", "%tracker_id", "', 'emailcompose', 980, 700, 1);"),
				"check"   => "%show_mail_icon"
			),
			array(
				"type"    => "action",
				"src"     => "search",
				"alt"     => gettext("open"),
				"link"    => array("javascript: popup('?mod=email&action=tracking&id=", "%tracker_id", "', 'campagin_mail_tracking', 860, 570, 1);"),
				"check"   => "%tracker_id"
			),
			array(
				"type"    => "action",
				"src"     => "choose",
				"alt"     => gettext("refresh classifications"),
				"link"    => array("javascript: popup('index.php?mod=campaign&action=refreshcla&id=", "%id", "', 'campaignrefresh', 600, 600, 1);"),
				"check"   => "%is_callscript"
			),
			array(
				"type"    => "action",
				"src"     => "edit",
				"alt"     => gettext("edit"),
				"link"    => array("javascript: popup('index.php?mod=campaign&action=editcampaign&id=", "%id", "', 'campaignedit', 640, 480, 1);")
			),
			array(
				"type"    => "action",
				"src"     => "delete",
				"alt"     => gettext("delete"),
				"link"    => array("javascript: removeCampaign(", "%id", ");"),
				"check"   => "%can_delete"
			),
			array(
				"type"    => "action",
				"src"     => "mail_tracking",
				"alt"     => gettext("open"),
				"link"    => array("javascript: popup('index.php?mod=campaign&action=show_script&id=", "%id", "', 'campaignshow', 600, 600, 1);"),
				"check"   => "%is_callscript"
			)
		));
		$view->defineComplexMapping("complex_cla", array(
			array(
				"type"    => "action",
				"src"     => "state_special",
				"alt"     => gettext("classification"),
				"check"   => "%classification_names"
			),
			array(
				"type"    => "text",
				"text"    => "%classification_names",
				"check"   => "%classification_names"
			),
			array(
				"type"    => "action",
				"src"     => "state_private",
				"alt"     => gettext("classification"),
				"check"   => "%classification_names_negative"
			),
			array(
				"type"    => "text",
				"text"    => "%classification_names_negative",
				"check"   => "%classification_names_negative"
			)
		));
		$venster->addCode( $view->generate_output() );

		$paging = new Layout_paging();
		$paging->setOptions((int)$_REQUEST["start"], $data["count"], "javascript: blader('%%');");
		$venster->addCode( $paging->generate_output() );

		$venster->endVensterData();

		$output->addCode( $venster->generate_output() );
		$output->load_javascript(self::include_dir."campaign.js");
		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* show_campaign {{{*/
    /**
     * 	generate contents of a campaign on the screen
     */
	public function show_campaign($id) {

		$output = new Layout_output();
		$output->layout_page(gettext("campaigns"));

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", $_REQUEST["mod"]);
		$output->addHiddenField("start", (int)$_REQUEST["start"]);
		$output->addHiddenField("address_id", $_REQUEST["address_id"]);
		$output->addHiddenField("sort", $_REQUEST["sort"]);

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("campaign"),
			"subtitle" => gettext("details")
		));

		/* menu items */
		$venster->addVensterData();

		$campaign_data = new Campaign_data();
		$data = $campaign_data->getCampaignsBySearch(array(
			"search" => $_REQUEST["search"],
			"start"  => $_REQUEST["start"]
		));

		$view = new Layout_view();
		$view->addData($data["data"]);

		/* add the mappings (columns) we needed */
		$view->addMapping(gettext("date"), "%datetime_h");
		$view->addMapping(gettext("name"), "%name");
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping(gettext("classifications"), "%classifications");
		$view->addMapping(gettext("type"), "%type_h");
		$view->addMapping(gettext("count"), "%count");
		$view->addMapping("", "%%complex_actions");

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "view",
				"alt"     => gettext("open"),
				"link"    => array("javascript: popup('index.php?mod=campaign&action=open&id=", "%id", "', 'campaignedit', 0, 0, 1);")
			),
			array(
				"type"    => "action",
				"src"     => "delete",
				"alt"     => gettext("delete"),
				"link"    => array("?mod=campaign&action=delete&id=", "%id")
			)
		));
		$venster->addCode( $view->generate_output() );

		$paging = new Layout_paging();
		$paging->setOptions((int)$_REQUEST["start"], $data["count"], "javascript: blader('%%');");
		$venster->addCode( $paging->generate_output() );

		$venster->endVensterData();

		$output->addCode( $venster->generate_output() );

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* show_new {{{*/
    /**
     * 	create a new campaign
     */
	public function show_new() {

		$output = new Layout_output();
		$output->layout_page(gettext("campaigns"), 1);

		$venster = new Layout_venster(array(
			"title"    => gettext("campaigns"),
			"subtitle" => gettext("new")
		));
		$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData();

				/* voip */
				$tbl->insertAction("data_business_telephone", "", "");
				$tbl->addSpace();
				$tbl->insertTag("a", gettext("new calling list"), array(
					"href" => "javascript: selectCampaign('2');"
				));
				$tbl->addTag("br");

				/* e-mailing */
				$tbl->insertAction("mail_new", "", "");
				$tbl->addSpace();
				$tbl->insertTag("a", gettext("new template/mailing"), array(
					"href" => "javascript: selectCampaign('1');"
				));
				$tbl->addTag("br");

				/* word/export mailing*/
				$tbl->insertAction("file_export", "", "");
				$tbl->addSpace();
				$tbl->insertTag("a", gettext("new export"), array(
					"href" => "javascript: selectCampaign('4');"
				));
				$tbl->addTag("br");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode($tbl->generate_output());
		$venster->endVensterData();

		$tbl = new Layout_table();

		$output->addTag("form", array(
			"action" => "index.php",
			"method" => "get",
			"id"     => "velden"
		));
		$output->addHiddenField("mod", "campaign");
		$output->addHiddenField("action", "");
		$output->addHiddenField("campaign", 1);
		$output->addHiddenField("type", "");
		
		$cla_data = new Classification_data();
		$cla_info = $cla_data->getSpecialClassification("do not contact");
		$cla_id = $cla_info[0]["id"];
		$output->addHiddenField("hidden_negative", $cla_id);
		
		$output->addCode( $tbl->createEmptyTable($venster->generate_output()) );
		$output->endTag("form");
		$output->load_javascript(self::include_dir."campaign.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* show_new2 {{{*/
    /**
     * 	create a new campaign
     */
	public function show_new2() {

		$output = new Layout_output();
		$output->layout_page(gettext("campaigns"), 1);

		$venster = new Layout_venster(array(
			"title"    => gettext("campaigns"),
			"subtitle" => gettext("new")
		));
		$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("name"));
			$tbl->addTableData();
				$tbl->addTextField("camp[name]", "", array(
					"style" => "width: 300px;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("description"));
			$tbl->addTableData();
				$tbl->addTextArea("camp[description]", "", array(
					"style" => "width: 300px; height: 150px;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableHeader("type");
			$tbl->addTableData();
				switch ($_REQUEST["type"]) {
					case 2:
						$tbl->insertAction("data_business_telephone", "", "");
						$tbl->addSpace();
						$tbl->addCode(gettext("new calling list"));
						break;
					case 1:
						$tbl->insertAction("mail_new", "", "");
						$tbl->addSpace();
						$tbl->addCode(gettext("new template/mailing"));
						break;
					case 3:
						$tbl->insertAction("addressbook", "", "");
						$tbl->addSpace();
						$tbl->addCode(gettext("new letter template"));
						break;
					case 4:
						$tbl->insertAction("file_export", "", "");
						$tbl->addSpace();
						$tbl->addCode(gettext("new export"));
						break;
				}
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addSpace();
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addTag("br");
				$tbl->insertAction("back", gettext("back"), "?mod=campaign&action=new");
				$tbl->addSpace(10);
				$tbl->insertAction("forward", gettext("next"), "javascript: document.getElementById('velden').submit();");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode($tbl->generate_output());
		$venster->endVensterData();

		$tbl = new Layout_table();

		$output->addTag("form", array(
			"action" => "index.php",
			"method" => "post",
			"id"     => "velden"
		));
		switch ($_REQUEST["type"]) {
			case 1:
				$_mod = "newsletter";
				$_action = "";
				break;
			default:
				$_mod = "campaign";
				$_action = "save";
				break;
		}
		$output->addHiddenField("mod", $_mod);
		$output->addHiddenField("action", $_action);
		$output->addHiddenField("campaign", $_REQUEST["type"]);
		$output->addHiddenField("type", $_REQUEST["type"]);
		$output->addHiddenField("exportid", $_REQUEST["exportid"]);

		$output->addCode( $tbl->createEmptyTable($venster->generate_output()) );
		$output->endTag("form");

		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* edit_campaign {{{ */
	/**
	 * Edit a campaign
	 *
	 * @param int $id The campaignid to edit
	 */
	public function edit_campaign($id) {
		$campaign_data = new Campaign_data();
		$campaign_info = $campaign_data->getCampaignById($id);

		$output = new Layout_output();
		$output->layout_page(gettext("campaigns"), 1);

		$venster = new Layout_venster(array(
			"title"    => gettext("campaigns"),
			"subtitle" => gettext("edit")
		));
		$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("name"));
			$tbl->addTableData();
				$tbl->addTextField("camp[name]", $campaign_info["name"], array(
					"style" => "width: 300px;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("description"));
			$tbl->addTableData();
				$tbl->addTextArea("camp[description]", $campaign_info["description"], array(
					"style" => "width: 300px; height: 150px;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("active"));
			$tbl->addTableData();
				$tbl->insertCheckBox("camp[is_active]", 1, $campaign_info["is_active"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addSpace();
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addTag("br");
				$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
				$tbl->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode($tbl->generate_output());
		$venster->endVensterData();

		$tbl = new Layout_table();

		$output->addTag("form", array(
			"action" => "index.php",
			"method" => "post",
			"id"     => "velden"
		));
		$output->addHiddenField("mod", "campaign");
		$output->addHiddenField("action", "save");
		$output->addHiddenField("id", $id);

		$output->addCode( $tbl->createEmptyTable($venster->generate_output()) );
		$output->endTag("form");

		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	public function show_contents($id) {

		$output = new Layout_output();
		$output->layout_page(gettext("campaigns"), 1);

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", $_REQUEST["mod"]);
		$output->addHiddenField("id", $id);
		$output->addHiddenField("sort", $_REQUEST["sort"]);
		$output->addHiddenField("action", "open");

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("campaigns"),
			"subtitle" => gettext("overview")
		));

		/* menu items */
		$venster->addMenuItem(gettext("close"), "javascript: window.close();");
		$venster->generateMenuItems();
		$venster->addVensterData();

		$campaign_data = new Campaign_data();
		$info = $campaign_data->getCampaignById($id);
		$data = $campaign_data->getCampaignRecordsBySearch(array(
			"search" => $_REQUEST["search"],
			"start"  => $_REQUEST["start"],
			"id"     => $id
		));
		$venster->addCode(gettext("search").": ");
		$venster->addTextField("search", $_REQUEST["search"]);
		$venster->insertAction("toggle", gettext("show all"), "javascript: document.getElementById('search').value = ''; document.getElementById('velden').submit();");
		$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('velden').submit();");

		$view = new Layout_view();
		$view->addData($data);

		/* add the mappings (columns) we needed */
		$view->addSubMapping("%%complex_row", "%is_extra_row");
		$view->addMapping(gettext("name"), "%name");
		$view->addMapping(gettext("by"), "%user_name");
		/* show email if type is mailing */
		if ($info["type"] == 1)
			$view->addMapping(gettext("email"), "%email");

		/* options */
		foreach ($campaign_data->actions as $k=>$v) {
			$view->addMapping($v, sprintf("%%options_%d", $k), array(
				"allow_html" => 1
			));
		}
		$view->addMapping("", "%%complex_actions");
		
		/* define mappings */
		$view->defineComplexMapping("complex_row", array(
			array(
				"type" => "text",
				"text" => gettext("options"),
				"check" => "%is_extra_row"
			)
		));
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "edit",
				"alt"     => gettext("edit"),
				"link"    => array("javascript: popup('?mod=campaign&action=edit_record&id=", "%id", "', 'campaign_record', 600, 500, 1);"),
				"check"   => "%id"
			),
			array(
				"type"    => "action",
				"src"     => "go_calendar",
				"alt"     => gettext("open appointment"),
				"link"    => array("javascript: popup('?mod=calendar&action=edit&id=", "%appointment_id", "', 'calendar_edit', 800, 650, 1);"),
				"check"   => "%appointment_id"
			),
			array(
				"type"    => "action",
				"src"     => "go_note",
				"alt"     => gettext("open note"),
				"link"    => array("javascript: popup('?mod=note&action=message&hidenav=1&msg_id=", "%note_id", "', 'note_open', 800, 450, 1);"),
				"check"   => "%note_id"
			),
			array(
				"type"    => "action",
				"src"     => "go_email",
				"alt"     => gettext("open mail"),
				"link"    => array("javascript: popup('?mod=email&action=open&hide=1&id=", "%email_id", "', 'email_open', 1000, 800, 1);"),
				"check"   => "%email_id"
			)
		));
		$venster->addCode( $view->generate_output() );
		$venster->endVensterData();

		$output->addCode( $venster->generate_output() );

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function show_edit_record($id) {
		$campaign_data = new Campaign_data();
		$data = $campaign_data->getCampaignRecordById($id);
		$address_data = $data["address_data"];
		$campaign_info = $campaign_data->getCampaignById($data["campaign_id"]);

		$conversion = new Layout_conversion();
		
		$output = new Layout_output();
		$output->layout_page(gettext("campaigns"), 1);

		$note = new Note_data();
		$note_info = $note->getNotesByContact($data["address_id"], 1, 1);

		$email = new Email_output();
		$output->addCode( $email->emailSelectFromPrepare() );

		$calendar = new Calendar_output();
		/* create date arrays */
		$days = array();
		for ($i=1; $i<=31; $i++) {
			$days[$i] = $i;
		}
		$months = array();
		for ($i=1; $i<=12; $i++) {
			$months[$i] = $i;
		}
		$years = array();
		for ($i=date("Y"); $i<=date("Y")+5; $i++) {
			$years[$i] = $i;
		}
		$hours = array();
		for ($i=1; $i<24; $i++) {
			$hours[$i] = $i;
		}
		$minutes = array();
		for ($i=0; $i<60; $i = $i+5) {
			$minutes[$i] = sprintf("%02d", $i);
		}
	
		$venster = new Layout_venster(array(
			"title"    => gettext("campaigns"),
			"subtitle" => gettext("edit record")
		));
		$venster->addVensterData();
		
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("name"), array(
				"style" => "width: 90px;",
			));
			$tbl->addTableData();
				if ($data["address_id"]) {
					$tbl->insertTag("a", $data["name"], array(
						"href" => sprintf("javascript: popup('index.php?mod=address&action=relcard&id=%d&addresstype=relations&hide=1&campaign_id=%d', 'addressedit', 1000, 600, 1);", $data["address_id"], $id)
					));
					$tbl->insertAction("edit", gettext("edit"),
						sprintf("javascript: popup('index.php?mod=address&action=edit&id=%d&addresstype=relations&sub=&campaign_id=%d', 'addressedit', 700, 600, 1);", $data["address_id"], $id
					));
					$tbl->addTag("br");
				}
				$tbl->addCode($address_data["tav"]." (".($address_data["contact_givenname"]?$address_data["content_givenname"]:$address_data["givenname"]).")");
				$tbl->insertAction("edit", gettext("edit"),
					sprintf("javascript: popup('index.php?mod=address&action=edit_bcard&id=%d&addresstype=bcards&sub=&campaign_id=%d', 'addressedit', 700, 600, 1);", $address_data["id"], $id
				));
			$tbl->endTableData();
		$tbl->endTableRow();

		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addSpace();
			$tbl->endTableData();
		$tbl->endTableRow();
		
		if ($data["call_again"]) {
			$cur_timestamp = time();
			$tbl->addTableRow();
				$tbl->insertTableHeader(gettext("call at"));
				$tbl->addTableData();
					$tbl->addCode(date("d-m-Y H:i", $data["call_again"]));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addSpace();
				$tbl->endTableData();
			$tbl->endTableRow();
		}
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("phone"));
			$tbl->addTableData();
				$tbl->addCode($data["address_data"]["phone_nr_link"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("mobile"));
			$tbl->addTableData();
				$tbl->addCode($data["address_data"]["mobile_nr_link"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addSpace();
			$tbl->endTableData();
		$tbl->endTableRow();
		
		if ($data["address_data"]["letop"]) {
			$tbl->addTableRow();
				$tbl->insertTableHeader(gettext("attention"));
				$tbl->addTableData();
					$tbl->addCode($data["address_data"]["letop"]);
				$tbl->endTableData();
			$tbl->endTableRow();
		}
		if ($note_info[0]) {
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addSpace();
			$tbl->endTableData();
		$tbl->endTableRow();
		
			$tbl->addTableRow();
				$tbl->insertTableHeader(gettext("last customer contact"));
				$tbl->addTableData();
					$tbl->insertTag("i", '"'.substr($note_info[0]["subject"].'"', 0, 30));
					$tbl->addTag("br");
					$tbl->addCode($note_info[0]["human_date"]);
				$tbl->endTableData();
			$tbl->endTableRow();
		}
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addSpace();
			$tbl->endTableData();
		$tbl->endTableRow();

		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("note"));
			$tbl->addTableData();
				$tbl->insertAction("go_note", gettext("new note"),
					sprintf("javascript: popup('?mod=note&action=edit&id=0&address_id=%d&campaign_id=%d', 'note', 840, 600, 1)", $data["address_id"], $id
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("email"));
			$tbl->addTableData();

				$tbl->insertAction("go_email", gettext("new note"),
					sprintf("javascript: emailSelectFrom('%s','%d','%d');", $data["address_data"]["email"], $data["address_id"], $id
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext("calendar"));
			$tbl->addTableData();
				$tbl->insertAction("go_calendar", gettext("new calendar item"),
					sprintf("javascript: popup('?mod=calendar&action=edit&id=0&address_id=%d&campaign_id=%d', 'note', 840, 600, 1)", $data["address_id"], $id
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			/* start date */
			$tbl->insertTableHeader(gettext("recall"));
			$tbl->addTableData("", "data");
				$tbl->addSelectField("calltime[day]", $days, date("d"));
				$tbl->addSelectField("calltime[month]", $months, date("m"));
				$tbl->addSelectField("calltime[year]", $years, date("Y"));
				$tbl->addTag("br");
				$tbl->addSelectField("calltime[hour]", $hours, date("G"));
				$tbl->addSelectField("calltime[minute]", $minutes, date("i"));
				$tbl->addCode( $calendar->show_calendar("document.getElementById('calltimeday')", "document.getElementById('calltimemonth')", "document.getElementById('calltimeyear')" ));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();
		
		$tblk = new Layout_table();
		$tblk->addTableRow();
			$tblk->addTableData(array("style"=>"vertical-align: top;"));
				$tblk->addCode($tbl->generate_output());
				$tblk->addTag("br");
		
				$tblk->addTag("br");

				foreach ($campaign_data->actions as $k=>$v) {
					$tblk->addCheckBox(sprintf("options[%d]", $k), 1);
					$tblk->addSpace();
					$tblk->addCode($v);
					$tblk->addTag("br");
				}
				$tblk->addTag("br");
				$tblk->insertAction("close", gettext("close"), "javascript: window.close();");
				$tblk->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
			$tblk->endTableData();

		/* calling manuscript */
			$tblk->addTableData(array("style"=>"width: 70%; vertical-align: top;"));
				$tblk->addCode($conversion->sanitize($campaign_info["description"]));
			$tblk->endTableData(); 
		$tblk->endTableRow();
		$tblk->endTable();

		$venster->addCode($tblk->generate_output());

		$venster->endVensterData();

		$tbl = new Layout_table();
		$output->addTag("form", array(
			"action" => "index.php",
			"method" => "post",
			"id"     => "velden"
		));
		$output->addHiddenField("mod", "campaign");
		$output->addHiddenField("callscript", $_REQUEST["callscript"]);
		$output->addHiddenField("action", "save_edit_record");
		$output->addHiddenField("id", $_REQUEST["id"]);
		
		$output->addCode( $tbl->createEmptyTable($venster->generate_output()) );
		$output->endTag("form");
		$output->load_javascript(self::include_dir."campaign.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	public function show_script($id) {
		$campaign_data = new Campaign_data();
		$data = $campaign_data->getCampaignById($id);

		$output = new Layout_output();
		$output->layout_page(gettext("campaigns"), 1);

		$venster = new Layout_venster(array(
			"title"    => gettext("campaigns"),
			"subtitle" => gettext("show callscript")
		));
		$venster->addVensterData();
		
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->insertTableData(nl2br($data["description"]));
		$tbl->endTableRow();
		$tbl->endTable();
		$venster->addCode($tbl->generate_output());
		$venster->addTag("br");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
		$venster->insertAction("edit", gettext("edit"), "index.php?mod=campaign&action=editcampaign&id=".$_REQUEST["id"]);
		$venster->endVensterData();

		$output->addCode( $venster->generate_output() );
		$output->layout_page_end();
		$output->exit_buffer();
	}


}
?>
