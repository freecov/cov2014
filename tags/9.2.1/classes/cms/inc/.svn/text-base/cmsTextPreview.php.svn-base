<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
class cmsTextPreview {
	/** 
	 * run the keyword suggestion module
	 * 
	 * @param id int the page id to check
	 * @param cms one cms_output instance
	 */
	static public function run($id, $cms) {

		$output = new Layout_output();
		$output->layout_page("cms", 1);

		$venster = new Layout_venster(array(
			"title" => gettext("CMS"),
			"subtitle" => gettext("Text preview")
		));
		$cms->addMenuItems(&$venster);
		$venster->generateMenuItems();

		$venster->addVensterData();
		
		$cms_data = new Cms_data;
		$uri = sprintf('%s/page/%d', 
			str_replace('/mode/linkchecker#param#', '', $cms_data->linkchecker["url"]), $id);

		$text = file_get_contents($uri);
		$email_data = new Email_data();
		$text = $email_data->html2text($text);

		$venster->insertTag('pre', $text);
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
}

?>
