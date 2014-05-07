<?php
/**
 * Covide Groupware-CRM Addressbook module. List
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
Class Product_output {

	/* constants */
	const include_dir = "classes/product/inc/";
	const class_name  = "Product_output";

	/* methods */

	/* __construct {{{ */
	/**
	 * Init some defaults
	 */
	public function __construct() {
	}
	/* }}} */

	/* ------------------------------------- Products */
	
	/* editProduct {{{ */
	/**
	 * Show edit screen for a product 
	 *
	 * @param int The product to edit
	 */
	public function editProduct($productid=0) {
		require(self::include_dir."editProduct.php");
	}
	/* editNoSell {{{ */
	/**
	 * Show edit screen for a no sell item
	 *
	 * @param productid The productid to edit
	 * @param nosellid  The id to edit
	 */
	public function editNoSell($productid, $nosellid=0) {
		require(self::include_dir."editNoSell.php");
	}
	/* showProduct {{{ */
	/**
	 * Show info screen for a product 
	 *
	 * @param int The product to show 
	 */
	public function showProduct($productid=0) {
		require(self::include_dir."showProduct.php");
	}
	/* }}} */
	/* showProductsOfSupplier {{{ */
	/**
	 * Show product list for one supplier
	 *
	 * @param int The supplier to show 
	 */
	public function showProductsOfSupplier($supplierid=0) {
		require(self::include_dir."show_list.php");
	}
	/* }}} */
	/* showList {{{ */
	/**
	 * showList. Show productss
	 *
	 * Show productlist. This includes search results.
	 * It will show the addresses in pages of 30 results
	 *
	 * @return bool true
	 */
	public function showList($options) {
		require(self::include_dir."show_list.php");
	}
	/* }}} */

	/* pick_product {{{ */
	/**
	 * Show form to fetch the right product 
	 */
        public function pick_bank() {
                require(self::include_dir."pick_product.php");
        }
	/* }}} */
	
	/* ------------------------------------- Offers */

	/** 
	 * Show all outstanding offers
	 */
	public function showOffers() 
	{
		require(self::include_dir."show_offers.php");
	}
        
        /**
         * show an offer
         */
	public function showOffer($offerid=0) 
	{
		require(self::include_dir."showOffer.php");
	}
        
	/**
	 * Show edit screen for an offer
	 *
	 * @param int The product to edit
	 */
	public function editOffer($id=0) 
        {
		require(self::include_dir."editOffer.php");
	}

	/**
	 * pdf for offer 
	 *
	 * @param int The product to edit
	 */
	public function PDFOffer($id=0,$template) 
        {
		require(self::include_dir."templates/$template.php");
	}
        
}

?>
