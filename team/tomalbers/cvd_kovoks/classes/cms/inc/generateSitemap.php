<?php
		// generate the complete visible sitemap
		//global $toonpages, $pids, $sids, $knipids, $paste_state, $del_date, $db_fields;

		$user_data = new User_data();

		$perms = $user_data->getUserPermissionsById($_SESSION["user_id"]);

		$db_fields = "
			`id`, `parentPage`, `pageTitle`, `pageLabel`, `datePublication`,
			`pageRedirect`, `isPublic`, `isActive`, `isMenuItem`, `keywords`,
			`apEnabled`, `isTemplate`, `isList`, `useMetaData`, `isSticky`,
			`search_fields`, `search_descr`, `isForm`, `date_start`, `date_end`,
			`date_changed`, `notifyManager`, `isGallery`, `pageRedirectPopup`,
			`popup_data`, `new_state`, `search_title`, `search_language`,
			`search_override`, `pageAlias`, `isSpecial`, `date_last_action`,
			`google_changefreq`, `google_priority`, `autosave_info`
		";


		// get all pages that have the same parentPage as in $id
		if ($special) {
			$sql = "select ".$db_fields." from cms_data where id = $id";
		} else {
			$sql = "select ".$db_fields." from cms_data where parentPage = $id order by pageLabel, pageTitle";
		}
		$res = sql_query($sql) or die($sql.sql_error());
			// for each page
			while ($row = sql_fetch_assoc($res)) {

				// check permissions on this page - user based
				$r = $cms_data->getUserPermissions($row["id"], $_SESSION["user_id"]);

				// scan for any subpages under this page
				$q = "select count(id) from cms_data where parentPage = ".$row["id"];
				$resc = sql_query($q) or die($q);
				if (sql_result($resc,0)>0) {
					$multi = 1;
				} else {
					$multi = 0;
				}

				// check page-specific permission for this page
				if ($cms_data->checkPagePermissions($row["id"])==true) {
					$locked = 1;
				} else {
					$locked = 0;
				}

				// does this page has additional metasearch information
				if ($row["search_fields"] || $row["search_descr"]) {
					$searchinfo = 1;
				} else {
					$searchinfo = 0;
				}

				// does this page include any date-specific attributes
				// check for publication ranges and calendar items
				if ($row["date_start"]>0 || $row["date_end"]>0) {
					$dateinfo = 1;
				} else {
					$q = "select count(*) from cms_date where pageid = ".$row["id"];
					$res2 = sql_query($q) or die($q);
					if (sql_result($res2,0)>0) {
						$dateinfo = 1;
					} else {
						$dateinfo = 0;
					}
				}

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

				//is the current user a global manager and is the pageid not zero
				if ($perms["xs_cms_level"] >= 2 || $row["parentPage"]==0) {
					$viewRight = 1;
					$editRight = 1;
					$deleteRight = 1;
					$manageRight = 1;
				}
				if ($perms["xs_cms_level"] <= 1 && $row["parentPage"]==0) {
					$editRight = 0;
					$deleteRight = 0;
					$manageRight = 0;
				}
				//is this page sticky
				$isSticky = (int)$row["isSticky"];

				//create the following arrays if they don't exist
				$sids    =& $cms_data->opts["sids"];
				$knipids =& $cms_data->opts["knipids"];

				if (!is_array($sids))    $sids = array();
				if (!is_array($knipids)) $knipids = array();

				//special color selection needed?
				if (in_array($row["id"],$knipids)) {
					//does this page match the search array
					$bgcolor = "#CCFFCC";
					$matched = 1;
				} elseif (in_array($row["id"],$sids)) {
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
						$pageLabel .= "[".addslashes($row["pageAlias"])."] ";
					}
					if ($row["pageLabel"]) {
						$pageLabel .= "(".addslashes($row["pageLabel"]).")";
					} else {
						$pageLabel .= "";
					}
				} else {
					$ts = ($row["date_last_action"]-$del_date);
					$ts = (int)($ts/60/60/24);
					$pageLabel .= gettext("verwijderd over").": ".$ts." ".gettext("dagen");
				}
				$pageLabel .= "'";
				//if the parentPage is > zero
				if ($row["parentPage"]>0) {
					//if there is a page title
					if (!trim($row["pageTitle"])){
						$ptitel = "[".gettext("geen titel")."]";
					} else {
						$ptitel = str_replace("'", "`", strip_tags( preg_replace("/<br>/si"," ",$row["pageTitle"]) ));
					}
				} else {
					switch ($row["isSpecial"]) {
						case "R":
							$ptitel = gettext("Siteroot (niet bewerkbaar)");
							break;
						case "X":
							$ptitel = gettext("Afgeschermde items");
							$bgcolor = "#d0d2ff";
							break;
						case "D":
							$ptitel = gettext("Verwijderde items");
							$bgcolor = "#ffd0d0";
							break;
						default:
							$ptitel = $row["pageTitle"];
							$bgcolor = "#ffffff";
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
				if ($paste_state == 0 && in_array($row["id"], $knipids)) {
					//set a locking state
					$paste_state = $level;
				}

				//permission check
				if ($paste_state == 0) {
					if ($editRight && $deleteRight && $_SESSION["knippages"]) {
						//wel plak rechten
						$allowplak = 2;
					} elseif ($_SESSION["knippages"]) {
						//geen plak rechten
						$allowplak = 1;
					} else {
						//bepaal zelf knip rechten
						$allowplak = 0;
					}
				} else {
					//no permissions
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