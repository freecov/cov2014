<?php
	if (!$covide) {
		$skip_run_module_cms = 1;
		require_once("index.php");
	}

	//create template object */
	$template = new Tpl_output();

	if ($_REQUEST["include"]) {
		$template->load_inline((int)$_REQUEST["include"]);
		exit();

	} else {

		$covide->force_ssl(3); //force to non-ssl
		$template->init_aliaslist();
		$template->redir_default_page();

		$GLOBALS["template"] =& $template;

		ini_set("open_basedir", dirname($_SERVER["SCRIPT_FILENAME"])."/tmp");

		$functions = array(
			"dl",
			"ini_set",
			"exec",
			"shell_exec",
			"passthru",
			"system",
			"proc_open",
			"popen",
			"unlink"
		);
		foreach ($functions as $f) {
			runkit_function_redefine($f, "", "echo \"function (".$f.") disabled by Covide!\";");
		}
		eval("?>".$template->exec_inline($template->getMainPage(), 1)."<?");
	}
	exit();
?>