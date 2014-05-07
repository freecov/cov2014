/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function template_delete_file(id, template_id) {
	location.href='index.php?mod=email&action=templateDeleteFile&id='+id+'&template_id='+template_id;
}
function template_view_file(id) {

}
function template_method() {
	if (document.getElementById('mail[use_complex_mode]')) {
		val = document.getElementById('mail[use_complex_mode]').checked;
		if (val == 0) {
			document.getElementById('template_view_advanced').style.display = 'none';
		} else {
			document.getElementById('template_view_advanced').style.display = 'block';
		}
	}
}
/* call once at load and attach event on selectbox onchange */
//template_method();
if (document.getElementById('mail[use_complex_mode]')) {
	//document.getElementById('mail[use_complex_mode]').onclick = function() { template_method(); }
}

function template_save_db() {
		sync_editor_contents();
		if (document.getElementById('velden').onsubmit) {
			document.getElementById('velden').onsubmit();
		}
	document.getElementById('velden').submit();
}