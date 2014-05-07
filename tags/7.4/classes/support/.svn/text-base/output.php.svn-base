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
Class Support_output {
	/* constants */
	const include_dir = "classes/support/inc/";
	/* variables */
	/* methods */

	/* show_list_external {{{ */
	/**
	 * show list of issues filed by customers on the website
	 *
	 * You can put a form on your webpage that injects issues
	 * into the Covide database. This function will allow you to
	 * choose what to do with it
	 */
	public function show_list_external() {
		require(self::include_dir."showListExternal.php");
	}
	/* }}} */

    /* 	show_list {{{ */
    /**
     * 	Show list of issues filed in the internal database
     *
     */
	public function show_list() {
		require(self::include_dir."showList.php");
	}
	/* }}} */

    /* 	show_issue {{{ */
    /**
     * 	Show a specific issue
     *
     */
	public function show_issue() {
		require(self::include_dir."showIssue.php");
	}
	/* }}} */

    /* 	show_edit {{{ */
    /**
     * 	show_edit. Show screen to create/alter issue
     *
     */
	public function show_edit() {
		require(self::include_dir."showEdit.php");
	}
  /* }}} */
	/* showSupportForm {{{ */
	/**
	 * showSupportForm. Show the external support form
	 */
	public function showSupportForm($options) {
		require(self::include_dir."showSupportForm.php");
	}
	/* }}} */
	/* export {{{ */
	/**
	 * export support items to csv
	 *
	 * @param array $request The $_REQUEST data
	 */
	public function export($request) {

		if (!$request["what_to_export"]) {
			$output = new Layout_output();
			$output->layout_page("", 1);
			$venster = new Layout_venster(array(
				"title" => gettext("support"),
				"subtitle" => gettext("export")
			));
			$venster->addVensterData();
				$venster->insertTag("b", gettext("What do you want to export")."?");
				$venster->addTag("br");
				$venster->addTag("form", array(
					"action" => "index.php",
					"id"     => "exportfrm"
				));
				$venster->addHiddenField("mod", "support");
				$venster->addHiddenField("action", "export");
				$venster->addHiddenField("dl", "1");
				$sel = array(
					"active"   => gettext("active issues"),
					"inactive" => gettext("resolved issues"),
					"all"      => gettext("all")
				);
				$venster->addSelectField("what_to_export", $sel, "active");
				$venster->addHiddenField("dl", "1");
				$venster->addTag("br");
				$venster->addTag("hr");
				$venster->insertTag("b", gettext("filter by").": ");
				$venster->addTag("br");

				$venster->addCode(gettext("relation").": ");
				$venster->addHiddenField("filter[address_id]", "");
				$venster->insertTag("span", $address_info, array("id"=>"layer_relation"));
				$venster->insertAction("edit", "wijzigen", "javascript: popup('?mod=address&action=searchRel', 'search_address');", 700, 600, 1);
				$venster->addTag("br");
				$venster->addTag("br");

				$venster->addCode(gettext("project").": ");
				$venster->addHiddenField("filter[project_id]", "");
				$venster->insertTag("span", "", array("id"=>"layer_project"));
				$venster->insertAction("edit", gettext("change:"), "javascript: pickProject();");
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addTag("br");

				$venster->addCode(gettext("user").": ");
				$venster->addHiddenField("filter[user_id]", "");
				$useroutput = new User_output();
				$venster->addCode( $useroutput->user_selection("filteruser_id", "", 0, 0, 1) );
				unset($useroutput);
				$venster->addTag("br");
				$venster->addTag("hr");


				$venster->insertTag("b", sprintf("%s (%s)", gettext("sort export by"), gettext("1st/2nd")));
				$venster->addTag("br");
				$venster->addTag("br");
				$sel = array(
					"timestamp"   => gettext("date"),
					"username"    => gettext("user name"),
					"priority"    => gettext("priority"),
					"companyname" => gettext("address name"),
					"projectname" => gettext("project")
				);
				$venster->addSelectField("sort_by_1", $sel, "timestamp");
				$venster->addCode(", ".gettext("then")." ");
				$venster->addSelectField("sort_by_2", $sel, "username");

				$venster->addTag("br");
				$venster->addTag("br");
				$venster->insertAction("close", gettext("close"), "javascript: window.close();");
				$venster->insertAction("forward", gettext("export"), "javascript: document.getElementById('exportfrm').submit();");
				$venster->endTag("form");
			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			$output->load_javascript(self::include_dir."showExport.js");
			$output->layout_page_end();
			$output->exit_buffer();
			exit;
		}

		/* get the data */
		$options = array();
		$options["nolimit"] = 1;
		switch ($request["what_to_export"]) {
			case "active"   : $options["active"] = 1;    break;
			case "inactive" : $options["active"] = 0;    break;
			case "all"      : unset($options["active"]); break;
		}
		$options["sort"]   = $_REQUEST["sort_by_1"];
		$options["sort2"]  = $_REQUEST["sort_by_2"];
		$options["filter"] = $_REQUEST["filter"];

		$support_data = new Support_data();
		$support_items_data = $support_data->getSupportItems($options);

		$support_items = $support_items_data["items"];
		unset($support_items_data);
		/* load conversion to be able to convert an array to a csv record */
		$conversion = new Layout_conversion();

		$csv   = array();
		$csv[] = gettext("date");
		$csv[] = gettext("reference nr");
		$csv[] = gettext("priority");
		$csv[] = gettext("email");
		$csv[] = gettext("done");
		$csv[] = gettext("executor");
		$csv[] = gettext("user");
		$csv[] = gettext("contact");
		$csv[] = gettext("project");
		$csv[] = gettext("complaint/incident");
		$csv[] = gettext("dispatching");
		$data = $conversion->generateCSVRecord($csv);
		foreach ($support_items as $item) {
			$csv = array();
			switch ($item["priority"]) {
				case 0: $item["priority"] = gettext("not specified"); break;
				case 1: $item["priority"] = gettext("high"); break;
				case 2: $item["priority"] = gettext("medium"); break;
				case 3: $item["priority"] = gettext("low"); break;
			}
			$csv[] = $item["human_date"];
			$csv[] = ($item["reference_nr"]) ? $item["reference_nr"]:gettext("none");
			$csv[] = $item["priority"];
			$csv[] = $item["email"];
			$csv[] = ($item["is_solved"]) ? gettext("yes"):gettext("no");
			$csv[] = $item["rcpt_name"];
			$csv[] = $item["sender_name"];
			$csv[] = ($item["relname"]) ? $item["relname"]:gettext("none");
			$csv[] = ($item["projectname"]) ? $item["projectname"]:gettext("none");
			$csv[] = trim(preg_replace("/(\t|\r)/s", "", $item["description"]));
			$csv[] = trim(preg_replace("/(\t|\r)/s", "", $item["solution"]));
			$data .= $conversion->generateCSVRecord($csv);
			unset($csv);
		}

		unset($conversion);

		/* send headers and data to client to give them a download prompt */
		header("Content-Transfer-Encoding: binary");
		header("Content-Type: text/plain; charset=UTF-8");

		$file = "support_export.csv";
		if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) {
			header("Content-Disposition: filename=\"".$file."\"");
		} else {
			header("Content-Disposition: attachment; filename=\"".$file."\"");
		}
		echo $data;
		exit();
	}
	/* }}} */
}
?>
