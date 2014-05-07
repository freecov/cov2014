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

	/* show_list {{{*/
    /**
     * generate list on welcome screen
     */
	public function show_list() {
		$active = !$_REQUEST["history"];
		$output = new Layout_output();
		$output->layout_page(gettext("Campaign"));

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
			"title"    => gettext("Campaign"),
			"subtitle" => gettext("overview")
		));

		/* menu items */
		$venster->addMenuItem(gettext("new campaign"), "javascript: popup('?mod=campaign&action=new', 'campaignnew', 640, 480, 1);", "", 0);
		if ($_SESSION["locale"] == "nl_NL") {
			$venster->addMenuItem(gettext("help (wiki)"), "http://wiki.covide.nl/Campagnemanagement", array("target" => "_blank"), 0);
		}
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
		$view->addMapping(gettext("description"), "%description", array(
			"allow_html" => 1
		));
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
				"src"     => "file_attach",
				"alt"     => gettext("add classification"),
				"link"    => array("javascript: popup('index.php?mod=campaign&action=addClass&id=", "%id", "', 'campaignopen', 0, 0, 1);")
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
	public function addClass($id) {

		$output = new Layout_output();
		$output->layout_page(gettext("campaigns"), 1);

		$output->addTag("form", array(
			"id"     => "addClassification",
			"method" => "post",
			"action" => "index.php"
		));

		$output->addHiddenField("mod", $_REQUEST["mod"]);
		$output->addHiddenField("action", "addClassToDatabase");
		$output->addHiddenField("id", $id);

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("campaign"),
			"subtitle" => gettext("details")
		));
		$venster->addVensterData();
		$classification_output = new Classification_output();

		$q = sprintf("SELECT classifications FROM campaign WHERE id = %d", $id);
		$r = sql_query($q);
		$classifications = unserialize(sql_result($r, 0));
		$classesPos = $classifications["positive"];
		$classesNeg = $classifications["negative"];

		$tbl = new Layout_table(array("cellspacing" => 1, "width" =>"40%", "style" => "border: 1px solid #E2E2E2"));
		$tbl->addHiddenField("classifications[positive]", "");
		$tbl->addHiddenField("classifications[negative]", "");

		$tbl->addTableRow();
			$tbl->addTableData(array("class" => "list_header", "colspan" => "2"));
				$tbl->addCode(gettext("Add new classification"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("class" => "list_data"));
				$tbl->addCode(gettext("positive"));
			$tbl->endTableData();
			$tbl->addTableData(array("class" => "list_data"));
				$tbl->addCode(gettext("negative"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addCode($classification_output->classification_selection("", $classesPos, "enabled", 1));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode($classification_output->classification_selection("", $classesNeg, "disabled", 1));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("class" => "list_data", "colspan" => "2"));
				$tbl->addCode(gettext("add new classification to existing classifications"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("class" => "list_data"));
				$tbl->addCode(gettext("positive"));
			$tbl->endTableData();
			$tbl->addTableData(array("class" => "list_data"));
				$tbl->addCode(gettext("negative: only for new classifications"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addCode($classification_output->classification_selection("classificationspositive"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCode($classification_output->classification_selection("classificationsnegative", "", "disabled"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("class" => "list_data", "colspan" => "2"));
				$tbl->addCode(gettext("save classification"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan" => "2"));
				$tbl->insertLink(gettext("save add classification"), array("href" => "javascript:document.getElementById('addClassification').submit();"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode($tbl->generate_output());
		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();

	}
	/* show_campaign {{{*/
    /**
     * generate contents of a campaign on the screen
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
					"style" => "width: 650px; height: 350px;"
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
				$tbl->insertAction("close", gettext("close"), "javascript: closepopup();");
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

	public function show_specific_contents($id, $answer) {

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

		$venster->addVensterData();

		$user_data = new User_data();
		$campaign_data = new Campaign_data();
		$info = $campaign_data->getCampaignById($id);
		$data = $campaign_data->getCampaignRecordsBySearch(array(
			"search" => $_REQUEST["search"],
			"start"  => $_REQUEST["start"],
			"id"     => $id,
			"answer" => $answer
		));

		/* menu items */
		$venster->addMenuItem(gettext("export as XML"), "index.php?mod=campaign&action=open&id=".$id."&export=xml");
		$venster->addMenuItem(gettext("export as CSV"), "index.php?mod=campaign&action=open&id=".$id."&export=csv");
		$venster->addMenuItem(gettext("close"), "javascript: closepopup();");
		$venster->generateMenuItems();


		$view = new Layout_view();
		$view->addData($data);

		/* add the mappings (columns) we needed */
		$view->addSubMapping("%%complex_row", "%is_extra_row");
		$view->addMapping(gettext("name"), "%name");
		$view->addMapping(gettext("by"), "%user_name");

		/* show email if type is mailing */
		if ($info["type"] == 1) {
			$view->addMapping(gettext("email"), "%email");
		}
		/* options */
		foreach ($campaign_data->actions as $k=>$v) {
			$view->addMapping($v, sprintf("%%options_%d", $k), array("allow_html" => 1));
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
				"link"    => array("javascript: popup('?mod=calendar&action=edit&user=".$_SESSION["user_id"]."&id=", "%appointment_id", "', 'calendar_edit', 800, 650, 1);"),
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
		$venster->addMenuItem(gettext("export as XML"), "index.php?mod=campaign&action=open&id=".$id."&export=xml");
		$venster->addMenuItem(gettext("export as CSV"), "index.php?mod=campaign&action=open&id=".$id."&export=csv");
		$venster->addMenuItem(gettext("close"), "javascript: closepopup();");
		$venster->generateMenuItems();
		$venster->addVensterData();

		$user_data = new User_data();
		$campaign_data = new Campaign_data();
		$info = $campaign_data->getCampaignById($id);
		$data = $campaign_data->getCampaignRecordsBySearch(array(
			"search" => $_REQUEST["search"],
			"start"  => $_REQUEST["start"],
			"id"     => $id
		));

		/* count handled and unhandled*/
		$stats["total"] = 0;
		$stats["users"] = array();
		foreach ($data as $dat) {
			if (!$dat["is_extra_row"]) {
				$stats["total"]++;
			}
			if (isset($dat["user_id"]) && $dat["user_id"] > 0) {
				$stats["users"][$dat["user_id"]]++;
			} elseif (!$dat["is_extra_row"]) {
				$stats["unhandled"]++;
			}
		}
		$stats["unhandled"] = ($stats["unhandled"]) ? $stats["unhandled"] : 0;
		$stats["handled"] = $stats["total"] - $stats["unhandled"];

		$view = new Layout_view();

		/* start statistics table */
		$tbl = new Layout_table(array("cellspacing" => 1));
		$tbl->addTableRow();
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("unhandled"));
			$tbl->endTableData();
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("handled"));
			$tbl->endTableData();
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("total"));
			$tbl->endTableData();
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("by"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData('', "data");
				$tbl->insertLink($stats["unhandled"],
						array("href" => "javascript: popup('?mod=campaign&action=open_specific&id=".$id."&answer=unhandled', 'campaignopen', 0, 0, 1);"));
			$tbl->endTableData();
			$tbl->addTableData('', "data");
				$tbl->addCode($stats["handled"]);
			$tbl->endTableData();
			$tbl->addTableData('', "data");
				$tbl->addCode($stats["total"]);
				$tbl->insertLink(' (export to CSV)',array("href" => "index.php?mod=campaign&action=open&id=".$id."&export=csv"));
			$tbl->endTableData();
			$tbl->addTableData('', "data");
			foreach ($stats["users"] as $user_id => $amount) {
					$tbl->addCode($user_data->getUsernameById($user_id).' ');
				unset($user_id);
			}
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		//put general information in div
		$venster->addTag("div", array("class" => "hence_campaign"));
		$venster->insertTag("h1", $info["name"]);
		$venster->addCode($tbl->generate_output());
		$venster->endTag("div");

		/* do exporting stuff */
		if ($_REQUEST["export"]) {
			if ($_REQUEST["export"] == "csv") {
				/* CSV data export */
				$conversion = new Layout_conversion();
				$csv = array();
				$csv[]= gettext("name");
				$csv[]= gettext("by");
				foreach ($campaign_data->actions as $k=>$v) {
					$csv[]= $v;
				}
				$csvdata = $conversion->generateCSVRecord($csv);
				unset($csv);
				if (is_array($data)) {
					foreach ($data as $dat) {
						$csv = array();
						$csv[] = $dat["name"];
						$csv[] = $dat["user_name"];
						foreach ($campaign_data->actions as $k=>$v) {
							if (!empty($dat[sprintf("options_%d", $k)])) {
								$csv[]= 1;
							} else {
								$csv[]= 0;
							}
						}
						$csvdata .= $conversion->generateCSVRecord($csv);
						unset($csv);
					}
				}
				header("Content-Transfer-Encoding: binary");
				if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")) {
					header("Content-Type: text/plain; charset=UTF-8"); // IE content-type
				} else {
					header("Content-type: application/vnd.ms-excel"); // Firefox/Opera/Chrome/Safari
				}

				if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
					header("Content-Disposition: filename=campaigninfo.csv"); //msie 5.5 header bug
				}else{
					header("Content-Disposition: attachment; filename=campaigninfo.csv");
				}
				echo $csvdata;
				exit();
			} else if ($_REQUEST["export"] == "xml") {
				/* XML export */
				$conversion = new Layout_conversion;
				$string = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<export>\n";
				if (!is_array($data)) {
					$string .= "<item>".gettext("no items found")."</item>";
				} else {
					foreach ($data as $dat) {
						$username = ($dat["user_name"]) ? $dat["user_name"] : 0;
						$string .= "<item>\n";
						$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "name", str_replace("&", "&amp;", $conversion->str2utf8($dat["name"])));
						$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "by", str_replace("&", "&amp;", $conversion->str2utf8($username)));
						foreach ($campaign_data->actions as $k=>$v) {
							if (!empty($dat[sprintf("options_%d", $k)])) {
								$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "option", $v);
							}
						}
						$string .= "</item>\n";
					}
				}
				$string .= "</export>\n";
				header("Content-Transfer-Encoding: binary");
				header("Content-Type: text/xml; charset=UTF-8");

				if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
					header("Content-Disposition: filename=campaigninfo.xml"); //msie 5.5 header bug
				}else{
					header("Content-Disposition: attachment; filename=campaigninfo.xml");
				}
				echo $string;
				exit();
			}

		}

		/* count reactions */
		for($i=1; $i<=8; $i++) {
			$amountReaction[$i] = 0;
		}
		foreach($data as $dat) {
			for($i=1; $i<=8; $i++) {
				$answer = explode(',', $dat["answer"]);
				if(in_array($i,$answer)) {
					$amountReaction[$i]++;
				}
			}
		}

		$tbl = new Layout_table(array("cellspacing" => 1));
			$tbl->addTableRow();
				foreach ($campaign_data->actions as $k=>$v) {
					$tbl->addTableData(array("class" => "list_header", "width" => "150px"));
						$tbl->addCode($v);
					$tbl->endTableData();
				}
			$tbl->endTableRow();
			$tbl->addTableRow();
			$i = 1;
				foreach ($campaign_data->actions as $k=>$v) {
					$tbl->addTableData('', "data");
						$popup = "javascript: popup('?mod=campaign&action=open_specific&id=%d&answer=%d', 'campaignopen', 0, 0, 1);";
						$tbl->insertLink($amountReaction[$k],
						array("href" => sprintf($popup, $id, $k)));
						$i++;
				}
			$tbl->endTableRow();
		$tbl->endTable();

		/* put reaction in div */
		$venster->addTag("div", array("class" => "hence_campaign"));
		$venster->insertTag("h1", "reactions");
		$venster->addCode($tbl->generate_output());
		$venster->endTag("div");

		/* Google Chart API - Create pie chart image(more than 100%) */
		$amount = array();
		$names = array();
		$percent = array();
		foreach ($campaign_data->actions as $k=>$v) {
			if ($amountReaction[$k]) {
				$percent[$k] = round(($amountReaction[$k]/$stats["handled"])*100);
				$names[] = $v.' ('.$percent[$k].'%)';
			}
		}

		$pieChart = array(
			"chs" => "577x200",
			"cht" => "p",
			"chco" => "",
			"chtt" => "Reactie overzicht(%)",
			"chd" => "t:".implode(",", $percent),
			"chl" => implode("|", $names),
		);
		$gPieChartLink = "index.php?mod=google&action=chart";
		foreach ($pieChart as $k => $v) {
			$gPieChartLink .= "&param[".$k."]=".urlencode($v);
		}

		/* Google Chart API - Create bar chart image */
		$amount = array();
		$reaction = array();
		$reactionAbbr = array(1=>'not-in',2=>'not-cont',3=>'appoint',4=>'email',8=>'note',5=>'call',6=>'contact',7=>'classifi');
		foreach ($campaign_data->actions as $k=>$v) {
			$amount[] = $amountReaction[$k];
			$reaction[] = $v;
		}
		$string_amount = implode(',', $amount);
		$string_reactionAbbr = implode('|', $reactionAbbr);
		$string_reaction = implode('|', $reaction);
		$highest_amount = max($amount);
		$steps = 1;
		if ($highest_amount > 20) {
			$steps = 5;
		}
		if ($highest_amount > 100) {
			$steps = 20;
		}
		if ($highest_amount > 500) {
			$steps = 50;
		}

		$barChart = array(
			"chs"  => "380x200",
			"cht"  => "bvg",
			"chtt" => "Reactie overzicht",
			"chd"  => "t:".$string_amount,
			"chxt" => "x,y",
			"chds" => "0,".$highest_amount,
			"chxl" => "0:|".$string_reactionAbbr,
			"chxr" => "1,0,".$highest_amount.",".$steps,
			"chbh" => "15,30,30",
			"chco" => "ff9900|ffa319|ffae33|ffb84c|ffc266|ffcc80|ffd799|ffe1b3",
			"chm"  => "N,727272,0,-1,12",
		);
		$gchartlink = "index.php?mod=google&action=chart";
		foreach ($barChart as $k => $v) {
			$gchartlink .= "&param[".$k."]=".urlencode($v);
		}

		/* view charts
		* get charts-image from google charts server(google returns a chart png-file)
		*/
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData('', "");
				$tbl->addTag("img", array("id"=>"pieChart", "src"=>$gPieChartLink));
				$tbl->endTag("img");
			$tbl->endTableData();
			$tbl->addTableData('', "");
				$tbl->addTag("img", array("id" => "barChart", "src" => $gchartlink));
				$tbl->endTag("img");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addSpace();
				$tbl->addTag("br");
				$tbl->addSpace();
				$tbl->addTag("br");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		/* put charts in div */
		$venster->addTag("div", array("class" => "hence_campaign"));
		$venster->insertTag("h1", "charts");
		$venster->addCode($tbl->generate_output());
		$venster->endTag("div");
		$venster->addSpace();

		$venster->endVensterData();
		$output->addCode( $venster->generate_output());
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
		$output->addCode($email->emailSelectFromPrepare());

		$calendar = new Calendar_output();
		/* create date arrays */
		$options = array();
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
		$venster->addSpace();
		$tbl = new Layout_table(array("cellspacing" => 1, "width" =>"100%"));
		$tbl->addTableRow();
			/* 01 - name */
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("name"));
			$tbl->endTableData();
			/* 02 - name */
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("name"));
			$tbl->endTableData();
			/* 03 - call again */
			if ($data["call_again"]) {
			$cur_timestamp = time();
				$tbl->addTableData('', "header");
					$tbl->addCode(gettext("call at"));
				$tbl->endTableData();
			}
			/* 04 - phonenumber */
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("phone"));
			$tbl->endTableData();
			/* 05 - mobilenumber */
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("mobile"));
			$tbl->endTableData();
			/*  06 - attention */
			if ($data["address_data"]["letop"]) {
				$tbl->addTableData('', "header");
				$tbl->addCode(gettext("attention"));
				$tbl->endTableData();
			}
			/*  07- note information */
			if ($note_info[0]) {
				$tbl->addTableData('', "header");
					$tbl->addCode('note information');
				$tbl->endTableData();
			}
			/*  08 - description */
			$tbl->addTableData('', "header");
				$tbl->addCode('description');
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			/*  01 - add name company */
			$tbl->addTableData('','data');
				if ($data["address_id"]) {
					$tbl->insertTag("a", $data["name"], array(
						"href" => sprintf("javascript: popup('index.php?mod=address&action=relcard&id=%d&addresstype=relations&hide=1&campaign_id=%d', 'addressedit', 1000, 600, 1);", $data["address_id"], $id)
					));
					$tbl->insertAction("edit", gettext("edit"),
						sprintf("javascript: popup('index.php?mod=address&action=edit&id=%d&addresstype=relations&sub=&campaign_id=%d', 'addressedit', 700, 600, 1);", $data["address_id"], $id
					));
				}
			$tbl->endTableData();
			/* 02 - add name contact */
			$tbl->addTableData('', 'data');
				$tbl->addCode($address_data["tav"]." (".($address_data["contact_givenname"]?$address_data["content_givenname"]:$address_data["givenname"]).")");
				$tbl->insertAction("edit", gettext("edit"),
					sprintf("javascript: popup('index.php?mod=address&action=edit_bcard&id=%d&addresstype=bcards&sub=&campaign_id=%d', 'addressedit', 700, 600, 1);", $address_data["id"], $id
				));
			$tbl->endTableData();
			/* 03 - add call again */
			if ($data["call_again"]) {
			$cur_timestamp = time();
				$tbl->addTableData('', 'data');
					$tbl->addCode(date("d-m-Y H:i", $data["call_again"]));
				$tbl->endTableData();
			}
			/* 04 - add phone number */
			$tbl->addTableData('', 'data');
				$tbl->addCode($data["address_data"]["phone_nr_link"]);
			$tbl->endTableData();
			/* 05 - add mobile number */
			$tbl->addTableData('', 'data');
				$tbl->addCode($data["address_data"]["mobile_nr_link"]);
			$tbl->endTableData();
			/* 06- add attention */
			if ($data["address_data"]["letop"]) {
				$tbl->addTableData('', 'data');
					$tbl->addCode($data["address_data"]["letop"]);
				$tbl->endTableData();
			}
			/* add note contact */
			if ($note_info[0]) {
				$tbl->addTableData('', 'data');
					$tbl->insertTag("i", '"'.substr($note_info[0]["subject"].'"', 0, 30));
					$tbl->addTag("br");
					$tbl->addCode($note_info[0]["human_date"]);
				$tbl->endTableData();
			}
			/* 08 - add description */
			$tbl->addTableData('', 'data');
				$tbl->addCode($conversion->sanitize($campaign_info["description"]));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addTag("div", array("class" => "hence_campaign"));
		$venster->addCode($tbl->generate_output());
		$venster->endTag("div");

		$tbl = new Layout_table(array("width" => "100%", "cellspacing" => 1));
		$tbl->addTableRow();
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("note"));
			$tbl->endTableData();
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("email"));
			$tbl->endTableData();
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("calendar"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			/* add note */
			$tbl->addTableData('', 'data');
				$tbl->insertAction("go_note", gettext("new note"),
					sprintf("javascript: popup('?mod=note&action=edit&id=0&is_custcont=1&address_id=%d&campaign_id=%d', 'note', 840, 600, 1)", $data["address_id"], $id
				));
			$tbl->endTableData();
			/* add email */
			$tbl->addTableData('', 'data');

				$tbl->insertAction("go_email", gettext("new note"),
					sprintf("javascript: emailSelectFrom('%s','%d','%d');", $data["address_data"]["email"], $data["address_id"], $id
				));
			$tbl->endTableData();
			/* add calender */
			$tbl->addTableData('', 'data');
				$tbl->insertAction("go_calendar", gettext("new calendar item"),
					sprintf("javascript: popup('?mod=calendar&action=edit&id=0&address_id=%d&campaign_id=%d', 'note', 840, 600, 1)", $data["address_id"], $id
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();
		$venster->addTag("div", array("class" => "hence_campaign"));
		$venster->addCode($tbl->generate_output());
		$venster->endTag("div");

		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan" => '2'), "header");
				$tbl->addCode('datum');
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData('', 'data');
				$tbl->addCode('datum: ');
			$tbl->endTableData();
			$tbl->addTableData('', 'data');
			$tbl->addSelectField("calltime[day]", $days, date("d"));
				$tbl->addSelectField("calltime[month]", $months, date("m"));
				$tbl->addSelectField("calltime[year]", $years, date("Y"));
				$tbl->addCode( $calendar->show_calendar("document.getElementById('calltimeday')", "document.getElementById('calltimemonth')", "document.getElementById('calltimeyear')" ));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData('', 'data');
				$tbl->addCode('time: ');
			$tbl->endTableData();
			$tbl->addTableData('', 'data');
				$tbl->addSelectField("calltime[hour]", $hours, date("G"));
				$tbl->addSelectField("calltime[minute]", $minutes, date("i"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addTag("div", array("class" => "hence_campaign"));
		$venster->addCode($tbl->generate_output());
		$venster->endTag("div");

		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData();
				$keys = array();
				$answers = $data["answer"];
				$keys = explode(',', $answers);
				foreach ($campaign_data->actions as $k=>$v) {
					if (in_array($k,$keys)){
						$tbl->insertCheckbox("options[]", $k, 1);
						$tbl->addCode($v);
						$tbl->addTag("br");
					}else {
						$tbl->insertCheckbox("options[]", $k);
						$tbl->addCode($v);
						$tbl->addTag("br");
					}
				}
				$tbl->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addTag("div", array("class" => "hence_campaign"));
		$venster->addCode($tbl->generate_output());
		$venster->endTag("div");

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
		$venster->insertAction("close", gettext("close"), "javascript: closepopup();");
		$venster->insertAction("edit", gettext("edit"), "index.php?mod=campaign&action=editcampaign&id=".$_REQUEST["id"]);
		$venster->endVensterData();

		$output->addCode( $venster->generate_output() );
		$output->layout_page_end();
		$output->exit_buffer();
	}


}
?>
