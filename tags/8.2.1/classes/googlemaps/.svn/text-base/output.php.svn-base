<?php
/**
 * Covide Groupware-CRM Googlemaps output module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
Class Googlemaps_output {
	/* constants */
	const include_dir = "classes/googlemaps/inc/";

	/* methods */
	public function generate_map($address, $settings=array()) {
		$settings["id"] = "map";
		$output = new Layout_output;
		$output->addTag("div", $settings);
		$output->endTag("div");
		$output->load_javascript("http://maps.google.com/maps?file=api&amp;hl=nl&amp;v=2&amp;key=".$GLOBALS["covide"]->license["google_map_key"], 1);
		$output->start_javascript();
			$output->addCode("var maplocation = '".$address."';");
		$output->end_javascript();
		$output->load_javascript(self::include_dir."googlemaps.js");
		return $output->generate_output();
	}
	
	public function generate_route($from, $to, $settings) {
		$settings["id"] = "map_canvas";
		$locale = ($_SESSION["locale"]) ? $_SESSION["locale"] : 'en_EN';
		$output = new Layout_output;
		$output->addTag("div", $settings);
		$output->endTag("div");
		$output->addTag("div", array("id"=>"directions", "style"=>"width: 500px; height:100%;"));
		$output->endTag("div");
		$output->load_javascript("http://maps.google.com/maps?file=api&amp;hl=nl&amp;v=2&amp;key=".$GLOBALS["covide"]->license["google_map_key"], 1);
		$output->start_javascript();
			$output->addCode("var from_loc = '".$from."';");
			$output->addCode("var to_loc = '".$to."';");
			$output->addCode("var locale = '".$locale."';");
		$output->end_javascript();
		$output->load_javascript(self::include_dir."googleroute.js");
		return $output->generate_output();
	}
	/* showMap {{{ */
	/**
	 * Shows the google maps
	 *
	 * @param string Address
	 */
	public function showMap($location, $id=0) {
		require(self::include_dir."show_googlemap.php");

	}
	/* }}} */
	/* showRoute {{{ */
	/**
	 * Shows the google maps
	 *
	 * @param string Address
	 */
	public function showRoute($from, $to) {
		require(self::include_dir."show_googleroute.php");

	}
	/* }}} */
	/*public function getDistance($from, $to) {
		$output->load_javascript("http://maps.google.com/maps?file=api&amp;hl=nl&amp;v=2&amp;key=".$GLOBALS["covide"]->license["google_map_key"], 1);
		$output->start_javascript();
			$output->addCode("var from_loc = '".$from."';");
			$output->addCode("var to_loc = '".$to."';");
		$output->end_javascript();
		$output->load_javascript(self::include_dir."googleroute.js");
		$bla = file_get_contents($googleapsuri."&from=&to=");
	}*/
}
?>