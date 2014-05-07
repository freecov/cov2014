<?php
/**
 * Covide Groupware-CRM privoxy config module
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
Class Privoxyconf_output {
	/* constants */
	const include_dir = "classes/privoxyconf/inc/";
	/* variables */
	/* methods */
	public function showList() {
		/* get current configdata */
		$privoxy_data = new Privoxyconf_data();
		$configdata   = $privoxy_data->read_configfile();
		
		/* start output buffer */
		$output = new Layout_output();
		$output->layout_page();
		/* frame */
		$frame = new Layout_venster(array("title" => gettext("Proxy"), "subtitle" => gettext("whitelist")));
		$frame->addMenuItem(gettext("back"), "?mod=user");
		$frame->generateMenuItems();
		$frame->addVensterData();
			/* ugly way, but cant be bothered to transform the data so it can fit in a view object */
			$table = new Layout_table(array("cellspacing" => 1));
			$table->addTableRow();
				$table->insertTableData(gettext("domain"), "", "header");
				$table->insertTableData(gettext("actions"), "", "header");
			$table->endTableRow();
			foreach ($configdata as $k=>$v) {
				$table->addTableRow();
					$table->addTableData("", "data");
						$table->addCode($v);
					$table->endTableData();
					$table->addTableData("", "data");
						$table->insertAction("edit", gettext("edit"), "javascript: editSite('$v');");
						$table->insertAction("delete", gettext("delete"), "javascript: deleteSite('$v');");
					$table->endTableData();
				$table->endTableRow();
			}
			$table->addTableRow();
				$table->addTableData(array("colspan" => 2), "data");
					$table->insertAction("new", gettext("add"), "javascript: addSite();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$frame->addCode($table->generate_output());
			unset($table);
		$frame->endVensterData();
		$output->addCode($frame->generate_output());
		unset($frame);
		$output->load_javascript(self::include_dir."showList.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}
}
?>
