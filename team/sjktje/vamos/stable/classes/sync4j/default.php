<?php
Class Sync4j {
	/* constants */
	const include_dir = "classes/sync4j/inc/";
	
	/* methods */
	public function __construct() {
		switch ($_REQUEST["action"]) {
			case "sync_address" :
				$this->sync_address();
				break;
			case "sync_calendar" :
				$this->sync_calendar();
				break;
			case "sync_todo" :
				$this->sync_todo();
				break;
			case "sync_prepare" :
				$this->sync_prepare();
				break;
			case "sync_reset" :
				$this->sync_reset();
				break;
			/* end switch statement */
		}
	}

	public function sync_address() {
		require(self::include_dir."sync_address.php");
	}

	public function sync_calendar() {
		require(self::include_dir."sync_calendar.php");
	}

	public function sync_todo() {
		require(self::include_dir."sync_todo.php");
	}

	public function sync_prepare() {
		require(self::include_dir."sync_prepare.php");
	}

	public function sync_reset() {
		$sync_reset = new Sync4j_reset();
		$sync_reset->sync_reset();
	}
}
?>
