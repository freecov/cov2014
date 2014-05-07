<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>sync clients download</title>
	<style type="text/css">
		body, td, th, a {
			color: black;
			font-family: arial, serif;
			font-size: 11px;
			text-align: left;
		}
		img {
			border: 0;
		}
		a {
			text-decoration: none;
		}
		th {
			font-weight: bold;
		}
		td.ok {
			color: #00a229;
		}
	</style>
	<script>window.resizeTo(690, 700);</script>
</head>
<body>
<?php
	@session_start();
	if ($_SESSION["user_id"]) {
?>
	<script type="text/javascript">
		top.location.href = 'http://download.forge.objectweb.org/sync4j/';
	</script>
<?php } else { ?>
	not logged in
<?php } ?>
</body>
</html>
