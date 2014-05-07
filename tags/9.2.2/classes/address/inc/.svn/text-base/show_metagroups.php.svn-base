<?php
/**
 * Covide Groupware-CRM Addressbook module.
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author IT Outsourcing Asia<krishitosasia@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

if (!class_exists("Address_output")) {
	die("no class definition found");
}
/* only allow global usermanagers and global address managers */
$user_data = new User_data();
$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);
if (!$user_info["xs_usermanage"] && !$user_info["xs_addressmanage"]) {
	header("Location: index.php?mod=address");
}

/* get the metafields */
$address_data = new Address_data();
$metafields = $address_data->get_metagroups();


/* start output */
$output = new Layout_output();
$output->layout_page();
	/* window object */
	$venster = new Layout_venster(array("title" => gettext("Addressbook"), "subtitle" => gettext("Meta Group")));
	/* create menu */
	$venster->addMenuItem(gettext("new"), "javascript: add_metagroup();");
	$venster->addMenuItem(gettext("back"), "?mod=address&action=glob_metalist");
	$venster->generateMenuItems();
	
	$venster->addVensterData();
		/* use view object */
		$view = new Layout_view();
		$view->addData($metafields);

		$view->addMapping("", "%%complex_actions");
		$view->addMapping(gettext("name"), "%name");
		$view->addMapping(gettext("description"), "%description");
		$view->defineComplexMapping("complex_actions", array(
				array(
					"type"    => "action",
					"src"     => "edit",
					"alt"     => gettext("edit"),
					"link"    => array("javascript: edit_metagroup(","%id",");")
					),
				array(
					"type"    => "action",
					"src"     => "delete",
					"alt"     => gettext("delete"),
					"link"    => array("javascript: remove_metagroup(","%id",");")
					)
			));
		$view->defineComplexMapping("complex_companyname", array(
			array(
				"type" => "link",
				"text" => array("%companyname"),
				"link" => array("index.php?mod=address&action=relcard&funambol_user=&id=", "%record_id")
			)
		));
		$venster->addCode($view->generate_output());
		$venster->addTag("br");
		$venster->insertAction("new", gettext("add"), "javascript: add_metagroup('relations', 0);");
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
/* end output and send to browser */
$output->load_javascript(self::include_dir."address_meta.js");
$output->layout_page_end();
$output->exit_buffer();
?>
