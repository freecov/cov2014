<?php
/**
 * Covide Groupware-CRM Twinfield integration data class
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
require("classes/nusoap/nusoap.php"); // we are using nuSOAP, download from: http://sourceforge.net/projects/nusoap/
Class Twinfield_data {
	/* constants */
	const include_dir = "classes/twinfield/inc/";
	const class_name  = "twinfield";
	/* variables */
	private $twinfield_url;
	private $username;
	private $password;
	public  $offices;
	private $default_office;
	private $company;
	/* methods */
	/* __construct {{{ */
	/**
	 * populate settings for twinfield
	 */
	public function __construct() {
		$sql = "SELECT * FROM twinfield_settings";
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$this->username        = $row["username"];
		$this->password        = $row["password"];
		$this->default_office  = $row["default_office"];
		$this->company         = $row["company"];
		$this->twinfield_url   = $this->GetCluster();
		$this->offices         = $this->getOffices();
	}
	/* }}} */
	/* GetMessage {{{ */
	/**
	 * Get the response message
	 *
	 * @param string $XML The raw xml feed
	 * @return sting the result as sting
	 */
	private function GetMessage($XML) {
		$doc = new DOMDocument();
		$doc->loadXML($XML);
		$strResult = "";
		if ($doc->documentElement->getAttribute("result") == "0") {
			$params = $doc->getElementsByTagName("*");
			foreach ($params as $param) {
				if ($param->hasAttributes()) {
					foreach ($param->attributes as $attribute){
						if ($attribute->name == "msg")
							$result = $result." ".$attribute->value;
					}
				}
			}
		}
		return $result;
	}
	/* }}} */
	/* GetCluster {{{ */
	/**
	 * Get the correct clusternode to comunicate with
	 *
	 * @return string the hostname of the clusternode
	 */
	public function GetCluster() {
		$error = "";
		$params = array(
			"p_strUser"     => strtoupper($this->username),
			"p_strPassword" => $this->password,
			"p_strOffice"   => strtoupper($this->default_office),
			"p_strCompany"  => strtoupper($this->company),
			"p_strError"    => $error
		);

		// Path to server
		$serverPath = "https://login.twinfield.com/SOAP/Redirect.wsdl";
		// Set the Namespace
		$namespace ="http://tempuri.org/Redirect/message/";
		// Set the Tempuri
		$action ="http://tempuri.org/Redirect/action/Redirect.GetClusterUrl";

		// Create a new SOAP Client object
		$objSOAP = new soap_client($serverPath);

		//$s->decodeUTF8(true);
		$encodingStyle="http://schemas.xmlsoap.org/soap/encoding/";

		// Uitvoeren
		$result =$objSOAP->call("GetClusterUrl", $params, $namespace, $action, $encodingStyle);
		if($err = $objSOAP->getError())
			die("#Error# ". $err);

		if ($error != "")
			die("#Error# ". $error);

		return $result["Result"];
	}
	/* }}} */
	/* SOAPCall {{{ */
	/**
	 * Send query to SOAP server and return the result
	 *
	 * @param string $office The office to connect to
	 * @param string $XML The XML document to send to the SOAP server
	 * @return string The XML return from the SOAP server
	 */
	private function SOAPCall($office, $XML) {
		$params = array(
			"p_strUser"     => strtoupper($this->username),
			"p_strPassword" => strtoupper(md5($this->password)),
			"p_strOffice"   => strtoupper($office),
			"p_strCompany"  => strtoupper($this->company),
			"p_strXML"      => $XML
		);

		// Path to server
		$serverPath = $this->twinfield_url."/SOAP/processxml.wsdl";
		// Set the Namespace
		$namespace ="http://tempuri.org/ProcessXML/message/";
		// Set the Tempuri
		$action ="http://tempuri.org/ProcessXML/action/ProcessXML.ProcessXMLAsString";

		// Create a new SOAP Client object
		$objSOAP = new soap_client($serverPath);

		$encodingStyle="http://schemas.xmlsoap.org/soap/encoding/";

		// Uitvoeren
		$result =$objSOAP->call("ProcessXMLAsString", $params, $namespace, $action, $encodingStyle);
		// Show errors
		if ($err = $objSOAP->getError())
			$result = "#Error# ". $err; 

		return $result;
	}
	/* }}} */
	/* getBrowseFields {{{ */
	/**
	 * Get all the fields for a browselist
	 */
	public function getBrowseFields() {
		$request = "
			<list>
				<type>browsefields</type>
			</list>
		";
		$request = sprintf("
			<read>
				<type>browse</type>
				<office>%s</office>
				<code>100</code>
			</read>
		", $this->default_office);
		$res = $this->SOAPCall($this->office, $request);
		header("Content-Type: text/xml");
		echo $res;
	}
	/* }}} */
	/* getOffices {{{ */
	/**
	 * Get all the offices we have in use
	 */
	public function getOffices() {
		$request = "
			<list>
				<type>offices</type>
			</list>
		";
		$twinfield_offices = array();
		$res = $this->SOAPCall($this->office, $request);
		$parser = new XMLParser($res);
		$parser->parseXML();
		foreach ($parser->document->office as $k=>$v) {
			$twinfield_offices[$v->tagData] = $v->tagAttrs["name"];
		}
		return $twinfield_offices;
	}
	/* }}} */
	/* getCustomers {{{ */
	/**
	 * Get a list of customers in the twinfield administration
	 *
	 * @return array The customers with the key as debtornr and the value the name
	 */
	public function getCustomers($office = 0) {
		$request = sprintf("
			<list>
				<type>dimensions</type>
				<office>%d</office>
				<dimtype>DEB</dimtype>
			</list>
		", $office);
		$res = $this->SOAPCall($office, $request);
		$parser = new XMLParser($res);
		$parser->parseXML();
		foreach ($parser->document->dimension as $k=>$v) {
			$customers[$v->tagData] = $v->tagAttrs["name"];
		}
		return $customers;
	}
	/* }}} */
	/* getFinancialsById {{{ */
	/**
	 * Get all the financial records of a specific customer/supplier
	 *
	 * @param int $address_id The specific customer/supplier
	 * @param int $address_type 1 for customer, 2 for supplier
	 * @return array
	 */
	public function getFinancialsById($address_id, $year, $address_type=1) {
		$request = "
			<columns code=\"%3\$d00\" optimize=\"false\">
				<column id=\"1\">
					<field>fin.trs.line.dim2</field>
					<operator>between</operator>
					<from>%d</from>
					<to></to>
					<visible>false</visible>
				</column>
				<column id=\"2\">
					<field>fin.trs.head.yearperiod</field>
					<operator>between</operator>
					<from>%2\$d/1</from>
					<to>%2\$d/12</to>
				</column>
				<column id=\"2\">
					<field>fin.trs.head.code</field>
					<visible>true</visible>
					<operator>none</operator>
				</column>
				<column id=\"3\">
					<field>fin.trs.head.number</field>
					<visible>true</visible>
					<operator>none</operator>
				</column>
				<column id=\"4\">
					<field>fin.trs.line.invnumber</field>
					<visible>true</visible>
					<operator>none</operator>
				</column>
				<column id=\"5\">
					<field>fin.trs.line.valuesigned</field>
					<visible>true</visible>
					<operator>none</operator>
				</column>
				<column id=\"6\">
					<field>fin.trs.line.openbasevaluesigned</field>
					<visible>true</visible>
					<operator>none</operator>
				</column>
			</columns>
		";
		$request = sprintf($request, $address_id, $year, $address_type);
		$res = $this->SOAPCall($this->office, $request);
		$res = preg_replace("/<browse(.*?)><th>(.*?)<\/th>/si", "", $res);
		$res = preg_replace("/<key(.*?)>(.*?)<\/key>/si", "", $res);
		$res = preg_replace("/field=\"(.*?)\"/si", "class=\"list_data\"", $res);
		$res = str_replace("</browse>", "", $res);
		return $res;
	}
	/* }}} */
	/* saveAddress {{{ */
	/**
	 * Saves an address in the twinfield database
	 *
	 * The options array has to look like this:
	 * array(
	 *   [code]           => integer - the code to use as uniq identifier,
	 *   [companyname]    => string - the name,
	 *   [url]            => string - company website,
	 *   [contact_person] => string - full name with titles etc ,
	 *   [address]        => string - streetname and number,
	 *   [zipcode]        => string - zipcode,
	 *   [city]           => string - city,
	 *   [country]        => string - ISO countrycode,
	 *   [phonenr]        => string - phonenumber,
	 *   [faxnr]          => string - faxnumber,
	 *   [email]          => string - contact email address,
	 *   [administration] => string - the administration to add/save this address in
	 * )
	 *
	 * @param array $options Customer data to save
	 * @param int $address_type 1 for customer, 2 for supplier
	 * @return bool false on failure and true on succes
	 */
	public function saveAddress($options, $address_type=1) {
		//XXX do some input checks
		$request = "
			<dimension>
				<code>%d</code>
				<name>%s</name>
				<type>%s</type>
				<website>%s</website>
				<addresses>
					<address default=\"true\" type=\"invoice\">
						<field1>%s</field1>
						<field2>%s</field2>
						<field3 />
						<postcode>%s</postcode>
						<city>%s</city>
						<country>%s</country>
						<telephone>%s</telephone>
						<telefax>%s</telefax>
						<field4 />
						<email>%s</email>
					</address>
				</addresses>
			</dimension>
		";
		$request = sprintf($request,
			$options["code"], $options["companyname"], ($address_type == 1?"DEB":"CRD"), $options["url"], $options["contact_person"],
			$options["address"], $options["zipcode"], $options["city"], $options["country"], $options["phonenr"], 
			$options["faxnr"], $options["email"]);
		$res = $this->SOAPCall($options["administration"], $request);
		if (strstr($res, "#Error#"))
			return false;
		$parser = new XMLParser($res);
		$parser->parseXML();
		if ($parser->document->tagAttrs["result"] == 0)
			var_dump($parser->document);
			//return false;
		//save the info in the address_info_twinfield table
		//find out if the record is already there
		$sql = sprintf("SELECT COUNT(*) FROM address_info_twinfield WHERE address_id = %d", $options["address_id"]);
		$r = sql_query($sql);
		$count = sql_result($r, 0);
		if ($count == 0) {
			//insert
			$sql = sprintf("INSERT INTO address_info_twinfield (address_id, office_id) VALUES (%d, %d)", $options["address_id"], $options["administration"]);
		} else {
			//update
			$sql = sprintf("UPDATE address_info_twinfield SET office_id = %d WHERE address_id = %d", $options["administration"], $options["address_id"]);
		}
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* getRelAdm {{{ */
	/**
	 * Get the administration for an address
	 *
	 * @param int $address_id The address to lookup administration for
	 * @return int The addministration code
	 */
	public function getRelAdm($address_id = 0) {
		if (!$address_id)
			return $this->default_office;
		$sql = sprintf("SELECT office_id FROM address_info_twinfield WHERE address_id = %d", $address_id);
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		return $row["office_id"];
	}
	/* }}} */
}
?>
