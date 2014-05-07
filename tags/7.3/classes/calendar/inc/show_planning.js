function change_week(offset) {
	var select_day = document.getElementById('day');
	var selected_day = parseInt(select_day.value);
	var new_day = selected_day+parseInt(offset);

	/*
	select_day.value = new_day;
	if (parseInt(select_day.value) == selected_day) {
		select_day.options[select_day.length] = new Option(new_day, new_day);
		select_day.value = new_day;
	}
	*/
	try {
		select_day.addItem(new_day, new_day);
	} catch(e) {
		addSelectBoxOption(select_day, new_day, new_day);
	}

	document.getElementById('planning').submit();
}
