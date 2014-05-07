<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Email")) {
		exit("no class definition found");
	}

	$output = new Layout_output();

	$mailData = new Email_data();
	$email = $mailData->getEmailAliases();
	$output->addTag("b");
	$output->addCode( gettext("sender email address").":" );
	$output->endTag("b");
	$output->addTag("br");
	$output->addSelectField("mail[from]", $email, $mail["from"]);
	$output->insertAction("cancel", gettext("cancel"), "javascript: mail_from_close_layer();");
	$output->insertAction("ok", gettext("next"), "mail_from_continue();");

	$output->exit_buffer();
?>