<?php

		/* some extra special folders that should be translated */
		$translate = array("medewerkers", "oud-medewerkers");

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
			case "mijn sync files":
				if ($_SESSION["user_id"] == $row["user_id"]) {
					$xs = "W";
				} else {
					$xs = "D";
				}
				break;
			case "projecten":
			case "declaration templates":
				/* upgrade permissions */
				if ($user_info["xs_projectmanage"] || $user_info["xs_filemanage"]) {
					$xs = "W";
				} else {
					$xs = $this->checkPermissions($row["id"], "projects");
				}
				if (!$xs) {
					$row["name"] = gettext("no access");
				}
				break;
			case "relaties":
				/* upgrade permissions */
				if (($user_info["xs_addressmanage"] && $user_info["relationmanage"]) || $user_info["xs_filemanage"]) {
					$xs = "W";
				} else {
					/*check address classifications */

					/* if strict permissions are set */
					if ($GLOBALS["covide"]->license["address_strict_permissions"]) {
						$a_ok = 0;
						/* get the address folder id */
						if (!$row["address_id"]) {
							/* try search backwards recursive till level 'relations' is reached */
							$relfolder = $this->getHighestParentSubfolder($row["id"], $hp["id"]);
							if ($relfolder["address_id"])
								$row["address_id"] = $relfolder["address_id"];
						}
						if ($row["address_id"]) {
							/* create address object */
							if (!$this->_cache["address_data"]) {
								$address_data = new Address_data();
								$this->_cache["address_data"] = $address_data;
							}
							$addressinfo[0] = $this->_cache["address_data"]->getAddressById($row["address_id"]);

							if (!$this->_cache["cla_permissions"]) {
								$classification_data = new Classification_data();
								$cla_permission = $classification_data->getClassificationByAccess(1);
								$this->_cache["cla_permissions"] = $cla_permission;
							}
							$cla_address = explode("|", $addressinfo[0]["classifi"]);
							foreach ($cla_address as $k=>$v) {
								if ($v && in_array($v, $this->_cache["cla_permissions"]))
									$a_ok = 1;
							}
						}
					}
					/* end strict permission check */
					if ($a_ok == 1) {
						$xs = "W";
					} else {
						$xs = $this->checkPermissions($row["id"], "relations");
					}
				}
				break;
			case "hrm":
				$check_extra_translate = 1;
				if ($user_info["xs_hrmmanage"]) {
					$xs = "W";
				} elseif ($user_info["id"] == $row["hrm_id"]) {
					$xs = "R";
				} else {
					$xs = "D";
				}
				if (!$xs) {
					$row["name"] = gettext("no access");
				}
				break;
			case "root":
				$xs = "R";
				break;
			case "cms":
				if (!$GLOBALS["covide"]->license["has_cms"]) {
					/* check for additional permissions */
					if ($user_info["xs_filemanage"]) {
						$xs = "W";
					} else {
						//check here for special high parent permissions
						$xs = $this->checkPermissions($row["id"]);
					}
					/* if no permission, upgrade to max */
					if (!$xs) {
						$xs = "W";
					}
				} else {
					if ($user_info["xs_cms_level"] >= 2) {
						$xs = "W";
					} else {
						$xs = $this->checkPermissions($row["id"]);
					}
				}
				break;
			default:
				/* check for additional permissions */
				if ($user_info["xs_filemanage"]) {
					$xs = "W";
				} else {
					//check here for special high parent permissions
					$xs = $this->checkPermissions($row["id"]);
				}
				/* if no permission, upgrade to max */
				if (!$xs) {
					$xs = "W";
				}
				break;
		}
		/* if parent id = 0 and folder name != my docs then no file permissions (only folder) */
		if ($row["parent_id"] == 0 && $row["name"] != "hrm" && $user_info["xs_filemanage"]) {
			$xs = "S";
		} elseif ($row["parent_id"] == 0 && $row["name"] != "hrm"
			&& $row["name"] != "projecten" && $row["name"] != "relaties") {
			$xs = "S";
		}
		if ($row["parent_id"] == 0 && $row["name"] == "mijn documenten") {
			$xs = "W";
		}
		if ($row["parent_id"] == 0 && $row["name"] == "mijn sync files") {
			$xs = "W";
			$row["xs_sync"] = 1;
		}
		if ($row["parent_id"] == 0 && $row["name"] == "declaration templates"
			&& ($user_info["xs_filemanage"] || $user_info["xs_projectmanage"])) {
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

		if ($hp["name"] == "cms" && $user_info["xs_cms_level"] >= 2 && !$_REQUEST["subaction"])
			$row["xs_edit"] = 1;

		/* some special icons */
		if ($row["parent_id"]==0) {
			switch ($row["name"]) {
				case "mijn documenten":
					$row["foldericon"] = "folder_my_docs";
					$row["h_name"] = gettext($row["name"]);
					break;
				case "mijn sync files":
					$row["foldericon"] = "folder_sync";
					$row["h_name"] = gettext($row["name"]);
					break;
				case "mijn mappen":
					$row["foldericon"] = "folder_myfolders";
					$row["h_name"] = gettext($row["name"]);
					break;
				case "google mappen":
					$row["foldericon"] = "google";
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
				case "cms":
					$row["foldericon"] = "folder_shared";
					$row["h_name"] = gettext("cms and public files");
					break;
				default:
					$row["foldericon"] = "folder_closed";
					if (!$row["parent_id"]) {
						$row["h_name"] = gettext($row["name"]);
					} else {
						/* some folders need to be translated */
						/* ONLY in the folder HRM */
						if (in_array($row["name"], $translate) && $check_extra_translate)
							$row["h_name"] = gettext($row["name"]);
						else
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
			/* some extra special folders that should be translated */
			if (in_array($row["name"], $translate) && $check_extra_translate)
				$row["h_name"] = gettext($row["name"]);
			else
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

		if ($xs == "W" && $row["parent_id"] > 0) {
			/* additional relation or projects check */
			if ($hp["name"]=="relaties") {
				if ($hp["id"] == $row["parent_id"]) {
					$q = sprintf("select count(*) from address where id = %d", $row["address_id"]);
					$res2 = sql_query($q);
					if (sql_result($res2,0)>0) {
						$row["xs_folder_actions"] = 0;
					} else {
						$row["xs_folder_actions"] = 1;
					}
				} else {
					$row["xs_folder_actions"] = 1;
				}
			} elseif ($hp["name"]=="projecten") {
				if ($hp["id"] == $row["parent_id"]) {
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
			} else {
				$row["xs_folder_actions"] = 1;
			}
		}
		/* if pastebuffer is active, do not allow folder modifications */
		if ($_REQUEST["pastebuffer"] || $_REQUEST["subaction"]) {
			$row["xs_folder_actions"] = 0;
		}

		/* if we have a subaction, do not allow updates on the view */
		if ($_REQUEST["subaction"] && $row["xs"] == "W") {
			$row["xs_subaction"] = "X";//$row["xs"];
			$row["xs"] = "X";
		} elseif ($_REQUEST["subaction"] && $row["xs"] != "D") {
			$row["xs_subaction"] = $row["xs"];
			$row["xs"] = "R";
		}

		if ($downgrade_permissions && ($row["xs"]=="W" || $row["xs"]=="S")) {
			$row["xs"] = "R";
		}

		if ($row["name"] == "projecten" && $GLOBALS["covide"]->license["has_project_declaration"])
			$row["h_name"] = gettext("dossier");

		if ($row["name"] == "mijn mappen") {
			$row["xs_edit"] = 0;
			$row["xs"] = "R";
			$xs = "R";
		}
?>
