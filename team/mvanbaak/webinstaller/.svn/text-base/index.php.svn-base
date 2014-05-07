<?php
require("covidewebinstaller.php");

$wi = new covidewebinstaller();

// some debugging only for now
if ($wi->checkOfficeByName("intern")) {
	echo "covide office with name 'intern' does not exist yet<br />\n";
} else {
	echo "covide office with name 'intern' already exists<br />\n";
}

if ($wi->checkOfficeByName("internbestaatniet")) {
	echo "covide office with name 'internbestaatniet' does not exist yet<br />\n";
} else {
	echo "covide office with name 'internbestaatniet' already exists<br />\n";
}

var_dump($wi->createConfig("michiel", array(), "michiel.covide.nl", array("vanbaak.covide.nl", "covide.vanbaak.info", "covide.vanbaak.eu")));
?>
