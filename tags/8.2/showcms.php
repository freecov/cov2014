<?php
	$skip_run_module     = 1;
	$skip_run_module_cms = 1;
	$skip_session_init   = 1;

	require_once("index.php");

	/* close session writing */
	//session_write_close();

	if ($_REQUEST["oldfile"]) {
		$cmsData = new Cms_data();
		$cmsData->handleOldCmsFile($_REQUEST["oldfile"]);
	}

	$orig_id               = $_REQUEST["id"];
	$_REQUEST["id"]        = preg_replace("/\/.*$/s", "", $_REQUEST["id"]);
	$_REQUEST["cacheid"]   = preg_replace("/\/.*$/s", "", $_REQUEST["cacheid"]);
	$_REQUEST["galleryid"] = preg_replace("/\/.*$/s", "", $_REQUEST["galleryid"]);

	/* msie fix with (excel?) documents */
	/*
	if ($_REQUEST["save"] && !is_numeric($orig_id)) {
		header(sprintf("Location: /showcms.php?dl=1&save=1&id=%d",	$_REQUEST["id"]));
		exit();
	}
	*/
	if ($_REQUEST["id"]) {
		$id = preg_replace("/[^0-9]/s", "", $_REQUEST["id"]);
		if ($id) {
			$cmsData = new Cms_data();
			$cmsData->getCmsFile((int)$id);
		}
	}
	if ($_REQUEST["cacheid"]) {
		$id = preg_replace("/[^0-9]/s", "", $_REQUEST["cacheid"]);
		if ($id) {
			$cmsData = new Cms_data();
			$cmsData->getCmsCache((int)$id);
		}
	}
	if ($_REQUEST["galleryid"]) {
		$id = preg_replace("/[^0-9]/s", "", $_REQUEST["galleryid"]);
		if ($_REQUEST["html"]) {

			$cms_data = new Cms_data();
			$item = $cms_data->getGalleryItem($id);
			$items = $cms_data->getGalleryData($item["pageid"]);
			$settings = $cms_data->getGallerySettings($item["pageid"]);

			$_items = array();
			$i = 0;
			foreach ($items as $k=>$v) {
				$i++;
				$_items[$i] = $k;
				if ($k == $id)
					$_cur = $i;
			}
			if ($_items[$_cur+1])
				$_next = $_items[$_cur+1];
			else
				$_next = $_items[1];

			if (!$settings["font"])
				$settings["font"] = "arial,serif";

			switch ($settings["font_size"]) {
				case 1: $settings["font_size"] = 8; break;
				case 2: $settings["font_size"] = 10; break;
				case 3: $settings["font_size"] = 12; break;
				case 4: $settings["font_size"] = 14; break;
				case 5: $settings["font_size"] = 18; break;
				case 6: $settings["font_size"] = 24; break;
				case 7: $settings["font_size"] = 36; break;
				default:  $settings["font_size"] = 10; break;
			}

			$output = new Layout_output();
			$output->addTag("html");
			$output->addTag("head");
			$output->addTag("style", array("type" => "text/css"));
				$output->addCode(" html, body { margin: 0px; } \n");
				$output->addCode(sprintf(" body, td, div, A { color: black; font-family: %s; font-size: %dpt; }",
					$settings["font"], $settings["font_size"]));
			$output->endTag("style");
			$output->endTag("head");
			$output->addTag("body");
			$output->start_javascript();
				$output->addCode("
					var NS = (navigator.appName==\"Netscape\")?true:false;

					function FitPic() {
						iWidth = (NS)?window.innerWidth:document.body.clientWidth;
						iHeight = (NS)?window.innerHeight:document.body.clientHeight;
						iWidth = document.images[0].width - iWidth;
						iHeight = document.images[0].height - iHeight;
						iHeight+= 170;

						window.resizeBy(iWidth, iHeight);

						iWidth = (NS)?window.innerWidth:document.body.clientWidth;
						iHeight = (NS)?window.innerHeight:document.body.clientHeight;

						var nWidth = iWidth;
						var nHeight = iHeight;

						if (iWidth  < 600) { nWidth = 600; }
						if (iHeight < 500) { nHeight = 500; }

						if (iWidth != nWidth || iHeight != nHeight) {
							window.resizeTo(nWidth, nHeight);
						}


					};
					window.onload = function() { FitPic(); }
				");
			$output->end_javascript();
				$output->addTag("center");
				$output->addTag("img", array(
					"src" => sprintf("/cmsgallery/page%d/%d&size=%s", $item["pageid"], $id, $_REQUEST["size"])
				));
				$output->endTag("center");
				$descr = $item["description"];

				$output->insertTag("div", "", array(
					"style" => "width: 100%; height: 2px; background-color: black;",
					"id" => "progressdiv"
				));

				#$output->addTag("div", array(
				#	"style" => "overflow: auto; height: 80px; width: 100%;"
				#));

				$tbl= new Layout_table(array("width" => "100%"));
				$tbl->addTableRow();
					$tbl->addTableData("", array("valign" => "top"));
						$tbl->addSpace(2);
						$tbl->addCode(nl2br($descr));
					$tbl->endTableData();
					$tbl->addTableData(array("valign" => "top"));
						$tbl->addTag("div", array(
							"margin-right: 2px;"
						));
						$tbl->addTag("nobr");
						$tbl->addCode(sprintf("[%s %d/%d]", gettext("image"), $_cur, $i));
						$tbl->addTag("br");

						$tbl->insertAction("down", "", sprintf("showcms.php?dl=1&galleryid=%d&size=%s", $id, "full"));
						$tbl->addSpace();
						$tbl->insertTag("a", gettext("download"), array(
							"href" => sprintf("/cmsgallery/page%d/%d&size=%s", $item["id"], $id, "full")
						));
						$tbl->addTag("br");
						$tbl->insertAction("calendar_reg_hour", "", "javascript: startStopSlide();");
						$tbl->addSpace();
						$tbl->insertTag("a", gettext("start slideshow"), array(
							"href" => "javascript: startStopSlide();",
							"id"   => "slideid"
						));
						$tbl->addTag("br");
						$tbl->insertAction("close", "", "javascript: window.close();");
						$tbl->addSpace();
						$tbl->insertTag("a", gettext("close"), array(
							"href" => "javascript: window.close();"
						));
						$tbl->endTag("nobr");
						$tbl->endTag("div");

						$tbl->start_javascript();
							$tbl->addCode(sprintf("
								var slider = 0;
								var slideTimer;
								var slideIval;
								var slprogress = 0;

								function updTimer() {
									if (slider) {
										slprogress--;
										var pc = parseInt((slprogress+10));
										if (pc > 100) {
											pc = 100;
										}
										if (pc > 0) {
											document.getElementById('progressdiv').style.width = pc+'%%';
										}
									}
								}
								function startStopSlide() {
									if (slider == 0) {
										slprogress = 100;
										slideIval = setInterval('updTimer();', 50);
										slideTimer = setTimeout('nextSlide();', 6000);
										slider = 1;
										document.getElementById('slideid').innerHTML = '%s';
									} else {
										slider = 0;
										slprogress = 100;
										document.getElementById('progressdiv').style.width = '100%%';
										clearInterval(slideIval);
										clearTimeout(slideTimer);
										document.getElementById('slideid').innerHTML = '%s';
									}
								}
								function nextSlide() {
									location.href='showcms.php?html=1&size=medium&galleryid=%d&slide=1';
								}
								%s
							",
							addslashes(gettext("stop slideshow")),
							addslashes(gettext("start slideshow")),
							$_next,
							($_REQUEST["slide"]) ? "startStopSlide();":"")
							);
						$tbl->end_javascript();

					$tbl->endTableData();
					$tbl->addTableData();
						$tbl->addSpace();
					$tbl->endTableData();
				$tbl->endTableRow();
				$tbl->endTable();

				$div = new Layout_output();
				$div->insertTag("div", $tbl->generate_output(), array(
					"style" => "height: 166px; overflow: auto;"
				));
				$output->addCode($div->generate_output());

				$output->endTag("div");


			$output->endTag("body");
			$output->endTag("html");
			$output->exit_buffer();
		} else {
			if ($id) {
				$cmsData = new Cms_data();
				$cmsData->loadGalleryFile((int)$id, $_REQUEST["size"]);
			}
		}
	}
	exit();
?>
