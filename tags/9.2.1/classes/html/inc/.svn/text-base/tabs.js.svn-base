var a_u = true;
// true = standaard staat de tab's functie aan
// false = standaard staat de tab's functie uit

var a_u_key = 18;
// hier kan je de nummer ingeven van snelkoppeling om de tab's functie aan of uit te zetten
// momenteel staat hij op altGr
// als je dus deze knop + TAB inhoudt, kan je switchen tussen aan en uit.

var checkbox = true;
// true: er komt boven de textarea een knop om te switchen tussen aan en uit.
// false: er gebeurt niets.
var checkbox_text = gettext("allow tabs in textarea");
// Hier zet je de text die na de checkbox moet komen.

//////////////////////////// versie 1.2: Tab's in een textarea //////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
//made by Haytjes //////////// http://www.alasal.be /////////////////////////////////////
var startPos = "";
var endPos = "";
var sel = "";
var altGr = false;
var input_id = "";
var shift = false;

function loadTabs()
{
  var oTextArea = document.getElementsByTagName("textarea");
  var y = oTextArea.length;
  for(var x=0;x<y;x++)
  {
    if(oTextArea.item(x).getAttribute("wysiwyg"))
    {
      if(checkbox)
      {
        var oDiv = document.createElement("DIV");
        var oInput=document.createElement("INPUT");
        var oTextNode = document.createTextNode(checkbox_text);
        oInput.type = "checkbox";
        oInput.onclick=function(){a_u = this.checked = !a_u;};
        oDiv.appendChild(oInput);
        oDiv.appendChild(oTextNode);
        input_id = oDiv.firstChild;
        oTextArea.item(x).parentNode.insertBefore(oDiv,oTextArea.item(x));
        oInput.checked = a_u;
      }
      oTextArea.item(x).onkeydown = position;
      oTextArea.item(x).onkeyup = function(e)
      {
        var key = (typeof e != 'undefined' && typeof e.which != 'undefined') ? e.which :
        (typeof e != 'undefined' && typeof e.keyCode != 'undefined') ? e.keyCode :
        (typeof window.event != 'undefined' && typeof event.keyCode != 'undefined') ? event.keyCode :
        null;
        if (key)
        {
          if(key == 16)
          {
            shift = false;
          }
        }
        return altGr = false;
      };
    }
  }
};
function position(e)
{
  if (document.selection)
  {
    this.focus();
    sel = document.selection.createRange();
  }
  if (this.selectionStart || this.selectionStart == '0')
  {
    startPos = this.selectionStart;
    endPos = this.selectionEnd;
  }
  var key = (typeof e != 'undefined' && typeof e.which != 'undefined') ? e.which :
  (typeof e != 'undefined' && typeof e.keyCode != 'undefined') ? e.keyCode :
  (typeof window.event != 'undefined' && typeof event.keyCode != 'undefined') ? event.keyCode :
  null;
  if (key)
  {
    if (key == a_u_key)
    {
      altGr = true;
      return true;
    }
    if (key == 16)
    {
      shift = true;
      return true;
    }
    if (key == 9)
    {
      if(altGr)
      {
        (a_u)?a_u=false:a_u=true;
        altGr = false;
        if(checkbox)
        {
          input_id.checked = a_u;
        }
        return false;
      }
      if(shift)
      {
        //shift = false;
        if(checkbox)
        {
          removeAtCursor(this);
        }
        return false;
      }
      if(a_u)
      {
        insertAtCursor(this);
        return false;
      }
    }
  }
}
function removeAtCursor(myField)
{
  //MOZILLA/NETSCAPE support
  if (myField.selectionStart || myField.selectionStart == '0')
  {
    var minEnd = 0;
    var minBegin = 0;
    replace = myField.value.substring(startPos,endPos);

    minEnd = replace.length+2;
    replace = replace.replace(/nt/g,"\n");
    first = myField.value.substring(0, startPos) + "1";
    lines = first.split("\n");
    line = lines[lines.length-1];
    lines[lines.length-1] = null;
    var len = line.length;
    if(len > 0)
    {
      line = line.replace(/^t/,"");
      minBegin += len - line.length;
    }
    else
    {
      var len = replace.length;
      replace = replace.replace(/^t/,"");
      minEnd += lin - replace.length

    }
    myField.value = lines.join("\n")+line.substr(0,line.length-1)+replace+myField.value.substring(endPos, myField.value.length);
    myField.selectionStart = startPos-minBegin;
    myField.selectionEnd = startPos-minBegin+replace.length;
  }
}
function insertAtCursor(myField)
{
  //MOZILLA/NETSCAPE support
  if (myField.selectionStart || myField.selectionStart == '0')
  {
    replace = myField.value.substring(startPos,endPos);
    var scroll = myField.scrollTop;
    if( replace.indexOf("\n") != -1)
    {
      replace = replace.replace(/n/g,"\n\t");
      first = myField.value.substring(0, startPos)
      if(first.indexOf("\n") == -1)
        first = "\t"+first
      else
        first = first.replace(/n(.*)$/,"\n\t$1")

      myField.value = first+replace+myField.value.substring(endPos, myField.value.length);
      myField.selectionStart = startPos+1;
      myField.selectionEnd = startPos+replace.length+1;
    }
    else
    {
      replace = "\t";
      myField.value = myField.value.substring(0, startPos)+replace+myField.value.substring(endPos, myField.value.length);
      myField.selectionStart = startPos+replace.length;
      myField.selectionEnd = startPos+replace.length;
    }
    myField.scrollTop = scroll;
  }
  // IE support
  else if (document.selection)
  {
    myField.focus();
    sel.text = "\t";
    sel.moveStart('character', 0);
    sel.select();
  }
  // if NOT supported
  else
  {
    myField.value += "\t";
  }
}
addLoadEvent(setTimeout('loadTabs();', 500));

