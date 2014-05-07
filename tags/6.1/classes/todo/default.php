<?php
Class Todo {
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		switch ($_REQUEST["action"]) {
			case "edit_todo" :
				$todo_output = new Todo_output();
				$todo_output->edit_todo();
				break;
			case "edit_multi" :
				$todo_output = new Todo_output();
				$todo_output->edit_multi_todo();
				break;
			case "save_todo" :
				$todo_data = new Todo_data();
				$todo_data->save_todo();
				break;
			case "save_multi" :
				$todo_data = new Todo_data();
				$todo_data->save_multi($_REQUEST);
				$todo_output = new Todo_output();
				$todo_output->show_todos();
				break;
			case "delete_todo" :
				$todo_data = new Todo_data();
				$todo_data->delete_todo();
				break;
			case "delete_multi" :
				$todo_data = new Todo_data();
				$todo_data->delete_multi_todo($_REQUEST);
				$todo_output = new Todo_output();
				$todo_output->show_todos();
				break;
			case "show_info" :
				$todo_output = new Todo_output();
				$todo_output->show_info();
				break;
			case "xml_check" :
				$todo_data = new Todo_data();
				$todo_data->xml_check($_REQUEST);
				break;
			default :
				$todo_output = new Todo_output();
				$todo_output->show_todos();
				break;
			/* end switch statement */
		}
	}
}
?>
