<?php
require("../../../common/headers.php");
no_cache_headers();
session_start();

/* time to store old logs */
$_cleanup_time = 5; //days

/* include paths */
ini_set('include_path',ini_get('include_path').':./PEAR:');

/* open database connection and set some defaults. */
require_once("MDB2.php");
require_once("../../../conf/offices.php");

$options = array(
	"persistent"  => TRUE,
	'portability' => MDB2_PORTABILITY_NONE
);

$db =& MDB2::connect($dsn, $options);
if (PEAR::isError($db)) {
	echo ("Warning: no Covide office configured at this address or no valid database specified. ");
	echo ($db->getMessage());
	die();
}
$db->setFetchMode(DB_FETCHMODE_ASSOC);
$db->setOption("autofree", 1);

/* get license */
$q = sprintf("select code from license");
$res = $db->query($q);
$license = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

/* get user info */
$q = sprintf("select language, xs_usermanage, xs_limitusermanage, id, username from users where id = %d", $_SESSION["user_id"]);
$res = $db->query($q);
$user = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

switch ($row["language"]) {
	case "NL" : $language = "nl_NL"; break;
	case "DE" : $language = "de_DE"; break;
	case "ES" : $language = "es_ES"; break;
	case "IT" : $language = "it_IT"; break;
	case "DA" : $language = "da_DK"; break;
	case "NO" : $language = "nn_NO"; break;
	default   : $language = "en_US"; break;
}
putenv("LANG=$language");
setlocale(LC_ALL, $language);

require_once dirname(__FILE__)."/src/phpfreechat.class.php";
$params = array();

$c = $_REQUEST["c"];
if ($c && $c != "global") {
	$server = sprintf("%s_%s", $license["code"], $c);
	$chan_name = "Private channel";
} else {
	$server = $license["code"];
	$chan_name = "Public channel";
}
$inv = sprintf("<a href='javascript: invite();' style='color: black;'>%s</a>",
	gettext("Invite other user(s)"));

/* parse dsn to readable info for the chat */
$dsn = preg_replace("/^mysql(i){0,1}:\/\//s", "", $dsn);
$dsn = str_replace("@tcp(", ":", $dsn);
$dsn = str_replace(")/", ":", $dsn);
$dsn = explode(":", $dsn);

/* mysql */
$params["container_type"] = "Mysql";

$conn["mysql_host"]     = $dsn[2];
$conn["mysql_port"]     = $dsn[3];
$conn["mysql_database"] = $dsn[4];
$conn["mysql_table"]    = "phpfreechat";
$conn["mysql_username"] = $dsn[0];
$conn["mysql_password"] = $dsn[1];
$GLOBALS["chat_dsn"] =& $conn;

$params["title"]                = sprintf("Covide chat - %s - %s", $chan_name, $inv);
$params["quit_on_closedwindow"] = true;
$params["frozen_nick"]          = true;
$params["max_nick_len"]         = 35;
$params["startwithsound"]       = false;
//$params["shownotice"]         = 0;
$params["refresh_delay"]        = 2000;
$params["timeout"]              = 5000;
$params["channels"]             = array( substr($chan_name, 0, 25) ); //limit to 26
$params["theme"]                = "zilveer";
$params["nick"]                 = substr($user["username"], 0, 34);
$params["isadmin"]              = ($user["xs_usermanage"] || $user["xs_limitusermanage"]) ? true : false;
$params["serverid"]             = $server;

$params["get_ip_from_xforwardedfor"] = ($_SERVER["HTTP_X_FORWARDED_FOR"]) ? true:false;

$chat = new phpFreeChat($params);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Chat [<?=$server?>]</title>
	<script type="text/javascript">
		function invite() {
			try {
				opener.showOnlineUsers('', '', '<?= ($_REQUEST["c"]) ? $_REQUEST["c"] : "global" ?>');
			} catch(e) {
				alert('<?= addslashes(gettext('Cannot find the Covide window. Please close this chat and invite again.')) ?>');
			}
		}
	</script>
</head>
<body>
	<div class="content">
		<?
			$chat->printChat();

			/* cleanup some old info */
			$q = sprintf("delete from phpfreechat where timestamp < %d",
				mktime(0,0,0,date("m"),date("d")-$_cleanup_time,date("Y")));
			$db->query($q);
		?>
	</div>
</body>
</html>
