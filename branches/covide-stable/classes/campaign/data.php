<?php
/**
 * Covide Groupware-CRM Campaign module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Campaign_data {

	/* constants */
	const include_dir = "classes/campaign/inc/";
	const include_dir_main = "classes/html/inc/";

	const class_name = "campaign";
	public $campaign_type = array();
	public $actions = array();

	/* methods */
	public function __construct() {
		$this->campaign_type = array(
			1 => gettext("mail"),
			2 => gettext("voip"),
			4 => gettext("export")
		);
		$this->actions = array(
			1 => gettext("not interested"),
			2 => gettext("do not contact again"),
			3 => gettext("appointment made"),
			4 => gettext("email sent"),
			8 => gettext("note sent"),
			5 => gettext("call again later"),
			6 => gettext("changed contact data"),
			7 => gettext("changed classifications")
		);
	}

	public function getCampaignById($id) {
		$q = sprintf("select * from campaign where id = %d", $id);
		$res = sql_query($q);
		return sql_fetch_assoc($res);
	}
	public function getCampaignsBySearch($options) {
		$like = sql_syntax("like");
		$conversion = new Layout_conversion();
		$class_obj = new Classification_data();
		$data = array();
		$q = sprintf("select * from campaign where (name %1\$s '%%%2\$s%%'
			or description %1\$s '%%%2\$s%%') and is_active = %3\$d order by datetime",
			$like, $options["search"], $options["is_active"]);
		$res = sql_query($q);
		$qcount = sprintf("select count(*) from campaign where (name %1\$s '%%%2\$s%%'
			or description %1\$s '%%%2\$s%%') and is_active = %3\$d order by datetime",
			$like, $options["search"], $options["is_active"]);
		$data["count"] = sql_result(sql_query($qcount));
		while ($row = sql_fetch_assoc($res)) {
		
			$cla_data = unserialize($row["classifications"]);
			/* positive classifications */
			$cla_array = explode("|", $cla_data["positive"]);
			$class_return = array();
			foreach ($cla_array as $k=>$v) {
				if (!$v) {
					unset($cla_array[$k]);
				} else	{
					$cla_info = $class_obj->getClassificationById($v);
					$class_return[] = $cla_info["description"];
				}
			}
			$row["classification_names"] = implode(", ", $class_return);
			/* negative classifications */
			$cla_array = explode("|", $cla_data["negative"]);
			$class_return = array();
			foreach ($cla_array as $k=>$v) {
				if (!$v) {
					unset($cla_array[$k]);
				} else	{
					$cla_info = $class_obj->getClassificationById($v);
					$class_return[] = $cla_info["description"];
				}
			}
			$row["classification_names_negative"] = implode(", ", $class_return);
			$row["datetime_h"] = date("d-m-Y H:i", $row["datetime"]);
			if (!$row["type"]) $row["type"] = 1;
			$row["type_h"] = $this->campaign_type[$row["type"]];

			if ($row["type"] == 2) {
				$row["is_callscript"] = 1;
				$q = sprintf("SELECT count(*) FROM campaign_records WHERE answer LIKE '%%%d%%' AND campaign_id = %d AND (call_again <= %d AND call_again > 0)", "5", $row["id"], time());
				$r = sql_query($q);
				if (sql_result($r, 0)) {
					$row["is_recallable"] = 1;
				} else {
					$row["is_recallable"] = 0;
				}
			} else {
				$row["is_callscript"] = 0;
			}
			
			/* Is it a newsletter? */
			unset($row["show_mail_icon"]);
			if ($row["type"] == 1) {
				/* Is the newsletter sent yet? */
				$q = sprintf("SELECT COUNT(*) FROM mail_messages WHERE is_new = 1 AND id = %d",
					$row["tracker_id"]);
				$row["show_mail_icon"] = sql_result(sql_query($q), 0);
			}

			$q = sprintf("select count(*) from campaign_records where campaign_id = %d",
				$row["id"]);
			$res2 = sql_query($q);
			$row["count"] = sql_result($res2,0);
			$row["description"] = $conversion->limit_string($row["description"], 255);

			$row["can_delete"] = ($row["is_active"]) ? 0 : 1;
			$data["data"][] = $row;
		}
		return $data;
	}
	public function saveCampaign($req) {
		if (!$req["id"]) {
			$address_data = new Address_data();
			$row["info"] = $address_data->getExportInfo($req["exportid"]);

			$data["classification"] = $row["info"]["classifications"];
			$data["classification"]["type"] = $row["info"]["addresstype"];
			$data["classification"]["operator"] = strtolower($row["info"]["selectiontype"]);
			$data["classification"]["target"] = "complete";

			$options        = $row["info"];
			$options["top"] = 0;

			if ($data["classification"]["type"] == "bcards")
				$options["bcard_export"] = true;

			$list = $address_data->getRelationsList($options);
			$res = sql_query($list["query_csv"]);

			$ids = array();
			while ($row = sql_fetch_assoc($res)) {
				$ids[] = $row["id"];
			}
			$data["ids"]  = $ids;
			$data["type"] = $req["type"];
			$data["name"] = $req["camp"]["name"];
			$data["description"] = $req["camp"]["description"];
			unset($ids);

			$this->addCampaign($data);
		} else {
			$sql = sprintf("UPDATE campaign SET name = '%s', description = '%s', is_active = %d WHERE id = %d", 
				$req["camp"]["name"], $req["camp"]["description"], $req["camp"]["is_active"], $req["id"]);
			sql_query($sql);
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("parent.location.href='?mod=campaign'; closepopup();");
			$output->end_javascript();
			$output->exit_buffer();
		}
	}
	public function addCampaign($data) {
		if (!$data["type"])
			$data["type"] = 1;
		$cla = serialize($data["classification"]);
		$q = sprintf("insert into campaign (name, description, classifications, datetime, type, tracker_id, is_active)
			values ('%s', '%s', '%s', %d, %d, %d, 1)",
			$data["name"],
			$data["description"],
			$cla,
			time(),
			$data["type"],
			$data["mail_tracker"]
		);
		sql_query($q);

		$new_id = sql_insert_id("campaign");

		foreach ($data["ids"] as $email=>$id) {
			if ($data["classification"]["type"] == "bcards") {
				$bcard_id = $id;
				$id = 0;
			} else {
				$bcard_id = 0;
			}
			$q = sprintf("insert into campaign_records (campaign_id, address_id, businesscard_id, email) values
				(%d, %d, %d, '%s')", $new_id, $id, $bcard_id, (!is_numeric($email)) ? $email:"");
			sql_query($q);
		}

		if ($data["type"] != 1) {
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("parent.location.href='?mod=campaign'; closepopup();");
			$output->end_javascript();
			$output->exit_buffer();
		}
		exit();
	}
	public function getCampaignRecordById($id) {
		$q = sprintf("select * from campaign_records where id = %d", $id);
		$res = sql_query($q);
		$row = sql_fetch_assoc($res);

		$address_data = new Address_data();
		$output = new Layout_output();
		$output->insertAction("ok", gettext("option set"), "");
		$buf = $output->generate_output();

		/* get address name */
		if (!$row["address_id"]) {
			$row["address_data"] = $address_data->getAddressById($row["businesscard_id"], "bcards");
			$row["name"] = $row["address_data"]["companyname"];
			$row["address_id"] = $row["address_data"]["address_id"];
			$row["address_data"]["phone_nr_link"] = $row["address_data"]["business_phone_nr_link"];
			$row["address_data"]["mobile_nr_link"] = $row["address_data"]["business_mobile_nr_link"];
			$row["address_data"]["email"] = $row["address_data"]["business_email"];
		} else {
			$row["name"] = $address_data->getAddressNameById($row["address_id"]);
			$row["address_data"] = $address_data->getAddressById($row["address_id"]);
		}
		/* get options */
		$opts = preg_split('//', $row["answer"], -1, PREG_SPLIT_NO_EMPTY);
		foreach ($opts as $opt) {
			$row[sprintf("options_%d", $opt)] = $buf;
		}

		return $row;
	}

	public function getCampaignRecordsBySearch($options) {
		$data = array();
		$address_data = new Address_data();
		$userdata = new User_data();
		if (isset($options["answer"])) {
			$answer = $options["answer"];
			if (isset($answer) && is_numeric($answer)) {
				$q = sprintf("
				select * from campaign_records where campaign_id = %d and answer LIKE '%%%d%%' ORDER BY answer",
				$options["id"], $answer);
			}
			if (isset($answer) && $answer == 'unhandled') {
				$q = sprintf("
				select * from campaign_records where campaign_id = %d and answer IS NULL ORDER BY answer",
				$options["id"], $answer);
			}
		} else {
			$q = sprintf("
			select * from campaign_records where campaign_id = %d ORDER BY answer",
			$options["id"]);
		}
		$res = sql_query($q);
		$camp_info = $this->getCampaignById($options["id"]);
		$output = new Layout_output();
		$output->insertAction("ok", gettext("option set"), "");
		$buf = $output->generate_output();

		$n = 0;
		while ($row = sql_fetch_assoc($res)) {
			/* Handle names */
			if ($row["address_id"]) {
				$row["name"] = $address_data->getAddressNameById($row["address_id"]);
				/* There is an address filled in, and it is a type 0 (newsletter). This means we're dealing with employees! */
				if ($camp_info["type"] == 0) {
					$employee = $address_data->getRecord(array("type"=>"user", "id"=>$row["address_id"]));
					$row["name"] = $employee["tav"];
				}
			}
			if ($row["businesscard_id"]) {
				$bcard_data = $address_data->getAddressById($row["businesscard_id"], "bcards");
				$row["name"] = $bcard_data["companyname"] . "\n(".$bcard_data["fullname"].")";
			}
			/* If handled by user, show name */
			if ($row["user_id"])
				$row["user_name"] = $userdata->getUsernameById($row["user_id"]);
			/* get human date for recall */
			if ($row["call_again"])
				$row["call_again_h"] = date("d-m-Y H:i", $row["call_again"]);
			/* get options */
			$opts = preg_split('//', $row["answer"], -1, PREG_SPLIT_NO_EMPTY);
			foreach ($opts as $opt) {
				$e_buf = '';
				if ($opt == 5)
					$e_buf = " ".$row["call_again_h"];
				$row[sprintf("options_%d", $opt)] = $buf.$e_buf;
			}

			/* every other 15 rows, add the data to maintain a good overview */
			if ($n % 15 == 0) {
				$extra_row["name"] = gettext("name");
				$extra_row["user_name"] = gettext("by");
				foreach ($this->actions as $k=>$v) {
					$extra_row[sprintf("options_%d", $k)] = $v;
				}
				$extra_row["is_extra_row"] = 1;
				$data[] = $extra_row;
			} 
			$n++;
						
			/* search/filter */
			if (($options["search"] && stristr($row["name"], $options["search"])) || !$options["search"]) {
				$data[] = $row;
			}
		}
		return $data;
	}

	public function save_edit_record($id, $options, $callscript=0, $calltime=0) {
		if(!is_array($options)){
			$options = array();
		}
		$keys = implode(',', $options);
		/* Default settings for every option */
		$q = sprintf("update campaign_records set is_called = %d, answer = '%s', user_id = %d, call_again = 0 where id = %d",
			1, $keys, $_SESSION["user_id"], $id);
		sql_query($q);

		/* Option "do not contact again" has been enabled */
		if (in_array('2', $options)) {
			$cla_data = new Classification_data();
			$cla_info = $cla_data->getSpecialClassification("do not contact");
			$cla_id = $cla_info[0]["id"];
			$record_data = $this->getCampaignRecordById($id);
			$address_id = $record_data["businesscard_id"];
			$q_get = sprintf("SELECT classification FROM address_businesscards WHERE id = %d", $address_id);
			$res_get = sql_query($q_get);
			$current_cla = sql_result($res_get, 0);
			$new_cla = preg_replace("/\|{1,}/si", "|", $current_cla."|".$cla_id."|");
			$qu = sprintf("update address_businesscards set classification = '%s' where id = %d",
			$new_cla, $address_id);
			sql_query($qu);
		}
		/* Option "call again later" has been enabled */
		if (in_array('5', $options)) {
			$call_timestamp = mktime($calltime["hour"], $calltime["minute"], 0, $calltime["month"], $calltime["day"], $calltime["year"]);
			$q = sprintf("UPDATE campaign_records SET call_again = %d WHERE id = %d ", $call_timestamp, $id);
			sql_query($q);
		}

		$q = sprintf("select campaign_id from campaign_records where id = %d", $id);
		$res = sql_query($q);
		$campaign = sql_result($res,0);

		$output = new Layout_output();
		$output->start_javascript();
			if ($callscript)
				if ($callscript == 1)
					$output->addCode(sprintf("document.location.href='?mod=campaign&action=callscript&id=%d'", $campaign));
				elseif ($callscript == 2)
					$output->addCode(sprintf("document.location.href='?mod=campaign&action=recallscript&id=%d'", $campaign));
				else
					$output->addCode("parent.document.getElementById('velden').submit(); closepopup();");
			else
				$output->addCode("parent.parent.document.getElementById('velden').submit(); closepopup();");
		$output->end_javascript();
		$output->exit_buffer();
	}
	public function getAddressIdByRecordId($id) {
		$q = sprintf("select address_id from campaign_records where id = %d", $id);
		$res = sql_query($q);
		return sql_result($res);
	}
	public function deleteCampaign($id) {
		$q = sprintf("delete from campaign_records where campaign_id = %d", $id);
		sql_query($q);
		$q = sprintf("delete from campaign where id = %d", $id);
		sql_query($q);
	}
	public function callscript($id) {
		/* get current record of this user */
		$q = sprintf("select id from campaign_records where campaign_id = %d and is_called = %d and (answer = '' or answer is null)",
			$id, $_SESSION["user_id"]);
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			/* get current record for this user */
			$record = sql_result($res);
			$uri = sprintf("?mod=campaign&action=show_edit_record&id=%d&campaign=%d&callscript=1", $record, $id);
		} else {
			/* get next free record */
			$q = sprintf("select id from campaign_records where campaign_id = %d and (is_called = 0 or is_called is null)", $id);
			$res = sql_query($q);
			if (sql_num_rows($res) > 0) {
				$record = sql_result($res);
				$q = sprintf("update campaign_records set is_called = %d where id = %d",
					$_SESSION["user_id"], $record);
				sql_query($q);
			} else {
				$record = 0;
			}
		}

		$output = new Layout_output();
		$output->layout_page('', 1);
		$output->start_javascript();
		if ($record) {
			$output->addCode(sprintf("document.location.href = '?mod=campaign&action=edit_record&id=%d&campaign=%d&callscript=1'",
				$record, $id));
		} else {
			$output->addCode("alert(gettext('Nothing to do, all called!')); closepopup();");
		}
		$output->end_javascript();
		$output->layout_page_end();
		$output->exit_buffer();
	}
	public function recallscript($id) {
		/* get current record of this user */
		$q = sprintf("select id from campaign_records where campaign_id = %d and answer LIKE '%%%d%%' AND (call_again <= %d AND call_again > 0)", $id, "5", time());
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			/* get current record for this user */
			$record = sql_result($res);
			$uri = sprintf("?mod=campaign&action=show_edit_record&id=%d&campaign=%d&callscript=2", $record, $id);
		} 

		$output = new Layout_output();
		$output->layout_page('', 1);
		$output->start_javascript();
		if ($record) {
			$output->addCode(sprintf("document.location.href = '?mod=campaign&action=edit_record&id=%d&campaign=%d&callscript=2'",
				$record, $id));
		} else {
			$output->addCode("alert(gettext('Nothing to do, all called!')); closepopup();");
		}
		$output->end_javascript();
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function addClassToDatabase($id) {
		// get input classification
		$classi = $_POST["classifications"];
		$new_pos = $classi["positive"];
		$new_neg = $classi["negative"];
		//make array so we can push the current classifiction in it
		$new_pos = explode ("|", $new_pos);
		$new_neg = explode ("|", $new_neg);
		//get first the current claasifications
		$q = sprintf("SELECT classifications FROM campaign WHERE id = %d", $id);
		$r = sql_query($q);
		$classifications = unserialize(sql_result($r, 0));
		$cur_pos = $classifications["positive"];
		$cur_neg = $classifications["negative"];
		//make array
		$cur_posi = explode ("|", $cur_pos);
		$cur_nega = explode ("|", $cur_neg);
		//push the claasification together in one array
		foreach($new_pos as $k=>$v) {
			array_push ($cur_posi, $v);
		}
		foreach($new_neg as $k=>$v) {
			array_push ($cur_nega, $v);
		}
		//get only the unique classifications
		$cur_posi = array_unique($cur_posi);
		$cur_nega = array_unique($cur_nega);
		//make string to insert to database
		$cur_posi = implode ("|", $cur_posi);
		$cur_nega = implode ("|", $cur_nega);
		$data["classification"]["type"]     = $classifications["type"];
		$data["classification"]["operator"] = $classifications["operator"];
		$data["classification"]["target"]   = $classifications["target"];

		$data["classification"]["positive"] = preg_replace("/\|{1,}/si", "|", $cur_posi);
		$data["classification"]["negative"] = preg_replace("/\|{1,}/si", "|", $cur_nega);
		$cla = serialize($data["classification"]);
		$q = sprintf("update campaign SET classifications = '%s' WHERE id = %d", $cla, $id);
		$res = sql_query($q);
		//refresh the classification
		$this->refreshClassifications($id);
	}

	public function refreshClassifications($campaign_id) {
		$q = sprintf("SELECT classifications FROM campaign WHERE id = %d", $campaign_id);
		$r = sql_query($q);
		$classifications = unserialize(sql_result($r, 0));
		$options["classifications"]["positive"] = $classifications["positive"];
		$options["classifications"]["negative"] = $classifications["negative"];
		$options["addresstype"] = $classifications["type"];
		$options["selectiontype"] = $classifications["operator"];

		$address_data = new Address_data;
		$list = $address_data->getRelationsList($options);
		$res = sql_query($list["query_csv"]);
		$ids = array();
		while ($row = sql_fetch_assoc($res)) {
			$ids[] = $row["id"];
		}
		$data["ids"]  = $ids;
		unset($ids);
		foreach ($data["ids"] as $email=>$id) {
			$q_check = sprintf("SELECT count(id) FROM campaign_records WHERE campaign_id = %1\$d AND (businesscard_id = %2\$d OR address_id = %2\$d)",
				$campaign_id, $id);
			$q_q = sql_query($q_check);
			if (!sql_result($q_q, 0)) {
				$q_add = sprintf("insert into campaign_records (campaign_id, businesscard_id, email) values
					(%d, %d, '%s')", $campaign_id, $id, (!is_numeric($email)) ? $email:"");
				sql_query($q_add);
			}
		}
		$output = new Layout_output();
		$output->layout_page('', 1);
		$output->start_javascript();
		$output->addCode("parent.location.href=parent.location.href; closepopup();");
		$output->end_javascript();
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function campaignHasTracker($id) {
		$q = sprintf("select count(*) as has_trakcker from campaign where tracker_id = %d", $id);
		$res = sql_query($q);
		return sql_result($res, 0);
	}
	
	public function campaignHasRecalls() {
		$current_time = time();
		$q = sprintf("SELECT count(campaign_records.id) as possibilities FROM campaign_records, campaign WHERE campaign.id = campaign_records.campaign_id AND campaign.is_active = 1 AND (campaign_records.call_again < %d AND campaign_records.call_again != 0)", $current_time);
		$res = sql_query($q);
		return sql_result($res, 0);
	}
	
	public function campaignHasAppointment($id) {
		$q = sprintf("SELECT appointment_id FROM campaign_records WHERE appointment_id = %d", $id);
		$res = sql_query($q);
		return sql_result($res, 0);
	}
}
?>
