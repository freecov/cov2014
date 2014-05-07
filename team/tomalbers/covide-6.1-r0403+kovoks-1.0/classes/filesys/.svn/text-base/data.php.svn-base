<?php
/**
 * Covide Groupware-CRM Filesys module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version 6.0
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Filesys_data {

	/* constants */
	const include_dir = "classes/filesys/inc/";
	const class_name = "filesys_data";

	/* variables */
	public $pagesize = 20;
	private $_cache;

	/* methods */
    /* 	__construct {{{ */
    /**
     * 	__construct. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function __construct() {
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}

	public function detectMimetype($file) {

		$file = escapeshellarg($file);
		$cmd = sprintf("file -i %s", $file);

		exec ($cmd, $ret, $status);
		$ret = explode(": ", $ret[0]);
		$ret = strtoupper(preg_replace("/\;.*$/s","",$ret[1]));
		return $ret;
	}
	/* }}} */

	/* {{{ compressFile($file) */
	/**
	 * return complete gz encoded file
	 *
	 * Get the data and create gz encoded file
	 * This function will also add the gz file headers.
	 * gzcat, gunzip etc will be able to read the return value
	 *
	 * @param string the file data to be compressed
	 * @return string the gzencoded representation of the original string
	 */
	public function compressFile($file) {
		$data = gzencode($file);
		return $data;
	}
	/* }}} */

	/* {{{ uncompressFile($file) */
	/**
	 * return actual file data from gz file
	 *
	 * Get the data and uncompress gz encoded file
	 * This function needs a complete gz file, including headers
	 * gzip file will create such a file
	 *
	 * @param string the file data to be uncompressed
	 * @return string the normal binary representation of the zipped file string
	 */
	public function uncompressFile($file) {
		$len = strlen($data);
		if ($len < 18 || strcmp(substr($data,0,2),"\x1f\x8b")) {
			return null;  // Not GZIP format (See RFC 1952)
		}
		$method = ord(substr($data,2,1));  // Compression method
		$flags  = ord(substr($data,3,1));  // Flags
		if ($flags & 31 != $flags) {
			// Reserved bits are set -- NOT ALLOWED by RFC 1952
			return null;
		}
		// NOTE: $mtime may be negative (PHP integer limitations)
		$mtime = unpack("V", substr($data,4,4));
		$mtime = $mtime[1];
		$xfl  = substr($data,8,1);
		$os    = substr($data,8,1);
		$headerlen = 10;
		$extralen  = 0;
		$extra    = "";
		if ($flags & 4) {
			// 2-byte length prefixed EXTRA data in header
			if ($len - $headerlen - 2 < 8) {
				return false;    // Invalid format
			}
			$extralen = unpack("v",substr($data,8,2));
			$extralen = $extralen[1];
			if ($len - $headerlen - 2 - $extralen < 8) {
				return false;    // Invalid format
			}
			$extra = substr($data,10,$extralen);
			$headerlen += 2 + $extralen;
		}

		$filenamelen = 0;
		$filename = "";
		if ($flags & 8) {
			// C-style string file NAME data in header
			if ($len - $headerlen - 1 < 8) {
				return false;    // Invalid format
			}
			$filenamelen = strpos(substr($data,8+$extralen),chr(0));
			if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
				return false;    // Invalid format
			}
			$filename = substr($data,$headerlen,$filenamelen);
			$headerlen += $filenamelen + 1;
		}

		$commentlen = 0;
		$comment = "";
		if ($flags & 16) {
			// C-style string COMMENT data in header
			if ($len - $headerlen - 1 < 8) {
				return false;    // Invalid format
			}
			$commentlen = strpos(substr($data,8+$extralen+$filenamelen),chr(0));
			if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
				return false;    // Invalid header format
			}
			$comment = substr($data,$headerlen,$commentlen);
			$headerlen += $commentlen + 1;
		}
		$headercrc = "";
		if ($flags & 1) {
			// 2-bytes (lowest order) of CRC32 on header present
			if ($len - $headerlen - 2 < 8) {
				return false;    // Invalid format
			}
			$calccrc = crc32(substr($data,0,$headerlen)) & 0xffff;
			$headercrc = unpack("v", substr($data,$headerlen,2));
			$headercrc = $headercrc[1];
			if ($headercrc != $calccrc) {
				return false;    // Bad header CRC
			}
			$headerlen += 2;
		}

		// GZIP FOOTER - These be negative due to PHP's limitations
		$datacrc = unpack("V",substr($data,-8,4));
		$datacrc = $datacrc[1];
		$isize = unpack("V",substr($data,-4));
		$isize = $isize[1];

		// Perform the decompression:
		$bodylen = $len-$headerlen-8;
		if ($bodylen < 1) {
			// This should never happen - IMPLEMENTATION BUG!
			return null;
		}
		$body = substr($data,$headerlen,$bodylen);
		$data = "";
		if ($bodylen > 0) {
			switch ($method) {
				case 8:
					// Currently the only supported compression method:
					$data = gzinflate($body);
					break;
				default:
					// Unknown compression method
					return false;
			}
		} else {
			// I'm not sure if zero-byte body content is allowed.
			// Allow it for now...  Do nothing...
		}

		// Verifiy decompressed size and CRC32:
		// NOTE: This may fail with large data sizes depending on how
		//      PHP's integer limitations affect strlen() since $isize
		//      may be negative for large sizes.
		if ($isize != strlen($data) || crc32($data) != $datacrc) {
			// Bad format!  Length or CRC doesn't match!
			return false;
		}
		return $data;
	}
	/* }}} */

	/* {{{ check_folder($settings) */
	/**
	 * check if the folder excists. if not, create it
	 *
	 * @param array the folder settings
	 *	At least the following keys should be present: name, parent_id
	 *	Optional: user_id, is_public, sticky, is_relation, address_id, is_shared, hrm_id, project_id
	 * @return bool true on success, false on fail
	 */
	public function check_folder($settings) {
		$sql  = "SELECT COUNT(*) FROM filesys_folders WHERE ";
		$sql .= sprintf("name = '%s' AND parent_id = %d",
			addslashes($settings["name"]),
			$settings["parent_id"]
		);
		/* already prepare the insert statement for when the record is not found */
		$fields = "name, parent_id";
		$values = sprintf("'%s', %d", addslashes($settings["name"]), $settings["parent_id"]);
		if (array_key_exists("user_id", $settings)) {
			$sql .= sprintf(" AND user_id = %d", $settings["user_id"]);
			$fields .= ", user_id"; $values .= sprintf(", %d", $settings["user_id"]);
		}
		if (array_key_exists("is_public", $settings)) {
			$sql .= sprintf(" AND is_public = %d", $settings["is_public"]);
			$fields .= ", is_public"; $values .= sprintf(", %d", $settings["is_public"]);
		}
		if (array_key_exists("sticky", $settings)) {
			$sql .= sprintf(" AND sticky = %d", $settings["sticky"]);
			$fields .= ", sticky"; $values .= sprintf(", %d", $settings["sticky"]);
		}
		if (array_key_exists("is_relation", $settings)) {
			$sql .= sprintf(" AND is_relation = %d", $settings["is_relation"]);
			$fields .= ", is_relation"; $values .= sprintf(", %d", $settings["is_relation"]);
		}
		if (array_key_exists("address_id", $settings)) {
			$sql .= sprintf(" AND address_id = %d", $settings["address_id"]);
			$fields .= ", address_id"; $values .= sprintf(", %d", $settings["address_id"]);
		}
		if (array_key_exists("is_shared", $settings)) {
			$sql .= sprintf(" AND is_shared = %d", $settings["is_shared"]);
			$fields .= ", is_shared"; $values .= sprintf(", %d", $settings["is_shared"]);
		}
		if (array_key_exists("hrm_id", $settings)) {
			$sql .= sprintf(" AND hrm_id = %d", $settings["hrm_id"]);
			$fields .= ", hrm_id"; $values .= sprintf(", %d", $settings["hrm_id"]);
		}
		if (array_key_exists("project_id", $settings)) {
			$sql .= sprintf(" AND project_id = %d", $settings["project_id"]);
			$fields .= ", project_id"; $values .= sprintf(", %d", $settings["project_id"]);
		}
		$res = sql_query($sql);
		$count = sql_result($res, 0);
		if (!$count) {
			$sql = "INSERT INTO filesys_folders ($fields) VALUES ($values)";
			$res = sql_query($sql);
			if (!$res) {
				return false;
			}
		}
		return true;
	}
	/* }}} */

	function parseSize($size) {
		$mod = "bytes";
		if ($size>1024) {
			$size /= 1024;
			$mod = "KB";
		}
		if ($size>1024) {
			$size /= 1024;
			$mod = "MB";
		}
		if ($mod=="bytes") {
			$size = (int)$size;
		} else {
			$size = number_format(($size),2,",",".");
		}
		$return = $size." ".$mod;
		return $return;
	}

	public function getProjectFolder($projectid) {
		/* get main projectss folder */
		$sql = "SELECT id FROM filesys_folders WHERE parent_id=0 AND name='projecten'";
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		/* check if the project folder is there */
		/* first, get project name from db */
		$project_data = new Project_data();
		$projectinfo = $project_data->getProjectById($projectid, 0);
		$projectname = $projectinfo[0]["name"];
		$projectrelation = $projectinfo[0]["address_id"];
		unset($project_data);
		$folder_settings = array(
			"name"        => $projectname,
			"is_public"   => 1,
			"is_relation" => 1,
			"address_id"  => (int)$projectrelation,
			"parent_id"   => (int)$row["id"],
			"sticky"      => 1,
			"project_id"  => (int)$projectid
		);
		$this->check_folder($folder_settings);
		/* get the project folder */
		$sql = sprintf("SELECT id FROM filesys_folders WHERE parent_id=%d AND project_id=%d", $row["id"], $projectid);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		return $row["id"];
	}

	public function getRelFolder($addressid) {
		/* get main relations folder */
		$sql = "SELECT id FROM filesys_folders WHERE parent_id=0 AND name='relaties'";
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		/* check if the relation folder is there */
		/* first, get relation name from db */
		$address_data = new Address_data();
		$relname = $address_data->getAddressNameById($addressid);
		unset($address_data);
		$folder_settings = array(
			"name"        => $relname,
			"is_public"   => 1,
			"is_relation" => 1,
			"address_id"  => $addressid,
			"parent_id"   => $row["id"],
			"sticky"      => 0,
			"project_id"  => 0
		);
		$this->check_folder($folder_settings);
		/* get the relation folder */
		$sql = sprintf("SELECT id FROM filesys_folders WHERE parent_id=%d AND address_id=%d", $row["id"], $addressid);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		return $row["id"];
	}

	public function getFoldernameById($folderid, $allow_translate=0) {
		$sql = sprintf("SELECT name, parent_id FROM filesys_folders WHERE id=%d", $folderid);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		if (!$row["parent_id"] && $allow_translate) {
			return gettext($row["name"]);
		} else {
			return $row["name"];
		}
	}

	public function getParentFolder($folderid) {
		$sql = sprintf("SELECT parent_id FROM filesys_folders WHERE id=%d", $folderid);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		return $row["parent_id"];
	}

	/* {{{ getFolders($settings) */
	/**
	 * get folder-structure from db
	 *
	 * @param array the folder settings
	 * @return array the structure
	 */
	public function getFolders($settings = array(), $top=0) {

		if (!$settings["sort"]) {
			$settings["sort"] = "upper(name)";
		} else {
			$settings["sort"] = sql_filter_col($settings["sort"]);
		}
		if ($settings["ids"]) {
			$sql        = sprintf("SELECT * FROM filesys_folders WHERE id IN (%s)", $settings["ids"]);
			$sql_count  = sprintf("SELECT count(*) FROM filesys_folders WHERE id IN (%s)", $settings["ids"]);

		} elseif (!$settings["parentfolder"]) {
			/* get toplevel structure */
			$sql        = sprintf("SELECT * FROM filesys_folders WHERE (parent_id = 0 OR parent_id is null) AND ((name='mijn documenten' AND user_id=%d)", $_SESSION["user_id"]);
			$sql_count  = sprintf("SELECT count(*) FROM filesys_folders WHERE (parent_id = 0 OR parent_id is null) AND ((name='mijn documenten' AND user_id=%d)", $_SESSION["user_id"]);

			if ($GLOBALS["covide"]->license["has_project"]) {
				$sql       .= " OR (name='projecten')";
				$sql_count .= " OR (name='projecten')";
			}
			if ($GLOBALS["covide"]->license["has_hrm"]) {
				$sql       .= " OR (name='hrm')";
				$sql_count .= " OR (name='hrm')";
			}
			$sql .= " OR (name='relaties')";
			$sql .= " OR (name='openbare mappen'))";
			$sql .= " ORDER BY is_public,UPPER(name)";
			$sql_count .= " OR (name='relaties' AND parent_id=0)";
			$sql_count .= " OR (name='openbare mappen' AND parent_id=0))";

		} else {

			$like = sql_syntax("like");
			if ($settings["search"]) {
				if (preg_match("/\*$/s", $settings["search"])) {
					$settings["search"] = preg_replace("/\*$/s", "%", $settings["search"]);
				} else {
					$settings["search"] = "%".$settings["search"]."%";
				}
				$sq = sprintf(" AND (name $like '%s' or description $like '%s') ", $settings["search"], $settings["search"]);
			}
			$join = " left join filesys_permissions on filesys_permissions.folder_id = filesys_folders.id ";

			$sql  = sprintf("SELECT filesys_folders.*, filesys_permissions.user_id as p_users, filesys_permissions.permissions as p_permissions FROM filesys_folders %s WHERE parent_id=%d %s ORDER BY %s ", $join, $settings["parentfolder"], $sq, $settings["sort"]);
			$sql_count  = sprintf("SELECT count(*) FROM filesys_folders %s WHERE parent_id=%d %s", $join, $settings["parentfolder"], $sq);
		}

		$res_count = sql_query($sql_count);
		$res       = sql_query($sql, "", (int)$top, $this->pagesize);

		/* extract permissions for this user */
		$user_data = new User_data();
		$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);

		#debug
		#$user_info["xs_filemanage"]=0;
		#$user_info["xs_addressmanage"]=0;
		#$user_info["xs_projectmanage"]=0;

		if ($settings["ids"]) {
			$downgrade = 1;
		} else {
			$downgrade = 0;
		}

		while ($row = sql_fetch_assoc($res)) {

			$row = $this->extractPermissions($row, &$user_info, $downgrade);
			if ($row) {
				$return["data"][] = $row;
			}

		}
		$return["count"] = sql_result($res_count,0);

		/* now get the basic folder permissions */
		$q = sprintf("select * from filesys_folders where id = %d",$settings["parentfolder"]);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$row = $this->extractPermissions($row, &$user_info);
		$return["xs"] = $row["xs"];
		$return["xs_subaction"]   = $row["xs_subaction"];
		$return["current_folder"] = $row;

		return $return;
	}
	/* }}} */


	private function extractPermissions($row, $user_info, $downgrade_permissions=0) {
		/* start with deny all level */
			$xs = "D";

			if ($row["parent_id"]==0) {
				$hp = array("id" => 0, "name" => "root");
			} else {
				$hp = $this->getHighestParent($row["id"]);
			}

			$row["hp_name"] = $hp["name"];
			$row["hp_id"]   = $hp["id"];

			/* get highest parent permissions */
			switch ($hp["name"]) {
				case "mijn documenten":
					if ($_SESSION["user_id"] == $row["user_id"]) {
						$xs = "W";
					} else {
						$xs = "D";
					}
					break;
				case "projecten":
					/* upgrade permissions */
					if ($user_info["xs_projectmanage"] || $user_info["xs_filemanage"]) {
						$xs = "W";
					} else {
						$xs = $this->checkPermissions($row["id"], "projects");
					}
					if (!$xs) {
						$row["name"] = gettext("geen toegang");
					}
					break;
				case "relaties":
					/* upgrade permissions */
					if (($user_info["xs_addressmanage"] && $user_info["relationmanage"]) || $user_info["xs_filemanage"]) {
						$xs = "W";
					} else {
						$xs = $this->checkPermissions($row["id"], "relations");
					}
					break;
				case "hrm":
					if ($user_info["xs_hrmmanage"]) {
						$xs = "W";
					} else {
						$xs = "D";
					}
					if (!$xs) {
						$row["name"] = gettext("geen toegang");
					}
					break;
				case "root":
					$xs = "R";
					break;
				default:
					/* check for additional permissions */
					if ($user_info["xs_filemanage"]) {
						$xs = "W";
					} else {
						$xs = $this->checkPermissions($row["id"]);
					}
					/* if no permission, upgrade to max */
					if (!$xs) {
						$xs = "W";
					}
					break;
			}

			/* if parent id = 0 and folder name != my docs then no file permissions (only folder) */
			if ($row["parent_id"] == 0 && $row["naam"] != "hrm" && $user_info["xs_filemanage"]) {
				$xs = "S";
			}
			if ($row["parent_id"] == 0 && $row["name"] == "mijn documenten") {
				$xs = "W";
			}

			/* if no permissions, fallback to deny all */
			if (!$xs) {
				$xs = "D";
			}

			/* add permissions to data array */
			$row["xs"] = $xs;
			if ($user_info["xs_filemanage"] && $row["parent_id"]!=0) {
				if (!$_REQUEST["subaction"] && !in_array($hp["name"], array("mijn documenten", "hrm"))) {
					$row["xs_edit"] = 1;
				}
			}

			/* some special icons */
			if ($row["parent_id"]==0) {
				switch ($row["name"]) {
					case "mijn documenten":
						$row["foldericon"] = "folder_my_docs";
						$row["h_name"] = gettext($row["name"]);
						break;
					case "projecten":
						if (!$user_info["xs_projectmanage"]) {
							$xs = "H"; // H = hide
						}
						$row["foldericon"] = "folder_project";
						$row["h_name"] = gettext($row["name"]);
						break;
					case "relaties":
						$row["foldericon"] = "folder_relation";
						$row["h_name"] = gettext($row["name"]);
						break;
					case "hrm":
						if (!$user_info["xs_hrmmanage"]) {
							$xs = "H"; // H = hide
						}
						$row["foldericon"] = "folder_hrm";
						$row["h_name"] = gettext($row["name"]);
						break;
					default:
						$row["foldericon"] = "folder_closed";
						if (!$row["parent_id"]) {
							$row["h_name"] = gettext($row["name"]);
						} else {
							$row["h_name"] = $row["name"];
						}
						break;
				}
			} else {
				if ($row["p_permissions"]) {
					$row["foldericon"] = "folder_lock";
				} else {
					$row["foldericon"] = "folder_closed";
				}
				$row["h_name"] = $row["name"];
			}

			/* if pastebuffer is active, do not allow the folder that is being processed */
			if (preg_match("/^folder/s", $_REQUEST["pastebuffer"])) {
				$pastefolder = preg_replace("/^folder,/s", "", $_REQUEST["pastebuffer"]);
			}

			/* if the pastebuffer is the current folder then allow no actions on this folder */
			if ($pastefolder == $row["id"]) {
				$row["disallow"] = 1;
				$row["foldericon"] = "cut";
			} elseif ($xs != "D") {
				$row["allow"] = 1;
			} else {
				$row["disallow"] = 1;
				$row["foldericon"] = "folder_denied";
			}

			if ($xs == "W" && $row["parent_id"]>0) {
				/* additional relation or projects check */
				if ($hp["name"]=="relaties") {
					$q = sprintf("select count(*) from address where id = %d", $row["address_id"]);
					$res2 = sql_query($q);
					if (sql_result($res2,0)>0) {
						$row["xs_folder_actions"] = 0;
					} else {
						$row["xs_folder_actions"] = 1;
					}
				} elseif ($hp["name"]=="projecten") {
					$q = sprintf("select count(*) from project where id = %d", $row["project_id"]);
					$res2 = sql_query($q);
					if (sql_result($res2,0)>0) {
						$row["xs_folder_actions"] = 0;
					} else {
						$row["xs_folder_actions"] = 1;
					}
				} else {
					$row["xs_folder_actions"] = 1;
				}
			}
			/* if pastebuffer is active, do not allow folder modifications */
			if ($_REQUEST["pastebuffer"] || $_REQUEST["subaction"]) {
				$row["xs_folder_actions"] = 0;
			}

			/* if we have a subaction, do not allow updates on the view */
			if ($_REQUEST["subaction"] && $row["xs"]!="D") {
				$row["xs_subaction"] = $row["xs"];
				$row["xs"] = "R";
			}

			if ($downgrade_permissions && ($row["xs"]=="W" || $row["xs"]=="S")) {
				$row["xs"] = "R";
			}

			if ($xs != "H") {
				return $row;
			} else {
				return false;
			}
	}

	/* {{{ getFiles($settings) */
	/**
	 * get file-structure from db
	 *
	 * @param array the file settings
	 * @return array the structure
	 */
	public function getFiles($settings = array()) {

		if (!$settings["sort"]) {
			$settings["sort"] = "upper(name) asc";
		} else {
			$settings["sort"] = sql_filter_col($settings["sort"]);
		}

		/* if no folderid, get folderid of my docs */
		if (!$settings["folderid"]) {
			$sql = sprintf("SELECT id FROM filesys_folders WHERE user_id=%d AND name='mijn documenten' AND parent_id=0", $_SESSION["user_id"]);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			$settings["folderid"] = $row["id"];
		}
		$sql = sprintf("SELECT filesys_files.*, users.username FROM filesys_files LEFT JOIN users ON users.id = filesys_files.user_id WHERE folder_id=%d ORDER BY %s", $settings["folderid"], $settings["sort"]);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$row["size_human"] = $this->parseSize($row["size"]);
			if ($row["timestamp"]) {
				$row["date_human"] = date("d-m-Y H:i", $row["timestamp"]);
			} else {
				$row["date_human"] = "---";
			}
			$row["user_name"]  =& $row["username"];

			if (!$settings["no_xs"]) {
				$row["show_actions"] = 1;
			} else {
				$row["show_actions"] = 0;
			}
			$row["fileicon"] = $this->getFileType($row["name"]);
			if ($_REQUEST["subaction"] == "add_attachment") {
				$row["attachment"] = 1;
			}

			$return[] = $this->detect_preview($row);
		}
		return $return;
	}
	/* }}} */

	public function getFileType($name) {
		$name = strtolower($name);
		$name = explode(".", $name);
		$ext  = $name[count($name)-1];

		$types = array(
			"txt"  => "ftype_text",
			"xml"  => "ftype_text",
			"rtf"  => "ftype_doc",
			"doc"  => "ftype_doc",
			"odt"  => "ftype_doc",
			"sxw"  => "ftype_doc",
			"htm"  => "ftype_html",
			"html" => "ftype_html",
			"xls"  => "ftype_calc",
			"ods"  => "ftype_calc",
			"sxc"  => "ftype_calc",
			"pdf"  => "ftype_pdf",
			"gif"  => "ftype_image",
			"bmp"  => "ftype_image",
			"png"  => "ftype_image",
			"jpg"  => "ftype_image",
			"jpeg" => "ftype_image",
			"wav"  => "ftype_sound",
			"midi" => "ftype_sound",
			"mp3"  => "ftype_sound",
			"ogg"  => "ftype_sound"
		);

		if ($name == "headers.txt") {
			$t = "ftype_rfc822";
		} else {
			$t = $types[$ext];
			if (!$t) {
				$t = "ftype_binary";
			}
		}
		return $t;
	}

	public function getBindataById($id, $ext) {
		/* open file */
		$file = $GLOBALS["covide"]->filesyspath."/bestanden/".$id.".".$ext;
		$fp = fopen($file, "r");
		$content = fread($fp, filesize($file));
		return $content;
	}

	public function deleteBindataById($id) {
		$q = sprintf("select name from filesys_files where id = %d", $id);
		$res = sql_query($q);
		$name = sql_result($res,0);

		$ext = $this->get_extension($name);
		$file = $GLOBALS["covide"]->filesyspath."/bestanden/".$id.".".$ext;
		@unlink($file);
		return true;
	}

	/* {{{ getFileById($id) */
	/**
	 * get file
	 *
	 * @param array the file id
	 * @return array the structure
	 */
	public function getFileById($id, $skip_binary=0) {
		$sql = sprintf("SELECT * FROM filesys_files WHERE id=%d", $id);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$return = $row;
		/* get the data from fs */

		$ext = $this->get_extension($row["name"]);

		if (!$skip_binary) {
			$fspath = $GLOBALS["covide"]->filesyspath;
			$fspath.= "/bestanden/".$id.".".$ext;
			$return["fsize"]  = filesize($fspath);

			$filedata = $this->getBindataById($id, $ext);
			$return["binary"] = $filedata;
		}
		return $return;
	}
	/* }}} */

	public function file_remove($fileid, $folderid, $skip_redir=0) {
		$this->deleteBindataById($fileid);
		$sql = sprintf("DELETE FROM filesys_files WHERE id=%d AND folder_id=%d", $fileid, $folderid);
		$res = sql_query($sql);
		if (!$skip_redir) {
			header("Location: index.php?mod=filesys&action=opendir&id=$folderid");
		}
	}

	public function file_remove_multi() {
		$files  = $_REQUEST["checkbox_file"];
		$folder = $_REQUEST["id"];

		if (is_array($files) && $folder) {
			foreach ($files as $k=>$v) {
				$this->file_remove($k, $folder, 1);
			}
		}
		header("Location: index.php?mod=filesys&action=opendir&id=$folder");
	}

	public function file_download($id=0) {
		/* get file data */
		if (!$id) {
			$error = 1;
			exit();
		} else {
			$file = $this->getFileById($id);

			header("Content-Transfer-Encoding: binary");
			header("Content-Type: ".strtolower($file["type"]));
			#header("Content-Length: ".$file["fsize"]);

			if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) {
				header("Content-Disposition: filename=\"".$file["name"]."\"");
			} else {
				header("Content-Disposition: attachment; filename=\"".$file["name"]."\"");
			}
			echo($file["binary"]);
			exit();
		}

		/* there was an error, show popup */
		if ($error) {
			$output = new Layout_output();
			$output->start_avascript();
			$output->addCode("alert('".$error."');");
			$output->end_javascript();
			$output->exit_buffer();
		}
	}


	public function multi_download_zip() {
		$folder = $_REQUEST["id"];

		$ids = explode(",",$_REQUEST["ids"]);

		$zipfile = new Covide_zipfile();
		// add the subdirectory ... important!
		$zipfile->add_dir("covide/");

		foreach ($ids as $k=>$v) {
			$file = $this->getFileById($v);
			$zipfile->add_file(&$file["binary"], sprintf("covide/%s", $file["name"]));
			unset($file);
		}

		$data = $zipfile->file();
		unset($zipfile);

		$fname = "covide.zip";

		header('Content-Transfer-Encoding: binary');
		header('Content-Type: application/zip');
		#header("Content-Length: ".strlen($data));


		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename="'.$fname.'"'); //msie 5.5 header bug
		} else {
			header('Content-Disposition: attachment; filename="'.$fname.'"');
		}

		echo $data;
		exit();
	}


	public function file_edit_save($formdata) {
		$sql = sprintf("UPDATE filesys_files SET description='%s', timestamp=".mktime().", user_id=%d WHERE id=%d AND folder_id=%d", $formdata["fedit"]["description"], $_SESSION["user_id"], $formdata["fileid"], $formdata["folderid"]);
		$res = sql_query($sql);
		$output = new Layout_output();
		$output->layout_page("", 1);
		$output->start_javascript();
			$output->addCode("
				opener.document.location.href = opener.document.location.href;
				window.close();
			");
		$output->end_javascript();
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function file_upload($data) {
		require(self::include_dir."datafile_upload.php");
	}

	public function create_dir($dirdata) {
		$sql  = "INSERT INTO filesys_folders (name, description, user_id, parent_id) VALUES ";
		$sql .= sprintf("('%s', '%s', %d, %d)", $dirdata["folder"]["name"], $dirdata["folder"]["description"], $_SESSION["user_id"], $dirdata["id"]);
		sql_query($sql);
		header("Location: index.php?mod=filesys&action=opendir&id=".$dirdata["id"]);
	}


	public function getHighestParent($folder) {
		$id = $folder;
		while ($id > 0) {
			$id = $this->getParentFolder($id);
			if ($id > 0) {
				$folder = $id;
			}
		}
		$name = $this->getFoldernameById($folder);

		return array("id" => $folder, "name" => $name);
	}

	public function getFolderPermissions($folder, $tree="") {
		/* first check folder specific permissions */
		$q = "select * from filesys_permissions where folder_id = ".$folder;
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		if ($row["permissions"]) {
			$row["user_id"]     = str_replace("|", ",", $row["user_id"]);
			$row["permissions"] = str_replace("|", ",", $row["permissions"]);

			$users = explode(",", $row["user_id"]);
			$perm  = explode(",", $row["permissions"]);

			foreach ($users as $k=>$v) {
				$ary[$v] = $perm[$k];
			}

			/* if a matching array key position is found */
			$permissions = $ary[$_SESSION["user_id"]];

			/* if no permissions are found for this user */
			if (!$permissions) {
				$permissions = "D";
			}
		}

		/* if no permissions are found, check for account manager permissions */
		if ($tree == "relations") {
			$q = "select address.account_manager from filesys_folders left join address on address.id = filesys_folders.address_id where filesys_folders.id = ".$folder;
			$res = sql_query($q);
			$accmanager = sql_result($res,0);

			if ($accmanager == $_SESSION["user_id"]) {
				/* if the account manager if me */
				$permissions = "W";
			} elseif ($accmanager > 0) {
				$user = new User_data();
				$arr = $user->getUserPermissionsById($accmanager);
				$arr = explode(",", $arr["addressaccountmanage"]);

				/* if i am in the allowed list of this accountmanager */
				if (in_array($_SESSION["user_id"], $arr)) {
					$permissions = "W";
				}
			}
		}

		/* projects */
		if ($tree == "projects") {
			/* check for subproject or standalone project */
			$q = "select projects_master.manager as globalmanager, project.manager as manager from filesys_folders left join project on project.id = filesys_folders.project_id ";
			$q.= "left join projects_master on projects_master.id = project.group_id where filesys_folders.id = ".$folder;
			$res = sql_query($q);
			$row = sql_fetch_assoc($res);

			if ($row["globalmanager"] == $_SESSION["user_id"] || $row["manager"] == $_SESSION["user_id"]) {
				$permissions = "W";
			}
		}
		return $permissions;
	}

	public function checkPermissions($folder, $tree="") {
		$id = $folder;
		$permissions = "";

		while ($id > 0 && !$permissions) {
			$permissions = $this->getFolderPermissions($id, $tree);
			$id = $this->getParentFolder($id);
		}

		return $permissions;
	}

	public function getFolderInfo($folder) {
		$q = "select * from filesys_folders where id = ".$folder;
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		return $row;
	}

	public function checkForRelation($folder) {
		$id = $folder;
		$relation = "";
		while ($id > 0 && !$relation) {
			$data = $this->getFolderInfo($id);
			if ($data["address_id"]) {
				$relation = $data["address_id"];
			}
			$id = $this->getParentFolder($id);
		}
		return $relation;
	}

	public function checkForProject($folder) {
		$id = $folder;
		$project = "";
		while ($id > 0 && !$project) {
			$data = $this->getFolderInfo($id);
			if ($data["project_id"]) {
				$project = $data["project_id"];
			}
			$id = $this->getParentFolder($id);
		}
		return $project;
	}

	public function retrieveFullPermissions($folder) {
		$q = "select count(*) from filesys_permissions where folder_id = ".$folder;
		$res = sql_query($q);
		$found = sql_result($res,0);

		if ($found==0) {
			$parent = $this->getParentFolder($folder);
			if ($parent==0) {
				return array("0","R");
			}
			$permissions = $this->retrieveFullPermissions($parent);
		} else {
			$q = "select user_id, permissions from filesys_permissions where folder_id = ".$folder;
			$res = sql_query($q);
			$row = sql_fetch_array($res);
			$permissions = array($row["user_id"], $row["permissions"]);
		}
		return $permissions;
	}

	public function modifyPermissionArray($r, $userid, $permissions) {
		$r_user        = explode("|",$r[0]);
		$r_permissions = explode("|",$r[1]);

		foreach ($r_user as $k=>$v) {
			if (!$v) {
				unset($r_user[$k]);
				unset($r_permissions[$k]);
			}
			if ($userid==$v) {
				unset($r_user[$k]);
				unset($r_permissions[$k]);
			}
		}
		if ($permissions != "D") {
			$r_user[]        = $userid;
			$r_permissions[] = $permissions;
		}

		$r[0] = implode("|", $r_user);
		$r[1] = implode("|", $r_permissions);

		return ($r);
	}

	public function updatePermissionsDb($folder, $r) {
		$q = "update filesys_permissions set user_id = '".$r[0]."', permissions = '".$r[1]."' where folder_id = $folder";
		sql_query($q);
	}

	public function getFolderArrayRecursive($folder, $folders, $level=1) {
		$q = "select * from filesys_folders where parent_id = $folder order by name";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$permissions = $this->checkPermissions($row["id"]);

			/* if write permissions go recursive */
			if ($permissions == "W") {
				$this->getFolderArrayRecursive($row["id"], &$folders, $level+1);
			}

			$row["level"] = $level;
			$output = new Layout_output();
			for ($i=0;$i<$level;$i++) {
				$output->insertAction("tree", "", "");
			}
			$row["spacing"] = $output->generate_output();
			unset($output);

			/* if no permissions are found, upgrade to max */
			if (!$permissions) {
				$permissions = "W";
			}
			if ($permissions == "W") {
				$row["foldericon"] = "folder_open";
			} else {
				$row["foldericon"] = "folder_denied";
			}

			$row["permissions"] = $permissions;
			$folders[] = $row;
		}
	}
	public function getFolderArray($folder) {
		$folders = array();
		$q = "select * from filesys_folders where id = ".$folder;
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		$row["foldericon"] = "folder_open";
		$row["permissions"] = "W";

		$folders[]=$row;

		$this->getFolderArrayRecursive($folder, &$folders);
		return $folders;
	}

	public function deleteFolderExec() {
		/* just another security check to be sure */
		$folders = $this->getFolderArray($_REQUEST["id"]);
		foreach ($folders as $v) {
			if ($v["permissions"] != "W") {
				die("error occured, no valid permissions");
			}
		}

		foreach ($folders as $folder) {
			$files = $this->getFiles(array("folderid"=>$folder["id"]));
			foreach ($files as $file) {
				$this->file_remove($file["id"], $folder["id"], 1);
			}
			$q = "delete from filesys_folders where id = ".$folder["id"];
			sql_query($q);
		}

		$output = new Layout_output();
		$output->start_javascript();
		$output->addCode("
			opener.document.getElementById('velden').submit(); window.close();
		");
		$output->end_javascript();
		$output->exit_buffer();
	}

	/* cut n paste */
	public function getFoldersByArray($ids) {
		$data = array();
		if (count($ids)>0) {
			$ids = implode(",", $ids);
			$q = sprintf("select * from filesys_folders where id IN (%s)", $ids);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$row["fileicon"] = "folder_open";
				$data[]=$row;
			}
		}
		return $data;
	}
	public function getFilesByArray($ids) {
		$data = array();
		if (count($ids)>0) {
			$ids = implode(",", $ids);
			$q = sprintf("select * from filesys_files where id IN (%s)", $ids);
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$row["fileicon"] = $this->getFileType($row["name"]);
				$data[]=$row;
			}
		}
		return $data;
	}

	public function pasteExec() {
		$target_folder = $_REQUEST["id"];
		$pastebuffer = $_REQUEST["pastebuffer"];

		if (preg_match("/^folder/s", $pastebuffer)) {
			/* process folders */
			$folder = preg_replace("/folder,/s", "", $pastebuffer);
			$q = sprintf("update filesys_folders set parent_id = %d where id = %d", $target_folder, $folder);
			sql_query($q);

		} else {
			/* process files */
			$files = preg_replace("/file,/s", "", $pastebuffer);
			if ($files) {
				$q = sprintf("update filesys_files set folder_id = %d where id IN (%s)", $target_folder, $files);
				sql_query($q);
			}
		}
		header("Location: index.php?mod=filesys&action=opendir&id=$target_folder");
	}

	public function save_attachment() {
		$folder = $_REQUEST["id"];
		$ids    = explode(",", $_REQUEST["ids"]);

		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir_source  = "email";
		$fsdir_target  = "bestanden";

		$email = new Email_data();

		foreach ($ids as $id) {


			$file = $email->getAttachment($id, 1);

			/* gather some file info */
			$name = addslashes($file["name"]);
			$type = addslashes($file["type"]);
			$size = addslashes($file["size"]);

			/* insert file into dbase */
			$q = "insert into filesys_files (folder_id, name, size, type, timestamp, user_id, description) values ";
			$q.= sprintf("(%d, '%s', '%s', '%s', %d, %d, '%s')", $folder, $name, $size, $type, mktime(), $_SESSION["user_id"], "");
			sql_query($q);
			$new_id = sql_insert_id("filesys_files");

			/* move data to the destination */
			$ext = $this->get_extension($file["name"]);

			$source = sprintf("%s/%s/%s.%s", $fspath, $fsdir_source, $file["id"], $ext);
			$destination = sprintf("%s/%s/%s.%s", $fspath, $fsdir_target, $new_id, $ext);

			@copy($source, $destination);

		}

	}

    /* 	save_fax {{{ */
    /**
     * 	save_fax. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function save_fax() {
		$folder = $_REQUEST["id"];
		$ids    = explode(",", $_REQUEST["ids"]);
		$description = $_REQUEST["description"];

		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir_source  = "faxes";
		$fsdir_target  = "bestanden";

		$voipdata = new Voip_data();

		foreach ($ids as $id) {


			$file = $voipdata->getFaxFromFS($id);

			/* gather some file info */
			$name = addslashes($file["name"]);
			$type = "application/pdf";
			$size = addslashes($file["size"]);

			/* insert file into dbase */
			$q = "insert into filesys_files (folder_id, name, size, type, timestamp, user_id, description) values ";
			$q.= sprintf("(%d, '%s', '%s', '%s', %d, %d, '%s')", $folder, $name, $size, $type, mktime(), $_SESSION["user_id"], $description);
			sql_query($q);
			$new_id = sql_insert_id("filesys_files");

			/* write binary data to disk */
			/* We cannot do a copy here, because we want to write a pdf and source is sff */

			$destination = sprintf("%s/%s/%s.pdf", $fspath, $fsdir_target, $new_id);
			$fp = fopen($destination, "wb");
			fwrite($fp, $file["bindata"]);
			fclose($fp);
			$voipdata->deleteFax($id, 0);
		}
	}
  /* }}} */

  public function searchAll($str, $max_hits="") {
  	/* prepare return variable */
  	$data = array(
  		"files"   => array(),
  		"folders" => array()
  	);

  	/* cache */
  	$folder_permissions = array();

  	/* extract permissions for this user */
		$user_data = new User_data();
		$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);

  	/* get syntax */
  	$like = sql_syntax("like");
  	//$buf  = sql_syntax("buffer");

  	/* search all folders */
  	$q = sprintf("select $buf * from filesys_folders where name %1\$s '%%%2\$s%%' or description %1\$s '%%%2\$s%%' order by name", $like, $str);
  	if ($max_hits) {
  		$res = sql_query($q, "", 0, $max_hits);
  	} else {
  		$res = sql_query($q);
  	}
  	while ($row = sql_fetch_assoc($res)) {
			$row = $this->extractPermissions($row, &$user_info, 1);
			if ($row["parent_id"] == 0 && $row["name"] == "mijn documenten") {
				if ($row["user_id"] == $_SESSION["user_id"]) {
					$data["folders"][$row["id"]] = $row;
					$folder_permissions[$row["id"]]["access"] = "R";
					$folder_permissions[$row["id"]]["name"]   = $row["name"];
				} else {
					$folder_permissions[$row["id"]]["access"] = "D";
					$folder_permissions[$row["id"]]["name"]   = $row["name"];
				}
			} elseif ($row["xs"] == "R" || $row["xs"] == "W" || $row["xs"] == "S") {
				$data["folders"][$row["id"]] = $row;
				$folder_permissions[$row["id"]]["access"] = "R";
				$folder_permissions[$row["id"]]["name"]   = $row["name"];
			} else {
				$folder_permissions[$row["id"]]["access"] = "D";
				$folder_permissions[$row["id"]]["name"]   = $row["name"];
			}
  	}

 		$user_data = new User_data();
		$users = $user_data->getUserlist();

  	/* search all files */
  	$q = sprintf("select $buf * from filesys_files where name %1\$s '%%%2\$s%%' or description %1\$s '%%%2\$s%%' order by folder_id, name", $like, $str);
  	if ($max_hits) {
  		$res = sql_query($q, "", 0, $max_hits);
  	} else {
  		$res = sql_query($q);
  	}
  	while ($row = sql_fetch_assoc($res)) {
  		/* check against cache */
  		if (!$folder_permissions[$row["folder_id"]]) {
  			$q2 = "select * from filesys_folders where id = ".$row["folder_id"];
  			$res2 = sql_query($q2);
  			$row2 = sql_fetch_assoc($res2);

  			$row2 = $this->extractPermissions($row2, &$user_info, 1);
  			/* add to cache */
  			if ($row2["parent_id"] == 0 && $row2["name"] == "mijn documenten") {
  				if ($row2["user_id"] == $_SESSION["user_id"]) {
	  				$folder_permissions[$row["folder_id"]]["access"] = "R";
  					$folder_permissions[$row["folder_id"]]["name"]   = $row2["name"];
  				} else {
	  				$folder_permissions[$row["folder_id"]]["access"] = "D";
  					$folder_permissions[$row["folder_id"]]["name"]   = $row2["name"];
  				}
  			} elseif ($row2["xs"] == "R" || $row2["xs"] == "W" || $row2["xs"] == "S") {
  				$folder_permissions[$row["folder_id"]]["access"] = "R";
  				$folder_permissions[$row["folder_id"]]["name"]   = $row2["name"];
  			} else {
	  			$folder_permissions[$row["folder_id"]]["access"] = "D";
  				$folder_permissions[$row["folder_id"]]["name"]   = $row2["name"];
  			}
  		}
  		/* lookup in cache */
  		if ($folder_permissions[$row["folder_id"]]["access"] == "R") {
				$row["size_human"] = $this->parseSize($row["size"]);
				if ($row["timestamp"]) {
					$row["date_human"] = date("d-m-Y H:i", $row["timestamp"]);
				} else {
					$row["date_human"] = "---";
				}
				$row["user_name"]   = $users[$row["user_id"]];
				$row["folder_name"] = $folder_permissions[$row["folder_id"]]["name"];

  			$row["fileicon"] = $this->getFileType($row["name"]);
  			$data["files"][$row["id"]] = $row;
  		}
  	}
  	return $data;
  }

  public function get_extension($file) {

 		$conversion = new Layout_conversion();
		$file = $conversion->decodeMimeString($file);

 		$file = basename($file);
 		$file = explode(".", $file);
 		$ext = strtolower( $file[count($file)-1] );
 		if ($ext == "html") $ext = "htm";
 		if (strlen($ext)!=3) {
 			$ext = "dat";
 		}
 		return $ext;
  }

  public function rename_filetype($id, $ftype1, $ftype2, $store) {
  	if ($store == "files") {
  		$folder = $GLOBALS["covide"]->filesyspath."/bestanden/";
  	} elseif ($store == "email") {
  		$folder = $GLOBALS["covide"]->filesyspath."/email/";
  	} else {
  		die("not a valid store");
  	}

  	$src  = sprintf("%s%d.%s", $folder, $id, $ftype1);
  	$dest = sprintf("%s%d.%s", $folder, $id, $ftype2);

  	echo "$src => $dest <br>";
  	if (file_exists($src)) {
  		rename($src, $dest);
  	}
  }

  public function checkFilePermissions($id) {
		$xs = 0;

		/* extract permissions for this user */
		if (!$this->_cache["userinfo"]) {
			$user_data = new User_data();
			$this->_cache["userinfo"] = $user_data->getUserdetailsById($_SESSION["user_id"]);
		}
		$user_info = $this->_cache["userinfo"];

		$q = "select folder_id, name, size, timestamp, id as file_id from filesys_files where id = ".$id;
		$res2 = sql_query($q);
		$row2 = sql_fetch_assoc($res2);
		$folder = $row2["folder_id"];

		/* now get the basic folder permissions */
		$q = sprintf("select * from filesys_folders where id = %d", $folder);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$row = $this->extractPermissions($row, &$user_info);
		if ($row["xs"] == "W" || $row["xs"] == "R" || $row["xs"] == "S") {
			$row["file_id"]   = $row2["file_id"];
			$row["file_name"] = $row2["name"];
			$row["file_size"] = $row2["size"];
			$row["file_date"] = date("d-m-Y", $row2["timestamp"]);

			$row["file_icon"] = $this->getFileType($row2["name"]);
			$xs = $row;
		}
		return $row;
	}

	public function file_preview_header() {
		$output = new Layout_output();
		$output->layout_page("preview", 1);
			$output->addTag("div", array("align"=>"right"));
			$output->addTag("b");
			$output->addCode( gettext("bestandsnaam").": [");
			$output->addCode($_REQUEST["file"]."] ");
			$output->endTag("b");
			$output->addSpace(10);
			$output->insertAction("print", gettext("afdrukken"), "javascript: parent.preview_main.print();");
			$output->addSpace(2);
			$output->insertAction("close", gettext("venster sluiten"), "javascript: parent.window.close();");
			$output->endTag("div");
		$output->layout_page_end();
		$output->exit_buffer();
	}

	/* file_preview_readfile {{{ */
	/**
	 * Send binary filedata to browser
	 *
	 * @param string The filename to open
	 * @param string ?
	 */
	public function file_preview_readfile($file, $altname="") {
		$tmp = $GLOBALS["covide"]->temppath;

		$file = $tmp.$file;
		$altfile = $tmp.$altname;

		if (file_exists($altname)) {
			$file = $altname;
		}
		$mime = $this->detectMimetype($file);

		header("Content-Transfer-Encoding: binary");
		header("Content-Type: ".strtolower($mime));

		readfile($file);
		@unlink($file);

		exit();
	}
	/* }}} */

	/* file_preview {{{ */
	/**
	 * Show file preview
	 *
	 * @param array The database record of a file
	 */
	public function file_preview($file) {

		$ext = $this->get_extension($file["name"]);

		$input = sprintf("%s/%s/%s.%s", $GLOBALS["covide"]->filesyspath, $file["module"], $file["id"], $ext);
		$tmpfile = $GLOBALS["covide"]->temppath."preview_".md5(rand()*mktime());

		switch ($file["subtype"]) {
			case "msword";
				$cmd = sprintf("wvHtml %s %s", $input, $tmpfile);
				$read_multi_file = 1;
				break;
			case "pdf":
				$cmd = sprintf("cd %s && pdftohtml %s %s", $GLOBALS["covide"]->temppath, $input, basename($tmpfile));
				$read_multi_file = 1;
				break;
			case "openoffice":
				$cmd = sprintf("unzip -p %s content.xml | o3tohtml | utf8tolatin1 > %s", $input, $tmpfile);
				break;
			case "msexcel":
				$cmd = sprintf("xlhtml %s > %s", $input, $tmpfile);
				break;
			case "rtf":
				$cmd = sprintf("unrtf %s > %s", $input, $tmpfile);
				break;
			case "text":
			case "html":
			case "csv":
				$cmd = sprintf("cp %s %s", $input, $tmpfile);
				break;
		}

		if ($cmd) {
			/* exec the command */
			exec($cmd, $ret);

			$conversion = new Layout_conversion();

			/* a default style for all html pages */
			$style = "<style type='text/css'>body, td, a, div, p, span { font-family: arial,serif; font-size: 12px; color: black; } </style>";

			if ($read_multi_file) {
				$files = array();
				$cmd = sprintf("ls %s*", $tmpfile);
				exec($cmd, $files, $retval);

				foreach ($files as $datafile) {
					$ext = $this->get_extension(basename($datafile));

					if ($ext != "gif" && $ext != "htm" && !$ext && $ext != "png") {
						/* not viewable */
						@unlink($datafile);
					}
					if ($ext == "htm" || $datafile == $tmpfile) {
						/* open the html file */
						if (filesize($datafile) > 0) {
							$handle = fopen($datafile, "r");
							$data = fread($handle, filesize($datafile));
							fclose($handle);
						} else {
							$data = "<html><body>file could not be opened - unknown format.</body></html>";
						}

						/* insert css styles */
						$data = preg_replace("/(<body[^>]*?>)/si", $style."$1", $data);
						$data = $conversion->utf8_convert($data);

						/* replace file links */
						foreach ($files as $f) {
							$f = basename($f);
							$ext = $this->get_extension(basename($f));

							if ($ext == "gif" || $ext == "htm" || $ext == "png") {
								$data = str_replace($f, "?mod=filesys&action=getPreviewFile&dl=1&file=".$f, $data);
							} else {
								$data = str_replace($f, "", $data);
							}
						}

						$data = $conversion->utf8_convert($data);

						$out = fopen($datafile, "w");
						fwrite($out, $data);
						fclose($out);
					}
				}

				if ($file["subtype"]=="pdf") {
					$tmpfile.=".html";
				}
				/* use an frameset */
				$output = new Layout_output();
				$output->addCode("
					<html>
						<head><title>Covide File Preview</title></head>
					<frameset  ROWS='30,*' frameborder='0'>
							<frame frameborder='0' noresize scrolling='no' SRC='?mod=filesys&action=preview_header&file=".$file["name"]."' NAME='preview_top'>
							<frame frameborder='0' noresize scrolling='yes' SRC='?mod=filesys&action=getPreviewFile&file=".basename($tmpfile)."' NAME='preview_main'>
					</frameset>
					</html>
				");
				$output->exit_buffer();
				//$this->file_preview_readfile(basename($tmpfile));
			} else {
				/* open the html file */
				$handle = fopen($tmpfile, "r");
				$data = fread($handle, filesize($tmpfile));
				fclose($handle);

				/* if subtype = text | csv */
				if ($file["subtype"] == "csv") {
					/* replace all data var by pointers to new array */
					preg_match_all("/\"[^\"]*?\"/si", $data, $matches);
					$matches = $matches[0];
					$matches = array_unique($matches);
					foreach ($matches as $k=>$v) {
						$data = str_replace($v, "##$k", $data);
						$matches[$k] = substr($v, 1, strlen($v)-2);
					}
					$data = str_replace(";", ",", $data);
					$data = explode("\n", $data);

					$html = "<html><body><table border='1'>";
					foreach ($data as $line) {
						$line = explode(",", $line);
						$html.= "<TR>";
						foreach ($line as $field) {
							if (preg_match("/##\d{1,}/s", $field)) {
								$field = str_replace("##", "", $field);
								$field = $matches[ $field ];
							}
							$html.= sprintf("<TD>%s</TD>", $field);
						}
						$html.= "</TR>";
					}
					$html.= "</table></body></html>";
					$data = $html;
				}
				if ($file["subtype"] == "text" || $file["subtype"] == "csv") {
					$data = "<html><body>".str_replace("\n", "<br>\n", $data)."</body></html>";
				}

				/* insert css styles */
				$data = preg_replace("/(<body[^>]*?>)/si", $style."$1", $data);

				$data = $conversion->utf8_convert($data);

				$out = fopen($tmpfile, "w");
				fwrite($out, $data);
				fclose($out);

				/* use an frameset */
				$output = new Layout_output();
				$output->addCode("
					<html>
						<head><title>Covide File Preview</title></head>
					<frameset  ROWS='30,*' frameborder='0'>
							<frame frameborder='0' noresize scrolling='no' SRC='?mod=filesys&action=preview_header&file=".$file["name"]."' NAME='preview_top'>
							<frame frameborder='0' noresize scrolling='yes' SRC='?mod=filesys&action=getPreviewFile&file=".basename($tmpfile)."' NAME='preview_main'>
					</frameset>
					</html>
				");
				$output->exit_buffer();
			}
			exit();
		}
	}
	/* }}} */

	/* detect_preview {{{ */
	/**
	 * Detect wether we can generate a preview of the given file info.
	 *
	 * @param array The database record of a file
	 * @return array The same array as the input, with subtype and subview added if we can preview it
	 */
	public function detect_preview($row) {
		$ext = $this->get_extension($row["name"]);

		switch (trim(strtolower($row["type"]))) {
			case "image/gif":
			case "image/jpeg":
			case "image/pjpeg":
				$row["subtype"] = "image";
				$row["subview"] = 1;
				break;
			case "text/plain":
			case "text/x-vcard":
				$row["subtype"] = "text";
				$row["subview"] = 1;
				break;
			case "text/html":
				$row["subtype"] = "html";
				$row["subview"] = 1;
				break;
			case "application/msword":
				$row["subtype"] = "msword";
				$row["subview"] = 1;
				break;
			case "application/pdf":
				$row["subtype"] = "pdf";
				$row["subview"] = 1;
				break;
			case "text/rtf":
				$row["subtype"] = "rtf";
				$row["subview"] = 1;
				break;
			case "application/vnd.ms-excel":
			case "text/x-comma-separated-values": //see detection correction later
				$row["subtype"] = "msexcel";
				$row["subview"] = 1;
				break;
			default:
				$row["subtype"] = "binary";
				break;
		}
		/* undetectable with 'file' by mime type */
		if (!$row["subview"]) {
			switch ($ext) {
				case "xls":
					$row["subtype"] = "msexcel";
					$row["subview"] = 1;
					break;
				case "odt":
				case "sxw":
					$row["subtype"] = "openoffice";
					$row["subview"] = 1;
					break;
				case "txt":
					$row["subtype"] = "text";
					$row["subview"] = 1;
			}
		}
		/* detect csv type */
		if ($row["subtype"] == "text" || $row["subtype"] == "msexcel") {
			if ($ext == "csv") {
				$row["subtype"] = "csv";
			}
		}
		/* maybe wrong detection */
		return $row;
	}
	/* }}} */
}
?>
