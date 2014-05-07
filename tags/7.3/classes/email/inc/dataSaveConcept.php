<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

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

			if (!$data["is_text"]) {
				// Specify configuration
				$config = array(
					'indent'         => true,
					'output-html'    => true,
					'wrap'           => 200,
					'drop-font-tags' => true,
					'drop-proprietary-attributes' => true,
					'word-2000'      => true,
					'clean'          => true

				);
				// Tidy
				$tidy = new Tidy();
				$tidy->parseString($data["body"], $config, 'utf8');
				$tidy->cleanRepair();

				$data["body"] = $tidy;
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
				if ($data["is_text"]) {
					/* text */
					$sig = $this->getEmailSignature($_REQUEST["from"]);

					$data["body"] = "> ".str_replace("\n", "\n> ", $data["body"]);

					$prefix = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n";
					$prefix.= "<table border='1' frames='all' rules='none'>\n";
					$prefix.= " <tr>\n";
					$prefix.= "  <td colspan='2' align='center'>";
					$prefix.= "------- ".gettext("original message")." -------\n";
					$prefix.= "  </td>\n";
					$prefix.= " </tr><tr>\n";
					$prefix.= "  <td valign='top'>".gettext("from").": </td><td>".htmlentities($mdata[0]["sender_emailaddress_h"])."</td>\n";
					$prefix.= " </tr><tr>\n";
					$prefix.= "  <td valign='top'>".gettext("to").": </td><td>".$mdata[0]["to"]."</td>\n";
					$prefix.= " </tr><tr>\n";
					$prefix.= "  <td valign='top'>".gettext("subject").": </td><td>".$mdata[0]["subject"]."</td>\n";
					$prefix.= " </tr><tr>\n";
					if ($mdata[0]["cc"]) {
						$prefix.= "  <td>".gettext("cc").": </td><td>".$mdata[0]["cc"]."</td>\n";
						$prefix.= " </tr><tr>\n";
					}
					$prefix.= "  <td class='cell1'>".gettext("date").": </td><td>".$mdata[0]["h_date"]."</td>\n";
					$prefix.= " </tr>\n</table>\n";

					$prefix = preg_replace("/<br[^>]*?>/si", "\n", $this->html2text($prefix));
					$prefix = preg_replace("/(\n|^) {0,}/s", "\n", $prefix);

					$data["body"] = $prefix."\n\n".$data["body"];


					$data["body"] = "\n\n\n".$sig."\n\n".$data["body"];
				} else {

					$sig = $this->getEmailSignatureHtml($_REQUEST["from"]);

					/* strip all head/body tags */
					$data["body"] = $this->stripBodyTags($data["body"]);
					$prefix = $this->getPrefix(2);

					$prefix.= "<br><br>";
					$prefix.= $sig."<br><br>\n\n";

					$prefix.= "<table class='table1' cellspacing='1' cellpadding='0'>\n";
					$prefix.= " <tr class='head1'>\n";
					$prefix.= "  <td colspan='2'>";
					$prefix.= "----- ".gettext("original message")." -----\n";
					$prefix.= "  </td>\n";
					$prefix.= " </tr><tr class='head2'>\n";
					$prefix.= "  <td><span class='cell1'>".gettext("from").": </span></td><td><span class='cell2'>".htmlentities($mdata[0]["sender_emailaddress_h"])."</span></td>\n";
					$prefix.= " </tr><tr class='head2'>\n";
					$prefix.= "  <td><span class='cell1'>".gettext("to").": </span></td><td><span class='cell2'>".$mdata[0]["to"]."</span></td>\n";
					$prefix.= " </tr><tr class='head2'>\n";
					$prefix.= "  <td><span class='cell1'>".gettext("subject").": </span></td><td><span class='cell2'>".$mdata[0]["subject"]."</span></td>\n";
					$prefix.= " </tr><tr class='head2'>\n";
					if ($mdata[0]["cc"]) {
						$prefix.= "  <td><span class='cell1'>".gettext("cc").": </span></td><td><span class='cell2'>".$mdata[0]["cc"]."</span></td>\n";
						$prefix.= " </tr><tr class='head2'>\n";
					}
					$prefix.= "  <td><span class='cell1'>".gettext("date").": </span></td><td><span class='cell2'>".$mdata[0]["h_date"]."</span></td>\n";
					$prefix.= " </tr>\n</table>\n";
					$prefix.= "<br>\n";

					$prefix.= "<div style='border-left: 2px solid blue; padding-left: 6px;'>\n";
					$suffix = "</div></body></html>";

					$data["body"] = $prefix.$data["body"].$suffix;
				}
			}


		} else {
			$user_data = new User_data();
			$userinfo = $user_data->getUserdetailsById($_SESSION["user_id"]);
			if ($_REQUEST["view_mode"]=="html" || $userinfo["mail_html"]) {
				$sig = $this->getEmailSignatureHtml($_REQUEST["mail"]["from"]);
				$data["is_text"] = 0;
				$data["body"] = "<br><br>".$sig;
			} else {
				$sig = $this->getEmailSignature($_REQUEST["mail"]["from"]);
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

		$fields["is_public"]           = array("d", ($userinfo["mail_default_private"] == 1) ? 2:0);

		if ($_REQUEST["ref_id"]) {
			$fields["options"]             = array("s",$this->encodeMailOptions( array("related_id"=>$_REQUEST["ref_id"]) ));
		}

		if (!$fields["subject"][1]) {
			$fields["subject"][1] = gettext("no subject");
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

				@$fsdata->FS_copyFile($source_file, $target_file);
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
			$fields["subject"][1] = gettext("no subject");
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
				/* replace signature */
				$sig = $this->getEmailSignature($_REQUEST["mail"]["from"]);
				$sig_html = $this->getEmailSignatureHtml($_REQUEST["mail"]["from"]);

				$contents = str_replace($sig, "##signature##", $contents);

				/* make html */
				$contents = htmlentities($contents, ENT_QUOTES, "UTF-8");
				/*
				$contents = sprintf("<PRE wrap='1' style='word-wrap: break-word; size: 10pt;'>%s</PRE>",
					htmlentities($contents, ENT_COMPAT, "UTF-8")
				);
				$contents = "<br><br>".$contents;
				$contents = str_replace("##signature##", "</PRE>".$sig_html."<PRE wrap='1' style='word-wrap: break-word; size: 10pt;'>", $contents);
				*/
				$contents = sprintf("<font face='monospace'>%s</font>",
					nl2br(htmlentities($contents, ENT_COMPAT, "UTF-8"))
				);
				$contents = "<br><br>".$contents;
				$contents = str_replace("##signature##", "</font>".$sig_html."<font face='monospace'>", $contents);
				$contents = sprintf("<P>%s</P>", $contents);


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
