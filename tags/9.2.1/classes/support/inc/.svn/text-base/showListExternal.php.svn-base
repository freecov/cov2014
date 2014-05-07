<?php
/**
 * Covide Groupware-CRM support module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
if (!class_exists("Support_output")) {
	die("no class definition found");
}
/* get external support requests from db */
$support_data = new Support_data();
$supportitems = $support_data->getExternalIssues();

$output = new Layout_output();
$output->layout_page();

	$venster = new Layout_venster(array(
		"title"    => gettext("support"),
		"subtitle" => gettext("overview of the filled in forms")
	));
	$venster->addMenuItem(gettext("new"), "javascript: popup('supportform.php?fullpage=1', 'supportform', 448, 446, 1);");
	$venster->addMenuItem(gettext("back"), "?mod=support");
	/* end menu items */
	$venster->generateMenuItems();
	
	$venster->addVensterData();
		$venster->addTag("div", array("style" => "width: 100%; overflow-x: auto;"));
		$view = new Layout_view();
		$view->addData($supportitems);
		$view->addMapping(gettext("date"), "%human_date");
		$view->addMapping(gettext("name"), "%relation_name");
		$view->addMapping(gettext("reference nr"), "%reference_nr");
		$view->addMapping(gettext("email"), "%email");
		$view->addMapping(gettext("type"), "%human_type");
		$view->addMapping(gettext("remark"), "%body");
		$view->addMapping("", "%%complex_actions");
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("javascript: forward_issue(", "%id", ", ", "%type", ", '", "%customer_id", "');")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: remove_issue(", "%id", ");")
			)
		));
		$venster->addCode($view->generate_output());
		unset($view);
		$venster->endTag("div");
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);

	$output->load_javascript(self::include_dir."external_support_actions.js");
$output->layout_page_end();
$output->exit_buffer();
print_r($supportitems);
?>
