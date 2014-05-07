<?
class Sync4j_convert {

	public function getSyncDB($filesys, &$fp_array) {
		//retrieve all .db files in the syncfolder
		$db_files = array();
		$cmd = "find $filesys -name '*.db'";
		exec ($cmd, $ret, $retval);
		foreach ($ret as $dbfile) {
			$db_files[]=$dbfile;
		}

		foreach ($db_files as $file) {
			$filename = $file;
			$fp_array[$filename] = fopen($filename, "r+");
			flock($fp_array[$file], LOCK_EX) or die("eof");
			if (filesize($filename)>0) {
				$data = explode("\n",fread($fp_array[$filename], filesize($filename)));
			} else {
				$data = array();
			}
			foreach ($data as $k=>$v) {
				if (preg_match("/^#.*$/s",$v) || !$v) {
					unset($data[$k]);
				}
			}
			$data = array_reverse($data);
			$db_index[$file] = $data;
		}
		return $db_index;
	}
	public function writeSyncDB($db_index, $filesys, &$fp_array) {
		if (!is_array($db_index))
			die("error: please sync with a SyncML client first");

		foreach ($db_index as $file=>$data) {
			ftruncate($fp_array[$file],0); 	//empty the stream resource
			fseek($fp_array[$file],0);			//jump to file position zero

			$data[]="#".strftime("%a %b %d %H:%M:%s %Z %Y");
			$data[]="#FileSystemSyncSource file database";
			$data = array_reverse($data);

			$d = implode("\n",$data)."\n";

			fwrite($fp_array[$file], $d);
			fclose($fp_array[$file]);
		}
	}

	public function removeSyncDbKey($key, $db_index) {
		foreach ($db_index as $d=>$data) {
			if (is_array($data)) {
				foreach ($data as $k=>$record) {
					$r = explode("=",$record);
					if ($r[0] == $key) {
						unset($data[$k]);
					}
				}
			}
			$db_index[$d] = $data;
		}
		return $db_index;
	}
	public function insertSyncDbKey($key, $db_index) {
		foreach ($db_index as $d=>$data) {
			$data[]=$key;
			$db_index[$d]=$data;
		}
		return $db_index;
	}
	public function insertSyncDb($key, $db_index, $keytype) {
		if ($keytype=="D") {
			//use future timestamp
			$db_index = $this->removeSyncDbKey($key, $db_index);
			$db_index = $this->insertSyncDbKey($key."=".$keytype.(mktime()+1)."000", $db_index);
			//$db_index = insertSyncDbKey($key."=".$keytype."2000000000000", $db_index);
		}

		return $db_index;
	}

	/* Function leeched from the internet */
	public function XMLtoArray($XML) {
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $XML, $vals);
		xml_parser_free($xml_parser);
		$_tmp='';
		foreach ($vals as $xml_elem) {
			$x_tag=$xml_elem['tag'];
			$x_level=$xml_elem['level'];
			$x_type=$xml_elem['type'];
			if ($x_level!=1 && $x_type == 'close') {
				if (isset($multi_key[$x_tag][$x_level])) {
					$multi_key[$x_tag][$x_level]=1;
				} else {
					$multi_key[$x_tag][$x_level]=0;
				}
			}
			if ($x_level!=1 && $x_type == 'complete') {
				if ($_tmp==$x_tag) {
					$multi_key[$x_tag][$x_level]=1;
				}
				$_tmp=$x_tag;
			}
		}
		// jedziemy po tablicy
		foreach ($vals as $xml_elem) {
			$x_tag=$xml_elem['tag'];
			$x_level=$xml_elem['level'];
			$x_type=$xml_elem['type'];
			if ($x_type == 'open') {
				$level[$x_level] = $x_tag;
			}
			$start_level = 1;
			$php_stmt = '$xml_array';
			if ($x_type=='close' && $x_level!=1) {
				$multi_key[$x_tag][$x_level]++;
			}
			while($start_level < $x_level) {
				$php_stmt .= '[$level['.$start_level.']]';
				if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level]) {
					$php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
				}
				$start_level++;
			}
			$add='';
			if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
				if (!isset($multi_key2[$x_tag][$x_level])) {
					$multi_key2[$x_tag][$x_level]=0;
				} else {
					$multi_key2[$x_tag][$x_level]++;
				}
				$add='['.$multi_key2[$x_tag][$x_level].']';
			}
			if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes',$xml_elem)) {
				if ($x_type == 'open') {
					$php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
				} else {
					$php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
				}
				eval($php_stmt_main);
			}
			if (array_key_exists('attributes',$xml_elem)) {
				if (isset($xml_elem['value'])) {
					$php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
					eval($php_stmt_main);
				}
				foreach ($xml_elem['attributes'] as $key=>$value) {
					$php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
					eval($php_stmt_att);
				}
			}
		}
		return $xml_array;
	}
	// END XMLtoArray

	public function getXML($file) {
		$xml_parser = xml_parser_create();
		if (!file_exists($file)) {
			die("file not found: $file");
		}
		$handle = fopen($file, "r");
		$data = '';
		while (!feof($handle)) {
			$data .= fread($handle, 8192);
		}
		fclose($handle);
		#xml_parse_into_struct($xml_parser, $data, $vals, $index);
		$ary = $this->XMLtoArray($data);
		#xml_parser_free($xml_parser);
		return $ary;
	}
}
?>
