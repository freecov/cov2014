<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Cms_data")) {
		die("no class definition found");
	}

	$q = "select * from cms_siteviews where user_id = ".$_SESSION["user_id"];
	$res = sql_query($q);
	if (sql_num_rows($res)==0) {
		$q = "insert into cms_siteviews (user_id) values (".$_SESSION["user_id"].")";
		sql_query($q);
		$opts = array();
	} else {
		$row = sql_fetch_assoc($res);
		$opts = unserialize($row["view"]);
	}
	$opts["pids"]  = $this->getAllParentPages();
	$this->opts = $opts;
	unset($opts);

	/* check if siteroot does still exist */
	if (is_numeric($this->opts["siteroot"])) {
		$q = sprintf("select count(*) from cms_data where id = %d", $this->opts["siteroot"]);
		$res = sql_query($q);
		if (sql_result($res,0,"",2) == 0)
			unset($this->opts["siteroot"]);
	}

	if (!$this->opts["siteroot"])
		$this->switchSiteRoot("R");

	switch ($req["cmd"]) {
		case "switchsiteroot":
			$this->switchSiteRoot($req["id"]);
			break;
		case "search":
			$this->searchPage($req["cms"]["search"]);
			break;
		case "expand":
			$this->expandPage($req["id"]);
			break;
		case "expandAll":
			$this->expandPage(-1);
			break;
		case "collapse":
			$this->collapsePage($req["id"]);
			break;
		case "collapseAll":
			$this->collapsePage(-1);
			if ($req["jump_to_anchor"])
				$this->expandTree((int)preg_replace("/^id/s", "", $req["jump_to_anchor"]));
			break;
		case "fillbuffer":
			$this->fillbuffer($req["page"]);
			break;
		case "pastebuffer":
			$this->pastebuffer($req["id"]);
			$this->expandPage($req["id"]);
			break;
		case "copybuffer":
			$this->copybuffer($req["id"]);
			$this->expandPage($req["id"]);

			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode(" location.href='?mod=cms'; ");
			$output->end_javascript();
			$output->exit_buffer();
			break;
		case "erasebuffer":
			$this->erasebuffer();
			break;
		case "togglebuffer":
			$this->togglebuffer($req["page"]);
			break;
		case "bufferActive":
			$this->bufferOperation("isActive", 1);
			break;
		case "bufferActiveDis":
			$this->bufferOperation("isActive", 0);
			break;
		case "bufferPublic":
			$this->bufferOperation("isPublic", 1);
			break;
		case "bufferPublicDis":
			$this->bufferOperation("isPublic", 0);
			break;
		case "bufferMenuitem":
			$this->bufferOperation("isMenuItem", 1);
			break;
		case "bufferMenuitemDis":
			$this->bufferOperation("isMenuItem", 0);
			break;
		case "bufferAddressLevel":
			$this->bufferOperation("address_level", 0);
			break;
		case "bufferAddressLevelDis":
			$this->bufferOperation("address_level", 1);
			break;
		default:
			if ($req["cmd"])
				echo "Unknown command: ".$req["cmd"];
	}
	if ($req["cmd"] != "search" && $req["cms"]["search"]) {
		$this->highlightSearch($req["cms"]["search"]);
	}
?>