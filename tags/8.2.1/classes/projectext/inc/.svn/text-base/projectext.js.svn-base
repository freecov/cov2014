/**
 * Covide ProjectExt module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

if (document.getElementById('datafield_type')) {
	document.getElementById('datafield_type').onchange = function() {
		detectTypeChange();
	}
	addLoadEvent(detectTypeChange());
}
if (document.getElementById('projectextmeta_field')) {
	document.getElementById('projectextmeta_field').onchange = function() {
		detectMetaSearchType();
	}
	addLoadEvent(detectMetaSearchType());
}

function formatValue(argvalue, format) {
  var numOfDecimal = 0;
  if (format.indexOf(".") != -1) {
    numOfDecimal = format.substring(format.indexOf(".") + 1, format.length).length;
  }
  argvalue = formatDecimal(argvalue, true, numOfDecimal);

  argvalueBeforeDot = argvalue.substring(0, argvalue.indexOf("."));
  retValue = argvalue.substring(argvalue.indexOf("."), argvalue.length);

  strBeforeDot = format.substring(0, format.indexOf("."));

  for (var n = strBeforeDot.length - 1; n >= 0; n--) {
    oneformatchar = strBeforeDot.substring(n, n + 1);
    if (oneformatchar == "#") {
      if (argvalueBeforeDot.length > 0) {
        argvalueonechar = argvalueBeforeDot.substring(argvalueBeforeDot.length - 1, argvalueBeforeDot.length);
        retValue = argvalueonechar + retValue;
        argvalueBeforeDot = argvalueBeforeDot.substring(0, argvalueBeforeDot.length - 1);
      }
    }
    else {
      if (argvalueBeforeDot.length > 0 || n == 0)
        retValue = oneformatchar + retValue;
    }
  }

  return retValue;
}

function formatDecimal(argvalue, addzero, decimaln) {
  var numOfDecimal = (decimaln == null) ? 2 : decimaln;
  var number = 1;

  number = Math.pow(10, numOfDecimal);

  argvalue = Math.round(parseFloat(argvalue) * number) / number;
  // If you're using IE3.x, you will get error with the following line.
  // argvalue = argvalue.toString();
  // It works fine in IE4.
  argvalue = "" + argvalue;

  if (argvalue.indexOf(".") == 0)
    argvalue = "0" + argvalue;

  if (addzero == true) {
    if (argvalue.indexOf(".") == -1)
      argvalue = argvalue + ".";

    while ((argvalue.indexOf(".") + 1) > (argvalue.length - numOfDecimal))
      argvalue = argvalue + "0";
  }

  return argvalue;
}



function checkProjectFinanceVal(obj) {
	obj.value = obj.value.replace(/[^0-9\.\,]/, '');
	obj.value = obj.value.replace(/\,/, '.');
	obj.value = formatValue(obj.value, "############.##")
}

function detectTypeChange() {
	var el = document.getElementById('datafield_type');
	switch (el.value) {
		case '3':
		case '4':
			document.getElementById('selectcheck').style.display = '';
			document.getElementById('fileupload').style.display = 'none';
			break;
		case '5':
			document.getElementById('selectcheck').style.display = 'none';
			document.getElementById('fileupload').style.display = '';
			break;
		default:
			document.getElementById('selectcheck').style.display = 'none';
			document.getElementById('fileupload').style.display = 'none';
	}
}

function showProjectExtTable(metaid, metafield, allow_select) {
	if (!document.getElementById(metaid)) {
		metaid    = 'd'+metaid;
	}
	var metacurrent = document.getElementById(metaid).value;
	popup('?mod=projectext&action=extShowMetaTable&metaid='+metaid+'&metafield='+metafield+'&metacurrent='+metacurrent+'&allow_select='+allow_select, 'metatable', 0, 0, 1);
}
function setSearch(val) {
	document.getElementById('filter').value = val;
	document.getElementById('velden').submit();
}
function detectMetaSearchType() {
	var el = document.getElementById('projectextmeta_field');
	var curval = el.options[el.selectedIndex].text;

	document.getElementById('textsearch').style.display = 'none';
	document.getElementById('datesearch').style.display = 'none';
	document.getElementById('projectextmetatype').value = '';

	curval = curval.split(':');
	if (curval[0] == gettext("text")) {
		document.getElementById('textsearch').style.display = 'inline';
		document.getElementById('projectextmetatype').value = 'text';
	} else if (curval[0] == gettext("date")) {
		document.getElementById('datesearch').style.display = 'inline';
		document.getElementById('projectextmetatype').value = 'date';
	}
}

function setMetaTableValue(targetid, str) {
	var el = opener.document.getElementById(targetid);
	try {
		el.addItem(str, str);
	} catch(e) {
		addSelectBoxOption(el, str, str);
	}
	window.close();
}

function selectRel(address_id, relname) {
	el_address = document.getElementById('projectaddress_id');
	el_span    = document.getElementById('searchrel');

	el_span.innerHTML = relname;
	el_address.value = address_id;

	updateBcards();
}

function updateBcards() {
	var ret = loadXMLContent('?mod=address&action=bcardsxml&address_id=' + document.getElementById('projectaddress_id').value + '&current=' + document.getElementById('projectbcard').value);
	document.getElementById('project_bcard_layer').innerHTML = ret;
}

function mergeFile(file) {
	document.getElementById('file_name').value = file;
	document.getElementById('velden').submit();
}

