<?
	if (!class_exists("Address_output")) {
		die("no class definition found");
	}
	/* only address managers are allowed to export globally */
	$userdata = new User_data();
	$userperms = $userdata->getUserPermissionsById($_SESSION["user_id"]);
	if (!$userperms["xs_addressmanage"]) {
		die("no access");
	}
	/* we can have a serialized request var when we start export from address overview */
	if ($_REQUEST["info"]) {
		$exportinfo = unserialize(stripslashes($_REQUEST["info"]));
	}

	if (!$_REQUEST["what_to_export"] && !is_array($exportinfo)) {
		$output = new Layout_output();
		$output->layout_page("", 1);
			$venster = new Layout_venster(array(
				"title" => gettext("adressen"),
				"subtitle" => gettext("export")
			));
			$venster->addVensterData();
				$venster->addCode(gettext("wat wilt u exporteren")."?");
				$venster->addTag("br");
				$venster->insertLink(gettext("relaties"), array("href" => "index.php?mod=address&action=export&what_to_export=rel&dl=1"));
				$venster->addTag("br");
				$venster->insertLink(gettext("businesscards"), array("href" => "index.php?mod=address&action=export&what_to_export=bcards&dl=1"));
			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	} else {
		$address_data = new Address_data();

		#session_cache_limiter('private, must-revalidate');
		#session_start();
		header("Content-Transfer-Encoding: binary");
		header("Content-Type: text/plain");

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header("Content-Disposition: filename=adreslijst.csv"); //msie 5.5 header bug
		}else{
			header("Content-Disposition: attachment; filename=adreslijst.csv");
		}
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
			//{{{ relations
			$data = "\"id\",\"title\",\"commencement\",\"letterhead\",\"tav\",\"contactperson\",\"companyname\",\"initials\",\"givenname\",\"infix\",\"surname\",\"phonenumber\",\"faxnumber\",\"mobilenumber\",\"email\",\"address\",\"zipcode\",\"city\",\"country\",\"memo\"\n";
			if (is_array($exportinfo)) {
				$options = $exportinfo;
			} else {
				$options = array(
					"addresstype" => "relations",
					"top"         => "0"
				);
			}
			$relations = $address_data->getRelationsList($options);

			if ($options["addresstype"] == "relations") {
				$relations["query_csv"].=" ORDER BY address.companyname";
			}

			$res = sql_query($relations["query_csv"]);
			while ($row = sql_fetch_assoc($res)) {
				$address_record = $address_data->getAddressById($row["id"]);
				if ($address_record["id"]) {
					$data .= $address_record["id"].",\"".$titles[$address_record["title"]]."\",\"".$commencements[$address_record["contact_commencement"]]."\"";
					$data .= ",\"".$letterheads[$address_record["contact_letterhead"]]."\",\"".$address_record["tav"]."\",\"".$address_record["contact_person"]."\"";
					$data .= ",\"".$address_record["companyname"]."\",\"".$address_record["contact_initials"]."\",\"".$address_record["contact_givenname"]."\"";
					$data .= ",\"".$address_record["contact_infix"]."\",\"".$address_record["contact_surname"]."\",\"".$address_record["phone_nr"]."\",\"".$address_record["fax_nr"]."\"";
					$data .= ",\"".$address_record["mobile_nr"]."\",\"".$address_record["email"]."\",\"".$address_record["address"]." ".$address_record["address2"]."\"";
					$data .= ",\"".$address_record["zipcode"]."\",\"".$address_record["city"]."\",\"".$address_record["country"]."\",\"".str_replace("\r\n", " ", $address_record["memo"])."\"";
					$data .= "\n";
				}
			}
			echo $data;

			// }}}
		}
	}
?>
