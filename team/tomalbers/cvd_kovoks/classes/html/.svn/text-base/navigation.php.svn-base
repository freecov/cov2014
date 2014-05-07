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
		$this->menu_items[$name] = array($image, $link);
	}

	public function generateNavigationItems() {
		if (!$GLOBALS["covide"]->mobile) {
			$this->addTag("table", array("id"=>"menu"));
			$this->addTag("tr");

			$this->addTag("td", array("id"=>"menuhome"));
			$this->insertImage("ond_top.gif", "Covide", "?mod=desktop", 1);
			$this->endTag("td");

			foreach ($this->menu_items as $k=>$a) {
				$mod = preg_replace("/^.*mod=(.*)$/si", "$1", $a[1]);

				if ($mod && $mod == $_REQUEST["mod"]) {
					$this->addTag("td", array("class"=>"menuitem", "style" => "background-position: 0px 22px"));
					$this->insertTag("div", "", array(
						"style" => "display: block; height: 12px; width: 5px;"
					));
				} else {
					$this->addTag("td", array("class"=>"menuitem"));
				}
				$this->insertImage($a[0], $k, $a[1]);
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
