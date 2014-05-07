<?php
/**
  * Covide Includes
  *
  * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
  * @version %%VERSION%%
  * @license http://www.gnu.org/licenses/gpl.html GPL
  * @link http://www.covide.net Project home.
  * @copyright Copyright 2000-2008 Covide BV
  * @package Covide
  */

/* *************************** */
/* Postgres Conversion Library */
/* *************************** */

/*{{{
	public functions:

	sql_query($q)
	sql_fetch_array($res);
	sql_result($res,0);
	sql_num_rows($res);
	sql_insert_id($tablename);
	sql_fetch_row($res);
	sql_affected_rows($res);

	(use query without conversion)
	sql_query_direct($q);


*/
//}}}---------------------------------------------------------------------
//{{{ sql_log_query: log insert, update and delete queries to a logfile.
//------------------------------------------------------------------------
function sql_log_query($q, $affected_rows=0) {
	if (preg_match("/^((insert)|(update)|(delete))/si", $q)) {
		/* exclude active calls */
		if (!preg_match("/^((delete from)|(update)|(insert into)) ((active_calls)|(cms_cache)|(cms_image_cache)|(cms_temp)) /si", $q)) {
			$file = $GLOBALS["covide"]->logpath."/query_log_".date("Ymd").".log";

			$ipaddr = $GLOBALS["covide"]->user_ip;
			/* if post, fake some stuff */
			if ($_SERVER["REQUEST_METHOD"] == "POST")
				$uri = sprintf("%s?mod=%s&action=%s", $_SERVER["REQUEST_URI"], $_REQUEST["mod"], $_REQUEST["action"]);
			else
				$uri = $_SERVER["REQUEST_URI"]; //get request

			/* switch to combined log format (http://httpd.apache.org/docs/1.3/logs.html#combined) */
			/* ipaddress - username [datetime] "GET/POST uri VERSION" 200 affected_rows "referer" "query" */
			$str = sprintf("%s - %d [%s] \"%s %s %s\" 200 %d \"%s\" \"%s\"\n",
				$ipaddr,
				$_SESSION["user_id"],
				date("j/M/Y:H:i:s T"),
				$_SERVER["REQUEST_METHOD"],
				addslashes($uri),
				$_SERVER["SERVER_PROTOCOL"],
				$affected_rows,
				addslashes($_SERVER["HTTP_REFERER"]),
				addslashes($q)
			);
			if (is_array($GLOBALS["covide"]->query_log)) {
				$GLOBALS["covide"]->query_log[] = $str;
				$GLOBALS["covide"]->query_file = $file;
			} elseif (is_writable($file)) {
				file_put_contents($file, $str, FILE_APPEND);
			}
		}
	}
}

//}}}---------------------------------------------------------------------
//{{{ sql_trigger_error: raise an error message in a database error occurs
//------------------------------------------------------------------------
function sql_trigger_error($query, $q, $err) {
	global $debug;
	if ($debug) {
		$h = "<br>";
		$h.= "<b>Query Function:</b>";
		$h.= "<ul><i><pre>".$query."</pre></i></ul>";
		$h.= "<b>Query Postgres:</b>";
		$h.= "<ul><i><pre>".$q."</pre></i></ul>";
		$h.= "<b>Error Details:</b>";
		$h.= "<ul><i><pre>$err</pre></i></ul>";
		$h.="<b>Debug Trace:</b>";
		$h.="<ul>";
		$vDebug = debug_backtrace();
		$h.="<table border=\"0\" cellcpacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\"><tr>";
		$h.="<td bgcolor=\"#CDCDCD\">Function Name</td>";
		$h.="<td bgcolor=\"#CDCDCD\">File Name</td>";
		$h.="<td bgcolor=\"#CDCDCD\">Line</td>";
		$h.="</tr>";
		for ($i=1; $i<count($vDebug);$i++) {
			$val = $vDebug[$i];
			if ($i==1) {
				$bg = "#EC7C7C";
			} else {
				$bg = "#FFFFFF";
			}
			$h.="<tr>";
			$h.="<td bgcolor=\"$bg\">".$val["function"]."</td>";
			$h.="<td bgcolor=\"$bg\">".$val["file"]."</td>";
			$h.="<td bgcolor=\"$bg\">".$val["line"]."</td>";
			$h.="</tr>";
		}
		$h.="</table>";
		$h.="</ul>";
		print($h);
	} else {
		echo "<b>SQL Error.</b>";
		echo "<br>Please file a bugreport on sourceforge";
	}
	die();
}

