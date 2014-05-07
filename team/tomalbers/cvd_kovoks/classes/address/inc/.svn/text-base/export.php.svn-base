<?
	if (!class_exists("Address_output")) {
		die("no class definition found");
	}

	set_time_limit(60*60); //1 hour time limit

	/* only address managers are allowed to export globally */
	$userdata = new User_data();
	$userperms = $userdata->getUserPermissionsById($_SESSION["user_id"]);
	if (!$userperms["xs_addressmanage"]) {
		die("no access");
	}
	/* we can have a serialized request var when we start export from address overview */
	if ($_REQUEST["info"]) {
		$address_data = new Address_data();
		$exportinfo = $address_data->getExportInfo($_REQUEST["info"]);
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
				//$venster->insertLink(gettext("relaties"), array("href" => "index.php?mod=address&action=export&what_to_export=rel&dl=1"));
				//$venster->addTag("br");
				//$venster->insertLink(gettext("businesscards"), array("href" => "index.php?mod=address&action=export&what_to_export=bcards&dl=1"));
				$venster->addTag("form", array(
					"action" => "index.php",
					"id"     => "exportfrm"
				));
				$venster->addHiddenField("mod", "address");
				$venster->addHiddenField("action", "export");
				$sel = array(
					"rel" => gettext("alle relatie kaarten"),
					"bcards" => gettext("alle business cards")
				);
				$venster->addSelectField("what_to_export", $sel, "rel");
				$venster->addHiddenField("dl", "1");

				/*
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addCode(gettext("Als welke karakterset wilt u exporteren")."?");
				$sel = array();
				$sel["Windows-1252"] = "Windows Systemen (standaard)";
				$sel["UTF-8"] = "Crossplatform - unicode (UTF-8)";
				$sel["ISO-8859-15"] = "Crossplatform - platte tekst (Latin-1)";
				$venster->addTag("br");
				$venster->addSelectField("encoding", $sel, "rel");
				*/

				$venster->addTag("br");
				$venster->addTag("br");
				$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");
				$venster->insertAction("forward", gettext("exporteer"), "javascript: document.getElementById('exportfrm').submit();");
				$venster->endTag("form");

			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	} else {
		$address_data = new Address_data();

		#session_cache_limiter('private, must-revalidate');
		#session_start();
		$commencements = $address_data->getCommencements();
		$titles = $address_data->getTitles();
		$letterheads = $address_data->getLetterheads();

		if ($_REQUEST["what_to_export"] == "bcards" || $exportinfo["addresstype"] == "bcards") {
			//{{{ bcards
			$csv = array();
			$csv[]= gettext("titel");
			$csv[]= gettext("aanroep");
			$csv[]= gettext("aanhef");
			$csv[]= gettext("tav");
			$csv[]= gettext("contactpersoon");
			$csv[]= gettext("bedrijfsnaam");
			$csv[]= gettext("voorletters");
			$csv[]= gettext("voornaam");
			$csv[]= gettext("tussenvoegsels");
			$csv[]= gettext("achternaam");
			$csv[]= gettext("telefoon");
			$csv[]= gettext("fax");
			$csv[]= gettext("mobiel");
			$csv[]= gettext("email");
			$csv[]= gettext("factuur_adres");
			$csv[]= gettext("factuur_adres_extra");
			$csv[]= gettext("factuur_postcode");
			$csv[]= gettext("factuur_plaats");
			$csv[]= gettext("vestigings_adres");
			$csv[]= gettext("vestigings_adres_extra");
			$csv[]= gettext("vestigings_postcode");
			$csv[]= gettext("vestigings_plaats");
			$csv[]= gettext("landcode");
			$csv[]= gettext("memo");
			$csv[]= gettext("bedrijfsonderdeel");
			$csv[]= gettext("afdeling");
			$csv[]= gettext("locatie code");
			$data = $this->generateCSVRecord($csv);

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
				$this->exportBcardRecord($data, $row, $address_data);
				if ($row["multirel"]) {
					$multi = explode(",", $row["multirel"]);
					foreach ($multi as $m) {
						if ($m) {
							$row2 = $row;
							$row2["address_id"] = $m;
							$this->exportBcardRecord($data, $row2, $address_data);
						}
					}
				}
			}
			//}}}
		} else {
			//{{{ relations
			$csv = array();
			$csv[]= gettext("titel");
			$csv[]= gettext("aanroep");
			$csv[]= gettext("aanhef");
			$csv[]= gettext("tav");
			$csv[]= gettext("contactpersoon");
			$csv[]= gettext("bedrijfsnaam");
			$csv[]= gettext("voorletters");
			$csv[]= gettext("voornaam");
			$csv[]= gettext("tussenvoegsels");
			$csv[]= gettext("achternaam");
			$csv[]= gettext("telefoon");
			$csv[]= gettext("fax");
			$csv[]= gettext("telefoon");
			$csv[]= gettext("email");
			$csv[]= gettext("factuur_adres");
			$csv[]= gettext("factuur_adres_extra");
			$csv[]= gettext("factuur_postcode");
			$csv[]= gettext("factuur_plaats");
			$csv[]= gettext("vestigings_adres");
			$csv[]= gettext("vestigings_adres_extra");
			$csv[]= gettext("vestigings_postcode");
			$csv[]= gettext("vestigings_plaats");
			$csv[]= gettext("landcode");
			$csv[]= gettext("memo");
			$data = $this->generateCSVRecord($csv);

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

					$csv = array();
					$csv[]= $titles[$address_record["title"]];
					$csv[]= $commencements[$address_record["contact_commencement"]];
					$csv[]= $letterheads[$address_record["contact_letterhead"]];
					$csv[]= $address_record["tav"];
					$csv[]= $address_record["contact_person"];
					$csv[]= $address_record["companyname"];
					$csv[]= $address_record["contact_initials"];
					$csv[]= $address_record["contact_givenname"];
					$csv[]= $address_record["contact_infix"];
					$csv[]= $address_record["contact_surname"];
					$csv[]= $address_record["phone_nr"];
					$csv[]= $address_record["fax_nr"];
					$csv[]= $address_record["mobile_nr"];
					$csv[]= $address_record["email"];

					if ($address_record["pobox"]) {
						$csv[]= gettext("Postbus")." ".$address_record["pobox"];
						$csv[]= "";
					} else {
						$csv[]= $address_record["address"];
						$csv[]= $address_record["address2"];
					}
					$csv[]= ($address_record["pobox_zipcode"]) ? $address_record["pobox_zipcode"]:$address_record["zipcode"];
					$csv[]= ($address_record["pobox_city"])    ? $address_record["pobox_city"]:$address_record["city"];

					$csv[]= $address_record["address"];
					$csv[]= $address_record["address2"];
					$csv[]= $address_record["zipcode"];
					$csv[]= $address_record["city"];
					$csv[]= $address_record["country"];

					$csv[]= preg_replace("/(\r)|(\t)|(\n)/s", " ", $address_record["memo"]);
					$data.= $this->generateCSVRecord($csv);
				}
			}
			// }}}
		}
	}
	/*
	if ($_REQUEST["encoding"] && $_REQUEST["encoding"] != "UTF-8")
		$data = mb_convert_encoding($data, $_REQUEST["encoding"], "UTF-8");
	*/

	header("Content-Transfer-Encoding: binary");
	header("Content-Type: text/plain");

	if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
		header("Content-Disposition: filename=addresslist.csv"); //msie 5.5 header bug
	}else{
		header("Content-Disposition: attachment; filename=addresslist.csv");
	}
	echo $data;
	exit();
?>
