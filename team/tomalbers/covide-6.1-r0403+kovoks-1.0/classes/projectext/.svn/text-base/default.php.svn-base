<?php
Class ProjectExt {
	/* function __construct {{{  */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		if (!$GLOBALS["covide"]->license["has_project"] || !$GLOBALS["covide"]->license["has_project_ext"]) {
			die("no license for this module");
		}
		switch ($_REQUEST["action"]) {
			case "extDepartmentEdit":
				$project_output = new ProjectExt_output();
				$project_output->extDepartmentEdit();
				break;
			case "extDepartmentSave":
				$project_data = new ProjectExt_data();
				$project_data->extSaveDepartment();
				break;
			case "extDepartmentsDelete":
				$project_data = new ProjectExt_data();
				$project_data->extDeleteDepartment();
				$project_output = new ProjectExt_output();
				$project_output->manageExtended();
				break;
			case "extActivityDelete":
				$project_data = new ProjectExt_data();
				$project_data->extDeleteActivity();
				$project_output = new ProjectExt_output();
				$project_output->manageExtended();
				break;
			case "extDefineFields":
				$project_output = new ProjectExt_output();
				$project_output->extDefineFields();
				break;
			case "defineMetaFields":
				$project_output = new ProjectExt_output();
				$project_output->defineMetaFields();
				break;
			case "extDefineFieldsEdit":
				$project_output = new ProjectExt_output();
				$project_output->defineMetaFieldsEdit();
				break;
			case "extDefineFieldsDelete":
				$project_data = new ProjectExt_data();
				$project_data->defineMetaFieldsDelete();
				$project_output = new ProjectExt_output();
				$project_output->defineMetaFields();
				break;
			case "extDefineFieldsSave":
				$project_data = new ProjectExt_data();
				$project_data->extSaveMetaField();
				break;
			case "extShowActivities":
				$project_output = new ProjectExt_output();
				$project_output->extShowActivities();
				break;
			case "extActivityEdit":
				$project_output = new ProjectExt_output();
				$project_output->extActivityEdit();
				break;
			case "extActivitySave":
				$project_data = new ProjectExt_data();
				$project_data->extActivitySave();
				break;
			case "extOpenActivity":
				$project_output = new ProjectExt_output();
				$project_output->extOpenActivity();
				break;
			case "filesys":
				$project_data = new ProjectExt_data();
				$project_data->openFileSys($_REQUEST["dir"]);
				break;
			case "dynamicFields":
				$project_output = new ProjectExt_output();
				$project_output->genDynamicProjectFields();
				break;
			case "extGenerateDocumentTree":
				$project_output = new ProjectExt_output();
				$project_output->extGenerateDocumentTree();
				break;
			case "extMergeTemplate":
				$project_data = new ProjectExt_data();
				$project_data->extMergeTemplate();
				break;
			case "defineMetaTables":
				$project_output = new ProjectExt_output();
				$project_output->extDefineTables();
				break;
			case "extShowMetaTable":
				$project_output = new ProjectExt_output();
				$project_output->extShowMetaTable();
				break;
			case "extend" :
			default :
				$project_output = new ProjectExt_output();
				$project_output->manageExtended();
				break;

		}
	}
	/* }}} */
}

?>
