<?php
/**
 * Covide Groupware-CRM support module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
Class Support_data {
	/* constants */
	const include_dir = "classes/support/inc/";

	/* variables */
	/* methods */

	/* getSupportItems {{{ */
	/**
	 * Get supportitems based on options
	 *
	 * @param array Options to controll what items to fetch
	 * @return array The items matching the options
	 */
	public function getSupportItems($options) {
		require(self::include_dir."dataGetSupportItems.php");
		return $supportItems;
	}
	/* }}} */
	/* getSupportItemById {{{ */
	/**
	 * Get a specific support item from db
	 *
	 * @param int The database id of the item to get
	 * @return array The support item
	 */
	public function getSupportItemById($id) {
		require(self::include_dir."dataGetSupportItemById.php");
		return $supportItem;
	}
	/* }}} */
	/* getExternalIssues {{{ */
	/**
	 * Get supportitems send by the webbased form
	 *
	 * @param int $id Optional issue id
	 *
	 * @return array the items optionally limited to the given id
	 */
	public function getExternalIssues($id="") {
		require(self::include_dir."dataGetExternalIssues.php");
		return $issues;
	}
	/* }}} */
	/* remove_ext_item {{{ */
	/**
	 * Remove external support request
	 *
	 * @param int $id The item id we want to remove
	 * @param int $returnxml if set, will return a js function call to be used in the loadXML() ajax function
	 * @param int $returnnone if set, nothing will be returned
	 *
	 * @return mixed see $returnxml and $returnnone parameters
	 */
	public function remove_ext_item($id, $returnxml=0, $returnnone=0) {
		$sql = sprintf("DELETE FROM support WHERE id = %d", $id);
		$res = sql_query($sql);
		if ($returnxml) {
			echo "reload_doc();";
			exit();
		} elseif (!$returnnone) {
			header("Location: index.php?mod=support&action=list_external");
			exit();
		}
	}
	/* }}} */
	/* save2db  {{{ */
	/**
	 * save issue to db
	 */
	public function save2db () {
		require(self::include_dir."save2db.php");
	}
  /* }}} */
	/* sendMail {{{ */
	/**
	 * Send mail to support requester about status changes to their ticket
	 *
	 * @param array $issueinfo Array with all the info about the issue
	 * @param string $type What kind of status info is this? Can be: insert, update, done, reopened
	 */
	public function sendMail($issueinfo, $type) {
		if ($issueinfo["email"]) {
			$email_data = new Email_data();
			/* check if license email flag is valid */
			if (!$email_data->validateEmail($GLOBALS["covide"]->license["email"])) {
				$output = new Layout_output();
				$output->layout_page("", 1);
				$output->start_javascript();
					$output->addCode("
						alert(gettext('There is a problem sending the email: license flag email is not set, please contact your system administrator.'));
						opener.document.location.href = opener.document.location.href;
						window.close();
					");
				$output->end_javascript();
				$output->layout_page_end();
				$output->exit_buffer();
			}
			/* check if issueinfo[email] is valid */
			if (!$email_data->validateEmail($issueinfo["email"])) {
				$output = new Layout_output();
				$output->layout_page("", 1);
				$output->start_javascript();
					$output->addCode("
						alert(gettext('There is a problem sending the email: the given email address is not valid.'));
						opener.document.location.href = opener.document.location.href;
						window.close();
					");
				$output->end_javascript();
				$output->layout_page_end();
				$output->exit_buffer();
			}
			
			$details = sprintf("%s: %s<br>", gettext("reference nr"), $issueinfo["reference_nr"]);
			$details.= sprintf("%s: %s<br>", gettext("registration date"), date("d-m-Y", mktime(0, 0, 0, $issueinfo["month"], $issueinfo["day"], $issueinfo["year"])));
			$details.= sprintf("%s: %s<br><br>", gettext("current date and time"), date("d-m-Y H:i"));
			$details.= sprintf("%s:<br><br>%s<br>", gettext("your support request"), $issueinfo["description"]);
			
			$user_data = new User_data();
			if ($issueinfo["user_id"]) {
				$userdetails = $user_data->getUserdetailsById($issueinfo["user_id"]);
				
				if ($userdetails["mail_email"]) {
					$email = $userdetails["mail_email"];
				} else {
					$email = $userdetails["mail_email1"];
				}
				if (!$email_data->validateEmail($email)) {
					$output = new Layout_output();
					$output->layout_page("", 1);
					$output->start_javascript();
						$output->addCode("
							alert(gettext('There is a problem sending the email: the selected employee / user has no valid email primary addresses.'));
							opener.document.location.href = opener.document.location.href;
							window.close();
						");
					$output->end_javascript();
					$output->layout_page_end();
					$output->exit_buffer();
				}
			}
			
			// create a Draft email that we can alter based on the type of update
			$draft_id = $email_data->save_concept();
			switch ($type) {
				case "insert":
					/*
					  Your support request is saved with ticketnumber: 445988
						Your request will be processed by our support department.
						Our support department will contact you and you will get an email with the name of the support technician who will work on your ticket.
						If you contact us or reply to questions please use the ticketnumber in the subject or body.
						
						We will process your ticket in a timely manner, but feel free to contact us if you think things are stalling.
					 */
					$txt = gettext("Your support request is saved with ticketnumber");
					$txt.= sprintf(": %s.\n\n", $issueinfo["reference_nr"]);
					$txt.= gettext("Your request will be processed by our support department.")."\n";
					$txt.= gettext("Our support department will contact you and you will get an email with the name of the support technician who will work on your ticket.")."\n";
					$txt.= gettext("If you contact us or reply to questions please use the ticketnumber in the subject or body.")."\n\n";
					$txt.= gettext("We will process your ticket in a timely manner, but feel free to contact us if you think things are stalling.")."\n\n";
					$txt.= gettext("You will find the details of this support request below this line.")."\n\n";
					$txt.= $details;
					
					$mailreq = array(
						"view_mode" => "html",
						"mail" => array(
							"from" => $GLOBALS["covide"]->license["email"],
							"subject" => gettext("support call registered"),
							"to" => $issueinfo["email"],
							"rcpt" => $issueinfo["email"],
						),
						"contents" => nl2br($txt)
					);
					$tmp_buf[1] = $email_data->save_concept($draft_id, $mailreq);
					$tmp_buf[2] = $email_data->sendMailComplex($draft_id, 1, 1);
					break;
				case "update":
					/*
					  Uw supportaanvraag met als referentienummer: 445988 is geclassificeerd 
						als klacht en zal worden behandeld door christian. Het emailadres van 
						deze medewerker is christian@terrazur.nl.
					 */
					if ($issueinfo["old_user_id"] != $issueinfo["user_id"]) {
						$body  = "<p>\n";
						$body .= gettext("Your support request with ticketnumber");
						$body .= sprintf(": %s.</p>", $issueinfo["reference_nr"]);
						$body .= "<p>\n";
						$body .= gettext("Your support request has been classified and is beeing processed by ");
						$body .= sprintf("%s.<br>", $user_data->getUsernameById($issueinfo["user_id"]));
						$body .= gettext("The email address of this support member is ");
						$body .= sprintf("&nbsp;<a href=\"mailto:%1\$s.\">%1\$s</a><br>", $email);
						$body .= gettext("Details of your support request are listed below.")."<br><br>";
						$body .= $details;
						$mailreq = array(
							"view_mode" => "html",
							"mail" => array(
								"from" => $email,
								"subject" => gettext("support call classified"),
								"to" => $issueinfo["email"],
								"rcpt" => $issueinfo["email"],
							),
							"contents" => $body
						);
						$tmp_buf[1] = $email_data->save_concept($draft_id, $mailreq);
						$tmp_buf[2] = $email_data->sendMailComplex($draft_id, 1, 1);
						
						/* send note to both old and new users */
						$note_data = new Note_data();
						if ($issueinfo["user_id"]) {
							$note["to"]      = $issueinfo["user_id"];
							$note["subject"] = gettext("support call classifified");
							$note["address_id"] = $issueinfo["address_id"];
							$note["project_id"] = $issueinfo["project_id"];
							$note["body"]    = addslashes(sprintf("%s\n\n<a href=\"javascript: popup('?mod=support&action=showitem&id=%d', 'support', 600, 450, 1);)\">%s</a>", 
								gettext("A support call has been assigned to your account."),
								$issueinfo["id"], 
								gettext("show details")
							));
							$note_data->store2db($note);
						}
						if ($issueinfo["old_user_id"]) {
							$note["to"]      = $issueinfo["old_user_id"];
							$note["subject"] = gettext("support call reassigned");
							$note["address_id"] = $issueinfo["address_id"];
							$note["project_id"] = $issueinfo["project_id"];
							$note["body"]    = addslashes(sprintf("%s\n\n<a href=\"javascript: popup('?mod=support&action=showitem&id=%d', 'support', 600, 450, 1);)\">%s</a>", 
								gettext("A support call has been reassigned to another user."),
								$issueinfo["id"], 
								gettext("show details")
							));
							$note_data->store2db($note);
						}
							
					}
					break;
				case "done":
					$txt = gettext("Support request with ticketnumber");
					$txt.= sprintf(": %s.\n\n", $issueinfo["reference_nr"]);
					$txt.= gettext("Your support request has been closed and marked as done. Below you find the dispatching and/or solution.");
					$txt.= sprintf("\n\n%s\n\n", $issueinfo["solution"]);
					$txt.= gettext("If you think the issue is still not fixed, please send an email to ");
					$txt.= sprintf("&nbsp;<a href=\"mailto:%1\$s\">%1\$s</a>", $email);
					$txt.= sprintf(" %1\$s <a href=\"mailto:%2\$s\">%2\$s</a>.\n\n", gettext("or"), $GLOBALS["covide"]->license["email"]);
					$txt.= gettext("Details of your original support request are listed below.")."\n\n";
					$txt.= $details;
					$mailreq = array(
						"view_mode" => "html",
						"mail" => array(
							"from" => $email,
							"subject" => gettext("support call closed"),
							"to" => $issueinfo["email"],
							"rcpt" => $issueinfo["email"],
						),
						"contents" => nl2br($txt)
					);
					$tmp_buf[1] = $email_data->save_concept($draft_id, $mailreq);
					$tmp_buf[2] = $email_data->sendMailComplex($draft_id, 1, 1);
	
					/* send note to currrent user */
					$note_data = new Note_data();
					if ($issueinfo["user_id"] && $issueinfo["user_id"] != $_SESSION["user_id"]) {
						$note["to"]      = $issueinfo["user_id"];
						$note["subject"] = gettext("support call closed");
						$note["address_id"] = $issueinfo["address_id"];
						$note["project_id"] = $issueinfo["project_id"];
						$note["body"]    = sprintf("%s\n\n%s\n\n<a href=\"javascript: popup('?mod=support&action=showitem&id=%d', 'support', 600, 450, 1);)\">%s</a>", 
							gettext("A support call assigned to you has been closed."),
							$issueinfo["id"], 
							gettext("show details")
						);
						$note_data->store2db($note);
					}
					
					break;
				case "reopened":
					$txt = gettext("Support request with ticketnumber");
					$txt.= sprintf(": %s.\n\n", $issueinfo["reference_nr"]);
					$txt.= gettext("Your support request has been reopened and assinged to");
					$txt.= sprintf(" %s.\n", $user_data->getUsernameById($issueinfo["user_id"]));
					$txt.= gettext("The email address of this support member is ");
					$txt.= sprintf("<a href=\"mailto:%1\$s\">%1\$s</a>.\n", $email);
					$txt.= gettext("Details of your support request are listed below.")."\n\n";
					$txt.= $details;

					$mailreq = array(
						"view_mode" => "html",
						"mail" => array(
							"from" => $email,
							"subject" => gettext("support call reopened"),
							"to" => $issueinfo["email"],
							"rcpt" => $issueinfo["email"],
						),
						"contents" => nl2br($txt)
					);
					$tmp_buf[1] = $email_data->save_concept($draft_id, $mailreq);
					$tmp_buf[2] = $email_data->sendMailComplex($draft_id, 1, 1);

					/* send note to currrent user */
					$note_data = new Note_data();
					if ($issueinfo["user_id"] && $issueinfo["user_id"] != $_SESSION["user_id"]) {
						$note["to"]      = $issueinfo["user_id"];
						$note["subject"] = gettext("support call reopened");
						$note["address_id"] = $issueinfo["address_id"];
						$note["project_id"] = $issueinfo["project_id"];
						$note["body"]    = sprintf("%s\n\n%s\n\n<a href=\"javascript: popup('?mod=support&action=showitem&id=%d', 'support', 600, 450, 1);)\">%s</a>", 
							gettext("A support call assigned to you has been reopened."),
							$issueinfo["id"], 
							gettext("show details")
						);
						$note_data->store2db($note);
					}
					break;
					
				default:
					die("not a valid action");
			}
		}
	}
	/* }}} */
	public function saveSupportForm($req) {

		$fields["timestamp"]     = mktime();
		$fields["body"]          = "'".$req["description"]."'";
		$fields["type"]          = (int)$req["type"];
		$fields["relation_name"] = "'".$req["name"]."'";
		$fields["email"]         = "'".$req["email"]."'";
		$fields["reference_nr"]  = "'".$req["reference_nr"]."'";
		
		$types = array(
			0 => gettext("no type"),
			1 => gettext("contact"),
			2 => gettext("question"),
			3 => gettext("complaint")
		);
		$desc = sprintf("%s: %s\n", gettext("type"), $types[$req["type"]]);
		$desc.= sprintf("%s: %s\n", gettext("date"), date("d-m-Y H:i"));
		$desc.= sprintf("%s: %s\n", gettext("email"), $req["email"]);
		$desc.= sprintf("%s: %s\n", gettext("reference nr"), $req["reference_nr"]);
		$desc.= sprintf("%s: %s\n", gettext("relation name"), $req["name"]);
		$desc.= sprintf("\n%s: %s\n", gettext("description"), $req["description"]);
		
		//in addressbook + bcards
		$address_data = new Address_data();
		$address = $address_data->getRelationsEmailArray();
		
		if ($address[strtolower($req["email"])] > 0) {
			$fields["customer_id"] = (int)$address[strtolower($req["email"])];
		}
		foreach ($fields as $k=>$v) {
			$keys[]=$k;
			$vals[]=$v;
		}
		
		$q = sprintf("insert into support (%s) values (%s)", 
			implode(",", $keys), implode(",", $vals));
		$res = sql_query($q);
		
		$email_data = new Email_data();
		if ($email_data->validateEmail($GLOBALS["covide"]->license["email"])) {
			mail(
				$GLOBALS["covide"]->license["email"], 
				gettext("new support call"), 
				gettext("There is a new support call in Covide")." ".$GLOBALS["covide"]->license["name"]."\n\n".$desc, 
				"From: ".$GLOBALS["covide"]->license["email"],
				"-f".$GLOBALS["covide"]->license["email"]
			);
		}

		// send mail to submitter
		$issueid = sql_insert_id($res);
		//$issueinfo = $this->getSupportItemById($issueid);
		$issueinfo = $this->getExternalIssues($issueid);
		$issuedata = $issueinfo[0];
		$issuedata["day"] = date("d", $issuedata["timestamp"]);
		$issuedata["month"] = date("m", $issuedata["timestamp"]);
		$issuedata["year"] = date("Y", $issuedata["timestamp"]);
		$issuedata["description"] = $issuedata["body"];

		$this->sendMail($issuedata, "insert");
		
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("
				parent.location.href='%s';
			", $req["result_url"]));
		$output->end_javascript();
		$output->exit_buffer();
	}
}
?>
