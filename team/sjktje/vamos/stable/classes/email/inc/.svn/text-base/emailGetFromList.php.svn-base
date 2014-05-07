<?
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