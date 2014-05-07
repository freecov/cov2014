<?php
/**
 * Covide Includes
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2010 Covide BV
 * @package Covide
 */

//autoload needed object files
spl_autoload_register();
spl_autoload_register('__covide_loader');

function __covide_loader($class_name) {
	if (!array_key_exists("autoloader_include_path", $GLOBALS)) {
		$GLOBALS["autoloader_include_path"] = "";
	}
	//Zend is a bit different then our setup.
	//Provide compatibility with that
	if (substr($class_name, 0, 4) == "Zend") {
		$parts = explode("_", $class_name);
		$includefile = sprintf("%sclasses/%s.php", $GLOBALS["autoloader_include_path"], implode("/", $parts));
		$oldinc = ini_get("include_path");
		$newinc = $oldinc."./classes:";
		ini_set("include_path", $newinc);
	} else {
		$class_name = strtolower($class_name);

		if (strpos($class_name, "_")) {
			$class_dir   = preg_replace("/[^a-z0-9]/si", "", substr($class_name, 0, strpos($class_name, "_")));
			$class_file  = preg_replace("/[^a-z0-9]/si", "", substr(strstr($class_name, "_"), 1));

			if ($class_dir == "layout") {
				//TODO: switch statement for output type
				$class_dir = "html";
			}
			$includefile = sprintf("%sclasses/%s/%s.php", $GLOBALS["autoloader_include_path"], $class_dir, $class_file);
		} else {
			$includefile = sprintf("%sclasses/%s/default.php", $GLOBALS["autoloader_include_path"], $class_name);
		}
	}
	if (file_exists($includefile)) {
		require_once($includefile);
	} else {
		#trigger_error("classfile ($includefile) does not exists.", E_USER_ERROR);
		return false;
	}
}
?>
