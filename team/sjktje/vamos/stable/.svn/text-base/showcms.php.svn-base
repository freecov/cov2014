<?
	$skip_run_module = 1;
	require_once("index.php");

	if ($_REQUEST["id"]) {
		$id = preg_replace("/[^0-9]/s", "", $_REQUEST["id"]);
		if ($id) {
			$cmsData = new Cms_data();
			$cmsData->getCmsFile((int)$id);
		}
	}
	if ($_REQUEST["galleryid"]) {
		$id = preg_replace("/[^0-9]/s", "", $_REQUEST["galleryid"]);
		if ($_REQUEST["html"]) {
			$output = new Layout_output();
			$output->addTag("html");
			$output->addTag("head");
			$output->addTag("style", array("type" => "text/css"));
				$output->addCode(" html, body { margin: 0px; } \n");
				$output->addCode(" body, td, div { font-face: arial, serif, font-size: 11px; }");
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
						iHeight+= 80;
						window.resizeBy(iWidth, iHeight);
						self.focus();
					};
					window.onload = function() { FitPic(); }
				");
			$output->end_javascript();
				$output->addTag("img", array(
					"src" => sprintf("showcms.php?dl=1&galleryid=%d&size=%s", $id, $_REQUEST["size"])
				));
				$output->addTag("br");
				if ($_REQUEST["description"])
					$descr = nl2br(base64_decode($_REQUEST["description"]));
				else
					$descr = gettext("no description");

				$output->addTag("div", array(
					"style" => "overflow: auto; height: 80px;"
				));
				$output->insertTag("center", $descr);
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
