<?
	if (!class_exists("Email")) {
		exit("no class definition found");
	}

	$output = new Layout_output();

	$mailData = new Email_data();
	$email = $mailData->getEmailAliases();
	$output->addTag("b");
	$output->addCode( gettext("afzender email adres").":" );
	$output->endTag("b");
	$output->addTag("br");
	$output->addSelectField("mail[from]", $email, $mail["from"]);
	$output->insertAction("cancel", gettext("annuleren"), "javascript: mail_from_close_layer();");
	$output->insertAction("ok", gettext("verder"), "mail_from_continue();");

	$output->exit_buffer();
?>