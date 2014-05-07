<?php
/**
 * Covide ProjectDeclaration module
 *
 * This module has been build based on demands of a specific customer.
 * That's why there are some Dutch text strings in here, and it's based on
 * the Dutch low etc. I dont know how global it is and/or who can use it besides
 * this one customer.
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */

Class ProjectDeclaration_data {
	/* constants */
	const include_dir = "classes/projectedeclaration/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "projectdeclaration";

	public $declaration_types;

	public function __construct() {
		$this->declaration_types =  array(
			1 => gettext("hours"),
			2 => gettext("kilometers"),
			3 => gettext("verschotten"),
			4 => gettext("fixed tarif"),
			5 => gettext("NCNP")
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

		switch ($req["declaration"]["address"]) {
			case "client":
				$project["selected_identifier"] = "";
				break;
			case "constituent":
				$project["selected_identifier"] = $project["identifier"];
				break;
			case "expertise":
				$project["selected_identifier"] = $project["identifier_expertise"];
				break;
			case "adversary":
				$project["selected_identifier"] = $project["identifier_adversary"];
				break;
		}

		$address_id = $project[$req["declaration"]["address"]];

		$address_data = new Address_data();
		$address = $address_data->getAddressById($address_id, "relations");

		/* detect bcard usage */
		$bcard_id = $project["bcard_".$req["declaration"]["address"]];
		if ($bcard_id > 0) {
			$bcard = $address_data->getAddressById($bcard_id, "bcards");
			/* some mappings */
			$bcard["contact_commencement"] = $bcard["commencement"];
			$bcard["contact_letterhead"]   = $bcard["letterhead"];
			$bcard["contact_initials"]     = $bcard["initials"];
			$bcard["contact_givenname"]    = $bcard["givenname"];
			$bcard["contact_surname"]      = $bcard["surname"];
			$bcard["contact_infix"]        = $bcard["infix"];

			$btav = $address_data->generate_letterinfo($bcard);
			if (trim($btav["tav"]))
				$address["tav"] = trim($btav["tav"]);
		}

		$user_data = new User_data();

		/* get next batch sequence */
		$batch = sprintf("%03d", $this->getNextBatchNumber($req["project_id"]));
		$totalbatchthisyear = $this->getTotalBatchByYear($req["date"]["timestamp_year"]);
		$totalbach = $this->getTotalBatchByYear(0);

		/* TODO: Perhaps we could make a function to attach every field to a ##(fieldname)## so we can easily scale this table in the DB */
		$textboolean = array(
						"0" => gettext("No"),
						"1" => gettext("Yes"),
						"2" => gettext("unknown")
					);
		$html =& $file["binary"];
		
		$output = new Layout_output();
		$output->addTag("br");
		$br = $output->generate_output();
	
		if ($address["is_person"]) {
			$html = str_replace("##name##", "", $html);
			$html = str_replace("##tav##", htmlentities($address["tav"], ENT_NOQUOTES, "UTF-8").$br, $html);
		} else {
			$html = str_replace("##name##", htmlentities($address["companyname"], ENT_NOQUOTES, "UTF-8").$br, $html);
			$html = str_replace("##tav##", "t.a.v. ".htmlentities($address["tav"], ENT_NOQUOTES, "UTF-8").$br, $html);
		}
		$accidents = $this->getAccidents();
		$html = str_replace("##taskdate##", date("d-m-Y", $project["task_date"]).$br, $html);
		$html = str_replace("##damage_date##", date("d-m-Y", $project["damage_date"]).$br, $html);
		$html = str_replace("##accident_type##", htmlentities($accidents[$project["accident_type"]], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##accident_description##", htmlentities($projectinfo[0]["description"], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##lesion_description##", htmlentities($project["lesion_description"], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##perc_liabilities_wished##", htmlentities($project["perc_liabilities_wished"], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##perc_liabilities_recognized##", $project["perc_liabilities_recognised"].$br, $html);
		$html = str_replace("##agreements##", htmlentities($project["agreements"], ENT_NOQUOTES, "UTF-8").$br, $html);
		
		$address_data = new Address_data();
		
		$cons = $address_data->getAddressByID($project["bcard_constituent"], "bcards");
		$html = str_replace("##constituent##", htmlentities($address_data->getAddressNameByID($project["constituent"]), ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##constituent_ident##", htmlentities($project["identifier"], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##constituent_bcard##", htmlentities($cons["fullname"], ENT_NOQUOTES, "UTF-8").$br, $html);
		
		$adve = $address_data->getAddressByID($project["bcard_adversary"], "bcards");
		$html = str_replace("##adversary##", htmlentities($address_data->getAddressNameByID($project["adversary"]), ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##adversary_ident##", htmlentities($project["identifier_adversary"], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##adversary_bcard##", htmlentities($adve["fullname"], ENT_NOQUOTES, "UTF-8").$br, $html);
		
		$expe = $address_data->getAddressByID($project["bcard_expertise"], "bcards");
		$html = str_replace("##expertise##", htmlentities($address_data->getAddressNameByID($project["expertise"]), ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##expertise_ident##", htmlentities($project["identifier_expertise"], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##expertise_bcard##", htmlentities($expe["fullname"], ENT_NOQUOTES, "UTF-8").$br, $html);
		
		$html = str_replace("##address##", htmlentities($address["address"], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##zipcode##", htmlentities($address["zipcode"], ENT_NOQUOTES, "UTF-8"), $html);
		$html = str_replace("##city##", htmlentities($address["city"], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##phone_nr##", $address["phone_nr"].$br, $html);
		$html = str_replace("##mobile_nr##", $address["mobile_nr"].$br, $html);
		$html = str_replace("##email##", $address["email"].$br, $html);
		$html = str_replace("##dob##", date("d-m-Y", $address["contact_birthday"]).$br, $html);
		$html = str_replace("##bankrekening_nr##", $address["bankaccount"].$br, $html);
		
		$profession = array(
						"0" => gettext("paid employment"),
						"1" => gettext("independent"),
						"2" => gettext("unknown"),
						"3" => gettext("student")
						);
		$html = str_replace("##profession##", htmlentities($profession[$project["profession"]], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##incapacity_for_work##", $textboolean[$project["incapacity_for_work"]].$br, $html);
		$html = str_replace("##employment##", htmlentities($project["employment"], ENT_NOQUOTES, "UTF-8").$br, $html);
		
		$lesion = $this->getLesions();
		$html = str_replace("##lesion##", htmlentities($lesion[$project["lesion"]], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##lesion_description##", htmlentities($project["lesion_description"], ENT_NOQUOTES, "UTF-8").$br, $html);
		$html = str_replace("##hospitalisation##", $textboolean[$project["hospitalisation"]].$br, $html);
		
		$html = str_replace("##year##", $req["date"]["timestamp_year"], $html);
		$html = str_replace("##number##", $batch, $html);
		$html = str_replace("##totalnumber##", ($totalbach+12), $html);
		if (date("Y") == "2007")
			$html = str_replace("##totalnumberthisyear##", ($totalbatchthisyear+12), $html);
		else
			$html = str_replace("##totalnumberthisyear##", $totalbatchthisyear, $html);

		$html = str_replace("##dossier_nr##", $project["project_id"], $html);
 		$html = str_replace("##ident1##", $project["selected_identifier"], $html);
		$html = str_replace("##ident2##", $projectinfo[0]["name"], $html);

		$fulluser = $user_data->getUserDetailsById($req["declaration"]["manager"]);
		$fulladdress = $address_data->getAddressById($fulluser["address_id"], "address_private");
		if (!$fulladdress["fullname"])
			$fullname = $fulladdress["givenname"]." ".$fulladdress["surname"];
		else
			$fullname = $fulladdress["fullname"];

		$html = str_replace("##sendername##", htmlentities($fullname, ENT_NOQUOTES, "UTF-8"), $html);
		$html = str_replace("##username##", htmlentities($fullname, ENT_NOQUOTES, "UTF-8"), $html);
		$html = str_replace("##fulldate##", strftime("%d %B %Y"), $html);

		$fulluser = $user_data->getUserDetailsById($req["declaration"]["secretary"]);
		$fulladdress = $address_data->getAddressById($fulluser["address_id"], "address_private");
		if (!$fulladdress["fullname"])
			$fullname = $fulladdress["givenname"]." ".$fulladdress["surname"];
		else
			$fullname = $fulladdress["fullname"];

		$html = str_replace("##secretary##", htmlentities($fullname, ENT_NOQUOTES, "UTF-8"), $html);

		if ($_REQUEST["declaration"]["filter"] == "mincalc") {
			$items = $this->getRegistrationItems($req["project_id"], 0, 1);
		} else {
			$items = $this->getRegistrationItems($req["project_id"], 0);
		}
		foreach ($items as $k=>$v) {

			/* remove thousand seperator */
			$items[$k]["total_price"] = str_replace(",", "", $v["total_price"]);
			/* htmlentity encode description */
			$items[$k]["description"] = htmlentities(utf8_decode($v["description"]));

			if ($v["declaration_type_plain"] == 5) {
				$ncnp++;
				$ncnp_record = $v;
			}
		}
		$items_detailed = $items;

		if ($ncnp) {
			$fee = ($ncnp_record["perc_NCNP"]/100) * $ncnp_record["price"];
			$fee_btw = ($ncnp_record["perc_btw"]/100) * $fee;
			$fee_total = $fee + $fee_btw;

			$details = "";
			$calc = "<table cellspacing='0' cellpadding='0' width='80%'>";
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("Honorarium"), number_format($fee,2, ",", "."));
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("19 % VAT"), number_format($fee_btw,2, ",", "."));

				$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("totaal"), number_format($fee_total,2, ",", "."));

			$calc.= "</table>";

			$html = preg_replace("/<br style=\"page-break: after;\">(.*)$/si", "</body></html>", $html);

		} else {

			/* ===================================== normal calculation ======================= */
			/* define hourslist view and map data */
			$view_hourlist = new Layout_view(1);

			$view_hourlist->addData($items_detailed);
			$view_hourlist->addMapping(gettext("date"), "%human_date", "left");
			$view_hourlist->addMapping(gettext("description")." &nbsp;&nbsp;", "%description", "left");
			$view_hourlist->addMapping(gettext("km"), "%kilometers", "right");
			$view_hourlist->addMapping(gettext("min"), "%time_units", "right");
			$view_hourlist->addMapping("", "&euro;&nbsp;", "right");
			$view_hourlist->addMapping(gettext("price ex vat"), "%price", "right");
			$view_hourlist->addMapping(gettext("% vat"), "%perc_btw", "right");
			$view_hourlist->addMapping(" ", "&euro;&nbsp;", "right");
			$view_hourlist->addMapping(gettext("total price"), "%total_price", "right");

			$details = $view_hourlist->generate_output();

			$nora_found = 0;
			foreach ($items as $k=>$v) {
				if ($v["declaration_type_plain"] == 4)
					$nora_found++;
				if ($v["declaration_type_plain"] == 5)
					$ncnp_found++;
			}

			$office_costs = $this->getFieldContent("officecosts");
			$max_btw = array_reverse(explode(",", $this->getFieldContent("BTW")));
			$dectype4 = array();
			foreach ($items as $k=>$v) {
				if (floor($v["hour_tarif"])) {
					$req["hours"][$v["hour_tarif"]]["price"] += $v["price"];
					$req["hours"][$v["hour_tarif"]]["units"] += $v["time_units"];
					$req["total_ext"] += $v["price"];
					$req["total_price"] += $v["total_price"];
					$req["total_price_office_ext"] += $v["price"];
					$req["total_price_office"] += $v["total_price"];
				} elseif ($v["kilometers"]) {
					$req["kilometers"]["price"] += $v["price"];
					$req["kilometers"]["units"] += $v["kilometers"];
					$req["total_ext"] += $v["price"];
					$req["total_price"] += $v["total_price"];
				} elseif ($v["declaration_type_plain"] == 4) {
					$dectype4[] = array(
						"desc"  => $v["description"],
						"price" => $v["price"],
						"btw"   => ($v["total_price"] - $v["price"]),
						"total" => $v["total_price"]
					);
					$req["nora"]["price"] += $v["price"];
					$req["nora"]["btw"]   += ($v["total_price"]-$v["price"]);
					$req["nora"]["total"] += $v["total_price"];
				} else {
					if ($v["declaration_type_plain"] == 3 && $v["perc_btw"]) {
						$req["verschotten_btw"]["price"] += $v["price"];
						$req["verschotten_btw"]["btw"]   += $v["price"]*$v["perc_btw"]/100;
					} elseif ($v["declaration_type_plain"] == 3) {
						$req["verschotten_ex"]["price"] += $v["price"];
					}
				}
			}
			$calc = "<table cellspacing='0' cellpadding='0' width='80%'>";
			if (count($req["hours"]) > 0) {
				foreach ($req["hours"] as $k=>$c) {
					$calc.= sprintf("<tr><td>%d %s x &euro; %s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
						$c["units"], gettext("minuten"), number_format($k,2, ",", "."), number_format($c["price"],2, ",", "."));
				}
			}
			if (count($req["kilometers"]) > 0) {
				$c = $req["kilometers"];
				$calc.= sprintf("<tr><td>%d %s x &euro; %s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					$c["units"], gettext("kilometers"), number_format($c["price"]/$c["units"], 2, ",", "."), number_format($c["price"],2, ",", "."));
			}

			if ($req["total_price"]) {
				$office_costs_total = $req["total_price_office_ext"]*$office_costs/100;
				$office_costs_btw   = $max_btw[0]*$office_costs_total/100;

				$calc.= sprintf("<tr><td>%s %d%%</td><td>&nbsp; &euro; &nbsp;<td align='right'>%s</td></tr>",
					gettext("kantoorkosten"), $office_costs, number_format($office_costs_total, 2, ",", "."));
				$req["total_ext"] += $office_costs_total;
				$ocosts = $req["total_ext"];
				$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("subtotal"), number_format($req["total_ext"], 2, ",", "."));
				if (count($req["verschotten_btw"]["price"]) > 0) {
					$c = $req["verschotten_btw"];
					$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
						gettext("taxable (out of pocket) expenses"), number_format($c["price"], 2, ",", "."));
				}
				/* get voorschotten (NORA) */
				if ((int)$batch == 2) {
					$nora = $this->getRemainingNora($req["project_id"]);
				}
				if ($nora > 0) {
					$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
						gettext("betaalde voorschotten"), $nora);
					//XXX: huh? Why not run those ?
					if ($req["total_ext"] > $nora) {
						$req["total_ext"]-=$nora;
						$q = sprintf("update projects_declaration_registration set remaining_nora = -1 where project_id = %d", $req["project_id"]);
						#sql_query($q);
					} else {
						$nora-=$req["total_ext"];
						$q = sprintf("update projects_declaration_registration set remaining_nora = %s where project_id = %d", $nora, $req["project_id"]);
						#sql_query($q);
					}
					$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
					$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
						gettext("subtotal"), number_format($req["total_ext"], 2, ",", "."));
				}
				$req["total_btw"] = $max_btw[0] * $req["total_ext"] / 100 + $req["verschotten_btw"]["btw"];
				$req["total_btw"] = round($req["total_btw"], 2);
				$req["total_ext"] = round($req["total_ext"], 2);
				$calc.= sprintf("<tr><td>%s%% %s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					$max_btw[0], gettext("VAT"), number_format($req["total_btw"],2, ",", "."));

				$req["total_price"] = $req["total_ext"]+$req["total_btw"] + round($req["verschotten_btw"]["price"], 2);

				if ($req["verschotten_ex"]["price"] > 0) {
					$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
					$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
						gettext("subtotal"), number_format($req["total_price"],2, ",", "."));

					$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
						gettext("non-taxable (out of pocket) expenses"), number_format($req["verschotten_ex"]["price"],2, ",", "."));
					$req["total_price"] += $req["verschotten_ex"]["price"];
				}

				$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("total"), number_format($req["total_price"], 2, ",", "."));
				$total_price_print = $calc."</table>";
			}

			if (count($req["nora"]) > 0) {
				$calc = "<table cellspacing='0' cellpadding='0' width='80%'>";
				$c = $req["nora"]["price"];
				$nora_btw = $req["nora"]["btw"];
				$nora_totaal = $req["nora"]["total"];

				$i = 1;
				$subtotal = 0;
				foreach ($dectype4 as $v) {
					if (substr($v["desc"], 0, 3) == "PIV" || substr($v["desc"], 0, 3) == "hon") {
						$subtotal += $v["price"];
						$calc_eigen .= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
							htmlentities($v["desc"], ENT_NOQUOTES, "UTF-8"), number_format($v["price"], 2, ",", "."));
					} else {
						if ($v["btw"]) {
							$calc_noneigen .= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
								htmlentities($v["desc"], ENT_NOQUOTES, "UTF-8"), number_format($v["price"], 2, ",", "."));
						} else {
							$calc_nonbtw .= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
								htmlentities($v["desc"], ENT_NOQUOTES, "UTF-8"), number_format($v["price"], 2, ",", "."));
						}
					}
				}
				$ncosts = $subtotal;
				$calc .= $calc_eigen;
				$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
				$calc .= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("subtotal"), number_format($subtotal, 2, ",", "."));
				$calc .= $calc_noneigen;
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("VAT"), number_format($nora_btw,2, ",", "."));
				$calc .= $calc_nonbtw;
				$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("total"), number_format($nora_totaal,2, ",", "."));
				$fixed_tarif_print = $calc."</table>";
				$calc = "<table cellspacing='0' cellpadding='0' width='80%'>";

				/* nora totals */
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("calculation total"), number_format($ocosts, 2, ",", "."));
				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("fixed tarif total"), number_format($ncosts, 2, ",", "."));
				$calc.= "<tr><td colspan='2'></td><td><hr></td></tr>";

				$calc.= sprintf("<tr><td>%s</td><td>&nbsp; &euro; &nbsp;</td><td align='right'>%s</td></tr>",
					gettext("total"), number_format($ocosts - $ncosts,2, ",", "."));
				$grand_total_print = $calc."</table>";
			}
		}

		$custom_font = $this->getFieldContent("font");
		if ($custom_font)
			$custom_font = explode("|", $custom_font);
		else
			$custom_font = array();

		$font["face"] = $custom_font[0];
		$font["size"] = $custom_font[1];

		$font["start"] = sprintf("<font face='%s' size='%d'>", $font["face"], $font["size"]);
		$font["end"]   = "</font>";

		/* quick and dirty insert some font definitions for pdf generation */
		$details = preg_replace("/(<t(d|h)[^>]*?>)/si", "$1<font face='".$font["face"]."' size='".$font["size"]."'>", $details);
		$details = preg_replace("/(<\/t(d|h)[^>]*?>)/si", "</font>$1", $details);
		$details = preg_replace("/<th[^>]*?>/si", "<td>", $details);
		$details = preg_replace("/<\/th[^>]*?>/si", "</td>", $details);

		$total_print_old = $total_price_print." ".$fixed_tarif_print." ".$grand_total_print;
		$html = str_replace("##calculation##", $total_print_old, $html);
		$html = str_replace("##calculation_1##", $total_price_print, $html);
		$html = str_replace("##calculation_2##", $fixed_tarif_print, $html);
		$html = str_replace("##calculation_3##", $grand_total_print, $html);

		$html = str_replace("##details##", $details, $html);

		$html = str_replace(
			array(
				"##fontface##",
				"##fontsize##",
				"##fontstart##",
				"##fontend##"
			),
			array(
				$font["face"],
				$font["size"],
				$font["start"],
				$font["end"]
			),
			$html
		);

		require_once('classes/html2pdf/pdf.php');

		$dir = $GLOBALS["covide"]->temppath;
		$file = $dir."pdf_".md5(time()*rand()).".html";
		$pdf = $dir."pdf_".md5(time()*rand()).".pdf";

		$ids = array(0);
		foreach ($items as $k=>$v) {
			$ids[]=$v["id"];
		}

		if ($_REQUEST["declaration"]["filter"] != "nocalc") {
			if ($ncnp) {
				$q = sprintf("update projects_declaration_registration set batch_nr = %d where declaration_type = 5 and batch_nr = 0",
					$batch, implode(",", $ids));
			} else {
				$q = sprintf("update projects_declaration_registration set batch_nr = %d where id IN (%s)",
					$batch, implode(",", $ids));
			}
			sql_query($q);
			// update batchcounter
			if ($totalbatchthisyear > 1) {
				// we already have something this year so update the database table
				$q = sprintf("UPDATE projects_declaration_batchcounter SET batchcounter = %d WHERE year = %d", $totalbatchthisyear, date("Y"));
			} else {
				// first one this year, so insert
				$q = sprintf("INSERT INTO projects_declaration_batchcounter VALUES (%d, %d)", date("Y"), $totalbatchthisyear);

			}
			sql_query($q);
		}

		file_put_contents($file, $html);

		createPdf($file, $_SERVER["SERVER_NAME"], $pdf, "center", $footer);

		$handle = fopen ($pdf, "r");
		$pdfdata = fread ($handle, filesize ($pdf));
		fclose ($handle);

		/* get the project folder in filesys */
		$filesys_data = new Filesys_data();
		$filesysfolder = $filesys_data->getProjectFolder($req["project_id"]);

		/* this is very specific custom code, so for now i copy the code */
		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir  = "bestanden";

		$output = new Layout_output();

		/* gather some file info */
		$type = $filesys_data->detectMimetype($pdf);
		$size = filesize($pdf);

		$name = sprintf("declaration_%03d_%s.pdf", $batch, date("d-m-Y_H:i"));
		$name = $filesys_data->checkDuplicates($name, $filesysfolder);

		if ($_REQUEST["declaration"]["filter"] != "nocalc") {
			/* insert file into dbase */
			$q = "insert into filesys_files (folder_id, name, size, type, timestamp, user_id) values ";
			$q.= sprintf("(%d, '%s', '%s', %d, %d, '%s')", $filesysfolder, $name, $size, $type, time(), $_SESSION["user_id"]);
	
			sql_query($q);
			$new_id = sql_insert_id("filesys_files");
	
			/* move data to the destination */
			$ext = $filesys_data->get_extension($name);
	
			$destination = sprintf("%s/%s/%s.".$ext, $fspath, $fsdir, $new_id);
			rename($pdf, $destination);
		} else {
			@unlink($pdf);
		}

		@unlink($file);

		#file download
		header('Content-Transfer-Encoding: binary');
		header('Content-Type: application/pdf');

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename="'.$name.'"'); //msie 5.5 header bug
		} else {
			header('Content-Disposition: attachment; filename="'.$name.'"');
		}

		print($pdfdata);
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

	public function getTotalBatchByYear($year) {
		if ($year == 0) {
			$sql = "SELECT SUM(batchcounter) FROM projects_declaration_batchcounter";
		} else {
			$sql = sprintf("SELECT batchcounter FROM projects_declaration_batchcounter WHERE year = %s", $year);
		}
		$res = sql_query($sql);
		$batch = sprintf("%d", 1+sql_result($res, 0));
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

	public function getRegistrationItems($project_id, $status=0, $negative=0, $user_id = 0, $ts = 0) {
		$data = array();
		$user_data = new User_data();

		if ($negative == 1) {
			$q = sprintf("select * from projects_declaration_registration where price < 0 AND batch_nr = %d and project_id = %d order by timestamp", $status, $project_id);
		} else {
			if ($status == -1) {
				$q_sub = "";
				if ($ts) {
					$q_sub = sprintf("AND timestamp BETWEEN %d AND %d", $ts, $ts+((24*3600)-1));
				}
				if ($project_id) {
					$q_type = sprintf("project_id = %d", $project_id);
				} else {
					$q_type = sprintf("user_id = %d", $user_id);
				}
				$q = sprintf("select * from projects_declaration_registration where %s %s order by timestamp", $q_type, $q_sub);
			} else {
				$q = sprintf("select * from projects_declaration_registration where batch_nr = %d and project_id = %d order by timestamp", $status, $project_id);
			}
		}
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["human_date"]       = date("d-m-Y", $row["timestamp"]);

			if (!(int)$row["perc_NCNP"]) {
				$row["total_price"] = number_format(($row["price"] + ($row["price"]*((int)$row["perc_btw"]/100))), 2, ".", "");
			} else {
				$row["total_price"] = number_format($row["price"] - ($row["price"]*((int)$row["perc_NCNP"]/100)) - ($row["price"]*((int)$row["perc_NCNP"]/100)*((int)$row["perc_btw"]/100)), 2);
			}

			$row["declaration_type_plain"] = $row["declaration_type"];
			$row["declaration_type"] = $this->declaration_types[$row["declaration_type"]];
			$row["user_name"]        = $user_data->getUserNameById($row["user_id"]);
			$row["perc_NCNP"]        = number_format($row["perc_NCNP"], 1);
			$row["user_name_input"]  = $user_data->getUserNameById($row["user_id_input"]);
			$row["description"]      = utf8_decode($row["description"]);

			//$row = preg_replace("/^0$/s", "", $row);
			$data[]=$row;
		}
		return $data;
	}

	public function getRegistrationItemById($id) {
		$q = sprintf("select * from projects_declaration_registration where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		return $row;
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
			case 5:
				$req["declaration"]["hour_tarif"] = 0;
				$req["declaration"]["kilometers"] = 0;
				$req["declaration"]["time_units"] = 0;
				break;
			default:
				$req["declaration"]["hour_tarif"] = 0;
				$req["declaration"]["kilometers"] = 0;
				$req["declaration"]["time_units"] = 0;
				$req["declaration"]["perc_NCNP"] = 0;
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
		if ($req["id"]) {
			$q_parts = array();
			foreach ($vals as $k=>$v) {
				$q_parts[] = $k."=".$v;
			}
			$q = sprintf("UPDATE projects_declaration_registration SET %s WHERE id = %d", implode(",", $q_parts), $req["id"]);
		} else {
			$q = sprintf("insert into projects_declaration_registration (%s) values (%s)",
				implode(",", $keys), implode(",", $vals));
		}
		sql_query($q);

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

		/* new fields */
		$fields["identifier_adversary"]        = array("s", $req["declaration"]["identifier_adversary"]);
		$fields["identifier_expertise"]        = array("s", $req["declaration"]["identifier_expertise"]);
		$fields["agreements"]                  = array("s", $req["declaration"]["agreements"]);

		$fields["bcard_constituent"]           = array("d", $req["declaration"]["bcard_constituent"]);
		$fields["bcard_client"]                = array("d", $req["declaration"]["bcard_client"]);
		$fields["bcard_adversary"]             = array("d", $req["declaration"]["bcard_adversary"]);
		$fields["bcard_expertise"]             = array("d", $req["declaration"]["bcard_expertise"]);

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
		/* set some sane defaults for the date fields */
		if (!$row["task_date"])
			$row["task_date"] = time();
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
