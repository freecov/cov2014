<?php
	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	/*
		fields available:
			id, from, to, cc, bcc, priority, relation, project, subject, contents, template
	*/
	$escape = sql_syntax("escape_char");
	$data = array();
	if (!$id) {
		/* create a new record in folders concepts */
		/* retrieve concept folder */
		$folderdata = $this->getSpecialFolder("Concepten", $_SESSION["user_id"]);
		$concept_folder = $folderdata["id"];

		if ($_REQUEST["ref_type"]) {
			$mdata = $this->getEmailById($_REQUEST["ref_id"]);
			$mdata[0]["subject"] = addslashes($mdata[0]["subject"]);
			//$mdata[0]["body"]  = addslashes($mdata[0]["body"]); //already done?
			$mdata[0]["to"]      = addslashes($mdata[0]["to"]);
			$mdata[0]["cc"]      = addslashes($mdata[0]["cc"]);
			$mdata[0]["bcc"]     = addslashes($mdata[0]["bcc"]);

			if ($_REQUEST["viewmode"]=="text") {
				/* the view mode was text */
				$data["is_text"] = 1;
				$data["body"] = $mdata[0]["body"];
				$data["body"] = $this->br2nl( $data["body"] );

			} elseif ($_REQUEST["viewmode"]=="html") {
				/* the viewmode was html (and so was the email) */
				$data["is_text"] = 0;
				$data["body"] = $mdata[0]["body_html"];

			} else {
				/* the viewmode was not set, use the text version */
				$data["is_text"] = 1;
				if ($mdata[0]["is_text"]) {
					$data["body"] = $mdata[0]["body"];
				} else {
					$data["body"] = $this->br2nl($mdata[0]["body"]);
				}
			}

			switch ($_REQUEST["ref_type"]) {
				case "reply_all":
					$data["to"] = ($mdata[0]["reply_to"]) ? $mdata[0]["reply_to"] : $mdata[0]["sender_emailaddress"];
					$tmp_cc = array_merge(explode(",", $mdata[0]["cc"]), explode(",", $mdata[0]["to"]));
					foreach ($tmp_cc as $ckey=>$cval) {
						$cval = $this->cleanAddress($cval);
						if (!preg_match("/^.*@.*\..*$/s", $cval)) {
							unset($tmp_cc[$ckey]);
						} else {
							$tmp_cc[$ckey] = trim($cval);
						}
					}
					$tmp_to = $this->cleanAddress($data["to"]);
					$tmp_cc = array_unique($tmp_cc);
					if (array_search($tmp_to, $tmp_cc)) {
						unset($tmp_cc[array_search($tmp_to, $tmp_cc)]);
					}
					natcasesort($tmp_cc);
					$data["cc"] = implode(", ", $tmp_cc);

					unset($tmp_to);
					unset($tmp_cc);

					$data["subject"] = "RE: ".$mdata[0]["subject"];
					break;
				case "reply":
					$data["to"] = ($mdata[0]["reply_to"]) ? $mdata[0]["reply_to"] : $mdata[0]["sender_emailaddress"];
					$data["subject"] = "RE: ".$mdata[0]["subject"];
					break;
				case "forward":
					$data["subject"] = "FW: ".$mdata[0]["subject"];

					/* if the email is forwarded we need to copy over the attachments */
					$data["copy_attachments"] = 1;
					break;
			}

			/* use original project and relation selection */
			$data["address_id"] = $mdata[0]["address_id"];
			$data["project_id"] = $mdata[0]["project_id"];

			/* TODO: check if this is correct, i think this adds too many slashes */
			//$data["body"] = addslashes($data["body"]);


			$data["to"]   = $this->cleanAddress($data["to"]);
			if ($_REQUEST["from"]) {
				$data["from"] = $_REQUEST["mail"]["from"];
			}

			/* retrieve the signature */
			if (is_numeric($_REQUEST["from"])) {
				$sig = $this->getEmailSignature($_REQUEST["from"]);
				if ($data["is_text"]) {
					/* text */
					$data["body"] = "> ".str_replace("\n", "\n> ", $data["body"]);


					$prefix.= "<table border='1' frames='all' rules='none'>\n";
					$prefix.= " <tr>\n";
					$prefix.= "  <td colspan='2' align='center'>";
					$prefix.= "------- ".gettext("origineel bericht")." -------\n";
					$prefix.= "  </td>\n";
					$prefix.= " </tr><tr>\n";
					$prefix.= "  <td valign='top'>".gettext("van").": </td><td>".$mdata[0]["sender_emailaddress"]."</td>\n";
					$prefix.= " </tr><tr>\n";
					$prefix.= "  <td valign='top'>".gettext("naar").": </td><td>".$mdata[0]["to"]."</td>\n";
					$prefix.= " </tr><tr>\n";
					$prefix.= "  <td valign='top'>".gettext("onderwerp").": </td><td>".$mdata[0]["subject"]."</td>\n";
					$prefix.= " </tr><tr>\n";
					if ($mdata[0]["cc"]) {
						$prefix.= "  <td>".gettext("cc").": </td><td>".$mdata[0]["cc"]."</td>\n";
						$prefix.= " </tr><tr>\n";
					}
					$prefix.= "  <td class='cell1'>".gettext("datum").": </td><td>".$mdata[0]["h_date"]."</td>\n";
					$prefix.= " </tr>\n</table>\n";

					$prefix = preg_replace("/<br[^>]*?>/si", "\n", $this->html2text($prefix));
					$prefix = preg_replace("/(\n|^) {0,}/s", "\n", $prefix);

					$data["body"] = $prefix."\n\n".$data["body"];


					$data["body"] = "\n\n\n".$sig."\n\n".$data["body"];
				} else {

					/* strip all head/body tags */
					$data["body"] = $this->stripBodyTags($data["body"]);

					$prefix = $this->getPrefix();

					$prefix.= "<br><br><br>";
					$prefix.= nl2br($sig)."<br><br>\n\n";

					$prefix.= "<table class='table1' cellspacing='1' cellpadding='0'>\n";
					$prefix.= " <tr class='head1'>\n";
					$prefix.= "  <td colspan='2'>";
					$prefix.= "----- ".gettext("origineel bericht")." -----\n";
					$prefix.= "  </td>\n";
					$prefix.= " </tr><tr class='head2'>\n";
					$prefix.= "  <td class='cell1'>".gettext("van").": </td><td class='cell2'>".$mdata[0]["sender_emailaddress"]."</td>\n";
					$prefix.= " </tr><tr class='head2'>\n";
					$prefix.= "  <td class='cell1'>".gettext("naar").": </td><td class='cell2'>".$mdata[0]["to"]."</td>\n";
					$prefix.= " </tr><tr class='head2'>\n";
					$prefix.= "  <td class='cell1'>".gettext("onderwerp").": </td><td class='cell2'>".$mdata[0]["subject"]."</td>\n";
					$prefix.= " </tr><tr class='head2'>\n";
					if ($mdata[0]["cc"]) {
						$prefix.= "  <td class='cell1'>".gettext("cc").": </td><td class='cell2'>".$mdata[0]["cc"]."</td>\n";
						$prefix.= " </tr><tr class='head2'>\n";
					}
					$prefix.= "  <td class='cell1'>".gettext("datum").": </td><td class='cell2'>".$mdata[0]["h_date"]."</td>\n";
					$prefix.= " </tr>\n</table>\n";
					$prefix.= "<br>\n";

					$prefix.= "<div style='border-left: 2px solid blue; padding-left: 6px;'>\n";
					$suffix = "</div></body></html>";

					$data["body"] = addslashes($prefix).$data["body"].addslashes($suffix);

				}
			}


		} else {
			$sig = $this->getEmailSignature($_REQUEST["mail"]["from"]);
			$user_data = new User_data();
			$userinfo = $user_data->getUserdetailsById($_SESSION["user_id"]);
			if ($_REQUEST["view_mode"]=="html" || $userinfo["mail_html"]) {
				$data["is_text"] = 0;
				$data["body"] = "<br><br>".nl2br($sig);
			} else {
				$data["is_text"] = 1;
				$data["body"] = "\n\n".$sig;
			}
			$data["from"] = $_REQUEST["mail"]["from"];
		}

		/* if some parameters are passed */
		if ($_REQUEST["from"]) {
			$data["from"] = $_REQUEST["from"];
		}
		if ($_REQUEST["to"]) {
			$data["to"] = $_REQUEST["to"];
		}
		if ($_REQUEST["relation"]) {
			$data["address_id"] = $_REQUEST["relation"];
		}

		//prepare the data
		$fields["message_id"]          = array("s", $this->generate_message_id($data["from"]) );
		$fields["sender"]              = array("s", "");
		$fields["sender_emailaddress"] = array("s",$data["from"]);
		$fields[$escape."to".$escape]  = array("s",$data["to"]);
		$fields["cc"]                  = array("s",$data["cc"]);
		$fields["bcc"]                 = array("s",$data["bcc"]);
		/* TODO: check this if it is correct */
		//$fields["subject"]             = array("s",addslashes($data["subject"]));
		$fields["subject"]             = array("s",$data["subject"]);

		$fields["address_id"]          = array("d",$data["address_id"]);
		$fields["project_id"]          = array("d",$data["project_id"]);
		$fields["folder_id"]           = array("d",$concept_folder);
		$fields["user_id"]             = array("d",$_SESSION["user_id"]);
		$fields["date"]                = array("d",mktime());
		$fields["is_new"]              = array("d",1);
		$fields["indexed"]             = array("d",2);
		$fields["is_text"]             = array("d",$data["is_text"]);

		if ($_REQUEST["ref_id"]) {
			$fields["options"]             = array("s",$this->encodeMailOptions( array("related_id"=>$_REQUEST["ref_id"]) ));
		}

		if (!$fields["subject"][1]) {
			$fields["subject"][1] = gettext("geen onderwerp");
		}
		$keys = array();
		$vals = array();
		foreach ($fields as $k=>$v) {
			$keys[] = $k;
			if ($v[0]=="s") {
				//addslashes already done
				$vals[]="'".$v[1]."'";
			} else {
				$vals[]=(int)$v[1];
			}
		}
		$keys = implode(",",$keys);
		$vals = implode(",",$vals);

		$q = sprintf("insert into mail_messages (%s) values (%s)", $keys, $vals);
		sql_query($q);

		$new_id = sql_insert_id("mail_messages");

		if ($data["copy_attachments"]) {
			$filesys = new Filesys_data();
			$fspath = $GLOBALS["covide"]->filesyspath;

			$dir = sprintf("%s/email/", $fspath);

			$attachments = $this->attachments_list($_REQUEST["ref_id"]);

			foreach ($attachments as $k=>$v) {

				/* insert into database */
				$q = sprintf("insert into mail_attachments (message_id, name, size, type, cid) values (%d, '%s', '%s', '%s', '%s')",
					$new_id, addslashes($v["name"]), addslashes($v["size"]), addslashes($v["type"]), addslashes($v["cid"]) );
				sql_query($q);

				$att_id = sql_insert_id("mail_attachments");

				#$fscopy = sprintf("cp -f %s %s", $source_file, $target_file);
				#exec ($fscopy, $ret, $reval);
				if (!$fsdata) $fsdata = new Filesys_data();
				$ext = $fsdata->get_extension($v["name"]);

				$source_file = sprintf("%s/%d.%s", $dir, $v["id"], $ext);
				$target_file = sprintf("%s/%d.%s", $dir, $att_id, $ext);

				@copy($source_file, $target_file);
			}
		}

		//delete old messages data (if any)
		$q = sprintf("delete from mail_messages_data where mail_id = %d", $new_id);
		sql_query($q);

		//insert new message data
		$q = sprintf("insert into mail_messages_data (mail_id, header, body) values ('%d', '%s', '%s')", $new_id, "", addslashes($data["body"]));
		sql_query($q);

		$this->dropMailBodyToFilesys($new_id);

		$id = $new_id;

	} else {

		$output = new Layout_output();
		$data = $_POST["mail"];

		//prepare the data
		$fields["message_id"]          = $this->generate_message_id($data["from"]);
		$fields["sender"]              = "";
		$fields["sender_emailaddress"] = array("s",$data["from"]);
		$fields[$escape."to".$escape]  = array("s",$data["rcpt"]);
		$fields["cc"]                  = array("s",$data["cc"]);
		$fields["bcc"]                 = array("s",$data["bcc"]);
		$fields["subject"]             = array("s",$data["subject"]);
		#$fields["address_id"]          = array("d",$data["address_id"]); //replaced by xmlhttp
		$fields["project_id"]          = array("d",$data["project_id"]);
		$fields["date"]                = array("d",mktime());
		if ($_REQUEST["convert_on_save"]) {
			if ($_REQUEST["is_text"]) {
				$is_text = 0;
			} else {
				$is_text = 1;
			}
		} else {
			$is_text = $_REQUEST["is_text"];
		}
		$fields["is_text"]             = array("d",$is_text);

		if (!$fields["subject"][1]) {
			$fields["subject"][1] = gettext("geen onderwerp");
		}
		$vals = array();
		foreach ($fields as $k=>$v) {
			if ($v[0]=="s") {
			  //addslashes already done
				$vals[$k]="'".$v[1]."'";
			} else {
				$vals[$k]=(int)$v[1];
			}
		}

		$encode = $this->encodeMailOptions($data);

		$vals["options"] = "'".$encode."'";

		$q = "update mail_messages set is_new = 1 ";
		foreach ($vals as $k=>$v) {
			$q.= sprintf(", %s = %s ", $k, $v);
		}
		$q.= sprintf(" where id = %d", $id);
		sql_query($q);

		//save body
		$contents = stripslashes($_REQUEST["contents"]);
		if ($_REQUEST["convert_on_save"]) {
			if ($_REQUEST["is_text"]) {
				/* make html */
				$contents = nl2br($contents);
			} else {
				/* make text */
				$contents = trim( $this->html2Text($contents) );
			}
		}

		$q = sprintf("update mail_messages_data set body = '%s' where mail_id = %d", addslashes($contents), $id);
		sql_query($q);

		$this->dropMailBodyToFilesys($id);

		$output->addCode("saved ...");
		if ($_POST["js_command"]) {
			$output->start_javascript();
			$output->addCode("parent.".stripslashes( $_POST["js_command"] ));
			$output->end_javascript();
		}
		$output->exit_buffer();
	}
?>
