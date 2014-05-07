<?php

Class ProjectExt_data Extends Project_data {
	/* constants */
	const include_dir      = "classes/project/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name       = "projectext";

	public $field_type = Array();

	public $share = "projects";
	public $share_path = "";
	private $extension = "odt";

	/* {{{ function __construct() */
	public function __construct() {
		/* declare default field types */
		$this->field_type = Array(
			0 => gettext("normaal text veld"),
			1 => gettext("groot text veld"),
			2 => gettext("datum veld"),
			3 => gettext("selectbox"),
			4 => gettext("checkbox"),
			5 => gettext("selectbox met tabel")
		);
		$this->share_path = $GLOBALS["covide"]->filesyspath."/".$this->share."/";
		$this->checkFolder("templates");
	}
	/* }}} */
	/* {{{ function checkFolderFS() */
	/**
	* Check if the folder on the file system does exist
	*
	* @param string Name of the folder
	*/
	public function checkFolderFS($foldername) {
		if (!file_exists($foldername)) {
			mkdir($foldername, 0777, 1);
		}
	}
	/* }}} */
	/* {{{ function checkFolder() */
	/**
	 * Check if a folder does exist
	 *
	 * @param string Name of the folder
	 */
	public function checkFolder($name) {

		$dir = $this->share_path.$name;
		$this->checkFolderFS($dir);
	}
	/* }}} */
	/* {{{ function extGetDepartments () */
	public function extGetDepartments($id=0) {
		$address_data = new Address_data();

		if (!$id) {
			$q = "select * from projects_ext_departments order by department";
		} else {
			$q = sprintf("select * from projects_ext_departments where id = %d", $id);
		}
		$data = array();
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {

			$this->checkFolder($row["department"]."/projecten");

			$row["address_name"] = $address_data->getAddressNameById($row["address_id"]);
			$data[$row["id"]] = $row;
		}
		return $data;
	}
	/* }}} */
	/* {{{ function extSaveDepartment() */
	public function extSaveDepartment() {
		$data = $_REQUEST["data"];

		if ($_REQUEST["id"]) {
			$q = sprintf("update projects_ext_departments set department = '%s', description = '%s', address_id = %d where id = %d", $data["department"], $data["description"], $data["address_id"], $_REQUEST["id"]);
		} else {
			$q = sprintf("insert into projects_ext_departments (department, description, address_id) values ('%s', '%s', %d)", $data["department"], $data["description"], $data["address_id"]);
		}
		sql_query($q);

		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("opener.location.href='index.php?mod=projectext&action=extend';");
		$output->addCode("setTimeout('window.close()', 1000);");
		$output->end_javascript();
		$output->exit_buffer();
	}
	/* }}} */
	/* {{{ function extDeleteDepartment() */
	public function extDeleteDepartment() {
			$q = sprintf("delete from projects_ext_departments where id = %d", $_REQUEST["id"]);
			sql_query($q);
	}
	public function extDeleteActivity() {
			$q = sprintf("delete from projects_ext_activities where id = %d", $_REQUEST["id"]);
			sql_query($q);
	}

	/* }}} */
	/* {{{ function extProjectActivityCleanup() */
	public function extProjectActivityCleanup() {
			$q = sprintf("delete from projects_ext_departments where id = %d", $_REQUEST["id"]);
			sql_query($q);
	}
	/* }}} */
	/* {{{ function extGetMetaFields() */
	public function extGetMetaFields($id=0, $project_id=0, $activity=0) {
		if (!$id) {
			$q = sprintf("select * from projects_ext_metafields where activity = %d order by field_order", $activity);
		} else {
			$q = sprintf("select * from projects_ext_metafields where id = %d", $id);
		}
		$data = array();
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["h_field_type"] = $this->field_type[$row["field_type"]];
			if ($project_id) {
				$q = sprintf("select meta_value from projects_ext_metavalues where meta_id = %d and project_id = %d", $row["id"], $project_id);
				$res2 = sql_query($q);
				if (sql_num_rows($res2)>0) {
					$row["value"] = sql_result($res2,0);
				}
			}
			$data[$row["id"]] = $row;
		}
		return $data;
	}
	/* {{{ function extGetMetaFields() */
	public function defineMetaFieldsDelete() {
		$id = $_REQUEST["id"];
		$q = sprintf("delete from projects_ext_metavalues where meta_id = %d", $id);
		sql_query($q);

		$q = sprintf("delete from projects_ext_metafields where id = %d", $id);
		sql_query($q);
	}
	/* }}} */

	/* }}} */
	/* {{{ function extGetProjectActivities() */
	public function extGetProjectActivities($project_id) {
		$data = array();
		$q = sprintf("select activity from projects_ext_metafields where id IN
			(select meta_id from projects_ext_metavalues where project_id = %d)
			and activity > 0 group by activity", $project_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[] = $row["activity"];
		}
		return $data;
	}

	/* }}} */
	/* {{{ function extSaveMetaField() */
	public function extSaveMetaField() {
		$data = $_REQUEST["data"];
		if ($_FILES["binFile"]["error"][0] == 0 && file_exists($_FILES["binFile"]["tmp_name"][0])) {
			$filename = $_FILES["binFile"]["tmp_name"][0];
			//$filename = "/tmp/Hypothese.csv"; #debug
			$handle = fopen($filename, "r");
			$bin = fread($handle, filesize($filename));
			fclose($handle);
			@unlink($filename);

			$conversion = new Layout_conversion();
			$bin = $conversion->utf8_convert($bin);

			/* replace all data vars by pointers */
			preg_match_all("/\"[^\"]*?\"/si", $bin, $matches);
			$matches = $matches[0];
			$matches = array_unique($matches);
			foreach ($matches as $k=>$v) {
				$bin = str_replace($v, "##$k", $bin);
				$matches[$k] = substr($v, 1, strlen($v)-2);
				$matches[$k] = str_replace(",",".",$matches[$k]);
			}

			/* convert semicolon to comma */
			if ($data["seperator"] == "semicolon") {
				$bin = str_replace(";", ",", $bin);
			}

			$stream = "";
			$bin = explode("\n", $bin);
			foreach ($bin as $record) {
				$record = explode(",", $record);
				foreach ($record as $k=>$v) {
					if (preg_match("/^##\d{1,}$/si",$v)) {
						$val = number_format( preg_replace("/^##/si","",$v) );
						$val = $matches[$val];
					} else {
						$val = $v;
					}
					$stream.= $val."|";
				}
				$stream = preg_replace("/\|$/s", "#", $stream);
			}
			$stream = preg_replace("/#{1,}/s", "#", $stream);
			$stream = preg_replace("/(^#)|(#$)/s", "", $stream);
			$stream = addslashes($stream);
		}
		if ($data["field_type"]==5) {
			$data["default_value"] = (int)$data["default_col"];
			if (!$data["default_value"]) $data["default_value"] = 1;
		} else {
			unset($stream);
		}
		if ($data["field_type"]!=3 && $data["field_type"]!=4) {
			$data["default_value"] = "";
		}

		if ($_REQUEST["id"]) {
			$q = sprintf("update projects_ext_metafields set field_order = %d, field_name = '%s', field_type = %d, show_list = %d, default_value = '%s', large_data = '%s' where id = %d", $data["field_order"], $data["field_name"], $data["field_type"], $data["show_list"], $data["default_value"], $stream, $_REQUEST["id"]);
		} else {
			$q = sprintf("insert into projects_ext_metafields (activity, field_order, field_name, field_type, show_list, default_value, large_data) values (%d, %d, '%s', %d, %d, '%s', '%s')", $_REQUEST["activity_id"], $data["field_order"], $data["field_name"], $data["field_type"], $data["show_list"], $data["default_value"], $stream);
		}
		sql_query($q);

		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("opener.location.href='index.php?mod=projectext&action=".(($_REQUEST["activity_id"]) ? "extOpenActivity":"defineMetaFields")."&department_id=".$_REQUEST["department_id"]."&activity_id=".$_REQUEST["activity_id"]."';");
		$output->addCode("setTimeout('window.close()', 1000);");
		$output->end_javascript();
		$output->exit_buffer();
	}
	/* }}} */
	/* {{{ function extSaveMetaFieldValues() */
	public function extSaveMetaFieldValues($meta, $project_id, $activity_id="", $activity_meta="") {

		/* default and activity fields */
		$fields = $this->extGetMetaFields();

		foreach ($fields as $k=>$v) {
			if ($v["field_type"]==2) {
				$val = mktime(0,0,0,$meta[$v["field_name"]."_day"], $meta[$v["field_name"]."_month"], $meta[$v["field_name"]."_year"]);
			} elseif ($v["field_type"]==4) {
				$val = @implode("\n", $meta[$v["field_name"]]);
			} else {
				$val = $meta[$v["field_name"]];
			}

			$q = sprintf("select count(*) from projects_ext_metavalues where project_id = %d and meta_id = %d", $project_id, $v["id"]);
			$res = sql_query($q);
			if (sql_result($res,0)>0) {
				$q = sprintf("update projects_ext_metavalues set meta_value = '%s' where meta_id = %d and project_id = %d", $val, $v["id"], $project_id);
				sql_query($q);
			} else {
				$q = sprintf("insert into projects_ext_metavalues (meta_id, project_id, meta_value) values (%d, %d, '%s')", $v["id"], $project_id, $val);
				sql_query($q);
			}
		}
		if ($activity_id) {
			$meta = $activity_meta;
			$fields = $this->extGetMetaFields("", "", $activity_id);
			foreach ($fields as $k=>$v) {

				if ($v["field_type"]==2) {
					$val = mktime(0,0,0,$meta[$v["field_name"]."_day"], $meta[$v["field_name"]."_month"], $meta[$v["field_name"]."_year"]);
				} elseif ($v["field_type"]==4) {
					$val = @implode("\n", $meta[$v["field_name"]]);
				} else {
					$val = $meta[$v["field_name"]];
				}
				$q = sprintf("select count(*) from projects_ext_metavalues where project_id = %d and meta_id = %d", $project_id, $v["id"]);
				$res = sql_query($q);
				if (sql_result($res,0)>0) {
					$q = sprintf("update projects_ext_metavalues set meta_value = '%s' where meta_id = %d and project_id = %d", $val, $v["id"], $project_id);
					sql_query($q);
				} else {
					$q = sprintf("insert into projects_ext_metavalues (meta_id, project_id, meta_value) values (%d, %d, '%s')", $v["id"], $project_id, $val);
					sql_query($q);
				}
			}
		}
		/* extra dynamic fields */
		if ($activity_id) {
			$q = sprintf("select count(*) from projects_ext_extrainfo where project_id = ".$project_id);
			$res = sql_query($q);
			if (sql_result($res,0)>0) {
				$q = sprintf("update projects_ext_extrainfo set activity_id = %d where project_id = %d", $activity_id, $project_id);
				sql_query($q);
			} else {
				$q = sprintf("insert into projects_ext_extrainfo (activity_id, project_id) values (%d, %d)", $activity_id, $project_id);
				sql_query($q);
			}
		} else {
			$q = sprintf("delete from projects_ext_extrainfo where project_id = %d", $project_id);
			sql_query($q);
		}
		/* delete empty meta fields */
		$q = sprintf("delete from projects_ext_metavalues where project_id = %d and (meta_value = '' or meta_value is null)", $project_id);
		sql_query($q);

	}
	/* }}} */
	/* {{{ function extGetProjectActivityType() */
	public function extGetProjectActivityType($project_id) {
		if ($project_id) {
			$q = sprintf("select activity_id from projects_ext_extrainfo where project_id = %d", $project_id);
			$res = sql_query($q);
			return sql_result($res,0);
		}
	}
	/* }}} */
	/* {{{ function extGetActivities() */
	public function extGetActivities($department, $id=0) {

		if (!$id) {
			$q = sprintf("select * from projects_ext_activities where department_id = %d order by activity", $department);
		} else {
			$q = sprintf("select * from projects_ext_activities where id = %d", $id);
		}
		$data = array();
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$tmp = $this->extGetDepartments($row["department_id"]);
			$row["department_name"] = $tmp[$row["department_id"]]["department"];
			$data[$row["id"]] = $row;

			$this->checkFolder($row["department_name"]."/activiteiten/".$row["activity"]);
		}
		return $data;
	}
	/* }}} */
	/* {{{ function extActivitySave() */
	public function extActivitySave() {
		$data = $_REQUEST["data"];

		if ($_REQUEST["id"]) {
			$q = sprintf("update projects_ext_activities set description = '%s', activity = '%s' where id = %d", $data["description"], $data["activity"], $_REQUEST["id"]);
		} else {
			$q = sprintf("insert into projects_ext_activities (department_id, description, activity) values (%d, '%s', '%s')", $data["department_id"], $data["description"], $data["activity"]);
		}
		sql_query($q);

		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("opener.location.href='index.php?mod=projectext&action=extShowActivities&department_id=".$data["department_id"]."';");
		$output->addCode("setTimeout('window.close()', 1000);");
		$output->end_javascript();
		$output->exit_buffer();
	}
	/* }}} */
	/* function extGetFileTemplates() {{{*/
	public function extGetFileTemplates($project_id, $activity_id) {
		/* list default templates */
		$dir = $this->share_path."templates";
		$cmd = sprintf("ls %s/*.%s", $dir, $this->extension);
		exec($cmd, $ret, $retval);

		$files["project"] = array();
		foreach ($ret as $k=>$v) {
			$s = str_replace($dir."/", "", $v);
			$row = array(
				"short_name" => $s,
				"link_name"  => urlencode($s)
			);
			$files["project"][] = $row;
		}
		return $files;
	}
	/*}}}*/
	/* {{{ function applyMetaSearch() */
	public function applyMetaSearch(&$projects) {
		$meta_field = $_REQUEST["projectext"]["meta_field"];

		$start["d"] = $_REQUEST["projectext"]["start_day"];
		$start["m"] = $_REQUEST["projectext"]["start_month"];
		$start["Y"] = $_REQUEST["projectext"]["start_year"];

		$end["d"]   = $_REQUEST["projectext"]["end_day"];
		$end["m"]   = $_REQUEST["projectext"]["end_month"];
		$end["Y"]   = $_REQUEST["projectext"]["end_year"];

		if ($start["d"] && $start["m"] && $start["Y"]) {
			$start["ts"] = mktime(0,0,0,$start["m"],$start["d"],$start["Y"]);
		}
		if ($end["d"] && $end["m"] && $end["Y"]) {
			$end["ts"] = mktime(0,0,0,$end["m"],$end["d"],$end["Y"]);
		}
		if ($start["ts"] && $end["ts"] && $meta_field) {
			foreach ($projects as $k=>$v) {
				$w = $this->extGetMetaFields(0, $v["id"]);
				foreach ($w as $z) {
					if ($z["id"]==$meta_field) {
						if ($z["value"] < $start["ts"] || $z["value"] > $end["ts"]) {
							unset($projects[$k]);
						}
					}
				}
			}
		}
	}
	/*}}}*/

	public function getMetaTableData($metaid) {
		$q = sprintf("select * from projects_ext_metafields where id = %d", $metaid);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$conversion = new Layout_conversion();

		$data = $conversion->utf8_convert($row["large_data"]);
		$data = explode("#", $data);
		foreach ($data as $k=>$v) {
			$data[$k] = explode("|", $v);
		}
		return $data;
	}

	/* {{{ function extMergeTemplate() */
	public function extMergeTemplate() {
		require("classes/tbsooo/tbs_class.php");
		require("classes/tbsooo/tbsooo_class.php");

		$ooo = new clsTinyButStrongOOo();

		/* set some parameters for OpenOffice.org conversion */
		$ooo->SetZipBinary("/usr/bin/zip");
		$ooo->SetUnzipBinary("/usr/bin/unzip");
		$ooo->SetProcessDir($GLOBALS["covide"]->temppath);

 		$address_data = new Address_data();
 		$project_data = new Project_data();

 		$project_name = $project_data->getProjectNameById($_REQUEST["project_id"]);

 		$user_data = new User_data();
 		$user = $user_data->getUserdetailsById($_SESSION["user_id"]);
 		$user_address = $address_data->getAddressByID($user["address_id"], "users");

		$sender = $address_data->getAddressByID($_REQUEST["sender_address"]);
		$rcpt   = $address_data->getAddressByID($_REQUEST["rcpt_address"]);

		/* sender variables */
		global $sender_givenname, $sender_infix, $sender_surname;
		global $sender_email, $sender_email_alt, $sender_companyname, $sender_name;
		global $sender_street, $sender_address, $sender_zipcode, $sender_city;
		global $sender_phone, $sender_fax, $sender_website;

		$sender_givenname = $user_address["givenname"];
		$sender_infix     = $user_address["infix"];
		$sender_surname   = $user_address["surname"];
		$sender_email     = $user["mail_email"];
		$sender_email_alt = $user["mail_email1"];

		$sender_companyname = $sender["companyname"];
		$sender_name      = preg_replace("/ {1,}/s", " ", $sender_givenname." ".$sender_infix." ".$sender_surname);

		$sender_street    = $sender["address"];
		$sender_address   = $sender["address"];
		$sender_zipcode   = $sender["zipcode"];
		$sender_city      = $sender["city"];
		$sender_phone     = $sender["phone_nr"];
		$sender_fax       = $sender["fax_nr"];
		$sender_website   = preg_replace("/http:\/\//s", "", $sender["website"]);

		/* rcpt variables */
		global $rcpt_companyname, $rcpt_street, $rcpt_address, $rcpt_zipcode, $rcpt_city;
		global $rcpt_phone, $rcpt_fax, $rcpt_website, $rcpt_tav, $rcpt_contact, $rcpt_project;

		$rcpt_companyname = $rcpt["companyname"];
		$rcpt_street      = $rcpt["address"];
		$rcpt_address     = $rcpt["address"];
		$rcpt_zipcode     = $rcpt["zipcode"];
		$rcpt_city        = $rcpt["city"];
		$rcpt_phone       = $rcpt["phone_nr"];
		$rcpt_fax         = $rcpt["fax_nr"];
		$rcpt_website     = preg_replace("/http:\/\//s", "", $rcpt["website"]);
		$rcpt_tav         = $rcpt["tav"];
		$rcpt_contact     = $rcpt["contact_person"];
		$rcpt_project     = $project_name;

		/* date variables */
		global $datetime, $datetime_full, $datetime_short;

		$datetime       = strftime("%a %d %b %Y");
		$datetime_full  = strftime("%a %d %b %Y");
		$datetime_short = date("d-m-Y");

		$project_id = $_REQUEST["project_id"];
		$data = $this->extGetMetaFields("", $project_id);
		foreach ($data as $k=>$v) {
			if ($v["type"] == 2) {
				$v["value"] = date("d-m-Y", $v["value"]);
			}

			eval(sprintf("global \$field_%d; \$field_%d = '%s'; ", $v["id"], $v["id"], addslashes($v["value"])));
		}
		if ($_REQUEST["file_type"] == "project") {
			$dir = $this->share_path."templates/".$_REQUEST["file_name"];
			$ooo->NewDocFromTpl($dir);
		}

		$ooo->LoadXmlFromDoc('content.xml');
		$ooo->SaveXmlToDoc();

		header("Content-Transfer-Encoding: binary");
		header("Content-type: ".$ooo->GetMimetypeDoc());

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename="'.$_REQUEST["file_name"].'"'); //msie 5.5 header bug
		} else {
			header('Content-Disposition: attachment; filename="'.$_REQUEST["file_name"].'"');
		}

		//header('Content-Length: '.filesize($ooo->GetPathnameDoc()));
		$ooo->FlushDoc();
		$ooo->RemoveDoc();
		exit();
	}
	/* }}} */
	/* {{{ function openFileSys() */
	/* this function will break all code folding and highlighting, so i dropped it down here :\ */
	public function openFileSys($folder) {
		$uri = sprintf("\\\\%s\\%s\\%s", $_SERVER["SERVER_ADDR"], $this->share, str_replace("/", "\\", $folder));
		return $uri;
	}
	/* }}} */
}
?>
