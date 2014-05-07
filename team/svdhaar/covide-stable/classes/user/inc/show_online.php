<?php
	/**
	* Covide Groupware-CRM calendar module
	*
	* Covide Groupware-CRM is the solutions for all groups off people
	* that want the most efficient way to work to together.
	* @version %%VERSION%%
	* @license http://www.gnu.org/licenses/gpl.html GPL
	* @link http://www.covide.net Project home.
	* @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
	* @copyright Copyright 2000-2007 Covide BV
	* @package Covide
	*/

	if (!class_exists("User_output")) {
		die("no class definition found");
	}

	/* retreive  data */
	$user_data = new User_data();
	$q    = $_REQUEST["q"];
	$mode = $_REQUEST["mode"];
	$chan = $_REQUEST["channel"];

	/* some initializing */
	$full_user_data = array();
	$user_xs = $user_data->getUserdetailsById($_SESSION["user_id"]);

	/* handle calendar access */
	$calendar_data = new Calendar_data();
	$calendar_permissions = $calendar_data->getDelegationByVisitor($_SESSION["user_id"]);
	$calendars = array($_SESSION["user_id"]);
	if (is_array($calendar_permissions)) {
		foreach ($calendar_permissions as $p) {
			$calendars[] = $p["user_id"];
		}
	}
	/* online users handling */
	$online_users = $user_data->getOnlineUsers();

	if ($q || !$mode) {
		$sort = array();
		$user_info = $user_data->getUserlist(1, $q);
		foreach ($user_info as $k=>$v) {
			$v = $user_data->getUserdetailsById($k);
			$v["online"] = array_search($v["id"], $online_users);
			$user_info[$k] = $v;
			$sort[$k] = sprintf("%d_%s", $v["online"], $v["username"]);
		}
		$users = $user_info;

		/* sort by multi-dimensional index */
		array_multisort($sort, SORT_DESC, $users);

	} else {
		$user_info = $user_data->getUserlist(1);
		foreach ($online_users as $k=>$v) {
			$v = $user_data->getUserdetailsById($k);
			$v["online"] = array_search($v["id"], $online_users);
			$online_users[$k] = $v;
		}
		$users = $online_users;
	}
	unset($user_info);
	unset($online_users);

	$output = new Layout_output();
	$search = (!$q) ? "" : $q;
	$output->addTextField("searchstringonline", $search);
	$output->insertAction("forward", gettext("search"), "javascript:searchUser()");

	$output->addTag("span", array(
		"id" => "layer_all"
	));
		$output->insertAction("go_support", gettext("show all"), "javascript:toggleStatus()");
		$output->addSpace();
		$output->insertTag("a", gettext("show all users"), array(
			"href" => "javascript:toggleStatus()"
		));
	$output->endTag("span");
	$output->addTag("span", array(
		"id" => "layer_online",
		"style" => "display: none;"
	));
		$output->insertAction("go_support", gettext("show all"), "javascript:showOnlineUsers()");
		$output->addSpace();
		$output->insertTag("a", gettext("show online users"), array(
			"href" => "javascript: showOnlineUsers()"
		));
	$output->endTag("span");

	$output->addTag("br");
	if ($chan) {
		$output->insertTag("b", sprintf("%s [%s]",
			gettext("Invite user for channel"), $chan
		));
	}	else {
		$output->insertAction("user_chat", gettext("global chat"), "javascript: initPrivateChat();");
		$output->addSpace();
		$output->insertTag("a", gettext("global chat"), array(
			"href" => "javascript: initPrivateChat();"
		));
	}

	$table = new Layout_table(array(
		"cellspacing" => 1,
		"cellpadding" => 1,
		"width" => "100%",
		"border" => 0,
	));
	$table->addTableRow();
	$table->insertTableData($output->generate_output(), array("colspan" => 3));
	$table->endTableRow();
	if (!$users) {
		$table->addTableRow();
			$table->insertTableData(gettext("no users"), array("colspan" => 5), "header");
		$table->endTableRow();
	}

	if ($GLOBALS["covide"]->license["has_voip"])
		$voip = new Voip();
	foreach ($users as $user) {
		$i++;
		$table->addTableRow();
			$table->addTableData(array(
				"style" => "padding-top: 8px; width: 135px;"
			), "header");
				if ($user["online"])
					$table->insertAction("user_online", gettext("online"), "javascript: void(0)");
				else
					$table->insertAction("user_offline", gettext("offline"), "javascript: void(0)");

				if (!$chan) {
					$table->addSpace(1);
					$table->insertAction("go_note", gettext("send note"), "javascript:popup('?mod=note&action=edit&id=0&to_id=".$user["id"]."', 'notecreate', 920, 500, 1)");
					$table->addSpace();

					if (in_array($user["id"], $calendars)) {
						$table->insertAction("go_calendar", gettext("make appointment"), "javascript:popup('?mod=calendar&action=edit&id=0&user=".$user["id"]."', 'appcreate', 920, 600, 1)");
						$table->addSpace();
					} else {
						$table->insertAction("go_calendar", "", "javascript:void(0)", "", 0, 1);
						$table->addSpace();
					}
				}
				/* chat */
				if ($user["online"] && $_SESSION["user_id"] != $user["id"]) {
					if ($chan) {
						$table->insertAction("forward", gettext("invite to chat"), "javascript: initPrivateChat('".$user["id"]."', '".$chan."');");
						$table->addSpace();
						$table->insertTag("a", gettext("invite"), array("href" => "javascript: initPrivateChat('".$user["id"]."', '".$chan."');"));
					} else {
						$table->insertAction("user_chat", gettext("chat"), "javascript: initPrivateChat('".$user["id"]."');");
					}
					$table->addSpace();
				} else {
					$table->insertAction("user_chat", "", "javascript:void(0);", 0, 0, 1);
					$table->addSpace();
				}
				/* voip */
				if ($GLOBALS["covide"]->license["has_voip"]) {
					if ($user["voip_number"] && $user_xs["voip_device"] && $_SESSION["user_id"] != $user["id"]) {
						if ($user["online"]) {
							$voipstatus = $voip->getUserStatus($user["id"]);
							switch($voipstatus) {
							case -1:
								$table->insertAction("data_private_telephone", "", "javascript:void(0);", 0, 0, 1);
								break;
							case 8:
								$table->insertAction("data_phone_ringing", gettext("ringing"), "javascript: void(0);");
								break;
							case 1:
								$table->insertAction("data_private_telephone", gettext("busy"), "javascript: void(0);");
								break;
							case 0:
							default:
								$table->insertAction("data_business_telephone", gettext("voip call"), "javascript: loadXML('index.php?mod=voip&action=call&number=".$user["voip_number"]."');");
								break;
							}
						} else {
							$table->insertAction("data_private_telephone", gettext("voip call"), "javascript: loadXML('index.php?mod=voip&action=call&number=".$user["voip_number"]."');");
						}
						$table->addSpace();
					} else {
						$table->insertAction("data_private_telephone", "", "javascript:void(0);", 0, 0, 1);
						$table->addSpace();
					}
				}

			$table->endTableData();
			$table->addTableData("", "data");
				$table->addSpace();
				$table->addCode($user["username"]);
			$table->endTableData();
			$table->addTableData("", "data");
				$user_address = $user_data->getEmployeedetailsById($user["id"]);
				$table->addSpace();
				$table->addCode(sprintf("(%s)", $user_address["realname"]));
			$table->endTableData();
		$table->endTableRow();
	}
	$table->endTable();

	$div = new Layout_output();
	if ($i > 8)
		$div->insertTag("div", $table->generate_output(), array(
			"style" => "overflow-y: auto; width: 450px; height: 250px;"
		));
	else
		$div->insertTag("div", $table->generate_output(), array(
			"style" => "width: 450px;"
		));

	$div->exit_buffer();
?>
