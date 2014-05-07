<?php
	/* data manipulation methods */
	/* _handle_php_bugs {{{ */
	/**
	 * Handle some php bugs.
	 *
	 * There's some weird bugs when register_globals is on.
	 * You can clear them with stuff like this: ?GLOBALS&GLOBALS[bla]=test
	 * So what we do is detect this and bail out.
	 * We also make sure that if register_globals is on the gpc stuff will be removed from the globals stuff
	 */
	function handle_php_bugs() {
		/**
		 * catch "PHP5 Globals Vulnerability".
		 * code taken from Advisory http://www.ush.it/2006/01/25/php5-globals-vulnerability/
		 */
		if (isset($HTTP_POST_VARS['GLOBALS']) || isset($_POST['GLOBALS']) || isset($HTTP_POST_FILES['GLOBALS']) || isset($_FILES['GLOBALS']) ||
			isset($HTTP_GET_VARS['GLOBALS']) || isset($_GET['GLOBALS']) || isset($HTTP_COOKIE_VARS['GLOBALS']) || isset($_COOKIE['GLOBALS']))
			die("GLOBAL GPC hacking attemt!");
		/**
		 *	if register_globals is on, you cannot turn it off with ini_set.
		 *	The vars will be registered before the ini_set is executed.
		 *	We can fake register_globals is off by removing the GPCFR keys from
		 *	the global var space :) I got the idea from Alan Hogan with his comment on php.net ini_set function docs.
		 *	I rewrote it to match mvblog codestyle
		 */
		if (ini_get("register_globals")) {
			check_global_vars($_GET);
			check_global_vars($_POST);
			check_global_vars($_COOKIE);
			check_global_vars($_FILES);
			check_global_vars($_REQUEST);
		}
	}
	function check_global_vars(&$global) {
		foreach ($global as $key => $value)
			if (preg_match("/^([a-z]|_){1}([a-z0-9]|_)*$/si", $key))
				unset($GLOBALS[$key]);
	}
	/* }}} */
?>
