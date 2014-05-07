<?php
	if (!$covide) {
		$skip_run_module_cms = 1;
		require_once("index.php");
	}

	$GLOBALS["site_loaded"] = 1;

	//create template object */
	$template = new Tpl_output();

	if ($_REQUEST["include"]) {
		/*
		$ident = sprintf("include_%d", $_REQUEST["include"]);
		$fetch = $template->getApcCache($ident);
		if ($fetch) {
			echo $fetch;
		} else {
			$template->load_inline($_REQUEST["include"]);
			$template->setApcCache($ident, ob_get_contents());
		}
		*/
		$template->load_inline($_REQUEST["include"]);
		exit();
	} else {

		//$covide->force_ssl(3); //force to non-ssl
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
			#runkit_function_remove($f);
			#runkit_function_redefine($f, "", "echo \"function (".$f.") disabled by Covide!\";");
		}
		$ident = sprintf("page_%d", $template->pageid);
		if (is_numeric($template->pageid) && !$_REQUEST["start"]) {
			$fetch = $template->getApcCache($ident);
			if ($fetch) {
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
