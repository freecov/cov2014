<?php
/**
 * Covide Groupware-CRM Classification_data
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
if (!class_exists("Classification_output")) {
	die("no class definition found");
}
/* only allow global usermanagers and global address managers */
$user_data = new User_data();
$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);
if (!$user_info["xs_classmanage"])
	header("Location: index.php?mod=address");

/* get classifications from db */
$classification_data = new Classification_data();
$classifications = $classification_data->getClassifications("", 1);
foreach($classifications as $key => $value) {
	$classifications[$key]["description_full"] = preg_replace("/(\\r\\n|\\r|\\n)/", " ", $classifications[$key]["description_full"]);
	$classifications[$key]["is_locked_check"] = ($classifications[$key]["is_locked"]) ? 0:1;
	/* if special classifiaction, add gettext */
	if ($classifications[$key]["is_locked"])
		$classifications[$key]["description"] = gettext($classifications[$key]["description"]);
}

/* init the output */
$output = new Layout_output();
$output->layout_page(gettext("classifications")." ".gettext("overview"));

/* make array with possible vars, for form generation */
$formitems = array(
	"mod"    => "classification",
	"action" => "show_classifications",
	"id"     => ""
);

/* generate nice window */
$venster = new Layout_venster(array(
	"title"    => gettext("classifications"),
	"subtitle" => gettext("overview")
));
/* menu items */
$venster->addMenuItem(gettext("new"), "javascript: cla_edit(0);");
$venster->generateMenuItems();
$venster->addVensterData();
	/* view object for the data */
	$view = new Layout_view();
	$view->addData($classifications);
	$view->addMapping(gettext("classification"), "%description");
	$view->addMapping(gettext("description"), "%description_full");
	$view->addMapping(gettext("active"), "%%complex_active");
	if ($GLOBALS["covide"]->license["has_cms"]) {
		$view->addMapping(gettext("cms"), "%%complex_cms");
	}
	if ($GLOBALS["covide"]->license["has_hypo"]) {
		$view->addMapping(gettext("type"), "%%complex_subtype");
	}
	$view->addMapping(gettext("edit"), "%%complex_actions");
	/* define the complex mappings */
	$view->defineComplexMapping("complex_active", array(
		array(
			"type"  => "action",
			"src"   => "ok",
			"alt"   => gettext("active"),
			"check" => "%is_active"
		),
		array(
			"type"  => "action",
			"src"   => "cancel",
			"alt"   => gettext("non-active"),
			"check" => "%is_nonactive"
		)
	));
	$view->defineComplexMapping("complex_cms", array(
		array(
			"type"  => "action",
			"src"   => "ok",
			"alt"   => gettext("available in cms"),
			"check" => "%is_cms"
		)
	));
	$view->defineComplexMapping("complex_subtype", array(
		array(
			"type"  => "action",
			"src"   => "state_special",
			"check" => "%h_subtype"
		),
		array(
			"type"  => "text",
			"text"  => "%h_subtype"
		)
	));
	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"  => "action",
			"src"   => "edit",
			"alt"   => gettext("change:"),
			"link"  => array("javascript: cla_edit(", "%id", ")")
		),
		array(
			"type"  => "action",
			"src"   => "delete",
			"alt"   => gettext("delete"),
			"link"  => array("javascript: cla_remove(", "%id", ")")
		)
	));
	/* output the view */
	$venster->addCode($view->generate_output());
$venster->endVensterData();
/* end window */
$output->addTag("form", array(
	"id"     => "claform",
	"method" => "get",
	"action" => "index.php"
));
foreach ($formitems as $item=>$value) {
	$output->addHiddenField($item, $value);
}
$output->addCode( $venster->generate_output() );
$output->endTag("form");

$output->load_javascript(self::include_dir."classification_actions.js");
$output->layout_page_end();
$output->exit_buffer();
?>
