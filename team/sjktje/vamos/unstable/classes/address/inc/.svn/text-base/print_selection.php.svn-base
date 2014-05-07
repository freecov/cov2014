<?
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
	/* we can have a serialized request var when we start print from address overview */

	if ($_REQUEST["info"]) {
		$address_data = new Address_data();
		$printinfo = $address_data->getExportInfo($_REQUEST["info"]);
	}

	if (!$_REQUEST["what_to_export"] && !is_array($printinfo)) {
		$output = new Layout_output();
		$output->layout_page("", 1);
			$venster = new Layout_venster(array(
				"title" => gettext("addresses"),
				"subtitle" => gettext("export")
			));
			$venster->addVensterData();
				$venster->addCode(gettext("What do you want to export")."?");
				$venster->addTag("br");
				$venster->insertLink(gettext("contacts"), array("href" => "index.php?mod=address&action=export&what_to_export=rel"));
				$venster->addTag("br");
				$venster->insertLink(gettext("business cards"), array("href" => "index.php?mod=address&action=export&what_to_export=bcards"));
			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	} else {
		$address_data = new Address_data();
		/*
		session_cache_limiter('private, must-revalidate');
		session_start();
		header('Content-Transfer-Encoding: binary');
		header('Content-Type: text/plain');

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename=adreslijst.csv'); //msie 5.5 header bug
		}else{
			header('Content-Disposition: attachment; filename=adreslijst.csv');
		}
		*/
		$commencements = $address_data->getCommencements();
		$titles = $address_data->getTitles();
		$letterheads = $address_data->getLetterheads();

		if ($_REQUEST["what_to_export"] == "bcards" || $exportinfo["addresstype"] == "bcards") {
			//{{{ bcards
			$data = "\"id\",\"title\",\"commencement\",\"letterhead\",\"tav\",\"contactperson\",\"companyname\",\"initials\",\"givenname\",\"infix\",\"surname\",\"phonenumber\",\"faxnumber\",\"mobilenumber\",\"email\",\"address\",\"zipcode\",\"city\",\"country\",\"memo\",\"businessunit\",\"department\",\"locationcode\"\n";
			if (is_array($exportinfo)) {
				$options = $exportinfo;
			} else {
				$options = array(
					"addresstype" => "bcards",
					"top"         => "0"
				);
			}
			$bcards = $address_data->getRelationsList($options);
			$res = sql_query($bcards["query_csv"]);
			while ($row = sql_fetch_assoc($res)) {
				if ($row["givenname"] || $row["surname"]) {
					/* include this record in the export */
					if (!(trim($row["business_address"]) || trim($row["personal_address"]))) {
						/* no address info attached, get addressinfo from company record */
						$addressinfo = $address_data->getAddressById($row["address_id"]);
						$row["address"] = $addressinfo["address"];
						$row["address2"] = $addressinfo["address2"];
						$row["zipcode"] = $addressinfo["zipcode"];
						$row["city"] = $addressinfo["city"];
						$row["country"] = $addressinfo["country"];
						$row["phone_nr"] = $addressinfo["phone_nr"];
						$row["mobile_nr"] = $addressinfo["mobile_nr"];
						$row["fax_nr"] = $addressinfo["fax_nr"];
						$row["email"] = $addressinfo["email"];
					} else {
						if (trim($row["business_address"])) {
							$row["address"] = $row["business_address"];
							$row["address2"] = "";
							$row["zipcode"] = $row["business_zipcode"];
							$row["city"] = $row["business_city"];
							$row["country"] = $row["business_country"];
						} else {
							/* put personal address there */
							$row["address"] = $row["personal_address"];
							$row["address2"] = $row["personal_address2"];
							$row["zipcode"] = $row["personal_zipcode"];
							$row["city"] = $row["personal_city"];
							$row["country"] = $row["personal_country"];
						}
					}
					if ($row["business_phone_nr"]) {
						$row["phone_nr"] = $row["business_phone_nr"];
					} elseif ($row["personal_phone_nr"]) {
						$row["phone_nr"] = $row["personal_phone_nr"];
					}
					if ($row["business_fax_nr"]) {
						$row["fax_nr"] = $row["business_fax_nr"];
					} elseif($row["personal_fax_nr"]) {
						$row["fax_nr"] = $row["personal_fax_nr"];
					}
					if ($row["business_mobile_nr"]) {
						$row["mobile_nr"] = $row["business_mobile_nr"];
					} elseif($row["personal_mobile_nr"]) {
						$row["mobile_nr"] = $row["personal_mobile_nr"];
					}
					if ($row["business_email"]) {
						$row["email"] = $row["business_email"];
					} elseif($row["personal_email"]) {
						$row["email"] = $row["personal_email"];
					}
					$row["letterinfo"] = $address_data->generate_letterinfo(array(
						"contact_letterhead" => $row["letterhead"],
						"contact_commencement" => $row["commencement"],
						"contact_givenname" => $row["givenname"],
						"contact_infix" => $row["infix"],
						"contact_surname" => $row["surname"],
						"title" => $row["title"]
					));
					$row["tav"] = $row["letterinfo"]["tav"];
					$row["contact_person"] = $row["letterinfo"]["contact_person"];
					$data .= $row["id"].",\"".$titles[$row["title"]]."\",\"".$commencements[$row["commencement"]]."\"";
					$data .= ",\"".$letterheads[$row["letterhead"]]."\",\"".$row["tav"]."\",\"".$row["contact_person"]."\"";
					$data .= ",\"".$address_data->getAddressNameById($row["address_id"])."\",\"".trim($row["initials"])."\",\"".trim($row["givenname"])."\"";
					$data .= ",\"".trim($row["infix"])."\",\"".trim($row["surname"])."\",\"".$row["phone_nr"]."\",\"".$row["fax_nr"]."\"";
					$data .= ",\"".$row["mobile_nr"]."\",\"".$row["email"]."\",\"".$row["address"]." ".$row["address2"]."\"";
					$data .= ",\"".$row["zipcode"]."\",\"".$row["city"]."\",\"".$row["country"]."\",\"".preg_replace("\"\r\n\"", " ", $row["memo"])."\"";
					$data .= "'\"".$row["businessunit"]."\",\"".$row["department"]."\",\"".$row["locationcode"]."\"";
					$data .= "\n";
				}
			}
			echo $data;
			//}}}
		} else {
			$output = new Layout_output();
			$output->layout_page(gettext("print"), 1);
			$settings = array(
				"title"    => gettext("Address")." ".gettext("information"),
				"subtitle" => gettext($printinfo["addresstype"])
			);

			$venster = new Layout_venster($settings);
			unset($settings);
			$venster->addVensterData();
			$table = new Layout_table(array("cellspacing" => 2, "cellpadding" => 2));
			
			//{{{ relations
			if (is_array($printinfo)) {
				$options = $printinfo;
			} else {
				$options = array(
					"addresstype" => "relations",
					"top"         => "0"
				);
			}
			$classification = new Classification_output();

			$table->addTableRow();
				$table->addTableData(array("colspan"=>3, "width" => "100%"), "header");
					$table->addCode(gettext("classifications").":");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("type").":", "", "bold top");
				if ($options["addresstype"]=="bcards") {
					$table->insertTableData(gettext("business cards"), array("colspan" => 2));
				} else {
					$table->insertTableData(gettext("contacts"), array("colspan" => 2));
				}
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("selection").":", "", "bold top");
				if ($options["selection_type"]=="and") {
					$table->insertTableData(gettext("unique classifications (AND)"), array("colspan" => 2));
				} else {
					$table->insertTableData(gettext("added classifications (OR)"), array("colspan" => 2));
				}
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("positive").":", "", "bold top");
				$table->addTableData(array("colspan" => 2));
					$table->addCode( $classification->classification_selection("null", $options["classifications"]["positive"], "enabled", 1) );
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("negative").":", "", "bold top");
				$table->addTableData(array("colspan" => 2));
					$table->addCode( $classification->classification_selection("null", $options["classifications"]["negative"], "disabled", 1) );
				$table->endTableData();
			$table->endTableRow();

			$relations = $address_data->getRelationsList($options);
			if ($options["addresstype"] == "relations") {
				$relations["query_csv"].=" ORDER BY address.companyname";
			}
			$res = sql_query($relations["query_csv"]);
			while ($row = sql_fetch_assoc($res)) {
				$address_record = $address_data->getAddressById($row["id"], $options["addresstype"]);
				if ($address_record["id"] && strlen(trim($address_record["companyname"]))) {
					if ($options["addresstype"] == "bcards") {
						/* prepare the data because bcards are different */
						$address_record["phone_nr"] = $address_record["business_phone_nr"];
						$address_record["tavdata"]  = $address_data->generate_letterinfo(array(
							"title" => $address_record["title"],
							"contact_initials"     => $address_record["initials"],
							"contact_letterhead"   => $address_record["letterhead"],
							"contact_commencement" => $address_record["commencement"],
							"contact_infix"        => $address_record["infix"],
							"contact_surname"      => $address_record["surname"],
							"contact_givenname"    => $address_record["givenname"]
						));
						$address_record["tav"]      = $address_record["tavdata"]["tav"];
						unset($address_record["tavdata"]);
						$address_record["companyname"] = $address_record["companyname"].", ".$address_record["fullname"];
					}
					if (strlen(trim($address_record["tav"])) == 0)      { $address_record["tav"] = "&nbsp;"; }
					if (strlen(trim($address_record["phone_nr"])) == 0) { $address_record["phone_nr"] = "&nbsp;"; }
					$table->addTableRow();
						$table->addTableData();
							$table->addCode($address_record["companyname"]);
						$table->endTableData();
						$table->addTableData();
							$table->addCode($address_record["tav"]);
						$table->endTableData();
						$table->addTableData();
							$table->addCode($address_record["phone_nr"]);
						$table->endTableData();
					$table->endTableRow();
				}
			}
				$table->endTable();
				$venster->addCode($table->generate_output());
				unset($table);

				$venster->endVensterData();
				$output->addCode($venster->generate_output());
				unset($venster);

			$output->start_javascript();
				$output->addCode("this.print();");
				$output->addCode("window.close();");
			$output->end_javascript();
			$output->exit_buffer();
			// }}}
		}
	}
?>
