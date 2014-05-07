<?php
/**
 * Covide Groupware-CRM user module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2008 Covide BV
 * @package Covide
 */
if (!class_exists("User_output")) {
	die("no class definition found");
}

$user_data = new User_data();
$user_data->_init_roles();
$userinfo = $user_data->getUserDetailsById($_SESSION["user_id"]);

if (!$userinfo["xs_usermanage"]) {
	if (!$userinfo["xs_limitusermanage"]) {
		/* we dont have access to other users. Redirect to edit screen for user */
		header("Location: index.php?mod=user&action=useredit&id=".$_SESSION["user_id"]);
		exit;
	}
}

$output = new Layout_output();
$output->layout_page();

$venster_access = new Layout_venster(array(
	"title" => gettext("permissions")
));
$venster_access->addVensterData();
	$table = new Layout_table(array("cellspacing" => 1));
	foreach ($user_data->manager_roles as $role=>$roledata) {
		//var_dump($roledata);
		if (is_array($roledata["license"])) {
			$enabled = false;
			foreach ($roledata["license"] as $license) {
				if ($GLOBALS["covide"]->license[$license]) {
					$enabled = true;
				}
			}
			if (!$enabled) {
				continue;
			}
		}
		if ($GLOBALS["covide"]->license["disable_basics"]) {
			if ($roledata["disable_basics"]) {
				continue;
			}
		}
		$table->addTableRow();
			$table->addTableData("", "data");
				if ($roledata["action"]) {
					$table->insertAction($roledata["action"], $roledata["name"], "");
				}
				if ($roledata["icon"]) {
					$table->insertImage($roledata["icon"], $roledata["name"], "");
				}
				$table->addCode($roledata["name"]);
			$table->endTableData();
			$table->addTableData("", "data");
				if (is_array($roledata["users"])) {
					asort($roledata["users"]);
					foreach ($roledata["users"] as $user_id=>$user_name) {
						$table->insertLink($user_name, array("href" => "index.php?mod=user&action=useredit&id=".$user_id));
						$table->addTag("br");
					}
				}
			$table->endTableData();
		$table->endTableRow();
	}
	$table->endTable();
	$venster_access->addCode($table->generate_output());
	unset($table);
$venster_access->endVensterData();
$output->addCode($venster_access->generate_output());
unset($venster_access);
$output->layout_page_end();
$output->exit_buffer();
?>
