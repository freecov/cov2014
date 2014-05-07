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
		$output->addCode("
    	_editor_url  = 'xinha/';
    	_editor_lang = 'nl';
    ");
		$output->end_javascript();

		$output->load_javascript("xinha/htmlarea.js");
		if ($mini==1) {

			/* minimalistic mode with a seperate call to init */
			$script = $output->external_file_cache_handler(self::include_dir."editor_mini.js");

			$output->insertTag("div", "", array("id"=>"editor_loader", "style"=>"position: absolute; display: none"));
			/*
			$output->start_javascript();
			$output->addCode("var editor_mini_script = '$script';");
			$output->end_javascript();
			*/
			$output->load_javascript(self::include_dir."editor_mini.js");
			$output->load_javascript(self::include_dir."editor_mini_init.js");
		} else {
			/* full mode (for html mail i.e.) */
			$output->load_javascript(self::include_dir."editor.js");
			$output->load_javascript(self::include_dir."editor_init.js");
		}

  	return $output->generate_output();
	}
	private function load_midas($settings="") {
		require(self::include_dir."editorMidas.php");
		return $output->generate_output();
	}
	private function load_java($settings="") {

	}

  public function generate_editor($settings="") {
  	return $this->load_xinha($settings);
  }
}
?>
