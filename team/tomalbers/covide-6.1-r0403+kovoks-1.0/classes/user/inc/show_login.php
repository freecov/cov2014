<?php
	if (!class_exists("User_output")) {
		die("no class definition found");
	}

	@session_destroy();
	@session_unset();
	@session_start();

	#sleep(1);

	/* generate an challenge string on the server for this request */
	$_SESSION["challenge"] = crc32(session_id().rand().mktime());

	$output = new Layout_output();
	$output->layout_page(gettext("login"));

	$output->addTag("form", array(
		"method" => "post",
		"id"     => "login",
		"action" => $GLOBALS["covide"]->webroot
	));

	/* prevent double slashes */
	if (preg_match("/\/{2,}/s", $_SERVER["SCRIPT_NAME"])) {
		header("Location: ".dirname($_SERVER["SCRIPT_NAME"]));
		exit();
	}
	$output->addHiddenField("mod", "user");
	$output->addHiddenField("subaction", "validate");

	$output->addTag("div", array(
		"style" => "position: absolute; right: 10px; bottom: 20px; z-index: 1;"
	));
	$output->insertImage("covide_bg.gif", "");
	$output->endTag("div");

	$table = new Layout_table( array(
		"style" => "margin-top: 50px;"
	));
	$table->addTableRow();
		$table->addTableData();
			$venster = new Layout_venster(array(
				"title" => "Covide Login"
			));
			$venster->addVensterData();
				$table1 = new Layout_table(array("cellspacing"=>1));
				$table1->addTableRow();
					$table1->addTableData(array("colspan"=>2, "align"=>"center"));
						$table1->insertImage("logo.gif", "Covide CRM-Groupware");
					$table1->endTableData();
				$table1->endTableRow();
				if ($error == 1) {
					$table1->addTableRow();
						$table1->insertTableData(gettext("onjuiste gebruikersnaam of wachtwoord"), array("colspan" => 2), "header");
					$table1->endTableRow();
				}
				/* detect supported browser versions */
				require_once("classes/covide/browser.php");
				$browser = browser_detection("full");
				$ok = 0;

				if ($browser["browser_name"] == "konq" && $browser["math_version"] >= 3.4) {
					$ok = 1;
				}
				if ($browser["browser_name"] == "moz" && $browser["math_version"] >= 1.8) {
					$ok = 1;
				}
				if ($browser["browser_name"] == "ie" && $browser["math_version"] >= 6.0) {
					$ok = 1;
				}
				if (!$ok) {
					$table1->addTableRow();
						$table1->addTableData(array("colspan" => 2), "header");
							$table1->addTag("div", array("style" => "width: 250px;"));
							$table1->addCode(gettext("U maakt mogelijk gebruik van een niet volledig ondersteunde of verouderde browser."));
							$table1->addTag("br");
							$table1->addTag("br");
							$table1->addCode(gettext("Om optimaal gebruik te kunnen maken van Covide raden wij u aan te upgraden naar "));
							$table1->insertTag("a", "Firefox 1.5", array("href"=>"http://www.mozilla.com", "target"=>"_blank", "style"=>"text-decoration: underline"));
							$table1->addCode(" (".gettext("of hoger").") ".gettext("of")." ");
							$table1->insertTag("a", "Internet Explorer", array("href"=>"http://www.microsoft.com/ie", "target"=>"_blank", "style"=>"text-decoration: underline"));
							$table1->addCode(" 6.0 SP1 (".gettext("of hoger").").");
							$table1->endTag("div");
						$table1->endTableData();
					$table1->endTableRow();
				}


				/* check if cookie parameter is set */
				if ($_COOKIE["covideuser"]) {
					$login_cookie = explode("|", base64_decode($_COOKIE["covideuser"]));
					for ($i=0;$i<=12;$i++) {
						$login_cookie[3].="*";
					}
					$passstyle = array("style" => "background-color: #d8ffb6");
				}
				$table1->addTableRow();
					$table1->addTableData((!$GLOBALS["covide"]->mobile) ? array("align"=>"right"):"");
						$table1->addCode("login:");
					$table1->endTableData();
					$table1->addTableData();
						$table1->addTextField("login[username]", ($_REQUEST["login"]["username"]) ? $_REQUEST["login"]["username"]:$login_cookie[0]);
					$table1->endTableData();
				$table1->endTableRow();
				$table1->addTableRow();
					$table1->addTableData((!$GLOBALS["covide"]->mobile) ? array("align"=>"right"):"");
						$table1->addCode("password:");
					$table1->endTableData();
					$table1->addTableData();
						$table1->addPasswordField("login[vis_password]", $login_cookie[3], $passstyle);
						$table1->addHiddenField("login[password]", $login_cookie[1]); //hash from cookie
						$table1->addHiddenField("login[use_cookie_password]", ($login_cookie[0]) ? 1:0);
						$table1->insertAction("ok", gettext("inloggen"), "javascript: login();");
					$table1->endTableData();
				$table1->endTableRow();
				$table1->addTableRow();
					$table1->addTableData(array("colspan"=>2, "align"=>"right"));
						$table1->insertCheckbox("login[save_password]", 1, ($login_cookie[0]) ? 1:0);
						$table1->addCode( "remember password" );
						$table1->addSpace(2);
						if ($GLOBALS["covide"]->sslmode == 2) {
							if ($_REQUEST["login"]["username"]) {
								if ($_REQUEST["use_ssl"]) {
									$ssl = 1;
								} else {
									$ssl = 0;
								}
							} else {
								if ($login_cookie[2]==0 && $login_cookie[0]) {
									$ssl = 0;
								} else {
									$ssl = 1;
								}
							}
							$table1->insertCheckbox("use_ssl", 1, $ssl);
							$table1->addCode( "use ssl encyption");
							$table1->addSpace(2);
						}
					$table1->endTableData();
				$table1->endTableRow();
				$table1->addTableRow();
					$table1->addTableData(array("colspan"=>2));
						$table1->addTag("div", array("id"=>"password_type_div", "style"=>"display: none"));
							$table1->addTag("br");
							$table1->addRadioField("login[remember_type]", "let the browser handle the password", "browser", ($login_cookie[3]) ? "covide":"browser", "pt_browser");
							$table1->addRadioField("login[remember_type]", "let Covide handle the password", "covide", ($login_cookie[3]) ? "covide":"browser", "pt_covide");
						$table1->endTag("div");
					$table1->endTableData();
				$table1->endTableRow();

				$table1->endTable();
				$venster->addCode($table1->generate_output());
			$venster->endVensterData();
			$table->addCode($venster->generate_output());
			unset($venster);
		$table->endTableData();
	$table->endTableRow();
	$table->endTable();

	$output->addTag("div", array(
		"style" => "position: relative; z-index: 2;"
	));
	$output->addCode($table->generate_output());
	$output->endTag("div");

	$output->endTag("form");

	$output->load_javascript(self::include_dir."md5.js");
	$output->load_javascript(self::include_dir."js_common.js");

	$output->start_javascript();
		$output->addCode("
			var crypt_challenge = '".$_SESSION["challenge"]."';

			if (document.getElementById('use_ssl')) {
				document.getElementById('use_ssl').onclick = function() {	checkstate();	}
				addLoadEvent(checkstate());
			}

			function login_challenge() {
				/* get visible password and real password fields */
				var pw = document.getElementById('loginvis_password');
				var realpw = document.getElementById('loginpassword');

				/* if password is set by user  */
				if (document.getElementById('loginuse_cookie_password').value == 0) {
					/* calculate the hash md5 (challenge + md5(password) ) */
					var str = new String().concat( hex_md5( pw.value ), crypt_challenge );
					realpw.value = hex_md5(str);
				} else {
					/* if set by cookie */
					var str = new String().concat(realpw.value, crypt_challenge );
					realpw.value = hex_md5(str);
				}
				/* overwrite the user password with (*) */
				if (document.getElementById('pt_browser').checked == false) {
					pw.value = '';
				}
			}

			if (!crypt_challenge) {
				alert('Your system does not accept sessions or cookies. Please contact your system administrator.');
			}


		");
	$output->end_javascript();

	$output->layout_page_end();
	$output->exit_buffer();
?>
