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

	$cleanup = time() - 60*60*24;
	$q = sprintf("delete from cms_users where is_enabled = 0 and is_active = 0 and registration_date <= %d and confirm_hash != ''",
		$cleanup);
	sql_query($q);

	$key = sprintf("s:{%s} p:{%s}", session_id(), $pageid);
	$challenge = md5(rand().session_id().time());

	/* check for list entry */
	$q = sprintf("select count(*) from cms_temp where userkey = '%s'", $key);
	$res = sql_query($q);
	$found = sql_result($res,0);

	if ($found == 1) {
		$q = sprintf("update cms_temp set datetime = %d, ids = '%s' where userkey = '%s'",
			time(), $challenge, $key);
		sql_query($q);
	} else {
		$q = sprintf("delete from cms_temp where userkey = '%s'", $key);
		sql_query($q);

		$q = sprintf("insert into cms_temp (userkey, ids, datetime) values ('%s', '%s', %d)",
			$key, $challenge, time());
		sql_query($q);
	}

	$output = new Layout_output();
	$output->addTag("br");
	$output->addCode("<a name=\"pagelist\" class=\"anchor\"></a>");
	$output->addTag("hr");
	$output->insertAction("toggle", "", "");
	$output->addSpace();
	$output->insertTag("b", gettext("Messages on this page").": ");
	$output->addTag("br");
	$output->addTag("br");

	/* check some permissions for Covide users */
	if ($_SESSION["user_id"]) {
		/* get page permissions */
		$user_data = new User_data();
		$user_perm = $user_data->getUserDetailsById($_SESSION["user_id"]);
		if (in_array($user_perm["xs_cms_level"], array(2,3))) {
			$xs_delete = 1;
		} else {
			$perm = $this->cms->getUserPermissions($this->pageid, $_SESSION["user_id"]);
			if ($perm["manageRight"])
				$xs_delete = 1;
		}
	}
	if ($_REQUEST["delitem"] && $xs_delete) {
		$q = sprintf("delete from cms_feedback where id = %d and page_id = %d",
			$_REQUEST["delitem"], $this->pageid);
		sql_query($q);
	}

	/* current feedback items */
	$pagesize = 5; //$this->pagesize;
	$start    = $_REQUEST["start"];
	if (!$start)
		$start = 0;

	/* next and prev results uri */
	$next_results = "/feedback/".$id."&amp;mode=".$_REQUEST["mode"]."&amp;start=%%#pagelist";

	$items = $this->cms->getFeedbackItems($this->pageid, $start, $pagesize);
	$tbl = new Layout_table(array(
		"class" => "view_header table_data",
		"cellpadding" => "3",
		"cellspacing" => "1",
		"style" => "width: 100%;"
	));
	$user_data = new User_data();

	if (!is_array($items["data"]))
		$items["data"] = array();

	if (!$items["count"]) {
		$tbl->addTag("tbody");
		$tbl->addTableRow();
			$tbl->addTableData(array("class" => "list_record"));
				$tbl->addCode(gettext("This page has no messages yet"));
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	foreach ($items["data"] as $d) {
		$tbl->addTag("tbody");
		$tbl->addTableRow();
			$tbl->addTableHeader(array("class" => "list_header", "align" => "left"));
				$tbl->addCode($d["subject"]);
			$tbl->endTableHeader();
			$tbl->addTableHeader(array("class" => "list_header", "align" => "left"));
				$tbl->addSpace(2);
				$tbl->insertAction("calendar_today", "", "");
				$tbl->addSpace();
				$tbl->addCode(date("d-m-Y H:i", $d["datetime"]));
				$tbl->addSpace(2);
			$tbl->endTableHeader();
			$tbl->addTableHeader(array("class" => "list_header", "align" => "left"));
				if ($d["is_visitor"])
					$tbl->insertAction("state_public", "", "");
				else
					$tbl->insertAction("state_special", "", "");
					$tbl->addSpace();
					if ($d["is_visitor"]) {
						$acc = $this->cms->getAccountList($d["user_id"]);
						if ($acc[0]["username"])
							$tbl->addCode($acc[0]["username"]);
						else
							$tbl->addCode(sprintf("[%s]", gettext("unknown user")));
					} else {
						$tbl->addCode($user_data->getUsernameById($d["user_id"])." (covide)");
					}

					if ($xs_delete) {
						$tbl->addSpace();
						$tbl->insertAction("delete", gettext("delete item"),
							str_replace("&amp;", "&", str_replace("%%", $start."&amp;delitem=".$d["id"], $next_results))
						);
					}
			$tbl->endTableHeader();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("class" => "list_record", "colspan" => 3));
				$tbl->addCode(nl2br(trim($d["body"])));
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->endTag("tbody");
	$tbl->endTable();
	$output->addCode($tbl->generate_output());

	if ($items["count"] > count($items["data"])) {
		$paging = new Layout_paging();
		$paging->setOptions($start, $items["count"], $next_results, $pagesize, 1);
		$output->addTag("br");
		$output->addcode($paging->generate_output());
		$output->addTag("br");
	}


	$output->addTag("br");
	$output->insertAction("mail_new", "", "");
	$output->addSpace();
	$output->insertTag("b", gettext("React").":");
	$output->addTag("br");
	$output->addTag("br");

	$output->addTag("div", array(
		"id" => "give_feedback_message"
	));
	$output->addCode(gettext("To leave your message on this page").", ");
	$output->insertTag("a", gettext("click here"), array(
		"href" => "javascript: showFeedbackForm();"
	));
	$output->endTag("div");

	$output->addTag("div", array(
		"id" => "give_feedback_layer",
		"style" => "display: none"
	));
	$output->insertTag("a", "", array("name" => "feedback_position"));

	if ($_SESSION["user_id"] || $_SESSION["visitor_id"]) {
		$output->addTag("form", array(
			"action" => "site.php",
			"method" => "get",
			"target" => "formhandler",
			"id"     => "formident"
		));
	}

	$output->addHiddenField("system[pageid]", $pageid);
	$output->addHiddenField("system[challenge]", $challenge);
	$output->addHiddenField("mode", "feedback");

	$tbl = new Layout_table();
		if (!$_SESSION["user_id"] && !$_SESSION["visitor_id"]) {
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addCode($this->triggerLogin($pageid, 1, 1));
				$tbl->endTableData();
			$tbl->endTableRow();
		} else {
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addCode(gettext("Subject").": ");
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->addTextField("feedback[subject]", "");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData(array("valign" => "top"));
					$tbl->addCode(gettext("Content").": ");
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->addTextArea("feedback[body]", "");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan" => 2, "style" => "text-align: right"));
					$tbl->insertAction("save", gettext("save"), "javascript:document.getElementById('formident').submit();");
				$tbl->endTableData();
			$tbl->endTableRow();
		}
	$tbl->endTable();
	$output->addCode($tbl->generate_output());

	if ($_SESSION["user_id"] || $_SESSION["visitor_id"])
		$output->endTag("form");

	$output->endTag("div");

	$output->addTag("iframe", array(
		"id"     => "formhandler",
		"name"   => "formhandler",
		"src"    => "blank.htm",
		"width"  => "0px",
		"frameborder" => 0,
		"border" => 0,
		"height" => "0px;",
		"visiblity" => "hidden"
	));
	$output->endTag("iframe");

	$data .= $output->generate_output();
?>
