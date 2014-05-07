<?php

class Customers_output {
	const include_dir = "classes/customers/inc/";

	/* {{{ listCustomers */
	/**
	 * Displays customer listing.
	 *
	 * Includes listCustomers.php which lists customers.
	 */
	public function listCustomers() {
		require(self::include_dir."listCustomers.php");
	} /* }}} */
	/* {{{ editContact */
	/** 
	 * Edit/add new contact.
	 *
	 * Includes editContact.php which lets the user add or edit an already added
	 * contact.
	 *
	 * @param array $options Url options.
	 * @return no return value
	 */
	public function editContact($options=array()) {
		$id = $options["id"];
		$customer_id = $options["customer_id"];
		require(self::include_dir."editContact.php");
	} /* }}} */
	/* {{{ editCustomer */
	/**
	 * Display edit/view customer form.
	 *
	 * Includes editCustomer.php which lets the user view/edit customer info.
	 * 
	 * @param int $id Id of customer to view/edit. If $id=0, present empty form (to add new users.)
	 * @return No return value
	 */
	public function editCustomer($id=0,$type=1,$consultant_id=0) {
		require(self::include_dir."editCustomer.php");
	} /* }}} */
	/* {{{ showContact */
	/**
	 * Displays contact information.
	 *
	 * @param int $id Id of contact to show.
	 * @return No return value.
	 */
	public function showContact($id) {
		require(self::include_dir."show_info.php");
	} /* }}} */
	/* {{{ showItem */
	/**
	 * 
	 * Display quick info. 
	 * This function displays quick information about a company, 
	 * which is used together with the javascript popup thingy.
	 *
	 * @param int $id Id of company to show information about.
	 * @return no return value
	 */
	public function showItem($id) {
		require(self::include_dir."show_item.php");
	} /* }}} */
}
?>
