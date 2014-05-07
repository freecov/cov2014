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
	$file = $filesys_data->getFileById($id, 1);
	$file["ext"] = $filesys_data->get_extension($file["name"]);

	$f = sprintf("%s/%s/%d.%s", $GLOBALS["covide"]->filesyspath, "bestanden",
		$id, $file["ext"]);

	$f_new = $filesys_data->FS_calculatePath($f);

	if (file_exists($f.".gz"))
		$f.= ".gz";
	elseif (file_exists($f_new))
		$f = $f_new;
	elseif (file_exists($f_new.".gz"))
		$f = $f_new.".gz";

	$sizes = getimagesize($f);

	if ($sizes[0] != $width || $sizes[1] != $height) {
		$file["binary"] = $filesys_data->getBindataById($id, $file["ext"]);
		$img = @imagecreatefromstring($file["binary"]);
	} else {
		$img = FALSE;
	}

	if ($img === FALSE) {
		$q = sprintf("insert into cms_image_cache (img_id, datetime, width, height, use_original) values (
			%d, %d, %d, %d, 1)", $id, mktime(), $width, $height);
		sql_query($q);

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
