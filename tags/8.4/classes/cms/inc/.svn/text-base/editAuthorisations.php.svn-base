<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Cms_output")) {
		die("no class definition found");
	}

	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("authorization")
	));
	$pageid = $id;

	$cms_data = new Cms_data();

	$cms_data->updateAuthorisations($_REQUEST["subact"], $id);
	$cms = $cms_data->getAccountList($id);
	$cms = $cms[0];

	$this->addMenuItems($venster);
	$venster->generateMenuItems();

	$xs = array(
		"D" => gettext("no permissions"),
		"R" => gettext("view"),
		"U" => gettext("change:"),
		"W" => gettext("add")."/".gettext("delete"),
		"F" => gettext("complete")
	);
	$venster->start_javascript();
		$venster->addCode("function hl_disable(user) {");
		foreach ($xs as $k=>$p) {
			$venster->addCode(" document.getElementById('td_'+user+'_$k').style.backgroundColor = ''; ");
		}
		$venster->addCode("}");
	$venster->end_javascript();

	$venster->addVensterData();

		if (!$cms_data->checkPagePermissions($id)) {
			$venster->addCode(gettext("This page has no custom permissions."));
			$venster->addCode(gettext("Click here to enable custom permissions").": ");
			$venster->insertAction("enabled", gettext("enable permissions"),
				"?mod=cms&action=authorisations&subact=enable&id=".$id);
		} else {
			$venster->addCode(gettext("This page has custom permissions."));
			$venster->addCode(gettext("Click here to disable custom permissions").": ");
			$venster->insertAction("disabled", gettext("disable permissions"),
				"?mod=cms&action=authorisations&subact=disable&id=".$id);
			$venster->addTag("br");
			$venster->addTag("br");

			$tbl = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$tbl->addTableRow();
				$tbl->addTableData("", "header");
					$tbl->addCode(gettext("user"));
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
					$tbl->addCode(gettext("CMS groups"));
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
					$tbl->addCode(gettext("Covide users with CMS access"));
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
			$top = $_REQUEST["top"];
			if (!$top)
				$top = 0;
			$list = $cms_data->getAccountList(0, 0, "", 1);
			$usercount = $list["count"];
			$list = $list["data"];
			$tbl->addTableRow();
				$tbl->addTableData(array(
					"colspan" => 6,
					"style"   => "text-align: center;"
				), "header");
					$tbl->addCode(gettext("CMS visitor"));
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
			/*
			$paging = new Layout_paging();
			$url = sprintf("index.php?mod=cms&action=authorisations&id=%d&top=%%%%", $pageid);
			$paging->setOptions($top, $usercount, $url);
			$tbl->addCode($paging->generate_output());
			*/

			$venster->addCode($tbl->generate_output());
			$venster->insertAction("save", gettext("save"), "javascript: saveSettings();");
		}

		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
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
