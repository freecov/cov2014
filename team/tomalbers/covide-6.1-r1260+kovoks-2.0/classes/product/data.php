<?php
	/**
	 * Covide Groupware-CRM Address_data
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @author Tom Albers <toma@kovoks.nl>
	 * @copyright Copyright 2000-2006 Covide BV
	 * @copyright Copyright 2006 KovoKs VOF
	 * @package Covide
	 */
	Class Product_data {
		/* constants */
		/**
		 * @const string the class include dir
		 */
		const include_dir = "classes/product/inc/";

		/* variables */
		/**
		 * @var array holds product information
		 */
		public $productinfo = Array();

		/* methods */


		/* store2db {{{ */
	    /**
	     * 	put productinfo in db
	     *
	     * @param array the product record
	     * @param array Metafield data (optional)
	     */
		public function store2db($data, $metafields = array()) {
			require(self::include_dir."dataStore2db.php");
		}
		/* }}} */

		/* nosellstore2db {{{ */
	    /**
	     * 	put no sell info in db
	     *
	     * @param array the no sell record
	     */
		public function nosellstore2db($data) {
			require(self::include_dir."dataNoSellStore2db.php");
		}
		/* }}} */


		/* delete {{{ */
	    /**
	     * 	delete productinfo in db
	     *
	     * @param int the id of the address
    	     * @param string the addresstype
	     */
		public function delete($address_id, $addresstype) {
			require_once(self::include_dir."dataDelete.php");
		}
		/* }}} */


		/* 	getProductNameByID {{{ */
	    /**
	     * 	getProductByNameId Find an address based on address id
	     *
	     * @param int the database id
	     */
		public function getProductNameByID($productid) {
			$sql = sprintf("SELECT name FROM products WHERE id=%d", $productid);
			$res = sql_query($sql);
			$productinfo = sql_fetch_assoc($res);
			/* return the info */
			return $productinfo["name"];
		}
		/* }}} */

		/* 	getProductByID {{{ */
	    /**
	     * 	getProductById Find an address based on address id
	     *
	     * @param int the database id
	     * @return int 1 if user is logged in, otherwise 0
	     */
		public function getProductByID($productid) {
			$sql = sprintf("SELECT * FROM products WHERE id=%d", $productid);
			$res = sql_query($sql);
			$productinfo = sql_fetch_assoc($res);
			/* return the info */
			$temp = $this->getProductNameByID($productinfo["replacement"]);
			$productinfo["replacement_human"] = $temp;
			return $productinfo;
		}
		/* }}} */


		/* getProductsBySupplier {{{ */
		/**
		 * find all products manufacturered by a supplier 
		 *
		 * @param int the id of the supplier
		 * @return array All product ids found
		 */
		public function getMailAddressesById($supplierid) {
			$result = array();
			$sql = sprintf("select id from products where supplierid=%d", $supplierid);
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$result[] = $row["id"];
				}
			return $result;
		}
		/* }}} */

    		/*      getProductList {{{ */
            /**
             *  getProductList generate big array with all addresses. The array has been made richer
	     *  so it also returns supplier_name, which holds the human readable supplier.
             *
                 * @param array options for search and addresstype etc.
                 * $options["l"]: First letter of relations to show.
                 * $options["search"]: search words to match addresses.
             * @return array addresses that match the options
             */
                public function getProductList($options) {
                        require(self::include_dir."dataGetProductList.php");
                        return $productinfo;
                }

                /* }}} */


		//------------------- Countries ---------------------------------------------
		/* getCountryList {{{ */
	    /**
	     * getCountryList will return an array with all existing countries
	     * maybe this should move to the address class, but I dont feel like it at the moment.
	     */
	       public function getCountryList() {
			$result = array();
			$sql = "select distinct country from address";
			$res = sql_query($sql);
			while ($row = sql_fetch_assoc($res)) {
				$c = $row["country"];
				if ($c)
	                                $result[$c]=$c;
                        }
                        return $result;
	       }


		//-------------------- No sell functions....------------------------------
		/* getNoSellDataById  {{{ */
	    /** 
	     * getNoSellDataById will return an array with countries and zipcodes where this product
	     * can not be sold.
	     *
	     * @param id the id of the product
	     * @return an array with country and raw zipcode areas
	     */
	        public function getNoSellDataById($id) {
		   $result = array();
		   $sql = sprintf("select id,country,zip from products_nosell where id=%d", $id);
		   $res = sql_query($sql);
                   while ($row = sql_fetch_assoc($res)) {
                              $result[] = array("id"=>$row["id"], 
			                        "country"=>$row["country"],
			                        "zip"=>$row["zip"]);
                   }
		   return $result;
		}
	        /* }}} */
		/* getNoSellDataByProductId  {{{ */
	    /** 
	     * getNoSellDataById will return an array with countries and zipcodes where this product
	     * can not be sold.
	     *
	     * @param id the id of the product
	     * @return an array with country and beautified zipcode areas
	     */
	        public function getNoSellDataByProductId($id) {
		   $result = array();
		   $sql = sprintf("select id,country,zip from products_nosell where productid=%d", $id);
		   $res = sql_query($sql);
                   while ($row = sql_fetch_assoc($res)) {
		   	      $zips = $row["zip"];
			      $zips = explode("|",$zips);
                              $result[] = array("id"=>$row["id"], 
			                        "country"=>$row["country"],
						"zipcode"=>implode(", ",$zips));
                   }
		   return $result;
		}
	        /* }}} */
                /* remove_nosell  {{{ */
            /**
             * remove_nosell will delete the entry
             *
             * @param id the id of the product
             */
                public function remove_nosell($id) {
                   $result = array();
                   $sql = sprintf("delete from products_nosell where id=%d", $id);
                   $res = sql_query($sql);
                }
                /* }}} */
	}
?>
