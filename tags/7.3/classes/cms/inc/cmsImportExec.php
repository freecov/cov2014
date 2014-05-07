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

	/* set time limit very high */
	set_time_limit(60 * 60 * 4);

	/* release session object */
	session_write_close();

	#echo "<a href='javascript: history.go(-1);'>back</a><br><br><hr><br>";

	/* create database link */
	$cms_db = mysql_connect($req["cms"]["server"], $req["cms"]["username"], $req["cms"]["password"])
		or die("Cannot connect to server: ".$req["cms"]["server"]);

	/* switch to source db */
	mysql_select_db($req["cms"]["database"], $cms_db) or die("Cannot open database: ".$req["cms"]["database"]);

	/* do a query to check if we have a cms database */
	$q = sprintf("select cms_files from cms_license");
	$res = mysql_query($q) or die("No CMS database found at this location: ".$req["cms"]["database"]);
	$license = mysql_result($res,0);

	if (!$license)
		die("No license information found in database: ".$req["cms"]["database"]);

	$path = preg_replace("/\/$/s", "", $req["cms"]["filestore"]);
	$path.= "/".$license."/";

	/* check for filestore */
	if (!file_exists($path))
		die("filestore not found: ".$path);

	/* check for old cms database version, this has to be 6.0.0 */
	$q = sprintf("select * from cms_license");
	$license_res = mysql_query($q, $cms_db) or die(mysql_error());
	$license = mysql_fetch_assoc($license_res);

	if (!in_array($license["db_version"], array(522, 600)))
		die("cms database version has to be 522 or 600, please upgrade your cms");

	/* look for the cmsfiles folder inside the cms folder */
	$cms_root_files = $this->createCmsDir("cmsfiles", 0);

	/* create old cms directory */
	if (!$license["cms_files"])
		die("cms files not specified in old database, please check your cms installation");

	/* get cms old files directory */
	$cms_old_files = $this->createCmsDir($license["cms_files"], $cms_root_files, 1);
	$cms_old_location = sprintf("%s/%s", $req["cms"]["filestore"], $license["cms_files"]);

	/* read old structure */
	$this->createCmsStruct($cms_old_location, $cms_old_files);

	/* import data */
	if ($req["cms"]["siteroot"] == "O") {
		/* we can erase it all */
		$tables = array(
			"cms_abbreviations",
			"cms_alias_history",
			"cms_banners",
			"cms_banners_log",
			"cms_banners_summary",
			"cms_data",
			"cms_date",
			"cms_date_index",
			"cms_formulieren",
			"cms_form_settings",
			"cms_gallery",
			"cms_gallery_photos",
			"cms_license_siteroots",
			"cms_list",
			"cms_logins_log",
			"cms_metadata",
			"cms_metadef",
			"cms_permissions",
			"cms_users",
			"cms_siteviews"
		);
		foreach ($tables as $t) {
			sql_query(sprintf("truncate table %s", $t));
		}

		/* remapping format */
		/* old (dutch) => new (english) */

		/* copy some tables */
		$this->copyCmsTable("cms_abbreviations");
		$this->copyCmsTable("cms_date", array(
			"datum"        => "date_begin",
			"omschrijving" => "description",
			"datum_end"    => "date_end",
			"repeterend"   => "repeating"
		));
		$this->copyCmsTable("cms_date_index", array(
			"datum" => "datetime"
		));
		$this->copyCmsTable("cms_formulieren", array(
			"veld_naam" => "field_name",
			"veld_type" => "field_type",
			"veld_value" => "field_value",
			"volgorde" => "order"
		));
		$this->copyCmsTable("cms_gallery");

		$this->copyCmsTable("cms_lijst", array(
			"velden"       => "fields",
			"aantal"       => "count",
			"lijstpositie" => "listposition"
		), "cms_list");
		$this->copyCmsTable("cms_metadata", array(
			"veldid" => "fieldid",
			"waarde" => "value"
		));
		$this->copyCmsTable("cms_metadef", array(
			"veldnaam"   => "field_name",
			"veldtype"   => "field_type",
			"veldwaarde" => "field_value",
			"volgorde"   => "order",
			"groep"      => "group"
		));
		/* we cannot migrate cms_rechten => cms_permissions and cms_users */

		/* gallery photos and data */
		$this->copyCmsTable("cms_gallery_fotos", array(
			"omschrijving" => "description",
			"volgorde"     => "order"
		), "cms_gallery_photos");

		$this->copyCmsGalleryThumbs($path);

		/* copy cms_data */
		$this->copyCmsTable("cms_data", array(
			/* re-mapping */
			"paginaTitel"     => "pageTitle",
			"datumPublicatie" => "datePublication",
			"isLijst"         => "isList",
			"notifyBeheerder" => "notifymanager",
			"paginaData"      => "pageData"
		), array());

		$this->applyCmsSiteRootPatches();

		$output = new Layout_output();
		$output->start_javascript();
			$output->addCode("
				opener.location.href = 'index.php?mod=cms&cmd=collapseAll&options_state=none';
				setTimeout('window.close();', 200);
			");
		$output->end_javascript();
		$output->exit_buffer();
	}

 ?>