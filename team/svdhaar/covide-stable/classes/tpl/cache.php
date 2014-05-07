<?php
/**
 * Covide CMS Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2010 Covide BV
 * @package Covide
 * @subpackage Tpl
 */
Class Tpl_cache {
	/** @var int current siteroot page */
	private $siteroot = 0;
	/** @var int page cache timeout */
	private $page_cache_timeout;
	/** @var int disable cache completely */
	private $apc_disable;
	/** @var int is cache fetched result */
	private $apc_fetch;
	/** @var Memcache internal memcache object */
	private $memcache;
	/**
	 * Initialize and set specific options
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @since 1.0 13/04/2010
	 */
	public function setOptions($siteroot, $page_cache_timeout, $apc_fetch, $apc_disable) {
		$this->siteroot           =& $siteroot;
		$this->page_cache_timeout =& $page_cache_timeout;
		$this->apc_disable        =& $apc_disable;
		$this->apc_fetch          =& $apc_fetch;

		if (class_exists('Memcache', false)) {
			$this->memcache = new Memcache;
			$this->memcache->connect('127.0.0.1');
		}
	}
	/**
	 * Get cache object by key 
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @since 1.0 13/04/2010
	 */

	public function getApcCache($ident) {
		/* always combine the ident with the siteroot identifier */
		$ident.= "_".$this->siteroot;

		if (!$_SESSION["user_id"] && !$_SESSION["visitor_id"] && !$this->apc_disable) {
			if ($this->memcache) {
				$data = $this->memcache->get($ident);
				if ($data) {
					header('Mem-cache: true', true);
				}
				return $data;
			} else {
				return $this->getSqlCache($ident);
			}
		}
	}
	/**
	 * Set object to cache
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @since 1.0 13/04/2010
	 */

	public function setApcCache($ident, $contents) {
		/* always combine the ident with the siteroot identifier */
		$ident.= "_".$this->siteroot;

		if (!$_SESSION["user_id"] && !$_SESSION["visitor_id"] && !$this->apc_fetch && !$this->apc_disable) {
			if ($this->memcache) {
				return $this->memcache->set($ident, $contents, MEMCACHE_COMPRESSED, $this->page_cache_timeout);
			} else {
				$this->setSqlCache($ident, $contents);
			}
		}
	}
	/**
	 * Get object from DB cache
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @since 1.0 13/04/2010
	 */

	public function getSqlCache($ident) {
		if ($this->page_cache_timeout) {
			$deltime = time()-$this->page_cache_timeout;
			$q = sprintf("delete from cms_cache where timestamp < %d", $deltime);
			sql_query($q);
		}

		$q = sprintf("select * from cms_cache where ident = '%s'", $ident);
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			$row = sql_fetch_assoc($res);
			$data = unserialize(gzuncompress(base64_decode($row["data"])));
			$this->apc_fetch = 1; //this is a apc fetched result
			header("Db-cache: true", true);

			return $data;

		}
	}
	/**
	 * Set object to DB cache
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @since 1.0 13/04/2010
	 */

	public function setSqlCache($ident, $contents) {
		$q = sprintf("delete from cms_cache where ident = '%s'", $ident);
		sql_query($q);

		$data = base64_encode(gzcompress(serialize($contents),1));
		$q = sprintf("insert into cms_cache (timestamp, ident, data) values (%d, '%s', '%s')",
			time(), $ident, addslashes($data));
		sql_query($q);
	}
}
?>
