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

Class Finance {
	/* function __construct {{{  */
	public function __construct() {
		/* TODO: translations */
		if (!$_SESSION["user_id"])
			$GLOBALS["covide"]->trigger_login();

		if (!$GLOBALS["covide"]->license["has_finance"])
			die("no license for this module");

		$user_data = new User_data();
		$user_info = $user_data->getUserPermissionsById($_SESSION["user_id"]);
		if (!$user_info["xs_turnovermanage"])
			$GLOBALS["covide"]->trigger_login();

		switch($_REQUEST["action"]) {
			case "verkopen" : tonen(); 			break;	//openstaande verkopen tonen
			case "inkopen"  : tonen();			break;	//openstaande inkopen tonen
			case "verkoop"  : tonen();			break;	//openstaande verkopen (compatibiliteit)
			case "detail"		: toondetail(); break;	//detail overzicht van boekingen
			case "wijzig" 	: invoeren();		break;
			case "opslaan"	: opslaan();		break;
			case "save"			: schrijf();		break;
			case "btw"			: btw();				break;
			case "invoer"		: invoeren();		break;
			case "invoerenDetail" 		: invoerenDetail(); 			break;
			case "invoerenBevestig" 	: invoerenBevestig();			break;
			case "invoerenDatabase"		: invoerenDatabase();			break;
			case "reset"							: reset_boeking(); 				break;
			case "omzet"							: toonOmzet();						break;
			case "fixeer"							: fixeer();								break;
			case "fixeerprint"				:	fixeerPrint();					break;
			case "tonenSpeciaal":
				$finance_output = new Finance_output();
				$finance_output->tonenSpeciaal();
				break;
			case "deleteSpeciaal":
				$finance_data = new Finance_data();
				$finance_data->deleteSpeciaal($_REQUEST["id"]);
				$finance_output = new Finance_output();
				$finance_output->tonenSpeciaal();
				break;
			case "invoerSpeciaal":
				$finance_output = new Finance_output();
				$finance_output->invoerenSpeciaal();
				break;
			case "invoerSpeciaalBevestig":
				$finance_output = new Finance_output();
				$finance_output->invoerenSpeciaalBevestig();
				break;
			case "autocomplete":
				$finance_data = new Finance_data();
				$finance_data->autocomplete($_REQUEST["str"]);
			default:
				$finance_output = new Finance_output();
				$finance_output->toonwelkom();
				break;
		}

	}
}
?>