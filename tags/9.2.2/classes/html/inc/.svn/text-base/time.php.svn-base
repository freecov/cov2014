<?php
	if ($_REQUEST["t"]) {
		$s = "
			document.getElementById('clock_seconds').innerHTML = '00';
			clock_start = 0;
		";
	} else {
		$s = "";
	}
	echo sprintf("
		document.getElementById('clock_time').innerHTML = '%s'; %s ",
			date("H:i:"), $s);
?>
