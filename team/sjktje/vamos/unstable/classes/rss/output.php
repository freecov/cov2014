<?php
/**
 * Covide Groupware-CRM RSS module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Rss_output {
	/* constants */
	const include_dir = "classes/rss/inc/";

	/* variables */

	/* methods */
	/* listFeeds {{{ */
	/**
	 * Generate a list of all configured rss feeds
	 */
	public function listFeeds() {
		/* get the configured feeds */
		$rss_data = new Rss_data();
		$rss_info = $rss_data->getFeeds($_SESSION["user_id"],1);
		$output = new Layout_output();
		$output->layout_page();
			/* draw frame */
			$frame = new Layout_venster(array(
				"title"    => gettext("RSS"),
				"subtitle" => gettext("list")
			));
			$frame->addMenuItem(gettext("new feed"), "javascript: popup('index.php?mod=rss&action=editFeed', 'feededit');");
			$frame->addMenuItem(gettext("desktop"), "index.php?mod=desktop");
			$frame->generateMenuItems();
			$frame->addVensterData();
				/* view object */
				$view = new Layout_view();
				$view->addData($rss_info);
				$view->addMapping(gettext("name"), "%name");
				$view->addMapping(gettext("homepage"), "%%complex_homepage");
				$view->addMapping("&nbsp;", "%%complex_actions");

				$view->defineComplexMapping("complex_homepage", array(
					array(
						"type" => "link",
						"link" => "%homepage",
						"text" => "%homepage"
					)
				));
				$view->defineComplexMapping("complex_actions", array(
					array(
						"type" => "action",
						"src"  => "state_public",
						"alt"  => gettext("public"),
						"check" => "%is_public"
					),
					array(
						"type" => "action",
						"src"  => "state_private",
						"alt"  => gettext("private"),
						"check" => "%is_private"
					),
					array(
						"type" => "action",
						"src"  => "edit",
						"link" => array("javascript: popup('index.php?mod=rss&action=editFeed&feedid=", "%id", "', 'feededit');"),
						"alt"  => gettext("change")
					),
					array(
						"type" => "action",
						"src"  => "delete",
						"link" => array("javascript: remove_feed(", "%id", ");"),
						"alt"  => gettext("delete")
					)
				));
				$frame->addCode($view->generate_output());
				unset($view);
			$frame->endVensterData();
			$output->addCode($frame->generate_output());
			unset($frame);
		$output->load_javascript(self::include_dir."rss_actions.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* editFeed {{{ */
	/**
	 * Show screen to edit/create a new rss feed in the config.
	 *
	 * @param int $feedid Optional feed id. if set, it will edit this feed. if 0 it will create a new feed
	 */
	public function editFeed($feedid=0) {
		$count = array();
		for ($i=1; $i<10; $i++)
			$count[$i] = $i;

		$user_data = new User_data();
		$userinfo = $user_data->getUserdetailsById($_SESSION["user_id"]); 
		$rss_data = new Rss_data();
		if ($feedid) 
			$feed_info = $rss_data->getFeedById($feedid);
		if($feedid != 0 && $feed_info["user_id"] != $_SESSION["user_id"] && $userinfo["xs_usermanage"] != 1 ) { die(gettext("Access denied")); }
		$output = new Layout_output();
		$output->layout_page("", 1);
			/* form */
			$output->addTag("form", array(
				"id" => "editRSS",
				"method" => "get",
				"action" => "index.php"
			));
			$output->addHiddenField("mod", "rss");
			$output->addHiddenField("action", "saveFeed");
			$output->addHiddenField("rss[id]", $feedid);

			$frame = new Layout_venster(array(
				"title"    => gettext("RSS"),
				"subtitle" => gettext("edit")
			));
			$frame->addVensterData();
				/* table for alignment */
				$table = new Layout_table();
				$table->addTableRow();
					$table->insertTableData(gettext("name"), "", "header");
					$table->addTableData("", "data");
						$table->addTextField("rss[name]", $feed_info["name"], array("style" => "width: 400px;"));
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->insertTableData(gettext("homepage"), "", "header");
					$table->addTableData("", "data");
						$table->addTextField("rss[homepage]", $feed_info["homepage"], array("style" => "width: 400px;"));
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->insertTableData(gettext("rss url"), "", "header");
					$table->addTableData("", "data");
						$table->addTextField("rss[url]", $feed_info["url"], array("style" => "width: 400px;"));
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->insertTableData(gettext("number of items on portal"), "", "header");
					$table->addTableData("", "data");
						$table->addSelectField("rss[count]", $count, $feed_info["count"]);
					$table->endTableData();
				$table->endTableRow();
			if ($userinfo["xs_usermanage"]) {
				if($feed_info["user_id"] == 0) { $publicRss = 1; } else { $publicRss = 0; }
						$table->addTableRow();
					$table->insertTableData(gettext("publically"), "", "header");
					$table->addTableData("", "data");
						$table->insertCheckbox("rss[user_id]", "0", $publicRss, 0);
					$table->endTableData();
				$table->endTableRow();
			}
				$table->addTableRow();
					$table->insertTableData("&nbsp;", "", "header");
					$table->addTableData("", "data");
						$table->insertAction("save", gettext("save"), "javascript: document.getElementById('editRSS').submit();");
						if ($feedid) {
							$table->addSpace(3);
							$table->insertAction("cancel", gettext("remove"), "javascript: remove_feed($feedid);");
						}
					$table->endTableData();
				$table->endTableRow();
				$table->endTable();
				$frame->addCode($table->generate_output());
				unset($table);
			$frame->endVensterData();
			$output->addCode($frame->generate_output());
			unset($frame);

			/* end form */
			$output->endTag("form");
		/* end page and flush to client */
		$output->load_javascript(self::include_dir."rss_actions.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* closepopup {{{ */
	public function closepopup() {
		$output = new Layout_output();
		$output->layout_page("", 1);
			$output->start_javascript();
				$output->addCode("
					opener.location.href=opener.location.href;
					window.close();
				");
			$output->end_javascript();
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
}
?>
