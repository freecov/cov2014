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
			$this->addTag("table", array("id"=>"menu", "style" => "position: relative; z-index: 1;"));
			$this->addTag("tr");

			$this->addTag("td", array("id"=>"menuHome"));
			$this->insertAction("module_covide", "Covide", "?mod=desktop");
			$this->endTag("td");

			foreach ($this->menu_items as $k=>$a) {
				$mod = preg_replace("/^.*mod=(.*)$/si", "$1", $a[1]);
				if ($mod && in_array($mod, $GLOBALS["covide"]->current_module)) {
					$this->addTag("td", array("class"=>"menuItem", "style" => "background-position: 0px 22px"));
					$this->insertTag("div", "", array(
						"style" => "display: block; height: 22px; width: 5px;"
					));
				} else {
					$this->addTag("td", array("class"=>"menuItem"));
				}
				$this->insertAction($a[0], $k, $a[1], "", "", 0);
				$this->endTag("td");
			}
			$this->endTag("tr");
			$this->endTag("table");
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
