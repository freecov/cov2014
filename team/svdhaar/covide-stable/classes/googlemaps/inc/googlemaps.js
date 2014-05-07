    //<![CDATA[

    function load() {
      if (GBrowserIsCompatible()) {
		// Initialize objects
		var map = new GMap2(document.getElementById("map"));
		var geocoder = new GClientGeocoder;
		// Coördinates
		geocoder.getLatLng(maplocation, 
			function(point) {
				var zoom = 15;
				if (!point) {
					// Default coords of The Netherlands
					var point = new GLatLng(51.9166666666667, 5.56666666666667);
					var nopoint = 1;
					zoom = 5;
				} 
				// Create map
		        map.setCenter(point, zoom);
				map.addControl(new GMapTypeControl());
				map.addControl(new GLargeMapControl());
				// Overlays
				if (!nopoint) {
					var gmark = new GMarker(point)
					map.addOverlay(gmark);
					//var html = '<span style="font-family: Verdana; font-size: 10px;">'+location+'</span>';
					//gmark.openInfoWindowHtml(html);
				}
		    }
		);
      }
    }
	setTimeout("load();", 200);
    //]]>