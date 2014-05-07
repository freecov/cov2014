<?php
Class Cms_output {
	/* constants */
	const include_dir = "classes/cms/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "cms";

	public $buffer = '';

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
			$output->insertAction("paste", gettext("plakken"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_new", "style"=>"display:none"));
			$output->insertAction("new", gettext("nieuwe pagina"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_edit", "style"=>"display:none"));
			$output->insertAction("edit", gettext("pagina wijzigen"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_delete", "style"=>"display:none"));
			$output->insertAction("cancel", gettext("deze pagina verwijderen"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_show", "style"=>"display:none"));
			$output->insertAction("view", gettext("deze pagina tonen"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_copy", "style"=>"display:none"));
			$output->insertAction("save_all", gettext("kopieer paginastructuur"), "");
		$output->endTag("div");
		$output->addTag("div", array("id"=>"cms_icon_important", "style"=>"display:none"));
			$output->insertAction("important", gettext("deze pagina bevat herstelinformatie"), "");
		$output->endTag("div");

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("Sitemap")
		));

		$venster->addMenuItem(gettext("cms instellingen"), "javascript: popup('?mod=cms&action=editCmsSettings', 'settings', 640, 480, 1);");
		$venster->addMenuItem(gettext("site templates"), "javascript: popup('?mod=cms&action=siteTemplates', 'settings', 970, 620, 1);");
		if ($cms_license["cms_permissions"]) {
			$venster->addMenuItem(gettext("cms accountbeheer"), "javascript: popup('?mod=cms&action=editAccountsList', 'settings', 640, 480, 1);");
		}
		if ($cms_license["cms_meta"]) {
			$venster->addMenuItem(gettext("metadata definities"), "javascript: popup('?mod=cms&action=metadataDefinitions', 'settings', 640, 480, 1);");
		}
		$venster->addMenuItem(gettext("bestandsbeheer"), "javascript: popup('index.php?mod=cms&action=filesys');");
		if ($cms_license["cms_linkchecker"]) {
			$venster->addMenuItem(gettext("linkchecker"), "");
		}
		$venster->addMenuItem(gettext("site informatie"), "");
		if ($cms_license["cms_banners"]) {
			$venster->addMenuItem(gettext("banners beheren"), "");
		}
		if ($cms_license["cms_changelog"] || $cms_license["cms_versioncontrol"]) {
			$venster->addMenuItem(gettext("wijzigen overzicht"), "");
		}
		$venster->addMenuItem(gettext("afkortingen beheer"), "");
		$venster->addMenuItem(gettext("online help"), "");
		$venster->addMenuItem(gettext("legenda"), "");
		//$venster->addMenuItem(gettext("external"), "");
		$venster->generateMenuItems();

		$venster->addVensterData();

			/* version control / inklappen / uitklappen / reload / batch options */
			$tbl = new Layout_table(array(
				"style" => "width: 100%;"
			));
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addCode(gettext("Geef een paginanummer of een zoekterm in en klik op zoek").": ");
					$tbl->addTextField("cms[search]", $cms["search"]);
					$tbl->insertAction("edit", gettext("pagina openen"), "javascript: cmsSearchPage();");
					$tbl->insertAction("forward", gettext("zoeken"), "javascript: cmsSearch();");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addTag("br");
					$tbl->insertLink(gettext("alles uitklappen"), array(
						"href" => "javascript: cmsExpand(-1);"
					));
					$tbl->insertAction("down", gettext("alles uitklappen"), "javascript: cmsExpand(-1);");
					$tbl->addCode(" | ");
					$tbl->insertLink(gettext("alles inklappen"), array(
						"href" => "javascript: cmsCollapse(-1);"
					));
					$tbl->insertAction("up", gettext("alles inklappen"), "javascript: cmsCollapse(-1);");
					$tbl->addCode(" | ");
					$tbl->insertLink(gettext("sitemap herladen"), array(
						"href" => "javascript: cmsReload();"
					));
					$tbl->insertAction("reload", gettext("sitemap herladen"), "javascript: cmsReload();");
					$tbl->addSpace(2);

					$sel[gettext("cms roots")]["R"]  = gettext("sitemap root");
					$sel[gettext("cms roots")]["D"]  = gettext("verwijderde items");
					$sel[gettext("cms roots")]["X"]  = gettext("afgeschermde items");
					$sel[gettext("gebruiker roots")] = $cms_data->getUserSitemapRoots();
					$tbl->addSelectField("cms[siteroot]", $sel, $cms["siteroot"]);

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
				$tbl->insertTableHeader(gettext("niveau"));
				$tbl->insertTableHeader(gettext("pagina"));
				$tbl->insertTableHeader("#");
				$tbl->insertTableHeader(gettext("opties"));
				$tbl->insertTableHeader(gettext("akties"));
			$tbl->endTableRow();
			/* sitemap generation code here */
			$tbl->addCode( $this->generateSpecialPage("R", $cms_data) );

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

		$this->generateSiteMap($id, 0, 1, $cms_data);
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
		$venster->addMenuItem(gettext("ontwerp"), "?mod=cms&action=editpage&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("tekstmodus"), "?mod=cms&action=editpagetext&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("bestandsbeheer"), "javascript: popup('index.php?mod=cms&action=filesys');");
		$venster->addMenuItem(gettext("pagina intellingen"), "?mod=cms&action=editSettings&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("authorisaties"), "?mod=cms&action=authorisations&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("datum opties"), "?mod=cms&action=dateoptions&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("meta data"), "?mod=cms&action=metadata&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("lijst generator"), "?mod=cms&action=cmslist&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("formulier generator"), "");
		$venster->addMenuItem(gettext("foto gallery"), "");
	}

	public function cmsPageSettings() {
		require(self::include_dir."cmsPageSettings.php");
	}

	public function editCmsSettings() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("CMS instellingen")
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
					$tbl->addCode(gettext("CMS Modules"));
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->insertAction("save", gettext("opslaan"), "javascript: saveSettings();");
					$tbl->insertAction("close", gettext("sluiten"), "javascript: window.close();");
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

	public function editAccountsList() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("externe accounts")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getAccountList();

		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("gebruikersnaam"), "%username" );
		$view->addMapping( gettext("actief"), "%is_enabled_h" );
		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("bewerken"),
				"link" => array("?mod=cms&action=editAccount&id=", "%id", "&user_id=", $user_id)
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("verwijderen"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteAccount&id=", "%id", "&user_id=", $user_id, "';")
			)
		));
		$venster->addCode($view->generate_output());

		$venster->insertAction("new", gettext("nieuwe gebruiker"), "?mod=cms&action=editAccount");
		$venster->insertAction("close", gettext("sluiten"), "javascript: window.close();");
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


}
?>
