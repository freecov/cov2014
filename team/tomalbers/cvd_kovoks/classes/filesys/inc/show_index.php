<?php
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

$output->layout_page("filesys", $hide);
/* window widget */
$venster = new Layout_venster(array(
	"title" => gettext("bestandsbeheer"),
	"subtitle" => $fsdata->getFolderPath($id, 1)
));
/* if subaction is not an cms action */
if ($_REQUEST["subaction"] != "cmsimage" && $_REQUEST["subaction"] != "cmsfile") {
	$uri = "./?mod=filesys&subaction=".$_REQUEST["subaction"]."&ids=".$_REQUEST["ids"]."&pastebuffer=".$_REQUEST["pastebuffer"];

	$venster->addMenuItem(gettext("bestandsbeheer"), $uri);
	$venster->addMenuItem(gettext("zoeken"), $uri."&action=search");
	if ($_REQUEST["search"]) {
		$venster->addMenuItem(gettext("zoekresultaten"), "javascript: history_goback();");
	}
	$venster->generateMenuItems();
}


$parent = $fsdata->getParentFolder($id);
$venster->addVensterData();

	$fs_obj  = new Filesys_data();
	$fs_data = $fs_obj->getFolders(array("parentfolder"=>$id, "search"=>$search, "sort"=>$_REQUEST["sortfolder"]), $top);
	$xs      = $fs_data["xs"];
	$xs_subaction = $fs_data["xs_subaction"];

	$venster->addTag("form", array(
		"id"     => "velden",
		"action" => "index.php",
		"method" => "post"
	));
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
		if ($id != $fs_obj->getCmsFolder()) {
			$venster->insertAction("folder_up", gettext("een map hoger"), "javascript: document.getElementById('id').value = '$parent'; document.getElementById('velden').submit();");
		}

		/* check for relation */
		if ($fs_data["current_folder"]["hp_name"] == "relaties") {
			if ($fs_data["current_folder"]["address_id"]) {
				$relation = $fs_data["current_folder"]["address_id"];
			} else {
				$relation = $fs_obj->checkForRelation($id);
			}
			if ($relation) {
				$venster->insertAction("addressbook", gettext("naar de relatiekaart"), "?mod=address&action=relcard&id=".$relation);
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
				$venster->insertAction("folder_project", gettext("naar het project"), "?mod=project&action=showhours&id=".$project);
			}
		}


		$venster->addSpace(2);

		$venster->addCode(gettext("zoeken in mappen").": ");

		if ($_REQUEST["subaction"] && $xs_subaction) {
			$xs_check = $xs_subaction;
		} else {
			$xs_check = $xs;
		}
		switch ($xs_check) {
			case "W":
				$txt = gettext("schrijfrechten");
				$img = "permissions_write";
				break;
			case "R":
				$txt = gettext("leesrechten");
				$img = "permissions_read";
				break;
			case "S":
				$txt = gettext("schrijfrechten op mappen");
				$img = "permissions_special";
				break;
			case "D":
				$txt = gettext("geen rechten");
				$img = "permissions_none";
				break;
		}
		$venster->addTextField("search", $search, "", "", 1);
		$venster->insertAction("forward", gettext("zoeken"), "javascript: set('top', '0'); submitform();");
		if ($search) {
			$venster->insertAction("toggle", "alles tonen", "javascript:  set('top', '0'); set('search',''); submitform();");
		}

		$venster->addSpace(4);
		$venster->insertAction($img, $txt, "");
		$venster->addSpace();
		$venster->addCode( gettext("u heeft in deze map")." ");
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

	$table = new Layout_table(array("width"=>"100%"));
	$table->addTableRow();
		$table->addTableData(array("colspan"=>2), "data");

			$view = $this->show_folders($id, $fs_data);
			$table->addCode($view->generate_output());
			unset($view);

			$paging = new Layout_paging();
			$paging->setOptions($top, $fs_data["count"], "javascript: blader('%%');");
			$table->addCode( $paging->generate_output() );
			$table->addTag("br");

		$table->endTableData();
	$table->endTableRow();
	$table->endTable();

	$venster->addCode( $table->generate_output() );

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
		} else {
			/* search relations for faxes */
			$voip = new Voip_data();
			$rinfo["relations"][] = $voip->getFaxRelationById($_REQUEST["ids"]);
		}

		$rf = array();
		if (is_array($rinfo["relations"])) {
			foreach ($rinfo["relations"] as $r) {
				if ($r) {
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
		$view = $this->show_folders($id, $rfdata);
		$venster->addCode($view->generate_output());
		$venster->addTag("br");
		unset($view);
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
			$venster->addCode( gettext("Ga naar de bestemmingsmap en klik op opslaan") );
			if ($xs_subaction == "W") {
				$venster->insertAction("save_all", gettext("De bestanden hier opslaan"), "javascript: save_attachment();");
			}
			$view = new Layout_view();
			$view->addData($attachments);
			$view->addMapping(gettext("geselecteerde email attachments"), "%name");
			$view->addMapping(gettext("grootte"), "%h_size");
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
			$venster->addCode( gettext("Ga naar de bestemmingsmap en klik op opslaan") );
			if ($xs_subaction == "W") {
				$venster->insertAction("save_all", gettext("De fax hier opslaan"), "javascript: save_fax();");
			}
			$view = new Layout_view();
			$view->addData($faxes);
			$view->addMapping(gettext("geselecteerde fax"), "%name");
			$view->addMapping(gettext("grootte"), "%h_size");
			$venster->addTag("br");
			$venster->addCode($view->generate_output());
			$venster->addTag("br");
			$venster->addTag("br");
		}
	}

	if (($xs == "R" || $xs == "W" || !$id) && !$pastebuffer) {
		$table = new Layout_table(array("width"=>"100%"));
		$table->addTableRow();
			$table->addTableData(array("colspan"=>2), "data");

				if ($xs=="R" || $xs=="W" || $xs=="S") {
					if ($xs=="W") {
						$view = $this->show_files($id, 0);
					} else {
						$view = $this->show_files($id, 1);
					}
				}
				$table->addCode($view->generate_output());

			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$venster->addCode( $table->generate_output() );
	}

	$venster->endTag("form");

	if ($id && !$_REQUEST["subaction"]=="add_attachment" && !$pastebuffer) {
		$table = new Layout_table(array("width"=>"100%"));
		if ($xs == "W" || $xs == "S") {
			$table->addTableRow();
				$table->addTableData();
					if ($xs == "W") {
						$table->addCode($this->show_fileupload()->generate_output());
					} else {
						$table->addSpace();
					}
				$table->endTableData();
				$table->addTableData();
					$table->addCode($this->show_newfolder()->generate_output());
				$table->endTableData();
			$table->endTableRow();
		}
		$table->endTable();
	} elseif ($pastebuffer) {
		$table = new Layout_table(array("cellspacing"=>1, "cellpadding"=>1));
		$table->addTableRow();
			$table->addTableData();
				$ids = $pastebuffer;
				$ids = explode(",", preg_replace("/^((file)|(folder)),/s", "", $ids));

				$table->addTag("b");
				if (preg_match("/^file/s", $pastebuffer)) {
					$table->addCode( gettext("U heeft de volgende bestanden geselecteerd voor verplaatsing.") );
					$data = $fsdata->getFilesByArray($ids);
				} else {
					$table->addCode( gettext("U heeft de volgende map geselecteerd voor verplaatsing.") );
					$data = $fsdata->getFoldersByArray($ids);
				}
				$table->addSpace();
				if ($xs == "W" || (preg_match("/^folder/s", $pastebuffer) && $xs == "S")) {
					$table->addCode(gettext("Ga naar de bestemmingsmap en klik op plakken"));
					$table->addSpace(3);
					$table->insertAction("paste", gettext("plakken"), "javascript: selection_paste();");
				} else {
					$table->addTag("br");
					$table->addCode(gettext("U heeft geen rechten in deze map om bestanden of mappen weg te schrijven."));
					$table->addSpace(3);
					$table->insertAction("important", "", "");
				}
				$table->insertAction("toggle", gettext("knippen ongedaan maken"), "javascript: selection_undo();");
				$table->endTag("b");

				$view = new Layout_view();
				$view->addData($data);
				$view->addMapping(gettext("naam"), "%%complex_name");
				$view->addMapping(gettext("omschrijving"), "%description");

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
$output->addCode( $history->generate_history_call() );

$output->load_javascript(self::include_dir."file_operations.js");
$output->layout_page_end();
$output->exit_buffer();
?>
