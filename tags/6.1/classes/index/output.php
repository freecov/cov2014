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
Class Index_output {

	/* constants */
	const include_dir =  "classes/index/inc/";

	/* variables */
	private $limit  = 100;

	/* methods */
	public function show_index() {
		$output = new Layout_output();
		$output->layout_page(gettext("index")." ".gettext("zoeken"));

		$output->addTag("form", array(
			"id"     => "fld",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "index");

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("index"),
			"subtitle" => gettext("zoeken")
		));
		/* menu items */
		$venster->addVensterData();

		if (!$_REQUEST["search"]["phrase"]) {
			$search["and"] = 1;
			$search["private"] = (int)$_REQUEST["search"]["private"];
		} else {
			$search["and"] = $_REQUEST["search"]["and"];
			$search["private"] = $_REQUEST["search"]["private"];
		}

		$div = new Layout_output();
		$div->addTag("img", array(
			"src" => "img/bar.png"
		));

		$output->start_javascript();
			$output->addCode("
				function search() {
					document.getElementById('marquee_progressbar').style.visibility = 'visible';
					document.getElementById('searchbutton').style.visibility = 'hidden';
					setTimeout(\"document.getElementById('fld').submit();\", 200);
				}
			");
		$output->end_javascript();

		$tbl = new layout_table();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addCode( gettext("zoeken naar items binnen Covide").": " );
				$tbl->addTextField("search[phrase]", $_REQUEST["search"]["phrase"], array("style"=>"width: 300px;"), "", 1);
				$tbl->addTag("div", array("id"=>"searchbutton", "style"=>"display: inline;"));
					$tbl->insertAction("forward", gettext("zoeken"), "javascript:search();");
				$tbl->endTag("div");
				$tbl->addTag("br");
				$tbl->start_javascript();
					$tbl->addCode("
						document.getElementById('searchphrase').focus();
					");
				$tbl->end_javascript();
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->insertTag("marquee", $div->generate_output(), array(
					"id"           => "marquee_progressbar",
					"behavoir"     => "scroll",
					"style"        => "width: 300px; background-color: #f1f1f1; height: 10px; border: 1px solid #b0b2c6; visibility:hidden; margin-top: 10px;",
					"scrollamount" => 3,
					"direction"    => "right",
					"scrolldelay"  => 60
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addTag("br");
				$tbl->insertTag("b", gettext("zoekgedrag bij meerdere zoekwoorden").": ");
				$tbl->addTag("br");
				$tbl->addRadioField("search[and]", gettext("zoeken naar alle zoekwoorden (en)"), 1, $search["and"]);
				$tbl->addRadioField("search[and]", gettext("zoeken naar een van de zoekwoorden (of)"), 0, $search["and"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->addTag("br");
				$tbl->insertTag("b", gettext("zoeken in de volgende soort items").": ");
				$tbl->addTag("br");
				$tbl->addRadioField("search[private]", gettext("zoeken in mijn eigen prive items"), 1, $search["private"]);
				$tbl->addRadioField("search[private]", gettext("zoeken in prive en publieke items"), 0, $search["private"]);
				$tbl->addTag("br");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData();
				$tbl->insertTag("b", gettext("zoeken in de volgende modules").": ");
				/* modules */
				$mods = array(
					"address"   => gettext("adresboek"),
					"calendar"  => gettext("agenda"),
					//"todos"     => gettext("todo's"),
					"notes"     => gettext("notities")." / ".gettext("klantcontacten"),
					"email"     => gettext("email")." (".gettext("berichten").")",
					"binemail"  => gettext("email")." (".gettext("in attachments").")*",
					"filesys"   => gettext("bestandsbeheer")." (".gettext("globaal").")*",
					"binfile"   => gettext("bestandsbeheer")." (".gettext("in de bestanden").")*"
				);
				$table = new Layout_table();
				foreach ($mods as $k=>$v) {
					$table->addTableRow();
						$table->addTableData();
							$table->addCheckBox("search[$k]", 1, $_REQUEST["search"][$k]);
						$table->endTableData();
						$table->addTableData();
							$table->addCode($v);
						$table->endTableData();
					$table->endTableRow();
				}
				$table->endTable();
				$table->addTag("br");
				$table->addCode(" * = ". gettext("alleen in publieke modus mogelijk"));
				$tbl->addCode( $table->generate_output() );

			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode( $tbl->generate_output() );
		$venster->endVensterData();

		$output->addCode( $venster->generate_output() );
		unset($venster);

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("resultaten"),
			"subtitle" => gettext("gevonden resultaten")
		));
		/* menu items */
		$venster->addVensterData();

			$index_data = new Index_data();
			$data = $index_data->execSearch($_REQUEST);

			$address_fields = array(
				"private" => gettext("prive adressen"),
				"address" => gettext("relaties"),
				"bcards"  => gettext("business cards"),
				//"other"   => gettext("overige adressen"),
				"users"   => gettext("medewerkers")
			);
			/* addressbook */
			foreach ($address_fields as $k=>$v) {
				if ($data["address"][$k]["total_count"] > 0) {
					$view = new Layout_view();
					//$view->addData($data["address"][$k]["address"]);
					switch ($k) {
						case "address":
							$view->defineComplexMapping("complex_name", array(
								array(
									"type" => "link",
									"text" => array("%companyname"),
									"link" => array("javascript: popup('?mod=address&action=relcard&id=", "%id", "');")
								)
							));
							break;
						case "bcards":
							$view->defineComplexMapping("complex_name", array(
								array(
									"type" => "link",
									"text" => array("%fullname"),
									"link" => array("javascript: popup('?mod=address&action=cardshow&cardid=", "%id", "');")
								)
							));
							$view->defineComplexMapping("complex_companyname", array(
								array(
									"type" => "link",
									"text" => array("%companyname"),
									"link" => array("javascript: popup('?mod=address&action=relcard&id=", "%address_id", "');")
								)
							));
							break;
						case "private":
							$view->defineComplexMapping("complex_name", array(
								array(
									"type" => "link",
									"text" => array("%fullname"),
									"link" => array("javascript: popup('?mod=address&action=cardshow&cardid=", "%id", "');")
								)
							));
							break;
						case "users":
							$view->defineComplexMapping("complex_name", array(
								array(
									"type" => "link",
									"text" => array("%fullname"),
									"link" => array("javascript: popup('?mod=address&action=usercard&id=", "%id", "');")
								)
							));
							break;
					}

					$view->addMapping(gettext("naam"), "%%complex_name");
					if ($k == "bcards") {
						$view->addMapping(gettext("bedrijfsnaam"), "%%complex_companyname");
					} else {
						$view->addMapping(gettext("adres"), "%address");
						$view->addMapping(gettext("plaats"), "%city");
					}
					if ($k != "users" && $k != "bcards") {
						$view->addMapping(gettext("contactpersoon"), "%tav");
					}

					$venster->insertTag("b", gettext("adresboek")." - ".$v);
					$this->limit_view($data["address"][$k]["total_count"], $view, $venster, $data["address"][$k]["address"], "address_".$k);
					$venster->addTag("br");
				}
			}

			/* calendar */
			if (count($data["calendar"]) > 0) {
				$venster->insertTag("b", gettext("agenda items"));
				$view = new Layout_view();
				//$view->addData($data["calendar"]);
				$view->addMapping(gettext("datum start"), "%human_start");
				$view->addMapping(gettext("datum eind"), "%human_end");
				$view->addMapping(gettext("onderwerp"), "%%complex_subject");
				$view->addMapping(gettext("gebruiker"), "%user_name");

				$view->defineComplexMapping("complex_subject", array(
					array(
						"type" => "link",
						"link" => array("javascript: popup('index.php?mod=calendar&day=", "%day", "&month=", "%month", "&year=", "%year", "&extrauser=", "%user_id", "');"),
						"text" => "%subject"
					)
				));

				$this->limit_view(count($data["calendar"]), $view, $venster, $data["calendar"], "calendar");
				$venster->addTag("br");
			}

			/* notes */
			if (count($data["notes"]) > 0) {
				$venster->insertTag("b", gettext("notities en klantcontacten"));
				$view = new Layout_view();
				//$view->addData($data["notes"]);

				/* add the mappings so we actually have something */
				$view->addMapping(gettext("onderwerp"), "%%complex_subject");
				$view->addMapping(gettext("afzender"), "%from_name");
				$view->addMapping(gettext("ontvanger"), "%to_name");
				if ($GLOBALS["covide"]->license["project"]) {
					$view->addMapping(gettext("project"), "");
				}
				$view->addMapping(gettext("relatie"), "%%relation_name");
				$view->addMapping(gettext("datum"), "%human_date");
				$view->addMapping(gettext("klantcontact"), "%%complex_contactitem");

				/* define the mappings */
				/* subject is link to complete note */
				$view->defineComplexMapping("complex_subject", array(
					array(
						"type" => "link",
						"link" => array("javascript: popup('index.php?mod=note&action=message&msg_id=", "%id", "');"),
						"text" => "%subject"
					)
				));
				/* contactitem is image that displays wether this is a contactmoment */
				$view->defineComplexMapping("complex_contactitem", array(
					array(
						"type" => "action",
						"src"  => "state_public",
						"check" => "%is_support"
					)
				));
				$view->defineComplexMapping("relation_name", array(
					array(
						"type" => "link",
						"link" => array("javascript: popup('index.php?mod=address&action=relcard&id=", "%address_id", "');"),
						"text" => "%relation_name"
					)
				));
				/* put the table in the $venster data buffer and destroy object */
				$this->limit_view(count($data["notes"]), $view, $venster, $data["notes"], "notes");
				$venster->addTag("br");
			}

			/* todo */

			/* email messages */
			$email_fields = array(
				"private" => "prive mappen",
				"archive" => "archief"
			);
			foreach ($email_fields as $k=>$v) {
				if ($data["email"][$k]["count"] > 0) {
					$venster->insertTag("b", gettext("email")." - ".$v);
					$view = new Layout_view();
					//$view->addData($data["email"][$k]["data"]);
					$view->addMapping(gettext("onderwerp"), "%%complex_subject");
					$view->addMapping(gettext("datum"), array("%short_date", " ", "%short_time"));
					$view->addMapping(gettext("afzender"), "%sender_emailaddress_h");
					$view->addMapping(gettext("map"), "%%complex_folder");

					$view->defineComplexMapping("complex_subject", array(
						array(
							"type" => "action",
							"src"  => "ftype_html"
						),
						array(
							"type" => "link",
							"link" => array("javascript: popup('index.php?mod=email&action=open&id=", "%id", "');"),
							"text" => "%subject"
						)
					));
					$view->defineComplexMapping("complex_folder", array(
						array(
							"type" => "action",
							"src"  => "view"
						),
						array(
							"type" => "link",
							"link" => array("javascript: popup('index.php?mod=email&action=list&address_id=", "%address_id", "&folder_id=", "%folder_id", "');"),
							"text" => "%folder_name"
						)
					));

					$this->limit_view($data["email"][$k]["count"], $view, $venster, $data["email"][$k]["data"], "email_".$k);
					$venster->addTag("br");
				}
			}

			/* email attachments */
			if (count($data["binemail"]) > 0) {
				$venster->insertTag("b", gettext("email attachments"));
				$view = new Layout_view();
				//$view->addData($data["binemail"]);
				$view->addMapping(gettext("naam"), "%%complex_name");
				$view->addMapping(gettext("email"), "%%complex_subject");
				$view->addMapping(gettext("datum"), array("%date"));
				$view->addMapping(gettext("map"), "%%complex_folder");

				$view->defineComplexMapping("complex_name", array(
					array(
						"type" => "action",
						"src"  => "%icon"
					),
					array(
						"type" => "link",
						"link" => array("?dl=1&mod=email&action=download_attachment&id=", "%attid"),
						"text" => "%name"
					)
				));
				$view->defineComplexMapping("complex_subject", array(
					array(
						"type" => "action",
						"src"  => "ftype_html"
					),
					array(
						"type" => "link",
						"link" => array("javascript: popup('index.php?mod=email&action=open&id=", "%mailid", "');"),
						"text" => "%subject"
					)
				));
				$view->defineComplexMapping("complex_folder", array(
					array(
						"type" => "action",
						"src"  => "view"
					),
					array(
						"type" => "link",
						"link" => array("javascript: popup('index.php?mod=email&action=list&address_id=", "%address_id", "&folder_id=", "%folderid", "');"),
						"text" => "%folder"
					)
				));
				$venster->insertTag("b", gettext("email")." - "."in de attachments");
				$this->limit_view(count($data["binemail"]) > 0, $view, $venster, $data["binemail"], "binemail");
				$venster->addTag("br");
			}

			/* filesys folders */
			/* create view for folders */
			if (count($data["filesys"]["folders"])>0) {
				$view = new Layout_view();
				//$view->addData($data["filesys"]["folders"]);
				$view->addMapping(gettext("mapnaam"), "%%complex_name");
				$view->addMapping(gettext("omschrijving"), "%description");

				$view->defineComplexMapping("complex_name", array(
					array(
						"type"  => "action",
						"src"   => "folder_closed"
					),
					array(
						"type"  => "link",
						"text"  => array(" ", "%name"),
						"link"  => array("?mod=filesys&action=opendir&id=", "%id")
					)
				));
				$venster->insertTag("b", gettext("bestandsbeheer")." - "."mappen");
				$this->limit_view(count($data["filesys"]["folders"]), $view, $venster, $data["filesys"]["folders"], "folders");
				$venster->addTag("br");
			}

			/* create view for files */
			if (count($data["filesys"]["files"])>0) {
				$view = new Layout_view();
				//$view->addData($data["filesys"]["files"]);

				$view->addMapping(gettext("bestandsnaam"), "%%complex_name");
				$view->addMapping(gettext("map"), "%%complex_folder");
				$view->addMapping(gettext("omschrijving"), "%description");
				$view->addMapping(gettext("datum"), "%date_human");
				$view->addMapping(gettext("gebruiker"), "%user_name");

				$view->defineComplexMapping("complex_folder", array(
					array(
						"type"  => "action",
						"src"   => "folder_global"
					),
					array(
					"type"  => "link",
					"text"  => array(" ", "%folder_name"),
					"link"  => array("javascript: popup('?mod=filesys&action=opendir&id=", "%folder_id", "');")
					)
				));


				$view->defineComplexMapping("complex_name", array(
					array(
						"type"  => "action",
						"src"   => "%fileicon"
					),
					array(
						"type"  => "link",
						"link"  => array("?dl=1&mod=filesys&action=fdownload&id=", "%id"),
						"text"  => array(" ", "%name")
					)
				));
				$venster->insertTag("b", gettext("bestandsbeheer")." - "."bestanden");
				$this->limit_view(count($data["filesys"]["files"]), $view, $venster, $data["filesys"]["files"], "files");
				$venster->addTag("br");
			}

			if (count($data["binfiles"])>0) {
				$view = new Layout_view();
				//$view->addData($data["binfiles"]);
				$view->addMapping(gettext("bestandsnaam"), "%%complex_name");
				$view->addMapping(gettext("map"), "%%complex_folder");
				$view->addMapping(gettext("omschrijving"), "%description");
				$view->addMapping(gettext("datum"), "%date_human");
				$view->addMapping(gettext("gebruiker"), "%user_name");

				$view->defineComplexMapping("complex_folder", array(
					array(
						"type"  => "action",
						"src"   => "%foldericon"
					),
					array(
						"type"  => "link",
						"link"  => array("javascript: popup('?mod=filesys&action=opendir&id=", "%id", "');"),
						"text"  => array(" ", "%name")
					)
				));
				$view->defineComplexMapping("complex_name", array(
					array(
						"type"  => "action",
						"src"   => "%file_icon"
					),
					array(
						"type"  => "link",
						"link"  => array("?dl=1&mod=filesys&action=fdownload&id=", "%file_id"),
						"text"  => array(" ", "%file_name")
					)
				));
				$venster->insertTag("b", gettext("bestandsbeheer")." - "."in de bestanden");
				$this->limit_view(count($data["binfiles"]), $view, $venster, $data["binfiles"], "binfiles");
				$venster->addTag("br");
			}

		$venster->addSpace();
		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		unset($venster);

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();

	}

	public function limit_view($count, &$view, &$venster, &$data, $module) {

		if (is_array($data)) {
			if (count($data) > $this->limit && $module != $_REQUEST["more"]) {
				$t = array_chunk($data, $this->limit);
				$data =& $t[0];
				$limit = 1;
			}
			$view->addData($data);
		}
		if ($count > (int)($this->limit/3) ) {
			$venster->insertTag("div", $view->generate_output(), array(
				"class" => "index_limit"
			));
		} else {
			$venster->addCode( $view->generate_output() );
		}

		if ($limit) {
			$url = "?mod=index";

			$p = $_REQUEST["search"];
			$url = "index.php?mod=index";
			foreach ($p as $k=>$v) {
				$url.= "&search[$k]=$v";
			}
			$url.="&more=".$module;
			$venster->insertAction("addressbook", gettext("meer resultaten"), $url);
			$venster->addSpace();
			$venster->insertTag("a", gettext("meer resultaten"), array(
				"href" => $url
			));
			$venster->addTag("br");
		}

	}
}
?>
