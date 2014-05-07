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

		$address = new Address_data();

		$timestamp_fields = array(
			"proposal",
			"order",
			"invoice",
			"prospect"
		);

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
				$q.= sprintf(" and sales.address_id = %d", $options["address_id"]);
			}
			/* text search */
			if ($options["text"]) {
				$q.= sprintf(" and (subject %1\$s '%%%2\$s%%' or sales.description %1\$s '%%%2\$s%%') ", $like, $options["text"]);
			}
			$q_count.=$q;

			$q = "select sales.*, address.companyname, users.username from sales left join address ON address.id = sales.address_id left join users ON users.id = sales.user_sales_id ".$q;

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
			$row["h_address"] = $address->getAddressNameById($row["address_id"]);

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
			$data["data"][]=$row;

		}
		return $data;
	}

	public function getTotals() {
		$totals = array();
		$q = $this->_last_query;
		if ($q) {
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$totals["score"][] = $row["expected_score"];
				$totals["sum"][] = $row["total_sum"];
			}
			$totals["count"]   = count($totals["sum"]);
			if (count($totals["sum"]) > 0 && is_array($totals["score"])) {
				$totals["score"]   = (array_sum($totals["score"]) / count($totals["sum"]));
			} else {
				$totals["score"]   = 0;
			}
			if (is_array($totals["sum"])) {
				$totals["sum"]     = array_sum($totals["sum"]);
			} else {
				$totals["sum"]     = 0;
			}
			$totals["average"] = number_format($totals["score"] / 100 * $totals["sum"],2,",",".");
			$totals["score"]   = (int)$totals["score"];
			$totals["sum"]     = number_format($totals["sum"],2,",",".");
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

		$fields["subject"]        = array("s",$sales["subject"]);
		$fields["description"]    = array("s",$sales["description"]);
		$fields["address_id"]     = array("d",$sales["address_id"]);
		$fields["is_active"]      = array("d",$sales["is_active"]);
		$fields["user_sales_id"]  = array("d",$sales["user_sales_id"]);
		$fields["expected_score"] = array("d",$sales["expected_score"]);
		$fields["total_sum"]      = array("d",$sales["total_sum"]);

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
