function selectProd(id, name, classname) 
{
    var i1=classname;
    var i2='human'+classname;
    document.getElementById( i1 ).value = id;
    document.getElementById( i2 ).innerHTML = name;

    set_product_info( id );
    
    var customerid=document.getElementById( 'datacustomerid' ).value;
    var supplierid=document.getElementById( 'proddatasupplierid' ).value;
    set_offer_info( customerid, supplierid, id );
}

function set_product_info( id )
{
    var ret = loadXMLContent('?mod=product&action=summary&productid=' + id);
    document.getElementById( 'product_info' ).innerHTML = ret;
}

function set_offer_info(customerid, supplierid, productid)
{
    var ret = loadXMLContent('?mod=product&action=offersummary&customerid=' + customerid + '&supplierid=' + supplierid + '&productid=' + productid);
    document.getElementById( 'offer_info' ).innerHTML = ret;
}

function price_save() {
          document.getElementById('priceedit').submit();
    }

function selectRel(id, relname, classname) {
    var i1=classname;
    var i2='human'+classname;
    document.getElementById( i1 ).value = id;
    document.getElementById( i2 ).innerHTML = relname;

    if (classname == "proddatasupplierid")
        price_save();
    }

function calendarPopUp() {
        eval("var wx = window.open('common/calendar.php?veld=agenda&start=1041375600&eind=1230764400&sday='+document.getElementById('datavalidity').value+'&smonth='+document.getElementById('datavalidity').value+'&syear='+document.getElementById('datavalidity').value, 'wx', 'toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=200,height=220,left = 412,top = 284');");
        }

