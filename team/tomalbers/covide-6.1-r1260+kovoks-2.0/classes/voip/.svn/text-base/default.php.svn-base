<?
class Voip {
	//set some defaults when calling
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		if (!$GLOBALS["covide"]->license["has_voip"]) {
			die("no license for this module");
		}
		$userdata = new User_data();
		$userinfo = $userdata->getUserDetailsById($_SESSION["user_id"]);

		#write to /tmp/voip.debug instead of socket
		$this->debug=0;
		$this->crlf = "\r\n";
		$this->convert_cr = 1;
		$this->server_hostname = "";
		$this->server_port     = 5038;
		$this->server_timeout  = 60;
		$this->manager_login   = "";
		$this->manager_pwd     = "";
		$this->user_device     = $userinfo["voip_device"];
		$this->context         = $GLOBALS["covide"]->license["code"];
		$this->whotocall       = sprintf("%s", $_REQUEST["number"]);
		$this->sms             = $sms;

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
			case "call" :
				$this->send();
				break;
		}
	}

	public function _set_server ($server, $port) {
		$this->server_hostname = $server;
		$this->server_port = $port;
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

		$fp = @fsockopen($this->server_hostname, $this->server_port, $errno, $errstr, $this->server_timeout);

		if (!$fp) {
			echo "alert('Connection to voip PBX failed!');";
			die();
		} else {
			$this->_login($fp);

			$this->_write($fp, "Action: Originate".$this->crlf);
			$this->_write($fp, "Channel: ".$this->user_device.$this->crlf);
			$this->_write($fp, "Context: ".$this->context.$this->crlf);
			if ($this->sms) {
				$this->_write($fp, "CallerID: ".$this->sms.$this->crlf);
			} else {
				$this->_write($fp, "CallerID: ".$this->whotocall.$this->crlf);
			}
			$this->_write($fp, "Timeout: 30000".$this->crlf);
			$this->_write($fp, "Exten: ".$this->whotocall.$this->crlf);
			$this->_write($fp, "Priority: 1".$this->crlf.$this->crlf.$this->crlf);
			sleep(1);
			$this->_write($fp, "Action: Logoff".$this->crlf);

			fclose($fp);
		}
	}

}
?>
