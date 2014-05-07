<?php
/* _handle_magic_quotes_gpc {{{ */
/**
 * Covide needs magic_quotes_gpc to be on in php.ini
 * Since most php installs dont have this we can emulate this.
 * This function will detect wether its on. If not, it will do the addslashes
 */
function _handle_magic_quotes_gpc() {
	/*
	if (!get_magic_quotes_gpc() && !ini_get("magic_quotes_sybase")) {
		$_GET     = _magic_quotes_add($_GET);
		$_POST    = _magic_quotes_add($_POST);
		$_COOKIE  = _magic_quotes_add($_COOKIE);
		$_REQUEST = _magic_quotes_add($_REQUEST);
		$_FILES   = _magic_quotes_add($_FILES);
		$_ENV     = _magic_quotes_add($_ENV);
		$_SERVER  = _magic_quotes_add($_SERVER);
	}
	*/
}
/* }}} */
/* _magic_quotes_add {{{ */
/**
 * addslashes run on input
 *
 * @param mixed $mixed input to addslashes. If it's an array recurse it
 * @return mixed the addslashes version of the input data
 */
function _magic_quotes_add($mixed) {
	if (is_array($mixed)) {
		foreach ($mixed as $key => $value) {
			$mixed[$key] = _magic_quotes_add($value);
		}
		return $mixed;
	} else {
		return addslashes($mixed);
	}
}
/* }}} */
?>
