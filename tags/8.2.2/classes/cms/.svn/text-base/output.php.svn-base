<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Cms_output {
	/* constants */
	const include_dir = "classes/cms/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "cms";

	public $buffer = "";
	private $current_root;
	private $sitemap;
	private $pagesize;

	private $editarea_url = "editarea0723/edit_area";

	public function __construct() {
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}

	public function cmsSitemap() {
		require(self::include_dir."cmsSitemap.php");
	}

	private function genFpHeader() {
		$tbl = new Layout_table(array(
			"cellspacing" => 0,
			"cellpadding" => 0,
			"class" => "cms"
		));
		$tbl->addTableRow(array("style" => "background-color: #ddd;"));
			$tbl->insertTableHeader(gettext("level"), array("class" => "cms_right cms_bottom"));
			$tbl->insertTableHeader(gettext("page"),  array("class" => "cms_right cms_bottom"));
			$tbl->insertTableHeader("#", array("class" => "cms_right cms_bottom"));
			$tbl->addTableHeader(array("colspan" => 2, "align" => "right", "class" => "cms_right cms_bottom"));
				$tbl->addTag("nobr");
					$tbl->insertAction("view_all", gettext("toggle extra icons"), "javascript: toggle_cms_table();");
				$tbl->endTag("nobr");
			$tbl->endTableHeader();
			$tbl->addTableHeader(array("colspan"=>2, "class" => "cms_bottom"));
				$tbl->addCode(gettext("actions"));
				$tbl->addSpace();
				$tbl->addCheckBox("checkbox_cms_toggle_all", 1, 0);
			$tbl->endTableHeader();
		$tbl->endTableRow();
		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode(sprintf("
				document.write('%s');\n",
				str_replace("/", "\\/",
					preg_replace("/(\n|\r|\t)/s", "",
						addslashes(
							$tbl->generate_output()
						)
					)
				)
			));
		return $output->generate_output();
	}
	private function genFpFooter() {
		$output = new Layout_output();

		$output->addCode("
			document.write('<tr><td colspan=\"6\" class=\"color_type_record\">&nbsp;<\/td><\/tr>');
			document.write('<\/table>');"
		);
		$output->end_javascript();
		return $output->generate_output();
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

		$cms_data->permission_prefetch = 1;
		$this->generateSiteMap($id, 0, 1, $cms_data, $restricted);
		return $this->buffer;
	}

	public function generateSitemap($id, $level, $special=0, &$cms_data) {
		require(self::include_dir."generateSitemap.php");
	}

	// function to generate one javascript trigger (record)
	public function genFp($opts) {
		$this->buffer .= "fp(".implode(",",$opts).");\n";
	}

	public function viewRestorePoint($id) {
		$cms_data = new Cms_data();
		$data = $cms_data->getPageById($id);

		$mail_data = new Email_data();
		#print_r($data);
		$body = $mail_data->stylehtml($data["autosave_data"]);
		$body = preg_replace("/(<body[^>]*?>)/six", "$1\n<h4>".$data["autosave_header"]."</h4>", $body);
		$body = preg_replace("/(<body[^>]*?>)/six", "$1\n<h3>".$data["pageTitle"]."</h3>", $body);
		echo $body;
		exit();
	}

	public function addMenuItems($venster) {
		$cms_data = new Cms_data();
		$data = $cms_data->getPageById($_REQUEST["id"]);
		$cms_license = $cms_data->getCmsSettings();

		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		$p = $cms_data->getUserPermissions($_REQUEST["id"], $_SESSION["user_id"]);

		$venster->addMenuItem(gettext("show on website"), "javascript: popup('http://".$_SERVER["HTTP_HOST"]."/page/".$_REQUEST["id"].".htm');");
		$venster->addMenuItem(gettext("design"), "?mod=cms&action=editpage&noredir=1&id=".$_REQUEST["id"]);
		$venster->addMenuItem(gettext("textmode"), "?mod=cms&action=editpagetext&id=".$_REQUEST["id"]);

		$venster->addMenuItem(gettext("alias history"), "?mod=cms&action=aliashistory&id=".$_REQUEST["id"]);

		if ($cms_license["cms_mailings"])
			$venster->addMenuItem(gettext("mailings"), "?mod=cms&action=mailings&id=".$_REQUEST["id"]);

		//$venster->addMenuItem(gettext("file management"), "javascript: popup('index.php?mod=cms&action=filesys');");
		$venster->addMenuItem(gettext("page properties"), "?mod=cms&action=editSettings&id=".$_REQUEST["id"]);

		if ($user_info["xs_cms_level"] >= 2 || $p["manageRight"])
			$venster->addMenuItem(gettext("authorization"), "?mod=cms&action=authorisations&id=".$_REQUEST["id"]);

		$venster->addMenuItem(gettext("date options"), "?mod=cms&action=dateoptions&id=".$_REQUEST["id"]);
		if ($cms_license["cms_meta"])
			$venster->addMenuItem(gettext("meta data"), "?mod=cms&action=metadata&id=".$_REQUEST["id"]);

		if ($data["isList"] && $cms_license["cms_list"])
			$venster->addMenuItem(gettext("list generator"), "?mod=cms&action=cmslist&id=".$_REQUEST["id"]);
		if ($data["isGallery"] && $cms_license["cms_gallery"])
		$venster->addMenuItem(gettext("photo gallery"), "?mod=cms&action=cmsgallery&id=".$_REQUEST["id"]);
		if ($data["isForm"] && $cms_license["cms_forms"]) {
			$venster->addMenuItem(gettext("form generator"), "?mod=cms&action=cmsform&id=".$_REQUEST["id"]."&pageid=".$_REQUEST["id"]);
			//$venster->addMenuItem(gettext("form results"), "?mod=cms&action=cmsformresults&id=".$_REQUEST["id"]."&pageid=".$_REQUEST["id"]);
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
			$venster->insertTag("a", gettext("Import non-oop Covide CMS 6 data"), array(
				"href" => "?mod=cms&action=cmsImport"
			));

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
		require(self::include_dir."editSiteInfo.php");
	}

	public function editAccountsList() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("visitor accounts")
		));

		$start = (int)$_REQUEST["start"];

		$cms_data = new Cms_data();
		$cms = $cms_data->getAccountList("", $start, $_REQUEST["search"]);

		$venster->addVensterData();
		$venster->addCode(gettext("search").": ");
		$venster->addTag("form", array(
			"action" => "index.php",
			"method" => "get",
			"id"     => "velden"
		));
		$venster->addHiddenField("mod", "cms");
		$venster->addHiddenField("action", "editAccountsList");
		$venster->addTextField("search", $_REQUEST["search"]);
		$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('velden').submit()");

		$view = new Layout_view();
		$view->addData($cms["data"]);

		$view->addMapping( gettext("username"), "%username" );
		$view->addMapping( gettext("email"), "%email" );
		$view->addMapping( gettext("enabled"), "%is_enabled_h" );
		$view->addMapping( gettext("registered"), "%is_active_h" );
		$view->addMapping( gettext("registration date"), "%registration_date_h" );
		$view->addMapping( gettext("address"), "%address_name" );
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

		$paging = new Layout_paging();
		$url = "index.php?mod=cms&action=editAccountsList&search=".$_REQUEST["search"]."&start=%%";
		$paging->setOptions($start, $cms["count"], $url);
		$venster->addCode($paging->generate_output());


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

	public function editAbbreviation($id) {
		require(self::include_dir."editAbbr.php");
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
					document.getElementById('contents').value = parent.document.getElementById('cmsdata').value;
					document.getElementById('velden').submit();
			");
			$output->end_javascript();

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	public function highlight_show($type) {
		$output = new Layout_output();
		$output->load_javascript($this->editarea_url."/edit_area_full.js", 1);
		$output->start_javascript();
		if ($type == "main")
			$type = "php";

		$hl_state = "true";
		$locale = explode("_", $_SESSION["locale"]);
		$lang = $locale[0];
		if (!$lang)
			$lang = "en";

		$output->addCode("
  			// initialisation
  			function handleClassFocus() {
  				void(0);
  			}
				editAreaLoader.init({
					id: \"contents\"       // id of the textarea to transform
					,start_highlight: $hl_state  // if start with highlight
					,allow_resize: \"both\"
					,language: \"$lang\"
					,syntax: \"$type\"
					,min_height: 460
					,min_width: 740
					,save_callback: \"my_save\"
					,plugins: \"charmap\"
					,allow_toggle: true
					,font_size: \"8\"
					,font_family: \"monospace\"
					,charmap_default: \"arrows\"
					,toolbar: \"save, |, fullscreen, |, charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help\"
				});
        function my_save(content){
        	document.getElementById('contents').value = editAreaLoader.getValue('contents');
        	saveSettings();
        }
		");
		$output->end_javascript();
		return $output->generate_output();
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
		$return = false;

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
						"style" => "width: 600px; height: 180px;"
					), $meta.$v["id"]);
					$return = $meta.$v["id"];
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
			case "shop":
				$tbl->addCode(gettext("please specify in page properties"));
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
					$tbl->addTag("span", array("style" => "display: none;"));
						$tbl->addCheckBox(sprintf($meta."[%s][%s]", $v["id"], 0), "", 1);
					$tbl->endTag("span");
				}
				break;
		}
		return $return;

	}
	public function cmsList($id) {
		require(self::include_dir."cmsList.php");
	}

	/* cmsForm {{{ */
	/**
	 * Cms form generator for a page
	 *
	 * @param int $id The form id
	 */
	public function cmsForm($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("form generator")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getFormData($id);
		//find out if we have a crm block so we can print crm options etc
		$crmform = false;
		foreach ($cms as $k=>$v) {
			if ($v["field_type"] == "crm") {
				$crmform = true;
			}
		}

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
		if ($crmform) {
			$venster->insertLink(gettext("crm field settings"), array("href" => "javascript: popup('?mod=cms&action=cmsformcrmoptions&pageid=".$_REQUEST["id"]."', 'forms', 650, 580, 1);"));
			$venster->addTag("br");
			$venster->addTag("br");
		}

		/*
		$venster->addTag("form", array(
			"id" => "formmode",
			"action" => "index.php"
		));
		$venster->addHiddenField("mod", "cms");
		$venster->addHiddenField("action", "saveFormMode");
		$venster->addHiddenField("id", $id);
		$sel = array(
			//0 => gettext("send with mail"),
			1 => gettext("store in database and send mail"),
			//2 => gettext("use as enquete or poll (no mail)")
		);
		$venster->addCode(gettext("Select mode for this form").": ");
		$venster->addSelectField("cms[mode]", $sel, $mode);
		$venster->insertAction("forward", gettext("selecteer"), "javascript: document.getElementById('formmode').submit();");

		$venster->endTag("form");
		$venster->addTag("br");
		$venster->addTag("br");
		*/
		$err = $cms_data->getFormErrors($id);
		$err = implode("<br>", $err);
		$venster->insertTag("b", $err);


		$venster->endVensterData();
		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();
	}
	/* }}} */
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

		$view->addSubMapping(array(
			gettext("date")." ", "%datetime_start", " ".gettext("till")." ", "%datetime_end", " (ip: ", "%ip_address", ")"
		), 1);

		foreach ($cms["fields"] as $k=>$v) {
			$view->addMapping($v, "%".$k);
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
		require(self::include_dir."cmsGallery.php");
	}

	public function cmsFormEdit($pageid, $id) {
		require(self::include_dir."cmsFormEdit.php");
	}

	public function preparePageDelete($pageid) {
		set_time_limit(60*60);
		session_write_close();

		require(self::include_dir."deletePageConfirm.php");
	}

	public function cmsGalleryItemEdit($id) {
		require(self::include_dir."cmsGalleryItemEdit.php");
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
							$tbl->insertTag("b", sprintf("%s: %s", gettext("last run"), $lr));
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

					$start = $_REQUEST["start"];
					if (!$start)
						$start = 0;

					$lc = $cms_data->checkLinkcheckerResults();

					$total = count($lc);
					$lc = array_slice($lc, $start, $this->pagesize);

					$view = new Layout_view();
					$view->addData($lc);

					$view->addMapping( gettext("page"), "%pageid" );
					$view->addMapping( gettext("name"), "%name" );
					$view->addMapping( gettext("result"), "%result" );
					$view->setHtmlField("url");

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

					$paging = new Layout_paging();
					$paging->setOptions($start, $total, "?mod=cms&action=linkchecker&start=%%");
					$tbl->addCode($paging->generate_output());

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
		require(self::include_dir."showOptionsInfo.php");
	}

	public function editAbbreviations() {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("abbreviations")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getAbbreviations();

		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("abbreviation"), "%abbreviation" );
		$view->addMapping( gettext("description"), "%description" );
		$view->addMapping( gettext("languages"), "%lang_h" );
		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("?mod=cms&action=editAbbreviation&id=", "%id", "&user_id=", $user_id)
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deleteAbbreviation&id=", "%id", "&user_id=", $user_id, "';")
			)
		));
		$venster->addCode($view->generate_output());

		$venster->insertAction("new", gettext("new abbreviation"), "?mod=cms&action=editAbbreviation");
		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
		$venster->endVensterData();

		$output->addCode($venster->generate_output());

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function cmsMailings($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("last mailings for this page")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getMailings($id);
		$emails = $cms_data->handleUpload();

		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("date"), "%datetime_h" );
		$view->addMapping( gettext("emails"), "%email" );
		$venster->addCode($view->generate_output());


		$table = new Layout_table();
		$table->addTableRow();
			$table->addTableData();
				if ($emails) {

					$email_data = new Email_data();
					$data = $cms_data->getPageById($id);

					$table->addTag("form", array(
						"id" => "mailing",
						"method" => "POST",
						"action" => "index.php"
					));
					$table->addTag("br");
					$html = sprintf("<b>%s</b> (%s)<br><br>", $data["pageTitle"], date("d-m-Y H:i", $data["datePublication"]));
					$html.= $email_data->html2text($data["pageData"]);
					$html = preg_replace("/\[\d{1,}\]/s", "", $html);

					$table->addCode(gettext("Total characters in this article").": ");
					$chars = mb_strlen($html);
					$table->insertTag("b", $chars);
					$table->addCode(".");
					$table->addCode(" ".gettext("Send % of article").": ");
					$sel = array(
						"10" => "10% - ".ceil($chars * (10/100))." ".gettext("chars"),
						"15" => "15% - ".ceil($chars * (15/100))." ".gettext("chars"),
						"25" => "25% - ".ceil($chars * (25/100))." ".gettext("chars"),
						"50" => "50% - ".ceil($chars * (50/100))." ".gettext("chars"),
						"75" => "75% - ".ceil($chars * (75/100))." ".gettext("chars"),
						"100" => "100% - ".($chars)." ".gettext("chars")
					);
					$table->addSelectField("length", $sel, "25");
					$table->addTag("br");

					$table->insertTag("b", gettext("The notification is going to be send to the following recipients")).":";
					$table->addSpace();
					$table->insertAction("mail_send", gettext("send notification"), "javascript: document.getElementById('mailing').submit();");
					$table->addTag("br");
					$table->addTag("br");
					$table->insertTag("div", $emails, array(
						"style" => "border: 1px solid #999; padding: 3px;"
					));
					$table->addHiddenField("mod", $_REQUEST["mod"]);
					$table->addHiddenField("id", $_REQUEST["id"]);
					$table->addHiddenField("action", "send_mailing");
					$table->addHiddenField("emails", $emails);
					$table->endTag("form");

				} else {
					$table->addTag("form", array(
						"id" => "uploadform",
						"method" => "POST",
						"enctype" => "multipart/form-data",
						"action" => "index.php"
					));
					$table->addTag("br");
					$table->insertTag("b", gettext("upload file"));
					$table->addTag("br");
					$table->addCheckBox("skipfirst", 1, 1);
					$table->addCode(gettext("skip first line in csv file (default)"));
					$table->addTag("br");
						$table->addHiddenField("MAX_FILE_SIZE", "67108864");
						$table->addHiddenField("mod", $_REQUEST["mod"]);
						$table->addHiddenField("id", $_REQUEST["id"]);
						$table->addHiddenField("action", "init_mailing");
						$table->addUploadField("binFile[]", array("size"=>"45") );
						$table->insertAction("forward", gettext("upload file"), "javascript: document.getElementById('uploadform').submit();");
					$table->endTag("form");
				}
				$table->addTag("br");
				$table->insertAction("close", gettext("close"), "javascript: window.close();");

			$table->endTableData();
		$table->endTableRow();
		$table->endTable();

		$venster->addCode($table->generate_output());

		$venster->endVensterData();
		$output->addCode($venster->generate_output());

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function cmsBannerList($pick=0) {
		require(self::include_dir."cmsBannerList.php");
	}

	public function cmsAliasHistory($id) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("alias history for this page")
		));

		$cms_data = new Cms_data();
		$cms = $cms_data->getAliasHistory($id);

		$this->addMenuItems(&$venster);
		$venster->generateMenuItems();
		$venster->addVensterData();

		$view = new Layout_view();
		$view->addData($cms);

		$view->addMapping( gettext("alias"), "%alias_h" );
		$view->addMapping( gettext("removed on"), "%datetime_h" );

		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("?mod=cms&action=deletealiashistory&itemid=", "%id", "&id=", $id)
			)
		));


		$venster->addCode($view->generate_output());
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function cmsBannerEdit($id=0) {
		require(self::include_dir."editBanner.php");
	}

	public function validateSitemap($sitemap) {
		require(self::include_dir."validateSitemap.php");
	}

	public function cmsImport() {
		require(self::include_dir."cmsImport.php");
	}

	public function registerAccount($data) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("Covide"),
			"subtitle" => gettext("new visitor account registration")
		));

		$venster->addVensterData();

		if ($data["subaction"] == "save") {
			$cms_data = new Cms_data();
			$ret = $cms_data->registrationCheckErrors($data);
			if ($ret !== true) {
				$venster->insertTag("b", gettext("An error occured").": ");
				$venster->insertTag("b", $ret);
				$venster->addTag("br");
				$venster->addTag("br");
			} else {
				/* save registration */
				$cms_data->saveRegistration($data);
			}
		}

		if ($ret === true) {
			$venster->addCode(gettext("Your registration request has been received."));
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode(gettext("You will receive an email within a few minutes with the confirmation message to activate your account."));
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->insertTag("a", gettext("close window"), array(
				"href" => "javascript: window.close();"
			));
			$venster->endVensterData();
			$output->addCode( $venster->generate_output() );

		} else {
			$venster->addCode(gettext("You are about to register a visitor account for this website."));
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode(gettext("Please fill in the following form. After this, you will receive a confirmation email you have to confirm within 24 hours. After this confirmation your account will be activated."));
			$venster->addTag("br");
			$venster->addTag("br");

			$tbl = new Layout_table();
			/* username */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("preferred username"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[username]", $data["username"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* email */
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addSpace();
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("email address"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[email]", $data["email"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* email cf */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("confirm email address"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[email_cf]", $data["email_cf"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* password */
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addSpace();
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("password"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addPasswordField("data[password]", $data["password"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* password cf */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("confirm password"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addPasswordField("data[password_cf]", $data["password_cf"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "right", "colspan" => 2));
					$tbl->insertTag("a", gettext("register account")." &gt;&gt;", array(
						"href" => "javascript: registerAccount();"
					));
				$tbl->endTableHeader();
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
			$venster->endVensterData();

			$output->addTag("form", array(
				"action" => "index.php",
				"method" => "post",
				"id"     => "formident"
			));

			$output->addHiddenField("mod", "cms");
			$output->addHiddenField("action", "registerAccount");
			$output->addHiddenField("data[subaction]", "save");
			$output->addHiddenField("data[uri]", ($data["uri"]) ? $data["uri"]:$_REQUEST["uri"]);
			$output->addHiddenField("data[siteroot]", ($data["siteroot"]) ? $data["siteroot"]:$_REQUEST["siteroot"]);

			$output->addCode( $venster->generate_output() );
			$output->endTag("form");
			$output->load_javascript(self::include_dir."registerAccount.js");
		}
		$output->exit_buffer();
	}

	public function registerAccountConfirm($userid, $hash, $site) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("Covide"),
			"subtitle" => gettext("new visitor account registration")
		));

		$venster->addVensterData();
			$cms_data = new Cms_data();
			$ret = $cms_data->updateRegistration($userid, $hash);
			switch ($ret) {
				case 0:
					$venster->addCode(gettext("Your account is now activated, you can now login with your username and password."));
					$venster->addTag("br");
					$venster->addTag("br");
					$venster->insertTag("a", gettext("click here to go to the website"), array(
						"href" => sprintf(base64_decode($site))
					));

				case 1:
				case 2:
			}
		$venster->endVensterData();

		$output->addCode( $venster->generate_output() );
		$output->exit_buffer();
	}

	public function recoverAccountPassword($data) {
		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("Covide"),
			"subtitle" => gettext("recover account password")
		));

		$venster->addVensterData();

		if ($data["subaction"] == "save") {
			$cms_data = new Cms_data();
			$ret = $cms_data->recoverPassword($data);
			if ($ret !== true) {
				$venster->insertTag("b", $ret);
				$venster->addTag("br");
				$venster->addTag("br");
			}
		}

		if ($ret === true) {
			$venster->addCode(gettext("Your account information has been recovered."));
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode(gettext("You will receive an email within a few minutes with your account details."));
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->insertTag("a", gettext("close window"), array(
				"href" => "javascript: window.close();"
			));
			$venster->endVensterData();
			$output->addCode( $venster->generate_output() );

		} else {
			$venster->addCode(gettext("You are about to recover a visitor account password for this website."));
			$venster->addTag("br");
			$venster->addTag("br");
			$venster->addCode(gettext("Please fill in the following form. After this, you will receive a confirmation email with your password"));
			$venster->addTag("br");
			$venster->addTag("br");

			if ($_REQUEST["email"]) {
				$data["email"]    = $_REQUEST["email"];
				$data["email_cf"] = $_REQUEST["email"];
			}

			$tbl = new Layout_table();
			/* email */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("email address"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[email]", $data["email"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			/* email cf */
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "left"));
					$tbl->addCode(gettext("confirm email address"));
				$tbl->endTableHeader();
				$tbl->addTableData();
					$tbl->addTextField("data[email_cf]", $data["email_cf"]);
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableHeader(array("align" => "right", "colspan" => 2));
					$tbl->insertTag("a", gettext("check account")." &gt;&gt;", array(
						"href" => "javascript: recoverAccount();"
					));
				$tbl->endTableHeader();
			$tbl->endTableRow();
			$tbl->endTable();
			$venster->addCode($tbl->generate_output());
			$venster->endVensterData();

			$output->addTag("form", array(
				"action" => "index.php",
				"method" => "post",
				"id"     => "formident"
			));

			$output->addHiddenField("mod", "cms");
			$output->addHiddenField("action", "recoverAccountPassword");
			$output->addHiddenField("data[subaction]", "save");
			$output->addHiddenField("data[uri]", ($data["uri"]) ? $data["uri"]:$_REQUEST["uri"]);
			$output->addHiddenField("data[siteroot]", ($data["siteroot"]) ? $data["siteroot"]:$_REQUEST["siteroot"]);

			$output->addCode( $venster->generate_output() );
			$output->endTag("form");
			$output->load_javascript(self::include_dir."registerAccount.js");
		}
		$output->exit_buffer();
	}

	public function siteCounters() {
		require(self::include_dir."siteCounters.php");
	}

	public function cmsPageList() {
		require(self::include_dir."cmsPageList.php");
	}

	/* cmsPolls {{{ */
	/**
	 * Show all polls
	 */
	public function cmsPolls() {
		/* prepare data we need */
		$cms_data = new Cms_data();
		$polls = $cms_data->getPolls();
		$user_data = new User_data();
		$perms = $user_data->getUserPermissionsById($_SESSION["user_id"]);

		/* generate output */
		$output = new Layout_output();
		$output->layout_page("Polls", 1);
		$venster = new Layout_venster();
		$venster->addVensterData();

		if ($perms["xs_cms_level"] < 2) {
			$venster->insertTag("b", gettext("No permissions to edit polls"));
		} else {
			$view = new Layout_view();
			$view->addData($polls);

			$view->addMapping(gettext("poll id"), "%id");
			$view->addMapping(gettext("name"), "%question" );
			$view->addMapping(gettext("active"), "%%complex_active");
			$view->addMapping( " ", "%%complex_actions" );

			$view->defineComplexMapping("complex_active", array(
				array(
					"type"  => "action",
					"src"   => "ok",
					"alt"   => gettext("yes"),
					"check" => "%is_active"
				)
			));
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type" => "action",
					"src"  => "edit",
					"alt"  => gettext("edit"),
					"link" => array("index.php?mod=cms&action=pollEdit&id=", "%id")
				),
				array(
					"type" => "action",
					"src"  => "delete",
					"alt"  => gettext("delete"),
					"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=cms&action=deletePoll&id=", "%id", "';")
				)
			));
			$venster->addCode($view->generate_output());
		}

		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
		$venster->addSpace(1);
		$venster->insertAction("new", gettext("new"), "index.php?mod=cms&action=pollEdit&id=0");
		$venster->endVensterData();

		$output->addCode($venster->generate_output());

		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* cmsPollEdit {{{ */
	/**
	 * Edit specific poll
	 *
	 * @param int $id The poll to edit
	 */
	public function cmsPollEdit($id) {
		$cms_data = new Cms_data();
		$polldata = $cms_data->getPollById($id);
		$user_data = new User_data();
		$perms = $user_data->getUserPermissionsById($_SESSION["user_id"]);
		$answerpositions = array();
		for ($i=0; $i<=10; $i++) {
			$answerpositions[$i] = $i;
		}

		$output = new Layout_output();
		$output->layout_page("Polls", 1);
		$venster = new Layout_venster();
		$venster->addVensterData();
		if ($perms["xs_cms_level"] < 2) {
			$venster->insertTag("b", gettext("No permissions to edit polls"));
		} else {
			$venster->addTag("form", array(
				"method" => "post",
				"name"   => "polleditform",
				"id"     => "polleditform",
				"action" => "index.php"
			));
			$venster->addHiddenField("mod", "cms");
			$venster->addHiddenField("action", "pollSave");
			$venster->addHiddenField("poll[id]", $id);
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("question"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("poll[question]", $polldata["question"], array("style" => "width: 300px;"));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("active"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("poll[is_active]", array(1 => gettext("yes"), 0 => gettext("no")), $polldata["is_active"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("options"), array("colspan" => 2), "header");
			$table->endTableRow();
			if (!is_array($polldata["items"]))
				$polldata["items"] = array();
			foreach ($polldata["items"] as $k=>$v) {
				$table->addTableRow();
					$table->insertTableData("(".$v["votecount"]."&nbsp;".gettext("votes").")", "", "header");
					$table->addTableData("", "data");
						$table->addTextField("poll[options][$k][value]", $polldata["items"][$k]["value"], array("style" => "width: 300px;")); 
						$table->addSelectField("poll[options][$k][position]", $answerpositions, $polldata["items"][$k]["position"]);
						$table->insertAction("delete", gettext("delete option"), "javascript:delete_poll_answer(".$k.");");
					$table->endTableData();
				$table->endTableRow();
			}
			$table->addTableRow();
				$table->insertTableData(" ", "", "header");
				$table->addTableData("", "data");
					$table->addTextField("poll[options][0][value]", "", array("style" => "width: 300px;")); 
					$table->addSelectField("poll[options][0][position]", $answerpositions);
				$table->endTableData();			
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData("", "", "header");
				$table->addTableData("", "header");
					$table->addTag("br");
					$table->insertAction("back", gettext("back"), "index.php?mod=cms&action=polllist");
					$table->addSpace(1);
					$table->insertAction("close", gettext("close window"), "javascript: window.close();");
					$table->addSpace(1);
					$table->insertAction("cancel", gettext("delete"), "javascript: poll_delete(".$id.");");
					$table->addSpace(1);
					$table->insertAction("save", gettext("save"), "javascript: poll_save();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
			$venster->endTag("form");
			unset($table);
		}
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."poll_actions.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* cmsFormSettings {{{ */
	public function cmsFormSettings($id) {
		require(self::include_dir."cmsFormSettings.php");
	}
	/* }}} */
}
?>
