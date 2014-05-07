<?php
	$avail_actions = array(
		/*navigation*/
		"up"     		  => array("alt"=>gettext("omhoog"),     "src"=>"top"),
		"down"   		  => array("alt"=>gettext("omlaag"),     "src"=>"bottom"),
		"back"   		  => array("alt"=>gettext("terug"),      "src"=>"back"),
		"forward"		  => array("alt"=>gettext("verder"),     "src"=>"forward"),
		"first"  		  => array("alt"=>gettext("begin"),      "src"=>"2leftarrow"),
		"last"   		  => array("alt"=>gettext("eind"),       "src"=>"2rightarrow"),
		"remove"	 	  => array("alt"=>gettext("verwijder"),  "src"=>"back"),
		"add"    		  => array("alt"=>gettext("toevoegen"),  "src"=>"forward"),
		"close"  		  => array("alt"=>gettext("sluiten"),    "src"=>"window_suppressed"),
		"cut"    		  => array("alt"=>gettext("knippen"),    "src"=>"editcut"),
		"paste"  		  => array("alt"=>gettext("plakken"),    "src"=>"editpaste"),
		"reload" 		  => array("alt"=>gettext("vernieuwen"), "src"=>"legalmoves"),

		/*views*/
		"view_all"    => array("alt"=>gettext("layout"), 		 "src"=>"view_choose"),
		"view_tree"   => array("alt"=>gettext("layout"), 		 "src"=>"view_tree"),
		"view_new"    => array("alt"=>gettext("layout"), 		 "src"=>"view_bottom"),
		"search"      => array("alt"=>gettext("zoeken"), 		 "src"=>"filefind"),

		/*moduleshortcuts*/
		"go_calendar" => array("alt"=>gettext("kalender"),   "src"=>"today"),
		"go_note"     => array("alt"=>gettext("notities"),   "src"=>"txt"),
		"go_email"    => array("alt"=>gettext("email"),      "src"=>"mail_new"),
		"go_todo"     => array("alt"=>gettext("todos"),      "src"=>"clock"),
		"go_rss"      => array("alt"=>gettext("rss"),        "src"=>"filter"),
		"go_alert"    => array("alt"=>gettext("alerts"),     "src"=>"bell"),
		"go_fax"      => array("alt"=>gettext("fax"),        "src"=>"fileprint"),
		"go_support"  => array("alt"=>gettext("support"),    "src"=>"kdmconfig"),
		"go_desktop"  => array("alt"=>gettext("desktop"),    "src"=>"desktop"),
		"go_sales"    => array("alt"=>gettext("sales"),      "src"=>"keditbookmarks"),
		"go_mortgage" => array("alt"=>gettext("mortgage"),   "src"=>"kdmconfig"),

		"enabled"     => array("alt"=>gettext("actief"),		 "src"=>"folder_green_open"),
		"disabled"    => array("alt"=>gettext("nietactief"), "src" => "folder_red"),

		/*actions*/
		"important" 	=> array("alt"=>gettext("belangrijk"),	 "src"=>"flag"),
		"open"      	=> array("alt"=>gettext("open"),				 "src"=>"fileopen"),
		"edit"      	=> array("alt"=>gettext("wijzig"),			 "src"=>"edit"),
		"new"       	=> array("alt"=>gettext("nieuw"),				 "src"=>"wizard"),
		"delete"    	=> array("alt"=>gettext("verwijder"),		 "src"=>"cancel"),
		"view"      	=> array("alt"=>gettext("tonen"),				 "src"=>"folder_open"),
		"ok"        	=> array("alt"=>gettext("ok"),					 "src"=>"ok"),
		"cancel"    	=> array("alt"=>gettext("annuleren"),		 "src"=>"cancel"),
		"save"      	=> array("alt"=>gettext("opslaan"),			 "src"=>"filesave"),
		"save_all"  	=> array("alt"=>gettext("allesopslaan"), "src"=>"save_all"),
		"contents"  	=> array("alt"=>gettext("inhoud"),			 "src"=>"contents"),
		"info"     		=> array("alt"=>gettext("info"),				 "src"=>"rss_tag"),
		"help"      	=> array("alt"=>gettext("help"),				 "src"=>"help"),
		"print"     	=> array("alt"=>gettext("print"),				 "src"=>"printer1"),
		"toggle"    	=> array("alt"=>gettext("wissel"),			 "src"=>"lastmoves"),
		"users"     	=> array("alt"=>gettext("gebruikers"),	 "src"=>"package_settings"),
		"move"      	=> array("alt"=>gettext("laatste"),			 "src"=>"2rightarrow"),

		/*fileoperations*/
		"attachment"     	=>array("alt"=>gettext("attach"),		 			"src"=>"attach"),
		"file_download"  	=>array("alt"=>gettext("download"),	 			"src"=>"kget"),
		"multi_download" 	=>array("alt"=>gettext("downloadmulti"),	"src"=>"vcs_update"),
		"file_add"				=>array("alt"=>gettext("toevoegen"),			"src"=>"vcs_add"),
		"file_delete"			=>array("alt"=>gettext("verwijder"),			"src"=>"editdelete"),
		"file_edit"				=>array("alt"=>gettext("wijzig"),					"src"=>"edit"),
		"file_export"			=>array("alt"=>gettext("export"),					"src"=>"3floppy_mount"),
		"file_zip"				=>array("alt"=>gettext("zip"),						"src"=>"package"),
		"file_upload"			=>array("alt"=>gettext("upload"),					"src"=>"forward"),
		"file_attach"			=>array("alt"=>gettext("attach"),					"src"=>"add"),
		"file_multiple"		=>array("alt"=>gettext("uploadmulti"),		"src"=>"save_all"),

		/*filetypes*/
		"ftype_text"			=>array("alt"=>gettext("txt"), "src"=>"txt"),
		"ftype_html"			=>array("alt"=>gettext("htm"), "src"=>"html"),
		"ftype_doc"				=>array("alt"=>gettext("doc"), "src"=>"wordprocessing"),
		"ftype_calc"			=>array("alt"=>gettext("xls"), "src"=>"spreadsheet"),
		"ftype_binary"		=>array("alt"=>gettext("dat"), "src"=>"unknown"),
		"ftype_pdf"				=>array("alt"=>gettext("pdf"), "src"=>"pdf"),
		"ftype_image"			=>array("alt"=>gettext("afb"), "src"=>"image"),
		"ftype_rfc822"		=>array("alt"=>gettext("rfc"), "src"=>"mail_get"),
		"ftype_sound"			=>array("alt"=>gettext("wav"), "src"=>"sound"),

		/*folderoperations*/
		"folder_up"				=>array("alt"=>gettext("maphoger"),			"src"=>"folder_outbox"),
		"folder_open"			=>array("alt"=>gettext("map"),					"src"=>"folder_open"),
		"folder_closed"		=>array("alt"=>gettext("map"),					"src"=>"folder"),
		"folder_lock"			=>array("alt"=>gettext("rechten"),			"src"=>"folder_locked"),
		"folder_relation"	=>array("alt"=>gettext("relatie"),			"src"=>"folder_green"),
		"folder_project"	=>array("alt"=>gettext("projecten"),		"src"=>"folder_tar"),
		"folder_hrm"			=>array("alt"=>gettext("hrm"),					"src"=>"folder_orange"),
		"folder_my_docs"	=>array("alt"=>gettext("mijndocs"),			"src"=>"folder_home"),
		"folder_denied"		=>array("alt"=>gettext("geentoegang"),	"src"=>"folder_red"),
		"folder_global"		=>array("alt"=>gettext("map"),					"src"=>"folder_grey"),
		"folder_shared"		=>array("alt"=>gettext("gedeeldemap"),	"src"=>"kdisknav"),
		"tree"						=>array("alt"=>gettext("boom"),					"src"=>"tree"),

		/*folderpermissions*/
		"permissions"						=>array("alt"=>gettext("rechten"),					"src"=>"lock"),
		"permissions_read"			=>array("alt"=>gettext("leesrechten"),			"src"=>"folder_orange"),
		"permissions_write"			=>array("alt"=>gettext("schrijfrechten"),		"src"=>"folder_green"),
		"permissions_special"		=>array("alt"=>gettext("specialerechten"),	"src"=>"folder_yellow"),
		"permissions_none"			=>array("alt"=>gettext("geenrechten"),			"src"=>"folder_red"),
		"permissions_add_write"	=>array("alt"=>gettext("toevoegrechten"),		"src"=>"vcs_add"),
		"permissions_add_read"	=>array("alt"=>gettext("leesrechten"),			"src"=>"vcs_remove"),

		/*states*/
		"state_public"			=>array("alt"=>"-",	"src"=>"personal_silver"),
		"state_private"			=>array("alt"=>"-",	"src"=>"personal_red"),
		"state_special"			=>array("alt"=>"-",	"src"=>"personal"),

		/*address*/
		"addressbook"				=>array("alt"=>gettext("adresboek"), "src"=>"toggle_log"),

		/*calendar*/
		"calendar_today"		=>array("alt"=>gettext("kalender"), "src"=>"today"),
		"calendar_reg_hour"	=>array("alt"=>gettext("uren"),			"src"=>"clock"),

		/*mail*/
		"mail_new"					=>array("alt"=>gettext("nieuwemail"),				"src"=>"mail_new"),
		"mail_new_alt"			=>array("alt"=>gettext("nieuwemail"),				"src"=>"mail_post_to"),
		"mail_external"			=>array("alt"=>gettext("mailextern"),				"src"=>"thunderbird"),
		"mail_forward"			=>array("alt"=>gettext("forward"),					"src"=>"mail_send"),
		"mail_send"					=>array("alt"=>gettext("stuur"),						"src"=>"mail_forward"),
		"mail_reply"				=>array("alt"=>gettext("reply"),						"src"=>"mail_reply"),
		"mail_reply_all"		=>array("alt"=>gettext("replyalle"),				"src"=>"mail_replyall"),
		"mail_headers"			=>array("alt"=>gettext("headers"),					"src"=>"mail_find"),
		"mail_copy"					=>array("alt"=>gettext("kopieer"),					"src"=>"mail_new"),
		"mail_move"					=>array("alt"=>gettext("verplaats"),				"src"=>"mail_forward"),
		"mail_signatures"		=>array("alt"=>gettext("signatures"),				"src"=>"personal"),
		"mail_filters"			=>array("alt"=>gettext("filters"),					"src"=>"filter"),
		"mail_templates"		=>array("alt"=>gettext("templates"),				"src"=>"view_sidetree"),
		"mail_tracking"			=>array("alt"=>gettext("tracking"),					"src"=>"viewmag"),
		"mail_readconfirm"	=>array("alt"=>gettext("leesbevestiging"),	"src"=>"pencil"),
		"mail_retrieve"			=>array("alt"=>gettext("forceerophalen"),		"src"=>"legalmoves"),

		/*addressdataicons*/
		"data_birthday"						=>array("alt"=>gettext("verjaardageply"),	"src"=>"wine"),
		"data_name"								=>array("alt"=>gettext("naam"),						"src"=>"personal"),
		"data_private_email"			=>array("alt"=>gettext("priveemail"),			"src"=>"data_mail_private"),
		"data_private_telephone"	=>array("alt"=>gettext("privetel"),				"src"=>"data_telephone_private"),
		"data_private_cellphone"	=>array("alt"=>gettext("privemobiel"),		"src"=>"data_cellphone_private"),
		"data_phone_rining"				=>array("alt"=>gettext("voip"),						"src"=>"data_telephone_ringing"),

		"data_business_email"			=>array("alt"=>gettext("zakelijkemail"),	"src"=>"data_mail_business"),
		"data_business_telephone"	=>array("alt"=>gettext("zakelijktel"),		"src"=>"data_telephone_business"),
		"data_business_cellphone"	=>array("alt"=>gettext("zakelijkmobiel"),	"src"=>"data_cellphone_business"),

		/*note*/
		"note"										=>array("alt"=>gettext("notitie"),				"src"=>"txt"),

		/*performancestats*/
		"performance_webserver"		=>array("alt"=>"-",	"src"=>"fork"),
		"performance_dbserver"		=>array("alt"=>"-",	"src"=>"vcs_commit"),
		"performance_total"				=>array("alt"=>"-",	"src"=>"xclock"),
		"performance_show"				=>array("alt"=>"-",	"src"=>"xload"),
		"performance_client"			=>array("alt"=>"-",	"src"=>"exec"),

		/*project*/
		"activity"								=>array("alt"=>"-",	"src"=>"xedit"),
		"default"									=>array("alt"=>"?", "src"=>"help_index")
	);

	if ($avail_actions[$action]) {
		$img = $avail_actions[$action]["src"];
		$alt = $avail_actions[$action]["alt"];
	} else {
		$img = $avail_actions["default"]["src"];
		$alt = $avail_actions["default"]["alt"];
	}

	$img = sprintf("icons/%s.png", $img);
	if (!is_array($link)) {
		$link = array(
			"href" => $link
		);
	}
	$link["class"] = "action";

	if ($action)
		$this->insertImage($img, $alt, $link, 1, "", $id, $str);

?>
