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

	$filesys_data = new Filesys_data();
	$file = $filesys_data->getFileById($id);

	$img = imagecreatefromstring($file["binary"]);
	if ($img === FALSE) {
		/* filetype is not supported */
		$ret = 0;
	} else {

		$q = sprintf("insert into cms_image_cache (img_id, datetime, width, height) values (
			%d, %d, %d, %d)", $id, mktime(), $width, $height);
		sql_query($q);
		$cacheid = sql_insert_id("cms_image_cache");

		$filename = sprintf("%s/%s/%d.jpg",
			$GLOBALS["covide"]->filesyspath, "cmscache", $cacheid);

		$src_width  = imagesx($img);
		$src_height = imagesy($img);
		$target = imagecreatetruecolor($width, $height);
		imagecopyresampled ($target, $img, 0, 0, 0, 0, $width, $height, $src_width, $src_height);
		imagejpeg($target, $filename, 90);
		imagedestroy($img);

		$ret = sprintf("/cmscache/%d", $cacheid);
	}
?>