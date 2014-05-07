<?php
/**
 * Covide Groupware-CRM Filesys output module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version 6.0
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Filesys_output {
	/* constants */
	const include_dir = "classes/filesys/inc/";
	const class_name  = "filesys";

	/* variables */

	/* methods */
	/* show_index {{{ */
	public function show_index($id=0) {
		require(self::include_dir."show_index.php");
	}
	/* }}} */

	/* show_folders {{{ */
	public function show_folders($id=0, $data) {
		require(self::include_dir."show_folders.php");
		return $view;
	}
	/* }}} */

	/* show_files {{{ */
	public function show_files($id=0, $noactions=0) {
		require(self::include_dir."show_files.php");
		return $view;
	}
	/* }}} */

	/* }}} */

	/* file_edit {{{ */
	public function file_edit($id) {
		$fs_data = new Filesys_data();
		$file = $fs_data->getFileById($id);
		$output = new Layout_output();
		$output->layout_page("", 1);
			$output->addTag("form", array(
				"name"   => "fedit",
				"method" => "get",
				"action" => "index.php",
				"id"     => "fedit"
			));
			$output->addHiddenField("mod", "filesys");
			$output->addHiddenField("action", "feditsave");
			$output->addHiddenField("fileid", $id);
			$output->addHiddenField("folderid", $file["folder_id"]);
			/* window widget */
			$venster = new Layout_venster(array(
				"title"    => gettext("file management"),
				"subtitle" => gettext("alter file")
			));
			$venster->addVensterData();
				$table = new Layout_table();
				$table->addTableRow();
					$table->insertTableData(gettext("filename"), array("style"=>"vertical-align: top;"), "header");
					$table->addTableData("", "data");
						$table->addCode($file["name"]);
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->insertTableData(gettext("description"), array("style"=>"vertical-align: top;"), "header");
					$table->addTableData();
						$table->addTextArea("fedit[description]", $file["description"], array(
							"style" => "width: 400px; height: 200px;"
						));
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->insertTableData("", "", "header");
					$table->addTableData("", "header");
						$table->insertAction("save", gettext("save"), "javascript: fsave();");
					$table->endTableData();
				$table->endTableRow();
				$table->endTable();
				$venster->addCode($table->generate_output());
			$venster->endVensterData();
			/* end window */
			$output->addCode($venster->generate_output());
			$output->endTag("form");
			$output->load_javascript(self::include_dir."file_operations.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */

	/* folder_edit {{{ */
	public function folderEdit($id) {
		$fs_data = new Filesys_data();
		$folder = $fs_data->getFolderInfo($id);
		$output = new Layout_output();
		$output->layout_page("", 1);
			$output->addTag("form", array(
				"name"   => "fedit",
				"method" => "get",
				"action" => "index.php",
				"id"     => "fedit"
			));
			$output->addHiddenField("mod", "filesys");
			$output->addHiddenField("action", "foldereditsave");
			$output->addHiddenField("folder[id]", $id);
			$output->addHiddenField("folder[parent_id]", $folder["parent_id"]);

			/* window widget */
			$venster = new Layout_venster(array(
				"title"    => gettext("file management"),
				"subtitle" => gettext("edit folder")
			));
			$venster->addVensterData();
				$table = new Layout_table();
				$table->addTableRow();
					$table->insertTableData(gettext("name"), array("style"=>"vertical-align: top;"), "header");
					$table->addTableData("", "data");
						$table->addTextField("folder[name]", $folder["name"]);
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->insertTableData(gettext("description"), array("style"=>"vertical-align: top;"), "header");
					$table->addTableData();
						$table->addTextArea("folder[description]", $folder["description"], array(
							"style" => "width: 400px; height: 200px;"
						));
					$table->endTableData();
				$table->endTableRow();
				$table->addTableRow();
					$table->insertTableData("", "", "header");
					$table->addTableData("", "header");
						$table->insertAction("save", gettext("save"), "javascript: fsave();");
					$table->endTableData();
				$table->endTableRow();
				$table->endTable();
				$venster->addCode($table->generate_output());
			$venster->endVensterData();
			/* end window */
			$output->addCode($venster->generate_output());
			$output->endTag("form");
			$output->load_javascript(self::include_dir."file_operations.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */

	/* show_fileupload {{{ */
	public function show_fileupload() {
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("upload file"));
				$table->addTag("br");
				$table->addTag("form", array(
				    "id" => "uploadform",
					"method" => "POST",
					"target" => "uploadhandler",
					"enctype" => "multipart/form-data"
				));
					$table->addHiddenField("MAX_FILE_SIZE", "67108864");
					$table->addHiddenField("mod", $_REQUEST["mod"]);
					$table->addHiddenField("id", $_REQUEST["id"]);
					$table->addHiddenField("action", "fupload");
					//$table->addTag("div", array("id"=>"uploadcode") );
						$table->addUploadField("binFile[]", array("size"=>"45") );

						$table->addTag("br");
						$table->addTextArea("filedata[description]", "", array(
							"style" => "width: 400px; height: 100px;"
						));
					//$table->endTag("div");
					$table->addTag("br");
					$table->addTag("span", array("id"=>"upload_msg", "style"=>"visibility: hidden") );
						$table->insertTag("b", gettext("uploading")." ...");
					$table->endTag("span");
				$table->endTag("form");
				$table->addTag("br");
				$table->insertAction("save", gettext("upload file"), "javascript: filesys_upload_files();");
				$table->addSpace(10);
				$table->insertAction("file_multiple", gettext("multiple file upload"), "javascript: popup('jupload.php?user=".$_SESSION["user_id"]."&map_id=".$_REQUEST["id"]."&mod=".$_REQUEST["mod"]."', 'multiupload', 0, 0, 1);");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		$table->addTag("iframe", array(
			"id"          => "uploadhandler",
			"name"        => "uploadhandler",
			"src"         => "blank.htm",
			"width"       => "0px",
			"frameborder" => 0,
			"border"      => 0,
			"height"      => "0px;",
			"visiblity"   => "hidden"
		));
		$table->endTag("iframe");
		return $table;
	}
	/* }}} */

	/* show_newfolder {{{ */
	public function show_newfolder() {
		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				$table->addCode(gettext("create folder"));
				$table->addTag("br");
				$table->addTag("form", array(
				    "id" => "createfolder",
					"method" => "POST",
					"action" => "index.php"
				));
					$table->addHiddenField("mod", $_REQUEST["mod"]);
					$table->addHiddenField("id", $_REQUEST["id"]);
					$table->addHiddenField("action", "dircreate");
						$table->addTextField("folder[name]", "", array("style"=>"width: 400px;") );

						$table->addTag("br");
						$table->addTextArea("folder[description]", "", array(
							"style" => "width: 400px; height: 100px;"
						));
					$table->addTag("br");
				$table->endTag("form");
				$table->addTag("br");
				$table->insertAction("save", gettext("create folder"), "javascript: filesys_create_dir();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();
		return $table;
	}
	/* }}} */

	public function set_permissions() {
		require(self::include_dir."setPermissions.php");

	}
	public function deleteFolderOverview() {
		require(self::include_dir."deleteFolderOverview.php");
	}
	public function moveFolderOverview() {
		require(self::include_dir."moveFolderOverview.php");
	}

	public function search() {
		require(self::include_dir."show_search.php");
	}

	public function filesCopied($parentaction = "") {
		$output = new Layout_output();
		$output->layout_page("filesys", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("file management"),
			"subtitle" => gettext("move files")
		));
		$venster->addVensterData();
			$venster->addCode( gettext("Files have been saved")." ... " );
		$venster->endVensterData();
		$output->addCode($venster->generate_output());

		$output->start_javascript();
		if ($parentaction == "faxredir") {
			$output->addCode("opener.location.href = opener.location.href;");
		}
		$output->addCode("var tcx = setTimeout('window.close();', 3000);");
		$output->end_javascript();

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function view_file($id) {
		$data = new Filesys_data();
		$file = $data->getFileById($id,1);

		$file = $data->detect_preview($file);

		switch ($file["subtype"]) {
			case "image":
				$this->viewFileImage($id);
				break;
			default:
				$filesys = new Filesys_data();

				$file = array(
					"id"      => $id,
					"subtype" => $file["subtype"],
					"module"  => "bestanden",
					"name"    => $file["name"]
				);
				$filesys->file_preview($file);
				exit();
		}
	}

	//functions to show attachments
	public function viewFileImage($id) {
		$output = new layout_output();
		$output->addTag("html");
		$output->addTag("body", array(
			"style" => "margin: 0px;"
		));
		$output->addTag("center");
			$output->addTag("img", array(
				"src" => "?dl=1&mod=filesys&action=fdownload&id=".$id, "viewimage"
			));
		$output->endTag("center");
		$output->load_javascript(self::include_dir."fitPicture.js");
		$output->endTag("body");
		$output->endTag("html");
		$output->exit_buffer();
	}

	public function find_double_rel() {
		$filesys_data = new Filesys_data();
		$doulble = $filesys_data->get_double_rel_folders();
	}
}
?>
