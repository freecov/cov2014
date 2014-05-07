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
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
	if (!class_exists("User_data")) {
		die("no class found");
	}
	//get the permissions of the logged in user
	$userpermissions = $this->getUserPermissionsById($_SESSION["user_id"]);
	$userinfo = $userdata["user"];

	//is the user usermanager or admin ?
	if ($userpermissions["xs_usermanage"] == 1 || $userpermissions["xs_limitusermanage"] == 1) {
		//pop3 support and greater than 2 weeks is like suicide with php
		if ($userinfo["mail_imap"]==0 && $userinfo["mail_server_deltime"]>mktime(0,0,1,1,15,1970)) {
			$userinfo["mail_server_deltime"] = mktime(0,0,1,1,15,1970);
		}

		$adresaccmanage_ar = $userinfo["addressaccountmanage"];
		$adressncmanage_ar = $userinfo["addresssyncmanage"];
		$agendasel_ar      = $userinfo["calendarselection"];

		if ($userinfo["password"]) {
			if ($userinfo["password"]==$userinfo["password1"]) {
				$velden[] = "password";
				$pw = md5($userinfo["password"]);
				$waarden[] = sprintf("'%s'", $pw);
			} else {
				$error = 1;
			}
		}
		if (!$userinfo["pers_nr"]) { $userinfo["pers_nr"] = "NULL"; }
		if ($userpermissions["xs_usermanage"] == 1) {
			$velden[] = "xs_classmanage";         $waarden[] = sprintf("%d", $userinfo["xs_classmanage"]);
			$velden[] = "xs_usermanage";          $waarden[] = sprintf("%d", $userinfo["xs_usermanage"]);
			$velden[] = "xs_addressmanage";       $waarden[] = sprintf("%d", $userinfo["xs_addressmanage"]);
			$velden[] = "xs_turnovermanage";      $waarden[] = sprintf("%d", $userinfo["xs_turnovermanage"]);
			$velden[] = "xs_salariscommanage";    $waarden[] = sprintf("%d", $userinfo["xs_salariscommanage"]);
			$velden[] = "xs_projectmanage";       $waarden[] = sprintf("%d", $userinfo["xs_projectmanage"]);
			$velden[] = "xs_forummanage";         $waarden[] = sprintf("%d", $userinfo["xs_forummanage"]);
			$velden[] = "xs_faqmanage";           $waarden[] = sprintf("%d", $userinfo["xs_faqmanage"]);
			$velden[] = "xs_pollmanage";          $waarden[] = sprintf("%d", $userinfo["xs_pollmanage"]);
			$velden[] = "xs_issuemanage";         $waarden[] = sprintf("%d", $userinfo["xs_issuemanage"]);
			$velden[] = "xs_notemanage";          $waarden[] = sprintf("%d", $userinfo["xs_notemanage"]);
			$velden[] = "xs_todomanage";          $waarden[] = sprintf("%d", $userinfo["xs_todomanage"]);
			$velden[] = "xs_salesmanage";         $waarden[] = sprintf("%d", $userinfo["xs_salesmanage"]);
			$velden[] = "xs_filemanage";          $waarden[] = sprintf("%d", $userinfo["xs_filemanage"]);
			$velden[] = "xs_limitusermanage";     $waarden[] = sprintf("%d", $userinfo["xs_limitusermanage"]);
			$velden[] = "xs_newslettermanage";    $waarden[] = sprintf("%d", $userinfo["xs_newslettermanage"]);
			$velden[] = "xs_relationmanage";      $waarden[] = sprintf("%d", $userinfo["xs_relationmanage"]);
			$velden[] = "xs_companyinfomanage";   $waarden[] = sprintf("%d", $userinfo["xs_companyinfomanage"]);
			$velden[] = "xs_hrmmanage";           $waarden[] = sprintf("%d", $userinfo["xs_hrmmanage"]);
			$velden[] = "xs_arbo";                $waarden[] = sprintf("%d", $userinfo["xs_arbo"]);
			$velden[] = "xs_funambol";            $waarden[] = sprintf("%d", $userinfo["xs_funambol"]);
			$velden[] = "xs_funambol_expunge";    $waarden[] = sprintf("%d", $userinfo["xs_funambol_expunge"]);
			$velden[] = "xs_hypo";                $waarden[] = sprintf("%d", $userinfo["xs_hypo"]);
			$velden[] = "xs_campaignmanage";      $waarden[] = sprintf("%d", $userinfo["xs_campaignmanage"]);
			$velden[] = "authmethod";             $waarden[] = sprintf("'%s'", $userinfo["authmethod"]);
		}
		if ($GLOBALS["covide"]->license["has_cms"]) {
			$velden[] = "xs_cms_level";           $waarden[] = sprintf("%d", $userinfo["xs_cms_level"]);
		}

		$velden[] = "pers_nr";                       $waarden[] = sprintf("%d",   $userinfo["pers_nr"]);
		$velden[] = "is_active";                     $waarden[] = sprintf("%d",   $userinfo["is_active"]);
		$velden[] = "address_id";                    $waarden[] = sprintf("%d",   $userinfo["address_id"]);
		$velden[] = "employer_id";                   $waarden[] = sprintf("%d",   $userinfo["employer_id"]);
		$velden[] = "change_theme";                  $waarden[] = sprintf("%d",   $userinfo["change_theme"]);
		$velden[] = "style";                         $waarden[] = sprintf("%d",   $userinfo["style"]);
		$velden[] = "mail_imap";                     $waarden[] = sprintf("'%s'", $userinfo["mail_imap"]);
		$velden[] = "mail_server";                   $waarden[] = sprintf("'%s'", $userinfo["mail_server"]);
		$velden[] = "mail_email";                    $waarden[] = sprintf("'%s'", $userinfo["mail_email"]);
		$velden[] = "mail_email1";                   $waarden[] = sprintf("'%s'", $userinfo["mail_email1"]);
		$velden[] = "mail_default_bcc";              $waarden[] = sprintf("'%s'", $userinfo["mail_default_bcc"]);
		$velden[] = "mail_user_id";                  $waarden[] = sprintf("'%s'", $userinfo["mail_user_id"]);
		$velden[] = "mail_password";                 $waarden[] = sprintf("'%s'", $userinfo["mail_password"]);
		$velden[] = "mail_html";                     $waarden[] = sprintf("%d",   $userinfo["mail_html"]);
		$velden[] = "mail_view_textmail_only";       $waarden[] = sprintf("%d",   $userinfo["mail_view_textmail_only"]);
		$velden[] = "language";                      $waarden[] = sprintf("'%s'", $userinfo["language"]);
		$velden[] = "mail_deltime";                  $waarden[] = sprintf("%d",   $userinfo["mail_deltime"]);
		$velden[] = "mail_showcount";                $waarden[] = sprintf("%d",   $userinfo["mail_showcount"]);
		$velden[] = "addressaccountmanage";          $waarden[] = sprintf("'%s'", $adresaccmanage_ar);
		$velden[] = "addresssyncmanage";             $waarden[] = sprintf("'%s'", $adressncmanage_ar);
		$velden[] = "addressmode";                   $waarden[] = sprintf("%d",   $userinfo["addressmode"]);
		$velden[] = "calendarselection";             $waarden[] = sprintf("'%s'", $agendasel_ar);
		$velden[] = "calendarmode";                  $waarden[] = sprintf("%d",   $userinfo["calendarmode"]);
		$velden[] = "calendarinterval";              $waarden[] = sprintf("%d",   $userinfo["calendarinterval"]);
		$velden[] = "hour_format";                   $waarden[] = sprintf("%d",   $userinfo["hour_format"]);
		$velden[] = "showhelp";                      $waarden[] = sprintf("%d",   $userinfo["showhelp"]);
		$velden[] = "showpopup";                     $waarden[] = sprintf("%d",   $userinfo["showpopup"]);
		$velden[] = "showvoip";                      $waarden[] = sprintf("%d",   $userinfo["showvoip"]);
		$velden[] = "voip_device";                   $waarden[] = sprintf("'%s'", $userinfo["voip_device"]);
		$velden[] = "voip_number";                   $waarden[] = sprintf("'%s'", $userinfo["voip_number"]);
		$velden[] = "mail_server_deltime";           $waarden[] = sprintf("%d",   $userinfo["mail_server_deltime"]);
		$velden[] = "automatic_logout";              $waarden[] = sprintf("%d",   $userinfo["automatic_logout"]);
		$velden[] = "dayquote";                      $waarden[] = sprintf("%d",   $userinfo["dayquote"]);
		$velden[] = "rssnews";                       $waarden[] = sprintf("%d",   $userinfo["rssnews"]);
		$velden[] = "infowin_altmethod";             $waarden[] = sprintf("%d",   $userinfo["infowin_altmethod"]);
		$velden[] = "alternative_note_view_desktop"; $waarden[] = sprintf("%d",   $userinfo["alternative_note_view_desktop"]);
		$velden[] = "htmleditor";                    $waarden[] = sprintf("%d",   $userinfo["htmleditor"]);
		$velden[] = "mail_num_items";                $waarden[] = sprintf("%d",   $userinfo["mail_num_items"]);
		$velden[] = "mail_shortview";                $waarden[] = sprintf("%d",   $userinfo["mail_shortview"]);
		$velden[] = "mail_default_private";          $waarden[] = sprintf("%d",   $userinfo["mail_default_private"]);
		$velden[] = "google_username";               $waarden[] = sprintf("'%s'", $userinfo["google_username"]);
		$velden[] = "google_password";               $waarden[] = sprintf("'%s'", $userinfo["google_password"]);
		$velden[] = "font";                          $waarden[] = sprintf("'%s'", $userinfo["font"]);
		$velden[] = "fontsize";                      $waarden[] = sprintf("%d",   $userinfo["fontsize"]);
		$velden[] = "mail_default_template";         $waarden[] = sprintf("%d",   $userinfo["mail_default_template"]);

		if ($userinfo["id"]==0) {
			// new user
			if ($userinfo["pers_nr"]) {
				$result = sql_query("SELECT id FROM users WHERE pers_nr=".(int)$userinfo["pers_nr"]);
				if (sql_num_rows($result)>0) {
					$error=2;
				}
			} else {
				$error=3;
			}
			// Check if password is at least 6 characters
			if (strlen($userinfo["password"]) < 6) {
				$error=4;
			}
			// built query
			$query = "INSERT INTO users ";
			$velden[] = "username";
			$waarden[] = sprintf("'%s'", $userinfo["username"]);
			$query  .= "(".implode(",", $velden).") VALUES (".implode(",", $waarden).")";
		} else {
			// editing user
			// verify password
			$query = "UPDATE users SET ";
			if ($userinfo["password"]!="") {
				if (strlen($userinfo["password"]) < 6) {
					$error = 4;
				} else {
					//if i am the user and i have arbo permissions
					if ($userinfo["id"]==$_SESSION["user_id"] && $userinfo["xs_arbo"]==1) {
						$velden[] = "xs_arbo_validated"; $waarden[] = 1;
					}
				}
			}
			foreach ($velden as $k=>$v) {
				$query .= $v."=".$waarden[$k].",";
			}
			$query = substr($query, 0, strlen($query)-1);
			$query .= sprintf(" WHERE id=%d", $userinfo["id"]);
		}
	} else {
		// no admin permissions.
		if ($userinfo["id"]==$_SESSION["user_id"]) {
			if (is_array($userinfo["calendarselection"])) {
				$agendasel_ar = implode(",", $userinfo["calendarselection"]);
			} else {
				$agendasel_ar = $userinfo["calendarselection"];
			}
			//allowed
			$query = "UPDATE users SET ";
			$query.= sprintf("mail_html=%d", $userinfo["mail_html"]);
			$query.= sprintf(", mail_view_textmail_only=%d", $userinfo["mail_view_textmail_only"]);
			$query.= sprintf(", mail_deltime=%d", $userinfo["mail_deltime"]);
			$query.= sprintf(", mail_showcount=%d", $userinfo["mail_showcount"]);
			$query.= sprintf(", automatic_logout=%d", $userinfo["automatic_logout"]);
			$query.= sprintf(", showhelp=%d", $userinfo["showhelp"]);
			$query.= sprintf(", showvoip=%d", $userinfo["showvoip"]);
			$query.= sprintf(", voip_device='%s'", $userinfo["voip_device"]);
			$query.= sprintf(", showpopup=%d", $userinfo["showpopup"]);
			$query.= sprintf(", dayquote=%d", $userinfo["dayquote"]);
			$query.= sprintf(", infowin_altmethod=%d", $userinfo["infowin_altmethod"]);
			$query.= sprintf(", htmleditor=%d", $userinfo["htmleditor"]);
			$query.= sprintf(", mail_num_items=%d", $userinfo["mail_num_items"]);
			$query.= sprintf(", mail_shortview=%d", $userinfo["mail_shortview"]);
			$query.= sprintf(", calendarselection='%s'", $agendasel_ar);
			$query.= sprintf(", calendarmode=%d", $userinfo["calendarmode"]);
			$query.= sprintf(", rssnews=%d", $userinfo["rssnews"]);
			$query.= sprintf(", hour_format=%d", $userinfo["hour_format"]);
			$query.= sprintf(", google_username='%s'", $userinfo["google_username"]);
			$query.= sprintf(", google_password='%s'", $userinfo["google_password"]);
			$query.= sprintf(", style = %d", $userinfo["style"]);
			$query.= sprintf(", font = '%s'", $userinfo["font"]);
			$query.= sprintf(", fontsize = %d", $userinfo["fontsize"]);

			// do we have to change the password
			if ($userinfo["password"]!= "") {
				// Check if password is at least 6 characters
				if (strlen($userinfo["password"]) < 6) {
					$error=4;
				}
				// are both given passwords the same ?
				if ($userinfo["password"]==$userinfo["password1"]) {
					$pw = md5($userinfo["password"]);
				} else {
					$error = 1;
				}
				$query .= ", password='$pw'";
			}
			if ($userinfo["language"]) {
				$query .= sprintf(", language='%s'", $userinfo["language"]);
			}
			if ($userinfo["style"] || $userinfo["style"]=="0") {
				$query .= sprintf(", style=%d", $userinfo["style"]);
			}
			$query .= sprintf(" WHERE id=%d", $_SESSION["user_id"]);
		} else {
			//not allowed
			die(gettext("no permisions to alter this user."));
		}
	}
	if (!$error) {
		// fire the query into the database
		if ($query) {

			//get previous state
			$q = "select * from users where id = ".(int)$userinfo["id"];
			$userres = sql_query($q);
			$userdb = @sql_fetch_array($userres);

			$changes = array();

			//if xs_usermanage
			if ($userpermissions["xs_usermanage"]) {
				//monitor all xs_ range permissions
				foreach ($userinfo as $k=>$v) {
					if (preg_match("/^xs_/si",$k)) {
						//check for diff
						if ((int)$v != (int)$userdb[$k]) {
							$changes[]= "[".$k."] ".(int)$userdb[$k]." => ".(int)$v;
						}
					}
				}
			}
			$result = sql_query($query);

			/* update theme ? */
			if ($_SESSION["user_id"] == $userinfo["id"]) {
				$_SESSION["theme"]        =  $userinfo["style"];
				$GLOBALS["covide"]->theme =  $userinfo["style"];
			}
			if ($_SESSION["pagesize"] != $GLOBALS["covide"]->pagesize)
				$_SESSION["pagesize"] = $GLOBALS["covide"]->pagesize;
		}

		// generate files needed for cronjobs
		if ($GLOBALS["covide"]->license["mail_shell"]) {
			// check and/or update userlist file in /var/covide_files
			$fsbasepath = dirname($GLOBALS["covide"]->filesyspath)."/../";
			$fsbasepath = dirname($fsbasepath);
			$code = $GLOBALS["covide"]->license["code"];
			$usersfile = $fsbasepath."/".$code."-users.txt";
			$urlfile   = $fsbasepath."/".$code."-url.txt";
			$codefile  = $fsbasepath."/codes.txt";
			$host = $GLOBALS["covide"]->webroot;
			$url = $host."index.php?mod=email&action=retrieve&user_id=";
			$sql = "SELECT id FROM users WHERE is_active=1 AND username NOT IN ('archiefgebruiker', 'administrator')";
			$res = sql_query($sql);
			$idlist = "";
			while ($row = sql_fetch_assoc($res))
				$idlist .= $row["id"]."\n";

			file_put_contents($usersfile, $idlist);

			if (!file_exists($urlfile))
				file_put_contents($urlfile, $url);

			if (file_exists($codefile)) {
				$codes = file($codefile);
				$codes[] = $code;
				$codes = array_unique($codes);
				foreach ($codes as $k=>$v) {
					if (!trim($v))
						unset($codes[$k]);
				}
			} else {
				$codes = array($code);
			}
			file_put_contents($codefile, implode("\n", $codes));
		}
		$useroutput = new User_output();
		$useroutput->usersaved();
		exit;
	} else {
		header("Location: index.php?mod=user&action=useredit&error=$error");
	}
?>
