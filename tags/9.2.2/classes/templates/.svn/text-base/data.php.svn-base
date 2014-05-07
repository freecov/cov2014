<?php
/**
 * Covide Groupware-CRM Templates data class
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Templates_data {

	/* constants */
	const include_dir =  "classes/templates/inc/";

	/* variables */
	private $pagesize = 20;

	/* methods */

	public function __construct() {
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}

	public function templateSaveSelection($address_ids, $id) {
		$q = sprintf("update templates set ids = '%s' where id = %d", $address_ids, $id);
		sql_query($q);
	}
	public function getFinanceTemplate($nr) {
		$q = sprintf("select id from templates where finance = %d", $nr);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0)
			return false;
		else
			return sql_result($res, 0);
	}
	public function templateSave() {
		$tpl             = $_REQUEST["tpl"];
		$tpl["contents"] = $_REQUEST["contents"];
		$tpl["id"]       = $_REQUEST["id"];

		if ($_REQUEST["use_signature"]) {
			$email_data = new Email_data();
			$tpl["sender"] = $email_data->getEmailSignature($tpl["address_signature"]);
		}


		$esc = sql_syntax("escape_char");

		$fields["body"]                = array("s",$tpl["contents"]);
		$fields[$esc."footer".$esc]    = array("s",$tpl["footer"]);
		$fields["sender"]              = array("s",addslashes($tpl["sender"]));
		$fields["description"]         = array("s",$tpl["description"]);
		$fields[$esc."header".$esc]    = array("s",$tpl["header"]);
		$fields["settings_id"]         = array("d",$tpl["settings_id"]);
		$fields[$esc."date".$esc]      = array("s",$tpl["date"]);
		$fields["city"]                = array("s",$tpl["city"]);
		$fields["fax_nr"]              = array("d",$tpl["fax_nr"]);
		$fields["signature"]           = array("d",$tpl["signature"]);

		$fields["font"]                = array("s",$tpl["font"]);
		$fields["fontsize"]            = array("d",$tpl["fontsize"]);
		$fields["finance"]             = array("d",$tpl["finance"]);

		$fields["address_businesscard_id"] = array("d", $tpl["address_businesscard_id"]);
		$fields["businesscard_id"]         = array("d", $tpl["businesscard_id"]);

		$fields["and_or"] = array("s", strtoupper($tpl["and_or"]));
		$fields["ids"] = array("s", $tpl["ids"]);

		$tpl["classification"] = str_replace("|", ",", $tpl["classification"]);
		$tpl["classification"] = preg_replace("/(^,)|(,$)/s", "", $tpl["classification"]);
		$fields["classification"] = array("s", $tpl["classification"]);

		$tpl["negative_classification"] = str_replace("|", ",", $tpl["negative_classification"]);
		$tpl["negative_classification"] = preg_replace("/(^,)|(,$)/s", "", $tpl["negative_classification"]);
		$fields["negative_classification"] = array("s", $tpl["negative_classification"]);


		if (!$tpl["id"]) {

			$fields["user_id"] = array("d", $_SESSION["user_id"]);

			/* insert */
			$keys = array();
			$vals = array();
			foreach ($fields as $k=>$v) {
				$keys[] = $k;
				if ($v[0]=="s") {
					$vals[]="'".$v[1]."'";
				} else {
					$vals[]=(int)$v[1];
				}
			}
			$keys = implode(",",$keys);
			$vals = implode(",",$vals);

			$q = sprintf("insert into templates (%s) values (%s)", $keys, $vals);
			sql_query($q);

			$id = sql_insert_id("templates");

		} else {
			/* update */
			$vals = array();
			foreach ($fields as $k=>$v) {
				if ($v[0]=="s") {
					$vals[$k]="'".$v[1]."'";
				} else {
					$vals[$k]=(int)$v[1];
				}
			}
			$q = "update templates set user_id = ".$_SESSION["user_id"];
			foreach ($vals as $k=>$v) {
				$q.= sprintf(", %s = %s ", $k, $v);
			}
			$q.= sprintf(" where id = %d", $tpl["id"]);
			sql_query($q);
		}
		return $id;

	}

	public function getTemplateSettings($fetchdata=0) {
		$data = array();
		$q = "select * from templates_settings order by description";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($fetchdata) {
				$data[$row["id"]] = $row;
			} else {
				$data[$row["id"]] = $row["description"];
			}
		}
		return $data;
	}
	public function getTemplateSettingById($id) {
		$q = sprintf("select * from templates_settings where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		return $row;
	}

	public function getTemplateBySearch($address_id="", $start=0, $nolimit=0, $sort="", $search="") {
		$data = array();

		$regex = sql_syntax("regex");
		$address_data = new Address_data();

		if ($address_id) {
			$q  = sprintf("select * from templates where address_id = %1\$d OR ids %2\$s '(^|,)R{0,1}%1\$d(,|$)' order by description", $address_id, $regex);
			$qc = sprintf("select count(*) from templates where address_id = %1\$d OR ids %2\$s '(^|\\\\|)%1\$d(\\\\||$)'", $address_id, $regex);
		} else {
			if (!$sort) {
				$sortkey = " address_id, description";
			} else {
				$sortkey = sql_filter_col($sort);
			}
			if ($search) {
				$like = sql_syntax("like");
				$esc  = sql_syntax("escape_char");
				$sq = sprintf(" WHERE (finance is null or finance = 0) and (address.companyname %s '%%%s%%' ", $like, $search);
				$sq.= sprintf(" OR body %s '%%%s%%' ", $like, $search);
				$sq.= sprintf(" OR footer %s '%%%s%%' ", $like, $search);
				$sq.= sprintf(" OR description %s '%%%s%%' ", $like, $search);
				$sq.= sprintf(" OR header %s '%%%s%%' ", $like, $search);
				$sq.= sprintf(" OR %sdate%s %s '%%%s%%') ", $esc, $esc, $like, $search);
			} else {
				$sq = " where finance is null or finance = 0 ";
			}
			$q  = sprintf("select templates.*, address.companyname from templates left join address on address.id = templates.address_id %s order by %s", $sq, $sortkey);
			$qc = sprintf("select count(*) from templates left join address on address.id = templates.address_id %s", $sq);

			/* update templates */
			$qu = "select id, ids, address_id from templates where address_id != ids and ids != '' and not ids IS NULL";
			$res = sql_query($qu);
			while ($row = sql_fetch_assoc($res)) {
				if (!preg_match("/[a-z,]/si", $row["ids"])) {
					//split
					$aids = explode(",", $row["ids"]);
					$qu = sprintf("update templates set address_id = %d where id = %d",
						$aids[0], $row["id"]);
					sql_query($qu);
				}
			}

		}
		if ($nolimit) {
			$res = sql_query($q);
		} else {
			$res = sql_query($q, "", (int)$start, $this->pagesize);
		}
		while ($row = sql_fetch_assoc($res)) {
			$ids = explode(",", $row["ids"]);
			if (count($ids) == 1) {
				if ($row["businesscard_id"]) {
					$tmp = $address_data->getAddressByID($row["businesscard_id"], "bcards");
					$row["address"] = sprintf("%s, %s", $tmp["companyname"], $tmp["fullname"]);
					$row["icon_bcard"] = 1;
					unset($tmp);
				} else {
					$row["address"] = $address_data->getAddressNameByID($ids[0]);
					$row["icon_single"] = 1;
				}
			} else {
				// multiple
				$row["icon_multi"] = 1;
				$row["address"] = sprintf("(%d %s)", count($ids), gettext("relations"));
			}
			$row["datecity"] = sprintf("%s, %s", $row["city"], $row["date"]);
			if (!$row["description"])
				$row["description"] = sprintf("[%s]", gettext("no description"));

			$data[$row["id"]] = $row;
		}
		$part["data"] =& $data;

		$res = sql_query($qc);
		$part["count"] = sql_result($res,0);

		return $part;
	}

	public function getTemplateById($id) {
		$q = sprintf("select * from templates where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$address_data = new Address_data();

		if ($row["address_id"]) {
			$row["address"] = $address_data->getAddressNameByID($row["address_id"]);
		} else {
			$ids = explode(",", $row["ids"]);
			if ($ids[0]) {
				$row["address"] = $address_data->getAddressNameByID($ids[0]);
			}
			$row["address_id"] = $ids[0];
		}
		return $row;
	}
	public function settingsSave() {
		$data = $_REQUEST["data"];

		$fields["address_position"] = array("d", $data["address_position"]);
		$fields["address_left"]     = array("f", $data["address_left"]);
		$fields["address_top"]      = array("f", $data["address_top"]);
		$fields["address_width"]    = array("f", $data["address_width"]);
		$fields["page_left"]        = array("f", $data["page_left"]);
		$fields["page_top"]         = array("f", $data["page_top"]);
		$fields["page_right"]       = array("f", $data["page_right"]);

		$fields["footer_position"]  = array("s", $data["footer_position"]);
		$fields["footer_text"]      = array("s", $data["footer_text"]);
		$fields["logo_position"]    = array("d", $data["logo_position"]);

		if (!$_REQUEST["id"]) {

			$fields["description"] = array("s", $data["description"]);

			/* insert */
			$keys = array();
			$vals = array();
			foreach ($fields as $k=>$v) {
				$keys[] = $k;
				if ($v[0]=="f") {
					$vals[]=number_format($v[1],2);
				} elseif ($v[0]=="s") {
					$vals[]="'".$v[1]."'";
				} else {
					$vals[]=(int)$v[1];
				}
			}
			$keys = implode(",",$keys);
			$vals = implode(",",$vals);

			$q = sprintf("insert into templates_settings (%s) values (%s)", $keys, $vals);
		} else {
			/* update */
			$vals = array();
			foreach ($fields as $k=>$v) {
				if ($v[0]=="f") {
					$vals[$k]=number_format($v[1],2);
				} elseif ($v[0]=="s") {
					$vals[$k]="'".$v[1]."'";
				} else {
					$vals[$k]=(int)$v[1];
				}
			}
			$q = sprintf("update templates_settings set description = '%s'", $data["description"]);
			foreach ($vals as $k=>$v) {
				$q.= sprintf(", %s = %s ", $k, $v);
			}
			$q.= sprintf(" where id = %d", $_REQUEST["id"]);
		}
		sql_query($q);


		//check for new files
		$files =& $_FILES["image"];
		$filesys = new Filesys_data();

		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir  = "templates";

		if (is_array($files)) {
			/* if file position is filled with a tmp_name */
			if ($files["error"] == UPLOAD_ERR_OK && $files["tmp_name"]) {

				/* gather some file info */
				$name = $files["name"];
				$type = $filesys->detectMimetype($files["tmp_name"]);
				$size = $files["size"];

				/* insert file into dbase */
				$q = "insert into templates_files (template_id, name, size, type) values ";
				$q.= sprintf("(%d, '%s', '%s', '%s')", $_REQUEST["id"], $name, $size, $type);
				sql_query($q);
				$new_id = sql_insert_id("templates_files");

				/* move data to the destination */
				$destination = sprintf("%s/%s/%s.tdat", $fspath, $fsdir, $new_id);
				move_uploaded_file($files["tmp_name"], $destination);
			}
		}

	}

	public function getTemplateFile($id) {
		$q = sprintf("select * from templates_files where template_id = %d", $id);
		$res = sql_query($q);
		if (sql_num_rows($res)>0) {
			$row = sql_fetch_assoc($res);
			return $row;

		}
	}
	public function showTemplateFile($id) {
		$q = sprintf("select * from templates_files where id = %d", $id);
		$res = sql_query($q);
		$data = sql_fetch_assoc($res);

		switch (trim(strtolower($data["type"]))) {
			case "image/bmp":
			case "image/gif":
			case "image/jpeg":
			case "image/pjpeg":
				$data["subtype"] = "image";
				break;
		}
		if ($data["subtype"] != "image") {
			exit();
		}

		$conversion = new Layout_conversion();
		$data["h_size"] = $conversion->convert_to_bytes($data["size"]);
		unset($conversion);

		/* retrieve from filesys */
		$fspath = $GLOBALS["covide"]->filesyspath;
		$file = sprintf("%s/templates/%s.tdat", $fspath, $id);
		if (!file_exists($file)) {
			exit;
		}

		$data["data_file"] = $file;
		$datafile = fopen($file,"r");
		$data["data_binary"] = fread($datafile, filesize($file));
		fclose($datafile);

		header('Content-Transfer-Encoding: binary');
		header('Content-Type: '.strtolower($data["type"]));

		echo $data["data_binary"];
		exit();

	}

	public function delTemplateFile() {
		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir  = "templates";

		$id = $_REQUEST["id"];
		$q = sprintf("select id from templates_files where template_id = %d", $id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$fid = $row["id"];
			$file = sprintf("%s/%s/%s.tdat", $fspath, $fsdir, $id);
			@unlink($file);
		}
		$q = sprintf("delete from templates_files where template_id = %d", $id);
		sql_query($q);
	}

	public function load_preview($data) {
		require(self::include_dir."templatePreview.php");
		return gzuncompress(base64_decode($data));
	}

	public function templateDelete($id) {
		$q = sprintf("delete from templates where id = %d", $id);
		sql_query($q);
	}

	public function settingsDelete($id) {
		$q = sprintf("delete from templates_settings where id = %d", $id);
		sql_query($q);
	}
	public function templateDeleteFinance($finance) {
		if ($finance > 0) {
			$q = sprintf("delete from templates where finance = %d", $finance);
			sql_query($q);
		}
		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("
			opener.document.getElementById('template_edit').style.display = 'none';
			opener.document.getElementById('template_new').style.display = '';
		");
		$output->end_javascript();
		$output->exit_buffer();
	}
	public function templateCopyFinance($old, $new) {
		$q = sprintf("select count(*) from templates where finance = %d", $old);
		$res = sql_query($q);

		if ($old && $new && sql_result($res,0) > 0) {
			$q = sprintf("select * from templates where finance = %d", $old);
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);

			$fields["address_businesscard_id"] = array("d", $row["address_businesscard_id"]);
			$fields["font"]                    = array("s", $row["font"]);
			$fields["fontsize"]                = array("d", $row["fontsize"]);
			$fields["body"]                    = array("s", $row["body"]);
			$fields["footer"]                  = array("s", $row["footer"]);
			$fields["sender"]                  = array("s", $row["sender"]);
			$fields["address_id"]              = array("s", $row["address_id"]);
			$fields["description"]             = array("s", $row["description"]);
			$fields["classification"]          = array("s", $row["classification"]);
			$fields["ids"]                     = array("s", $row["ids"]);
			$fields["header"]                  = array("s", $row["header"]);
			$fields["user_id"]                 = array("d", $row["user_id"]);
			$fields["settings_id"]             = array("d", $row["settings_id"]);
			$fields["date"]                    = array("s", $row["date"]);
			$fields["city"]                    = array("s", $row["city"]);
			$fields["negative_classification"] = array("s", $row["negative_classification"]);
			$fields["multirel"]                = array("s", $row["multirel"]);
			$fields["save_date"]               = array("d", $row["save_date"]);
			$fields["and_or"]                  = array("s", $row["and_or"]);
			$fields["fax_nr"]                  = array("d", $row["fax_nr"]);
			$fields["signature"]               = array("d", $row["signature"]);
			$fields["finance"]                 = array("d", $new);

			$keys = array();
			$vals = array();
			foreach ($fields as $k=>$v) {
				$keys[] = $k;
				if ($v[0]=="s") {
					//addslashes already done
					$vals[]="'".$v[1]."'";
				} else {
					$vals[]=(int)$v[1];
				}
			}
			$keys = implode(",",$keys);
			$vals = implode(",",$vals);

			$q = sprintf("insert into templates (%s) values (%s)", $keys, $vals);
			sql_query($q);
		}
	}
}
?>
