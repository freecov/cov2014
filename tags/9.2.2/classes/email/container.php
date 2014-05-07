<?php
/**
 * Covide Email Container
 *
 * just create a simple contrainer for the data
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
class Email_container{
	var $name; 		//attachment name
	var $part; 		//subpart of attachment
	var $type; 		//mime type attachment
	var $subtype;	//subtype object
	var $data; 		//binary data of the attachment
	var $enc;		  //part encoding
	var $cid; 		//cid inline att
	var $htmlMail;	//html (eml) attachment or not
	var $uuenc;		  //uuenc encoding-begin 666
	var $disposition;  //attachment or not
	var $fetchData;    //data needs to be fetched
	var $p=0; //how many attachments
}
?>