//}}}--------------------------------------------------------
//{{{ sql_query_exec_real: exec a query
//-----------------------------------------------------------
function sql_query_exec_real($query, &$db, $no_log=0) {
	if (preg_match("/^((select)|(show)|(describe))/si", $query)) {
		/* if select, return a result set */
		return $db->query($query);
	} else {
		/* if update/delete/insert/other, just exec a query */
		$affected_rows = $db->exec($query);
		/* if logging is enabled and affected_rows > 0, then log it */
		if ($affected_rows > 0 && !$no_log)
			sql_log_query($query, $affected_rows);

		return $affected_rows;
	}
}
//}}}--------------------------------------------------------
//{{{ sql_query: convert query to postgres and do a sql query
//-----------------------------------------------------------
function sql_query($query, $db=0, $top=0, $limit=0, $no_log=0, $no_error=0) {
	$query = trim($query);

	if (!$db) {
		global $db;
	}

	if (!$db) {
		$db = $GLOBALS["covide"]->db;
	}

	if (!$db) {
		die("database connection cannot be established.");
	}
	$starttime = microtime(1);
	if ($limit) {
		//$res = $db->limitQuery($query, $top, $limit);
		$db->setLimit($limit, $top);
		$res = sql_query_exec_real($query, $db, $no_log);
	} else {
		$res = sql_query_exec_real($query, $db, $no_log);
	}
	if (!$no_error && PEAR::isError($res)) {
		echo "<pre>";
		echo "<b><font color=red>An error occured inside Covide. Error details are displayed below:</font></b>\n\n";
		echo "<a href='javascript: history.go(-1);'><font color=black>&lt;&lt; go back</font></a>\n\n";

		echo "<b>query details:</b>\n";
		echo $query . "\n\n";
		echo "<b>error details:</b>\n";
		echo $res->getUserInfo() . "\n\n";
		echo "<b>Backtrace:</b>\n";
		echo "<pre>";
		echo "<style>";
		echo "td { border-left: 1px solid black; border-bottom: 1px solid black;}";
		echo "table { border-right: 1px solid black; border-top: 1px solid black;}";
		echo "</style>";
		echo "<table cellpadding=\"0\" cellspacing=\"0\"><tr>";
		echo "<td>&nbsp;file&nbsp;</td><td>&nbsp;line&nbsp;</td><td>&nbsp;function&nbsp;</td>";
		echo "</tr>";
		$backtrace = array_reverse($res->backtrace);
		foreach ($backtrace as $v) {
			if (strstr($v["file"], "functions_pear")) {
				break;
			}
			echo "<tr>";
			echo "<td>&nbsp;".$v["file"]."&nbsp;</td>";
			echo "<td align=\"right\">&nbsp;".$v["line"]."&nbsp;</td>";
			echo "<td>&nbsp;".$v["function"]."()&nbsp;</td>";
			echo "</tr>";
		}
		echo "</table>";
		exit(1);
	}

	$endtime = microtime(1);
	$totaltime = ($endtime - $starttime);
	$GLOBALS["dbstat"]["time"] += $totaltime;
	$GLOBALS["dbstat"]["count"]++;
	$GLOBALS["dbstat"]["double"][crc32($query)]++;

	return $res;
}

//}}}--------------------------------------------------------
//{{{ sql_query_direct: use a direct query without conversion
//-----------------------------------------------------------
function sql_query_direct($query, $db=0) {
	if (!$db) {
		global $db;
	}
	$res = sql_query($query);
	return $res;
}

//}}}-----------------------------------------------------------------
//{{{ sql_fetch_array: fetch array and put ishtml as non-caps in array
//--------------------------------------------------------------------
function sql_fetch_array($result, $skip_filter=0) {
	if (PEAR::isError($result)) {
		die($result->getMessage());
	}
	//$result->fetchInto($row, DB_FETCHMODE_ASSOC);
	$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

	if ($row["ishtml"])
		$row["isHtml"] = &$row["ishtml"];

	if ($skip_filter != 2) {
		if ($GLOBALS["covide"]->conversion)
			$conversion =& $GLOBALS["covide"]->conversion;
		else
			$conversion = new Layout_conversion();

		if (is_array($row)) {
			foreach ($row as $k=>$v) {
				$row[$k] = $conversion->str2utf8($v);
			}
		}

		if (!$skip_filter)
			sql_filter_tags($row);
	}
	return $row;
}

//}}}--------------
//{{{ sql_fetch_row
//-----------------
function sql_fetch_row($result, $skip_filter=0) {
	//$result->fetchInto($row, DB_FETCHMODE_ORDERED);
	$row = $result->fetchRow(MDB2_FETCHMODE_ORDERED);

	if ($skip_filter != 2) {
		if ($GLOBALS["covide"]->conversion)
			$conversion =& $GLOBALS["covide"]->conversion;
		else
			$conversion = new Layout_conversion();

		if (is_array($row)) {
			foreach ($row as $k=>$v) {
				$row[$k] = $conversion->str2utf8($v);
			}
		}
		if (!$skip_filter)
			sql_filter_tags($row);
	}

	return $row;
}

