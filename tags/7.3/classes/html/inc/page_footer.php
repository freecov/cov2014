<?php
	if (!class_exists("Layout_output")) {
		die("no class definition found");
	}
	$this->endTag("td");
	$this->endTag("tr");
	if (!$this->_hide_navigation) {
		$this->addTag("tr");
		$this->addTag("td", array(
			"colspan" => 3,
			"style"   => "text-align: right"
		));

		$this->addTag("div", array("id"=>"page_bottom") );
		$this->insertLink("Covide", array(
			"href"=>"http://www.covide.net",
			"target"=>"_new"
		));
		$this->addCode(" v");
		$this->addCode($GLOBALS["covide"]->vernr);
		$this->addCode(" &copy; ");
		$this->insertLink("Covide", array(
			"href"=>"http://www.covide.nl",
			"target"=>"_new"
		));
		$this->addCode(" 2003-".date("Y")." ");

		if (!$GLOBALS["covide"]->mobile) {
			$this->addTag("div", array(
				"style" => "display: none; border: 1px solid #999;",
				"id"    => "performance_info"
			));
			$this->addSpace(3);

			$starttime = $this->_rendertime;
			$endtime   = microtime(1);
			$totaltime = ($endtime - $starttime);

			$web = ($totaltime - $GLOBALS["dbtime"]);
			if ($web < 0) {
				$web = 0;
			}

			$this->insertAction("performance_webserver", gettext("webserver processing time"), "");
			$this->addCode("www:");
			$this->addCode(" ".number_format($web,2). " ");
			$this->insertAction("performance_dbserver", gettext("database processing time"), "");
			$this->addCode("db:");
			$this->addCode(" ".number_format($GLOBALS["dbtime"],2). " ");
			$this->insertAction("performance_total", gettext("total page rendering time"), "");
			$this->addCode("server:");
			$this->addCode(" ".number_format($totaltime, 2). " ");

			$this->addSpace(2);
			$this->insertAction("performance_client", gettext("client and browser processing time"), "");
			$this->addCode("client:");
			$this->addSpace();
			$this->insertTag("div", "", array("id"=>"performance_clienttime", "style"=>"display: inline;"));

			$this->addSpace(2);
			$this->addCode("served by: ".$_SERVER["SERVER_ADDR"]);

			$this->endTag("div");
			$this->addTag("div", array(
				"id"    => "performance_info_trigger",
				"style" => "display: inline;"
			));
			$this->insertAction("performance_show", gettext("show performance information"), "javascript: showPerformanceInfo();");
			$this->endTag("div");
		}

		$this->endTag("div");
		$this->endTag("td");
		$this->endTag("tr");
	}
	$this->endTag("table");

	$this->endTag("div");
	$this->load_javascript("classes/html/inc/js_classes_end.js");


	$this->endTag("body");
	$this->endTag("html");
?>
