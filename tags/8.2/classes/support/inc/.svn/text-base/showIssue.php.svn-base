<?php
/**
 * Covide Groupware-CRM support module
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
if (!class_exists("Support_output")) {
	die("no class definition found");
}


$supportdata = new Support_data();
$supportitem[] = $supportdata->getSupportItemById($_REQUEST["id"]);
$address_data = new Address_data();
$address_info = $address_data->getAddressById($supportitem[0]["address_id"]);
$is_printing = $_REQUEST["printIt"];

$output = new Layout_output();
$output->layout_page("", 1);

if ($is_printing) {
$output->start_javascript();
	$output->addCode("
		window.print();
		setTimeout('window.close();', 2000);
	");
$output->end_javascript();
}

$settings = array(
	"title"    => gettext("Support"),
	"subtitle" => gettext("reference number: ").$supportitem[0]["reference_nr"]
);

$venster = new Layout_venster($settings);
unset($settings);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
    $table = new Layout_table(array("cellspacing"=>1, "width"=>'100%'));
	$table->addTableRow();
		$table->insertTableHeader(gettext("sender firm information"), array("colspan" => "4", "align"=>"left"));
	$table->endTableRow();


	$table->addTableRow();
          $table->insertTableData(gettext("firmname"));
          $table->insertTableData($supportitem[0]["relname"]);
          $table->insertTableData(gettext("telephone"));
          $table->insertTableData($address_info["phone_nr"]);
	$table->endTableRow();

	$table->addTableRow();
          $table->insertTableData(gettext("contactperson"));
          $table->insertTableData($address_info["tav"]);
          $table->insertTableData(gettext("fax"));
          $table->insertTableData($address_info["fax_nr"]);
	$table->endTableRow();

	$table->addTableRow();
          $table->insertTableData(gettext("address"));
          $table->insertTableData($address_info["address"]);
          $table->insertTableData(gettext("mobile"));
          $table->insertTableData($address_info["mobile_nr"]);
	$table->endTableRow();

	$table->addTableRow();
          $table->insertTableData(gettext("zipcode"));
          $table->insertTableData($address_info["zipcode"]);
	$table->endTableRow();

	$table->addTableRow();
          $table->insertTableData(gettext("city"));
          $table->insertTableData($address_info["city"]);
	$table->endTableRow();


	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);

$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);

$output->addTag("br");

$venster = new Layout_venster($settings);
unset($settings);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
       $table = new Layout_table(array("cellspacing"=>1, "width"=>'100%'));
	$table->addTableRow();
		$table->insertTableHeader(gettext("support issue description"), array("align"=>"left"));
	$table->endTableRow();


	$table->addTableRow();
          $table->insertTableData(nl2br($supportitem[0]["description"]));

	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);



$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);



$output->addTag("br");

$venster = new Layout_venster($settings);
unset($settings);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
       $table = new Layout_table(array("cellspacing"=>5));
	$table->addTableRow();
		$table->insertTableHeader(gettext("execution date"));
		if (!$is_printing)
			$table->insertTableData(gettext("please fill in"));
	$table->endTableRow();

	$table->addTableRow();
        $table->insertTableData($supportitem[0]["execution_human_date"]);
	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);

$output->addTag("br");

$venster = new Layout_venster($settings);
unset($settings);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
       $table = new Layout_table(array("cellpadding"=>2));
	$table->addTableRow();
		$table->insertTableHeader(gettext("support picked up by"),array("colspan"=>"2", "align"=>"left"));
	$table->endTableRow();

	$table->addTableRow();
          $table->insertTableData(gettext("picked up by"));
          $table->insertTableData($supportitem[0]["sender_name"]);
	$table->endTableRow();

	$table->addTableRow();
          $table->insertTableData(gettext("received on"));
          $table->insertTableData($supportitem[0]["human_date"]);
	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);


$output->addTag("br");

$venster = new Layout_venster($settings);
unset($settings);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
       $table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableHeader(gettext("remarks"));
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData($supportitem[0]["remarks"]);
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);


$output->addTag("br");

$venster = new Layout_venster($settings);
unset($settings);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
       $table = new Layout_table(array("cellpadding"=>2));
	$table->addTableRow();
		$table->insertTableHeader(gettext("applied solutions")." ".gettext("by")." ". $supportitem[0]["rcpt_name"], array("align"=>"left"));
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableData(nl2br($supportitem[0]["solution"]));
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->insertTableHeader(gettext("time of arrival").":", array("align"=>"left"));
	$table->endTableRow();
	
	$table->addTableRow();
		$table->insertTableHeader(gettext("time of departure").":", array("align"=>"left"));
	$table->endTableRow();
	
	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);

$output->addTag("br");

$venster = new Layout_venster($settings);
unset($settings);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
       $table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableHeader(gettext("used supplies"));
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);

$output->addTag("br");

$venster = new Layout_venster($settings);
unset($settings);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
       $table = new Layout_table(array("cellspacing"=>1));
	$table->addTableRow();
		$table->insertTableHeader(gettext("name and signature customer"));
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->addTableRow();
		$table->addTableData();
		$table->addSpace("1");
		$table->addTableData();
	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);



if(!$is_printing) {

$output->addTag("br");

$venster = new Layout_venster($settings);
unset($settings);

	/* put the table in the $venster data buffer and destroy object */
$venster->addVensterData();

	unset($view);
       $table = new Layout_table(array("cellspacing"=>1));

	$table->addTableRow();
		$table->addTableData();
		$table->insertAction("print", gettext("print"), "index.php?mod=support&action=showitem&id=".$_REQUEST["id"]."&printIt=1");
		$table->addSpace("2");
		$table->insertAction("close", gettext("close"), "javascript: window.close();");
		$table->addTableData();
	$table->endTableRow();

	$table->endTable();
	$venster->addCode($table->generate_output());
	unset($table);
$venster->endVensterData();
$output->addCode($venster->generate_output());
unset($venster);
}


/* end of entire page */
$output->layout_page_end();
echo $output->generate_output();


?>
