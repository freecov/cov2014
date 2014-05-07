<?
	$skip_run_module = 1;
	require_once("index.php");

	if ($_REQUEST["attachment_id"]) {
		$data["attachment_id"] = $_REQUEST["attachment_id"];
	} else {
		$data["content"]       = explode("|",$_REQUEST["content"]);
		$data["mailcode"]      = $data["content"][0];
		$data["attachment_id"] = $data["content"][2];
		$data["email"]         = $data["content"][1];
	}

	if ($data["attachment_id"]) {
		$emailData = new Email_data();
		$emailData->getTrackerImage($data);
		exit();
	}
	if ($_REQUEST["contentlink"]) {
		$data["content"]       = explode("|", $_REQUEST["contentlink"]);
		$data["mailcode"]      = $data["content"][0];
		$data["link"]          = $data["content"][1];
		$data["email"]         = $data["content"][2];
		$emailData = new Email_data();
		$emailData->getTrackerLink($data);
		exit();
	}
?>
