<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}

	/* get form mode */
	$mode = 1; //$this->cms->getFormMode($req["system"]["pageid"]);

	if ($req["system"]["is_shop"]) {
		$art = array();
		$total = 0;
		foreach ($_SESSION["shop"] as $id=>$count) {
			$page = $this->getPageById($id);
			$price = number_format($page["shopPrice"] * $count, 2);
			$total+= ($page["shopPrice"] * $count);
			$art[] = array(
				"id"          => $id,
				"article_nr"  => $page["pageTitle"],
				"description" => $page["pageHeader"],
				"count"       => $count,
				"price"       => $page["shopPrice"],
				"sum"         => $price
			);
		}
	}
	//if (is_array($art))
	//	$req["data"][gettext("artikelen")] = implode("\n", $art);

	/* if store to db */
	/* if ($mode == 1) {
		$newid = $this->createVistorRecord($req["system"]["pageid"]);
		foreach ($req["data"] as $k=>$v) {
			$this->updateFieldRecord($req["system"]["pageid"], $k, $v, $newid);
		}
	}
	*/

	/*
	if (is_array($art)) {
		$t = array_pop($art);
		$req["data"][gettext("Artikelen")] = sprintf("<ul><li>%s</ul>%s",
			implode("<li>", $art), $t);
		$req["data"][gettext("artikelen")] = sprintf("%s\n\n%s", implode("\n", $art), $t);
	}
	*/

	$key = sprintf("s:{%s} p:{%s}", session_id(), $req["system"]["pageid"]);

	$q = sprintf("select count(*) from cms_temp where userkey = '%s' and ids = '%s'",
		$key, $_REQUEST["system"]["challenge"]);
	$res = sql_query($q);
	$num = sql_result($res,0,"",2);
	if ($num == 1) {
		$forms = $this->cms->getFormData($req["system"]["pageid"]);
		/*
		if (is_array($art)) {
			$forms[] = array(
				"id" => -1,
				"pageid" => $req["system"]["pageid"],
				"field_name" => gettext("artikelen"),
				"field_type" => "textarea"
			);
		}
		*/

		$email_data = new Email_data();
		$_id[1] = $email_data->save_concept();
		$_id[2] = $email_data->save_concept();

		$smtp["rcpt"]    = "";
		$smtp["from"]    = "";
		$smtp["subject"] = "";
		$smtp["result"]  = "";

		$uri  = sprintf("%s%s/page/%s", $this->protocol, $_SERVER["HTTP_HOST"], $this->checkAlias($req["system"]["pageid"]));
		$uri2 = sprintf("<a target=\"_blank\" href=\"http://%s/\">%s</a>",
			$this->http_host, $this->http_host);
		$uri3 = sprintf("%s%s/page/", $this->protocol, $_SERVER["HTTP_HOST"]);


		$frm["name"] = sprintf("<a target=\"_blank\" href=\"http://%s/\">%s</a>",
			$this->http_host, $frm["name"]);

		$html = "<table class='table1' style='background-color: white;'>";
		$html.= sprintf("<tr><td class='head1' colspan='2'>%s</td></tr>",
			gettext("Confirmation from website")." ".$uri2);


		$q = sprintf("select * from cms_license_siteroots where pageid = %d",
			$this->siteroot);
		$res = sql_query($q);
		if (sql_num_rows($res) == 1)
			$row = sql_fetch_assoc($res);
		else
			$row = array();

		/* website name */
		if ($row["search_fields"])
			$frm["name"] = $row["search_fields"];
		else
			$frm["name"] = $this->cms_license["search_fields"];

		/* website logo */
		if ($row["cms_logo"])
			$frm["logo"] = $row["cms_logo"];
		else
			$frm["logo"] = $this->cms_license["cms_logo"];

		$fpage = $this->getPageById($req["system"]["pageid"]);

		$html.= sprintf("<tr><td class='head1' colspan='2' style='text-align: left'>%s: <a href='%s' target='_blank'>%s</a></td></tr>",
			gettext("Page"), $uri, $fpage["pageTitle"]);

		foreach ($forms as $k=>$v) {
			$isspecial = 0;
			$req_orig = $req;

			if ($v["field_type"] == "address") {
				$req["data"][$v["field_name"]] = $req["data"][$v["field_name"]]["email"];
			}

			if ($v["field_type"] == "hidden") {
				$req["data"][$v["field_name"]] = $v["field_value"];
				$isspecial = 1;
			}
			if ($v["is_mailto"]) {
				$smtp["rcpt"] = $req["data"][$v["field_name"]];
				$req["data"][$v["field_name"]] = sprintf("<a href='mailto:%1\$s'>%1\$s</a>",
					$req["data"][$v["field_name"]]);
				$isspecial = 1;
			}
			if ($v["is_mailfrom"]) {
				$smtp["from"] = $req["data"][$v["field_name"]];
				$req["data"][$v["field_name"]] = sprintf("<a href='mailto:%1\$s'>%1\$s</a>",
					$req["data"][$v["field_name"]]);
				$isspecial = 1;
			}
			if ($v["is_mailsubject"]) {
				$smtp["subject"] = $req["data"][$v["field_name"]];
				$isspecial = 1;
			}
			if ($v["is_redirect"]) {
				$smtp["result"] = $req["data"][$v["field_name"]];
				$isspecial = 1;
			}
			$req = $req_orig;

			if ($isspecial == 0 || $v["field_type"] != "hidden") {
				/* normal field */
				if ($v["field_type"] == "checkbox") {
					$req["data"][$v["field_name"]] = implode(", ", $req["data"][$v["field_name"]]);
				} elseif ($v["field_type"] == "upload") {
					if (is_array($files)) {
						foreach ($files["binFile"]["name"] as $f) {
							$req["data"][$v["field_name"]] .= $f."\n";
						}
					}
				} elseif ($v["field_type"] == "date") {
					$req["data"][$v["field_name"]] = sprintf("%d-%d-%d",
						$req["data"][$v["field_name"]]["d"],
						$req["data"][$v["field_name"]]["m"],
						$req["data"][$v["field_name"]]["y"]
					);
				} elseif ($v["field_type"] == "datetime") {
					if ($req["data"][$v["field_name"]]["i"] < 10)
						$req["data"][$v["field_name"]]["i"] = "0".$req["data"][$v["field_name"]]["i"];

					$req["data"][$v["field_name"]] = sprintf("%d-%d-%d %d:%s",
						$req["data"][$v["field_name"]]["d"],
						$req["data"][$v["field_name"]]["m"],
						$req["data"][$v["field_name"]]["y"],
						$req["data"][$v["field_name"]]["h"],
						$req["data"][$v["field_name"]]["i"]
					);
				} elseif ($v["field_type"] == "address") {
					$fields = $this->address_fields;

					$t2 = new Layout_table();
					foreach ($fields as $fld=>$name) {
						$t2->addTableRow();
							$t2->insertTableData(gettext($name).":&nbsp;", array(
								"class" => "cell1"
							));
							if ($fld == "email")
								$req["data"][$v["field_name"]][$fld] = sprintf("<a href='mailto:%1\$s'>%1\$s</a>",
									$req["data"][$v["field_name"]][$fld]);

							$t2->insertTableData($req["data"][$v["field_name"]][$fld]);
						$t2->endTableRow();
					}
					$t2->endTable();
					$req["data"][$v["field_name"]] = $t2->generate_output();
				}

				if (!$v["is_mailfrom"] && !$v["is_mailto"])
					$req["data"][$v["field_name"]] = str_replace(htmlentities($this->valuta), $this->valuta, nl2br(htmlentities($req["data"][$v["field_name"]])));

				$html.= sprintf("<tr bgcolor='#f6f6f6'><td class='cell1' valign='top'>%s: </td><td class='cell2'>%s</td></tr>",
					$v["field_name"], $req["data"][$v["field_name"]]);
			}
		}
		$html.= "</table>";

		if (is_array($art)) {
			$html.= "<br>";
			$html.= "<table class='table1' style='background-color: white;'>";
			$html.= sprintf("
				<tr bgcolor='#f6f6f6'>
					<td class='head1'>%s</td>
					<td class='head1'>%s</td>
					<td class='head1'>%s</td>
					<td class='head1'>%s</td>
					<td class='head1'>%s</td>
				</tr>",
				gettext("article"),
				gettext("description"),
				gettext("price"),
				gettext("count"),
				gettext("subtotal")
			);
			foreach ($art as $a) {
				$html.= sprintf("
					<tr bgcolor='#f6f6f6'>
						<td valign='top' class='cell2'><a href='%s%s'>%s</a></td>
						<td valign='top' class='cell2'>%s</td>
						<td valign='top' class='cell2'><nobr>%s %s</nobr></td>
						<td valign='top' class='cell2'>%sx</td>
						<td valign='top' class='cell2'><nobr>%s %s</nobr></td>
					</tr>",
					$uri3, $this->checkAlias($a["id"]),
					$a["article_nr"],
					$this->limit_string($a["description"], 150, 0),
					$this->valuta,
					$a["price"],
					$a["count"],
					$this->valuta,
					$a["sum"]
				);
			}
			$html.= sprintf("
				<tr bgcolor='#f6f6f6'>
					<td class='head1' colspan='4' align='right'>%s</td>
					<td class='head1'>%s %s</td>
				</tr>",
				gettext("total"),
				$this->valuta,
				number_format($total, 2)
			);
			$html.= "</table>";
		}

		$emailData =& $email_data;

		if ($_SESSION["visitor_id"]) {
			$user = $this->cms->getAccountList($_SESSION["visitor_id"]);
			$address_id = $user[0]["address_id"];
		}
		$req["mail"] = array(
			"from"       => $smtp["from"],
			"to"         => $smtp["rcpt"],
			"rcpt"       => $smtp["rcpt"],
			"address_id" => $address_id,
			"subject"    => sprintf("[%s] %s", $this->http_host, $smtp["subject"])
		);
		$req["contents"] = $html;
		$emailData->save_concept($_id[1], $req);

		$req["mail"] = array(
			"from"       => $smtp["rcpt"],
			"to"         => $smtp["from"],
			"rcpt"       => $smtp["from"],
			"subject"    => sprintf("[%s] %s", $this->http_host, $smtp["subject"])
		);
		$req["contents"] = $html;

		if (strtolower($smtp["from"]) == strtolower($smtp["rcpt"]))
			$double = 1;

		if (!$double)
			$emailData->save_concept($_id[2], $req);
		if (is_array($files)) {
			echo $emailData->upload_files($_id[1], 1);
			if (!$double)
				echo $emailData->upload_files($_id[2], 1);
		}
		$msg[1] = $emailData->sendMailComplex($_id[1], 1, 1);
		if (!$double)
			$msg[2] = $emailData->sendMailComplex($_id[2], 1, 1);

		/* move the new mail to its archive */
		$folder_id = $emailData->checkSharedFolders($req["system"]["pageid"]);
		$emailData->messageToFolder($_id[1], $folder_id);

		/* drop the second mail to deleted items */
		if (!$double)
			$emailData->mail_delete($_id[2]);

		/* check result uri */
		if (!preg_match("/^http(s){0,1}/s", $smtp["result"])) {
			$smtp["result"] = preg_replace("/^\//s", "", $smtp["result"]);
			$smtp["result"] = sprintf("http://%s/%s", $this->http_host, $smtp["result"]);
			$smtp["result"] = urldecode($smtp["result"]);
		}

		if ($req["system"]["is_shop"] && $this->cms_license["ideal_type"] == "rabolite") {
			$next_order = -1;
			$expected_order = 0;

			/* update database order number */
			while ($next_order != $expected_order) {
				$next_order = -1;
				$expected_order = 0;

				$q = "select ideal_last_order from cms_license";
				$res = sql_query($q);
				$expected_order = sql_result($res,0,"",2) + 1;

				/* reset counter if it goes too big, this way we can handle 99.999 transactions a day */
				if ($expected_order > 99999) {
					$q = "update cms_license set ideal_last_order = 0";
					$res = sql_query($q);
					$expected_order = 1;
				}

				/* set last order + 1 */
				$q = "update cms_license set ideal_last_order = ideal_last_order + 1";
				sql_query($q);

				/* now get the new order */
				$q = "select ideal_last_order from cms_license";
				$res = sql_query($q);
				$next_order = sql_result($res,0,"",2);
			}
			/* create unique order number */
			$ordernr = sprintf("%s-%05s-%03s", date("dmy"), (int)$next_order, rand(0, 999));

			/* create ideal form */
			$win = md5(mktime()*rand());
			$ideal = new Layout_output();
			$ideal->addTag("form", array(
				"action" => sprintf("https://ideal%s.rabobank.nl/ideal/mpiPayInitRabo.do", ($this->cms_license["ideal_test_mode"]) ? "test":""),
				"method" => "post",
				"id"     => "idealfrm",
				"target" => "_top" //sprintf("ideal_%s", $win)
			));
			$ts = date("Y-m-d\TG:i:s\Z", mktime()+(5*60)); //5 minutes

			$ideal->addHiddenField("merchantID",
				$this->ideal_filter($this->cms_license["ideal_merchant_id"], 9));

			$ideal->addHiddenField("subID", "0");
			$ideal->addHiddenField("amount",      $this->ideal_filter((int)($total*100), 12));
			$ideal->addHiddenField("purchaseID",  $this->ideal_filter($ordernr, 16));
			$ideal->addHiddenField("language",    ($this->language) ? $this->language:"en");
			$ideal->addHiddenField("currency",    "EUR");
			$ideal->addHiddenField("description", $this->ideal_filter($ordernr, 16));
			$ideal->addHiddenField("paymentType", "ideal");
			$ideal->addHiddenField("validUntil",  $ts);

			$ideal->addHiddenField("urlCancel",   $this->ideal_filter(sprintf("http://%s/mode/shop_cancel", $this->http_host), 512));
			$ideal->addHiddenField("urlError",    $this->ideal_filter(sprintf("http://%s/mode/shop_error", $this->http_host), 512));
			$ideal->addHiddenField("urlSuccess",  $this->ideal_filter(sprintf("%s", $smtp["result"]), 512));

			$hash = "";
			$i = 0;
			foreach ($_SESSION["shop"] as $k=>$v) {
				$i++;
				$page = $this->getPageById($k);
				$ideal->addHiddenField(sprintf("itemNumber%d", $i),      $this->ideal_filter($page["pageTitle"], 12));
				$ideal->addHiddenField(sprintf("itemDescription%d", $i), $this->ideal_filter($page["pageHeader"], 32));
				$ideal->addHiddenField(sprintf("itemQuantity%d", $i),    $this->ideal_filter((int)$v, 4));
				$ideal->addHiddenField(sprintf("itemPrice%d", $i),       $this->ideal_filter((int)($page["shopPrice"]*100), 12));

				$hash.= sprintf("%s%s%s%s",
					$this->ideal_filter($page["pageTitle"], 12),
					$this->ideal_filter($page["pageHeader"], 32),
					$this->ideal_filter((int)$v, 4),
					$this->ideal_filter((int)($page["shopPrice"]*100), 12)
				);
			}
			$hash = html_entity_decode(sprintf("%s%s%s%s%s%s%s%s",
				$this->cms_license["ideal_secret_key"],
				$this->ideal_filter($this->cms_license["ideal_merchant_id"], 9),
				0, $this->ideal_filter((int)($total*100), 12),
				$this->ideal_filter($ordernr, 16),
				"ideal", $ts, $hash
			));

			$hash = str_replace(array("\t", "\n", "\r", " "), "", $hash);
			$hash = sha1($hash, false);

			$ideal->addHiddenField("hash", $this->ideal_filter($hash, 50));
			$ideal->start_javascript();
				$ideal->addCode(sprintf("
					//parent.document.getElementById('ideal_order_button').style.display = 'none';
					//parent.document.getElementById('ideal_order_done').style.display = '';
					//parent.popup('blank.htm', 'ideal_%s', 980, 700, 1);
					setTimeout('document.getElementById(\"idealfrm\").submit();', 1000);
				", $win));
			$ideal->end_javascript();
			$ideal->endTag("form");
		}

		/* remove key */
		$output = new Layout_output();
		if ($ideal) {
			$output->addCode($ideal->generate_output());
		} else {
			$t = str_replace("http://".$this->http_host."/", "", $smtp["result"]);
			if (preg_match("/^index\.php\?id=\d{1,}/si", $t))
				$smtp["result"] = preg_replace("/^(.*)index\.php\?id=(\d{1,})/si", "/page/$2", $t);

			$output->start_javascript();
				$output->addCode(sprintf("parent.document.location.href = '%s'; ",
					addslashes($smtp["result"])));
			$output->end_javascript();
		}
		/* delete shop contents */
		unset($_SESSION["shop"]);
		echo $output->generate_output();

	} else {
		/* not accepted */
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf(" alert('%s'); ",
				addslashes(gettext(
					"Het formulier kon niet verstuurd worden. Mogelijke oorzaken zijn dat u geen geldige aanroep gebruikt of de pagina voor meerdere uren open hebt laten staan alvorens te versturen."
				))."\\n\\n".
				addslashes(gettext(
					"Ververs de pagina en probeer het opnieuw."
				))
			));
		$output->end_javascript();
		echo $output->generate_output();
	}
?>