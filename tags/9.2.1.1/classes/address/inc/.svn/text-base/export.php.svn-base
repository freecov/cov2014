<?php
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
				"title" => gettext("addresses"),
				"subtitle" => gettext("export")
			));
			$venster->addVensterData();
				$venster->addCode(gettext("What do you want to export")."?");
				$venster->addTag("br");
				//$venster->insertLink(gettext("contacts"), array("href" => "index.php?mod=address&action=export&what_to_export=rel&dl=1"));
				//$venster->addTag("br");
				//$venster->insertLink(gettext("business cards"), array("href" => "index.php?mod=address&action=export&what_to_export=bcards&dl=1"));
				$venster->addTag("form", array(
					"action" => "index.php",
					"id"     => "exportfrm"
				));
				$venster->addHiddenField("mod", "address");
				$venster->addHiddenField("action", "export");
				$sel = array(
					"rel" => gettext("all relationcards"),
					"bcards" => gettext("all businesscards")
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
				$venster->insertAction("close", gettext("close"), "javascript: window.close();");
				$venster->insertAction("forward", gettext("export"), "javascript: document.getElementById('exportfrm').submit();");
				$venster->endTag("form");

			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	} else {
		$conversion = new Layout_conversion();
		$address_data = new Address_data();

		#session_cache_limiter('private, must-revalidate');
		#session_start();
		$commencements = $address_data->getCommencements();
		$titles = $address_data->getTitles();
		$letterheads = $address_data->getLetterheads();

		if ($_REQUEST["what_to_export"] == "bcards" || $exportinfo["addresstype"] == "bcards") {
			//{{{ bcards
			$csv = array();
			$csv[]= gettext("title");
			$csv[]= gettext("call");
			$csv[]= gettext("commencement");
			$csv[]= gettext("tav");
			$csv[]= gettext("contact");
			$csv[]= gettext("company name");
			$csv[]= gettext("initials");
			$csv[]= gettext("given name");
			$csv[]= gettext("infix");
			$csv[]= gettext("last name");
			$csv[]= gettext("telephone");
			$csv[]= gettext("fax");
			$csv[]= gettext("mobile");
			$csv[]= gettext("email");
			$csv[]= gettext("website");
			$csv[]= gettext("invoice_address");
			$csv[]= gettext("invoice_address_extra");
			$csv[]= gettext("invoice_zipcode");
			$csv[]= gettext("invoice_city");
			$csv[]= gettext("company_address");
			$csv[]= gettext("company_address_extra");
			$csv[]= gettext("company_address_zipcode");
			$csv[]= gettext("company_address_city");
			$csv[]= gettext("countrycode");
			$csv[]= gettext("memo");
			$csv[]= gettext("company department");
			$csv[]= gettext("department");
			$csv[]= gettext("locationcode");
			$csv[]= gettext("free field");
			$csv[]= gettext("jobtitle");
			$csv[]= gettext("assistant name");
			$csv[]= gettext("assistant phone");
			$csv[]= gettext("profession");
			$csv[]= gettext("manager name");
			$csv[]= gettext("birthday");
			$csv[]= gettext("classifications");
			$data = $conversion->generateCSVRecord($csv);

			if (is_array($exportinfo)) {
				$options = $exportinfo;
			} else {
				$options = array(
					"addresstype" => "bcards",
					"top"         => "0"
				);
			}
			$options["bcard_export"] = true;
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
			if (!$_REQUEST["reeleezee"]) {
				$csv = array();
				$csv[]= gettext("title");
				$csv[]= gettext("call");
				$csv[]= gettext("commencement");
				$csv[]= gettext("tav");
				$csv[]= gettext("contact");
				$csv[]= gettext("company name");
				$csv[]= gettext("deptor nr");
				$csv[]= gettext("warning");
				$csv[]= gettext("initials");
				$csv[]= gettext("given name");
				$csv[]= gettext("infix");
				$csv[]= gettext("last name");
				$csv[]= gettext("jobtitle");
				$csv[]= gettext("telephone");
				$csv[]= gettext("fax");
				$csv[]= gettext("telephone");
				$csv[]= gettext("email");
				$csv[]= gettext("website");
				$csv[]= gettext("invoice_address");
				$csv[]= gettext("invoice_address_extra");
				$csv[]= gettext("invoice_zipcode");
				$csv[]= gettext("invoice_city");
				$csv[]= gettext("company_address");
				$csv[]= gettext("company_address_extra");
				$csv[]= gettext("company_address_zipcode");
				$csv[]= gettext("company_address_city");
				$csv[]= gettext("countrycode");
				$csv[]= gettext("classifications");
				$csv[]= gettext("memo");
				$data = $conversion->generateCSVRecord($csv);
			} else {
				$data = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
				$data .= "<Reeleezee version=\"1.12\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.reeleezee.nl/taxonomy/1.13/Reeleezee.xsd\" xmlns=\"http://www.reeleezee.nl/taxonomy/1.12\">\r\n";
				$data .= "\t<Import AutoIgnoreEnabled=\"true\" AutoTrimEnabled=\"true\">\r\n";
				$data .= "\t\t<ExportInfo>\r\n";
				$data .= "\t\t\t<Name>Covide export</Name>\r\n";
				$data .= "\t\t\t<Source>Reeleezee administratie Reeleezee Taxonomy</Source>\r\n";
				$data .= "\t\t\t<CreateDateTime>2008-03-31T16:36:24.4141424+02:00</CreateDateTime>\r\n";
				$data .= "\t\t</ExportInfo>\r\n";
				$data .= "\t\t<CustomerList>\r\n";
			}

			if (is_array($exportinfo)) {
				$options = $exportinfo;
			} else {
				$options = array(
					"addresstype" => "relations",
					"top"         => "0"
				);
			}
			/*
				Customerlist:
					array
					  'addresstype' => string 'relations' (length=9)
					  'bcard_export' => boolean true
					  'top' => int 0
					  'action' => string '' (length=0)
					  'l' => null
					  'sub' => string 'klanten' (length=7)
					  'and_or' => null
					  'search' => null
					  'specified' => null
					  'landSelect' => null
					  'classifications' => null
					  'selectiontype' => null
					  'sort' => null
					  'funambol_user' => string '' (length=0)
					  'cmsforms' => null


				VendorList
					array
					  'addresstype' => string 'relations' (length=9)
					  'bcard_export' => boolean true
					  'top' => int 0
					  'action' => string '' (length=0)
					  'l' => null
					  'sub' => string 'leveranciers' (length=12)
					  'and_or' => null
					  'search' => null
					  'specified' => null
					  'landSelect' => null
					  'classifications' => null
					  'selectiontype' => null
					  'sort' => null
					  'funambol_user' => string '' (length=0)
					  'cmsforms' => null
			 */
			$relations = $address_data->getRelationsList($options);
			$countries = $address_data->listCountries();

			if ($options["addresstype"] == "relations") {
				$relations["query_csv"].=" ORDER BY address.companyname";
			}

			$res = sql_query($relations["query_csv"]);
			while ($row = sql_fetch_assoc($res)) {
				$address_record = $address_data->getAddressById($row["id"], $options["addresstype"]);
				if ($address_record["id"]) {
					$csv = array();
					$csv[]= $titles[$address_record["title"]]["title"];
					$csv[]= $commencements[$address_record["contact_commencement"]]["title"];
					$csv[]= $letterheads[$address_record["contact_letterhead"]]["title"];
					$csv[]= $address_record["tav"];
					$csv[]= $address_record["contact_person"];
					$csv[]= $address_record["companyname"];
					$csv[]= $address_record["debtor_nr"];
					$csv[]= $address_record["warning"];
					$csv[]= $address_record["contact_initials"];
					$csv[]= $address_record["contact_givenname"];
					$csv[]= $address_record["contact_infix"];
					$csv[]= $address_record["contact_surname"];
					$csv[]= $address_record["jobtitle"];
					$csv[]= $address_record["phone_nr"];
					$csv[]= $address_record["fax_nr"];
					$csv[]= $address_record["mobile_nr"];
					$csv[]= $address_record["email"];
					$csv[]= $address_record["website"];

					if ($address_record["pobox"]) {
						$csv[]= gettext("PO box")." ".$address_record["pobox"];
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
					if (array_key_exists($address_record["country"], $countries)) {
						$country = $countries[$address_record["country"]];
					} else {
						$country = $address_record["country"];
					}
					$csv[]= $country;
					$cla_names = explode("\n", $address_record["classification_names"]);
					$cla_names = array_unique($cla_names);
					$csv[]= implode(",", $cla_names);

					$csv[]= preg_replace("/(\r)|(\t)|(\n)/s", " ", $address_record["memo"]);
					if (!$_REQUEST["reeleezee"]) {
						$data.= $conversion->generateCSVRecord($csv);
					} else {
						if ($address_record["debtor_nr"] > 0) {
							//someone told me customers should start with 12 ?????
							//$data .= "\t\t\t<Customer ID=\"12".sprintf("%08s", $address_record["debtor_nr"])."\">\r\n";
							$data .= "\t\t\t<Customer ID=\"".sprintf("%s", $address_record["debtor_nr"])."\">\r\n";
							$data .= "\t\t\t\t<FullName>".substr(trim(str_replace("&", "&amp;", $address_record["companyname"])), 0, 48)."</FullName>\r\n";
							$data .= "\t\t\t\t<SearchName>".substr(trim(str_replace("&", "&amp;", $address_record["companyname"])), 0, 48)."</SearchName>\r\n";
							$data .= "\t\t\t\t<Code xsi:nil=\"true\" />\r\n";
							if ($address_record["pobox"]) {
								$data .= "\t\t\t\t<DefaultAddress>Delivery</DefaultAddress>\r\n";
							} else {
								$data .= "\t\t\t\t<DefaultAddress>Office</DefaultAddress>\r\n";
							}
							$data .= "\t\t\t\t<LanguageCode>nl</LanguageCode>\r\n";
							if ($address_record["phone_nr"]) {
								$data .= "\t\t\t\t<PhoneNumber>".str_replace(".", "", trim($address_record["phone_nr"]))."</PhoneNumber>\r\n";
							} else {
								$data .= "\t\t\t\t<PhoneNumber xsi:nil=\"true\" />\r\n";
							}
							if ($address_record["fax_nr"]) {
								$data .= "\t\t\t\t<FaxNumber>".str_replace(".", "", trim($address_record["fax_nr"]))."</FaxNumber>\r\n";
							} else {
								$data .= "\t\t\t\t<FaxNumber xsi:nil=\"true\" />\r\n";
							}
							if ($address_record["email"]) {
								$data .= "\t\t\t\t<EmailAddress>".trim($address_record["email"])."</EmailAddress>\r\n";
							} else {
								$data .= "\t\t\t\t<EmailAddress xsi:nil=\"true\" />\r\n";
							}
							$data .= "\t\t\t\t<WebsiteAddress xsi:nil=\"true\" />\r\n";
							$data .= "\t\t\t\t<Comment xsi:nil=\"true\" />\r\n";
							$data .= "\t\t\t\t<ChamberOfCommerceNumber xsi:nil=\"true\" />\r\n";
							$data .= "\t\t\t\t<ChamberOfCommerceCity xsi:nil=\"true\" />\r\n";
							$data .= "\t\t\t\t<FiscalIdentificationNumber xsi:nil=\"true\" />\r\n";
							$data .= "\t\t\t\t<TaxDepositLHNumber xsi:nil=\"true\" />\r\n";
							$data .= "\t\t\t\t<TaxDepositOBNumber xsi:nil=\"true\" />\r\n";
							$data .= "\t\t\t\t<TaxDepositICLNumber xsi:nil=\"true\" />\r\n";
							if ($address_record["bankaccount"]) {
								$data .= "\t\t\t\t<BankAccountNumber>".trim($address_record["bankaccount"])."</BankAccountNumber>\r\n";
							} else {
								$data .= "\t\t\t\t<BankAccountNumber xsi:nil=\"true\" />\r\n";
							}
							$data .= "\t\t\t\t<Branch xsi:nil=\"true\" />\r\n";
							$data .= "\t\t\t\t<AddressList>\r\n";
							$data .= "\t\t\t\t\t<Address Type=\"Office\">\r\n";
							if ($address_record["address"]) {
								$data .= "\t\t\t\t\t\t<Street>".trim($address_record["address"])."</Street>\r\n";
							} else {
								$data .= "\t\t\t\t\t\t<Street xsi:nil=\"true\" />\r\n";
							}
							$data .= "\t\t\t\t\t\t<Number xsi:nil=\"true\"/>\r\n";
							if ($address_record["zipcode"]) {
								$data .= "\t\t\t\t\t\t<Zipcode>".trim($address_record["zipcode"])."</Zipcode>\r\n";
							} else {
								$data .= "\t\t\t\t\t\t<Zipcode xsi:nil=\"true\" />\r\n";
							}
							if ($address_record["city"]) {
								$data .= "\t\t\t\t\t\t<City>".trim($address_record["city"])."</City>\r\n";
							} else {
								$data .= "\t\t\t\t\t\t<City xsi:nil=\"true\" />\r\n";
							}
							$data .= "\t\t\t\t\t\t<CountryCode>NL</CountryCode>\r\n";
							$data .= "\t\t\t\t\t</Address>\r\n";
							if ($address_record["pobox"]) {
								$data .= "\t\t\t\t\t<Address Type=\"Delivery\">\r\n";
								$data .= "\t\t\t\t\t\t<Street>POSTBUS</Street>\r\n";
								$data .= "\t\t\t\t\t\t<Number>".$address_record["pobox"]."</Number>\r\n";
								if ($address_record["pobox_zipcode"]) {
									$data .= "\t\t\t\t\t\t<Zipcode>".trim($address_record["pobox_zipcode"])."</Zipcode>\r\n";
								} else {
									$data .= "\t\t\t\t\t\t<Zipcode xsi:nil=\"true\" />\r\n";
								}
								if ($address_record["pobox_city"]) {
									$data .= "\t\t\t\t\t\t<City>".trim($address_record["pobox_city"])."</City>\r\n";
								} else {
									$data .= "\t\t\t\t\t\t<City xsi:nil=\"true\" />\r\n";
								}
								$data .= "\t\t\t\t\t\t<CountryCode>NL</CountryCode>\r\n";
								$data .= "\t\t\t\t\t</Address>\r\n";
							} else {
								$data .= "\t\t\t\t\t<Address Type=\"Delivery\" xsi:nil=\"true\" />\r\n";
							}
							$data .= "\t\t\t\t</AddressList>\r\n";
							$data .= "\t\t\t\t<ContactPersonList />\r\n";
							$data .= "\t\t\t</Customer>\r\n";
						}
					}
				}
			}
			if ($_REQUEST["reeleezee"]) {
				$data .= "\t\t</CustomerList>\r\n";
				$data .= "\t\t<VendorList>\r\n";

				$options["sub"] = "leveranciers";
				$relations = $address_data->getRelationsList($options);

				if ($options["addresstype"] == "relations") {
					$relations["query_csv"].=" ORDER BY address.companyname";
				}

				$res = sql_query($relations["query_csv"]);
				while ($row = sql_fetch_assoc($res)) {
					$address_record = $address_data->getAddressById($row["id"], $options["addresstype"]);
					if ($address_record["id"] && $address_record["debtor_nr"] > 0) {
						//someone told me vendors should start with 16 ?????
						//$data .= "\t\t\t<Vendor ID=\"16".sprintf("%08s", $address_record["debtor_nr"])."\">\r\n";
						$data .= "\t\t\t<Vendor ID=\"".sprintf("%s", $address_record["debtor_nr"])."\">\r\n";
						$data .= "\t\t\t\t<FullName>".substr(trim(str_replace("&", "&amp;", $address_record["companyname"])), 0, 48)."</FullName>\r\n";
						$data .= "\t\t\t\t<SearchName>".substr(trim(str_replace("&", "&amp;", $address_record["companyname"])), 0, 48)."</SearchName>\r\n";
						$data .= "\t\t\t\t<Code xsi:nil=\"true\" />\r\n";
						$data .= "\t\t\t\t<DefaultAddress>Office</DefaultAddress>\r\n";
						$data .= "\t\t\t\t<LanguageCode>nl</LanguageCode>\r\n";
						if ($address_record["phone_nr"]) {
							$data .= "\t\t\t\t<PhoneNumber>".str_replace(".", "", trim($address_record["phone_nr"]))."</PhoneNumber>\r\n";
						} else {
							$data .= "\t\t\t\t<PhoneNumber xsi:nil=\"true\" />\r\n";
						}
						if ($address_record["fax_nr"]) {
							$data .= "\t\t\t\t<FaxNumber>".str_replace(".", "", trim($address_record["fax_nr"]))."</FaxNumber>\r\n";
						} else {
							$data .= "\t\t\t\t<FaxNumber xsi:nil=\"true\" />\r\n";
						}
						if ($address_record["email"]) {
							$data .= "\t\t\t\t<EmailAddress>".trim($address_record["email"])."</EmailAddress>\r\n";
						} else {
							$data .= "\t\t\t\t<EmailAddress xsi:nil=\"true\" />\r\n";
						}
						$data .= "\t\t\t\t<WebsiteAddress xsi:nil=\"true\" />\r\n";
						$data .= "\t\t\t\t<Comment xsi:nil=\"true\" />\r\n";
						$data .= "\t\t\t\t<ChamberOfCommerceNumber xsi:nil=\"true\" />\r\n";
						$data .= "\t\t\t\t<ChamberOfCommerceCity xsi:nil=\"true\" />\r\n";
						$data .= "\t\t\t\t<TaxDepositOBNumber xsi:nil=\"true\" />\r\n";
						$data .= "\t\t\t\t<BankAccountNumber xsi:nil=\"true\" />\r\n";
						$data .= "\t\t\t\t<Branch xsi:nil=\"true\" />\r\n";
						$data .= "\t\t\t\t<AddressList>\r\n";
						$data .= "\t\t\t\t\t<Address Type=\"Office\">\r\n";
						if ($address_record["address"]) {
							$data .= "\t\t\t\t\t\t<Street>".trim($address_record["address"])."</Street>\r\n";
						} else {
							$data .= "\t\t\t\t\t\t<Street xsi:nil=\"true\" />\r\n";
						}
						$data .= "\t\t\t\t\t\t<Number xsi:nil=\"true\"/>\r\n";
						if ($address_record["zipcode"]) {
							$data .= "\t\t\t\t\t\t<Zipcode>".trim($address_record["zipcode"])."</Zipcode>\r\n";
						} else {
							$data .= "\t\t\t\t\t\t<Zipcode xsi:nil=\"true\" />\r\n";
						}
						if ($address_record["city"]) {
							$data .= "\t\t\t\t\t\t<City>".trim($address_record["city"])."</City>\r\n";
						} else {
							$data .= "\t\t\t\t\t\t<City xsi:nil=\"true\" />\r\n";
						}
						$data .= "\t\t\t\t\t\t<CountryCode>NL</CountryCode>\r\n";
						$data .= "\t\t\t\t\t</Address>\r\n";
						$data .= "\t\t\t\t\t<Address Type=\"Delivery\" xsi:nil=\"true\" />\r\n";
						$data .= "\t\t\t\t</AddressList>\r\n";
						$data .= "\t\t\t\t<ContactPersonList />\r\n";
						$data .= "\t\t\t</Vendor>\r\n";
					}

				}

				$data .= "\t\t</VendorList>\r\n";
				$data .= "\t</Import>\r\n";
				$data .= "</Reeleezee>\r\n";
			}

			// }}}
		}
	}
	/*
	if ($_REQUEST["encoding"] && $_REQUEST["encoding"] != "UTF-8")
		$data = mb_convert_encoding($data, $_REQUEST["encoding"], "UTF-8");
	*/

	if ($_REQUEST["reeleezee"]) {
		$filename="addresslist_reeleezee.xml";
	} else {
		$filename="addresslist.csv";
	}
	header("Content-Transfer-Encoding: binary");
	header("Content-Type: text/plain; charset=UTF-8");

	if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
		header("Content-Disposition: filename=".$filename); //msie 5.5 header bug
	}else{
		header("Content-Disposition: attachment; filename=".$filename);
	}
	echo $data;
	exit();
?>
