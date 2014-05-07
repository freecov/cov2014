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

	/* ie6 has no integrated rss support */
	header("Content-Type: application/xhtml+xml");

	if (preg_match("/MSIE (6|5)/si", $_SERVER["HTTP_USER_AGENT"])) {
		if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) {
			header("Content-Disposition: filename=\"rssfeed.xml\"");
		} else {
			header("Content-Disposition: attachment; filename=\"rssfeed.xml\"");
		}
	}
	$this->sendHeadersCachePublic();

	$apckey = sprintf("rss_%s", md5(serialize($_REQUEST).$this->siteroot));
	$fetch = $this->getApcCache($apckey);
	if ($fetch) {
		echo $fetch;
	} else {

		/* get basic info */
		$xml_main = file_get_contents(self::include_dir."rss_main.xml");
		$xml_item = file_get_contents(self::include_dir."rss_item.xml");

		/* some global vars */
		//$settings = $this->cms->getCmsSettings();
		$settings = $this->cms_license;

		$descr =& $settings["search_descr"];

		if ($_REQUEST["address"]) {
			$type = "address";
			$address_data = new Address_data();
			$a = explode("|", $_REQUEST["sel"]);
			$text = sprintf("%s: %s",
				gettext("Last updates of address"),
				$address_data->getAddressNameById($a[0]));

			if ($a[1]) {
				$text.= sprintf(", %s: %s",
					gettext("with category"),
					$this->getPageTitle($a[1], -1, 1));
			}
		} elseif ($_REQUEST["meta"]) {
			$type = "meta";

			$data = $this->queryDecodeMetadata($_REQUEST["sel"]);
			$text = array();
			foreach ($data as $k=>$v) {
				if (is_array($v))
					$v = implode(" ".gettext("and")." ", $v);

				$field = $this->cms->getMetadataDefinitionById($k);
				$text[] = sprintf("%s = %s", $field["field_name"], $v);
			}
			$text = gettext("Last updated pages meeting the following criteria").": ".implode(", ", $text);

		} elseif ($_REQUEST["live"]) {
			$type = "live";
			$text = sprintf("%s: %s%s%s",
				gettext("Live feed of page"),
				$this->protocol,
				$_SERVER["HTTP_HOST"],
				$this->page2rewrite($this->checkAlias($_REQUEST["live"])));
		} elseif ($_REQUEST["parent"]) {
			$type = "parent";
			$text = sprintf("%s: %s%s%s",
				gettext("Last updated child pages of page"),
				$this->protocol,
				$_SERVER["HTTP_HOST"],
				$this->page2rewrite($this->checkAlias($_REQUEST["parent"])));
		} else {
			$type = "default";
			$text = sprintf("%s: %s%s",
				gettext("Last updated pages of"),
				$this->protocol,
				$_SERVER["HTTP_HOST"]);
		}

		if (!preg_match("/^http(s){0,1}:\/\//si", $this->logo))
			$rsslogo = $this->protocol.$_SERVER["HTTP_HOST"]."/".$this->logo;
		else
			$rsslogo = $this->logo;

		$vars = array(
			"title"       => $settings["cms_name"],
			"link"        => $GLOBALS["covide"]->webroot,
			"description" => $text,
			"language"    => $settings["search_language"],
			"copyright"   => $settings["search_copyright"],
			#"date"        => time(),
			"webmaster"   => $settings["search_email"],
			"author"      => $settings["search_author"],
			"favicon"     => $rsslogo
		);

		$vars["title"]       = htmlentities($vars["title"]);
		$vars["description"] = htmlentities($vars["description"]);
		$vars["copyright"]   = htmlentities($vars["description"]);

		foreach ($vars as $k=>$v) {
			$xml_main = str_replace(sprintf("{%s}", $k), $v, $xml_main);
		}

		switch ($type) {
			case "address":
				$vars = explode("|", $_REQUEST["sel"]);
				$addressid = (int)$vars[0];
				$parent    = (int)$vars[1];

				$regex_syntax = sql_syntax("regex");
				$repl = " replace(address_ids, ',', '|') ";
				$reg = " ($repl $regex_syntax '(^|\\\\|)". $addressid ."(\\\\||$)') ";

				$start = (int)$_REQUEST["start"];

				$data = array();

				if ($parent)
					$subq = sprintf("address_level = 1 AND parentPage = %d", $parent);
				else
					$subq = "address_level is NULL or address_level = 0";

				$q = sprintf("select * from cms_data where (%s) and apEnabled IN (%s) and %s and %s order by datePublication DESC",
					$subq, implode(",", $this->public_roots), $this->base_condition, $reg);
				$res = sql_query($q, "", $start, $this->pagesize);
				while ($row = sql_fetch_assoc($res)) {
					$row["datePublication_h"] = date("d-m-Y", $row["datePublication"]);
					$this->rssRecord($xml_main, $xml_item, $row);
				}
				break;
			case "meta":

				//$data = unserialize(stripslashes($_REQUEST["sel"]));
				$data = $this->queryDecodeMetaData($_REQUEST["sel"]);

				require(self::include_dir."showMetaResultsBase.php");
				$pages = array_slice($pages, 0, 20, TRUE);

				if (is_array($pages)) {
					foreach ($pages as $id) {
						if ($id > 0) {
							$data = $this->cms->getPageById($id);
							$this->rssRecord($xml_main, $xml_item, $data);
						}
					}
				}
				break;
			case "live":
				$data = $this->cms->getPageById($_REQUEST["live"]);
				$row["author"] = $vars["author"];
				$this->rssRecord($xml_main, $xml_item, $data);
				break;
			case "parent":
				$pages = $this->getPagesByParent($_REQUEST["parent"], "datePublication desc", 20);
				foreach ($pages as $id=>$page) {
					$data = $this->cms->getPageById($id);
					$this->rssRecord($xml_main, $xml_item, $data);
				}
				break;
			default:
				/* get current feeds of the current public domain list */
				$q = sprintf("select * from cms_data where
					apEnabled IN (%s) and %s order by datePublication desc LIMIT 20",
					implode(",", $this->public_roots), $this->base_condition);
				$res = sql_query($q);
				while ($row = sql_fetch_assoc($res)) {
					$row["author"] = $vars["author"];
					$this->rssRecord($xml_main, $xml_item, $row);
				}
		}
		$xml_main = str_replace("{records}", "", $xml_main);
		$this->setApcCache($apckey, $xml_main);
		echo $xml_main;
	}
	exit();
?>
