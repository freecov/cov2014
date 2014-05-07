<?php
/**
 * Covide Groupware-CRM Sales output class
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Sales_output {

	/* constants */
	const include_dir =  "classes/sales/inc/";

	/* variables */

	/* methods */
	public function salesEdit($options = array()) {

		$output = new Layout_output();
		if ($options["noiface"] == 1) {
			$output->layout_page(gettext("sales"), 1);
		} else {
			$output->layout_page(gettext("sales"));
		}

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php"
		));

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("sales"),
			"subtitle" => gettext("edit")
		));
		$venster->addVensterData();

		if ($options["note_id"]) {
			$note_data = new Note_data();
			$note_info = $note_data->getNoteById($options["note_id"]);
			$data = array(
				"subject"            => $note_info["subject"],
				"description"        => $note_info["body"],
				"is_active"          => 1,
				"timestamp_prospect" => $note_info["timestamp"],
				"address_id"         => $note_info["address_id"]
			);
			$sales[0] = $data;
			$output->addHiddenField("sales[fromnotes]", "1");
		} else {
			$sales_data = new Sales_data();
			$sales_info = $sales_data->getSalesById($_REQUEST["id"], $_REQUEST["address_id"]);
			$sales =& $sales_info["data"];
		}
		#print_r($sales);

		$tbl = new layout_table();
		/* subject */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("title"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("sales[subject]", $sales[0]["subject"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* description */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("description"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextArea("sales[description]", $sales[0]["description"], array(
					"style" => "width: 300px; height: 100px;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* description */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("active"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCheckbox("sales[is_active]", 1, $sales[0]["is_active"]);
			$tbl->endTableData();
		$tbl->endTableRow();

		$timestamp_fields = array(
			"prospect" => gettext("prospect"),
			"proposal" => gettext("quote"),
			"order"    => gettext("order/commission"),
			"invoice"  => gettext("invoice"),
		);

		$days = array("--");
		for ($i=1; $i<=31; $i++) {
			$days[$i] = $i;
		}
		$months = array("--");
		for ($i=1; $i<=12; $i++) {
			$months[$i] = $i;
		}
		$years = array("--");
		for ($i=2003; $i<=date("Y")+5; $i++) {
			$years[$i] = $i;
		}

		foreach ($timestamp_fields as $k=>$v) {
			/* dates */
			$tbl->addTableRow();
				$tbl->addTableData("", "header");
					$tbl->addCode($v);
				$tbl->endTableData();
				$tbl->addTableData("", "data");
					if ($sales[0]["timestamp_".$k]>0) {
						$tbl->addSelectField("sales[timestamp_".$k."_day]",   $days,   date("d", $sales[0]["timestamp_".$k]));
						$tbl->addSelectField("sales[timestamp_".$k."_month]", $months, date("m", $sales[0]["timestamp_".$k]));
						$tbl->addSelectField("sales[timestamp_".$k."_year]",  $years,  date("Y", $sales[0]["timestamp_".$k]));
					} else {
						$tbl->addSelectField("sales[timestamp_".$k."_day]",   $days,   "");
						$tbl->addSelectField("sales[timestamp_".$k."_month]", $months, "");
						$tbl->addSelectField("sales[timestamp_".$k."_year]",  $years,  "");
					}
				$tbl->endTableData();
			$tbl->endTableRow();
		}

		/* user */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("user"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addHiddenField("sales[user_sales_id]", $sales[0]["user_sales_id"]);
				$useroutput = new User_output();
				$tbl->addCode( $useroutput->user_selection("salesuser_sales_id", $sales[0]["user_sales_id"], 0, 0, 0) );
			$tbl->endTableData();
		$tbl->endTableRow();
		/* score */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("expected score in %"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("sales[expected_score]", $sales[0]["expected_score"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* total sum */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("total costs in &euro;"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("sales[total_sum]", $sales[0]["orig_sum"]);
			$tbl->endTableData();
		$tbl->endTableRow();

		/* relation*/
		$address = new Address_data();
		$address_info = $address->getAddressNameByID($sales[0]["address_id"]);

		$tbl->addTableRow();

		$tbl->endTableRow();
			$tbl->insertTableData(gettext("contact").": ", "", "header");

			$tbl->addHiddenField("sales[address_id]", $sales[0]["address_id"]);
			$tbl->addTableData("", "data");
			$tbl->insertTag("span", $address_info, array("id"=>"layer_relation"));
			$tbl->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');");
		$tbl->endTableRow();
		
		/* project */
		$project = new Project_data();
		$project_info = $project->getProjectNameByID($sales[0]["project_id"]);

		$tbl->addTableRow();

		$tbl->endTableRow();
			$tbl->insertTableData(gettext("project").": ", "", "header");

			$tbl->addHiddenField("sales[project_id]", $sales[0]["project_id"]);
			$tbl->addTableData("", "data");
			$tbl->insertTag("span", $project_info, array("id"=>"layer_projectname"));
			//$tbl->insertAction("edit", "wijzigen", "javascript: popup('?mod=project&action=searchProject', 'searchproject');");
			$tbl->insertAction("edit", "wijzigen", "javascript: pickProject();");
		$tbl->endTableRow();
		
		/* actions */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->insertAction("back", gettext("back"), "javascript: window.close();");
				$tbl->addSpace(2);
				$tbl->insertAction("save", gettext("save"), "javascript: sales_save();");
			$tbl->endTableData();
		$tbl->endTableRow();

		$tbl->endTable();
		$venster->addCode( $tbl->generate_output() );

		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "sales");
		$output->addHiddenField("action", "");
		$output->addHiddenField("id", $_REQUEST["id"]);

		$output->addCode( $venster->generate_output() );

		$output->endTag("form");

		$output->load_javascript(self::include_dir."salesEdit.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function generate_list() {
		$start = (int)$_REQUEST["start"];

		$output = new Layout_output();
		$output->layout_page(gettext("sales")." ".gettext("overview"));

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("sales"),
			"subtitle" => gettext("overview")
		));
		/* menu items */
		if ($_REQUEST["search"]["address_id"] > 0) $and = "&address_id=".$_REQUEST["search"]["address_id"];
		$venster->addMenuItem(gettext("new"), "javascript: popup('?mod=sales&action=edit".$and."');");
		$venster->addMenuItem(gettext("address book"), "?mod=address");
		$venster->generateMenuItems();
		$venster->addVensterData();

		$tbl = new layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1
		));
		$tbl->addTableRow();
			$tbl->addTableData();

				$hdr = new Layout_table(array(
					"cellspacing" => 1,
					"cellpadding" => 1,
					"style"       => "border: 1px solid #666;"
				));
				$hdr->addTableRow();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));

					/* get users */
					$useroutput = new User_output();
					$hdr->addCode( gettext("user") );
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));

					$hdr->addHiddenField("search[user_id]", $_REQUEST["search"]["user_id"]);
					$hdr->addCode( $useroutput->user_selection("searchuser_id", $_REQUEST["search"]["user_id"], 0, 0, 0, 0) );

					$hdr->insertAction("forward", gettext("search"), "javascript: submitform();");
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));

					/* relation*/
					$address = new Address_data();
					$address_info = $address->getAddressNameByID($_REQUEST["search"]["address_id"]);

					/* address id */
					$hdr->addCode( gettext("contact").": " );
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addHiddenField("search[address_id]", $_REQUEST["search"]["address_id"]);
					$hdr->insertTag("span", $address_info, array("id"=>"layer_relation"));
					$hdr->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');");

				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));

					/* projectname*/
					if($_REQUEST["productId"]) { 
						$_REQUEST["search"]["project_id"] = $_REQUEST["productId"]; 
					} else { 
						$_REQUEST["search"]["project_id"] = $_REQUEST["search"]["project_id"]; 
					}
					$project = new Project_data();
					$project_info = $project->getProjectNameByID($_REQUEST["search"]["project_id"]);

					/* project id */
					$hdr->addCode( gettext("project").": " );
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addHiddenField("search[project_id]", $_REQUEST["search"]["project_id"]);
					$hdr->insertTag("span", $project_info, array("id"=>"layer_projectname"));
					$hdr->insertAction("edit", "wijzigen", "javascript: popup('?mod=project&action=searchProject', 'searchproject');");

				$hdr->endTableData();
				
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					/* search */
					$hdr->addCode(gettext("search").": ");
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addTextField("search[text]", $_REQUEST["search"]["text"]);
					$hdr->insertAction("forward", gettext("search"), "javascript: submitform();");

				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					/* active */
					$hdr->addCode(gettext("show all inactive sales items").": ");
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addCheckBox("search[in_active]", "1", $_REQUEST["search"]["in_active"]);
					$hdr->insertAction("forward", gettext("search"), "javascript: submitform();");

				$hdr->endTableData();
			$hdr->endTableRow();
			$hdr->endTable();

			$tbl->addCode( $hdr->generate_output() );
			$tbl->addTag("br");

			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode( $tbl->generate_output() );

		$options = array(
			"text"       => $_REQUEST["search"]["text"],
			"sort"       => $_REQUEST["sort"],
			"user_id"    => $_REQUEST["search"]["user_id"],
			"address_id" => $_REQUEST["search"]["address_id"],
			"project_id" => $_REQUEST["search"]["project_id"],
			"in_active"  => $_REQUEST["search"]["in_active"]
		);

		$sales_data = new Sales_data();
		$data = $sales_data->getSalesBySearch($options, $_REQUEST["start"], $_REQUEST["sort"]);
		$view = new Layout_view();
		$view->addData($data["data"]);

		/* add the mappings (columns) we needed */
		$view->addMapping("%%complex_heading", "%%complex_address");
		$view->addMapping(gettext("prospect"), "%h_timestamp_prospect");
		$view->addMapping(gettext("quote"), "%h_timestamp_proposal");
		$view->addMapping(gettext("order/commission"), "%h_timestamp_order");
		$view->addMapping(gettext("invoice"), "%h_timestamp_invoice");

		$view->addMapping(gettext("user"), "%username");
		$view->addMapping(gettext("score"), array("%expected_score", "&#037;"), "right");
		$view->addMapping(gettext("price"), "%total_sum", "right");
		$view->addMapping(" ", "%%complex_actions");
		$view->addSubMapping("%%complex_subject", 1);

		/* define sort columns */
		$view->defineSortForm("sort", "velden");
		$view->defineSort(gettext("prospect"), "timestamp_prospect");
		$view->defineSort(gettext("quote"), "timestamp_proposal");
		$view->defineSort(gettext("order/commission"), "timestamp_order");
		$view->defineSort(gettext("invoice"), "timestamp_invoice");
		$view->defineSort(gettext("contact"), "companyname");
		$view->defineSort(gettext("title"), "subject");
		$view->defineSort(gettext("price"), "total_sum");
		$view->defineSort(gettext("score"), "expected_score");
		$view->defineSort(gettext("user"), "username");


		$view->defineComplexMapping("complex_heading", array(
			array(
				"text"    => gettext("salesitem")
			),
			array(
				"text"    => array(" / ", gettext("contact")),
				"alias"   => "relatie"
			)
		));
		$view->defineComplexMapping("complex_subject", array(
			array(
				"type"    => "link",
				"link"    => array("javascript: popup('index.php?mod=sales&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);"),
				"text"    => "%subject"
			)
		));
		$view->defineComplexMapping("complex_address", array(
			array(
				"type" => "action",
				"src"  => "addressbook",
				"check" => "%has_address"
			),
			array(
				"type"  => "link",
				"text"  => "%h_address",
				"link"  => array("?mod=address&action=relcard&id=", "%address_id"),
				"check" => "%has_address"
			),
			array(
				"text" => "<BR>",
				"check" => "%has_address"
			),
			array(
				"type" => "action",
				"src"  => "folder_open",
				"check" => "%has_project"
			),
			array(
				"type"  => "link",
				"text"  => "%h_project",
				"link"  => array("?mod=project&action=showhours&id=", "%project_id"),
				"check" => "%has_project"
			)
		));

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "edit",
				"alt"     => gettext("edit"),
				"link"    => array("javascript: popup('index.php?mod=sales&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);")
			),
			array(
				"type"    => "action",
				"src"     => "delete",
				"alt"     => gettext("delete"),
				"link"    => array("?mod=sales&action=delete&id=", "%id"),
				"confirm" => gettext("Are you sure you want to delete this item?")
			)
		));

		$venster->addCode( $view->generate_output() );

		$paging = new Layout_paging();
		$paging->setOptions($start, $data["count"], "javascript: blader('%%');");
		$venster->addCode( $paging->generate_output() );

		$totals = $sales_data->getTotals();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("total number of orders/commissions").":", "", "header");
			$tbl->insertTableData($totals["count"], "", "header");
			$tbl->insertTableData(gettext("avg score").":", "", "header");
			$tbl->insertTableData($totals["score"]."%", "", "header");
			$tbl->insertTableData(gettext("total sum").":", "", "header");
			$tbl->insertTableData("&euro; ".$totals["sum"], "", "header");
			$tbl->insertTableData(gettext("	total").":", "", "header");
			$tbl->insertTableData("&euro; ".$totals["average"], "", "header");
		$tbl->endTableRow();
		$tbl->endTable();
		$venster->addTag("br");
		$venster->addCode($tbl->generate_output());

		$venster->endVensterData();

		$output->addHiddenField("mod", "sales");
		$output->addHiddenField("action", "");
		$output->addHiddenField("sort", $_REQUEST["sort"]);
		$output->addHiddenField("id", "");
		$output->addHiddenField("start", $start);

		$output->addCode( $venster->generate_output() );

		$output->endTag("form");

		$output->load_javascript(self::include_dir."salesList.js");

		$history = new Layout_history();
		$output->addCode( $history->generate_save_state("action") );


		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function salesSave() {
		$data = new Sales_data();
		$data->saveItem();

		$output = new Layout_output();
		$output->start_javascript();
			if ($_REQUEST["sales"]["fromnotes"]) {
				$output->addCode("opener.location.href = opener.location.href;");
			} else {
				$output->addCode("opener.document.getElementById('velden').submit();");
			}
			$output->addCode("window.close();");
		$output->end_javascript();
		$output->exit_buffer();
	}
}
?>
