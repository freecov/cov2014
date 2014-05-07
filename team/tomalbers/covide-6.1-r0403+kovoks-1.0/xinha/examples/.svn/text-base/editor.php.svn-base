<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"; ?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<?
		$data = stripslashes($_REQUEST["data"]);
		$data = gzuncompress(urldecode($data));
	?>

<head>
	<?
	/*
  <!--------------------------------------:noTabs=true:tabSize=2:indentSize=2:--
    --  Xinha example usage.  This file shows how a developer might make use of
    --  Xinha, it forms the primary example file for the entire Xinha project.
    --  This file can be copied and used as a template for development by the
    --  end developer who should simply removed the area indicated at the bottom
    --  of the file to remove the auto-example-generating code and allow for the
    --  use of the file as a boilerplate.
    --
    --  $HeadURL: svn://gogo@xinha.gogo.co.nz/repository/trunk/examples/full_example-body.html $
    --  $LastChangedDate: 2005-03-05 21:42:32 +1300 (Sat, 05 Mar 2005) $
    --  $LastChangedRevision: 35 $
    --  $LastChangedBy: gogo $
    --------------------------------------------------------------------------->
  */
  ?>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Example of Xinha</title>
  <link rel="stylesheet" href="full_example.css" />

  <script type="text/javascript">
    // You must set _editor_url to the URL (including trailing slash) where
    // where xinha is installed, it's highly recommended to use an absolute URL
    //  eg: _editor_url = "/path/to/xinha/";
    // You may try a relative URL if you wish]
    //  eg: _editor_url = "../";
    // in this example we do a little regular expression to find the absolute path.
    _editor_url  = document.location.href.replace(/examples\/.*/, '')
    _editor_lang = "nl";      // And the language we need to use in the editor.
  </script>

  <!-- Load up the actual editor core -->
  <script type="text/javascript" src="../htmlarea.js"></script>

  <script type="text/javascript">
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
       //'InsertSmiley',
       //'FormOperations',
       //'Forms',
       'DoubleClick',
   		 'CharacterMap',
       'ContextMenu',
       //'FullScreen',
       'ListType',
       'SuperClean',
       //'TableOperations',
       'EnterParagraphs',
       'FindReplace',
       'HorizontalRule',
       //'InsertAnchor',
       //'InsertMarquee',
       'InsertPagebreak',
       'PasteText',
       'SpellChecker',
       'UnFormat',
       'CharCounter',
       'EditTag',
       //'Equation',
       'QuickTag'
      ];
			// THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
			if(!HTMLArea.loadPlugins(xinha_plugins, xinha_init)) return;

      /** STEP 2 ***************************************************************
       * Now, what are the names of the textareas you will be turning into
       * editors?
       ************************************************************************/

      xinha_editors = xinha_editors ? xinha_editors :
      [
        'myTextArea'
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
			 xinha_config.width  = 900;
			 xinha_config.height = 490;

			xinha_config.toolbar =
				[
					["popupeditor"],
					["separator","formatblock","fontname","fontsize","bold","italic","underline","strikethrough"],
					["separator","forecolor","hilitecolor","textindicator"],
					["separator","subscript","superscript"],
					["linebreak","separator","justifyleft","justifycenter","justifyright","justifyfull"],
					["separator","insertorderedlist","insertunorderedlist","outdent","indent"],
					["separator","inserthorizontalrule","createlink","insertimage"],
					["separator","undo","redo","selectall","print"], (HTMLArea.is_gecko ? [] : ["cut","copy","paste","overwrite"]),
					["separator","killword","clearfonts","removeformat","toggleborders","splitblock"],
					["separator","htmlmode","showhelp","about"]
				];

       xinha_config = xinha_config ? xinha_config : new HTMLArea.Config();

        xinha_config.pageStyle = 'font-family: arial,sans-serif; font-size: 10px;';
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

      HTMLArea.startEditors(xinha_editors);
      window.onload = null;
    }

    window.onload   = xinha_init;
    // window.onunload = HTMLArea.collectGarbageForIE;

		function callback() {
			alert(xinha_editors);
		}
  </script>
  <link type="text/css" rel="alternate stylesheet" title="blue-look" href="../skins/blue-look/skin.css" />
</head>

<body>
  <form action="blank.htm" id="editorfrm" onsubmit="return false;">
    <textarea id="myTextArea" name="myTextArea" style="width:100%;height:320px;"><?=$data?></textarea>
  </form>
  <script language="javascript">
    var bla = (document.compatMode);
  </script>
</body>
</html>