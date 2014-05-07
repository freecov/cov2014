<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurfl
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @version Stable Stable 2.1.1 $Date: 2010/03/01 15:40:10
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * The server-side Tera-WURFL webservice provider.  Normally used with webservice.php
 * @package TeraWurfl
 *
 */
class TeraWurflWebservice {
	
	/**
	 * Allow clients to query the webservice only from the listed networks. Setting this
	 * variable to false disables the filter and allows connections from ANY client IP.
	 * To allow only certain networks, put them in CIDR notation in an array.  For example,
	 * to allow only the range 172.16.10.0/24 and the single IP 192.168.2.17 you would use
	 * this as the setting:
	 * 
	 * <code>
	 * public static $ALLOWED_CLIENT_IPS = array('172.16.10.0/24','192.168.2.17/32');
	 * </code>
	 * 
	 * NOTE: 127.0.0.1/32 is automatically allowed, however, some clients may use a different
	 * loopback address like 127.1.1.1.  In this case, add 127.0.0.0/8 to your list.
	 * 
	 * Unauthorized attempts to use this webservice are logged to the Tera-WURFL log file
	 * with a severity of LOG_WARNING.
	 * 
	 * @var Mixed
	 */
	public static $ALLOWED_CLIENT_IPS = false;
	
	protected $xml;
	protected $out_cap = array();
	protected $search_results = array();
	protected $out_errors = array();
	protected $userAgent;
	protected $wurflObj;
	protected $flatCapabilities = array();
	
