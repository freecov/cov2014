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
		$rss_info = $rss_data->getFeeds();
		$output = new Layout_output();
		$output->layout_page();
			/* draw frame */
			$frame = new Layout_venster(array(
				"title"    => gettext("RSS"),
				"subtitle" => gettext("lijst")
			));
			$frame->addMenuItem(gettext("nieuwe feed"), "javascript: popup('index.php?mod=rss&action=editFeed', 'feededit');");
			$frame->addMenuItem(gettext("desktop"), "index.php?mod=desktop");
			$frame->generateMenuItems();
			$frame->addVensterData();
				/* view object */
				$view = new Layout_view();
				$view->addData($rss_info);
				$view->addMapping(gettext("naam"), "%name");
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
						"src"  => "edit",
						"link" => array("javascript: popup('index.php?mod=rss&action=editFeed&feedid=", "%id", "', 'feededit');"),
						"alt"  => gettext("aanpassen")
					),
					array(
						"type" => "action",
						"src"  => "delete",
						"link" => array("javascript: remove_feed(", "%id", ");"),
						"alt"  => gettext("verwijderen")
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

		$rss_data = new Rss_data();
		if ($feedid) 
			$feed_info = $rss_data->getFeeds($feedid);

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
				"subtitle" => gettext("bewerk")
			));
			$frame->addVensterData();
				/* table for alignment */
				$table = new Layout_table();
				$table->addTableRow();
					$table->insertTableData(gettext("naam"), "", "header");
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
					$table->insertTableData(gettext("aantal items op portal"), "", "header");
					$table->addTableData("", "data");
						$table->addSelectField("rss[count]", $count, $feed_info["count"]);
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->insertTableData("&nbsp;", "", "header");
					$table->addTableData("", "data");
						$table->insertAction("save", gettext("opslaan"), "javascript: document.getElementById('editRSS').submit();");
						if ($feedid) {
							$table->addSpace(3);
							$table->insertAction("cancel", gettext("verwijder"), "javascript: remove_feed($feedid);");
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
