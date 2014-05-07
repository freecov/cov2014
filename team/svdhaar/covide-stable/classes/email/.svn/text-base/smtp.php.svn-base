<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

/* PHP5 smtp class */
class Email_smtp {

	private $rcpt, $from, $data, $helo, $crlf, $convert_cr, $debug, $extraheader;
	private $server_hostname, $server_port, $server_timeout, $err, $stop, $smtpauthm, $logscript;

	public function __construct() {

		#write to /tmp/smtp.debug instead of socket
		$this->debug = 0;
		$this->crlf = "\r\n";
		$this->convert_cr = 1;
		$this->stop = 0;
		/* custom smtp configuration */
		require("conf/offices.php");
		if ($smtp["logscript"]) {
			$this->logscript = $smtp["logscript"];
		}


		if (is_array($smtp)) {
			$this->server_hostname = $smtp["server_hostname"];
			$this->server_port     = $smtp["server_port"];
			$this->server_timeout  = $smtp["server_timeout"];
			$this->helo            = $smtp["helo"];
			$this->tls             = $smtp["tls"];

			/* smtp auth */
			$this->smtpauth["enable"]   = $smtp["auth_enable"];
			$this->smtpauth["method"]   = "login";
			$this->smtpauth["username"] = $smtp["auth_username"];
			$this->smtpauth["password"] = $smtp["auth_password"];

		} else {
			$this->server_hostname = "localhost";
			$this->server_port     = 25;
			$this->server_timeout  = 60;
			$this->helo = "localhost";
		}

		// FIXME: do not program vendor specific hacks into generic software. Disabled for now by svdhaar
		// if user uses [imap|pop].gmail.com we are going to send the mail with google smtp
		/* if ($_SESSION["user_id"]) {
			$user_data = new User_data();
			$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);
			if ($user_info["mail_server"] == "imap.gmail.com" || $user_info["mail_server"] == "pop.gmail.com") {
				$this->server_hostname = "smtp.gmail.com";
				$this->server_port = 587;
				$this->tls = 1;
				$this->smtpauth = array(
					"enable" => 1,
					"method" => "login",
					"username" => $user_info["mail_user_id"],
					"password" => $user_info["mail_password"]
				);
			}
		}
		*/
	
	}

	public function parseAddress($str){
		$arr = explode(",",$str);
		for ($i=0;$i<count($arr);$i++){
			$arr[$i] = trim($arr[$i]);

			if (preg_match("/<&>/",$arr[$i])){
				$arr[$i] = trim(preg_replace("/<([^>]*?)>/","$1",$arr[$i]));
			}
		}
		return ($arr);
	}

	public function clear_rcpt () {
		$this->rcpt = array();
	}

	public function add_rcpt ($rcpt) {
		$to = $this->parseAddress($rcpt);
		foreach ($to as $v) {
			if (preg_match("/^(.*)@(.*)\.(.*)/s",$v)) {
				$this->rcpt[] = $v;
			}
		}
	}

	public function set_server ($server, $port) {
		$this->server_hostname = $server;
		$this->server_port = $port;
	}

	public function set_server_options($server_options) {
		if (array_key_exists("tls", $server_options)) {
			$this->tls = $server_options["tls"];
		}
		if (array_key_exists("smtpauth", $server_options) && is_array($server_options["smtpauth"])) {
			$this->smtpauth = $server_options["smtpauth"];
		}
		if (array_key_exists("helo", $server_options)) {
			$this->helo = $server_options["helo"];
		}
	}

	public function set_from ($from)	{
		$from = $this->parseAddress($from);
		$this->from = $from[0];
	}

	public function set_timeout ($timeout) { $this->timeout = $timeout; }

	public function set_helo ($helo) { $this->helo = $helo; }

	public function set_data ($data) {
		$data = str_replace("\r","",$data);
		if ($this->convert_cr==1) {
			$data = str_replace("\n","\r\n",$data);
		}
		$this->data =& $data;
	}

	private function write (&$fp, $line, $expect="250", $nowait=0) {
		fputs($fp, $line.$this->crlf);
		$this->read_response($fp, $expect, $nowait);
	}

	private function read_response($fp, $expect="250", $nowait=0) {
		/* only check for response when debug is off */
		if (!$this->debug && $nowait==0) {
			$res = fgets($fp, 256);
			$stat = substr($res,0,3);
			if ($stat != $expect) {
				echo "Fatal error occured - mailserver response: ".$stat."\n";
				echo "<a href='index.php?mod=email&action=compose&fatal=1&id=".$_REQUEST["id"]."'>retry</a>";
				exit();
			}
		}
	}

