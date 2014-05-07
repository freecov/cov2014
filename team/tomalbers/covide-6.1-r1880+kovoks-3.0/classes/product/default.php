<?php
/**
 * Covide Groupware-CRM Addressbook module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @copyright Copyright 2006 KovoKs VOF
 * @package Covide
 */
Class Product {

	/* variables */

	/**
	 * @var array Product accesslist
	 */
	public $access = Array();

	/* methods */

	/* __construct {{{ */
	/**
	 * __construct. What in the addressbook do you want to do?
	 *
	 * Init the correct address class based on request vars.
	 * This is the Addressbook Controller.
	 */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		$this->get_access();
		switch ($_REQUEST["action"]) {
		
			/* -------------------------- Products ------------------------- */
			
                        case "save" :
                                $product_data = new Product_data();
                                $product_data->store2db($_REQUEST["data"], $_REQUEST["metafield"]);
                                break;
                        case "nosellsave" :
                                $product_data = new Product_data();
                                $product_data->nosellstore2db($_REQUEST["data"]);
                                break;
			case "edit" :
				$product_output = new Product_output();
				$product_output->editProduct($_REQUEST["productid"]);
				break;
			case "noselledit" :
				$product_output = new Product_output();
				$product_output->editNoSell($_REQUEST["productid"], $_REQUEST["nosellid"]);
				break;
			case "show" :
				$product_output = new Product_output();
				$product_output->showProduct($_REQUEST["productid"]);
				break;
			case "showonesupplier" :
				$product_output = new Product_output();
				$product_output->showProductsOfSupplier($_REQUEST["supplierid"]);
				break;

			/* ------------------------ Offers -------------------------- */
			
			case "showoffers" :
			        $product_output = new Product_output();
				$product_output->showOffers();
			case "showoffer" :
			        $product_output = new Product_output();
				$product_output->showOffer($_REQUEST["id"]);
				break;
			case "editoffer" :
				$product_output = new Product_output();
				$product_output->editOffer($_REQUEST["id"]);
				break;
                        case "saveoffer" :
                                $product_data = new Product_data();
                                $product_data->storeOffer2db($_REQUEST["data"], $_REQUEST["metafield"]);
                                break;
			case "pdfoffer" :
				$product_output = new Product_output();
				$product_output->PDFOffer($_REQUEST["id"], $_REQUEST["template"]);
				break;

			/* ---------------------------------------------------------- */
			
			default :
				$product_output = new Product_output();
				$product_output->showList($_REQUEST["options"]);
				break;
		}
	}
	/* }}} */
	/* get_access {{{ */
	/**
	 * get_access. put addressbook access in array
	 *
	 * This one should be moved to a covide/user specific class.
	 * We dont want to be doing this over and over again for every module.
	 *
	 * @return Array Accesslist
	 */
	public function get_access() {
		$query      = "SELECT xs_productmanage FROM users WHERE id=".$_SESSION["user_id"];
		$result     = $GLOBALS["covide"]->db->query($query);
		$result->fetchInto($row);
		$this->access["xs_product"] = $row["xs_productmanage"];
		return $this->access;
	}
	/* }}} */
}
?>
