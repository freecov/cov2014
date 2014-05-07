<?php
/**
 * Covide Groupware-CRM Filesys module
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
Class Filesys_data {

	/* constants */
	const include_dir = "classes/filesys/inc/";
	const class_name = "filesys_data";

	/* variables */
	public $mapSize = 3;
	public $pagesize = 20;
	private $_cache;

	/* methods */
    /* __construct {{{ */
	public function __construct() {
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}
	/* }}} */
	/* detectMimetype {{{ */
	/**
	 * Return the mimetype of given file.
	 *
	 * @param string $file The full path+filename to get the mimetype from
	 * @return string The mimetype as detected by the unix commandline tool 'file -i' or 'file -iz' when the .gz extension is attached to the sourcefile
	 */
	public function detectMimetype($file, $nocheck=0) {

		$new_file = $this->FS_calculatePath($file);
		if (file_exists($new_file) || file_exists($new_file.".gz"))
			$file = $new_file;

		/* file can be .gz compressed */
		$gzip_found = 0;
		if (file_exists($file.".gz") && !$nocheck) {
			$gzip_found = 1;
			$cmd = sprintf("file -iz %s", escapeshellarg($file.".gz"));
		} else {
			$cmd = sprintf("file -i %s", escapeshellarg($file));
		}

		exec ($cmd, $ret, $status);
		$ret = explode(": ", $ret[0]);
		$ret = strtoupper(preg_replace("/\;.*$/s","",$ret[1]));
		if ($gzip_found == 1)
			$ret = str_replace(" (APPLICATION/OCTET-STREAM)", "", $ret);

		if (trim(strtolower($ret)) == "error")
			$ret = "application/octet-stream";

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
	 * @param string $file the file data to be compressed
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
	 * @param string $file the file data to be uncompressed
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
	 * check if the folder exists. if not, create it
	 *
	 * @param array $settings the folder settings. At least the following keys should be present: name, parent_id and optional: user_id, is_public, sticky, is_relation, address_id, is_shared, hrm_id, project_id
	 * @return bool true on success, false on fail
	 */
	public function check_folder($settings) {
		$sql  = "SELECT id FROM filesys_folders WHERE ";
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
		if (array_key_exists("address_id", $settings) && (!(array_key_exists("project_id", $settings)) || $settings["project_id"] == 0 )) {
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
		$count = sql_num_rows($res);
		if (!$count) {
			$q = "INSERT INTO filesys_folders ($fields) VALUES ($values)";
			$r = sql_query($q);
			if (!$r)
				return false;
			else
				$folderid = sql_insert_id("filesys_folders");
		} else {
			$row = sql_fetch_assoc($res);
			$folderid = $row["id"];
			//XXX need to fix this in the list above.
			//due to a bug there (noted) relation folders never get the addressid set
			//so we set it here if needed
			if (array_key_exists("address_id", $settings) && $settings["address_id"] > 0) {
				$q = sprintf("UPDATE filesys_folders SET address_id = %d WHERE id = %d", $settings["address_id"], $folderid);
				$r = sql_query($q);
			}
		}
		return $folderid;
	}
	/* }}} */
	/* parseSize {{{ */
	/**
	 * return human readable version of a size in bytes
	 *
	 * @param int $size size in bytes
	 * @return mixed if size below 1 KB, return size as int, otherwise it will return 1KB (for example)
	 */
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
	/* }}} */
	/* getProjectFolder {{{ */
	/**
	 * Return the folderid of the folder linked to supplied projectid.
	 *
	 * This function first detects wether the folder is in the database more then one time.
	 * If so, it will mark one of them as non-projectfolder so a filesysadmin can remove it.
	 * After this test, it will check wether the project folder does exists and if not create it.
	 *
	 * @param int $projectid The project id to lookup
	 * @return int The folderid
	 */
	public function getProjectFolder($projectid) {
		/* get the project data.
			we do this with the function getRecord()
			That function is not limited by access permissions
		*/
		$projectdata = new Project_data();
		$projectinfo = $projectdata->getRecord(array("id" => $projectid, "master" => 0));

		/* get main projectss folder */
		$sql = "SELECT id FROM filesys_folders WHERE parent_id=0 AND name='projecten'";
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$projects_folder = $row["id"];
		unset($row);

		/* detect double project folders */
		$q = sprintf("update filesys_folders set is_relation = 0 where project_id = %d and is_relation = 1", $projectid);
		sql_query($q);

		$detect=0;
		$q = sprintf("select id from filesys_folders where project_id = %d and parent_id = %d order by id", $projectid, $projects_folder);
		$res2 = sql_query($q);
		$correct_projectid = 0;
		if (sql_num_rows($res2) > 1) {
			while ($row2 = sql_fetch_assoc($res2)) {
				$detect++;
				if ($detect > 1) {
					if ($correct_projectid) {
						/* move files */
						$q = sprintf("UPDATE filesys_files SET folder_id = %d WHERE folder_id = %d", $correct_projectid, $row2["id"]);
						sql_query($q);
						$q = sprintf("UPDATE filesys_folders SET parent_id = %d WHERE parent_id = %d", $correct_projectid, $row2["id"]);
						sql_query($q);
						$q = sprintf("DELETE FROM filesys_folders where id = %d", $row2["id"]);
						sql_query($q);
					} else {
						die("something bad happened");
					}
				} else {
					$correct_projectid = $row2["id"];
					$q = sprintf("update filesys_folders set sticky = 1, name = '%s' where id = %d", addslashes($projectinfo["name"]), $row2["id"]);
					sql_query($q);
				}
			}
		} elseif (sql_num_rows($res2) == 1) {
			/* detect sticky flag errors */
			$row2 = sql_fetch_assoc($res2);
			if (!$row2["sticky"]) {
				$q = sprintf("update filesys_folders set sticky = 1 where id = %d", $row2["id"]);
				sql_query($q);
			}
		}
		/* cleanup run */
		$this->cleanup_empty($projects_folder);

		/* check if the project folder is there */
		/* first, get project name from db */
		$projectname = $projectinfo["name"];
		$projectrelation = $projectinfo["address_id"];
		$folder_settings = array(
			"name"        => $projectname,
			"is_public"   => 1,
			"is_relation" => 0,
			"address_id"  => (int)$projectrelation,
			"parent_id"   => (int)$projects_folder,
			"sticky"      => 1,
			"project_id"  => (int)$projectid
		);
		$projectfolderid = $this->check_folder($folder_settings);
		return $projectfolderid;
	}
	/* }}} */
	/* getRelFolder {{{ */
	/**
	 * Return the folderid of the folder linked to supplied addressid.
	 *
	 * This function first detects wether the folder is in the database more then one time.
	 * If so, it will mark one of them as non-relationfolder so a filesysadmin can remove it.
	 * After this test, it will check wether the relation folder does exists and if not create it.
	 *
	 * @param int $addressid The address id to lookup
	 * @return int The folderid
	 */
	public function getRelFolder($addressid) {
		/* get the project data.
			we do this with the function getRecord()
			That function is not limited by access permissions
		*/
		$addressdata = new Address_data();
		$addressinfo = $addressdata->getRecord(array("id" => $addressid, "type" => "relation"));
		/* get main relations folder */
		$sql = "SELECT id FROM filesys_folders WHERE parent_id=0 AND name='relaties'";
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$relfolder = $row["id"];

		/* detect double relation folders */
		$detect=0;
		$q = sprintf("select id from filesys_folders where address_id = %d and parent_id = %d order by id", $addressid, $relfolder);
		$res2 = sql_query($q);
		$correct_folderid = 0;
		if (sql_num_rows($res2) > 1) {
			while ($row2 = sql_fetch_assoc($res2)) {
				$detect++;
				if ($detect > 1) {
					$q = sprintf("UPDATE filesys_files SET folder_id = %d WHERE folder_id = %d", $correct_folderid, $row2["id"]);
					sql_query($q);
					$q = sprintf("UPDATE filesys_folders SET parent_id = %d WHERE parent_id = %d", $correct_folderid, $row2["id"]);
					sql_query($q);
					$q = sprintf("DELETE FROM filesys_folders WHERE id = %d", $row2["id"]);
					sql_query($q);
				} else {
					$correct_folderid = $row2["id"];
					$q = sprintf("update filesys_folders set sticky = 1, name = '%s' where id = %d", addslashes($addressinfo["companyname"]), $correct_folderid);
					sql_query($q);
				}
			}
		} elseif (sql_num_rows($res2) == 1) {
			/* detect sticky flag errors */
			$row2 = sql_fetch_assoc($res2);
			if (!$row2["sticky"]) {
				$q = sprintf("update filesys_folders set sticky = 1 where id = %d", $row2["id"]);
				sql_query($q);
			}
		}
		/* cleanup run */
		$this->cleanup_empty($relfolder);

		$folder_settings = array(
			"name"        => $addressinfo["companyname"],
			"is_public"   => 1,
			"is_relation" => 1,
			"address_id"  => $addressid,
			"parent_id"   => $relfolder,
			"sticky"      => 1,
			"project_id"  => 0
		);
		$relfolderid = $this->check_folder($folder_settings);
		return $relfolderid;
	}
	/* }}} */
	/* getUserFolder {{{ */
	/**
	 * Return the folderid of the folder linked to supplied userid.
	 *
	 * This function first detects wether the folder is in the database more then one time.
	 * If so, it will mark one of them as non-userfolder so a filesysadmin can remove it.
	 * After this test, it will check wether the user folder does exists and if not create it.
	 *
	 * @param int $user_id The user id to lookup
	 * @return int The folderid
	 */
	public function getUserFolder($user_id) {
		/* find out if the user is active or not */
		$sql = sprintf("SELECT is_active FROM users WHERE id = %d", $user_id);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		if ($row["is_active"])
			$sql_hrm = "SELECT id FROM filesys_folders WHERE parent_id = %d AND name = 'medewerkers'";
		else
			$sql_hrm = "SELECT id FROM filesys_folders WHERE parent_id = %d AND name = 'oud-medewerkers'";

		/* get main users folder */
		$sql = "SELECT id FROM filesys_folders WHERE parent_id=0 AND name='hrm'";
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		/* get active and non-active folders */
		$sql = sprintf($sql_hrm, $row["id"]);
		$res = sql_query($sql);
		$hrm = sql_fetch_assoc($res);
		$hrmfolder = $hrm["id"];

		/* detect double relation folders */
		$detect=0;
		$q = sprintf("select id from filesys_folders where hrm_id = %d and parent_id = %d order by id", $user_id, $hrmfolder);
		$res2 = sql_query($q);
		if (sql_num_rows($res2) > 1) {
			while ($row2 = sql_fetch_assoc($res2)) {
				$detect++;
				if ($detect > 1) {
					$q = sprintf("update filesys_folders set sticky = 0, hrm_id = 0 where id = %d", $row2["id"]);
					sql_query($q);
				} else {
					$q = sprintf("update filesys_folders set sticky = 1 where id = %d", $row2["id"]);
					sql_query($q);
				}
			}
		} elseif (sql_num_rows($res2) == 1) {
			/* detect sticky flag errors */
			$row2 = sql_fetch_assoc($res2);
			if (!$row2["sticky"]) {
				$q = sprintf("update filesys_folders set sticky = 1 where id = %d", $row2["id"]);
				sql_query($q);
			}
		}

		$user_data = new User_data();
		$username = $user_data->getUsernameById($user_id);
		unset($user_data);

		$folder_settings = array(
			"name"        => $username,
			"is_public"   => 1,
			"is_relation" => 0,
			"parent_id"   => $hrmfolder,
			"sticky"      => 1,
			"project_id"  => 0,
			"hrm_id"      => $user_id
		);
		$userfolderid = $this->check_folder($folder_settings);
		return $userfolderid;
	}
	/* }}} */
	/* getFoldernameById {{{ */
	/**
	 * Find the name of a folder with given id.
	 *
	 * @param int $folderid The folderid to lookup
	 * @param int $allow_translate If set, run the found name through gettext to get a localized version of the name
	 * @return string The foldername or '&nbsp;' if nothing is found
	 */
	public function getFoldernameById($folderid, $allow_translate=0) {
		if (preg_match("/^g_/s", $folderid)) {
			if ($folderid == "g_0")
				return gettext("items not in folders");
			else
				return base64_decode(preg_replace("/^g_/s", "", $folderid));
		} else {
			$sql = sprintf("SELECT name, parent_id FROM filesys_folders WHERE id=%d", $folderid);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if (!$row["parent_id"] && $allow_translate) {
				if ($row["name"] == "projecten" && $GLOBALS["covide"]->license["has_project_declaration"])
					return gettext("dossier");

				if (strlen($row["name"]))
					return gettext($row["name"]);
				else
					return "&nbsp;";
			} else {
				return $row["name"];
			}
		}
	}
	/* }}} */
	/* getMyFolders {{{ */
	/**
	 * Find the toplevel My folders and create if not found
	 *
	 * @return int The toplevel My folders
	 */
	public function getMyFolders() {
		$q = "select id from filesys_folders where name = 'mijn mappen' and (parent_id = 0 or parent_id IS NULL)";
		$res = sql_query($q);
		if (sql_num_rows($res)==0) {
			$q = "insert into filesys_folders (name, parent_id) values ('mijn mappen', 0)";
			sql_query($q);
			$id = sql_insert_id("filesys_folders");
		} else {
			$id = sql_result($res,0);
		}
		return $id;
	}
	public function getGoogleFolders() {
		$q = "select id from filesys_folders where name = 'google mappen' and (parent_id = 0 or parent_id IS NULL)";
		$res = sql_query($q);
		if (sql_num_rows($res)==0) {
			$q = "insert into filesys_folders (name, parent_id) values ('google mappen', 0)";
			sql_query($q);
			$id = sql_insert_id("filesys_folders");
		} else {
			$id = sql_result($res,0);
		}
		return $id;
	}
	/* }}} */
	/* getCmsFolder {{{ */
	/**
	 * Find the toplevel CMS folder and create if not found
	 *
	 * @return int The toplevel CMS folder
	 */
	public function getCmsFolder() {
		$q = "select id from filesys_folders where name = 'cms' and (parent_id = 0 or parent_id IS NULL)";
		$res = sql_query($q);
		if (sql_num_rows($res)==0) {
			$q = "insert into filesys_folders (name, parent_id) values ('cms', 0)";
			sql_query($q);
			$id = sql_insert_id("filesys_folders");
		} else {
			$id = sql_result($res,0);
		}
		return $id;
	}
	/* }}} */
	/* getParentFolder {{{ */
	/**
	 * Get the parent folderid of given folder
	 *
	 * @param int $folderid The folderid for which to find parent
	 * @return int The parentfolderid
	 */
	public function getParentFolder($folderid) {
		if (preg_match("/^g_/s", $folderid)) {
			return $this->getGoogleFolders();
		} else {
			$sql = sprintf("SELECT parent_id FROM filesys_folders WHERE id=%d", $folderid);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			return $row["parent_id"];
		}
	}
	/* }}} */
	/* getSyncFolder {{{ */
	/**
	 * Get the id of the folder 'my sync files'.
	 * If this folder does not exists for the user create it.
	 *
	 * @return int The folder id of 'my sync files'
	 */
	public function getSyncFolder() {
		$q = sprintf("select id from filesys_folders where parent_id = 0 and user_id = %d
			and name = 'mijn sync files'", $_SESSION["user_id"]);
		$res = sql_query($q);
		if (sql_num_rows($res,0) == 0)
			$sync = $this->createSyncFolder();
		else
			$sync = sql_result($res,0);

		return $sync;
	}
	/* }}} */
	/* createSyncFolder {{{ */
	/**
	 * Create 'my sync files' folder if it does not exists
	 *
	 * @return mixed the id of the 'my sync files' folder if created, true if the folder already exists
	 */
	private function createSyncFolder() {
		/* check for sync folder */
		$defname = "mijn sync files";
		$q = sprintf("select count(*) from filesys_folders where
			name = '%s' and user_id = %d and parent_id = 0 and is_public = 0",
			$defname, $_SESSION["user_id"]);
		$res = sql_query($q);
		if (sql_result($res,0) == 0) {
			$q = sprintf("insert into filesys_folders (name, user_id, parent_id, is_public)
				values ('%s', %d, 0, 0)", $defname, $_SESSION["user_id"]);
			sql_query($q);
			return sql_insert_id("filesys_folders");
		} else {
			return true;
		}
	}
	/* }}} */
	/* {{{ getFolders($settings) */
	/**
	 * get folder-structure from db
	 *
	 * @param array $settings the folder settings
	 * @param int $top Optional offset for start in recordset
	 * @return array the structure
	 */
	public function getFolders($settings = array(), $top=0) {
		$my_folders     = $this->getMyFolders();
		$google_folders = $this->getGoogleFolders();

		if (!$settings["parentfolder"] && $GLOBALS["covide"]->license["has_funambol"])
			$this->getSyncFolder();

		if (!$settings["sort"])
			$settings["sort"] = "upper(name)";
		else
			$settings["sort"] = sql_filter_col($settings["sort"]);

		if ($settings["ids"]) {
			$sql        = sprintf("SELECT * FROM filesys_folders WHERE id IN (%s) ", $settings["ids"]);
			$sql_count  = sprintf("SELECT count(*) FROM filesys_folders WHERE id IN (%s)", $settings["ids"]);

		} elseif (!$settings["parentfolder"]) {
			/* get toplevel structure */
			$sql        = sprintf("SELECT * FROM filesys_folders WHERE (parent_id = 0 OR parent_id is null) AND (");
			$sql_count  = sprintf("SELECT count(*) FROM filesys_folders WHERE (parent_id = 0 OR parent_id is null) AND (");

			if (!in_array($_REQUEST["subaction"], array("cmsimage", "cmsfile"))) {
				$sql.= sprintf("(name='mijn documenten' AND user_id=%d)", $_SESSION["user_id"]);
				$sql_count .= sprintf("(name='mijn documenten' AND user_id=%d)", $_SESSION["user_id"]);
			} else {
				$sql.= "1=0";
				$sql_count.= "1=0";
			}
			/* my folders */
			$sql       .= " OR (name='mijn mappen')";
			$sql_count .= " OR (name='mijn mappen')";

			if (!in_array($_REQUEST["subaction"], array("cmsimage", "cmsfile"))) {
				$sql       .= " OR (name='google mappen')";
				$sql_count .= " OR (name='google mappen')";

				if ($GLOBALS["covide"]->license["has_project"]) {
					$sql       .= " OR (name='projecten')";
					$sql_count .= " OR (name='projecten')";
				}
				if ($GLOBALS["covide"]->license["has_hrm"]) {
					$sql       .= " OR (name='hrm')";
					$sql_count .= " OR (name='hrm')";
				}
				if ($GLOBALS["covide"]->license["has_funambol"]) {
					$sql       .= sprintf(" OR (user_id = %d AND name='mijn sync files')", $_SESSION["user_id"]);
					$sql_count .= sprintf(" OR (user_id = %d AND name='mijn sync files')", $_SESSION["user_id"]);
				}
				$sql .= " OR (name='relaties')";
				$sql .= " OR (name='openbare mappen')";
				$sql_count .= " OR (name='relaties' AND parent_id=0)";
				$sql_count .= " OR (name='openbare mappen' AND parent_id=0)";
			}
			//if ($GLOBALS["covide"]->license["has_cms"]) {
				$sql       .= " OR (name='cms')";
				$sql_count .= " OR (name='cms')";
			//}
			$sql .= ")";
			$sql_count .= ")";

			$sql .= " ORDER BY is_public,UPPER(name)";

		} elseif ($settings["parentfolder"] == $google_folders
			|| preg_match("/^g_/s", $settings["parentfolder"])) {

			$google_current_folder = $settings["parentfolder"];
			$settings["parentfolder"] = $google_folders;

		} elseif ($settings["parentfolder"] == $my_folders) {

			$user_data = new User_data();
			$user_info = $user_data->getUserGroups($_SESSION["user_id"]);
			$r = array($_SESSION["user_id"]);
			foreach ($user_info as $u) {
				if ($u > 0)
					$r[] = sprintf("G%d", $u);
			}
			$r = "((".implode(")|(", $r)."))";

			$regex_syntax = sql_syntax("regex");
			$regex = $regex_syntax." '(^|\\\\,)". $r ."(\\\\,|$)' ";

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

			$sql       = sprintf("select filesys_folders.*, filesys_permissions.user_id as p_users, filesys_permissions.permissions as p_permissions from filesys_folders %s where filesys_permissions.user_id %s %s", $join, $regex, $sq);
			$sql_count = sprintf("select count(*) from filesys_folders %s where filesys_permissions.user_id %s %s", $join, $regex, $sq);

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

		if ($settings["parentfolder"] != $google_folders) {
			$res_count = sql_query($sql_count);
			$res       = sql_query($sql, "", (int)$top, $this->pagesize);

			/* extract permissions for this user */
			$user_data = new User_data();
			$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);

			if ($settings["ids"]) {
				$downgrade = 1;
			} else {
				$downgrade = 0;
			}
			$indexID = 0;
			while ($row = sql_fetch_assoc($res)) {
				$row = $this->extractPermissions($row, &$user_info, $downgrade);

				/* check for cms folder */
				if ($settings["parentfolder"] == $my_folders && $row) {
					$hp2 = $this->getHighestParent($row["id"]);
					if (in_array($_REQUEST["subaction"], array("cmsimage", "cmsfile")) && $hp2["name"] != "cms")
						unset($row);
				}
				if ($row) {
					/* add path info */
					if ($settings["parentfolder"] == $my_folders) {
						$row["description"] = trim(sprintf("%s: %s\n\n%s",
							gettext("Path"),
							$this->getFolderPath($row["id"]),
							$row["description"]));
					}

					/* count files in the folder */
					$q = sprintf("select count(*) from filesys_files where folder_id = %d", $row["id"]);
					$res2 = sql_query($q);
					$row["filecount"] = sql_result($res2,0);

					/* count subfolders */
					$q = sprintf("select count(*) from filesys_folders where parent_id = %d", $row["id"]);
					$res2 = sql_query($q);
					$row["foldercount"] = sql_result($res2,0);

					$return["data"][$indexID] = $row;

					/* Get project information */
					$qP = sprintf("select * from project where id = %d", $row["project_id"]);
					$resP = sql_query($qP);
					$rowP = sql_fetch_assoc($resP);
					/* Overwrite description with project description */
					if($rowP["description"] && $return["data"][$indexID]["xs"] != "D") {
						/* Replace line breaks and carriage returns with a space because we want to have the description one-line only */
						$rowP["description"] = ereg_replace("[\n\r]", " ", $rowP["description"]);
						/* Cut off the description if it's 50 characters or more and add some dots */
						if(strlen($rowP["description"]) > 49) {
							$return["data"][$indexID]["description"] = substr($rowP["description"], 0, 50) . "....";
						} else {
							$return["data"][$indexID]["description"] = $rowP["description"];
						}
					}
				}
				$indexID++;
			}

			$return["count"] = sql_result($res_count,0);
		}

		/* if google folders */
		if ($google_current_folder) {
			$google = new Google_data();
			if ($google->checkGoogleSession() == true)
				$return["data"] = $google->getGoogleFolders($google_current_folder, $settings["subaction"]);
			else
				$return["need_google_login"] = $google->getGoogleUserLogin();
		}

		/* now get the basic folder permissions */
		$q = sprintf("select * from filesys_folders where id = %d",$settings["parentfolder"]);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		if (!$settings["parentfolder"] && $GLOBALS["covide"]->license["disable_basics"]) {
			foreach ($return["data"] as $k=>$v) {
				if (!$GLOBALS["covide"]->license["has_cms"] || $v["name"] != "cms")
					unset($return["data"][$k]);
			}
		}
		$row = $this->extractPermissions($row, &$user_info);
		$return["xs"] = $row["xs"];
		$return["xs_subaction"]   = $row["xs_subaction"];
		$return["current_folder"] = $row;

		if ($settings["parentfolder"] == $google_folders) {
			$return["xs"] = "R";
			$return["xs_subaction"] = "R";
		}

		return $return;
	}
	/* }}} */
	/* extractPermissions {{{ */
	/**
	 * Internal function to find permissions for given folder and user.
	 *
	 * This function will return the permissions on a folder.
	 * If the folder is a special one (relations, users etc) it will look in those modules as well to find access.
	 * If it's a normal folder it will lookup folder specific access. If those are not found it will look at the parent.
	 * If the parent has no special access it will look at that parent etc. This is done as long as no access permissions are found or if
	 * the search hits the toplevel dir. Toplevel dirs are special and always have special access flags.
	 *
	 * The $downgrade_permissions flag can be set to force all permissions higher then 'READ-ONLY' to be degraded to 'READ-ONLY'
	 *
	 * @param array $row The folderinfo to extract permissions for
	 * @param array $user_info The array that the userobject created for a user
	 * @param int $downgrade_permissions Optional If set it will degrade all permissions above read-only to read-only
	 * @return array Access/Action permissions for a folder
	 */
	private function extractPermissions($row, $user_info, $downgrade_permissions=0) {
		require(self::include_dir."extractPermissions.php");
		if ($xs != "H") {
			return $row;
		} else {
			return false;
		}
	}
	/* }}} */
	/* {{{ getFiles($settings) */
	/**
	 * get file-structure from db
	 *
	 * @param array $settings the file settings
	 * @return array the structure
	 */
	public function getFiles($settings = array()) {
		$google_folders = $this->getGoogleFolders();

		if ($settings["folderid"] == $google_folders
			|| preg_match("/^g_/s", $settings["folderid"])) {

			$google = new Google_data();
			return $google->getGoogleDocList($settings["folderid"], $settings["search"], $settings["subaction"]);
		}

		if ($this->_cache["getFiles"][$settings["folderid"]])
			return $this->_cache["getFiles"][$settings["folderid"]];

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

		if ($settings["search"]) {
			$like = sql_syntax("like");
			if (preg_match("/^[a-z]\*$/si", $settings["search"]))
				$sq = sprintf(" (name %1\$s '%2\$s%%' or description %1\$s '%2\$s%%') AND ",
					$like, preg_replace("/\*$/s", "", $settings["search"]));
			else
				$sq = sprintf(" (name %1\$s '%%%2\$s%%' or description %1\$s '%%%2\$s%%') AND ",
					$like, $settings["search"]);
		}
		$sql = sprintf("SELECT filesys_files.*, users.username FROM filesys_files LEFT JOIN users ON users.id = filesys_files.user_id WHERE %s folder_id=%d ORDER BY %s", $sq, $settings["folderid"], $settings["sort"]);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {

			$row["size_human"] = $this->parseSize($row["size"]);

			if ($settings["max_size"]) {
				if ($row["size"] > $settings["max_size"])
					$row["size_human"] = sprintf("<font color='red'><b>%s</b></font>", $row["size_human"]);
			}

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
			if ($settings["subaction"] == "add_attachment") {
				$row["attachment"] = 1;
			}

			$row["show_checkbox"] = 1;
			if ($_REQUEST["subaction"] == "cmsfile" || $_REQUEST["subaction"] == "cmsimage") {
				$row["show_checkbox"] = 0;
				if ($_REQUEST["subaction"] == "cmsimage") {
					if ($this->getFileType($row["name"]) == "ftype_image") {
						if (!preg_match("/\.bmp$/si", $row["name"])) {
							$row["cmsaction"] = 1;
						}
					}
				} else {
					$row["cmsaction"] = 1;
				}
			}
			if ($settings["highlight"] == $row["id"])
				$row["highlight"] = 1;

			$return[] = $this->detect_preview($row);
		}

		/* get highest parent of the requested folder */
		$hp = $this->getHighestParent($settings["folderid"]);

		/* if highest parent is cms, do an extra call */
		if ($GLOBALS["covide"]->license["has_cms"] && $hp["name"] == "cms" && !$settings["no_cms_scan"]) {
			$cms_data = new Cms_data();
			$cms_data->scanForFiles($return);
		}
		$this->_cache["getFiles"][$settings["folderid"]] = $return;
		return $return;
	}
	/* }}} */
	/* getFileType {{{ */
	/**
	 * Guess filetype based on extension
	 *
	 * @param string $name The filename to get the filetype for
	 * @return string ftype_<something>
	 */
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

		#if ($name == "headers.txt") {
		#	$t = "ftype_rfc822";
		#} else {
			$t = $types[$ext];
			if (!$t) {
				$t = "ftype_binary";
			}
		#}
		return $t;
	}
	/* }}} */
	/* getBindataById {{{ */
	/**
	 * Get the binary contents of a fileid from the filesys
	 *
	 * @param int $id The file_id to get the data for
	 * @param string $ext The file extension of the file
	 * @return string The binary data from the file
	 */
	public function getBindataById($id, $ext) {
		/* open file */

		/*
		 * new routines for gerben
		$file = $GLOBALS["covide"]->filesyspath."/bestanden";
		$content = $this->FS_readFile($file, $id, $ext);
		*/

		// old routines that work
		$file = $GLOBALS["covide"]->filesyspath."/bestanden/".$id.".".$ext;
		$content = $this->FS_readFile($file);
		// end old routines
		return $content;
	}
	/* }}} */
	/* deleteBindataById {{{ */
	/**
	 * Delete the binary content of a file from filesystem
	 *
	 * @param int $id The fileid to remove
	 * @return bool true
	 */
	public function deleteBindataById($id) {
		$q = sprintf("select name from filesys_files where id = %d", $id);
		$res = sql_query($q);
		$name = sql_result($res,0);

		$ext = $this->get_extension($name);

		/* new routines by gerben
		$folder = $GLOBALS["covide"]->filesyspath."/bestanden";
		$file = $this->FS_lookUpFile($id.'.'.$ext, $folder);
		*/

		/* old routines that work */
		$file = $GLOBALS["covide"]->filesyspath."/bestanden/".$id.".".$ext;
		/* end old routines */

		#@unlink($file);
		$this->FS_removeFile($file);
		return true;
	}
	/* }}} */
	/* {{{ getFileById($id) */
	/**
	 * get file info into an array, including the binary data
	 *
	 * @param int $id the file id
	 * @param int $skip_binary Optional if set the binary data will not be added to the array
	 * @return array the structure
	 */
	public function getFileById($id, $skip_binary=0) {
		$sql = sprintf("SELECT * FROM filesys_files WHERE id=%d", $id);
		$res = sql_query($sql);
		if (sql_num_rows($res)>0) {
			$row = sql_fetch_assoc($res);
		} else {
			$row = array();
			$skip_binary = 1;
		}
		$return = $row;
		/* get the data from fs */

		if (!$skip_binary) {
			$ext = $this->get_extension($row["name"]);
			$return["ext"] = $ext;

			$filedata = $this->getBindataById($id, $ext);
			$return["binary"] = $filedata;
			$return["fsize"] = strlen($filedata);
		}
		return $return;
	}
	/* }}} */
	/* file_remove {{{ */
	/**
	 * Remove a file from the filesysmodule and also nuke the binary data on filesystem
	 *
	 * @param int $fileid The file_id to remove
	 * @param int $folderid The folder where the file is in
	 * @param int $skip_redir Optional If set redirect to the folder where the file was in
	 * @return bool if $skip_redir is set this will be true, otherwise it will issue a header() command
	 */
	public function file_remove($fileid, $folderid, $skip_redir=0) {
		$this->deleteBindataById($fileid);

		if ($GLOBALS["covide"]->license["has_cms"]) {
			/* check if the cms has cached versions of this file */
			$cms_data = new Cms_data();
			$cms_data->removeThumbCache($fileid);
		}

		if ($GLOBALS["covide"]->license["has_funambol"]) {
			$sync_folder = $this->getSyncFolder();
			if ($folderid == $sync_folder) {
				$funambol_data = new Funambol_data();
				$funambol_data->removeRecord("files", $fileid);
			}
		}
		$sql = sprintf("DELETE FROM filesys_files WHERE id=%d AND folder_id=%d", $fileid, $folderid);
		$res = sql_query($sql);

		if (!$skip_redir)
			header("Location: index.php?mod=filesys&action=opendir&id=$folderid");
		else
			return true;
	}
	/* }}} */
	/* file_remove_multi {{{ */
	/**
	 * Remove multiple files in one run and return to the folder where the files were in.
	 *
	 * @param array $files All the file_id's to remove
	 * @param int $folder The folder_id where the files are in
	 */
	public function file_remove_multi($files, $folder) {
		if (is_array($files) && $folder) {
			foreach ($files as $k=>$v) {
				$this->file_remove($k, $folder, 1);
			}
		}
		header("Location: index.php?mod=filesys&action=opendir&id=$folder");
	}
	/* }}} */
	/* file_download {{{ */
	/**
	 * Download file to users device
	 *
	 * @param int $id The file_id to download
	 * @param int $view_only Optional if set dont show download dialog but hand the file to the browser
	 */
	public function file_download($id=0, $view_only=0) {
		/* get file data */
		if (!$id) {
			$error = gettext("no files specified");
			exit();
		} else {
			$file = $this->getFileById($id);

			$conversion = new Layout_conversion();
			$conversion->convertFilename($file["name"]);

			$conversion->filterNulls($file["name"]);
			$conversion->filterNulls($file["type"]);
			
			header("Content-Transfer-Encoding: binary");
			header("Content-Type: ".strtolower($file["type"]));

			#if (!$_SERVER["HTTPS"] && $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] != "https")
			#	header("Content-Length: ".strlen($file["binary"]));

			if ($view_only == 2) {
				header("Content-Disposition: inline; filename=\"".$file["name"]."\"");
			} elseif (!$view_only) {
				if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) {
					header("Content-Disposition: filename=\"".$file["name"]."\"");
				} else {
					header("Content-Disposition: attachment; filename=\"".$file["name"]."\"");
				}
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
	/* }}} */
	/* multi_download_zip {{{ */
	/**
	 * Dowload multiple files at once using a zip archive
	 *
	 * @param array $ids The file_id's to download
	 * @param int $folder The folder containing the files
	 */
	public function multi_download_zip($ids, $folder) {
		$ids = explode(",", $ids);

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
		header('Content-Type: application/x-zip');

		#if (!$_SERVER["HTTPS"] && $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] != "https")
		#	header("Content-Length: ".strlen($data));

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename="'.$fname.'"'); //msie 5.5 header bug
		} else {
			header('Content-Disposition: attachment; filename="'.$fname.'"');
		}

		echo $data;
		exit();
	}
	/* }}} */
	/* file_edit_save {{{ */
	/**
	 * Save modifications to a files description and folder to database
	 *
	 * @param array $formdata The data from the form in Filesys_output::file_edit()
	 */
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
	/* }}} */
	/* file_upload_bindata {{{ */
	/**
	 * Replace file in filesystem with newly uploaded file.
	 *
	 * @param array $data File data
	 */
	public function file_upload_bindata($data) {

		/* update bindata */
		$size = $data["size"];
		$name = $data["name"];
		$folder = $data["folder"];

		$file = $this->getFileById($data["id"]);
		$ext = $this->get_extension($name);

		$q = sprintf("update filesys_files set name = '%s', size = %d, timestamp = %d where
			id = %d and folder_id = %d",
			addslashes($name), $size, mktime(), $data["id"], $folder);
		sql_query($q);

		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir  = "bestanden";
		$destination = sprintf("%s/%s/%s.".$ext, $fspath, $fsdir, $data["id"]);

		/* remove old version(s) */
		$this->FS_removeFile($destination);

		file_put_contents($destination, base64_decode($data["base64"]));

		/* compress contents */
		$this->FS_compressFile($destination);
	}
	/* }}} */
	/* file_upload_alt {{{ */
	/**
	 * Alternative file upload function
	 *
	 * @param array $data The filedata like name and type etc
	 * @return int The database id for the new file
	 */
	public function file_upload_alt($data) {
		/* gather some file info */
		$name = $data["name"];
		/* write temp file */
		$tmp_name = $GLOBALS["covide"]->temppath."filedata_".md5(rand().session_id());

		if (!$data["raw"])
			$data["bindata"] = base64_decode($data["base64"]);

		/* drop to fs */
		file_put_contents($tmp_name, $data["bindata"]);

		$type     = $this->detectMimetype($tmp_name);
		$size     = $data["size"];
		$folderid = $data["folder_id"];

		$ext = $this->get_extension($name);
		/* insert file into dbase */
		$q = "insert into filesys_files (folder_id, name, size, type, timestamp, user_id, description) values ";
		$q.= sprintf("(%d, '%s', '%s', '%s', %d, %d, '%s')", $folderid, addslashes($name), $size, $type, mktime(), $_SESSION["user_id"], '');
		sql_query($q);
		$new_id = sql_insert_id("filesys_files");

		/* move data to the destination */

		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir  = "bestanden";
		$destination = sprintf("%s/%s/%s.".$ext, $fspath, $fsdir, $new_id);

		/* remove old version(s) */
		$this->FS_removeFile($destination);

		rename($tmp_name, $destination);

		/* compress contents */
		$this->FS_compressFile($destination);

		return $new_id;
	}
	/* }}} */
	/* file_upload {{{ */
	/**
	 * Store uploaded file into database
	 */
	public function file_upload($data) {
		require(self::include_dir."datafile_upload.php");
	}
	/* }}} */
	/* create_dir {{{ */
	/**
	 * Create directory in database
	 *
	 * @param array $dirdata The name and description etc for the new directory
	 */
	public function create_dir($dirdata, $return=0) {
		$sql  = "INSERT INTO filesys_folders (name, description, user_id, parent_id) VALUES ";
		$sql .= sprintf("('%s', '%s', %d, %d)", $dirdata["folder"]["name"], $dirdata["folder"]["description"], $_SESSION["user_id"], $dirdata["id"]);
		sql_query($sql);
		if($dirdata["isPopup"]) {
			$output = new Layout_output();
			$output->start_javascript();
			$output->addCode("opener.location.href = opener.location.href;");
			$output->addCode("var tcx = setTimeout('window.close();', 50);");
			$output->end_javascript();
			$output->exit_buffer();

		}
		if ($dirdata["opener"]) {
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode(
					"parent.reset_upload_status();"
				);
			$output->end_javascript();
			$output->exit_buffer();
		} elseif ($return)
			return sql_insert_id("filesys_folders");
		else
			header("Location: index.php?mod=filesys&action=opendir&id=".$dirdata["id"]);

	}
	/* }}} */
	/* getHighestParent {{{ */
	/**
	 * Get the toplevel parent for a folder
	 *
	 * @param int $folder The folder_id to find the toplevel dir for
	 * @return array id and name of toplevel folder
	 */
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
	/* }}} */
	/* getHighestParent {{{ */
	/**
	 * Get the toplevel parent for a folder
	 *
	 * @param int $folder The folder_id to find the toplevel dir for
	 * @return array id and name of toplevel folder
	 */
	public function getHighestParentSubfolder($folder, $hp) {
		$id = $folder;
		while ($id > 0 && $hp != $id) {
			$currid = $id;
			$id = $this->getParentFolder($id);
			if ($id > 0) {
				$folder = $id;
			}
		}
		$data = $this->getFolderInfo($currid);
		return $data;
	}
	/* }}} */
	/* getFolderPath {{{ */
	/**
	 * Return current path of folder
	 *
	 * @param int $folder The folder_id to get the path of
	 * @return string The complete path (eg. relations->covide->logos)
	 */
	public function getFolderPath($folder, $allow_translate=0) {
		$id = $folder;
		$i = 0;
		$name[$i] = $this->getFoldernameById($folder, $allow_translate);
		while ($id > 0 || $id == "g_0") {
			$id = $this->getParentFolder($id, $allow_translate);
			if ($id > 0) {
				$i++;
				$name[$i] = $this->getFoldernameById($id, $allow_translate);
			}
		}

		// Now we now the path, make a nice string....
		for ($it = count($name)-1; $it >= 0; $it --) {
			/* the first one can be translated */
			if ($it == count($name)-1 && strlen(trim($name[$it])))
				$name[$it] = gettext($name[$it]);

			$return .= $name[$it];
			if ($it > 0)
				$return .= " -> ";
		}
		return $return;
	}
	/* }}} */
	/* getFolderPermissions {{{ */
	/**
	 * Lookup specific access permissions for a folder
	 *
	 * @param int $folder The folder_id to process
	 * @param string $tree Optional toplevel special dir where this folder is located
	 * @return string The permissions for this folder (R = readonly, W = read/write, D = denied)
	 */
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

			if ($permissions != "R") {
				$user = new User_data();
				$arr = $user->getUserGroups($_SESSION["user_id"]);
				foreach ($arr as $g) {
					if ($ary["G".$g] == "W") {
						$permissions = "W";
					} elseif ($ary["G".$g] == "R" && !$permissions) {
						$permissions = "R";
					}
				}
			}

			/* if no permissions are found for this user */
			if (!$permissions) {
				$permissions = "D";
			}
		}

		/* if no permissions are found, check for account manager permissions */
		if ($tree == "relations") {
			/*
			if (!$this->_cache["hp_folder"])
				$this->_cache["hp_folder"] = $this->getHighestParent($folder);

			$relfolder = $this->getHighestParentSubfolder($folder, $this->_cache["hp_folder"]["id"]);
			*/

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
			$q = sprintf("select project_id from filesys_folders where id = %d", $folder);
			$res = sql_query($q);
			$project_id = sql_result($res,0);

			if ($project_id) {
				if (!$this->_cache["project_data"])
					$this->_cache["project_data"] = new Project_data();

				$project_data =& $this->_cache["project_data"];
				$data = $project_data->getProjectById($project_id);
				if ($project_data->dataCheckPermissions($data[0])) {
					$permissions = "W";
				} else {
					$mdata = $project_data->getProjectById($data[0]["group_id"], 1);
					if ($project_data->dataCheckPermissions($mdata[0]))
						$permissions = "W";
				}
			}
		}
		return $permissions;
	}
	/* }}} */
	/* checkPermissions {{{ */
	/**
	 * Fetch permissions for specific folder
	 *
	 * @param int $folder The folder_id to lookup
	 * @param string $tree Optional
	 * @return string The permissions flag
	 */
	public function checkPermissions($folder, $tree="") {
		$id = $folder;
		$permissions = "";

		while ($id > 0 && !$permissions) {
			$permissions = $this->getFolderPermissions($id, $tree);
			$id = $this->getParentFolder($id);
		}
		return $permissions;
	}
	/* }}} */
	/* getFolderInfo {{{ */
	/**
	 * Return all info from a folder
	 *
	 * @param int $folder The folder_id to lookup
	 * @return array The data that's in the database for this folder
	 */
	public function getFolderInfo($folder) {
		$q = "select * from filesys_folders where id = ".$folder;
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		return $row;
	}
	/* }}} */
	/* folderSave {{{ */
	/**
	 * Store modified folder information in the database
	 *
	 * @param array $folder The complete folder info in the same format as getFolderInfo returns
	 */
	public function folderSave($folder) {
		$q = sprintf("update filesys_folders set name = '%s', description = '%s' where id = %d",
			$folder["name"], $folder["description"], $folder["id"]);
		sql_query($q);

		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode("
				opener.location.href='index.php?mod=filesys&action=opendir&id=".$folder["parent_id"]."';
				window.close();
			");
		$output->end_javascript();
		$output->exit_buffer();
	}
	/* }}} */
	/* checkForRelation {{{ */
	/**
	 * Find out if a folder is a relations folder
	 *
	 * @param int $folder The folder_id to check
	 * @return int The relation id if the folder is actually a relations folder, nothing if not
	 */
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
	/* }}} */
	/* checkForProject {{{ */
	/**
	 * Find out if the folder is a project folder
	 *
	 * @param int $folder The folder_id to check
	 * @return int The project id if the folder is actually a project folder, nothing if not
	 */
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
	/* }}} */
	/* retrieveFullPermissions {{{ */
	/**
	 * Lookup all the permissions that apply to a directory
	 *
	 * @param int $folder The folder_id of the folder to lookup
	 * @return array The folders permissions in the format: array($userid=>$permission, $userid=>$permission)
	 */
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
	/* }}} */
	/* modifyPermissionArray {{{ */
	/**
	 * Alter permissions array of a folder.
	 * This function is used to grant or deny permissions to a folder that already has specific permissions.
	 *
	 * @param array $r users and their permissions as it was before
	 * @param int $userid User to add/remove permissions for
	 * @param string $permissions The permissions for this user
	 * @return array The new permissions array for a folder
	 */
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
	/* }}} */
	/* updatePermissionsDb {{{ */
	/**
	 * Write modified permissions on a folder to the database
	 *
	 * @param int $folder The folder_id to update
	 * @param array $r The permissions as set on the folder
	 */
	public function updatePermissionsDb($folder, $r) {
		$q = "update filesys_permissions set user_id = '".$r[0]."', permissions = '".$r[1]."' where folder_id = $folder";
		sql_query($q);
	}
	/* }}} */
	/* getFolderArrayRecursive {{{ */
	/**
	 * Function to append child directories to an already prepared array
	 * For internal use only
	 *
	 * @param int $folder folder_id to lookup children for
	 * @param array $folders The array to attach the info to
	 * @param int Optional The level of deepness we are at
	 */
	public function getFolderArrayRecursive($folder, $folders, $level=1) {
		$q = "select * from filesys_folders where parent_id = $folder order by name";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
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

			$permissions = $this->checkPermissions($row["id"]);
			/* if some permissions go recursive */
			if ($permissions != "D") {
				$this->getFolderArrayRecursive($row["id"], &$folders, $level+1);
			}
		}
	}
	/* }}} */
	/* getFolderArray {{{ */
	/**
	 * Get the complete directory structure of a folder in an array
	 *
	 * @param int $folder The folder_id to start at
	 * @return array The complete tree
	 */
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
	/* }}} */
	/* deleteFolderExec {{{ */
	/**
	 * Remove a folder including all subfolders and files
	 *
	 * @param int $folderid The folder_id to remove
	 */
	public function deleteFolderExec($folderid = 0, $no_headers = 0) {
		if (!$folderid)
			$folderid = $_REQUEST["id"];

		/* just another security check to be sure */
		$folders = $this->getFolderArray($folderid);
		foreach ($folders as $v) {
			if ($v["permissions"] != "W") {
				die("error occured, no valid permissions");
			}
		}

		foreach ($folders as $folder) {
			$files = $this->getFiles(array("folderid"=>$folder["id"]));
			if (is_array($files)) {
				foreach ($files as $file) {
					$this->file_remove($file["id"], $folder["id"], 1);
				}
			}
			$q = "delete from filesys_folders where id = ".$folder["id"];
			sql_query($q);
		}

		if (!$no_headers) {
			$output = new Layout_output();
			$output->start_javascript();
			$output->addCode("
				opener.document.getElementById('velden').submit(); window.close();
			");
			$output->end_javascript();
			$output->exit_buffer();
		}
	}
	/* }}} */
	/* getFoldersByArray {{{ */
	/**
	 * Get info of an array of folder_ids
	 *
	 * @param array $ids The folder_id's to fetch
	 * @return array All the info found on the folders
	 */
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
	/* }}} */
	/* getFilesByArray {{{ */
	/**
	 * Get info of an array of file_ids
	 *
	 * @param array $ids The file_id's to fetch
	 * @return array All the info found on the files
	 */
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
	/* }}} */
	/* pasteExec {{{ */
	/**
	 * Store pasted folder info in database
	 *
	 * @todo move $target_folder and $pastebuffer to function arguments
	 */
	public function pasteExec() {
		$target_folder = $_REQUEST["id"];
		$pastebuffer = $_REQUEST["pastebuffer"];

		if (preg_match("/^folder/s", $pastebuffer)) {
			/* process folders */
			$folder = preg_replace("/folder,/s", "", $pastebuffer);
			$q = sprintf("update filesys_folders set parent_id = %d where id = %d", $target_folder, $folder);
			sql_query($q);

		} else {
			/* check if destination folder is a sync folder */
			if ($GLOBALS["covide"]->license["has_funambol"])
				$sync_folder = $this->getSyncFolder();

			/* process files */
			$files = preg_replace("/file,/s", "", $pastebuffer);
			if ($files) {
				$files = explode(",", $files);
				foreach ($files as $file) {
					$q = sprintf("select name, folder_id from filesys_files where id = %d", $file);
					$res = sql_query($q);
					$name = addslashes(sql_result($res,0,"name"));
					$source_folder = sql_result($res,0,"folder_id");

					$name = $this->checkDuplicates($name, $target_folder);

					$q = sprintf("update filesys_files set name = '%s', folder_id = %d where id = %d", $name, $target_folder, $file);
					sql_query($q);

					/* check if source folder was the sync folder */
					if ($sync_folder == $source_folder) {
						/* remove file from the device, but leave it in covide */
						$funambol_data = new Funambol_data();
						$funambol_data->removeRecord("files", $file);
					}
				}
			}
		}
		header("Location: index.php?mod=filesys&action=opendir&id=$target_folder");
	}
	/* }}} */
	/* save_attachment {{{ */
	/**
	 * Save an attachment from email in the filesysmodule
	 *
	 * @todo move $folder and $ids to function arguments
	 */
	public function save_attachment() {
		$folder = $_REQUEST["id"];
		$ids    = explode(",", $_REQUEST["ids"]);
		$description = $_REQUEST["description"];

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
			$q.= sprintf("(%d, '%s', '%s', '%s', %d, %d, '%s')", $folder, $name, $size, $type, mktime(), $_SESSION["user_id"], $description);
			sql_query($q);
			$new_id = sql_insert_id("filesys_files");

			/* move data to the destination */
			$ext = $this->get_extension($file["name"]);

			$source = sprintf("%s/%s/%s.%s", $fspath, $fsdir_source, $file["id"], $ext);
			$destination = sprintf("%s/%s/%s.%s", $fspath, $fsdir_target, $new_id, $ext);

			#@copy($source, $destination);
			$this->FS_copyFile($source, $destination);
		}

	}
	/* }}} */
	/* save_fax {{{ */
	/**
	 * Store a fax in the filesystem module
	 *
	 * @todo move $folder $ids and $description to function arguments
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

			/* compress */
			$this->FS_compressFile($destination);

			$voipdata->deleteFax($id, 0);
		}
	}
	/* }}} */
	/* searchAll {{{ */
	/**
	 * Search in complete filesystem for files and folders.
	 *
	 * @param array $options The search options
	 * @return array Matches in the filesystem
	 */
	public function searchAll($options) {
		require(self::include_dir."searchAll.php");
		return $data;
	}
	/* }}} */
	/* get_extension {{{ */
	/**
	 * Take filename and return the extension of the file
	 *
	 * @param string $file the filename
	 * @return string The file's extension
	 */
	public function get_extension($file) {
		$conversion = new Layout_conversion();
		$file = $conversion->decodeMimeString($file);
		/*
		$file = basename($file);
		$file = explode(".", $file);
		$ext = strtolower( $file[count($file)-1] );
		*/
		$p = pathinfo($file);
		$ext = $p["extension"];

		if ($ext == "html") $ext = "htm";
		if (strlen($ext)!=3) {
			$ext = "dat";
		}
		return $ext;
	}
	/* }}} */
	/* rename_filetype {{{ */
	/**
	 * Give a file another extension
	 *
	 * @param int $id The file_id to rename
	 * @param string $ftype1 The current extension
	 * @param string $ftype2 The new extension
	 * @param string $store files or email
	 */
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

		#echo "$src => $dest <br>";
		if (file_exists($src)) {
			rename($src, $dest);
		}
	}
	/* }}} */
	/* checkFilePermissions {{{ */
	/**
	 * Get a files permissions for the current user
	 *
	 * @param int $id The file_id to check
	 * @return array complete fileinfo including access permissions
	 */
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
			$row["file_date"] = date("d-m-Y", (int)$row2["timestamp"]);
			$row["file_icon"] = $this->getFileType($row2["name"]);
			$xs = $row;
		}
		return $row;
	}
	/* }}} */
	/* file_preview_header {{{ */
	/**
	 * This function should not be here
	 * Please comment what it is and why it's here.
	 *
	 * @todo document and move to output class
	 */
	public function file_preview_header() {
		$output = new Layout_output();
		$output->layout_page("preview", 1);
			$output->addTag("div", array("align"=>"right"));
			$output->addTag("b");
			$output->addCode( gettext("filename").": [");
			$output->addCode($_REQUEST["file"]."] ");
			$output->endTag("b");
			$output->addSpace(10);
			$output->insertAction("print", gettext("print"), "javascript: parent.preview_main.print();");
			$output->addSpace(2);
			$output->insertAction("close", gettext("close window"), "javascript: parent.window.close();");
			$output->endTag("div");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* file_preview_readfile {{{ */
	/**
	 * Send binary filedata to browser
	 *
	 * @param string The filename to open
	 * @param string Alt tag for images
	 */
	public function file_preview_readfile($file, $altname="") {
		$tmp = $GLOBALS["covide"]->temppath;

		$file = $tmp.$file;
		$altfile = $tmp.$altname;

		if (file_exists($altname)) {
			$file = $altname;
		}
		$mime = $this->detectMimetype($file);

		/* vcard exception */
		if ($_REQUEST["type"] == "vcard") {
			require("classes/email/inc/vbook.php");
			print_vcard_address_book(file_get_contents($file));
		} else {
			header("Content-Transfer-Encoding: binary");
			header("Content-Type: ".strtolower($mime));
			readfile($file);
		}
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
		require(self::include_dir."file_preview.php");
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
			case "image/png":
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
			case "application/mspowerpoint":
				$row["subtype"] = "ppt";
				$row["subview"] = 0;
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
		/* detect vcard type */
		if ($row["subtype"] == "text" && $ext == "vcf") {
			$row["subtype"] = "vcard";
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
	/* get_double_rel_folders {{{ */
	/**
	 * Fix double relation folders that were introduced by a bug early in 6.0
	 *
	 * This function is created to fix bug 1560558.
	 * You can trigger it by opening index.php?mod=filesys&action=find_double_rel when logged in
	 * as filesys manager or global admin
	 */
	public function get_double_rel_folders() {
		/* only filesysadmins and admins can run this function */
		$userdata = new User_data();
		$userinfo = $userdata->getUserdetailsById($_SESSION["user_id"]);
		if (!$userinfo["xs_usermanage"] || !$userinfo["xs_filemanage"])
			die("no access to this hidden function!");
		$address_data = new Address_data();
		/* default dir settings */
		$folder_settings = array(
			"name"        => "",
			"is_public"   => 1,
			"is_relation" => 1,
			"address_id"  => "",
			"parent_id"   => "",
			"sticky"      => 1,
			"project_id"  => 0
		);
		/* first lets find the relation folder */
		echo "<PRE>";
		$sql = "SELECT * FROM filesys_folders WHERE name='relaties' AND parent_id=0";
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$relationfolder = $row["id"];

		/* folders WITHOUT address_id set */
		$sql = "SELECT name FROM filesys_folders WHERE parent_id=".$relationfolder." AND address_id = 0 GROUP BY name having count(name) > 1";
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			/* get the address_id if it's there */
			$q = sprintf("SELECT id FROM address WHERE companyname = '%s'", addslashes($row["name"]));
			$r = sql_query($q);
			$address_id = sql_result($r, 0);
			if ($address_id) {
				$update_folders = sprintf("UPDATE filesys_folders SET address_id = %d WHERE name = '%s'", $address_id, addslashes($row["name"]));
				sql_query($update_folders);
			}
		}

		/* folders WITH address_id set */

		/* find doubles */
		$sql = "SELECT address_id FROM filesys_folders WHERE parent_id=".$relationfolder." AND address_id != 0 GROUP BY address_id having count(address_id) > 1";
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			/* get relation name */
			$relname = $address_data->getAddressNameByID($row["address_id"]);
			/* create new and correct folder for this relation */
			$f_settings               = $folder_settings;
			$f_settings["name"]       = $relname;
			$f_settings["address_id"] = $row["address_id"];
			$f_settings["parent_id"]  = $relationfolder;
			$new_relfolderid          = $this->check_folder($f_settings);
			$q = "SELECT id FROM filesys_folders WHERE parent_id = ".$relationfolder." AND address_id = ".$row["address_id"]." and address_id != 0 ORDER BY address_id";
			$r = sql_query($q);
			while ($info = sql_fetch_assoc($r)) {
				echo "Processing dir with id ".$info["id"]." for relation '$relname'...";
				if ($info["id"] != $new_relfolderid)
					$this->move_to_correct($new_relfolderid, $info["id"]);
				echo "done.\n";
			}
		}
		/* folders with double name, but one with and one(or n) without address_id */
		$sql = sprintf("SELECT name FROM filesys_folders WHERE parent_id = %d GROUP BY name HAVING count(name)>1", $relationfolder);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			echo "processing ".$row["name"]."\n";
			/* loop through the results, find the folders affected, get the one WITH address_id and put everything in there and remove the other one */
			$q = sprintf("SELECT id, address_id FROM filesys_folders WHERE name='%s' ORDER BY address_id", $row["name"]);
			$r = sql_query($q);
			$folderinfo = array();
			$null = 1;
			while ($tmp = sql_fetch_assoc($r)) {
				$tmp["relname"] = $address_data->getAddressNameByID($tmp["address_id"]);
				if ($tmp["relname"] == $row["name"])
					$tmp["correct"] = 1;
				else
					$tmp["correct"] = 0;
				$folderinfo[] = $tmp;
				if (!$tmp["address_id"])
					$null++;
			}
			if ($null == count($folderinfo)) {
				/* find the correct one in the array */
				foreach ($folderinfo as $k=>$v) {
					if ($v["correct"])
						$okone = $k;
				}
				/* loop again to put all non correct ones in correct one */
				foreach ($folderinfo as $k=>$v) {
					if ($k != $okone) {
						if ($folderinfo[$okone]["id"] != $v["id"])
							$this->move_to_correct($folderinfo[$okone]["id"], $v["id"]);
					}
				}
			}
		}
	}
	/* }}} */
	/* move_to_correct {{{ */
	/**
	 * This function is called internally by get_double_rel_folders
	 * It moves everything under a folder to another folder and remove the old folder
	 *
	 * @param int $correctfolder The folder to move stuff to
	 * @param int $wrongfolder The folder to move stuff away from
	 */
	private function move_to_correct($correctfolder, $wrongfolder) {
		if ($correctfolder && $wrongfolder) {
			/* move all the files from this dir to the newly created dir */
			$q_files = sprintf("UPDATE filesys_files SET folder_id = %d WHERE folder_id = %d", $correctfolder, $wrongfolder);
			sql_query($q_files);
			echo "\nfiles: ".$q_files."\n";
			/* move all the subfolders from this dir to the newly created dir */
			$q_folders = sprintf("UPDATE filesys_folders SET parent_id = %d WHERE parent_id = %d", $correctfolder, $wrongfolder);
			sql_query($q_folders);
			echo "folders: ".$q_folders."\n";
			/* remove this and everything under it */
			$folders = $this->getFolderArray($wrongfolder);

			foreach ($folders as $folder) {
				$files = $this->getFiles(array("folderid"=>$folder["id"]));
				if (is_array($files)) {
					foreach ($files as $file) {
						$this->file_remove($file["id"], $folder["id"], 1);
					}
				}
				$q = "delete from filesys_folders where id = ".$folder["id"];
				sql_query($q);
			}
		}
	}
	/* }}} */
	/* checkWebPermissions {{{ */
	/**
	 * Check permissions on a file for the website
	 *
	 * @param int $fileid The database id of the file to check
	 * @return bool true on access, false on no access
	 */
	public function checkWebPermissions($fileid) {
		$cache =& $this->_cache["webfolders"];
		$q = sprintf("select folder_id from filesys_files where id = %d", $fileid);
		$res = sql_query($q);
		$folder = sql_result($res,0);

		if (!$cache[$folder]) {
			$hp = $this->getHighestParent($folder);

			if ($hp["name"] == "cms")
				$cache[$folder] = "R";
			else
				$cache[$folder] = "D";
		}

		if ($cache[$folder] == "R")
			return true;
		else
			return false;
	}
	/* }}} */
	/* searchWebFiles {{{ */
	/**
	 * Search for a file on the website
	 *
	 * @param string $keyword The searchphrase to use
	 * @return array The filesystem ids of files matching the search
	 */
	public function searchWebFiles($keyword) {
		$ids = array();
		$like = sql_syntax("like");
		$q = sprintf("select id from filesys_files where name %1\$s '%%%2\$s%%' or description %1\$s '%%%2\$s%%'",
			$like, $keyword);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			if ($this->checkWebPermissions($row["id"]))
				$ids[$row["id"]] = 1;
		}
		return $ids;
	}
	/* }}} */
	/* cleanOrphanedItems {{{ */
	/**
	 * Echo file id's that have no bindata anymore
	 */
	public function cleanOrphanedItems() {
		/* filesys data */
		$fspath = $GLOBALS["covide"]->filesyspath."/bestanden/";
		echo "\nfilesystem: filesys_data\n";
		$dir = scandir($fspath);
		foreach ($dir as $k=>$v) {
			$f = (int)$v;
			if ($f > 0) {
				$q = sprintf("select count(*) from filesys_files where id = %d", $f);
				$res = sql_query($q);
				if (sql_result($res,0) == 0) {
					echo sprintf("[%d] could not find file id: %d\n", $f, $f);
				}
			}
		}
	}
	/* }}} */
	/* cleanup_empty {{{ */
	public function cleanup_empty($parentfolder) {
		$sql = sprintf("SELECT name FROM filesys_folders WHERE id = %d", $parentfolder);
		$res = sql_query($sql);
		$foldername = sql_result($res,0);
		switch ($foldername) {
		case "relaties":
			$doneflag = "R";
			$checktable = "address";
			$checkfield = "address_id";
			break;
		case "projecten":
			$doneflag = "P";
			$checktable = "project";
			$checkfield = "project_id";
			break;
		}
		if (array_key_exists("filesystem_checked", $GLOBALS["covide"]->license)) {
			if (!preg_match("/$doneflag/", $GLOBALS["covide"]->license["filesystem_checked"])) {
				/* extract permissions for this user */
				$user_data = new User_data();
				$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);

				$sql = sprintf("SELECT id, address_id, project_id FROM filesys_folders WHERE (user_id IS NULL OR user_id = 0) AND parent_id = %d", $parentfolder);
				$res_loop = sql_query($sql);
				while ($row = sql_fetch_assoc($res_loop)) {
					$q = sprintf("SELECT COUNT(*) FROM filesys_folders WHERE parent_id = %d", $row["id"]);
					$res = sql_query($q);
					$count = sql_result($res,0);
					$q = sprintf("SELECT COUNT(*) FROM filesys_files WHERE folder_id = %d", $row["id"]);
					$res = sql_query($q);
					$count2 = sql_result($res,0);
					$count_total = $count+$count2;
					if ($count_total == 0) {
						$q = sprintf("SELECT COUNT(*) FROM %s WHERE id = %d", $checktable, $row[$checkfield]);
						$res2 = sql_query($q);
						if (sql_result($res2, 0) <= 0) {
							/* delete folder */
							$q = sprintf("DELETE FROM filesys_folders WHERE id = %d", $row["id"]);
							sql_query($q);
						}
					}
				}
				/* write flag in the database */
				$sql = sprintf("UPDATE license SET filesystem_checked = '%s'", $GLOBALS["covide"]->license["filesystem_checked"].$doneflag);
				sql_query($sql);
			}
		}
	}
	/* }}} */
	/* checkDuplicates {{{ */
	/**
	 * Check if a file with the given name is already in specified folder
	 *
	 * @param string $filename The name to check
	 * @param int $folderid The folder to look for this filename
	 * @return string Filename with optional (N) ppostfix if file already exists
	 */
	public function checkDuplicates($filename, $folderid) {
		$name =& $filename;
		$id   =& $folderid;
		/* check for duplicates */
		$file_ok = 0;
		while ($file_ok == 0) {
			if ($file_prefix) {
				$tname = explode(".", $name);
				$tempext = array_pop($tname);
				$tname = sprintf("%s(%d).%s", implode(".", $tname), $file_prefix, $tempext);
				$q = sprintf("select count(*) from filesys_files where folder_id = %d and name like '%s'",
					$id, $tname);
			} else {
				$q = sprintf("select count(*) from filesys_files where folder_id = %d and name like '%s'",
					$id, $name);
			}
			$res = sql_query($q);
			if (sql_result($res,0) == 0) {
				$file_ok = 1;
			} else {
				$file_prefix++;
			}
		}
		if ($file_prefix) {
			$name = $tname;
		}
		return $name;
	}
	/* }}} */
	/* FS_calculatePath {{{ */
	/**
	 * calculate new path for a/b/c folder structure
	 *
	 * @param string $file The filename
	 * @return string The new location for the file, ex 0/1/2/10.txt
	 */
	public function FS_calculatePath($file) {
		/* get file id */
		$id = preg_replace("/^(\d{1,})\..*$/s", "$1", basename($file));

		/* left string padding */
		$id = sprintf("%05s", $id);

		/* dir calculation */
		$id = substr($id, 0, 3);
		$dir = preg_split('//', $id, -1, PREG_SPLIT_NO_EMPTY);
		$dir = implode("/", $dir);

		$new_file = sprintf("%s/%s/%s", dirname($file), $dir, basename($file));

		return $new_file;
	}
	/* }}} */
	/* FS_migrateFiles {{{ */
	/**
	 * Move binary files from root to subdir like 0/1/2
	 *
	 * @param int $nolimit if set will only process this amount of files per run
	 */
	public function FS_migrateFiles($nolimit=0) {
		set_time_limit(60*60*4);

		$dirs = array("bestanden", "email", "maildata");
		foreach ($dirs as $d) {
			$path = sprintf("%s/%s", $GLOBALS["covide"]->filesyspath, $d);
			$dir = scandir($path);
			foreach ($dir as $d) {
				$file = sprintf("%s/%s", $path, $d);
				if (!is_dir($file)) {
					if (!$nolimit)
						$i++;

					if ($i > 5000) return;

					$this->FS_checkFilePath($file);
				}
			}
		}
	}
	/* }}} */
	/* FS_checkFilePath {{{ */
	/* move file to a subdirectory like 0/1/0/<file> */
	private function FS_checkFilePath($file) {
		$new_file = $this->FS_calculatePath($file);

		if (!file_exists(dirname($new_file))) {
			// umask(022);
			mkdir(dirname($new_file), 0777, 1);
		}
		if (file_exists($file))
			rename($file, $new_file);
	}
	/* }}} */
	/* FS_readFile {{{ */
	/**
	 * read a file from the filesystem
	 *
	 * @param string $file The file to read
	 * @return string The binary content of the file
	 */
	public function FS_readFile($file) {
		//$file = dirname($file)."/".strtolower(basename($file));

		$new_file = $this->FS_calculatePath($file);
		if (!file_exists($file) && !file_exists($file.".gz") && !file_exists($new_file) && !file_exists($new_file.".gz"))
			$file = dirname($file)."/".strtolower(basename($file));
			$new_file = $this->FS_calculatePath($file);
		if (file_exists($file.".gz") || file_exists($new_file.".gz")) {
			/* check for duplicate */
			if (file_exists($file))     unlink($file);
			if (file_exists($new_file)) unlink($new_file);

			/* return gzipped version */
			if (file_exists($file.".gz"))
				return $this->gzfile_get_contents($file.".gz");
			else
				return $this->gzfile_get_contents($new_file.".gz");

		} else {
			if (file_exists($file))
				return file_get_contents($file);
			else
				return file_get_contents($new_file);
		}
	}
	/* }}} */
	/* FS_checkFile {{{ */
	/**
	 * check if a file does exist on the filesystem
	 *
	 * @param string The file
	 * @return bool true if the file is there, false if not
	 */
	public function FS_checkFile($file) {

		$new_file = $this->FS_calculatePath($file);
		if (file_exists($file.".gz") || file_exists($new_file.".gz")) {
			/* check for duplicate */
			if (file_exists($file))     unlink($file);
			if (file_exists($new_file)) unlink($new_file);

			/* return gzipped version */
			if (file_exists($file.".gz"))
				$return = $file.".gz";
			else
				$return = $new_file.".gz";

		} else {
			if (file_exists($file))
				$return = $file;
			else
				$return = $new_file;
		}
		if (file_exists($return))
			return true;
		else
			return false;
	}
	/* }}} */
	/* gzfile_get_contents {{{ */
	/**
	 * read gzipped file content and unzip on-the-fly
	 *
	 * @param string $filename The file to open
	 * @param int $use_include_path flag for gzopen
	 * @return string the binary unzipped data
	 */
	private function gzfile_get_contents($filename, $use_include_path = 0) {
		$file = @gzopen($filename, 'rb', $use_include_path);
		if ($file) {
			$data = '';
			while (!gzeof($file)) {
				$data .= gzread($file, 1024);
			}
			gzclose($file);
		}
		return $data;
	}
	/* }}} */
	/* FS_compressFile {{{ */
	/**
	 * compress a file uploaded to the filesystem
	 *
	 * @param string $file filename to compress (based on license setting)
	 */
	public function FS_compressFile($file) {
		if (file_exists($file.".gz"))
			unlink($file.".gz");

		if ($GLOBALS["covide"]->license["enable_filestore_gzip"] == 1) {
			$cmd = sprintf("gzip %s", escapeshellarg($file));
			exec($cmd, $ret, $retval);
		}

		/* move the file to a subdirectory */
		if (file_exists($file.".gz"))
			$this->FS_checkFilePath($file.".gz");
		else
			$this->FS_checkFilePath($file);
	}
	/* }}} */
	/* FS_removeFile {{{ */
	/**
	 * Remove a file from the filesystem
	 *
	 * @param string $file the filename
	 */
	public function FS_removeFile($file) {
		$file_new = $this->FS_calculatePath($file);

		if (file_exists($file))           unlink($file);
		if (file_exists($file.".gz"))     unlink($file.".gz");
		if (file_exists($file_new))       unlink($file_new);
		if (file_exists($file_new.".gz")) unlink($file_new.".gz");
	}
	/* }}} */
	/* FS_copyFile {{{ */
	/**
	 * Copy binary file data with optional gzip compression
	 *
	 * @param string $src The file to copy
	 * @param string $dest The new file
	 */
	public function FS_copyFile($src, $dest) {
		$src_new   = $this->FS_calculatePath($src);

		if (file_exists($src.".gz") || file_exists($src_new.".gz")) {
			if (file_exists($src.".gz"))
				$src .= ".gz";
			else
				$src = $src_new.".gz";

			$dest .= ".gz";
			copy($src, $dest);
		} elseif (file_exists($src_new))
			copy($src_new, $dest);
		elseif (file_exists($src))
			copy($src, $dest);

		$this->FS_checkFilePath($dest);
	}
	/* }}} */
	/* getFolderIdByName {{{ */
	/**
	 * Get the id of the specified foldername
	 *
	 * @param string $name The foldername
	 * @param int $parent_id The parent of the folder we want
	 * @return mixed false if not found, the folderid otherwise
	 */
	public function getFolderIdByName($name, $parent_id) {
		$q = sprintf("select id from filesys_folders where parent_id = %d and name like '%s'",
			$parent_id, $name);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0)
			return false;
		else
			return sql_result($res,0);
	}
	/* }}} */
	/* getFileIdByName {{{ */
	/**
	 * Get the id of the specified filename
	 *
	 * @param string $name The filename
	 * @param int $folder_id The folder to look for this file
	 * @return mixed false if not found, The file id outherwise
	 */
	public function getFileIdByName($name, $folder_id) {
		$q = sprintf("select id from filesys_files where folder_id = %d and name like '%s'",
			$folder_id, $name);
		$res = sql_query($q);
		if (sql_num_rows($res) == 0)
			return false;
		else
			return sql_result($res,0);
	}
	/* }}} */
	/* checkFilesysQuota {{{ */
	/**
	 * Check if any filestore quotas are active
	 *
	 * @return mixed - array (quota_limit, quota_left, quota_current) or false if no quota is active
	 */
	public function checkFilesysQuota() {
		if ($GLOBALS["covide"]->license["filesys_quota"]) {
			$conversion = new Layout_conversion();

			$quota = trim($GLOBALS["covide"]->license["filesys_quota"]);

			// M and G conversion
			if (preg_match("/M$/s", $quota))
				$quota = preg_replace("/[^0-9]/s", "", $quota) * 1024 * 1024;
			elseif (preg_match("/G$/s", $quota))
				$quota = preg_replace("/[^0-9]/s", "", $quota) * 1024 * 1024 * 1024;

			$quotas = array();
			$quotas["limit"] = $quota;

			// calculate current quota
			$cmd = sprintf("du -sb %s | cut -f 1", escapeshellarg($GLOBALS["covide"]->filesyspath));
			exec($cmd, $ret, $retval);
			$quotas["current"] = $ret[0];

			// calculate left space
			$quotas["left"] = $quotas["limit"] - $quotas["current"];

			// calculate info
			$quotas["info"] = sprintf("(%s of %s)", 
				$conversion->convert_to_bytes($quotas["current"]),
				$conversion->convert_to_bytes($quotas["limit"])
			);
			
			return $quotas;

		} else {
			return false;
		}
	}

}
?>
