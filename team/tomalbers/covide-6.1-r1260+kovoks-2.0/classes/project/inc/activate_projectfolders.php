<?
die("THIS SCRIPT IS REAL BAD!");
require("../inc_common.php");
// find folder
$sql = "SELECT id FROM filesys_mappen WHERE naam='openbare mappen' AND hoofdmap=0";
$res = sql_query($sql);
$openbare_mappen = sql_result($res,0);
$sql = "SELECT COUNT(id) FROM filesys_mappen WHERE naam='projecten' AND hoofdmap=$openbare_mappen";
$res = sql_query($sql);
$aantal = sql_result($res,0);
if (!$aantal) {
	// make folder
	$sql = "INSERT INTO filesys_mappen SET naam='projecten', sticky=1, openbaar=1, hoofdmap=$openbare_mappen";
	$res = sql_query($sql);
	$pmid = sql_insert_id("filesys_mappen");
} else {
	$sql = "SELECT id FROM filesys_mappen WHERE naam='projecten' AND hoofdmap=$openbare_mappen";
	$res = sql_query($sql);
	$pmid = sql_result($res,0);
}
// make project folders


$sql = "SELECT id,naam,debiteur FROM project";
$res = sql_query($sql);
while ($row = sql_fetch_assoc($res)) {
	if ($row["debiteur"]) {
		$q = "INSERT INTO filesys_mappen SET naam='".addslashes($row["naam"])."', hoofdmap=".$pmid.", relatie=1, relatie_id=".$row["debiteur"].", openbaar=1, sticky=1, project_id=".$row["id"];
		$r = sql_query($q);
	} else {
		$q = "INSERT INTO filesys_mappen SET naam='".addslashes($row["naam"])."', hoofdmap=".$pmid.", relatie=0, openbaar=1, sticky=1, project_id=".$row["id"];
		$r = sql_query($q);
	}
}
?>
