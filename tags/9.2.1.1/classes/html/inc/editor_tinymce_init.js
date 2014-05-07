/**
 * Covide JS CLasses
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

function sync_editor_mini() {
	sync_editor_contents();
}
function sync_editor_contents() {
	if (tinyMCE) {
		//tinyMCE.triggerSave(true, true);
		if (tinyMCE.getContent) {
			//version 2
			document.getElementById('contents').value = tinyMCE.getContent('mce_editor_0');
		} else {
			//version 3
			for (i=0; i < tinymce_editor_fields.length; i++) {
				document.getElementById(tinymce_editor_fields[i]).value = tinymce.EditorManager.editors[tinymce_editor_fields[i]].getContent()
			}
		}

	}
}

/* state */
var mini_editor_inited = true;

