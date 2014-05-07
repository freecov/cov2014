<?php
/**
 * Covide Groupware-CRM Mortgage data class
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
Class Mortgage_data {

	/* variables */
	private $pagesize = 20;
	private $_last_query = "";

	/* methods */

	public function __construct() {
		$this->pagesize = $GLOBALS["covide"]->pagesize;
	}

	public function mortGageDelete($id) {
		$q = sprintf("delete from morgage where id = %d", $id);
		sql_query($q);
	}

	public function getMortgageBySearch($options="", $start=0) {
		$data = array();
		$start = (int)$start;

		$address = new Address_data();

		//TODO: fix table name morgage => mortgage
		$like = sql_syntax("like");

		if ($options["id"]) {
			$q_count = sprintf("select count(*) from morgage where id = %d", $options["id"]);
			$q = sprintf("select morgage.*, address.companyname from morgage left join address ON address.id = morgage.address_id where morgage.id = %d", $options["id"]);
		} else {
			$q_count = "select count(*) from morgage";
			$q_total = "select sum(total_sum) as total, sum(expected_score) as score from morgage";
			$q = "";
			/* active/in_active */
			if ($options["in_active"]) {
				$q.= " where (morgage.is_active = 0 or morgage.is_active is null) ";
			} else {
				$q.= " where (morgage.is_active = 1) ";
			}
			/* user_id */
			if ($options["user_id"]) {
				$q.= sprintf(" and morgage.user_id = %d ", $options["user_id"]);
			}
			/* address_id */
			if ($options["address_id"]>0) {
				$q.= sprintf(" and morgage.address_id = %d", $options["address_id"]);
			}
			/* text search */
			if ($options["text"]) {
				$q.= sprintf(" and (subject %1\$s '%%%2\$s%%' or morgage.description %1\$s '%%%2\$s%%') ", $like, $options["text"]);
			}
			$q_count.=$q;

			$q = "select morgage.*, address.companyname, users.username from morgage left join address ON address.id = morgage.address_id left join users ON users.id = morgage.user_id ".$q;

			if (!$options["sort"]) {
				$q.= " order by timestamp desc";
			} else {
				$q.= " order by ".sql_filter_col($options["sort"]);
			}
		}

		$res = sql_query($q_count);
		$this->_last_query = $q;

		$data["count"] = sql_result($res,0);

		$res = sql_query($q, "", $start, $this->pagesize);
		while ($row = sql_fetch_assoc($res)) {
			$row["h_address"] = $address->getAddressNameById($row["address_id"]);

			if ($row["timestamp"]) {
				$row["h_timestamp"] = date("d-m-Y", $row["timestamp"]);
			} else {
				$row["h_timestamp"] = "--";
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
			//TODO: typo in db schema
			$row["year_payment"] =& $row["year_payement"];
			$row["year_payment_h"] = "&euro; ".number_format($row["year_payement"],2);
			switch ($row["type"]) {
				case 0: $row["h_type"] = gettext("mortgage"); break;
				case 1: $row["h_type"] = gettext("lifeinsurance"); break;
			}

			$row = $this->addProvisionField($row);
			$data["data"][]=$row;

		}
		return $data;
	}

	public function addProvisionField($row) {
		$pperc = 0;
		if ($row["investor"] == 1) {
			$q = "select provision_perc from address_info where address_id = ".(int)$row["investor"];
			$res2 = sql_query($q);
			if (sql_num_rows($res2)>0) {
				$pperc = sql_result($res2,0);
			} else {
				$pperc = 0;
			}
		} else {
			//if hypotheekverstrekker
			$q = "select provision_perc from address_info where address_id = ".(int)$row["insurancer"];
			$res2 = sql_query($q);
			if (sql_num_rows($res2)>0) {
				$pperc = sql_result($res2,0);
			} else {
				$pperc = 0;
			}
		}
		$row["provision"] = number_format(($pperc / 100 * $row["orig_sum"]), 2);
		$row["provision_h"] = "&euro ".$row["provision"];
		return $row;
	}

	public function getTotals() {
		$totals = array();
		$q = $this->_last_query;
		if ($q) {
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				$row["orig_sum"] = $row["total_sum"];
				$row = $this->addProvisionField($row);
				$totals["sum"][] = $row["total_sum"];
				$totals["provision"][] = $row["provision"];

			}
			$totals["count"]     = count($totals["sum"]);
			if (is_array($totals["sum"])) {
				$totals["sum"]       = array_sum($totals["sum"]);
				$totals["provision"] = array_sum($totals["provision"]);
			} else {
				$totals["sum"]       = 0;
				$totals["provision"] = 0;
			}

			$totals["sum"]       = number_format($totals["sum"], 2,",",".");
			$totals["provision"] = number_format($totals["provision"], 2,",",".");
		}

		return $totals;
	}


	public function getMortgageById($id) {
		if ($id) {
			return $this->getMortgageBySearch(array("id"=>$id));
		} else {
			$array["data"][0]["is_active"]=1;
			$array["data"][0]["user_sales_id"]=$_SESSION["user_id"];
			return $array;
		}
	}

	public function getMortgageAddresses($type) {
		$classification_data = new Classification_data();
		$cla = $classification_data->getClassificationByType($type);

		if (is_array($cla)) {
			$ids = array();
			foreach ($cla as $c) {
				$ids[] = $c["id"];
			}
			$options["classifications"]["positive"] = implode("|", $ids);
			$options["selectiontype"] = "or";
			$options["nolimit"] = 1;
			$options["addresstype"] = "relations";

			$address_data = new Address_data();
			$list = $address_data->getRelationsList($options);

			$data = array();
			foreach ($list["address"] as $v) {
				$data[$v["id"]] = $v["companyname"];
			}
			natcasesort($data);
		}
		return $data;
	}

	public function saveItem() {
		$mortgage = $_REQUEST["mortgage"];
		$id    = $_REQUEST["id"];

		$fields["subject"]        = array("s",$mortgage["subject"]);
		$fields["description"]    = array("s",$mortgage["description"]);
		$fields["address_id"]     = array("d",$mortgage["address_id"]);
		$fields["is_active"]      = array("d",$mortgage["is_active"]);
		$fields["user_id"]        = array("d",$mortgage["user_id"]);
		$fields["type"]           = array("d",$mortgage["type"]);

		//TODO: typo in db schema
		$mortgage["year_payment"] = $mortgage["year_payement"];
		$fields["year_payement"]  = array("d",$mortgage["year_payment"]);
		$fields["total_sum"]      = array("d",$mortgage["total_sum"]);
		$fields["investor"]       = array("d",$mortgage["investor"]);
		$fields["insurancer"]     = array("d",$mortgage["insurancer"]);

		if ($mortgage["timestamp_day"] && $mortgage["timestamp_month"] && $mortgage["timestamp_year"]) {
			$time = mktime(0,0,0, $mortgage["timestamp_month"], $mortgage["timestamp_day"], $mortgage["timestamp_year"]);
		} else {
			$time = 0;
		}
		$fields["timestamp"] = array("d", $time);

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

			$q = sprintf("insert into morgage (%s) values (%s)", $keys, $vals);
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
			$q = "update morgage set user_src = ".$_SESSION["user_id"];
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