//}}}------------------------------------------------------------
//{{{ sql_fetch_assoc: return named array with ishtml as non-caps
//---------------------------------------------------------------
function sql_fetch_assoc($result, $skip_filter=0, $skip_fields=array()) {
	if (PEAR::isError($result)) {
		$ret = array();
		return $ret;
	}
	//$result->fetchInto($row, DB_FETCHMODE_ASSOC);
	$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);

	if ($row["ishtml"])
		$row["isHtml"] = &$row["ishtml"];

	if ($skip_filter != 2) {
		if ($GLOBALS["covide"]->conversion)
			$conversion =& $GLOBALS["covide"]->conversion;
		else
			$conversion = new Layout_conversion();

		if (is_array($row)) {
			foreach ($row as $k=>$v) {
				if (!in_array($k, $skip_fields))
					$row[$k] = $conversion->str2utf8($v);
			}
		}
		if (!$skip_filter)
			sql_filter_tags($row);
	}

	return $row;
}

//}}}-----------
//{{{ sql_result
//--------------
function sql_result($result, $pos=0, $field="", $skip_filter=0) {
	if (PEAR::isError($result))
		die($result->getMessage());

	if ($field) {
		//$result->fetchInto($ret, DB_FETCHMODE_ASSOC, $pos);
		$ret = $result->fetchRow(MDB2_FETCHMODE_ASSOC, $pos);
		$return = $ret[$field];
	} else {
		//$result->fetchInto($ret, DB_FETCHMODE_ORDERED, $pos);
		$ret = $result->fetchRow(MDB2_FETCHMODE_ORDERED, $pos);
		$return = $ret[0];
	}

	if ($skip_filter != 2) {
		if ($GLOBALS["covide"]->conversion)
			$conversion =& $GLOBALS["covide"]->conversion;
		else
			$conversion = new Layout_conversion();

		$return = $conversion->str2utf8($return);

		if (!$skip_filter)
			sql_filter_tags($return);
	}

	return $return;
}

//}}}-------------
//{{{ sql_num_rows
//----------------
function sql_num_rows($result="") {
	if (PEAR::isError($result)) {
		die($result->getMessage());
	}
	return $result->numRows();
}

//}}}------------------
//{{{ sql_affected_rows
//---------------------
#function sql_affected_rows($result="") {
#	return pg_affected_rows($result);
#}

//}}}--------------
//{{{ sql_data_seek
//-----------------
#function sql_data_seek($result, $pos) {
#	return pg_result_seek($result, $pos);
#}

//}}}-----------------------------------------------------------
//{{{ sql_insert_id: Get the current id sequence and return that
//--------------------------------------------------------------
function sql_insert_id($table, $dblink=0) {
	if (!$dblink)
		$dblink = $GLOBALS["covide"]->db->connection;

	$dbtype =& $GLOBALS["covide"]->db->dbsyntax;
	switch ($dbtype) {
		case "mysql" :
			$return = mysql_insert_id($dblink);
			break;
		case "mysqli":
			$return = mysqli_insert_id($dblink);
			break;
		case "pgsql" :
			if (!$table){
				echo gettext("PostgreSQL script problem - please contact system administrator");
				die();
			}
			$q = "SELECT currval('".$table."_id_seq')";
			$res = pg_query($q);
			$row = pg_fetch_array($res);
			$return = $row[0];
			break;
	}
	return $return;
}

//}}}--------------------------
//{{{ sql_error: dummy function
//-----------------------------
function sql_error($filename="unknown", $linenumber=0, $query="") {
	global $debug, $db;
	return 1;
}

//}}}----------------------------------------------
//{{{ sql_syntax: return some sql specific syntaxes
//-------------------------------------------------
function sql_syntax($syntax) {
	$dbtype =& $GLOBALS["covide"]->db->dbsyntax;
	switch ($dbtype) {
		case "mysql":
		case "mysqli":
			switch ($syntax) {
				case "casttype"    : return "SIGNED";
				case "escape_char" : return "`";
				case "regex"       : return "REGEXP";
				case "like"        : return "LIKE";
				case "random"      : return "RAND()";
			}
			break;
		case "pgsql":
			switch ($syntax) {
				case "casttype"    : return "INT";
				case "escape_char" : return "\"";
				case "regex"       : return "~*";
				case "like"        : return "~~*";
				case "random"      : return "RANDOM()";
			}
			break;
	}
}
//}}}
/* fix_db {{{ */
/**
 * Update all tables to have the correct sequence set
 *
 * @todo Check if this is still working. Probably not because pgsql support is outdated and unmaintained at the moment
 */
