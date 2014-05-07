<?php
/**
 * Covide Venster object
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
class Layout_navigation Extends Layout_output {
	/* constants */
	const include_dir =  "classes/html/inc/";

	/* variables */
	var $_output = "";
	var $menu_items = array();

	/* methods   */

  public function __construct() {
		$this->output     = "";
		$this->menu_items = array();
  }

	public function addNavigationItem($name, $image, $link) {
		if ($GLOBALS["autoloader_include_path"])
			$link = sprintf("/%s", $link);

		$this->menu_items[$name] = array($image, $link);
	}

	public function generateNavigationItems() {
		if (!$GLOBALS["covide"]->mobile) {
			$this->addTag("div", array("id" => "topnav", "class" => "test_bg_topnav"));
			$this->addCode("\n");
			$this->addTag("ul");
			$this->addCode("\n");

			$current_modules = array_merge(
				$GLOBALS['covide']->current_module, 
				array(strtolower($_REQUEST['mod']))
			);
			foreach ($this->menu_items as $k=>$a) {
				$mod = preg_replace("/^.*mod=(.*)$/si", "$1", $a[1]);
				$this->addTag("li");
				
				if ($mod && in_array($mod, $current_modules)) {
					$this->insertLink($k, array("href" => $a[1], "class" => $a[0]." active"));
				} else {
					$this->insertLink($k, array("href" => $a[1], "class" => $a[0]));
				}
				$this->endTag("li");
				$this->addCode("\n");
			}
			$this->endTag("ul");
			$this->addCode("\n");
			$this->endTag("div");
			$this->addCode("\n");
		} else {
			$this->insertTag("a", "Covide", array("href" => "index.php"));
			$this->addSpace();
			foreach ($this->menu_items as $k=>$a) {
				$i++;
				$this->insertTag("a", "[$k]", array("href" => $a[1]));
				$this->addCode(" ");
			}
		}
	}
}
?>
