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

	$apckey = sprintf("address_%d_%d_%d", $addressid, $parent, (int)$_REQUEST["start"]);
	$fetch = $this->getApcCache($apckey);
	if ($fetch) {
		echo $fetch;
	} else {

		//$cms_license = $this->cms->getCmsSettings();
		$cms_license = $this->cms_license;

		if (!$cms_license["cms_address"]) {
			$this->triggerError(403);
			echo ("Module is disabled");
			return;
		}

		$output = new Layout_output();
		$address_data = new Address_data();

		$output->insertAction("addressbook", gettext("Address search"), "");
		$output->addSpace();
		$output->generate_output();
		$output->addTag("b", array(
			"id" => "address_relation"
		));
		$output->addCode(gettext("Relation").": ");
		$output->addCode(sprintf("<a href='/addressdata/?address=%d'>%s</a>",
			$addressid, $address_data->getAddressNameById($addressid)));
		if ($_REQUEST["parent"]) {
			$output->addCode(", ".gettext("category").": ");
			$output->addCode($this->getPageTitle($parent, -1, 1));
		}
		$output->addTag("br");
		$output->addTag("br");
		$output->endTag("b");

		$regex_syntax = sql_syntax("regex");
		$repl = " replace(address_ids, ',', '|') ";
		$reg = " ($repl $regex_syntax '(^|\\\\|)". $addressid ."(\\\\||$)') ";

		$start = (int)$_REQUEST["start"];

		$data = array();

		if ($parent)
			$subq = sprintf("address_level = 1 AND parentPage = %d", $parent);
		else
			$subq = "address_level is NULL or address_level = 0";

		$q = sprintf("select * from cms_data where (%s) and apEnabled IN (%s) and %s and %s order by datePublication DESC",
			$subq, implode(",", $this->public_roots), $this->base_condition, $reg);
		$res = sql_query($q, "", $start, $this->pagesize);
		while ($row = sql_fetch_assoc($res)) {
			$row["datePublication_h"] = date("d-m-Y", $row["datePublication"]);
			if ($row["pageAlias"])
				$row["pageAlias"].= ".htm";
			else
				$row["pageAlias"] = $row["id"].".htm";

			$data[]= $row;
		}

		$q = sprintf("select count(*) from cms_data where (%s) and apEnabled IN (%s) and %s and %s",
			$subq, implode(",", $this->public_roots), $this->base_condition, $reg);
		$res = sql_query($q);
		$num = sql_result($res,0,"",2);

		$q = sprintf("select parentPage, count(*) as num from cms_data where (address_level = 1) and apEnabled IN (%s) and %s and %s group by parentPage order by datePublication DESC",
			implode(",", $this->public_roots), $this->base_condition, $reg);
		$res = sql_query($q, "", (int)$_REQUEST["start"], $this->pagesize);

		if (sql_num_rows($res) > 0) {
			$output->addCode(gettext("Available subcategories").": ");
			$output->addTag("ul", array(
				"id" => "address_subcategories"
			));
			while ($row = sql_fetch_assoc($res)) {
				if ($parent == $row["parentPage"])
					if ($parent == $row["parentPage"]) {
						$bold1 = "<b>";
						$bold2 = "</b>";
					} else {
						$bold1 = "";
						$bold2 = "";
					}
					$output->addCode(
						sprintf("<li><a href='/addressdata/?address=%d&parent=%d'>%s%s (%d)%s</a></li>",
						$addressid, $row["parentPage"], $bold1, $this->getPageTitle($row["parentPage"], -1, 1), $row["num"], $bold2)
					);
			}
			$output->endTag("ul");
		}


		$view = new Layout_view(1);
		$view->addData($data);

		$view->setHtmlField("pageTitle");
		$view->addMapping(gettext("page name"), "%%complex", "left");
		$view->addMapping(gettext("date"), "%datePublication_h", "left");
		$view->defineComplexMapping("complex", array(
			array(
				"type" => "link",
				"link" => array("/page/", "%pageAlias"),
				"text" => "%pageTitle"
			)
		));

		$output->addCode($view->generate_output(1));

		if ($num > $this->pagesize) {
			$output->addTag("br");
			$output->addTag("br");
			$next_results = sprintf("/addressdata/?address=%d&amp;start=%%%%", $addressid);
			$paging = new Layout_paging();
			$paging->setOptions($start, $num, $next_results, $this->pagesize, 1);
			$output->addCode($paging->generate_output());
			$output->addTag("br");
		}
		$buffer = $output->generate_output();
		$this->setApcCache($apckey, $buffer);
		echo $buffer;
	}
?>