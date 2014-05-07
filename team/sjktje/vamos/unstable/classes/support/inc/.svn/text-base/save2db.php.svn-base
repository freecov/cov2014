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
	if (!class_exists("Support_data")) {
		die("no class definition found");
	}
	$issueinfo = $_REQUEST["issue"];
	/* make array which can be imploded in sql queries */
	$fields = array();
	$values = array();
	$fields[] = "reference_nr";   $values[] = sprintf("%d",   $issueinfo["reference_nr"]);
	$fields[] = "email";          $values[] = sprintf("'%s'", $issueinfo["email"]);
	$fields[] = "registering_id"; $values[] = sprintf("%d",   $issueinfo["registering_id"]);
	$fields[] = "timestamp";      $values[] = sprintf("%d",   mktime(0, 0, 0, $issueinfo["month"], $issueinfo["day"], $issueinfo["year"]));
	$fields[] = "description";    $values[] = sprintf("'%s'", $issueinfo["description"]);
	$fields[] = "solution";       $values[] = sprintf("'%s'", $issueinfo["solution"]);
	$fields[] = "project_id";     $values[] = sprintf("%d",   $issueinfo["project_id"]);
	$fields[] = "user_id";        $values[] = sprintf("%d",   $issueinfo["user_id"]);
	$fields[] = "priority";       $values[] = sprintf("%d",   $issueinfo["priority"]);
	$fields[] = "is_solved";      $values[] = sprintf("%d",   $issueinfo["is_solved"]);
	$fields[] = "address_id";     $values[] = sprintf("%d",   $issueinfo["address_id"]);
	
	if ($issueinfo["id"]) {
		/* we are updating an item */
		$sql = "UPDATE issues SET ";
		foreach ($fields as $k=>$v) {
			$sql .= $v."=".$values[$k].", ";
		}
		$sql  = substr($sql, 0, strlen($sql)-2);
		$sql .= sprintf(" WHERE id=%d", $issueinfo["id"]);
		sql_query($sql);

		/* if email address has changed */
		if ($issueinfo["old_email"] != $issueinfo["email"] && $issueinfo["email"]) {
			$this->sendMail($issueinfo, "insert");
			if ($issueinfo["is_solved"]) {
				/* send done mail */
				$this->sendMail($issueinfo, "done");
			} else {
				/* send update mail */
				$this->sendMail($issueinfo, "update");
			}
		}
		
		if (!$issueinfo["is_solved"] && $issueinfo["old_is_solved"]) {
			/* send reopened mail when request is reopened */
			$this->sendMail($issueinfo, "reopened");
		} elseif ($issueinfo["is_solved"] && !$issueinfo["old_is_solved"]) {
			/* send done mail when request is solved */
			$this->sendMail($issueinfo, "done");
		} else {
			/* send update mail when request is changed and still open */
			$this->sendMail($issueinfo, "update");
		}
	} else {
		$sql = "INSERT INTO issues (".implode(",", $fields).") VALUES (".implode(",", $values).")";
		sql_query($sql);
		$issueinfo["id"] = sql_insert_id("issues");
		
		/* send email with insert message */
		$this->sendMail($issueinfo, "insert");
		
		if ($issueinfo["is_solved"]) {
			/* if closed, send done message */
			$this->sendMail($issueinfo, "done");
		} elseif ($issueinfo["user_id"]) {
			/* if executor */
			$this->sendMail($issueinfo, "update");
		}
		
		/* if support id is specified */
		$this->remove_ext_item($issueinfo["support_id"], 0, 1);
	}
	$output = new Layout_output();
	$output->start_javascript();
		$output->addCode(
			"
			opener.document.location.href = opener.document.location.href;
			window.close();
			"
		);
	$output->end_javascript();
	$output->exit_buffer();
?>
