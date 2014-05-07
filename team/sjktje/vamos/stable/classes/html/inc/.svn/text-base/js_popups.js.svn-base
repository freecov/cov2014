function popup(url, controller) {
	/* usage:
		 url        - uri of the resource
		 controller - name of the controller object (alias for window)
		 width      - width of the window (in px)
		 height     - height of the window (in px)
		 hidenav    - boolean (1 = hide the navigation items)
	*/
	var w = screen.width - 50;
	var h = screen.height - 200;
	var nav = "yes";
	var modal = "no";

	if (arguments[2]) {
		w = arguments[2];
	}
	if (arguments[3]) {
		h = arguments[3];
	}
	if (arguments[4]==1) {
		nav = "no";
		modal = "yes";
	}

	var opts = '';

	opts = opts.concat("width="+ w +",height="+ h);
	opts = opts.concat(",directories="+nav+", location="+nav+",menubar="+nav+",status="+nav+",toolbar="+nav+",personalbar="+nav+",resizable=yes,scrollbars=yes");

	var controller_window = window.open(url, controller, opts);
}
