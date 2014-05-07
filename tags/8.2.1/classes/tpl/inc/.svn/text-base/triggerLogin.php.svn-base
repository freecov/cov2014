<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}

	$output = new Layout_output();

	/* generate an challenge string on the server for this request */
	$_SESSION["challenge"] = crc32(session_id().rand().mktime());

	$output->start_javascript();
		$output->addCode("
			var crypt_challenge = '".$_SESSION["challenge"]."';

			if (!crypt_challenge) {
				alert('Your system does not accept sessions or cookies. Please contact your system administrator.');
			}
		");
	$output->end_javascript();
	$output->load_javascript("classes/user/inc/md5.js", true);

	if (!$compact) {
		if (!$use_feedback) {
			if ($this->custom_status) {
				$this->getPageData($this->custom_status);
			} else {
				if ($page) {
					$output->addCode(gettext("You have not sufficient permissions to visit this page."));
					$output->addTag("br");
				}
				$output->addCode(gettext("Please login with a username / password combination that have access to this page and try again."));
			}
		} else {
			$s = $this->cms->getCmsSettings($this->siteroot);
			if (!$s["custom_feedback"])
				$s["custom_feedback"] = $this->cms_license["feedback"];

			if ($s["custom_feedback"])
				$this->getPageData($s["custom_feedback"]);
			else
				$output->addCode(gettext("To give feedback on this page, please login with your account."));
		}
		$output->addTag("br");
		$output->addTag("br");
	}

	/* create the uri */
	$uri = sprintf("%s%s%s%s",
		$this->protocol, $_SERVER["HTTP_HOST"], ($this->page_less_rewrites) ? "/":"/page/", $this->checkAlias($page));

	$txt = gettext("Are you sure you want to logout? All data that is not saved will be lost! Continue?");
	$output->insertTag("span", $txt, array(
		"style" => "display: none",
		"id"    => "logout_confirm"
	));

	if ($_SESSION["user_id"] || $_SESSION["visitor_id"]) {
		if ($this->cms_license["custom_loginprofile"]) {
			header("Location: /mode/loginprofile");
			exit();
		}
		$user_data = new User_data();
		if ($_SESSION["user_id"])
			$output->insertTag("b", gettext("You are already logged in as Covide user").": ".$user_data->getUserNameById($_SESSION["user_id"]));
		else
			$output->insertTag("b", gettext("You are already logged in as visitor").": ".$this->cms->getUserNameById($_SESSION["visitor_id"]));
		$output->addTag("br");
		$output->addTag("br");
		$output->addCode(gettext("Please logout and login again with a user with sufficient permissions to access this page."));
		$output->addTag("br");
		$output->addTag("br");
		$output->addCode(gettext("You can click here to logout").": ");
		$output->insertAction("logout", gettext("logout"), sprintf(
			"javascript: cmsLogout('%s', '%s', '%s');",
			urlencode($uri),
			addslashes(gettext("Do you also want to logout from the Covide office / CMS backend?")),
			($this->manage_hostname == $this->http_host) ? "":$this->manage_hostname
		));
	} else {
		if (!$compact) {
			$output->insertAction("state_special", "", "");
			$output->addSpace();
			$output->addCode(gettext("Login below here with your account."));
			$output->addTag("br");
			$output->addTag("br");
		}

		$output->addTag("form", array(
			"id" => "loginfrm",
			"action" => "site.php",
			"method" => "post"
		));
		if (!$page)
			$uri = "/mode/loginprofile";

		$output->addHiddenField("uri", $uri);
		$output->addHiddenField("mode", "cmslogin");
		if ($notable) {
			$output->addTag("div", array("id" => "loginbox"));
				$output->addTag("fieldset");
					$output->insertTag("legend", $legend);
					if ($this->login_text_username) {
						$output->insertTag("label", $this->login_text_username);
					} else {
						$output->insertTag("label", gettext("username"));
					}
					$output->addTextField("username", "", array(
						"style" => ($compact) ? "width: 100px":"width: 180px;"
					));
					if ($this->login_text_password) {
						$output->insertTag("label", $this->login_text_password);
					} else {
						$output->insertTag("label", gettext("password"));
					}
					$output->addHiddenField("password", "");
					$output->addPasswordField("vis_password", "", array(
						"style" => ($compact) ? "width: 100px":"width: 180px;"
					));
					$output->addTag("input", array("type" => "button", "onclick" => "login();", "value" => gettext("login")));
				$output->endTag("fieldset");
				if (!$compact) {
					$output->addTag("br");

					$clicense = $this->cms_license;
					if ($clicense["cms_user_register"]) {
						/* new account */
						$output->insertAction("state_public", gettext("register"), array(
								"href" => "javascript: cmsVisitorRegistration('".$uri."', '".$this->siteroot."');"));
							$output->addSpace();
							$output->insertTag("a", gettext("If you don't have an account, you can sign up here."), array(
								"href" => "javascript: cmsVisitorRegistration('".$uri."', '".$this->siteroot."');"));
							$output->addTag("br");
					}

					/* password recover */
					$output->insertAction("help", gettext("password lost"), array(
						"href" => "javascript: cmsVisitorPasswordRecover('".$uri."', '".$this->siteroot."');"));
					$output->addSpace();
					$output->insertTag("a", gettext("If you forgot your password, you can recover it here."), array(
						"href" => "javascript: cmsVisitorPasswordRecover('".$uri."', '".$this->siteroot."');"));
				} else {
					$output->addTag("div", array("style" => "text-align: right;"));
					$clicense = $this->cms_license;
					if ($clicense["cms_user_register"]) {
						$output->insertTag("a", gettext("register"), array(
							"href" => "javascript: cmsVisitorRegistration('".$uri."', '".$this->siteroot."');"));
						$output->addSpace(2);
					}
					$output->insertTag("a", gettext("password lost")."?", array(
						"href" => "javascript: cmsVisitorPasswordRecover('".$uri."', '".$this->siteroot."');"));
					$output->endTag("div");
				}
			$output->endTag("div");
		} else {
			$tbl = new Layout_table(array(
				"cellspacing" => 1,
				"cellpadding" => 1
			));
			$tbl->addTableRow();
				$tbl->addTableData();
					if ($this->login_text_username)
						$tbl->addCode($this->login_text_username.": ");
					else
						$tbl->addCode(gettext("username").": ");
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->addTextField("username", "", array(
						"style" => ($compact) ? "width: 100px":"width: 180px;"
					));
				$tbl->endTableData();
			$tbl->endTableRow();
			$tbl->addTableRow();
				$tbl->addTableData();
					if ($this->login_text_password)
						$tbl->addCode($this->login_text_password.": ");
					else
						$tbl->addCode(gettext("password").": ");
				$tbl->endTableData();
				$tbl->addTableData();
					$tbl->addHiddenField("password", "");
					$tbl->addPasswordField("vis_password", "", array(
						"style" => ($compact) ? "width: 100px":"width: 180px;"
					));
					if (!$compact) {
						$tbl->addSpace();
						$tbl->insertTag("a", gettext("login")." &gt;",
							array("href" =>  "javascript: login();"
						));
					}
				$tbl->endTableData();
			$tbl->endTableRow();
			if ($compact) {
				$tbl->addTableRow();
					$tbl->addTableData(array("colspan" => 2, "style" => "text-align: right"));
						$tbl->insertTag("a", gettext("login")." &gt;",
							array("href" =>  "javascript: login();"
						));
					$tbl->endTableData();
				$tbl->endTableRow();
			}
			$tbl->addTableRow();
				$tbl->addTableData(array("colspan" => 5));
					if (!$compact) {
						$tbl->addTag("br");

						$clicense = $this->cms_license;
						if ($clicense["cms_user_register"]) {
							/* new account */
							$tbl->insertAction("state_public", gettext("register"), array(
								"href" => "javascript: cmsVisitorRegistration('".$uri."', '".$this->siteroot."');"));
							$tbl->addSpace();
							$tbl->insertTag("a", gettext("If you don't have an account, you can sign up here."), array(
								"href" => "javascript: cmsVisitorRegistration('".$uri."', '".$this->siteroot."');"));
							$tbl->addTag("br");
						}

						/* password recover */
						$tbl->insertAction("help", gettext("password lost"), array(
							"href" => "javascript: cmsVisitorPasswordRecover('".$uri."', '".$this->siteroot."');"));
						$tbl->addSpace();
						$tbl->insertTag("a", gettext("If you forgot your password, you can recover it here."), array(
							"href" => "javascript: cmsVisitorPasswordRecover('".$uri."', '".$this->siteroot."');"));
					} else {
						$tbl->addTag("div", array("style" => "text-align: right;"));
						$clicense = $this->cms_license;
						if ($clicense["cms_user_register"]) {
							$tbl->insertTag("a", gettext("register"), array(
								"href" => "javascript: cmsVisitorRegistration('".$uri."', '".$this->siteroot."');"));
							$tbl->addSpace(2);
						}
						$tbl->insertTag("a", gettext("password lost")."?", array(
							"href" => "javascript: cmsVisitorPasswordRecover('".$uri."', '".$this->siteroot."');"));
						$tbl->endTag("div");
					}
				$tbl->endTableData();
			$tbl->endTableRow();


			$tbl->endTable();

			$output->addCode($tbl->generate_output());
		}
		$output->endTag("form");
	}
?>
