<?php
require("../inc_common.php");
$finance_data = new Finance_data();

$q = sprintf("SELECT * FROM finance_offertes WHERE id = %d", $id);
$result = sql_query($q);
$row = sql_fetch_assoc($result);

$titel  = $row["titel"];
$id     = $row["id"];
$status = $row["status"];
$datum  = $row["datum_".$status];
$datum  = strtotime($datum);

$html   = $row["html_".$status];
$producten_id = $row["producten_id_".$status];
$prec_betaald = $row["prec_betaald_".$status];
$font     = $row["font"];
$fontsize = $row["fontsize"];
$template_id = $row["template_setting"];

$html = str_replace("[producten hier]", "", $html);

//offertes / opdracht kent geen precentage betalen dus die is gewoon 100
if ($status <= 1)
	$prec_betaald = 100;

$factuur_nr = $row["factuur_nr_".$status];
$opdracht   = $row["opdracht"];
$uitvoerder = $row["uitvoerder"];

if ($row["address_id"])
	$debiteur_nr = $row["address_id"];
else
	$debiteur_nr = $row["debiteur_nr"];
$bcard_id = $row["bcard_id"];

$btw_tonen = 1; //$row["btw_tonen"];
$btw_prec  = $row["btw_prec"];
if (!$btw_prec)
	$btw_prec = "19";

//omschrijvingen van de producten
$q2 = sprintf("SELECT * FROM finance_producten_in_offertes WHERE link_id = %d", $producten_id);
$result2 = sql_query($q2);
$productenOmschrTekst = "<table border='0' width='90%' cellpadding='0'>";

while ($row2 = sql_fetch_assoc($result2)){
	$q = sprintf("SELECT * FROM finance_producten WHERE id = %d", $row2["producten_id"]);
	$result = sql_query($q);
	$row = sql_fetch_assoc($result);
	$productenOmschrTekst .= "<tr><td><i><b>".$row["titel"]."</b></i></td></tr><tr><td>".$row["html"]."</td></tr><tr><td>&nbsp;</td></tr>";
}
$productenOmschrTekst .= "</table>";

$veldInfo="<tr><td><b>Omschrijving</b></td><td align='right' width='75'><b>Aantal</b></td><td align='right' width='75'><b>Prijs</b></td><td align='right' width='75'><b>Bedrag</b></td></tr><tr><td colspan=4><hr size=1 color=\"black\"></td></tr>";

$result2 = sql_query($q2);
//producten moeten in tweeen gesplitst worden (eenmalig en per jaar)
$productenTekst = "<table border='0' width='100%' cellpadding='4'>".$veldInfo;
//productenlijst voor opdracht
$productenTekstO = "";
//productenlijst voor factuur
$productenTekstF = "";

$totaal=0;
$totaalTekst="";
while ($row2 = sql_fetch_assoc($result2)){
	$q = sprintf("SELECT * FROM finance_producten WHERE id = %d", $row2["producten_id"]);
	$result = sql_query($q);
	$row = sql_fetch_assoc($result);

	//kijken hoe vaak dit product in de lijst voorkomt
	$aantalKeer = $row2["aantal"];

	$productenTekst  .="<tr><td valign='top'>".nl2br(htmlentities($row["titel"], ENT_NOQUOTES, "UTF-8"))."<br><small>".htmlentities($row2["omschrijving"], ENT_NOQUOTES, "UTF-8")."<br></small></td>";
	$productenTekst  .="<td align='right' valign='top'>".$aantalKeer."</td><td align='right' valign='top'><nobr>".$finance_data->formatCurrency($row2["prijs"], 0, 1)."</nobr></td><td align='right' valign='top'><nobr>".$finance_data->formatCurrency((($aantalKeer*$row2["prijs"])/100)*$prec_betaald, 0, 1)."</nobr></td></tr>";
	$productenTekstO .="".$row["titel"]."<br>";

	$totaal      += ((($aantalKeer*$row2["prijs"]))/100)*$prec_betaald;
	$totaalBtw   += (($aantalKeer*($row2["prijs"]*($row2["btw"]/100)))/100)*$prec_betaald;
	$totaalTekst  = "<tr><td rowspan='2'>&nbsp;</td></tr><tr><td colspan='3'><br></td></tr><tr><td valign=top align='right' colspan='2'><i>totaal excl. BTW</i></td><td align='right' colspan='2' valign='top'><nobr><b>&nbsp;&nbsp;&nbsp;".$finance_data->formatCurrency($totaal, 0, 1)."</b></nobr></td></tr>";

	if ($btw_tonen==1) {
		$totaalTekst .= "<tr><td valign=top align='right' colspan='2'><i>".$btw_prec."% BTW</i></td><td align='right' colspan='2'><nobr><b>&nbsp;&nbsp;&nbsp;".$finance_data->formatCurrency($totaalBtw, 0, 1)."</b></nobr></td></tr>";
	}
	$xtotaal = round($totaal,2)+round($totaalBtw,2);
	$totaalTekst .= "<tr><td rowspan='2'>&nbsp;</td></tr><tr><td colspan='3'><hr size=1 color=\"black\" width=100%></td></tr><tr><td valign=top align='right' colspan='2'><i>totaal incl. BTW</i></td><td align='right' colspan='2'><nobr><b>&nbsp;&nbsp;&nbsp;".$finance_data->formatCurrency($xtotaal, 0, 1)."</b></nobr></td></tr>";
}
$productenTekst .= $totaalTekst."</table>";

