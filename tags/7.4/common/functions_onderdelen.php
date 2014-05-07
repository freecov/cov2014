<?
if ($_SESSION["user_id"]) {
	//-----------
	// menu parts
	//-----------
	$q = "select id from users where username = 'archiefgebruiker'";
	$resc =& $GLOBALS["covide"]->db->query($q);

	//$resc->fetchInto($archief_user_idx);
	$archief_user_idx = sql_fetch_row($resc);
	$archief_user_id = $archief_user_idx["id"];

	if ($_SESSION["user_id"]!=$archief_user_id) {

		if ($GLOBALS["covide"]->license["disable_basics"]!=1) {
			$ond     = array("adressen", "agenda", "notities", "email", "bestandsbeheer");
			$ondLink = array($parent."?mod=address", $parent."?mod=calendar", $parent."?mod=note", $parent."?mod=email" ,$parent."?mod=filesystem");
			$ondAlt  = array(gettext("Addresses"), gettext("Calendar"), gettext("Notes"), gettext("E-mail"), gettext("File management"));
			$ond[count($ond)]         = "nieuwsbrief";
			$ondLink[count($ondLink)] = $parent."?mod=newsletter";
			$ondAlt[count($ondAlt)]   = gettext("Newsletter");
		} else {
			/*
			$ond     = array("adressen", "bestandsbeheer");
			$ondLink = array($parent."adres/", $parent."bestandsbeheer/");
			$ondAlt  = array(gettext("Addresses"), gettext("File management"));
			*/
			if ($GLOBALS["covide"]->license["has_voip"]) {
				//if voip enable notes for support calls
				$ond     = array("adressen", "notities");
				$ondLink = array($parent."?mod=address", $parent."?mod=note");
				$ondAlt  = array(gettext("Addresses"), gettext("Notes"));
			} else {
				$ond     = array("adressen");
				$ondLink = array($parent."?mod=address");
				$ondAlt  = array(gettext("Addresses"));
			}
		}

		if ($GLOBALS["covide"]->license["has_project"]==1) {
			$ond[count($ond)]         = "projecten";
			$ondLink[count($ondLink)] = $parent."project/";
			$ondAlt[count($ondAlt)]   = gettext("Projects");
		}
		if ($GLOBALS["covide"]->license["has_issues"]==1) {
			$ond[count($ond)]         = "klachten";
			$ondLink[count($ondLink)] = $parent."klachten.php?start=1";
			$ondAlt[count($ondAlt)]   = gettext("Support");
		}
		// for now this is for Terrazur only
		if ($GLOBALS["covide"]->license["has_finance"]==1) {
			$ond[count($ond)]         = "omzet";
			$ondLink[count($ondLink)] = $parent."finance/";
			$ondAlt[count($ondAlt)]   = gettext("Covide Finance");
		}
		// end Terrazur only stuff
		// Link to Dutch company that does paycheck stuff

		if ($GLOBALS["covide"]->license["has_salariscom"]==1) {
			$resx = $db->query("SELECT xs_salariscommanage FROM users WHERE id=".$_SESSION["user_id"]);
			//$resx->fetchInto($rxx);
			$rxx = sql_fetch_row($resx);
			$rx = $rxx["xs_salariscommanage"];
			if ($rx) {
				$ond[count($ond)]         = "salaris";
				$ondLink[count($ondLink)] = "http://www.salaris.com/inloggen.htm";
				$ondAlt[count($ondAlt)]   = "salaris.com";
			}
		}

		// end Dutch paycheck management
		if ($GLOBALS["covide"]->license["has_faq"]==1) {
			$ond[count($ond)]         = "vraag en antwoord";
			$ondLink[count($ondLink)] = $parent."faq.php";
			$ondAlt[count($ondAlt)]   = gettext("Question and Answer");
		}
		if ($GLOBALS["covide"]->license["has_forum"]==1) {
			$ond[count($ond)]         = "forum";
			$ondLink[count($ondLink)] = $parent."forum.php";
			$ondAlt[count($ondAlt)]   = gettext("Forum");
		}
		if ($GLOBALS["covide"]->license["has_announcements"]==1) {
			$resp = $db->query("SELECT xs_pollmanage FROM users WHERE id=".$_SESSION["user_id"]);
			//$resp->fetchInto($rpp);
			$rpp = sql_fetch_row($resp);
			$rp = $rpp["xs_pollmanage"];
			if ($rp) {
				$ond[count($ond)]         = "prikbord";
				$ondLink[count($ondLink)] = $parent."prikbord.php";
				$ondAlt[count($ondAlt)]   = gettext("Clipboard");
			}
		}

	} else {
		$ond = array();
		$ondLink = array();
		$ondAlt = array();
	}
	if ($gprs) $theme = 6;

	//-----------------
	// horizontal menu
	//-----------------

	if ($_SESSION["user_id"]!=$archief_user_id) {

		$ondO     = array("instellingen", "help");
		$ondOLink = array($parent."gebruikersbeheer/index.php", "javascript:openWinHelp('".$parent."handleiding/$hlindex?q=1&theme=$theme');");
		$ondOAlt  = array(gettext("Settings"), gettext("Manual"));

		//snack. Terrazur internal module to order junkfood on friday
		if ((date("w")==5) && $licensie["has_snack"]==1) {
			$ondO[count($ondO)]         = "snack";
			$ondOLink[count($ondOLink)] = $parent."snack.php";
			$ondOAlt[count($ondOAlt)]   = gettext("Snackorder");
		}

	}

	//logout icon is always the last in the horizontal top menu
	$ondO[count($ondO)]         = "uitloggen";
	$ondOLink[count($ondOLink)] = $parent."login.php?action=logout";
	$ondOAlt[count($ondOAlt)]   = gettext("Logout");
}
?>
