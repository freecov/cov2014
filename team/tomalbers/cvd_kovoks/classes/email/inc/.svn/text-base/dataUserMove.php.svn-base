<?php
	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	/* create note object if needed */
	if ($_REQUEST["note"]) {
		$note_data = new Note_data();
	}

	//new user id
	$user_ids = explode(",", $_REQUEST["user_id"]);
	$id = $_REQUEST["id"];

	//first user gets the original
	$user_id = array_shift($user_ids);

	$data = $this->getEmailById($id);
	$description = $_REQUEST["description"];


	//other user will get a copy
	foreach ($user_ids as $copy) {
		$new_id = $this->userCopy($id, $copy);

		if ($_REQUEST["note"]) {
			$note["from"]       = $_SESSION["user_id"];
			$note["to"]         = $copy;
			$note["subject"]    = gettext("Email: ").substr($data[0]["subject"], 0, 35);
			$note["address_id"] = $data[0]["address_id"];
			$note["project_id"] = $data[0]["project_id"];
			$note["body"]       = $description."\n\n<a href=\"?mod=email&action=notelink&id=".$new_id."\">[".gettext("email")."]</a>";
			$note_data->store2db($note);
		}
	}

	//retrieve inbox for this user
	$inbox = $this->getSpecialFolder("Postvak-IN", $user_id);

	$user = new User_data();
	$username = $user->getUsernameById($_SESSION["user_id"]);

	$description .= sprintf("\n[%s %s]\n", gettext("van"), $username);
	//addslashes already done, this is a browser request
	//$description  = addslashes($description);

	if ($_REQUEST["note"]) {
		$s = " (+".gettext("notitie").")";
		$note["from"]       = $_SESSION["user_id"];
		$note["to"]         = $inbox["user_id"];
		$note["subject"]    = gettext("Email: ").substr($data[0]["subject"], 0, 35);
		$note["address_id"] = $data[0]["address_id"];
		$note["project_id"] = $data[0]["project_id"];
		$note["body"]       = $description."\n\n<a href=\"?mod=email&action=notelink&id=".$id."\">[".gettext("email")."]</a>";
		$note_data->store2db($note);
	}

	$msg_id = $this->generate_message_id();
	$q = sprintf("update mail_messages set description = '%s', message_id = '%s', user_id = %d, folder_id = %d, is_new = 1 where id = %d", $description, $msg_id, $inbox["user_id"], $inbox["id"], $id);
	sql_query($q);

	echo "alert('De email$s is verplaatst naar de gebruiker(s).');\n";
	echo "history_goback();\n";
	exit();
?>
