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

	private $data = array();
	private $beagle_home = "/var/covide_files";

	private $max_hits = 2000;

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
	public function beagleSearch($str, $type) {

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

			if ($type=="maildata") {
				$xs = $data->checkFilePermissions("", $file);
			} else {
				$xs = $data->checkFilePermissions($file);
			}
			if ($xs) $results[$file]=$xs;
		}
		return $results;

	}
	/* methods */
	public function execSearch($param) {


		/* set time limti to 10 * 60 sec */
		set_time_limit(120);

		session_write_close();

		$phrase = explode(" ",$param["search"]["phrase"]);
		$and     = $param["search"]["and"];

		foreach ($phrase as $k=>$v) {
			if (strlen($v)>=3) {
				$param["search"]["phrase"] = $v;
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

		/* local temp results */
		$data = array();

		/* exec calendar query */
		if ($param["calendar"]) {
			$calendar_data = new Calendar_data();
			$opts = array(
				"history"   => 1,
				"searchkey" => $param["phrase"],
				"max_hits"  => $this->max_hits
			);
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
			$data["address"]["private"] = $address_data->getRelationsList( array("addresstype"=>"private", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );
			if (!$param["private"]) {
				$data["address"]["other"]   = $address_data->getRelationsList( array("addresstype"=>"other", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );
				$data["address"]["bcards"]  = $address_data->getRelationsList( array("addresstype"=>"bcards", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );
				$data["address"]["users"]   = $address_data->getRelationsList( array("addresstype"=>"users", "search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );
				$data["address"]["address"] = $address_data->getRelationsList( array("search"=>$param["phrase"], "nolimit"=>1, "max_hits"=>$this->max_hits) );

			}
		}

		if ($param["notes"]) {
			$note_data = new Note_data();
			$data["notes"] = $note_data->searchAll($param["phrase"], $param["private"]);
		}

		/* email */
		if ($param["email"]) {
			/* use beagle for archive search */
			if (!$param["private"]) {
				$data["email"]["archive"]["data"] = $this->beagleSearch($param["phrase"], "maildata");
				$data["email"]["archive"]["count"] = count($data["email"]["archive"]["data"]);
			}

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
			$data["email"]["private"] = $email_data->getEmailBySearch($opts);
		}

		if (!$param["private"]) {
			/* filesys */
			if ($param["filesys"]) {
				$fsdata = new Filesys_data();
				$data["filesys"] = $fsdata->searchAll($param["phrase"], $this->max_hits);
			}

			/* binfiles */
			if ($param["binfile"]) {
				$data["binfiles"] = $this->beagleSearch($param["phrase"], "files");
			}

			/* binemail */
			if ($param["binemail"]) {
				$data["binemail"] = $this->beagleSearch($param["phrase"], "email");
			}
		}

		return $data;
	}

}
?>
