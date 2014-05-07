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

class Consultants_output {
	const include_dir = "classes/consultants/inc/";
	/* {{{ listConsultants */
	/**
	 *
	 * Displays consultant list.
	 * 
	 * This function includes the listConsultants.php page.
	 */
	public function listConsultants() {
		require(self::include_dir."listConsultants.php");
	} 
	/* }}} */
	/* {{{ editConsultant */
	/**
	 * 
	 * This function displays the edit/add consultant form.
	 * 
	 * If an id is sent as an argument to this function, informaiton about
	 * the consultant with that id will be displayed, and information may
	 * be changed and saved. If no id is sent, an empty consultant form will
	 * be shown where the user can choose to add a new consultant.
	 *
	 * @param int $id Id of consultant to edit (default 0)
	 */
	public function editConsultant($id=0) {
		require(self::include_dir."editConsultant.php");
	} 
	/* }}} */
	/* {{{ showItem */
	/**
	 * 
	 * Displays quick info.
	 *
	 * This function displays quick information about a consultant, 
	 * which is used together with the javascript popup thingy.
	 *
	 * @param int $id Id of consultant to show information about.
	 * @return no return value
	 */
	public function showItem($id) {
		require(self::include_dir."show_item.php");
	}
	/* }}} */
	/* {{{ searchCompany */
	/**
	 * Display search company form in pretty javascript box.
	 *
	 * @param int $id Id of consultant company should be linked to.
	 * @return no return value
	 */
	public function searchCompany($id,$data) {
		require(self::include_dir."searchCompany.php");
	} /* }}} */
}
?>

