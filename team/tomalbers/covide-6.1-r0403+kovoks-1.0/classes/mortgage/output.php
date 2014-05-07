<?php
/**
 * Covide Groupware-CRM Mortgage output class
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
Class Mortgage_output {

	/* constants */
	const include_dir =  "classes/mortgage/inc/";

	/* variables */

	/* methods */
	public function mortgageEdit($options = array()) {

		$output = new Layout_output();
		if ($options["noiface"] == 1) {
			$output->layout_page(gettext("mortgage"), 1);
		} else {
			$output->layout_page(gettext("mortgage"));
		}

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php"
		));

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("mortgage"),
			"subtitle" => gettext("bewerken")
		));
		$venster->addVensterData();

		$mortgage_data = new Mortgage_data();
		$mortgage_info = $mortgage_data->getMortgageById($_REQUEST["id"]);
		$mortgage =& $mortgage_info["data"];

		$tbl = new layout_table();
		/* type */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("soort"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$available_types = array(
					0 => gettext("hypotheek"),
					1 => gettext("levensverzekering")
				);
				$tbl->addSelectField("mortgage[type]", $available_types, $mortgage[0]["type"]);
			$tbl->endTableData();
		$tbl->endTableRow();

		/* addresses in select field */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("geldverstrekker"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSelectField("mortgage[investor]", $mortgage_data->getMortgageAddresses(1), $mortgage[0]["investor"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("verzekeraar"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSelectField("mortgage[insurancer]", $mortgage_data->getMortgageAddresses(2), $mortgage[0]["insurancer"]);
			$tbl->endTableData();
		$tbl->endTableRow();

		/* subject */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("titel"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("mortgage[subject]", $mortgage[0]["subject"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* description */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("omschrijving"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextArea("mortgage[description]", $mortgage[0]["description"], array(
					"style" => "width: 300px; height: 100px;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* description */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("actief"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCheckbox("mortgage[is_active]", 1, $mortgage[0]["is_active"]);
			$tbl->endTableData();
		$tbl->endTableRow();

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

		/* dates */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("datum"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				if ($mortgage[0]["timestamp"]>0) {
					$tbl->addSelectField("mortgage[timestamp_day]",   $days,   date("d", $mortgage[0]["timestamp"]));
					$tbl->addSelectField("mortgage[timestamp_month]", $months, date("m", $mortgage[0]["timestamp"]));
					$tbl->addSelectField("mortgage[timestamp_year]",  $years,  date("Y", $mortgage[0]["timestamp"]));
				} else {
					$tbl->addSelectField("mortgage[timestamp_day]",   $days,   date("d"));
					$tbl->addSelectField("mortgage[timestamp_month]", $months, date("m"));
					$tbl->addSelectField("mortgage[timestamp_year]",  $years,  date("Y"));
				}
				$calendar = new Calendar_output();
				$tbl->addCode( $calendar->show_calendar("document.getElementById('mortgagetimestamp_day')", "document.getElementById('mortgagetimestamp_month')", "document.getElementById('mortgagetimestamp_year')" ));
			$tbl->endTableData();
		$tbl->endTableRow();

		/* user */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("gebruiker"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addHiddenField("mortgage[user_id]", $mortgage[0]["user_id"]);
				$useroutput = new User_output();
				$tbl->addCode( $useroutput->user_selection("mortgageuser_id", $mortgage[0]["user_id"], 0, 0, 0) );
			$tbl->endTableData();
		$tbl->endTableRow();

		/* total sum */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("bedrag in &euro;"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("mortgage[total_sum]", $mortgage[0]["orig_sum"]);
			$tbl->endTableData();
		$tbl->endTableRow();

		/* total sum */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("jaarpremie in &euro;"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("mortgage[year_payement]", $mortgage[0]["orig_payement"]);
			$tbl->endTableData();
		$tbl->endTableRow();

		/* relation*/
		$address = new Address_data();
		$address_info = $address->getAddressNameByID($mortgage[0]["address_id"]);

		$tbl->addTableRow();

		$tbl->endTableRow();
			$tbl->insertTableData(gettext("relatie").": ", "", "header");

			$tbl->addHiddenField("mortgage[address_id]", $mortgage[0]["address_id"]);
			$tbl->addTableData("", "data");
			$tbl->insertTag("span", $address_info, array("id"=>"layer_relation"));
			$tbl->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');");
		$tbl->endTableRow();
		/* actions */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->insertAction("back", gettext("terug"), "javascript: window.close();");
				$tbl->addSpace(2);
				$tbl->insertAction("save", gettext("opslaan"), "javascript: mortgage_save();");
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
		$output->addHiddenField("mod", "mortgage");
		$output->addHiddenField("action", "");
		$output->addHiddenField("id", $_REQUEST["id"]);

		$output->addCode( $venster->generate_output() );

		$output->endTag("form");

		$output->load_javascript(self::include_dir."mortgageEdit.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function generate_list() {

		$start = (int)$_REQUEST["start"];

		$output = new Layout_output();
		$output->layout_page(gettext("hypotheek")." ".gettext("overzicht"));

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("hypotheek"),
			"subtitle" => gettext("overzicht")
		));
		/* menu items */
		$venster->addMenuItem(gettext("nieuw"), "javascript: popup('?mod=mortgage&action=edit');");
		$venster->addMenuItem(gettext("adresboek"), "?mod=address");
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

					$hdr->addCode( gettext("gebruiker") );
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));

					$hdr->addHiddenField("search[user_id]", $_REQUEST["search"]["user_id"]);
					$hdr->addCode( $useroutput->user_selection("searchuser_id", $_REQUEST["search"]["user_id"], 0, 0, 0, 0) );

					$hdr->insertAction("forward", gettext("zoek"), "javascript: submitform();");
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));

					/* relation*/
					$address = new Address_data();
					$address_info = $address->getAddressNameByID($_REQUEST["search"]["address_id"]);

					/* address id */
					$hdr->addCode( gettext("relatie").": " );
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addHiddenField("search[address_id]", $_REQUEST["search"]["address_id"]);
					$hdr->insertTag("span", $address_info, array("id"=>"layer_relation"));
					$hdr->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');");

				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					/* search */
					$hdr->addCode(gettext("zoeken").": ");
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addTextField("search[text]", $_REQUEST["search"]["text"]);
					$hdr->insertAction("forward", gettext("zoek"), "javascript: submitform();");

				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					/* active */
					$hdr->addCode(gettext("toon inactieve hypotheek items").": ");
				$hdr->endTableData();
				$hdr->addTableData(array("style" => "vertical-align: bottom;"));
					$hdr->addCheckBox("search[in_active]", "1", $_REQUEST["search"]["in_active"]);
					$hdr->insertAction("forward", gettext("zoek"), "javascript: submitform();");

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
			"in_active"  => $_REQUEST["search"]["in_active"]
		);

		$mortgage_data = new Mortgage_data();
		$data = $mortgage_data->getMortgageBySearch($options, $_REQUEST["start"], $_REQUEST["sort"]);

		$view = new Layout_view();
		$view->addData($data["data"]);

		/* add the mappings (columns) we needed */
		$view->addMapping("%%complex_heading", "%%complex_address");
		$view->addMapping(gettext("soort"), "%h_type");
		$view->addMapping(gettext("titel"), "%subject");
		$view->addMapping(gettext("datum"), "%h_timestamp");

		$view->addMapping(gettext("gebruiker"), "%username");
		$view->addMapping(gettext("bedrag"), "%total_sum", "right");
		//TODO: fix column name!
		$view->addMapping(gettext("jaarpremie"), "%year_payment_h", "right");
		$view->addMapping(gettext("provisie"), "%provision_h", "right");

		$view->addMapping(" ", "%%complex_actions");
		$view->addSubMapping("%%complex_subject", 1);

		/* define sort columns */
		$view->defineSortForm("sort", "velden");
		$view->defineSort(gettext("soort"), "type");
		$view->defineSort(gettext("relatie"), "companyname");
		$view->defineSort(gettext("titel"), "subject");
		$view->defineSort(gettext("datum"), "timestamp");
		$view->defineSort(gettext("bedrag"), "total_sum");
		$view->defineSort(gettext("jaarpremie"), "year_payement");
		$view->defineSort(gettext("gebruiker"), "username");


		$view->defineComplexMapping("complex_heading", array(
			array(
				"text"    => gettext("hypoyheekitem")
			),
			array(
				"text"    => array(" / ", gettext("relatie")),
				"alias"   => "relatie"
			)
		));
		$view->defineComplexMapping("complex_subject", array(
			array(
				"type"    => "link",
				"link"    => array("javascript: popup('index.php?mod=mortgage&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);"),
				"text"    => "%subject"
			)
		));


		$view->defineComplexMapping("complex_address", array(
			array(
				"type"  => "link",
				"text"  => "%h_address",
				"link"  => array("?mod=address&action=relcard&id=", "%address_id")
			)
		));

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "edit",
				"alt"     => gettext("bewerken"),
				"link"    => array("javascript: popup('index.php?mod=mortgage&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);")
			),
			array(
				"type"    => "action",
				"src"     => "delete",
				"alt"     => gettext("verwijderen"),
				"link"    => array("?mod=mortgage&action=delete&id=", "%id"),
				"confirm" => gettext("Weet u zeker dat u dit item wilt verwijderen?")
			)
		));

		$venster->addCode( $view->generate_output() );

		$paging = new Layout_paging();
		$paging->setOptions($start, $data["count"], "javascript: blader('%%');");
		$venster->addCode( $paging->generate_output() );

		$totals = $mortgage_data->getTotals();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("totaal aantal").":", "", "header");
			$tbl->insertTableData($totals["count"], "", "header");
			$tbl->insertTableData(gettext("totaal bedrag").":", "", "header");
			$tbl->insertTableData("&euro; ".$totals["sum"], "", "header");
			$tbl->insertTableData(gettext("totaal provisie").":", "", "header");
			$tbl->insertTableData("&euro; ".$totals["provision"], "", "header");
		$tbl->endTableRow();
		$tbl->endTable();
		$venster->addTag("br");
		$venster->addCode($tbl->generate_output());


		$venster->endVensterData();

		$output->addHiddenField("mod", "mortgage");
		$output->addHiddenField("action", "");
		$output->addHiddenField("sort", $_REQUEST["sort"]);
		$output->addHiddenField("id", "");
		$output->addHiddenField("start", $start);

		$output->addCode( $venster->generate_output() );

		$output->endTag("form");

		$output->load_javascript(self::include_dir."mortgageList.js");

		$history = new Layout_history();
		$output->addCode( $history->generate_save_state("action") );


		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function mortgageSave() {
		$data = new Mortgage_data();
		$data->saveItem();

		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode("opener.document.getElementById('velden').submit();");
			#$output->addCode("window.close();");
		$output->end_javascript();
		$output->exit_buffer();
	}
}
?>
