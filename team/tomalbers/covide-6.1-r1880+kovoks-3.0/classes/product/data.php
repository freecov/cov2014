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


	/* -------------------------------------------- Products ----*/
	
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

	/* -------------------------------------------- Offers */

    /**
     *  getOfferList generate big array with all offers. The array has been made richer
     *  so it also returns supplier_name and customer+name, which holds the human readable supplier and customer
     *
     * @param array options for search and addresstype etc.
     * $options["l"]: First letter of relations to show.
     * $options["search"]: search words to match addresses.
     * @return array addresses that match the options
     */
    public function getOfferList($options)
    {
        require(self::include_dir."dataGetOfferList.php");
        return $productinfo;
    }
    
    /**
     * getOfferById returns the basic info belonging to an offer.
     * @param id the offer id
     */
    public function getOfferByID($id) 
    {
        $sql = sprintf("SELECT * FROM products_offers WHERE id=%d", $id);
        $res = sql_query($sql);
        $offerinfo = sql_fetch_assoc($res);
        if ($offerinfo[data] > 0)
            $offerinfo["date_human"] = date("d-m-Y H:i", $offerinfo[date]);
        if ($offerinfo["locked"] == "1")
            $offerinfo["unlocked"] = "0";
        else
            $offerinfo["unlocked"] = "1";

        $offerinfo["validity_human"] = date("d-m-Y", $offerinfo[validity]);
        return $offerinfo;
    }
 
    /**
     * 	put offerinfo in db
     * @param $data the product record
     * @param metafields Metafield data (optional)
     */
    public function storeOffer2db($data, $metafields = array()) 
    {
        require(self::include_dir."dataStoreOffer2db.php");
    }

    /**
     * get the products which are used in this offer
     * @param id the offer id
     */
    public function getProductsFromOffer($id)
    {
        $result = array();
        $offerdata = $this->getOfferByID($id);
        $sql = sprintf("SELECT * FROM products_offers_prods WHERE offerid=%d", $id);
        $res = sql_query($sql);
        while ($row = sql_fetch_assoc($res)) 
        {
            $res2 = $this->getProductById($row["productid"]);
            $offerdata["locked"] == "1" ?$row["unlocked"] = "0" : $row["unlocked"] = "1";
            if ($res2["price"] == "") $res2["price"] = 0;
            $result[] = array_merge($res2,array("minorder"=>$row["minorder"],
                                                "offerprice"=>$row["offerprice"], "offerprodid"=>$row["id"], 
                                                "locked"=>$offerdata["locked"], "unlocked"=>$row["unlocked"]));
        }
        return $result;
    }

    public function addProdToOffer($offerid, $prodid)
    {
        $data = $this->getProductByID($prodid);
        
        $sql =  sprintf("INSERT INTO products_offers_prods VALUES('',%d,%d,%f,%d)", $offerid, $prodid,$data["price"],0);
        $res = sql_query($sql);
    }
        
    public function delProdFromOffer($offerid, $prodid)
    {
        $sql =  sprintf("DELETE FROM products_offers_prods where offerid=%d and productid=%d",  $offerid, $prodid);
        $res = sql_query($sql);
    }

    public function delOffer($offerid)
    {
        $sql =  sprintf("DELETE FROM products_offers where id=%d limit 1",  $offerid);
        $res = sql_query($sql);
        $sql =  sprintf("DELETE FROM products_offers_prods where offerid=%d",  $offerid);
        $res = sql_query($sql);
    }

    public function getConditions()
    {
        $result[] = "Select one";
        $res = sql_query("SELECT DISTINCT `condition` FROM products_offers where length(`condition`) > 2 ORDER BY `condition` ASC");
        while ($row = sql_fetch_row($res))
            $result[] = $row[0];
        return $result;
    }

    public function changeOfferPrice($offerprod, $price)
    {
        $sql = sprintf("UPDATE products_offers_prods set offerprice=%f where id=%d",$price,$offerprod);
        $res = sql_query($sql);
    }

    public function changeOfferQuantity($offerprod, $minorder)
    {

        $sql = sprintf("UPDATE products_offers_prods set minorder=%d where id=%d",$minorder,$offerprod);
        $res = sql_query($sql);
    }

    public function lockOffer($offerid)
    {
        $date = date("U");
        $sql = sprintf("update products_offers set locked = '1', date = $date where id = %d",$offerid);
        $res = sql_query($sql);
    }
}
?>
