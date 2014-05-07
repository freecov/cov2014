<?php
/**
 * Covide Finance module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Finance_data {
	/* constants */
	const include_dir = "classes/finance/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name  = "finance";

	public $grootboeknummer = array();

	/* function __construct {{{  */
	public function __construct($skip_user_check=0) {

		if (!$skip_user_check) {
			$userdata = new User_data();
			$userdata->getUserPermissionsById($_SESSION["user_id"]);
			if (!$userdata->checkPermission("xs_turnovermanage")) {
				die("access denied!");
			}
		}
		//TODO this is customer specific at the moment
		$this->grootboeknummer["bank"]        = 59;
		$this->grootboeknummer["kas"]         = 57;
		$this->grootboeknummer["memoriaal"]   = 104;
		$this->grootboeknummer["debiteuren"]  = 66;
		$this->grootboeknummer["crediteuren"] = 89;
		$this->grootboeknummer["btw"]         = 76;
	}
	/* }}} */

	public function formatCurrency($float, $output_numeric=0, $output_factuur=0) {
		if (!$output_numeric) {
			$float = number_format($float, 2 ,"," ,".");
			if ($output_factuur) {
				$float = sprintf("&euro;&nbsp;%s", $float);
			} else {
				$o = new Layout_table(array(
					"cellspacing" => 0,
					"cellpadding" => 0,
					"style"       => "width: 100%; margin: 0 2px;"
				));
				$o->addTableRow();
				$o->insertTableData($float, array(
					"style" => "text-align: right; width: 100%;"

				));
				$o->insertTableData("&nbsp;&euro;&nbsp;");
				$o->endTableData();
				$o->endTableRow();
				$o->endTable();
				$float = trim(str_replace("\n", "", $o->generate_output()));
			}
		} else {
			$float = number_format($float, 2 ,"." ,"");
		}
		return $float;
	}
	public function getWelkomData() {
		$data = array();

		/* bankboek, kas en memoriaal */
		$ary = array(
			$this->grootboeknummer["bank"],
			$this->grootboeknummer["kas"],
			$this->grootboeknummer["memoriaal"]
		);
		foreach ($ary as $a) {
			$q = sprintf("select stand from finance_begin_standen_finance where grootboek_id in (
				select nr from finance_grootboeknummers where id = %d)", $a);
			$res = sql_query($q);
			if (sql_num_rows($res) > 0)
				$stand = sql_result($res,0);
			else
				$stand = 0;

			/* debet */
			$q = sprintf("select sum(bedrag) as totaal from finance_boekingen
				where grootboek_id = %d and credit = 0", $a);
			$res = sql_query($q);
			$_debet = sql_result($res,0);

			/* credit */
			$q = sprintf("select sum(bedrag) as totaal from finance_boekingen
				where grootboek_id = %d and credit = 1", $a);
			$res = sql_query($q);
			$_credit = sql_result($res,0);

			$data["standen"][$a]["titel"] = $this->getGrootboekNameById($a);
			$data["standen"][$a]["bedrag"] = $this->formatCurrency($stand + ($_debet- $_credit));
		}
		/* bankflow, kasflow, verkopen, inkopen, btw */
		$month["this"]["start"]  = mktime(0,0,0,date("m"), 1, date("Y"));
		$month["this"]["end"]    = mktime(0,0,0,date("m")+1, 1, date("Y"));

		$month["prev"]["start"]  = mktime(0,0,0,date("m")-1, 1, date("Y"));
		$month["prev"]["end"]    = mktime(23,59,59,date("m"), 0, date("Y"));

		$month["prev2"]["start"] = mktime(0,0,0,date("m")-2, 1, date("Y"));
		$month["prev2"]["end"]   = mktime(23,59,59,date("m")-1, 0, date("Y"));

		$month["prev3"]["start"] = mktime(0,0,0,date("m")-3, 1, date("Y"));
		$month["prev3"]["end"]   = mktime(23,59,59,date("m")-2, 0, date("Y"));

		$ary = array(
			"Bankflow" => sprintf(" grootboek_id = %d ", $this->grootboeknummer["bank"]),
			"Kasflow"  => sprintf(" grootboek_id = %d ", $this->grootboeknummer["kas"]),
			"Verkopen" => sprintf(" product > 0 "),
			"Inkopen"  => sprintf(" inkoop = 1 and grootboek_id = %d ", $this->grootboeknummer["crediteuren"]),
			"Btw"      => sprintf(" grootboek_id = %d ", $this->grootboeknummer["btw"])
		);
		foreach ($ary as $name=>$a) {
			foreach ($month as $k=>$v) {
				$q = sprintf("select sum(bedrag) as totaal from finance_boekingen
					where datum between %d and %d and %s and credit = 0",
					$v["start"], $v["end"], $a);
				#echo $q."<BR>";

				$res = sql_query($q);
				$_debet = sql_result($res,0);

				$q = sprintf("select sum(bedrag) as totaal from finance_boekingen
					where datum between %d and %d and %s and credit = 1",
					$v["start"], $v["end"], $a);
				#echo $q."<BR>";

				$res = sql_query($q);
				$_credit = sql_result($res,0);

				/* switch debiteuren */
				if ($name == "Verkopen")
					$_credit = 0-$_credit;

				$data["flow"][$k][$name] = $this->formatCurrency($_debet - $_credit);
			}
		}
		$address_data = new Address_data();

		/* oudste posten - verkopen */
		$q = sprintf("select (sum(rekeningflow)-sum(bedrag_betaald)) as totaal
			from finance_omzet_akties where datum > %d", $GLOBALS["covide"]->license["finance_start_date"]);
		$res = sql_query($q);
		$data["openstaand"]["verkopen"]["totaal"] = $this->formatCurrency(sql_result($res,0));

		$q = sprintf("select count(*) as aantal from finance_omzet_akties
			where rekeningflow != bedrag_betaald and datum > %d", $GLOBALS["covide"]->license["finance_start_date"]);
		$res = sql_query($q);
		$data["openstaand"]["verkopen"]["count"] = sql_result($res,0)."x";

		if ($GLOBALS["covide"]->license["has_finance"])
			$cnt = 5;
		else
			$cnt = 20;

		$q = sprintf("select * from finance_omzet_akties
			where rekeningflow != bedrag_betaald
			and datum > %d
			order by datum", $GLOBALS["covide"]->license["finance_start_date"]);
		/*
		$dagen = $this->getDagen();

		$items = array();
		$address_data = new Address_data();

		$q = sprintf("select * from finance_omzet_akties where datum <= %1\$d and datum >= %2\$d and bedrag_betaald != rekeningflow order by datum, factuur_nr",
			mktime(0,0,0,date("m"),date("d")-(int)$dagen, date("Y")),
			$GLOBALS["covide"]->license["finance_start_date"]
		);
		*/
		$res = sql_query($q, "", 0, $cnt);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["address_id"]) {
				$q = sprintf("select id from address where debtor_nr = %d", $row["address_id"]);
				$res2 = sql_query($q);
				$row["address_id"] = sql_result($res2,0);
			}

			$data["openstaand"]["verkopen"]["laatste"][$row["factuur_nr"]] = array(
				"datum"   => date("d-m-Y", $row["datum"]),
				"descr"   => $row["omschrijving"],
				"bedrag"  => $this->formatCurrency($row["rekeningflow"]),
				"factuur" => $row["factuur_nr"],
				"address_name" => ($row["address_id"])?$address_data->getAddressNameById($row["address_id"]):"none",
				"address_id"   => $row["address_id"]
			);
		}
		if ($GLOBALS["covide"]->license["has_finance"]) {
			/* oudste posten - inkopen */
			$q = sprintf("select (sum(bedrag_inc)-sum(betaald)) as totaal
				from finance_inkopen where datum > %d", $GLOBALS["covide"]->license["finance_start_date"]);
			$res = sql_query($q);
			$data["openstaand"]["inkopen"]["totaal"] = $this->formatCurrency(sql_result($res,0));

			$q = sprintf("select count(id) as aantal from finance_inkopen
				where bedrag_inc != betaald and datum > %d", $GLOBALS["covide"]->license["finance_start_date"]);
			$res = sql_query($q);
			$data["openstaand"]["inkopen"]["count"] = sql_result($res,0)."x";

			$q = sprintf("select * from finance_inkopen
				where bedrag_inc != betaald and datum > %d order by datum", $GLOBALS["covide"]->license["finance_start_date"]);
			$res = sql_query($q, "", 0, 5);
			while ($row = sql_fetch_assoc($res)) {
				$data["openstaand"]["inkopen"]["laatste"][$row["boekstuknr"]] = array(
					"datum"   => date("d-m-Y", $row["datum"]),
					"descr"   => $row["descr"],
					"bedrag"  => $this->formatCurrency($row["bedrag_inc"]),
					"factuur" => $row["boekstuknr"],
					"address_name" => $address_data->getAddressNameById($row["leverancier_nr"]),
					"address_id"   => $row["leverancier_nr"]
				);
			}
		}
		return $data;
	}

	public function getGrootboekNameById($id, $two_lines_view=0) {
		$q = sprintf("select nr, titel from finance_grootboeknummers where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
		if ($two_lines_view)
			return sprintf("%s:\n%s", $row["nr"], $row["titel"]);
		else
			return sprintf("%s - %s", $row["nr"], $row["titel"]);
	}

	public function getSpecialeBoekingen($options = array()) {
		if (!$options["year"])  $options["year"] = date("Y");
		if (!$options["month"]) $options["month"] = date("m");

		if ($options["month"] > 0) {
			$date["start"] = mktime(0,0,0,$options["month"],1,$options["year"]);
			$date["end"]   = mktime(0,0,0,$options["month"]+1,1,$options["year"]);
		} else {
			$date["start"] = mktime(0,0,0,1,1,$options["year"]);
			$date["end"]   = mktime(0,0,0,1,1,$options["year"]+1);
		}
		if ($options["search"]) {
			$sq = sprintf(" and (omschrijving like '%%%1\$s%%' or bedrag like '%%%1\$s') ",
				$options["search"]);
		}
		$data = array();
		$address_data = new Address_data();

		$q = sprintf("select * from finance_overige_posten where datum >= %d and datum <= %d %s order by datum",
			$date["start"], $date["end"], $sq);
		#echo $q;

		$years = $this->getLockedYears();

		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["address_name"]       = $address_data->getAddressNameById($row["debiteur"]);
			$row["grootboek_rekening"] = $this->getGrootboekNameById($row["grootboek_id"]);
			$row["tegen_rekening"]     = $this->getGrootboekNameById($row["tegenrekening"]);
			$row["datum_h"]            = date("d-m-Y", $row["datum"]);
			$row["bedrag_h"]           = $this->formatCurrency($row["bedrag"]);

			if (in_array(date("Y", $row["datum"]), $years))
				$row["is_locked"]        = 1;
			else
				$row["allow_delete"]     = 1;

			$data[] = $row;
		}
		return $data;
	}
	public function getFirstRecordDate() {
		$q = "select min(datum) from finance_boekingen";
		$res = sql_query($q);
		if (sql_num_rows($res) == 0)
			return date("Y");
		else
			return date("Y", sprintf("%d", sql_result($res,0)));
	}
	public function getLockedYears() {
		$data = array();
		$q = "select jaar from finance_jaar_afsluitingen order by jaar";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$data[] = $row["jaar"];
		}
		return $data;
	}
	public function deleteSpeciaal($id) {
		//controle
		$q = sprintf("select count(*) as aantal from finance_boekingen where koppel_id = %d and status = 5 and inkoop = 2", $id);
		$res = sql_query($q);
		if (sql_result($res,0) == 2) {
			//delete from boekingen
			$sql = sprintf("delete from finance_boekingen where koppel_id = %d and status = 5 and inkoop = 2", $id);
			sql_query($sql);

			//delete from overige posten
			$sql = sprintf("delete from finance_overige_posten where id = %d", $id);
			sql_query($sql);
		}
	}
	public function getGrootboekRekening($id=0) {
	}

	public function autocomplete($str) {
		$q = sprintf("select * from finance_grootboeknummers where nr like '%%%1\$s%%'
			or titel like '%%%1\$s%%' order by nr", $str);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			echo sprintf("%d|%s - %s\n", $row["id"], $row["nr"], $row["titel"]);
		}
		exit();
	}

	public function getRelationCardData($address_id, $history=0) {
		if ($history)
			$ts = 0;
		else
			$ts = mktime(0,0,0,date("m"),date("d"),date("Y")-1);

		$ts2  = 0;
		$data = array();

		$address_data = new Address_data();

		$address_info = $address_data->getAddressById($address_id);

		// speciale boekingen
		/*
		$q = sprintf("select * from finance_overige_posten where datum >= %d and debiteur = %d order by datum desc",
			$ts2, $address_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["grootboek_van"]  = $this->getGrootboekNameById($row["grootboek_id"], 1);
			$row["grootboek_naar"] = $this->getGrootboekNameById($row["tegenrekening"], 1);
			$row["datum_h"]        = strftime("%d-%m-%Y", $row["datum"]);
			$row["bedrag_h"]       = $this->formatCurrency($row["bedrag"]);
			$data["speciaal"][] = $row;
		}

		// inkopen
		$q = sprintf("select * from finance_inkopen where datum >= %d and leverancier_nr = %d order by datum desc",
			$ts2, $address_id);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["datum_h"]    = date("d-m-Y", $row["datum"]);
			$row["bedrag_h"]   = $this->formatCurrency($row["bedrag_inc"]);
			$row["betaald_1"]  = ($row["bedrag_inc"] == $row["betaald"]) ? 1:0;
			$row["betaald_0"]  = ($row["bedrag_inc"] != $row["betaald"]) ? 1:0;
			$data["inkopen"][] = $row;
		}

		// verkopen totalen
		$q = sprintf("SELECT SUM(rekeningflow) FROM finance_omzet_akties where address_id = %d",
			$address_info["debtor_nr"]);
		$res = sql_query($q);
		$data["verkopen"]["totaal"] = sql_result($res,0);

		$q = sprintf("SELECT SUM(rekeningflow) FROM finance_omzet_akties where address_id= %d AND datum >= %d",
			$address_info["debtor_nr"], $ts);
		$res = sql_query($q);
		$data["verkopen"]["laatste_jaar"] = sql_result($res,0);
		*/

		$q = sprintf("SELECT * FROM finance_omzet_akties
			WHERE address_id = %d and datum >= %d ORDER BY datum DESC",
			$address_info["debtor_nr"], $ts);

		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$q = sprintf("select definitief_2, definitief_3 from finance_offertes where
				factuur_nr_2 = %1\$d or factuur_nr_3 = %1\$d", $row["factuur_nr"]);
			$res2 = sql_query($q);
			while ($row2 = sql_fetch_assoc($res2)) {
				if (($row2["definitief_2"] == 1 && $row2["factuur_nr_2"] == $row["factuur_nr"])
					|| ($row["definitief_3"] == 1 && $row2["factuur_nr_3"] == $row["factuur_nr"])) {
					$row["definitief_1"] = 1;
				} else {
					$row["definitief_0"] = 1;
				}
			}
			$row["datum_h"]  = date("d-m-Y", $row["datum"]);
			$row["bedrag_h"] = $this->formatCurrency($row["rekeningflow"]);
			if ($row["rekeningflow"] == $row["bedrag_betaald"])
				$row["betaald_1"] = 1;
			else
				$row["betaald_0"] = 1;

			$data["verkopen"]["data"][] = $row;
		}
		return $data;
	}
	public function toggleFactuur($id) {
		/* get record */
		$q = sprintf("select * from finance_omzet_akties where id = %d", $id);
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			$row = sql_fetch_assoc($res);
			if ($row["bedrag_betaald"] != $row["rekeningflow"])
				$q = sprintf("update finance_omzet_akties set bedrag_betaald = rekeningflow where id = %d", $id);
			else
				$q = sprintf("update finance_omzet_akties set bedrag_betaald = 0 where id = %d", $id);
			sql_query($q);
		}
	}
	public function getOpenstaandeFacturen() {
		$user_data = new User_data();
		$user_info = $user_data->getUserPermissionsById($_SESSION["user_id"]);
		if (!$user_info["xs_turnovermanage"])
			return array();

		$dagen = $this->getDagen();

		$items = array();
		$address_data = new Address_data();

		$q = sprintf("select * from finance_omzet_akties where datum <= %1\$d and datum >= %2\$d and bedrag_betaald != rekeningflow order by datum, factuur_nr",
			mktime(0,0,0,date("m"),date("d")-(int)$dagen, date("Y")),
			$GLOBALS["covide"]->license["finance_start_date"]
			);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$row["address_id"]   = $address_data->getAddressIdByDebtor($row["address_id"]);
			$row["address_name"] = $address_data->getAddressNameById($row["address_id"]);
			$row["datum_h"]      = date("d-m-Y", $row["datum"]);
			$row["bedrag_h"]     = $this->formatCurrency($row["rekeningflow"]);
			$items[] = $row;
		}
		return $items;
	}
	public function getDagen() {
		$q = "select html from finance_teksten where omschrijving = 'betaling binnen'";
		$res = sql_query($q);
		$dagen = sql_result($res,0);
		if (!$dagen)
			$dagen = 14; //default

		return $dagen;
	}
}
?>
