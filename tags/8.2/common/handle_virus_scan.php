<?php
	/* some virus scanner functionality to scan the $_FILES array */
	function handle_virus_scan() {
		// check for custom config directive inside offices.php
		require("conf/offices.php");
		if ($virus["conf"])
			$conf = $virus["conf"];

		// check if defaults are set
		if (!is_array($conf))                           $conf = array();
		if (!array_key_exists("enable_clamav", $conf))  $conf["enable_clamav"] = 0;
		if (!array_key_exists("enable_fprot", $conf))   $conf["enable_fprot"]  = 1;

		if (in_array(1, $conf) && is_array($_FILES)) {
			foreach ($_FILES as $f) {
				if (is_array($f["tmp_name"])) {
					foreach ($f["tmp_name"] as $k=>$v) {
						$ret = virscan_clamav($v, $conf);
						virscan_handle_response($ret, $f["name"][$k]);

						$ret = virscan_fprot($v, $conf);
						virscan_handle_response($ret, $f["name"][$k]);
					}
				} else {
					$ret = virscan_clamav($f["tmp_name"], $conf);
					virscan_handle_response($ret, $f["name"][$k]);

					$ret = virscan_fprot($f["tmp_name"], $conf);
					virscan_handle_response($ret, $f["name"]);
				}
			}
		}
	}
	function virscan_clamav($file, $conf) {
		if ($conf["enable_clamav"] && file_exists("/usr/bin/clamscan")) {
			$cmd = sprintf("/usr/bin/clamscan --no-summary '%s'", $file);
			exec ($cmd, $ret, $ret1);
			if ($ret1 > 0)
				return $ret;
		}
	}
	function virscan_fprot($file, $conf) {
		if ($conf["enable_fprot"] && file_exists("/usr/local/bin/f-prot")) {
			$cmd = sprintf("/usr/local/bin/f-prot -archive -ai '%s'", $file);
			exec ($cmd, $ret, $ret1);
			if ($ret1 > 0) {
				foreach ($ret as $k=>$v) {
					if (!preg_match("/Infection: /s", $v))
						unset($ret[$k]);
				}
				return $ret;
			}

		}
	}
	function virscan_handle_response($ret, $file) {
		if (is_array($ret)) {
			$ret = implode("\n", $ret);
			$ret = str_replace($file, "", $ret);
			ob_clean();
			ob_start();
			header('HTTP/1.1 403 Forbidden');
			header('Status: 403 Forbidden');
			echo "A virus was found in file: ".$file;
			echo sprintf("<script>alert('A virus was found in uploaded file: %s\\n\\n%s');</script>",
				$file, $ret);
			exit();
		}
	}

?>
