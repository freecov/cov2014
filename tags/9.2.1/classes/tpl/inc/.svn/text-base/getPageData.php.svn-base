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
	echo "\n\n<!-- [start of page data] -->\n\n";
	echo "<div>";
	if (!$id)
		$id = $this->pageid;

	if ($this->checkInternalRequest($id)==1) {
		switch ($id) {
			case "__cmslogin":
				$this->cmsLoginVisitor($_REQUEST);
				break;
			case "__metadata":
				switch ($_REQUEST["show"]) {
					case "result":
					case "rss":
					default:
						if ($_REQUEST["metainit"])
							$this->metaInitResults();
						elseif ($_REQUEST["query"])
							$this->metaShowResults($_REQUEST["query"]);
						else
							$this->metaShowOptions();
						break;
				}
				break;
			case "__addressdata":
				if ($_REQUEST["address"])
					$this->generateAddressRecords($_REQUEST["address"], $_REQUEST["parent"]);
				else
					$this->generateAddressList();
				break;
			case "__sitemap":
				$this->generateSitemap();
				break;
			case "__shopcontents":
				$this->shopContents();
				break;
			case "__err401":
				$this->triggerError(401);
				$this->triggerLogin($this->need_authorisation);
				break;
			case "__err403":
				$this->triggerError(403);
				if ($this->custom_status)
					$this->getPageData($this->custom_status);
				else
					echo gettext("Access to this page is forbidden. The page is not active or access is restricted.");
				break;
			case "__err602":
				$this->triggerError(602);
				if ($this->custom_status) {
					$this->getPageData($this->custom_status);
				} else {
					$output_alt = new Layout_output();
					$output_alt->addTag("br");
					$output_alt->addCode(gettext("The requested page is redirecting in a loop."));
					$output_alt->addCode(gettext("The current request has been cancelled."));
					$output_alt->addTag("br");
					$output_alt->addTag("br");
					$output_alt->insertAction("previous", gettext("back"), "javascript: pageHistory();");
					$output_alt->addSpace();
					$output_alt->insertTag("a", gettext("go back to the previous page"), array(
						"href" => "javascript: pageHistory();"
					));
					echo $output_alt->generate_output();
				}
				break;
			case "__err404":
				$this->triggerError(404);
				$custom = $this->custom_status;
				//fallback to search (next case)
			case "__search":
				require(self::include_dir."search.php");
				break;
			case "__filesearch":
				require(self::include_dir."filesearch.php");
				break;
			case "__blog":
				$this->plugins["mvblog"]->blog_content(0,0);
				break;
			case "__covidelogin":
				$q = "select cms_manage_hostname from cms_license";
				$res = sql_query($q);
				$manage = sql_result($res,0);

				if ($manage != $this->http_host) {
					$this->triggerError(403);
					$output_alt = new Layout_output();
					$output_alt->addTag("br");
					$output_alt->addCode(gettext("The requested action is not available for this domain."));
					$output_alt->addTag("br");
					$output_alt->addCode(gettext("Please go to the following location to login:"));

					if (!$manage) {
						$output_alt->addTag("br");
						$output_alt->addTag("br");
						$output_alt->insertTag("b", gettext("no manage domain set, please update your configuration or login with the login icon").": ");
						$output_alt->insertAction("covide", gettext("login"), "javascript: popup('/mode/covidelogin', 'cms_covide_login');");
					} else {
						$uri = sprintf("http://%s/covide", $manage);
						$output_alt->insertTag("a", $uri, array(
							"target" => "_blank",
							"href"   => $uri
						));
					}

					$output_alt->addTag("br");
					$output_alt->addTag("br");
					$output_alt->insertAction("previous", gettext("back"), "javascript: pageHistory();");
					$output_alt->addSpace();
					$output_alt->insertTag("a", gettext("go back to the previous page"), array(
						"href" => "javascript: pageHistory();"
					));
					echo $output_alt->generate_output();
				} else {
					$this->triggerError(301);
					header("Location: /?mod=desktop");
					exit();
				}
				break;
			case "__forum":
				$output_alt = new Layout_output();
				$output_alt->insertTag("iframe", "", array(
					"onload" => "forum_resize_frame();",
					"id"     => "iframe",
					"src"    => "plugins/punbb/upload/",
					"style"  => "width:100%; height: 600px;",
					"frameborder" => 0,
					"border" => 0
				));
				echo $output_alt->generate_output();
				break;
			case "__login":
				$this->triggerLogin();
				break;
			case "__loginprofile":
				$output_alt = new Layout_output();
				$output_alt->addCode(gettext("You are now logged in as"));
				$output_alt->addSpace();
				if ($_SESSION["user_id"]) {
					$user_data = new User_data();
					$output_alt->addCode(gettext("Logged in as user").": ");
					$output_alt->insertTag("b", $user_data->getUserNameById($_SESSION["user_id"]));
				} else {
					$output_alt->addCode(gettext("Logged in as visitor or customer").": ");
					$output_alt->insertTag("b", $this->cms->getUserNameById($_SESSION["visitor_id"]));
				}
				if ($this->cms_license["custom_loginprofile"]) {
					$this->getPageData($this->cms_license["custom_loginprofile"]);
				} else {
					$output_alt->addTag("br");
					$output_alt->addTag("br");
					$output_alt->addCode(gettext("You will be redirected to the site in a few seconds")." ...");
					$output_alt->start_javascript();
					$output_alt->addCode("setTimeout(\"location.href='/';\", 1000)");
					$output_alt->end_javascript();
					echo $output_alt->generate_output();
				}
				break;
		}
	} else {
		if (!$this->page_cache[$id]) {
			$this->page_cache[$id] = $this->cms->getPageById($id, "", $strip_hostnames);
		}

		$page =& $this->page_cache[$id];

		$data = "";

		if ($page["pageHeader"])
			$data.= sprintf("\n<h2>%s</h2>", $page["pageHeader"]);

		/* handle lists */
		if ($page["isList"]) {
			$list = $this->cms->getListData($id);
			if (!$this->disableLists) {
				$listdata = "";
				$this->handleList($listdata, $id);
			}
		}
		/* only allow lists to selected page id (list on top) */
		if ($list["listposition"] == "boven")
			$data.= $listdata;

		/* metadata */
		if ($page["useMetaData"] && !$this->disableMeta)
			$data.= $this->handleMetaData($data, $id);

		/* shop */
		if ($this->page_cache[$id]["isShop"])
			$this->handleShop($data, $id);

		/* convert xhtml back to html */
		if ($GLOBALS["covide"]->output_xhtml == 0)
			$page["pageData"] = str_replace("/>", ">", $page["pageData"]);

		/* add page data */
		$data .= "\n".$page["pageData"];

		/* look for relation set */
		$this->getAddressNames($id, $page["address_ids"]);

		/* only allow lists to selected page id (list on top) */
		if ($list["listposition"] == "onder")
			$data.= $listdata;
		elseif ($list["listposition"] == "content")
			$data = str_replace("%%list%%", $listdata, $data);


		/* apply filters */
		$this->handleRewrites($data, $prefix);

		if ($this->page_cache[$id]["isGallery"])
			$this->handleGallery($data, $id);

		if ($this->page_cache[$id]["isFeedback"])
			$this->handleFeedback($data, $id);

		if ($this->page_cache[$id]["isForm"]) {
			//if ($this->page_cache[$id]["form_mode"] == 2)
			//	$this->handleEnquete($data, $id);
			//else
				$this->handleForm($data, $id);
		}

		if ($GLOBALS["covide"]->output_xhtml == 0)
			$data = str_replace(" />", ">", $data);

		$data = $this->checkPageElements($data);

		$this->handleAbbr($data, $no_inline_edit);

		/* strip onmouser function */
		#$data = preg_replace("/onmouse((out)|(over))=\"setBgColor\([^\)]*?\);\"/six", "", $data);
		#$data = str_replace("ondblclick=\"return false;\"", "", $data);

		/* strip some commontable errors */
		$data = str_ireplace("<tr></tr>", "", $data);

		/* strip some text mode js */
		if ($this->textmode) {
			$data = preg_replace("/ onmouseover=\"return escape\(tt_abbr\[\d{1,}\]\);\"/s", "", $data);
			$data = preg_replace("/<em class=\"tt_tooltip\">/s", "<em>", $data);
		}

		$this->handleImages($data);
		echo $this->cleanHtml($data);

		if ($this->checkCalendar($id))
			$this->getCalendar($id);

		/* conversion scripts */
		if ($page["conversion_script"]) {
			echo "\n<!-- [start conversion script] -->\n";
			echo $page["conversion_script"];
			echo "\n<!-- [end conversion script] -->\n";
		}
	}
	if ($_SESSION["user_id"]) {
		$output_alt = new Layout_output();
		$user_data = new User_data();
		$user_perm = $user_data->getUserDetailsById($_SESSION["user_id"]);
		if ($id == "__blog") {
			/* set some blog session vars */
			$_SESSION["author_id"]       = $_SESSION["user_id"];

			$_SESSION["author_name"]     = $user_perm["username"];
			$_SESSION["author_fullname"] = $user_perm["username"];
			$_SESSION["author_email"]    = $user_perm["mail_email"];
			$_SESSION["author_website"]  = $this->protocol.$_SERVER["HTTP_HOST"];
			$_SESSION["blog_user"]       = 1;

			$output_alt->insertAction("view_all", gettext("admin mode"), sprintf("javascript: blogAdmin();", $this->pageid));
			$output_alt->addSpace(2);
		} else {
			if ($user_perm["xs_cms_level"] > 0) {
				if (in_array($user_perm["xs_cms_level"], array(2,3))) {
					$page_xs = 1;
				} else {
					$perm = $this->cms->getUserPermissions($this->pageid, $_SESSION["user_id"]);
					if ($perm["editRight"]) {
						$page_xs = 1;
					}
				}
			}
			if (!$no_inline_edit) {
				if ($page_xs && is_numeric($this->pageid)) {
					$this->allow_edit[$id] = 1;
				}
			}
		}
	}
	echo "</div>";
	echo "\n\n<!-- [end of page data] -->\n\n";
?>
