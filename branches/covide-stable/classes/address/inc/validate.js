function validate_email(frmEle) {

	if (!frmEle.value) {
		removeBorder(frmEle);
		return true;
	}

	var uri = "?mod=address&action=validateEmail&email=" + frmEle.value;
	content = loadXMLContent(uri);

	if (content == "true") {
		removeBorder(frmEle);
		return true;
	} else {
		eleTd = frmEle.parentNode;
		eleDiv = document.createElement('div');
		eleDiv.style.border = frmEle.style.border;
		eleDiv.style.display = "none";
		eleDiv.setAttribute("id", frmEle.id+"_copy");

		addBorder(frmEle, eleTd, eleDiv);
		return false;
	}
}

function addBorder(frmEle, eleTd, eleDiv) {
	if (!document.getElementById(frmEle.id+"_copy")) {
		eleTd.appendChild(eleDiv);
	}

	frmEle.style.border = "solid 1px #ff0000";
}

function removeBorder(frmEle) {
	hiddenDiv = document.getElementById(frmEle.id+"_copy");
	if (hiddenDiv) {
		frmEle.style.border = hiddenDiv.style.border;
	}
}
