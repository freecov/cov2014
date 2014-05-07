function product_save() {
	document.getElementById('productedit').submit();
}
function nosell_save() {
	document.getElementById('noselledit').submit();
}

function selectRel(id, relname, classname) {
        var i1=classname;
        var i2='human'+classname;
        document.getElementById( i1 ).value = id;
        document.getElementById( i2 ).innerHTML = relname;
}

