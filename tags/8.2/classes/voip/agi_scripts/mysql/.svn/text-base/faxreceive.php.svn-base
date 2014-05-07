#!/usr/bin/php
<?php
// settings.
// change these to reflect your actual situation
$debug = true; //toggle more verbose debugging output
$logfile = "/var/log/asterisk/my_agi.log"; //logfile to use when debugging is on
$faxspool = "/var/spool/asterisk/fax/"; //directory where raw incoming faxes are stored
// end settings.
ob_implicit_flush(true);
set_time_limit(6);
$in = fopen("php://stdin","r");
$stdlog = fopen($logfile, "w");
// Do function definitions before we start the main loop
function read() {
	global $in, $debug, $stdlog;
	$input = str_replace("\n", "", fgets($in, 4096));
	if ($debug) fputs($stdlog, "read: $input\n");
	return $input;
}

function errlog($line) {
	global $err;
	echo "VERBOSE \"$line\"\n";
}

function write($line) {
	global $debug, $stdlog;
	if ($debug) fputs($stdlog, "write: $line\n");
	echo $line."\n";
}

// parse agi headers into array
while ($env=read()) {
	$s = split(": ",$env);
	$agi[str_replace("agi_","",$s[0])] = trim($s[1]);
	if (($env == "") || ($env == "\n")) {
		break;
	}
}

//make database connection
require("db_asp.php");
$db = mysql_connect($db_host, $db_user, $db_passwd);
mysql_select_db($db_database, $db);

//get the filesyspath
$sql = "SELECT code,filesyspath FROM license";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
if ($row["filesyspath"]) {
	$filesyspath = $row["filesyspath"];
} else {
	$filesyspath = "/var/covide_files/";
}

$filesyspath .= $row["code"];

// this is for development.
// delete this when placing the script in the actual * config,
// the agi interface will fill it.
//$agi["uniqueid"] = "asterisk-28502-1113214082.572";
//$agi["callerid"] = "0";
//$agi["dnid"]     = "0342423577";
// end devel overwrites

//where is the fax ?
$faxfile = $faxspool.$agi["uniqueid"];
if (file_exists($faxfile)) {
	//if the file is empty, someone called our faxnumber and the fax contains no data
	//We don't want those empty faxes in Covide, so we remove them.
	if (filesize($faxfile)) {
		//find relation
		if ((int)$agi["callerid"]) {
			$sql = "SELECT a.id,a.companyname FROM address as a LEFT JOIN address_businesscards as b ON a.id=b.address_id WHERE (replace(replace(a.phone_nr,'-',''), ' ','') LIKE '%".$agi["callerid"]."' OR replace(replace(a.fax_nr,'-',''), ' ','') LIKE '%".$agi["callerid"]."' OR replace(replace(a.mobile_nr,'-',''), ' ','') LIKE '%".$agi["callerid"]."' OR replace(replace(b.business_fax_nr,'-',''), ' ','') LIKE '%".$agi["callerid"]."' OR replace(replace(b.business_phone_nr,'-',''), ' ','') LIKE '%".$agi["callerid"]."' OR replace(replace(b.business_mobile_nr,'-',''), ' ','') LIKE '%".$agi["callerid"]."' OR replace(replace(b.personal_fax_nr,'-',''), ' ','') LIKE '%".$agi["callerid"]."' OR replace(replace(b.personal_phone_nr,'-',''), ' ','') LIKE '%".$agi["callerid"]."' OR replace(replace(b.personal_mobile_nr,'-',''), ' ','') LIKE '%".$agi["callerid"]."')";
			$res = mysql_query($sql);
			if (mysql_num_rows($res)) {
				$row = mysql_fetch_assoc($res);
			} else {
				$row["id"] = 0;
				$row["companyname"] = "unknown";
			}
		} else {
			$row["id"] = 0;
			$row["companyname"] = "unknown";
		}
		$q = "INSERT INTO faxes (date,sender,receiver,relation_id) VALUES (".mktime().",'".$agi["callerid"]."','".$agi["dnid"]."',".$row["id"].")";
		$r = mysql_query($q);
		$faxid = mysql_insert_id();
		$cmd = "cp $faxfile $filesyspath/faxes/$faxid.dat";
		$commandout = @system($cmd,$returnval);
		if ($returnval) {
			//something went wrong, delete the database entry
			$q = "DELETE FROM faxes WHERE id=$faxid";
			$r = mysql_query($q);
		}
	} else {
		@unlink($faxfile);
	}
}
?>
