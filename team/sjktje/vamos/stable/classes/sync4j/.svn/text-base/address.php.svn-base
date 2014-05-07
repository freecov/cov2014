<?
Class Sync4j_address {

	function insertAdresData($s, $k, $hash, $data) {
		$q = "insert into address_sync (parent_id, address_id, address_table, is_private, account_manager, sync_modified, sync_hash) values (";
		$q.= sprintf("%d, %d, '%s', %d, %d, %d, '%s')", $data["parent"], $k, $s, $data["prive"], $data["acc_manager"], $data["sync_modified"], $hash);
		sql_query($q);
	}

	function updateAdresData($s, $k, $hash, $data) {
		$q = sprintf("update address_sync set is_private = %d", $data["prive"]);
		$q.= sprintf(", account_manager = %d", $data["acc_manager"]);
		$q.= sprintf(", sync_modified = %d", $data["sync_modified"]);
		$q.= sprintf(", sync_hash = '%s' where address_id = %d and address_table = '%s'", $hash, $k, $s);
		sql_query($q);
	}


	function getAdresData($table, $id) {

		$q = "select * from $table where id = $id";
		$res = sql_query($q);
		$row = sql_fetch_array($res);


		$data = array();
		switch ($table) {

			case "address" :
				//internal vars
				if ($row["is_public"]!=1) {
					$data["prive"]        = $row["user_id"];
					$data["priveperson"]  = 2;
				}
				$data["account_manager"] = $row["account_manager"];
				$data["sync_modified"]   = $row["sync_modified"];
				//public vars
				$data["contactperson"]   = $row["companyname"]." (relatie)";
				$data["tav"]             = $row["companyname"];
				$data["givenname"]       = $row["contact_givenname"];
				$data["initials"]        = $row["contact_initials"];
				$data["infix"]           = $row["contact_infix"];
				$data["surname"]         = $row["contact_surname"];
				#$data["country"]        = $row["country"];
				$data["tel_mobile_nr"]   = $row["mobile_nr"];
				$data["tel_fax_nr"]      = $row["fax_nr"];
				$data["tel_phone_nr"]    = $row["phone_nr"];
				$data["address"]         = $row["address"];
				$data["zipcode"]         = $row["zipcode"];
				$data["city"]            = $row["city"];
				$data["business_email"]  = $row["email"];
				#$data["website"]        = $row["website"];
				$data["companyname"]     = $row["companyname"];
				#$data["memo"]           = trim($row["letop"]."\n\n".$row["comment"]);

				break;

			case "address_businesscards" :

				//internal vars
				$data["sync_modified"]        = $row["sync_modified"];
				$data["parent"]               = $row["address_id"];

				//public vars
				$q                            = sprintf("select * from address where id = %d", $row["address_id"]);
				$res2                         = sql_query($q);
				$row2                         = sql_fetch_array($res2);

				if ($row["alternative_name"]) {
					$data["contactperson"]        = $row["alternative_name"]." (bcard)";
				} else {
					$data["contactperson"]        = str_replace("  "," ",$row["givenname"]." ".$row["infix"]." ".$row["surname"])." (bcard)";
				}
				$data["tav"]                  = $data["contactperson"];
				$data["givenname"]            = $row["givenname"];
				$data["initials"]             = $row["initials"];
				$data["infix"]                = $row["infix"];
				$data["surname"]              = $row["surname"];
				#$data["country"]             = $row2["country"];
				$data["tel_fax_nr"]           = $row["business_fax_nr"];
				$data["tel_phone_nr"]         = $row["business_phone_nr"];
				$data["address"]              = $row["business_address"];
				$data["zipcode"]              = $row["business_zipcode"];
				$data["city"]                 = $row["business_city"];
				$data["business_email"]       = $row["business_email"];
				$data["private_email"]        = $row["personal_email"];
				#$data["adres_prive"]         = $row["padres"];
				#$data["postcode_prive"]      = $row["ppostcode"];
				#$data["plaats_prive"]        = $row["pplaats"];
				$data["tel_private_phone_nr"] = $row["personal_phone_nr"];
				if ($row["business_mobile_nr"]) {
					$data["tel_mobile_nr"]        = $row["business_mobile_nr"];
				} else {
					$data["tel_mobile_nr"]        = $row["personal_mobile_nr"];
				}
				$data["companyname"]          = $row2["companyname"];
				#$data["memo"]                = trim($row["memo"]);

				break;

			case "address_private" :

				$q = sprintf("select count(*) from users where address_id = %d", $row["id"]);
				$res2 = sql_query($q);
				if (sql_result($res2,0)>0) {
					$data["contactperson"] = str_replace("  "," ",$row["givenname"]." ".$row["surname"])." (".gettext("employee").")";
				} else {
					$data["contactperson"] = str_replace("  "," ",$row["givenname"]." ".$row["surname"])." (".gettext("person").")";
				}

				//internal vars
				if ($row["is_public"]!=1) {
					$data["prive"]          = $row["user_id"];
					$data["priveperson"]    = 2;
				}
				$data["sync_modified"]  = $row["sync_modified"];
				$data["tav"]            = str_replace("  "," ",$row["givenname"]." ".$row["surname"]);
				$data["givenname"]      = $row["givenname"];
				$data["initials"]       = substr($row["givenname"],0,1).".";
				$data["surname"]        = $row["surname"];
				#$data["country"]       = $row2["country"];
				$data["tel_fax_nr"]     = $row["fax_nr"];
				$data["tel_phone_nr"]   = $row["phone_nr"];
				$data["address"]        = $row["address"];
				$data["zipcode"]        = $row["zipcode"];
				$data["city"]           = $row["city"];
				$data["business_email"] = $row["email"];
				$data["tel_mobile_nr"]  = $row["mobile_nr"];
				#$data["website"]       = $row["website"];
				#$data["memo"]          = trim($row["comment"]);

				break;

			case "address_other":

				$data["contactperson"]  = $row["companyname"]." (".gettext("remaining").")";
				//internal vars
				if ($row["is_public"]!=1) {
					$data["prive"]          = $row["user_id"];
					$data["priveperson"]    = 2;
				}
				$data["sync_modified"]  = $row["sync_modified"];
				$data["tav"]            = str_replace("  "," ",$row["givenname"]." ".$row["surname"]);
				$data["givenname"]      = $row["givenname"];
				$data["initials"]       = substr($row["givenname"],0,1).".";
				$data["surname"]        = $row["surname"];
				$data["tel_fax_nr"]     = $row["fax_nr"];
				$data["tel_phone_nr"]   = $row["phone_nr"];
				$data["address"]        = $row["address"];
				$data["zipcode"]        = $row["zipcode"];
				$data["city"]           = $row["city"];
				$data["business_email"] = $row["email"];
				$data["tel_mobile_nr"]  = $row["mobile_nr"];
				//				#$data["memo"]    = trim($row["comment"]);

				break;

			default:
				die("source not defined!<br>");
				break;
		}
		return $data;
	}

	function adress2sync($data) {

		//format for data:
		/*
			priveperson (0 of 2)
			tav
			givenname
			infix
			surname
			country
			tel_mobile_nr
			tel_fax_nr
			tel_phone_nr
			tel_private_phone_nr
			address
			zipcode
			city
			initials
			business_email
			website
			private_email
			companyname
			memo
		*/


		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$xml.= "<contact>";
		$xml.= "<Companies></Companies>";
		$xml.= "<Email3Address></Email3Address>";
		$xml.= "<CompanyMainTelephoneNumber></CompanyMainTelephoneNumber>";
		$xml.= "<Business2TelephoneNumber></Business2TelephoneNumber>";
		$xml.= "<CarTelephoneNumber></CarTelephoneNumber>";
		$xml.= "<Email2Address>".$data["private_email"]."</Email2Address>";
		$xml.= "<OtherAddressCountry></OtherAddressCountry>";
		$xml.= "<OtherFaxNumber></OtherFaxNumber>";
		$xml.= "<Suffix></Suffix>";
		$xml.= "<BusinessAddressPostOfficeBox></BusinessAddressPostOfficeBox>";
		$xml.= "<FirstName>".$data["givenname"]."</FirstName>";
		$xml.= "<Subject>".$data["tav"]."</Subject>";
		$xml.= "<Hobby></Hobby>";
		$xml.= "<HomeAddressPostOfficeBox></HomeAddressPostOfficeBox>";
		$xml.= "<OtherTelephoneNumber></OtherTelephoneNumber>";
		$xml.= "<Department></Department>";
		$xml.= "<Home2TelephoneNumber></Home2TelephoneNumber>";
		$xml.= "<HomeAddressStreet>".$data["address_private"]."</HomeAddressStreet>";
		$xml.= "<JobTitle></JobTitle>";
		$xml.= "<Anniversary></Anniversary>";
		$xml.= "<PrimaryTelephoneNumber></PrimaryTelephoneNumber>";
		$xml.= "<MobileTelephoneNumber>".$data["tel_mobile_nr"]."</MobileTelephoneNumber>";
		$xml.= "<YomiCompanyName></YomiCompanyName>";
		$xml.= "<BusinessAddressCountry>".$data["country"]."</BusinessAddressCountry>";
		$xml.= "<OtherAddressState></OtherAddressState>";
		$xml.= "<Sensitivity>".(int)$data["priveperson"]."</Sensitivity>";
		$xml.= "<NickName></NickName>";
		$xml.= "<HomeAddressPostalCode></HomeAddressPostalCode";
		$xml.= "<OrganizationalIDNumber></OrganizationalIDNumber>";
		$xml.= "<ManagerName></ManagerName>";
		$xml.= "<BusinessTelephoneNumber>".$data["tel_phone_nr"]."</BusinessTelephoneNumber>";
		$xml.= "<YomiLastName></YomiLastName>";
		$xml.= "<WebPage>".$data["website"]."</WebPage>";
		$xml.= "<Email2AddressType></Email2AddressType>";
		$xml.= "<BusinessAddressCity>".$data["zipcode"]."</BusinessAddressCity>";
		$xml.= "<Folder>/</Folder>";
		$xml.= "<Title></Title>";
		$xml.= "<MiddleName>".$data["infix"]."</MiddleName>";
		$xml.= "<FileAs>".$data["contactperson"]."</FileAs>";
		$xml.= "<HomeAddressCountry></HomeAddressCountry>";
		$xml.= "<Birthday>".$data["geboortedatum"]."</Birthday>";
		$xml.= "<HomeWebPage></HomeWebPage>";
		$xml.= "<RadioTelephoneNumber></RadioTelephoneNumber>";
		$xml.= "<OtherAddressPostalCode></OtherAddressPostalCode>";
		$xml.= "<BusinessAddressStreet>".$data["address"]."</BusinessAddressStreet>";
		$xml.= "<BusinessAddressPostalCode></BusinessAddressPostalCode>";
		$xml.= "<Language></Language>";
		$xml.= "<AssistantTelephoneNumber></AssistantTelephoneNumber>";
		$xml.= "<PagerNumber></PagerNumber>";
		$xml.= "<HomeAddressCity>".$data["postcode_prive"]."</HomeAddressCity>";
		$xml.= "<Profession></Profession>";
		$xml.= "<HomeAddressState>".$data["plaats_prive"]."</HomeAddressState>";
		$xml.= "<BillingInformation></BillingInformation>";
		$xml.= "<YomiFirstName></YomiFirstName>";
		$xml.= "<OtherAddressStreet></OtherAddressStreet>";
		$xml.= "<OtherAddressCity></OtherAddressCity>";
		$xml.= "<CallbackTelephoneNumber></CallbackTelephoneNumber>";
		$xml.= "<OtherAddressPostOfficeBox></OtherAddressPostOfficeBox>";
		$xml.= "<Initials>".$data["initials"]."</Initials>";
		$xml.= "<Mileage></Mileage>";
		$xml.= "<Email1Address>".$data["business_email"]."</Email1Address>";
		$xml.= "<Children></Children>";
		$xml.= "<BusinessFaxNumber>".$data["tel_fax_nr"]."</BusinessFaxNumber>";
		$xml.= "<Email3AddressType></Email3AddressType>";
		$xml.= "<Importance>1</Importance>";
		$xml.= "<Email1AddressType>SMTP</Email1AddressType>";
		$xml.= "<Body>".$data["memo"]."</Body>";
		$xml.= "<TelexNumber></TelexNumber>";
		$xml.= "<OfficeLocation></OfficeLocation>";
		$xml.= "<AssistantName></AssistantName>";
		$xml.= "<Spouse></Spouse>";
		$xml.= "<Categories></Categories>";
		$xml.= "<HomeTelephoneNumber>".$data["private_phone_nr"]."</HomeTelephoneNumber>";
		$xml.= "<BusinessAddressState>".$data["city"]."</BusinessAddressState>";
		$xml.= "<ComputerNetworkName></ComputerNetworkName>";
		$xml.= "<CompanyName>".$data["companyname"]."</CompanyName>";
		$xml.= "<HomeFaxNumber></HomeFaxNumber>";
		$xml.= "<LastName>".$data["surname"]."</LastName>";
		$xml.= "</contact>";

		return $xml;
	}
}
?>
