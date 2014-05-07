<?php
/**
 * Covide Html output
 *
 * Html interface class.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2005 Covide BV
 * @package Covide
 */
class Layout_output {
	/* constants */
	const include_dir =  "classes/html/inc/";

	/* variables */
	public $output;
	public $block_output;

	private $_rendertime;
	private $_dbtime;
	private $_hide_navigation;

	/* methods   */

	/* __construct {{{ */
	/**
	 * __construct. Set default settings
	 */
	function __construct() {
		$output = "";
	}
	/* }}} */

	/* external_file_cache_handler {{{ */
	/**
	 * function to load an external file and still 'control' the client side caching of this file
	 *
	 * @param string The filename
	 * @return string The url and url parameters to send to the browser
	 */
	public function external_file_cache_handler($file) {
		if (!file_exists($file)) {
			$file = "themes/".$GLOBALS["covide"]->theme."/icons/help_index.png";
		} else {
			$file = sprintf("?load_external_file=%s&amp;version=%s", $file, filemtime($file));
		}
		return $file;
	}
	/* }}} */

	/* load_javascript {{{ */
	/**
	 * function to load javascript files. It will only load a javascript once, no matter how many times we request it
	 * It will append the html code to the class var $output
	 *
	 * @param string The javascript filename to load
	 */
	public function load_javascript($file) {
		$loaded_scripts =& $GLOBALS["covide"]->loaded_scripts;

		if (!in_array($file, $loaded_scripts)) {
			$loaded_scripts[]=$file;

			$file = $this->external_file_cache_handler($file);

			$this->output .= "<script language=\"Javascript1.2\" type=\"text/javascript\" src=\"".$file."\"></script>\n";
		} else {
			$this->output .= "<!-- script [$file] already loaded -->\n";
		}
	}
	/* }}} */

	/* start_javascript {{{ */
	/**
	 * Add javascript tag to the output buffer
	 */
	public function start_javascript() {
		$this->output .= "<script language=\"Javascript1.2\" type=\"text/javascript\">";
	}
	/* }}} */

	/* end_javascript {{{ */
	/**
	 * add javascript end tag to the output buffer
	 */
	public function end_javascript() {
		$this->output .= "</script>";
	}
	/* }}} */

