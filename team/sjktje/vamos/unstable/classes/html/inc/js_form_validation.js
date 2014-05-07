// Javascript form validation script. This script is heavily based on Stephen
// Poley's script which can be found at http://www.xs4all.nl/~sbpoley/webmatters/formval.html

var nbsp = 160;		// non-breaking space char
var node_text = 3;	// DOM text node-type
var emptyString = /^\s*$/ ;
var global_valfield;	// retain valfield for timer thread
var requiredmsg = '* '+gettext("required"); // This is a string we use often.

// --------------------------------------------
//                  trim
// Trim leading/trailing whitespace off string
// --------------------------------------------

function trim(str) {
  return str.replace(/^\s+|\s+$/g, '');
}


// --------------------------------------------
//                  setfocus
// Delayed focus setting to get around IE bug
// --------------------------------------------

function setFocusDelayed()
{
  global_valfield.focus();
}

function setfocus(valfield)
{
  // save valfield in global variable so value retained when routine exits
  global_valfield = valfield;
  setTimeout( 'setFocusDelayed()', 100 );
}


// --------------------------------------------
//                  msg
// Display warn/error message in HTML element.
// commonCheck routine must have previously been called
// --------------------------------------------

function msg(fld,     // id of element to display message in
             msgtype, // class to give element ("warn" or "error")
             message) // string to display
{
  // setting an empty string can give problems if later set to a 
  // non-empty string, so ensure a space present. (For Mozilla and Opera one could 
  // simply use a space, but IE demands something more, like a non-breaking space.)
  var dispmessage;
  if (emptyString.test(message)) 
    dispmessage = String.fromCharCode(nbsp);    
  else  
    dispmessage = message;

  var elem = document.getElementById(fld);
  elem.firstChild.nodeValue = dispmessage;  
  
  elem.className = msgtype;   // set the CSS class to adjust appearance of message
}

// --------------------------------------------
//            commonCheck
// Common code for all validation routines to:
// (a) check for older / less-equipped browsers
// (b) check if empty fields are required
// Returns true (validation passed), 
//         false (validation failed) or 
//         proceed (don't know yet)
// --------------------------------------------

var proceed = 2;  

function commonCheck    (valfield,   // element to be validated
                         infofield,  // id of element to receive info/error msg
                         required)   // true if required
{
  if (!document.getElementById) 
    return true;  // not available on this browser - leave validation to the server
  var elem = document.getElementById(infofield);
  if (!elem.firstChild) return true;  // not available on this browser 
  if (elem.firstChild.nodeType != node_text) return true;  // infofield is wrong type of node  

  if (emptyString.test(valfield.value)) {
    if (required) {
      msg(infofield, 'error', requiredmsg);  
      setfocus(valfield);
      return false;
    } else {
      msg(infofield, 'warn', '');   // OK
      return true;  
    }
  }
  msg(infofield, "warn", ""); 
  return proceed;
}

// --------------------------------------------
//            validatePresent
// Validate if something has been entered
// Returns true if so 
// --------------------------------------------

function validatePresent(valfield,   // element to be validated
                         infofield ) // id of element to receive info/error msg
{
  var stat = commonCheck (valfield, infofield, true);
  if (stat != proceed) return stat;

  msg (infofield, "warn", "");  
  return true;
}

// --------------------------------------------
//               validateEmail
// Validate if e-mail address
// Returns true if so (and also if could not be executed because of old browser)
// --------------------------------------------

function validateEmail  (valfield,   // element to be validated
                         infofield,  // id of element to receive info/error msg
                         required)   // true if required
{
  var stat = commonCheck (valfield, infofield, required);
  if (stat != proceed) return stat;

  var tfld = trim(valfield.value);  // value of field with whitespace trimmed off
  var email = /^[^@]+@[^@.]+\.[^@]*\w\w$/  ;
  if (!email.test(tfld)) {
    msg(infofield, 'error', '* '+gettext("not a valid e-mail address"));
    setfocus(valfield);
    return false;
  }

  return true;
}


// --------------------------------------------
//            validateTelnr
// Validate telephone number
// Returns true if so (and also if could not be executed because of old browser)
// Permits spaces, hyphens, brackets and leading +
// --------------------------------------------

function validateTelnr  (valfield,   // element to be validated
                         infofield,  // id of element to receive info/error msg
                         required)   // true if required
{
  var stat = commonCheck (valfield, infofield, required);
  if (stat != proceed) return stat;

  var tfld = trim(valfield.value);  // value of field with whitespace trimmed off
  var telnr = /^\+?[0-9 ()-]+[0-9]$/  ;
  if (!telnr.test(tfld)) {
	msg (infofield, 'error', '* '+gettext("Not a valid telephone number"));
    setfocus(valfield);
    return false;
  }

  var numdigits = 0;
  for (var j=0; j<tfld.length; j++)
    if (tfld.charAt(j)>='0' && tfld.charAt(j)<='9') numdigits++;

  if (numdigits<5) {
    msg (infofield, 'error', '* ' + numdigits + gettext("digits - too short"));
    setfocus(valfield);
    return false;
  }

  msg (infofield, "warn", "");
  return true;
}

// --------------------------------------------
//			validateSSN
// Validates social security number.
// Returns true if ssn is valid.
// Permits spaces and hyphens.

function validateSSN(valfield, infofield, required) {
	var stat = commonCheck(valfield, infofield, required);
	if (stat != proceed) return stat;
	var tfld = trim(valfield.value); // value of field without trailing/leading spaces
	var ssn = /^[0-9-\s]+$/;
	if (!ssn.test(tfld)) {
		msg(infofield, 'error', '* ' + gettext("Not a valid social security number"));
		setfocus(valfield);
		return false;
	}
	return true;
}

// --------------------------------------------
//          validateZipcode
// Validates zipcode. Returns true if zipcode
// is valid. Permits spaces.

function validateZipcode(valfield, infofield, required) {
	var stat = commonCheck(valfield, infofield, required);
	if (stat != proceed) return stat;
	var tfld = trim(valfield.value);
	var zipcode = /^[0-9\s]+$/;
	if (!zipcode.test(tfld)) {
		msg(infofield, 'error', '* ' + gettext("Not a valid zipcode"));
		setfocus(valfield);
		return false;
	}
	return true;
}

// --------------------------------------------
//          validatePrescriptionCode
// Validates prescription codes. A prescription
// code is seven digits long. We permit digits,
// spaces and hyphens.

function validatePrescriptionCode(valfield, infofield, required) {
	var stat = commonCheck(valfield, infofield, required);
	if (stat != proceed) return stat;
	var tfld = trim(valfield.value);
	var code = /^[-\s]*([0-9][-\s]*){7}$/
	if (!code.test(tfld)) {
		msg(infofield, 'error', '* ' + gettext("Not a valid prescription code"));
		setfocus(valfield);
		return false;
	}
	return true;
}
