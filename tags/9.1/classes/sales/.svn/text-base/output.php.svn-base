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

		/* get relation name */
		$address = new Address_data();
		if ((int)$sales[0]["address_id"] && !$sales[0]["multirel"]) {
			$relname = $address->getAddressNameById($sales[0]["address_id"]);
		} else {
			$relname = "";
		}
		/* see if we need to do some magic on the selected addresses */
		if ($sales[0]["multirel"]) {
			$address_ids = explode(",", $sales[0]["multirel"]);
			$address_ids[] = $sales[0]["address_id"];
			sort($address_ids);
			$multirel = array();
			foreach ($address_ids as $aid) {
				$multirel[$aid] = $address->getAddressNameById($aid);
			}
			unset($address_ids);
			unset($sales[0]["address_id"]);
			$relname = "";
		} else {
			$multirel = array(
				$sales[0]["address_id"] => $address->getAddressNameById($sales[0]["address_id"])
			);
			unset($sales[0]["address_id"]);
			$relname = "";
		}

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
		
		/* classifications */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("classification"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addHiddenField("sales[classification]", $sales[0]["classification"]);
				$classification = new Classification_output();
				$tbl->addCode( $classification->classification_selection("salesclassification", $sales[0]["classification"]) );
			$tbl->endTableData();
		$tbl->endTableRow();
		
		/* active */
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
		$calendar = new Calendar_output();
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
						$tbl->addCode( $calendar->show_calendar("document.getElementById('salestimestamp_".$k."_day')", "document.getElementById('salestimestamp_".$k."_month')", "document.getElementById('salestimestamp_".$k."_year')" ));
					} else {
						$tbl->addSelectField("sales[timestamp_".$k."_day]",   $days,   "");
						$tbl->addSelectField("sales[timestamp_".$k."_month]", $months, "");
						$tbl->addSelectField("sales[timestamp_".$k."_year]",  $years,  "");
						$tbl->addCode( $calendar->show_calendar("document.getElementById('salestimestamp_".$k."_day')", "document.getElementById('salestimestamp_".$k."_month')", "document.getElementById('salestimestamp_".$k."_year')" ));
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
		/* access */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("readonly access"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addHiddenField("sales[users]", $sales[0]["users"]);
				$useroutput = new User_output();
				//$tbl->addCode( $useroutput->user_selection("projectusers", $projectinfo["users"], 1, 0, 1, 0, 1) );
				$tbl->addCode($useroutput->user_selection("salesusers", $sales[0]["users"], array(
					"multiple" => 1,
					"inactive" => 1,
					"groups"   => 1,
					"confirm"  => 1
				)));
				unset($useroutput);
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
		
		/* relation (multiple) */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("contact"), "", "header");
			$tbl->addTableData("", "data");
			$tbl->addHiddenField("sales[address_id]", $sales[0]["address_id"]);
				$tbl->insertTag("span", $relname, array(
					"id" => "searchrel"
				));
				$tbl->addSpace(1);
				$tbl->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
			$tbl->endTableData();
		$tbl->endTableRow();
	
		/* relation
		$address = new Address_data();
		$address_info = $address->getAddressNameByID($sales[0]["address_id"]);

		$tbl->addTableRow();
			$tbl->insertTableData(gettext("contact").": ", "", "header");

			$tbl->addHiddenField("sales[address_id]", $sales[0]["address_id"]);
			$tbl->addTableData("", "data");
			$tbl->insertTag("span", $address_info, array("id"=>"layer_relation"));
			$tbl->insertAction("edit", gettext("edit"), "javascript: popup('?mod=address&action=searchRel', 'search_address', 0, 0, 1);");
		$tbl->endTableRow();
		*/
		
		/* project */
		$project = new Project_data();
		$project_info = $project->getProjectNameByID($sales[0]["project_id"]);

		$tbl->addTableRow();

		$tbl->endTableRow();
			$tbl->insertTableData(gettext("project").": ", "", "header");

			$tbl->addHiddenField("sales[project_id]", $sales[0]["project_id"]);
			$tbl->addTableData("", "data");
			$tbl->insertTag("span", $project_info, array("id"=>"layer_projectname"));
			$tbl->insertAction("edit", gettext("edit"), "javascript: pickProject();");
		$tbl->endTableRow();
		
		/* actions */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
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
		/* do some more magic with the rel field if necessary */
		if (is_array($multirel)) {
			$output->start_javascript();
			$output->addCode("addLoadEvent( update_relsearch() );\n");
			$output->addCode("function update_relsearch() { \n");
			foreach ($multirel as $i=>$n) {
				if ($i) {
					$output->addCode("\n");
					$output->addCode("selectRel($i, \"$n\");");
				}
			}
			$output->addCode("\n}\n");
			$output->end_javascript();
		}
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function generate_list() {
		$start = (int)$_REQUEST["start"];
		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		if($_REQUEST["productId"]) { 
			$_REQUEST["search"]["project_id"] = $_REQUEST["productId"]; 
		} else { 
			$_REQUEST["search"]["project_id"] = $_REQUEST["search"]["project_id"]; 
		}
		$options = array(
			"text"       => $_REQUEST["search"]["text"],
			"sort"       => $_REQUEST["sort"],
			"user_id"    => $_REQUEST["search"]["user_id"],
			"address_id" => $_REQUEST["search"]["address_id"],
			"project_id" => $_REQUEST["search"]["project_id"],
			"classification" => $_REQUEST["search"]["classification"],
			"in_active"  => $_REQUEST["search"]["in_active"]
		);
		$sales_data = new Sales_data();
		$data = $sales_data->getSalesBySearch($options, $_REQUEST["start"], $_REQUEST["sort"]);
		$totals = $sales_data->getTotals();

		/* XML data export */
		if ($_REQUEST["export_xml"]) {
			$conversion = new Layout_conversion;
			$string = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<export>\n";
			if (!is_array($data["data"])) {
				$string .= "<item>".gettext("no items found")."</item>";
			} else {
				foreach ($data["data"] as $dat) {
					$string .= "<item>\n";
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "salesitem", str_replace("&", "&amp;", $conversion->str2utf8($dat["subject"])));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "description", str_replace("&", "&amp;", $conversion->str2utf8($dat["description"])));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "contact", str_replace("&", "&amp;", $conversion->str2utf8($dat["companyname"])));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "project", htmlentities($conversion->str2utf8($dat["h_project"]), ENT_NOQUOTES, "UTF-8"));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "prospect", htmlentities($dat["h_timestamp_prospect"], ENT_NOQUOTES, "UTF-8"));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "quote", htmlentities($dat["h_timestamp_proposal"], ENT_NOQUOTES, "UTF-8"));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "order", htmlentities($dat["h_timestamp_order"], ENT_NOQUOTES, "UTF-8"));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "invoice", htmlentities($dat["h_timestamp_invoice"], ENT_NOQUOTES, "UTF-8"));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "user", htmlentities($conversion->str2utf8($dat["username"]), ENT_NOQUOTES, "UTF-8"));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "score", htmlentities($dat["expected_score"], ENT_NOQUOTES, "UTF-8"));
					$string .= sprintf("<%1\$s>%2\$s</%1\$s>\n", "price", htmlentities(number_format($dat["orig_sum"], 2, ",", "."), ENT_NOQUOTES, "UTF-8"));
					$string .= "</item>\n";
				}
			}
			$string .= "</export>\n";
			
			header("Content-Transfer-Encoding: binary");
			header("Content-Type: text/xml; charset=UTF-8");

			if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
				header("Content-Disposition: filename=salesitems.xml"); //msie 5.5 header bug
			}else{
				header("Content-Disposition: attachment; filename=salesitems.xml");
			}
			echo $string;
			exit();
		} else if ($_REQUEST["export_field"]) {
			/* CSV data export */
			$conversion = new Layout_conversion();
			$csv = array();
			$csv[]= gettext("salesitem");
			$csv[]= gettext("contact");
			$csv[]= gettext("project");
			$csv[]= gettext("prospect");
			$csv[]= gettext("quote");
			$csv[]= gettext("order/commission");
			$csv[]= gettext("invoice");
			$csv[]= gettext("user");
			$csv[]= gettext("score");
			$csv[]= gettext("price");
			$csvdata = $conversion->generateCSVRecord($csv);
			unset($csv);
			
			if (is_array($data["data"])) {
				foreach ($data["data"] as $dat) {
					$csv = array();
					$csv[] = $dat["subject"];
					$csv[] = $dat["companyname"];
					$csv[] = $dat["h_project"];
					$csv[] = $dat["h_timestamp_prospect"];
					$csv[] = $dat["h_timestamp_proposal"];
					$csv[] = $dat["h_timestamp_order"];
					$csv[] = $dat["h_timestamp_invoice"];
					$csv[] = $dat["username"];
					$csv[] = $dat["expected_score"];
					$csv[] = number_format($dat["orig_sum"], 2, ",", ".");
					$csvdata .= $conversion->generateCSVRecord($csv);
					unset($csv);
				}
			}
			header("Content-Transfer-Encoding: binary");
			header("Content-Type: text/plain; charset=UTF-8");

			if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
				header("Content-Disposition: filename=salesitems.csv"); //msie 5.5 header bug
			}else{
				header("Content-Disposition: attachment; filename=salesitems.csv");
			}
			echo $csvdata;
			exit();
		} else {

		$view = new Layout_view();
		$view->addData($data["data"]);

		$output = new Layout_output();
		$output->layout_page(gettext("Sales")." ".gettext("overview"));

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));
		// Export CSV
		$output->addHiddenField("export_field", "0");
		// Export XML
		$output->addHiddenField("export_xml", "0");
		
		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("Sales"),
			"subtitle" => gettext("overview")
		));

		/* menu items */
		if ($_REQUEST["search"]["address_id"] > 0) $and = "&address_id=".$_REQUEST["search"]["address_id"];
		$venster->addMenuItem(gettext("new"), "javascript: popup('?mod=sales&action=edit".$and."');");
		$venster->addMenuItem(gettext("address book"), "?mod=address");
		if ($_REQUEST["search"]["project_id"]){
			$venster->addMenuItem(gettext("back to project"), "?mod=project&action=showhours&id=".$_REQUEST["search"]["project_id"]);
		}
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
					//"style"       => "border: 1px solid #666;"
				));
				$hdr->addTableRow();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));

					/* get users */
					$useroutput = new User_output();
					$hdr->addCode( gettext("user").": "  );
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
					$hdr->insertAction("edit", gettext("edit"), "javascript: popup('?mod=address&action=searchRel', 'search_address');");

				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));

					/* projectname*/
					$project = new Project_data();
					$project_info = $project->getProjectNameByID($_REQUEST["search"]["project_id"]);

					/* project id */
					$hdr->addCode( gettext("project").": " );
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addHiddenField("search[project_id]", $_REQUEST["search"]["project_id"]);
					$hdr->insertTag("span", $project_info, array("id"=>"layer_projectname"));
					$hdr->insertAction("edit", gettext("edit"), "javascript: pickProject();");

				$hdr->endTableData();
				
				/* classifaction search */
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addCode( gettext("classification").": " );
				$hdr->endTableData();
				
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addHiddenField("search[classification]", $_REQUEST["search"]["classification"]);
					$classification = new Classification_output();
					$classification_selected = ($_REQUEST["search"]["classification"]) ? $_REQUEST["search"]["classification"] : '';
					$hdr->addCode($classification->classification_selection("searchclassification", $classification_selected));
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
		$spacer = new Layout_output();
		$spacer->addSpace(15);
		
		/* add the mappings (columns) we needed */
		$view->addMapping(gettext("salesitem"), "%subject");
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping(gettext("contact"), "%%complex_address");
		$view->addMapping(gettext("project"), "%%complex_project");
		$view->addMapping(gettext("prospect"), "%h_timestamp_prospect");
		$view->addMapping(gettext("quote"), "%h_timestamp_proposal");
		$view->addMapping(gettext("order/commission"), "%h_timestamp_order");
		$view->addMapping(gettext("invoice"), "%h_timestamp_invoice");

		$view->addMapping(gettext("user"), "%username");
		$view->addMapping(gettext("score"), array("%expected_score", "&#037;"), "right");
		$view->addMapping(gettext("price"), "%total_sum", "right");
		$view->addMapping($spacer->generate_output(), "%%complex_actions");
		unset($spacer);

		/* define sort columns */
		$view->defineSortForm("sort", "velden");
		$view->defineSort(gettext("prospect"), "timestamp_prospect");
		$view->defineSort(gettext("quote"), "timestamp_proposal");
		$view->defineSort(gettext("order/commission"), "timestamp_order");
		$view->defineSort(gettext("invoice"), "timestamp_invoice");
		$view->defineSort(gettext("contact"), "companyname");
		$view->defineSort(gettext("salesitem"), "subject");
		$view->defineSort(gettext("price"), "total_sum");
		$view->defineSort(gettext("score"), "expected_score");
		$view->defineSort(gettext("user"), "username");
		$view->defineSort(gettext("project"), "h_project");
		$view->defineSort(gettext("description"), "description");

		$view->defineComplexMapping("complex_project", array(
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
				"type" => "multilink",
				"link" => array("index.php?mod=address&action=relcard&id=", "%all_address_ids"),
				"text" => "%all_address_names",
				"check" => "%all_address_ids"
			),
			array(
				"text" => $tbl->addTag("br"),
				"check" => "%has_address"
			)
		));

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "edit",
				"alt"     => gettext("edit"),
				"link"    => array("javascript: popup('index.php?mod=sales&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);"),
				"check"   => "%check_actions"
			),
			array(
				"type"    => "action",
				"src"     => "delete",
				"alt"     => gettext("delete"),
				"link"    => array("?mod=sales&action=delete&id=", "%id"),
				"confirm" => gettext("Are you sure you want to delete this item?"),
				"check"   => "%check_actions"
			)
		));

		$venster->addCode( $view->generate_output() );

		$paging = new Layout_paging();
		$paging->setOptions($start, $data["count"], "javascript: blader('%%');");
		$venster->addCode( $paging->generate_output() );
		$venster->insertAction("file_download", gettext("export as")." CSV", 'javascript: exportSale();');
		$venster->insertAction("file_xml", gettext("export as")." XML", 'javascript: exportSaleXML();');

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
	}

	public function salesSave() {
		$data = new Sales_data();
		$data->saveItem();

		$output = new Layout_output();
		$output->start_javascript();
			if ($_REQUEST["sales"]["fromnotes"]) {
				$output->addCode("parent.location.href = parent.location.href;");
			} else {
				$output->addCode("parent.document.getElementById('velden').submit();");
			}
			$output->addCode("closepopup();");
		$output->end_javascript();
		$output->exit_buffer();
	}
}
?>
