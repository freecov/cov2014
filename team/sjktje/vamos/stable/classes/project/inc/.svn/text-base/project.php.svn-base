<?
check_login();
// {{{ switch ($action) - bepaal actie
switch ($action) {
	case 'activiteit'      : activiteit();		break;
	case 'info'            : info();			break;
	case 'info_hoofd'      : info_hoofd();		break;
	case 'wijzig'          : wijzig();			break;
	case 'nieuw'           : wijzig();			break;
	case 'Verwijder'       : verwijder();		break;
	case 'Opslaan'         : opslaan();		break;
	case 'uren'            : uren();			break;
	case 'nieuw_hoofd'     : wijzig_hoofd();	break;
	case 'wijzig_hoofd'    : wijzig_hoofd();	break;
	case 'Opslaan_hoofd'   : opslaan_hoofd();	break;
	case 'Verwijder_hoofd' : verwijder_hoofd();	break;
	default                : break;
}
// }}}

// {{{ recursief updaten van hoofdproject als subproject actief is
//kan heel snel
$groep = array();
$groep[] = "0";
$q = "select groep from project where actief=1 group by groep";
$res = sql_query($q);
while ($row = sql_fetch_array($res)){
	$groep[] = $row["groep"];
}
$groep = implode(",",$groep);
$q = "update hoofd_project set actief=1 where actief=0 and id IN ($groep)";
sql_query($q);
// }}} end of update


// {{{ afrondfunctie
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
// }}}

