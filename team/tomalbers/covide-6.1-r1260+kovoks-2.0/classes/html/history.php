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
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Layout_history Extends Layout_output {

	const include_dir =  "classes/html/inc/";

	public function __construct() {
		$history      =& $_SESSION["history"];
		$id           = $_REQUEST["id"];
		$scope        = $_REQUEST["scope"];
		$descr        = $_REQUEST["descr"];
		$restorepoint = $_REQUEST["restorepoint"];
		$steps        = $_REQUEST["steps"];

		if ($restorepoint) {
			if ($restorepoint == -1) {
				/* erase restorepoints */
				unset($_SESSION["history"]);
				$data["request_type"] = "get";
				$data["request_data"] = array();
				header("Location: index.php?mod=desktop");
				exit();
			} else {
				if ($steps > 1) {
					/* lookup current restorepoint */
					/* get the current scope */
					$scope = $history[$restorepoint]["scope"];
					
					/* build current scope array */
					$local_scope = array();
					foreach ($history as $k=>$v) {
						if ($v["scope"] == $scope) {
							$v["restorepoint"] = $k;
							$local_scope[] = $v;
						}
					}
					end($local_scope);
					$local_scope = array_reverse($local_scope);
					
					$target_scope = $local_scope[(int)($steps-1)];
					if ($target_scope["restorepoint"]) {
						$restorepoint = $target_scope["restorepoint"];
					}
				}
				/* restorepoint */
				$data =& $history[$restorepoint];
				if (count($data["get"])>0) {
					/* request was of type get */
					$data["request_type"] = "get";
					$data["request_data"] =& $data["get"];
				} else {
					/* request was of type post */
					$data["request_type"] = "post";
					$data["request_data"] =& $data["post"];
				}
				$data["request_data"]["selected_restorepoint"] = $restorepoint;
			}

			$this->addTag("html");
			$this->addTag("body");
			$this->css(-1);
			$this->addTag("form", array(
				"id"     => "history_frm",
				"method" => $data["request_type"],
				"action" => "index.php"
			));
			foreach ($data["request_data"] as $k=>$v) {
				if (is_array($v)) {
					foreach ($v as $sk=>$sv) {
						$this->addHiddenField(sprintf("%s[%s]", $k, $sk), $sv);
					}
				} else {
					$this->addHiddenField($k, $v);
				}
			}
			$this->endTag("form");
			$this->start_javascript();
			$this->addCode("document.getElementById('history_frm').submit();");
			$this->end_javascript();
			$this->endTag("body");
			$this->endTag("html");

			$this->exit_buffer();

		} elseif ($id && $scope) {
			$history[$id]["scope"] = $scope;
			$history[$id]["descr"] = $descr;
		}

		$this->history_clean();
	}

	public function generate_save_state($exclude_vars="") {
		/* exclude prevent double inserting items into database */

		/* generate history identifier */
		$id = md5( rand()*mktime() );

		/* make data array */
		$data = array(
			"get"  => $_GET,
			"post" => $_POST,
			"time" => mktime(),
			"id"   => $id
		);

		if ($exclude_vars) {
			if (!is_array($exclude_vars)) {
				$exclude_vars = array($exclude_vars);
			}
			foreach ($exclude_vars as $exclude) {
				if ($data["get"][$exclude]) {
					unset($data["get"][$exclude]);
				}
				if ($data["post"][$exclude]) {
					unset($data["post"][$exclude]);
				}
			}
		}

		$history =& $_SESSION["history"];
		if (!is_array($history)) {
			$history = array();
		}
		if ($data != end($history)) {
			$history[$id]=$data;
		}
		$this->history_clean();

		/*
		$this->insertTag("div", $id, array(
			"id"    => "history_identifier",
			"style" => "display: none; position: absolute;"
		));
		*/
		$this->addTag("form", array(
			"id" => "historyfrm"
		));
		$this->addHiddenField("history_identifier", $id);
		$this->endTag("form");
		$this->load_javascript(self::include_dir."history.js");
		$this->load_javascript(self::include_dir."xmlhttp.js");
		$this->start_javascript();
		$this->addCode('js_history_generate()');
		$this->end_javascript();

		return $this->generate_output();

	}
	private function history_clean() {

			/* keep one history item per scope */
		$history =& $_SESSION["history"];

		$scope = array();
		if (!is_array($history)) {
			$history = array();
		}
		$history = array_reverse($history);
		foreach ($history as $k=>$v) {
			/* max 60 items per scope */
			if ($scope[$v["scope"]]>6) {
				/* if more than 6 items, cleanup */
				unset($history[$k]);
			} elseif ($history[$k]["time"]+(3600*6) < mktime()) {
				/* garbage collector */
				/* if item is older than 6 hours, cleanup */
				unset($history[$k]);
			} else {
				$scope[$v["scope"]]++;
			}
		}
		$history = array_reverse($history);
	}

	public function get_history_scope_list() {
		$history =& $_SESSION["history"];
		/* retrieve all scopes */
		$scopes = array();
		foreach ($history as $k=>$v) {
			$scopes[$v["scope"]][]=$k;
		}
		$this->start_javascript();
		$scope_counter = 0;
		foreach ($scopes as $k=>$scope) {
			$scope_counter++;
			$this->addCode("var ".gettext("scherm").$scope_counter." = '".$k."';\n");
		}
		$this->end_javascript();
		return $this->generate_output();
	}
	public function get_history_data() {
		$data = array(
			"0" => "- ".gettext("opgeslagen checkpoints")." -",
			"-1" => "- ".gettext("wis alle checkpoints")." -"
		);

		$history =& $_SESSION["history"];
		/* retrieve all scopes */
		$scopes = array();
		foreach ($history as $k=>$v) {
			/* if item creation time is less than 1 hour in the past, add it to the list */
			if ($v["time"]+3600 > mktime()) {
				$scopes[$v["scope"]][]=$k;
			}
		}
		$scope_counter = 0;
		foreach ($scopes as $k=>$scope) {
			$scope_counter++;
			foreach ($scope as $id) {
				$data[gettext("scherm").$scope_counter][$id] = sprintf("[%s] %s", date("H:i:s", $history[$id]["time"]), $history[$id]["descr"]);
			}
		}
		return $data;
	}
	public function generate_history_call() {
		$history =& $_SESSION["history"];

		$this->load_javascript(self::include_dir."history.js");
		$this->start_javascript();
		foreach ($history as $k=>$v) {
			if ($v["scope"]) {
				$this->addCode( sprintf("var %s = '%s';\n", $v["scope"], $v["id"]) );
			}
		}
		$this->end_javascript();
		return $this->generate_output();
	}

}
?>
