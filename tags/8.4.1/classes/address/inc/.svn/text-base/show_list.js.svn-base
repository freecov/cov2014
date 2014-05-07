function specifiedHandler() {
  document.getElementById("specified").onchange = function() { checkIt(); };
}
function checkIt() {
	if (document.getElementById("specified").value == 5) {
		document.getElementById("landSelect").disabled = false;
		document.getElementById("search").disabled = true;
	} else {
		document.getElementById("landSelect").value = 'XX'; //TODO: fix this!
		document.getElementById("landSelect").disabled = true;
		document.getElementById("search").disabled = false;
	}
}
addLoadEvent(checkIt());
addLoadEvent(specifiedHandler());