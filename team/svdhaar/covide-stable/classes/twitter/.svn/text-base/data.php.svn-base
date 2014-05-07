<?php
/**
 * Covide Groupware-CRM Twitter_data
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2009 Covide BV
 * @package Covide
 */

class Twitter_data {
	/* variables */
	private $_username;
	private $_password;
	public $error;
	/* methods */
	/* __construct {{{ */
	/**
	 * Constructor to setup class and test if username/password is valid
	 *
	 * @param string $username Twitter username
	 * @param string $password Twitter password
	 *
	 * @return bool true on succesful login, false on error
	 */
	public function __construct($username, $password) {
		$this->_username = $username;
		$this->_password = $password;
		$login = $this->sendrequest('http://twitter.com/account/verify_credentials.xml');
		if (!$login) {
			$this->error = "Invalid twitter username/password combination";
			return false;
		} else {
			return true;
		}
	}
	/* }}} */
	/* sendMessage {{{ */
	/**
	 * Sends message to Twitter.
	 *
	 * @param string $message message encoded in UTF-8
	 * @return mixed ID on success or FALSE on failure
	 */
	public function sendMessage($message) {
		$message = stripslashes($message);
		$postdata = array("source" => "covide", "status" => urlencode($message), "urlencoded" => "status");
		$res = $this->sendrequest("https://twitter.com/statuses/update.xml", $postdata);
		if ($res && $res->id) {
			return $xml->id;
		} else {
			return false;
		}
	}
	/* }}} */
	/* getTweets {{{ */
	/**
	 * Returns the 20 most recent statuses posted from you and optionally your friends.
	 * @todo implement rate limit check. url for this is http://twitter.com/account/rate_limit_status.xml
	 *
	 * @param int $show_friends if set, show friend's tweets
	 *
	 * @return SimpleXMLElement on success, false on failure
	 */
	public function getTweets($show_friends = 0) {
		if ($show_friends) {
			$url = sprintf("http://twitter.com/statuses/friends_timeline/%s.xml", $this->_username);
		} else {
			$url = sprintf("http://twitter.com/statuses/user_timeline/%s.xml", $this->_username);
		}
		// check the ratelimit first
		$ratelimiturl = "http://twitter.com/account/rate_limit_status.xml";
		$remaining = 0;
		$res = $this->sendrequest($ratelimiturl);
		if (!$res) {
			$this->error = "Cannot check rate limit";
		} else {
			foreach($res as $k=>$v) {
				if ($k == "remaining-hits") {
					$remaining = $v;
				}
			}
		}
		if ($remaining > 0) {
			$res = $this->sendrequest($url);
			if (!$res || !$res->status) {
				$this->error = "Cannot load tweets";
				return false;
			}
			return $res;
		} else {
			return false;
		}
	}
	/* }}} */
	/* sendrequest {{{ */
	/**
	 * Process HTTP request.
	 *
	 * @param string $url Twitter API url
	 * @param array $post Array with data to send to the api
	 * @return mixed A SimpleXMLElement on success, false on failure
	 */
	private function sendrequest($url, $post = "") {
		/* the following 'hack' is needed to allow text with @-sign at the beginning
		 * Without this, curl will think you are referencing a file
		 */
		if ($post["urlencoded"]) {
			unset($post["urlencoded"]);
			$tmppost = array();
			foreach ($post as $k=>$v) {
				$tmppost[] = $k."=".$v;
			}
			unset($post);
			$post = implode("&", $tmppost);
			unset($tmppost);
		}
		if (!function_exists("curl_init")) {
			die("No curl support");
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERPWD, "$this->_username:$this->_password");
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_NOBODY, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 20);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Covide');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Twitter-Client: covide", "Expect:"));
		if ($post) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);
		$ok = curl_errno($curl) === 0 && curl_getinfo($curl, CURLINFO_HTTP_CODE) === 200; // code 200 is required

		if (!$ok) {
			return false;
		}
		return new SimpleXMLElement($result);
	}
	/* }}} */
}
