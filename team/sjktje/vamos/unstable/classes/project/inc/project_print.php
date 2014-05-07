<?
session_cache_limiter('private, must-revalidate');
session_start();
$q = $_SESSION["printquery"];
unset($_SESSION["printquery"]);
$language = $_SESSION["locale"];
putenv("LANG=$language");
setlocale(LC_ALL, $language);
setlocale(LC_NUMERIC, "en_US"); //always use . as decimal
setlocale(LC_MONETARY, "C"); //C system locale
$domain = 'messages';
$filepath = $_SERVER["SCRIPT_FILENAME"];
$path_parts = explode("/", $filepath);
$path_len = count($path_parts)-1;
$path1 = "";
for ($i=0;$i<$path_len;$i++) {
	$path1 .= "/".$path_parts[$i];
}
$path1 .= "/../lang";
bindtextdomain($domain, $path1);
textdomain($domain);

function fix($bedrag){
	$bedrag = round($bedrag,2);

	//helemaal geen punt in het getal
	if(!strstr($bedrag,".")){
		$bedrag.=".00";
	}
	//wel een punt, maar maar 1 getal achter de punt
	if(!!strstr($bedrag,".")){
		if( (strlen($bedrag)-strpos($bedrag,".")) == 2){
			$bedrag.="0";
		}
	}
	//punt -> komma
	$bedrag = str_replace(".", ",", $bedrag);
	return($bedrag);
}

require("../inc_db.php");
db_init();

