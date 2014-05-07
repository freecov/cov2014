<?php
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
$metafields = $address_data->get_metafields();

/* start output */
$output = new Layout_output();
$output->layout_page();
	/* window object */
	$venster = new Layout_venster(array("title" => gettext("extra velden")));
	$venster->addVensterData();
		/* use view object */
		$view = new Layout_view();
		$view->addData($metafields);
		$view->addMapping("", "%%complex_actions");
		$view->addMapping(gettext("naam"), "%fieldname");
		$view->addMapping(gettext("type"), "%h_fieldtype");
		$venster->addCode($view->generate_output());
	$venster->endVensterData();
	$output->addCode($venster->generate_output());
	unset($venster);
/* end output and send to browser */
$output->layout_page_end();
$output->exit_buffer();
?>
