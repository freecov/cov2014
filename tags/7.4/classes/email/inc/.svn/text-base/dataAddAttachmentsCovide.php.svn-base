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

	$fsdata = new Filesys_data();
	$fspath = $GLOBALS["covide"]->filesyspath;
	$fsdir_source  = "bestanden";
	$fsdir_target  = "email";

	$ids = explode(",", $REQUEST["ids"]);
	$mail_id = $REQUEST["mail_id"];
	if (!$mail_id) {
		die("no mail identifier found");
	}

	$new_ids = array();
	foreach ($ids as $id) {
		$file = $fsdata->getFileById($id, 1);

		/* insert file into dbase */
		$q = "insert into mail_attachments (message_id, name, size, type) values ";
		$q.= sprintf("(%d, '%s', '%s', '%s')", $mail_id, addslashes($file["name"]), $file["size"], $file["type"]);
		sql_query($q);
		$new_id = sql_insert_id("mail_attachments");
		$new_ids[] = $new_id;

		/* move data to the destination */
		$ext = $fsdata->get_extension($file["name"]);

		$source = sprintf("%s/%s/%s.%s", $fspath, $fsdir_source, $file["id"], $ext);
		$destination = sprintf("%s/%s/%s.%s", $fspath, $fsdir_target, $new_id, $ext);

		#@copy($source, $destination);
		$fsdata->FS_copyFile($source, $destination);
	}
	echo "mail_upload_update_list();";

?>