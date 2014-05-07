<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Remote Tera-WURFL Remote Client Example</title>
</head>
<body>
<?php
require_once('../../TeraWurflRemoteClient.php');
$wurflObj = new TeraWurflRemoteClient('http://localhost/Tera-Wurfl/webservice.php');
$capabilities = array("product_info","fake_capability");
$wurflObj->getCapabilitiesFromAgent(TeraWurflRemoteClient::getUserAgent(),$capabilities);
echo "<h3>Response from Tera-WURFL ".$wurflObj->getAPIVersion()."</h3>";
echo "<pre>".var_export($wurflObj->capabilities,true)."</pre>";
if($wurflObj->errors){
	foreach($wurflObj->errors as $name => $error){
		echo "$name: $error<br/>";
	}
}
?>
</body>
</html>