<?php
/**
 * Covide Editor object
 *
 * Window/Venster interface class.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
class Layout_editor {
	/* constants */
	const include_dir = "classes/html/inc/";
	const editor_path = "xinha/";

	private $settings = array();

	public function __construct() {
	  	$this->settings = array(
	  		"width"   => "910",
	  		"height"  => "500",
	  		"toolbar" => "full"
	  	);
	}
	public function setData($html) {
		$this->settings["data"] = urlencode(gzcompress($html,9));
	}

	private function load_xinha($mini=0) {
		$output = new Layout_output();
		$output->start_javascript();

		$locale = explode("_", $_SESSION["locale"]);

		$output->addCode(sprintf("
	    	_editor_url  = '%s';
	    	_editor_lang = '".$locale[0]."';
	    	_editor_skin = '';
	    	", preg_replace("/^http(s){0,1}:\/\/[^\/]*?\//si", "/", $GLOBALS["covide"]->webroot."xinha/")
	    ));
		$output->end_javascript();

		$output->load_javascript("xinha/htmlarea.js");
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
	private function load_midas($mini=0) {
		//require(self::include_dir."editorMidas.php");
		//return $output->generate_output();
		die("not implemented");
	}
	private function load_java($mini=0, $data="") {
		$output = new Layout_output();

		$base64 = base64_encode($data);
		$width = 765;
		$height = 490;


		$locale = explode("_", $_SESSION["locale"]);

		$output->addTag("div", array("style" => "border: 1px dashed #666; width: ".($width-4).";"));
		$output->addCode( gettext("Uw browser ondersteunt niet alle mogelijkheden van de volledige editor. Het systeem gebruikt daarom nu de minder omvangrijke java editor.") );
		$output->addTag("br");
		$output->addCode(gettext("Om optimaal gebruik te kunnen maken van Covide raden wij u aan te upgraden naar "));
		$output->insertTag("a", "Firefox 1.5", array("href"=>"http://www.mozilla.com", "target"=>"_blank", "style"=>"text-decoration: underline"));
		$output->addCode(" (".gettext("of hoger").") ".gettext("of")." ");
		$output->insertTag("a", "Internet Explorer", array("href"=>"http://www.microsoft.com/ie", "target"=>"_blank", "style"=>"text-decoration: underline"));
		$output->addCode(" 6.0 SP1 (".gettext("of hoger").").");
		$output->endTag("div");

		$output->addCode("
			<SCRIPT LANGUAGE=\"JavaScript\">
				document.getElementById('contents').style.display = 'none';

				function sync_editor_contents() {
					document.getElementById('contents').value = document.applets[\"Ekit\"].getDocumentText();
				}
			</SCRIPT>
			<FORM NAME=\"EkitDemoForm\">
			<SCRIPT LANGUAGE=\"JavaScript\"><!--
				var _info=navigator.userAgent;
				var _ns=false;
				var _ns6=false;
				var _ie=(_info.indexOf(\"MSIE\") > 0 && _info.indexOf(\"Win\") > 0 && _info.indexOf(\"Windows 3.1\") < 0);
			//--></SCRIPT>
			<COMMENT>
			<SCRIPT LANGUAGE=\"JavaScript1.1\"><!--
				var _ns=(navigator.appName.indexOf(\"Netscape\") >=0 && ((_info.indexOf(\"Win\") > 0 && _info.indexOf(\"Win16\") < 0 && java.lang.System.getProperty(\"os.version\").indexOf(\"3.5\") < 0) || (_info.indexOf(\"Sun\") > 0) || (_info.indexOf(\"Linux\") > 0) || (_info.indexOf(\"AIX\") > 0) || (_info.indexOf(\"OS/2\") > 0) || (_info.indexOf(\"IRIX\") > 0)));
				var _ns6=((_ns==true) && (_info.indexOf(\"Mozilla/5\") >=0));
			//--></SCRIPT>
			</COMMENT>
			<SCRIPT LANGUAGE=\"JavaScript\"><!--
				if (_ie==true) document.writeln('<OBJECT ID=\"Ekit\" classid=\"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93\" WIDTH=\"".$width."\" HEIGHT=\"".$height."\" NAME=\"Ekit\" codebase=\"http://java.sun.com/products/plugin/autodl/jinstall-1_4-windows-i586.cab#Version=1,4,0,0\"><NOEMBED><XMP>');
				else if (_ns==true && _ns6==false) document.writeln('<EMBED type=\"application/x-java-applet;version=1.4\" code=\"com.hexidec.ekit.EkitApplet.class\" archive=\"ekit/ekitapplet.jar\" name=\"Ekit\" width=\"".$width."\" height=\"".$height."\" mayscript=true scriptable=\"true\" codebase=\".\" DOCUMENT=\"".$base64."\" BASE64=\"true\" STYLESHEET=\"ekit.css\" LANGCODE=\"".$locale[0]."\" LANGCOUNTRY=\"".$locale[1]."\" TOOLBAR=\"true\" TOOLBARMULTI=\"true\" TOOLBARSEQ=\"NW|SP|CT|CP|PS|SP|UN|RE|SP|FN|SP|UC|UM|SP|LK|SP|SR|*|BL|IT|UD|SP|SK|SU|SB|SP|AL|AC|AR|AJ|SP|UL|OL|*|ST|SP|FO\" SOURCEVIEW=\"false\" EXCLUSIVE=\"true\" SPELLCHECK=\"false\" MENUICONS=\"true\" MENU_EDIT=\"true\" MENU_VIEW=\"true\" MENU_FONT=\"true\" MENU_FORMAT=\"true\" MENU_INSERT=\"true\" MENU_TABLE=\"true\" MENU_FORMS=\"true\" MENU_SEARCH=\"true\" MENU_TOOLS=\"true\" MENU_HELP=\"true\" pluginspage=\"http://java.sun.com/products/plugin/index.html#download\"><NOEMBED><XMP>');
			//--></SCRIPT>
			<APPLET CODE=\"com.hexidec.ekit.EkitApplet.class\" ARCHIVE=\"ekit/ekitapplet.jar\" WIDTH=\"".$width."\" HEIGHT=\"".$height."\" NAME=\"Ekit\" MAYSCRIPT=true></XMP>
				<PARAM NAME=\"codebase\" VALUE=\".\">
				<PARAM NAME=\"code\" VALUE=\"com.hexidec.ekit.EkitApplet.class\">
				<PARAM NAME=\"archive\" VALUE=\"ekit/ekitapplet.jar\">
				<PARAM NAME=\"name\" VALUE=\"Ekit\">
				<PARAM NAME=\"MAYSCRIPT\" VALUE=true>
				<PARAM NAME=\"type\" VALUE=\"application/x-java-applet;version=1.4\">
				<PARAM NAME=\"scriptable\" VALUE=\"true\">
				<PARAM NAME=\"DOCUMENT\" VALUE=\"".$base64."\">
				<PARAM NAME=\"BASE64\" VALUE=\"true\">
				<PARAM NAME=\"STYLESHEET\" VALUE=\"ekit/ekit.css\">
				<PARAM NAME=\"LANGCODE\" VALUE=\"".$locale[0]."\">
				<PARAM NAME=\"LANGCOUNTRY\" VALUE=\"".$locale[1]."\">
				<PARAM NAME=\"TOOLBAR\" VALUE=\"true\">
				<PARAM NAME=\"TOOLBARMULTI\" VALUE=\"true\">
				<PARAM NAME=\"TOOLBARSEQ\" VALUE=\"NW|SP|CT|CP|PS|SP|UN|RE|SP|FN|SP|UC|UM|SP|LK|SP|SR|*|BL|IT|UD|SP|SK|SU|SB|SP|AL|AC|AR|AJ|SP|UL|OL|*|ST|SP|FO\">
				<PARAM NAME=\"SOURCEVIEW\" VALUE=\"false\">
				<PARAM NAME=\"EXCLUSIVE\" VALUE=\"true\">
				<PARAM NAME=\"SPELLCHECK\" VALUE=\"false\">
				<PARAM NAME=\"MENUICONS\" VALUE=\"true\">
				<PARAM NAME=\"MENU_EDIT\" VALUE=\"true\">
				<PARAM NAME=\"MENU_VIEW\" VALUE=\"true\">
				<PARAM NAME=\"MENU_FONT\" VALUE=\"true\">
				<PARAM NAME=\"MENU_FORMAT\" VALUE=\"true\">
				<PARAM NAME=\"MENU_INSERT\" VALUE=\"true\">
				<PARAM NAME=\"MENU_TABLE\" VALUE=\"true\">
				<PARAM NAME=\"MENU_FORMS\" VALUE=\"true\">
				<PARAM NAME=\"MENU_SEARCH\" VALUE=\"true\">
				<PARAM NAME=\"MENU_TOOLS\" VALUE=\"true\">
				<PARAM NAME=\"MENU_HELP\" VALUE=\"true\">
			</XMP>
			</APPLET>
			</NOEMBED>
			</EMBED>
			</OBJECT>
		");
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
	public function generate_editor($settings=0, $data="") {
		/* detect supported browser versions */
		require_once("classes/covide/browser.php");
		$browser = browser_detection("full");
		if (($browser["browser_name"] == "moz" && $browser["math_version"] >= 1.8) ||
			($browser["browser_name"] == "ie" && $browser["math_version"] >= 6.0))
			$xinha = 1;

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
