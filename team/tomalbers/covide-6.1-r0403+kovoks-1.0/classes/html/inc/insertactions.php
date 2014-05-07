<?php
	if (!class_exists("Layout_output")) {
		die("no class definition found");
	}

	switch ($action) {
		/* navigation */
		case "up"       : $str = gettext("omhoog"); $img = "top";            break;
		case "down"     : $str = gettext("omlaag"); $img = "bottom";         break;
		case "back"     : $str = gettext("terug");  $img = "back";           break;
		case "forward"  : $str = gettext("verder"); $img = "forward";        break;
		case "first"    : $str = gettext("begin");  $img = "2leftarrow";     break;
		case "last"     : $str = gettext("eind");   $img = "2rightarrow";    break;
		case "remove"   : $str = gettext("verwijder"); $img = "back";              break;
		case "add"      : $str = gettext("toevoegen"); $img = "forward";           break;
		case "close"    : $str = gettext("sluiten");   $img = "window_suppressed"; break;
		case "cut"      : $str = gettext("knippen");   $img = "editcut";           break;
		case "paste"    : $str = gettext("plakken");   $img = "editpaste";         break;

		/* views */
		case "view_all"    : $str = gettext("layout"); $img = "view_choose"; break;
		case "view_tree"   : $str = gettext("layout"); $img = "view_tree";   break;
		case "view_new"    : $str = gettext("layout"); $img = "view_bottom"; break;
		case "search"      : $str = gettext("zoeken"); $img = "filefind";    break;

		/* module shortcuts */
		case "go_calendar": $str = gettext("kalender"); $img = "today";        break;
		case "go_note"    : $str = gettext("notities"); $img = "txt";          break;
		case "go_email"   : $str = gettext("email");    $img = "mail_new";     break;
		case "go_todo"    : $str = gettext("todos");    $img = "clock";        break;
		case "go_rss"     : $str = gettext("rss");      $img = "filter";       break;
		case "go_alert"   : $str = gettext("alerts");   $img = "bell";         break;
		case "go_fax"     : $str = gettext("fax");      $img = "fileprint";    break;
		case "go_support" : $str = gettext("support");  $img = "kdmconfig";    break;
		case "go_desktop" : $str = gettext("desktop");  $img = "desktop";      break;
		case "go_sales"   : $str = gettext("sales");    $img = "keditbookmarks"; break;
		case "go_mortgage": $str = gettext("mortgage"); $img = "kdmconfig";      break;

		/* actions */
		case "important": $str = gettext("belangrijk"); $img = "flag";           break;
		case "open"     : $str = gettext("open");       $img = "fileopen";       break;
		case "edit"     : $str = gettext("wijzig");     $img = "edit";           break;
		case "new"      : $str = gettext("nieuw");      $img = "wizard";         break;
		case "delete"   : $str = gettext("verwijder");  $img = "cancel";         break;
		case "view"     : $str = gettext("tonen");      $img = "folder_open";    break;
		case "ok"       : $str = gettext("ok");         $img = "ok";             break;
		case "cancel"   : $str = gettext("annuleren");  $img = "cancel";         break;
		case "save"     : $str = gettext("opslaan");    $img = "filesave";       break;
		case "save_all" : $str = gettext("alles opslaan"); $img = "save_all";    break;
		case "contents" : $str = gettext("inhoud");     $img = "contents";       break;
		case "info"     : $str = gettext("info");       $img = "rss_tag";        break;
		case "help"     : $str = gettext("help");       $img = "help";           break;
		case "print"    : $str = gettext("print");      $img = "printer1";       break;
		case "toggle"   : $str = gettext("wissel");     $img = "lastmoves";      break;
		case "users"    : $str = gettext("gebruikers"); $img = "package_settings"; break;
		case "move"     : $str = gettext("laatste");    $img = "2rightarrow";     break;

		/* file operations */
		case "attachment"    : $str = gettext("attach");         $img = "attach";         break;
		case "file_download" : $str = gettext("download");       $img = "kget";           break;
		case "multi_download": $str = gettext("download multi"); $img = "vcs_update";     break;
		case "file_add"      : $str = gettext("toevoegen");      $img = "vcs_add";        break;
		case "file_delete"   : $str = gettext("verwijder");      $img = "editdelete";     break;
		case "file_edit"     : $str = gettext("wijzig");         $img = "edit";           break;
		case "file_export"   : $str = gettext("export");         $img = "3floppy_mount";  break;
		case "file_zip"      : $str = gettext("zip");            $img = "package";        break;
		case "file_upload"   : $str = gettext("upload");         $img = "forward";        break;
		case "file_attach"   : $str = gettext("attach");         $img = "add";            break;
		case "file_multiple" : $str = gettext("upload multi");   $img = "save_all";       break;

		/* file types */
		case "ftype_text"    : $str = gettext("txt"); $img = "txt";            break;
		case "ftype_html"    : $str = gettext("htm"); $img = "html";           break;
		case "ftype_doc"     : $str = gettext("doc"); $img = "wordprocessing"; break;
		case "ftype_calc"    : $str = gettext("xls"); $img = "spreadsheet";    break;
		case "ftype_binary"  : $str = gettext("dat"); $img = "unknown";        break;
		case "ftype_pdf"     : $str = gettext("pdf"); $img = "pdf";            break;
		case "ftype_image"   : $str = gettext("afb"); $img = "image";          break;
		case "ftype_rfc822"  : $str = gettext("rfc"); $img = "mail_get";       break;
		case "ftype_sound"   : $str = gettext("wav"); $img = "sound";       break;

		/* folder operations */
		case "folder_up"        : $str = gettext("map hoger");    $img = "folder_outbox";  break;
		case "folder_open"      : $str = gettext("map");          $img = "folder_open";    break;
		case "folder_closed"    : $str = gettext("map");          $img = "folder";         break;
		case "folder_lock"      : $str = gettext("rechten");      $img = "folder_locked";  break;
		case "folder_relation"  : $str = gettext("relatie");      $img = "folder_green";   break;
		case "folder_project"   : $str = gettext("projecten");    $img = "folder_tar";     break;
		case "folder_hrm"       : $str = gettext("hrm");          $img = "folder_orange";  break;
		case "folder_my_docs"   : $str = gettext("mijn docs");    $img = "folder_home";    break;
		case "folder_denied"    : $str = gettext("geen toegang"); $img = "folder_red";     break;
		case "folder_global"    : $str = gettext("map");          $img = "folder_grey";     break;
		case "tree"             : $str = gettext("boom");         $img = "tree";           break;

		/* folder permissions */
		case "permissions"           : $str = gettext("rechten");          $img = "lock";              break;
		case "permissions_read"      : $str = gettext("leesrechten");      $img = "folder_orange";     break;
		case "permissions_write"     : $str = gettext("schrijfrechten");   $img = "folder_green";      break;
		case "permissions_special"   : $str = gettext("speciale rechten"); $img = "folder_yellow";     break;
		case "permissions_none"      : $str = gettext("geen rechten");     $img = "folder_red";        break;
		case "permissions_add_write" : $str = gettext("toevoeg rechten");  $img = "vcs_add";           break;
		case "permissions_add_read"  : $str = gettext("lees rechten");     $img = "vcs_remove";        break;

		/* states */
		case "state_public"  : $str = "-"; $img = "personal_silver"; break;
		case "state_private" : $str = "-"; $img = "personal_red";    break;
		case "state_special" : $str = "-"; $img = "personal";        break;

		/* address */
		case "addressbook"       : $str = gettext("adresboek"); $img = "toggle_log"; break;

		/* calendar */
		case "calendar_today"    : $str = gettext("kalender"); $img = "today"; break;
		case "calendar_reg_hour" : $str = gettext("uren");     $img = "clock"; break;

		/* mail */
		case "mail_new"       : $str = gettext("nieuwe mail"); $img = "mail_new";      break;
		case "mail_new_alt"   : $str = gettext("nieuwe mail"); $img = "mail_post_to";  break;
		case "mail_external"  : $str = gettext("mail extern"); $img = "thunderbird";   break;
		case "mail_forward"   : $str = gettext("forward");     $img = "mail_send";     break;
		case "mail_send"      : $str = gettext("stuur");       $img = "mail_forward";  break;
		case "mail_reply"     : $str = gettext("reply");       $img = "mail_reply";    break;
		case "mail_reply_all" : $str = gettext("reply alle");  $img = "mail_replyall"; break;
		case "mail_headers"   : $str = gettext("headers");     $img = "mail_find";     break;
		case "mail_copy"      : $str = gettext("kopieer");     $img = "mail_new";      break;
		case "mail_move"      : $str = gettext("verplaats");   $img = "mail_forward";  break;
		case "mail_signatures": $str = gettext("signatures");  $img = "personal";      break;
		case "mail_filters"   : $str = gettext("filters");     $img = "filter";        break;
		case "mail_templates" : $str = gettext("templates");   $img = "view_sidetree"; break;
		case "mail_tracking"  : $str = gettext("tracking");    $img = "viewmag";       break;
		case "mail_readconfirm" : $str = gettext("leesbevestiging"); $img = "pencil";  break;
		case "mail_retrieve"    : $str = gettext("forceer ophalen"); $img = "legalmoves"; break;

		/* address data icons */
		case "data_birthday"           : $str = gettext("verjaardageply"); $img = "wine"; break;
		case "data_name"               : $str = gettext("naam");           $img = "personal"; break;
		case "data_private_email"      : $str = gettext("prive email");    $img = "data_mail_private"; break;
		case "data_private_telephone"  : $str = gettext("prive tel");      $img = "data_telephone_private"; break;
		case "data_private_cellphone"  : $str = gettext("prive mobiel");   $img = "data_cellphone_private"; break;
		case "data_phone_rining"       : $str = gettext("voip");           $img = "data_telephone_ringing"; break;

		case "data_business_email"     : $str = gettext("zakelijk email");  $img = "data_mail_business"; break;
		case "data_business_telephone" : $str = gettext("zakelijk tel");    $img = "data_telephone_business"; break;
		case "data_business_cellphone" : $str = gettext("zakelijk mobiel"); $img = "data_cellphone_business"; break;

		/* note */
		case "note"           : $str = gettext("notitie"); $img = "txt"; break;

		/* performance stats */
		case "performance_webserver" : $str = "-"; $img = "fork";       break;
		case "performance_dbserver"  : $str = "-"; $img = "vcs_commit"; break;
		case "performance_total"     : $str = "-"; $img = "xclock";     break;
		case "performance_show"      : $str = "-"; $img = "xload";      break;
		case "performance_client"    : $str = "-"; $img = "exec";       break;

		/* project */
		case "activity": $str = "-"; $img = "xedit"; break;

		default: $str = "error!"; $img = "help_index"; break;
	}

	$img = sprintf("icons/%s.png", $img);
	if (!is_array($link)) {
		$link = array(
			"href" => $link
		);
	}
	$link["class"] = "action";

	$this->insertImage($img, $alt, $link, 1, "", $id, $str);

?>
