function financialcard_save() {
	if (document.getElementById('editfinancialcard')) {
		document.getElementById('editfinancialcard').submit();
	}
}

function financialbank_save() {
        if (document.getElementById('editfinancialbank')) {
                document.getElementById('editfinancialbank').submit();
        }
}

function remove_item(cardid, id) {
        if (confirm(gettext("Are you sure you want to delete this bank account")+'?')) {
		document.location.href='index.php?mod=address&action=financialshow&bankaction=remove&cardid='+cardid+'&bankid='+id;
        }
}
function selectRel(id, relname) {
	document.getElementById('bcardaddress_id').value = id;
	document.getElementById('searchrel').innerHTML = relname;
}