	public function __construct($userAgent,$searchPhrase){
		require_once('./TeraWurfl.php');
		$this->userAgent = $userAgent;
		$this->wurflObj = new TeraWurfl();
		if(!$this->isClientAllowed()){
			$this->wurflObj->toLog("Denied webservice access to client {$_SERVER['REMOTE_ADDR']}",LOG_WARNING,'TeraWurflWebservice');
			echo "access is denied from ".$_SERVER['REMOTE_ADDR'];
			exit(0);
		}
		$this->wurflObj->getDeviceCapabilitiesFromAgent($this->userAgent);
		$this->flattenCapabilities();
		$this->search($searchPhrase);
		$this->generateXML();
	}
	/**
	 * Get the XML response that would normally be sent to the client.
	 * @return String XML Response
	 */
	public function getXMLResponse(){
		return $this->xml;
	}
	/**
	 * Send the HTTP Headers for the XML return data
	 * @return void
	 */
	public function sendHTTPHeaders(){
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Content-Type: text/xml");
	}
	/**
	 * Send the complete response to the client, including the HTTP Headers and the XML response.
	 * @return void
	 */
	public function sendResponse(){
		$this->sendHTTPHeaders();
		echo $this->getXMLResponse();
	}
	/**
	 * See if a given ip ($ip) is in a given CIDR network ($cidr_range)
	 * @param String CIDR Network (e.g. "192.168.2.0/24")
	 * @param String IP Address
	 * @return Bool IP Address is in CIDR Network
	 */
	public static function ipInCIDRNetwork($cidr_network,$ip){
		// Thanks Bill Grady for posting a *working* IP in CIDR network function!
		// Source: http://billgrady.com/wp/2009/05/21/ip-matching-with-cidr-notation-in-php/
		// Get the base and the bits from the CIDR
		list($base, $bits) = explode('/', $cidr_network);
		if($bits < 8 || $bits > 32){
			throw new Exception("Error: Invalid CIDR mask specified.");
		}
		// Now split it up into it's classes
		list($a, $b, $c, $d) = explode('.', $base);
		// Now do some bit shifting/switching to convert to ints
		$i    = ($a << 24) + ($b << 16) + ( $c << 8 ) + $d;
		$mask = $bits == 0 ? 0: (~0 << (32 - $bits));
		// Here's our lowest int
		$low = $i & $mask;
		// Here's our highest int
		$high = $i | (~$mask & 0xFFFFFFFF);
		// Now split the ip we're checking against up into classes
		list($a, $b, $c, $d) = explode('.', $ip);
		// Now convert the ip we're checking against to an int
		$check = ($a << 24) + ($b << 16) + ( $c << 8 ) + $d;
		// If the ip is within the range, including highest/lowest values,
		// then it's witin the CIDR range
		if ($check >= $low && $check <= $high) return true;
		return false;
	}
	/**
	 * Is the connecting client allowed to use this webservice
	 * @return Bool
	 */
	protected function isClientAllowed(){
		if(!self::$ALLOWED_CLIENT_IPS || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') return true;
		$ip = $_SERVER['REMOTE_ADDR'];
		foreach(self::$ALLOWED_CLIENT_IPS as $cidr_range){
			if(self::ipInCIDRNetwork($cidr_range,$ip)) return true;
		}
		return false;
	}
	/**
	 * Converts PHP variables to an XML friendly string
	 * @param Mixed Value
	 * @return String Value
	 */
	protected function exportValue($in){
		if(is_bool($in))return var_export($in,true);
		if(is_null($in) || !isset($in))return '';
		return $in;
	}
	/**
	 * Add an error to the errors array that will be sent in the XML response
	 * @param String Capability name that is in error
	 * @param String Description of the error
	 * @return void
	 */
	protected function addError($name,$desc){
		$this->out_errors[] = array('name'=>$name,'desc'=>$desc);
	}
	/**
	 * Search through all the capabilities and place the requested ones in search_results to
	 * be sent in the XML response.
	 * @param String Search phrase (e.g. "is_wireless_device|streaming|tera_wurfl")
	 * @return void
	 */
	protected function search($searchPhrase){
		if (!empty($searchPhrase)){
			$capabilities = explode('|',$_REQUEST['search']);
			foreach($capabilities as $cap){
				$cap = strtolower($cap);
				$cap = preg_replace('/[^a-z0-9_\- ]/','',$cap);
				// Individual Capability
				if(array_key_exists($cap,$this->flatCapabilities)){
					$this->search_results[$cap] = $this->flatCapabilities[$cap];
					continue;
				}
				// Group
				if(array_key_exists($cap,$this->wurflObj->capabilities) && is_array($this->wurflObj->capabilities[$cap])){
					foreach($this->wurflObj->capabilities[$cap] as $group_cap => $value){
						$this->search_results[$group_cap] = $value;
					}
					continue;
				}
				$this->addError($cap,"The group or capability is not valid.");
				$this->search_results[$cap] = null;
			}
		}else{
			$this->search_results = $this->flatCapabilities;
		}
	}
	/**
	 * Flatten the multi-tiered capabilities array into a list of capabilities.
	 * @return void
	 */
	protected function flattenCapabilities(){
		$this->flatCapabilities = array();
		foreach($this->wurflObj->capabilities as $key => $value){
			if(is_array($value)){
				foreach($value as $subkey => $subvalue){
					$this->flatCapabilities[$subkey] = $subvalue;
				}
			}else{
				$this->flatCapabilities[$key] = $value;
			}
		}
	}
	/**
	 * Generate the XML response
	 * @return void
	 */
	protected function generateXML(){
		$this->xml = '<?xml version="1.0" encoding="iso-8859-1"?>'."\n";
		$this->xml .= "<TeraWURFLQuery>\n";
		$this->xml .= sprintf("\t".'<device apiVersion="%s" useragent="%s" id="%s">'."\n",
			$this->wurflObj->release_version,
			$this->wurflObj->capabilities['user_agent'],
			$this->wurflObj->capabilities['id']
		);
		foreach( $this->search_results as $cap_name => $value){
			$value = $this->exportValue($value);
			$this->xml .= "\t\t<capability name=\"$cap_name\" value=\"$value\"/>\n";
		}
		$this->xml .= "\t</device>\n";
		$this->xml .= $this->generateXMLErrors();
		$this->xml .= "</TeraWURFLQuery>";
	}
	/**
	 * Generate the errors section of the XML response
	 * @return String XML errors section
	 */
	protected function generateXMLErrors(){
		$xml = '';
		if(count($this->out_errors)==0){
			$xml .= "\t<errors/>\n";
		}else{
			$xml .= "\t<errors>\n";
			foreach($this->out_errors as $error){
				$xml .= "\t\t<error name=\"{$error['name']}\" description=\"{$error['desc']}\"/>\n";
			}
			$xml .= "\t</errors>\n";
		}
		return $xml;
	}
}