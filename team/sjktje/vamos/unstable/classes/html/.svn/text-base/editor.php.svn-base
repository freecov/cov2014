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
	const editor_path = "xinha/";

	/* variables */
	private $settings = array();

	/* methods */
	/* __construct {{{ */
	public function __construct() {
		$this->settings = array(
			"width"   => "910",
			"height"  => "500",
			"toolbar" => "full"
		);
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
	/* load_xinha {{{ */
	/**
	 * show the dhtml xinha editor
	 *
	 * @param int $mini if 1 smoll editor will be shown, otherwise the full blown version
	 * @return string the code to show the xinha editor
	 */
	private function load_xinha($mini=0) {
		$output = new Layout_output();
		$output->start_javascript();

		$locale = explode("_", $_SESSION["locale"]);

		switch ($GLOBALS["covide"]->theme) {
			case 6:
			case 7:
				$editor_theme = "xp-blue";
				break;
			case 0:
			case 1:
			case 3:
			case 5:
				$editor_theme = "silva";
				break;
			default:
				$editor_theme = "default";
				break;
		}

		switch ($locale[0]) {
			case "nl":
				$dict = "nl";
				break;
			case "de":
				$dict = "de_DE";
				break;
			default:
				$dict = "en_US";
		}

		$output->addCode(sprintf("
			_editor_url  = '%s';
			_editor_lang = '".$locale[0]."';
			_editor_dict = '".$dict."';
			_editor_skin = '%s';
			", preg_replace("/^http(s){0,1}:\/\/[^\/]*?\//si", "/", $GLOBALS["covide"]->webroot."xinha/"),
				$editor_theme)
		);
		$output->end_javascript();

		$output->load_javascript("xinha/XinhaCore.js");
		if ($mini==1) {
			/* minimalistic mode with a seperate call to init */
			$script = $output->external_file_cache_handler(self::include_dir."editor_mini.js");

			$output->insertTag("div", "", array("id"=>"editor_loader", "style"=>"position: absolute; display: none"));
			$output->load_javascript(self::include_dir."editor_mini.js");
			$output->load_javascript(self::include_dir."editor_mini_init.js");
		} else {
			/* full mode (for html mail i.e.) */
			$output->load_javascript(self::include_dir."editor.js");
			$output->load_javascript(self::include_dir."editor_init.js");
		}
		return $output->generate_output();
	}
	/* }}} */

	private function load_tinymce($mini=0, $cleanup="true") {

		$locale = explode("_", $_SESSION["locale"]);
		$langs = array(
			"en" => "English",
			"nl" => "Dutch",
			"de" => "German",
			"fr" => "French",
			"no" => "Norwegian",
			"es" => "Spanish"
		);
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
		$output->load_javascript("tinymce/jscripts/tiny_mce/tiny_mce_gzip.js");
		$output->start_javascript();
		if ($mini) {
			$t_plugins = "style,advlink,spellchecker,contextmenu,paste,visualchars,nonbreaking,xhtmlxtras";
			$output->addCode(sprintf("
				tinyMCE_GZ.init({
					plugins : '%s',
					themes : 'advanced',
					languages : '".$locale[0]."',
					disk_cache : true,
					debug : false
				});
			", $t_plugins));
		} else {
			$t_plugins = "style,advlink,spellchecker,contextmenu,paste,visualchars,nonbreaking,xhtmlxtras,layer,table,advhr,advimage,preview,media,searchreplace,print,fullscreen";
			$output->addCode(sprintf("
				tinyMCE_GZ.init({
					plugins : '%s',
					themes : 'advanced',
					languages : '".$locale[0]."',
					disk_cache : true,
					debug : false
				});
			", $t_plugins));
		}
		$output->end_javascript();
		$output->start_javascript();

		if ($mini) {
			$toolbars = sprintf("%s",
				"
					theme_advanced_disable : 'image,styleselect,anchor,help,cleanup,code',
					theme_advanced_buttons1_add: \"seperator,bullist,numlist,outdent,indent,seperator,undo,redo,seperator,link,unlink,seperator,charmap,spellchecker,styleprops\",
					theme_advanced_buttons2: \"\",
					theme_advanced_buttons3: \"\",
				"
			);
		} else {
			$toolbars = sprintf("%s",
				"
					theme_advanced_disable : 'styleselect,help,cleanup',
					theme_advanced_buttons1_add : \"fontselect,fontsizeselect\",
					theme_advanced_buttons2_add : \"separator,preview,separator,forecolor,backcolor,seperator,styleprops,visualchars,nonbreaking,seperator,cleanup\",
					theme_advanced_buttons2_add_before: \"cut,copy,paste,pastetext,pasteword,separator,search,replace,separator\",
					theme_advanced_buttons3_add_before : \"tablecontrols,separator\",
					theme_advanced_buttons3_add : \"spellchecker,media,separator,print,separator,fullscreen,seperator,insertlayer,moveforward,movebackward,absolute\",
				"
			);
		}
		$output->addCode(sprintf("
			tinyMCE.init({
				theme : 'advanced',
				mode : 'exact',
				plugins : '%s',
				language : '%s',
				inline_styles : true,
				cleanup_on_startup : $cleanup,
				cleanup : $cleanup,
				extended_valid_elements : 'form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit|style|title|target],input[accept|accesskey|align<bottom?left?middle?right?top|alt|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang|maxlength|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect|readonly<readonly|size|src|style|tabindex|title|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text|usemap|value],button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type|value],select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name|onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style|tabindex|title],textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect|readonly<readonly|rows|style|tabindex|title],option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|selected<selected|style|title|value]',
				apply_source_formatting : false,
				//remove_trailing_nbsp : false,
				auto_focus : 'mce_editor_0',
				add_form_submit_trigger : false,
				submit_patch : false,
				auto_reset_designmode : false,
				nowrap : false,
				button_tile_map : true,
				convert_fonts_to_spans: true,

				theme_advanced_fonts : 'Arial=arial,helvetica,sans-serif;Book Antiqua=book antiqua,serif,serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sa ns-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica, sans-serif;Impact=impact;Palatino Linotype=palatino linotype,serif',
				theme_advanced_toolbar_location : 'top',
				theme_advanced_toolbar_align : 'left',
				%s
				content_css : 'tinymce/style.php',
				spellchecker_languages : '%s',
				elements : 'contents',
				debug : false
			});
		",
			$t_plugins,
			$locale[0],
			$toolbars,
			$langdev
		));
		$output->end_javascript();
		return $output->generate_output();
	}
	/* load_midas {{{ */
	private function load_midas($mini=0) {
		die("not implemented");
	}
	/* }}} */
	/* load_java {{{ */
	/**
	 * Show the java ekit editor
	 *
	 * @param int $mini if 1 smoll version will be shown, otherwise fullblown version
	 * @param string $data The data to show in the editor
	 * @return string htmlcode to show the editor
	 */
	private function load_java($mini=0, $data="") {
		$output = new Layout_output();

		$base64 = base64_encode(html_entity_decode($data));
		$width = 765;
		$height = 490;


		$locale = explode("_", $_SESSION["locale"]);

		$output->addTag("div", array("style" => "border: 1px dashed #666;"));
		$output->addCode( gettext("Your browser might not support all function of the normal editor. The java based alternative is now activated.") );
		/*
		$output->addTag("br");
		$output->addCode(gettext("To be able to use all functions of Covide we suggest you upgrade to"));
		$output->insertTag("a", "Firefox 1.5", array("href"=>"http://www.mozilla.com", "target"=>"_blank", "style"=>"text-decoration: underline"));
		$output->addCode(" (".gettext("or better").") ".gettext("or")." ");
		$output->insertTag("a", "Internet Explorer", array("href"=>"http://www.microsoft.com/ie", "target"=>"_blank", "style"=>"text-decoration: underline"));
		$output->addCode(" 6.0 SP1 (".gettext("or better").").");
		*/
		$output->endTag("div");

		$output->addCode("
			<SCRIPT LANGUAGE=\"JavaScript\">
				document.getElementById('contents').style.display = 'none';

				function sync_editor_contents() {
					document.getElementById('contents').value = document.applets[\"Ekit\"].getDocumentText();
				}
			</SCRIPT>
			<FORM NAME=\"EkitDemoForm\">
		");
		$ekitdata = file_get_contents(self::include_dir."ekit.htm");
		$find = array(
			"===data===",
			"===lang===",
			"===country==="
		);
		$repl = array(
			$base64, $locale[0], $locale[1]
		);
		$ekitdata = str_replace($find, $repl, $ekitdata);
		$output->addCode("\n\n");
		$output->addCode($ekitdata);
		$output->addCode("\n\n");
		$output->addCode("</FORM>");

		return $output->generate_output();
	}
	/* }}} */
	/* generate_editor {{{ */
	/**
	 * Detect what editor we can use and load it
	 *
	 * @param int $settings if 1 a small mini editor will be loaded, otherwise fullblown editor will be shown
	 * @param string $data The content to load into the editor
	 * @return string html to give the browser to show the editor or false if operation is impossible
	 */
	public function generate_editor($settings=0, $data="", $cleanup="true") {
		/* detect supported browser versions */
		require_once("classes/covide/browser.php");
		$browser = browser_detection("full");
		if (($browser["browser_name"] == "moz" && $browser["math_version"] >= 1.8) ||
			($browser["browser_name"] == "ie" && $browser["math_version"] >= 6.0) ||
			($browser["browser_name"] == "op" && $browser["math_version"] >= 9.21))
		{
			$xinha = 0;
			$tinymce = 1; //we use this a default now
		}

		if ($tinymce)
			return $this->load_tinymce($settings, $cleanup);
		if ($xinha)
			return $this->load_xinha($settings);
		elseif (!$settings)
			return $this->load_java($settings, $data);
		else
			return false;
	}
	/* }}} */
}
?>
