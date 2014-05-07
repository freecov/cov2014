<?php
Class Rss {

    private $_parser;

    /* 	__construct {{{ */
    /**
     * 	__construct. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function __construct() {
		$this->updateFeeds();
	}
    /* }}} */

    /* 	element_start {{{ */
    /**
     * 	element_start. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function element_start($parser, $name, $attributes) {
		/*
		global $item, $element, $tag;
		switch ($name) {
			case "IMAGE"     :
			case "TEXTINPUT" :
				$element = $name;
				break;
			case "ITEM"      :
				$element = $name;
				$item += 1;
		}
		$tag = $name;
		*/
		var_dump($name);
	}
    /* }}} */

    /* 	element_end {{{ */
    /**
     * 	element_end. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function element_end($parser, $name) {
		global $element;

		switch ($name) {
			case "IMAGE"     :
			case "TEXTINPUT" :
			case "ITEM"      :
				$element = "";
		}
	}
    /* }}} */

    /* 	element_data {{{ */
    /**
     * 	element_data. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function element_data($parser, $data) {
		/*
		global $channel, $element, $items, $item, $image, $tag;

		switch ($element) {
			case "ITEM"      : $items[$item][$tag] .= $data; break;
			case "IMAGE"     : $image[$tag]        .= $data; break;
			case "TEXTINPUT" :                               break;
			default          : $channel[$tag]      .= $data; break;
		}
		*/
		var_dump($data);
	}
    /* }}} */
    
    /* 	getFeedData {{{ */
    /**
     * 	getFeedData. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
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
     * 	getFeeds. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
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
     * 	updateFeeds. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
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

    /* 	parseFeed {{{ */
    /**
     * 	parseFeed. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
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
				$timestamp = mktime();
			}

			if ($timestamp < 0) {
				$timestamp = time(); // better than nothing
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
				echo $sql."<br>";
				$res = sql_query($sql);
			}
		}
		return 1;
	}
    /* }}} */
}
?>
