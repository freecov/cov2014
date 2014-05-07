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

	/* if a replace was requested */
	#echo $_POST["filedata"]["binReplace"];
	if ($_POST["filedata"]["binReplace"])
		$this->file_remove($_POST["filedata"]["binReplace"], $id, 1);


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
			$prefix = $_POST["filedata"]["prefix"];
			if ($prefix) {
				$ext = $this->get_extension($files["name"][$pos]);
				$name = sprintf("%s.%s", $prefix, $ext);

				/* remove duplicates */
				$like = sql_syntax("like");
				$q = sprintf("select id from filesys_files where folder_id = %d and name %s '%s\.%%'",
					$id, $like, $prefix);
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res)) {
					$this->file_remove($row["id"], $id, 1);
				}
			} else {
				$name = $files["name"][$pos];
			}
			$type = $this->detectMimetype($tmp_name);
			$size = $files["size"][$pos];

			$name = $this->checkDuplicates($name, $id);

			/* insert file into dbase */
			if (!$_POST["filedata"]["binReplace"]) {
				$q = "insert into filesys_files (folder_id, name, size, type, timestamp, user_id, description) values ";
				$q.= sprintf("(%d, '%s', '%s', '%s', %d, %d, '%s')", $id, $name, $size, $type, time(), $_SESSION["user_id"], $_POST["filedata"]["description"]);
				sql_query($q);
				$new_id = sql_insert_id("filesys_files");
			} else {
				$q = "insert into filesys_files (id, folder_id, name, size, type, timestamp, user_id, description) values ";
				$q.= sprintf("(%d, %d, '%s', '%s', '%s', %d, %d, '%s')", $_POST["filedata"]["binReplace"],
					$id, $name, $size, $type, time(), $_SESSION["user_id"], $_POST["filedata"]["description"]);
				sql_query($q);
				$new_id = $_POST["filedata"]["binReplace"];
			}

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
