<?php
if (!class_exists("Product_data")) {
	die("no class definition found");
}
if ($addresstype == "private") {
	/* check if user record is linked */
	$sql = sprintf("SELECT COUNT(*) FROM users WHERE address_id=%d", $address_id);
	$res = sql_query($sql);
	$linkcount = sql_result($res, 0);
	if ($linkcount) {
		die("user record, we cannot delete this entry !");
	}
	$sql = sprintf("DELETE FROM address_private WHERE id=%d", $address_id);
	$res = sql_query($sql);
}
$output = new Layout_output();
$output->start_javascript();
	$output->addCode("
		if (opener) {
			opener.document.getElementById('deze').submit();
			var t = setTimeout('window.close();', 100);
		}
	");
$output->end_javascript();
$output->exit_buffer();
?>
