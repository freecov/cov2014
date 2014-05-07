<?php
/**
 * Covide ProjectExt module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

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
			0 => gettext("normal text field"),
			1 => gettext("large text field"),
			2 => gettext("date field"),
			3 => gettext("selectbox"),
			4 => gettext("checkbox"),
			5 => gettext("selectbox with table"),
			6 => gettext("field with users"),
			7 => gettext("finance field")
		);
		$this->share_path = $GLOBALS["covide"]->filesyspath."/".$this->share."/";
		//$this->checkFolder("templates");
	}
	/* }}} */
	/* {{{ function checkFolderFS() */
	/**
	* Check if the folder on the file system does exist
	*
	* @param string Name of the folder
	*/
	public function checkFolderFS($foldername) {
		if ($GLOBALS["covide"]->license["has_project_ext_samba"]) {
			if (!file_exists($foldername)) {
				mkdir($foldername, 0777, 1);
			}
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
		$dir = $this->share_path . preg_replace("/\.{1,}($|\/)/s", "$1", $name);
		//echo $dir."\n";
		$this->checkFolderFS($dir);
	}
	/* }}} */
	/* {{{ function checkCompleteFolderStruct () */
	public function checkCompleteFolderStruct() {
		return;

		if ($GLOBALS["covide"]->license["has_project_ext_samba"]) {
			/*
			 * we want the following format:
			 *  /projects/templates
			 *  /projects/structure
			 *  /<department>/templates
			 *  /<department>/projects/<year>/<project nr>/<sub struct (if any)>
			 */

			/* basic folders */
			$this->checkFolder("templates");
			$this->checkFolder("structure");

			/* for each department */
			$departments = $this->extGetDepartments();
			foreach ($departments as $v) {
				$this->checkFolder($v["department"]);
				$this->checkFolder($v["department"]."/projects");
				$this->checkFolder($v["department"]."/templates");

				/* get all activities (for templates) */
				$activities = $this->extGetActivities($v["id"]);
				foreach ($activities as $a) {
					$this->checkFolder($v["department"]."/templates/".$a["activity"]);
				}

				/* get all main projects according to this department */
				$q = sprintf("select id from projects_master where ext_department = %d", $v["id"]);
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res)) {
					/* now get all sub projects for this master project */
					$q = sprintf("select name, project_year from project left join projects_ext_extrainfo on project.id = projects_ext_extrainfo.project_id where group_id = %d", $row["id"]);
					$res2 = sql_query($q);
					while ($row2 = sql_fetch_assoc($res2)) {
						if ($row2["project_year"]) {
							$this->checkFolder(sprintf("%s/projects/%d",
								$v["department"], $row2["project_year"]));
							$this->checkFolder(sprintf("%s/projects/%d/%s",
								$v["department"], $row2["project_year"], $row2["name"]));
						}
					}
				}
			}
		}
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

			//$this->checkFolder($row["department"]."/projecten");

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
			$q = sprintf("update projects_ext_departments set department = '%s', description = '%s', address_id = %d, users = '%s' where id = %d", $data["department"], $data["description"], $data["address_id"], $data["users"], $_REQUEST["id"]);
		} else {
			$q = sprintf("insert into projects_ext_departments (department, description, address_id, users) values ('%s', '%s', %d, '%s')", $data["department"], $data["description"], $data["address_id"], $data["users"]);
		}
		sql_query($q);

		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("opener.location.href=opener.location.href;\n");
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
	public function extGetMetaFields($id=0, $project_id=0, $activity=0, $only_listitems=0) {
		if (!$id) {
			if ($only_listitems) {
				$q = sprintf("select id, field_name, field_type, field_order, activity, show_list from projects_ext_metafields where show_list = 1 and activity = %d order by field_order", $activity);
			} else {
				$q = sprintf("select * from projects_ext_metafields where activity = %d order by field_order", $activity);
			}
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
				if (sql_num_rows($res2) > 0) {
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
			$bin = str_replace("\r", "", $bin); //windows line endings are sometimes \r\r\n
			fclose($handle);
			@unlink($filename);

			$conversion = new Layout_conversion();
			#$bin = $conversion->utf8_convert($bin);

			/* replace all data vars by pointers */
			preg_match_all("/\"[^\"]*?\"/si", $bin, $matches);
			$matches = $matches[0];
			$matches = array_unique($matches);
			foreach ($matches as $k=>$v) {
				$bin = str_replace($v, "##$k", $bin);
				$matches[$k] = substr($v, 1, strlen($v)-2);
				#$matches[$k] = str_replace(",",".",$matches[$k]);
			}

			/* convert semicolon to comma */
			/*
			if ($data["seperator"] == "semicolon") {
				$bin = str_replace(";", ",", $bin);
			}
			*/

			$stream = "";
			$bin = explode("\n", $bin);
			foreach ($bin as $record) {
				if ($data["seperator"] == "semicolon") {
					$record = explode(";", $record);
				} else {
					$record = explode(",", $record);
				}
				foreach ($record as $k=>$v) {
					if (preg_match("/^##\d{1,}$/si",$v)) {
						$val = number_format( preg_replace("/^##/si","",$v) );
						$val = $matches[$val];
					} else {
						$val = $v;
					}
					$val = preg_replace("/#|\|/s", "", $val);
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
		if ($data["field_type"]!=3 && $data["field_type"]!=4 && $data["field_type"]!=5) {
			$data["default_value"] = "";
		}

		if ($_REQUEST["id"]) {
			$q = sprintf("update projects_ext_metafields set field_order = %d, field_name = '%s', field_type = %d, show_list = %d, default_value = '%s', large_data = '%s' where id = %d", $data["field_order"], $data["field_name"], $data["field_type"], $data["show_list"], $data["default_value"], $stream, $_REQUEST["id"]);
		} else {
			$q = sprintf("insert into projects_ext_metafields (activity, field_order, field_name, field_type, show_list, default_value, large_data) values (%d, %d, '%s', %d, %d, '%s', '%s')", $_REQUEST["activity_id"], $data["field_order"], $data["field_name"], $data["field_type"], $data["show_list"], $data["default_value"], $stream);
		}
		//echo $q;
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
	public function extSaveMetaFieldValues($meta, $project_id, $activity_id="", $activity_meta="", $project_year="") {

		/* default and activity fields */
		$fields = $this->extGetMetaFields();

		foreach ($fields as $k=>$v) {
			if ($v["field_type"]==2) {
				if (!$meta[$v["field_name"]."_year"]) {
					$val = 0;
				} else {
					$val = mktime(0,0,0,$meta[$v["field_name"]."_month"], $meta[$v["field_name"]."_day"], $meta[$v["field_name"]."_year"]);
				}
			} elseif ($v["field_type"]==4) {
				$val = @implode("\n", $meta[$v["field_name"]]);
			} elseif ($v["field_type"] == 7) {
				if (substr($meta[$v["field_name"]], -3, 1) == ",") {
					$val = str_replace(",", ".", str_replace(".", "", $meta[$v["field_name"]]));
				} else {
					$val = $meta[$v["field_name"]];
				}
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
					if (!$meta[$v["field_name"]."_year"]) {
						$val = 0;
					} else {
						$val = mktime(0,0,0,$meta[$v["field_name"]."_month"], $meta[$v["field_name"]."_day"], $meta[$v["field_name"]."_year"]);
					}
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
		#if ($activity_id) {
			$q = sprintf("select count(*) from projects_ext_extrainfo where project_id = ".$project_id);
			$res = sql_query($q);
			if (sql_result($res,0)>0) {
				$q = sprintf("update projects_ext_extrainfo set activity_id = %d, project_year = %d where project_id = %d", $activity_id, $project_year, $project_id);
				sql_query($q);
			} else {
				$q = sprintf("insert into projects_ext_extrainfo (activity_id, project_year, project_id) values (%d, %d, %d)", $activity_id, $project_year, $project_id);
				sql_query($q);
			}
		#} else {
		#	$q = sprintf("delete from projects_ext_extrainfo where project_id = %d", $project_id);
		#	sql_query($q);
		#}
		/* delete empty meta fields */
		$q = sprintf("delete from projects_ext_metavalues where project_id = %d and (meta_value = '' or meta_value is null)", $project_id);
		sql_query($q);

		#echo $project_year;
		#die();

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
	/* {{{ function extGetProjectYear() */
	public function extGetProjectYear($project_id) {
		if ($project_id) {
			$q = sprintf("select project_year from projects_ext_extrainfo where project_id = %d", $project_id);
			$res = sql_query($q);
			return sql_result($res,0);
		} else {
			return date("Y");
		}
	}
	/* }}} */
	/* {{{ function extGetActivities() */
	public function extGetActivityDepartment($id) {
		$q = sprintf("select * from projects_ext_activities where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$q = sprintf("select * from projects_ext_departments where id = %d", $row["department_id"]);
		$res2 = sql_query($q);
		$row2 = sql_fetch_assoc($res2);

		 return array(
		 	"activity" => $row,
		 	"department" => $row2
		 );
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

			//$this->checkFolder($row["department_name"]."/activiteiten/".$row["activity"]);
		}
		return $data;
	}
	/* }}} */
	/* {{{ function extActivitySave() */
	public function extActivitySave() {
		$data = $_REQUEST["data"];

		if ($_REQUEST["id"]) {
			$q = sprintf("update projects_ext_activities set description = '%s', activity = '%s', users = '%s' where id = %d", $data["description"], $data["activity"], $data["users"], $_REQUEST["id"]);
		} else {
			$q = sprintf("insert into projects_ext_activities (department_id, description, activity, users) values (%d, '%s', '%s', '%s')", $data["department_id"], $data["description"], $data["activity"], $data["users"]);
		}
		sql_query($q);

		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("opener.location.href=opener.location.href;");
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

		$start["d"] = sprintf("%d", $_REQUEST["projectext"]["start_day"]);
		$start["m"] = sprintf("%d", $_REQUEST["projectext"]["start_month"]);
		$start["Y"] = sprintf("%d", $_REQUEST["projectext"]["start_year"]);

		$end["d"]   = sprintf("%d", $_REQUEST["projectext"]["end_day"]);
		$end["m"]   = sprintf("%d", $_REQUEST["projectext"]["end_month"]);
		$end["Y"]   = sprintf("%d", $_REQUEST["projectext"]["end_year"]);

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
	/* {{{ function applyMetaSearch() */
	public function prepareMetaSearchQuery() {
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
		if (($start["ts"] && $end["ts"]) && $_REQUEST["projectext"]["metatype"]=="date") {
			$join = " RIGHT JOIN projects_ext_metavalues ON project.id = projects_ext_metavalues.project_id ";
			$cond = sprintf(" AND (meta_id = %d AND meta_value BETWEEN %d AND %d) ", $meta_field, $start["ts"], $end["ts"]) ;
		} elseif ($_REQUEST["projectext"]["metatype"] == "text") {
			$like = sql_syntax("like");
			$join = " RIGHT JOIN projects_ext_metavalues ON project.id = projects_ext_metavalues.project_id ";
			$cond = sprintf(" AND (meta_id = %d AND meta_value %s '%%%s%%') ", $meta_field, $like, $_REQUEST["search"]) ;
		}
		return array(
			"join" => $join,
			"cond" => $cond
		);
	}
	/*}}}*/

	/* {{{ function getAllMetaFields() */
	public function getAllMetaFields() {

		/* get all departments */
		$departments = $this->extGetDepartments();

		/* get activities */
		$activities = array();
		$keys = array();
		foreach ($departments as $k=>$v) {
			$tmp = $this->extGetActivities($v["id"]);
			$activities[$v["id"]] = $tmp;
			if (count($tmp) > 0) {
				foreach ($tmp as $x) {
					$keys[$x["id"]] = $x["department_id"];
				}
			}
		}

		$data = array();
		$q = "select * from projects_ext_metafields order by activity, field_order";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["activity"]) {
				$dep = $keys[$row["activity"]];
				$row["activity_name"] = $activities[$dep][$row["activity"]]["activity"];
				$row["department_name"] = $departments[$dep]["department"];
			}
			$data[]= $row;
		}
		return $data;
	}
	/*}}}*/
	public function getMetaTableData($metaid, $current="", $filter="") {
		$q = sprintf("select * from projects_ext_metafields where id = %d", $metaid);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$conversion = new Layout_conversion();
		#$data = $conversion->utf8_convert($row["large_data"]);
		$data = $row["large_data"];
		if (strlen($filter) < 1 && !$current) {
			return array();
		} else {
			$ndata = array();
			$data = explode("#", $data);
			foreach ($data as $k=>$v) {
				$i++;
				$data[$k] = explode("|", $v);
				if ($filter == "*") {
					$ndata[$data[$k][$row["default_value"]-1]] = $data[$k];
				} elseif (preg_match("/^.\*$/s", $filter)) {
				 	$f = "/^".str_replace("*", "", $filter).".*$/si";
				 	if (preg_match($f, $v)) {
						$ndata[$data[$k][$row["default_value"]-1]] = $data[$k];
				 	}
				}
				if (!((strlen($filter) <= 2 || !stristr($v, $filter)) && $i>1 &&
					$data[$k][$row["default_value"]-1] != $current)) {

					$ndata[$data[$k][$row["default_value"]-1]] = $data[$k];
				}
			}
			return $ndata;
		}
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

 		//$project_name = $project_data->getProjectNameById($_REQUEST["project_id"]);
		$project_info = $project_data->getProjectById($_REQUEST["project_id"]);
		$project_info = $project_info[0];

		$name = $project_info["name"];

 		$user_data = new User_data();
 		$user = $user_data->getUserdetailsById($_REQUEST["project"]["sender"]);
 		//$user_address = $address_data->getAddressByID($user["address_id"], "users");

		//$sender = $address_data->getAddressByID($_REQUEST["sender_address"]);

		if ($_REQUEST["project"]["bcard"]) {
	 		$rcpt = $address_data->getAddressByID($_REQUEST["project"]["bcard"], "bcards");
		} else {
	 		$rcpt = $address_data->getAddressByID($_REQUEST["project"]["address_id"]);
		}

		if ($_REQUEST["project"]["bcard"]) {
			$addressinfo = $address_data->getAddressById($rcpt["address_id"]);
			if (!(trim($rcpt["business_address"]) || trim($rcpt["personal_address"]))) {
				/* no address info attached, get addressinfo from company record */
				$rcpt["address"]   = $addressinfo["address"];
				$rcpt["address2"]  = $addressinfo["address2"];
				$rcpt["zipcode"]   = $addressinfo["zipcode"];
				$rcpt["city"]      = $addressinfo["city"];
				$rcpt["country"]   = $addressinfo["country"];
				$rcpt["phone_nr"]  = $addressinfo["phone_nr"];
				$rcpt["mobile_nr"] = $addressinfo["mobile_nr"];
				$rcpt["fax_nr"]    = $addressinfo["fax_nr"];
				$rcpt["email"]     = $addressinfo["email"];
			} else {
				if (trim($rcpt["business_address"])) {
					$rcpt["address"] = $rcpt["business_address"];
					$rcpt["address2"] = "";
					$rcpt["zipcode"] = $rcpt["business_zipcode"];
					$rcpt["city"] = $rcpt["business_city"];
					$rcpt["country"] = $rcpt["business_country"];
				} else {
					/* put personal address there */
					$rcpt["address"] = $rcpt["personal_address"];
					$rcpt["address2"] = $rcpt["personal_address2"];
					$rcpt["zipcode"] = $rcpt["personal_zipcode"];
					$rcpt["city"] = $rcpt["personal_city"];
					$rcpt["country"] = $rcpt["personal_country"];
				}
			}
			if ($rcpt["timestamp_birthday"]) {
				$rcpt["birthday"] = date("d-m-Y", $rcpt["timestamp_birthday"]);
			} else {
				$rcpt["birthday"] = "N/A";
			}
			if ($rcpt["business_phone_nr"]) {
				$rcpt["phone_nr"] = $rcpt["business_phone_nr"];
			} elseif ($rcpt["personal_phone_nr"]) {
				$rcpt["phone_nr"] = $rcpt["personal_phone_nr"];
			}
			if ($rcpt["business_fax_nr"]) {
				$rcpt["fax_nr"] = $rcpt["business_fax_nr"];
			} elseif($rcpt["personal_fax_nr"]) {
				$rcpt["fax_nr"] = $rcpt["personal_fax_nr"];
			}
			if ($rcpt["business_mobile_nr"]) {
				$rcpt["mobile_nr"] = $rcpt["business_mobile_nr"];
			} elseif($rcpt["personal_mobile_nr"]) {
				$rcpt["mobile_nr"] = $rcpt["personal_mobile_nr"];
			}
			if ($rcpt["business_email"]) {
				$rcpt["f"] = $rcpt["business_email"];
			} elseif($rcpt["personal_email"]) {
				$rcpt["email"] = $rcpt["personal_email"];
			}
			/*
			var_dump($addressinfo);
			die();
			*/
		}

		$rcpt["letterinfo"] = $address_data->generate_letterinfo(array(
			"contact_initials"     => $rcpt["initials"],
			"contact_letterhead"   => $rcpt["letterhead"],
			"contact_commencement" => $rcpt["commencement"],
			"contact_givenname"    => $rcpt["givenname"],
			"contact_infix"        => $rcpt["infix"],
			"contact_surname"      => $rcpt["surname"],
			"title"                => $rcpt["title"]
		));
		$rcpt["tav"] = $rcpt["letterinfo"]["tav"];
		$rcpt["contact_person"] = $rcpt["letterinfo"]["contact_person"];

		if ($user["address_id"]) {
			$sender = $address_data->getAddressByID($user["address_id"], "users");
		}

		/* sender variables */
		global $sender_givenname, $sender_infix, $sender_surname;
		global $sender_email, $sender_email_alt, $sender_companyname, $sender_name;
		global $sender_street, $sender_address, $sender_zipcode, $sender_city;
		global $sender_phone, $sender_fax, $sender_website;

		$sender_givenname = $sender["givenname"];
		$sender_infix     = $sender["infix"];
		$sender_surname   = $sender["surname"];
		$sender_email_1   = $user["mail_email"];
		$sender_email_2   = $user["mail_email1"];
		$sender_email     = $sender["email"];
		$sender_name      = preg_replace("/ {1,}/s", " ", $sender_givenname." ".$sender_infix." ".$sender_surname);

		$sender_address   = $sender["address"];
		$sender_zipcode   = $sender["zipcode"];
		$sender_city      = $sender["city"];
		$sender_phone     = $sender["phone_nr"];
		$sender_fax       = $sender["fax_nr"];
		$sender_mobile    = $sender["mobile_nr"];

		$sender_pobox         = $sender["pobox"];
		$sender_pobox_zipcode = $sender["pobox_zipcode"];
		$sender_pobox_city    = $sender["pobox_city"];

		$sender_website   = preg_replace("/http:\/\//s", "", $sender["website"]);

		/* rcpt variables */
		global $rcpt_companyname, $rcpt_street, $rcpt_address, $rcpt_zipcode, $rcpt_city;
		global $rcpt_phone, $rcpt_fax, $rcpt_website, $rcpt_tav, $rcpt_contact, $rcpt_project;
		global $rcpt_birthday;

		$rcpt_companyname = $rcpt["companyname"];
		$rcpt_street      = $rcpt["address"];
		$rcpt_address     = $rcpt["address"];
		$rcpt_zipcode     = $rcpt["zipcode"];
		$rcpt_city        = $rcpt["city"];
		$rcpt_phone       = $rcpt["phone_nr"];
		$rcpt_fax         = $rcpt["fax_nr"];
		$rcpt_website     = preg_replace("/http:\/\//s", "", $rcpt["website"]);
		$rcpt_email       = $rcpt["email"];
		$rcpt_mobile      = $rcpt["mobile_nr"];
		$rcpt_tav         = $rcpt["tav"];
		$rcpt_contact     = $rcpt["contact_person"];
		$rcpt_project     = $project_name;
		$rcpt_birthday    = $rcpt["birthday"];

		/* date variables */
		global $datetime, $datetime_full, $datetime_short;

		$datetime       = strftime("%a %d %b %Y");
		$datetime_full  = strftime("%a %d %b %Y");
		$datetime_short = date("d-m-Y");

		/* project variables */
		global $project_name, $project_description, $project_manager, $project_executor;
		global $project_currentactivity;

		$project_name        = $project_info["name"];
		$project_description = $project_info["description"];

		if ($project_info["manager"]) {
			$project_manager_arr   = $user_data->getEmployeedetailsById($project_info["manager"]);
			$project_manager = $project_manager_arr["realname"];
		} else
			$project_manager   = "";

		if ($project_info["executor"]) {
			$project_executor_arr  = $user_data->getEmployeedetailsById($project_info["executor"]);
			$project_executor = $project_executor_arr["realname"];
		} else
			$project_executor  = "";

		$project_id = $_REQUEST["project_id"];
		$act = $this->extGetProjectActivities($project_id);

		/* get current activity */
		$activity_id = $this->extGetProjectActivityType($project_id);
		$activity = $this->extGetActivities("", $activity_id);
		$project_currentactivity = $activity[$activity_id]["activity"];
		if (!$activity_id) $project_currentactivity = gettext("none");


		$act[]=0;
		foreach ($act as $a) {
			$data = $this->extGetMetaFields("", $project_id, $a);
			foreach ($data as $k=>$v) {
				if ($v["field_type"] == 2) {
					$v["value"] = date("d-m-Y", (int)$v["value"]);
				} elseif ($v["field_type"] == 6) {
					if ($v["value"] == 0)
						$v["value"] = "";
					else
						$v["value"] = $user_data->getEmployeedetailsById($v["value"]);
				} elseif ($v["field_type"] == 7) {
					$v["value"] = number_format($v["value"], 2, ",", ".");
				}

				eval(sprintf("global \$field_%d; \$field_%d = '%s'; ", $v["id"], $v["id"], addslashes($v["value"])));
				if ($v["field_type"] == 5) {
					$table = $this->getMetaTableData($v["id"], $v["value"]);
					if (is_array($table[$v["value"]])) {
						foreach ($table[$v["value"]] as $key=>$vals) {
							eval(sprintf("global \$field_%d_%d; \$field_%d_%d = '%s'; ", $v["id"], $key, $v["id"], $key, addslashes($vals)));
						}
					}
				}
			}
		}

		if ($_REQUEST["file_type"] == "project") {
			$dir = $this->share_path."templates/".$_REQUEST["file_name"];
			$dir = urldecode($dir);
			#echo $dir;
			#die();
			$ooo->NewDocFromTpl($dir);
		}
		//get calendar appointments
		$cal_data = new Calendar_data();
		//$cal_list = $cal_data->getAppointmentsBySearch(array("project_id" => $_REQUEST["project_id"]));
		$cal_list_tmp = $project_data->getHoursList(array("projectid"=>$_REQUEST["project_id"], "lfact"=>0));
		$cal_list = $cal_list_tmp["items"];
		if (!is_array($cal_list) || count($cal_list) < 1) {
			$cal_list = array();
		} else {
			foreach ($cal_list as $k=>$v) {
				$v["body"] = strip_tags(str_replace("</p>", "\n\n", str_replace("<br />", "\n", str_replace("<br>", "\n", html_entity_decode($v["description"])))));
				$v["human_start"] = $v["human_start_date"];
				$v["human_end"] = $v["human_end_date"];
				$cal_list[$k] = $v;
			}
		}
		global $cal, $cal1;
		$cal = $cal_list;
		$cal1 = $cal_list;
		$ooo->LoadXmlFromDoc('content.xml');
		$ooo->MergeBlock('cal', $cal);
		$ooo->MergeBlock('cal1', $cal1);
		$ooo->SaveXmlToDoc();

		header("Content-Transfer-Encoding: binary");
		header("Content-type: ".$ooo->GetMimetypeDoc());

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename="'.basename($_REQUEST["file_name"]).'"'); //msie 5.5 header bug
		} else {
			header('Content-Disposition: attachment; filename="'.basename($_REQUEST["file_name"]).'"');
		}

		//header('Content-Length: '.filesize($ooo->GetPathnameDoc()));
		$ooo->FlushDoc();
		$ooo->RemoveDoc();
		exit();
	}
	/* }}} */
}
?>