	private function check() {
		$stop=false;
		if (!$this->from) $stop=true;
		if (count($this->rcpt)==0) $stop=true;
		if (!$this->data) $stop=true;
		return ($stop);
	}

	public function set_extraheader($hdr) {
		$this->extraheader = $hdr;
	}


	public function send() {
		if ($this->check()==true) {
			$this->err[] = "Missing 1 or more required fields";
		}

		if ($_REQUEST["try"])
			sleep(2);

		if ($this->debug) {
			$fp = fopen("/tmp/smtp.debug.".rand(),"w");
		} else {
			$fp = fsockopen($this->server_hostname, $this->server_port, $errno, $errstr, $this->server_timeout);
		}

		if (!$fp) {

			$err = ob_get_contents();
			ob_clean();
			ob_start();

			/* stream is NOT OK */
			$output = new Layout_output();
			$output->layout_page("error", 1);

				$venster = new Layout_venster();
				$venster->addVensterData();


				$venster->insertTag("b", gettext("There was an error while sending the email.")." (".gettext("Try").": ".($_REQUEST["try"]+1).")" );
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addCode(gettext("Click")." ");
				$venster->insertTag("a", gettext("here"), array(
					"href" => "?mod=email&action=mail_send&dl=1&id=".$_REQUEST["id"]."&try=".$_REQUEST["try"]+1
				));
				$venster->insertAction("mail_send", gettext("try again"), "?mod=email&action=mail_send&dl=1&id=".$_REQUEST["id"]."&try=".($_REQUEST["try"]+1));
				$venster->addCode(" ".gettext("to try again").".");
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addCode(gettext("Click")." ");
				$venster->insertTag("a", gettext("here"), array(
					"href" => "?mod=email&action=compose&id=".$_REQUEST["id"]
				));
				$venster->insertAction("back", gettext("to covide"), "?mod=email&action=compose&id=".$_REQUEST["id"]);
				$venster->addCode(" ".gettext("to go back to the email module").".");
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addTag("br");
				$venster->addCode(gettext("Error message:"));
				$venster->addTag("hr");
				$venster->addTag("br");

				$err = preg_replace("/(<a [^>]*?>)|(<\/a>)/si", "", $err);
				$venster->addCode($err);

				$venster->endVensterData();
				$output->addCode( $venster->generate_output() );

			$output->layout_page_end();
			$output->exit_buffer();

		}
		$this->read_response($fp, "220");

		/* stream is OK and ready to receive */
		$this->write ($fp, "HELO ".$this->helo);

		/* enable tls if needed */
		if ($this->tls == 1) {
			$this->write($fp, "STARTTLS", "220");
			stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
		}

		/* check for smtp auth */
		if ($this->smtpauth["enable"]) {
			$this->write ($fp, "EHLO ".$this->helo);
			$response = "999";
			$f = 0;

			/*
			EHLO localhost
			250-<server address removed> Hello <client address removed> [127.0.0.1]
			250-SIZE
			250-PIPELINING
			250-AUTH LOGIN
			250 HELP
			*/
			// notice the space after 250<space>
			while ($response != "250 " && $f < 100) {
				$f++;
				$response = substr(trim(fgets($fp, 256)), 0, 4);
			}
			$this->write ($fp, "AUTH LOGIN ".base64_encode($this->smtpauth["username"]), "334");
			$this->write ($fp, base64_encode($this->smtpauth["password"]), "235");
		}

		$this->write ($fp, "MAIL FROM: <".$this->from.">");

		if ($this->rcpt) {
			foreach ($this->rcpt as $v) {
				$this->write ($fp, "RCPT TO: <".$v.">");
			}
		}

		$this->write ($fp, "DATA", "354");
		if ($this->extraheader) {
			$this->write ($fp, $this->extraheader, "", 1);
		}
		$this->write ($fp, $this->data, "", 1);
		$this->write ($fp, ".");
		$this->write ($fp, "QUIT", "221");


		// write some info to the logger. This can be useful for traffic measurement
		if ($this->logscript) {
			$cmd = sprintf("%s %s %s %s %s",
				$this->logscript,
				escapeshellarg($_SERVER['HTTP_HOST']),
				escapeshellarg($this->from),
				escapeshellarg(implode(',', $this->rcpt)),
				escapeshellarg(strlen($this->data)));
			exec($cmd, $ret, $retval);
		}
		unset($this->data);

		if ($this->err) {
			//return false;
			return ( implode("\n", $this->err) );
		} else {
			return true;
		}
	}
}
?>
