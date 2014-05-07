<?php
/*
 *  Copyright (C) 2006 Svante Kvarnstrom
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307
 *  USA
 */

Class Customers_filesys extends Filesys_data {
	
	/* getCustomerRootDir {{{ */
	/**
	 * Checks if the customer root dir exists and creates it if it doesn't.
	 *
	 * Checks if the customer root dir exists by using the check_folder
	 * function in the filesys module. If the directory does not exist
	 * check_Folder creates it. In either case, the id of the root customer
	 * directory is returned. 
	 *
	 * @return int The id of the customers root directory.
	 */
	public function getCustomerRootDir() {
		$dir_settings = array("nama" => "customers", "parent_id" => 0);
		$customers_dir = $this->check_folder($dir_settings);
		unset($dir_settings);
		return $customers_dir;
	} /* }}} */
	/* getCustomerDir {{{ */
	/**
	 * 
	 * Checks if the customer dir exists and creates it if it doesn't.
	 *
	 * Checks if the customer dir exists by using the check_folder 
	 * function in the filesys module. IF the directory does not exist
	 * check_folder creates it. In either case, the id of the customer
	 * directory is returned.
	 *
	 * @param int $id Id of customer
	 * @return int THe id of the customer directory.
	 */
	public function getCustomerDir($id) {
		$dir_settings = array("name" => $id, "parent_id" => $this->getCustomerRootDir());
		$customer_dir = $this->check_folder($dir_settings);
		unset($dir_settings);
		return $customer_dir;
	} /* }}} */

} 
?>
