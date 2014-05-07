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
			$csv[]= gettext("telephone");
			$csv[]= gettext("email");
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
			$data = $conversion->generateCSVRecord($csv);

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
				$address_record = $address_data->getAddressById($row["id"], $options["addresstype"]);
				if ($address_record["id"]) {

					$csv = array();
					$csv[]= $titles[$address_record["title"]]["title"];
					$csv[]= $commencements[$address_record["contact_commencement"]]["title"];
					$csv[]= $letterheads[$address_record["contact_letterhead"]]["title"];
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
					$csv[]= $address_record["country"];

					$csv[]= preg_replace("/(\r)|(\t)|(\n)/s", " ", $address_record["memo"]);
					$data.= $conversion->generateCSVRecord($csv);
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
	header("Content-Type: text/plain; charset=UTF-8");

	if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
		header("Content-Disposition: filename=addresslist.csv"); //msie 5.5 header bug
	}else{
		header("Content-Disposition: attachment; filename=addresslist.csv");
	}
	echo $data;
	exit();
?>
