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

	preg_match_all("/<img[^>]*?>/sxi", $str, $imglist);

	foreach ($imglist[0] as $l) {
		//$str = str_replace($l, "IMG", $str);

		$r = trim(preg_replace("/(^<img)|(>$) /sxi", "", $l));
		$img = explode("\"", $r);

		$keys = array();

		foreach ($img as $k=>$v) {
			$v = trim($v);
			if (preg_match("/\=$/s", $v)) {
				$keys[strtolower(str_replace("=", "", $v))] = trim($img[$k+1]);
			}
		}
		/* if no alt tag is set */
		if (!$keys["alt"])
			$keys["alt"] = "";

		/* copy alt value to title field */
		$keys["title"] = $keys["alt"];

		/* check if the uri is local */
		if (preg_match("/^\/{0,1}cmsfile\/\d{1,}$/si", $keys["src"])) {
			/* extract style key */
			if ($keys["style"]) {
				$style = explode(";", $keys["style"]);
				foreach ($style as $style_attr) {
					$style_attr = explode(":", $style_attr);
					/* parse style width/height */
					if (strtolower(trim($style_attr[0])) == "width" && !$keys["width"])
						$keys["width"] = trim($style_attr[1]);

					if (strtolower(trim($style_attr[0])) == "height" && !$keys["height"])
						$keys["height"] = trim($style_attr[1]);
				}
				/* parse width/height to int */
				$keys["width"]  = (int)preg_replace("/[^0-9]/s", "", $keys["width"]);
				$keys["height"] = (int)preg_replace("/[^0-9]/s", "", $keys["height"]);
			}

			/* check if width and height are set */
			if ($keys["width"] > 0 && $keys["height"] > 0) {
				/* check height width */
				/* resize the image to the H+W values */
				$img_id = (int)preg_replace("/[^0-9]/s", "", $keys["src"]);
				$cmscache = $this->cms->createInlineThumb($img_id, $keys["width"], $keys["height"], $this->pageid);
				if ($cmscache) {
					$keys["src"] = $cmscache;
				}
			}
		}
		$keys["src"] = preg_replace("/^\//s", "", $keys["src"]);
		if (preg_match("/^((cmscache)|(cmsfile)|(savefile))\/\d{1,}$/s", $keys["src"])) {
			if (!$this->filesys)
				$this->filesys = new Filesys_data();

			$src = explode("/", $keys["src"]);
			switch ($src[0]) {
				case "cmsgallery":
					break;
				case "cmscache":
					if ($img_id) {
						$src[2] = $img_id;
					} else {
						$q = sprintf("select img_id from cms_image_cache where id = %d", $src[1]);
						$res = sql_query($q);
						$src[2] = sql_result($res,0,"",2);
					}
					/* no break */
				case "cmsfile":
				case "savefile":
					$src[2] = $img_id;

					$file = $this->filesys->getFileById($src[2]);
					if (!$this->filesys_folder_cache[$file["folder_id"]]) {
						$tmp = explode(" -> ", $this->filesys->getFolderPath($file["folder_id"]));
						unset($tmp[0]);
						$this->filesys_folder_cache[$file["folder_id"]] = $tmp;
					}
					$keys["src"] = sprintf("/%s/%d/%s/%s", $src[0], $src[1],
						implode("/", $this->filesys_folder_cache[$file["folder_id"]]),
						$file["name"]);
			}
		}
		/* re-create img tag */
		$r = "\n<img";
		foreach ($keys as $k=>$v) {
			$r.= sprintf(" %s=\"%s\"", $k, $v);
		}
		$r.= ">";
		$str = str_replace($l, $r, $str);
	}
?>