<?php
  /*------------------------------------------------------------------*/
  /* project.php                                                      */
  /*                                                                  */
  /* Projecten beheer                                                 */
  /*                                                                  */
  /*------------------------------------------------------------------*/

  require ("inc_common.php");
  check_login();
//  error_reporting(63);
  switch ($action) {
	case 'activiteit'	: activiteit();	break;
	case 'info'			: info(); 		break;
	case 'wijzig'		: wijzig();		break;
	case 'nieuw'		: wijzig();		break;
	case 'Verwijder'	: verwijder();	break;
	case 'Opslaan'		: opslaan();	break;
	case 'uren'			: uren();		break;
	default				: 				break;
  }

  //-----------------------------------------------------------
  // Activiteit: Beheer de activiteiten voor de urenregistratie
  //-----------------------------------------------------------
  function activiteit() {
	//
	// Verwijder: Een activiteit verwijderen
	//
	function activ_verwijder() {
	  global $project;
	  $result = sql_query ("DELETE FROM uren_activ WHERE id=".$project["id"]);
	}

	//
	// Opslaan : Een gewijzigde activiteit opslaan
	//
	function activ_opslaan() {
	  global $project;
	  $result = sql_query ("UPDATE uren_activ SET activiteit='".$project["activiteit"]."', uurtarief=".$project["uurtarief"]." WHERE id=".$project["id"]);
	}
	//
	// Voegtoe : Een activiteit toevoegen
	//
	function activ_toevoegen() {
	  global $id, $naam, $tarief;
	  $result = sql_query ("INSERT INTO uren_activ SET activiteit=\"$naam\", uurtarief=$tarief");
	}

	global $user_id, $subaction;

	//
	// Main: Opties laten zien voor verwijderen/toevoegen van activiteiten
	//

	// Kijken of de gebruiker rechten heeft
	$result = sql_query ("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
	$row = sql_fetch_array ($result);
	if ($row["xs_projectmanage"]==0) {
	  dialog ("Activiteiten manager", "U heeft geen toegang tot de activiteiten manager", "project.php|Projectenlijst");
	  exit;
	}

	// Actie bepalen
	switch ($subaction) {
	  case 'Verwijder'	: activ_verwijder();	break;
	  case 'Opslaan'	: activ_opslaan();		break;
	  case 'Toevoegen'	: activ_toevoegen();	break;
	  default			: 						break;
	}

	html_header();
	venster_header("activiteiten wijzigen","",Array("terug","project.php"),0,-1);
	?>
	<tr><td>
	  <?php
	  // Activiteiten ophalen
	  $result = sql_query ("SELECT * FROM uren_activ");
	  while ($row = sql_fetch_array($result)) {
		print "<form action=\"project.php\" method=\"post\">\n";
		print "<input type=\"hidden\" name=\"action\" value=\"activiteit\">\n";
		print "<input type=\"hidden\" name=\"project[id]\" value=\"".$row["id"]."\">\n";
		print "<input class=\"inputtext\" type=\"tekst\" name=\"project[activiteit]\" value=\"".$row["activiteit"]."\">\n ";
		print "<input class=\"inputtext\" type=\"tekst\" name=\"project[uurtarief]\" value=\"".$row["uurtarief"]."\" size=\"7\">\n ";
		print "<input type=\"submit\" name=\"subaction\" value=\"Opslaan\">\n ";
		print "<input type=\"submit\" name=\"subaction\"  value=\"Verwijder\" onclick=\"return confirm('Het verwijderen van een activiteit kan kwalijke gevolgen hebben.\\nWeet u zeker dat u wilt doorgaan?')\"><br>\n";
		print "</form>";
	  }
	  print "</td></tr>";
	  venster_footer();
	  venster_header("activiteiten toevoegen","",Array(),0,-1);
	  ?>
	<tr><td>
	  <form action="project.php" method="post">
	  <input type="hidden" name="action" value="activiteit">
	  <input class="inputtext" type="text" name="naam">
	  <input class="inputtext" type="text" name="tarief" size="7">
	  <input type="submit" value="Toevoegen" name="subaction">
	  </form>
	</td></tr>
	<?php
	venster_footer();
	html_footer();
	exit;
	}

	//-----------------------------------------------------------
	// Wijzig: Wijzig project informatie
	//-----------------------------------------------------------
	function wijzig() {
	  global $db, $user_id, $project;
	  global $id;
	  html_header();

	  // Kijken of de user rechten heeft om een project aan te passen of
	  // om een nieuw project aan te maken.
	  $result = sql_query ("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
	  $row = sql_fetch_row($result);
	  if ($row[0]!=1) {
		dialog ("project beheer","u heeft geen rechten om projecten te beheren.","project.php|projecten lijst");
	  exit;
	  }

	  if ($id) {
		// Project informatie inladen
		$result = sql_query ("SELECT * FROM project WHERE id=$id");
		$project = sql_fetch_array($result);
		$titel = "bewerken";
	  } else {
		// Nieuw project
		$titel = "nieuw";
	  }
	  venster_header("projecten",$titel,Array(),0,-1);
	  ?>
	  <form action="project.php" method="post" name="deze">
		<input type="hidden" name="project[id]" value="<?php echo $project["id"] ?>">
	  <tr>
		<td align="right" <?php echo td(0) ?>><?php echo gettext("naam"); ?></td>
		<td <?php echo td(1) ?>><input class="inputtext" type="text" name="project[naam]" value="<?php echo $project["naam"] ?>"></td>
	  </tr><tr>
		<td align="right" <?php echo td(0) ?>><?php echo gettext("omschrijving"); ?></td>
		<td <?php echo td(1) ?>><textarea name="project[omschrijving]" cols="40" class="inputtextarea" rows="10"><?php echo $project["omschrijving"] ?></textarea></td>
	  </tr><tr>
		<td align="right" <?php echo td(0) ?>><?php echo gettext("projectmanager"); ?></td>
		<td <?php echo td(1) ?>>
		  <select name="project[beheerder]" class="inputselect">
			<option value="0"> <?php echo gettext("geen"); ?>
			<?php
			//lijst met users ophalen
			$res_user = sql_query("SELECT id,naam,actief FROM gebruikers WHERE actief=1 ORDER BY naam ASC");
			while ($row_user=sql_fetch_array($res_user)) {
			  if ($project["beheerder"]==$row_user["id"]) {
				print "<option value=\"$row_user[id]\" SELECTED>".$row_user["naam"];
			  } else {
				print "<option value=\"$row_user[id]\">".$row_user["naam"];
			  }
			}
			?>
		  </select>
		</td>
	  </tr><tr>
		<td align="right" <?php echo td(0) ?>><?php echo gettext("deelproject van"); ?></td>
		<td <?php echo td(1) ?>>
		  <select name="project[groep]" class="inputselect">
			<option value="0"> <?php echo gettext("geen"); ?>
			<?php
			// lijst met hoofdprojecten ophalen
			$res_proj = sql_query("SELECT id,naam,groep FROM project WHERE groep=0 ORDER BY naam ASC");
			while ($row_proj=sql_fetch_array($res_proj)) {
			  if ($project["groep"]==$row_proj["id"]) {
				print "<option value=\"$row_proj[id]\" SELECTED>".$row_proj["naam"];
			  } else {
				print "<option value=\"$row_proj[id]\">".$row_proj["naam"];
			  }
			}
			?>
		  </select>
		</td>
	  </tr><tr>
	  <?php if ($row["groep"]!=0) { ?>
		<td align="right" <?php echo td(0) ?>><?php echo gettext("status"); ?></td>
		<td <?php echo td(1) ?>><span class="d">
		  <select name="project[status]" class="inputselect">
			<option value="1"><?php echo gettext("geopend"); ?></option>
			<option value="2"><?php echo gettext("lopend"); ?></option>
			<option value="3"><?php echo gettext("afgesloten"); ?></option>
			<option value="4"><?php echo gettext("verworpen(als probleem)"); ?></option>
			<option value="5"><?php echo gettext("frozen(als probleem)"); ?></option>
		  </select>
		</td>
	</tr><tr>
	  <?php } ?>
		<td align="right" <?php echo td(0) ?>><?php echo gettext("actief"); ?></td>
		<td <?php echo td(1) ?>>
		  <input type="radio" value="1" name="project[actief]" <?php if ($project["actief"]==1) { print "CHECKED"; }?>><?php echo gettext("ja"); ?>
		  <input type="radio" value="0" name="project[actief]" <?php if ($project["actief"]==0) { print "CHECKED"; }?>><?php echo gettext("nee"); ?>
		</td>
	  </tr><tr>
		<td align="right" <?php echo td(0) ?>><?php echo gettext("relatie"); ?></td>
		<td <?php echo td(1) ?>>
		<input type="hidden" name="project[debiteur]" value="<?php echo $project[debiteur] ?>">
		<?php
		if ($project[debiteur]) {
			$result = sql_query("SELECT bedrijfsnaam FROM adres WHERE id=".$project[debiteur]);
		?>
			<?php echo sql_result($result,0) ?>&nbsp;&nbsp;&nbsp;<a href="javascript:var pop = window.open('../adres/?action=zoekRelProj');"><?php echo gettext("wijzig"); ?> <img src="img/knop_rechts.gif" border="0" valign="absmiddle"></a>
		<?php } else { ?>
			<a href="javascript:var pop = window.open('../adres/?action=zoekRelProj');"><?php echo gettext("zoeken"); ?> <img src="img/knop_rechts.gif" border="0"></a>
		<?php } ?>
		</td>
	  </tr><tr>
		<td colspan="2" align="right" <?php echo td(0) ?>>
		  	<input name="action" type="hidden" value="">
			 <?php if ($id){ ?>
				<a href="Javascript:document.deze.action.value='Verwijder';document.deze.submit();"><img src="img/knop_verwijder.gif" border="0"></a>
			 <?php } ?>
				<a href="Javascript:document.deze.action.value='Opslaan';document.deze.submit();"><img src="img/knop_ok.gif" border="0"></a>
		</td>
	  </tr></form>
	  <?php
	  venster_footer();
	  html_footer();
	  exit;
	}

	//-----------------------------------------------------------
	// Opslaan: Nieuw of gewijzigd project opslaan
	//-----------------------------------------------------------
	function opslaan() {
	  global $db, $user_id, $project;
	  // Kijken of de user rechten heeft om een project aan te passen of
	  // om een nieuw project aan te maken.
	  $result = sql_query ("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
	  $row = sql_fetch_row($result);
	  if ($row[0]!=1) {
		dialog ("project beheer","u heeft geen rechten om projecten te beheren.","project.php|projecten lijst");
		exit;
	  }
	  if ($project["status"]) {
	  }
	  if ($project["id"] == "") {
		// Nieuw project
		$query = "INSERT INTO project SET ";
	  } else {
		// Bestaand project updaten
		$query = "UPDATE project SET ";
	  }

	  $query .= "naam=\"".$project["naam"]."\", ";
	  $query .= "omschrijving=\"".$project["omschrijving"]."\", ";
	  $query .= "beheerder=\"".$project["beheerder"]."\", ";
	  $query .= "groep=\"".$project["groep"]."\", ";
	  $query .= "actief=".$project["actief"].", ";
	  if ($project["status"]) {
		$query .= "status=".$project["status"].", ";
	  } else {
		$query .= "status=0, ";
	  }
	  if ($project["debiteur"]=="") {
		$project["debiteur"]="\" \"";
	  }
	  $query .= "debiteur=".$project["debiteur"]."  ";
	  if ($project["id"] != "") {
		// Bestaand project updaten
		$query .= " WHERE id=".$project["id"];
	  }
	  $result = sql_query ($query, $db) or die ("Kan niet in de mysql database opslaan {".$query."}");
	}

	//-----------------------------------------------------------
	// Verwijder: Verwijder een project
	//-----------------------------------------------------------
	function verwijder() {
	  global $db, $user_id, $project, $id;

	  html_header();

	  // Kijken of de gebruiker rechten heeft om een project te verwijderen
	  $result = sql_query ("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
	  $row = sql_fetch_row($result);
	  if ($row[0]!=1) {
		dialog ("project verwijderen","u heeft geen rechten om projecten te verwijderen.","project.php|projecten lijst");
		exit;
	  }

	  if ($project["id"]!="") {
		// project werd verwijderd vanuit wijzig formulier
		$id=$project["id"];
	  }

	  $result = sql_query ("DELETE FROM project WHERE id=$id", $db);

	  dialog ("project verwijderen","het project is verwijderd", "project.php|lijst");
	  html_footer();

	  exit;
	}

	//-----------------------------------------------------------
	// Info: Laat informatie zien over een bepaald project
	//-----------------------------------------------------------

function info() {
	  global $db, $id;
	  html_header();
	  // Informatie over project inlezen
	  $result = sql_query ("SELECT * FROM project WHERE id=$id");
	  $row = sql_fetch_array($result);
	  $naam=$row["naam"];
	  venster_header("projecten",$naam,Array("wijzig","project.php?action=wijzig&id=".$row[id],"lijst","project.php"),0,-1);
	  ?>
	  <tr>
		<td align="right"><span class="d"><?php echo gettext("naam"); ?></span></td>
		<td><span class="d"><?php echo $row["naam"] ?>&nbsp;</td>
	  </tr><tr>
		<td align="right"><span class="d"><?php echo gettext("omschrijving"); ?></span></td>
		<td><span class="d"><?php echo nl2br($row["omschrijving"]) ?>&nbsp;</td>
	  </tr><tr>
		<td align="right"><span class="d"><?php echo gettext("projectmanager"); ?></span></td>
		<td><span class="d">
		<?php
		if ($row["beheerder"]==0) {
		  echo "geen";
		} else {
		  $res_user = sql_query("SELECT * FROM gebruikers WHERE id=$row[beheerder]");
		  $row_user = sql_fetch_array($res_user);
		  echo $row_user["naam"];
		}
		?>
		</span></td>
	  </tr><tr>
		<td align="right"><span class="d"><?php echo gettext("actief"); ?></span></td>
		<td><span class="d">
		<?php
		if ($row["actief"]==0) {
		  echo gettext("nee");
		} else {
		  echo gettext("ja");
		}
		?>
		</span></td>
	  </tr><tr>
		<td align="right"><span class="d"><?php echo gettext("debiteur"); ?></span></td>
		<td>
		<?php
		// Naam van de debiteur opzoeken dmv id
		if ($row["debiteur"]==null) {
		  $row["debiteur"]=0;
		}
		$result = sql_query ("SELECT id, debiteur_nr,bedrijfsnaam FROM adres WHERE id=".$row["debiteur"]);
		$row_debiteur = sql_fetch_array($result);
		if (!$row_debiteur["id"]) {
		  print "<span class='d'>";
			echo gettext("geen debiteur");
			echo "</span>";
		} else {
		  // Gewone id uit adres is debiteur_nr geworden. Dit kan problemen op gaan leveren.
		  print "<span class='d'><a href=\"klant.php?klant_id=".$row_debiteur["id"]."\">".$row_debiteur["bedrijfsnaam"]."</a></span>";
		}
		?>
		</td>
	  </tr>
	  <?php
	  venster_footer();
	  // deelprojecten ophalen
	  $res_sub = sql_query("SELECT count(*) FROM project WHERE groep=$id");
	  $row_sub = sql_fetch_row($res_sub);
	  if ($row_sub[0]!=0) {
		venster_header("projecten","deelprojecten",Array(),0,-1);
		$result=sql_query("SELECT * FROM project WHERE groep=$id");
		print "<tr><td>";
		tabel_header(1);
		print "<tr>";
		print "<td ".td(0)."><span class=\"dT\">";
		echo gettext("naam");
		echo "</span></td>";
		print "<td ".td(0)."><span class=\"dT\">";
		echo gettext("omschrijving");
		echo "</span></td>";
		print "<td ".td(0)."><span class=\"dT\">";
		echo gettext("uitvoerder");
		echo "</span></td>";
		print "<td ".td(0)."><span class=\"dT\">";
		echo gettext("actief");
		echo "</span></td>";
		print "<td ".td(0)."><span class=\"dT\">";
		echo gettext("status");
		echo "</span></td>";
		print "</tr>";
		while ($row=sql_fetch_array($result)) {
		  ?>
		  <tr>
			<td><span class="d"><a href="project.php?action=wijzig&id=<?php echo $row["id"] ?>"><?php echo $row["naam"] ?></a></span></td>
			<td><span class="d"><?php echo $row["omschrijving"] ?></span></td>
			<td><span class="d">
			<?php
			if ($row["beheerder"]==0) {
			  echo gettext("geen");
			} else {
			  $res_user = sql_query("SELECT * FROM gebruikers WHERE id=$row[beheerder]");
			  $row_user = sql_fetch_array($res_user);
			  echo $row_user["naam"];
			}
			?>
			</span></td>
			<td align="center"><span class="d"><?php if ($row["actief"]==1) { ?><img src="img/f_ja.gif" border="0"><?php } else { ?><img src="img/f_nee.gif" border="0"><?php } ?></span></td>
			<td><span class="d">
			<?php
			switch ($row["status"]) {
			  case '1'	:	echo gettext("geopend");		break;
			  case '2'	:	echo gettext("lopend");		break;
			  case '3'	:	echo gettext("afgesloten");	break;
			  case '4'	:	echo gettext("verworpen");	break;
			  case '5'	:	echo gettext("frozen");		break;
			  default	:	echo gettext("geen");		break;
			}
			?>
			</span></td>
		  </tr>
		  <?php
		}
		tabel_footer();
		print "</td></tr>";
		venster_footer();
	  }
	  html_footer();
	  exit;
	}

	//-----------------------------------------------------------
	// Uren: Genereer urenoverzichten van projecten
	//-----------------------------------------------------------<u>	function uren() {
	  //------------------------------------------------------------------------
	  // Totaal: Totaal overzicht van alle uren die aan een project zijn besteed
	  //------------------------------------------------------------------------
	  function totaal() {
		global $db, $id, $filter, $subaction, $factuur, $nftof, $ftonf;
		$totaal=0;
		// uren omzetten van factureren naar service en vice versa
		if ($nftof) {
		$query = "UPDATE urenreg set factureren=1 WHERE id=$nftof";
		$result = sql_query($query);
		}
		if ($ftonf) {
		$query = "UPDATE urenreg set factureren=0 WHERE id=$ftonf";
		$result = sql_query($query);
		}
		// Laatst gefactureerde datum zetten?
		if ($subaction=="Factuur") {
		  $result = sql_query ("UPDATE project SET lfact=".mktime(0,0,0,$factuur[m],$factuur[d],$factuur[y])." WHERE id=$id");
		}
		// Gebruikersnamen inlezen
		$result = sql_query ("SELECT id,naam FROM gebruikers");
		while ($row=sql_fetch_array($result)) {
		  $gebruiker[$row["id"]] = $row["naam"];
		}
		// Activiteiten inlezen
		$result = sql_query ("SELECT id,activiteit,uurtarief FROM uren_activ");
		while ($row = sql_fetch_array($result)) {
		  $activiteiten[$row["id"]] = $row["activiteit"];
		  $uurtarief[$row["id"]] = $row["uurtarief"];
		}
		// tabel om 2 vensters naast elkaar te tonen
		print "<table border=\"0\" cellspacing=\"2\" cellpadding=\"0\"><tr><td align=\"left\" valign=\"top\">";
		venster_header("","",Array(),0,-1);
		?>
		<tr>
		  <td <?php echo td(0) ?> align="right"><span class="dT"><?php echo gettext("laatste factuur"); ?></span></td>
		  <td <?php echo td(1) ?>>
		  <?php
			$res_factuur = sql_query ("SELECT lfact FROM project WHERE id=$id");
			$row_factuur = sql_fetch_array ($res_factuur);
			if ($row_factuur["lfact"]=="") {
			  echo gettext("nooit");
			} else {
			  $filter["from_day"] = strftime("%d",$row_factuur["lfact"]);
			  $filter["from_month"] = strftime("%m",$row_factuur["lfact"]);
			  $filter["from_year"] = strftime("%Y",$row_factuur["lfact"]);
			  print utf8_encode(strftime ("%d %B %Y",$row_factuur["lfact"]));
			}
		  ?>
		  </td>
		</tr><tr>
		  <td <?php echo td(0) ?> align="right"><span class="dT"><?php echo gettext("factureren"); ?></span></td>
		  <td <?php echo td(1) ?>>
			<form action="project.php" method="post">
			  <input type="hidden" name="action" value="uren">
			  <input type="hidden" name="id" value="<?php echo $id ?>">
			  <select name="factuur[d]">
			  <?php
			  for ($i=1; $i!=32; $i++) {
				if ($i==date("j")) {
				  ?><option value="<?php echo $i ?>" selected><?php echo $i ?><?php
				} else {
				  ?><option value="<?php echo $i ?>"><?php echo $i ?><?php
				}
			  }
			  ?>
			  </select>
			  <select name="factuur[m]">
			  <?php
			  for ($i=1; $i!=13; $i++) {
				if ($i==date("n")) {
				  ?><option value="<?php echo $i ?>" selected><?php echo utf8_encode(strftime("%B",mktime(0,0,0,$i+1,0,2000))) ?><?php
				} else {
				  ?><option value="<?php echo $i ?>"><?php echo utf8_encode(strftime("%B",mktime(0,0,0,$i+1,0,2000))) ?><?php
				}
			  }
			  ?>
			  </select>
			  <select name="factuur[y]">
			  <?php
			  for ($i=2001; $i!=2007; $i++) {
				if ($i==date("Y")) {
				  ?><option value="<?php echo $i ?>" selected><?php echo $i ?><?php
				} else {
				  ?><option value="<?php echo $i ?>"><?php echo $i ?><?php
				}
			  }
			  ?>
			  </select>
			  <input type="submit" value="Factuur" name="subaction">
			</form>
		  </td>
		</tr>
		<?php
		venster_footer();
		print "</td><td>";
		venster_header("","",Array(),0,-1);
		?>
		<!-- zoeken -->
		<form method="post" action="project.php">
		<input type="hidden" name="action" value="uren">
		<input type="hidden" name="id" value="<?php echo $id ?>">
		<tr>
		<td>
		<table border="0" cellspacing="0" cellpadding="0"><tr>
		  <td align="right" <?php echo td(1) ?>><span class="dT"><?php echo gettext("van"); ?></span></td>
		  <td>
			<select name="filter[from_day]">
			<?php
			for ($i=1; $i!=32; $i++) {
			  if ($i==$filter["from_day"]) {
				?><option value="<?php echo $i ?>" selected><?php echo $i ?><?php
			  } else {
				?><option value="<?php echo $i ?>"><?php echo $i ?><?php
			  }
			}
			?>
			</select>
			<select name="filter[from_month]">
			<?php
			for ($i=1; $i!=13; $i++) {
			  if ($i==$filter["from_month"]) {
				?><option value="<?php echo $i ?>" selected><?php echo utf8_encode(strftime("%B",mktime(0,0,0,$i+1,0,2000))) ?><?php
			  } else {
				?><option value="<?php echo $i ?>"><?php echo utf8_encode(strftime("%B",mktime(0,0,0,$i+1,0,2000))) ?><?php
			  }
			}
			?>
			</select>
			<select name="filter[from_year]">
			<?php
			for ($i=2001; $i!=2008; $i++) {
			  if ($i==$filter["from_year"]) {
				?><option value="<?php echo $i ?>" selected><?php echo $i ?><?php
			  } else {
				?><option value="<?php echo $i ?>"><?php echo $i ?><?php
			  }
			}
			?>
			</select>
		  </td>
		</tr><tr>
		  <td <?php echo td(1) ?> align="right"><span class="dT"><?php echo gettext("tot"); ?></span></td>
		  <td>
			<select name="filter[to_day]">
			<?php
			for ($i=1; $i!=32; $i++) {
			  if ($i==$filter["to_day"]) {
				?><option value="<?php echo $i ?>" selected><?php echo $i ?><?php
			  } else {
				?><option value="<?php echo $i ?>"><?php echo $i ?><?php
			  }
			}
			?>
			</select>
			<select name="filter[to_month]">
			<?php
			for ($i=1; $i!=13; $i++) {
			  if ($i==$filter["to_month"]) {
				?><option value="<?php echo $i ?>" selected><?php echo utf8_encode(strftime("%B",mktime(0,0,0,$i+1,0,2000))) ?><?php
			  } else {
				?><option value="<?php echo $i ?>"><?php echo utf8_encode(strftime("%B",mktime(0,0,0,$i+1,0,2000))) ?><?php
			  }
			}
			?>
			</select>
			<select name="filter[to_year]">
			<?php
			for ($i=2001; $i!=2008; $i++) {
			  if ($i==$filter["to_year"]) {
				?><option value="<?php echo $i ?>" selected><?php echo $i ?><?php
			  } else {
				?><option value="<?php echo $i ?>"><?php echo $i ?><?php
			  }
			}
			?>
			</select>
		  </td>
		  </tr></table>
		  </td>
		  <td>
			<select name="filter[user]" size="6">
			<?php
			$result = sql_query ("SELECT id,naam FROM gebruikers ORDER BY naam");
			while ($row = sql_fetch_array($result)) {
			  if ($row["id"]==$filter["user"]) {
				print "<option value=\"".$row["id"]."\" SELECTED>".$row["naam"]."\n";
			  } else {
				print "<option value=\"".$row["id"]."\">".$row["naam"]."\n";
			  }
			}
			?>
			</select> &nbsp; <br>
		  </td>
		</tr><tr>
		  <td colspan="2" align="right"><input type="submit" name="subaction" value="Filter"><input name="subaction" type="submit" value="Alles">&nbsp;&nbsp;</td>
		</tr>
		<?php
		venster_footer();
		print "</td></tr><tr><td colspan=\"2\" align=\"center\">";
		// Naam van project
		$result = sql_query ("SELECT naam FROM project WHERE id=$id");
		$row = sql_fetch_row($result);

		venster_header($row[0],"urenlijst",Array(),0,-1);
		?>
		<!-- Uren overzicht -->
		<tr><td>

		  <table cellspacing="1" cellpadding="2" border="0" width="100%"><tr>
			<td <?php echo td(0) ?>><span class="dT"><?php echo gettext("datum"); ?></span></td>
			<td <?php echo td(0) ?>><span class="dT"><?php echo gettext("uren"); ?></span></td>
			<td <?php echo td(0) ?>><span class="dT">&nbsp;</span></td>
			<td <?php echo td(0) ?>><span class="dT"><nobr><?php echo gettext("service uren"); ?></span></td>
			<td <?php echo td(0) ?>><span class="dT"><?php echo gettext("gebruiker"); ?></span></td>
			<td <?php echo td(0) ?>><span class="dT"><?php echo gettext("activiteit"); ?></span></td>
			<td <?php echo td(0) ?>><span class="dT"><?php echo gettext("omschrijving"); ?></span></td>
			<td <?php echo td(0) ?>><span class="dT"><?php echo gettext("prijs"); ?></span></td>
		  </tr>
		  <?php
		  // Query opbouwen aan de hand van de from_*, to_* en gebruik params
		  $query = "SELECT * FROM urenreg WHERE project=$id ";
		  if ($filter && $subaction!="Alles") {
			if (!$filter["from_day"])   { $filter["from_day"]=1; }
			if (!$filter["from_month"]) { $filter["from_month"]=1; }
			if (!$filter["from_year"])  { $filter["from_year"]=date("Y"); }
			if (!$filter["to_day"])   { $filter["to_day"]=31; }
			if (!$filter["to_month"]) { $filter["to_month"]=12; }
			if (!$filter["to_year"])  { $filter["to_year"]=date("Y"); }
			$from = mktime(0, 0, 0, $filter["from_month"], $filter["from_day"], $filter["from_year"]);
			$to   = mktime(24, 60, 60, $filter["to_month"], $filter["to_day"], $filter["to_year"]);
			$query .= "AND tijd_begin > $from AND tijd_eind < $to ";
			if ($filter["user"]) {
			  $query .= "AND gebruiker = ".$filter["user"]." ";
			}
		  }
		  if ($subaction=="Alles") {
			unset($filter["user"]);
		  }
		  $query .= "ORDER BY tijd_begin DESC ";
		  $result = sql_query ($query);
		  if (sql_numrows($result)==0) {
			print "<tr><td colspan=\"3\"".td(1).">";
			echo gettext("Geen uren geboekt");
			echo "</td></tr>";
		  }
		  while ($row=sql_fetch_array($result)) {
			if ($bgcolor==td(1)) { $bgcolor=td(0); } else { $bgcolor=td(1); }
			print "<tr valign=\"top\" $bgcolor>";
			print "<td $bgcolor>".date("d-m-Y",$row["tijd_begin"])." </td>";
			print "<td align=\"right\" $bgcolor>";
			if ($row["factureren"]==1) {
			  print number_format((($row["tijd_eind"]-$row["tijd_begin"])/3600),1);
			}
			print " &nbsp;</td>";
			print "<td $bgcolor>";
			if ($row["factureren"]==1) {
			  print "<a href=\"project.php?action=uren&id=$id&ftonf=".$row[id]."\"><img src=\"img/knop_rechts.gif\" border=\"0\"></a>";
			} else {
			  print "<a href=\"project.php?action=uren&id=$id&nftof=".$row[id]."\"><img src=\"img/knop_links.gif\" border=\"0\"></a>";
			}
			print "&nbsp;";
			print "</td>";
			print "<td align=\"right\" $bgcolor>";
			if ($row["factureren"]!=1) {
			  print number_format((($row["tijd_eind"]-$row["tijd_begin"])/3600),1);
			}
			print " &nbsp;</td>";
			print "<td $bgcolor><a href=\"uren.php?action=info&id=".$row["id"]."\" target=\"_blank\">".$gebruiker[$row["gebruiker"]]."</a> &nbsp;</td>";
			print "<td $bgcolor><nobr>".$activiteiten[$row["activiteit"]]."</nobr> &nbsp;</td>";
			print "<td $bgcolor>".$row["omschrijving"]." &nbsp;</td>";
			print "<td $bgcolor align=\"right\">&nbsp;";
			if ($row["factureren"]==1) {
			  print (number_format((($row["tijd_eind"]-$row["tijd_begin"])/3600),3)*$uurtarief[$row["activiteit"]])."</td>";
			}
			print "</tr>\n";
			if ($row["factureren"]==1) {
			  $totaal = $totaal + (($row["tijd_eind"]-$row["tijd_begin"])/3600);
			  $totaalprijs = $totaalprijs + ((($row["tijd_eind"]-$row["tijd_begin"])/3600)*$uurtarief[$row["activiteit"]]);
			}
		  }
		  ?>
		  <tr>
			<td <?php echo td(2) ?> colspan="6"><?php echo gettext("totaal uur:"); ?> <b><?php echo number_format($totaal,1) ?></b></td>
			<td <?php echo td(2) ?> align="right" colspan="2"><?php echo gettext("totaal prijs:"); ?> <b><?php echo number_format($totaalprijs,2) ?></b></td>
		  </tr></table></td>
		</tr>

		<?php
		venster_footer();
		print "</td></tr></table>";
		}

function wat_voor_functie_stond_hier_ooit() {

		global $user_id, $db, $subaction;

		//-------------------------------
		// Uren: Rechten en actie bepalen
		//-------------------------------
		html_header();

		// Kijken of de gebruiker rechten heeft om de uren te zien.
		$result = sql_query ("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
		$row = sql_fetch_row($result);
		if ($row[0]!=1) {
			dialog ("Project Urenoverzicht","U heeft geen rechten om een urenoverzicht van dit project te bekijken.","project.php|projecten lijst");
			exit;
		}

		switch ($action) {
			case 'uren'	: uren();	break;
			default		: totaal();	break;
		}

		html_footer();

		//</u>
		exit();
}	


  //-----------------------------------------------------------
  // Lijst: genereer een lijst met projecten en hun opties.
  //-----------------------------------------------------------
  html_header();

  if (!$top) { $top = 0; }

  // Xs_projectmanager status ophalen
  $result = sql_query ("SELECT xs_projectmanage FROM gebruikers WHERE id=$user_id");
  $row = sql_fetch_array ($result);
  $xs_projectmanage = $row["xs_projectmanage"];
  global $f;
  venster_header("projecten","zoeken",Array(),0,-1);
  ?>
  <form name="zoeken" action="project.php" method="post"><tr>
	<td><input type="text" class="inputtext" name="f[zoek]" value="<?php echo $f["zoek"] ?>"></td>
	<td><a href="Javascript:document.zoeken.submit();"><img src="img/knop_rechts.gif" border="0"></a></td>
  </tr></form>
  <?php
  venster_footer();
  venster_header("projecten","overzicht",Array("nieuw project","project.php?action=nieuw","manage activiteiten","project.php?action=activiteit","uren registratie","uren.php"),0,-1);
  ?>
  <tr>
	<td><?php tabel_header(0); ?><tr><td <?php echo td(1) ?> colspan="5">
	<?php
	// Haal totaal aantal projecten op
	$query = "SELECT COUNT(id) FROM project";
	if ($f["zoek"]!="") {
	  $query.= " WHERE (naam LIKE '%".$f["zoek"]."%' OR omschrijving LIKE '%".$f["zoek"]."%')";
	} else {
	  $query.=" WHERE groep=0";
	}
	$query.= " ORDER BY naam DESC";
	$result = sql_query($query);
	$row = sql_fetch_row($result);
	$totaal = $row[0];
	if ($totaal!=0) {
	  // Laat huidige positie in lijst zien
	  if (($top+10) > $totaal) { $bottom = $totaal; } else { $bottom=$top+10; }
	  if ($top==0) { print "1"; } else { print "$top"; }
	  print " t/m $bottom van de $totaal";
	}
	?>
	</td>
  </tr><tr>
	<td <?php echo td(0) ?> colspan="2"><span class="dT"> &nbsp; </span></td>
	<td <?php echo td(0) ?>><span class="dT"><?php echo gettext("naam"); ?></span></td>
	<td <?php echo td(0) ?>><span class="dT"><?php echo gettext("omschrijving"); ?></span></td>
	<td <?php echo td(0) ?>><span class="dT"><?php echo gettext("deelprojecten"); ?></span></td>
  </tr>
  <?php
  if ($totaal!=0) {
	// Haal informatie over elk project op
	$query = "SELECT * FROM project";
	if ($f["zoek"]!="") {
	  $query .= " WHERE (naam LIKE '%".$f["zoek"]."%' OR omschrijving LIKE '%".$f["zoek"]."%') AND groep=0";
	} else {
	  $query.=" WHERE groep=0";
	}
	$query .= " ORDER BY naam ASC LIMIT $top,10";

	$result = sql_query($query);
	while ($row = sql_fetch_array($result)) {
	  // kijk of er uren zijn gemaakt die nog niet gefactureerd zijn
	  if ($row["lfact"]=="") {
		$lfact_time=0;
	  } else {
		$lfact_time = $row["lfact"];
	  }
	  $query_fac = "SELECT * FROM urenreg WHERE project=".$row["id"]." AND tijd_begin > $lfact_time AND factureren=1";
	  $result_fac = sql_query($query_fac);
	  $uren_non_fac = sql_num_rows($result_fac);
	  print "<tr>";
	  print "<td ".td(1)." align=\"center\">";
	  if ($uren_non_fac!=0) {
		print "<img src=\"img/f_ja.gif\" alt=\"ongefactureerde uren aanwezig\">&nbsp;";
	  } else {
		print "<img src=\"img/f_nee.gif\" alt=\"geen ongefactureerde uren\">&nbsp;";
	  }
	  print "</td><td ".td(1)." width=\"55\" align='center'>";
	  print "<a href=\"project.php?action=info&id=".$row["id"]."\"><img src=\"img/knop_info.gif\" border=\"0\" alt=\"Info\"></a>&nbsp;";
	  print "<a href=\"project.php?action=uren&id=".$row["id"]."\"><img src=\"img/knop_uren.gif\" border=\"0\" alt=\"Uren\"></a>&nbsp;";
	  print "<a href=\"project.php?action=wijzig&id=".$row["id"]."\"><img src=\"img/knop_bewerk.gif\" border=\"0\" alt=\"Wijzig\"></a>";
	  print "</td>";
	  print "<td ".td(1).">".$row["naam"]."</td>";
	  print "<td ".td(1).">".substr($row["omschrijving"],0,60)	. "&nbsp;</td>";
	  $res=sql_query("SELECT * FROM project WHERE groep=$row[id]");
	  print "<td ".td(1).">";
	  while ($r=sql_fetch_array($res)) {
		print "<table><tr><td ".td(1)." align='left'>";
		print "<a href=\"project.php?action=info&id=".$r["id"]."\"><img src=\"img/knop_info.gif\" border=\"0\" alt=\"Info\"></a>&nbsp;";
		print "<a href=\"project.php?action=uren&id=".$r["id"]."\"><img src=\"img/knop_uren.gif\" border=\"0\" alt=\"Uren\"></a>&nbsp;";
		print "<a href=\"project.php?action=wijzig&id=".$r["id"]."\"><img src=\"img/knop_bewerk.gif\" border=\"0\" alt=\"Wijzig\"></a>";
		print "</td><td>";
		?>
		<span class="d"><a href="project.php?action=info&id=<?php echo $r["id"] ?>"><?php echo $r["naam"] ?></a></span><br>
		<?php
	  echo "</td></tr></table>";

	  }
	  echo "</td>";
	}
  }
  tabel_footer(); ?></td></tr>

  <tr>
	<td align="right">
	<?php
	// Laat de knoppen zien om door de projectenlijst te bladeren
	if ($top>0) {
	  ?><a href="project.php?top=<?php echo $top-10 ?>"><img src="img/knop_links.gif" border="0"></a><?php
	}
	if ($top>0 & $top+10<=$totaal) { print "&nbsp;"; }
	if ($top+10<=$totaal) {
	  ?><a href="project.php?top=<?php echo $top+10 ?>"><img src="img/knop_rechts.gif" border="0"></a><?php
	}
	?>
	</td>
  </tr>
<?php  venster_footer(); ?>
<?php  html_footer(); ?>
