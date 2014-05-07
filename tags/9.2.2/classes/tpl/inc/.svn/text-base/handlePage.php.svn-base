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
	$page =& $this->pageid;

	$this->init_aliaslist();

	if ($_REQUEST["mode"]) {
		switch ($_REQUEST["mode"]) {
			case "sitemap":
			case "sitemap_plain": 
				$page = "__sitemap"; break;

			case "sitemap"     : $page = "__sitemap";  break;
			case "cmslogin"    : $page = "__cmslogin";  break;
			case "sponsor"     : $page = "__banner"; break;
			case "sponsors"    : $page = "__banners"; break;
			case "search"      : $page = "__search";   break;
			case "filesearch"  : $page = "__filesearch";   break;
			case "google"      : $page = "__google";   break;
			case "googlegz"    : $page = "__googlegz"; break;
			case "forum"       : $page = "__forum";    break;
			case "rss"         : $page = "__rss";      break;
			case "metadata"    : $page = "__metadata"; break;
			case "addressdata" : $page = "__addressdata"; break;
			case "abbreviations" : $page = "__abbreviations"; break;
			case "covideloginalt": $page = "__covidelogin"; break;
			case "shopadd"       : $page = "__shopadd"; break;
			case "shopcontents"  : $page = "__shopcontents"; break;
			case "linkchecker"   : $page = "__linkchecker";
				$this->linkcheckerAuth();
				$this->linkcheckerPageList();
				exit();
				break;
			case "blog"     :
				$page = "__blog";
				/* filesys dir */
				$base_dir = "plugins/mvblog/";
				/* web base dir */
				$this->base_dir = "/blog/";
				/* load the class */
				require_once($base_dir."common/mvblog.php");
				$this->plugins["mvblog"] = new MvBlog($base_dir);
				break;
			case "blogadmin":
				$page = "__blog";
				$this->syncBlogAuthors();
				$this->redirBlogAdmin();
				break;
			case "login":
				$page = "__login";
				break;
			case "loginprofile":
				$page = "__loginprofile";
				break;
		}
	}

	if (!preg_match("/^__/s", $page)) {
		if ($_REQUEST["id"]) {
			$page = $_REQUEST["id"];
		} elseif ($_REQUEST["page"]) {
			$page = $_REQUEST["page"];
		} else {
			$page = "__def";
		}
	}
	if ($page == "__def")
		$this->pageid = $this->default_page;

	if ($this->checkInternalRequest()==0) {
		$requested_page = $page;
		$page = preg_replace("/\.((htm)|(html))/si","",$page);

		if (!is_numeric($page)) {
			/* check current alias existance */
			$q = sprintf("select id, apEnabled from cms_data where pageAlias like '%s'", $page);
			$res = sql_query($q);
			if (sql_num_rows($res)==1) {
				$page = sql_result($res,0,"id",2);
			} else {
				$apEnabled = sql_result($res,0,"apEnabled",2);

				/* check if the alias did exists in the past */
				$q = sprintf("select pageid from cms_alias_history where alias like '%s'", $page);
				$res = sql_query($q);

				if (sql_num_rows($res) > 0) {
					/* check the current alias of the found page */
					$historypage = $this->checkAlias(sql_result($res,0,"",2));
					$this->triggerError(301);

					$uri = $this->page2rewrite($historypage);
					header(sprintf("Location: %s", $uri));
					exit();
				} else {
					$page = "__err404"; // go to 404 later
				}
			}
		} else {
			/* check if a paging request was done */
			if ($_REQUEST["gallery"] || $_REQUEST["calendar"] || $_REQUEST["list"] || $_REQUEST["feedback"] || $_REQUEST["start"])
				$is_paging = 1;

			/* check page existance */
			$q = "select pageAlias, apEnabled from cms_data where id = ".(int)$page;
			$res = sql_query($q);
			if (sql_num_rows($res) != 1) {
				$page = "__err404";
			} elseif (!$_REQUEST["print"]) {
				$alias = sql_result($res,0,"pageAlias",2);
				$apEnabled = sql_result($res,0,"apEnabled",2);

				if ($alias && $page != $this->default_page && !$is_paging) {
					/* we should not access the page directly, go to the alias */
					$this->triggerError(301);

					$q = sprintf("select id from cms_data where isSpecial = 'D' and parentPage = 0");
					$res = sql_query($q);
					$hp = sql_result($res,0,"",2);

					if ($apEnabled == $hp) {
						$page = "__err404";
					} else {
						$this->triggerError(301);
						$uri = $this->page2rewrite($alias);
						header(sprintf("Location: %s", $uri));
						exit();
					}
				}
			}
		}
		if (is_numeric($page)) {
			/* check if (numeric) page is active */
			$q = sprintf("select isActive from cms_data where id = %d", $page);
			$res = sql_query($q);
			if (sql_result($res,0) != 1) {
				$hp = $this->cms->getHighestParent($page);

				$q = sprintf("select id from cms_data where isSpecial = 'D' and parentPage = 0");
				$res = sql_query($q);
				$deleted = sql_result($res,0,"",2);
				if ($hp == $deleted) {
					// the page is deleted and should be removed asap from index
					$page = "__err410";
				} else {
					// the page is not active and should be 404 temporary state
					$page = "__err404";
				}
			}
		}
		/* check for 'page' less feature */
		if ($page != "__err404"
			&& $page != $this->default_page
			&& !$is_paging
			&& !$_REQUEST["print"]
			&& $this->page_less_rewrites 
			&& !$_REQUEST["mode"]
			&& !$_REQUEST["pageless"]) {

			$this->triggerError(301);
			$uri = $this->page2rewrite(($requested_page) ? $requested_page:$page);
			header(sprintf("Location: %s", $uri));
			exit();
		}

		/* check if page was called without .htm */
		if ($page != "__err404"
			&& $page != $this->default_page
			&& !preg_match("/\.htm$/si", $requested_page)
			&& !$is_paging
			&& !$_REQUEST["print"]) {

			$this->triggerError(301);
			$uri = $this->page2rewrite($page);
			header(sprintf("Location: %s", $uri));
			exit();
		}
	}
	/* save current path */
	$this->path = $this->getPath($page);

	/* get visitor page restrictions, flag isProtected */
	$this->getVisitorRestrictions($page, $this->path);

	/* get visitor page restrictions, flag isProtected */
	$this->getSSLmode($page, $this->path);

	/* rss feeds */
	$this->rss[gettext("rss feed of the complete site")] = $this->protocol.$_SERVER["HTTP_HOST"]."/rss";
	$this->rss[gettext("rss feed of subpages of this page")] = $this->protocol.$_SERVER["HTTP_HOST"]."/rss/feed/".$this->pageid;

	if ($this->pageid == "__addressdata" && $_REQUEST["address"]) {
		$qry = $_REQUEST["address"]."|".$_REQUEST["parent"];

		$this->rss[gettext("rss feed of this address list")] = $this->protocol.$_SERVER["HTTP_HOST"]."/rss/address/".$qry;
	} elseif ($this->pageid == "__metadata" && $_REQUEST["query"])
		$this->rss[gettext("rss feed of this category")] = $this->protocol.$_SERVER["HTTP_HOST"]."/rss/meta/".$_REQUEST["query"];

	if ($this->pageid != $this->default_page && is_numeric($this->pageid))
		$this->rss[gettext("rss feed of this page")] = $this->protocol.$_SERVER["HTTP_HOST"]."/rss/live/".$this->pageid;


	/* add and check page log */
	$this->addPageLog();
	$this->checkPageLog();

	if (is_numeric($page)) {
		/* check for domain permissions */
		$q = sprintf("select apEnabled from cms_data where id = %d", $page);
		$res = sql_query($q);
		$ap = sql_result($res,0,"",2);

		if (!in_array($ap, $this->public_roots) && $this->siteroot) {
			//throw error

			$this->triggerError(301);

			//get authorized host name
			$host = $this->cms->getHostnameByPage($this->pageid);

			$p = $this->page2rewrite($this->checkAlias($this->pageid));
			$uri = sprintf("%s%s%s", $this->protocol, $host, $p);

			#$uri = sprintf("%s%s/page/%s", $this->protocol, $host, $this->checkAlias($this->pageid));
			header("Location: ".$uri);
			exit();
		}

		/* save page to last visited page */
		if ($page > 0)
			$_SESSION["cms_lastpage"] = $page;


		/* check if a domain redirect to a preferred one needs to be done */
		$this->checkDomainRedir();

		/* check for other redirects */

		$this->getRedirEndPoint($page);
	}

	/* internal status codes should never end with .htm */
	if (preg_match("/^__/s", $page))
		$page = preg_replace("/\.htm$/s", "", $page);
?>
