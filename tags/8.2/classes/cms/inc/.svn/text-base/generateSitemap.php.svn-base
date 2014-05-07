<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Cms_output")) {
		die("no class definition found");
	}

	// generate the complete visible sitemap
	//global $toonpages, $pids, $sids, $knipids, $paste_state, $del_date, $db_fields;

	/* set a page limit - not implemented yet! */
	$limit = 20;

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
		while ($row = sql_fetch_assoc($res,2)) {
			$cms_data->sitemap_cache["parentpages"][] = $row["parentPage"];
		}
	}
	/* cache dateinfo */
	if (!$cms_data->sitemap_cache["datepages"]) {
		$cms_data->sitemap_cache["datepages"] = array();
		$q = "select pageid from cms_date group by pageid";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res,2)) {
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
			google_changefreq, google_priority, autosave_info,
			address_ids, address_level, isShop, shopPrice
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

	$res = sql_query($sql);
		// for each page
		while ($row = sql_fetch_assoc($res)) {

			/* cut recursion if all info is already on the screen */
			if ($this->sitemap["curr"] >= ($this->pagesize*2) + $this->sitemap["offset"] + 1)
				return;

			// check permissions on this page - user based
			$r = $cms_data->getUserPermissions($row["id"], $_SESSION["user_id"]);

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
			if (count($cms_data->opts["sids"]) > 0) {
				if (!is_array($cms_data->opts["spids"]))
					$cms_data->opts["spids"] = array();
				if (!in_array($row["id"], $cms_data->opts["sids"])
					&& !in_array($row["id"], $cms_data->opts["spids"])
					&& $row["parentPage"] > 0 && !$row["isSpecial"])
					$viewRight = 0;
			}

			if ($viewRight) {
				/* if apEnabled flag != current site root */
				if ($row["apEnabled"] != $this->current_root)
					$cms_data->updateApEnabled($row["id"], $this->current_root);

				// scan for any subpages under this page
				if (in_array($row["id"], $cms_data->sitemap_cache["parentpages"]))
					$multi = 1;
				else
					$multi = 0;

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

				//is this page sticky
				$isSticky = (int)$row["isSticky"];

				//create the following arrays if they don't exist
				$sids    =& $cms_data->opts["sids"];
				$buffer  =& $cms_data->opts["buffer"];

				if (!is_array($sids))    $sids = array();
				if (!is_array($buffer))  $buffer = array();

				//special color selection needed?
				if (in_array($row["id"], $buffer)) {
					//does this page match the buffer array
					$bgcolor = "color_buffer";
					$matched = 1;
				} elseif (in_array($row["id"], $sids)) {
					//does this page match the searched items
					$bgcolor = "color_search";
					$matched = 1;
				} else {
					//normal color selection
					$matched = 0;
					switch ($level) {
						case 0: $bgcolor = "color_level_0"; break;
						case 1: $bgcolor = "color_level_1"; break;
						case 2: $bgcolor = "color_level_2"; break;
						case 3: $bgcolor = "color_level_3"; break;
						case 4: $bgcolor = "color_level_4"; break;
						case 5: $bgcolor = "color_level_5"; break;
						case 6: $bgcolor = "color_level_6"; break;
						case 7: $bgcolor = "color_level_7"; break;
						default: $bgcolor = "color_level_other"; break;
					}
				}
				//if the pagelabel is set
				$pageLabel = "'";
				if ($cms_data->opts["siteroot"] == "D" && $row["parentPage"] > 0) {
					$ts = ($row["date_last_action"]+($cms_data->delete_interval*60*60*24)-mktime());
					$ts = ceil($ts/60/60/24);
					$pageLabel .= gettext("will be deleted in").": ".$ts." ".gettext("days");
				} else {
					if ($row["pageAlias"]) {
						$pageLabel .= sprintf("<a href=\"/page/%s\" target=\"_blank\">[%s]<\\/a>&nbsp;",
							$row["id"], addslashes($row["pageAlias"]));
					}
					if ($row["pageLabel"]) {
						$pageLabel .= "(".addslashes($row["pageLabel"]).")";
					} else {
						$pageLabel .= "";
					}
				}
				if ($row["isShop"])
					$pageLabel.= " &euro; ".str_replace(".", ",", number_format($row["shopPrice"],2));

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
							$bgcolor = "color_type_protected";
							break;
						case "D":
							$ptitel = gettext("Deleted items");
							$bgcolor = "color_type_protected";
							break;
						default:
							$settings = $cms_data->getCmsSettings($row["id"]);
							$settings["cms_hostnames"] = str_replace("\n", ", ", $settings["cms_hostnames"]);
							$settings["cms_hostnames"] = preg_replace("/(\r|\t|\n)/s", "", $settings["cms_hostnames"]);

							$ptitel = sprintf("%s (%s)", $row["pageTitle"], $settings["cms_hostnames"]);
							$bgcolor = "color_type_default";
							break;
					}
				}

				//is this page needed to be expanded in the view?
				$toonpages =& $cms_data->opts["toonpages"];
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

				if ($row["autosave_info"] && $row["autosave_info"] != $row["pageData"])
					$important = 1;
				else
					$important = 0;

				if ($row["address_ids"] && !$row["address_level"])
					$address = 1;
				elseif ($row["address_level"])
					$address = 2;
				else
					$address = 0;


				//do we need to show this page or not?
				if (!is_array($toonpages))
					$toonpages = array();

				if (in_array($row["id"], $toonpages ) || $level < 1)
					$recurse = 1;
				else
					$recurse = 0;

				//compress the search results
				if ($_REQUEST["cms"]["search"]) {
					if (!in_array($row["id"], $pids) && !in_array($row["id"], $sids))
						$viewRight = 0;
				}

				$ptitel = str_replace("'", "`", $ptitel);
				$ptitel = preg_replace("/(\r|\n|\t)/s", "", $ptitel);

				//generate option array
				$opts = array();
				$opts[] = $row["id"];

				if ($_REQUEST["jump_to_anchor"] == sprintf("id%d", $row["id"]))
					$opts[] = sprintf("'<b>%s</b>'", $ptitel);
				else
					$opts[] = sprintf("'%s'", $ptitel);

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
				$opts[] = $address;

				if (!is_array($this->sitemap["pathcache"]))
					$this->sitemap["pathcache"] = array();

				if ($this->sitemap["last_level"] <= $level) {
					if ($level > 0)
						$this->sitemap["pathcache"][(int)$level] = $opts;
				} else {
					foreach ($this->sitemap["pathcache"] as $k=>$v) {
						if ($k > $level)
							unset($this->sitemap["pathcache"][$k]);
					}
				}
				//write sitepage item
				if ($this->sitemap["curr"] == $this->sitemap["offset"]) {
					foreach ($this->sitemap["pathcache"] as $k=>$v) {
						if ($k < $level) {
							$v[9] = -2;
							$this->genFp($v);
						}
					}
				}

				if ((($this->sitemap["curr"] < $this->pagesize + $this->sitemap["offset"]
					&& $this->sitemap["curr"] >= $this->sitemap["offset"]))
					|| $row["parentPage"] == 0) {
					$this->genFp($opts);
				}
				$this->sitemap["curr"]++;

				//recursive go into a level deeper
				if ($recurse) {
					$cms_data->precachePagePermissionsByParent($row["id"]);
					$this->generateSiteMap($row["id"], $level+1, 0, $cms_data);
				}
			}
		//} //level
	}
?>
