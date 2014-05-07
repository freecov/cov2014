<?php
	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("authorisaties")
	));
	$pageid = $id;

	$cms_data = new Cms_data();
	$cms = $cms_data->getAccountList($id);
	$cms = $cms[0];

	$this->addMenuItems($venster);
	$venster->generateMenuItems();

	$xs = array(
		"D" => gettext("geen rechten"),
		"R" => gettext("bekijken"),
		"U" => gettext("wijzigen"),
		"W" => gettext("toevoegen")."/".gettext("verwijderen"),
		"F" => gettext("volledig")
	);
	$venster->start_javascript();
		$venster->addCode("function hl_disable(user) {");
		foreach ($xs as $k=>$p) {
			$venster->addCode(" document.getElementById('td_'+user+'_$k').style.backgroundColor = ''; ");
		}
		$venster->addCode("}");
	$venster->end_javascript();

	$venster->addVensterData();
		$tbl = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1
		));
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("gebruiker"));
			$tbl->endTableData();
			foreach ($xs as $k=>$v) {
				$tbl->addTableData(array(
					"style" => "width: 120px; text-align: center;"
				), "header");
					$tbl->addCode($v);
				$tbl->endTableData();
			}
		$tbl->endTableRow();

		$user_data = new User_data();
		$auth = $cms_data->getAuthorisations($pageid);

		/* Covide groups */
		unset($list);
		$list = $user_data->getGroupList();
		$tbl->addTableRow();
			$tbl->addTableData(array(
				"colspan" => 6,
				"style"   => "text-align: center;"
			), "header");
				$tbl->addCode(gettext("CMS Groepen"));
			$tbl->endTableData();
		$tbl->endTableRow();
		foreach ($list as $id=>$group) {
			$id = "G".$id;
			$tbl->addTableRow();
				$tbl->addTableData("", "header");
					$tbl->addTag("li", array("class"=>"group"));
					$tbl->addCode($group["name"]);
				$tbl->endTableData();
				foreach ($xs as $k=>$v) {
					$tbl->addTableData(array(
						"style" => "text-align: center; ",
						"id"    => "td_".$id."_".$k
					), "data");
					if (!$auth[$id]) $auth[$id] = "D";
					$tbl->addRadioField("auth[$id]", "", $k, $auth[$id], "", "hl_auth('$id', '$k');");
					$tbl->endTableData();
					if ($auth[$id] == $k) {
						$tbl->start_javascript();
							$tbl->addCode("document.getElementById('td_".$id."_".$k."').style.backgroundColor = '#dddddd';");
						$tbl->end_javascript();
					}
				}
			$tbl->endTableRow();
		}


		/* Covide users */
		unset($list);
		$list = $user_data->getUserList();
		$tbl->addTableRow();
			$tbl->addTableData(array(
				"colspan" => 6,
				"style"   => "text-align: center;"
			), "header");
				$tbl->addCode(gettext("Covide gebruikers met CMS rechten"));
			$tbl->endTableData();
		$tbl->endTableRow();
		foreach ($list as $id=>$name) {
			$perm = $user_data->getUserPermissionsById($id);
			if ($perm["xs_cms_level"] > 0) {

				$tbl->addTableRow();
					$tbl->addTableData("", "header");
						$tbl->addTag("li", array("class"=>"enabled"));
						$tbl->addCode($name);
					$tbl->endTableData();
					foreach ($xs as $k=>$v) {
						$tbl->addTableData(array(
							"style" => "text-align: center; ",
							"id"    => "td_".$id."_".$k
						), "data");
							if ($perm["xs_cms_level"] < 2) {
								/* if access level is smaller than 2 */
								if (!$auth[$id]) $auth[$id] = "D";
								$tbl->addRadioField("auth[$id]", "", $k, $auth[$id], "", "hl_auth('$id', '$k');");
							} else {
								if (($k == "F" && $perm["xs_cms_level"] == 3)
									|| ($k == "F" && $perm["xs_cms_level"] == 2)) {
									$tbl->addCode($cms_data->cms_xs_levels[$perm["xs_cms_level"]]);
								} else {
									$tbl->addCode("--");
								}
							}
						$tbl->endTableData();
						if (($auth[$id] == $k)
							|| ($k == "F" && $perm["xs_cms_level"] == 3)
							|| ($k == "F" && $perm["xs_cms_level"] == 2)) {
							$tbl->start_javascript();
								$tbl->addCode("document.getElementById('td_".$id."_".$k."').style.backgroundColor = '#dddddd';");
							$tbl->end_javascript();
						}
					}
				$tbl->endTableRow();
			}
		}
		/* CMS guests */
		unset($list);
		$list = $cms_data->getAccountList();
		$tbl->addTableRow();
			$tbl->addTableData(array(
				"colspan" => 6,
				"style"   => "text-align: center;"
			), "header");
				$tbl->addCode(gettext("CMS bezoekers"));
			$tbl->endTableData();
		$tbl->endTableRow();

		foreach ($list as $cuser) {
			$id =& $cuser["id"];
			$id = "U".$id;

			$tbl->addTableRow();
				$tbl->addTableData("", "header");
					$tbl->addTag("li", array("class"=>"special"));
					$tbl->addCode($cuser["username"]);
				$tbl->endTableData();
				foreach ($xs as $k=>$v) {
					$tbl->addTableData(array(
						"style" => "text-align: center; ",
						"id"    => "td_".$id."_".$k
					), "data");
						if (!$auth[$id]) $auth[$id] = "D";
						if ($k == "D" || $k == "R") {
							$tbl->addRadioField("auth[$id]", "", $k, $auth[$id], "", "hl_auth('$id', '$k');");
						} else {
							$tbl->addCode("--");
						}
					$tbl->endTableData();
					if ($auth[$id] == $k) {
						$tbl->start_javascript();
							$tbl->addCode("document.getElementById('td_".$id."_".$k."').style.backgroundColor = '#dddddd';");
						$tbl->end_javascript();
					}
				}
			$tbl->endTableRow();
		}

		$tbl->endTable();

		$venster->addCode($tbl->generate_output());

		$venster->insertAction("save", gettext("opslaan"), "javascript: saveSettings();");
		$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveAuthorisations");
	$output->addHiddenField("id", $pageid);

	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();

?>