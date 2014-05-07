<?php
	$skip_run_module = 1;
	require_once("index.php");

	if ($_REQUEST["lang"])
		$GLOBALS["covide"]->override_language(strtoupper($_REQUEST["lang"]));
	
	// we need this for error messages from recaptcha
	$output = new Layout_output();
	$output->addTag('style', array('type' => 'text/css'));
	$output->addCode('* { font-family: sans-serif; font-size: 8pt }');
	$output->endTag('style');
	echo $output->generate_output();
	
	$tpl = new Tpl_Output;
	if ($_REQUEST['action'] == 'submit') {
		if ($tpl->cms_license['recaptcha_private']) {
			require('classes/recaptcha/recaptchalib.php');
			$response = recaptcha_check_answer (
				$tpl->cms_license['recaptcha_private'],
				$_SERVER["REMOTE_ADDR"],
				$_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]
			);
		} else {
			$response = new StdClass;
			$response->is_valid = true;
		}
	} else {
		$response = new StdClass;
		$response->is_valid = null;
	}
	if ($response->is_valid === false) {
		/* not accepted because of captcha */
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf(" alert('%s'); ",
				addslashes(gettext(
					"Wrong confirmation text filled in. Please try again."
				))
			));
		$output->end_javascript();
		echo $output->generate_output();

	} elseif ($_REQUEST["action"] == "submit") {
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
