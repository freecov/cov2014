function infoLayer(str) {
	if (arguments[1]) {
		var position = arguments[1];
	} else {
		var position = 0;
	}
	/* position: */
	/*  0 = default top right */
	/*  1 = top center        */
	/*  2 = page bottom       */

	switch (position) {
		case 0:
			var el = document.getElementById("inforight");
			break;
		case 1:
			var el = document.getElementById("infotop");
			break;
		case 2:
			var el = document.getElementById("infobottom");
			break;
	}
	if (position == 0) {
		var msg = document.getElementById("infoLayerMsg").innerHTML;
		el.innerHTML = msg.concat("<br>", str);
	} else {
		el.innerHTML = str;
	}
	el.style.display = 'block';
}

function hideInfoLayer() {
	var obj = document.getElementById('inforight');
	obj.innerHTML = '';
	obj.style.display = 'none';
}

