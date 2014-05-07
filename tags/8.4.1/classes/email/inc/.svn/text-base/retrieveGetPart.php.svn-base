<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Email_retrieve")) {
		exit("no class definition found");
	}
	if (!$structure) {
		$structure = imap_fetchstructure($stream, $msg_number);
	}
	if ($structure) {
		$cur_mime = $this->get_mime_type($structure);
		if ($mime_type == $cur_mime) {
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
			/* detect character encoding */
			if ($structure->parameters) {
				foreach ($structure->parameters as $s) {
					if ($s->attribute == "charset") {
						$character_encoding = $s->value;
					}
				}
			}
			$return = array(
				"text" => $text,
				"enc"  => $character_encoding
			);
		}
		if ($structure->type==1 || $structure->type==2) {
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