<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}
	//$cms_license = $this->cms->getCmsSettings();
	$cms_license = $this->cms_license;

	switch ($_REQUEST["mode"]) {
		case "shopadd":
			if (!$_REQUEST["id"]) {
				echo sprintf("alert('%s');", addslashes(gettext("No article specified")));
				exit();
			} elseif ((int)$_REQUEST["num"] < 0) {
				echo sprintf("alert('%s');", addslashes(gettext("No valid count specified")));
				exit();
			} else {
				$q = sprintf("select shopPrice from cms_data where id = %d", $_REQUEST["id"]);
				$res = sql_query($q);
				$price = sql_result($res,0,"",2);

				if ($price <= 0) {
					echo sprintf("alert('%s');", addslashes(gettext("Article has no valid price")));
					exit();
				} else {
					if ($_REQUEST["reset"]) {
						if ($_REQUEST["num"] == 0) {
							unset($_SESSION["shop"][(int)$_REQUEST["id"]]);
						} else {
							$_SESSION["shop"][(int)$_REQUEST["id"]] = 0;
						}
					}
					if ($_REQUEST["num"] > 0)
						$_SESSION["shop"][(int)$_REQUEST["id"]] += (int)$_REQUEST["num"];

					if ($_REQUEST["reset"])
						echo "location.href='/mode/shopcontents';";
					else
						echo sprintf("alert('%s');",
							addslashes(gettext("The article has been added to your shopping cart.")));

					exit();
				}
			}
			exit();
			break;
		case "shopcontents":
			if (!$this->cms_license["has_shop"]) {
				$this->triggerError(403);
				exit("Module is disabled");
			}
			break;
		case "form":
			if (!$cms_license["cms_forms"]) {
				$this->triggerError(403);
				exit("Module is disabled");
			}
			$this->sendform($_REQUEST, $_FILES);
			exit();
			break;
		case "formresult":
			if (!$cms_license["cms_forms"]) {
				$this->triggerError(403);
				exit("Module is disabled");
			}
			$this->formresult($_REQUEST["result"]);
			exit();
		case "sponsors":
			$this->getBannersXML($_REQUEST["count"], $_REQUEST["h"]);
			exit();
			break;
		case "sponsor":
			$banner = (int)$_REQUEST["id"];
			if (!$banner) {
				$this->pageid = "__err404";
			} else {
				$q = sprintf("select url from cms_gallery_photos where id = %d", $banner);
				$res = sql_query($q);
				if (sql_num_rows($res) > 0) {
					$location = sql_result($res,0);

					$currdate = mktime(0,0,0,date("m"),date("d"),date("Y"));
					$q = sprintf("select count(*) from cms_banner_views where banner_id = %d
						and datetime = %d", $banner, $currdate);
					$res2 = sql_query($q);
					if (sql_result($res2,0,"",2) == 0) {
						$q = sprintf("insert into cms_banner_views (banner_id, datetime, visited, clicked)
							values (%d, %d, 1, 1)", $banner, $currdate);
						sql_query($q);
					} else {
						$q = sprintf("update cms_banner_views set clicked = clicked + 1 where
							banner_id = %d and datetime = %d", $banner, $currdate);
						sql_query($q);
					}
					if (!$location) {
						$this->pageid = "__err404";
					} else {
						$this->triggerError(301);
						header(sprintf("Location: %s", $location));
						exit();
					}
				} else {
					$this->triggerError(404);
				}
			}
			break;
		case "feedback":
			if (!$cms_license["cms_feedback"]) {
				$this->triggerError(403);
				exit("Module is disabled");
			}
			$this->saveFeedback($_REQUEST);
			exit();
		case "menu":
			$this->generate_menu_loader((int)$_REQUEST["pid"]);
			exit();
		case "text":
			$this->textPage();
			exit();
		case "calendar":
			$this->loadCalendar($_REQUEST["page"], $_REQUEST["start"]);
			exit();
		case "rss":
			$this->sendHeadersCache();
			$this->rssPage();
			exit();
		case "google":
			if (!$cms_license["cms_searchengine"]) {
				$this->triggerError(403);
				exit("Module is disabled");
			}
			$this->sendHeadersCache();
			$this->googleMaps();
			break;
		case "googlegz":
			if (!$cms_license["cms_searchengine"]) {
				$this->triggerError(403);
				exit("Module is disabled");
			}
			$this->sendHeadersCache();
			$this->googleItems();
			break;
		case "sitemap_plain":
			$this->textPage();
			exit();
		case "robots":
			$this->sendHeadersCache();
			$this->generate_robots_file();
			exit();
		case "abbreviations":
			$this->loadAbbreviations();
			exit();
		case "favicon":
			$this->triggerError(301);
			header(sprintf("Location: %s", $this->favicon));
			exit();
		case "covideloginalt":
			break;
		case "covidelogin":
			//$this->triggerError(403);
			//cms login
			$uri = sprintf("http://".$this->http_host);
			if ($_REQUEST["uri"]) {
				$uri .= "/page/".$_REQUEST["uri"];
			}
			header(sprintf("Location: /?mod=desktop&uri=%s", urlencode($uri)));
			exit();
			break;
		case "covidelogout":
			$this->start_html(1);
			$output = new Layout_output();
			$output->start_javascript();
			$output->addCode("window.close();");
			$output->end_javascript();
			echo $output->generate_output();
			$this->end_html();
			exit();
		case "login":
		case "loginprofile":
			//foo
			break;
		case "loginimage":
			/*
			$name = "covide.png";
			header("Content-Type: image/png");
			if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) {
				header("Content-Disposition: filename=\"".$name."\"");
			} else {
				header("Content-Disposition: attachment; filename=\"".$name."\"");
			}
			*/
			if ($_SESSION["user_id"]) {
				header("Location: /themes/default/icons/jabber_online.png");
			} else {
				header("Location: /themes/default/icons/jabber_offline.png");
			}
			exit();
	}
?>