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
	 * @copyright Copyright 2000-2008 Covide BV
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
		 * generate array with classifications
		 *
		 * @param array $options array with classification ids to return
		 * @param int $inactive if set, also return inactive classifications, if not set only return active classifications
		 * @param string $search optional search text to limit result
		 * @param int $cmsonly if set, only return classifications that are exposed to the cms
		 * @return array all relevant info
		 */
		public function getClassifications($options="", $inactive="", $search="", $cmsonly = 0) {

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
			if ($cmsonly) {
				$sq .= " AND is_cms = 1";
			}

			$cla = array();
			$sql = "SELECT * FROM address_classifications $sq ORDER BY is_locked DESC, UPPER(description) ASC";
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
			$address_data = new Address_data;
			$address_cla = $address_data->getAddressIdByClassification($data["id"]);
			$address_ids = explode(",", $data["address_id"]);
			$address_array_ids = array_flip($address_ids);
			$address_array = (count($address_cla) > count($address_array_ids)) ? $address_cla : $address_array_ids;
			foreach ($address_array as $id=>$companyname) {
				$address = $address_data->getAddressById($id);
				$classifications = explode("|", $address["classification"]);
				/* sanitize */
				$classifications[] = $data["id"];
				foreach ($classifications as $k=>$v) {
					if (!$v) {
						unset($classifications[$k]);
					}
				}
				$classifications = array_unique($classifications);
				
				/* If user removed a relation .. */
				if (!in_array($id, $address_ids)) {
					$key = array_search($data["id"], $classifications);
					unset($classifications[$key]);
				}
				$cla = "|".implode("|", $classifications)."|";
				$sql = sprintf("UPDATE address_businesscards SET classification = '%s' WHERE address_id = %d AND rcbc = 1", $cla, $id);
				$res = sql_query($sql);
				unset($classifications);
			}
			if ($data["id"]) {
				/* save altered record */
				$sql = sprintf("UPDATE address_classifications SET access = '%s', access_read = '%s', description='%s', description_full='%s', is_active=%d, subtype=%d, is_cms=%d WHERE id=%d", 
					$data["access"], $data["access_read"], $data["description"], $data["description_full"], $data["is_active"], $data["subtype"], $data["is_cms"], $data["id"]);
			} else {
				/* new record */
				$sql = "INSERT INTO address_classifications (access, access_read, description,  description_full, is_active, subtype, is_cms) VALUES ('%s', '%s', '%s', '%s', %d, %d, %d)";
				$sql = sprintf($sql, $data["access"], $data["access_read"], $data["description"], $data["description_full"], $data["is_active"], $data["subtype"], $data["is_cms"]);
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
		
		/* getSpecialClassification {{{*/
	    /**
			* generate array with classification info
			*
		 	* @param int the classification subtype
	     * @return array all relevant info
	     */
		public function getSpecialClassification($name) {
			$data = array();
			$sql = sprintf("SELECT * FROM address_classifications WHERE is_locked = 1 AND description = '%s'", $name);
			$res = sql_query($sql);
			if (!sql_num_rows($res)) {
				$add_sql = sprintf("INSERT INTO address_classifications (description, is_active, is_locked) 
									VALUES ('%s', 1, 1)", $name);
				$add_res = sql_query($add_sql);
				$data = $this->getSpecialClassification($name);
			} else {
				while ($row = sql_fetch_assoc($res)) {
					$data[] = $row;
				}
			}
			return $data;
		}
		/* }}} */


	}
?>
