<?php
/**
 * Covide Conversion module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

class Layout_conversion {
	/* constants */
	const include_dir =  "classes/html/inc/";

	/* variables */
	public $output;
	public $utf8_euro_out;
	public $utf8_euro_err;

	/* methods   */

	/* __construct {{{ */
	/**
	 * __construct. Set default settings
	 *
	 * @param string File to include file
	 */
	function __construct() {
		$output = "";

		$this->utf8_euro_out = chr(hexdec("E2")).chr(hexdec("82")).chr(hexdec("AC"));
		$this->utf8_euro_err = chr(226).chr(130).chr(172);
	}
	/* }}} */

	/**
	* convert_to_bytes. Outputs size in B/KB/MB/GB
	*
	* @param int number of bytes to convert
	* @return string
	*/
	public function convert_to_bytes($size) {
		$mod = "bytes";
		if ($size > 1024) {
			$size /= 1024;
			$mod = "KB";
		}
		if ($size > 1024) {
			$size /= 1024;
			$mod = "MB";
		}
		if ($size > 1024) {
			$size /= 1024;
			$mod = "GB";
		}
		if ($mod!="bytes") {
			$size = number_format($size,2);
			$size = str_replace(".",",",$size);
		}
		$ret = $size." ".$mod;
		return $ret;
	}

	public function utf8_convert($data) {
		#echo $data."<BR>";
		//convert data from utf8 to single-character-encodings (if any)
		#$data = html_entity_decode(htmlentities($data, ENT_NOQUOTES, 'UTF-8'));

		$enc = strtolower(mb_detect_encoding($data));
		if ($enc == "utf-8") {
			$convmap = array(0x0, 0x10000, 0, 0xfffff);
			$data = mb_decode_numericentity($data, $convmap);
		} else {
			/* detect utf-16 be/le */
			if (ord(substr($data,0,1)) == 255 && ord(substr($data,1,2)) == 254) {
				return $this->utf16_decode($data);
			}
		}
		return utf8_decode($data);
	}

	/**
	 * Decode utf-16 string with detection for big/little endian
	 *
	 * @param sting $str UTF-16 le/be encoded string
	 * @return string the decoded string ready for further processing
	 */
	private function utf16_decode($str) {
		if (strlen($str) < 2 )
			return $str;

		$bom_be = true;
		$c0 = ord($str{0});
		$c1 = ord($str{1});
		if( $c0 == 0xfe && $c1 == 0xff ) {
			$str = substr($str,2);
		} elseif( $c0 == 0xff && $c1 == 0xfe ) {
			$str = substr($str,2);
			$bom_be = false;
		}
		$len = strlen($str);
		$newstr = '';
		for($i=0;$i<$len;$i+=2) {
			if( $bom_be ) {
				$val = ord($str{$i}) << 4;
				$val += ord($str{$i+1});
			} else {
				$val = ord($str{$i+1}) << 4;
				$val += ord($str{$i});
			}
			$newstr .= ($val == 0x228) ? "\n" : chr($val);
		}
		return $newstr;
	}


	public function limit_string($str, $len=50) {
		$str = str_replace("&gt;","#<",$str);
		$str = str_replace("&lt;","#>",$str);
		if (strlen($str)>$len) {
			$code = substr($str,0,$len)."...";
		} else {
			$code = $str;
		}
		$code = str_replace("#<","&lt;",$code);
		$code = str_replace("#>","&gt;",$code);
		$code = str_replace("#","",$code);
		return $code;
	}

	public function removeTags($text, $tags_array) {
		$length = strlen($text);
		$pos =0;
		$tags_array = array_flip($tags_array);
		while ($pos < $length && ($pos = strpos($text,'<',$pos)) !== false){
				$dlm_pos = strpos($text,' ',$pos);
				$dlm2_pos = strpos($text,'>',$pos);
				if ($dlm_pos > $dlm2_pos)$dlm_pos=$dlm2_pos;
				$which_tag = strtolower(substr($text,$pos+1,$dlm_pos-($pos+1)));
				$tag_length = strlen($srch_tag);
				if (!isset($tags_array[$which_tag])){
						//if no tag matches found
						++$pos;
						continue;
				}
				//find the end
				$sec_tag = '</'.$which_tag.'>';
				$sec_pos = stripos($text,$sec_tag,$pos+$tag_length);
				//remove everything after if end of the tag not found
				if ($sec_pos === false) $sec_pos = $length-strlen($sec_tag);
				$rmv_length = $sec_pos-$pos+strlen($sec_tag);
				$text = substr_replace($text,'',$pos,$rmv_length);
				//update length
				$length = $length - $rmv_length;
				$pos++;
		}
		return $text;
	}

	public function filterTags($html) {
		$this->removeTags($html, array("script", "iframe"));
		return $html;
	}

	public function handleHTML($str) {
		if (preg_match("/<((br)|(p))[^>]*?>/si", $str)) {
			$str = preg_replace("/(\t)|(\r)|(\n)/si", "", $str);
			$str = preg_replace("/<br[^>]*?>/si", "\n", $str);
			$str = preg_replace("/<p[^>]*?>/si", "\n", $str);
			$str = preg_replace("/<\/p>/si", "\n", $str);
		}
		return $str;
	}

	public function decodeMimeString($ow) {
		//converts mime encoded text to normal text
		if (preg_match("/=\?/s", $ow)) {
			$elements = imap_mime_header_decode($ow);
			$ow = "";
			for ($i=0; $i<count($elements); $i++) {
				$cs = $elements[$i]->charset;
				if (strtoupper($cs) != "UTF-8" && $cs != "default") {
					$ow .= @mb_convert_encoding($elements[$i]->text, 'UTF-8', $cs);
				} else {
					$ow .= $elements[$i]->text;
				}
			}
		} else {
			$str = $this->str2utf8($str);
		}

		return $ow;
	}

	public function str2utf8($str) {
		if (!$this->detectUTF8($str)) {
			$detect = mb_detect_encoding($str);
			if ($detect == "UTF-8")
				$detect = "ISO-8859-15";

			if ($detect != "default" && $detect) {
				$str = mb_convert_encoding($str, "UTF-8", $detect);
			}
			//detect Windows-1252
			$this->handleWindows1251ControlRange($str);

		}
		return $str;
	}

	public function detectUTF8($string) {
		return preg_match('%(?:
		[\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
		|\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
		|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
		|\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
		|\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
		|[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
		|\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
		)+%xs', $string);
	}

	public function escapeBinaryCharacters($data) {
		/* strip tags in range \0 - \10 and skip escaped data */
		$data = addcslashes($data, "\0..\10");
		$tmp = str_replace("\\000", "", $data);
		if ($tmp != $data) {
			$data = $tmp;
			$data = "warning: binary data detected!\n".$data;

			/* re apply sql filter */
			sql_filter_tags($data);
		}


		return $data;
	}

	public function handleWindows1251ControlRange(&$str) {
		/* check for range 'C1 control code'. Html should not use this. */
		/* is this range is used, a wrong encoding is specified in the mail */
		/* mostly the encoding is not ISO-8859-xx as specified but Windows-1251 */
		if (preg_match("/[\x80-\x9F]/s", $str)) {
			/* if the reserved range is used, we try to upgrade all characters in the range to UTF-8 */
			preg_match_all("/[\x80-\x9F]/s", $str, $conversion_range);
			if (is_array($conversion_range[0])) {
				$conversion_range = $conversion_range[0];
				$conversion_range = array_unique($conversion_range);

				$conv_inp = array();
				$conv_out = array();
				foreach ($conversion_range as $c) {
					/* replace character position ISO-8859-xx by Windows-1252 position */
					$str = str_replace($c, mb_convert_encoding($c, "UTF-8", "Windows-1252"), $str);
				}
			}
		}
	}

	public function generateCSVRecord($data) {

		foreach ($data as $k=>$v) {
			$data[$k] = str_replace("---", "", $data[$k]);
			$data[$k] = trim(str_replace("\"", "\"\"", $data[$k]));
			$data[$k] = preg_replace("/ {1,}/s", " ", $data[$k]);
		}
		$data = "\"".implode("\",\"", $data)."\"\r\n";
		return $data;
	}

	public function convertFilename(&$name) {
		$restricted = array("\"", "/", "\\", "*", "?", "<", ">", "|", ":");
		$name = str_replace($restricted, "", $name);
	}

	public function html2txtLines($data) {
		if (preg_match("/<((br)|(p))[^>]*?>/si", $data)) {
			$data = preg_replace("/((\r)|(\n))/s", "", $data);
			$data = preg_replace("/<br[^>]*?>/si", "\n", $data);
			$data = preg_replace("/<p[^>]*?>/si", "\n", $data);
			$data = preg_replace("/<\/p[^>]*?>/si", "\n", $data);
		}
		return trim($data);
	}

	public function convertHtmlTags($data) {
		$data = str_replace(">", "&gt;", $data);
		$data = str_replace("<", "&lt;", $data);
		return $data;
	}
}
?>
