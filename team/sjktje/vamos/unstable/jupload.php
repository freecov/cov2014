<?php
	$skip_run_module = 1;
	require("index.php");

	$name = $covide->license["name"];

	$md5 = md5($_SESSION["user_id"].$name.$_SERVER["HTTP_HOST"].$_REQUEST["map_id"]);
	$param = "?action=store&md5=".$md5."&user_id=".$_REQUEST["user"]."&map_id=".$_REQUEST["map_id"]."&mod=".$_REQUEST["mod"];
	$url = $covide->webroot."jupload.php".$param;

	if ($_REQUEST["action"] == "store") {

		$fspath = $GLOBALS["covide"]->filesyspath;
		$fsdir  = "bestanden";
		$fs_data = new Filesys_data();

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

					if (in_array($ext, array("jpg", "png", "gif", "jpeg"))) {

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
						$dest = sprintf("%s/%s/%d_full.jpg",
							$GLOBALS["covide"]->filesyspath, "gallery", $dbid);
						move_uploaded_file($tmp_name, $dest);

						//create cached thumbnails
						$cms_data->createCache($dbid);
					}

				}
				echo "uploaded ".$name."\n";
			}
		}
		echo "done.";
	} else {
		/* show applet */
		$output = new Layout_output();
		$output->layout_page("upload", 1);
		$output->start_javascript();
			if ($_REQUEST["mod"] == "filesys") {
				$output->addCode("
					resizeTo(680, 350);

					window.onunload = function() {
						opener.location.href='index.php?mod=filesys&action=opendir&id=".$_REQUEST["map_id"]."';
					}

					var url = '".$url."';
				");
			} else {
				$output->addCode("
					resizeTo(680, 350);

					window.onunload = function() {
						opener.location.href='index.php?mod=cms&action=cmsgallery&id=".$_REQUEST["map_id"]."';
					}

					var url = '".$url."';
				");
			}
		$output->end_javascript();
		$output->load_javascript("jupload.js");

		$output->addCode("
			<APPLET CODE=\"wjhk.jupload.JUploadApplet\" ARCHIVE=\"jupload/wjhk.jupload.jar\" WIDTH=\"640\" HEIGHT=\"300\"></XMP>
				<PARAM NAME=\"CODE\" VALUE=\"wjhk.jupload.JUploadApplet\">
			    <PARAM NAME=\"ARCHIVE\" VALUE=\"jupload/wjhk.jupload.jar\">
			    <PARAM NAME=\"type\" VALUE=\"application/x-java-applet;version=1.4\">
			    <PARAM NAME=\"scriptable\" VALUE=\"false\">
			    <PARAM NAME=\"postURL\" VALUE=\"".$url."\">
				Java 1.4 or higher plugin required.
			</APPLET>
			</NOEMBED>
			</EMBED>
			</OBJECT>
		");
		$output->exit_buffer();
	}
?>
