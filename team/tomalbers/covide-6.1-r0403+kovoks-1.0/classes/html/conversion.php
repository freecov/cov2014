<?php
/**
 * Covide Html output conversions
 *
 * Html interface class.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
class Layout_conversion {
	/* constants */
	const include_dir =  "classes/html/inc/";

	/* variables */
	public $output;

	/* methods   */

	/* __construct {{{ */
	/**
	 * __construct. Set default settings
	 *
	 * @param string File to include file
	 */
	function __construct() {
		$output = "";
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
		}
		return utf8_decode($data);

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
		//euro sign
		//$ow = str_replace("=?x-unknown?B?gA==?=","&euro;",$ow);

		//converts mime encoded text to normal text
		$elements = imap_mime_header_decode($ow);
		$ow = "";
		for ($i=0; $i<count($elements); $i++) {
   	 	 $ow .= $elements[$i]->text;
		}

		/*
		$conversion = new Layout_conversion();
		$ow = $conversion->utf8_convert($ow);
		$ow = str_replace("\0", "", $ow);
		$ow = stripslashes($ow);
		*/

		return $ow;
	}

}
?>
