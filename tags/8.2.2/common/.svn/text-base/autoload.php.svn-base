<?php
/**
  * Covide Includes
  *
  * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
  * @version %%VERSION%%
  * @license http://www.gnu.org/licenses/gpl.html GPL
  * @link http://www.covide.net Project home.
  * @copyright Copyright 2000-2008 Covide BV
  * @package Covide
  */

	//autoload needed object files
	function __autoload($class_name) {
		$class_name = strtolower($class_name);

		if (strpos($class_name, "_")) {
			$class_dir   = preg_replace("/[^a-z0-9]/si", "", substr($class_name, 0, strpos($class_name, "_")));
			$class_file  = preg_replace("/[^a-z0-9]/si", "", substr(strstr($class_name, "_"), 1));

			if ($class_dir == "layout") {
				//TODO: switch statement for output type
				$class_dir = "html";
			}
			$includefile = sprintf("%sclasses/%s/%s.php",
				$GLOBALS["autoloader_include_path"], $class_dir, $class_file);
		} else {
			$includefile = sprintf("%sclasses/%s/default.php",
				$GLOBALS["autoloader_include_path"], $class_name);
		}
		if (file_exists($includefile)) {
			require_once($includefile);
		} else {
			trigger_error("classfile ($includefile) does not exists.", E_USER_ERROR);
		}
	}
?>
