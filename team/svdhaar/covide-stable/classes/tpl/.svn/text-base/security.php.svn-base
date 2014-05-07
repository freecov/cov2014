<?php
/**
 * Covide CMS Template Security module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2010 Covide BV
 * @package Covide
 */
/* load smarty security include file */
$include = sprintf('%s/../%s/sysplugins/smarty_security.php',
	dirname(__FILE__), Tpl_Output::SMARTY_FOLDER);

if (file_exists($include)) {
	require_once($include);
}

if (!class_exists('Smarty_Security', false)) {
/** 
 * Smarty Security handler, compatibility class for smarty 2.x
 * 
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @since 1.0 13/04/2010
 * @package Covide
 * @subpackage Tpl
 */
class Smarty_Security {}
}


/** 
 * Smarty Security handler 
 * 
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @since 1.0 13/04/2010
 * @package Covide
 * @subpackage Tpl
 */
class Tpl_security extends Smarty_Security {
	/**
	 * Specify some smarty security settings 
	 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
	 * @since 1.0 13/04/2010
	 */
	public function __construct() {
		/* specify php function we allow to execute */
		$this->php_functions = array('array', 'list',
			'isset', 'empty',
			'count', 'sizeof',
			'in_array', 'is_array', 'array_search',
			'true', 'false', 'null', 'strstr', 'str_istr'
		);
		/* specify the modifiers we allow */
		$this->modifiers = array('count', 'var_dump');
		/* do not allow constants */
		$this->allow_constants = false;
		/* do not allow php tags */
		$this->allow_php_tag = false;
	}
}
?>
