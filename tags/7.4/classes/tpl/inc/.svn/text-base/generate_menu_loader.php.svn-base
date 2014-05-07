<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}

	$fetch = $this->getApcCache("menu_".$id);
	if ($fetch) {
		echo $fetch;
	} else {

		$output = new Layout_output();
		require_once 'menudata/lib/PHPLIB.php';
		require_once 'menudata/lib/layersmenu-common.inc.php';
		require_once 'menudata/lib/layersmenu.inc.php';
		$mid = new LayersMenu();
		$mid->setTableName("cms_data");
		$mid->setTableFields(array(
			"id"		     => "id",
			"parent_id"	 => "parentPage",
			"text"	   	 => "pageTitle",
			"href"		   => "pageAlias",
			"icon"       => "",
			"title"		   => "pageTitle",
			"orderfield" => "pageLabel, datePublication desc",
			"expanded"	 => ""	,
			"target"     => ""
		));
		$mid->setPrependedUrl("/page/");
		$mid->setIconsize(16, 16);

		$q = $this->base_condition_menu;

		$sql = sprintf("select apEnabled from cms_data where id = %d", $id);
		$res = sql_query($sql);
		$apEnabled = sql_result($res,0,"",2);
		$q.= sprintf(" and apEnabled = %d ", $apEnabled);

		$mid->scanTableForMenu("menu", "", $q, &$db, $id);
		if ($_REQUEST["tpl"]) {
			$template->mid =& $mid;
			$this->exec_inline((int)$_REQUEST["tpl"]);
		}

		switch ($_REQUEST["type"]) {
			case "horizontal":
				$mid->newHorizontalMenu("menu");
				break;
			case "vertical":
				$mid->newVerticalMenu("menu");
				break;
			default:
				echo "Unknown menu type: ".$_REQUEST["type"];
				exit();
		}

		$output->addCode( $mid->getHeader() );
		$output->addCode( $mid->getMenu('menu') );
		$output->addCode( $mid->getFooter() );
		$buffer = $output->generate_output();

		$this->setApcCache("menu_".$id, $buffer);
		echo $buffer;

	}
?>