<?php
/**
 * Covide Groupware-CRM
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2009 Covide BV
 * @package Covide
 */
/**
 * Data Class to communicate with ExactOnline
 */
Class Exactonline_data {
	/* constants */
	const BASE_URL       = "https://start.exactonline.nl";
	const FILE_DOWNLOAD  = "/docs/XMLDownload.aspx";
	const FILE_UPLOAD    = "/docs/XMLUpload.aspx";
	const TOPIC_RELATION = "Accounts";
	/* variables */
	private $_exact_username;
	private $_exact_password;
	private $_exact_partnerkey;
	/* methods */
	/* __construct {{{ */
	/**
	 * Populate the private variables needed to communicate with ExactOnline
	 *
	 * @return void
	 */
	public function __construct() {
		$sql = "SELECT username, password, partnerkey FROM exactonline_settings";
		$res = sql_query($sql);
		$row = sql_fetch_assoc($res);
		$this->_exact_username   = $row["username"];
		$this->_exact_password   = $row["password"];
		$this->_exact_partnerkey = $row["partnerkey"];
	}
	/* }}} */
	/* sendRel {{{ */
	/**
	 * Send xml to update or create a relation in ExactOnline
	 *
	 * When the debtornumber does not exist in ExactOnline, their webservice
	 * will create it. If it does exist, it will be updated.
	 *
	 * @param array $data Array with data like debtornumber, name and address info
	 *
	 * @return void
	 */
	public function sendRel($data) {
		/* format the data */
		$xml = $this->_formatXML($data);
		/* build the url */
		$url = sprintf("%s%s?Topic=%s&UserName=%s&Password=%s&PartnerKey=%s",
			self::BASE_URL,
			self::FILE_UPLOAD,
			self::TOPIC_RELATION,
			$this->_exact_username,
			$this->_exact_password,
			$this->_exact_partnerkey
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, trim($xml));
		$res = curl_exec($ch);
	}
	/* }}} */
	/* _formatXML {{{ */
	/**
	 * Create XML structure to send to ExactOnline
	 *
	 * @param array $data The relation data
	 *
	 * @return string XML string ready to be send to ExactOnline
	 */
	private function _formatXML($data) {
		$xml_fmt = '
			<?xml version="1.0" encoding="utf-8"?>
			<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-XML.xsd">
				<Accounts>
					<Account code="%d" searchcode="" status="%s" ID="">
						<Name>%s</Name>
						<Phone>%s</Phone>
						<PhoneExt />
						<Fax>%s</Fax>
						<Email>%s</Email>
						<HomePage>%s</HomePage>
						<IsSupplier>%d</IsSupplier>
						<IsBlocked>0</IsBlocked>
						<IsSales>%d</IsSales>
						<IsPurchase>%d</IsPurchase>
						<Address type="VIS" default="1">
							<AddressLine1>%s</AddressLine1>
							<AddressLine2 />
							<AddressLine3 />
							<PostalCode>%s</PostalCode>
							<City>%s</City>
							<State />
							<Country code="%s" />
							<Phone>%s</Phone>
							<Fax>%s</Fax>
						</Address>
					</Account>
				</Accounts>
			</eExact>
		';
		$xml = sprintf($xml_fmt,
			$data["debtor_nr"],
			($data["is_customer"])?"C":"",
			$data["companyname"],
			$data["business_phone_nr"],
			$data["business_fax_nr"],
			$data["email"],
			$data["website"],
			($data["is_supplier"])?1:0,
			($data["is_customer"])?1:0,
			($data["is_customer"])?1:0,
			$data["business_address"],
			$data["business_zipcode"],
			$data["business_city"],
			$data["business_country"],
			$data["business_phone_nr"],
			$data["business_fax_nr"]
		);
		return trim($xml);
	}
	/* }}} */
}
?>
