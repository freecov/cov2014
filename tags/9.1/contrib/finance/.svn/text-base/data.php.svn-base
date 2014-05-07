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
	public function __construct() {
		$this->grootboeknummer["bank"]        = 59;
		$this->grootboeknummer["kas"]         = 57;
		$this->grootboeknummer["memoriaal"]   = 104;
		$this->grootboeknummer["debiteuren"]  = 66;
		$this->grootboeknummer["crediteuren"] = 89;
		$this->grootboeknummer["btw"]         = 76;
	}

	public function formatCurrency($float, $output_numeric=0) {
		$float = number_format($float, 2 ,"." ,"");
		if (!$output_numeric) {
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
		$month["prev"]["end"]    = mktime(0,0,0,date("m"), 1, date("Y"));

		$month["prev2"]["start"] = mktime(0,0,0,date("m")-2, 1, date("Y"));
		$month["prev2"]["end"]   = mktime(0,0,0,date("m")-1, 1, date("Y"));

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
				$res = sql_query($q);
				$_debet = sql_result($res,0);

				$q = sprintf("select sum(bedrag) as totaal from finance_boekingen
					where datum between %d and %d and %s and credit = 1",
					$v["start"], $v["end"], $a);
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

		$q = sprintf("select * from finance_omzet_akties
			where rekeningflow != bedrag_betaald
			and datum > %d
			order by datum", $GLOBALS["covide"]->license["finance_start_date"]);
		$res = sql_query($q, "", 0, 5);
		while ($row = sql_fetch_assoc($res)) {
			$q = sprintf("select id from address where debtor_nr = %d", $row["address_id"]);
			$res2 = sql_query($q);
			$row["address_id"] = sql_result($res2,0);

			$data["openstaand"]["verkopen"]["laatste"][$row["factuur_nr"]] = array(
				"datum"   => date("d-m-Y", $row["datum"]),
				"descr"   => $row["omschrijving"],
				"bedrag"  => $this->formatCurrency($row["rekeningflow"]),
				"factuur" => $row["factuur_nr"],
				"address_name" => $address_data->getAddressNameById($row["address_id"]),
				"address_id"   => $row["address_id"]
			);
		}

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
		return $data;
	}

	public function getGrootboekNameById($id) {
		$q = sprintf("select nr, titel from finance_grootboeknummers where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);
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
			return date("Y", sql_result($res,0));
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
}
?>