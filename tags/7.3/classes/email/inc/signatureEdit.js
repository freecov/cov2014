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

function signature_save() {
	if (window.sync_editor_mini) {
		sync_editor_mini();
	}
	document.getElementById('velden').submit();
}