function fix_db() {
	if ($GLOBALS["covide"]->db->dbsyntax != "pgsql") {
		die("This action is not allowed with the database backend you are using.");
	}
	$excluded_tables = array(
		"finance_jaar_afsluitingen",
		"active_calls",
		"cdr",
		"license",
		"mail_messages_data",
		"statistics",
		"status_conn"
	);
	//script to update all sequences in postgres.
	//I have no idea if this is still working.
	$sql = "select relname from pg_stat_user_tables WHERE relname not like 'sql_%' order by relname";
	$res = sql_query($sql);
	while ($row=sql_fetch_assoc($res)) {
		if (!in_array($row["relname"], $excluded_tables)) {
			$q = "select max(id) from ".$row["relname"];
			$r = sql_query($q);
			$seqnr = sql_result($r,0);
			if ($seqnr) {
				echo($row["relname"]." - ".$seqnr."<br>");
				$bla = sql_query("SELECT setval('".$row["relname"]."_id_seq',$seqnr)");
			}
		}
	}
}
/* }}} */
/* sql_filter_tags {{{ */
/**
 * Remove html tags from data in a recordset
 *
 * @param array $row The recordset. Note that this is by reference so the data passed to this function will be altered
 */
function sql_filter_tags(&$row) {
	/* exception for cms pagedata and cms templates */
	if (is_array($row)) {
		if (array_key_exists("pageData", $row)
			&& array_key_exists("autosave_info", $row)
			&& array_key_exists("pageTitle", $row)) {

			$cms = array(
				"pageData"       => $row["pageData"],
				"autosave_info"  => $row["autosave_info"],
				"conversion_script" => $row["conversion_script"]
			);
			$row["pageData"]      = "";
			$row["autosave_info"] = "";
		}
		if (array_key_exists("title", $row)
			&& array_key_exists("data", $row)
			&& array_key_exists("category", $row)
			&& in_array($row["category"], array("main", "php", "smarty", "javascript", "html"))) {

			$cms = array(
				"data"       => $row["data"]
			);
			$row["data"] = "";
		}
	}

	if (!$GLOBALS["covide"]->filter_pattern && is_array($GLOBALS["covide"]->filter_tags)) {
		$GLOBALS["covide"]->filter_pattern = sprintf("/<(\/{0,1}(%s)[^>]*?)>/si", implode("|", $GLOBALS["covide"]->filter_tags));
	}
	if ($GLOBALS["covide"]->filter_pattern) {
		$row = preg_replace($GLOBALS["covide"]->filter_pattern, "<!-- $1 -->", $row);
	}
	/* exception for cms pagedata */
	if (is_array($cms)) {
		foreach ($cms as $k=>$v) {
			$row[$k] = $v;
		}
	}
}
/* }}} */
/* sql_filter_col {{{ */
/**
 * Create sql code for sorting a list
 *
 * @param string $col | seperated columnname and sort order
 * @return string sql code you can use in a query
 */
function sql_filter_col($col) {
	$col = explode("|", $col);

	$field = preg_replace("/[^a-z0-9_\"`]/si", "", $col[0]);
	switch ($col[1]) {
		case "asc":
		case "desc":
			$order = $col[1];
			break;
		default:
			$order = "";
	}
	return sql_syntax("escape_char").$field.sql_syntax("escape_char")." ".$order;
}
/* }}} */
/* sql_escape_string {{{ */
/**
 * Function to use the correct escape function.
 * Used to protect against sql injection
 *
 * @param string $data The data to escape
 * @return string the string you can use in an sql_query
 */
function sql_escape_string($data) {
	//check if we have a valid connection. mysql_real_escape_string needs this.
	if (!MDB2::isConnection($GLOBALS["covide"]->db)) {
		die("no valid connection");
	}
	$dbtype =& $GLOBALS["covide"]->db->dbsyntax;
	// Reverse magic_quotes_gpc/magic_quotes_sybase effects on those vars if ON.
	if (get_magic_quotes_gpc()) {
		$data = stripslashes($data);
	}
	switch($dbtype) {
	case "mysql":
		$return = mysql_real_escape_string($data);
		break;
	case "mysqli":
		$return = mysqli_real_escape_string($GLOBALS["covide"]->db->connection, $data);
		break;
	case "pgsql":
		$return = pg_escape_string($data);
		break;
	}
	return $return;
}
/* }}} */
/* TODO: temporary function for translations (NL->EN)
 * that need to be done in the code (for now the finance module) */
function gettext_nl($str) {
	return $str;
}

?>
