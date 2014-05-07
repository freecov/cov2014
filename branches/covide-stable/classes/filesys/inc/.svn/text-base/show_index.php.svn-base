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

if ($_REQUEST["subaction"]) {
	$hide = 1;
} else {
	$hide = 0;
}

/* gather some parameters */
$top         = $_REQUEST["top"];
$search      = $_REQUEST["search"];
$pastebuffer = $_REQUEST["pastebuffer"];

if (preg_match("/^((file)|(folder))$/s", $pastebuffer)) {
	$pastebuffer = "";
}

$output->layout_page(gettext("File management"), $hide);
/* window widget */
$venster = new Layout_venster(array(
	"title" => gettext("File management"),
	"subtitle" => $fsdata->getFolderPath($id, 1)
));
/* if subaction is not an cms action */
if ($_REQUEST["subaction"] != "cmsimage" && $_REQUEST["subaction"] != "cmsfile") {
	$uri = "./?mod=filesys&subaction=".$_REQUEST["subaction"]."&ids=".$_REQUEST["ids"]."&pastebuffer=".$_REQUEST["pastebuffer"];

	if ($_SESSION["locale"] == "nl_NL") {
		$venster->addMenuItem(gettext("help (wiki)"), "http://wiki.covide.nl/Bestandsbeheer", array("target" => "_blank"), 0);
	}
	$venster->addMenuItem(gettext("file management"), $uri);
	$venster->addMenuItem(gettext("search"), $uri."&action=search");
	if ($_REQUEST["search"]) {
		$venster->addMenuItem(gettext("search results"), "javascript: history_goback();");
	}
	if ($id == $fsdata->getGoogleFolders() || preg_match("/^g_/s", $id)) {
		$venster->addMenuItem(gettext("Google docs"), "javascript: popup('https://docs.google.com/');");
	}
	if ($_SESSION["google_id"]) {
		$venster->addMenuItem(gettext("Google logout"), "javascript: popup('?mod=google&action=gtoken&token=-1');");
	}
	$venster->generateMenuItems();
}

