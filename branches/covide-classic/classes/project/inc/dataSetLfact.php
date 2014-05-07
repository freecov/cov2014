<?php
	if (!class_exists("Project_data")) {
		die("no class definition found");
	}
	if ($_REQUEST["id"] && $_REQUEST["setlfact_month"] && $_REQUEST["setlfact_day"] && $_REQUEST["setlfact_year"]) {
		$lfact = mktime(0, 0, 0, $_REQUEST["setlfact_month"], $_REQUEST["setlfact_day"], $_REQUEST["setlfact_year"]);
		$sql = sprintf("UPDATE project SET lfact = %d WHERE id = %d", $lfact, $_REQUEST["id"]);
		$res = sql_query($sql);
	}
	header("Location: index.php?mod=project&action=showhours&id=".(int)$_REQUEST["id"]);
?>
