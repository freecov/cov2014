<?php
Class Cms_output {
	/* constants */
	const include_dir = "classes/cms/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "cms";

	public $buffer = '';
	private $current_root;


	public function cmsSitemap() {

		$cms_data = new Cms_data();
		$cms_data->decodeOptions($_REQUEST);
		$cms_license = $cms_data->getCmsSettings();

		$cms = $_REQUEST["cms"];

		$output = new Layout_output();
		$output->layout_page("CMS");
		$output->load_javascript(self::include_dir."script_fp.js");
		$output->load_javascript(self::include_dir."script_cms.js");

		$output->addTag("div", array("id"=>"cms_icon_paste", "style"=>"display:none"));
			$output->insertAction("paste", gettext("paste"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_new", "style"=>"display:none"));
			$output->insertAction("new", gettext("new page"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_info", "style"=>"display:none"));
			$output->insertAction("info", gettext("show options legend"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_delete", "style"=>"display:none"));
			$output->insertAction("cancel", gettext("alter this page"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_copy", "style"=>"display:none"));
			$output->insertAction("copy", gettext("copy page structure"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_important", "style"=>"display:none"));
			$output->insertAction("important", gettext("this page contains an autosave value"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_paste", "style"=>"display:none"));
			$output->insertAction("paste", gettext("paste selection under this page"), "");
		$output->endTag("div");

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("Sitemap")
		));

		/* menu items */
		$menuitems[sprintf("- %s -", gettext("choose"))] = "void(0);";
		$menuitems[gettext("cms settings")] = "popup('?mod=cms&action=editCmsSettings', 'settings', 640, 480, 1);";
		$menuitems[gettext("site templates")] = "popup('?mod=cms&action=siteTemplates', 'settings', 970, 620, 1);";
		if ($cms_license["cms_permissions"])
			$menuitems[gettext("cms account manager")] = "popup('?mod=cms&action=editAccountsList', 'settings', 640, 480, 1);";

		if ($cms_license["cms_meta"])
			$menuitems[gettext("metadata definitions")] = "popup('?mod=cms&action=metadataDefinitions', 'settings', 640, 480, 1);";

		$menuitems[gettext("file management")] = "popup('index.php?mod=cms&action=filesys');";
		if ($cms_license["cms_linkchecker"])
			$menuitems[gettext("linkchecker")] = "popup('?mod=cms&action=linkchecker', 'settings', 840, 480, 1);";

		$menuitems[gettext("site information")] = "popup('?mod=cms&action=editSiteInfo', 'settings', 640, 480, 1);";
		if ($cms_license["cms_banners"])
			$menuitems[gettext("manage banners")] = "";

		if ($cms_license["cms_changelog"] || $cms_license["cms_versioncontrol"])
			$menuitems[gettext("changes overview")] = "";

		$menuitems[gettext("abbreviation management")] = "";
		$menuitems[gettext("online help")] = "";
		$menuitems[gettext("legenda")] = "";

		foreach ($menuitems as $k=>$v) {
			unset($menuitems[$k]);
			$menuitems[$v] = $k;
		}

		$venster->addVensterData();

			/* version control / inklappen / uitklappen / reload / batch options */
			$tbl = new Layout_table(array(
				"style" => "width: 100%;"
			));
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addCode(gettext("Provide pagenumber or searchkey an hit search").": ");
					$tbl->addTextField("cms[search]", $cms["search"]);
					$tbl->insertAction("edit", gettext("open page"), "javascript: cmsSearchPage();");
					$tbl->insertAction("forward", gettext("search"), "javascript: cmsSearch();");
				$tbl->endTableData();
				$tbl->addTableData(array("align" => "right"));
					$tbl->addCode(gettext("CMS options").": ");
					$tbl->addSelectField("menuitems", $menuitems, "void(0);");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addTag("br");
					$tbl->insertLink(gettext("expand all"), array(
						"href" => "javascript: cmsExpand(-1);"
					));
					$tbl->insertAction("down", gettext("collapse all"), "javascript: cmsExpand(-1);");
					$tbl->addCode(" | ");
					$tbl->insertLink(gettext("collapse all"), array(
						"href" => "javascript: cmsCollapse(-1);"
					));
					$tbl->insertAction("up", gettext("uncollapse all"), "javascript: cmsCollapse(-1);");
					$tbl->addCode(" | ");
					$tbl->insertLink(gettext("reload sitemap"), array(
						"href" => "javascript: cmsReload();"
					));
					$tbl->insertAction("reload", gettext("reload sitemap"), "javascript: cmsReload();");
					$tbl->addSpace(2);

					$sel[gettext("cms roots")]["R"]  = gettext("sitemap root");
					$sel[gettext("cms roots")]["D"]  = gettext("deleted items");
					$sel[gettext("cms roots")]["X"]  = gettext("protected items");
					$sel[gettext("user roots")] = $cms_data->getUserSitemapRoots();
					$tbl->addSelectField("siteroot", $sel, $cms_data->opts["siteroot"]);
					$tbl->insertAction("file_attach", gettext("add new site root"), "javascript: addSiteRoot();");

					if (!in_array($cms_data->opts["siteroot"], array("X", "D"))) {
						$state = $cms_data->getSiteRootPublicState($cms_data->opts["siteroot"]);
						if ($state == 1)
							$tbl->insertAction("enabled", gettext("siteroot is non-public"), "javascript: popup('?mod=cms&action=editSiteInfo&siteroot=".$cms_data->opts["siteroot"]."');");
						else
							$tbl->insertAction("disabled", gettext("siteroot is public public"), "javascript: popup('?mod=cms&action=editSiteInfo&siteroot=".$cms_data->opts["siteroot"]."');");
					}
					$tbl->start_javascript();
					$tbl->addCode("checkSiteRootChange();");
					$tbl->end_javascript();

				$tbl->endTableData();
				$tbl->addTableData(array(
					"valign" => "bottom",
					"align"  => "right"
				));
					if (count($cms_data->opts["buffer"])==0) {
						$tbl->insertTag("a", "plaats geselecteerde items in buffer", array(
							"href" => "javascript: fillBuffer();"
						));
						$tbl->insertAction("forward", gettext("copy selected items to buffer"), "javascript: fillBuffer();");
					} else {
						$tbl->addCode(gettext("buffer actions").": ");
						$sel = array(
							" "       => gettext("choose an action"),
							gettext("page properties") => array(
								"bufferActive"      => gettext("activate selection")." (+A)",
								"bufferActiveDis"   => gettext("deactivate selection")." (-A)",
								"bufferPublic"      => gettext("make selection public")." (+P)",
								"bufferPublicDis"   => gettext("make selection private")." (-P)",
								"bufferMenuitem"    => gettext("set menuitem on selection")." (+M)",
								"bufferMenuitemDis" => gettext("remove menuitem from selection")." (-M)",
							),
							gettext("page actions") => array(
								"erasePermissions"  => gettext("remove authorizations from selection"),
								"deletebuffer"      => gettext("remove all pages in buffer"),
							),
							gettext("buffer actions") => array(
								"erasebuffer"       => gettext("undo selection")
							)
						);
						$tbl->addSelectField("cms[buffer]", $sel, "");
						$tbl->start_javascript();
							$tbl->addCode("
								document.getElementById('cmsbuffer').onchange = function() {
									exec_buffer();
								}
							");
						$tbl->end_javascript();
					}
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
			$venster->addTag("br");
			unset($tbl);


			$tbl = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1,
				"style" => "background-color: #999;"
			));
			$tbl->addTableRow(array("style" => "background-color: #ddd;"));
				$tbl->insertTableHeader(gettext("level"));
				$tbl->insertTableHeader(gettext("page"));
				$tbl->insertTableHeader("#");
				$tbl->addTableHeader(array("colspan" => 2, "align" => "right"));
					$tbl->addTag("nobr");
						$tbl->insertAction("view_all", gettext("toggle extra icons"), "javascript: toggle_cms_table();");
					$tbl->endTag("nobr");
				$tbl->endTableHeader();
				$tbl->insertTableHeader(gettext("actions"));
			$tbl->endTableRow();
			/* sitemap generation code here */
			$tbl->addCode( $this->generateSpecialPage($cms_data->opts["siteroot"], $cms_data) );

			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
			unset($tbl);



		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "cms");
		$output->addHiddenField("cmd", "");
		$output->addHiddenField("id", "");
		$output->addHiddenField("options_state", $_REQUEST["options_state"]);
		$output->addHiddenField("jump_to_anchor", "");
		$output->addCode( $venster->generate_output() );

		$cms_data->saveOptions();
		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();

	}

	public function cmsEditor($text_only=0) {
		require(self::include_dir."cmsEditor.php");
	}

	public function generateSpecialPage($special, &$cms_data) {
		if (is_numeric($special)) {
			$q = "select id from cms_data where id = ".(int)$special;
		} else {
			$q = "select id from cms_data where isSpecial = '".$special."' and parentPage = 0";
		}
		$res = sql_query($q) or die($q);
		$id = sql_result($res,0);

		/* set current (special) root */
		$this->current_root = $id;

		$this->generateSiteMap($id, 0, 1, $cms_data, $restricted);
		return $this->buffer;
	}

	public function generateSitemap($id, $level, $special=0, &$cms_data) {
		require(self::include_dir."generateSitemap.php");
	}

	// function to generate one javascript trigger (record)
	public function genFp($opts) {
		$this->buffer .= "<script>fp(".implode(",",$opts).");</script>\n";
	}

	public function viewRestorePoint($id) {
		$cms_data = new Cms_data();
		$data = $cms_data->getPageById($id);
		echo "<html><body bgcolor='white'>";
		echo $data["autosave_data"];
		echo "</body></html>";
		exit();
	}

	public function addMenuItems($venster) {
		$cms_data = new Cms_data();
		$data = $cms_data->getPageById($_REQUEST["id"]);

		$venster->addMenuItem(gettext("show on website"), "javascript: popup('http://".$_SERVER["HTTP_HOST"]."/page/".$_REQUEST["id"].".htm');");
		$venster->addMenuItem(gettext("design"), "?mod=cms&action=editpage&noredir=1&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("textmode"), "?mod=cms&action=editpagetext&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("file management"), "javascript: popup('index.php?mod=cms&action=filesys');");
		$venster->addMenuItem(gettext("page properties"), "?mod=cms&action=editSettings&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("authorization"), "?mod=cms&action=authorisations&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("date options"), "?mod=cms&action=dateoptions&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("meta data"), "?mod=cms&action=metadata&id=".$_REQUEST["id"]);
		if ($data["isList"])
			$venster->addMenuItem(gettext("list generator"), "?mod=cms&action=cmslist&id=".$_REQUEST["id"]);
		if ($data["isGallery"])
		$venster->addMenuItem(gettext("photo gallery"), "?mod=cms&action=cmsgallery&id=".$_REQUEST["id"]);
		if ($data["isForm"]) {
			$venster->addMenuItem(gettext("form generator"), "?mod=cms&action=cmsform&id=".$_REQUEST["id"]."&pageid=".$_REQUEST["id"]);
			$venster->addMenuItem(gettext("form results"), "?mod=cms&action=cmsformresults&id=".$_REQUEST["id"]."&pageid=".$_REQUEST["id"]);
		}
	}

	public function cmsPageSettings() {
		require(self::include_dir."cmsPageSettings.php");
	}

	public function editCmsSettings() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("CMS settings")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getCmsSettings();

		$venster->addVensterData();
			$tbl = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan" => 4), "header");
					$tbl->insertAction("view_all", "", "");
					$tbl->addSpace();
					$tbl->addCode(gettext("CMS modules"));
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->insertAction("save", gettext("save"), "javascript: saveSettings();");
					$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
				$tbl->endTableData();
			$tbl->endTableRow();
			/* modules */
			$modules =& $cms_data->modules;
			$tbl->addTableRow();
			foreach ($modules as $k=>$v) {
				$i++;
				if ($i > 2) {
					$tbl->endTableRow();
					$tbl->addTableRow();
					$i=1;
				}
				$tbl->insertTableData($v, "", "header");
				$tbl->addTableData("", "data");
					$tbl->insertCheckBox("cms[$k]", 1, ($cms[$k]) ? 1:0);
				$tbl->endTableData();
			}
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());

		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "cms");
		$output->addHiddenField("action", "saveCmsSettings");

		$output->addCode($venster->generate_output());
		$output->endTag("form");
		$output->load_javascript(self::include_dir."script_cms.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function editSiteInfo($siteroot="") {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("CMS site settings")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getCmsSettings($siteroot);
		$cms["search_language"] = explode(",", $cms["search_language"]);

		if ($_REQUEST["custom"]) {
			$custom = $cms_data->getPageById($_REQUEST["custom"]);
		}

		$venster->addVensterData();
			$tbl = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan" => 4), "header");
					$tbl->insertAction("view_all", "", "");
					$tbl->addSpace();
					$tbl->addCode(gettext("CMS site information"));
					if ($_REQUEST["custom"]) {
						$tbl->addCode(sprintf(" (%s: %s)", gettext("site root"), $custom["pageTitle"]));
					}
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->insertAction("save", gettext("save"), "javascript: saveSettings();");
					$tbl->insertAction("close", gettext("close"), "javascript: window.close();");
				$tbl->endTableData();
			$tbl->endTableRow();

			/* cms title */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("website title"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[cms_name]", $cms["cms_name"], array(
						"style" => "width: 350px;"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* keywords */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("keywords"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextArea("cms[search_fields]", $cms["search_fields"], array(
						"style" => "width: 350px; height: 100px;"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* description */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("description"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextArea("cms[search_descr]", $cms["search_descr"], array(
						"style" => "width: 350px; height: 100px;"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* author */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("author"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[search_author]", $cms["search_author"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* copyright */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("copyright"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[search_copyright]", $cms["search_copyright"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* email */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("webmaster email"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[search_email]", $cms["search_email"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* use pagetile in title bar */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("add page title to browser title"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addCheckBox("cms[search_use_pagetitle]", 1, $cms["search_use_pagetitle"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* siteroot is public */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("this siteroot is global public"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addCheckBox("cms[isPublic]", 1, $cms_data->getSiteRootPublicState($siteroot));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* some basic info */
			$tbl->addTableRow();
				$tbl->insertTableData("standaard pagina id", "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[cms_defaultpage]", $cms["cms_defaultpage"], array("style" => "width: 60px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->insertTableData("google verify", "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextField("cms[google_verify]", $cms["google_verify"], array("style" => "width: 300px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->insertTableData("cms hostnames", "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$tbl->addTextArea("cms[cms_hostnames]", $cms["cms_hostnames"], array("style" => "width: 300px; height: 80px;"));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* languages */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("site language"), "", "header");
				$tbl->addTableData(array("colspan"=>3), "data");
					$sel = $cms_data->lang;
					foreach ($sel as $k=>$v) {
						$tbl->insertCheckBox("cms[search_language][$k]", $k, (in_array($k, $cms["search_language"])) ? 1:0);
						$tbl->addSpace();
						$tbl->addCode($v." (".$k.")");
						$tbl->addTag("br");
					}
				$tbl->endTableData();
			$tbl->endTableRow();


			$tbl->endTable();
			$venster->addCode($tbl->generate_output());

		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "cms");
		$output->addHiddenField("action", "saveSiteInfo");
		$output->addHiddenField("siteroot", $siteroot);

		$output->addCode($venster->generate_output());
		$output->endTag("form");
		$output->load_javascript(self::include_dir."script_cms.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function editAccountsList() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("external accounts")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getAccountList();

		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("username"), "%username" );
		$view->addMapping( gettext("active"), "%is_enabled_h" );
		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("?mod=cms&action=editAccount&id=", "%id", "&user_id=", $user_id)
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteAccount&id=", "%id", "&user_id=", $user_id, "';")
			)
		));
		$venster->addCode($view->generate_output());

		$venster->insertAction("new", gettext("new user"), "?mod=cms&action=editAccount");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
		$venster->endVensterData();

		$output->addCode($venster->generate_output());

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function editAccount($id) {
		require(self::include_dir."editAccount.php");
	}

	public function editAuthorisations($id) {
		require(self::include_dir."editAuthorisations.php");
	}
	public function dateOptions($id) {
		require(self::include_dir."dateOptions.php");
	}
	public function siteTemplates() {
		require(self::include_dir."siteTemplates.php");
	}
	public function editTemplate($id) {
		require(self::include_dir."editTemplate.php");
	}
	public function highlight_init() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);
			$output->addTag("form", array(
				"id" => "velden",
				"method" => "post",
				"action" => "index.php"
			));
			$output->addHiddenField("mod", "cms");
			$output->addHiddenField("action", "highlight_show");
			$output->addTextArea("contents", array(
				"style" => "display: none",
				"wrap"  => "off",
				"width" => "800",
				"height" => "10"
			));
			$output->start_javascript();
				$output->addCode("
					document.getElementById('contents').value = opener.document.getElementById('cmsdata').value;
					document.getElementById('velden').submit();
			");
			$output->end_javascript();

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	public function highlight_show() {
		$output = new Layout_output();
		$output->addTag("html");
		$output->addTag("body");

		$output->load_javascript("edit_area/edit_area_gzip.php", 1);
		$output->start_javascript();
		$output->addCode("
        // initialisation
        editArea.initArea({
                id: \"src\"       // id of the textarea to transform
                ,debug: false
                ,line_selection: false
                ,font_size: \"8\"      // not for IE
                ,do_highlight: true     // if start with highlight
                //,begin_toolbar: \"save, |\"     // or end_toolbar
                ,toolbar: \"save, |, search, go_to_line, |, undo, redo, |, select_font, change_line_selection, highlight, reset_highlight, |, help\"
                ,load_callback: \"my_load\"
                ,save_callback: \"my_save\"
                , allow_resize: \"both\"
                , allow_toogle: false
                //, language: \"fr\"
                , code_lang: \"php\"
        });

        function my_save(content){
        	opener.document.getElementById('cmsdata').value = content;
        	opener.saveSettings();
        }

        function my_load(elem){
                elem.value=\"The content is loaded from the load_callback function into EditArea\";
        }
		");
		$output->end_javascript();
		$output->addTextArea("src", stripslashes($_REQUEST["contents"]), array(
			"width"  => 860,
			"height" => 580
		));

		$output->endTag("body");
		$output->endTag("html");
		$output->exit_buffer();
	}

	public function dateOptionsItemEdit($id) {
		require(self::include_dir."dateOptionsItemEdit.php");
	}

	public function metadataDefinitions() {
		require(self::include_dir."metadataDefinitions.php");
	}
	public function metadataDefinitionsEdit() {
		require(self::include_dir."metadataDefinitionsEdit.php");
	}

	public function metadata($id) {
		require(self::include_dir."metadata.php");
	}

	private function switchFieldType(&$tbl, &$v) {
		$meta = "meta";

		switch ($v["field_type"]) {
			case "text":
				if ($output_only) {
					$tbl->addCode($v["value"]);
				} else {
					$tbl->addTextField(sprintf($meta."[%s]", $v["id"]), $v["value"]);
				}
				break;
			case "textarea":
				if ($output_only) {
					$tbl->addCode($v["value"]);
				} else {
					$tbl->addTextArea(sprintf($meta."[%s]", $v["id"]), $v["value"], array(
						"style" => "width: 300px; height: 80px;"
					));
				}
				break;
			case "datetime":
				$days[0] = "-";
				for ($i=1;$i<=31;$i++) {
					$days[$i] = $i;
				}
				$months[0] = "-";
				for ($i=1;$i<=12;$i++) {
					$months[$i] = $i;
				}
				$year[0] = "-";
				for ($i=1990;$i!=date("Y")+5;$i++) {
					$year[$i] = $i;
				}
				if ($output_only) {
					if ($v["value"] > 0) {
						$tbl->addCode(date("d-m-Y", $v["value"]));
					} else {
						$tbl->addCode("--");
					}
				} else {
					if ($v["value"] > 0) {
						$tbl->addSelectField(sprintf($meta."[%s_day]", $v["id"]), $days, date("d", $v["value"]));
						$tbl->addSelectField(sprintf($meta."[%s_month]", $v["id"]), $months, date("m", $v["value"]));
						$tbl->addSelectField(sprintf($meta."[%s_year]", $v["id"]), $year, date("Y", $v["value"]));
					} else {
						$tbl->addSelectField(sprintf($meta."[%s_day]", $v["id"]), $days, 0);
						$tbl->addSelectField(sprintf($meta."[%s_month]", $v["id"]), $months, 0);
						$tbl->addSelectField(sprintf($meta."[%s_year]", $v["id"]), $year, 0);
					}
				}
				break;
			case "select":
				$sel = array();
				$v["default_value"] = explode("\n", $v["default_value"]);
				foreach ($v["default_value"] as $z) {
					$z = trim($z);
					$sel[$z] = $z;
				}
				if ($output_only) {
					$tbl->addCode($sel[$v["value"]]);
				} else {
					$tbl->addSelectField(sprintf($meta."[%s]", $v["id"]), $sel, $v["value"]);
				}
				break;
			case "checkbox":
				if ($output_only) {
					$tbl->addCode(nl2br($v["value"]));
				} else {

					$sel = array();
					$values = explode("\n", $v["value"]);
					$v["default_value"] = explode("\n", $v["default_value"]);
					foreach ($v["default_value"] as $z) {
						if (in_array(trim($z), $values)) {
							$checked = 1;
						} else {
							$checked = 0;
						}
						$tbl->addCheckBox(sprintf($meta."[%s][%s]", $v["id"], $z), $z, $checked);
						$tbl->addCode($z);
						$tbl->addTag("br");
					}
				}
				break;
		}

	}
	public function cmsList($id) {
		require(self::include_dir."cmsList.php");
	}

	public function cmsForm($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("form generator")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getFormData($id);

		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);
		$view->addMapping(gettext("order"), "%order");
		$view->addMapping(gettext("name"), "%field_name");
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping(gettext("type"), "%field_type_h");
		$view->addMapping(gettext("value"), "%field_value");
		$view->addMapping(gettext("properties"), "%options");
		$view->addMapping("", "%%complex_actions");

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("javascript: popup('?mod=cms&action=cmsformedit&pageid=", "%pageid", "&id=", "%id", "&user_id=", $user_id, "');")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=cmsformdelete&id=", "%pageid", "&itemid=", "%id", "';")
			)
		));

		$venster->addCode($view->generate_output());
		$venster->insertAction("new", gettext("add new field"), "javascript: popup('?mod=cms&action=cmsformedit&pageid=".$_REQUEST["id"]."', 'forms', 650, 580, 1);");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");

		$venster->addTag("br");
		$venster->addTag("br");

		$mode = $cms_data->getFormMode($id);

		$venster->addTag("form", array(
			"id" => "formmode",
			"action" => "index.php"
		));
		$venster->addHiddenField("mod", "cms");
		$venster->addHiddenField("action", "saveFormMode");
		$venster->addHiddenField("id", $id);
		$sel = array(
			0 => gettext("send with mail"),
			1 => gettext("store to database and mail"),
			2 => gettext("use as enquete or poll (no mail)")
		);
		$venster->addCode(gettext("Select mode for this form").": ");
		$venster->addSelectField("cms[mode]", $sel, $mode);
		$venster->insertAction("forward", gettext("selecteer"), "javascript: document.getElementById('formmode').submit();");

		$venster->endTag("form");
		$venster->addTag("br");
		$venster->addTag("br");
		$err = $cms_data->getFormErrors($id);
		$err = implode("<br>", $err);
		$venster->insertTag("b", $err);


		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();
	}

	public function cmsFormResults($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("form results")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getFormResults($id);

		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms["data"]);

		#$view->addMapping(gettext("date start"), "%datetime_start");
		#$view->addMapping(gettext("date end"), "%datetime_end");
		#$view->addMapping(gettext("ipaddress"), "%ip_address");
		$view->addSubMapping(array(
			gettext("date")." ", "%datetime_start", " ".gettext("till")." ", "%datetime_end", " (ip: ", "%ip_address", ")"
		), 1);

		foreach ($cms["fields"] as $k=>$v) {
			$view->addMapping($v, "%field_".$v);
		}
		$view->addMapping("", "%%complex_actions");

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=cmsresultdelete&id=", $id, "&itemid=", "%id", "';")
			)
		));

		$venster->addCode($view->generate_output());
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
		$venster->addTag("br");

		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();
	}

	public function cmsGallery($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("photo gallery")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getGalleryData($id);

		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);
		$view->addMapping(gettext("order"), "%order");
		$view->addMapping(gettext("change"), "%%complex_order");
		$view->addMapping(gettext("name"), "%file_short");
		$view->addMapping(gettext("omschrijving"), "%description");
		$view->addMapping("", "%%complex_actions", "", "nowrap");

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("javascript: popup('?mod=cms&action=cmsgalleryitemedit&id=", "%pageid", "&itemid=", "%id", "', 'gallery', 640, 400, 1);")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Are you sure you want to delete this entry?'))) document.location.href='index.php?mod=cms&action=cmsgalleryitemdelete&item=", "%id" ,"&id=", "%pageid", "&itemid=", "%id", "';")
			)
		));
		$view->defineComplexMapping("complex_order", array(
			array(
				"type" => "action",
				"src"  => "up",
				"alt"  => gettext("up"),
				"link" => array("?mod=cms&action=galleryitemswitch&direction=up&id=", "%pageid", "&itemid=", "%id")
			),
			array(
				"type" => "action",
				"src"  => "down",
				"alt"  => gettext("down"),
				"link" => array("?mod=cms&action=galleryitemswitch&direction=down&id=", "%pageid", "&itemid=", "%id")
			),
		));

		if (count($cms) >= 6) {
			$limit_height = "height: 250px; overflow:auto;";
		} else {
			$limit_height = "";
		}

		$venster->addTag("div", array(
			"class"  => "limit_height",
			"style" => $limit_height
		));
			$venster->addCode($view->generate_output());
		$venster->endTag("div");

		$fs_output = new Filesys_output();
		$venster->addTag("hr");
		$venster->addTag("form", array(
			"id"     => "file_upload",
			"method" => "post",
			"action" => "index.php",
			"enctype" => "multipart/form-data"
		));
		$venster->addCode($fs_output->show_fileupload()->generate_output());
		$venster->endTag("form");
		$venster->start_javascript();
			$venster->addCode("
				function filesys_upload_files() {
					document.getElementById('file_upload').submit();
				}
			");
		$venster->end_javascript();

		#$venster->insertAction("new", gettext("add new field"), "javascript: popup('https://covide.atreides.aol/index.php?mod=filesys&action=opendir&subaction=cmsgallery', 'forms', 650, 580, 1);");
		#$venster->insertAction("close", gettext("close"), "javascript: window.close();");

		$venster->addTag("br");
		$venster->addTag("br");

		$cms = $cms_data->getGallerySettings($id);

		$table = new Layout_table(array(
			"cellspacing" => 1,
			"cellpadding" => 1
		));
		/* layout type */
		$table->addTableRow();
			$table->insertTableData(gettext("Layout"), "", "header");
			$table->addTableData("", "data");
				$sel = array(
					0 => gettext("table"),
					1 => gettext("slideshow"),
					//2 => gettext("slideshow (horizontal)"),
					3 => gettext("listing")
				);
				$table->addSelectField("cms[gallerytype]", $sel, $cms["gallerytype"]);
			$table->endTableData();
		$table->endTableRow();
		/* cols */
		$table->addTableRow(array(
			"id" => "layer_cols"
		));
			$table->addTableData("", "header");
				$table->addCode(gettext("Number of columns"));
				$table->addTag("br");
				$table->addSpace();
				$table->addCode(gettext("(if table layout)"));
			$table->endTableData();
			$table->addTableData("", "data");
				$sel = array();
				for ($i=1;$i<=10;$i++) {
					$sel[$i]=$i;
				}
				$table->addSelectField("cms[cols]", $sel, $cms["cols"]);
			$table->endTableData();
		$table->endTableRow();
		/* cols */
		$table->addTableRow(array(
			"id" => "layer_rows"
		));
			$table->addTableData("", "header");
				$table->addCode(gettext("Number of rows per page"));
				$table->addTag("br");
				$table->addSpace();
				$table->addCode(gettext("(if table or list layout)"));
			$table->endTableData();
			$table->addTableData("", "data");
				$sel = array();
				for ($i=1;$i<=10;$i++) {
					$sel[$i]=$i;
				}
				$table->addSelectField("cms[rows]", $sel, $cms["rows"]);
			$table->endTableData();
		$table->endTableRow();
		/* allow max size */
		$table->addTableRow();
			$table->insertTableData(gettext("Allow viewing full size"), "", "header");
			$table->addTableData("", "data");
				$sel = array(
					0 => gettext("no"),
					1 => gettext("yes")
				);
				$table->addSelectField("cms[fullsize]", $sel, $cms["fullsize"]);
			$table->endTableData();
		$table->endTableRow();
		/* thumbnail size medium */
		$table->addTableRow();
			$table->insertTableData(gettext("Thumbnail size (medium)"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("cms[bigsize]", $cms["bigsize"]);
			$table->endTableData();
		$table->endTableRow();
		/* thumbnail size small */
		$table->addTableRow();
			$table->insertTableData(gettext("Thumbnail size (small)"), "", "header");
			$table->addTableData("", "data");
				$table->addTextField("cms[thumbsize]", $cms["thumbsize"]);
			$table->endTableData();
		$table->endTableRow();

		$div = new Layout_output();
		$div->addTag("img", array(
			"src" => "img/bar.png"
		));
		$table->addTableRow(array(
			"id" => "layer_busy",
			"style" => "xdisplay: none;"
		));
			$table->addTableData("", "data");
				$table->insertTag("marquee", $div->generate_output(), array(
					"id"           => "marquee_progressbar",
					"behavoir"     => "scroll",
					"style"        => "width: 300px; background-color: #f1f1f1; height: 10px; border: 1px solid #b0b2c6; visibility:hidden; margin-top: 10px;",
					"scrollamount" => 3,
					"direction"    => "right",
					"scrolldelay"  => 60
				));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow(array(
			"id" => "layer_actions"
		));
			$table->addTableData("", "data");
				$table->insertAction("save", gettext("save"), "javascript: saveSettingsGallery();");
				$table->insertAction("close", gettext("close"), "javascript: window.close();");
			$table->endTableData();
		$table->endTableRow();

		$table->endTable();
		$venster->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$venster->addHiddenField("mod", "cms");
		$venster->addHiddenField("action", "saveGallerySettings");
		$venster->addHiddenField("id", $id);
		$venster->addCode($table->generate_output());
		$venster->endTag("form");


		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."script_cms.js");
		$output->start_javascript();
			$output->addCode("
				function updateLayerCols() {
					document.getElementById('layer_cols').style.visibility = 'hidden';
					document.getElementById('layer_rows').style.visibility = 'hidden';
					if (document.getElementById('cmsgallerytype').value == 0) {
						document.getElementById('layer_cols').style.visibility = 'visible';
						document.getElementById('layer_rows').style.visibility = 'visible';
					}
					if (document.getElementById('cmsgallerytype').value == 3) {
						document.getElementById('layer_cols').style.visibility = 'visible';
					}
				}
				document.getElementById('cmsgallerytype').onchange = function() {
					updateLayerCols();
				}
				addLoadEvent(updateLayerCols());
			");
		$output->end_javascript();

		/* add a container for multiple downloads */
		$output->insertTag("div", "", array(
			"id"    => "download_container",
			"style" => "display: none; width: 0px; height: 0px;"
		));

		$output->exit_buffer();
	}

	public function cmsFormEdit($pageid, $id) {
		require(self::include_dir."cmsFormEdit.php");
	}

	public function preparePageDelete($pageid) {
		require(self::include_dir."deletePageConfirm.php");
	}

	public function cmsGalleryItemEdit($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("alter gallery item")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getGalleryItem($id);

		$venster->addVensterData();

			$tbl = new Layout_table(array(
				"cellspacing" => 1
			));
			/* order */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("order"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->addTextField("cms[order]", $cms["order"], array("style" => "width: 50px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* field name */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("name"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->addTextField("cms[file]", $cms["file"], array("style" => "width: 250px"));
				$tbl->endTableData();
			$tbl->endTableRow();
			/* default value */
			$tbl->addTableRow();
				$tbl->insertTableData(gettext("description"), "", "header");
				$tbl->addTableData("", "data");
					$tbl->addTextArea("cms[description]", $cms["description"], array(
						"style" => "width: 300px; height: 150px;"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode( $tbl->generate_output() );

			$venster->insertAction("back", gettext("back"), "javascript: window.close();");
			$venster->insertAction("save", gettext("new item"), "javascript: saveSettings();");

		$venster->endVensterData();

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "post",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", "cms");
		$output->addHiddenField("action", "saveGalleryItem");
		$output->addHiddenField("id", $id);

		$output->addCode($venster->generate_output());
		$output->endTag("form");

		$output->load_javascript(self::include_dir."script_cms.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function linkchecker() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("CMS linkchecker")
		));
		$cms_data = new Cms_data();

		$div = new Layout_output();
		$div->addTag("img", array(
			"src" => "img/bar.png"
		));
		$venster->addVensterData();

			$tbl = new Layout_table();
			$tbl->addTableRow();
				$tbl->addTableData();
					if ($cms_data->checkLinkcheckerStatus()) {
						$tbl->addCode(gettext("Linkchecker is running")."...");
						$tbl->insertTag("marquee", $div->generate_output(), array(
							"id"           => "marquee_progressbar",
							"behavoir"     => "scroll",
							"style"        => "width: 300px; background-color: #f1f1f1; height: 10px; border: 1px solid #b0b2c6; margin-top: 10px;",
							"scrollamount" => 3,
							"direction"    => "right",
							"scrolldelay"  => 60
						));
						$tbl->start_javascript();
							$tbl->addCode("
								setTimeout('location.href = location.href;', 10000);
							");
						$tbl->end_javascript();

					} else {
						$tbl->insertTag("a", gettext("Start linkchecker"), array(
							"href" => "javascript: startLinkChecker();"
						));
						$tbl->insertAction("ok", gettext("Start linkchecker"), "javascript: startLinkChecker();");
						$tbl->addSpace();
							$lr = $cms_data->lastLinkchecker();
							$tbl->addCode(sprintf("%s: %s", gettext("last run"), $lr));
						$tbl->addSpace();
					}
					$tbl->addTag("br");
					$tbl->addTag("br");
					$tbl->insertTag("b", gettext("The following errors are found").":");

				$tbl->endTableData();
				$tbl->addTableData(array(
					"valign" => "top"
				));
					$tbl->insertAction("close", gettext("Close window"), "javascript: window.close();");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData();

					$lc = $cms_data->checkLinkcheckerResults();
					$view = new Layout_view();
					$view->addData($lc);

					$view->addMapping( gettext("page"), "%pageid" );
					$view->addMapping( gettext("name"), "%name" );
					$view->addMapping( gettext("result"), "%result" );

					$view->addMapping( " ", "%%complex_actions" );
					$view->addSubMapping("%url", 0);

					$view->defineComplexMapping("complex_actions", array(
						array(
							"type" => "action",
							"src"  => "edit",
							"alt"  => gettext("edit"),
							"link" => array("javascript: opener.cmsEdit('cmsEditor','", "%pageid", "','');")
						)
					));
					$tbl->addCode($view->generate_output());

				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->endTable();

			$venster->addCode($tbl->generate_output());
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."script_cms.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function showOptionsInfo($id) {
		$cms_data = new Cms_data();
		$data = $cms_data->getPageById($id);

		$table = new Layout_table(array(
			"cellspacing" => 1, "cellpadding" => 1
		));
		$table->addTableRow();
			$table->addTableData(array("colspan" => 2), "header");
				$table->addCode(gettext("details of pageid")." [".$id."]");
				$table->addSpace();
				$table->insertAction("view_all", gettext("view page"), sprintf(
					"javascript: popup('http://%s/page/%s.htm');", $_SERVER["HTTP_HOST"], $data["pageAlias"]));
				$table->insertAction("edit", gettext("view page"), sprintf(
					"javascript:cmsEdit('cmsEditor','%d','');", $id));

			$table->endTableData();
		$table->endTableRow();
		/* page title */
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("page title"));
			$table->endTableData();
			$table->addTableData();
				$table->addCode($data["pageTitle"]);
			$table->endTableData();
		$table->endTableRow();
		/* page alias */
		if ($data["pageAlias"]) {
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("page alias"));
				$table->endTableData();
				$table->addTableData();
					$table->addCode($data["pageAlias"].".htm");
				$table->endTableData();
			$table->endTableRow();
		}
		/* page label */
		if ($data["pageLabel"]) {
			$table->addTableRow();
				$table->addTableData("", "header");
					$table->addCode(gettext("page label"));
				$table->endTableData();
				$table->addTableData();
					$table->addCode($data["pageLabel"]);
				$table->endTableData();
			$table->endTableRow();
		}
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("publication date"));
			$table->endTableData();
			$table->addTableData();
				$table->addCode(date("d-m-Y H:i", $data["datePublication"]));
			$table->endTableData();
		$table->endTableRow();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("last modification"));
			$table->endTableData();
			$table->addTableData();
				$table->addCode(date("d-m-Y H:i", $data["date_changed"]));
			$table->endTableData();
		$table->endTableRow();

		/* start page options */
		$opts = new Layout_table(array(
			"cellspacing" => 1, "cellpadding" => 2
		));
		$opts->addTableRow();
			$opts->addTableData("", "data");
				if ($data["isActive"])
					$opts->insertAction("enabled", "", "");
				else
					$opts->insertAction("disabled", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				if ($data["isActive"])
					$opts->addCode(gettext("This page is active"));
				else
					$opts->addCode(gettext("This page is inactive"));
			$opts->endTableData();
		$opts->endTableRow();
		/* ispublic */
		if ($data["isPublic"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("go_support", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page is public"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* ismenuitem */
		if ($data["isMenuItem"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("go_desktop", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page is a menuitem"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* istemplate */
		if ($data["isTemplate"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("ftype_calc", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page is available as template"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* istemplate */
		if ($data["pageRedirect"]) {
			$opts->addTableRow();
				$opts->addTableData(array("valign" => "top"), "data");
					$opts->insertAction("reload", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has a redirect"));
					$opts->addTag("br");
					$opts->addCode(gettext("Location").": ");
					$opts->insertTag("a", $data["pageRedirect"], array(
						"href" => sprintf("javascript: popup('%s');", $data["pageRedirect"])
					));
					$opts->addTag("br");
					if ($data["pageRedirectPopup"]) {
						$opts->addCode(gettext("Redirect in a popup"));
						$opts->addSpace();
						if ($data["popup_height"] && $data["popup_width"])
							$opts->addCode(sprintf("%dx%d ", $data["popup_width"], $data["popup_height"]));
						if ($data["popup_hidenav"])
							$opts->addCode(gettext("and hide navigation"));

					}
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* search override */
		if ($data["search_override"]) {
			$opts->addTableRow();
				$opts->addTableData(array("valign" => "top"), "data");
					$opts->insertAction("mail_tracking", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has custom SEO settings"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		/* date info */
		if ($data["isDateRange"]) {
			$opts->addTableRow();
				$opts->addTableData(array("valign" => "top"), "data");
					$opts->insertAction("calendar_today", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has a publication range"));
					$opts->addTag("br");
					if ($data["date_start"] && $data["date_end"]) {
						$opts->addCode(date("d-m-Y H:i", $data["date_start"]));
						$opts->addCode(" - ");
						$opts->addCode(date("d-m-Y H:i", $data["date_end"]));
					}	elseif ($data["date_start"]) {
						$opts->addCode(date("d-m-Y H:i", $data["date_start"]));
						$opts->addCode(" - ".gettext("no end date"));
					} else {
						$opts->addCode(gettext("no start date")." - ");
						$opts->addCode(date("d-m-Y H:i", $data["date_end"]));
					}

				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isDate"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("calendar_reg_hour", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has calendar items"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isList"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("mail_templates", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has a listing"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isForm"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("state_special", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has a contact form"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["useMetaData"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("mail_readconfirm", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has extra meta information"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isSticky"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("down", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page is sticky / locked"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($data["isGallery"]) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("ftype_image", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has an image gallery"));
				$opts->endTableData();
			$opts->endTableRow();
		}
		if ($cms_data->checkPagePermissions($id)) {
			$opts->addTableRow();
				$opts->addTableData("", "data");
					$opts->insertAction("permissions", "", "");
				$opts->endTableData();
				$opts->addTableData("", "data");
					$opts->addCode(gettext("This page has custom page permissions"));
				$opts->endTableData();
			$opts->endTableRow();
		}



		$opts->endTable();
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("page options"));
			$table->endTableData();
			$table->addTableData("", "header");
				$table->addCode($opts->generate_output());
			$table->endTableData();
		$table->endTableRow();

		$table->endTable();

		$buf = str_replace("'", "\'", preg_replace("/(\r|\n)/si", "", $table->generate_output()) );
		echo sprintf("infoLayer('%s');", $buf);
	}
}
?>
