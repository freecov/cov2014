<?php
/**
 * Covide Groupware-CRM Project module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */
Class Project {
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		} else {
			$sql = sprintf("select is_active from users where id = %d", $_SESSION["user_id"]);
			$res = sql_query($sql);
			$row = sql_fetch_assoc($res);
			if ($row["is_active"] != 1) {
				unset($_SESSION["user_id"]);
				$GLOBALS["covide"]->trigger_login();
			}
		}
		if (!$GLOBALS["covide"]->license["has_project"]) {
			die("no license for this module");
		}
		switch ($_REQUEST["action"]) {
			case "pick_project"   :
			case "search_project" :
			case "searchproject"  :
			case "searchProject"  :
				$project_output = new Project_output();
				$project_output->pick_project();
				break;
			case "showinfo" :
				$project_output = new Project_output();
				$project_output->show_info($_REQUEST["id"], $_REQUEST["master"]);
				break;
			case "edit" :
				$project_output = new Project_output();
				$project_output->edit_project($_REQUEST["id"], $_REQUEST["master"], $_REQUEST["sub_of"]);
				break;
			case "save_project" :
			case "save" :
				$project_data = new Project_data();
				$project_data->saveProject();
				break;
			case "delete_project" :
				$project_data = new Project_data();
				$project_data->deleteProject($_REQUEST["project"]["id"], $_REQUEST["project"]["master"]);
				break;
			case "showhours" :
				$project_output = new Project_output();
				$project_output->showHours();
				break;
			case "toggleHours" :
				$project_data = new Project_data();
				$project_data->toggleHours();
				break;
			case "toggleActive" :
				$project_data = new Project_data();
				$project_data->toggleActive();
				break;
			case "setlfact" :
				$project_data = new Project_data();
				$project_data->setLfact();
				break;
			case "showcal" :
				$project_output = new Project_output();
				$project_output->showCal();
				break;
			case "shownotes" :
				$project_output = new Project_output();
				$project_output->showNotes();
				break;
			case "showtodos" :
				$project_output = new Project_output();
				$project_output->showTodos();
				break;
			case "showsales" :
				$project_output = new Project_output();
				$project_output->showSales();
				break;
			case "show_activities" ;
				$project_output = new Project_output();
				$project_output->showActivities();
				break;
			case "show_costs" ;
				$project_output = new Project_output();
				$project_output->showCosts();
				break;
			case "hour_overview" :
				$project_output = new Project_output();
				$project_output->hourOverview();
				break;
			case "updaterelxml" :
				$project_data = new Project_data();
				$project_data->updateRel($_REQUEST["project_id"], $_REQUEST["master"], $_REQUEST["address_id"]);
				break;
			case "autocomplete_project_name":
				$project_data = new Project_data();
				$project_data->autocomplete_project_name($_REQUEST["str"]);
				break;
			case "showprojecthours" :
				$project_output = new Project_output();
				$project_output->showProjectHours($_REQUEST);
				break;
			case "exporthours" :
				$project_output = new Project_output();
				$project_output->exportHours($_REQUEST);
				break;
			case "hour_stats" :
				$project_output = new Project_output();
				$project_output->hourStats();
				break;
			case "exporthoursxml" :
				$project_output = new Project_output();
				$project_output->exportHoursXML($_REQUEST);
				break;
			case "hoursuserperproject" :
				$project_output = new Project_output();
				$project_output->hoursUserPerProject();
				break;
			case "hoursuserperday" :
				$project_output = new Project_output();
				$project_output->hoursUserPerDay();
				break;
			case "show_activitygroups" :
				$project_output = new Project_output();
				$project_output->showActivityGroups();
				break;
			case "payrollexport":
				$project_output = new Project_output();
				$project_output->payrollExport($_REQUEST["start"], $_REQUEST["end"], $_REQUEST["export_id"]);
				break;
			case "list_payrollexports":
				$project_output = new Project_output();
				$project_output->list_payrollexports($_REQUEST["pr_options"]);
				break;
			default:
				$project_output = new Project_output();
				$project_output->show_overview();
				break;
			/* end switch statement */
		}
	}
}
?>
