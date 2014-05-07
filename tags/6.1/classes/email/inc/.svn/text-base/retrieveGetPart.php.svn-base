<?php
	if (!class_exists("Email_retrieve")) {
		exit("no class definition found");
	}
	if (!$structure) {
		$structure = imap_fetchstructure($stream, $msg_number);
	}
	if ($structure) {
		if ($mime_type == $this->get_mime_type($structure)) {
			if (!$part_number) {
				$part_number = "1";
			}
			$text = imap_fetchbody($stream, $msg_number, $part_number);
			if($structure->encoding == 3) {
				$text= (imap_base64($text));
			} else if($structure->encoding == 4) {
				$text = str_replace("\r\r","",$text);
				$text = preg_replace("'=\r[^\n]'s","=\r\n",$text);
				$text = quoted_printable_decode($text);
			}
			$return = $text;
		}
		if($structure->type==1 || $structure->type==2) {
			//multipart
			while (list($index, $sub_structure) = @each($structure->parts)) {
				if ($part_number) {
					$prefix = $part_number . '.';
				}
				$data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));

				if ($data && !$return) {
					$return = $data;
				}
			}
		}
	}

?>