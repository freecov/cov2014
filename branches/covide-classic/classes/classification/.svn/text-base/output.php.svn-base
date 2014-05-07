<?php
/**
 * Covide Groupware-CRM Classification output module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Classification_output {

	/* constants */
	const include_dir = "classes/classification/inc/";
	const classname   = "Classification_output";

	/* methods */
	/* show_classifications {{{ */
	/**
	 * show overview of classifications
	 *
	 * show a list of classifications etc.
	 * Includes search results and action buttons
	 */
	public function show_classifications() {
		require(self::include_dir."show_classifications.php");
	}
	/* }}} */

	/* show_edit {{{ */
	/**
	 * show edit/create form for classification
	 *
	 * @param int 0 for new, otherwise the classification id
	 */
	public function show_edit($id) {
		require(self::include_dir."cla_edit.php");
	}
	/* }}} */

	/* pick_cla {{{ */
	public function pick_cla() {
		require(self::include_dir."pick_cla.php");
	}
	/* }}} */

	/* select_classification {{{ */
	public function select_classification($prefix="", $suffix="", $allow_mixed=0, $newsletter=0) {
		require(self::include_dir."select_classification.php");
		return $output->generate_output();
	}
	/* }}} */

	/* classification_selection {{{ */
	public function classification_selection($id, $current="", $type="enabled", $show_only=0, $single_cla = 0) {

		if (!is_array($current)) {
			$current = explode("|", $current);
		}
		foreach ($current as $k=>$v) {
			if (!$v) {
				unset($current[$k]);
			}
		}
		if (count($current)==0) {
			$current = array(0);
		}

		$classification_data = new Classification_data();
		$list = $classification_data->getClassifications($current);

		$output = new Layout_output();
		$output->addTag("span", array(
			"id"   => "classifications_name_".$id,
			"name" => "classifications_name_".$id
		));

		//limit view
		if (count($list) > 6) {
			$limit_height = "height: 140px; overflow-y:auto;";
		} else {
			$limit_height = "";
		}

		$output->addTag("div", array(
			"class"  => "limit_height",
			"style" => $limit_height
		));
		foreach ($list as $k=>$v) {
			$output->addTag("li", array("class"=>$type));
			$output->addCode($v["description"]);
			$output->endTag("li");
		}
		$output->endTag("div");

		$output->endTag("span");
		if (!$show_only) {
			$output->addTag("br");
			$output->insertAction("edit", gettext("choose classifications"), "javascript: classification_select_$id();");
			$output->start_javascript();
			$output->addCode("
				function classification_select_$id() {
					popup('index.php?mod=classification&action=pick_cla&sub_action=init&field_id=$id&type=$type&single_cla=$single_cla', 'classification_select', 700, 410, 1);
				}
			");
			$output->end_javascript();
		}

		return $output->generate_output();
	}
	/* }}} */
	/* show_groups {{{ */
	/**
	 * Show list of classification groups
	 */
	public function show_groups() {
		/* only allow global usermanagers and global address managers */
		$user_data = new User_data();
		$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);
		if (!$user_info["xs_classmanage"]) {
			header("Location: index.php?mod=address");
		}
		/* get data */
		$cla_data = new Classification_data();
		$cla_groups = $cla_data->getGroups();
		/* make array with possible vars, for form generation */
		$formitems = array(
			"mod"    => "classification",
			"action" => "showgroups",
			"id"     => ""
		);
		/* show output */
		$output = new Layout_output();
		$output->layout_page(gettext("classifications")." ".gettext("groups"));
		$venster = new Layout_venster(array("title" => "classifications", "subtitle" => "groups"));
		$venster->addMenuItem(gettext("new"), "javascript: cla_group_edit(0);");
		$venster->addMenuItem(gettext("back"), "index.php?mod=classification&action=show_classifications");
		$venster->generateMenuItems();
		$venster->addVensterData();
			/* view object for the data */
			$view = new Layout_view();
			$view->addData($cla_groups);
			$view->addMapping(gettext("name"), "%name");
			$view->addMapping(gettext("description"), "%description");
			$view->addMapping(gettext("edit"), "%%complex_actions");
			$view->defineComplexMapping("complex_actions", array(
				array(
					"type"  => "action",
					"src"   => "edit",
					"alt"   => gettext("change:"),
					"link"  => array("javascript: cla_group_edit(", "%id", ")")
				),
				array(
					"type"  => "action",
					"src"   => "delete",
					"alt"   => gettext("delete"),
					"link"  => array("javascript: cla_group_remove(", "%id", ")")
				)
			));
			/* output the view */
			$venster->addCode($view->generate_output());
		$venster->endVensterData();
		$output->addTag("form", array(
			"id"     => "claform",
			"method" => "get",
			"action" => "index.php"
		));
		foreach ($formitems as $item=>$value) {
			$output->addHiddenField($item, $value);
		}
		$output->addCode($venster->generate_output());
		unset($venster);
		$output->endTag("form");
		$output->load_javascript(self::include_dir."classification_actions.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* cla_group_edit {{{ */
	/**
	 * Show form to add/edit a classification group
	 */
	public function cla_group_edit($id = 0) {
		/* get the data if id is given, otherwise init empty array */
		if ($id) {
			$classification_data = new Classification_data();
			$classification_info = $classification_data->getClassificationGroupById($id, 1);
			
			$address_data = new Address_data();
			$subtitle = gettext("alter classification group");
		} else {
			$classification_info = array(
				"id"          => 0,
				"name"        => "",
				"description" => ""
			);
			$subtitle = gettext("new classification group");
		}

		/* start output buffer */
		$output = new Layout_output();
		$output->layout_page(gettext("alter classification group"), 1);
		/* make nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("classifications"),
			"subtitle" => $subtitle
		));
		$venster->addMenuItem(gettext("back"), "javascript: window.close();");
		$venster->generateMenuItems();
		$venster->addVensterData();
			/* create form */
			$venster->addTag("form", array(
				"method" => "post",
				"id"     => "clagroupedit",
				"action" => "index.php"
			));
			$venster->addHiddenField("mod", "classification");
			$venster->addHiddenField("action", "cla_group_save");
			$venster->addHiddenField("cla[id]", $id);
			/* put a table here for the layout */
			$table = new Layout_table(array("cellspacing"=>1));
			$table->addTableRow();
				$table->insertTableData(gettext("classification group"), "", "header");
				$table->addTableData("", "data");
					$table->addTextField("cla[name]", $classification_info["name"], array("style"=>"width: 300px;"));
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow();
				$table->insertTableData(gettext("description"), "", "header");
				$table->addTableData("", "data");
					$table->addTextArea("cla[description]", $classification_info["description"], array("rows"=>"4", "cols"=> "50"));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("classification"), "", "header");
				$table->addTableData("", "data");
					$table->addHiddenField("cla[classification]", $classification_info["classifi"]);
					$table->endTag("span");
					$classification = new Classification_output();
					$table->addCode( $classification->classification_selection("claclassification", $classification_info["classifi"]) );
				$table->endTableData();
			$table->endTableRow();

			$table->addTableRow();
				$table->insertTableData("", "", "header");
				$table->addTableData("", "data");
					$table->insertAction("save", gettext("save"), "javascript: cla_group_save();");
					if ($id) {
						$table->insertAction("delete", gettext("delete"), "javascript: cla_group_remove($id)");
					}
				$table->endTableData();
			$table->endTableRow();

			$table->endTable();
			$venster->addCode($table->generate_output());
			$venster->endTag("form");
			/* end form */
		$venster->endVensterData();
		/* include window in output buffer */
		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."classification_actions.js");
		/* do some more magic with the rel field if necessary */
		if (is_array($multirel)) {
			$output->start_javascript();
			$output->addCode("addLoadEvent( update_relsearch() );\n");
			$output->addCode("function update_relsearch() { \n");
			foreach ($multirel as $i=>$n) {
				if ($i) {
					$output->addCode("\n");
					$output->addCode("selectRel($i, \"$n\");");
				}
			}
			$output->addCode("\n}\n");
			$output->end_javascript();
		}
		$output->layout_page_end();
		/* flush the buffer to the browser */
		$output->exit_buffer();
	}
}
?>
