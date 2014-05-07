<?php
/**
 * Covide Chat module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Chat {

	private $voip_data;
	private $user_data;

	public function __destruct() {
		if ($_SESSION["user_id"]) {
			$this->voip_data->updateCallFile();
		}
	}
	public function __construct() {
		if ($_SESSION["user_id"]) {

			$this->voip_data = new Voip_data();
			$this->user_data = new User_data();
			$user_data =& $this->user_data;

			switch ($_REQUEST["action"]) {
				case "notice":
					$window_id = rand(0,9999999);

					$auto_accept = array();

					$output = new Layout_output();
					$output->layout_page("chat", 1);
					$output->start_javascript();
						$output->addCode("
							function init_chat(ident) {
								//open popup
								window.open('?mod=chat&ident=' + ident, 'chat_".$window_id."', 710, 670);

								//remove record
								parent.handleVoipAction(ident, 0);

								var iv = setTimeout('closepopup()', 2000);
							}
							function close_window() {
								setTimeout('closepopup()', 100);
							}
						");
					$output->end_javascript();

					$idents = explode(",", $_REQUEST["idents"]);
					foreach ($idents as $k=>$v) {
						$idents[$k] = sprintf("'%s'", $v);
					}
					$idents = implode(",", $idents);

					$data = array();
					$q = sprintf("select * from active_calls where ident IN (%s)", $idents);
					$res = sql_query($q);
					while ($row = sql_fetch_assoc($res)) {
						$d = mktime()-$row["timestamp"];
						if ($d < 0)
							$d = sprintf("%d %s", (int)(0-($d/60)), gettext("min in the future")."!");
						elseif ($d < 60)
							$d = sprintf("< 1 ".gettext("minute ago"));
						else
							$d = sprintf("%d %s", (int)$d/60, gettext("minutes ago"));

						$row["timestamp_h"] = $d;
						switch ($row["invite"]) {
							case 0:
								// other user has declined invitation
								$row["decline"] = sprintf("javascript: parent.handleVoipAction('%s', 0); close_window();", $row["ident"]);
								break;
							case 1:
								// current user has received invitation
								$row["accept"]  = sprintf("javascript: parent.handleVoipAction('%s', 1); close_window();", $row["ident"]);
								$row["decline"] = sprintf("javascript: parent.handleVoipAction('%s', 0); close_window();", $row["ident"]);								break;
							case 2:
								// other user has accepted invitation
								$auto_accept[]  = $row["ident"];
								$row["accept"]  = sprintf("javascript: init_chat('%s'); ", $row["ident"]);
								break;
						}
						$data[] = $row;

					}

					$venster = new Layout_venster(array(
						"title" => gettext("Chat"),
						"subtitle" => gettext("new invite")
					));
					$venster->addVensterData();
						$venster->insertTag("b", gettext("You have a new chat invite or event,"));
						$venster->addTag("br");

						$view = new Layout_view();
						$view->addData($data);
						$view->addMapping(gettext("time"), "%timestamp_h");
						$view->addMapping(gettext("event"), "%name");
						$view->addMapping("", "%%complex_actions");

						$view->defineComplexMapping("complex_actions", array(
							array(
								"type"  => "action",
								"src"   => "ok",
								"check" => "%accept",
								"alt"   => gettext("accept"),
								"link" => "%accept"
							),
							array(
								"type"  => "action",
								"src"   => "cancel",
								"check" => "%decline",
								"alt"   => gettext("cancel"),
								"link" => "%decline"
							)
						));
						$venster->addCode($view->generate_output());
					$venster->endVensterData();
					$output->addCode($venster->generate_output());

					foreach ($auto_accept as $a) {
						$output->start_javascript();
							$output->addCode(sprintf("init_chat('%s');", $a));
						$output->end_javascript();
					}
					$output->layout_page_end();
					$output->exit_buffer();
					break;

				case "accept":
					$accept = 1;
					// no break;

				case "cancel":
					/* if the invited user presses cancel or accept */

					/* get old record data */
					$q = sprintf("select * from active_calls where ident = '%s'", $_REQUEST["ident"]);
					$res = sql_query($q);
					if (sql_num_rows($res) > 0) {
						/* if the request does still exist */
						$row = sql_fetch_assoc($res);

						/* if the request was an invite */
						if ($row["invite"] == 1) {

							if ($accept) {
								// copy over the old ident
								$ident = $_REQUEST["ident"];
							} else {
								// create a new decline ident
								$ident = $this->voip_data->mkIdent(array(
									$row["user_id"],
									$row["user_id_src"]
								));
							}
							$txt = ($accept) ? gettext("user accepted invitation") : gettext("user declined invitation");

							$data = array(
								"timestamp"   => mktime(),
								"user_id"     => $row["user_id_src"], //swap
								"user_id_src" => $row["user_id"],     //swap
								"invite"      => ($accept) ? 2 : 0,
								"ident"       => $ident,
								"name"        => sprintf("[%s] %s",
									$user_data->getUserNameById($_SESSION["user_id"]),
									$txt)
							);
							$new_q = sprintf("insert into active_calls (name, timestamp, user_id, user_id_src, invite, ident)
								values ('%s', %d, %d, %d, %d, '%s')",
								$data["name"],
								$data["timestamp"],
								$data["user_id"],
								$data["user_id_src"],
								$data["invite"],
								$data["ident"]
							);
						}
						/* delete the old record */
						$q = sprintf("delete from active_calls where ident = '%s'", $_REQUEST["ident"]);
						sql_query($q);

						//insert new record when channel is not prefixed by _
						if ($new_q && !preg_match("/^_/s", $_REQUEST["ident"]))
							sql_query($new_q);
					}
					break;

				case "private":
					/* create a new private channel or invite a user to one? */
					if ($_REQUEST["channel"]) {
						// if this is an invite done later, prefix channel with _
						$ident = sprintf("_%s", $_REQUEST["channel"]);
					} else {
						$ident = $this->voip_data->mkIdent(array(
							$_SESSION["user_id"], $_REQUEST["user"]
						));
					}

					// create a new record for active_calls
					$data = array(
						"timestamp"   => mktime(),
						"user_id"     => $_REQUEST["user"],
						"user_id_src" => $_SESSION["user_id"],
						"invite"      => 1,
						"ident"       => $ident,
						"name"        => sprintf("[%s] %s",
							$user_data->getUserNameById($_SESSION["user_id"]),
							gettext("user sent invitation"))
					);

					/* cancel old requests for the current user to the requested user */
					$q = sprintf("delete from active_calls where user_id = %d and user_id_src = %d and invite = 1",
						$data["user_id"], $data["user_id_src"]);
					sql_query($q);

					/* create a new request */
					$q = sprintf("insert into active_calls (name, timestamp, user_id, invite, user_id_src, ident)
						values ('%s', %d, %d, %d, %d, '%s')",
						$data["name"],
						$data["timestamp"],
						$data["user_id"],
						$data["invite"],
						$data["user_id_src"],
						$data["ident"]
					);
					sql_query($q);
					$this->voip_data->updateCallFile();
					// no break!

				default:
					if ($_REQUEST["ident"])
						$ident = $_REQUEST["ident"];

					$p = "?load_external_file=1";
					if ($ident) {
						$p .= sprintf("&c=%s", preg_replace("/^_/s", "", $ident));

						if ($_REQUEST["ident"]) {
							// trim _
							$ident = preg_replace("/^_/s", "", $_REQUEST["ident"]);

							/* if someone joined this request and it is the target user, delete the invitation */
							$q = sprintf("delete from active_calls where user_id = %d and invite = 2 and ident = '%s'",
								$_SESSION["user_id"], $ident);
							sql_query($q);
						}
					}
					header("Location: classes/chat/inc/".$p);
			}
		}
		exit();
	}
}
?>
