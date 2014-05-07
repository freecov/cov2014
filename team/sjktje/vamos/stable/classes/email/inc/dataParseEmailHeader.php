<?php
	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	$data = array();

	$hx = explode("\n",str_replace("\r","",$header));
	foreach ($hx as $v) {
		if (preg_match("/^Disposition-Notification-To:(.*)/si",$v)) {
			$data["readconfirm"] = 1;
		}
	}
	foreach ($hx as $v) {
		if (preg_match("/^User-Agent(.*)/si",$v)) {
			$data["user_agent_1"] = trim( preg_replace("/^User-Agent:/si","",$v) );
		}
	}
	foreach ($hx as $v) {
		if (preg_match("/^X-Mailer(.*)/si",$v)) {
			$data["user_agent_2"] = trim( preg_replace("/^X-Mailer:/si","",$v) );
		}
	}
	foreach ($hx as $v) {
		if (preg_match("/^Delivered-To:(.*)/si",$v)) {
			$data["delivered_to"] = trim( preg_replace("/^Delivered-To:/si","",$v) );
		}
	}
	foreach ($hx as $v) {
		if (preg_match("/^X-Priority:(.*)/si",$v)) {
			$v = trim( preg_replace("/X-Priority:/si","",$v) );
			$v = explode(" ",$v);
			switch ($v[0]) {
				case "1":		$primg = "prior_up.gif";     $prior = "1 - ".gettext("Highest");           break;
				case "2":		$primg = "prior_up.gif";     $prior = "2 - ".gettext("High"); break;
				case "3":		$primg = "prior_normal.gif"; $prior = "3 - ".gettext("Normal");       break;
				case "4":		$primg = "prior_down.gif";   $prior = "4 - ".gettext("Low"); break;
				case "5":		$primg = "prior_down.gif";   $prior = "5 - ".gettext("Lowest");            break;
			}
		}
		$data["priority_image"] = $primg;
		$data["prior"] = $prior;
	}
	//classification headers
	foreach ($hx as $v) {
		if (preg_match("/^X-ontvanger: /si",$v)) {
			$data["x_receipient"] = trim( preg_replace("/^X-ontvanger: /si","",$v) );
		}
	}
	foreach ($hx as $v) {
		if (preg_match("/^X-classificatie: /si",$v)) {
			$data["x_classifications"] = trim( preg_replace("/^X-classificatie: /si","",$v) );
		}
	}
	foreach ($hx as $v) {
		if (preg_match("/^X-classificatie niet: /si",$v)) {
			$data["x_negative_classifications"] = trim( preg_replace("/^X-classificatie niet: /si","",$v) );
		}
	}
	foreach ($hx as $v) {
		if (preg_match("/^X-classificering: /si",$v)) {
			$data["x_classification"] = trim( preg_replace("/^X-classificering: /si","",$v) );
		}
	}
	if ($xontvanger) {
		$data["classifications_sent"] = implode("\n", array($data["x_receipient"], $data["x_classification"], $data["x_classifications"], $data["x_negative_classifications"]) );
	}
	//end classifications

	foreach ($hx as $v) {
		if (preg_match("/^X-Spam-Status:(.*)/si",$v)) {
			$v = trim( preg_replace("/X-Spam-Status:/si","",$v) );
			$v = explode(" ",$v);
			foreach ($v as $z) {
				if (preg_match("/^hits=/si",$z)) {
					$data["spam_score_hits"] = preg_replace("/^hits=/si","",$z);
				}
				if (preg_match("/^score=/si",$z)) {
					$data["spam_score_hits"] = preg_replace("/^score=/si","",$z);
				}
				if (preg_match("/^required=/si",$z)) {
					$data["spam_score_max"] = preg_replace("/^required=/si","",$z);
				}
			}
			if ($data["spam_score_max"]>0) {
				$data["spam_percentage"] = number_format( (($data["spam_score_hits"]/$data["spam_score_max"])*100),2 )."%";
			}
		}
	}

?>