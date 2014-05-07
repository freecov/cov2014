<?php
	if ($_REQUEST["action"] == "close") {
		echo "<script>window.close();</script>";
		exit();
	}
	$skip_run_module = 1;
	require("index.php");

	if (!$_SESSION["user_id"])
		die("not logged in");

	session_write_close();

	$lang = preg_replace("/\_.*$/s", "", $_SESSION["locale"]);

	$name = $covide->license["name"];

	$md5 = md5($_SESSION["user_id"].$name.$_SERVER["HTTP_HOST"].$_REQUEST["map_id"]);
	$param = "?action=store&md5=".$md5."&user_id=".$_REQUEST["user"]."&map_id=".$_REQUEST["map_id"]."&mod=".$_REQUEST["mod"];
	$url = $covide->webroot."jupload.php".$param;

	if ($_REQUEST["action"] == "store") {

		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir  = "bestanden";
		$fs_data = new Filesys_data();

		$quota = $fs_data->checkFilesysQuota();
		if ($quota !== false && $quota["left"] < 0) {
			echo "QUOTA REACHED! exit...";
			exit();
		}

		foreach ($_FILES as $file) {
			/* if file position is filled with a tmp_name */
			if ($file["error"] == UPLOAD_ERR_OK && $file["tmp_name"]) {

				/* gather some file info */
				$name = $file["name"];
				$type = $fs_data->detectMimetype($file["tmp_name"]);
				$size = $file["size"];

				/* insert file into dbase */
				if ($_REQUEST["mod"] == "filesys") {
					/* ================= */
					/* filesystem upload */
					/* ================= */
					$q = "insert into filesys_files (folder_id, name, size, type, timestamp, user_id, description) values ";
					$q.= sprintf("(%d, '%s', '%s', '%s', %d, %d, '%s')", $_REQUEST["map_id"], $name, $size, $type, mktime(), $_REQUEST["user_id"], "uploaded by applet");
					sql_query($q);
					$new_id = sql_insert_id("filesys_files");

					/* move data to the destination */
					$ext = $fs_data->get_extension($name);

					$destination = sprintf("%s/%s/%s.".$ext, $fspath, $fsdir, $new_id);
					move_uploaded_file($file["tmp_name"], $destination);
				} elseif ($_REQUEST["mod"] == "cms") {
					/* ========================= */
					/* gallery upload and resize */
					/* ========================= */

					$ext = $fs_data->get_extension($name);
					$cms_data = new Cms_data();
					$tmp_name =& $file["tmp_name"];

					if (in_array(strtolower($ext), array("jpg", "png", "gif", "jpeg"))) {

						//convert orig input to jpeg, max quality
						$cms_data->convertThumb($tmp_name);

						/* get order + 1 */
						$esc = sql_syntax("escape_char");
						$q = sprintf("select max(%1\$sorder%1\$s) from cms_gallery_photos where pageid = %2\$d", $esc, $_REQUEST["map_id"]);
						$res = sql_query($q);
						$order = sql_result($res,0)+1;

						/* insert file into dbase */
						$q = sprintf("insert into cms_gallery_photos (pageid, file, description, %1\$sorder%1\$s) values ", $esc);
						$q.= sprintf("(%d, '%s', '%s', %d)",
							$_REQUEST["map_id"], $name, "", $order);
						sql_query($q);
						$dbid = sql_insert_id("cms_gallery_photos");

						/* move thumb to destination */
						$dest = sprintf("%s/%s/%s/%d_full.jpg",
							$GLOBALS["covide"]->filesyspath, "gallery", $_REQUEST["map_id"], $dbid);

						if (!file_exists(dirname($dest)))
							mkdir(dirname($dest), 0777);

						move_uploaded_file($tmp_name, $dest);

						//create cached thumbnails
						$cms_data->createCache($dbid);
					}

				}
				//echo "uploaded ".$name."\n";
			}
		}
		echo "SUCCESS";
		exit();

	} else {
		/* show applet */
	 	header('Pragma: no-cache');
  	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

		echo ("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Covide - Multiple file upload</title>
<style type="text/css">
	body, select {
		font-family: arial, serif; font-size: 10pt; font-weight: bold;
	}
	body {
		background-color: #eeeeee;
	}
	select {
		background-color: #eef3f9;
	}
</style>
</head>
<body>
<?php
  	if ($_REQUEST["mod"] == "filesys") {
		$policy = "FileByFileUploadPolicy";
		$uri    = "opener.location.href='index.php?mod=filesys&action=opendir&id=".$_REQUEST["map_id"]."';";
	} else {
		$policy = "PictureUploadPolicy";
		$uri    = "opener.location.href='index.php?mod=cms&action=cmsgallery&id=".$_REQUEST["map_id"]."';";
	}
?>
<script type="text/javascript">
	window.resizeTo(900, 630);
	window.onbeforeunload = function() {
		<?php echo $uri ?>
	}
</script>
<?php if ($_REQUEST["mod"] != "filesys") { ?>
	<form name="frm" value="jupload.php" method="get">
		<input type="hidden" name="map_id" value="<?php echo $_REQUEST["map_id"] ?>" />
		<input type="hidden" name="mod" value="<?php echo $_REQUEST["mod"] ?>" />
		<?php echo gettext("resize before upload if image is larger than") ?>:&nbsp;
		<?php
			switch ((int)$_REQUEST["width_height"]) {
				case -1:
					$w = 0;
					$h = 0;
					$s = -1;
					break;
				case 0:
				case 1280:
					$w = 1280;
					$h = 1024;
					$s = 1280;
					break;
				case 1600:
					$w = 1600;
					$h = 1200;
					$s = 1600;
					break;
				case 1024:
					$w = 1024;
					$h = 768;
					$s = 1024;
					break;
				case 800:
					$w = 800;
					$h = 600;
					$s = 800;
					break;
				case 640:
					$w = 640;
					$h = 480;
					$s = 640;
					break;
			}

			$ary = array(
				-1   => gettext("do not resize"),
				1600 => "1600x1200",
				1280 => "1280x1024",
				1024 => "1024x768",
				800  => "800x600",
				640  => "640x480"
			);
		?>
		<select name="width_height">
			<?php foreach ($ary as $k=>$v) { ?>
				<option value="<?php echo $k ?>" <?php echo ($s==$k) ? "selected":""; ?>><?php echo $v ?></option>
			<?php } ?>
		</select>
	</form>
<?php } ?>
<applet code="wjhk.jupload2.JUploadApplet" archive="jupload/wjhk.jupload.jar?m=<?php echo filemtime("jupload/wjhk.jupload.jar") ?>" width="800" height="500" mayscript>
	<param name="postURL" value="<?php echo $url ?>" />
	<param name="uploadPolicy" value="<?php echo $policy ?>" />
	<param name="nbFilesPerRequest" value="1" />
	<param name="showStatusBar" value="false" />
	<param name="allowHttpPersistent" value="true" />
	<param name="highQualityPreview" value="false" />
	<param name="type" value="application/x-java-applet;version=1.6" />
	<param name="lang" value="<?php echo $lang ?>">
	<?php if ($w && $h) { ?>
		<param name="maxPicWidth" value="<?php echo $w ?>" />
		<param name="maxPicHeight" value="<?php echo $h ?>" />
	<?php } ?>
	<param name="serverProtocol" value="HTTP/1.1" />
	<param name="debugLevel" value="0" />
	<param name="afterUploadURL" value="jupload.php?action=close" />
</applet>
</body>
</html>
<?php } ?>
