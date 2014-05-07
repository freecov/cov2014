<?php
/**
 * Covide Groupware-CRM error handler
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */
/* covide_error_handler {{{ */
/**
 * Covide error handler
 *
 * Prints an error including backtrace when an error occurs
 *
 * @param int $errno The error number
 * @param string $errstr The error message
 * @param string $errfile The file the error is in
 * @param int $errline The line the error is on
 *
 * @return void
 */
function covide_error_handler($errno, $errstr, $errfile, $errline) {
	// errors can be supressed with an @ sign
	if (error_reporting() == 0) {
		return true;
	}

	// if we are called by trigger_error we will have 5 arguments, else we caught an exception
	if (func_num_args() == 5) {
		$backtrace = array_reverse(debug_backtrace());
	} else {
		$exc = func_get_arg(0);
		$errno = $exc->getCode();
		$errno = $exc->getMessage();
		$errno = $exc->getFile();
		$errno = $exc->getLine();
		$backtrace = $exc->getTrace();
	}
	if ($errno == E_NOTICE || strpos(strtolower($errfile), strtolower($_SERVER["DOCUMENT_ROOT"])) === false || strpos(strtolower($errfile), "pear") !== false) {
		return true;
	}

	$errorType = array(
		E_ERROR             => "ERROR",
		E_WARNING           => "WARNING",
		E_PARSE             => "PARSE ERROR",
		E_NOTICE            => "NOTICE",
		E_CORE_ERROR        => "CORE ERROR",
		E_CORE_WARNING      => "CORE WARNING",
		E_COMPILE_ERROR     => "COMPILE ERROR",
		E_COMPILE_WARNING   => "COMPILE WARNING",
		E_USER_ERROR        => "USER ERROR",
		E_USER_WARNING      => "USER WARNING",
		E_USER_NOTICE       => "USER NOTICE",
		E_STRICT            => "STRICT NOTICE",
		E_RECOVERABLE_ERROR => "RECOVERABLE ERROR"
	);
	if (array_key_exists($errno, $errorType)) {
		$err = $errorType[$errno];
	} else {
		$err = "CAUGHT EXCEPTION";
	}
	echo "<pre>";
	echo "<b><font color=red>An error occured inside Covide. Error details are displayed below:</font></b>\n\n";
	echo "<b>error details:</b>\n";
	echo "$err: $errstr in $errfile on line $errline\n";
	// do some backtrace magic
	echo "<b>Backtrace:</b>\n";
	echo "<pre>";
	echo "<style>";
	echo "td { border-left: 1px solid black; border-bottom: 1px solid black;}";
	echo "table { border-right: 1px solid black; border-top: 1px solid black;}";
	echo "</style>";
	echo "<table cellpadding=\"0\" cellspacing=\"0\"><tr>";
	echo "<td>&nbsp;file&nbsp;</td><td>&nbsp;line&nbsp;</td><td>&nbsp;function&nbsp;</td>";
	echo "</tr>";
	foreach ($backtrace as $v) {
		if ($v["function"] == "covide_error_handler") {
			continue;
		}
		echo "<tr>";
		echo "<td>&nbsp;".$v["file"]."&nbsp;</td>";
		echo "<td align=\"right\">&nbsp;".$v["line"]."&nbsp;</td>";
		if (isset($v["class"])) {
			echo "<td>&nbsp;".$v["class"]."::".$v["function"]."(";
			if (isset($v["args"])) {
				echo errorhandler_parseArguments($v["args"]);
			}
			echo ")&nbsp;</td>";
		} else {
			echo "<td>&nbsp;".$v["function"]."(";
			if (isset($v["args"])) {
				echo errorhandler_parseArguments($v["args"]);
			}
			echo ")&nbsp;</td>";
		}
		echo "</tr>";
	}
	echo "</table>\n\n";
	echo "</pre>";
	die();
}
/* }}} */
/* errorhandler_parseArguments {{{ */
/**
 * Format the arguments for use in the errorhandler
 *
 * @param array $arguments The arguments for a function/method in the error backtrace
 *
 * @return string formatted output of the arguments
 */
function errorhandler_parseArguments($arguments) {
	$output = "";
	foreach ($arguments as $argument) {
		switch (strtolower(gettype($argument))) {
		case "string":
			$output .= $seperator."\"".str_replace(array("\n"), array(""), $argument)."\"";
			break;
		case "boolean":
			$output .= $seperator.(bool)$argument;
			break;
		case "object":
			$output .= $seperator."object(".get_class($argument).")";
			break;
		case "array":
			$ret = "array(";
			$sep = "";
			foreach ($argument as $k => $v) {
				$ret .= $seperator.errorhandler_parseArguments($k)." => ".errorhandler_parseArguments($v);
				$sep = ", ";
			}
			$ret .= ")";
			$output .= $seperator.$ret;
			break;
		case "resource":
			$output .= $seperator."resource(".get_resource_type($argument).")";
			break;
		default:
			$output .= $seperator.$argument;
			break;
		}
		$seperator = ", ";
	}
	return $output;
}
/* }}} */
?>
