<?php
/**
 * Covide Groupware-CRM Sales data class
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Index_data {

	/* variables */
	private $data = array();
	private $beagle_home = "/var/covide_files";

	private $max_hits = 2000;
	private $beagle_cache = array();

	/* methods */
	/* beagle daemon */
	public function beagleDaemon() {
		return true;

		/* check if daemon is running */
		$cmd = "pidof beagled";
		exec($cmd, $ret, $retval);

		/* if daemon is not running, start it */
		if (!preg_match("/\d{1,}/s", $ret[0])) {

			#$cmd = "export BEAGLE_HOME=".$this->beagle_home." && beagled && true";
			#$handle = popen($cmd, "r");
			#pclose($handle);

			/* redirect to itself */
			$p = $_REQUEST["search"];
			$url = "index.php?mod=index";
			foreach ($p as $k=>$v) {
				$url.= "&search[$k]=$v";
			}
			echo "<html><body><script>location.href='".$url."';</script></body></html>";
			exit();
		}


	}
	/* beagle exec class */
	public function beagleSearch($str, $type, $address_id=0, $websearch=0) {

		#$this->beagleDaemon();

		$str = escapeshellarg($str);
		$str = escapeshellcmd($str);
		$str = preg_replace("/[^a-z0-9\-_ ]/si", "", $str);

		$results = array();

		/* get Covide base path and identifier */
		$fspath = "file://".$GLOBALS["covide"]->filesyspath."/";
		$fspath_global = $fspath;

		if ($type == "files") {
			$fspath.= "bestanden/";
			$data = new Filesys_data();
		} elseif ($type == "maildata" || $type == "maildata_private") {
			$fspath.= "maildata/";
			$data = new Email_data();
		} else {
			$fspath.= "email/";
			$data = new Email_data();
		}
		$max_fs_hits = pow(2,30);
		//$max_fs_hits = 200;
		//$max_fs_hits = 50000;

		require("conf/offices.php");
		if ($beagle["home"])
			$this->beagle_home = $beagle["home"];

		$cmd = sprintf("export BEAGLE_HOME=%s && beagle-query --max-hits %d '%s*' | grep '%s'",
			$this->beagle_home, $max_fs_hits, $str, $fspath_global);


		if ($beagle["prefix"])
			$cmd = sprintf("%s \"%s\"", $beagle["prefix"], $cmd);

		if (!is_array($this->beagle_cache[crc32($str)])) {
			exec($cmd, $ret, $retval);
			//$ret = array();
			$this->beagle_cache[crc32($str)] = $ret;
		} else {
			$ret = $this->beagle_cache[crc32($str)];
		}

		/* foreach result */
		foreach ($ret as $i=>$file) {
			/* if path matches the requested category */
			if (strstr($file, $fspath)) {
				/* try to extract the file id */
				$file = preg_replace("/\#.*$/s", "", $file);
				$file = basename($file);
				$file = (int)preg_replace("/\..*$/s", "", $file);

				/* switch category to check permissions */
				if ($type == "maildata") {
					$xs = $data->checkFilePermissions("", $file, $address_id);
				} elseif ($type == "maildata_private") {
					$xs = $data->checkFilePermissions("", $file, $address_id, 1);
				} elseif ($type == "email") {
					$xs = $data->checkFilePermissions($file, "", $address_id);
				} else {
					if ($websearch)
						$xs = $data->checkWebPermissions($file);
					else
						$xs = $data->checkFilePermissions($file, $address_id);
				}
				/* if we have access, add the file */
				if ($xs)
					$results[$file] = $xs;
			}
		}
		return $results;
	}
	public function execSearch($param) {
		/* set time limti to 5 * 60 sec */
		set_time_limit(60*5);
		session_write_close();

		// Split intelligent, keep single quoted keywords together, so people can search for 'tom a'
		// Copied from http://us3.php.net/fgetcsv
		$expr="/ (?=(?:[^']*'[^']*')*(?![^']*'))/";
		$ressplit=preg_split($expr,stripslashes($param["search"]["phrase"]));
		$zoekwoorden = preg_replace("/^'(.*)'$/","$1",$ressplit);

		$and     = $param["search"]["and"];

		foreach ($zoekwoorden as $k=>$v) {
			if (strlen($v)>=3) {
				$param["search"]["phrase"] = addslashes($v);
				$results[] = $this->execSearchCommand($param);
			}
		}
		if ($param["search"]["note_user_id"]) {
			$results[] = $this->execSearchCommand($param);
		}

		/* negative diff or merge the arrays */
		$data = $results[0];
		for ($i = 1; $i < count($results); $i++) {
			/* calendar */
			if (!$data["calendar"]) $data["calendar"] = array();
			if ($results[$i]["calendar"]) {
				$this->search_merge($data["calendar"], $results[$i]["calendar"], $and);
			}

			/* notes */
			if (!$data["notes"]) $data["notes"] = array();
			if ($results[$i]["notes"]) {
				$this->search_merge($data["notes"], $results[$i]["notes"], $and);
			}

			/* addressbook */
			$ary = array("other", "bcards", "address", "users");
			foreach ($ary as $t) {
				/* merge data part */
				if (!$data["address"][$t]["address"]) $data["address"][$t]["address"] = array();
				if ($results[$i]["address"][$t]["address"]) {
					$this->search_merge($data["address"][$t]["address"], $results[$i]["address"][$t]["address"], $and);
				}
				/* update count stats */
				$data["address"][$t]["count"] = count($data["address"][$t]["address"]);
			}
			/* email */
			$ary = array("private", "archive");
			foreach ($ary as $t) {
				/* merge data part */
				if (!$data["email"][$t]["data"]) $data["email"][$t]["data"] = array();
				if ($results[$i]["email"][$t]["data"]) {
					$this->search_merge($data["email"][$t]["data"], $results[$i]["email"][$t]["data"], $and);
				}
				/* update count stats */
				$data["email"][$t]["count"] = count($data["email"][$t]["data"]);
			}
			/* bin email */
			if (!$data["binemail"]) $data["binemail"] = array();
			if ($results[$i]["binemail"]) {
				$this->search_merge($data["binemail"], $results[$i]["binemail"], $and);
			}
			/* bin files */
			if (!$data["binfiles"]) $data["binfiles"] = array();
			if ($results[$i]["binfiles"]) {
				$this->search_merge($data["binfiles"], $results[$i]["binfiles"], $and);
			}
			/* filesys */
			$ary = array("files", "folders");
			foreach ($ary as $t) {
				/* merge data part */
				if (!$data["filesys"][$t]) $data["filesys"][$t] = array();
				if ($results[$i][$t]) {
					$this->search_merge($data["filesys"][$t], $results[$i]["filesys"][$t], $and);
				}
			}
			/* support */
			if (!$data["support"]) $data["support"] = array();
			if ($results[$i]["support"]) {
				$this->search_merge($data["support"], $results[$i]["support"], $and);
			}
		}

		/* calendar */
		$this->array_sort($data["calendar"], "timestamp_start");

		/* address */
		$this->array_sort($data["address"]["address"]["address"], "companyname");
		$this->array_sort($data["address"]["bcards"]["address"], "fullname");
		$this->array_sort($data["address"]["users"]["address"], "fullname");

		/* notes */
		$this->array_sort($data["notes"], "timestamp");

		/* email */
		$this->array_sort($data["email"]["archive"]["data"], "timestamp");
		$this->array_sort($data["email"]["private"]["data"], "timestamp");

		/* binary files */
		$this->array_sort($data["binemail"]);
		$this->array_sort($data["binfiles"]);

		/* filesys */
		$this->array_sort($data["filesys"]["files"], "name");
		$this->array_sort($data["filesys"]["folders"], "name");
		
		/* support */
		$this->array_sort($data["support"], "id");
		return $data;
	}

	private function array_sort(&$array, $sortkey="") {

		if (!is_array($array))
			return false;

		if ($sortkey) {
			/* sort by multi-dimensional index */
			$tmp = array();
			foreach ($array as $k=>$v) {
				$tmp[$k] = $v[$sortkey];
			}
			if ($sortkey == "timestamp")
				array_multisort($tmp, SORT_DESC, $array);
			else
				array_multisort($tmp, SORT_ASC, $array);

		} else {
			/* sort by key and pass back */
			ksort($array);
		}
	}

	private function search_merge(&$main, &$new, $and, $sortkey="") {
		if ($and)
			$main = array_intersect_assoc($main, $new);
		else
			$main = array_merge($main, $new);
	}

	/*
	function aSortBySecondIndex(&$multiArray, $secondIndex) {
		return $multiArray;
		if (is_array($multiArray)) {
			while (list($firstIndex, ) = each($multiArray))
					$indexMap[$firstIndex] = $multiArray[$firstIndex][$secondIndex];
			asort($indexMap);
			while (list($firstIndex, ) = each($indexMap))
					if (is_numeric($firstIndex))
							$sortedArray[] = $multiArray[$firstIndex];
					else $sortedArray[$firstIndex] = $multiArray[$firstIndex];
			return array_reverse($sortedArray);
		} else {
			return $multiArray;
		}
	}
	*/

	public function execSearchCommand($param) {
		$param = $param["search"];
		
		/* if date range has been fiddled with */
		if ($param["start_year"]) {
			$start_stamp = mktime(0, 0, 0, $param["start_month"], $param["start_day"], $param["start_year"]);
		}
		if ($param["end_year"]) {
			// If we only got a year, make sure it's the last day of the year.
			$param["end_day"] = ($param["end_day"]) ? $param["end_day"] : 31;
			$param["end_month"] = ($param["end_month"]) ? $param["end_month"] : 12;
			$end_stamp = mktime(23, 59, 59, $param["end_month"], $param["end_day"], $param["end_year"]);
		}
		
		/* sanitize address_id */
		if ($param["address_id"]) {
			$address_id = explode(",", $param["address_id"]);
			foreach ($address_id as $k=>$v) {
				if (!$v)
					unset($address_id[$k]);
			}
			$param["address_id"] = implode(",", $address_id);
		}

		/* local temp results */
		$data = array();

		/* exec calendar query */
		if ($param["calendar"]) {
			$calendar_data = new Calendar_data();
			$opts = array(
				"all"       => 1,
				"searchkey" => $param["phrase"],
				"max_hits"  => $this->max_hits
			);
			if ($start_stamp) {
				$opts["date"]["start"] = $start_stamp;
				if ($end_stamp) {
					$opts["date"]["end"] = $end_stamp;
				} else {
					$opts["date"]["end"] = time();
				}
			}
			if ($param["address_id"])
				$opts["address_id"] = $param["address_id"];
			if ($param["private"]) {
				$opts["user_id"] = $_SESSION["user_id"];
				$data["calendar"] = $calendar_data->getAppointmentsBySearch($opts);
			} else {
				$data["calendar"] = $calendar_data->getAppointmentsBySearch($opts);
			}
		}

		/* address book */
		if ($param["address"]) {
			$address_data = new Address_data();
			if (!$param["address_id"])
				$data["address"]["private"] = $address_data->getRelationsList( array("addresstype"=>"private", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );
			if (!$param["private"]) {
				$data["address"]["other"]   = $address_data->getRelationsList( array("addresstype"=>"other", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );
				$data["address"]["bcards"]  = $address_data->getRelationsList( array("addresstype"=>"bcards", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );
				$data["address"]["users"]   = $address_data->getRelationsList( array("addresstype"=>"users", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );
				$data["address"]["address"] = $address_data->getRelationsList( array("addresstype"=>"relations", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );

			}
		}

		/* notes */
		if ($param["notes"]) {
			$note_data = new Note_data();
			$opts = array(
				"searchkey" => $param["phrase"],
				"private"   => $param["private"],
			);
			if ($param["address_id"])
				$opts["address_id"] = $param["address_id"];
			if ($start_stamp) {
				$opts["date"]["start"] = $start_stamp;
				if ($end_stamp) {
					$opts["date"]["end"] = $end_stamp;
				} else {
					$opts["date"]["end"] = time();
				}
			}
			$data["notes"] = $note_data->searchAll($opts);
			unset($opts);
		}

		/* email */
		if ($param["email"]) {
			/* use beagle for archive search */
			if (!$param["private"]) {
				$data["email"]["archive"]["data"] = $this->beagleSearch($param["phrase"], "maildata", $param["address_id"]);
				$data["email"]["archive"]["count"] = count($data["email"]["archive"]["data"]);
			}
				$data["email"]["private"]["data"] = $this->beagleSearch($param["phrase"], "maildata_private", $param["address_id"]);
				$data["email"]["private"]["count"] = count($data["email"]["private"]["data"]);

			/*
			$email_data = new Email_data();
			$opts = array(
				"search"       => $param["phrase"],
				"global_index" => 1,
				"user_id"      => $_SESSION["user_id"],
				"nolimit"      => 1,
				"skip_extends" => 1,
				"show_folders" => 1,
				"max_hits"     => $this->max_hits
			);
			if ($param["address_id"])
				$opts["relation"] = $param["address_id"];

			$data["email"]["private"] = $email_data->getEmailBySearch($opts);
			*/
		}

		if (!$param["private"]) {
			/* filesys */
			if ($param["filesys"]) {
				$opts = array(
					"phrase"   => $param["phrase"],
					"max_hits" => $this->max_hits
				);
				if ($param["address_id"])
					$opts["address_id"] = $param["address_id"];
				$fsdata = new Filesys_data();
				$data["filesys"] = $fsdata->searchAll($opts);
			}

			/* binfiles */
			if ($param["binfile"]) {
				$data["binfiles"] = $this->beagleSearch($param["phrase"], "files", $param["address_id"], $param["websearch"]);
			}

			/* binemail */
			if ($param["binemail"]) {
				$data["binemail"] = $this->beagleSearch($param["phrase"], "email", $param["address_id"]);
			}
		}
		
		/* support */
		if ($param["support"]) {
			$support_data = new Support_data();
			$options["search"] = $param["phrase"];
			$options["active"] = 1;
			if ($start_stamp) {
				$options["date"]["start"] = $start_stamp;
				if ($end_stamp) {
					$options["date"]["end"] = $end_stamp;
				} else {
					$options["date"]["end"] = time();
				}
			}
			if ($param["address_id"]) {
				$options["address_id"] = $param["address_id"];
			}
			$data["support"] = $support_data->getSupportItems($options);
		}
		
		/* projects */
		if ($param["projects"]) {
			$project_data = new Project_data();
			$options["searchkey"] = $param["phrase"];
			if ($param["address_id"]) {
				$options["address_id"] = $param["address_id"];
			}
			$data["projects"] = $project_data->getProjectsBySearch($options);
		}
		
		/* sales */
		if ($param["sales"]) {
			$sales_data = new Sales_data();
			$options["text"] = $param["phrase"];
			if ($param["address_id"]) {
				$options["address_id"] = $param["address_id"];
			}
			$sales_search = $sales_data->getSalesBySearch($options);
			$data["sales"] = $sales_search["data"];
		}
		
		return $data;
	}

}
?>
