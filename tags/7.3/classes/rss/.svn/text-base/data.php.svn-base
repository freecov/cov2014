<?php
/**
 * Covide Groupware-CRM RSS module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Rss_data {
	/* variables */
	/**
	 * @var mixed Holds the xml parser object
	 */
    private $_parser;

	/* methods */
    /* 	getFeedData {{{ */
    /**
     * Read the rss data from the network for specified rss feed.
     *
     * @param int $feed The database id from rssfeeds table.
     * @return text The binary data as found on the rss url.
     */
	public function getFeedData($feed) {
		$feedData = $this->getFeeds($feed);
		/* set socket timeout to 15 seconds */
		$_old_timeout = ini_set("default_socket_timeout", 15);
		$fp = fopen($feedData["url"], "rb");
		ini_set("default_socket_timeout", $_old_timeout);
		$data = "";
		if ($fp) {
			while(!feof($fp)) {
				$data .= fread($fp, 8129);
			}
		}
		return $data;
	}
    /* }}} */
    /* 	getFeeds {{{ */
    /**
     * Get the data from rssfeeds table.
     *
     * @param int $feedid Optional id of a rss feed to get info from.
     * @return array The database data for a feed if feedid is specified, all feeds if no feedid.
     */
	public function getFeeds($feedid = 0) {
		if (!$feedid) {
			$feeds = array();
			$sql = "SELECT * FROM rssfeeds";
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$feeds[] = $row;
			}
		} else {
			$sql = sprintf("SELECT * FROM rssfeeds WHERE id = %d", $feedid);
			$res = sql_query($sql);
			$feeds = sql_fetch_assoc($res);
		}
		return $feeds;
	}
    /* }}} */
    /* 	updateFeeds {{{ */
    /**
     * Update all specified rss feeds.
     */
	public function updateFeeds() {
		$feeds = $this->getFeeds();
		foreach ($feeds as $v) {
			if ($v["url"]) {
				$feeddata = $this->getFeedData($v["id"]);
				$this->parseFeed($feeddata, $v["id"]);
			}
		}
	}
    /* }}} */

	/* getRSSitems() {{{ */
	/**
	 * Retrieve rss items for a specific feed, limited by some count
	 *
	 * @param int The id of the feed.
	 * @param int The ammount of items to return. Newest first
	 * @return array The complete items as they are in the database
	 */
	public function getRSSitems($feedid, $count) {
		$sql = sprintf("SELECT * FROM rssitems WHERE feed=%d ORDER BY date DESC LIMIT %d", $feedid, $count);
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			$return[] = $row;
		}
		return $return;
	}
	/* }}} */

    /* 	parseFeed {{{ */
    /**
     * Put RSS xml data in the covide rssitems database table.
     *
     * @param text $data The binary xml data of the feed
	 * @param int $feed The feedid as found in rssfeeds table
     * @return int 1 on success.
     */
	public function parseFeed($data, $feed) {
		$conversion = new Layout_conversion();
		$items   = array();
		$image   = array();
		$channel = array();
		// parse the data:
		$this->_parser = xml_parser_create();
		xml_set_object($this->_parser, $this);
		xml_parser_set_option($this->_parser, XML_OPTION_TARGET_ENCODING, "utf-8");
		xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->_parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($this->_parser, $data, $values, $tags);
		xml_parser_free($this->_parser);
		unset($data);

		$current_item = 0;
		/* Walk through XML tree and find any <item> tags */
		for ($i=0; $i < count($values); $i++) {
			if ($values[$i]["tag"] == "item" && $values[$i]["type"] == "open") {
				$current_item++;
			}

			switch ($values[$i]["tag"]) {
				case "rm:publisher"    : $items[$current_item]["publisher"]   = $values[$i]["value"]; break;
				case "title"           : $items[$current_item]["title"]       = $values[$i]["value"]; break;
				case "link"            : $items[$current_item]["link"]        = $values[$i]["value"]; break;
				case "guid"            : $items[$current_item]["guid"]        = $values[$i]["value"]; break;
				case "description"     : $items[$current_item]["description"] = $values[$i]["value"]; break;
				case "pubdate"         :
				case "pubDate"         :
				case "dc:date"         :
				case "dcterms:issued"  :
				case "dcterms:created" :
				case "dcterms:modified":
				case "date"            : $items[$current_item]["date"]        = strtotime($values[$i]["value"]); break;
				default: break;
			}
		}
		unset($items[0]);
		foreach ($items as $item) {
			//clean up the items
			foreach ($item as $key => $value) {
				$value = strip_tags($value, "<a> <b> <br> <dd> <dl> <dt> <em> <i> <li> <ol> <p> <strong> <u> <ul>");
				$value = preg_replace("/\Wstyle\s*=[^>]+?>/i", ">", $value);
				$value = preg_replace("/\Won[a-z]+\s*=[^>]+?>/i", ">", $value);
				$item[$key] = $value;
			}

			//get title. If none use the first 40 characters of description
			if ($item["title"]) {
				$title = $item["title"];
			} else {
				$title = substr($item["description"], 0, 40);
			}
			$title = utf8_decode($title);

			if ($item["link"]) {
				$link = $item["link"];
			} elseif ($item["guid"] && (strncmp($item["guid"], 'http://', 7) == 0)) {
				$link = $item["guid"];
			} else {
				$link = $feed["link"];
			}

			//get the published date. If non found, use current time
			if ($item["date"]) {
				$timestamp = $item["date"];
			} else {
				$timestamp = 0;
			}

			//save this item in the database
			//if we dont have a link we have to check some other way to see if this record is already in our database.
			$skip = 0;
			if (!$link) {
				$sql = "SELECT count(id) FROM rssitems where feed=$feed AND subject LIKE '".addslashes(substr(trim($title)), 0, 20)."%'";
				$res = sql_query($sql);
				$count = sql_result($res, 0);
				if ($count) {
					$skip = 1;
				}
			} else {
				$sql = "SELECT count(id) FROM rssitems WHERE feed=$feed AND link='".trim($link)."'";
				$res = sql_query($sql);
				$count = sql_result($res, 0);
				if ($count) {
					$skip = 1;
				}
			}
			if (!$skip) {
				$sql = "INSERT INTO rssitems (feed, subject, body, link, date) VALUES (";
				$sql.= "$feed, '".addslashes(trim($title))."', '".addslashes(trim($item["description"]))."', '".trim($link)."', $timestamp)";
				#echo $sql."<br>";
				$res = sql_query($sql);
			}
		}
		return 1;
	}
    /* }}} */
	/* saveFeed {{{ */
	/**
	 * Save feed info to rssfeeds database table
	 *
	 * @param array $feeddata The form data from editFeed
	 * @return bool true on success, false on error
	 */
	public function saveFeed($feeddata) {
		$rssdata = $feeddata["rss"];
		if ($rssdata["id"]) {
			/* update feed */
			$sql = sprintf("UPDATE rssfeeds SET name='%s', homepage='%s', url='%s', count=%d WHERE id=%d",
				$rssdata["name"], $rssdata["homepage"], $rssdata["url"], $rssdata["count"], $rssdata["id"]);
		} else {
			/* insert feed into database */
			$sql = sprintf("INSERT INTO rssfeeds (name, homepage, url, count) VALUES ('%s', '%s', '%s', %d)",
				$rssdata["name"], $rssdata["homepage"], $rssdata["url"], $rssdata["count"]);
		}
		$res = sql_query($sql);
		if (PEAR::isError($res)) {
			return false;
		} else {
			return true;
		}
	}
	/* }}} */
	/* removeFeed {{{ */
	/**
	 * Remove a feed from database
	 *
	 * @param int $feedid The record to remove
	 * @return bool true on success, false on failure.
	 */
	public function removeFeed($feedid) {
		$user_data = new User_data();
		$user_info = $user_data->getUserdetailsById($_SESSION["user_id"]);
		if (!$user_info["xs_usermanage"] && !$user_info["xs_limitusermanage"]) {
			die("no access.");
		}
		if ((int)$feedid) {
			/* remove feed from rssfeeds */
			$sql = sprintf("DELETE FROM rssfeeds WHERE id=%d", $feedid);
			$res = sql_query($sql);
			/* remove items from database */
			$sql = sprintf("DELETE FROM rssitems WHERE feed=%d", $feedid);
			$res = sql_query($sql);
			return true;
		} else {
			return false;
		}
	}
	/* }}} */
}
?>
