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
	if (!class_exists("Filesys_data")) {
		exit("no class definition found");
	}

	$id = $_POST["id"];
	if (!$id) {
		die("error");
	}

	$files =& $_FILES["binFile"];

	$fspath = $GLOBALS["covide"]->filesyspath;
	$fsdir  = "bestanden";

	$output = new Layout_output();

	/* check if upload folder is a sync folder */
	$q = sprintf("select * from filesys_folders where id = %d", $id);
	$res = sql_query($q);
	$fld = sql_fetch_assoc($res);

	if (!$fld["parent_id"] && $fld["name"] == "mijn sync files")
		$xs_sync = 1;

	foreach ($files["tmp_name"] as $pos=>$tmp_name) {
		/* if file position is filled with a tmp_name */
		if ($files["error"][$pos] == UPLOAD_ERR_OK && $tmp_name) {

			/* gather some file info */
			$name = $files["name"][$pos];
			$type = $this->detectMimetype($tmp_name);
			$size = $files["size"][$pos];

			$name = $this->checkDuplicates($name, $id);

			/* insert file into dbase */
			$q = "insert into filesys_files (folder_id, name, size, type, timestamp, user_id, description) values ";
			$q.= sprintf("(%d, '%s', '%s', '%s', %d, %d, '%s')", $id, $name, $size, $type, mktime(), $_SESSION["user_id"], $_POST["filedata"]["description"]);
			sql_query($q);
			$new_id = sql_insert_id("filesys_files");

			/* move data to the destination */
			$ext = $this->get_extension($name);

			/* remove old version(s) */
			$this->FS_removeFile($destination);

			$destination = sprintf("%s/%s/%s.".$ext, $fspath, $fsdir, $new_id);
			move_uploaded_file($tmp_name, $destination);

			/* compress contents */
			$this->FS_compressFile($destination);

			$output->addCode("file: ".$name." uploaded ...");
			$output->addTag("br");
		}
	}

	$output->start_javascript();
		$output->addCode(
			"parent.reset_upload_status();"
		);
	$output->end_javascript();
	$output->exit_buffer();
?>
