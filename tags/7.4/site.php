<?php

	if (!$covide) {
		$skip_run_module_cms = 1;
		require_once("index.php");
	}
	$GLOBALS["site_loaded"] = 1;

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

		$ident = sprintf("page_%d", $template->pageid);
		if (is_numeric($template->pageid) && !$_REQUEST["start"]) {
			$fetch = $template->getApcCache($ident);
			if ($fetch) {
				header("X-Covide-Cache: cached");
				echo $fetch;
			} else {
				eval("?>".$template->exec_inline($template->getMainPage(), 1)."<?");
				$template->setApcCache($ident, ob_get_contents());
			}
		} else {
			eval("?>".$template->exec_inline($template->getMainPage(), 1)."<?");
		}
	}
	exit();
?>
