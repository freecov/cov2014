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

	$gallery = $this->cms->getGallerySettings($pageid);
	$items   = $this->cms->getGalleryData($pageid);

	if (!$gallery["last_update"])
		$gallery["last_update"] = mktime(0,0,0,1,1,2000);

	$show = array();
	$curr = 0;
	$row  = 0;
	/* get current page */
	$start = (int)$_REQUEST["start"]+1;
	if ($start < 1)
		$start = 1;

	if ($gallery["gallerytype"] != 0)
		$gallery["cols"] = 1;

	if (in_array($gallery["gallerytype"], array(1,2)))
		$gallery["rows"] = count($items);

	foreach ($items as $i) {
		if ($curr % $gallery["cols"] == 0)
			$row++;
		$show[$row][]=$i;
		$curr++;
	}
	for ($i=$start; $i<$start+$gallery["rows"];$i++) {
		$use[$i] =& $show[$i];
	}

	switch ($gallery["gallerytype"]) {
		case 0:
			/* overview  */
			$data.= "<a name='pagelist'></a>";
			$table = new Layout_table(array(
				"cellspacing" => 0,
				"cellpadding" => 3,
				"class" => "photo_gallery"
			));
			foreach ($use as $row) {
				$table->addTableRow();
				if (!is_array($row))
					$row = array();

				foreach ($row as $item) {
					$table->addTableData(array(
						"style" => "vertical-align: top; text-align: center; border-bottom: 1px solid #666;"
					));
						$table->addTag("a", array(
							"href" => sprintf("javascript: openGalleryItem('%d', '%s');", $item["id"], '')
						));
						$table->addCode($this->generate_gallery_image($item, $gallery));
						$table->endTag("a");
						$table->addTag("br");
						$table->addCode(nl2br($item["description"]));
					$table->endTableData();
				}
				$table->endTableRow();
			}
			$table->endTable();

			$data.= "<BR>".$table->generate_output();
			$pagesize = $gallery["rows"];
			if (count($items) > $gallery["rows"]*$gallery["cols"]) {
				$next_results = "/gallery/".$this->pageid."&amp;mode=".$_REQUEST["mode"]."&amp;start=%%#gallery";
				$paging = new Layout_paging();
				$paging->setOptions($start-1, count($show), $next_results, $pagesize, 1);
				$data.= $paging->generate_output();
				$data.= "<br>";
			}

			break;
		case 1:
			/* slideshow vertical */
			$data.= "<a name='pagelist'></a>";
			$table = new Layout_table(array(
				"cellspacing" => 0,
				"cellpadding" => 3,
				"width"       => "100%"
			));
			foreach ($use as $row) {
				if (!is_array($row))
					$row = array();

				foreach ($row as $item) {
					$table->addTableRow();
						$table->addTableData(array(
							"style" => "vertical-align: top; text-align: center; border-bottom: 1px solid #666;"
						));
							$table->addTag("a", array(
								"href" => sprintf("javascript: iframeGalleryItem('%d', '%s');", $item["id"], '')
							));
							$table->addCode($this->generate_gallery_image($item, $gallery));
							$table->endTag("a");
						$table->endTableData();
					$table->endTableRow();
				}
			}
			$table->endTable();

			$tbl = new Layout_table();
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addTag("div", array(
						"style" => sprintf("overflow-y: auto; height: %dpx", $gallery["bigsize"]+100)
					));
						$tbl->addCode($table->generate_output());
					$tbl->endTag("div");
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->insertTag("iframe", "", array(
						"src" => "/blank.htm",
						"frameborder" => 0,
						"id" => "galleryframe",
						"style" => sprintf("border: 1px solid #666; width: %dpx; height: %dpx", $gallery["bigsize"], $gallery["bigsize"]+100)
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->endTable();

			$data.= "<BR>".$tbl->generate_output();
			break;

		case 2:
			return;
			/* disabled due bad layout */
			/* slideshow horizontal */
			$data.= "<a name='pagelist'></a>";
			$table = new Layout_table(array(
				"cellspacing" => 0,
				"cellpadding" => 3,
				"width"       => "100%"
			));
			$table->addTableRow();
			foreach ($use as $row) {
				if (!is_array($row))
					$row = array();

				foreach ($row as $item) {
						$table->addTableData(array(
							"style" => "vertical-align: top; text-align: center; border-bottom: 1px solid #666;"
						));
							#$img = sprintf("showcms.php?dl=1&amp;galleryid=%d&amp;size=small", $item["id"]);
							$img = sprintf("/cmsgallery/page%d/%d&amp;size=small&amp;file=%s", $item["pageid"], $item["id"], $item["file_short"]);

							$table->addTag("a", array(
								"href" => sprintf("javascript: iframeGalleryItem('%d', '%s');", $item["id"], '')
							));
							$table->addCode($this->generate_gallery_image($item, $gallery));
							$table->endTag("a");
						$table->endTableData();
				}
			}
			$table->endTableRow();
			$table->endTable();

			$tbl = new Layout_table();
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->addTag("div", array(
						"style" => sprintf("overflow-x: auto; width: %dpx", $gallery["bigsize"])
					));
						$tbl->addCode($table->generate_output());
					$tbl->endTag("div");
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData();
					$tbl->insertTag("iframe", "", array(
						"src" => "/blank.htm",
						"frameborder" => 0,
						"id" => "galleryframe",
						"style" => sprintf("border: 1px solid #666; width: %dpx; height: %dpx", $gallery["bigsize"], $gallery["bigsize"]+100)
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->endTable();

			$data.= "<BR>".$tbl->generate_output();
			break;

		case 3:
			/* list */
			$data.= "<a name='pagelist'></a>";
			$table = new Layout_table(array(
				"cellspacing" => 0,
				"cellpadding" => 3,
				"width"       => "100%"
			));
			foreach ($use as $row) {
				$table->addTableRow();
				if (!is_array($row))
					$row = array();

				foreach ($row as $item) {
					$ci++;
					if ($ci == 1)
						$bt = " border-top: 1px solid #666; ";
					else
						$bt = "";

					$table->addTableData(array(
						"style" => "vertical-align: top; text-align: center; $bt border-bottom: 1px solid #666;"
					));
						#$img = sprintf("showcms.php?dl=1&amp;galleryid=%d&amp;size=small", $item["id"]);
						$img = sprintf("/cmsgallery/page%d/%d&amp;size=small&amp;file=%s", $item["pageid"], $item["id"], $item["file_short"]);

						$table->addTag("a", array(
							"href" => sprintf("javascript: openGalleryItem('%d', '%s');", $item["id"], '')
						));
						$table->addCode($this->generate_gallery_image($item, $gallery));
						$table->endTag("a");
					$table->endTableData();
					$table->addTableData(array(
						"style" => "vertical-align: top; text-align: left; $bt border-bottom: 1px solid #666; width: 99%;"
					));
						$table->addCode(nl2br($item["description"]));
						$table->addSpace();
					$table->endTableData();
				}
				$table->endTableRow();
			}
			$table->endTable();
			$data.= "<BR>".$table->generate_output();
			$pagesize = $gallery["rows"];
			if (count($items) > $gallery["rows"]) {
				$next_results = "/gallery/".$this->pageid."&amp;mode=".$_REQUEST["mode"]."&amp;start=%%#gallery";
				$paging = new Layout_paging();
				$paging->setOptions($start-1, count($show), $next_results, $pagesize, 1);
				$data.= $paging->generate_output();
				$data.= "<br>";
			}
		break;
	}
?>
