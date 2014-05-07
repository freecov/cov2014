<?php
/**
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurflRemoteClient
 * @author Steve Kamerman <stevekamerman AT gmail.com>
 * @version Stable Stable 2.1.1 $Date: 2010/03/01 15:40:10
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 */
/**
 * Tera-WURFL remote webservice client for PHP
 * @package TeraWurflRemoteClient
 */
class TeraWurflRemoteClient {
	
	/**
	 * If you try to use a capability that has not been retrieved yet and this is set to true,
	 * it will generate another request to the webservice and retrieve this capability automatically.
	 * @var Bool
	 */
	public $autolookup = true;
	/**
	 * Flattened version of Tera-WURFL's capabilities array, containing only capability names and values.
	 * Since it is 'Flattened', there a no groups in this array, just individual capabilities.
	 * @var Array
	 */
	public $capabilities;
	/**
	 * Array of errors that were encountered while processing the request and/or response.
	 * @var Array
	 */
	public $errors;
	
	private $userAgent;
	private $webserviceUrl;
	private $xml;
	private $clientVersion = '2.1.1';
	private $apiVersion;
	
	public function __construct($TeraWurflWebserviceURL){
		if(!self::validURL($TeraWurflWebserviceURL)){
			throw new Exception("TeraWurflRemoteClient Error: the specified webservice URL is invalid.  Please make sure you pass the full url to Tera-WURFL's webservice.php.");
			exit(1);
		}
		$this->capabilities = array();
		$this->errors = array();
		$this->webserviceUrl = $TeraWurflWebserviceURL;
	}
	/**
	 * Get the requested capabilities from Tera-WURFL for the given user agent
	 * @param $userAgent String HTTP User Agent of the device being detected
	 * @param $capabilities Array of capabilities that you would like to retrieve
	 * @return bool Success
	 */
	public function getCapabilitiesFromAgent($userAgent, Array $capabilities){
		$this->userAgent = (is_null($userAgent))? self::getUserAgent(): $userAgent;
		// build request string
		$uri = $this->webserviceUrl . (strpos($this->webserviceUrl,'?')===false?'?':'&') 
		. 'ua=' . urlencode($this->userAgent)
		. '&search=' . implode('|',$capabilities);
		$this->callTeraWurfl($uri);
		$this->loadCapabilities();
		$this->loadErrors();
		return true;
	}
	/**
	 * Returns the value of the requested capability
	 * @param $capability The WURFL capability you are looking for (e.g. "is_wireless_device")
	 * @return Mixed String, Numeric, Bool
	 */
	public function getDeviceCapability($capability){
		$capability = strtolower($capability);
		if(!array_key_exists($capability, $this->capabilities)){
			if($this->autolookup){
				$this->getCapabilitiesFromAgent($this->userAgent, array($capability), array());
			}
			return $this->capabilities[$capability];
		}
		return $this->capabilities[$capability];
	}
	/**
	 * Get the version of the Tera-WURFL Remote Client (this file)
	 * @return String
	 */
	public function getClientVersion(){
		return $this->clientVersion;
	}
	/**
	 * Get the version of the Tera-WURFL Webservice (webservice.php on server).  This is only available
	 * after a query has been made since it is returned in the XML response.
	 * @return String
	 */
	public function getAPIVersion(){
		return $this->apiVersion;
	}
	/**
	 * Make the webservice call to the server using the GET method and load the XML response into $this->xml 
	 * @param $uri
	 * @return void
	 */
	private function callTeraWurfl($uri){
		try{
			$this->xml = simplexml_load_file($uri);
		}catch(Exception $ex){
			// Can't use builtin logging here through Tera-WURFL since it is on the client, not the server
			throw new Exception("TeraWurflRemoteClient Error: Could not query Tera-WURFL master server.");
			exit(1);
		}
	}
	/**
	 * Parse the XML response into the capabilities array
	 * @return void
	 */
	private function loadCapabilities(){
		$this->apiVersion = $this->xml->device['apiVersion'];
		foreach($this->xml->device->capability as $cap){
			$this->capabilities[(string)$cap['name']] = self::niceCast((string)$cap['value']);
		}
	}
	/**
	 * Parse the XML response's errors into the errors array
	 * @return void
	 */
	private function loadErrors(){
		foreach($this->xml->errors->error as $error){
			$this->errors[(string)$error['name']]=(string)$error['description'];
		}
	}
	/**
	 * Cast strings into proper variable types, i.e. 'true' into true
	 * @param $value
	 * @return Mixed String, Bool, Float
	 */
	private static function niceCast($value){
		// Clean Boolean values
		if($value === 'true')$value=true;
		if($value === 'false')$value=false;
		if(!is_bool($value)){
			// Clean Numeric values by loosely comparing the (float) to the (string)
			$numval = (float)$value;
			if(strcmp($value,$numval)==0)$value=$numval;
		}
		return $value;
	}
	/**
	 * Is the given URL valid
	 * @param $url
	 * @return Bool
	 */
	private static function validURL($url){
		if(preg_match('/^(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/',$url)) return true;
		return false;
	}	
	/**
	 * Return the requesting client's User Agent
	 * @param $source
	 * @return String
	 */
	public static function getUserAgent($source=null){
		if(is_null($source) || !is_array($source))$source = $_SERVER;
		$userAgent = '';
		if(isset($_GET['UA'])){
			$userAgent = $_GET['UA'];
		}elseif(isset($source['HTTP_X_DEVICE_USER_AGENT'])){
			$userAgent = $source['HTTP_X_DEVICE_USER_AGENT'];
		}elseif(isset($source['HTTP_X_ORIGINAL_USER_AGENT'])){
			$userAgent = $source['HTTP_X_ORIGINAL_USER_AGENT'];
		}elseif(isset($source['HTTP_X_OPERAMINI_PHONE_UA'])){
			$userAgent = $source['HTTP_X_OPERAMINI_PHONE_UA'];
		}else{
			$userAgent = $source['HTTP_USER_AGENT'];
		}
		return $userAgent;
	}
}