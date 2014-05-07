function selectProd(id, name, offerid) 
{
    var url = '?mod=product&action=showoffer&offerid='+offerid+'&add='+id;
    document.location.href=url;
}

function offer_save() {
    var i = document.getElementById('datasupplierid').value;
    if (i == '')
          alert("Please set a supplier");
    else
          document.getElementById('offeredit').submit();
    }

function selectRel(id, relname, classname) {
    var i1=classname;
    var i2='human'+classname;
    document.getElementById( i1 ).value = id;
    document.getElementById( i2 ).innerHTML = relname;

    if (classname == "datasupplierid")
        offer_save();
    }

function edit_offer_price(listprice,price, offerid, offerprod) {
    do {
        var new_price = prompt("What should be the offerprice (max: "+listprice+")?", price);
        var newPrice = new_price.replace(/\,/,".");
    } while (listprice < newPrice);

    var url = '?mod=product&action=showoffer&subaction=change&offerid='+offerid+'&offerprod='+offerprod+'&price='+newPrice; 
    document.location.href=url;
    }

function edit_quantity(minorder, offerid, offerprod) {
    var new_minorder = prompt("What should be the minimum quantity?", minorder);
    var url = '?mod=product&action=showoffer&subaction=changequantity&offerid='+offerid+'&offerprod='+offerprod+'&minorder='+new_minorder; 
    document.location.href=url;
    }

function calendarPopUp() {
        eval("var wx = window.open('common/calendar.php?veld=agenda&start=1041375600&eind=1230764400&sday='+document.getElementById('datavalidity').value+'&smonth='+document.getElementById('datavalidity').value+'&syear='+document.getElementById('datavalidity').value, 'wx', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=200,height=220,left = 412,top = 284');");
        }