	/* print_javascript {{{ */
	/**
	 * Add a print handler to the page javascript onload event stack
	 */
	public function print_javascript() {
		$this->start_javascript();
		$this->addCode("
			addLoadEvent(
				function() {
					setTimeout('window.print(); window.close();',500);
				}
			);
		");
		$this->end_javascript();
	}
	/* }}} */

	/* redir_javascript {{{ */
	/**
	 * Add a javascript redirection to the page
	 *
	 * @param string The location to redirect to
	 */
	public function redir_javascript($location) {
		$location = addslashes($location);
		$this->start_javascript();
		$this->addCode("location.href='$location';");
		$this->end_javascript();
		$this->exit_buffer();
	}
	/* }}} */

	/* redir_location {{{ */
	/**
	 * Send a redirection html header
	 *
	 * @param string The location to redirect to
	 */
	public function redir_location($location) {
		$location = addslashes($location);
		header("Location: ".$location);
		$this->exit_buffer();
	}
	/* }}} */

	/* addTag {{{ */
	/**
	 * Add a html start tag to the output buffer
	 *
	 * @param string the tag type (eg: a br form)
	 * @param array the settings for the tag (eg: style width)
	 * @param int Not used right now
	 */
	public function addTag($tag, $settings="", $skip_newline=0) {
		if (!is_array($settings)) {
			$settings = array();
		}
		if ($GLOBALS["covide"]->mobile) {
			$settings["onmouseover"] = "";
			$settings["onmouseout"]  = "";
		}
		$this->addCode("<".$tag);
		foreach ($settings as $k=>$v) {
			if ($k) {
				$v = trim($v);
				if (($k=="checked" || $k=="selected" || $k=="multiple") && $v) {
					$this->addCode(" $k");
				} elseif ($k=="alt" || $v !== "") {
					$this->addCode(" $k=\"$v\"");
				}
			}
		}
		$this->addCode(">");
		/*
		if ($skip_newline!=1) {
			$this->addCode("\n");
		}
		*/
	}
	/* }}} */

	/* endTag {{{ */
	/**
	 * Add a html end tag to the output buffer
	 *
	 * @param string the tag type
	 */
	public function endTag($tag) {
		$this->addCode("</".$tag.">");
	}
	/* }}} */

	/* insertTag {{{ */
	public function insertTag($tag, $code, $settings="") {
		$this->addTag($tag, $settings);
		$this->addCode($code);
		$this->endTag($tag);
	}
	/* }}} */

	public function addCode($code, $breakline="") {
		if (!$this->block_output) {
			$this->output .= $code;
			if ($breakline==1) {
				$this->output .= "<br>";
			}
		}
	}

	public function addComment($text) {
		$this->output .= "<!-- ".$text." -->\n";
	}

	public function layout_page($title="", $hide_navigation=0) {
		global $covide, $db;
		$this->_hide_navigation = $hide_navigation;

		$mtime = microtime(1);
		$this->_rendertime = $mtime;

		require(self::include_dir."page_header.php");
	}

	public function layout_page_end() {
		require(self::include_dir."page_footer.php");
	}

	public function css($theme_overwrite=0) {
		if ($theme_overwrite == -1) {
			$theme = 0;
		}

		$theme = $GLOBALS["covide"]->theme;

		$dir = "themes/".$theme."/covide.css";

		$file = $this->external_file_cache_handler($dir);
		$this->addCode("<link rel=\"stylesheet\" href=\"".$file."\" type=\"text/css\">");

		/* some additional stylesheets */
		if (preg_match("/MSIE (5|6)/s", $_SERVER["HTTP_USER_AGENT"])) {
			/* extra css code for MSIE */
			$ext_file = "themes/".$theme."/styles_msie.css";
		} elseif (preg_match("/Gecko/s", $_SERVER["HTTP_USER_AGENT"])) {
			/* extra css code for gecko */
			$ext_file = "themes/".$theme."/styles_gecko.css";
		} elseif (preg_match("/KHTML/s", $_SERVER["HTTP_USER_AGENT"])) {
			/* extra css code for KHtml */
			$ext_file = "themes/".$theme."/styles_khtml.css";
		}

		if ($ext_file) {
			$file = $this->external_file_cache_handler($ext_file);
			$this->addCode("<link rel=\"stylesheet\" href=\"".$file."\" type=\"text/css\">");
		}

		if (file_exists("custom.css")) {
			$file = $this->external_file_cache_handler("custom.css");
			$this->addCode("<link rel=\"stylesheet\" href=\"".$file."\" type=\"text/css\">");
		}

	}

	public function insertLink($name, $settings="", $skip_encoding=0) {

		if ($settings["confirm"]) {
			$settings["onclick"] = "return confirm('".addslashes($settings["confirm"])."');";
			unset($settings["confirm"]);
		}
		if (preg_match("/^javascript\:/si", $settings["href"])) {
			$skip_encoding = 1;
		}
		if (!$skip_encoding) {
			$settings["href"] = str_replace("&","&amp;",$settings["href"]);
		}
		$settings["ondblclick"] = "return false;";

		$this->addTag("a", $settings);
		$this->addCode($name);
		$this->endTag("a");
	}

	public function insertAction($action, $alt, $link, $id="", $no_mobile=0) {
		if (!$GLOBALS["covide"]->mobile || !$no_mobile) {
			require(self::include_dir."insertactions.php");
		}
	}

	/* replaceImage {{{ */
	/**
	 * replaceImage
	 *
	 * @param string The image to fetch
	 * @param bool Should we use the image from the theme of the user, or just some default image
	 * @return string The full path for the image
	 */
	public function replaceImage($image, $theme=0) {
		if ($theme) {
			$dir = "themes/".$GLOBALS["covide"]->theme."/";
		} else {
			$dir = "img/";
		}
		return ($dir.$image);
	}
	/* }}} */

	public function getThemeFolder() {
		$dir = "themes/".$GLOBALS["covide"]->theme."/";
		return $dir;
	}

	/* insertImage {{{ */
	/**
	 * insert an image in the output buffer
	 *
	 * @param string The image
	 * @param string The alt and title tag for the image
	 * @param string If set, put url around image so you can click on it
	 * @param bool Should we use the image from the theme of the user, or just some default image
	 * @param bool Do not urlencode the link.
	 * @param string Overwrite the default generated id for the image
	 * @param bool If not 0 this image is part of insertAction
	 */
	public function insertImage($image, $alt, $link="", $theme=0, $skip_encoding=0, $id="", $is_action="") {

		if ($theme) {
			$dir = $this->getThemeFolder();
		} else {
			$dir = "img/";
		}

		if (preg_match("/MSIE (5|6)/s", $_SERVER["HTTP_USER_AGENT"]) && preg_match("/icons\/.*\.png$/si", $image)) {
			$xdir = $this->external_file_cache_handler($dir.$image);
			$xstyle = "filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='".$xdir."', sizingMethod='image');";
			$dir = "img/";
			$image = "spacer.gif";
		} elseif ($is_action) {
			/* limit rendering with and height for action buttons */
			/* opera has rendering problems while loading */
			$xstyle = "max-width: 20px; max-height: 20px;";
		}

		/* get image file string */
		$file = $this->external_file_cache_handler($dir.$image);

		$settings_image = array(
			"src"   => $file,
			"alt"   => $alt,
			"title" => $alt,
			"border"=> 0,
			"class" => $class,
			"id"    => $id,
			"style" => $xstyle
		);
		if ($link) {
			$settings_image["class"] = "imagebutton";
		}
		if (!is_array($link)) {
			$link = array("href"=>$link);
		} else {
			if (!$skip_encoding) {
				$link["href"] = str_replace("&","&amp;",$link["href"]);
			}
		}

		if ($link["confirm"]) {
			$link["onclick"] = "return confirm('".addslashes($link["confirm"])."');";
			unset($link["confirm"]);
		}

		/* if normal rendering mode */
		if (!$GLOBALS["covide"]->mobile) {
			if ($link["href"]) {
				$link["ondblclick"] = "return false;";
				$this->addTag("a", $link, 1);
				$this->addTag("img", $settings_image);
				$this->endTag("a");
			} else {
				$this->addTag("img", $settings_image);
			}
		} else {
			/* mobile version */
			if ($alt) {
				if ($is_action != "-" && $is_action) {
					$short = "[".$is_action."]";
				} else {
					$short = "[".$alt."]";
				}

				if ($link["href"]) {
					$link["title"] = $alt;
					$link["alt"]   = $alt;
					$this->insertTag("a", $short, $link);
				} else {
					$this->insertTag("span", "[".$short."]", array("alt" => $alt, "title" => $alt));
				}
			} else {
				$this->addSpace();
			}
		}
	}
	/* }}} */

	/* begin form functions */
	public function insertButton($text, $alt, $link) {
		$this->addTag("input", array(
			"type"=>"button",
			"name"=>"button",
			"value"=>$text,
			"onclick"=>"location.href='".addslashes($link)."';",
			"alt"=>$alt,
			"title"=>$alt,
			"class"=>"inputbutton"
		));
	}

	public function addCheckbox($name, $value, $checked=0, $view_compatible=0) {
		return $this->insertCheckbox($name, $value, $checked, $view_compatible, $onchange);
	}

	public function insertCheckbox($name, $value, $checked=0, $view_compatible=0) {
		if ($view_compatible) {
			if (!is_array($name)) {
				$name = array($name);
			}
			if (!is_array($value)) {
				$value = array($value);
			}
			/* output is returned directly and is compatible with the view class */
			$buf = array();
			$buf[] = "<input type='checkbox' name='";
			foreach ($name as $b) {
				$buf[] = $b;
			}
			$buf[] = "' id='";
			foreach ($name as $b) {
				$buf[] = preg_replace("/(\[)|(\])|( )/s", "", $b);
			}
			$buf[]="'";
			$buf[]=" value='";
			foreach ($value as $b) {
				$buf[] = $b;
			}
			$buf[]="'";
			if ($checked) {
				$buf[]= " checked";
			}
			$buf[]=">";
			return $buf;

		} else {
			if (!$checked) {
				$checked="";
			}
			$id = preg_replace("/(\[)|(\])|( )/s", "", $name);

			$this->addTag("input", array("type"=>"checkbox", "id"=>$id, "name"=>$name, "checked"=>$checked, "value"=>$value) );
		}
	}

	/* addRadioField {{{ */
	/**
	 * Add a radio button to the output buffer
	 */
	public function addRadioField($name, $label, $value, $selected_value, $id="", $onclick="") {
		if (!$id) {
			$id = preg_replace("/(\[)|(\])|( )/s", "", $name);
		}
		$settings["type"]  = "radio";
		$settings["name"]  = $name;
		$settings["id"]    = $id;
		$settings["value"] = $value;
		$settings["onclick"] = $onclick;

		if ($value == $selected_value) {
			$settings["checked"] = 1;
		}

		if (preg_match("/MSIE (5|6)/s", $_SERVER["HTTP_USER_AGENT"])) {
			$this->addTag("label", array("style" => "border: 0;"));
		} else {
			$this->addTag("label");
		}
		$this->addTag("input", $settings);
		$this->addCode(" ".$label);
		$this->endTag("label");
	}
	/* }}} */

	/* addTextField {{{ */
	/**
	 * Add a txt field to the output buffer
	 * Will also attach a javascript call to the onkeydown to allow/disallow submit of form when this field has the focus
	 *
	 * @param string the name of the field
	 * @param string the content of the field
	 * @param array Custom tag settings
	 * @param string if set, will overwrite generated id
	 * @param int if set, will add javascript so we cannot submit the form by hitting enter key
	 */
	public function addTextField($name, $value, $settings="", $id="", $allow_enters=0) {
		if (!is_array($settings)) {
			$settings = array($settings);
		}
		if (!$id) {
			$id = preg_replace("/(\[)|(\])|( )/s", "", $name);
		}
		$settings["type"]  = "text";
		$settings["name"]  = $name;
		$settings["id"]    = $id;
		$settings["value"] = $value;
		$settings["class"] = "inputtext";
		$settings["onfocus"] = "handleClassFocus(this, 1);";
		$settings["onblur"]  = "handleClassFocus(this, 0);";
		$settings["onchange"]= "scanSpecialCharacters(this);";

		$this->addTag("input", $settings);
		if (!$allow_enters) {
			$this->start_javascript();
				$this->addCode("document.getElementById('$id').onkeydown = scanKeyCode;");
			$this->end_javascript();
		}
	}
	/* }}} */

	/* addUploadField {{{ */
	/**
	 * Add a file upload field to the output buffer
	 *
	 * @param string the name of the field
	 * @param array Custom tag settings
	 * @param string if set, will overwrite generated id
	 */
	public function addUploadField($name, $settings="", $id="") {
		if (!is_array($settings)) {
			$settings = array($settings);
		}
		if (!$id) {
			$id = preg_replace("/(\[)|(\])|( )/s", "", $name);
		}
		$settings["type"]  = "file";
		$settings["name"]  = $name;
		$settings["id"]    = $id;
		$settings["class"] = "inputtext";

		$this->addTag("input", $settings);
	}
	/* }}} */

	/* addPasswordField {{{ */
	/**
	 * Adds a password field to the output buffer
	 *
	 * @param string The name of the input field
	 * @param string The content of the input field
	 * @param array Custom tag settings
	 * @param string if set, will overwrite the generated id
	 */
	public function addPasswordField($name, $value, $settings="", $id="") {
		if (!is_array($settings)) {
			$settings = array($settings);
		}
		if (!$id) {
			$id = preg_replace("/(\[)|(\])|( )/s", "", $name);
		}
		$settings["type"]  = "password";
		$settings["name"]  = $name;
		$settings["id"]    = $id;
		$settings["value"] = $value;
		$settings["class"] = "inputtext";
		$settings["onfocus"] = "handleClassFocus(this, 1);";
		$settings["onblur"]  = "handleClassFocus(this, 0);";
		$this->addTag("input", $settings);
	}
	/* }}} */

	/* addTextArea {{{ */
	/**
	 * Add a textarea to the output buffer
	 *
	 * @param string The name of the textarea
	 * @param string The content of the textarea
	 * @param array Custom tag settings
	 * @param string If set, will overwrite the generated id of the field
	 */
	public function addTextArea($name, $value, $settings="", $id="") {
		if (!is_array($settings)) {
			$settings = array($settings);
		}
		if (!$id) {
			$id = preg_replace("/(\[)|(\])|( )/s", "", $name);
		}
		$settings["name"]  = $name;
		$settings["id"]    = $id;
		$settings["class"] = "inputtextarea";
		$settings["onfocus"] = "handleClassFocus(this, 1);";
		$settings["onblur"]  = "handleClassFocus(this, 0);";

		$this->addTag("textarea", $settings);
		$this->addCode($value);
		$this->endTag("textarea");
	}
	/* }}} */

	/* addHiddenField {{{ */
	/**
	 * Add a hidden input field to the output buffer
	 *
	 * @param string the name of the field
	 * @param string the value of the field
	 * @param string if set, overwrites the generated id of the field
	 */
	public function addHiddenField($name, $value, $id="") {
		if (!$id) {
			$id= preg_replace("/(\[)|(\])|( )/s", "", $name);
		}
		$this->addTag("input", array(
			"type"  => "hidden",
			"id"    => $id,
			"name"  => $name,
			"value" => $value
		));
	}
	/* }}} */

	/* addBinaryField {{{ */
	/**
	 * adds a input type=file field to the output buffer
	 *
	 * @param string the name of the input field
	 * @param string If set, overwrite the id, if not, this function will create an id based on the name
	 */
	public function addBinaryField($name, $id="") {
		if (!$id) {
			$id = preg_replace("/(\[)|(\])|( )/s", "", $name);
		}
		$this->addTag("input", array(
			"type"  => "file",
			"id"    => $id,
			"name"  => $name
		));
	}
	/* }}} */

	/* addSelectField {{{ */
	/**
	 * Add a select field to the output buffer
	 *
	 * @param string Name of the input field
	 * @param array The values, or an array with groups => arrays with values for the groupus
	 * @param mixed The item to put on SELECTED
	 * @param int Allow multiple selections
	 * @param mixed Custom tag settings
	 * @param string Overwrite the id of the tag, If not set will use the name field, stripped from [] chars
	 */
	public function addSelectField($name, $values, $selected_values="", $multiple = 0, $settings="", $id="") {
		if (!$id) {
			$id = preg_replace("/(\[)|(\])|( )/s", "", $name);
		}
		if (!is_array($settings)) {
			$settings = array();
		}
		$settings["name"]  = $name;
		$settings["id"]    = $id;
		$settings["class"] = "inputselect";
		if ($multiple) {
			$settings["multiple"] = 1;
		}

		if (!is_array($selected_values)) {
			$selected_values = array($selected_values);
		}
		$this->addTag("select", $settings);
			foreach ($values as $k=>$v) {
				if (is_array($v)) {
					$this->addTag("optgroup", array("title"=>$k, "label"=>$k));
					foreach ($v as $kk=>$vv) {
						if (in_array($kk, $selected_values)) {
							$this->addTag("option", array("value"=>$kk, "selected"=>1));
						} else {
							$this->addTag("option", array("value"=>$kk));
						}
						$this->addCode($vv);
						$this->endTag("option");
					}
					$this->endTag("optgroup");
				} else {
					if (in_array($k, $selected_values)) {
						$this->addTag("option", array("value"=>$k, "selected"=>1));
					} else {
						$this->addTag("option", array("value"=>$k));
					}
					$this->addCode($v);
					$this->endTag("option");
				}
			}
		$this->endTag("select");
	}
	/* }}} */
	/* end form functions */

	/* exit_buffer {{{ */
	/**
	 * Flush output buffer to the client.
	 * This is the last call you should do in a output class/method.
	 */
	public function exit_buffer() {
		echo $this->output;
		exit();
	}
	/* }}} */

	/* nbspace {{{ */
	public function nbspace($count=1) {
		$buf="";
		for ($i=0;$i<$count;$i++) {
			$buf.="&nbsp;";
		}
		return $buf;
	}
	/* }}} */

	/* addspace {{{ */
	/**
	 * Add a non breaking space to the output buffer
	 *
	 * @param int the number of spaces to add
	 */
	public function addspace($count=1) {
		$this->addCode( $this->nbspace($count) );
	}
	/* }}} */

	/* insertSpacer {{{ */
	/**
	 * Add a span with a non breaking space to the output buffer
	 *
	 * @param int The width of the span in px
	 */
	public function insertSpacer($width) {
		$this->addCode("<span style='width:".$width."px;'>&nbsp;</span>");
	}
	/* }}} */

	/* debug_output {{{ */
	/**
	 * Print content of parameter inside pre tags, outside the output buffer
	 *
	 * @param mixed the information to print
	 */
	public function debug_output($var) {
		echo "<PRE>";
		print_r($var);
		echo "</PRE>";
	}
	/* }}} */

	/* generate_output {{{ */
	/**
	 * Return the output buffer we created and filled
	 *
	 * @param int if 1 there will be no return at all
	 */
	public function generate_output($no_mobile=0) {
		//copy the buffer into a local variable
		$buffer = $this->output;

		//erase the original buffer to save memory
		$this->output = "";

		//return the output string
		if (!$GLOBALS["covide"]->mobile || !$no_mobile) {
			return $buffer;
		}
	}
	/* }}} */

	/* png2gif {{{ */
	/**
	 * convert png image tag to an gif image tag with IE transform script
	 * This is needed because IE doesnt understand transparancy in png files
	 *
	 * @param string the png file
	 * @return string the gif string
	 */
	public function png2gif($img) {
		require(self::include_dir."png2gif.php");
	}
	/* }}} */
}
?>
