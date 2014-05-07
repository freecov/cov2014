<?php
	/**
	 * Covide Groupware-CRM Classification_data
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2006 Covide BV
	 * @package Covide
	 */
	Class Classification_data {
		/* constants */
		/**
		 * @const string the class include dir
		 */
		const include_dir = "classes/classification/inc/";
		const classname   = "classification";

		/* methods */

		/* getClassifications {{{*/
	    /**
	     * 	generate array with classifications
		 *
		 * If $options in given a user can search in it
		 *
		 * @param array search key etc.
	     * @return array all relevant info
	     */
		public function getClassifications($options="", $inactive="", $search="") {

			$like = sql_syntax("like");

			if ($inactive) {
				$sq.= " where (is_active=1 or is_active=0) ";
			} else {
				$sq.= " where (is_active=1) ";
			}
			if ($search) {
				$sq.= sprintf(" and description %s '%%%s%%' ", $like, $search);
			}
			if (is_array($options)) {
				$sq.= sprintf(" and id IN (%s)", implode(",", $options));
			}

			$cla = array();
			$sql = "SELECT * FROM address_classifications $sq ORDER BY UPPER(description)";
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$row["description"] = trim($row["description"]);
				switch ($row["subtype"]) {
					case 1: $row["h_subtype"] = gettext("geldverstrekker"); break;
					case 2: $row["h_subtype"] = gettext("verzekeraar");     break;
				}
				if ($row["is_active"])
					$row["is_nonactive"] = 0;
				else
					$row["is_nonactive"] = 1;
				$cla[] = $row;
			}
			return $cla;
		}
		/* }}} */

		/* getClassificationById {{{*/
	    /**
	     * 	generate array with classification info
		 *
		 * @param int the classification id
	     * @return array all relevant info
	     */
		public function getClassificationById($cla_id=0) {
			$sql = sprintf("SELECT * FROM address_classifications WHERE id=%d", $cla_id);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			return $row;
		}
		/* }}} */

		/* getClassificationByType {{{*/
	    /**
	     * 	generate array with classification info
		 *
		 * @param int the classification subtype
	     * @return array all relevant info
	     */
		public function getClassificationByType($type) {
			$data = array();
			$sql = sprintf("SELECT * FROM address_classifications WHERE subtype=%d", $type);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$data[] = $row;
			}
			return $data;
		}
		/* }}} */

		/* store2db {{{ */
		public function store2db($data) {
			if ($data["id"]) {
				/* save altered record */
				$sql = sprintf("UPDATE address_classifications SET description='%s', is_active=%d, subtype=%d WHERE id=%d", $data["description"], $data["is_active"], $data["subtype"], $data["id"]);
			} else {
				/* new record */
				$sql = "INSERT INTO address_classifications (description, is_active, subtype) VALUES ('%s', %d, %d)";
				$sql = sprintf($sql, $data["description"], $data["is_active"], $data["subtype"]);
			}
			$res = sql_query($sql);
			if ($data["id"])
				return $data["id"];
			else
				return sql_insert_id("address_classifications");
		}
		/* }}} */

		/* removecla {{{ */
		public function removecla($claid) {
			$sql = sprintf("DELETE FROM address_classifications WHERE id=%d", $claid);
			$res = sql_query($sql);
			header("Location: index.php?mod=classification&action=show_classifications");
		}
		/* }}} */


	}
?>
