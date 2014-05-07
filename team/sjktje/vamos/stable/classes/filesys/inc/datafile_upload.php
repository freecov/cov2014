<?php
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

	foreach ($files["tmp_name"] as $pos=>$tmp_name) {
		/* if file position is filled with a tmp_name */
		if ($files["error"][$pos] == UPLOAD_ERR_OK && $tmp_name) {

			/* gather some file info */
			$name = $files["name"][$pos];
			$type = $this->detectMimetype($tmp_name);
			$size = $files["size"][$pos];

			/* insert file into dbase */
			$q = "insert into filesys_files (folder_id, name, size, type, timestamp, user_id, description) values ";
			$q.= sprintf("(%d, '%s', '%s', '%s', %d, %d, '%s')", $id, $name, $size, $type, mktime(), $_SESSION["user_id"], $_POST["filedata"]["description"]);
			sql_query($q);
			$new_id = sql_insert_id("filesys_files");

			/* move data to the destination */
			$ext = $this->get_extension($name);

			$destination = sprintf("%s/%s/%s.".$ext, $fspath, $fsdir, $new_id);
			move_uploaded_file($tmp_name, $destination);

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
