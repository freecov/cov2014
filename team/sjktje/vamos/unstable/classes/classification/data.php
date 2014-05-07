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
	 * @copyright Copyright 2000-2007 Covide BV
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
					case 1: $row["h_subtype"] = gettext("loan"); break;
					case 2: $row["h_subtype"] = gettext("insurance company");     break;
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

		public function getClassificationByAccess($only_rw=0) {
			$data = array();

			$userdata = new User_data();
			$user_perm = $userdata->getUserdetailsById($_SESSION["user_id"]);
			if (!$user_perm["xs_addressmanage"]) {
				$groups = $userdata->getUserGroups($_SESSION["user_id"]);
				if (count($groups) > 0) {
					$regex_syntax = sql_syntax("regex");
					$sq = " AND ( 1=0 ";
					foreach ($groups as $g) {
						$g = "G".$g;
						$sq.= " OR access ".$regex_syntax." '(^|\\\\,)". $g."(\\\\,|$)' ";
						if (!$only_rw)
							$sq.= " OR access_read ".$regex_syntax." '(^|\\\\,)". $g."(\\\\,|$)' ";
					}
					$sq.= " OR access ".$regex_syntax." '(^|\\\\,)". (int)$_SESSION["user_id"]."(\\\\,|$)' ";
					if (!$only_rw)
						$sq.= " OR access_read ".$regex_syntax." '(^|\\\\,)". (int)$_SESSION["user_id"]."(\\\\,|$)' ";
					$sq.= ") ";
				}
			}

			$sql = sprintf("SELECT id FROM address_classifications WHERE 1=1 %s", $sq);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$data[] = $row["id"];
			}
			return $data;
		}

		/* store2db {{{ */
		public function store2db($data) {
			if ($data["id"]) {
				/* save altered record */
				$sql = sprintf("UPDATE address_classifications SET access = '%s', access_read = '%s', description='%s', description_full='%s', is_active=%d, subtype=%d WHERE id=%d", $data["access"], $data["access_read"], $data["description"], $data["description_full"], $data["is_active"], $data["subtype"], $data["id"]);
			} else {
				/* new record */
				$sql = "INSERT INTO address_classifications (access, access_read, description,  description_full, is_active, subtype) VALUES ('%s', '%s', '%s', '%s', %d, %d)";
				$sql = sprintf($sql, $data["access"], $data["access_read"], $data["description"], $data["description_full"], $data["is_active"], $data["subtype"]);
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
