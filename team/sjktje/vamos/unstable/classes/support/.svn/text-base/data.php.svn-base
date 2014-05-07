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
 * @copyright Copyright 2000-2007 Covide BV
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
	public function getExternalIssues($id="") {
		require(self::include_dir."dataGetExternalIssues.php");
		return $issues;
	}
	/* }}} */
	/* remove_ext_item {{{ */
	public function remove_ext_item($id, $returnxml=0, $returnnone=0) {
		$sql = sprintf("DELETE FROM support WHERE id = %d", $id);
		$res = sql_query($sql);
		if ($returnxml) {
			echo "reload_doc();";
			exit;
		} elseif (!$returnnone) {
			header("Location: index.php?mod=support&action=list_external");
			exit;
		}
	}
	/* }}} */
    /* 	save2db  {{{ */
    /**
     * 	save2db . save issue to db
     */
	public function save2db () {
		require(self::include_dir."save2db.php");
	}
  /* }}} */
	
	public function sendMail($issueinfo, $type) {
		if ($issueinfo["email"]) {
			$email_data = new Email_data();
			/* check if license email flag is valid */
			if (!$email_data->validateEmail($GLOBALS["covide"]->license["email"])) {
				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("
						alert(gettext('There is a problem sending the email: license flag email is not set, please contact your system administrator.');
						opener.document.location.href = opener.document.location.href;
						window.close();
					");
				$output->end_javascript();			
				$output->exit_buffer();
			}
			/* check if issueinfo[email] is valid */
			if (!$email_data->validateEmail($issueinfo["email"])) {
				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("
						alert(gettext('There is a problem sending the email: the given email address is not valid.');
						opener.document.location.href = opener.document.location.href;
						window.close();
					");
				$output->end_javascript();			
				$output->exit_buffer();
			}
			
			$details = sprintf("%s: %s\n", gettext("reference nr"), $issueinfo["reference_nr"]);
			$details.= sprintf("%s: %s\n", gettext("registration date"), date("d-m-Y", mktime(0, 0, 0, $issueinfo["month"], $issueinfo["day"], $issueinfo["year"])));
			$details.= sprintf("%s: %s\n\n", gettext("current date and time"), date("d-m-Y H:i"));
			$details.= sprintf("%s:\n\n%s\n", gettext("complaint/incident"), $issueinfo["description"]);
			
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
					$output->start_javascript();
						$output->addCode("
							alert(gettext('There is a problem sending the email: the selected employee / user has no valid email primary addresses.');
							opener.document.location.href = opener.document.location.href;
							window.close();
						");
					$output->end_javascript();			
					$output->exit_buffer();
				}
			}
			
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
					$txt.= sprintf(": %d.\n\n", $issueinfo["reference_nr"]);
					$txt.= gettext("Your request will be processed by our support department.");
					$txt.= gettext("Our support department will contact you and you will get an email with the name of the support technician who will work on your ticket.");
					$txt.= gettext("If you contact us or reply to questions please use the ticketnumber in the subject or body.")."\n\n";
					$txt.= gettext("We will process your ticket in a timely manner, but feel free to contact us if you think things are stalling.")."\n\n";
					$txt.= gettext("You will find the details of this support request below this line.")."\n\n";
					$txt.= $details;
					
					$headers = sprintf("From: %s\n", $GLOBALS["covide"]->license["email"]);
					/* send only if not solved */
					mail($issueinfo["email"], gettext("support call registered"), $txt, $headers, sprintf("-f%s", $GLOBALS["covide"]->license["email"]));
					break;
				case "update":
					/*
					  Uw supportaanvraag met als referentienummer: 445988 is geclassificeerd 
						als klacht en zal worden behandeld door christian. Het emailadres van 
						deze medewerker is christian@terrazur.nl.
					 */
					if ($issueinfo["old_user_id"] != $issueinfo["user_id"]) {
						$txt = gettext("Support request with ticketnumber");
						$txt.= sprintf(": %d.\n\n", $issueinfo["reference_nr"]);
						$txt.= gettext("Your support request has been classified as issue/complain and is beeing processed by ");
						$txt.= sprintf("%s.\n", $user_data->getUsernameById($issueinfo["user_id"]));
						$txt.= gettext("The email address of this support member is ");
						$txt.= sprintf("%s.\n", $email);
						$txt.= gettext("Details of your support request are listed below.")."\n\n";
						$txt.= $details;
						$headers = sprintf("From: %s\n", $email);
						mail($issueinfo["email"], gettext("support call classified"), $txt, $headers, sprintf("-f%s", $email));
						
						/* send note to both old and new users */
						$note_data = new Note_data();
						if ($issueinfo["user_id"]) {
							$note["to"]      = $issueinfo["user_id"];
							$note["subject"] = gettext("support call classifified");
							$note["body"]    = sprintf("%s\n\n%s\n\n<a href=\"javascript: popup('?mod=support&action=showitem&id=%d', 'support', 600, 450, 1);)\">%s</a>", 
								gettext("A support call has been assigned to your account."),
								$issueinfo["id"], 
								gettext("show details")
							);
							$note_data->store2db($note);
						}
						if ($issueinfo["old_user_id"]) {
							$note["to"]      = $issueinfo["old_user_id"];
							$note["subject"] = gettext("support call reassigned");
							$note["body"]    = sprintf("%s\n\n%s\n\n<a href=\"javascript: popup('?mod=support&action=showitem&id=%d', 'support', 600, 450, 1);)\">%s</a>", 
								gettext("A support call has been reassigned to another user."),
								$issueinfo["id"], 
								gettext("show details")
							);
							$note_data->store2db($note);
						}
							
					}
					break;
				case "done":
					$txt = gettext("Support request with ticketnumber");
					$txt.= sprintf(": %d.\n\n", $issueinfo["reference_nr"]);
					$txt.= gettext("Your support request has been closed and marked as done. Below you find the dispatching and/or solution.");
					$txt.= sprintf("\n\n%s\n\n", $issueinfo["solution"]);
					$txt.= gettext("If you think the issue is still not fixed, please send an email to ");
					$txt.= sprintf("%s or %s.\n\n", $email, $GLOBALS["covide"]->license["email"]);
					$txt.= gettext("Details of your original support request are listed below.")."\n\n";
					$txt.= $details;
					$headers = sprintf("From: %s\n", $email);
					mail($issueinfo["email"], gettext("support call closed"), $txt, $headers, sprintf("-f%s", $email));
					
					/* send note to currrent user */
					$note_data = new Note_data();
					if ($issueinfo["user_id"] && $issueinfo["user_id"] != $_SESSION["user_id"]) {
						$note["to"]      = $issueinfo["user_id"];
						$note["subject"] = gettext("support call closed");
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
					$txt.= sprintf(": %d.\n\n", $issueinfo["reference_nr"]);
					$txt.= gettext("Your support request has been reopened and assinged to");
					$txt.= sprintf(" %s.\n", $user_data->getUsernameById($issueinfo["user_id"]));
					$txt.= gettext("The email address of this support member is ");
					$txt.= sprintf("%s.\n", $email);
					$txt.= gettext("Details of your support request are listed below.")."\n\n";
					$txt.= $details;
					$headers = sprintf("From: %s\n", $email);
					mail($issueinfo["email"], gettext("support call reopened"), $txt, $headers, sprintf("-f%s", $email));

					/* send note to currrent user */
					$note_data = new Note_data();
					if ($issueinfo["user_id"] && $issueinfo["user_id"] != $_SESSION["user_id"]) {
						$note["to"]      = $issueinfo["user_id"];
						$note["subject"] = gettext("support call reopened");
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
	
	public function saveSupportForm($req) {

		$fields["timestamp"]     = mktime();
		$fields["body"]          = "'".$req["description"]."'";
		$fields["type"]          = (int)$req["type"];
		$fields["relation_name"] = "'".$req["name"]."'";
		$fields["email"]         = "'".$req["email"]."'";
		$fields["reference_nr"]  = (int)$req["reference_nr"];
		
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
		sql_query($q);
		
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
