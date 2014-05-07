<?
	if (!class_exists("Address_output")) {
		die("no class definition found");
	}
	/* we can have a serialized request var when we start print from address overview */
	if ($_REQUEST["info"]) {
		$printinfo = unserialize(stripslashes($_REQUEST["info"]));
	}

	if (!$_REQUEST["what_to_export"] && !is_array($printinfo)) {
		$output = new Layout_output();
		$output->layout_page("", 1);
			$venster = new Layout_venster(array(
				"title" => gettext("adressen"),
				"subtitle" => gettext("export")
			));
			$venster->addVensterData();
				$venster->addCode(gettext("wat wilt u exporteren")."?");
				$venster->addTag("br");
				$venster->insertLink(gettext("relaties"), array("href" => "index.php?mod=address&action=export&what_to_export=rel"));
				$venster->addTag("br");
				$venster->insertLink(gettext("businesscards"), array("href" => "index.php?mod=address&action=export&what_to_export=bcards"));
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
			$table = new Layout_table(array("border" => 1, "cellspacing" => 2, "cellpadding" => 2));
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
				$table->addTableData(array("colspan"=>3), "header");
					$table->addCode(gettext("classificaties").":");
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("soort").":", "", "bold top");
				if ($options["addresstype"]=="bcards") {
					$table->insertTableData(gettext("business cards"), array("colspan" => 2));
				} else {
					$table->insertTableData(gettext("relaties"), array("colspan" => 2));
				}
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("selectie").":", "", "bold top");
				if ($options["selection_type"]=="and") {
					$table->insertTableData(gettext("unieke classificaties (en)"), array("colspan" => 2));
				} else {
					$table->insertTableData(gettext("opgetelde classificaties (of)"), array("colspan" => 2));
				}
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("positief").":", "", "bold top");
				$table->addTableData(array("colspan" => 2));
					$table->addCode( $classification->classification_selection("null", $options["classifications"]["positive"], "enabled", 1) );
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("negatief").":", "", "bold top");
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
				$address_record = $address_data->getAddressById($row["id"]);
				if ($address_record["id"] && strlen(trim($address_record["companyname"]))) {
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
			$output->addCode($table->generate_output());
			unset($table);
			$output->start_javascript();
				$output->addCode("this.print();");
				$output->addCode("window.close();");
			$output->end_javascript();
			$output->exit_buffer();
			// }}}
		}
	}
?>
