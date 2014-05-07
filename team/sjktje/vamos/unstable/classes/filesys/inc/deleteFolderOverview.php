<?php
	/**
	 * Covide Groupware-CRM Filesys module
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
	if (!class_exists("Filesys_output")) {
		exit("no class definition found");
	}

	$fsdata = new Filesys_data();

	$output = new Layout_output();
	$output->layout_page("filesys", 1);

	$folder = $_REQUEST["folder"];

	$venster = new Layout_venster(array(
		"title" => gettext("file management"),
		"subtitle" => gettext("delete foldertree")
	));
	$venster->addVensterData();

		$table = new Layout_table( array(
			"width"   => "100%",
			"cellspacing" => 0,
			"cellpadding" => 0
		));
		$table->addTableRow();
			$table->addTableData("", "data");

				$fsdata = new Filesys_data();
				$folders = $fsdata->getFolderArray($_REQUEST["folder"]);

				$table->addCode( gettext("You are about to delete the next folders").": ");
				$table->addTag("br");
				foreach ($folders as $k=>$v) {
					if ($v["permissions"] != "W") {
						$deny = 1;
					}
					$files = $fsdata->getFiles(array("folderid"=>$v["id"]));
					$v["filecount"] = count($files);
					$folders[$k] = $v;
				}
				if ($deny == 1) {
					$table->addTag("br");

					$tbl = new Layout_table( array(
						"style" => "border: 2px dotted red"
					));
					$tbl->addTableRow();
						$tbl->addTableData();
							$tbl->insertAction("important", "", "");
						$tbl->endTableData();
						$tbl->addTableData();
							$tbl->addCode( gettext("You cannot move the complete tree because you dont have permissionson the red marked folders."));
							$tbl->addTag("br");
							$tbl->addCode( gettext("Contact someone who can grant you permissions."));
						$tbl->endTableData();
						$tbl->addTableData();
							$tbl->insertAction("important", "", "");
						$tbl->endTableData();
						$tbl->endTable();

						$table->addCode( $tbl->generate_output() );
						unset($tbl);
				}
				$table->addTag("br");
				$view = new Layout_view();
				$view->addData($folders);
				$view->addMapping(gettext("folders"), "%%complex_name");
				#$view->addMapping(gettext("description"), "%description");

				$view->defineComplexMapping("complex_name", array(
					array(
						"text"  => "%spacing"
					),
					array(
						"type"  => "action",
						"src"   => "%foldericon"
					),
					array(
						"text"  => array(" ","%name", " (", gettext("with"), " ",  "%filecount", " ", gettext("files"), ")")
					)
				));
				$view->setHtmlField("spacing");
				$table->addCode( $view->generate_output() );

				$table->addTag("br");
				$table->insertAction("back", gettext("back"), "javascript: window.close();");
				$table->addSpace(3);
				if (!$deny) {
					$table->insertAction("ok", gettext("continue to delete"), "?mod=filesys&action=delete_folder_exec&id=".$folder);
				}


			$table->endTableData();
		$table->endTableRow();
		$table->endTable();

		$venster->addCode( $table->generate_output() );

	$venster->endVensterData();
	$output->addCode( $venster->generate_output() );

	$output->exit_buffer();
?>
