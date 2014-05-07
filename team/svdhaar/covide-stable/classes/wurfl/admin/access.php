<?php
	session_start();
	while (ob_end_clean()) { }
	ob_start();

	$ok = false;
	if (array_key_exists('user_id', $_SESSION)) {
		if ($_SESSION['user_id'] > 0) {
			$ok = true;
		}
	}
	if (!$ok) {
		die('access is denied, please login into covide');
	}
?>
