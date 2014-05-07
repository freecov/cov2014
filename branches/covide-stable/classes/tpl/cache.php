<?php
/**
 * Covide CMS Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Tpl_cache {

	private $siteroot = 0;
	private $page_cache_timeout;
	private $apc_disable;
	private $apc_fetch;

	/*
			"siteroot"           => $this->siteroot,
			"page_cache_timeout" => $this->page_cache_timeout,
			"apc_fetch"          => $this->apc_fetch,
			"apc_disable"        => $this->apc_disable
	*/
	public function setOptions($siteroot, $page_cache_timeout, $apc_fetch, $apc_disable) {
		$this->siteroot           =& $siteroot;
		$this->page_cache_timeout =& $page_cache_timeout;
		$this->apc_disable        =& $apc_disable;
		$this->apc_fetch          =& $apc_fetch;
	}

	public function getApcCache($ident) {
		/* always combine the ident with the siteroot identifier */
		$ident.= "_".$this->siteroot;

		/* if apc functions do not exists or if a user is logged in, we bypass the cache */
		/*if (function_exists('apc_fetch') && !$_SESSION["user_id"] && !$_SESSION["visitor_id"] && !$this->apc_disable) {
			$fetch = apc_fetch(sprintf($ident));
			if ($fetch) {
				$this->apc_fetch = 1; //this is a apc fetched result
				header("Apc-cache: true");
				return unserialize(gzuncompress($fetch));
			}
		}
		*/
		if ($this->page_cache_timeout) {
			$deltime = time()-$this->page_cache_timeout;
			$q = sprintf("delete from cms_cache where timestamp < %d", $deltime);
			sql_query($q);
		}

		if (!$_SESSION["user_id"] && !$_SESSION["visitor_id"] && !$this->apc_disable) {
			$q = sprintf("select * from cms_cache where ident = '%s'", $ident);
			$res = sql_query($q);
			if (sql_num_rows($res) > 0) {
				$row = sql_fetch_assoc($res);
				$data = unserialize(gzuncompress(base64_decode($row["data"])));
				$this->apc_fetch = 1; //this is a apc fetched result
				header("Apc-cache: true");

				return $data;

			}
		}
	}
	public function setApcCache($ident, $contents) {
		/* always combine the ident with the siteroot identifier */
		$ident.= "_".$this->siteroot;

		/* if apc functions do not exists or if a user is logged in or this call was done
				after a successfull apcfetch command, we bypass the cache */
		#if (function_exists('apc_fetch') && !$_SESSION["user_id"] && !$_SESSION["visitor_id"] && !$this->apc_fetch && !$this->apc_disable)
		#	apc_store($ident, gzcompress(serialize($contents),1), 60);

		if (!$_SESSION["user_id"] && !$_SESSION["visitor_id"] && !$this->apc_fetch && !$this->apc_disable) {
			$q = sprintf("delete from cms_cache where ident = '%s'", $ident);
			sql_query($q);

			$data = base64_encode(gzcompress(serialize($contents),1));
			$q = sprintf("insert into cms_cache (timestamp, ident, data) values (%d, '%s', '%s')",
				time(), $ident, addslashes($data));
			sql_query($q);
		}
	}
}
?>
