
/* custom onload handler */
/* the window.onload handler has only space for 1 (or a few?) handler(s) */
function addLoadEvent(func) {
	if (window.onload) {
  	var oldonload = window.onload;
  } else {
  	var oldonload = '';
  }
  if (typeof(func) == 'function') {
		if (typeof window.onload != 'function') {
			window.onload = func;
		} else {
			window.onload = function() {
				oldonload();
				if (func) {
					func();
				}
			}
		}
	}
}

/* css class focus selector */
function handleClassFocus(obj, state) {
	/* remove focus class from obj */
	obj.className = obj.className.replace(/ inputfocus/gi,'');
	if (state == 1) {
		obj.className = obj.className.concat(' inputfocus');
	}
}

/* function to scan keyCodes */
function scanKeyCode(e) {
	var keycode;
		if (window.event) keycode = window.event.keyCode;
		else if (e) keycode = e.which;
		if(keycode == 13){
			return false;
	}
}

/* function to scan for problematic characters */
function scanSpecialCharacters(obj) {
	obj.value = obj.value.replace(/"/g, "''");
}

/* intercept double clicks */
window.ondblclick = function() {
	return false;
}

function setBgColor(obj, enabled) {
	if (enabled) {
		obj.style.backgroundColor = '#e7e7e7';
	} else {
		obj.style.backgroundColor = '#f4f4f4';
	}
}

function showPerformanceInfo() {
	document.getElementById('performance_info_trigger').style.display = 'none';
	document.getElementById('performance_info').style.display = 'inline';
}

function gettext(str) {
	var ret = loadXMLContent('?mod=user&action=translate&str=' + escape(str) );
	if (!ret) {
		return str;
	} else {
		return ret;
	}
}
function updatePagesize(num, cmd) {
	var ret = loadXMLContent('?mod=user&action=updatePagesize&pagesize=' + num);
	if (ret.match(/updated to/)) {
		eval(cmd);
	}
}

function loadHTML() {
	if (document.getElementById('html_page_content')) {
		document.getElementById('html_page_content').style.visibility = 'visible';
	}
}



