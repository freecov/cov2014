<?
Class ProjectDeclaration_data {
	/* constants */
	const include_dir = "classes/projectedeclaration/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "projectdeclaration";

	public $declaration_types;

	public function __construct() {
		$this->declaration_types =  array(
			1 => gettext("hour registration"),
			2 => gettext("kilometers"),
			3 => gettext("verschotten"),
			4 => gettext("NORA-tariff")
		);
	}
	public function addToDeclare($project_id) {
		$q = sprintf("select sum(price) from projects_declaration_registration where batch_nr = 0 and hour_tarif > 0 and project_id = %d", $project_id);
		$res = sql_query($q);
		$sum1 = sql_result($res,0);

		$office_costs = $this->getFieldContent("officecosts");
		$sum1 += $sum1*($office_costs/100);

		$q = sprintf("select sum(price) from projects_declaration_registration where batch_nr = 0 and kilometers > 0 and project_id = %d", $project_id);
		$res = sql_query($q);
		$sum1 += sql_result($res,0);

		return $sum1;
	}
	public function generateDocument($req) {
		$fs_data = new Filesys_data();
		$file = $fs_data->getFileById($req["file_id"]);

		$project_data = new Project_data();
		$projectinfo = $project_data->getProjectById($req["project_id"]);

		//get free batch sequence
		$project = $this->getDeclarationByProjectId($req["project_id"]);
		$address_id = $project[$req["declaration"]["address"]];

		$address_data = new Address_data();
		$address = $address_data->getAddressById($address_id, "relations");

		$user_data = new User_data();

		/* get next batch sequence */
		$batch = sprintf("%03d", $this->getNextBatchNumber($req["project_id"]));

		$html =& $file["binary"];
		$html = str_replace("##name##", $address["companyname"], $html);
		$html = str_replace("##tav##", $address["tav"], $html);
		$html = str_replace("##address##", $address["address"], $html);
		$html = str_replace("##zipcode##", $address["zipcode"], $html);
		$html = str_replace("##city##", $address["city"], $html);

		$html = str_replace("##year##", $req["date"]["timestamp_year"], $html);
		$html = str_replace("##number##", $batch, $html);

 		$html = str_replace("##ident1##", $project["identifier"], $html);
		$html = str_replace("##ident2##", $projectinfo[0]["name"], $html);


		$fulluser = $user_data->getUserDetailsById($req["declaration"]["manager"]);
		$fulladdress = $address_data->getAddressById($fulluser["address_id"], "address_private");
		$fullname = $fulladdress["givenname"]." ".$fulladdress["surname"];

		$html = str_replace("##sendername##", $fullname, $html);
		$html = str_replace("##username##", $fullname, $html);
		$html = str_replace("##fulldate##", strftime("%a %d %b %Y"), $html);

		$fulluser = $user_data->getUserDetailsById($req["declaration"]["secretary"]);
		$fulladdress = $address_data->getAddressById($fulluser["address_id"], "address_private");
		$fullname = $fulladdress["givenname"]." ".$fulladdress["surname"];

		$html = str_replace("##secretary##", $fullname, $html);

		/*
		$html = explode("##beginfooter##", $html);
		$footer = $html[1];
		$html   = $html[0];
		*/

		$items = $this->getRegistrationItems($req["project_id"], 0);
		$items_detailed = $items;

		/* define hourslist view and map data */
		$view_hourlist = new Layout_view(1);
		$view_hourlist->addData($items_detailed);
		$view_hourlist->addMapping(gettext("date"), "%human_date", "left");
		$view_hourlist->addMapping(gettext("declaration type")." &nbsp; &nbsp;", "%declaration_type", "left");
		#$view_hourlist->addMapping(gettext("description")." &nbsp; &nbsp;", "%description", "left");
		$view_hourlist->addMapping(gettext("kilometers"), "%kilometers", "right");
		$view_hourlist->addMapping(gettext("minutes"), "%time_units", "right");
		$view_hourlist->addMapping(gettext("price ex btw"), array("&euro; ", "%price"), "right");
		$view_hourlist->addMapping(gettext("% NCNP"), "%perc_NCNP", "right");
		$view_hourlist->addMapping(gettext("% btw"), "%perc_btw", "right");
		$view_hourlist->addMapping(gettext("total price"), array("&euro; ", "%total_price"), "right");
		$details = $view_hourlist->generate_output();

		$nora_found = 0;
		foreach ($items as $k=>$v) {
			if ($v["declaration_type_plain"] == 4)
				$nora_found++;
		}

		$office_costs = $this->getFieldContent("officecosts");
		$max_btw = array_reverse(explode(",", $this->getFieldContent("BTW")));

		foreach ($items as $k=>$v) {
			if ($v["hour_tarif"]) {
				$req["hours"][$v["hour_tarif"]]["price"] += $v["total_price"];
				$req["hours"][$v["hour_tarif"]]["units"] += $v["time_units"];
				$req["total_price"] += $v["total_price"];
				$req["total_price_office"] += $v["total_price"];
				#$req["btw"][$v["perc_btw"]] += number_format($v["total_price"]*$v["perc_btw"]/100,2);
			} elseif ($v["kilometers"]) {
				$req["kilometers"]["price"] += $v["total_price"];
				$req["kilometers"]["units"] += $v["kilometers"];
				$req["total_price"] += $v["total_price"];
				#$req["btw"][$v["perc_btw"]] += number_format($v["total_price"]*$v["perc_btw"]/100,2);
			} elseif ($v["declaration_type_plain"] == 4) {
				$req["nora"] += $v["total_price"];
			} else {
				if ($v["declaration_type_plain"] == 3 && $v["perc_btw"]) {
					$req["verschotten_btw"]["price"] += $v["total_price"];
					$req["verschotten_btw"]["btw"]   += $v["total_price"]*$v["perc_btw"]/100;
					$req["verschotten_btw"]["btw"]   = number_format($v["verschotten_btw"]["btw"],2);
					$req["total_price"] += $v["total_price"];
				} elseif ($v["declaration_type_plain"] == 3) {
					$req["verschotten_ex"]["price"] += $v["total_price"];
				}
			}
		}
		$calc = "<table cellspacing='0' cellpadding='0'>";
		if ($req["nora"] > 0) {
			$c = $req["nora"];
			$nora_btw = $c * $max_btw[0]/100;

			$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
				gettext("conform afspraak"), number_format($c,2));
			$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
				gettext("BTW over NORA tariff"), number_format($nora_btw,2));
			$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
			$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
				gettext("totaal"), number_format($c + $nora_btw,2));
			$calc.= "<tr><td>&nbsp;</td></tr>";
		}

		if (count($req["hours"]) > 0) {
			foreach ($req["hours"] as $k=>$c) {
				$calc.= sprintf("<tr><td>%d %s x &euro; %s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
					$c["units"], gettext("min"), number_format($k,2), number_format($c["price"],2));
			}
		}
		if (count($req["kilometers"]) > 0) {
			$c = $req["kilometers"];
			$calc.= sprintf("<tr><td>%d %s x &euro; %s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
				$c["units"], gettext("kilometers"), number_format($c["price"]/$c["units"], 2), number_format($c["price"],2));
		}
		if (count($req["verschotten_btw"]["price"]) > 0) {
			$c = $req["verschotten_btw"];
			$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
				gettext("belaste verschotten"), number_format($c["total_price"], 2));
		}
		if ($req["total_price"]) {
			$office_costs_total = number_format($req["total_price_office"]*$office_costs/100, 2);
			$office_costs_btw   = $max_btw[0]*$office_costs_total/100;
			#$req["btw"][$max_btw[0]] += $office_costs_btw;

			$calc.= sprintf("<tr><td>%s %d%%</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
				gettext("office costs"), $office_costs, $office_costs_total);

			$req["total_price"] = $req["total_price"]*($office_costs/100+1);
			$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
			$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
				gettext("subtotaal"), number_format($req["total_price"], 2));

			/* get voorschotten (NORA) */
			if ((int)$batch == 2) {
				$nora = $this->getRemainingNora($req["project_id"]);
			}
			if ($nora > 0) {
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
					gettext("betaalde voorschotten"), $nora);
				if ($req["total_price"] > $nora) {
					$req["total_price"]-=$nora;
					$q = sprintf("update projects_declaration_registration set remaining_nora = -1 where project_id = %d", $req["project_id"]);
					#sql_query($q);
				} else {
					$nora-=$req["total_price"];
					$req["total_price"] = number_format(0,2);
					$q = sprintf("update projects_declaration_registration set remaining_nora = %s where project_id = %d", $nora, $req["project_id"]);
					#sql_query($q);
				}
				$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
					gettext("subtotaal"), $req["total_price"]);
			}
			$req["total_btw"] = $max_btw[0] * $req["total_price"] / 100;
			$calc.= sprintf("<tr><td>%s%% %s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
				$max_btw[0], gettext("BTW"), number_format($req["total_btw"],2));

			$req["total_price"]+= $req["total_btw"];

			if ($req["verschotten_ex"]["price"] > 0) {
				$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
					gettext("subtotaal"), number_format($req["total_price"],2));

				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
					gettext("onbelaste verschotten"), number_format($req["verschotten_ex"]["price"],2));
				$req["total_price"] += $req["verschotten_ex"]["price"];
			}

			$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
			$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
				gettext("totaal"), number_format($req["total_price"], 2));
		}

		$calc.= "</table>";
		$html = str_replace("##calculation##", $calc, $html);
		$html = str_replace("##details##", $details, $html);


		require_once('classes/html2pdf/pdf.php');

		$dir = $GLOBALS["covide"]->temppath;
		$file = $dir."pdf_".md5(mktime()*rand()).".html";
		$pdf = $dir."pdf_".md5(mktime()*rand()).".pdf";

		$ids = array(0);
		foreach ($items as $k=>$v) {
			$ids[]=$v["id"];
		}
		#echo $html;
		#die();

		$q = sprintf("update projects_declaration_registration set batch_nr = %d where id IN (%s)",
			$batch, implode(",", $ids));
		#sql_query($q);

		file_put_contents($file, $html);

		createPdf($file, $_SERVER["SERVER_NAME"], $pdf, "center", $footer);

		#file download
		header('Content-Transfer-Encoding: binary');
		header('Content-Type: application/pdf');

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename="covide_template.pdf"'); //msie 5.5 header bug
		} else {
			header('Content-Disposition: attachment; filename="covide_template.pdf"');
		}

		$handle = fopen ($pdf, "r");
		$pdfdata = fread ($handle, filesize ($pdf));
		fclose ($handle);

		print($pdfdata);

		@unlink($file);
		@unlink($pdf);

		exit();
	}
	public function getRemainingNora($project_id) {
		$q = sprintf("select * from projects_declaration_registration where declaration_type = 4 and project_id = %d and batch_nr = 1 order by timestamp desc limit 1", $project_id);
		$res = sql_query($q);
		if (sql_num_rows($res)==1) {
			$row = sql_fetch_assoc($res);
			return $row["price"];
		} else {
			return 0;
		}
	}
	public function getFolderId() {
		$q = sprintf("select id from filesys_folders where (user_id = 0 or user_id is null)
			and (parent_id = 0 or parent_id is null) and name = '%s'",
			"declaration templates");
		$res = sql_query($q);
		return sql_result($res, 0);
	}
	public function getFolderDocuments() {
		$id = $this->getFolderId();
		$fs_data = new Filesys_data();
		$data = $fs_data->getFiles(array("folderid" => $id));
		foreach ($data as $k=>$v) {
			if (!preg_match("/\.htm(l){0,1}$/si", $v["name"]))
				unset($data[$k]);
		}
		return $data;
	}

	public function getNextBatchNumber($project_id) {
		$q = sprintf("select max(batch_nr) from projects_declaration_registration where project_id = %d and batch_nr > 0", $project_id);
		$res = sql_query($q);
		$batch = (int)(sql_result($res,0)+1);
		return $batch;
	}
	public function getRegistrationHistory($project_id) {
		$data = array(
			0 => "not declared"
		);
		$q = sprintf("select batch_nr from projects_declaration_registration where project_id = %d and batch_nr > 0", $project_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[$row["batch_nr"]] = gettext("declaration")." ".sprintf("%03d", $row["batch_nr"]);
		}
		return $data;
	}

	public function getRegistrationItems($project_id, $status=0) {
		$data = array();
		$user_data = new User_data();

		$q = sprintf("select * from projects_declaration_registration where batch_nr = %d and project_id = %d order by timestamp desc", $status, $project_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["human_date"]       = date("d-m-Y", $row["timestamp"]);
			$row["total_price"]      = number_format($row["price"] + ($row["price"] * ($row["perc_btw"]/100)), 2, ".", "");
			$row["declaration_type_plain"] = $row["declaration_type"];
			$row["declaration_type"] = $this->declaration_types[$row["declaration_type"]];
			$row["user_name"]        = $user_data->getUserNameById($row["user_id"]);
			$row["perc_NCNP"]        = number_format($row["perc_NCNP"]);
			$row["user_name_input"]  = $user_data->getUserNameById($row["user_id_input"]);

			$row = preg_replace("/^0$/s", "", $row);
			$data[]=$row;
		}
		return $data;
	}

	public function saveRegistration($req) {
		switch ($req["declaration"]["declaration_type"]) {
			case 1:
				/* calculate price ex btw */
				$q = sprintf("select tarif from hours_activities where id = %d", $req["declaration"]["hour_tarif"]);
				$res = sql_query($q);
				$tarif = sql_result($res,0);

				$req["declaration"]["activity_id"] = $req["declaration"]["hour_tarif"];
				$req["declaration"]["hour_tarif"] = $tarif;
				$req["declaration"]["price"] = ($tarif/60) * $req["declaration"]["time_units"];
				$req["declaration"]["kilometers"] = 0;
				$req["declaration"]["perc_NCNP"] = 0;
				break;
			case 2:
				$kilometer_price = $this->getFieldContent("kilometerstarif");
				$kilometer_price/=100; //in eurocents

				$req["declaration"]["hour_tarif"] = 0;
				$req["declaration"]["price"] = $req["declaration"]["kilometers"] * $kilometer_price;
				$req["declaration"]["btw"] = 0;
				$req["declaration"]["time_units"] = 0;
				$req["declaration"]["perc_NCNP"] = 0;
				break;
			default:
				$req["declaration"]["hour_tarif"] = 0;
				$req["declaration"]["kilometers"] = 0;
				$req["declaration"]["time_units"] = 0;
				break;
		}

		$fields["project_id"]       = array("d", $req["project_id"]);
		$fields["declaration_type"] = array("d", $req["declaration"]["declaration_type"]);
		$fields["hour_tarif"]       = array("f", $req["declaration"]["hour_tarif"]);
		$fields["activity_id"]      = array("f", $req["declaration"]["activity_id"]);
		$fields["time_units"]       = array("d", $req["declaration"]["time_units"]);
		$fields["kilometers"]       = array("d", $req["declaration"]["kilometers"]);
		$fields["perc_btw"]         = array("f", $req["declaration"]["btw"]);
		$fields["price"]            = array("f", $req["declaration"]["price"]);
		$fields["description"]      = array("s", $req["declaration"]["description"]);
		$fields["user_id"]          = array("d", $req["declaration"]["user_id"]);
		$fields["user_id_input"]    = array("d", $_SESSION["user_id"]);
		$fields["timestamp"]        = array("d", mktime(0,0,0, $req["date"]["timestamp_month"], $req["date"]["timestamp_day"], $req["date"]["timestamp_year"]));
		$fields["perc_NCNP"]        = array("f", $req["declaration"]["perc_NCNP"]);

		$vals = array();
		foreach ($fields as $k=>$v) {
			if ($v[0]=="s") {
				//addslashes already done
				$vals[$k]="'".$v[1]."'";
			} elseif ($v[0]=="f") {
				$vals[$k]=(float)$v[1];
			} else {
				$vals[$k]=(int)$v[1];
			}
		}
		foreach ($vals as $k=>$v) {
			$keys[]=$k;
		}
		$q = sprintf("insert into projects_declaration_registration (%s) values (%s)",
			implode(",", $keys), implode(",", $vals));
		sql_query($q);
		#echo $q;
		#die();

		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("location.href='?mod=project&action=showhours&id=%d&master=0';", $req["project_id"]));
		$output->end_javascript();
		$output->exit_buffer();


	}

	public function getFieldContent($type, $array=0) {
		$data = $this->getOptionsByType($type, 1);
		$ret = array();
		if (is_array($data)) {
			foreach ($data as $k=>$v) {
				$ret[]=$v;
			}
		}
		if ($array)
			return $ret;
		else
			return implode(",", $ret);
	}

	public function saveMulti($req) {
		foreach ($req["value"] as $k=>$v) {
			if ($k < 0) {
				/* insert */
				if ($v !== "") {
					$esc = sql_syntax("escape_char");
					$q = sprintf("insert into projects_declaration_options (%1\$sgroup%1\$s, %1\$sname%1\$s) values ('%2\$s', '%3\$s')", $esc, $req["type"], $v);
					sql_query($q);
				}
			} else {
				if ($v !== "") {
					$q = sprintf("update projects_declaration_options set %1\$sname%1\$s = '%2\$s' where id = %3\$d", $esc, $v, $k);
					sql_query($q);
				} else {
					$q = sprintf("delete from projects_declaration_options where id = %d", $k);
					sql_query($q);
				}
			}
		}
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode("location.href='?mod=projectdeclaration&action=start';");
		$output->end_javascript();
		$output->exit_buffer();

	}

	public function saveOne($req) {
		$esc = sql_syntax("escape_char");
		$q = sprintf("update projects_declaration_options set %1\$sname%1\$s = '%2\$s' where %1\$sgroup%1\$s = '%3\$s'",
			$esc, (float)$req["value"], $req["type"]);
		sql_query($q);

		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode("location.href='?mod=projectdeclaration&action=start';");
		$output->end_javascript();
		$output->exit_buffer();

	}

	public function checkOption($type) {
		$esc = sql_syntax("escape_char");
		$q = sprintf("select count(*) from projects_declaration_options where %sgroup%s = '%s'",
			$esc, $esc, $type);
		$res = sql_query($q);
		if (sql_result($res,0)==0) {
			$q = sprintf("insert into projects_declaration_options (%1\$sgroup%1\$s) values ('%2\$s')", $esc, $type);
			sql_query($q);
		}
	}

	public function getOptionsByType($type, $plain=0, $skip_empty=0) {
		if ($plain)
			$skip_empty = 1;

		if (!$skip_empty) {
			$data = array(
				"0" => "- ".gettext("choose")." -"
			);
		}
		$data = array();
		$esc = sql_syntax("escape_char");
		$q = sprintf("select * from projects_declaration_options where %sgroup%s = '%s' order by name", $esc, $esc, $type);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($plain)
				$data[]=$row["name"];
			else
				$data[$row["id"]]=$row["name"];
		}
		natcasesort($data);
		return $data;
	}


	public function getAccidents() {
		return $this->getOptionsByType("accident_type");
	}
	public function getTarifs() {
		//return $this->getOptionsByType("tarifs");
		$data = array();
		$q = "select * from hours_activities order by activity";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[$row["id"]] = sprintf("%s (%s)", $row["activity"], $row["tarif"]);
		}
		return $data;
	}
	public function getLesions() {
		return $this->getOptionsByType("lesion");
	}

	public function saveProjectFields($projectid, $req) {
		/*
		"id","project_id","task_date","damage_date","accident_type","perc_liabilities_wished",
		"perc_liabilities_recognised","constituent","tarif","is_NCNP","perc_NCNP","client",
		"adversary","expertise","lesion","lesion_description","hospitalisation",
		"incapacity_for_work","profession","employment"
		*/
		$fields["task_date"] = array("d", mktime(0,0,0,
			$req["task_date"]["timestamp_month"],
			$req["task_date"]["timestamp_day"],
			$req["task_date"]["timestamp_year"]));
		$fields["damage_date"] = array("d", mktime(0,0,0,
			$req["damage_date"]["timestamp_month"],
			$req["damage_date"]["timestamp_day"],
			$req["damage_date"]["timestamp_year"]));

		$fields["accident_type"]               = array("d", $req["declaration"]["accident_type"]);
		$fields["perc_liabilities_wished"]     = array("f", $req["declaration"]["perc_liabilities_wished"]);
		$fields["perc_liabilities_recognised"] = array("f", $req["declaration"]["perc_liabilities_recognised"]);
		$fields["constituent"]                 = array("d", $req["declaration"]["constituent"]);
		$fields["tarif"]                       = array("d", $req["declaration"]["tarif"]);
		$fields["client"]                      = array("d", $req["declaration"]["client"]);
		$fields["adversary"]                   = array("d", $req["declaration"]["adversary"]);
		$fields["expertise"]                   = array("d", $req["declaration"]["expertise"]);
		$fields["lesion"]                      = array("d", $req["declaration"]["lesion"]);
		$fields["lesion_description"]          = array("s", $req["declaration"]["lesion_description"]);
		$fields["hospitalisation"]             = array("d", $req["declaration"]["hospitalisation"]);
		$fields["employment"]                  = array("s", $req["declaration"]["employment"]);
		$fields["profession"]                  = array("d", $req["declaration"]["profession"]);
		$fields["incapacity_for_work"]         = array("d", $req["declaration"]["incapacity_for_work"]);
		$fields["identifier"]                  = array("s", $req["declaration"]["identifier"]);

		$vals = array();
		foreach ($fields as $k=>$v) {
			if ($v[0]=="s") {
				//addslashes already done
				$vals[$k]="'".$v[1]."'";
			} elseif ($v[0]=="f") {
				$vals[$k]=(float)$v[1];
			} else {
				$vals[$k]=(int)$v[1];
			}
		}
		$q = sprintf("select count(*) from projects_declaration_extrainfo where project_id = %d", $projectid);
		$res = sql_query($q);
		if (sql_result($res,0) > 0) {
			$q = sprintf("update projects_declaration_extrainfo set project_id = %d", $projectid);
			foreach ($vals as $k=>$v) {
				$q.= sprintf(", %s = %s", $k, $v);
			}
			$q.= sprintf(" where project_id = %d ", $projectid);
			sql_query($q);
		} else {
			foreach ($vals as $k=>$v) {
				$keys[]=$k;
			}
			$keys[]="project_id"; $vals[]=$projectid;
			$q = sprintf("insert into projects_declaration_extrainfo (%s) values (%s)",
				implode(",", $keys), implode(",", $vals));
			sql_query($q);
		}
	}

	public function getDeclarationByProjectId($project_id) {
		$q = sprintf("select * from projects_declaration_extrainfo where project_id = %d", $project_id);
		$res = sql_query($q);
		if (sql_num_rows($res)>0) {
			$row = sql_fetch_assoc($res);
		} else {
			$row = array();
		}
		return $row;
	}

	public function deleteRegistration($id) {
		$q = sprintf("select * from projects_declaration_registration where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$q = sprintf("delete from projects_declaration_registration where id = %d", $id);
		sql_query($q);

		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(
				sprintf("location.href = 'index.php?mod=project&action=showhours&id=%d&master=0';", $row["project_id"])
			);
		$output->end_javascript();
		$output->exit_buffer();

	}
}
?>
