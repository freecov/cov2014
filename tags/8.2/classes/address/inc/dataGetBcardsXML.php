<?php
	/**
	 * Covide Groupware-CRM Addressbook module.
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @copyright Copyright 2000-2008 Covide BV
	 * @package Covide
	 */
	if (!class_exists("Address_data")) {
		die("no class definition found");
	}
	$output = new Layout_output();

	/* init user object */
	$user_data = new User_data();
	$userperms = $user_data->getUserPermissionsById($_SESSION["user_id"]);
	$accmanager_arr = explode(",", $user_data->permissions["addressaccountmanage"]);

	/* get the address */
	$address_data   = new Address_data();
	$addressinfo[0] = $address_data->getAddressById($id);

	if ($userperms["xs_addressmanage"]) {
		$astrict = 1;
		$astrict_rw = 1;
	} elseif ($GLOBALS["covide"]->license["address_strict_permissions"]) {

		$classification_data = new Classification_data();
		$cla_permission = $classification_data->getClassificationByAccess();

		/* get rw permissions for later use */
		$cla_address = explode("|", $addressinfo[0]["classifi"]);
		$cla_permission_rw = $classification_data->getClassificationByAccess(1);
		//$cla_xs = array_intersect($cla_address, $cla_permission_rw);
		$cla_xs = $cla_permission_rw;
		if (count($cla_xs) > 0)
			$astrict_rw = 1;
/*
		$cla_xs = array_intersect($cla_address, $cla_permission);
		if (count($cla_xs) > 0)
			$astrict = 1;
*/
	} elseif ($addressinfo[0]["addressacc"] || $addressinfo[0]["addressmanage"]) {
		$astrict_rw = 1;
		$astrict = 1;
	} else {
		$astrict_rw = 0;
		$astrict = 0;
	}

	if (($GLOBALS["covide"]->license["address_strict_permissions"] && $astrict_rw)
		|| (!$GLOBALS["covide"]->license["address_strict_permissions"] && $astrict)) {
		$achange = 1;
	}

	$businesscards  = $address_data->getBcardsByRelationID($id, $search, $cla_xs);

	$view = new Layout_view();
	$view->addData($businesscards);
	$view->addMapping("", array("%%complex_sync", "%%complex_actions"));
	$view->addMapping(gettext("name"), "%%name");
	$view->addMapping(gettext("telephone/mobile"), "%%complex_phone");

	$user_info_fb = $user_data->getUserDetailsById($_SESSION["user_id"]);

	if ($user_info_fb["xs_funambol"] || ($_REQUEST["funambol_user"]
		&& $_REQUEST["funambol_user"] != $_SESSION["user_id"])) {

		$view->defineComplexMapping("complex_sync", array(
			array(
				"type"    => "image",
				"src"     => "f_oud.gif",
				"alt"     => gettext("toggle sync"),
				"link"    => array("javascript: toggleSync('", "%id", "', 'address_businesscards', 'activate');"),
				"id"      => array("toggle_sync_", "%id"),
				"check"   => "%sync_no"
			),
			array(
				"type"    => "image",
				"src"     => "f_nieuw.gif",
				"alt"     => gettext("toggle sync"),
				"link"    => array("javascript: toggleSync('", "%id", "', 'address_businesscards', 'deactivate');"),
				"id"      => array("toggle_sync_", "%id"),
				"check"   => "%sync_yes"
			)
		));
	} else {
		$view->defineComplexMapping("complex_sync", array());
	}
	/* first column in list holds action buttons */
	if ($achange) {
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "info",
				"alt"     => gettext("more information"),
				"link"    => array("javascript: popup('index.php?mod=address&action=show_bcard&id=", "%id", "', 'view', 0, 0, 1);")
			),
			array(
				"type"    => "action",
				"src"     => "ftype_pdf",
				"alt"     => gettext("create template"),
				"link"    => array("javascript: popup('index.php?mod=templates&action=edit&address_id=", $id,"&businesscard_id=", "%id", "', 'template', 960, 600, 1);")
			),
			array(
				"type"    => "action",
				"src"     => "edit",
				"alt"     => gettext("edit"),
				"link"    => array("javascript: bcard_edit(", "%id", ");")
			),
			array(
				"type"    => "action",
				"src"     => "delete",
				"alt"     => gettext("delete"),
				"link"    => array("javascript:if(confirm(gettext('This will delete the businesscard and all linked information. Are you sure?'))){document.location.href='index.php?mod=address&action=relcard&id=$id&history=".$_REQUEST["history"]."&relcardaction=cardrem&cardid=", "%id", "';}")
			)
		));
	} else {
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "image",
				"src"     => "f_oud.gif",
				"alt"     => gettext("toggle sync"),
				"link"    => array("javascript: toggleSync('", "%id", "', 'address_businesscards', 'activate');"),
				"id"      => array("toggle_sync_", "%id"),
				"check"   => "%sync_no"
			),
			array(
				"type"    => "image",
				"src"     => "f_nieuw.gif",
				"alt"     => gettext("toggle sync"),
				"link"    => array("javascript: toggleSync('", "%id", "', 'address_businesscards', 'deactivate');"),
				"id"      => array("toggle_sync_", "%id"),
				"check"   => "%sync_yes"
			),
			array(
				"type"    => "action",
				"src"     => "info",
				"alt"     => gettext("more information"),
				"link"    => array("javascript: popup('index.php?mod=address&action=show_bcard&id=", "%id", "', 'view', 0, 0, 1);")
			),
		));

	}
	$view->defineComplexMapping("name", array(
		array(
			"type"  => "action",
			"src"   => "data_name",
			"check" => "%norcbc"
		),
		array(
			"type"  => "action",
			"src"   => "addressbook",
			"check" => "%rcbc"
		),
		array(
			"text"  => array(
				" ",
				"%alternative_name",
				"\n"
				),
			"check" => "%alternative_name"
		),
		array(
			"text"  => array(
				" ",
				"%givenname",
				" ",
				"%infix",
				" ",
				"%surname",
				"\n"
			)
		),
		array(
			"type"    => "action",
			"src"     => "data_business_email",
			"alt"     => gettext("business email address"),
			"check"   => "%business_email"
		),
		array(
			"type"    => "link",
			"text"    => array(" ","%business_email"),
			"link"    => array("javascript: emailSelectFrom('", "%business_email", "','", "$id", "');"),
			"check"   => "%business_email"
		),
		array("text" => "\n"),
		array(
			"type"    => "action",
			"src"     => "data_private_email",
			"alt"     => gettext("private email address"),
			"check"   => "%personal_email"
		),
		array(
			"type"    => "link",
			"text"    => array(" ", "%personal_email"),
			"link"    => array("javascript: emailSelectFrom('", "%personal_email", "','", "$id", "');"),
			"check"   => "%personal_email"
		)
	));
	$view->defineComplexMapping("complex_phone", array(
		array(
			"type"    => "action",
			"src"     => "data_business_telephone",
			"alt"     => gettext("business phone number"),
			"check"   => "%business_phone_nr_link"
		),
		array(
			"text" => array(
				" ",
				"%business_phone_nr_link",
				"\n"
			),
			"check" => "%business_phone_nr_link"
		),
		array(
			"type"    => "action",
			"src"     => "data_private_telephone",
			"alt"     => gettext("private phone number"),
			"check"   => "%personal_phone_nr_link"
		),
		array(
			"text" => array(
				" ",
				"%personal_phone_nr_link",
				"\n"
			),
			"check" => "%personal_phone_nr_link"
		),
		array(
			"type"    => "action",
			"src"     => "data_business_cellphone",
			"alt"     => gettext("business mobile number"),
			"check"   => "%business_mobile_nr_link"
		),
		array(
			"text" => array(
				" ",
				"%business_mobile_nr_link",
				"\n"
			),
			"check" => "%business_mobile_nr_link"
		),
		array(
			"type"    => "action",
			"src"     => "data_private_cellphone",
			"alt"     => gettext("private mobile number"),
			"check"   => "%personal_mobile_nr_link"
		),
		array(
			"text" => array(
				" ",
				"%personal_mobile_nr_link",
				"\n"
			),
			"check" => "%personal_mobile_nr_link"
		)
	));
	$view->setHtmlField("business_phone_nr_link");
	$view->setHtmlField("business_mobile_nr_link");
	$view->setHtmlField("personal_phone_nr_link");
	$view->setHtmlField("personal_mobile_nr_link");


	if (count($businesscards) >= 10) {
		$limit_height = "height: 400px; overflow:auto;";
	} else {
		$limit_height = "";
	}
	$output->insertTag("div", $view->generate_output(), array(
		"class"  => "limit_height",
		"style" => $limit_height
	));
	unset($view);

	if ($output_buffer)
		$output->exit_buffer();

?>
