<?php
/**
 * Covide Groupware-CRM Voip module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
class Voip {
	const include_dir = "classes/voip/inc/";
	const class_name  = "voip";
	//set some defaults when calling
	public function __construct() {
		/* updatecallts writes a file from remote so no need to be logged in */
		if (!$_SESSION["user_id"] && $_REQUEST["action"] != "updatecallts") {
			$GLOBALS["covide"]->trigger_login();
		}
		if (!$GLOBALS["covide"]->license["has_voip"] && $_REQUEST["action"] != "active_calls") {
			die("no license for this module");
		}
		if ($GLOBALS["covide"]->license["has_voip"]) {
			if ($_REQUEST["action"] != "updatecallts") {
				$userdata = new User_data();
				$userinfo = $userdata->getUserDetailsById($_SESSION["user_id"]);
			} else {
				$userinfo = array();
			}

			#write to /tmp/voip.debug instead of socket
			$this->debug=0;
			$this->crlf = "\r\n";
			$this->convert_cr = 1;
			$this->user_device     = $userinfo["voip_device"];
			$this->callerid        = $userinfo["voip_number"];
			$this->context         = $GLOBALS["covide"]->license["code"];
			$this->whotocall       = sprintf("%s", $_REQUEST["number"]);
			$this->sms             = $sms;

			$this->_set_server();
		}
		switch ($_REQUEST["action"]) {
			case "active_calls" :
				$data = new Voip_data();
				$data->getActiveCalls();
				break;
			case "previewfax":
				$fax_output = new Voip_output();
				$fax_output->previewFax();
				break;
			case "viewfax" :
				$fax_output = new Voip_output();
				$fax_output->viewFax();
				break;
			case "faxlist" :
				$fax_output = new Voip_output();
				$fax_output->showFaxes();
				break;
			case "deletefax" :
				$fax_data = new Voip_data();
				$fax_data->deleteFax();
				break;
			case "alterfax" :
				$fax_data = new Voip_data();
				$fax_data->alterFax($_REQUEST["faxid"], $_REQUEST["address_id"], 1);
				break;
			case "updatecallts" :
				$voip_data = new Voip_data();
				$voip_data->updateCallTS($_REQUEST, $this->manager_login, $this->manager_pwd);
				break;
			case "call" :
				$this->send();
				break;
			case "getUserStatus" :
				$this->getUserStatus($_REQUEST["user_id"]);
				break;
		}
	}

	public function _set_server() {
		// grab settings from the database
		$sql = "SELECT * FROM asterisk_settings";
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$this->server_hostname = $row["server_hostname"];
		$this->server_port = $row["server_port"];
		$this->server_timeout = $row["server_timeout"];
		$this->manager_login = $row["manager_login"];
		$this->manager_pwd = $row["manager_pwd"];
	}

	public function _set_timeout ($timeout) { $this->timeout = $timeout; }

	//send commands to the manager socket
	public function _write (&$fp, $line) {
		if ($this->debug) {
			echo "<pre>";
			echo $line;
			echo "<pre>";
		}
		fputs($fp, $line);
	}

	//check some values we really should have
	public function _check() {
		$stop=false;
		if (!$this->user_device) { $stop=true; die("no device"); }
		if (!$this->context)     { $stop=true; die("no context"); }
		if (!$this->whotocall)   { $stop=true; die("no endpoint"); }
		return ($stop);
	}

	//send login command to Manager socket
	public function _login(&$fp) {
		$this->_write($fp, "Action: Login".$this->crlf);
		$this->_write($fp, "Username: ".$this->manager_login.$this->crlf);
		$this->_write($fp, "Secret: ".$this->manager_pwd.$this->crlf.$this->crlf);
	}

	//disconnect
	public function _logout(&$fp) {
		$this->_write($fp, "Action: Logoff".$this->crlf);
	}

	//Do the actual connecting and sending of command sequence.
	public function send() {

		if ($this->_check()==true) {
			echo "alert('Missing one or more required fields!');";
			die();
		}

		$agi = new Voip_AsteriskManager();
		$fp = $agi->connect($this->server_hostname, $this->manager_login, $this->manager_pwd);


		if ($fp === false) {
			echo "alert('Connection to voip PBX failed!');";
			die();
		} else {
			$variables = array();
			if ($_REQUEST["address_id"]) {
				$variables[] = "CustomerID=".$_REQUEST["address_id"];
			}
			if ($_REQUEST["user_id"]) {
				$variables[] = "UserID=".$_REQUEST["user_id"];
			}
			$vars = implode(",", $variables);
			if ($this->sms) {
				$callerid = $this->sms;
			} elseif ($this->callerid && strlen($this->whotocall) <= 4) {
				$callerid = $this->callerid;
			} else {
				$callerid = $this->whotocall;
			}
			$agi->Originate($this->user_device, $this->whotocall, $this->context, 1, "", "", 30000, $callerid, $vars);
			$agi->disconnect();
			if ($_REQUEST["address_id"]) {
				echo "popup('index.php?mod=note&action=edit&id=0&address_id=".$_REQUEST["address_id"]."&is_custcont=1&phone=1&phonenr=".$this->whotocall."');";
			}
		}
	}

	public function getUserStatus($user_id) {
		$user_data = new User_data();
		$user_info = $user_data->getUserDetailsById($user_id);
		if ($user_info["voip_number"] && $user_info["voip_device"]) {
			if ($this->context == "intern") {
				$context = "terrazur";
			} else {
				$context = $this->context;
			}

			if (substr($user_info["voip_device"], 0, 3) == "SIP") {
				$sippeer = str_ireplace("SIP/", "", $user_info["voip_device"]);
				$agi = new Voip_AsteriskManager();
				$agi->connect($this->server_hostname, $this->manager_login, $this->manager_pwd);

				$sipshowpeer = $agi->Command('sip show peer '.$sippeer);

				preg_match("/Status(.*)/", $sipshowpeer["data"], $matches);
			} elseif (substr($user_info["voip_device"], 0, 4) == "IAX2") {
				$iaxpeer = str_ireplace("IAX2/", "", $user_info["voip_device"]);
				$agi = new Voip_AsteriskManager();
				$agi->connect($this->server_hostname, $this->manager_login, $this->manager_pwd);

				$iaxshowpeer = $agi->Command('iax2 show peer '.$iaxpeer);

				preg_match("/Status(.*)/", $iaxshowpeer["data"], $matches);
			} else {
				$matches = array(0, 0);
			}
			$status = trim(str_replace(":", "", $matches[1]));
			if (strpos($status, "OK") !== false) {
				$extenstatus = $agi->ExtensionState($user_info["voip_number"], $context);
				switch($extenstatus["Status"]) {
				case 8:
				case 1:
				case 0:
					return $extenstatus["Status"];
					break;
				default:
					return 0;
					break;
				}
			} else {
				$agi->disconnect();
				return -1;
			}
		}
		$agi->disconnect();
		return -1;
	}
}
?>
