<?php
	/* constructor / destructor */
	class Finance_init {
		/* constants */
		public $include_dir         = "classes/finance/non-oop";
		public $include_dir_oop     = "classes/finance";
		public $include_dir_factuur = "classes/finance/non-oop/factuur";
		public $include_dir_finance = "/non-oop/finance";

		public $include_path;
		public $buffer;
		public $pagesize = 50;
		public $data;

		public function __construct() {
			$this->include_path = ini_get("include_path");
			$p = preg_replace("/^(.*)\/classes\/finance\/non-oop\/.*$/si", "$1",
				$_SERVER["SCRIPT_FILENAME"]);

			$GLOBALS["autoloader_include_path"] = $p."/";

			if (file_exists(sprintf("%s/conf/offices.php", $p))
				&& file_exists(sprintf("%s/classes/covide/default.php", $p))) {
				ini_set("include_path", sprintf("%s:%s", $this->include_path, $p));
			} else {
				echo $p."<br />";
				die("path incorrect!");
				$GLOBALS["autoloader_include_path"] = "";
			}
			require_once( sprintf("%s/classes/finance/data.php", $p) );
			$this->data = new Finance_data(1);
		}
		public function __destruct() {
			ini_set("include_path", $this->include_path);
		}
	}
	$GLOBALS["finance"] = new Finance_init;

	/* require some common wrapper function */
	require("../inc_wrapper.php");

	/* create new Covide object */
	$skip_run_module = 1;
	require("../../../../index.php");

	$user_data = new User_data();
	$user_info = $user_data->getUserPermissionsById($_SESSION["user_id"]);
	if (!$user_info["xs_turnovermanage"])
		die("access denied");

	/* fake a superglobal */
	$licensie = $GLOBALS["covide"]->license;
	$licensie["begin_standen_finance"] =& $licensie["finance_start_date"];

	/* quick and dirty hack for register_globals=off */
  extract($_REQUEST,EXTR_SKIP);
  extract($_SESSION,EXTR_SKIP);
  extract($_SERVER,EXTR_SKIP);
  extract($_ENV,EXTR_SKIP);
  extract($_FILES,EXTR_SKIP);
?>