$html = str_replace("&rdquo;", chr(34), $html);

$q = "select * from finance_teksten where description = 'betaling'";
$res2 = sql_query($q);
$row2 = sql_fetch_assoc($res2);
$betaling = $row2["html"];
$betaling.= "<br><br>";

		/*
		$q = "select * from finance_teksten where description = 'btw nummer'";
		$res2 = sql_query($q);
		$row2 = sql_fetch_assoc($res2);
		$betaling.= "Ons btw nummer: ".$row2["html"]."<br>";
		 */

if ($email) {
	$q = "select * from finance_teksten where description = 'email'";
	$res2 = sql_query($q);
	$row2 = sql_fetch_assoc($res2);
	$email_afzender = $row2["html"];

	/* match against user signatures */
	$email_data = new Email_data();
	$sigs = $email_data->get_signature_list(0, $_SESSION["user_id"]);
	/* loop emails */
	$def = 0;
	foreach ($sigs as $ks=>$s) {
		if ($s["email"] == $email_afzender)
			$email_signature = $s["id"];
		//elseif ($s["default"] == 1)
		//	$def = $ks;
	}
	if (!$email_signature)
		$email_signature = "-1";
}


$address_data = new Address_data();
$aid = $address_data->getAddressIdByDebtor($debiteur_nr);
$row = $address_data->getAddressById($aid);
if ($email) {
	if ($bcard_id) {
		$address_info = $address_data->getAddressById($bcard_id, "bcards");
		$email_address = $address_info["business_email"];
	} else {
		$email_address = $row["email"];
	}
}

switch ($status) {
case 0:
	$term = "offerte";
	break;
case 1:
	$term = "opdracht";
	break;
default:
	$term = "factuur";
	break;
}
if (!$factuur_nr) {
	$factuur_nr    = "";
	$factuur_regel = "";
	$betaling      = "";
} else {
	$factuur_regel = "<b>Nummer: </b>".$factuur_nr." / ".$debiteur_nr."<br>";
}

$term = strtoupper($term);
$f = "
	<p align='left'>
	<big><big><b><i>$term</i></b></big></big>
	<br>
	<small>$factuur_nr</small>
	</p>
	";
$h = "
	<table border='0' cellspacing='0' cellpadding='0' height='90%' width='100%'>
	<tr>
	<td valign='top'>
	###addressdata###
	<b>Datum:</b> ".utf8_encode(strftime("%d %B %Y", $datum))."<br>
	".$factuur_regel."
	<b>Referentie: </b>".$titel."<br><br></b>
	<p>".$productenTekst."</p>
	</td>
	</tr>
	<tr>
	<td valign='bottom'>
	<small>".$betaling."</small>
	</td>
	</tr>
	</table>
	";

$has_html = trim(strip_tags(str_replace("&nbsp;", "", $html)));

if ($has_html) {
	$h.= "<!--NewPage-->";
	$h.= $html;
}
	/*
						<table width='100%' border='0' cellspacing='0' cellpadding='0'>
							<tr>
								<td width='60%'>&nbsp;</td>
								<td>
									<nobr>
										<b>
											".$row["companyname"]."<br>
											tav&nbsp;".$row['tav']."<br>
											".$row["address"]."<br>
											".$row["zipcode"]."&nbsp;&nbsp;".$row["city"]."<br>
											".$row["country"]."
										</b>
									</nobr>
								</td>
							</tr>
						</table>

	 */
if ($factuur_nr)
	$extra = $factuur_nr;
else
	$extra = date("d-m-Y");

$template = new Templates_output();
$template->templatePrint(1, array(
	"id"       => $producten_id,
	"txt"      => $h,
	"header"   => $f,
	"debtor"   => $debiteur_nr,
	"tpl"      => $template_id,
	"font"     => $font,
	"fontsize" => $fontsize,
	"email"    => $email_address,
	"email_sender" => $email_afzender,
	"email_signature" => $email_signature,
	"email_subject"   => sprintf("%s %s", $term, $extra),
	"bcard_id" => $bcard_id
));
?>
