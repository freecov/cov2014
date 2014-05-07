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
	public $script_output;

	private $_rendertime;
	private $_dbtime;
	private $_hide_navigation;


	/* flag to set all input fields read-only */
	private $input_read_only = 0;

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
			$file = "";
		} else {
			/* The system is faster with caching ssl encrypted files */
			/* The following browsers do allow mixed content without warnings */
			if (preg_match("/(Opera)|(KHtml)/is", $_SERVER["HTTP_USER_AGENT"]))
				$webroot = preg_replace("/^https:\/\//s", "http://", $GLOBALS["covide"]->webroot);
			else
				$webroot = "";

			$file = sprintf("%s%s?m=%s", $webroot, $file, filemtime($file));
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
	public function load_javascript($file, $no_rewrite=0) {
		$loaded_scripts =& $GLOBALS["covide"]->loaded_scripts;
		if (!$loaded_scripts) $loaded_scripts = array();

		if (!in_array($file, $loaded_scripts)) {
			$loaded_scripts[]=$file;

			if ($no_rewrite==0) {
				$file = $this->external_file_cache_handler($file);
			}

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
		/* lowercase tag */
		$tag = trim(strtolower($tag));

		if (!is_array($settings)) {
			$settings = array();
		}
		/* filter some common mistakes */
		switch ($tag) {
			case "a":
				/* a does not have an alt tag */
				unset($settings["alt"]);
				break;
			case "span":
				/* span does not have an alt tag */
				unset($settings["alt"]);
				break;
			case "form":
				/* check for action attribute */
				if (!$settings["action"])
					$settings["action"] = "#";
				break;
		}
		/* id cannot be zero */
		if (!$settings["id"])
			unset($settings["id"]);

		/* encode onchange handler */
		if ($settings["onchange"]) {
			$settings["onchange"] = trim(preg_replace("/^javascript:/s", "", $settings["onchange"]));
			$settings["onchange"] = htmlentities($settings["onchange"]);
		}


		/*
		if (strtolower($tag) == "form") {
			$settings["accept-charset"] = "ISO-8859-15";
		}
		*/
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

	public function layout_page($title="", $hide_navigation=0, $prefetch=0) {
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

		/* -- for development
		for ($i=0;$i<=7;$i++) {
			$dir = "themes/".$i."/covide.css";
			$file = $this->external_file_cache_handler($dir);
			if ($i == $theme)
				$this->addCode("<link rel=\"stylesheet\" href=\"".$file."\" type=\"text/css\" title=\"Covide theme $i\">\n");
			else
				$this->addCode("<link rel=\"alternate stylesheet\" href=\"".$file."\" type=\"text/css\" title=\"Covide theme $i\">\n");
		}
		*/
		$dir = "themes/".$theme."/covide.css";
		$file = $this->external_file_cache_handler($dir);
		$this->addCode("<link rel=\"stylesheet\" href=\"".$file."\" type=\"text/css\" title=\"Covide theme $theme\">\n");

		if ($_REQUEST["mod"] == "cms" && !$_REQUEST["action"]) {
			$this->addCode("<!-- cms css -->");
			$this->addTag("link", array(
				"rel"  => "stylesheet",
				"type" => "text/css",
				"href" => $this->external_file_cache_handler("themes/default/cms.css"))
			);
			$this->addCode("\n");
		}

		/* some additional stylesheets */
		if (preg_match("/MSIE (5|6|7)/s", $_SERVER["HTTP_USER_AGENT"])) {
			/* extra css code for MSIE */
			$ext_file = "themes/".$theme."/styles_msie.css";
		} elseif (preg_match("/KHtml/si", $_SERVER["HTTP_USER_AGENT"])) {
			/* extra css code for KHtml */
			$ext_file = "themes/".$theme."/styles_khtml.css";
		} elseif (preg_match("/Gecko/s", $_SERVER["HTTP_USER_AGENT"])) {
			/* extra css code for gecko */
			$ext_file = "themes/".$theme."/styles_gecko.css";
		} elseif (preg_match("/Opera/s", $_SERVER["HTTP_USER_AGENT"])) {
			/* extra css code for gecko */
			$ext_file = "themes/".$theme."/styles_opera.css";
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
		/*
		if (preg_match("/^javascript\:/si", $settings["href"])) {
			$skip_encoding = 1;
		}
		*/
		if (!$skip_encoding) {
			$settings["href"] = str_replace("&","&amp;",$settings["href"]);
		}
		//$settings["ondblclick"] = "return false;";

		$this->addTag("a", $settings);
		$this->addCode($name);
		$this->endTag("a");
	}

	public function insertAction($action, $alt, $link, $id="", $no_mobile=0, $fader=0) {
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
	 /* @param bool Do not urlencode the link.
	 * @param string Overwrite the default generated id for the image
	 * @param bool If not 0 this image is part of insertAction
	 */
	public function insertImage($image, $alt, $link="", $theme=0, $skip_encoding=0, $id="", $is_action="", $fader=0) {

		if ($theme) {
			$dir = $this->getThemeFolder();
			if (!file_exists($dir.$image))
				$dir = "themes/default/";
		} else {
			$dir = "img/";
		}

		if ($GLOBALS["covide"]->contrib["USE_CONTRIB_THEME"]) {
			$contrib_dir = sprintf("contrib/%s/themes/", $GLOBALS["covide"]->contrib["USE_CONTRIB_THEME"]);
			if (file_exists($contrib_dir.$image))
				$dir = $contrib_dir;
		}
		/* get image file string */
		$file = $this->external_file_cache_handler($dir.$image);
		//$file = $dir.$image;

		/* get image file string */
		$file = $this->external_file_cache_handler($dir.$image);
		//$file = $dir.$image;
		if (!$GLOBALS["site_loaded"] && preg_match("/MSIE 6/si", $_SERVER["HTTP_USER_AGENT"])) {
			$xstyle = sprintf("filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='%s', sizingMethod='image');",
				$file);
			$file = "img/spacer.gif";
		}

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
				//$link["ondblclick"] = "return false;";
				$this->addTag("a", $link, 1);
				$this->addTag("img", $settings_image);
				$this->endTag("a");
			} else {
				$this->addTag("img", $settings_image);
			}
		} else {
			/* mobile version */
			if ($alt) {
				/*
				if ($is_action != "-" && $is_action) {
					$short = "[".$is_action."]";
				} else {
					$short = "[".$alt."]";
				}
				*/
				$short = sprintf("[%s]", $alt);

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
		return $this->insertCheckbox($name, $value, $checked, $view_compatible);
	}

	public function insertCheckbox($name, $value, $checked=0, $view_compatible=0) {
		if ($this->input_read_only)
			return "";

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
		if ($this->input_read_only)
			return "";

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
			$this->addTag("label", array("style" => "border: 0;", "class" => "radiobutton"));
		} else {
			$this->addTag("label", array("class" => "radiobutton"));
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

		if ($this->input_read_only)
			return $this->addReadonlyTextField($name, $value);

		/* replace double quotes */
		$value = str_replace("\"", "&#".ord("\"").";", $value);

		if (!is_array($settings)) {
			$settings = array($settings);
		}
		if (!$id) {
			$id = preg_replace("/(\[)|(\])|( )/s", "", $name);
		}
		$settings["type"]      = "text";
		$settings["name"]      = $name;
		$settings["id"]        = $id;
		$settings["value"]     = $value;
		$settings["class"]    .= "inputtext";
		$settings["onfocus"]  .= "handleClassFocus(this, 1);";
		$settings["onblur"]   .= "handleClassFocus(this, 0);";

		//obsolete by double quote char filter
		//$settings["onchange"] .= "scanSpecialCharacters(this);";

		$this->addTag("input", $settings);
		if (!$allow_enters) {
			$this->script_output.= "document.getElementById('$id').onkeydown = scanKeyCode; ";
		}
	}
	public function addReadonlyTextField($name, $value) {
		$this->addSpace();
		$this->addCode($value);
		$this->addHiddenField($name, $value);
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

		if ($this->input_read_only)
			return "";

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

		if ($this->input_read_only)
			return "";

		$value = str_replace("\"", "&#".ord("\"").";", $value);

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
		if ($this->input_read_only)
			return $this->addReadonlyTextField($name, nl2br($value));

		$value = str_replace("<", "&#".ord("<").";", $value);

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
		$value = str_replace("\"", "&#".ord("\"").";", $value);
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

		if ($this->input_read_only)
			return "";

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

		if ($this->input_read_only) {
			if ((int)$selected_values == $selected_values)
				return $this->addReadonlyTextField($name, $values[(int)$selected_values]);
			else
				return $this->addReadonlyTextField($name, $values[$selected_values]);
		}

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
		if (!is_array($values))
			$values = array();

		$this->addTag("select", $settings);
			foreach ($values as $k=>$v) {
				if (is_array($v)) {
					$this->addTag("optgroup", array("title"=>$k, "label"=>$k));
					foreach ($v as $kk=>$vv) {
						if (in_array($kk, $selected_values)) {
							$this->addTag("option", array("value"=>htmlentities($kk), "selected"=>1));
						} else {
							$this->addTag("option", array("value"=>htmlentities($kk)));
						}
						$this->addCode($vv);
						$this->endTag("option");
					}
					$this->endTag("optgroup");
				} else {
					if (in_array($k, $selected_values)) {
						$this->addTag("option", array("value"=>htmlentities($k), "selected"=>1));
					} else {
						$this->addTag("option", array("value"=>htmlentities($k)));
					}
					$this->addCode($v);
					$this->endTag("option");
				}
			}
		$this->endTag("select");
		$this->start_javascript();
			$this->addCode("document.getElementById('$id').addItem = function(val, txt) { addSelectBoxOption(this, val, txt); return true; }");
		$this->end_javascript();
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
		if ($this->script_output) {
			$this->start_javascript();
			$this->addCode($this->script_output);
			$this->end_javascript();
			unset($this->script_output);
		}
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
	public function setInputReadonly($state) {
		$this->input_read_only = $state;
	}

	public function expandCollapse($class_id, $initial_state = 0) {
		/* templates */
		$this->addTag("span", array(
			"id" => sprintf("expand_%s", $class_id),
			"style" => "display: none;"
		));
			$this->insertAction("tab_expand", gettext("expand view"),
				sprintf("javascript: expandCollapse(1, '%s');", $class_id));
		$this->endTag("span");
		$this->addTag("span", array(
			"id" => sprintf("collapse_%s", $class_id),
			"style" => "display: none;"
		));
			$this->insertAction("tab_collapse", gettext("close view"),
				sprintf("javascript: expandCollapse(0, '%s');", $class_id));
		$this->endTag("span");

		$this->addTag("span", array(
			"id" => sprintf("control_%s", $class_id)
		));
		if (!$initial_state) {
			$this->insertAction("tab_expand", gettext("expand view"),
				sprintf("javascript: expandCollapse(1, '%s');", $class_id));
			$this->addTag("style", array("type" => "text/css"));
				$this->addCode(sprintf("TR.%s { display: none; }", $class_id));
			$this->endTag("style");

		} else
			$this->insertAction("tab_collapse", gettext("close view"),
				sprintf("javascript: expandCollapse(0, '%s');", $class_id));

		$this->endTag("span");
		$this->addSpace(2);

	}

}
?>
