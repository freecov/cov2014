//<![CDATA[
var map;
var gdir;
var geocoder = null;
var addressMarker;
var destinationaddress;

function initialize() {
	if (GBrowserIsCompatible()) {      
		map = new GMap2(document.getElementById("map_canvas"));
		gdir = new GDirections(map, document.getElementById("directions"));
		GEvent.addListener(gdir, "load", onGDirectionsLoad);
		GEvent.addListener(gdir, "error", handleErrors);

		setDirections(from_loc, to_loc, locale);
	}
}

function setDirections(fromAddress, toAddress, locale) {
	destinationaddress = toAddress;
	if (GBrowserIsCompatible()) {      
		map = new GMap2(document.getElementById("map_canvas"));
		gdir = new GDirections(map, document.getElementById("directions"));
		GEvent.addListener(gdir, "error", handleErrors);
		GEvent.addListener(gdir, "load", onGDirectionsLoad);
		gdir.load("from: " + fromAddress + " to: " + toAddress, { "locale": locale });
	}
}

function handleErrors(){
	if (gdir.getStatus().code == G_GEO_UNKNOWN_ADDRESS)
		alert(gettext("The supplied address could not be found."));
	else if (gdir.getStatus().code == G_GEO_SERVER_ERROR)
		alert(gettext("The google maps server could not be contacted, please try again."));

	else if (gdir.getStatus().code == G_GEO_MISSING_QUERY)
		alert("The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.\n Error code: " + gdir.getStatus().code);

	else if (gdir.getStatus().code == G_GEO_BAD_KEY)
		alert(gettext("The google map license key is incorrect."));

	else if (gdir.getStatus().code == G_GEO_BAD_REQUEST)
		alert(gettext("No parameters were given."));

	else alert(gettext("An unknown error occured"));

}

function onGDirectionsLoad(){ 
	if (opener) {
		el = opener;
	} else {
		el = parent;
	}
	if (el.document.getElementById("appointmentkilometers")) {
		var e = el.document.getElementById("appointmentkilometers");
		e.value = Math.round((gdir.getDistance().meters/1000));
	}
	if (el.document.getElementById("appointmentlocation")) {
		var e = el.document.getElementById("appointmentlocation");
		e.value = destinationaddress;
	}
}

setTimeout("initialize();", 200);
//]]>
