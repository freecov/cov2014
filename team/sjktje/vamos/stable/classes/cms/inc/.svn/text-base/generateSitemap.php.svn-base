<?php
	// generate the complete visible sitemap
	//global $toonpages, $pids, $sids, $knipids, $paste_state, $del_date, $db_fields;

	/* cache user permissions */
	if (!$cms_data->sitemap_cache["user_perms"]) {
		$user_data = new User_data();
		$perms = $user_data->getUserPermissionsById($_SESSION["user_id"]);
		$cms_data->sitemap_cache["user_perms"] = $perms;
	} else {
		$perms = $cms_data->sitemap_cache["user_perms"];
	}

	/* cache parentpages */
	if (!$cms_data->sitemap_cache["parentpages"]) {
		$cms_data->sitemap_cache["parentpages"] = array();
		$q = "select parentPage from cms_data group by parentPage";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$cms_data->sitemap_cache["parentpages"][] = $row["parentPage"];
		}
	}
	/* cache dateinfo */
	if (!$cms_data->sitemap_cache["datepages"]) {
		$cms_data->sitemap_cache["datepages"] = array();
		$q = "select pageid from cms_date group by pageid";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$cms_data->sitemap_cache["datepages"][] = $row["pageid"];
		}
	}

	$paste_state =& $cms_data->opts["paste_state"];

	if (!$cms_data->sitemap_cache["db_fields"]) {
		$db_fields = "
			id, parentPage, pageTitle, pageLabel, datePublication,
			pageRedirect, isPublic, isActive, isMenuItem, keywords,
			apEnabled, isTemplate, isList, useMetaData, isSticky,
			search_fields, search_descr, isForm, date_start, date_end,
			date_changed, notifyManager, isGallery, pageRedirectPopup,
			popup_data, new_state, search_title, search_language,
			search_override, pageAlias, isSpecial, date_last_action,
			google_changefreq, google_priority, autosave_info
		";
		$db_fields = explode(",", $db_fields);
		foreach ($db_fields as $k=>$v) {
			$v = trim($v);
			$db_fields[$k] = sprintf("`cms_data`.`%s`", $v);
		}
		$db_fields = implode(", ", $db_fields);
		$cms_data->sitemap_cache["db_fields"] = $db_fields;
	} else {
		$db_fields = $cms_data->sitemap_cache["db_fields"];
	}

	// get all pages that have the same parentPage as in $id
	if ($special) {
		$sql = "select ".$db_fields." from cms_data where id = $id";
	} else {
		$sql = "select ".$db_fields." from cms_data where parentPage = $id order by pageLabel, pageTitle";
	}

	$res = sql_query($sql) or die($sql.sql_error());
		// for each page
		while ($row = sql_fetch_assoc($res)) {

			/* if apEnabled flag != current site root */
			if ($row["apEnabled"] != $this->current_root)
				$cms_data->updateApEnabled($row["id"], $this->current_root);


			// check permissions on this page - user based
			$r = $cms_data->getUserPermissions($row["id"], $_SESSION["user_id"]);

			// scan for any subpages under this page
			if (in_array($row["id"], $cms_data->sitemap_cache["parentpages"]))
				$multi = 1;
			else
				$multi = 0;

			/*
			$q = "select count(id) from cms_data where parentPage = ".$row["id"];
			$resc = sql_query($q) or die($q);
			if (sql_result($resc,0)>0) {
				$multi = 1;
			} else {
				$multi = 0;
			}
			*/

			// check page-specific permission for this page
			if ($cms_data->checkPagePermissions($row["id"])==true)
				$locked = 1;
			else
				$locked = 0;

			// does this page has additional metasearch information
			if ($row["search_fields"] || $row["search_descr"])
				$searchinfo = 1;
			else
				$searchinfo = 0;

			// does this page include any date-specific attributes
			// check for publication ranges and calendar items
			if ($row["date_start"]>0 || $row["date_end"]>0)
				$daterange = 1;
			else
				$daterange = 0;

			if (in_array($row["id"], $cms_data->sitemap_cache["datepages"]))
				$dateinfo = 1;
			else
				$dateinfo = 0;

			$metainfo   = (int)$row["useMetaData"];	//does this page have meta data
			$isForm     = (int)$row["isForm"];			//does this page have a form
			$isLijst    = (int)$row["isList"];			//does this page have a list
			$isActive   = (int)$row["isActive"];		//is this page active
			$isMenuItem = (int)$row["isMenuItem"];	//is this page part of the site menu (if any)
			$isPublic   = (int)$row["isPublic"];		//is this page public
			$isTemplate = (int)$row["isTemplate"];	//can this page be used as a template
			$isGallery  = (int)$row["isGallery"];		//does this page contain a gallery

			// do we have any redirects on this page
			if (trim($row["pageRedirect"])!="") {
				$isRedirect = 1;
			} else {
				$isRedirect = 0;
			}

			$viewRight = ($r["viewRight"]);			//can this user view this page
			$editRight = ($r["editRight"]);			//can this user edit this page
			$deleteRight = ($r["deleteRight"]);	//can this user delete this page
			$manageRight = ($r["manageRight"]); //can this user manage this page

			//is the current user a global manager or the pageid is zero
			if ($perms["xs_cms_level"] >= 2 || $row["parentPage"] == 0) {
				$viewRight = 1;
				$editRight = 1;
				$deleteRight = 1;
				$manageRight = 1;
			}
			if ($perms["xs_cms_level"] <= 1 && $row["parentPage"] == 0) {
				$editRight = 0;
				$deleteRight = 0;
				$manageRight = 0;
			}
			//is this page sticky
			$isSticky = (int)$row["isSticky"];

			//create the following arrays if they don't exist
			$sids    =& $cms_data->opts["sids"];
			$buffer  =& $cms_data->opts["buffer"];

			if (!is_array($sids))    $sids = array();
			if (!is_array($buffer))  $buffer = array();

			//special color selection needed?
			if (in_array($row["id"], $buffer)) {
				//does this page match the search array
				$bgcolor = "#CCFFCC";
				$matched = 1;
			} elseif (in_array($row["id"], $sids)) {
				//does this page match the selected items
				$bgcolor = "#FFCCCC";
				$matched = 1;
			} else {
				//normal color selection
				$matched = 0;
				switch ($level) {
					case 0: $bgcolor = "#ffebb0"; break;
					case 1: $bgcolor = "#ffffff"; break;
					case 2: $bgcolor = "#eeeeee"; break;
					case 3: $bgcolor = "#dddddd"; break;
					case 4: $bgcolor = "#cccccc"; break;
					case 5: $bgcolor = "#bbbbbb"; break;
					case 6: $bgcolor = "#aaaaaa"; break;
					case 7: $bgcolor = "#999999"; break;
					default: $bgcolor = "#888888"; break;
				}
			}
			//if the pagelabel is set
			$pageLabel = "'";
			if ($_SESSION["seltype"]!="D" || $row["isSpecial"]=="D") {
				if ($row["pageAlias"]) {
					$pageLabel .= "[".addslashes($row["pageAlias"]).".htm] ";
				}
				if ($row["pageLabel"]) {
					$pageLabel .= "(".addslashes($row["pageLabel"]).")";
				} else {
					$pageLabel .= "";
				}
			} else {
				$ts = ($row["date_last_action"]-$del_date);
				$ts = (int)($ts/60/60/24);
				$pageLabel .= gettext("deleted").": ".$ts." ".gettext("days");
			}
			$pageLabel .= "'";
			//if the parentPage is > zero
			if ($row["parentPage"]>0) {
				//if there is a page title
				if (!trim($row["pageTitle"])){
					$ptitel = "[".gettext("no title")."]";
				} else {
					$ptitel = str_replace("'", "`", strip_tags( preg_replace("/<br>/si"," ",$row["pageTitle"]) ));
				}
			} else {
				switch ($row["isSpecial"]) {
					case "R":
						$ptitel = gettext("Default siteroot");
						break;
					case "X":
						$ptitel = gettext("Protected items");
						$bgcolor = "#d0d2ff";
						break;
					case "D":
						$ptitel = gettext("Deleted items");
						$bgcolor = "#ffd0d0";
						break;
					default:
						$settings = $cms_data->getCmsSettings($row["id"]);
						$settings["cms_hostnames"] = str_replace("\n", ", ", $settings["cms_hostnames"]);
						$settings["cms_hostnames"] = preg_replace("/(\r|\t|\n)/s", "", $settings["cms_hostnames"]);

						$ptitel = sprintf("%s (%s)", $row["pageTitle"], $settings["cms_hostnames"]);
						$bgcolor = "#e3e5e4";
						break;
				}
			}

			//do we need to show this page or not?
			$toonpages =& $cms_data->opts["toonpages"];
			if (!is_array($toonpages)) $toonpages = array();
			if (in_array($row["parentPage"], $toonpages ) || $level <= 1) {
				$tonen = 1;
			} else {
				$tonen = 0;
			}

			//is this page needed to be expanded in the view?
			$pids =& $cms_data->opts["pids"];
			if (!is_array($pids)) $pids = array();
			if (in_array($row["id"], $pids )) {
				if (in_array($row["id"], $toonpages )) {
					$uit = 1;
				} else {
					$uit = 0;
				}
			} else {
				$uit = -1;
			}

			/* detect paste state changes */
			/* do not allow a page pasted inside the selection */
			if ($level == $paste_state) {
				//if level is back at the changed level
				$paste_state = 0;
			}
			//if the current page is in the selection
			if ($paste_state == 0 && in_array($row["id"], $buffer)) {
				//set a locking state
				$paste_state = $level;
			}

			//permission check
			if ($paste_state == 0) {
				if ($editRight && $deleteRight && $buffer) {
					//paste permissions
					$allowplak = 2;
				} elseif ($buffer) {
					//no paste permissions
					$allowplak = 1;
				} else {
					//determine paste permissions later
					$allowplak = 0;
				}
			} else {
				//no paste permissions
				$allowplak = 1;
			}

			//check version control field
			if ($row["new_state"]) {
				if ($row["new_state"]=="N") {
					$new_state = "&nbsp;<font color=\"green\"><b>N</b></font>";
				} elseif ($row["new_state"]=="U") {
					$new_state = "&nbsp;<font color=\"orange\"><b>U</b></font>";
				} elseif ($row["new_state"]=="D") {
					$new_state = "&nbsp;<font color=\"red\"><b>D</b></font>";;
				}
			} else {
				$new_state = "";
			}

			if ($row["autosave_info"]) {
				$important = 1;
			} else {
				$important = 0;
			}

			//if we are allowed to view this page
			if ($tonen && $viewRight) {

				//generate option array
				$opts = array();
				$opts[] = $row["id"];
				$opts[] = "'".addslashes($ptitel)."'";
				$opts[] = $deleteRight;
				$opts[] = $viewRight;
				$opts[] = $manageRight;
				$opts[] = $editRight;
				$opts[] = (int)$level;
				$opts[] = "'".$bgcolor."'";
				$opts[] = $row["parentPage"];
				$opts[] = $uit;
				$opts[] = $matched;
				$opts[] = $locked;
				$opts[] = $isSticky;
				$opts[] = $isActive;
				$opts[] = $isRedirect;
				$opts[] = $isMenuItem;
				$opts[] = $isPublic;
				$opts[] = $isTemplate;
				$opts[] = $multi;
				$opts[] = $searchinfo;
				$opts[] = $dateinfo;
				$opts[] = $daterange;
				$opts[] = $isLijst;
				$opts[] = $metainfo;
				$opts[] = $isForm;
				$opts[] = $allowplak;
				$opts[] = $isGallery;
				$opts[] = $pageLabel;
				$opts[] = "'".$new_state."'";
				$opts[] = $_SESSION["theme"];
				$opts[] = $important;

				//write sitepage item
				$this->genFp($opts);

				//recursive go into a level deeper
				$this->generateSiteMap($row["id"], $level+1, 0, $cms_data);
			}
		//} //level
	}
?>