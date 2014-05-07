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

	/* pick_cla {{{ */
	public function select_classification($prefix="", $suffix="", $allow_mixed=0, $newsletter=0) {
		require(self::include_dir."select_classification.php");
		return $output->generate_output();
	}

	public function classification_selection($id, $current="", $type="enabled", $show_only=0) {

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
					popup('index.php?mod=classification&action=pick_cla&sub_action=init&field_id=$id&type=$type', 'classification_select', 700, 410, 1);
				}
			");
			$output->end_javascript();
		}

		return $output->generate_output();
	}
	/* }}} */
}
?>