function activiteit() {
// Verwijder: Een activiteit verwijderen
	function activ_verwijder() {
		global $project;
		$result = sql_query("DELETE FROM uren_activ WHERE id=".$project["id"]);
	}

	// Opslaan : Een gewijzigde activiteit opslaan
	function activ_opslaan() {
		global $project;
		$result = sql_query("UPDATE uren_activ SET activiteit='".$project["activiteit"]."', uurtarief=".$project["uurtarief"]." WHERE id=".$project["id"]);
	}
	// Voegtoe : Een activiteit toevoegen
	function activ_toevoegen() {
		global $id, $naam, $tarief;
		if (!$tarief) { $tarief=0; }
		$result = sql_query("INSERT INTO uren_activ SET activiteit=\"$naam\", uurtarief=$tarief");
	}

	global $subaction;

	// Main: Opties laten zien voor verwijderen/toevoegen van activiteiten

	// Kijken of de gebruiker rechten heeft
	$result = sql_query("SELECT xs_projectmanage FROM gebruikers WHERE id=".$_SESSION["user_id"]);
	$row = sql_fetch_array ($result);
	if ($row["xs_projectmanage"]==0) {
	  dialog ("Activiteiten manager", "U heeft geen toegang tot de activiteiten manager", "index.php|Projectenlijst");
	  exit;
	}

	// Actie bepalen
	switch ($subaction) {
	  case "Verwijder" : activ_verwijder(); break;
	  case "Opslaan"   : activ_opslaan();   break;
	  case "Toevoegen" : activ_toevoegen(); break;
	  default          :                    break;
	}

	html_header();
	?><table border="0" cellspacing="0" cellpadding="0"><tr><td align="right"><?
	venster_header("activiteiten wijzigen","",Array("terug","index.php"),0,-1);
	?>
	<tr><td><nobr>
	  <?
	  // Activiteiten ophalen
	  $result = sql_query("SELECT * FROM uren_activ ORDER BY activiteit");
	  while ($row = sql_fetch_array($result)) {
	  	?>
	  	<form action="index.php" method="post" name="activf_<?=$row["id"]?>">
			<input type="hidden" name="action" value="activiteit">
			<input type="hidden" name="subaction" value="">
			<input type="hidden" name="project[id]" value="<?=$row["id"]?>">
			<input class="inputtext" type="tekst" name="project[activiteit]" value="<?=$row["activiteit"]?>">
			<input class="inputtext" type="tekst" name="project[uurtarief]" value="<?=$row["uurtarief"]?>" size="7">
			<a href="Javascript:document.forms.activf_<?=$row["id"]?>.subaction.value='Opslaan';document.forms.activf_<?=$row["id"]?>.submit();"><? button("knop_ok.gif", "opslaan"); ?></a>
			<a href="Javascript:document.forms.activf_<?=$row["id"]?>.subaction.value='Verwijder';document.forms.activf_<?=$row["id"]?>.submit();" onclick="return confirm('Het verwijderen van een activiteit kan kwalijke gevolgen hebben.\nWeet u zeker dat u wilt doorgaan?')"><? button("knop_verwijder.gif", "verwijderen"); ?></a>
			</form><br />
			<?
	  }
	  print "<nobr></td></tr>";
	  venster_footer();
	  ?></td></tr><tr><td align="right"><?
	  venster_header("activiteiten toevoegen","",Array(),0,-1);
	  ?>
	<tr><td><nobr>
	  <form action="index.php" method="post" name="activ_nieuw">
	  <input type="hidden" name="action" value="activiteit">
		<input type="hidden" name="subaction" value="Toevoegen">
	  <input class="inputtext" type="text" name="naam">
	  <input class="inputtext" type="text" name="tarief" size="7">
		<a href="Javascript:document.forms.activ_nieuw.submit();"><? button("knop_ok.gif", "toevoegen"); ?></a>
	  </form>
	</nobr></td></tr>
	<?
	venster_footer();
	?></td></tr></table><?
	html_footer();
	exit;
}
//-----------------------------------------------------------
//  {{{ Wijzig: Wijzig project informatie
//-----------------------------------------------------------
function wijzig() {
  global $db, $user_id, $project;
  global $id;
  html_header();
  // Kijken of de user rechten heeft om een project aan te passen of
  // om een nieuw project aan te maken.
  $result = sql_query("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
  $row = sql_fetch_row($result);

	//check subproject
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


  if ($row[0]!=1 && $proj_access_sub!=1) {
		dialog ("project beheer","u heeft geen rechten om projecten te beheren.","index.php|projecten lijst");
	  exit;
  }

  if ($id) {
	// Project informatie inladen
	$result = sql_query("SELECT * FROM project WHERE id=$id");
	$project = sql_fetch_array($result);
	$titel = gettext("edit");
  } else {
	// Nieuw project
	$titel = gettext("new");
  }
  venster_header(gettext("projects"),$titel,Array(),0,-1);
  ?>
	<form action="index.php" method="post" name="deze">
	<input type="hidden" name="project[id]" value="<?=$project["id"]?>">
	  <tr>
		<td align="right" <?=td(0)?>><? echo gettext("name"); ?></td>
		<td <?=td(1)?>><input class="inputtext" maxlength="28" type="text" name="project[naam]" value="<?=$project["naam"]?>"></td>
	  </tr><tr>
		<td align="right" <?=td(0)?>><? echo gettext("description"); ?></td>
		<td <?=td(1)?>><textarea name="project[omschrijving]" cols="40" class="inputtextarea" rows="10"><?=$project["omschrijving"]?></textarea></td>
	  </tr><tr>
		<td align="right" <?=td(0)?>><? echo gettext("project manager"); ?></td>
		<td <?=td(1)?>>
		  <select id="userlist1" name="project[beheerder]" class="inputselect">
			<option value="0"> <? echo gettext("none"); ?>
			<?
			//lijst met users ophalen
			$res_user = sql_query("SELECT id,naam,actief FROM gebruikers WHERE actief=1 ORDER BY naam ASC");
			while ($row_user=sql_fetch_array($res_user)) {
			  if ($row_user["naam"] != "archiefgebruiker" && $row_user["naam"] != "administrator") {
				  if ($project["beheerder"]==$row_user["id"]) {
					print "<option value=\"".$row_user["id"]."\" SELECTED>".$row_user["naam"];
				  } else {
					print "<option value=\"".$row_user["id"]."\">".$row_user["naam"];
				  }
			  }
			}
			?>
		  </select>
      <? userlistInit("userlist_1", "document.getElementById('userlist1')");?>
		</td>
	  </tr><tr>
		<td align="right" <?=td(0)?>><? echo gettext("sub project of"); ?></td>
		<td <?=td(1)?>>
		  <select name="project[groep]" class="inputselect" class="inputselect">
			<option value="0"> <? echo gettext("none"); ?>
			<?
			// lijst met hoofdprojecten ophalen
			$res_proj = sql_query("SELECT id,naam FROM hoofd_project ORDER BY naam ASC");
			while ($row_proj=sql_fetch_array($res_proj)) {
			  if ($project["groep"]==$row_proj["id"]) {
				print "<option value=\"".$row_proj["id"]."\" SELECTED>".$row_proj["naam"];
			  } else {
				print "<option value=\"".$row_proj["id"]."\">".$row_proj["naam"];
			  }
			}
			?>
		  </select>
		</td>
	  </tr><tr>
	  <? if ($row["groep"]!=0) { ?>
		<td align="right" <?=td(0)?>><? echo gettext("status"); ?></td>
		<td <?=td(1)?>><span class="d">
		  <select name="project[status]" class="inputselect">
			<option value="1"><? echo gettext("opened"); ?></option>
			<option value="2"><? echo gettext("running"); ?></option>
			<option value="3"><? echo gettext("closed"); ?></option>
			<option value="4"><? echo gettext("verworpen(als probleem)"); ?></option>
			<option value="5"><? echo gettext("frozen(als probleem)"); ?></option>
		  </select>
		</td>
	</tr><tr>
	  <? } ?>
		<td align="right" <?=td(0)?>><? echo gettext("active"); ?></td>
		<td <?=td(1)?>>
		  <input type="radio" value="1" name="project[actief]" <? if ($project["actief"]==1) { print "CHECKED"; }?>><? echo gettext("yes"); ?>
		  <input type="radio" value="0" name="project[actief]" <? if ($project["actief"]==0) { print "CHECKED"; }?>><? echo gettext("no"); ?>
		</td>
	  </tr><tr>
		<td align="right" <?=td(0)?>><? echo gettext("contact"); ?></td>
		<td <?=td(1)?>>
		<input type="hidden" name="project[debiteur]" value="<?=$project[debiteur]?>">
		<?
		if ($project["debiteur"]) {
			$result = sql_query("SELECT bedrijfsnaam FROM adres WHERE id=".$project["debiteur"]);
			if (sql_num_rows($result)!=0) {
		?>
			<?=@sql_result($result,0)?>&nbsp;&nbsp;&nbsp;<a href="javascript:var pop = window.open('../adres/?action=zoekRelProj');"><? echo gettext("change"); ?> <img src="../img/knop_rechts.gif" alt="Wijzig" border="0" valign="absmiddle"></a>
			<?
			} else {
				?>
				<a href="javascript:var pop = window.open('../adres/?action=zoekRelProj');"><? echo gettext("search"); ?> <img src="../img/knop_rechts.gif" border="0" alt="Zoeken"></a>
				<?
			}
			?>
		<? } else { ?>
			<a href="javascript:var pop = window.open('../adres/?action=zoekRelProj');"><? echo gettext("search"); ?> <img src="../img/knop_rechts.gif" alt="Zoeken" border="0"></a>
		<? } ?>
		</td>
	  </tr>
		<tr>
	<td align="right" <?=td(0)?>><? echo gettext("businesscard"); ?></td>
	<td <?=td(1)?>>
		<select class="inputselect" name="project[bcards]">
			<option value="0">- <?=gettext("none")?> -</option>
			<?
				$q = "select * from bcards where bedrijfs_id = ".(int)$project["debiteur"]." order by achternaam";
				$resx = sql_query($q);
				while ($rowx = sql_fetch_array($resx)) {
					?>
						<option value="<?=$rowx["id"]?>" <? if ($rowx["id"]==$project["bcards"]){ echo "selected";} ?>>
							<?
								if ($rowx["eigen"]) {
									echo $rowx["eigen"];
								} else {
										echo $rowx["voornaam"]." ".$rowx["tussenvoegsel"]." ".$rowx["achternaam"];
								}
							?>
						</option>
					<?
				}
			?>
		</select>

	</td>
</tr>

		<tr>
			<td align="right" <?=td(0)?>><? echo gettext("budget"); ?> &euro;</td>
			<td <?=td(1)?>><input class="inputtext" type="text" name="project[budget]" value="<?=$project["budget"]?>"></td>
	  </tr><tr>
			<td align="right" <?=td(0)?>><? echo gettext("hours"); ?></td>
			<td <?=td(1)?>><input class="inputtext" type="text" name="project[uren]" value="<?=$project["uren"]?>"></td>
		</tr><tr>
			<td colspan="2" align="right" <?=td(0)?>>
		  	<input name="action" type="hidden" value="">
			 <? if ($id){ ?>
				<a href="Javascript:document.deze.action.value='Verwijder';document.deze.submit();"><img onclick="return confirmDelete('<?=$project["naam"]?>')" src="../img/knop_verwijder.gif" alt="Verwijder" border="0"></a>
			 <? } ?>
				<a href="Javascript:document.deze.action.value='Opslaan';document.deze.submit();"><img src="../img/knop_ok.gif" alt="OK" border="0"></a>
			</td>
	  </tr></form>
	  <?
	  venster_footer();
	  html_footer();
	  exit;
	}

	// }}} -------------------------------------------------------
	// {{{ Opslaan: Nieuw of gewijzigd project opslaan
	//-----------------------------------------------------------
	function opslaan() {
	  global $db, $user_id, $project;
	  // Kijken of de user rechten heeft om een project aan te passen of
	  // om een nieuw project aan te maken.
	  $result = sql_query("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
	  $row = sql_fetch_row($result);

		//check subproject
		$id = $project["id"];
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

	  if ($row[0]!=1 && $proj_access_sub!=1) {
		dialog ("project beheer","u heeft geen rechten om projecten te beheren.","index.php|projecten lijst");
		exit;
	  }
	  if ($project["id"] == "") {
			// Nieuw project
			$query = "INSERT INTO project SET ";
	  } else {
			// Bestaand project updaten
			$query = "UPDATE project SET ";
	  }
		if (!$project["budget"]) { $project["budget"]=0; }
		if (!$project["uren"]) { $project["uren"]=0; }
	  $query .= "naam='".$project["naam"]."', ";
	  $query .= "omschrijving='".$project["omschrijving"]."', ";
	  $query .= "beheerder='".$project["beheerder"]."', ";
		$query .= "bcards='".(int)$project["bcards"]."', ";
	  $query .= "groep='".$project["groep"]."', ";
	  $query .= "actief=".(int)$project["actief"].", ";
		$query .= "budget=".(int)$project["budget"].", ";
		$query .= "uren=".(int)$project["uren"].", ";
	  if ($project["status"]) {
			$query .= "status=".$project["status"].", ";
	  } else {
			$query .= "status=0, ";
	  }
	  if ($project["debiteur"]=="") {
			$project["debiteur"]="0";
	  }
	  $query .= "debiteur=".(int)$project["debiteur"]."  ";
	  if ($project["id"] != "") {
			// Bestaand project updaten
			$query .= " WHERE id=".(int)$project["id"];
	  }
	  $result = sql_query($query);
		if ($project["id"] != "" ) {
			$projectid = $project["id"];
		} else {
			$projectid = sql_insert_id("project");
		}
		// maak update filesystem
		$sql = "SELECT id FROM filesys_mappen WHERE naam='projecten' AND hoofdmap=0";
		$res = sql_query($sql);
		$pmid = sql_result($res,0);
		if ($project["id"] != "") {
			// check if the folder is there. if not create it.
			$sql = "SELECT id FROM filesys_mappen WHERE project_id=".$project["id"]." AND hoofdmap=$pmid";
			$res = sql_query($sql);
			if (sql_num_rows($res)) {
				$sql = "UPDATE filesys_mappen SET naam='".$project["naam"]."', relatie_id=".$project["debiteur"]." WHERE project_id=".$project["id"]." AND hoofdmap=$pmid";
			} else {
				$sql = "INSERT INTO filesys_mappen SET naam='".$project["naam"]."', relatie_id=".$project["debiteur"].", project_id=".$project["id"].", sticky=1, openbaar=1, hoofdmap=$pmid";
			}
		} else {
			$sql = "INSERT INTO filesys_mappen SET naam='".$project["naam"]."', relatie_id=".$project["debiteur"].", project_id=$projectid, sticky=1, openbaar=1, hoofdmap=$pmid";
		}
		$res = sql_query($sql);
	}
	// }}}-------------------------------------------------------
	// {{{ Verwijder: Verwijder een project
	//-----------------------------------------------------------
	function verwijder() {
	  global $db, $user_id, $project, $id;

	  html_header();

	  // Kijken of de gebruiker rechten heeft om een project te verwijderen
	  $result = sql_query("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
	  $row = sql_fetch_row($result);
	  if ($row[0]!=1) {
			dialog ("project verwijderen","u heeft geen rechten om projecten te verwijderen.","index.php|projecten lijst");
			exit;
	  }

	  if ($project["id"]!="") {
			// project werd verwijderd vanuit wijzig formulier
			$id=$project["id"];
	  }

	  $result = sql_query("DELETE FROM project WHERE id=$id", $db);
		//$sql = "SELECT id FROM filesys_mappen WHERE naam='openbare mappen' AND hoofdmap=0";
		//$res = sql_query($sql);
		//$oid = sql_result($res,0);
		$sql = "SELECT id FROM filesys_mappen WHERE naam='projecten' AND hoofdmap=0";
		$res = sql_query($sql);
		$pmid = sql_result($res,0);
		$sql = "UPDATE filesys_mappen SET sticky=0 WHERE hoofdmap=$pmid AND project_id=$id";
		$res = sql_query($sql);

	  dialog ("project verwijderen","het project is verwijderd", "index.php|lijst");
	  html_footer();

	  exit;
	}

	// }}}-------------------------------------------------------
	// {{{ Info: Laat informatie zien over een bepaald project
	//-----------------------------------------------------------
	function info() {
	  global $db, $id;
	  html_header();
	  // Informatie over project inlezen
	  $result = sql_query("SELECT * FROM project WHERE id=$id");
	  $row = sql_fetch_array($result);
	  $naam=$row["naam"];
	  venster_header("projecten",$naam,Array("wijzig","index.php?action=wijzig&id=".$row["id"],"overzicht","index.php?action=uren&id=".$row["id"]),0,-1);
	  ?>
	  <tr>
		<td align="right"><span class="d"><? echo gettext("name"); ?></span></td>
		<td><span class="d"><?=$row["naam"]?>&nbsp;</td>
	  </tr><tr>
		<td align="right"><span class="d"><? echo gettext("description"); ?></span></td>
		<td><span class="d"><?=nl2br($row["omschrijving"])?>&nbsp;</td>
	  </tr><tr>
		<td align="right"><span class="d"><? echo gettext("project manager"); ?></span></td>
		<td><span class="d">
		<?
		if ($row["beheerder"]==0) {
		  echo gettext("none");
		} else {
		  $res_user = sql_query("SELECT * FROM gebruikers WHERE id=".$row["beheerder"]);
		  $row_user = sql_fetch_array($res_user);
		  echo $row_user["naam"];
		}
		?>
		</span></td>
	  </tr><tr>
		<td align="right"><span class="d"><? echo gettext("active"); ?></span></td>
		<td><span class="d">
		<?
		if ($row["actief"]==0) {
		  echo gettext("no");
		} else {
		  echo gettext("yes");
		}
		?>
		</span></td>
	  </tr><tr>
		<td align="right"><span class="d"><? echo gettext("debtor"); ?></span></td>
		<td>
		<?
		// Naam van de debiteur opzoeken dmv id
		if ($row["debiteur"]==null) {
		  $row["debiteur"]=0;
		}
		$result = sql_query("SELECT id, debiteur_nr,bedrijfsnaam FROM adres WHERE id=".$row["debiteur"]);
		$row_debiteur = sql_fetch_array($result);
		if (!$row_debiteur["id"]) {
		  print "<span class='d'>";
			echo gettext("no debtor");
			echo "</span>";
		} else {
		  // Gewone id uit adres is debiteur_nr geworden. Dit kan problemen op gaan leveren.
		  print "<span class='d'><a href=\"../adres/klant.php?klant_id=".$row_debiteur["id"]."\">".$row_debiteur["bedrijfsnaam"]."</a></span>";
		}
		?>
		</td>
	  </tr><tr>
			<td align="right"><span class="d"><?=gettext("folder")?></span></td>
			<td><a href="../bestandsbeheer/?action=folder&id=<?=getfolderinfo($id,0)?>"><?=gettext("open")?></a></td>
		</tr>
	  <?
	  venster_footer();
	  html_footer();
	  exit;
	}
// }}}------------------------------------------------------------------------
// {{{ getfolderinfo: check if folder is there. Make it if not. return the id.
//----------------------------------------------------------------------------

function getfolderinfo($projectid, $new) {
	// maak update filesystem
	$sql = "SELECT id FROM filesys_mappen WHERE naam='projecten' AND hoofdmap=0";
	$res = sql_query($sql);
	$pmid = sql_result($res,0);
	$sql = "SELECT * FROM project WHERE id=$projectid";
	$res = sql_query($sql);
	$project = sql_fetch_assoc($res);
	if (!$new) {
		// check if the folder is there. if not create it.
		$sql = "SELECT id FROM filesys_mappen WHERE naam='".addslashes($project["naam"])."' AND relatie_id=".$project["debiteur"]." AND project_id=".$project["id"]." AND hoofdmap=$pmid";
		$res = sql_query($sql);
		if (sql_num_rows($res)) {
			$return_id = sql_result($res,0);
			$sql = "UPDATE filesys_mappen SET naam='".addslashes($project["naam"])."', relatie_id=".$project["debiteur"]." WHERE project_id=".$project["id"]." AND hoofdmap=$pmid";
		} else {
			$sql = "INSERT INTO filesys_mappen SET naam='".addslashes($project["naam"])."', relatie_id=".$project["debiteur"].", project_id=".$project["id"].", sticky=1, openbaar=1, hoofdmap=$pmid";
		}
	} else {
		$sql = "INSERT INTO filesys_mappen SET naam='".addslashes($project["naam"])."', relatie_id=".$project["debiteur"].", project_id=$projectid, sticky=1, openbaar=1, hoofdmap=$pmid";
	}
	$res = sql_query($sql);
	if (!$return_id) {
		$return_id = sql_insert_id("filesys_mappen");
	}
	return $return_id;
}
// }}}-------------------------------------------------------
// {{{ Lijst: genereer een lijst met projecten en hun opties.
//-----------------------------------------------------------
html_header();
// Xs_projectmanager status ophalen
$result = sql_query("SELECT xs_projectmanage FROM gebruikers WHERE id=".$_SESSION["user_id"]);
$row = sql_fetch_array ($result);
$xs_projectmanage = $row["xs_projectmanage"];
global $f;
venster_header("projecten","zoeken",Array(),0,-1);
?>
<form name="zoeken" action="index.php" method="post"><tr>
<td><input type="text" class="inputtext" name="f[zoek]" id="zoek" value="<?=$f["zoek"]?>"></td>
<td><a href="Javascript:document.zoeken.submit();"><img src="../img/knop_rechts.gif" alt="Zoeken" border="0"></a></td>
</tr><tr>
<td colspan="2"><span class="d"><nobr><a href="javascript:document.zoeken.zoek.value='*';document.zoeken.submit();"><? echo gettext("Show all projects"); ?></a></nobr></span></td>
</tr></form>
<?
venster_footer();
if ($f["zoek"] == "*") { $f["zoek"] = "%"; }

if ($xs_projectmanage) {
	venster_header("projecten","overzicht",Array("nieuw hoofdproject","index.php?action=nieuw_hoofd","nieuw project","index.php?action=nieuw","manage activiteiten","index.php?action=activiteit","uren overzicht","uren_overzicht.php", "planning", "planningp.php"),0,-1);
	echo("<tr><td>");
	tabel_header(0);
} else {
	venster_header("projecten","overzicht",Array("nieuw project","index.php?action=nieuw"),0,-1);
	echo("<tr><td>");
	tabel_header(0);
}

//move orphanic projects to a new created project named [apart gezet]
//build list with all existing root projects
$_proj = array(0);
$q = "select id from hoofd_project";
$resp = sql_query($q);
while ($rowp = sql_fetch_array($resp)) {
	$_proj[] = $rowp["id"];
}

//check all existing sub projects
$q = "select id, groep, naam from project where groep NOT IN (".implode(",",$_proj).")";
$resp = sql_query($q);
if (sql_num_rows($resp)>0) {

	//search for group '???' in database
	$q = "select id from hoofd_project where naam = '???'";
	$resx = sql_query($q);
	if (sql_num_rows($resx)>0) {
		$dbid = sql_result($resx,0);
	} else {
		$q = "insert into hoofd_project (naam, omschrijving, actief) values ('???','subprojecten waarvan het hoofdproject is verwijderd',1)";
		sql_query($q);
		$dbid = sql_insert_id("hoofd_project");
	}
	$q = "update project set groep = $dbid where groep NOT IN (".implode(",",$_proj).")";
	sql_query($q);
}

?>
<tr>
	<td <?=td(0)?>><span class="dT"> &nbsp; </span></td>
	<td <?=td(0)?>><span class="dT"><? echo gettext("name"); ?></span></td>
	<td <?=td(0)?>><span class="dT"><? echo gettext("description"); ?></span></td>
</tr>

<?

// If we are trying to find a project we dont care about hours
// If we are not searching, only show projects with sub-projects with hours and stand-alone projects with hours.

if ($f["zoek"]) {
	//first the easy part. get the main projects and stand alone projects
	$i = 0;
	$found_ids = Array();
	$sql = "SELECT * FROM hoofd_project WHERE naam LIKE '%".$f["zoek"]."%' OR omschrijving LIKE '%".$f["zoek"]."%' ORDER BY naam";
	$res = sql_query($sql);
	while ($row = sql_fetch_assoc($res)) {
		$pr[$i]["id"]           = $row["id"];
		$pr[$i]["hoofd"]        = 1;
		$pr[$i]["naam"]         = $row["naam"];
		$pr[$i]["omschrijving"] = $row["omschrijving"];
		$found_ids[$i]                = $row["id"];
		$i++;
	}
	$sql = "SELECT * FROM project WHERE (naam LIKE '%".$f["zoek"]."%' OR omschrijving LIKE '%".$f["zoek"]."%') AND groep=0 ORDER BY naam";
	$res = sql_query($sql);
	while ($row = sql_fetch_assoc($res)) {
		$pr[$i]["id"]           = $row["id"];
		$pr[$i]["hoofd"]        = 0;
		$pr[$i]["naam"]         = $row["naam"];
		$pr[$i]["omschrijving"] = $row["omschrijving"];
		$i++;
	}
	//now the hard part. get the main projects with sub projects that match the search string
	$sql = "SELECT groep FROM project WHERE groep!=0 AND (naam LIKE '%".$f["zoek"]."%' OR omschrijving LIKE '%".$f["zoek"]."%') GROUP BY groep";
	$res = sql_query($sql);
	$get_h = "0";
	while ($row = sql_fetch_assoc($res)) {
		$get_h .= ",".$row["groep"];
	}
	$sql = "SELECT * FROM hoofd_project WHERE id IN (".$get_h.")";
	$res = sql_query($sql);
	while ($row = sql_fetch_assoc($res)) {
		if (!in_array($row["id"], $found_ids)) {
			$pr[$i]["id"]           = $row["id"];
			$pr[$i]["hoofd"]        = 1;
			$pr[$i]["naam"]         = $row["naam"];
			$pr[$i]["omschrijving"] = $row["omschrijving"];
			$found_ids[$i]                = $row["id"];
			$i++;
		}
	}

	for ($j = 0; $j < sizeof($pr); $j++) {
		$sort_values[$j] = strtolower($pr[$j]["naam"]);
	}
	if (count($sort_values)) {
		asort ($sort_values);
		reset ($sort_values);
		while (list ($arr_key, $arr_val) = each ($sort_values)) {
			$sorted_arr[] = $pr[$arr_key];
		}


		//subproject
		$proj_access["sub"] = array();
		$proj_access["hoofd"] = array();
		$proj_access["parent"] = array();
		$q = "select id,groep from project where beheerder = ".$_SESSION["user_id"];
		$resx = sql_query($q);
		while ($rowx = sql_fetch_array($resx)) {
			$proj_access["sub"][]=$rowx["id"];
			$proj_access["parent"][]=$rowx["groep"];
		}
		$proj_access["parent"] = array_unique($proj_access["parent"]);
		$q = "select id from hoofd_project where beheerder = ".$_SESSION["user_id"];
		$resx = sql_query($q);
		while ($rowx = sql_fetch_array($resx)) {
			$proj_access["hoofd"][]=$rowx["id"];
		}

		//new permission code check
		if (!$instelling["xs_projectmanage"]) {
			foreach ($sorted_arr as $key=>$val) {
				if ($val["hoofd"]) {
					if (!in_array($val["id"],$proj_access["hoofd"])) {
						if (!in_array($val["id"],$proj_access["parent"])) {
							unset($sorted_arr[$key]);
						}
					}
				}else{
					if (!in_array($val["id"],$proj_access["sub"])) {
						unset($sorted_arr[$key]);
					}
				}
			}
		}

		foreach ($sorted_arr as $key=>$val) {
			echo("<tr><td ".td(0)." width=\"65\" align=\"center\"><nobr>");
			if ($val["hoofd"]) {
//				echo("<img src=\"../img/null.gif\">&nbsp;");
				echo("<a href=\"index.php?action=info_hoofd&id=".$val["id"]."\"><img src=\"../img/knop_info.gif\" border=\"0\" alt=\"info\"></a>&nbsp;");
				echo("<img src=\"../img/null.gif\">&nbsp;");

				if (in_array($val["id"],$proj_access["hoofd"]) || $instelling["xs_projectmanage"]) {
					echo("<a href=\"index.php?action=wijzig_hoofd&id=".$val["id"]."\"><img src=\"../img/knop_bewerk.gif\" border=\"0\" alt=\"Wijzig\"></a>");
				}

			} else {
//				echo("<a href=\"../bestandsbeheer/?action=folder&id=".getfolderinfo($val["id"],0)."\"><img src=\"../img/folder_closed.gif\" border=\"0\" alt=\"".gettext("folder")."\"></a>&nbsp;");


				echo("<a href=\"index.php?action=info&id=".$val["id"]."\"><img src=\"../img/knop_info.gif\" border=\"0\" alt=\"info\"></a>&nbsp;");
				echo("<a href=\"index.php?action=uren&id=".$val["id"]."\"><img src=\"../img/knop_uren.gif\" border=\"0\" alt=\"Uren\"></a>&nbsp;");

				echo("<a href=\"index.php?action=wijzig&id=".$val["id"]."\"><img src=\"../img/knop_bewerk.gif\" border=\"0\" alt=\"Wijzig\"></a>");
			}
			echo("</nobr></td>");
			echo("<td ".td(0)."><b><nobr>".$val["naam"]."</nobr></b></td>");
			echo("<td ".td(0)."><b>".substr($val["omschrijving"],0,60)	. "&nbsp;</b></td>");
			echo("</tr>");
		}
	} else {
		echo("<tr><td ".td(0)." colspan=\"3\"><nobr>");
		echo gettext("no projects found");
		echo("</nobr></td></tr>");
	}
} else {
	$uren = Array();
	$q1 = "SELECT project, MAX(tijd_begin) AS tijd FROM urenreg GROUP BY project";
	$res1 = sql_query($q1);
	while ($row = sql_fetch_array($res1)) {
		if (!array_key_exists($row["project"],$uren)) {
			$uren[$row["project"]] = $row["tijd"];
		}
	}
	ksort($uren);
	reset($uren);
	// aray maken met alle te tonen projecten.
	$query = "SELECT COUNT(id) FROM hoofd_project";
	$query.=" WHERE actief=1";
	$result = sql_query($query);
	$totaal = sql_result($result,0);
	$query = "SELECT COUNT(id) FROM project";
	if ($f["zoek"]!="") {
		$query .= " WHERE (naam LIKE '%".$f["zoek"]."%' OR omschrijving LIKE '%".$f["zoek"]."%')";
	} else {
		$query .= " WHERE actief=1 AND groep=0";
	}
	$result = sql_query($query);
	$tot1 = sql_result($result,0);
	$totaal = $totaal + $tot1;
	if ($totaal!=0) {
		// Haal informatie over elk project op
		$query = "SELECT * FROM project";
		if ($f["zoek"]!="") {
			$query .= " WHERE (naam LIKE '%".$f["zoek"]."%' OR omschrijving LIKE '%".$f["zoek"]."%') AND groep=0";
		} else {
			$query.=" WHERE actief=1 AND groep=0";
		}
		$query .= " ORDER BY naam ASC";
		$result = sql_query($query);
		$i = 0;
		while ($row = sql_fetch_array($result)) {
			// kijk of er uren zijn gemaakt die nog niet gefactureerd zijn
			if ($row["lfact"]=="") {
				$lfact_time=0;
			} else {
				$lfact_time = $row["lfact"];
			}
			if ($f["zoek"] != "") {
				$fac[$i]["id"] = $row["id"];
				$fac[$i]["hoofd"] = 0;
				$fac[$i]["naam"] = $row["naam"];
				$fac[$i]["omschrijving"] = $row["omschrijving"];
				$i++;
			} else {
				if ($uren[$row["id"]] > $lfact_time) {
					$fac[$i]["id"] = $row["id"];
					$fac[$i]["hoofd"] = 0;
					$fac[$i]["naam"] = $row["naam"];
					$fac[$i]["omschrijving"] = $row["omschrijving"];
					$i++;
				}
			}
		}
		if (!$f["zoek"]) {
			$sql = "SELECT * FROM hoofd_project ORDER BY naam";
			$res = sql_query($sql);
			while ($row = sql_fetch_array($res)) {
				if ($f["zoek"] != "") {
					$fac[$i]["id"] = $row["id"];
					$fac[$i]["hoofd"] = 1;
					$fac[$i]["naam"] = $row["naam"];
					$fac[$i]["omschrijving"] = $row["omschrijving"];
					$i++;
				} else {
					$aantal_subs = 0;
					$r = sql_query("SELECT * FROM project WHERE groep=".$row["id"]." AND actief=1");
					while ($row1 = sql_fetch_array($r)) {
						if ($row1["lfact"]=="") {
							$lfact_time=0;
						} else {
							$lfact_time = $row1["lfact"];
						}
						if ($uren[$row1["id"]] > $lfact_time) {
							$aantal_subs++;
						}
					}
					if ($aantal_subs) {
						$fac[$i]["id"] = $row["id"];
						$fac[$i]["hoofd"] = 1;
						$fac[$i]["naam"] = $row["naam"];
						$fac[$i]["omschrijving"] = $row["omschrijving"];
						$i++;
					}
				}
			}
		}
		for ($j = 0; $j < sizeof($fac); $j++) {
			$sort_values[$j] = strtolower($fac[$j]["naam"]);
		}
		if (count($sort_values)) {
			asort ($sort_values);
			reset ($sort_values);
			while (list ($arr_key, $arr_val) = each ($sort_values)) {
				$sorted_arr[] = $fac[$arr_key];
			}




		//subprojects
		$proj_access["sub"] = array();
		$proj_access["hoofd"] = array();
		$proj_access["parent"] = array();
		$q = "select id,groep from project where beheerder = ".$_SESSION["user_id"];
		$resx = sql_query($q);
		while ($rowx = sql_fetch_array($resx)) {
			$proj_access["sub"][]=$rowx["id"];
			$proj_access["parent"][]=$rowx["groep"];
		}
		$proj_access["parent"] = array_unique($proj_access["parent"]);
		$q = "select id from hoofd_project where beheerder = ".$_SESSION["user_id"];
		$resx = sql_query($q);
		while ($rowx = sql_fetch_array($resx)) {
			$proj_access["hoofd"][]=$rowx["id"];
		}


	//new permission code check
		if (!$instelling["xs_projectmanage"]) {
			foreach ($sorted_arr as $key=>$val) {
				if ($val["hoofd"]) {
					if (!in_array($val["id"],$proj_access["hoofd"])) {
						if (!in_array($val["id"],$proj_access["parent"])) {
							unset($sorted_arr[$key]);
						}
					}
				}else{
					if (!in_array($val["id"],$proj_access["sub"])) {
						unset($sorted_arr[$key]);
					}
				}
			}
		}


			foreach ($sorted_arr as $key=>$val) {
				echo("<tr><td ".td(0)." width=\"65\" align=\"center\">");
				if ($val["hoofd"]) {
					echo("<a href=\"index.php?action=info_hoofd&id=".$val["id"]."\"><img src=\"../img/knop_info.gif\" border=\"0\" alt=\"info\"></a>&nbsp;");
					echo("<img src=\"../img/null.gif\">");

					if (in_array($val["id"],$proj_access["hoofd"]) || $instelling["xs_projectmanage"]) {
						echo("<a href=\"index.php?action=wijzig_hoofd&id=".$val["id"]."\"><img src=\"../img/knop_bewerk.gif\" border=\"0\" alt=\"Wijzig\"></a>");
					}
				} else {
					echo("<a href=\"index.php?action=info&id=".$val["id"]."\"><img src=\"../img/knop_info.gif\" border=\"0\" alt=\"info\"></a>&nbsp;");
					echo("<a href=\"index.php?action=uren&id=".$val["id"]."\"><img src=\"../img/knop_uren.gif\" border=\"0\" alt=\"Uren\"></a>&nbsp;");
					echo("<a href=\"index.php?action=wijzig&id=".$val["id"]."\"><img src=\"../img/knop_bewerk.gif\" border=\"0\" alt=\"Wijzig\"></a>");
				}
				echo("</td>");
				echo("<td ".td(0)."><b><nobr>".$val["naam"]."</nobr></b></td>");
				echo("<td ".td(0)."><b>".substr($val["omschrijving"],0,60)	. "&nbsp;</b></td>");
				echo("</tr>");
			}
		} else {
			echo("<tr><td ".td(0)." colspan=\"3\"><nobr>");
			echo gettext("no projects with approved hours");
			echo("</nobr></td></tr>");
		}
	} else {
		echo("<tr><td colspan=\"3\" ".td(0).">&nbsp;</td></tr>");
	}
}
tabel_footer();
echo("</td></tr>");
venster_footer();

?>
<?  html_footer(); ?>
<? // }}} ?>
