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

	/* methods */
	/* beagle daemon */
	public function beagleDaemon() {

		session_write_close();

		/* check if daemon is running */
		$cmd = "pidof beagled";
		exec($cmd, $ret, $retval);

		/* if daemon is not running, start it */
		if (!preg_match("/\d{1,}/s", $ret[0])) {

			$cmd = "export BEAGLE_HOME=".$this->beagle_home." && beagled && true";
			$handle = popen($cmd, "r");
			pclose($handle);

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
	public function beagleSearch($str, $type, $address_id=0) {

		$this->beagleDaemon();

		$str = escapeshellarg($str);
		$str = escapeshellcmd($str);
		$str = preg_replace("/[^a-z0-9\-_ ]/si", "", $str);

		$results = array();

		/* get Covide base path and identifier */
		$fspath = "file://".$GLOBALS["covide"]->filesyspath."/";
		if ($type == "files") {
			$fspath.= "bestanden/";
			$data = new Filesys_data();
		} elseif ($type == "maildata") {
			$fspath.= "maildata/";
			$data = new Email_data();
		} else {
			$fspath.= "email/";
			$data = new Email_data();
		}
		$max_fs_hits = pow(2,24);
		$cmd = sprintf("export BEAGLE_HOME=".$this->beagle_home." && beagle-query --max-hits %d '%s' | grep '%s'", $max_fs_hits, $str, $fspath);
		exec($cmd, $ret, $retval);

		foreach ($ret as $i=>$file) {
			$file = basename($file);
			$file = (int)preg_replace("/\..*$/s", "", $file);

			if ($type=="maildata")
				$xs = $data->checkFilePermissions("", $file, $address_id);
			else
				$xs = $data->checkFilePermissions($file, $address_id);

			if ($xs) $results[$file]=$xs;
		}
		return $results;

	}
	public function execSearch($param) {

		/* set time limti to 10 * 60 sec */
		set_time_limit(120);

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

		}

		/* calendar */
		$data["calendar"] = $this->aSortBySecondIndex($data["calendar"], "timestamp_start");

		/* address */
		$data["address"]["address"]["address"] = $this->aSortBySecondIndex($data["address"]["address"]["address"], "companyname");
		$data["address"]["bcards"]["address"]  = $this->aSortBySecondIndex($data["address"]["bcards"]["address"], "fullname");
		$data["address"]["users"]["address"]   = $this->aSortBySecondIndex($data["address"]["users"]["address"], "fullname");

		/* notes */
		$data["notes"] = $this->aSortBySecondIndex($data["notes"], "timestamp");

		return $data;
	}

	public function search_merge(&$main, &$new, $and) {
		$diff = array_diff_key($main, $new);
		foreach ($diff as $k=>$v) {
			if ($and) {
				/* unset */
				unset($main[$k]);
			} else {
				/* add */
				$main[$k]=$v;
			}
		}
	}

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

	public function execSearchCommand($param) {

		$param = $param["search"];
		
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
				$data["address"]["other"]   = $address_data->getRelationsList( array("addresstype"=>"other", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits, "address_id" => $param["address_id"]) );
				$data["address"]["bcards"]  = $address_data->getRelationsList( array("addresstype"=>"bcards", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits, "address_id" => $param["address_id"]) );
				$data["address"]["users"]   = $address_data->getRelationsList( array("addresstype"=>"users", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits, "address_id" => $param["address_id"]) );
				$data["address"]["address"] = $address_data->getRelationsList( array("search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits, "address_id" => $param["address_id"]) );

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
				
			$data["notes"] = $note_data->searchAll($opts);
			unset($opts);
		}

		/* email */
		if ($param["email"]) {
			/* use beagle for archive search */
			if (!$param["private"]) {
				$data["email"]["archive"]["data"] = $this->beagleSearch($param["phrase"], "maildata", $param["address_id"]);
				$data["email"]["archive"]["count"] = count($data["email"]["archive"]["data"]);
			} else {

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
					$opts["address_id"] = $param["address_id"];
				$data["email"]["private"] = $email_data->getEmailBySearch($opts);
			}
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
				$data["binfiles"] = $this->beagleSearch($param["phrase"], "files", $param["address_id"]);
			}

			/* binemail */
			if ($param["binemail"]) {
				$data["binemail"] = $this->beagleSearch($param["phrase"], "email", $param["address_id"]);
			}
		}

		return $data;
	}

}
?>
