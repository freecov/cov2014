<?php
	die("just a test");
	$folder   = $this->getThemeFolder();
	$cache    = preg_replace("/\.png/si", ".gif", $img);

	$file_in  = sprintf("%sicons/%s", $folder, $img);
	$file_out = sprintf("%sgif_cache/%s", $folder, $cache);

	$im = @imagecreatefrompng($file_in);
	imageAlphaBlending($im, false);
	imageSaveAlpha($im, true);

	imagecolortransparent($im);

	@imagegif($im, $file_out);
?>
