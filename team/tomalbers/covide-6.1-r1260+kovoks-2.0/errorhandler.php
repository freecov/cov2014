<?php
error_reporting(0); //we do our own error handling

// user defined error handling function
function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
	// timestamp for the error entry
	$dt = date("Y-m-d H:i:s (T)");

	// define an assoc array of error string
	// in reality the only entries we should
	// consider are E_WARNING, E_NOTICE, E_USER_ERROR,
	// E_USER_WARNING and E_USER_NOTICE
	$errortype = array (
		E_ERROR           => "Error",
		E_WARNING         => "Warning",
		E_PARSE           => "Parsing Error",
		E_NOTICE          => "Notice",
		E_CORE_ERROR      => "Core Error",
		E_CORE_WARNING    => "Core Warning",
		E_COMPILE_ERROR   => "Compile Error",
		E_COMPILE_WARNING => "Compile Warning",
		E_USER_ERROR      => "User Error",
		E_USER_WARNING    => "User Warning",
		E_USER_NOTICE     => "User Notice",
		E_STRICT          => "Runtime Notice"
	);
	$err="<ul>";
	$vDebug = debug_backtrace();
	$h.="<table border=\"0\" cellcpacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\"><tr>";
	$h.="<td bgcolor=\"#CDCDCD\">Class</td>";
	$h.="<td bgcolor=\"#CDCDCD\">Function Name</td>";
	$h.="<td bgcolor=\"#CDCDCD\">File Name</td>";
	$h.="<td bgcolor=\"#CDCDCD\">Line</td>";
	$h.="</tr>";
	for ($i=1; $i<count($vDebug);$i++) {
		$val = $vDebug[$i];
		if ($i==1) {
			$bg = "#EC7C7C";
		} else {
			$bg = "#FFFFFF";
		}
		$h.="<tr>";
		$h.="<td bgcolor=\"$bg\">".$val["class"]."</td>";
		$h.="<td bgcolor=\"$bg\">".$val["function"]."</td>";
		$h.="<td bgcolor=\"$bg\">".$val["file"]."</td>";
		$h.="<td bgcolor=\"$bg\">".$val["line"]."</td>";
		$h.="</tr>";
	}
	$h.="</table>";
	$h.="</ul>";

	$err .= "There was an error. Details:<br>";
	$err .= "<table><tr>\n";
	$err .= "\t<td>date</td><td>$dt</td>\n</tr><tr>\n";
	$err .= "\t<td>file</td><td>$filename</td>\n</tr><tr>";
	$err .= "\t<td>errornr</td><td>" . $errno . "</td>\n</tr><tr>\n";
	$err .= "\t<td>errortype</td><td>" . $errortype[$errno] . "</td>\n</tr><tr>\n";
	$err .= "\t<td>errormessage</td><td>" . nl2br($errmsg) . "</td>\n</tr><tr>\n";
	$err .= "\t<td colspan=\"2\">debug trace</td>\n";
	$err .= "</tr></table>\n";
	$err .= $h;

	// for testing
	//echo "<pre>".str_replace("<", "&lt;", str_replace(">", "&gt;", $err));
	//print_r(debug_backtrace());
	//echo "</pre>";
	// save to the error log, and e-mail me if there is a critical user error
	//error_log($err, 3, "/usr/local/php4/error.log");
	//if ($errno == 2 || $errno == E_USER_WARNING || $errno == E_USER_ERROR || $errnr == E_ERROR || $errnr == E_PARSE || $errnr == E_CORE_ERROR || $errnr == E_CORE_ERROR || $errnr == E_WARNING) {
	if ($errno < 1025) {
		//mail("phpdev@example.com", "Critical User Error", $err);
		echo $err;
	}
}
$old_error_handler = set_error_handler("userErrorHandler");
?>
