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

		if (!class_exists("Email_retrieve")) {
			exit("no class definition found");
		}

		$uniq = strtolower(md5(uniqid(time())));

		$name_in	= "mail_".$uniq.".bin.in"; //unique name
		$name_out = "mail_".$uniq.".bin.out";

		//dont write header for uuenc cause it's already there
		if ($uuname==0)	{
			$hdx = ("Content-Type: ".strtolower($ct)."; ".chr(13).chr(10)."\tname=\"$name_out\"".chr(13).chr(10).chr(13).chr(10) );
			$hdx.=$data;
		} else {
			$hdx =$data;
		}

		//prepare temp files
		$path_tmp = $GLOBALS["covide"]->temppath;
		$myFile_in	= $path_tmp.$name_in;
		$myFile_out = $path_tmp.$name_out;

		$fp = fopen($myFile_in,"w+");
		fwrite($fp, $hdx);
		unset($fp);
		unset($hdx);

		if ($uuname == 1) {
			$cmd = "uudecode $myFile_in -o $myFile_out ";
		} else {
			if ($enc == "QUOTED-PRINTABLE")
				$cmd = "uudeview -z -i -q $myFile_in -p $path_tmp ";
			else
				$cmd = "uudeview -i -q $myFile_in -p $path_tmp ";
		}
		//execute command
		exec($cmd, $ret);

		if (file_exists($myFile_out)) {
			unset($data);
			$datafile = fopen($myFile_out,"r");
			$data = fread($datafile, filesize($myFile_out));
			fclose($datafile);
			@unlink($myFile_out);
		} else {

			#skip extra conversions for uuenc
			if ($uuname == 0) {
				//no transfer encoding, possibly char enc like qprint or utf8

				$conversion = new Layout_conversion();
				switch ($enc) {
					case 3:
					case "BASE64":
						$data = base64_decode($data, 1);
						$data = $conversion->utf8_convert($data);
						break;
					case 4:
						$data	= $conversion->utf8_convert(imap_qprint($data));
						break;
					default:	$data = $conversion->utf8_convert($data);	break;
				}
			}
		}
		@unlink($myFile_in);
?>