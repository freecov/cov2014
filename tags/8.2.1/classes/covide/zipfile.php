<?php
/**
 * Covide Zip module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

// official ZIP file format: http://www. // pkware.com/appnote.txt
class Covide_zipfile {

	private $zipfile;
	private $zip;

	function __construct() {
		$this->zipfile = sprintf("%szip_%s", $GLOBALS["covide"]->temppath,
			md5(rand().session_id().mktime()));

		$this->zip = new ZipArchive;
		$res = $this->zip->open($this->zipfile, ZipArchive::CREATE);
		if ($res === false)
			exit("cannot create file");

	}
	public function __destruct() {
		#@unlink($this->zipfile);
	}
	public function add_dir($name) {
		//$this->zip->addEmptyDir($name);
	}

	public function add_file($data, $name) {
		$this->zip->addFromString($name, $data);
	}

	public function file() { // dump out file
		$this->zip->close();
		return file_get_contents($this->zipfile);
	}
}
?>
