<html>
	<head>
	<title>initing editor</title>
	</head>
<body>
<form id="frm" method="post" action="<?= ($_REQUEST["mini"]) ? "editor_mini.php":"editor.php"; ?>">
	<input type="hidden" name="skin" value="blue-look">
	<input type="hidden" id="data" name="data" value="">
</form>
<script language="JavaScript1.2" type="text/javascript">
	window.onload = function() {
		document.getElementById('data').value = parent.document.getElementById('objEditor_data').value;
		document.getElementById('frm').submit();
	}
</script>
</body>
</html>