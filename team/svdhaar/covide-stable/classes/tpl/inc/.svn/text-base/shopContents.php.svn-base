<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}

	/* set flag for later */
	$this->is_shop = 1;

	$shop =& $_SESSION["shop"];
	if (!is_array($shop))
		$shop = array();

	if ($this->cms_license["cms_shop_page"])
		$this->getPageData($this->cms_license["cms_shop_page"]);

	$output = new Layout_output();
	$output->insertAction("shop", "", "");
	$output->addSpace();
	$output->insertTag("b", gettext("current shopping cart contents").": ");
	$output->addTag("br");
	$output->addTag("br");
	$output->insertTag("span", gettext("enter new amount for article"), array(
		"style" => "display: none",
		"id"    => "shop_edit_msg"
	));
	$output->insertTag("span", gettext("remove article"), array(
		"style" => "display: none",
		"id"    => "shop_del_msg"
	));
	echo $output->generate_output();

	$data = array();
	$total = 0;
	foreach ($shop as $id=>$item) {
		foreach ($item as $field=>$num) {
			$page = $this->getPageById($id);
			$page["num"] = $num;
			$page["thumb"] = $this->getPageThumb($page["id"], 100, 50);
			
			if ($field) {
				$meta = $this->getMetadataByPage($id);
				$page['shopPrice'] = $meta[$field]['value'];
				$page['pageTitle'] .= sprintf(' (%s)', $meta[$field]['field_name']);
			}


			$page["total"] = $page["shopPrice"] * $page["num"];
			$total += $page["total"];
			$page["total"] = number_format($page["total"], 2);
			$page["shopPrice"] = number_format($page["shopPrice"], 2);
			$page["pageTitle"] = addslashes($page["pageTitle"]);
			$page['metafield'] = $field;

			$data[] = $page;
		}
	}
	$view = new Layout_view();
	$view->addData($data);
	$view->addMapping(gettext("image"), "%thumb", "left");
	$view->addMapping(gettext("article"), "%%complex_title", "left");
	$view->addMapping(gettext("description"), "%pageHeader", "left");
	$view->addMapping(gettext("price"), array($this->valuta, "&nbsp;", "%shopPrice"), "left", "nowrap");
	$view->addMapping(gettext("count"), array("%num", "x"), "left");
	$view->addMapping(gettext("total"), array($this->valuta, "&nbsp;", "%total"), "left", "nowrap");
	$view->addMapping("", "%%complex_actions");

	$view->defineComplexMapping("complex_title", array(
		array(
			"type"  => "link",
			"text"   => "%pageTitle",
			"link"  => array("/", "%id", ".htm")
		),
	));



	$view->defineComplexMapping("complex_actions", array(
		array(
			"type"  => "action",
			"src"   => "edit",
			"alt"   => gettext("edit"),
			"link"  => array("javascript: shopEdit('", "%id", "', '", "%num", "', '", "%pageTitle", "', '", "%metafield", "');")
		),
		array(
			"type"  => "action",
			"src"   => "delete",
			"alt"   => gettext("delete"),
			"link"  => array("javascript: shopDel('", "%id", "', '", "%pageTitle", "', '", "%metafield", "');")
		)
	));

	$view->setHtmlField("thumb");
	echo $view->generate_output();

	echo sprintf("<br><b>%s: %s %s</b><br><br>",
		gettext("Total price"), $this->valuta, number_format($total,2));

	if (count($_SESSION["shop"]) > 0) {
		if ($this->cms_license["cms_shop_results"])
			$this->getPageData($this->cms_license["cms_shop_results"]);
	} else {
		echo sprintf("<b>%s</b><br><br>", gettext("You have no articles in your shopping card. Please select one or more articles before you continue."));
	}
?>
