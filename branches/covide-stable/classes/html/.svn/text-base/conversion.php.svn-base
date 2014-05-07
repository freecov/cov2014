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
	public $language_cache = array();

	/* methods   */

	/* __construct {{{ */
	/**
	 * sets utf8_euro string
	 */
	function __construct() {
		$output = "";

		$this->utf8_euro_out = chr(hexdec("E2")).chr(hexdec("82")).chr(hexdec("AC"));
		$this->utf8_euro_err = chr(226).chr(130).chr(172);
	}
	/* }}} */
	/* convert_to_bytes {{{ */
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
	/* }}} */
	/* utf8_convert {{{ */
	/**
	 * Convert data from utf8 to single-character-encodings
	 *
	 * @param string $data The utf8 encoded data
	 * @return string utf[8|16] decoded data
	 */
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
	/* }}} */
	/* utf16_decode {{{ */
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
	/* }}} */
	/* limit_string {{{ */
	/**
	 * Limit a string with detection of html tags
	 *
	 * @param string $str The full string
	 * @param int $len Where to stop the new string
	 * @return string $len part of the string
	 */
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
	/* }}} */
	/* removeTags {{{ */
	/**
	 * Remove given html tags from text
	 *
	 * @param string $text The text where we want the tags to be removed
	 * @param array $tags_array tags to remove
	 * @return string The input with the specified tags removed
	 */
	public function removeTags($text, $tags_array) {
		$length = strlen($text);
		$pos =0;
		$tags_array = array_flip($tags_array);
		while ($pos < $length && ($pos = strpos($text,'<',$pos)) !== false){
			$dlm_pos = strpos($text,' ',$pos);
			$dlm2_pos = strpos($text,'>',$pos);
			if ($dlm_pos > $dlm2_pos)
				$dlm_pos = $dlm2_pos;
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
			if ($sec_pos === false)
				$sec_pos = $length-strlen($sec_tag);
			$rmv_length = $sec_pos-$pos+strlen($sec_tag);
			$text = substr_replace($text,'',$pos,$rmv_length);
			//update length
			$length = $length - $rmv_length;
			$pos++;
		}
		return $text;
	}
	/* }}} */
	/* filterTags {{{ */
	/**
	 * Wrapper around removeTags to remove script and iframe tags from html source
	 *
	 * @param string $html The source to strip the tags from
	 * @return string source with script and iframe tags removed
	 */
	public function filterTags($html) {
		$this->removeTags($html, array("script", "iframe"));
		return $html;
	}
	/* }}} */
	/* handleHTML {{{ */
	/**
	 * Remove html break and paragraph and replace them with \n
	 *
	 * @param string $str html source
	 * @return string result
	 */
	public function handleHTML($str) {
		if (preg_match("/<((br)|(p))[^>]*?>/si", $str)) {
			$str = preg_replace("/(\t)|(\r)|(\n)/si", "", $str);
			$str = preg_replace("/<br[^>]*?>/si", "\n", $str);
			$str = preg_replace("/<p[^>]*?>/si", "\n", $str);
			$str = preg_replace("/<\/p>/si", "\n", $str);
		}
		return $str;
	}
	/* }}} */
	/* urlEncodeDownload {{{ */
	/**
	 * URLencode given string
	 *
	 * @param string $str The string to urlencode
	 * @return string source parsed by php native function 'rawurlencode'
	 */
	public function urlEncodeDownload($str) {
		return rawurlencode($str);
	}
	/* }}} */
	/* decodeMimeString {{{ */
	/**
	 * Convert mime-encoded text to normal human readable text
	 *
	 * @param string $ow Mime encoded text
	 * @return string mime-decoded text
	 */
	public function decodeMimeString($ow) {
		//converts mime encoded text to normal text
		if (preg_match("/=\?/s", $ow)) {
			$elements = imap_mime_header_decode($ow);
			$ow = "";
			for ($i=0; $i<count($elements); $i++) {
				$cs = $elements[$i]->charset;
				if ($cs == "x-unknown") {
					$ow.= $this->str2utf8($elements[$i]->text);
				} elseif (strtoupper($cs) != "UTF-8" && $cs != "default") {
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
	/* }}} */
	/* str2utf8 {{{ */
	/**
	 * Convert string to UTF-8
	 *
	 * @param string $str Input
	 * @return string UTF-8 version of input
	 */
	public function str2utf8($str) {
		if (!$this->detectUTF8($str)) {
			$detect = mb_detect_encoding($str);
			if ($detect == "UTF-8")
				$detect = "ISO-8859-15";

			if ($detect != "default" && $detect)
				$str = mb_convert_encoding($str, "UTF-8", $detect);

			//detect Windows-1252
			$this->handleWindows1251ControlRange($str);

		}
		return $str;
	}
	/* }}} */
	/* detectUTF8 {{{ */
	/**
	 * Detects if a string is UTF-8
	 *
	 * @param string $string Input
	 * @return true if utf-8, false if not
	 */
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
	/* }}} */
	/* escapeBinaryCharacters {{{ */
	/**
	 * Strip tags in binary range \0 - \10 and skip escaped data
	 *
	 * @param string $data Binary data to parse
	 * @return string Binary data
	 */
	public function escapeBinaryCharacters($data) {
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
	/* }}} */
	/* handleWindows1251ControlRange {{{ */
	/**
	 * Check for range 'C1 control code'. Html should not use this.
	 * If this range is used, a wrong encoding is specified.
	 * Mostly the encoding is not ISO-8859-xx as specified but it's actually Windows-1251
	 *
	 * @param string $str String to convert
	 */
	public function handleWindows1251ControlRange(&$str) {
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
	/* }}} */
	/* generateCSVRecord {{{ */
	/**
	 * Convert given array to a string that can be appended to a CSV file
	 *
	 * @param array $data The fields of a line for CSV format
	 * @return string properly escaped and delimited representation of the input array
	 */
	public function generateCSVRecord($data) {
		foreach ($data as $k=>$v) {
			$data[$k] = str_replace("---", "", $data[$k]);
			$data[$k] = trim(str_replace("\"", "\"\"", $data[$k]));
			$data[$k] = preg_replace("/ {1,}/s", " ", $data[$k]);
		}
		$data = "\"".implode("\",\"", $data)."\"\r\n";
		return $data;
	}
	/* }}} */
	/* convertFilename {{{ */
	/**
	 * Remove some characters from input that are not allowed in filenames
	 *
	 * @param string Raw filename
	 */
	public function convertFilename(&$name) {
		$restricted = array("\"", "/", "\\", "\"", "*", "?", "<", ">", "|", ":");
		for ($i=0;$i<=31;$i++) {
			$restricted[] = chr($i);
		}
		$name = str_replace($restricted, "", $name);
	}
	/* }}} */
	/* filterNulls {{{ */
	/**
	 * Remove null characters from a string and convert them to - for example header data
	 *
	 * @param string Raw data
	 */
	public function filterNulls(&$str) {
		$str = str_replace(chr(0), "-", $str);
	}
	/* }}} */

	/* html2txtLines {{{ */
	/**
	 * see function handleHTML
	 */
	public function html2txtLines($data) {
		return trim($this->handleHTML($data));
	}
	/* }}} */
	/* convertHtmlTags {{{ */
	/**
	 * Replace the < and > in the given data with their htmlentity
	 *
	 * @param string $data The text to do the replacement
	 * @return string replaced text
	 */
	public function convertHtmlTags($data) {
		$data = str_replace(">", "&gt;", $data);
		$data = str_replace("<", "&lt;", $data);
		return $data;
	}
	/* }}} */
	/* print_k {{{ */
	/**
	 * Print some debugging info
	 *
	 * @param mixed $var array or string
	 */
	public function print_k($var) {
		if (is_array($var)) {
			foreach ($var as $k=>$v) {
				$b[] = $k;
			}
			echo sprintf("Array length: %d ", count($b));
			print_r($b);
		} else {
			echo $var;
		}
	}
	/* }}} */
	/* getLangName {{{ */
	/**
	 * Get the language name for an ISO lang code
	 *
	 * @param string $code ISO language code
	 * @return string the language name for the given ISO code
	 */
	public function getLangName($code) {
		$code = trim(strtolower($code));
		/* check if cached object is present inside Covide object */
		if ($GLOBALS["covide"]->conversion)
			$this->language_cache =& $GLOBALS["covide"]->conversion->language_cache;

		/* if cache if not present */
		if (count($this->language_cache) == 0) {
			$f = "classes/covide/inc/languages.txt";
			if ($GLOBALS["autoloader_include_path"])
				$f = sprintf("%s/%s", $GLOBALS["autoloader_include_path"], $f);

			if (file_exists($f)) {
				$handle = fopen($f, "r");
				while (($line = fgetcsv($handle, 128, "|")) !== FALSE) {
					if ($line[2])
						$this->language_cache[$line[2]] = $line[3];
				}
				fclose($handle);
			}
		}
		if ($this->language_cache[$code])
			return $this->language_cache[$code];
		else
			return gettext("Unknown").sprintf(" [%s]", $code);
	}
	/* }}} */
	public function getFonts($compatible=0) {
		/* compatible in: 0 (default), 1 (pdf export), 2 (with empty default/user settings) */
		switch ($compatible) {
			case 0:
				$font["sizes"][3] = "3 (12pt)";
				$font["sizes"][1] = "1 (8pt)";
				$font["sizes"][2] = "2 (10pt)";
				$font["sizes"][4] = "4 (14pt)";
				$font["sizes"][5] = "5 (18pt)";
				$font["sizes"][6] = "6 (24pt)";
				$font["sizes"][7] = "7 (36pt)";
				break;
			case 2:
				$font["fonts"][0] = gettext("default font");
				$font["sizes"][0] = gettext("default size");
				$font["sizes"][1] = "8px";
				$font["sizes"][2] = "10px";
				$font["sizes"][3] = "12px";
				$font["sizes"][4] = "14px";
				$font["sizes"][5] = "18px";
				$font["sizes"][6] = "24px";
				$font["sizes"][7] = "36px";
				break;
			case 1:
				$font["sizes"][3] = "12pt";
				$font["sizes"][1] = "8pt";
				$font["sizes"][2] = "10pt";
				$font["sizes"][4] = "14pt";
				$font["sizes"][5] = "18pt";
				$font["sizes"][6] = "24pt";
				$font["sizes"][7] = "36pt";
				break;
		}
		$font["fonts"]["arial,serif"]             = "Arial";
		/*
		 * XXX these 2 fonts dont work in pdf
		$font["fonts"]["book antiqua,serif"]      = "Book Antiqua";
		$font["fonts"]["comic sans ms,serif"]     = "Comic Sans MS";
		 */
		$font["fonts"]["courier,monospace"]       = "Courier";
		$font["fonts"]["georgia,serif"]           = "Georgia";
		$font["fonts"]["palatino,serif"] = "Palatino Linotype";
		$font["fonts"]["tahoma,serif"]            = "Tahoma";
		$font["fonts"]["times,serif"]             = "Times";
		$font["fonts"]["verdana,serif"]           = "Verdana";

		return $font;
	}

	/* mb_ucfirst {{{ */
	/**
	 * Multibyte safe function to uppercase the first character of given string
	 *
	 * @param string The multibyte string where we want the first character uppercased
	 * @param string The encoding the string is. will be "UTF-8" in covide
	 */
	public function mb_ucfirst($str, $encoding) {
		$fc = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
		return $fc.mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
	}
	/* }}} */
	/* seconds_to_hours {{{ */
	/**
	 * Converts seconds to hours and minutes
	 *
	 * @param int seconds to be converted
	 */
	public function seconds_to_hours($seconds) {
		$hours = floor($seconds / 3600);
		$hours_remainder = $seconds - $hours*3600;
		$minutes = floor($hours_remainder / 60);
		if ($hours == 1)
			$output_hours = gettext("hour");
		else
			$output_hours = gettext("hours");
		return $hours." ".$output_hours." ".$minutes." ".gettext("minutes");
	}
	/* }}} */
	/* sanitize {{{ */
	/**
	 * Converts string to sanitized string with nl2br()
	 *
	 * @param string text to be sanitized
	 */
	public function sanitize($data) {
		return nl2br($data);
	}
	/* }}} */
	/* parseMoney($subject,$retIntValue = false) {{{ */
	/**
	* Parses as money string to an double or int
	*
	* @author wwinterberg
	* @param string $subject string to be parsed
	* @param bool $retIntValue true -> value returned will be a integer else an rounded double
	* @return integer or a double number
	*/
	public function parseMoney($subject,$retIntValue = false) {
		// detect comma thousand separator money notation 1,234,567.89
		if(preg_match('/^\$?[0-9]+(,[0-9]{3})*(\.[0-9]{2})?$/', $subject)) {
			$subject = str_replace(',', '', $subject);
		} elseif(preg_match('/^\$?[0-9]+(.[0-9]{3})*(\,[0-9]{2})?$/', $subject))  {
			// detect dot thousand separator money notation 1.234.567,89
			$subject = str_replace('.', '', $subject);
		}

		// replace decimal comma to dot -> sprint works with dot as decimal_separator not matter what locale is set [ setlocale(LC_NUMERIC,'nl_NL') ]
		$subject = str_replace(',', '.', $subject);

		return ($retIntValue) ? sprintf("%d", $subject) : sprintf("%.2f", $subject);;
	}
	/* }}} */
	/* html2text {{{ */
	/**
	 * Convert html input to plain text
	 *
	 * This method uses the class.html2text.inc
	 * This class is released under the GPL with the following copyright:
	 * Copyright (c) 2005-2007 Jon Abernathy <jon@chuggnutt.com>
	 *
	 * @param string $html The html input
	 *
	 * @return string the plaintext version of the html
	 */
	public function html2text($html) {
		// Include the class definition file.
		require_once(self::include_dir."class.html2text.php");

		// Instantiate a new instance of the class. Passing the string
		// variable automatically loads the HTML for you.
		$h2t =& new html2text($html);

		// Simply call the get_text() method for the class to convert
		// the HTML to the plain text. Store it into the variable.
		$return = $h2t->get_text();
		return $return;
	}
	/* }}} */
}
?>
