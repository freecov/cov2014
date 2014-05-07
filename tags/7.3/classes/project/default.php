<?
Class Project {
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
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
			case "show_activities" ;
				$project_output = new Project_output();
				$project_output->showActivities();
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
			default:
				$project_output = new Project_output();
				$project_output->show_overview();
				break;
			/* end switch statement */
		}
	}
}
?>
