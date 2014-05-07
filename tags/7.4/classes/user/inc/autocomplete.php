<?
	/**
	 * Covide Groupware-CRM user module
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
	if (!class_exists("User_data")) {
		exit("no class definition found");
	}

	$str = $_REQUEST["str"];
	$archive = $_REQUEST["archive"];

	$users = array();

	header("Content-type: text/plain; charset=ISO-8859-1");
	natsort($users);

	$userlist = array();
	if ($_REQUEST["showgroups"]) {
		$group = $this->getGroupList(1, $str);
		foreach ($group as $k=>$v) {
			$userlist[$k] = $v;
		}
	}
	if ($_REQUEST["showcalendar"])
		$users = $this->getUserList(1, $str, 0, $_SESSION["user_id"]);
	else
		$users = $this->getUserList(1, $str);
	foreach ($users as $k=>$v) {
		$userlist[$k] = $v;
	}

	if (is_array($userlist)) {
		natcasesort($userlist);
	}

	if ($archive) {
		echo gettext("archiveuser")."|".$this->getArchiveUserId()."|1#";
	}
	$max = 15;

	if (is_array($userlist)) {
		foreach ($userlist as $k=>$v) {
			$cur++;
			echo $v."|".$k."|0#";
			if ($cur >= $max) {
				exit();
			}
		}
	}
?>
