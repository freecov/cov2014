<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurflAdmin
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @version Stable Stable 2.1.1 $Date: 2010/03/01 15:40:10
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
require_once("../../TeraWurfl.php");

$ua = $_GET['ua'];

$tw = new TeraWurfl();
$db = $tw->db;

$missing_tables = false;
if($db->connected === true){
	$required_tables = array(TeraWurflConfig::$CACHE);
	$tables = $db->getTableList();
// See what tables are in the DB
//die(var_export($tables,true));
	foreach($required_tables as $req_table){
		if(!in_array($req_table,$tables)){
			$missing_tables = true;
		}
	}
}
$cap = $db->getDeviceFromCache($ua);
$parts = explode(',',$cap['tera_wurfl']['fall_back_tree']);
$cap['tera_wurfl']['fall_back_tree'] = implode(' - ',$parts);
$nicecap = '';
$groups = array();
foreach($cap as $group => $capability){
	$nicecap .= "<tr><th colspan=\"2\"><a id=\"$group\"/>&nbsp;</th></tr>";
	$groups[] = $group;
	if(is_array($capability)){
		$nicecap .= "<tr><td class=\"cap_heading\">$group</td><td class=\"cap_value\">"; 
		$nicecap .= "<table>";
		$i=0;
		foreach($capability as $property => $value){
			$class = ($i++ % 2 == 0)? 'lightrow': 'darkrow';
			// Primary Group
			$value = (is_bool($value) || $value == "true" || $value == "false")? WurflSupport::showBool($value): $value;
			if($value == ""){
				$value = "[null]";
			}else{
				$value = htmlspecialchars($value);
			}
			$nicecap .= "<tr><td class=\"cap_title $class\">$property</td><td class=\"cap_value $class\">".htmlspecialchars($value)."</td></tr>\n";
		}
		$nicecap .= "</table>";
		$nicecap .= "</td></tr>\n";
	}else{
		// Top Level attribute
		$capability = (is_bool($capability) || $capability == "true" || $capability == "false")? WurflSupport::showBool($capability): $capability;
		if($capability == ""){
			$capability = "[null]";
		}else{
			$capability = htmlspecialchars($capability);
		}
		$nicecap .= "<tr><td class=\"cap_heading\">$group</td><td class=\"cap_value\">$capability</td></tr>\n"; 
	}
	
}
foreach($groups as $num => $group){
	$groups[$num]="<a href=\"#$group\">$group</a>";
}
$grouplinks = implode(' | ',$groups);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Tera-WURFL Cache Browser</title>
<link href="../style.css" rel="stylesheet" type="text/css" /></head>
<body>
<div align="center" class="titlediv version">
	<pre style="margin 0px;"><?php echo htmlspecialchars($ua);?></pre>
</div>
<h3><?php echo $grouplinks;?></h3>
<!-- pre><?php echo htmlspecialchars(var_export($cap,true));?></pre> -->
<table>
<?php echo $nicecap;?>
</table>
<table width="800">
	<tr><td>

</td></tr></table>
</body>
</html>