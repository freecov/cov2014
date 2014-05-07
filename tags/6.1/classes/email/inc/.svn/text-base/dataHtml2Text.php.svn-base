<?php
	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	$temp = tempnam($GLOBALS["covide"]->temppath, "MAIL_");
	$handle = fopen($temp, "w");
	fwrite($handle, $html);
	fclose($handle);

	$cmd = sprintf("elinks -dump -dump-charset LATIN1 -force-html -no-connect 1 -no-home 1 %s", $temp);
	exec ($cmd, $ret, $retcode);

	$return = implode("<br>", $ret);

	unlink($temp);
?>