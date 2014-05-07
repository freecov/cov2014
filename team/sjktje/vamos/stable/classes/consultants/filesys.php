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

/* 
 * I want to do as little unofficial changes to the other modules as possible
 * since it's a pain to re-patch modules when there's a new official version etc.
 * So here is Consultants_filesys - a class that extends Filesys_data. It adds a 
 * couple of filesys functions that we need.
 *
 * TODO: When clicking the file upload/view thing, make sure no side menues are 
 * shown in the filesys module. I guess the only way to do this is to actually 
 * comment out the menues ... which kinda goes against the above statement, but
 * at least that's easier to maintain than a bunch of other code.
 */
Class Consultants_filesys extends Filesys_data {

	/* getConsultantRootDir {{{ */
	/* 
	 * Checks if the consultant root dir exists and creates it if it doesn't.  
	 * 
	 * Checks if the consultant root dir exists by using the check_folder 
	 * function in the filesys module. If the directory does not exist 
	 * check_folder creates it. In either case, the id of the root consultant
	 * directory is returned.
	 *
	 * @return int The id of the consultants root directory.
	 */
	public function getConsultantRootDir() {
		$dir_settings = array("name" => "consultants", "parent_id" => 0);
		$consultants_dir = $this->check_folder($dir_settings);
		unset($dir_settings);
		return $consultants_dir;	 
	} /* }}} */

	/* getConsultantDir {{{ */
	/*
	 * Checks if the consultant dir exists and creates it if it doesn't.
	 * 
	 * Checks if the consultant dir exists by using the check_folder 
	 * function in the filesys module. If the directory does not exist
	 * check_folder creates it. In either case, the id of the consultant 
	 * directory is returned.
	 * 
	 * @param int Id of consultant.
	 * @return int The id of the consultant directory.
	 */
	public function getConsultantDir($id) {
		$dir_settings = array("name" => $id, "parent_id" => $this->getConsultantRootDir());
		$consultant_dir = $this->check_folder($dir_settings);
		unset($dir_settings);
		return $consultant_dir;
	} /* }}} */
}
