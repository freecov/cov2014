var inputId = 'blog_search';
					// This is the id on the input/textarea that you want to use as the query.

var outputId = 'searchresults';
 					// use this to have the results populate your own ID'd tag.
					// leave it blank and a div tag will automatically be added
					// with an id="liveSearchResults"

var processURI    = '/plugins/mvblog/common/livesearch.php';
					// this is the file that you request data from.

var emptyString   = '';
					// What to display in the results field when there's nothing
					// Leaving this null will cause the results field to be set to display: none

/*--------------------------------------
	Script Stuff
--------------------------------------*/

var t;

function liveReqInit() {

	inputElement  = document.getElementById(inputId);
	outputElement = document.getElementById(outputId);

	inputElement.onkeydown = function() {
		liveReqStart();
	}

	if(emptyString == '') {
		// set the result field to hidden, or to default string
		outputElement.style.display = "none";
	} else {
		outputElement.innerHTML = emptyString;
	}
}

addLoadEvent(liveReqInit);

function liveReqStart() {
	if (t) {
		window.clearTimeout(t);
	}
	t = window.setTimeout("liveReqDoReq()",400);
}

function liveReqDoReq() {
		ret = loadXMLContent(processURI + "?s=" + encodeURI(inputElement.value));
		liveReqProcessReqChange(ret);
}

function liveReqProcessReqChange(responseString) {
	var responseXML = (new DOMParser()).parseFromString(responseString, "text/xml");
	var content = new String("<ul>");
	var items = responseXML.getElementsByTagName("item");
	for (var i = 0; i < items.length; i++) {
		content += new String("<li><a href=\"index.php?action=view&amp;id=");
		content += new String(getElementTextNS("","articleID",items[i],0));
		content += new String("\">");
		content += new String(getElementTextNS("","articleTitle",items[i],0));
		content += new String("</a></li>");
	}
	content += new String("</ul>");
	outputElement.innerHTML = content;
	if(emptyString == '') {
		outputElement.style.display = "block";
	}
}

function getElementTextNS(prefix, local, parentElem, index) {
	var result = "";
	if (navigator.appName == "Microsoft Internet Explorer")
		var isIE = 1;
	else
		var isIE = 0;

	if (isIE) {
		// IE/Windows way of handling namespaces
		result = parentElem.getElementsByTagName(local)[index];
	} else {
		// the namespace versions of this method
		// (getElementsByTagNameNS()) operate
		// differently in Safari and Mozilla, but both
		// return value with just local name, provided
		// there aren't conflicts with non-namespace element
		// names
		result = parentElem.getElementsByTagName(local)[index];
	}
	if (result) {
		// get text, accounting for possible
		// whitespace (carriage return) text nodes
		if (result.childNodes.length > 1) {
			return result.childNodes[1].nodeValue;
		} else {
			return result.firstChild.nodeValue;
		}
	} else {
		return "n/a";
	}
}
