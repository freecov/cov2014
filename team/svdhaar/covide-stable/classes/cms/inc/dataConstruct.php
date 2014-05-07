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

	/* check for a specific database upgrade inside patches_runonce */
	$this->checkCmsDataTable();

	/* declare default field types */
	$this->weekdays = array(
		"zo" => gettext("sunday"),
		"ma" => gettext("monday"),
		"di" => gettext("tuesday"),
		"wo" => gettext("wednesday"),
		"do" => gettext("thursday"),
		"vr" => gettext("friday"),
		"za" => gettext("saturday"),
	);

	$this->repeat_table = $this->weekdays;
	$this->repeat_table["maand"] = gettext("monthly");
	$this->repeat_table["jaar"]  = gettext("yearly");

	$this->modules = array(
		"cms_meta"         => gettext("metadata"),
		"cms_date"         => gettext("date options"),
		"cms_forms"        => gettext("forms"),
		"cms_list"         => gettext("listoptions"),
		"cms_linkchecker"  => gettext("linkchecker"),
		"cms_changelist"   => gettext("modification overview")." (n/a)",
		"cms_banners"      => gettext("banners")." (n/a)",
		"cms_searchengine" => gettext("searchengine"),
		"cms_gallery"      => gettext("image gallery"),
		"cms_versioncontrol" => gettext("version control")." (n/a)",
		"cms_page_elements"  => gettext("page elements"),
		"multiple_sitemaps"  => gettext("multiple sitemaps (protected items)"),
		"cms_permissions"    => gettext("protected items (multiple_sitemaps)"),
		"cms_mailings"       => gettext("mailing module"),
		"cms_address"        => gettext("address link module"),
		"cms_protected"      => gettext("protected items"),
		"cms_feedback"       => gettext("feedback system"),
		"cms_user_register"  => gettext("register new users"),
		"cms_shop"           => gettext("internet shop"),
		"cms_use_strict_mode" => gettext("only use validated output")
	);

	$this->meta_field_types = array(
		"text"     => gettext("text field"),
		"textarea" => gettext("text area"),
		"select"   => gettext("select box"),
		"checkbox" => gettext("check box"),
		"shop"     => gettext("shop field")
	);

	$this->cms_xs_levels = array(
		0 => gettext("no access at all"),
		1 => sprintf("%s (%s)", gettext("cms user"), gettext("access based on page permissions")),
		2 => sprintf("%s (%s)", gettext("cms manager"), gettext("full content access")),
		3 => sprintf("%s (%s%s)",
			gettext("cms admin"),
			($GLOBALS["covide"]->license["cms_lock_settings"]) ? "css ":"",
			gettext("template and full content access"))
	);
	$this->default_page = 0;

	/* some linkchecker constants */
	$this->linkchecker["outfile"] = sprintf("%s/linkchecker_%s.xml",
		$GLOBALS["covide"]->filesyspath,
		$GLOBALS["covide"]->license["code"]
	);
	$this->linkchecker["startcmd"] = "/usr/bin/linkchecker -a -r2 -t1 --no-status -ocsv --timeout=15 #site#";
	$this->linkchecker["checkcmd"] = "ps afx | grep linkchecker | grep '#site#' | grep  -v 'grep' | cut -d ' ' -f 1";
	$this->linkchecker["url"]      = sprintf("http://%s/mode/linkchecker#param#", $_SERVER["HTTP_HOST"]);
?>
