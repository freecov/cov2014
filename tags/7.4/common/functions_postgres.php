<?
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

//}}}-------------------
//{{{ define global vars
//----------------------
$querytime=0;
$_cache_crc = array();
$_cache_output = array();
$_query_crc = array();
$_query_result = array();

//}}}---------------------------------------------------------------------
//{{{ fix_keywords_multiple: fix all reserved keywords in postgres at once
//------------------------------------------------------------------------
function fix_keywords_multiple($veld){
	$keywords = implode("|",Array("begin","end","start","comment","type","user","path","prior") );
	$veld = preg_replace("',(\S)'",", $1",$veld);
	$veld = preg_replace("'(\W)($keywords)(\W)'si","$1\"$2\"$3",$veld);
	return ($veld);
}
//}}}-----------------------------------------------
//{{{ fix_keywords: fix reserved keywords one by one
//--------------------------------------------------
function fix_keywords($veld) {
	$keywords = Array("begin","end","start","comment","type","user","path","prior");
	if (in_array(trim($veld),$keywords)) {
		$veld_new = "\"".trim($veld)."\"";
	} else {
		$veld_new = $veld;
	}
	return $veld_new;
}

//}}}---------------------------------------------------------------------
//{{{ sql_trigger_error: raise an error message in a database error occurs
//------------------------------------------------------------------------
function sql_trigger_error($query, $q, $err){
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

//}}}---------------------------------------------------------
//{{{ time_query_start: set starttime right before executing q
//------------------------------------------------------------
function time_query_start() {
	$qmtime = microtime();
	$qmtime = explode(" ",$qmtime);
	$qmtime = $qmtime[1] + $qmtime[0];
	return ($qmtime);
}

//}}}--------------------------------------------------
//{{{ time_query_total: set starttime before converting
//-----------------------------------------------------
function time_query_total($start) {
	$qmtime = microtime();
	$qmtime = explode(" ",$qmtime);
	$qmtime = $qmtime[1] + $qmtime[0];
	$qendtime = $qmtime;
	$qtotaltime = ($qendtime - $start);
	$qtotaltime = round($qtotaltime,2);
	return ($qtotaltime);
}

//}}}--------------------------------------------------------
//{{{ sql_query: convert query to postgres and do a sql query
//-----------------------------------------------------------
function sql_query($query, $database=0){
	global $_cache_crc, $_cache_output;
	global $_query_crc, $_query_result;
	global $querytime, $debug, $db;
	
	if ($database==0) $database = &$db;
	
	$qstarttime = time_query_start();

	//cache hit
		if (preg_match("/^select /si",$query)) {
			$q = sql_query_transform_fast($query);
		} else {
			$q = sql_query_transform($query);
		}

	$result = pg_query($db, $q);
	if (pg_last_error($db)) {
		sql_trigger_error($query, $q, pg_last_error($db));
	}

	$qtotaltime = time_query_total($qstarttime);
	$querytime+=$qtotaltime;

	if ($_REQUEST["debug"]==1) {
		echo $q." ($qtotaltime)<BR><BR>";
	}
	return ($result);
}

//}}}--------------------------------------------------------
//{{{ sql_query_direct: use a direct query without conversion
//-----------------------------------------------------------
function sql_query_direct($query) {
	global $querytime, $debug;
	$qstarttime = time_query_start();	
	
	$result = pg_query($query);
	if (pg_last_error()) {
		sql_trigger_error($query, $query, pg_last_error());
	}

	$qtotaltime = time_query_total($qstarttime);
	$querytime+=$qtotaltime;

	if ($_REQUEST["debug"]==1) {
		echo $q." ($qtotaltime)<BR><BR>";
	}
	return ($result);
}

//}}}--------------------------------------------
//{{{ fix_order_by: fix case insensitive order by
//-----------------------------------------------
function fix_order_by($q){
	if (preg_match("/ ORDER BY /si",$q) && !preg_match("/ DISTINCT /si",$q)){
		preg_match_all("/ ORDER BY (.*)$/si", $q, $ordermatch);
		$order = explode(",", $ordermatch[1][0]);
		unset($ordermatch);
		foreach ($order as $k=>$v){
			$v = explode(" ",trim($v));
			if (!preg_match("/datum/si",$v[0])){
				$order[$k] = "lower(".$v[0].") ";
			}else{
				$order[$k] = $v[0]." ";
			}
			unset($v[0]);
			$order[$k].= implode(" ",$v);
		}
		$repl = " ORDER BY ".implode(",",$order);

		$q = preg_replace("/ ORDER BY (.*)$/si", $repl, $q);
	}
	return ($q);
}

//}}} ----------------------------------------------------------------------
//{{{ sql_query_transform_fast: transform sql query fast - used by sql_query
//--------------------------------------------------------------------------
function sql_query_transform_fast($q){
	global $debug;

	$q = trim($q);
	if (preg_match("/'%.%'/si",$q)){
		$q = preg_replace("/'%(.)%'/si","'$1'",$q);
	}
	$q = preg_replace("/;$/s","",$q);

	$q = sql_mysql_to_postgres($q);
	
	return ($q);
}

//}}}------------------------------------------------------------------------------
//{{{ sql_query_transform: transform sql query slow but torough - used by sql_query
//---------------------------------------------------------------------------------
function sql_query_transform($query) {
	global $debug;

	$q = $query;
	
	//disable searching for fields containing only 1 character in the middle
	//only %<char>%, not <char>%
	if (preg_match("/'%.%'/si",$q)){
		$q = preg_replace("/'%(.)%'/si","'$1'",$q);
	}

	//conversions
	$q = trim($q);
	$q = preg_replace("/;$/s","",$q);

	$arr = explode(" ",$q);
	$method = trim(strtolower($arr[0]));

	$q = str_replace("\\\"","#1#",$q);
	$q = str_replace("\"","'",$q);
	$q = str_replace("#1#","\\\"",$q);

	//inserto into set naar insert into ansi
	if ($method=="insert" && !stristr( preg_replace("/'[^']*?'/si","#data#",$q), " values ") ){

		$q = str_replace("\'","#1#",$q);
		preg_match_all("/'[^']*?'/si",$q,$arr);
		foreach ($arr[0] as $k=>$v){
			$v = str_replace(",","#2#",$v);
			$v = str_replace(";","#3#",$v);
			$v = str_replace("=","#4#",$v);
			$q = str_replace($arr[0][$k],$v,$q);
		}
		$q2 = preg_replace("/^(.*)[^( set )]*? set /si","",$q);
		$val = explode(",",$q2);
		$fields=array();
		$values=array();
		foreach ($val as $k=>$v){
			$v = explode("=",$v);
			$fields[]=$v[0];
			$v[1] = str_replace("#1#","\\'",$v[1]);
			$v[1] = str_replace("#2#",",",$v[1]);
			$v[1] = str_replace("#3#",";",$v[1]);
			$v[1] = str_replace("#4#","=",$v[1]);
			$values[]=$v[1];
		}
		unset($val);

		$q2 = trim(str_replace($q2,"",$q));
		$q2 = preg_replace("/ set$/si","",$q2);

		$q = $q2." (".implode(",",$fields).") values (".implode(",",$values).");";
	}
	//end insert conversion

	$q = sql_mysql_to_postgres($q);

	return ($q);
}

//}}}-------------------------------------------------------
//{{{ sql_mysql_to_postgres: internal mysql2pgsql conversion
//----------------------------------------------------------
function sql_mysql_to_postgres($q) {

	$q = str_replace("\'","#XX#",$q);
	preg_match_all("/'[^']*?'/si",$q,$data);

	foreach ($data[0] as $k=>$v){
		$q = str_replace($v,"#$k#",$q);
	}
	$q = preg_replace("'FROM_UNIXTIME'si","abstime", $q); #This only happens on the desktop
	$q = preg_replace("'DAYOFMONTH\('si", "date_part('day',", $q);
	$q = preg_replace("'MONTH\('si", "date_part('month',", $q);
	$q = str_replace("as SIGNED","as INT",$q);
	$q = str_replace("REGEXP", "~*", $q);
	$q = preg_replace("/LIMIT (\d{1,}),(\d{1,})/si","LIMIT $2 OFFSET $1",$q);
	$q = preg_replace("/isHtml/","ishtml",$q);
	$q = preg_replace("/LIKE/si","ILIKE",$q);

	$q = fix_keywords_multiple($q);
	$q = fix_order_by($q);

	
	foreach ($data[0] as $k=>$v){
		$q = str_replace("#$k#",$v,$q);
	}
	$q = str_replace("#XX#","\'",$q);
	
	return $q;
}

//}}}-----------------------------------------------------------------
//{{{ sql_fetch_array: fetch array and put ishtml as non-caps in array
//--------------------------------------------------------------------
function sql_fetch_array($result) {
	$row = pg_fetch_array($result);
	if ($row["ishtml"]) {
		$row["isHtml"] = &$row["ishtml"];
	}
	return $row;
}

//}}}--------------
//{{{ sql_fetch_row
//-----------------
function sql_fetch_row($result) {
	return pg_fetch_row($result);
}

//}}}------------------------------------------------------------
//{{{ sql_fetch_assoc: return named array with ishtml as non-caps
//---------------------------------------------------------------
function sql_fetch_assoc($result) {
	$row = pg_fetch_assoc($result);
	if ($row["ishtml"]) {
		$row["isHtml"] = &$row["ishtml"];
	}
	return $row;
}

//}}}-----------
//{{{ sql_result
//--------------
function sql_result($result, $pos=0, $field="") {
	if ($field) {
		$ret = pg_fetch_array($result, $pos);
		$return = $ret[$field];
	} else {
		$ret = pg_fetch_array($result, $pos);
		$return = $ret[0];
	}
	return $return;
}

//}}}-------------
//{{{ sql_num_rows
//----------------
function sql_num_rows($result="") {
	return pg_num_rows($result);
}

//}}}------------------
//{{{ sql_affected_rows
//---------------------
function sql_affected_rows($result="") {
	return pg_affected_rows($result);
}

//}}}--------------
//{{{ sql_data_seek
//-----------------
function sql_data_seek($result, $pos) {
	return pg_result_seek($result, $pos);
}

//}}}-----------------------------------------------------------
//{{{ sql_insert_id: Get the current id sequence and return that
//--------------------------------------------------------------
function sql_insert_id($table,$database=0) {
	global $db;
	if ($database==0) { $database = &$db; }
	if (!$table){
		echo gettext("PostgreSQL script problem - please contact system administrator");
		die();
	}
	$q = "SELECT currval('".$table."_id_seq')";
	$res = pg_query($database,$q);
	$row = pg_fetch_array($res);
	$return = $row[0];
	return $return;
}

//}}}--------------------------
//{{{ sql_error: dummy function
//-----------------------------
function sql_error($filename="unknown", $linenumber=0, $query="") {
	global $debug, $db;
	return 1;
}
?>
