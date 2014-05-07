<?php
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
			$includefile = "classes/".$class_dir."/".$class_file.".php";
		} else {
			$includefile = "classes/".$class_name."/default.php";
		}
		if (file_exists($includefile)) {
			require_once($includefile);
		} else {
			trigger_error("classfile ($includefile) does not exists.", E_USER_ERROR);
		}
	}
?>