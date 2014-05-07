function search_classification() {
	selection_select_all();
	document.getElementById('velden').submit();
}

function search_show_all() {
	selection_select_all();
	document.getElementById('search').value = '';
	document.getElementById('velden').submit();
}

function selection_add() {
	selection_select_all();
	document.getElementById('sub_action').value = 'add';
	document.getElementById('velden').submit();
}

function selection_remove() {
	/* just inverse the selection state */
	var obj = document.getElementById('classifications_selected');
	for (var i=0; i<obj.options.length; i++) {
		if (obj.options[i].selected == true) {
			obj.options[i].selected = false;
		} else {
			obj.options[i].selected = true;
		}
	}
	document.getElementById('sub_action').value = '';
	document.getElementById('velden').submit();
}

function selection_init(id) {
	window.resizeTo(700,550);
	if (!parent.document.getElementById(id)) {
		alert('opener id not found');
		closepopup();
		return false;
	}
	var ids = parent.document.getElementById(id).value;
	document.getElementById('init_ids').value = ids;
	document.getElementById('velden').submit();
}

function selection_update_parent(id, ids, str) {
	/* set opener ids */
	parent.document.getElementById(id).value = ids;

	/* set description */
	parent.document.getElementById('classifications_name_'+id).innerHTML = str;
}

function selection_select_all() {
	var obj = document.getElementById('classifications_selected');
	for (var i=0; i<obj.options.length; i++) {
		obj.options[i].selected = true;
	}
}

function create_classification() {
	if (document.getElementById('newcladescription')) {
		if (document.getElementById('newcladescription').value) {
			selection_select_all();
			document.getElementById('newclaaction').value='save';
			document.getElementById('velden').submit();
		} else {
			alert(gettext('No name specified'));
		}
	}
}
