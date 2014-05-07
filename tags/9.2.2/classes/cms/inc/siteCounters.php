<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Cms_output")) {
		die("no class definition found");
	}

	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("hitcounters")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getHitCounters();

	$venster->addVensterData();

	$user_data = new User_data();
	$perms = $user_data->getUserPermissionsById($_SESSION["user_id"]);

	if ($perms["xs_cms_level"] < 3) {
		$venster->insertTag("b", gettext("No permissions to edit counters"));
	} else {
		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("name"), "%name" );
		$view->addMapping( gettext("counter"), "%counter1" );
		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteHitCounter&id=", "%id", "';")
			)
		));
		$venster->addCode($view->generate_output());
	}

	#$venster->insertAction("close", gettext("close"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addCode($venster->generate_output());

	$output->layout_page_end();
	$output->exit_buffer();

?>
