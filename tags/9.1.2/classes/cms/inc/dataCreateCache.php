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

	if (!class_exists("Cms_data")) {
		die("no class definition found");
	}

	$q = sprintf("select * from cms_gallery_photos where id = %d", $file_id);
	$res = sql_query($q);
	$row = sql_fetch_assoc($res);

	/* get gallery data and cache it (for batch processing) */
	if (!$this->gallery_cache) {
		#$q = sprintf("select * from cms_gallery where pageid = %d", $row["pageid"]);
		#$res2 = sql_query($q);
		#$this->gallery_cache = sql_fetch_assoc($res2);
		$this->gallery_cache = $this->getGallerySettings($row["pageid"]);
	}

	$settings =& $this->gallery_cache;

	// if pageid is -1 it's a banner
	if ($row["pageid"] == "-1") {
		$pageid = "banner";
	} else {
		$pageid = $row["pageid"];
	}

	$filename = sprintf("%s/%s/%s/%d_%s.jpg",
		$GLOBALS["covide"]->filesyspath, "gallery", $pageid, $file_id, "full");
	$cache_file_large = sprintf("%s/%s/%s/%d_%s.jpg",
		$GLOBALS["covide"]->filesyspath, "gallery", $pageid, $file_id, "medium");
	$cache_file_thumb = sprintf("%s/%s/%s/%d_%s.jpg",
		$GLOBALS["covide"]->filesyspath, "gallery", $pageid, $file_id, "small");


	//recheck filename
	if (!file_exists($filename)) { die("file not found: $filename"); }

	//extract image information
	$type   = getimagesize($filename);
	$mime   = &$type["mime"];
	$width  = &$type[0];
	$height = &$type[1];

	$source = imagecreatefromjpeg($filename);

	//determine scale factor
	if ($width >= $height) {
		//use w for size
		if ($width > $settings["thumbsize"]) {
			$scale_thumb = ($settings["thumbsize"]/$width);
		} else {
			$scale_thumb = "1";
		}
		if ($width > $settings["bigsize"]) {
			$scale_large = ($settings["bigsize"]/$width);
		} else {
			$scale_large = "1";
		}
	} else {
		//use h for size
		if ($height > $settings["thumbsize"]) {
			$scale_thumb = ($settings["thumbsize"]/$height);
		} else {
			$scale_thumb = "1";
		}
		if ($height > $settings["bigsize"]) {
			$scale_large = ($settings["bigsize"]/$height);
		} else {
			$scale_large = "1";
		}
	}

	//new w+h for thumbnail
	$w = (int)($scale_thumb*$width);
	$h = (int)($scale_thumb*$height);

	$thumbX = $w;
	$thumbY = $h;
	$imageX = imagesx($source);
	$imageY = imagesy($source);
	$dest  = imagecreatetruecolor($thumbX, $thumbY);
	imagecopyresampled ($dest, $source, 0, 0, 0, 0, $thumbX, $thumbY, $imageX, $imageY);
	imagejpeg($dest, $cache_file_thumb);
	imagedestroy($dest);
	#imagedestroy($source);

	//new w+h for midsize image (e.g. slideshow)
	$w = (int)($scale_large*$width);
	$h = (int)($scale_large*$height);

	$thumbX = $w;
	$thumbY = $h;
	$imageX = imagesx($source);
	$imageY = imagesy($source);
	$dest  = imagecreatetruecolor($thumbX, $thumbY);
	imagecopyresampled ($dest, $source, 0, 0, 0, 0, $thumbX, $thumbY, $imageX, $imageY);
	imagejpeg($dest, $cache_file_large);
	imagedestroy($dest);
	imagedestroy($source);
?>
