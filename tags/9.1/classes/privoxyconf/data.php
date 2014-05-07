<?php
/**
 * Covide Groupware-CRM privoxy config module
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
Class Privoxyconf_data {
	/* constants */
	/* variables */
	/* methods */
	/* read_configfile {{{ */
	/**
	 * Read privoxy configfile and return contents
	 *
	 * @return array every line of the file as array element
	 */
	public function read_configfile() {
		$filename = $GLOBALS["covide"]->filesyspath."/covide.action";

		if (filesize($filename)>0) {
			$handle = fopen($filename, "r");
			$data = fread($handle, filesize($filename));
			fclose($handle);
		} else {
			$data = "";
		}

		$data = preg_replace("/^(.*){covide-list}/si","",$data);
		$data = explode("\n",$data);
		foreach ($data as $k=>$v) {
			if ($v=="") unset($data[$k]);
		}
		return $data;
	}
	/* }}} */
	/* write_configfile {{{ */
	/**
	 * Write privoxy configfile
	 *
	 * @param array $data The filedata with every line as array element
	 */
	public function write_configfile($data) {
		$data = array_unique($data);
		$data = implode("\n",$data);
		$str = "# covide file\n";
		$str.= "{{alias}}\n";
		$str.= "covide-list   = -block -crunch-client-header -crunch-if-none-match -crunch-server-header -filter -deanimate-gifs -fast-redirects -hide-referer -hide-if-modified-since +hide-forwarded-for-headers -session-cookies-only -crunch-outgoing-cookies -crunch-incoming-cookies -kill-popups\n";
		$str.= "{covide-list}\n";
		$str.= $data;
		$str = trim($str);

		$filename = $GLOBALS["covide"]->filesyspath."/covide.action";
		$out = fopen($filename, "w");
		fwrite($out, $str);
		fclose($out);
	}
	/* }}} */
	/* editSite {{{ */
	/**
	 * Replace entry with usersupplied new value
	 *
	 * @param string $site The entry to replace
	 * @param string $data The new value
	 */
	public function editSite($site, $data) {
		/* get the old data */
		$configdata = $this->read_configfile();
		$configdata[ array_search($site, $configdata) ] = $data;
		sort($configdata);
		$this->write_configfile($configdata);
		return true;
	}
	/* }}} */
	/* deleteSite {{{ */
	/**
	 * Remove an entry from configfile
	 *
	 * @param string $site The entry to remove
	 */
	public function deleteSite($site) {
		$configdata = $this->read_configfile();
		unset($configdata[ array_search($site, $configdata) ]);
		asort($configdata);
		$this->write_configfile($configdata);
		return true;
	}
	/* }}} */
	/* addSite {{{ */
	/**
	 * Add a site to configfile
	 *
	 * @param string $site The entry to add
	 */
	public function addSite($site) {
		$configdata = $this->read_configfile();
		$configdata[]=$site;
		asort($configdata);
		$this->write_configfile($configdata);
		return true;
	}
	/* }}} */
}
?>
