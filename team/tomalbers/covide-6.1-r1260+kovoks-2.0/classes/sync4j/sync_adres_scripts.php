<?
//if syncML enabled user
	if ($instelling["sync4j_source_adres"]) {

		//build syncml list
		$q = "select * from adres_sync_records where gebruiker_id = ".$_SESSION["user_id"];
		$sres = sql_query($q);
		while ($srow = sql_fetch_array($sres)) {
			$_sync[$srow["adres_table"]][$srow["adres_id"]]=1;
		}

		//if a mass update is incoming
		if ($_REQUEST["sync_action"] == "synctoggleon" || $_REQUEST["sync_action"] == "synctoggleoff") {

			//get all ids from the current selection
			$_syncids = array(0);
			$res_toggle = sql_query($print_csv);
			while ($row_toggle = sql_fetch_array($res_toggle)) {
				$_syncids[]=$row_toggle["id"];
			}
			//determine sync table
			if ($bcards) {
				$_table = "bcards";
			} else {
				$_table = "adres".$t;
			}
			if ($_REQUEST["sync_action"] == "synctoggleon") {
				//insert if not exists
				foreach ($_syncids as $_sid) {
					if ($_sid>0 && !$_sync[$_table][$_sid]) {
						$q = "insert into adres_sync_records (adres_table, adres_id, gebruiker_id) values ('$_table', $_sid, ".$_SESSION["user_id"].")";
						sql_query($q);
					}
				}
			} else {
				//delete if exists
				$q = "delete from adres_sync_records where gebruiker_id = ".$_SESSION["user_id"]." and adres_table = '$_table' and adres_id IN (".implode(",",$_syncids).")";
				sql_query($q);
			}

			//rebuild the status list
			$_sync = array();
			$q = "select * from adres_sync_records where gebruiker_id = ".$_SESSION["user_id"];
			$sres = sql_query($q);
			while ($srow = sql_fetch_array($sres)) {
				$_sync[$srow["adres_table"]][$srow["adres_id"]]=1;
			}

		}

		//generate an div layer handler
		?>
			<script language="JavaScript1.2" type="text/javascript">
				var xmlhttp

				function loadXMLDoc(url) {
					// code for Mozilla, etc.
					if (window.XMLHttpRequest) {
						xmlhttp=new XMLHttpRequest()
						xmlhttp.onreadystatechange=state_Change
						xmlhttp.open("GET",url,true)
						xmlhttp.send(null)
					}
					// code for IE
					else if (window.ActiveXObject) {
						xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")
						if (xmlhttp)
						{
						xmlhttp.onreadystatechange=state_Change
						xmlhttp.open("GET",url,true)
						xmlhttp.send()
						}
					}
				}

				function state_Change() {
					// if xmlhttp shows "loaded"
					if (xmlhttp.readyState==4) {
						if (xmlhttp.status==200) {
							ret = xmlhttp.responseText;
							ret = ret.split('|');
							if (ret[0]==1) {
								//on
								document.getElementById('sync_'+ret[1]).src = '../img/f_nieuw.gif';
							} else {
								//off
								document.getElementById('sync_'+ret[1]).src = '../img/f_oud.gif';
							}
						} else {
							alert("Problem retrieving data:" + xmlhttp.statusText)
						}
					}
				}
				function toggleSync(id, table) {
					//document.getElementById('syncml_handler').src='../sync/sync_adres_toggle.php?user=<?=$_SESSION["user_id"]?>&id='+id+'&table='+table;
					loadXMLDoc('../sync/sync_adres_toggle.php?user=<?=$_SESSION["user_id"]?>&id='+id+'&table='+table);
				}
			</script>
		<?
	}
?>