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

	$fetch = $this->getApcCache("addresslist");
	if ($fetch) {
		echo $fetch;
	} else {
		//$cms_license = $this->cms->getCmsSettings();
		$cms_license = $this->cms_license;

		if (!$cms_license["cms_address"]) {
			$this->triggerError(403);
			echo ("Module is disabled");
			return;
		}

		$table = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 3,
			"class" => "view_header table_data"
		));

		$list = $this->getCmsAddressList();

		$i = 0;
		$data = array(
			1 => array(),
			2 => array()
		);
		foreach ($list as $k=>$v) {
			$i++;
			$data[($i % 2 != 0) ? 1:2][] = array(
				"name"  => $v["name"],
				"count" => $v["count"],
				"id"    => $k
			);
		}

		$table->addTableHeader(array(
			"colspan" => 2,
			"style"   => "text-align: left;"
		));
			$table->addCode(gettext("Addresses"));
		$table->endTableHeader();

		for ($i=0; $i < count($data[1]); $i++) {
			$table->addTableRow(array(
				"class" => "list_record"
			));
				$table->addTableData();
					$table->insertTag("a", sprintf("%s (%d)", $data[1][$i]["name"], $data[1][$i]["count"]), array(
						"href" => sprintf("/addressdata/?address=%d", $data[1][$i]["id"])
					));
				$table->endTableData();
				$table->addTableData();
					$table->insertAction("rss", gettext("rss"), sprintf("/rss/address/%d|", $data[1][$i]["id"]));
					$table->addSpace();
				$table->endTableData();
				$table->addTableData();
					if ($data[2][$i])
					$table->insertTag("a", sprintf("%s (%d)", $data[2][$i]["name"], $data[2][$i]["count"]), array(
						"href" => sprintf("/addressdata/?address=%d", $data[2][$i]["id"])
					));
				$table->endTableData();
				$table->addTableData();
					$table->insertAction("rss", gettext("rss"), sprintf("/rss/address/%d|", $data[2][$i]["id"]));
					$table->addSpace();
				$table->endTableData();
			$table->endTableRow();
		}
		$table->endTable();
		$buffer = $table->generate_output();
		$this->setApcCache("addresslist", $buffer);
		echo $buffer;
	}
?>