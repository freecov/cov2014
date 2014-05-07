<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	if (!$id)
		$id = (int)$_POST["id"];

	if (!$id) {
		die("error");
	}

	$files =& $_FILES["binFile"];
	$filesys = new Filesys_data();

	$fspath = $GLOBALS["covide"]->filesyspath;
	$fsdir  = "email";

	$output = new Layout_output();

	if (!is_array($files["tmp_name"]))
		$files["tmp_name"] = array();

	foreach ($files["tmp_name"] as $pos=>$tmp_name) {
		/* if file position is filled with a tmp_name */
		if ($files["error"][$pos] == UPLOAD_ERR_OK && $tmp_name) {

			/* gather some file info */
			$name = $files["name"][$pos];
			$type = $filesys->detectMimetype($tmp_name);
			$size = $files["size"][$pos];

			/* insert file into dbase */
			$q = "insert into mail_attachments (message_id, name, size, type) values ";
			$q.= sprintf("(%d, '%s', '%s', '%s')", $id, $name, $size, $type);
			sql_query($q);
			$new_id = sql_insert_id("mail_attachments");

			$ext = $filesys->get_extension($name);

			/* move data to the destination */
			$destination = sprintf("%s/%s/%s.%s", $fspath, $fsdir, $new_id, $ext);
			move_uploaded_file($tmp_name, $destination);

			/* compress file */
			$filesys->FS_compressFile($destination);

			$output->addCode("file: ".$name." uploaded ...");
			$output->addTag("br");
		}
	}
	if (!$return) {
		$output->start_javascript();
			$output->addCode(
				"parent.reset_upload_status();"
			);
		$output->end_javascript();
		$output->exit_buffer();
	} else {
		return $output->generate_output();
	}
?>