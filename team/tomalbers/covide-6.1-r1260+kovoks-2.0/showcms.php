<?
	$skip_run_module = 1;
	require_once("index.php");

	if ($_REQUEST["id"]) {
		$id = preg_replace("/[^0-9]/s", "", $_REQUEST["id"]);
		if ($id) {
			$cmsData = new Cms_data();
			$cmsData->getCmsFile((int)$id);
		}
	}
	exit();
?>
