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

		xinha_editors = null;
		xinha_init    = null;
		xinha_config  = null;
		xinha_plugins = null;

		// This contains the names of textareas we will make into Xinha editors
		xinha_init = xinha_init ? xinha_init : function()
		{
			/** STEP 1 ***************************************************************
			* First, what are the plugins you will be using in the editors on this
			* page.  List all the plugins you will need, even if not all the editors
			* will use all the plugins.
			************************************************************************/

			xinha_plugins = xinha_plugins ? xinha_plugins :
			[
			'CharacterMap',
			'ContextMenu',
			'Filter',
			'HtmlEntities',
			'ListType',
			'SuperClean',
			'TableOperations',
			'FindReplace',
			'HorizontalRule',
			'InsertAnchor',
			'InsertPagebreak',
			'PasteText',
			'SpellChecker',
			//'CharCounter',
			'EditTag',
			'QuickTag',
			'SmartReplace'
			];
			// THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
			if(!HTMLArea.loadPlugins(xinha_plugins, xinha_init)) return;

			/** STEP 2 ***************************************************************
			* Now, what are the names of the textareas you will be turning into
			* editors?
			************************************************************************/

			xinha_editors = xinha_editors ? xinha_editors :
			[
				'contents'
			];

			/** STEP 3 ***************************************************************
			* We create a default configuration to be used by all the editors.
			* If you wish to configure some of the editors differently this will be
			* done in step 4.
			*
			* If you want to modify the default config you might do something like this.
			*
			*   xinha_config = new HTMLArea.Config();
			*   xinha_config.width  = 640;
			*   xinha_config.height = 420;
			*
			*************************************************************************/
			xinha_config = new HTMLArea.Config();
			xinha_config.width  = 765;
			xinha_config.height = 490;
			xinha_config.showLoading = 1;
			xinha_config.flowToolbars = 1;
			xinha_config.undoSteps = 200;
			xinha_config.mozParaHandler = 'best';
			xinha_config.killWordOnPaste = 1;
			xinha_config.statusBar = 0;
			xinha_config.browserQuirksMode = 1;

			xinha_config.toolbar =
				[
					["popupeditor"],
					["separator","formatblock","fontname","fontsize","bold","italic","underline","strikethrough"],
					["separator","forecolor","hilitecolor","textindicator"],
					["separator","subscript","superscript"],
					["linebreak","separator","justifyleft","justifycenter","justifyright","justifyfull"],
					["separator","insertorderedlist","insertunorderedlist","outdent","indent"],
					["separator","inserthorizontalrule","createlink","insertimage", "inserttable"],
					["separator","undo","redo","selectall","print"], (HTMLArea.is_gecko ? [] : ["cut","copy","paste","overwrite"]),
					["separator","killword","clearfonts","removeformat","toggleborders","splitblock"],
					["separator","htmlmode","showhelp","about"]
				];

			xinha_config.pageStyle = 'body, p, td { font-family: arial,sans-serif; font-size: 10pt; }';
			xinha_config = xinha_config ? xinha_config : new HTMLArea.Config();

/*
			// We can load an external stylesheet like this - NOTE : YOU MUST GIVE AN ABSOLUTE URL
			//  otherwise it won't work!
			xinha_config.stylistLoadStylesheet(document.location.href.replace(/[^\/]*\.html/, 'stylist.css'));

			// Or we can load styles directly

			xinha_config.stylistLoadStyles('p.red_text { color:red }');

			// If you want to provide "friendly" names you can do so like
			// (you can do this for stylistLoadStylesheet as well)
			xinha_config.stylistLoadStyles('p.pink_text { color:pink }', {'p.pink_text' : 'Pretty Pink'});
*/
			/** STEP 3 ***************************************************************
			* We first create editors for the textareas.
			*
			* You can do this in two ways, either
			*
			*   xinha_editors   = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);
			*
			* if you want all the editor objects to use the same set of plugins, OR;
			*
			*   xinha_editors = HTMLArea.makeEditors(xinha_editors, xinha_config);
			*   xinha_editors['myTextArea'].registerPlugins(['Stylist','FullScreen']);
			*   xinha_editors['anotherOne'].registerPlugins(['CSS','SuperClean']);
			*
			* if you want to use a different set of plugins for one or more of the
			* editors.
			************************************************************************/

			xinha_editors   = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);

			/** STEP 4 ***************************************************************
			* If you want to change the configuration variables of any of the
			* editors,  this is the place to do that, for example you might want to
			* change the width and height of one of the editors, like this...
			*
			*   xinha_editors.myTextArea.config.width  = 640;
			*   xinha_editors.myTextArea.config.height = 480;
			*
			************************************************************************/


			/** STEP 5 ***************************************************************
			* Finally we "start" the editors, this turns the textareas into
			* Xinha editors.
			************************************************************************/

			editor_controller = HTMLArea.startEditors(xinha_editors);
			//window.onload = null;
		}

		addLoadEvent(xinha_init);

		//document.getElementById('velden').onsubmit();

		// window.onunload = HTMLArea.collectGarbageForIE;