$parent = $fsdata->getParentFolder($id);
$venster->addVensterData();
	$venster->addTag("form", array(
		"id"     => "velden",
		"action" => "index.php",
		"method" => "post"
	));

	if (!$id) {
		$table = new layout_table();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode( $output->nbspace(3) );
				$table->addCode( gettext("search").": ");
				$table->addTextField("search", $_REQUEST["search"]);
				$table->start_javascript();
					$table->addCode("
						document.getElementById('search').focus();
					");
				$table->end_javascript();
				$table->insertAction("forward", gettext("search"), "javascript: document.getElementById('velden').action.value='search'; document.getElementById('velden').submit();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode($table->generate_output());
		unset($table);
		$venster->addTag("br");
	}

	$fs_obj  = new Filesys_data();
	$fs_data = $fs_obj->getFolders(array(
		"parentfolder" => $id,
		"search"       => $search,
		"sort"         => $_REQUEST["sortfolder"],
		"subaction"    => $_REQUEST["subaction"]
	), $top);
	$xs      = $fs_data["xs"];

	$xs_subaction = $fs_data["xs_subaction"];
	$xs_sync      = $fs_data["current_folder"]["xs_sync"];

	$venster->addHiddenField("mod", "filesys");
	$venster->addHiddenField("action", "opendir");
	$venster->addHiddenField("subaction", $_REQUEST["subaction"]);
	$venster->addHiddenField("description", $_REQUEST["description"]);
	$venster->addHiddenField("ids", $_REQUEST["ids"]);
	$venster->addHiddenField("id", $_REQUEST["id"]);
	$venster->addHiddenField("top", $_REQUEST["top"]);
	$venster->addHiddenField("pastebuffer", $pastebuffer);
	$venster->addHiddenField("address", $_REQUEST["address"]);
	$venster->addHiddenField("sortfile", $_REQUEST["sortfile"]);
	$venster->addHiddenField("sortfolder", $_REQUEST["sortfolder"]);

	if ($id) {
		$venster->addSpace(3);
		$venster->addCode(gettext("search").": ");
		$venster->addTextField("search", $search, "", "search", 1);
		$venster->start_javascript();
			$venster->addCode("addLoadEvent(document.getElementById('search').focus());");
		$venster->end_javascript();

		$venster->insertAction("forward", gettext("search"), "javascript: set('top', '0'); submitform();");
		if ($search) {
			$venster->insertAction("toggle", "alles tonen", "javascript:  set('top', '0'); set('search',''); submitform();");
		}
		$venster->addSpace(4);
		$venster->insertAction("folder_up", gettext("up one folder"), "javascript: document.getElementById('id').value = '$parent'; document.getElementById('velden').submit();");

		/* check for relation */
		if ($fs_data["current_folder"]["hp_name"] == "relaties") {
			if ($fs_data["current_folder"]["address_id"]) {
				$relation = $fs_data["current_folder"]["address_id"];
			} else {
				$relation = $fs_obj->checkForRelation($id);
			}
			if ($relation) {
				$venster->insertAction("addressbook", gettext("to contact card"), "?mod=address&action=relcard&id=".$relation);
			}
		}

		/* check for project */
		if ($fs_data["current_folder"]["hp_name"] == "projecten") {
			if ($fs_data["current_folder"]["project_id"]) {
				$project = $fs_data["current_folder"]["project_id"];
			} else {
				$project = $fs_obj->checkForProject($id);
			}
			if ($project) {
				$venster->insertAction("folder_project", gettext("to project"), "?mod=project&action=showhours&id=".$project);
			}
		}


		if ($_REQUEST["subaction"] && $xs_subaction) {
			$xs_check = $xs_subaction;
		} else {
			$xs_check = $xs;
		}
		switch ($xs_check) {
			case "W":
				if ($xs_sync) {
					$txt = gettext("special permissions");
					$img = "permissions_special";

					/* execute some sync check calls */
					if ($GLOBALS["covide"]->license["has_funambol"]) {
						$funambol_data = new Funambol_data();
						$funambol_data->checkRecords("files");
						unset($funambol_data);
					}
				} else {
					$txt = gettext("write permissions");
					$img = "permissions_write";
				}
				break;
			case "X":
				$txt = gettext("create/upload permissions");
				$img = "permissions_special";
				break;
			case "R":
				$txt = gettext("read permissions");
				$img = "permissions_read";
				break;
			case "S":
				$txt = gettext("write permissions on folders");
				$img = "permissions_special";
				break;
			case "D":
				$txt = gettext("no permissions");
				$img = "permissions_none";
				break;
		}

		$venster->addSpace(4);
		$venster->insertAction($img, $txt, "");
		$venster->addSpace();
		$venster->addCode( gettext("in this folder you have")." ");
		$venster->addCode( $txt );
		$venster->addTag("br");
		$venster->addSpace(2);
		for ($i=0; $i!=26; $i++) {
			$venster->addSpace();
			$venster->insertLink(chr(65+$i), array(
				"href" => "javascript: setSearch('".chr(65+$i)."*');"
			) );
			$venster->addSpace(1);
		}

	}

	if (!$xs_sync) {
		$table = new Layout_table(array("width"=>"100%"));
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "data");
				if ($fs_data["need_google_login"]) {
					$table->start_javascript();
						$table->addCode(sprintf("
							function googleLogin() {
								window.open('%s', 'google_login', 640, 480, 1);
							}
						", $fs_data["need_google_login"]
					));
					$table->end_javascript();
					$table->addTag("br");
					$table->addCode(gettext("You need to login into Google Docs and save the settings in your user profile to continue. Click the icon to sign up"));
					$table->addSpace();
					$table->insertAction("google", gettext("login"), "javascript: googleLogin()");
					$table->insertAction("state_special", gettext("user"), "?mod=user&action=useredit&id=".$_SESSION["user_id"]);
				}
				$view = $this->show_folders($id, $fs_data);
				$table->addCode($view->generate_output());
				unset($view);

				$paging = new Layout_paging();
				$paging->setOptions($top, $fs_data["count"], "javascript: blader('%%');");
				$table->addCode( $paging->generate_output() );
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode( $table->generate_output() );
	}


	/* add short paths */
	if (($_REQUEST["subaction"] == "save_attachment" || $_REQUEST["subaction"] == "add_attachment" || $_REQUEST["subaction"] == "save_fax") && !$_REQUEST["id"]) {
		if ($_REQUEST["subaction"] == "save_attachment" || $_REQUEST["subaction"] == "add_attachment") {
			/* search relations for email attachments */
			$email = new Email_data();
			if ($_REQUEST["ids"]) {
				$rinfo = $email->getAttachmentsInfo($_REQUEST["ids"]);
			}
			if ($_REQUEST["address"]) {
				$rinfo["relations"][]=$_REQUEST["address"];
			}
			if ($_REQUEST["project"]) {
				$rinfo["projects"][]=$_REQUEST["project"];
			}
		} else {
			/* search relations for faxes */
			$voip = new Voip_data();
			$rinfo["relations"][] = $voip->getFaxRelationById($_REQUEST["ids"]);
		}

		$rf = array();
		$rel_ids = array();
		if (is_array($rinfo["relations"])) {
			foreach ($rinfo["relations"] as $r) {
				if ($r) {
					$rel_ids[] = $r;
					$relfolder = $fsdata->getRelFolder($r);
					if ($relfolder) {
						$rf[]= $relfolder;
					}
				}
			}
		}
		if (count($rf)>0) {
			$rf = implode(",", $rf);
			$rfdata = $fsdata->getFolders( array("ids"=>$rf) );
		}
		$rfdata["is_shortcut"] = 1;
		$rfdata["is_projects"] = 0;
		$rfdata["is_relations"] = 1;
		$view = $this->show_folders($id, $rfdata);
		$venster->addCode($view->generate_output());
		$venster->addTag("br");

		unset($view);

		$pf = array();
		if (is_array($rinfo["projects"])) {
			foreach ($rinfo["projects"] as $p) {
				if ($p) {
					$projectfolder = $fsdata->getProjectFolder($p);
					if ($projectfolder) {
						$pf[]= $projectfolder;
					}
				}
			}
		}
		/* Add project folders for user who's not project manager */
		if (!$GLOBALS["covide"]->license["has_projects"]) {
			/* Get project IDs by relation where user has acces */
			$project_data = new Project_data();
			foreach ($rel_ids as $id) {
				$project_ids[$id] = $project_data->getProjectAccessByRelation($id, $_SESSION["user_id"]);
				foreach ($project_ids as $relation_id => $project_data) {
					if (!is_array($project_data)) {
						$project_data = array();
					}
					foreach ($project_data as $project_id => $project_name) {
						$projectfolder = $fsdata->getProjectFolder($project_id);
						if ($projectfolder) {
							$pf[]= $projectfolder;
						}
					}
				}
			}
			
		}

		if (count($pf)>0) {
			$pf = implode(",", $pf);
			$pfdata = $fsdata->getFolders( array("ids"=>$pf) );
		}
		$pfdata["is_shortcut"] = 1;
		$pfdata["is_projects"] = 1;
		$pfdata["is_relations"] = 0;
		$view = $this->show_folders($id, $pfdata);
		$venster->addCode($view->generate_output());
		$venster->addTag("br");

		unset($view);
	}
	/* If we're filing an attachment add the option to add a new subdirectoy */
	$subAction = $fs_data["current_folder"]["xs_subaction"];
	if($id && $_REQUEST["subaction"] == "save_attachment" && ($subAction == "S" || $subAction == "W" )) {
		$venster->addTag("br");
		$venster->insertAction("new", gettext("Create subdirectory"), array(
			"href" => "javascript: create_subdir($_REQUEST[id]);"
		));
	}

	/* some module specific paste operations */

	if ($_REQUEST["ids"]) {
		/* if mail */
		if ($_REQUEST["subaction"] == "save_attachment") {
			$email = new Email_data();
			$att_ids = explode(",", $_REQUEST["ids"]);
			$attachments = array();
			foreach ($att_ids as $att_id) {
				$attachments[] = $email->getAttachment($att_id);
			}
			$venster->addTag("br");
			$venster->addCode( gettext("Go to the preferred folder an click on save") );
			if (in_array($xs_subaction, array("W", "X"))) {
				$venster->insertAction("save_all", gettext("Save files here"), "javascript: save_attachment();");
			}

			$view = new Layout_view();
			$view->addData($attachments);
			$view->addMapping(gettext("selected email attachments"), "%name");
			$view->addMapping(gettext("size"), "%h_size");
			$venster->addTag("br");
			$venster->addCode($view->generate_output());
			$venster->addTag("br");
			$venster->addTag("br");
		}
		if ($_REQUEST["subaction"] == "save_fax") {
			$voip_data = new Voip_data();
			$faxes = array();
			$faxes[] = $voip_data->getFaxFromFS($_REQUEST["ids"], 0);
			$venster->addTag("br");
			$venster->addCode( gettext("Go to the preferred folder an click on save") );
			if (in_array($xs_subaction, array("W","X"))) {
				$venster->insertAction("save_all", gettext("Save fax here"), "javascript: save_fax();");
			}
			$view = new Layout_view();
			$view->addData($faxes);
			$view->addMapping(gettext("selected fax"), "%name");
			$view->addMapping(gettext("size"), "%h_size");
			$venster->addTag("br");
			$venster->addCode($view->generate_output());
			$venster->addTag("br");
			$venster->addTag("br");
		}
	}
	if ((in_array($xs, array("R", "W", "X"))) && !$pastebuffer && $id) {
		$table = new Layout_table(array("width"=>"100%"));
		$table->addTableRow();
			$table->addTableData(array("colspan"=>2), "data");

				if (in_array($xs, array("R", "W", "S", "X"))) {
					if ($xs == "W") {
						$view = $this->show_files($id, 0, ($xs_sync) ? 8192:0);
					} else {
						$view = $this->show_files($id, 1, 0);
					}
				}
				$table->addCode($view->generate_output());

			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode( $table->generate_output() );
	}

	$venster->endTag("form");



	//if ($id && !$_REQUEST["subaction"]=="add_attachment" && !$pastebuffer) {
	if ($id && !$pastebuffer) {
		$table = new Layout_table(array("width"=>"100%"));
		//if ($xs == "W" || $xs == "S") {
		if (in_array($xs, array("W", "S", "X"))) {
			$table->addTableRow();
				$table->addTableData();
					if ($xs == "W" || $xs == "X") {
						$table->addCode($this->show_fileupload($id)->generate_output());
					} else {
						$table->addSpace();
					}
				$table->endTableData();
				if (!$xs_sync) {
					$table->addTableData();
						$table->addCode($this->show_newfolder()->generate_output());
					$table->endTableData();
				}

				if ($xs_sync) {
					$table2 = new Layout_table(array("width"=>"100%"));
					$table2->addTableRow();
						$table2->addTableData(array("colspan"=>2), "data");
							$table2->insertTag("b", "Special restrictions are active in this folder:");
							$table2->addTag("br");
							$table2->addCode("- ".gettext("Only files can be uploaded, no folders can be created"));
							$table2->addTag("br");
							$table2->addCode("- ".gettext("A filename can only occur once. Duplicates will be renamed automatically."));
							$table2->addTag("br");
							$table2->addCode("- ".gettext("Max file size is 8 KB, files exceeding this size will be marked red."));
						$table2->endTableData();
					$table2->endTableRow();
					$table2->endTable();
					$table->addTableData();
						$table->addCode( $table2->generate_output() );
					$table->endTableData();
				}
			$table->endTableRow();
		}
		$table->endTable();
	} elseif ($pastebuffer) {
		/* if pastebuffer is set */
		$table = new Layout_table(array("cellspacing"=>1, "cellpadding"=>1));
		$table->addTableRow();
			$table->addTableData();
				$ids = $pastebuffer;
				$ids = explode(",", preg_replace("/^((file)|(folder)),/s", "", $ids));

				$table->addTag("b");
				if (preg_match("/^file/s", $pastebuffer)) {
					$table->addCode( gettext("You selected the following files to move") );
					$data = $fsdata->getFilesByArray($ids);
				} else {
					$table->addCode( gettext("You selected the following folder to move") );
					$data = $fsdata->getFoldersByArray($ids);
				}
				$table->addSpace();
				$xp = 0;
				if ($xs == "W") {
					if (!$xs_sync || preg_match("/file/s", $pastebuffer)) {
						$xp = 1;
					} elseif (preg_match("/^folder/s", $pastebuffer) && $xs == "S") {
						$xp = 1;
					}
				}
				if ($xp) {
					$table->addCode(gettext("Go to the destination folder to paste"));
					$table->addSpace(3);
					$table->insertAction("paste", gettext("paste"), "javascript: selection_paste();");
				} else {
					$table->addTag("br");
					$table->addCode(gettext("You dont have permissions to write files or folders here"));
					$table->addSpace(3);
					$table->insertAction("important", "", "");
				}
				$table->insertAction("toggle", gettext("undo cut"), "javascript: selection_undo();");
				$table->endTag("b");

				$view = new Layout_view();
				$view->addData($data);
				$view->addMapping(gettext("name"), "%%complex_name");
				$view->addMapping(gettext("description"), "%description");

				$view->defineComplexMapping("complex_name", array(
					array(
						"type"  => "action",
						"src"   => "%fileicon"
					),
					array(
						"text"  => array(" ", "%name")
					)
				));
				$table->addCode( $view->generate_output() );

			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
	}
	$venster->addCode($table->generate_output());
$venster->endVensterData();
/* end window */
$output->addCode($venster->generate_output());
unset($venster);

/* add a container for multiple downloads */
$output->insertTag("div", "", array(
	"id"    => "download_container",
	"style" => "display: none; width: 0px; height: 0px;"
));

$history = new Layout_history();
$output->addCode( $history->generate_save_state() );

$output->load_javascript(self::include_dir."file_operations.js");
/*
if ($_REQUEST["jump_to_anchor"]) {
	$output->start_javascript();
		$output->addCode(sprintf(" document.location.href = document.location.href.concat('#%s'); ", $_REQUEST["jump_to_anchor"]));
	$output->end_javascript();
}
*/
$output->layout_page_end();
$output->exit_buffer();
?>
