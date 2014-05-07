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
				"style" => "display: none; border-left: 1px solid #666; border-top: 1px solid #666;",
				"id"    => "performance_info"
			));
			$this->addSpace(3);

			$starttime = $this->_rendertime;
			$endtime   = microtime(1);
			$totaltime = number_format(($endtime - $starttime), 2);


			$web = ($totaltime - $GLOBALS["dbstat"]["time"]);
			if ($web < 0)
				$web = 0;

			if ($totaltime < $web)
				$web = $totaltime;
			$local_hostinfo = posix_uname();
			$this->addCode(sprintf("%s %s (%s): ", date("d-m-Y H:i:s"), gettext("server"), $local_hostinfo["nodename"]));
			$this->insertAction("performance_webserver", gettext("webserver processing time"), "");
			$mem_size = memory_get_peak_usage();
			$mem_size/=1024;
			$mem_mod = "KB";

			if ($mem_size >= 1024) {
				$mem_size/=1024;
				$mem_mod = "MB";
			}
			if ($mem_size >= 1024) {
				$mem_size/=1024;
				$mem_mod = "GB";
			}
			$this->addCode(sprintf(" %ss / %s%s ",
				number_format($web,2),
				number_format($mem_size,2), $mem_mod
			));
			$double = 0;
			if (is_array($GLOBALS["dbstat"]["double"])) {
				foreach ($GLOBALS["dbstat"]["double"] as $v) {
					if ($v > 1) {
						$double += $v;
					}
				}
			}
			$this->insertAction("performance_dbserver", gettext("database processing time"), "");
			$this->addCode(sprintf(" %ss / %sq / %sd ",
				number_format($GLOBALS["dbstat"]["time"],2),
				number_format($GLOBALS["dbstat"]["count"],0),
				number_format($double,0)
			));
			$this->insertAction("performance_total", gettext("total page rendering time"), "");
			$this->addCode(sprintf(" %ss %s ", number_format($totaltime, 2),
				gettext("server total")));

			$this->addSpace(2);
			$this->addCode(sprintf(" | %s: ", gettext("client rendering")));
			$this->insertAction("performance_client", gettext("client and browser processing time"), "");
			$this->addSpace();
			$this->insertTag("div", "", array("id"=>"performance_clienttime", "style"=>"display: inline;"));
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
	$this->load_javascript(self::include_dir."infolayer.js");
	$this->load_javascript(self::include_dir."js_floatlayer_msie.js");

	/* this one always needs to be last */
	$this->load_javascript(self::include_dir."js_classes_end.js");
	$this->endTag("body");
	$this->endTag("html");
?>
