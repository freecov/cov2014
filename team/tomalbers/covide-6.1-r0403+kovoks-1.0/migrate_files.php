<?php
	$skip_run_module = 1;
	/* set time limti to 100 * 60 sec */
	set_time_limit(6000);

	require("index.php");

	$fsdata = new Filesys_data();
	$maildata = new Email_data();

	/* drop email messages stripped to filesys */
	$q = "select mail_id from mail_messages_data order by mail_id";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$maildata->dropMailBodyToFilesys($row["mail_id"]);
	}
	die();

	/* migrate filesys files */
	$q = "select * from filesys_files order by id";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$ext = $fsdata->get_extension($row["name"]);
		$fsdata->rename_filetype($row["id"], "dat", $ext, "files");
	}

	/* migrate email attachments */
	$q = "select * from mail_attachments order by id";
	$res = sql_query($q);
	while ($row = sql_fetch_assoc($res)) {
		$ext = $fsdata->get_extension($row["name"]);
		$fsdata->rename_filetype($row["id"], "dat", $ext, "email");
	}
?>