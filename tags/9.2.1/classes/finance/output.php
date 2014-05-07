<?php
/**
 * Covide Finance module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Finance_output {

	/* constants */
	const include_dir = "classes/finance/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "finance";

	/* function __construct {{{  */
	public function __construct() {
		//foo
	}
	/* }}} */
	public function toonwelkom() {
		#$GLOBALS["covide"]->license["has_finance"] = 0;

		$output = new Layout_output();
		$output->layout_page("finance");
		$venster = new Layout_venster(array(
			"title" => gettext("Finance"),
			"subtitle" => gettext_nl("welkom")
		));
		if ($_SESSION["locale"] == "nl_NL") {
			$venster->addMenuItem(gettext("help (wiki)"), "http://wiki.covide.nl/Factureren", array("target" => "_blank"), 0);
		}
		if ($GLOBALS["covide"]->license["has_finance"]) {
			$venster->addMenuItem(gettext_nl("speciale boekingen"), "javascript: popup('non-oop/finance/bankboek.php?action=tonenSpeciaal');");
			$venster->addMenuItem(gettext_nl("lopende inkopen"), "javascript: popup('non-oop/finance/bankboek.php?action=inkopen');");
			$venster->addMenuItem(gettext_nl("lopende verkopen"), "javascript: popup('non-oop/finance/bankboek.php?action=verkopen');");
			$venster->addMenuItem(gettext_nl("grootboek"), "javascript: popup('non-oop/factuur/grootboeknummers.php');");
			$venster->addMenuItem(gettext_nl("btw module"), "javascript: popup('non-oop/finance/bankboek.php?action=btw');");
			$venster->addMenuItem(gettext_nl("inkoopfacturen"), "javascript: popup('non-oop/finance/inkoop.php');");
			$venster->addMenuItem(gettext_nl("omzet"), "javascript: popup('non-oop/finance/bankboek.php?action=omzet');");
			$venster->addMenuItem(gettext_nl("verkoopfacturen"), "classes/finance/non-oop/factuur/index.php?chr=`");
		} else {
			$venster->addMenuItem(gettext_nl("verkoopfacturen"), "classes/finance/non-oop/factuur/index.php?chr=`", "", 0);
		}
		$venster->generateMenuItems();
		$venster->addVensterData();

		$finance_data = new Finance_data();
		$data = $finance_data->getWelkomData();

		$tbl = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1,
			"width"       => "100%",
		), 0, 1);

		if ($GLOBALS["covide"]->license["has_finance"]) {
			/* standen */
			$tbl->addTableRow();
				$tbl->insertTableHeader(gettext_nl("Standen op dit moment").":", array(
					"colspan" => 6,
					"style"   => "text-align: left;"
				));
			$tbl->endTableRow();
			foreach ($data["standen"] as $k=>$v) {
				$tbl->addTableRow();
					$tbl->insertTableData($v["titel"], array(
						"colspan" => 2
					));
					$tbl->insertTableData($v["bedrag"]);
				$tbl->endTableRow();
			}
			/* overzichten */
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addSpace();
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->insertTableHeader(gettext_nl("Overzichten").":", array(
					"style"   => "text-align: left;",
					"colspan" => 2
				));
				$tbl->insertTableHeader(date("F Y", mktime()), array(
					"style"   => "text-align: right;"
				));
				$tbl->insertTableHeader(date("F Y", mktime(0,0,0,date("m")-1,1,date("Y"))), array(
					"style"   => "text-align: right;"
				));
				$tbl->insertTableHeader(date("F Y", mktime(0,0,0,date("m")-2,1,date("Y"))), array(
					"style"   => "text-align: right;"
				));
				$tbl->insertTableHeader(date("F Y", mktime(0,0,0,date("m")-3,1,date("Y"))), array(
					"style"   => "text-align: right;"
				));
			$tbl->endTableRow();

			foreach ($data["flow"]["this"] as $k=>$v) {
				$tbl->addTableRow();
					$tbl->insertTableData($k, array(
						"colspan" => 2
					));
					$tbl->insertTableData($v);
					$tbl->insertTableData($data["flow"]["prev"][$k]);
					$tbl->insertTableData($data["flow"]["prev2"][$k]);
					$tbl->insertTableData($data["flow"]["prev3"][$k]);
				$tbl->endTableRow();
			}

			/* openstaand */
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addSpace();
				$tbl->endTableData();
			$tbl->endTableRow();
		}
		$tbl->addTableRow();
			if ($GLOBALS["covide"]->license["has_finance"])
				$nrofposts = 5;
			else
				$nrofposts = 20;
			$tbl->insertTableHeader($nrofposts." ".gettext_nl("oudste openstaande posten").":", array(
				"style"   => "text-align: left;",
				"colspan" => 6
			));
		$tbl->endTableRow();
		foreach ($data["openstaand"] as $ko=>$openstaand) {
			$tbl->addTableRow();
				$tbl->insertTableHeader($ko);
				$tbl->addTableHeader(array(
					"style" => "text-align: right;"
				));
					$tbl->addCode(gettext_nl("totaal").": ");
				$tbl->endTableHeader();
				$tbl->addTableHeader();
					$tbl->addCode($openstaand["totaal"]);
				$tbl->endTableHeader();
				$tbl->addTableHeader(array(
					"style" => "text-align: right;"
				));
					$tbl->addCode(gettext_nl("factuur/boekstuk").": ");
				$tbl->endTableHeader();
				$tbl->addTableHeader(array(
					"style" => "text-align: left; font-weight: normal;",
					"colspan" => 2
				));
					$tbl->addCode(gettext_nl("totaal aantal").": ");
					$tbl->addSpace(2);
					$tbl->addCode($openstaand["count"]);
				$tbl->endTableHeader();
			$tbl->endTableRow();

			if (!is_array($openstaand["laatste"]))
				$openstaand["laatste"] = array();
			foreach ($openstaand["laatste"] as $r) {
				$tbl->addTableRow();
					$tbl->insertTableData($r["datum"]);
					$tbl->insertTableData($r["descr"]);
					$tbl->insertTableData($r["bedrag"]);
					$tbl->addTableData(array("style" => "text-align: right;"));
						$tbl->insertTag("a", $r["factuur"], array(
							href => sprintf("/classes/finance/non-oop/factuur/projecten.php?zoeken=%d", $r["factuur"])
						));
					$tbl->endTableData();
					$tbl->addTableData(array(
						"colspan" => 2
					));
						$tbl->insertTag("a", $r["address_name"], array(
							"href" => sprintf("?mod=address&action=relcard&id=%d", $r["address_id"])
						));
					$tbl->endTableData();
				$tbl->endTableRow();
			}
		}

		$tbl->endTable();
		$venster->addCode($tbl->generate_output());
		unset($tbl);

		$venster->endVensterData();

		$tbl = new Layout_table(array("width" => "100%"));
		$output->addCode( $tbl->createEmptyTable($venster->generate_output()) );
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function tonenSpeciaal() {
		$output = new Layout_output();
		$output->layout_page("finance");
		$venster = new Layout_venster(array(
			"title" => gettext("Finance"),
			"subtitle" => gettext_nl("overzicht van speciale boekingen")
		));
		$venster->addMenuItem(gettext_nl("nieuwe boeking"), "?mod=finance&action=invoerSpeciaal");
		$venster->addMenuItem(gettext_nl("bankboek"), "?mod=finance");
		$venster->generateMenuItems();
		$venster->addVensterData();

		$venster->addCode(gettext_nl("datum").": ");
		$finance_data = new Finance_data();

		$data = $_REQUEST["data"];
		if (!$data["month"]) $data["month"] = date("m");
		if (!$data["year"])  $data["year"]  = date("Y");

		$sel[-1] = gettext_nl("alle");
		for ($i=1;$i<=12;$i++)
			$sel[$i] = $i;

		$venster->addSelectField("data[month]", $sel, $data["month"]);
		unset($sel);

		for ($i=$finance_data->getFirstRecordDate(); $i<=date("Y")+1; $i++)
			$sel[$i] = $i;

		$venster->addSelectField("data[year]", $sel, $data["year"]);
		$venster->addSpace();
		$venster->addCode(gettext_nl("zoeken").": ");
		$venster->addTextField("data[search]", $data["search"], "", "", 1);
		$venster->insertAction("forward", gettext_nl("zoeken"), "javascript: document.getElementById('velden').submit();");

		$data = $finance_data->getSpecialeBoekingen($data);

		$view = new Layout_view();
		$view->addData($data);
		$view->addMapping(gettext_nl("grootboek"), "%grootboek_rekening");
		$view->addMapping(gettext_nl("tegenrekening"), "%tegen_rekening");
		$view->addMapping(gettext_nl("adres"), "%%complex_address");
		$view->addMapping(gettext_nl("datum"), "%datum_h");
		$view->addMapping(gettext_nl("omschrijving"), "%omschrijving");
		$view->addMapping(gettext_nl("bedrag"), "%bedrag_h");
		$view->addMapping("", "%%complex_action");
		$view->setHtmlField("bedrag_h");

		$view->defineComplexMapping("complex_address", array(
			array(
				"type" => "link",
				"text" => "%address_name",
				"link" => array("?mod=address&action=relcard&id=", "%debiteur"),
				"check" => "%debiteur"
			)
		));
		$view->defineComplexMapping("complex_action", array(
			array(
				"type"    => "action",
				"src"     => "delete",
				"alt"     => gettext_nl("verwijder"),
				"link"    => array("javascript: deleteRecord('", "%id", "')"),
				"confirm" => array(
					gettext_nl("Weet u zeker dat u"),
					" [", "%omschrijving", "] ",
					gettext_nl("wilt verwijderen?")
				),
				"check"   => "%allow_delete"
			),
			array(
				"type"  => "action",
				"src"   => "state_public",
				"alt"   => gettext_nl("locked"),
				"link"  => array(sprintf("javascript: alert('%s')",
					addslashes(gettext_nl("jaar is reeds afgesloten")))),
				"check" => "%is_locked"
			)
		));
		$venster->addCode($view->generate_output());
		$venster->endVensterData();
		$venster->start_javascript();
			$venster->addCode("
				function deleteRecord(id) {
					document.getElementById('id').value = id;
					document.getElementById('velden').action.value = 'deleteSpeciaal';
					document.getElementById('velden').submit();
				}
			");
		$venster->end_javascript();

		$tbl = new Layout_table();

		$output->addTag("form", array(
			"action" => "index.php",
			"method" => "post",
			"id"     => "velden"
		));
		$output->addHiddenField("mod", "finance");
		$output->addHiddenField("action", "tonenSpeciaal");
		$output->addHiddenField("id", 0);

		$output->addCode( $tbl->createEmptyTable($venster->generate_output()) );

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	public function invoerenSpeciaal() {
		$finance_data = new Finance_data();
		$output = new Layout_output();
		$output->layout_page("finance");
		$venster = new Layout_venster(array(
			"title" => gettext("Finance"),
			"subtitle" => gettext_nl("overzicht van speciale boekingen")
		));
		$venster->addMenuItem(gettext_nl("terug"), "?mod=finance&action=tonenSpeciaal");
		$venster->generateMenuItems();
		$venster->addVensterData();


		$tbl = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1
		), "", 1);
		/* grootboek */
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext_nl("grootboek rekening"));
			$tbl->addTableData();
				$tbl->addCode($this->addGrootboekField("data[grootboek_rekening]"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* tegenrekening */
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext_nl("tegen rekening"));
			$tbl->addTableData();
				$tbl->addCode($this->addTegenrekeningField("data[tegen_rekening]"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* relatie */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext_nl("relatie"), "", "header");
			$tbl->addTableData();
			$tbl->addHiddenField("data[address_id]", 0);
				$tbl->insertTag("span", $relname, array(
					"id" => "searchrel"
				));
				$tbl->addSpace(1);
				$tbl->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* datum */
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext_nl("datum"));
			$tbl->addTableData();
				$sel = array();
				for ($i=1;$i<=31;$i++) {
					$sel[$i] = $i;
				}
				$tbl->addSelectField("data[date][day]", $sel, date("d"));
				$sel = array();
				for ($i=1;$i<=12;$i++) {
					$sel[$i] = $i;
				}
				$tbl->addSelectField("data[date][month]", $sel, date("m"));
				$sel = array();
				$locked_years = $finance_data->getLockedYears();
				for ($i=$finance_data->getFirstRecordDate();$i <= date("Y")+1;$i++) {
					if (!in_array($i, $locked_years))
						$sel[$i] = $i;
				}
				$tbl->addSelectField("data[date][year]", $sel, date("Y"));

				$calendar_output = new Calendar_output();
				$tbl->addCode($calendar_output->show_calendar("datadateday", "datadatemonth", "datadateyear"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* bedrag */
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext_nl("bedrag in &euro;")."*");
			$tbl->addTableData();
				$tbl->addTextField("data[bedrag]", "", array("style" => "width: 80px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* omschrijving */
		$tbl->addTableRow();
			$tbl->insertTableHeader(gettext_nl("omschrijving"));
			$tbl->addTableData();
				$tbl->addTextField("data[omschrijving]", "", array("style" => "width: 200px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* note */
		$tbl->addTableRow();
			$tbl->insertTableHeader(
				"* = ".gettext_nl("Let op: plus en min bedragen worden gezien vanuit het bankboek"), array(
				"colspan" => 2
			));
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableHeader(array(
				"colspan" => 2
			));
				$tbl->insertAction("back", gettext_nl("terug"), "");
				$tbl->addSpace(2);
				$tbl->insertAction("save", gettext_nl("opslaan"), "javascript: document.getElementById('velden').submit();");
			$tbl->endTableHeader();
		$tbl->endTableRow();


		$tbl->endTable();
		$venster->addCode($tbl->generate_output());
		$venster->endVensterData();

		$venster->start_javascript();
			$venster->addCode("
				function selectRel(id, relname) {
					document.getElementById('dataaddress_id').value = id;
					document.getElementById('searchrel').innerHTML = relname;
				}
				var el = document.getElementById('databedrag');
				el.onchange = function() {
					el.value = el.value.replace(/,/g, '.');
				}
			");
		$venster->end_javascript();

		unset($tbl);
		$output->addTag("form", array(
			"action" => "index.php",
			"method" => "post",
			"id"     => "velden"
		));
		$output->addHiddenField("mod", "finance");
		$output->addHiddenField("action", "invoerSpeciaalBevestig");
		$output->addHiddenField("id", 0);

		$tbl = new Layout_table();
		$output->addCode( $tbl->createEmptyTable($venster->generate_output()) );

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function addGrootboekField($name, $id=0) {
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				$table->addSpace();
				$table->insertTag("span", sprintf("%s:", gettext_nl("kies een grootboekrekening")), array(
					"id" => "grootboek_name"
				));
				$table->addSpace();
			$table->endTableData();
			$table->addTableData();
				$table->addHiddenField($name, $id, "grootboek_id");
				$table->addTextField("grootboek_autocomplete", gettext_nl("zoek"), array("style" => "width: 60px;"));
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();

		$output = new Layout_output();
		$output->addCode($table->generate_output());
		$output->insertTag("div", "&nbsp;", array(
			"id"    => "grootboek_autocomplete_layer",
			"style" => "visibility:hidden; position:absolute; top:0px; left:0px; z-index: 10;"
		));
		$output->insertTag("iframe", "", array(
			"id"    => "grootboek_layer_iframe",
			"style" => "z-index: 6; display: none; left: 0px; position: absolute; top: 0px;",
			"src"   => "blank.htm",
			"frameborder" => 0,
			"scrolling"   => "no"
		));
		$output->load_javascript(self::include_dir."autocomplete_grootboek.js");

		return $output->generate_output();
	}
	public function addTegenrekeningField($name, $id=0) {
		$finance_data = new Finance_data();
		$output = new Layout_output();
		$ary = array("bank", "kas", "memoriaal");
		foreach ($ary as $a) {
			$output->addRadioField($name, $a, $finance_data->grootboeknummer[$a], $finance_data->grootboeknummer["bank"]);
		}
		return $output->generate_output();
	}
	public function relationCard($address_id, $toggle=0, $history=0) {
		$output = new Layout_output();

		$finance_data = new Finance_data();

		if ($toggle)
			$finance_data->toggleFactuur($toggle);

		$finance_info = $finance_data->getRelationCardData($address_id, $history);

		/*
		if ($GLOBALS["covide"]->license["has_finance"] && 1==0) {
			// special finance records
			if (count($finance_info["speciaal"]) > 0) {
				$venster = new Layout_venster(array(
					"title"   => gettext("finance"),
					"subtitle" => gettext_nl("speciale boekingen")
				));
				$venster->addVensterData();

				$view = new Layout_view();
				$view->addData($finance_info["speciaal"]);
				$view->addMapping(gettext_nl("from"), "%grootboek_van");
				$view->addMapping(gettext_nl("to"), "%grootboek_naar");
				$view->addMapping(gettext_nl("description"), "%omschrijving");
				$view->addMapping(gettext_nl("date"), "%datum_h");
				$view->addMapping(gettext_nl("amount"), "%bedrag_h", array(
					"allow_html" => 1
				));
				$view->addMapping("", "%%complex_actions");

				$venster->addCode($view->generate_output());
				$venster->endVensterData();

				$output->insertTag("div", $venster->generate_output());
			}
			// inkopen
			if (count($finance_info["inkopen"]) > 0) {
				$venster = new Layout_venster(array(
					"title"   => gettext("finance"),
					"subtitle" => gettext_nl("inkopen")
				));
				$venster->addVensterData();

				$view = new Layout_view();
				$view->addData($finance_info["inkopen"]);
				$view->addMapping(gettext_nl("boekstuknummer"), "%boekstuknr");
				$view->addMapping(gettext_nl("description"), "%descr");
				$view->addMapping(gettext_nl("date"), "%datum_h");
				$view->addMapping(gettext_nl("amount"), "%bedrag_h", array(
					"allow_html" => 1
				));
				$view->addMapping(gettext_nl("betaald"), "%%complex_betaald");

				$view->defineComplexMapping("complex_betaald", array(
					array(
						"type"  => "action",
						"src"   => "ok",
						"link"  => "",
						"alt"   => gettext_nl("is betaald"),
						"check" => "%betaald_1"
					),
					array(
						"type"  => "action",
						"src"   => "delete",
						"link"  => "",
						"alt"   => gettext_nl("niet betaald"),
						"check" => "%betaald_0"
					)
				));

				$venster->addCode($view->generate_output());
				$venster->endVensterData();

				$output->insertTag("div", $venster->generate_output());
			}
		}
		*/
		if ($GLOBALS["covide"]->license["has_factuur"]) {
			/* verkopen */
			if (count($finance_info["verkopen"]["data"]) > -1) {
				$venster = new Layout_venster(array(
					"title"   => gettext("facturen"),
					"subtitle" => gettext_nl("verkopen dit jaar")
				));
				$venster->addVensterData();

				$view = new Layout_view();
				$view->addData($finance_info["verkopen"]["data"]);
				$view->addMapping(gettext_nl("factuur"), "%%complex_factuur_nr");
				$view->addMapping(gettext_nl("omschrijving"), "%omschrijving");
				$view->addMapping(gettext_nl("date"), "%datum_h");
				$view->addMapping(gettext_nl("amount"), "%bedrag_h", array(
					"allow_html" => 1
				));
				$view->addMapping(gettext_nl("betaald"), "%%complex_betaald");

				$view->defineComplexMapping("complex_factuur_nr", array(
					array(
						"type" => "link",
						"link" => array("classes/finance/non-oop/factuur/projecten.php?zoeken=", "%factuur_nr"),
						"text" => "%factuur_nr"
					)
				));
				$view->defineComplexMapping("complex_betaald", array(
					array(
						"type"  => "action",
						"src"   => "ok",
						"link"  => array("javascript: financePaymentDone('", "%id", "');"),
						"alt"   => gettext_nl("is betaald"),
						"check" => "%betaald_1"
					),
					array(
						"type"  => "action",
						"src"   => "reload",
						"link"  => array("javascript: financePaymentDone('", "%id", "');"),
						"alt"   => gettext_nl("niet betaald"),
						"check" => "%betaald_0"
					)
				));

				if (count($finance_info["verkopen"]["data"]) > 25) {
					$venster->insertTag("div", $view->generate_output(), array(
						"style" => "overflow-y: auto; height: 500px;"
					));
				} else {
					$venster->addCode($view->generate_output());
				}


				if (!$GLOBALS["covide"]->license["has_finance"]) {
					$cf = "var cf = confirm(gettext('Are you sure you want to change the payment status?'));";
					$uri = sprintf("?mod=address&action=relcard&funambol_user=%d&id=%d&finance_history=%d&finance_toggle=",
						$_REQUEST["funambol_user"], $_REQUEST["id"], $_REQUEST["finance_history"]);
				} else {
					$cf = "var cf = true;";
					$uri = "non-oop/finance/bankboek.php?action=detail&type=verkoop&id=";
				}
				$venster->start_javascript();
				$venster->addCode(sprintf("
					function financePaymentDone(id) {
						%s
						if (cf == true) {
							location.href = '%s' + id;
						}
					}
				", $cf, $uri));
				$venster->end_javascript();

				$venster->insertAction("go_sales", "", "");
				$venster->addSpace();
				$venster->insertTag("a", gettext("go to invoice / finance module"), array(
					"href" => "classes/finance/non-oop/factuur/projecten.php?klanten=".$_REQUEST["id"]
				));
				$venster->addSpace(2);
				$venster->insertAction("performance_total", "", "");
				$venster->addSpace();
				if (!$_REQUEST["finance_history"]) {
					$venster->insertTag("a", gettext("show older invoices"), array(
						"href" => sprintf("?mod=address&action=relcard&id=%d&funambol_user=%d&finance_history=1",
							$_REQUEST["id"], $_REQUEST["funambol_user"])
					));
				} else {
					$venster->insertTag("a", gettext("show current invoices"), array(
						"href" => sprintf("?mod=address&action=relcard&id=%d&funambol_user=%d&finance_history=0",
							$_REQUEST["id"], $_REQUEST["funambol_user"])
					));
				}
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->insertAction("new", gettext_nl("nieuwe factuur"), "javascript: popup('classes/finance/non-oop/factuur/projecten.php?pagina=nieuw&statusnieuw=2&address_id_new=".$_REQUEST["id"]."');");
				$venster->addSpace();
				$venster->insertTag("a", gettext_nl("nieuwe factuur"), array(
					"href" => "javascript: popup('classes/finance/non-oop/factuur/projecten.php?pagina=nieuw&statusnieuw=2&address_id_new=".$_REQUEST["id"]."');"
				));
				$venster->endVensterData();
			}

			if ($venster)
				$output->insertTag("div", $venster->generate_output());

		}
		return $output->generate_output();
	}
}
?>
