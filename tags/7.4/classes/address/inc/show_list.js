window.onload = function() {
  document.getElementById("landSelect").disabled = true;
  document.getElementById("specified").onchange = checkIt;
} 
function checkIt() {
	if(document.getElementById("specified").value == 5) {
		document.getElementById("landSelect").disabled = false;
		document.getElementById("search").disabled = true;
	} else {
		document.getElementById("landSelect").disabled = true;
		document.getElementById("search").disabled = false;
	}
}