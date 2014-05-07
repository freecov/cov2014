<?php

		$ext = $this->get_extension($file["name"]);

		$input = sprintf("%s/%s/%s.%s", $GLOBALS["covide"]->filesyspath, $file["module"], $file["id"], $ext);
		$tmpfile = $GLOBALS["covide"]->temppath."preview_".md5(rand()*mktime());

		/* read input file and copy over to temp store */
		$bindata = $this->FS_readFile($input);

		/* do not allow php files here */
		if (preg_match("/\.php$/si", $input))
			$input.= ".txt";

		$filename = sprintf("%shtmlview_%s", $GLOBALS["covide"]->temppath, basename($input));
		file_put_contents($filename, $bindata);
		$input = $filename;

		switch ($file["subtype"]) {
			case "msword";
				$cmd = sprintf("wvHtml %s %s", $input, $tmpfile);
				$read_multi_file = 1;
				break;
			case "pdf":
				$cmd = sprintf("cd %s && pdftohtml %s %s", $GLOBALS["covide"]->temppath, $input, basename($tmpfile));
				$read_multi_file = 1;
				break;
			case "openoffice":
				$cmd = sprintf("unzip -p %s content.xml | o3tohtml | utf8tolatin1 > %s", $input, $tmpfile);
				break;
			case "msexcel":
				$cmd = sprintf("xlhtml %s > %s", $input, $tmpfile);
				break;
			case "rtf":
				$cmd = sprintf("unrtf %s > %s", $input, $tmpfile);
				break;
			case "text":
			case "html":
			case "csv":
				$cmd = sprintf("cp %s %s", $input, $tmpfile);
				break;
			case "vcard":
				$cmd = sprintf("cp %s %s", $input, $tmpfile);
				break;
		}

		if ($cmd) {
			/* exec the command */
			exec($cmd, $ret);

			$conversion = new Layout_conversion();

			/* a default style for all html pages */
			$style = "<style type='text/css'>body, td, a, div, p, span { font-family: arial,serif; font-size: 12px; color: black; } </style>";

			if ($read_multi_file) {
				$files = array();
				$cmd = sprintf("ls %s*", $tmpfile);
				exec($cmd, $files, $retval);

				foreach ($files as $datafile) {
					$ext = $this->get_extension(basename($datafile));

					if ($ext != "gif" && $ext != "htm" && !$ext && $ext != "png") {
						/* not viewable */
						@unlink($datafile);
					}
					if ($ext == "htm" || $datafile == $tmpfile) {
						/* open the html file */
						if (filesize($datafile) > 0) {
							$handle = fopen($datafile, "r");
							$data = fread($handle, filesize($datafile));
							fclose($handle);
						} else {
							$data = "<html><body>file could not be opened - unknown format.</body></html>";
						}

						/* insert css styles */
						$data = preg_replace("/(<body[^>]*?>)/si", $style."$1", $data);
						$data = $conversion->utf8_convert($data);

						/* replace file links */
						foreach ($files as $f) {
							$f = basename($f);
							$ext = $this->get_extension(basename($f));

							if ($ext == "gif" || $ext == "htm" || $ext == "png") {
								$data = str_replace($f, "?mod=filesys&action=getPreviewFile&dl=1&file=".$f, $data);
							} else {
								$data = str_replace($f, "", $data);
							}
						}

						$data = $conversion->utf8_convert($data);

						$out = fopen($datafile, "w");
						fwrite($out, $data);
						fclose($out);
					}
				}

				if ($file["subtype"]=="pdf") {
					$tmpfile.=".html";
				}
				/* use an frameset */
				$output = new Layout_output();
				$output->addCode("
					<html>
						<head><title>Covide File Preview</title></head>
					<frameset  ROWS='30,*' frameborder='0'>
							<frame frameborder='0' noresize scrolling='no' SRC='?mod=filesys&action=preview_header&file=".$file["name"]."' NAME='preview_top'>
							<frame frameborder='0' noresize scrolling='yes' SRC='?mod=filesys&action=getPreviewFile&file=".basename($tmpfile)."' NAME='preview_main'>
					</frameset>
					</html>
				");
				$output->exit_buffer();
				//$this->file_preview_readfile(basename($tmpfile));
			} else {
				/* open the html file */

				$handle = fopen($tmpfile, "r");
				$data = fread($handle, filesize($tmpfile));
				fclose($handle);

				/* if subtype = text | csv */
				if ($file["subtype"] == "csv") {
					/* replace all data var by pointers to new array */
					preg_match_all("/\"[^\"]*?\"/si", $data, $matches);
					$matches = $matches[0];
					$matches = array_unique($matches);
					foreach ($matches as $k=>$v) {
						$data = str_replace($v, "##$k", $data);
						$matches[$k] = substr($v, 1, strlen($v)-2);
					}
					$data = str_replace(";", ",", $data);
					$data = explode("\n", $data);

					$html = "<html><body><table border='1'>";
					foreach ($data as $line) {
						$line = explode(",", $line);
						$html.= "<TR>";
						foreach ($line as $field) {
							if (preg_match("/##\d{1,}/s", $field)) {
								$field = str_replace("##", "", $field);
								$field = $matches[ $field ];
							}
							$html.= sprintf("<TD>%s</TD>", $field);
						}
						$html.= "</TR>";
					}
					$html.= "</table></body></html>";
					$data = $html;
				}
				if ($file["subtype"] == "text" || $file["subtype"] == "csv") {
					$data = "<html><body>".str_replace("\n", "<br>\n", $data)."</body></html>";
				}

				/* insert css styles */
				$data = preg_replace("/(<body[^>]*?>)/si", $style."$1", $data);

				$data = $conversion->utf8_convert($data);

				$out = fopen($tmpfile, "w");
				fwrite($out, $data);
				fclose($out);

				if ($file["subtype"] == "vcard") {
					$conf = "&type=vcard";
				}
				/* use an frameset */
				$output = new Layout_output();
				$output->addCode("
					<html>
						<head><title>Covide File Preview</title></head>
					<frameset  ROWS='30,*' frameborder='0'>
							<frame frameborder='0' noresize scrolling='no' SRC='?mod=filesys&action=preview_header&file=".$file["name"]."' NAME='preview_top'>
							<frame frameborder='0' noresize scrolling='yes' SRC='?mod=filesys&action=getPreviewFile&file=".basename($tmpfile).$conf."' NAME='preview_main'>
					</frameset>
					</html>
				");
				$output->exit_buffer();
			}
			exit();
		}
		unlink($input);
?>