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
		"subtitle" => gettext("site templates")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getSiteTemplates();

	$venster->addVensterData();

	$user_data = new User_data();
	$perms = $user_data->getUserDetailsById($_SESSION["user_id"]);

	if ($perms["xs_cms_level"] < 3) {
		$venster->insertTag("b", gettext("No permissions to edit templates"));
	} else {
		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("number"), "%id" );
		$view->addMapping( gettext("category"), "%%complex_category" );
		$view->addMapping( gettext("description"), "%title" );
		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_category", array(
			array(
				"type" => "action",
				"src"  => "%category_h",
				"alt"  => "%category"
			),
			array(
				"text"  => "<b>",
				"check" => "%edit_access"
			),
			array(
				"text" => array(
					" [", "%category", "]"
				)
			),
			array(
				"text"  => "</b>",
				"check" => "%edit_access"
			)
		));
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"  => "action",
				"src"   => "edit",
				"alt"   => gettext("edit"),
				"link"  => array("javascript: popup('?mod=cms&action=editTemplate&id=", "%id", "', 'template_", "%id", "', 960, 630, 1);"),
				"check" => "%edit_access"
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteTemplate&id=", "%id", "';"),
				"check" => "%del_access"
			)
		));
		$venster->addCode($view->generate_output());
		$venster->start_javascript();
			$venster->addCode("
				function popWindow() {
					var day = new Date();
					var windowid = day.getTime();
					popup('?mod=cms&action=editTemplate', windowid, 960, 630, 1);
				}
			");
		$venster->end_javascript();
		$venster->insertAction("new", gettext("new template"), "javascript: popWindow();");
	}

	$venster->insertAction("close", gettext("close"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addCode($venster->generate_output());

	$output->layout_page_end();
	$output->exit_buffer();

?>
