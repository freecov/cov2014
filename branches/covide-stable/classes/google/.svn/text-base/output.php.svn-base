<?php
/**
 * Covide Google Apps module
 *
 * @author Chris Janse
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */
Class Google_output {

	public function createGoogleCharts($parameters){
		$params = "";
		foreach ($parameters as $k => $v) {
			$params .= "&".$k."=".urlencode($v);
		}
		$params = substr($params, 1);
		header("Content-type: image/png");
		$fp = fopen("http://chart.apis.google.com/chart?".$params, "rb");
		fpassthru($fp);
		fclose($fp);
	}
}
?>