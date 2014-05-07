<?php
	/**
	 * Covide Groupware-CRM user module
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
	if (!class_exists("User_output")) {
		die("no class definition found");
	}

	@session_destroy();
	@session_unset();
	@session_start();

	/* allow some custom scripting before the loginscreen is shown
		A good example is to reset demo users. */
	if (file_exists("custom/login_pre.php"))
		@include("custom/login_pre.php");

	if ($GLOBALS["covide"]->contrib["USE_CONTRIB_LOGIN"]) {
		$file = "contrib/".$GLOBALS["covide"]->contrib["USE_CONTRIB_LOGIN"]."/pre_login.php";
		if (file_exists($file))
			require_once($file);
	}

	/* generate an challenge string on the server for this request */
	$_SESSION["challenge"] = crc32(session_id().rand().mktime());

	$output = new Layout_output();
	$output->layout_page(gettext("login"));

	if ($GLOBALS["covide"]->license["has_cms"]) {
		/* detect dirname */
		if ($_REQUEST["uri"]) {
			$output->start_javascript();
				$output->addCode("window.resizeTo(700, 550);");
			$output->end_javascript();

			$GLOBALS["covide"]->sslmode = 3;
		}
		if (preg_match("/[a-z0-9]/si", dirname($_SERVER["SCRIPT_NAME"]))) {
			$output->addCode("<br><br><center><b>WARNING: COVIDE WITH CMS CANNOT RUN IN A DIRECTORY!</b></center>");
		}

		$uri = $GLOBALS["covide"]->webroot."/index.php?mod=desktop";
		$uri = preg_replace("/\/{1,}/s", "/", $uri);
	} else {
		$uri = $GLOBALS["covide"]->webroot."index.php?mod=desktop";
		if (in_array($GLOBALS["covide"]->license["force_ssl"], array(1,2)))
			$uri = preg_replace("/^http(s){0,1}:\/\//si", "https://", $GLOBALS["covide"]->webroot."index.php?mod=desktop");
	}
	if (!$GLOBALS["covide"]->mobile)
		$uri = sprintf("%s/index.php", dirname($uri));
	else
		$uri = "index.php";

	$output->addTag("form", array(
		"method" => "post",
		"id"     => "login",
		"action" => $uri
	));

	/* prevent double slashes */
	if (preg_match("/\/{2,}/s", $_SERVER["SCRIPT_NAME"])) {
		$uri = dirname($_SERVER["SCRIPT_NAME"])."/index.php?mod=desktop";
		$uri = preg_replace("/\/{1,}/s", "/", $uri);
		header("Location: ".$uri);
		exit();
	}
	$output->addHiddenField("mod", "user");
	$output->addHiddenField("subaction", "validate");
	$output->addHiddenField("mobile", $GLOBALS["covide"]->mobile);
	if ($GLOBALS["covide"]->mobile) {
		$mobile_uri = sprintf("%s/%s", $_SERVER["HTTP_HOST"], dirname($_SERVER["SCRIPT_NAME"]));
		$output->addHiddenField("mobile_uri", $mobile_uri);
	}

	$output->addHiddenField("webroot", $GLOBALS["covide"]->webroot);
	$output->addHiddenField("basepath", preg_replace("/^http(s){0,1}:\/\//si", "", $GLOBALS["covide"]->webroot));

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
			$table->start_javascript();
				$table->addCode("var radius_auth = 0;\n");
				if (!$GLOBALS["covide"]->license["has_radius"])
					$table->addCode("var check_radius = 0;\n");
				else
					$table->addCode("var check_radius = 1;\n");
			$table->end_javascript();

			$venster = new Layout_venster(array(
				"title" => ($_REQUEST["uri"]) ? "CMS Login":"Covide Login"
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
						$table1->insertTableData(gettext("wrong username or password"), array("colspan" => 2), "header");
					$table1->endTableRow();
				}
				if ($error == 98) {
					$table1->addTableRow();
						$table1->insertTableData(gettext("User is already logged in"), array("colspan" => 2), "header");
					$table1->endTableRow();
				}
				if ($error == 99) {
					$table1->addTableRow();
						$table1->insertTableData(
							gettext("An error occured while trying to contact the Radius server. Please contact support."),
							array("colspan" => 2),
							"header"
						);
					$table1->endTableRow();
				}
				/* detect supported browser versions */
				require_once("classes/covide/browser.php");
				$browser = browser_detection("full");
				$ok = 0;
				/* mobile browsers do not support html editors, we know ;) */
				if ($GLOBALS["covide"]->mobile)
					$ok = 1;
				/* mozilla */
				if ($browser["browser_name"] == "moz" && $browser["math_version"] >= 1.8)
					$ok = 1;
				/* msie */
				if ($browser["browser_name"] == "ie" && $browser["math_version"] >= 6.0)
					$ok = 1;
				/* konqueror */
				if ($browser["browser_name"] == "konq" && $browser["math_version"] >= 3.5)
					$ok = 1;
				/* opera */
				if ($browser["browser_name"] == "op" && $browser["math_version"] >= 9.0)
					$ok = 1;
				/* safari */
				if ($browser["browser_name"] == "saf" && $browser["math_version"] >= 417.9)
					$ok = 1;

				#print_r($browser);
				if (!$ok) {
					$table1->addTableRow();
						$table1->addTableData(array("colspan" => 2), "header");
							$table1->addTag("div", array("style" => "width: 250px;"));
							$table1->addCode(gettext("Your browser is not fully supported."));
							$table1->addTag("br");
							$table1->addTag("br");
							$table1->addCode(gettext("To be able to use all functions of Covide we suggest you upgrade to"));
							$table1->insertTag("a", " Firefox 2.0", array("href"=>"http://www.mozilla.com", "target"=>"_blank", "style"=>"text-decoration: underline"));
							$table1->addCode(" (".gettext("or better").") ".gettext("or")." ");
							$table1->insertTag("a", "Internet Explorer", array("href"=>"http://www.microsoft.com/ie", "target"=>"_blank", "style"=>"text-decoration: underline"));
							$table1->addCode(" 7 (".gettext("or better").").");
							$table1->endTag("div");
						$table1->endTableData();
					$table1->endTableRow();
				}

				if (in_array($GLOBALS["covide"]->license["force_ssl"], array(0,1,2)) && $browser["browser_name"] == "ie"
					&& $GLOBALS["covide"]->certificate) {

					$table1->addTableRow();
						$table1->addTableData(array("colspan" => 2), "data");
							$table1->addTag("div", array("style" => "width: 250px; text-align: center;"));
							$table1->addCode(gettext("If you encounter certificate errors after login, please install the certificate once by clicking")." ");
							$table->addTag("b");
							$table1->insertTag("a", gettext("here"), array(
								"href"   => $GLOBALS["covide"]->certificate,
								"align"  => "center"
							));
							$table->endTag("b");

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
					$table1->addTableData(array("align"=>"center", "colspan" => 2));
						$table1->addHiddenField("uri", str_replace("'", "", strip_tags(stripslashes($_REQUEST["uri"]))));
						if ($_REQUEST["uri"]) {
							$seq = new Layout_output();
							$seq->addSpace();
							$seq->addCode(htmlentities(">>"));
							$seq->addTag("br");
							$seq->addSpace();

							$_uri = $_REQUEST["uri"];
							$_uri = stripslashes($_uri);
							$_uri = strip_tags($_uri);
							$_uri = wordwrap($_uri, 30, $seq->generate_output(), 1);

							$table1->insertTag("b", gettext("referrer").": ");
							$table1->insertTag("i", $_uri);

							unset($_uri);
							unset($seq);
						}
					$table1->endTableData();
				$table1->endTableRow();
				$table1->addTableRow();
					$table1->addTableData((!$GLOBALS["covide"]->mobile) ? array("align"=>"right"):"");
						$table1->addCode(gettext("login").":");
					$table1->endTableData();
					$table1->addTableData();
						$table1->addTextField("login[username]", ($_REQUEST["login"]["username"]) ? stripslashes($_REQUEST["login"]["username"]):$login_cookie[0], "");
					$table1->endTableData();
				$table1->endTableRow();
				$table1->addTableRow();
					$table1->addTableData((!$GLOBALS["covide"]->mobile) ? array("align"=>"right"):"");
						$table1->addCode(gettext("password").":");
					$table1->endTableData();
					$table1->addTableData();
						$table1->addPasswordField("login[vis_password]", $login_cookie[3], $passstyle);
						$table1->addHiddenField("login[password]", $login_cookie[1]); //hash from cookie
						$table1->addHiddenField("login[use_cookie_password]", ($login_cookie[0]) ? 1:0);
						$table1->insertAction("ok", gettext("login"), "javascript: login();", "login_button");
					$table1->endTableData();
				$table1->endTableRow();
				$table1->addTableRow();
					$table1->addTableData(array("colspan"=>2, "align"=>"right"));
						if (!$GLOBALS["covide"]->license["has_radius"]) {
							$table1->insertCheckbox("login[save_password]", 1, ($login_cookie[0]) ? 1:0);
							$table1->addCode( gettext("remember password") );
							$table1->addSpace(2);
						} else {
							$table1->addHiddenField("login[save_password]", "");
						}
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
							$table1->addCode(gettext("use ssl encryption"));
							$table1->addSpace(2);
						} else {
							if ($GLOBALS["covide"]->sslmode == 1) {
								$table1->insertAction("logout", "ssl", "");
								$table1->addCode(" ".gettext("ssl encryption enabled"));
							}

							$table1->addTag("div", array("style" => "display: none;"));
							$table1->addCheckBox("use_ssl", 1, ($GLOBALS["covide"]->license["force_ssl"] == 1 && $GLOBALS["covide"]->sslmode != 3) ? 1:0);
							$table1->endTag("div");
						}
					$table1->endTableData();
				$table1->endTableRow();
				if (!$GLOBALS["covide"]->license["has_radius"]) {
					$table1->addTableRow();
						$table1->addTableData(array("colspan"=>2));
							$table1->addTag("div", array("id"=>"password_type_div", "style"=>"display: none"));
								$table1->addTag("br");
								$table1->addRadioField("login[remember_type]", gettext("let the browser handle the password"), "browser", ($login_cookie[3]) ? "covide":"browser", "pt_browser");
								$table1->addRadioField("login[remember_type]", gettext("let Covide handle the password"), "covide", ($login_cookie[3]) ? "covide":"browser", "pt_covide");
							$table1->endTag("div");
						$table1->endTableData();
					$table1->endTableRow();
				} else {
					$table1->addTableRow();
						$table1->addTableData(array("colspan" => 2));
							$table1->addTag("div", array("id" => "password_type_div", "style" => "display: none;"));
								$table1->addHiddenField("pt_browser", "");
								$table1->addHiddenField("pt_covide", "");
							$table1->endTag("div");
						$table1->endTableData();
					$table1->endTableRow();
				}
				/* progress bar */
				$table1->addTableRow(array("style" => "visibility: hidden;", "id" => "progressbar"));
					$table1->addTableData(array("colspan" => 2, "align" => "center"));
						$div = new Layout_output();
						$div->addTag("marquee", array(
							"id"           => "marquee_progressbar",
							"behavoir"     => "scroll",
							"style"        => "width: 200px; background-color: #f1f1f1; height: 10px; border: 1px solid #b0b2c6; margin-top: 10px;",
							"scrollamount" => 3,
							"direction"    => "right",
							"scrolldelay"  => 60
						));
						$div->addTag("img", array(
							"src" => "img/bar.png",
							"alt" => ""
						));
						//escape for w3c compat
						$div->addCode("</marquee>");

						$table1->start_javascript();
							$table1->addCode(sprintf("
								document.write('%s');
							", str_replace("/", "\\/", addslashes($div->generate_output()))));
						$table1->end_javascript();

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
	$output->load_javascript(self::include_dir."pop_tester.js");


	$output->start_javascript();
		$output->addCode("
			var crypt_challenge = '".$_SESSION["challenge"]."';

			if (!crypt_challenge) {
				alert('Your system does not accept sessions or cookies. Please contact your system administrator.');
			}
			if (document.getElementById('use_ssl')) {
				document.getElementById('use_ssl').onclick = function() {	checkstate();	}
				addLoadEvent(checkstate());
			}
		");
	$output->end_javascript();

	$output->layout_page_end();
	$output->exit_buffer();
?>
