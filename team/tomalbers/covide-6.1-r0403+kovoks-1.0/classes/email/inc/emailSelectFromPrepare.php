<?php
	if (!class_exists("Email")) {
		exit("no class definition found");
	}

	if (!$this->email_selector_loaded) {

		$this->email_selector_loaded = 1;

		$output = new Layout_output();
		$output->addTag("form", array(
			"method"  => "GET",
			"action"  => "index.php",
			"id"      => "mailSelectForm"
		));

		$output->addTag("div", array(
			"id"      => "email_sender_layer",
			"style"   => "visibility: hidden; position:absolute; top:0px; left:0px; z-index: 10; background-color: white; padding: 10px; border: 1px solid black;"
		));
		$output->addHiddenField("mod", "email", "alt_mod");
		$output->addHiddenField("action", "compose", "alt_action");
		$output->addHiddenField("to", "", "alt_mail_to");
		$output->addHiddenField("relation", "", "alt_mail_relation");

		$mailData = new Email_data();
		$email = $mailData->getEmailAliases();

		$table = new Layout_table( array("class"=>"list_data") );
		$table->addTableRow();
			$table->insertTableData( sprintf("<b>%s:</b> ", gettext("Afzender email adres")) );
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData();
				$table->addSelectField("mail[from]", $email, "");
				if (count($email[gettext("standaard")]) == 1 && count($email[gettext("alternatieve handtekeningen")]==0)) {
					$table->addHiddenField("alt_mail_show_select_form", 0);
				} else {
					$table->addHiddenField("alt_mail_show_select_form", 1);
				}
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData( array("align"=>"right") );
				$table->insertAction("cancel", gettext("Annuleren"), "javascript: emailSelectFromHide();");
				$table->addSpace();
				$table->insertAction("mail_send", gettext("Verder"), "javascript: document.getElementById('mailSelectForm').submit();");
			$table->endTableData();
		$table->endTableRow();
		$table->endTable();

		$venster = new Layout_venster(Array(
			"title"    => gettext("Nieuwe E-mail"),
			"subtitle" => gettext("afzender kiezen"),
			"skip_id"  => 1
		));
		$venster->addVensterData();
			$venster->addCode( $table->generate_output() );
		$venster->endVensterData();

		$table_layout = new Layout_table();
		$table_layout->addTableRow();
			$table_layout->addTableData();
				$table_layout->addCode( $venster->generate_output() );
			$table_layout->endTableData();
		$table_layout->endTableRow();
		$table_layout->endTable();

		$output->addCode( $table_layout->generate_output() );

		$output->endTag("div");
		$output->endTag("form");

		if (!$GLOBALS["covide"]->mobile) {
			$output->load_javascript(self::include_dir."emailSelectFrom.js");
			$buf = $output->generate_output();
		} else {
			unset($output);
			$output = new Layout_output();
			$output->load_javascript(self::include_dir."emailSelectFrom.js");
			$buf = $output->generate_output();
		}

	} else {

		return "";

	}
?>