if ($_REQUEST["mode"]=="csv") {

	header('Content-Transfer-Encoding: binary');
	header('Content-Type: text/plain');

	$naam = "projectexport.csv";

	if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
		header('Content-Disposition: filename="'.$naam.'"'); //msie 5.5 header bug
	} else {
		header('Content-Disposition: attachment; filename="'.$naam.'"');
	}

	// Kijk of gebruiker toegang heeft
		$result = sql_query("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
		$row = sql_fetch_array ($result);

		//check subproject
		$id = $project;
		if ($id) {
			$q = "select beheerder,groep from project where id = $id";
			$resx = sql_query($q);
			$rowx = sql_fetch_array($resx);
			if ($rowx["beheerder"]==$user_id) {
				$proj_access_sub = 1;
			} else {
				$groep = $rowx["groep"];
				//check hoofdproject
				$q = "select count(*) from hoofd_project where beheerder = $user_id and id = $groep";
				$resx = sql_query($q);
				if (sql_result($resx,0)>0) {
					$proj_access_sub=1;
				}
			}
		}
		if ($row["xs_projectmanage"]!=1 && $proj_access_sub!=1) {
			echo "Geen toegang!!!";
			exit();
		}

		$query = "select naam from project where id = $project";
		$res = sql_query($query);
		$row = sql_fetch_array($res);
		$projnaam = $row["naam"];


		//aantal records per pagina
		$num_page = 48;
		$num_page_kol = 55;


		// Gebruikersnamen inlezen
		$result = sql_query("SELECT id,naam FROM gebruikers");
		while ($row=sql_fetch_array($result)) {
		  $gebruiker[$row["id"]] = $row["naam"];
		}
		// Activiteiten inlezen
		$result = sql_query("SELECT id,activiteit,uurtarief FROM uren_activ");
		while ($row = sql_fetch_array($result)) {
		  $activiteiten[$row["id"]] = $row["activiteit"];
		  $uurtarief[$row["id"]] = $row["uurtarief"];
		}

			$query = urldecode($q);
		  $result = sql_query($query);


			$line = "\"".gettext("Project").":\",\"".$projnaam."\"\n";
			if ($from) {
				$line.= "\"datum van/tot:\",\"".strftime("%d %B %Y",$from)." - ".strftime("%d %B %Y",$to)."\"\n";
			}
			$line.= "\"datum afdruk:\",\"".strftime("%d-%m-%Y %H:%M")."\"\n\n";

			$line.= "\"f = facturabele uren\"\n";
			$line.= "\"s = service uren\"\n\n";

		  if (sql_num_rows($result)==0) {
		  	$line.="\"".gettext("No hours booked")."\"\n";
		  }

			//kopjes
			$line.= "\"".gettext("date")."\",\"".gettext("hours")."\",\"".gettext("user")."\",";
			$line.= "\"".gettext("activity")."\",\"".gettext("description")."\",\"".gettext("price")."\"\n";


				// Query opbouwen aan de hand van de from_*, to_* en gebruik params
				$query = "SELECT * FROM urenreg WHERE project=".$_GET["project"];
				$query .= "AND tijd_begin > $from AND tijd_eind < $to ";
				$query .= "ORDER BY tijd_begin DESC ";
				$result = sql_query($query);
				if (sql_num_rows($result)==0) {
					$line.= "\"".gettext("No hours booked")."\"\n";
				}
				while ($row=sql_fetch_array($result)) {

					$line.= "\"".date("d-m-Y",$row["tijd_begin"])."\",";
					if ($row["factureren"]==1) {
						$line.= "\"".number_format((($row["tijd_eind"]-$row["tijd_begin"])/3600),1)." f\",";
					}else{
						$line.= "\"".number_format((($row["tijd_eind"]-$row["tijd_begin"])/3600),1)." s\",";
					}
					$line.= "\"".$gebruiker[$row["gebruiker"]]."\",";
					$line.= "\"".$activiteiten[$row["activiteit"]]."\",";
					$line.= "\"".preg_replace("'<br[^>]*?>'si","\n",$row["omschrijving"])."\",";

					if ($row["factureren"]==1) {
						$prijs= (number_format((($row["tijd_eind"]-$row["tijd_begin"])/3600),3)*$uurtarief[$row["activiteit"]]);
						$line.= "\"EUR ".fix($prijs)."\"\n";
					}else{
						$line.= "\"EUR ".fix(0)."\"\n";
					}

					if ($row["factureren"]==1) {
						$totaal = $totaal + (($row["tijd_eind"]-$row["tijd_begin"])/3600);
						$totaalprijs = $totaalprijs + ((($row["tijd_eind"]-$row["tijd_begin"])/3600)*$uurtarief[$row["activiteit"]]);
					}

				}
				$line.= "\n\"".gettext("total hours:")."\",\"".number_format($totaal,1)."\"\n";
				$line.= "\n\"".gettext("total price:")."\",\"EUR ".number_format($totaalprijs,2,",",".")."\"\n";

				echo $line;

} else {

?>

<html>
<head>
<title><?=gettext("Project details")?></title>
</head>
<body>
<style type="text/css">
	BODY, TD {font-family: monospace; font-size: 12px;}
	BR.page { page-break-after: always }
</style>

<div name="loading" id="loading" style="position:absolute;top:50px;left:50px;" align="center">
	<font size="2"><b><?=gettext("Reading data")?>.... <?=gettext("please wait")?>...</b></font>
</div>

<div name="main" id="main" style="visibility:hidden">
<?

	// Kijk of gebruiker toegang heeft
		$result = sql_query("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
		$row = sql_fetch_array ($result);

		//check subproject
		$id = $project;
		if ($id) {
			$q = "select beheerder,groep from project where id = $id";
			$resx = sql_query($q);
			$rowx = sql_fetch_array($resx);
			if ($rowx["beheerder"]==$user_id) {
				$proj_access_sub = 1;
			} else {
				$groep = $rowx["groep"];
				//check hoofdproject
				$q = "select count(*) from hoofd_project where beheerder = $user_id and id = $groep";
				$resx = sql_query($q);
				if (sql_result($resx,0)>0) {
					$proj_access_sub=1;
				}
			}
		}
		if ($row["xs_projectmanage"]!=1 && $proj_access_sub!=1) {
			echo "Geen toegang!!!";
			exit();
		}

		$query = "select naam from project where id = $project";
		$res = sql_query($query);
		$row = sql_fetch_array($res);
		$projnaam = $row["naam"];


		//aantal records per pagina
		$num_page = 48;
		$num_page_kol = 55;


		// Gebruikersnamen inlezen
		$result = sql_query("SELECT id,naam FROM gebruikers");
		while ($row=sql_fetch_array($result)) {
		  $gebruiker[$row["id"]] = $row["naam"];
		}
		// Activiteiten inlezen
		$result = sql_query("SELECT id,activiteit,uurtarief FROM uren_activ");
		while ($row = sql_fetch_array($result)) {
		  $activiteiten[$row["id"]] = $row["activiteit"];
		  $uurtarief[$row["id"]] = $row["uurtarief"];
		}

			$query = urldecode($q);
		  $result = sql_query($query);

			echo "<html><body>";

			?>
			<table width="100%">
				<tr>
					<td>
						<big><b><?=gettext("Project")?>: <?=$projnaam?></b></big>
						<? if ($from){ ?>
							(<?=strftime("%d %B %Y",$from)?> - <?=strftime("%d %B %Y",$to)?>)
						<? } ?>
						<br>
						<?=gettext("print date")?>: <?=strftime("%d-%m-%Y %H:%M")?><BR><BR>
					</td>
					<td align="right">
						f = <?=gettext("billing hours")?><br>
						s = <?=gettext("service hours")?>
					</td>
				</tr>
			</table>

			<?
			echo "<table>";
		  if (sql_num_rows($result)==0) {
			print "<tr><td colspan=\"3\">";
			echo gettext("No hours booked");
			echo "</td></tr>";
		  }

			//kopjes
			print "<tr valign=\"top\">";
			print "<td><b>".gettext("date")." /</b><BR>";
			print "<b>".gettext("duration")."</b></td>";
			print "<td align=\"right\">";

			print " &nbsp;</td>";
			print "<td><b>".gettext("user")." /</b><br>";
			print "<b>".gettext("activity")."</b></nobr></td>";
			print "<td><b>&nbsp;<br>".gettext("description")."</b></td>";
			print "<td align=\"right\"><b>&nbsp;<br>".gettext("price")."</b></td>";
			print "</tr>\n";

			print "<tr valign=\"top\">";
			print "<td colspan=9><hr></td></tr>";


				// Query opbouwen aan de hand van de from_*, to_* en gebruik params
				$query = "SELECT * FROM urenreg WHERE project=".$_GET["project"];
				$query .= "AND tijd_begin > $from AND tijd_eind < $to ";
				$query .= "ORDER BY tijd_begin DESC ";
				$result = sql_query($query);
				if (sql_num_rows($result)==0) {
					print "<tr><td colspan=\"8\"".td(1).">";
					echo gettext("No hours booked");
					echo "</td></tr>";
				}
				while ($row=sql_fetch_array($result)) {

					print "<tr valign=\"top\">";
					print "<td><nobr>".date("d-m-Y",$row["tijd_begin"])." <BR>";
					if ($row["factureren"]==1) {
						print number_format((($row["tijd_eind"]-$row["tijd_begin"])/3600),1)." f";
					}else{
						print number_format((($row["tijd_eind"]-$row["tijd_begin"])/3600),1)." s";
					}
					print " &nbsp;</td>";
					print "<td>";
					print "&nbsp;";
					print "</td>";
					print "<td>".$gebruiker[$row["gebruiker"]]."&nbsp;<br>";
					print "<nobr>".$activiteiten[$row["activiteit"]]."</nobr> &nbsp;</td>";
					print "<td>".$row["omschrijving"]." &nbsp;</td>";
					print "<td align=\"right\"><table><tr><td width=20>&nbsp;&euro;&nbsp;</td><td>";
					if ($row["factureren"]==1) {
						$prijs= (number_format((($row["tijd_eind"]-$row["tijd_begin"])/3600),3)*$uurtarief[$row["activiteit"]]);
						echo fix($prijs);
					}else{
						echo fix(0);
					}

					print "</td></tr></table></td></tr>\n";
					if ($row["factureren"]==1) {
						$totaal = $totaal + (($row["tijd_eind"]-$row["tijd_begin"])/3600);
						$totaalprijs = $totaalprijs + ((($row["tijd_eind"]-$row["tijd_begin"])/3600)*$uurtarief[$row["activiteit"]]);
					}

				}
				?>
<tr>

			<td colspan="6">
				<? echo gettext("total hours:"); ?> <b><?=number_format($totaal,1)?></b>
			</td>
			<td align="right" colspan="2"><? echo gettext("total price:"); ?> <b>&euro;&nbsp;<?=number_format($totaalprijs,2,",",".")?></b></td>
		  </tr></></td>
		</tr>
	</table>
	</div>
	<script>
	function makePrint(){
		var loading = document.getElementById("loading");
		var main = document.getElementById("main");

		loading.style.visibility = 'hidden';
		main.style.visibility = 'visible';

		setTimeout('window.print()', 1000);
		setTimeout('window.close()', 2000);
	}

	makePrint();
</script>
	</body>
	</html>

<? } ?>
