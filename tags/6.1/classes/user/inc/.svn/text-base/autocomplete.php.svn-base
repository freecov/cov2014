<?
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
	$users = $this->getUserList(1, $str);
	foreach ($users as $k=>$v) {
		$userlist[$k] = $v;
	}

	if (is_array($userlist)) {
		natcasesort($userlist);
	}

	if ($archive) {
		echo gettext("archiefgebruiker")."|".$this->getArchiveUserId()."|1#";
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