<?php
/**
 * Covide Editor module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

class Layout_editor {
	/* constants */
	const include_dir = "classes/html/inc/";

	/* variables */
	private $settings = array();
	private $browser;
	private $xhtml = 0;

	/* path to tinymce folder */
	private $tinymce_folder = "tinymce3202";

	/* methods */
	/* __construct {{{ */
	public function __construct() {
		$this->settings = array(
			"width"   => "910",
			"height"  => "500",
			"toolbar" => "full"
		);
		$this->xhtml = $GLOBALS["covide"]->output_xhtml;
	}
	/* }}} */
	/* setData {{{ */
	/**
	 * put urlencoded, gzipped html data in class settings array
	 *
	 * @param string $html HTML code to process
	 */
	public function setData($html) {
		$this->settings["data"] = urlencode(gzcompress($html,9));
	}
	/* }}} */

	private function load_tinymce($editor_type=0, $cleanup="true", $txtareaname="contents", $version=3) {
		if ($this->xhtml)
			$doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		else
			$doctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";

		/* switch editor type */
		switch ($editor_type) {
			case 2:
				$mini = 0;
				$font = 0;
				break;
			case 1:
				$mini = 1;
				$font = 1;
				break;
			default:
				$mini = 0;
				$font = 1;
				break;
		}
		/* get preference */
		require("conf/offices.php");

		if ($editor["version"])
			$version = $editor["version"];

		$txtareaname = str_replace(" ", "", $txtareaname);

		/* get locale */
		$locale = explode("_", $_SESSION["locale"]);
		$conversion = new Layout_conversion();

		/* dump aspell dictionaries */
		$langs = array();
		$cmd = "aspell dump dicts";
		exec($cmd, $ret, $retval);
		foreach ($ret as $r	) {
			$r = trim(substr($r, 0, 2));
			if ($r && !$langs[$r])
				$langs[$r] = $conversion->getLangName($r);
		}

		switch ($locale[0]) {
			case "nl":
				$deflang = "Dutch=nl";
				unset($langs["nl"]);
				break;
			case "de":
				$deflang = "German=de";
				unset($langs["de"]);
				break;
			default:
				$deflang = "English=en";
				unset($langs["en"]);
				break;
		}
		$langdev = $deflang;
		foreach ($langs as $k=>$v) {
			$langdev.= sprintf(",%s=%s", $v, $k);
		}

		$output = new Layout_output();
		$output->load_javascript(self::include_dir."editor_tinymce_init.js");

		/* IE 6/7, Gecko 1.8/1.9 (FF2/3) work faster with compression enabled */
		/*  Safari, opera do not work at all if it is enabled */
		
		/* include to check a setting */
		require("conf/offices.php");

		$_use_gzip = 0;
		if (!$html["no_static_gzip_compression"]) {
			if (($this->browser["browser_name"] == "ie" && $this->browser["math_version"] >= 6.0)
				|| ($this->browser["browser_name"] == "moz" && $this->browser["math_version"] >= 1.8)) {
				$_use_gzip = 0;
			}
		}		


		/* set editor path */
		$editor_path = $this->tinymce_folder."/jscripts/tiny_mce/";

		/* to gzip or not, that is the question */
		if ($_use_gzip)
			$editor_path .= "tiny_mce_gzip.js";
		else
			$editor_path .= "tiny_mce.js";

		/* load the editor */
		$output->load_javascript($editor_path);

		/* plugin loader */
		if ($mini) {
			$t_plugins = array(
				"style",
				"advlink",
				"spellchecker",
				"contextmenu",
				"paste",
				"visualchars",
				"nonbreaking",
				"xhtmlxtras",
				"searchreplace",
				"print",
				"fullscreen",
				"advhr"
			);
		} else {
			$t_plugins = array(
				"style",
				"advlink",
				"spellchecker",
				"contextmenu",
				"paste",
				"visualchars",
				"nonbreaking",
				"xhtmlxtras",
				"layer",
				"table",
				"advhr",
				"advimage",
				"preview",
				"media",
				"searchreplace",
				"print",
				"fullscreen",
				"advhr"
			);
		}
		$t_lang = $locale[0];
		$t_plugins[] = "inlinepopups";
		$t_plugins[] = "pagebreak";
		if ($this->browser["browser_name"] == "saf")
			$t_plugins[] = "safari";

		$t_plugins = implode(",", $t_plugins);

		/* init the editor */
		if ($_use_gzip) {
			$output->start_javascript();
			$output->addCode(sprintf("
				tinyMCE_GZ.init({
					plugins : '%1\$s',
					compress : false,
					themes : 'advanced',
					language : '%2\$s',
					languages : '%2\$s',
					disk_cache : true,
					debug : false
				});
				", $t_plugins, $t_lang
			));
			$output->end_javascript();
		}
		$output->start_javascript();
			$t = explode(",", $txtareaname);
			$t = sprintf("'%s'", implode("','", $t));

		$output->addCode(sprintf(" var tinymce_editor_fields = new Array(%s); ", $t));

		/* since 3.0.5 we have four theme options:
			skin: o2k7
				variant: blue (default), silver or black
			skin: classic
		*/
		switch ($_SESSION["theme"]) {
			case 6:
			case 7:
				$skin = "skin : 'o2k7',";
				break;
			default:
				$skin = "skin : 'o2k7', skin_variant : 'silver',";
			
		}

		if ($mini) {
			$toolbars = sprintf("%s",
				"
					theme_advanced_disable : 'image,styleselect,anchor,help,cleanup,',
					theme_advanced_buttons1_add : \"fontselect,fontsizeselect,print,removeformat\",
					theme_advanced_buttons2_add_before: \"cut,copy,paste,pastetext,pasteword,separator,search,replace,separator,forecolor,backcolor,seperator,spellchecker,charmap,advhr,seperator\",
					theme_advanced_buttons3: \"\",
				"
			);
		} else {
			$toolbars = sprintf("%s",
				"
					theme_advanced_disable : 'styleselect,help,cleanup',
					theme_advanced_buttons1_add : \"fontselect,fontsizeselect\",
					theme_advanced_buttons2_add : \"separator,preview,separator,forecolor,backcolor,seperator,styleprops,visualchars,seperator,advhr,nonbreaking\",
					theme_advanced_buttons2_add_before: \"cut,copy,paste,pastetext,pasteword,separator,search,replace,separator\",
					theme_advanced_buttons3_add_before : \"tablecontrols,separator\",
					theme_advanced_buttons3_add : \"spellchecker,media,separator,print,separator,fullscreen,seperator,insertlayer,moveforward,movebackward,absolute,pagebreak\",
				"
			);
		}
		$output->addCode(sprintf("
			tinyMCE.init({
				doctype : '%s',
				theme : 'advanced',
				%s
				mode : 'exact',
				plugins : '%s',
				language: '%s',
				languages: '%s',
				inline_styles : true,
				cleanup_on_startup : false,//$cleanup,
				cleanup : $cleanup,
				extended_valid_elements : '%sform[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit|style|title|target],input[accept|accesskey|align<bottom?left?middle?right?top|alt|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang|maxlength|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect|readonly<readonly|size|src|style|tabindex|title|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text|usemap|value],button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type|value],select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name|onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style|tabindex|title],textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect|readonly<readonly|rows|style|tabindex|title],option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|selected<selected|style|title|value]',
				apply_source_formatting : false,
				remove_trailing_nbsp : false,
				remove_linebreaks : true,
				convert_newlines_to_brs: false,
				//auto_focus : 'mce_editor_0',
				add_form_submit_trigger : false,
				submit_patch : false,
				auto_reset_designmode : false,
				nowrap : false,
				button_tile_map : true,
				convert_fonts_to_spans: true,

				theme_advanced_fonts : '%s',
				theme_advanced_toolbar_location : 'top',
				theme_advanced_toolbar_align : 'left',
				%s
				content_css : 'themes/default/editor.css',
				spellchecker_languages : '+%s',
				elements : '%s',
				debug : false,
				pagebreak_separator : '<br style=\"page-break-after: always\" />'
			});
		",
			$doctype,
			$skin,
			$t_plugins,
			$locale[0], $locale[0],
			"img[id|dir|lang|longdesc|usemap|style|class|src|onmouseover|onmouseout|border|alt=|title|hspace|vspace|width|height|align|clsid],",
			($font) ? "Arial=arial,helvetica,sans-serif;Book Antiqua=book antiqua,serif,serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sa ns-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica, sans-serif;Impact=impact;Palatino Linotype=palatino linotype,serif":"Not available=arial",
			$toolbars,
			$langdev,
			$txtareaname
		));
		$output->end_javascript();
		return $output->generate_output();
	}
	/* generate_editor {{{ */
	/**
	 * Detect what editor we can use and load it
	 *
	 * @param int $settings if 1 a small mini editor will be loaded, otherwise fullblown editor will be shown
	 * @param string $data The content to load into the editor
	 * @return string html to give the browser to show the editor or false if operation is impossible
	 */
	public function generate_editor($settings=0, $data="", $cleanup="true", $txtareaname="contents") {
		/* detect supported browser versions */
		require_once("classes/covide/browser.php");
		$browser = browser_detection("full");
		$this->browser = $browser;

		if (($browser["browser_name"] == "moz" && $browser["math_version"] >= 1.8) ||
			($browser["browser_name"] == "ie" && $browser["math_version"] >= 6.0) ||
			($browser["browser_name"] == "op" && $browser["math_version"] >= 9.21) ||
			($browser["browser_name"] == "saf" && $browser["math_version"] >= 522.12)
		)
		{
			$tinymce = 1; //we use this a default now
		}

		if ($tinymce)
			return $this->load_tinymce($settings, $cleanup, $txtareaname, "", $doctype="");
		else
			return false;
	}
	/* }}} */
	/* force_doctype {{{ */
	/**
	 * Force doctype to html/xhtml
	 *
	 * @param int force xhtml (1), html (0)
	 */
	public function force_doctype($xhtml) {
		if ($xhtml == 1)
			$this->xhtml = 1;
		else
			$this->xhtml = 0;
	}
	/* }}} */
}
?>
