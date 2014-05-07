<?php
	$skip_run_module = 1;
	require_once("index.php");

	if ($_REQUEST["lang"])
		$GLOBALS["covide"]->override_language(strtoupper($_REQUEST["lang"]));
	
	if ($_REQUEST["action"] == "submit") {
		/* save support call */
		$support_data = new Support_data();
		$support_data->saveSupportForm($_REQUEST["support"]);
	} else {
		/* show support form */
		$options = array(
			"result_url" => $_REQUEST["result_url"],
			"css"        => $_REQUEST["css"],
			"fullpage"   => $_REQUEST["fullpage"]
		);
		$support_output = new Support_output();
		$support_output->showSupportForm($options);
	}
?>