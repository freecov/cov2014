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
		document.getElementById('contents').value = tinyMCE.getContent('mce_editor_0');
	}
}

/* state */
var mini_editor_inited = true;

