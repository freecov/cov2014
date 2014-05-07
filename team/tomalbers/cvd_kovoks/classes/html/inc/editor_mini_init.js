function sync_editor_mini() {
	if (window.editor_controller) {
		var str = editor_controller.getInnerHTML();

		str = str.replace(/<br[^>]*?>/gi, '\n');
		str = str.replace(/<p[^>]*?>/gi, '\n');
		str = str.replace(/<\/p[^>]*?>/gi, '\n');
		document.getElementById('contents').value = str;
	}
}
var mini_editor_inited = false;
function init_editor(obj) {
	if (mini_editor_inited == false) {
		mini_editor_inited = true;
		document.getElementById(obj).style.visibility = 'hidden';
		var el = document.getElementById('contents');
		el.value = el.value.replace(/\n/g, '<br>');

		xinha_init();
		/* loadXML(editor_mini_script); */
	}
}