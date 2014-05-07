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
	$output  = new Layout_output();
	$output2 = new Layout_output();

	if (!$pageid)
		$pageid = $this->pageid;

	echo "\n<div id='print_container'></div>\n";

	$output2->addSpace();
	$output2->addTag("span", array(
		"id"    => "cms_navigation",
		"class" => "cms_navigation"
	));

	/* check for minimal footer setting, this setting will only
	   show a login icon when not logged in to users */
	require("conf/offices.php");
	$output2->addSpace();

	if (isset($cms) && array_key_exists("minimal_footer", $cms)) {
		$minimal_footer = $cms["minimal_footer"];
	} else {
		$minimal_footer = 0;
	}

	if (!$minimal_footer || $_SESSION['user_id']) {

		$output2->insertAction("previous", gettext("back"), "javascript: pageHistory();");
		$output2->addSpace();
		$output2->addCode("\n");

		$p = ($_REQUEST["mode"] == "sitemap") ? "/sitemap_plain.htm": sprintf("/text/%s", $_REQUEST["page"]);

		$output2->insertAction("access", gettext("text version"), $p);
		$output2->addSpace();
		$output2->addCode("\n");
		if ($this->pageid) {
			if ($this->printcss) {
				$output2->insertAction("print", gettext("print"), "javascript: print();");
			} else {
				$output2->insertAction("print", gettext("print"), "javascript: pagePrint('".$this->pageid."');");
			}
			$output2->addSpace();
			$output2->addCode("\n");
		}

		if ($this->cms_license["cms_shop"]) {
			$output2->insertAction("shop", gettext("shopping cart"), "/mode/shopcontents");
			$output2->addSpace();
			$output2->addCode("\n");
		}
		if ($this->allow_edit[$pageid] || $page_xs == 1) {
			$output2->insertAction("edit", gettext("edit this page"), sprintf("javascript: cmsEdit(%d);", $pageid));
			$output2->addSpace();
			$output2->addCode("\n");
		}
	}
	if ($this->display_login || $_SESSION["user_id"]) {
		if ($_SESSION["user_id"]) {
			$uri = sprintf("oldpopup('http://%s/?mod=desktop&amp;cmslogin=1', 'covide_%d'); return false", $this->manage_hostname, rand(1000000, 99999999));
			$link = sprintf("http://%s/login", $this->manage_hostname);
			$output2->addTag("a", array(
				"href" => $link,
				"onclick" => $uri, 
				"style" => "text-decoration: none;"
			));
			if ($_SESSION["user_id"] && $this->manage_hostname == $this->http_host) {
				$output2->addTag("img", array(
					"src"    => "themes/default/icons/jabber_online.png?m=".filemtime("themes/default/icons/jabber_online.png"),
					"alt"    => gettext("login"),
					"id"     => "remote_login_image",
					"width"  => 16,
					"height" => 16
					#"border" => 0
				));

				$output2->endTag("a");
			} else {
				$output2->addTag("img", array(
					"src"    => "themes/default/icons/jabber_offline.png?m=".filemtime("themes/default/icons/jabber_offline.png"),
					"alt"    => gettext("login"),
					"id"     => "remote_login_image",
					"width"  => 16,
					"height" => 16
					#"border" => 0
				));

				$output2->endTag("a");
				$output2->start_javascript();
					$output2->addCode(sprintf("addLoadEvent(checkRemoteLogin('%s'))", $this->manage_hostname));
				$output2->end_javascript();
			}
		} elseif (!$_SESSION['visitor_id']) {
			$uri = sprintf("javascript: cmsLoginPage('%s')", $_REQUEST["page"]);
			$output2->insertAction("covide", gettext("login to covide"), $uri);
		}
		$output2->addSpace();
		$output2->addCode("\n");
	}

	/* create the uri */
	$uri = sprintf("%s%s%s%s",
		$this->protocol, $_SERVER["HTTP_HOST"], ($this->page_less_rewrites) ? "/":"/page/", $this->checkAlias($this->pageid));

	if ($_SESSION["user_id"] || $_SESSION["visitor_id"]) {
		$output2->addSpace();
		if ($_SESSION["user_id"]) {
			$user_data = new User_data();
			$output2->addCode(gettext("manager").": ");
			$output2->insertTag("b", $user_data->getUserNameById($_SESSION["user_id"]));
		} else {
			$output2->addCode(gettext("user").": ");
			$output2->insertTag("b", $this->cms->getUserNameById($_SESSION["visitor_id"]));
		}
		$output2->addSpace(3);

		$output2->insertAction("logout", gettext("logout"), sprintf(
			"javascript: cmsLogout('%s', '%s', '%s');",
			urlencode($uri),
			addslashes(gettext("Do you also want to logout from the Covide office / CMS backend?")),
			($this->manage_hostname == $this->http_host) ? "":$this->manage_hostname
		));
		$output2->addSpace();
		$output2->addCode("\n");
	}
	$pt = $this->getPageTitle($pageid, -1, 1);
	$pt = $this->limit_string($pt, 70, 0);
	$pt = preg_replace("/\.{3}$/s", "", $pt);

	#if ($this->pageid != $this->default_page) {
		$output->addTag("div", array(
			"id"    => "cms_textfooter",
			"class" => "cms_textfooter"
		));
		$output->insertTag("a", "&lt; ".gettext("back"), array(
			"href" => "javascript: pageHistory();"
		));
		$output->addCode(" ");
		if ($this->pageid) {
			$output->addCode("| ");
			if ($this->printcss) {
				$output->insertTag("a", gettext("print"), array(
					"href" => "javascript: print();"
				));
			} else {
				$output->insertTag("a", gettext("print"), array(
					"href" => "javascript: pagePrint('".$this->pageid."');"
				));
			}
			$output->addCode(" | ");
			$output->insertTag("a", gettext("text"), array(
				"href" => "/text/".$_REQUEST["page"]
			));
			$output->addCode(" | ");
		}
		$output->insertTag("b",
			$this->limit_string($this->getPageTitle($this->pageid, -1, 1), 100, 0
		));
		$output->endTag("div");
		$output2->addCode("\n");
	#}
	$output2->endTag("span");

	echo $output->generate_output();

	if ($this->alternative_footer) {
		$this->alternative_text.= "<div id='alternative_footer'>\n";
		$this->alternative_text.= $output2->generate_output();
		$this->alternative_text.= "</div>";
	} else {
		echo $output2->generate_output();
	}
?>
