<?php
/* Configuration variables */
$store = "/tmp/openbare_mappen"; // _NO_ trailing / here
$database = array(
	"server" => "localhost",
	"username" => "user",
	"password" => "pass",
	"database" => "covide",
);
$filestore = "/var/covide_files/cms_oop_info2people/bestanden/"; // trailing / must be set here
/* End configuration */

$folders = array();
$db = mysql_connect($database["server"], $database["username"], $database["password"]);
mysql_select_db($database["database"]);

// find "public folders" dir
$sql = "SELECT id FROM filesys_folders WHERE name = 'openbare mappen' AND parent_id = 0";
$res = mysql_query($sql);
$public_folder = mysql_result($res, 0);

scan_folder($public_folder, $store);

function copy_file($file_id, $file_name, $file_path) {
	/* dir calculation */
	$id = sprintf("%05s", $file_id);
	$id = substr($id, 0, 3);
	$dir = preg_split('//', $id, -1, PREG_SPLIT_NO_EMPTY);
	$dir = implode("/", $dir);
	$src = sprintf("%s/%d.*", $filestore, $dir, $file_id);
	if (substr($src, -3, 3) == ".gz") {
		$compression = 1;
	} else {
		$compression = 0;
	}
	if ($compression) {
		$dst = sprintf("%s/%s.gz", $file_path, $file_name);
	} else {
		$dst = sprintf("%s/%s", $file_path, $file_name);
	}
	exec("cp -a $src '$dst'");
	if ($compression) {
		exec("gunzip '$dst'");
	}
	echo sprintf("copy file id %d from %s/%d.* to %s/%s\n", $file_id, $dir, $file_id, $file_path, $file_name);
}
function scan_folder($folder_id, $path) {
	@mkdir($path);
	$sql = sprintf("SELECT * FROM filesys_files WHERE folder_id = %d", $folder_id);
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		// copy file
		copy_file($row["id"], $row["name"], $path);
	}
	$sql = sprintf("SELECT * FROM filesys_folders WHERE parent_id = %d", $folder_id);
	$res = mysql_query($sql);
	while ($row = mysql_fetch_assoc($res)) {
		scan_folder($row["id"], $path."/".$row["name"]);
	}
}
?>
