<html>
<head>
	<title>Newsletter unsubscription</title>
	<style>
	div.container {
		border: 1px solid black;
		font-family: Arial, sans-serif;
		font-size: 0.8em;
		padding: 10px;
		padding-top: 20px;
		margin: auto;
		margin-top: 50px;
		height: 200px;
		width: 200px;
	}
	div.container h1 {
		margin: 0px;
		font-size: 1.2em;
	}
	div.container div.content {
		height: 190px;
	}
	div.container div.sub {
		text-align: right;
		font-size: 0.7em;
		height: 10px;
	}
	</style>
</head>
<body>
<div class="container">
<div class="content">
<?php
	$bottom = '
		</div>
		<div class="sub">newsletter powered by <a target="_blank" href="http://www.covide.net">Covide</a></div>
		</div>
	</body>
	</html>';
	$skip_run_module = 1;
	require_once("index.php");

	$output = new Layout_output();
	$output->addTag("h1");
	$output->addCode("Newsletter unsubscription");
	$output->endTag("h1");

	if ($_REQUEST["confirmed"] && $_REQUEST["code"] && $_REQUEST["hashcode"]) {
		/* Check if the url used to unsubscribe is valid */
		$sql = sprintf("select * from mail_unsubscription where email='%s' and hashcode='%s'", $_REQUEST["code"], $_REQUEST["hashcode"]);
		$rs = mysql_query($sql);
		if (mysql_num_rows($rs)>0) {
			$data["email"] = $_REQUEST["code"];

			$address_data = new Address_data();
			$emailsql = sprintf("select id from address_businesscards where business_email = '%s' or personal_email = '%s' or other_email = '%s'", $data["email"], $data["email"], $data["email"]);
			$emailres = mysql_query($emailsql);
			$addresses = mysql_fetch_assoc($emailres);

			foreach ($addresses as $k => $v) {
				$address_ids[] = $v;
			}

			$data = array();

			/* create classification "no newsletter" and assign it to these addresses */
			$classification = new Classification_data();
			$class_arr = $classification->getSpecialClassification("no newsletter");
			$data = $class_arr[0];
			$data["bcard_id"] .= implode(",", $address_ids);
			$classification->store2db($data);
			$output->addCode("You have unsubscribed from the newsletter.");

			/* Remove the unsubscription record */
			$sql = sprintf("delete from mail_unsubscription where email='%s' and hashcode='%s'", $_REQUEST["code"], $_REQUEST["hashcode"]);
			$rs = mysql_query($sql);
		} else {
			$output->addCode("This not a valid url for unsubscription.");
		}
	} else {
		$query = "select name from license";
		$output->addTag("a", array("href" => "unsubscribe.php?code=".$_REQUEST["code"]."&hashcode=".$_REQUEST["hashcode"]."&confirmed=1"));
			$output->addCode("Click here");
		$output->endTag("a");
		$output->addCode(" if you really want to unsubscribe the e-mail address <b>".$_REQUEST["code"]."</b> from this newsletter");
	}
	$output->addCode($bottom);
	$output->exit_buffer();
?>


