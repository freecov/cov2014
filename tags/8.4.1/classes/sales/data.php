<?php
/**
 * Covide Groupware-CRM Sales data class
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
Class Sales_data {

	/* variables */
	private $pagesize = 20;
	private $_last_query = "";

	/* methods */

	public function __construct() {
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}

	public function salesDelete($id) {
		$q = sprintf("delete from sales where id = %d", $id);
		sql_query($q);
	}

	public function getSalesBySearch($options="", $start=0) { 
		$data = array();
		$start = (int)$start;

		$user_data = new User_data();
		$user_perm = $user_data->getUserdetailsById($_SESSION["user_id"]);
		$address = new Address_data();
		$project = new Project_data();

		$timestamp_fields = array(
			"proposal",
			"order",
			"invoice",
			"prospect"
		);
		/* prepare a basic permissions subquery if this user is not a global sales manager */
		if (!$user_perm["xs_salesmanage"]) {
			$groups = $user_data->getUserGroups($_SESSION["user_id"]);
			if (count($groups) > 0) {
				$regex_syntax = sql_syntax("regex");
				$sq = " AND ( 1=0 ";
				foreach ($groups as $g) {
					$g = "G".$g;
					$sq.= " OR sales.users ".$regex_syntax." '(^|\\\\,)". $g."(\\\\,|$)' ";
				}
				$sq.= " OR sales.users ".$regex_syntax." '(^|\\\\,)". (int)$_SESSION["user_id"]."(\\\\,|$)' ";
				$sq.= sprintf(" OR sales.user_sales_id = %d ", $_SESSION["user_id"]);
				$sq.= ") ";
			}
		}
		
		$like = sql_syntax("like");

		if ($options["id"]) {
			$q_count = sprintf("select count(*) from sales where id = %d", $options["id"]);
			$q = sprintf("select sales.*, address.companyname from sales left join address ON address.id = sales.address_id where sales.id = %d", $options["id"]);
		} else {
			$q_count = "select count(*) from sales";
			$q_total = "select sum(total_sum) as total, sum(expected_score) as score from sales";
			$q = "";
			/* active/in_active */
			if ($options["in_active"]) {
				$q.= " where (sales.is_active = 0 or sales.is_active is null) ";
			} else {
				$q.= " where (sales.is_active = 1) ";
			}
			/* user_id */
			if ($options["user_id"]) {
				$q.= sprintf(" and user_sales_id = %d ", $options["user_id"]);
			}
			/* address_id */
			if ($options["address_id"]>0) {
				$regex_syntax = sql_syntax("regex");
				$q.= sprintf(" and (sales.address_id = %1\$d OR sales.multirel ".$regex_syntax." '(^|\\\\,)%1\$d(\\\\,|$)')", $options["address_id"]);
			}
			/* projectid */
			if ($options["project_id"]>0) {
				$q.= sprintf(" and sales.project_id = %d", $options["project_id"]);
			}
			/* classifications */
			if ($options["classification"]) {
				$regex_syntax = sql_syntax("regex");
				$regex_op = " and (";
				$classifications = explode("|", substr(substr($options["classification"], 1, strlen($options["classification"])), 0, -1));
				foreach ($classifications as $cla) {
					$q.= $regex_op." (sales.classification ".$regex_syntax." '(^|\\\\|)". $cla ."(\\\\||$)') ";
					$regex_op = "or";
				}
				$q.= ") ";
			}
			/* Add users permission query */
			if ($sq) {
				$q .= $sq;
			}
			
			/* text search */
			if ($options["text"]) {
				$q.= sprintf(" and (subject %1\$s '%%%2\$s%%' or sales.description %1\$s '%%%2\$s%%') ", $like, $options["text"]);
			}
			$q_count.=$q;

			$q = "select sales.*, address.companyname, users.username, project.name AS h_project from sales left join address ON address.id = sales.address_id left join users ON users.id = sales.user_sales_id left join project ON project.id = sales.project_id ".$q;

			if (!$options["sort"]) {
				$q.= " order by total_sum desc";
			} else {
				$q.= " order by ".sql_filter_col($options["sort"]);
			}
		}
		$res = sql_query($q_count);
		$data["count"] = sql_result($res,0);

		$res = sql_query($q, "", $start, $this->pagesize);
		$this->_last_query = $q;

		while ($row = sql_fetch_assoc($res)) {
			if($row["project_id"] || $row["project_id"] != 0) { $row["has_project"] = 1; }
			if($row["address_id"] || $row["address_id"] != 0) { $row["has_address"] = 1; }
			$row["h_address"] = $row["companyname"];
			
			foreach ($timestamp_fields as $ts) {
				if ($row["timestamp_".$ts]) {
					$row["h_timestamp_".$ts] = date("d-m-Y", $row["timestamp_".$ts]);
				} else {
					$row["h_timestamp_".$ts] = "--";
				}
			}
			/* put currency in normal notation */
			/* the en_US is different */
			/* TODO: this should be done a better way */
			$row["orig_sum"] = $row["total_sum"];
			if ($_SESSION["language"] != "en_US") {
				$row["total_sum"] = "&euro; ".number_format($row["total_sum"],2,",",".");
			} else {
				$row["total_sum"] = "&euro; ".number_format($row["total_sum"],2,".");
			}
			
			/* Handle if user can perform actions (such as edit or delete) */
			if (!$user_perm["xs_salesmanage"]) {
				$row["check_actions"] = 0;
				/* User has no rights, maybe users has assigned items */
				if ($_SESSION["user_id"] == $row["user_sales_id"]) {
					$row["check_actions"] = 1;
				}
			} else {
				/* User has global sales permssions always allow actions */
				$row["check_actions"] = 1;
			}
			
			/* Handle multirel data */
			if ($row["multirel"]) {
				$multirels = explode(",", $row["multirel"]);
				foreach ($multirels as $rel) {
					$multicompany[] = $address->getAddressNameById($rel);
				}
				$row["all_address_ids"] = $row["address_id"].','.$row["multirel"];
				$row["all_address_names"] = $row["companyname"].", ".implode(", ", $multicompany);
				/* Unsetting the company names, important */
				unset($multicompany);
			} else {
				$row["all_address_ids"] = $row["address_id"];
				$row["all_address_names"] = $row["companyname"];
			}
			
			$data["data"][] = $row;
		}
		return $data;
	}

	public function getTotals() {
		$totals = array();
		$q = $this->_last_query;
		if ($q) {
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$totals["score"][]   = $row["expected_score"];
				$totals["sum"][]     = $row["total_sum"];
				$totals["average"][] = ($row["total_sum"] * $row["expected_score"]/100);
			}
			if ($totals) {
				$totals["score"]   = (array_sum($totals["score"]) / count($totals["score"]));
				$totals["sum"]     = array_sum($totals["sum"]);
				$totals["average"] = array_sum($totals["average"]);

				$totals["average"] = number_format($totals["average"],2,",",".");
				$totals["score"]   = (int)$totals["score"];
				$totals["sum"]     = number_format($totals["sum"],2,",",".");
			}
		}
		return $totals;
	}

	public function getSalesById($id, $address_id = 0) {
		if ($id) {
			return $this->getSalesBySearch(array("id"=>$id));
		} else {
			$array["data"][0]["is_active"]=1;
			$array["data"][0]["user_sales_id"] = $_SESSION["user_id"];
			$array["data"][0]["address_id"]    = $address_id;
			return $array;
		}
	}

	public function saveItem() {
		$sales = $_REQUEST["sales"];
		$id    = $_REQUEST["id"];

		$timestamp_fields = array(
			"proposal",
			"order",
			"invoice",
			"prospect"
		);

		/* get address_id's into an array */
		$addresses = explode(",", $sales["address_id"]);
		/* strip empty items */
		$addresses = array_unique($addresses);
		foreach ($addresses as $k=>$v) {
			if (!$v) {
				unset($addresses[$k]);
			}
		}
		sort($addresses);
		/* put first id we find in address_id. If more are found set multirel to the remaining values */
		$sales["address_id"] = $addresses[0];
		if (count($addresses) > 1) {
			unset($addresses[0]);
			$sales["multirel"] = implode(",", $addresses);
		}

		$sales["expected_score"] = (int)$sales["expected_score"];
		if ($sales["expected_score"] > 100)
			$sales["expected_score"] = 100;
		elseif ($sales["expected_score"] < 0)
			$sales["expected_score"] = 0;


		$fields["subject"]        = array("s",$sales["subject"]);
		$fields["description"]    = array("s",$sales["description"]);
		$fields["address_id"]     = array("d",$sales["address_id"]);
		$fields["project_id"]     = array("d",$sales["project_id"]);
		$fields["is_active"]      = array("d",$sales["is_active"]);
		$fields["user_sales_id"]  = array("d",$sales["user_sales_id"]);
		$fields["expected_score"] = array("d",$sales["expected_score"]);
		$fields["total_sum"]      = array("d",$sales["total_sum"]);
		$fields["classification"] = array("s",$sales["classification"]);
		$fields["users"]          = array("s",$sales["users"]);
		$fields["multirel"]       = array("s",$sales["multirel"]);

		foreach ($timestamp_fields as $ts) {
			if ($sales["timestamp_".$ts."_day"] && $sales["timestamp_".$ts."_month"] && $sales["timestamp_".$ts."_year"]) {
				$time = mktime(0,0,0, $sales["timestamp_".$ts."_month"], $sales["timestamp_".$ts."_day"], $sales["timestamp_".$ts."_year"]);
			} else {
				$time = 0;
			}
			$fields["timestamp_".$ts] = array("d", $time);
		}

		if (!$id) {
			$keys = array();
			$vals = array();
			foreach ($fields as $k=>$v) {
				$keys[] = $k;
				if ($v[0]=="s") {
					$vals[]="'".addslashes( $v[1] )."'";
				} else {
					$vals[]=(int)$v[1];
				}
			}
			$keys = implode(",",$keys);
			$vals = implode(",",$vals);

			$q = sprintf("insert into sales (%s) values (%s)", $keys, $vals);
			sql_query($q);

		} else {

			$vals = array();
			foreach ($fields as $k=>$v) {
				if ($v[0]=="s") {
					$vals[$k]="'".addslashes( $v[1] )."'";
				} else {
					$vals[$k]=(int)$v[1];
				}
			}
			$q = "update sales set user_id_modified = ".$_SESSION["user_id"];
			foreach ($vals as $k=>$v) {
				$q.= sprintf(", %s = %s ", $k, $v);
			}
			$q.= sprintf(" where id = %d", $id);
			sql_query($q);

		}
		echo "<script>opener.document.getElementById('velden').submit(); window.close();</script>";
	}
}
?>
