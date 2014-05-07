<?php
/**
 * Covide View object
 *
 * Window/Venster interface class.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2005 Covide BV
 * @package Covide
 */
class Layout_autocomplete Extends Layout_output {
	/* constants */
	const include_dir =  "classes/html/inc/";

	/* variables */
	private $_output = "";

	/* methods   */
  public function __construct() {
  	$this->insertTag("div", "&nbsp;", array(
			"id"    => "layer_autocomplete",
			"style" => "visibility:hidden; position:absolute; top:0px; left:0px; z-index: 10;"
		));
		$this->insertTag("iframe", "", array(
			"id"    => "layer_iframe",
			"style" => "z-index: 6; display: none; left: 0px; position: absolute; top: 0px;",
			"src"   => "blank.htm",
			"frameborder" => 0,
			"scrolling"   => "no"
		));
  }
}
?>
