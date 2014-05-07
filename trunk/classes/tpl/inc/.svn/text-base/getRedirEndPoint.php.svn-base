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
	$q = sprintf("select pageRedirect, pageRedirectPopup, popup_data from cms_data where id = %d", $page);
	$res = sql_query($q);
	$row = sql_fetch_assoc($res);

	$p = ($this->page_less_rewrites) ? "/":"/page/";

	$row["pageRedirect"] = preg_replace("/^index\.php/s", "", $row["pageRedirect"]);
	if (preg_match("/^\?((id)|(page))=\d{1,}/s", $row["pageRedirect"])) {
		$row["pageRedirect"] = preg_replace("/^\?page=(\d{1,})/s", "?id=$1", $row["pageRedirect"]);
		$row["pageRedirect"] = preg_replace("/^\?id=(\d{1,})/s", $p."$1", $row["pageRedirect"]);
	}

	if ($row["pageRedirect"]) {
		if ($row["pageRedirectPopup"]) {

			if (!preg_match("/(^\/)|(:\/\/)/s", $row["pageRedirect"]))
				$row["pageRedirect"] = $p.$row["pageRedirect"];

			$popup_data = explode("|", $row["popup_data"]);
			$opts = "top=0,left=0";
			if ($popup_data[0] && $popup_data[1])
				$opts.= sprintf(",height=%s,width=%s", $popup_data[0], $popup_data[1]);

			if ($popup_data[2])
				$opts.= ",toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=1";
			else
				$opts.= "toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1";

			if (!$this->textmode) {
				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode( sprintf("var cvd_%s = setTimeout(\"window.open('%s', '%s', '%s');\", 500);",
						md5(rand()), $row["pageRedirect"], "cmswindow_".time(), $opts ));
				$output->end_javascript();
				$this->redir = $output->generate_output();
			}

		} else {
			session_write_close();

			/* check if request is no menu request */
			if (!$_REQUEST["mode"] == "menu") {
				/* check if the redir is internal */
				if (preg_match("/^\//s", $row["pageRedirect"]))
					$this->triggerError(301);
				else
					$this->triggerError(307);

				header( sprintf("Location: %s", $row["pageRedirect"]) );
				exit();
			}
		}
	}
?>
