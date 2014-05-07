<?php
	if (!class_exists("Sync4j")) {
		die("no class defenition found");
	};
	//update php execution time naar 10 min!
	set_time_limit(600);
	$sync4j_convert = new Sync4j_convert();
	$sync4j_address = new Sync4j_address();

	require_once(self::include_dir."../sync_db.php");

	/* ----------------------------------------------------------------------------------- */
	/* some settings/vars																																	 */
	/* ----------------------------------------------------------------------------------- */
	$user_id = (int)$_REQUEST["user_id"];

	//query all items from mapping table in the sync server
	$sources = array("address", "address_businesscards", "address_private", "address_other");
	/* ----------------------------------------------------------------------------------- */
	/* prepare adressdata   																															 */
	/* ----------------------------------------------------------------------------------- */

	foreach ($sources as $s) {

		$items_store = array();
		$items_adres = array();

		//from adress book
		$q = "SELECT id, sync_modified FROM $s ORDER BY id";
		$res = sql_query($q);
		while ($row = sql_fetch_array($res)) {
			$items_adres[$row["id"]] = (int)$row["sync_modified"];
		}

		//from store
		$q = sprintf("SELECT address_id, sync_modified FROM address_sync WHERE address_table = '%s'", $s);
		$res = sql_query($q);
		while ($row = sql_fetch_array($res)) {
			$items_store[$row["address_id"]] = (int)$row["sync_modified"];
		}

		//check for deleted items (adressbook=>store)
		foreach ($items_store as $k=>$v) {
			if (!array_key_exists($k, $items_adres)) {
				$q = sprintf("DELETE FROM address_sync WHERE address_table = '%s' AND address_id = %d", $s, $k);
				sql_query($q);
				unset($items_store[$k]);
				echo "adress removed (store) ...<br>";
			}
		}

		//check for new items (adressbook=>store)
		foreach ($items_adres as $k=>$v) {
			if (!array_key_exists($k, $items_store)) {

				//retrieve xml data
				$data = $sync4j_address->getAdresData($s, $k);
				$xml = $sync4j_address->adress2sync($data);

				//hash it
				$hash = md5($xml);

				//insert into store
				$sync4j_address->insertAdresData($s, $k, $hash, $data);

				echo "adress inserted (store) ...<br>";
			}
		}
		//check for updated items (adressbook=>store)
		$diff = array_diff($items_adres, $items_store);
		foreach ($diff as $k=>$v) {

			//retrieve xml data
			$data = $sync4j_address->getAdresData($s, $k);
			$xml = $sync4j_address->adress2sync($data);

			//hash it
			$hash = md5($xml);

			$sync4j_address->updateAdresData($s, $k, $hash, $data);
			echo "adress updated (store) ...<br>";
		}
	}
