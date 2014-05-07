<?php
/**
 * Covide Groupware-CRM Addressbook module. List
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Address_output {

	/* constants */
	const include_dir = "classes/address/inc/";
	const class_name  = "Address_output";

	/* methods */

	/* __construct {{{ */
	/**
	 * Init some defaults
	 */
	public function __construct() {
		if ($GLOBALS["covide"]->license["snelstart"]) {
			if (count($_REQUEST) <= 1) {
				require_once("snelstart_sync.php");
			}
		}
	}
	/* }}} */
	/* relationCard {{{ */
	/**
	 * Show all info about relation/contact
	 *
	 * @param int The address id of the relation/contact
	 */
	public function relationCard($id) {
		require(self::include_dir."relationCard.php");

	}
	/* }}} */
	/* userCard {{{ */
	/**
	 * Show all info about user
	 *
	 * @param int The address id of the user
	 */
	public function userCard($id) {
		require(self::include_dir."userCard.php");

	}
	/* }}} */
	/* show_edit {{{ */
	/**
	 * Show edit screen for relation
	 *
	 * @param int The address to edit
	 * @param string The type of address to edit
	 * @param string If type == other, what kind of other address to edit
	 */
	public function show_edit($id=0, $type="relations", $sub="kantoor", $src_id=0, $view_only=0) {
		require(self::include_dir."show_edit.php");
	}
	/* }}} */

	public function show_edit_private($id = 0, $view_only = 0) {
		require(self::include_dir."show_edit_private.php");
	}

	/* show_item {{{ */
	/**
	 * Show detailed address information
	 *
	 * @param int The address id to show
	 * @param string The address type to show
	 */
	public function show_item($id=0, $addresstype="relations") {
		require(self::include_dir."show_item.php");
	}
	/* }}} */
	/* show_list {{{ */
	/**
	 * show_list. Show addresses
	 *
	 * Show addresslist. This includes search results.
	 * It will show the addresses in pages of 30 results
	 *
	 * @return bool true
	 */
	public function show_list() {
		require(self::include_dir."show_list.php");
	}
	/* }}} */
	/* selectClassification {{{ */
	/**
	 * show classification selection screen
	 */
	public function selectClassification() {
		require(self::include_dir."selectClassification.php");
	}
	/* }}} */
	/* edit_bcard {{{ */
	/**
	 * show businesscard edit screen
	 *
	 * @param int The businesscard id to edit
	 * @param int The address id to force on the bcard
	 */
	public function edit_bcard($id, $address_id=0, $src_id=0, $view_only=0) {
		require(self::include_dir."editBcard.php");
	}
	/* }}} */
	/* show_bcard {{{ */
	/**
	 * show all info for a business card
	 *
	 * @param int The id of the businesscard to show
	 */
	public function show_bcard($cardid) {
		require(self::include_dir."showBcard.php");
	}
	/* }}} */
	/* relcardsearchform {{{ */
	/**
	 * Show form to start search in all items a relation has
	 */
	public function relcardsearchform() {
		require(self::include_dir."relcardsearchform.php");
	}
	/* }}} */
	/* relcardsearch {{{ */
	/**
	 * Show results of search to all comm items a relation has.
	 */
	public function relcardsearch() {
		require(self::include_dir."relcardsearch.php");
	}
	/* }}} */
	/* 	export {{{ */
	/**
		* 	export addresses in csv
		*/
	public function export() {
		require(self::include_dir."export.php");
	}
  /* }}} */
	/* 	exportBcardRecord {{{ */
	/**
		* 	export a bcard record in csv
		*/
  private function exportBcardRecord(&$data, &$row, &$address_data) {
  	require(self::include_dir."exportBcard.php");
  }
  /* }}} */
	/* 	print_selection {{{ */
	/**
		* 	print selection
		*/
	public function print_selection() {
		require(self::include_dir."print_selection.php");
	}
    /* }}} */
	/* show_import_start {{{ */
	/**
	 * Show first screen for import addresses
	 */
	public function show_import_start() {
		require(self::include_dir."show_import_start.php");
	}
	/* }}} */
	/* show_import_step2 {{{ */
	/**
	 * Show second screen for import addresses
	 *
	 * This screen will allow the user to select
	 * which csv field is which database field.
	 */
	public function show_import_step2() {
		require(self::include_dir."show_import_step2.php");
	}
	/* }}} */
	/* addcla_multi {{{ */
	/**
	 * Add a new classification to the selection of addresses
	 *
	 * @param string The serialized options array to feed to the address lookup function
	 */
	public function addcla_multi($searchoptions) {
		require(self::include_dir."addclaMulti.php");
	}
	/* }}} */
	/* showRelIMG {{{ */
	/**
	 * Output image. This can be used inside an <img src=""> tag
	 * It will generate an image based on the original, but no larger then 162x162
	 *
	 * @param array The image size, type and name
	 * @param string relation or bcard, so we know in what directory to get the bin data
	 */
	public function showRelIMG($photo, $addresstype="relations") {
		if (!is_array($photo))  { die("invalid parameters found"); }
		if (count($photo) != 4) { die("invalid parameters found"); }

		$base = $GLOBALS["covide"]->filesyspath;
		if ($addresstype == "relations") {
			$photopath = $base."/relphotos/".$photo["id"].".dat";
		} else {
			$photopath = $base."/relphotos/bcards/".$photo["id"].".dat";
		}
		/* check if the file is there */
		if (file_exists($photopath)) {
			/* create new img and resize the img */
			switch ($photo["type"]) {
				case "image/jpeg":
					$org = imagecreatefromjpeg($photopath);
					break;
				case "image/png":
					$org = imagecreatefrompng($photopath);
					break;
				case "image/gif":
					$org = imagecreatefromgif($photopath);
					break;
			}

			$ow = imagesx($org);
			$oh = imagesy($org);
			$maxh = 162;
			$maxw = 162;
			$new_h = $oh;
			$new_w = $ow;

			if($oh > $maxh || $ow > $maxw) {
				$new_h = ($oh > $ow) ? $maxh : $oh*($maxw/$ow);
				$new_w = $new_h/$oh*$ow;
			}

			header('Content-Transfer-Encoding: binary');
			header("Content-Type: image/jpeg");
			$dest = @imagecreatetruecolor(ceil($new_w), ceil($new_h));
			@imagecopyresampled($dest, $org, 0, 0, 0, 0, ceil($new_w), ceil($new_h), $ow, $oh);
			imagejpeg($dest);
			imagedestroy($dest);
			exit();
		} else {
			die("error");;
		}
	}
	/* }}} */
	/* edit_hrm {{{ */
	/**
	 * Show screen to add/edit HRM info to a usercard
	 *
	 * @param int The user id to edit the hrm info for
	 */
	public function edit_hrm($user_id) {
		require_once(self::include_dir."edit_hrm.php");
	}
	/* }}} */
	/* show_metafields {{{ */
	/**
	 * Show a list with defined global address metafields
	 */
	public function show_metafields() {
		require_once(self::include_dir."show_metafields.php");
	}
	/* }}} */
	public function getProjectBcardsByRelationXML($address_id, $current) {
		$output = new Layout_output();
		$address_data = new Address_data();

		$address_id = explode(",", $address_id);

		$sel = array( 0 => gettext("none") );
		foreach ($address_id as $a) {
			if ($a) {
				$name = $address_data->getAddressNameByID($a);
				$data = $address_data->getBcardsByRelationID($a);

				foreach ($data as $v) {
					if (trim($v["fullname"])) {
						$sel[$name][$v["id"]] = $v["fullname"];
					}
				}
			}
		}
		$output->addSelectField("project[bcard]", $sel, $current);
		$output->exit_buffer();
	}
	public function movePrivate2Public($id, $subaction="") {

		$address_data = new Address_data();

		if ($subaction == "keepprivate") {
			$address_data->keepPrivate($id);
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("
					if (opener) {
						if (opener.location.href.match(/\?mod=address/gi)) {
							if (opener.location.href.match(/\&action=relcard/gi)) {
								var uri = opener.location.href;
								uri = uri.replace(/\&restore_point_steps=\d/gi, '');
								uri = uri.concat('&restore_point_steps=2');
								opener.location.href = uri;
							} else {
								opener.location.href = opener.location.href;
							}
						} else {
							opener.document.getElementById('deze').submit();
						}
						var t = setTimeout('window.close();', 100);
					}
				");
			$output->end_javascript();
			$output->exit_buffer();
		}
		$info = $address_data->getAddressByID($id, "private");
		$name = $info["fullname"];

		$output = new Layout_output();
		$output->layout_page("", 1);
			$venster = new Layout_venster(array(
				"title" => gettext("addresses"),
				"subtitle" => gettext("export")
			));
			$venster->addVensterData();
				$venster->addTag("form", array(
					"method" => "get",
					"action" => "index.php",
					"id"     => "moveform"
				));
				$venster->addHiddenField("mod", "address");
				$venster->addHiddenField("action", "move2publicExec");
				$venster->addHiddenField("id", $id);

				$venster->addCode(gettext("Select the target of the selected private address").":");
				$venster->addSpace();
				$venster->insertTag("b", $name);

				$table = new Layout_table();
				/* relation */
				$table->addTableRow();
					$table->insertTableData(gettext("create relation"), "", "header");
				$table->endTableRow();
				$table->addTableRow();
					$table->addTableData("", "data");
						$table->insertAction("addressbook", gettext("continue"), sprintf("?mod=address&action=edit&addresstype=relations&private_id=%d", $id));
						$table->addSpace();
						$table->insertTag("a", gettext("create relation card"), array(
							"href" => sprintf("?mod=address&action=edit&addresstype=relations&private_id=%d", $id)
						));
					$table->endTableData();
				$table->endTableRow();

				/* bcard */
				$table->addTableRow();
					$table->insertTableData(gettext("create business card"), "", "header");
				$table->endTableRow();
				$table->addTableRow();
					$table->addTableData("", "data");
						$table->insertAction("state_special", gettext("continue"), sprintf("?mod=address&action=edit_bcard&private_id=b%d&addresstype=bcards", $id));
						$table->addSpace();
						$table->insertTag("a", gettext("create business card"), array(
							"href" => sprintf("?mod=address&action=edit_bcard&private_id=b%d&addresstype=bcards", $id)
						));
					$table->endTableData();
				$table->endTableRow();

				/* relation */
				$table->addTableRow();
					$table->insertTableData(gettext("leave private"), "", "header");
				$table->endTableRow();
				$table->addTableRow();
					$table->addTableData("", "data");
						$table->insertAction("state_private", gettext("continue"), sprintf("?mod=address&action=move2public&subaction=keepprivate&id=", $id));
						$table->addSpace();
						$table->insertTag("a", gettext("leave this address private"), array(
							"href" => sprintf("?mod=address&action=move2public&subaction=keepprivate&id=", $id)
						));
					$table->endTableData();
				$table->endTableRow();

				$table->endTable();

				$venster->addTag("br");
				$venster->addTag("br");

				$venster->addCode($table->generate_output());

				$venster->addTag("br");
				$venster->addTag("br");
				$venster->insertAction("close", gettext("cancel"), "javascript: window.close();");
				$venster->endTag("form");

			$venster->endVensterData();

			$output->addCode($venster->generate_output());
			unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function getBCardsXML($id, $search="", $output_buffer=0) {
		require(self::include_dir."dataGetBcardsXML.php");
		return $output->generate_output();
	}



}
?>
