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

	preg_match_all("/<a[^>]*?>/si", $str, $link);
	foreach ($link[0] as $orig) {
		$l = $orig;
		//replace server name
		$regex = "/ href=(\"|')http(s){0,1}:\/\/".$_SERVER["HTTP_HOST"]."/";
		$l = preg_replace($regex, "href=$1", $l);
		$l = preg_replace("/ href=(\"|')((index)|(site))\.php/sxi", " href=$1", $l);

		$r = trim(preg_replace("/(^<a)|(>$) /sxi", "", $l));
		$keys = array();
		$href = explode("\"", $r);

		foreach ($href as $k=>$v) {
			$v = trim($v);
			if (preg_match("/\=$/s", $v)) {
				$keys[strtolower(str_replace("=", "", $v))] = trim($href[$k+1]);
			}
		}

		$keys["href"] = preg_replace("/^\?((page)|(id))\=/s", "page/", $keys["href"]);

		if (preg_match("/^page\//six", $keys["href"])) {
			$pageid = preg_replace("/(^page\/)|(\.htm)$/s", "", $keys["href"]);
			if (is_numeric($pageid))
				$pageid = $this->checkAlias($pageid);
			else
				$pageid.= ".htm";

			#$keys["href"] = sprintf("/page/%s", $pageid);
			$keys["href"] = $this->page2rewrite($pageid);

		} elseif (preg_match("/^\#/s", $keys["href"]) && $this->pageid != $this->default_page) {
			/* anchor compatibility */
			$keys["href"] = sprintf("/%s%s", $_SERVER["REQUEST_URI"], $keys["href"]);
		}
		if (preg_match("/^(\/{0,1})cmsfile\//six", $keys["href"])) {
			/* image found */
			$keys["href"] = preg_replace("/cmsfile\/(\d{1,})/s", "savefile/$1", $keys["href"]);
			$file_id = (int)preg_replace("/^(\/{0,1})savefile\//s", "", $keys["href"]);

			if (!$this->filesys)
				$this->filesys = new Filesys_data();

			if ($file_id > 0) {
				$file = $this->filesys->getFileById($file_id);
				if (!$this->filesys_folder_cache[$file["folder_id"]]) {
					$tmp = explode(" -> ", $this->filesys->getFolderPath($file["folder_id"]));
					unset($tmp[0]);
					$this->filesys_folder_cache[$file["folder_id"]] = $tmp;
				}
				$keys["href"] = sprintf("/savefile/%d/%s/%s", $file_id,
					implode("/", $this->filesys_folder_cache[$file["folder_id"]]),
					$file["name"]);
			}
		}
		/* make url's relative */

		$keys["href"] = preg_replace("/^\//", "", $keys["href"]);
		/* check for textmode */
		if ($this->textmode) {
			$keys["href"] = preg_replace("/^page\//s", "text/", $keys["href"]);
		}

		/* re-create a tag */
		$r = "\n<a";
		foreach ($keys as $k=>$v) {
			$r.= sprintf(" %s=\"%s\"", $k, $v);
		}
		$r.= ">";
		$str = str_replace($orig, $r, $str);
	}
?>
