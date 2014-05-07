<?php
	if (!$covide) {
		$skip_run_module_cms = 1;
		require_once("index.php");
	}
	$GLOBALS["site_loaded"] = 1;

	if ($_SERVER['REQUEST_URI'] == '/index.php' && !count($_POST)) {
		header("Location: /", true, 301);
		exit;
	}

	//create template object */
	$template = new Tpl_output();
	if ($_REQUEST["include"]) {
		$template->load_inline($_REQUEST["include"]);
		exit();
	} else {
		$template->init_aliaslist();
		$template->redir_default_page();

		$GLOBALS["template"] =& $template;
		ini_set("open_basedir", dirname($_SERVER["SCRIPT_FILENAME"])."/tmp");

		eval("?>".$template->exec_inline($template->getMainPage(), 1));
	}
	exit();
?>
