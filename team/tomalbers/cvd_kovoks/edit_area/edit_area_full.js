/******
 *
 *	Edit Area v0.4
 * 	Developped by Christophe Dolivet
 *	Released under GPL license
 *
******/
	function EditArea(){
		this.error= false;	// to know if load is interrrupt
		this.loadedFiles = new Array();
		this.baseURL="";
		this.file_name="edit_area.js";
		this.suffix="";
		this.scriptsToLoad= new Array("area_template", "manage_area", "resize_area", "edit_area_functions", "elements_functions", "reg_syntax", "regexp", "highlight", "keyboard", "search_replace");
		this.cssToLoad= new Array("edit_area.css");
		this.inlinePopup= new Array({popup_id: "area_search_replace", icon_id: "search_icon"},
									{popup_id: "edit_area_help", icon_id: "help_icon"});
		
		this.previous_content= new Array();
		this.previous_text="";
		this.last_highlight_line_selected= -1;
		this.tab_text_operator= new Array("=","+","-","/","*",";","->");
		this.is_updating=false;
		this.is_waiting_for_update=false;
		this.last_line_selected= -1;		
		this.last_selection_range= -1;
		this.last_selection=new Array();		
		this.textareaFocused= false;
		//this.loaded= false;
		this.doSmartTab=true;		// must use it
		this.assocBracket=new Array();
		this.revertAssocBracket= new Array();		
		this.textarea="";	
		this.previous= new Array();
		this.next= new Array();
		this.state="declare";
		this.code = new Array(); // store highlight syntax for languagues
		// font datas
		this.lineHeight= 16;
		this.charWidth=8;
		this.default_font_family= "monospace";
		this.default_font_size= 10;
		this.tab_nb_char= 8;	//nb of white spaces corresponding to a tabulation
		this.is_tabbing= false;
		
		// navigator identification
		ua= navigator.userAgent;
		this.isIE = (navigator.appName == "Microsoft Internet Explorer");
		this.isNS = ua.indexOf('Netscape/') != -1;
		if(this.isNS){	// work only on netescape > 8 with render mode IE
			this.NSvers= ua.substr(ua.indexOf('Netscape/')+9);
			if(this.NSvers<8 || !this.isIE)
				this.error=true;			
		}
		this.isOpera = (ua.indexOf('Opera') != -1);
		if(this.isOpera==true){	
			this.OperaVers= ua.substr(ua.indexOf('Opera ')+6);
			if(this.OperaVers<9)
					this.error=true;
			this.isIE=false;			
		}
		this.isGecko = (ua.indexOf('Gecko') != -1);
		this.isFirefox = (ua.indexOf('Firefox') != -1);
		this.isSafari = (ua.indexOf('Safari') != -1);
		this.back_compat_mode=(document.compatMode=="BackCompat");
		//alert(this.back_compat_mode);
		//alert(navigator.appName+"\n"+ua+"\n: IE: "+this.isIE+" opera: "+this.isOpera+" v:"+this.OperaVers+"\nFirefox: "+this.isFirefox+"\nGecko: "+this.isGecko+"\nSafari: "+this.isSafari);
		//alert(ua+"\n"+this.isGecko);
		// resize var
		this.isResizing=false;
		this.resize_start_mouse_x=0;
		this.resize_start_mouse_y=0;
		this.resize_start_width=0;
		this.resize_start_height=0;
		this.resize_new_width=0;
		this.resize_new_height=0;
		this.resize_start_inner_height=0;	
		this.min_width= 375;
		this.min_height= 50;
		this.resize_mouse_x=0;
		this.resize_mouse_y=0;
		// available options
		this.do_highlight=false;
		this.debug=false; 
		this.id="";
		this.font_family= "monospace";
		this.font_size= 10;
		this.line_selection= true;
		this.max_undo= 15;
		this.save_callback="";
		this.load_callback="";
		this.toolbar= "new_document, |, search, go_to_line, |, undo, redo, |, select_font, change_line_selection, highlight, reset_highlight, |, help";
		this.allow_resize= "both"; // "no", "x", "y", "both"
		this.allow_toogle=true;
		this.language="en";
		this.do_html_tags= true;
		this.code_lang="php";
		this.lang_style= new Array();
		
		this.setBaseURL();
		//load needed files
		if(this.file_name!="edit_area_gzip.php"){	// don't load files if they were loaded in php		
			for(var script in this.scriptsToLoad){
				this.loadScript(this.baseURL + this.scriptsToLoad[script]+ this.suffix +".js");
			}
		}
		
		for(var css in this.cssToLoad){
			this.loadCSS(this.baseURL + this.cssToLoad[css]);
		}
		
	};
	
	EditArea.prototype.initArea= function(settings){
		//alert("init");		
		//alert(document.compatMode);
		if(this.error)
			return;
		// init settings
		this.settings = settings;
		for(var i in this.settings){
			if( this.settings[i]===false ||  this.settings[i]===true)
				eval("this."+ i +"="+ this.settings[i]+";");
			else
				eval("this."+ i +"=\""+ this.settings[i]+"\";");
		}
		if(this.begin_toolbar)
			this.toolbar= this.begin_toolbar +","+ this.toolbar;
		if(this.end_toolbar)
			this.toolbar= this.toolbar +","+ this.end_toolbar;
		this.tab_toolbar= this.toolbar.replace(/ /g,"").split(",");
		
		this.code_lang=this.code_lang.toLowerCase();
		
		//alert(this.tab_toolbar.length+"\n"+this.tab_toolbar.join("\n =>"));
		if(this.isIE || this.isNS){	// IE work well only with those settings
			this.font_familly= this.default_font_family;
			this.font_size= this.default_font_size;		
		}
		if(this.isOpera){
			this.tab_nb_char=6;
		}
		
		// bracket selection init 
		this.assocBracket["("]=")";
		this.assocBracket["{"]="}";
		this.assocBracket["["]="]";		
		for(var index in this.assocBracket){
			this.revertAssocBracket[this.assocBracket[index]]=index;
		}		
		// load language file
		this.loadScript(this.baseURL + "langs/"+ this.language + ".js");
		// load coe regexp syntax file
		this.loadScript(this.baseURL + "reg_syntax/"+ this.code_lang +".js");
		this.addEvent(window, "load", EditArea.prototype.startArea);
		this.state="init";
	};
	
	EditArea.prototype.first_display= function(){
		//reg exp initialisation
		this.initRegExp();
		/*if(editArea.phpLevel=="middle"){
			editArea.php_functions_reg= new RegExp(editArea.getRegExp(editArea.php_functions_middle),"g");
		}else if(editArea.phpLevel=="simple"){
			editArea.php_functions_reg= new RegExp(editArea.getRegExp(editArea.php_functions_simple),"g");
		}	*/
		
		// get toolbar content
		var html_toolbar_content="";
		for(var i=0; i<this.tab_toolbar.length; i++){
		//	alert(this.tab_toolbar[i]+"\n"+ this.get_control_html(this.tab_toolbar[i]));
			html_toolbar_content+= this.get_control_html(this.tab_toolbar[i]);
		}
		
		// create template
		this.template= this.get_template().replace("[__TOOLBAR__]",html_toolbar_content);
		var div_line_number="";
		for(i=1; i<10000; i++)
			div_line_number+=i+"<br>";
		this.template= this.template.replace("[__LINE_NUMBER__]", div_line_number);
		
		if(this.debug)
			this.template="<textarea id='line' style='z-index: 20; width: 100%; height: 120px;overflow: auto; border: solid black 1px;'></textarea><br>"+ this.template;
		if(this.allow_toogle==true)
			this.template+="<div id='edit_area_toogle'><input id='edit_area_toogle_checkbox' type='checkbox' onclick='editArea.toogle();' accesskey='e' checked /><label for='edit_area_toogle_checkbox'>{$toogle}</label></div>";	
		
			
		// fill template with good language sentences
		this.template=this.template.replace(/\{\$([^\}]+)\}/gm, this.traduc_template);
		
		// insert template in the document after the textarea
		var father= this.textarea.parentNode;
		var content= document.createElement("span");
		var next= this.textarea.nextSibling;
		if(next==null)
			father.appendChild(content);
		else
			father.insertBefore(content, next) ;
		content.innerHTML=this.template;
	

		
	

	/*	
		content=document.getElementById("edit_area_template");
		alert("nb child"+count_children(content, 5)+"\n direct: "+ count_children(content, 0));
		var test= count_child_type(content, 2);
		var res="";
		for(var i in test){
			res+=i+": "+test[i]+"\n";
		}
		alert(res);
		//content.normalize();
		alert("nb child"+count_children(content, 4)+"\n direct: "+ count_children(content, 0));
		
		// add toggle button
		if(this.allow_toogle==true){			
			var next=this.textarea.nextSibling;
			if(next!= null)
				father.insertBefore(document.getElementById("edit_area_toogle"), next) ;
			else
				father.appendChild(document.getElementById("edit_area_toogle"));
		}*/
		
		// init to good size
		this.toolbars_height= this.get_all_toolbar_height();
		var edit_area= document.getElementById("edit_area");					
		var width= (this.textarea.style.width || getAttribute(this.textarea, "width"));
		var height= (this.textarea.style.height || getAttribute(this.textarea, "height"));						
		
		edit_area.style.width= width;
		edit_area.style.height= height;
		
		// check min size
		if(edit_area.offsetWidth < this.min_width)
			edit_area.style.width=this.min_width+"px";
		if(edit_area.offsetHeight < this.min_height)
			edit_area.style.height=this.min_height+"px";
		
		// get effective size
		width=edit_area.offsetWidth;
		height= edit_area.offsetHeight;
		if(this.isIE)	// with height=100% for result we must withdraw again toolbar height
			height-= editArea.toolbars_height;
	//	if(!this.isFirefox)
			height-=4;
		
						
		var result_height= height - this.toolbars_height;				
		document.getElementById("edit_area_template").style.visibility= "visible";			
		if(this.isIE){ 
			document.getElementById("result").style.width=(edit_area.offsetWidth -2 )+"px";
			document.getElementById("result").style.height= (result_height+2)+"px";
		}else{
			/*if(this.back_compat_mode)
				result_height+=2;
			else
				result_height-=9;*/
			document.getElementById("result").style.height= result_height+"px";
		}
		
				
	};
	
	EditArea.prototype.startArea= function(){
		editArea.textarea= document.getElementById(editArea.id);
		if(editArea.textarea==null){
			document.getElementById("edit_area_template").style.display="none";
			return;
		}
		
		if(editArea.state=="init"){
			editArea.first_display();
		}			
		//alert("start");
	
		var template_area= document.getElementById("editArea_textarea");

		// insert template datas in the place of the textarea
		template_area.value=editArea.textarea.value;	
		// invert the two textarea		
		setAttribute(template_area, "name", getAttribute(editArea.textarea, "name") );
		setAttribute(template_area, "id", getAttribute(editArea.textarea, "id") );
		//setAttribute(editArea.textarea, "name",  getAttribute(editArea.textarea, "name")+"_replaced");
		editArea.textarea.removeAttribute("name") ;
		setAttribute(editArea.textarea, "id",  getAttribute(editArea.textarea, "id")+"_replaced");
				
		if(editArea.state!="init"){			
			document.getElementById("edit_area_template").style.display= "block";
		}
		
		// hide old textarea
		editArea.textarea.style.display="none";
		editArea.textarea= template_area;
		if(editArea.isIE){
			editArea.textarea.style.marginTop= "1px";
		/*	editArea.textarea.style.marginLeft= "0px";*/
		}
		if(document.getElementById("redo_icon") != null)
			editArea.switchClassSticky(document.getElementById("redo_icon"), 'editAreaButtonDisabled', true);
		
		// get font size datas		
		editArea.set_font(editArea.font_family, editArea.font_size);
		
		// highlight
		if(editArea.do_highlight===true){
			editArea.disableHighlight();	// init with correct values			
			editArea.enableHighlight();
		}else
			editArea.disableHighlight();
		
		// line selection init
		editArea.change_line_selection_mode(editArea.line_selection);
		editArea.textarea.focus();

		
		// init key events
		editArea.textarea.onkeydown= keyDown;
		if(editArea.isIE || editArea.isFirefox)
			editArea.textarea.onkeydown= keyDown;
		else
			editArea.textarea.onkeypress= keyDown;
		for(var i in editArea.inlinePopup){
			if(editArea.isIE || editArea.isFirefox)
				document.getElementById(editArea.inlinePopup[i]["popup_id"]).onkeydown= keyDown;
			else
				document.getElementById(editArea.inlinePopup[i]["popup_id"]).onkeypress= keyDown;
			//document.getElementById(editArea.inlinePopup[i]["popup_id"]).onkeydown= keyDown;
		}
		
		// allow resize area
		if(editArea.allow_resize!="no")
			document.getElementById("resize_area").onmousedown= editArea.startResizeArea;
		
		if(!this.isIE && !this.isOpera)	// force a refresh of the result area
			document.getElementById("result").style.right="0";
		
		editArea.state="loaded";		
		
		//start checkup routine
		editArea.check_undo();
		editArea.startMajArea(true);
		editArea.checkLineSelection();
		editArea.formatArea();		
	};
	
	
	
	EditArea.prototype.setBaseURL= function(){
		//this.baseURL="";
		if (!this.baseURL) {
			var elements = document.getElementsByTagName('script');
	
			for (var i=0; i<elements.length; i++) {
				if (elements[i].src && (elements[i].src.indexOf("edit_area.js") != -1  || elements[i].src.indexOf("edit_area_src.js") != -1 || elements[i].src.indexOf("edit_area_gzip.php") != -1 )) {
					var src = elements[i].src;
					src = src.substring(0, src.lastIndexOf('/'));
					this.baseURL = src;
					this.file_name= elements[i].src.substr(elements[i].src.lastIndexOf("/")+1);
					break;
				}
			}
		}
		
		var documentBasePath = document.location.href;
		if (documentBasePath.indexOf('?') != -1)
			documentBasePath = documentBasePath.substring(0, documentBasePath.indexOf('?'));
		var documentURL = documentBasePath;
		documentBasePath = documentBasePath.substring(0, documentBasePath.lastIndexOf('/'));
	
		// If not HTTP absolute
		if (this.baseURL.indexOf('://') == -1 && this.baseURL.charAt(0) != '/') {
			// If site absolute
			this.baseURL = documentBasePath + "/" + this.baseURL;
		}
		this.baseURL+="/";	
	};

	EditArea.prototype.loadScript= function(url){
		for (var i=0; i<this.loadedFiles.length; i++) {
			if (this.loadedFiles[i] == url)
				return;
		}	
	//	alert("laod: "+url);
		document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></script>');
		this.loadedFiles[this.loadedFiles.length] = url;
	};

	EditArea.prototype.loadCSS= function(url) {
		for (var i=0; i<this.loadedFiles.length; i++) {
			if (this.loadedFiles[i] == url)
				return;
		}	
		document.write('<link href="' + url + '" rel="stylesheet" type="text/css" />');
		this.loadedFiles[this.loadedFiles.length] = url;
	};
	
	EditArea.prototype.execCommand= function(cmd){
		switch(cmd){
			case "save":
				if(this.save_callback!="")
					eval(this.save_callback+"(editArea.textarea.value);");
				break;
			case "load":
				if(this.load_callback!="")
					eval(this.load_callback+"(editArea.textarea);");
				break;			
			default:
				eval("editArea."+cmd+"();");	
		}
	};
	
	
	/*
	EditArea.prototype.importCSS = function(doc, css_file) {
		if (css_file == '')
			return;	
		if (typeof(doc.createStyleSheet) == "undefined") {
			var elm = doc.createElement("link");
	
			elm.rel = "stylesheet";
			elm.href = css_file;
	
			if ((headArr = doc.getElementsByTagName("head")) != null && headArr.length > 0)
				headArr[0].appendChild(elm);
		} else
			var styleSheet = doc.createStyleSheet(css_file);
	};*/
	
	
	EditArea.prototype.startMajArea= function(reload_all, waitingUpdate){
		if(this.do_highlight==false){
			
		}else if(this.is_waiting_for_update==false || waitingUpdate==true){
			// don't enqueue call to majArea is area is currently upadating
			if(!reload_all)
				reload_all=false;
			if(this.is_updating==false){
				//disableHighlight(true);
				this.is_waiting_for_update=false;
				this.majArea(reload_all);
				//enableHighlight(true);
			}else{
				this.is_waiting_for_update=true;
				setTimeout("editArea.startMajArea("+reload_all+", true);", 50);
			}			
		}
		return true;
	};


	EditArea.prototype.addEvent = function(obj, name, handler) {
		if (this.isIE) {
			obj.attachEvent("on" + name, handler);
		} else{
			obj.addEventListener(name, handler, false);
		}
	};
	
	EditArea.prototype.toogle= function(toogle_to){
		if(this.isIE)
			this.getIESelection();
		var pos_start= this.textarea.selectionStart;
		var pos_end= this.textarea.selectionEnd;
		
		
		if(this.state=="loaded" || toogle_to=="off"){
			this.toogle_off();
		}else{
			this.toogle_on();
		}
		
		this.textarea= document.getElementById(this.id);
		this.textarea.focus();
		this.textarea.selectionStart = pos_start;
		this.textarea.selectionEnd = pos_end;
		if(this.isIE)
			this.setIESelection();
		return false;
	};
	
	EditArea.prototype.toogle_off= function(){
		if(!this.state=="loaded")
			return;
		var scrollLeft= document.getElementById("result").scrollLeft;
		var scrollTop= document.getElementById("result").scrollTop;
		
		// give back good name and id to the textarea	
		var previous_area= document.getElementById(this.id+"_replaced");
		setAttribute(previous_area, "name", getAttribute(editArea.textarea, "name") );
		setAttribute(previous_area, "id", this.id);
		
	//	setAttribute(editArea.textarea, "name",  "");
		editArea.textarea.removeAttribute('name');
		setAttribute(editArea.textarea, "id",  "editArea_textarea");
		// init to good size
		var edit_area= document.getElementById("edit_area");
	
		previous_area.style.width= edit_area.offsetWidth+"px";
		previous_area.style.height= edit_area.offsetHeight+"px";
		previous_area.value= this.textarea.value;
		document.getElementById("edit_area_toogle_checkbox").checked=false;
		// disaply the previous textarea
		document.getElementById("edit_area_template").style.display= "none";
		previous_area.style.display= "block";
		//alert("h: "+previous_area.offsetHeight+" w: "+previous_area.offsetWidth+"\n"+getAttribute(previous_area, "style"))
		this.state="hidden";
		
		previous_area.scrollLeft= scrollLeft;
		previous_area.scrollTop= scrollTop;
	};		
	
	EditArea.prototype.toogle_on= function(){
		var scrollLeft= document.getElementById(this.id).scrollLeft;
		var scrollTop= document.getElementById(this.id).scrollTop;
		document.getElementById("edit_area_toogle_checkbox").checked=true;
		this.startArea();
		
		document.getElementById("result").scrollLeft= scrollLeft;
		document.getElementById("result").scrollTop= scrollTop;
	};
	
	EditArea.prototype.traduc_template= function(){
		return editArea.getLang(EditArea.prototype.traduc_template.arguments[1]);
	};
	
	EditArea.prototype.getLang= function(val){
		
		for(var i in EditArea_lang){
			if(i == val)
				return EditArea_lang[i];
		}
		return "_"+val;
	};
		
	// Global instances
	var editArea = new EditArea();
	var isIE= (navigator.appName == "Microsoft Internet Explorer");
	var EditArea_language_data= new Array();	var EditArea_advanced_buttons = [
		// Control id, button img, button title, command
		['new_document', 'newdocument.gif', 'new_document'],
		['search', 'search.gif', 'show_search'],
		['go_to_line', 'go_to_line.gif', 'go_to_line'],
		['undo', 'undo.gif', 'undo'],
		['redo', 'redo.gif', 'redo'],
		['change_line_selection', 'line_selection.gif', 'change_line_selection_mode'],
		['reset_highlight', 'reset_highlight.gif', 'reSync'],
		['highlight', 'highlight.gif','changeHighlight'],
		['help', 'help.gif', 'show_help'],
		['save', 'save.gif', 'save'],
		['load', 'load.gif', 'load']
	];

	EditArea.prototype.get_control_html= function(button_name) {		
		
		for (var i=0; i<EditArea_advanced_buttons.length; i++)
		{
			var but = EditArea_advanced_buttons[i];			
			if (but[0] == button_name)
			{
				var cmd = 'editArea.execCommand(\'' + but[2] + '\')';
				html= '<a href="javascript:' + cmd + '" onclick="' + cmd + ';return false;" onmousedown="return false;" target="_self">';
				html+= '<img id="' + but[0] + '_icon" src="'+ this.baseURL +'images/' + but[1] + '" title="' + this.getLang(but[0]) + '" width="20" height="20" class="editAreaButtonNormal" onmouseover="editArea.switchClass(this,\'editAreaButtonOver\');" onmouseout="editArea.restoreClass(this);" onmousedown="editArea.restoreAndSwitchClass(this,\'editAreaButtonDown\');" /></a>';
				return html;
			}	
		}		
				
		switch (button_name){
			case "|":
		  	case "separator":
				return '<img src="'+ this.baseURL +'images/spacer.gif" width="1" height="15" class="editAreaSeparatorLine">';
			case "select_font":
				if(!editArea.isIE && !editArea.isNS){
					html= "<select id='area_font_size' onchange='javascript:editArea.change_font_size()'>"
						+"			<option value='-1'>--Font size--</option>"
						+"			<option value='8'>8 pt</option>"
						+"			<option value='9'>9 pt</option>"
						+"			<option value='10'>10 pt</option>"
						+"			<option value='11'>11 pt</option>"
						+"			<option value='12'>12 pt</option>"
						+"			<option value='14'>14 pt</option>"
						+"		</select>";
					return html + this.get_control_html("|");
				}
		}
		
		return "";		
	};
	
	
	EditArea.prototype.get_template= function() {
		return "<div id='edit_area_template' style='visibility: hidden;'>"
+"<div id='resize_hidden_field'></div>"
+"<div id='edit_area' class='edit_area' style='border: solid 1px #888888;'>"
+"	<div class='area_toolbar' id='toolbar_1'>[__TOOLBAR__]</div>"
+"	"
+"  <div id='result' class='result' style='position: relative; z-index: 4; overflow: scroll;border-top: solid #888888 1px;border-bottom: solid #888888 1px;'> "
+"    <div id='container' style=' '> "
+"      <div id='cursor_pos' class='edit_area_cursor'>&nbsp;</div>"
+"      <div id='end_bracket' class='edit_area_cursor'>&nbsp;</div>"
+"      <div id='selection_field' class='edit_area_selection_field' style=''></div>"
+"      <div id='line_number' class='line_number' style='position: absolute;overflow: hidden;border-right: solid black 1px;z-index:8'>[__LINE_NUMBER__]</div>"
+"      <div id='content_highlight' style='padding: 1px 0 0 45px; position : absolute; z-index: 4; overflow: visible; white-space: nowrap;'></div>"
+"      <textarea id='editArea_textarea' style='padding: 0 0px 0 45px; width: 100%; position: absolute; overflow: hidden;  z-index: 7; border: solid red 0px;background-color: transparent;' "
+"			class='area hidden' wrap='off' onfocus='javascript:editArea.textareaFocused=true;' onblur='javascript:editArea.textareaFocused=false;'>"
+"		</textarea>"
+"		<span id='edit_area_test_font_size' style='padding: 0; margin: 0; visibility: hidden; border: solid red 0px;'></span>"
+"    </div>"
+"  </div>"
+"	"
+"	<table class='area_toolbar' style='' cellspacing='0' cellpadding='0'>"
+"		<tr>"
+"			<td class='total'>{$position}:</td>"
+"			<td class='infos'>"
+"				{$line_abbr} <span  id='linePos'>0</span>, {$char_abbr} <span id='currPos'>0</span>"
+"			</td>"
+"			<td class='total'>{$total}:</td>"
+"			<td class='infos'>"
+"				{$line_abbr} <span id='nbLine'>0</span>, {$char_abbr} <span id='nbChar'>0</span>"
+"			</td>"
+"			"
+"			"
+"			<td align='right'><span id='resize_area' style='cursor: nw-resize;'><img src='"+ editArea.baseURL +"images/statusbar_resize.gif'></span></td>"
+"		</tr>"
+"	</table>"
+"</div>"
+"<div id='area_search_replace' class='editarea_popup'>"
+"	<table cellspacing='2' cellpadding='0' style='width: 100%'>"
+"		<tr>"
+"			<td>{$search}</td>"
+"			<td><input type='text' id='area_search' /></td>"
+"			<td rowspan='2' style='text-align: right; vertical-align: top; white-space: nowrap;'>"
+"				<a href='Javascript:editArea.hidden_search()'><img src='"+ editArea.baseURL +"images/close.gif' alt='close' title='{$close_popup}' /></a><br>"
+"				<div id='move_area_search_replace' style='cursor: move; padding: 3px 3px; margin-top: 3px; border: solid 1px #888888;' onmousedown='start_move_element(event,\"area_search_replace\")'  />move</div>			"
+"		</tr><tr>"
+"			<td>{$replace}</td>"
+"			<td><input type='text' id='area_replace' /></td>"
+"		</tr>"
+"	</table>"
+"	<div class='button'>"
+"		<input type='checkbox' id='area_search_match_case' /><label for='area_search_match_case'>{$match_case}</label>"
+"		<input type='checkbox' id='area_search_reg_exp' /><label for='area_search_reg_exp'>{$reg_exp}</label>"
+"		<br />"
+"		<a href='Javascript:editArea.area_search()'>{$find_next}</a>"
+"		<a href='Javascript:editArea.area_replace()'>{$replace}</a>"
+"		<a href='Javascript:editArea.area_replace_all()'>{$replace_all}</a><br />"
+"	</div>"
+"	<div id='area_search_msg' style='height: 18px; overflow: hidden; border-top: solid 1px #888888; margin-top: 3px;'></div>"
+"</div>"
+"<div id='edit_area_help' class='editarea_popup'>"
+"	<div class='close_popup' style='float: right'>"
+"		<a href='Javascript:editArea.close_all_inline_popup()'><img src='"+ editArea.baseURL +"images/close.gif' alt='close' title='{$close}' /></a>"
+"	</div> "
+"	<div><h2>Editarea</h2><br>"
+"		<h3>{$shortcuts}:</h3>"
+"			Tab: {$add_tab}<br>"
+"			Shift+Tab: {$remove_tab}<br>"
+"			Ctrl+f: {$search_command}<br>"
+"			Ctrl+r: {$replace_command}<br>"
+"			Ctrl+h: {$highlight}<br>"
+"			Ctrl+g: {$go_to_line}<br>"			
+"			Ctrl+q: {$close_popup}<br>"
+"			Ctrl+e: {$help}<br>"
+"			Accesskey E: {$toogle}<br>"
+"		<br>"
+"		<em>{$about_notice}</em>"
+"		<br><div class='copyright'>&copy; Christophe Dolivet - 2006</div>"
+"	</div>"
+"</div>"
+"</div>";
	};
	

	EditArea.prototype.formatArea= function(){		
		var text=this.textarea.value;
		
		
		if(this.do_highlight){
			/*
			document.getElementById("line").value="content_offset_w: "+document.getElementById("content_highlight").offsetWidth;
			document.getElementById("line").value+="result_offset_w: "+document.getElementById("result").offsetWidth;
			document.getElementById("line").value+="area_client_w: "+this.textarea.scrollWidth;
			*/
			var new_width=Math.max(document.getElementById("content_highlight").offsetWidth, document.getElementById("result").offsetWidth);
			if(this.isGecko || ( this.isNS && !this.isIE ) ){
				new_width= Math.max(new_width, this.textarea.scrollWidth+50);
			}
			//if(document.compatMode!="BackCompat")
			if(!this.back_compat_mode)
				new_width-=45;
			if(this.isGecko && !this.isFirefox)
				new_width-=45;
				/*
			if(document.doctype!=null)
				new_width-=45;
			else if(this.isIE && document.compatMode!="BackCompat"){	// equals to a doctype is present for IE
				new_width-=45;			
			}*/
			
			this.textarea.style.width=new_width+"px";
			
			new_height=Math.max(document.getElementById("content_highlight").offsetHeight+15, document.getElementById("result").offsetHeight-15);			
			this.textarea.style.height=new_height+"px";			
			if(this.isGecko || ( this.isNS && !this.isIE ) ){
				this.textarea.style.height=(new_height+this.lineHeight + 5)+"px";		
			}
			document.getElementById("line_number").style.height=new_height+"px";
			document.getElementById("container").style.height=new_height+"px";			
		}else{
			// modify textarea size to content size
			var tab=text.split("\n");					
			var new_height=tab.length*this.lineHeight;
			var new_width=this.textarea.scrollWidth;
			if(!this.back_compat_mode )
				new_width-=45;
			
			
			
			
				/*
			if(document.doctype!=null)
				new_width-=45;
			else if(this.isIE && document.compatMode!="BackCompat"){	// equals to a doctype is present for IE
				new_width-=45;			
			}*/
			date= new Date();
			if(this.debug)
				document.getElementById("line").value="new height: "+new_height+" new width: "+new_width+ " scroll_w: "+document.getElementById("result").scrollWidth+ " "+date.getTime();

			this.textarea.style.height=new_height+"px";
			if(this.isGecko || ( this.isNS && !this.isIE ) ){
				this.textarea.style.height=(new_height+this.lineHeight + 5)+"px";		
			}
			
			document.getElementById("line_number").style.height=new_height+"px";		
			
			//document.getElementById("container").style.height=new_height+"px";
		/*	if(this.isOpera){
				new_width= document.getElementById("result").scrollWidth - 50;
				//new_height= document.getElementById("result").scrollHeight;
				var style=getAttribute(this.textarea, "style");
				if(new_width != this.textarea.style.width.replace("px","")){
				//	var new_style= style.replace(/ +;?width:[^;]*;/gmi, "")+";width: "+ new_width+"px; height: "+new_height+"px;";
				//	setAttribute(this.textarea, "style",  new_style);
					this.textarea.style.width= new_width+"px";
				}
				if(this.debug)
					document.getElementById("line").value="new w: "+new_width+" curr_width: "+this.textarea.style.width+" contain "+document.getElementById("result").scrollWidth+"\n"+style;
				
			}else if(this.isIE){
				this.textarea.style.width=new_width+"px";	
			}else{ // for padding
				this.textarea.style.width=new_width+45+"px";	
			}*/
			if(this.isOpera){
				new_width= document.getElementById("result").scrollWidth - 50;
				var style=getAttribute(this.textarea, "style");
				if(new_width != this.textarea.style.width.replace("px","")){
					var new_style= style.replace(/ +;?width:[^;]*;/gmi, "")+";width: "+ new_width+"px; height: "+new_height+"px;";
					setAttribute(this.textarea, "style",  new_style);
				}
				if(this.debug)
					document.getElementById("line").value="new w: "+new_width+" curr_width: "+this.textarea.style.width+" contain "+document.getElementById("result").scrollWidth+"\n scroll_l: "+this.textarea.scrollLeft+"\n"+style;			
			}else if(!this.isIE){// for padding
				new_width+=45;	
			}
			if(this.textarea.style.width.replace("px","") < new_width){
				new_width+=50;
			}
			/*if(this.isGecko && !this.isFirefox)
				document.getElementById*/
			this.textarea.style.width=new_width+"px";
			//}
			if(this.isGecko && !this.isFirefox){
				/*document.getElementById("result").style.width="500px";
				document.getElementById("container").style.width="500px";
				document.getElementById("src").style.width="500px";
				document.getElementById("selection_field").style.width="500px";*/
			}
			
			if(this.state=="loaded")
				setTimeout("editArea.formatArea();", 500);
		}		
	};
	
	EditArea.prototype.checkLineSelection= function(){
		//if(do_highlight==false){
		/*if(this.once!=1){
			alert("ONCE a"+ this.isResizing);
			this.once=1;
		}*/
		if(!this.line_selection && !this.do_highlight){
			//formatArea();
		}else if(this.textareaFocused && this.isResizing==false){
			infos= this.getSelectionInfos();
				
			if(infos["line_start"]<1)
				infos["line_start"]=1;
		
			if(this.last_line_selected != infos["line_start"] || this.last_selection_range != infos["line_nb"] || infos["full_text"] != this.last_selection["full_text"]){
			// if selection change
				new_top=this.lineHeight * (infos["line_start"]-1);
			//	if(this.isIE)
					//new_top++;
				new_height=Math.max(0, this.lineHeight * infos["line_nb"]);
				//new_width=Math.max(document.getElementById("content_highlight").offsetWidth, document.getElementById("result").offsetWidth, this.textarea.offsetWidth);
				new_width=Math.max(this.textarea.scrollWidth, document.getElementById("container").clientWidth -50);
				//document.getElementByIf("line").value
				//alert("new_geigh: "+ new_height);
				document.getElementById("selection_field").style.top=new_top+"px";	
				document.getElementById("selection_field").style.width=new_width+"px";
				document.getElementById("selection_field").style.height=new_height+"px";	
				document.getElementById("cursor_pos").style.top=new_top+"px";	
				
				if(this.do_highlight==true){
					var curr_text=infos["full_text"].split("\n");
					var content="";
					//alert("length: "+curr_text.length+ " i: "+ Math.max(0,infos["line_start"]-1)+ " end: "+Math.min(curr_text.length, infos["line_start"]+infos["line_nb"]-1)+ " line: "+infos["line_start"]+" [0]: "+curr_text[0]+" [1]: "+curr_text[1]);
					var start=Math.max(0,infos["line_start"]-1)
					var end=Math.min(curr_text.length, infos["line_start"]+infos["line_nb"]-1);
					
					//curr_text[start]= curr_text[start].substr(0,infos["curr_pos"]-1) +"¤_overline_¤"+ curr_text[start].substr(infos["curr_pos"]-1);
					for(i=start; i< end; i++){
						//content+= previous_content[i]+"<br>";					
						new_line= curr_text[i];						
						new_line= new_line.replace(/((\n?)([^\t\n]*)\t)/gi, this.smartTab);		
						new_line= new_line.replace(/&/g,"&amp;");
						new_line= new_line.replace(/</g,"&lt;");
						new_line= new_line.replace(/>/g,"&gt;");
						
						new_line= new_line.replace(/ /g,"&nbsp;");
						content+=new_line+"<br>";
					}
					/*this.textarea.selectionStart;
					this.textarea.selectionEnd;		*/
					//document.getElementById("selection_field").innerHTML=content.substr(0,infos["curr_pos"]-1) +"<span class='overline'>"+ content.substr(infos["curr_pos"])+ "</span>";
					document.getElementById("selection_field").innerHTML=content.replace(/¤_overline_¤/, "<span class='overline'>");
					this.startMajArea();
				}
				//document.getElementById("line").value="Curseur: "+infos["line_start"]+" nb_line: "+ infos["line_nb"]+ " new_top: "+new_top+" new_width: "+new_width+" new_height: "+new_height+" this.lineHeight: "+document.getElementById("content_highlight").style.lineHeight;
				/*document.getElementById("line").value+="\n container: "+document.getElementById("container").offsetWidth;
				document.getElementById("line").value+="\n result: "+document.getElementById("result").offsetWidth;
				document.getElementById("line").value+="\n src: "+this.textarea.offsetWidth;*/
					
			}
			
			if(infos["line_start"] != this.last_selection["line_start"] || infos["curr_pos"] != this.last_selection["curr_pos"]){
				// move _cursor_pos
				var selec_char= infos["curr_line"].charAt(infos["curr_pos"]-1);
				var no_real_move=true;
				if(infos["line_nb"]==1 && (this.assocBracket[selec_char] || this.revertAssocBracket[selec_char]) ){
					
					no_real_move=false;					
					//findEndBracket(infos["line_start"], infos["curr_pos"], selec_char);
					if(this.findEndBracket(infos, selec_char) === true){
						document.getElementById("end_bracket").style.visibility="visible";
						document.getElementById("cursor_pos").style.visibility="visible";
					}else{
						document.getElementById("end_bracket").style.visibility="hidden";
						document.getElementById("cursor_pos").style.visibility="hidden";
					}
				}else{
					document.getElementById("cursor_pos").style.visibility="hidden";
					document.getElementById("end_bracket").style.visibility="hidden";
				}
				this.displayToCursorPosition("cursor_pos", infos["line_start"], infos["curr_pos"]-1, infos["curr_line"], no_real_move);
				if(infos["line_nb"]==1)
					this.scroll_to_view();
			}
			//document.getElementById("line").value="end";
			//posLeft=infos["curr_line"].substr(0, infos["curr_pos"] -1).replace(/\t/g,"        ").length*8 + 45;
			//document.getElementById("line").value="Len: "+infos["curr_line"].substr(0, infos["curr_pos"]).replace(/\t/g,"        ").length +" cur: "+infos["curr_pos"]+ " char: "+selec_char;
			//document.getElementById("cursor_pos").style.left= posLeft+"px";		
			
			this.last_line_selected= infos["line_start"];
			this.last_selection_range= infos["line_nb"];
			this.last_selection=infos;
		}
		if(this.state=="loaded"){
			if(this.do_highlight==true)	//can slow down check speed when highlight mode is on
				setTimeout("editArea.checkLineSelection()", 100);
			else
				setTimeout("editArea.checkLineSelection()", 50);
		}
	}

	EditArea.prototype.getSelectionInfos= function(){
		var selections=new Array();
		selections["line_start"]=1;
		selections["line_nb"]=1;
		selections["full_text"]= this.textarea.value;
		selections["curr_pos"]=0;
		selections["curr_line"]="";
		selections["indexOfCursor"]=0;
		//return selections;	
		
		var splitTab=selections["full_text"].split("\n");
		var nbLine=Math.max(0, splitTab.length);		
		var nbChar=Math.max(0, selections["full_text"].length - (nbLine - 1));	// (remove \n caracters from the count)
		if(this.isIE)
			nbChar= nbChar - (nbLine -1);		// (remove \r caracters from the count)
		selections["nb_line"]=nbLine;		
		selections["nb_char"]=nbChar;		
		if(this.isIE)
			this.getIESelection();
		start=this.textarea.selectionStart;
		end=this.textarea.selectionEnd;		
		if(start>0){
			var str=selections["full_text"].substr(0,start);
			selections["curr_pos"]=str.length - str.lastIndexOf("\n");
			selections["line_start"]=str.split("\n").length;
		}
		if(end>start){
			selections["line_nb"]=selections["full_text"].substring(start,end).split("\n").length;
		}
		selections["indexOfCursor"]=this.textarea.selectionStart;		
		selections["curr_line"]=splitTab[Math.max(0,selections["line_start"]-1)];
		
		document.getElementById("nbLine").innerHTML= nbLine;		
		document.getElementById("nbChar").innerHTML= nbChar;		
		document.getElementById("linePos").innerHTML=selections["line_start"];
		document.getElementById("currPos").innerHTML=selections["curr_pos"];
		
		return selections;
	};
	
	// set IE position in Firfox mode (textarea.selectionStart and textarea.selectionEnd)
	EditArea.prototype.getIESelection= function(){	
		var range = document.selection.createRange();
		
		var stored_range = range.duplicate();
		stored_range.moveToElementText( this.textarea );
		stored_range.setEndPoint( 'EndToEnd', range );
		if(stored_range.parentElement() !=this.textarea)
			return;
	
		// the range don't take care of empty lines in the end of the selection
		var scrollTop=document.getElementById("result").scrollTop + document.body.scrollTop;
		
		var relative_top= range.offsetTop - calculeOffsetTop(this.textarea) + scrollTop;
		if(!this.back_compat_mode)
			relative_top+= document.documentElement.scrollTop;
		var line_start = Math.round((relative_top / this.lineHeight) +1);
		
		var line_nb=Math.round(range.boundingHeight / this.lineHeight);
					
		var range_start=stored_range.text.length - range.text.length;
		var tab=this.textarea.value.substr(0, range_start).split("\n");			
		range_start+= (line_start - tab.length)*2;		// add missing empty lines to the selection
		this.textarea.selectionStart = range_start;
		
		var range_end=this.textarea.selectionStart + range.text.length;
		tab=this.textarea.value.substr(0, range_start + range.text.length).split("\n");			
		range_end+= (line_start + line_nb - 1 - tab.length)*2;
		
		this.textarea.selectionEnd = range_end;
	};
	
	// select the text for IE (and take care of \r caracters)
	EditArea.prototype.setIESelection= function(){
		var nbLineStart=this.textarea.value.substr(0, this.textarea.selectionStart).split("\n").length - 1;
		var nbLineEnd=this.textarea.value.substr(0, this.textarea.selectionEnd).split("\n").length - 1;
		var range = document.selection.createRange();
		range.moveToElementText( this.textarea );
		range.setEndPoint( 'EndToStart', range );
		
		range.moveStart('character', this.textarea.selectionStart - nbLineStart);
		range.moveEnd('character', this.textarea.selectionEnd - nbLineEnd - (this.textarea.selectionStart - nbLineStart)  );
		range.select();
	};
	
	EditArea.prototype.tabSelection= function(){
		if(this.is_tabbing)
			return;
		this.is_tabbing=true;
		//infos=getSelectionInfos();
		//if( document.selection ){
		if( this.isIE )
			this.getIESelection();
		
		/* Insertion du code de formatage */
		var start = this.textarea.selectionStart;
		var end = this.textarea.selectionEnd;
		var insText = this.textarea.value.substring(start, end);
		
		/* Insert tabulation and ajust cursor position */
		var pos_start=0;
		var pos_end=0;
		if (insText.length == 0) {
			// if only one line selected
			this.textarea.value = this.textarea.value.substr(0, start) + "\t" + insText + this.textarea.value.substr(end);
			pos_start = start + 1;
			pos_end=pos_start;
		} else {
			start= Math.max(0, this.textarea.value.substr(0, start).lastIndexOf("\n")+1);
			endText=this.textarea.value.substr(end);
			startText=this.textarea.value.substr(0, start);
			tmp= this.textarea.value.substring(start, end).split("\n");
			insText= "\t"+tmp.join("\n\t");
			this.textarea.value = startText + insText + endText;
			pos_start = start;
			pos_end= this.textarea.value.indexOf("\n", startText.length + insText.length);
			if(pos_end==-1)
				pos_end=this.textarea.value.length;
			//pos = start + repdeb.length + insText.length + ;
		}
		this.textarea.selectionStart = pos_start;
		this.textarea.selectionEnd = pos_end;
		
		//if( document.selection ){
		if(this.isIE){
			this.setIESelection();
			setTimeout("editArea.is_tabbing=false;", 100);	// IE can accept to make 2 tabulation without a little break between both
		}else
			this.is_tabbing=false;	
		
  	};
	
	EditArea.prototype.invertTabSelection= function(){
		if(this.is_tabbing)
			return;
		this.is_tabbing=true;
		//infos=getSelectionInfos();
		//if( document.selection ){
		if(this.isIE)
			this.getIESelection();
		
		var start = this.textarea.selectionStart;
		var end = this.textarea.selectionEnd;
		var insText = this.textarea.value.substring(start, end);
		
		/* Tab remove and sorsor selecitona djust */
		var pos_start=0;
		var pos_end=0;
		if (insText.length == 0) {
			this.textarea.value = this.textarea.value.substr(0, start) + "\t" + insText + this.textarea.value.substr(end);
			pos_start = start + 1;
			pos_end=pos_start;
		} else {
			start= this.textarea.value.substr(0, start).lastIndexOf("\n")+1;
			endText=this.textarea.value.substr(end);
			startText=this.textarea.value.substr(0, start);
			tmp= this.textarea.value.substring(start, end).split("\n");
			insText="";
			for(i=0; i<tmp.length; i++){				
				for(j=0; j<this.tab_nb_char; j++){
					if(tmp[i].charAt(0)=="\t"){
						tmp[i]=tmp[i].substr(1);
						j=this.tab_nb_char;
					}else if(tmp[i].charAt(0)==" ")
						tmp[i]=tmp[i].substr(1);
				}		
				insText+=tmp[i];
				if(i<tmp.length-1)
					insText+="\n";
			}
			//insText+="_";
			this.textarea.value = startText + insText + endText;
			pos_start = start;
			pos_end= this.textarea.value.indexOf("\n", startText.length + insText.length);
			if(pos_end==-1)
				pos_end=this.textarea.value.length;
			//pos = start + repdeb.length + insText.length + ;
		}
		this.textarea.selectionStart = pos_start;
		this.textarea.selectionEnd = pos_end;
		
		//if( document.selection ){
		if(this.isIE){
			// select the text for IE
			this.setIESelection();
			setTimeout("editArea.is_tabbing=false;", 100);	// IE can accept to make 2 tabulation without a little break between both
		}else
			this.is_tabbing=false;
  	};
	
	EditArea.prototype.pressEnter= function(){	
		if(!this.line_selection)
			return false;
		if(this.isIE)
			this.getIESelection();
		var start=this.textarea.selectionStart;
		var end= this.textarea.selectionEnd;
		var start_last_line= Math.max(0 , this.textarea.value.substring(0, start-1).lastIndexOf("\n") + 1 );
		var begin_line= this.textarea.value.substring(start_last_line, start).replace(/^([ \t]*).*/gm, "$1");
		if(begin_line=="\n" || begin_line.length==0)
			return false;
			//begin_line="";
		if(this.isIE)
			begin_line="\r\n"+ begin_line;
		else
			begin_line="\n"+ begin_line;
	
		//alert(start_last_line+" strat: "+start +"\n"+this.textarea.value.substring(start_last_line, start)+"\n_"+begin_line+"_")
		this.textarea.value= this.textarea.value.substring(0, start) + begin_line + this.textarea.value.substring(end);
		//put the cursor after the last postion
		this.textarea.selectionStart= start + begin_line.length;
	//	if(this.isIE)	// for \r
	//		this.textarea.selectionStart++;
		this.textarea.selectionEnd= this.textarea.selectionStart;
		
		if(this.isIE)
			this.setIESelection();
		return true;
		
	};
	
	
	EditArea.prototype.findEndBracket= function(infos, bracket){
			
		var start=infos["indexOfCursor"];
		var normal_order=true;
		//curr_text=infos["full_text"].split("\n");
		if(this.assocBracket[bracket])
			endBracket=this.assocBracket[bracket];
		else if(this.revertAssocBracket[bracket]){
			endBracket=this.revertAssocBracket[bracket];
			normal_order=false;
		}	
		var end=-1;
		var nbBracketOpen=0;
		
		for(var i=start; i<infos["full_text"].length && i>=0; ){
			if(infos["full_text"].charAt(i)==endBracket){				
				nbBracketOpen--;
				if(nbBracketOpen<=0){
					//i=infos["full_text"].length;
					end=i;
					break;
				}
			}else if(infos["full_text"].charAt(i)==bracket)
				nbBracketOpen++;
			if(normal_order)
				i++;
			else
				i--;
		}
		
		//end=infos["full_text"].indexOf("}", start);
		if(end==-1)
			return false;	
		var endLastLine=infos["full_text"].substr(0, end).lastIndexOf("\n");		
		var line= infos["full_text"].substr(0, endLastLine).split("\n").length + 1;			
		var curPos= end - endLastLine;
		
		this.displayToCursorPosition("end_bracket", line, curPos, infos["full_text"].substring(endLastLine +1, end));
		return true;
	};
	
	EditArea.prototype.displayToCursorPosition= function(id, start_line, cur_pos, lineContent, no_real_move){
	
		var elem=document.getElementById(id);
		var begin_line= lineContent.substr(0, cur_pos).replace(/((\n?)([^\t\n]*)\t)/gi, this.smartTab);
		var posLeft= 45 + begin_line.length* this.charWidth;
		var posTop=this.lineHeight * (start_line-1);
		if(isIE)
			posTop++;
		if(this.debug){
		/*	document.getElementById("line").value="line: "+start_line+ " carPos: "+cur_pos+" top: "+posTop+" left: "+posLeft+" \nlineStart: "+ lineContent;
			document.getElementById("line").value+="\n  area_scrollTop: "+document.getElementById("result").scrollTop+"  area_scrollLeft: "+document.getElementById("result").scrollLeft+""
													+"\n offset_w: "+document.getElementById("result").offsetWidth+" offset_h: "+document.getElementById("result").offsetHeight
													+"\n client_w: "+document.getElementById("result").clientWidth+" client_h: "+document.getElementById("result").clientHeight;
			
		*/}
		if(no_real_move!=true){	// when the cursor is hidden no need to move him
			document.getElementById(id).style.top=posTop+"px";
			document.getElementById(id).style.left=posLeft+"px";		
		}
		// usefull for smarter scroll
		document.getElementById(id).cursor_top=posTop;
		document.getElementById(id).cursor_left=posLeft;
		
	//	document.getElementById(id).style.marginLeft=posLeft+"px";
		
	};
	
	
	EditArea.prototype.area_select= function(start, length){
		this.textarea.focus();
		
		start= Math.max(0, Math.min(this.textarea.value.length, start));
		length= Math.max(0, Math.min(this.textarea.value.length-start, length));
		if(this.isOpera)	// Opera can't select 0 caracters...
			length= Math.max(1, length);
		if(this.isOpera && start > this.textarea.selectionEnd){	// Opera can't set selectionEnd before selectionStart
			this.textarea.selectionEnd = start + length;	
			this.textarea.selectionStart = start;				
		}else{
			this.textarea.selectionStart = start;
			this.textarea.selectionEnd = start+ length;		
		}
		//if( document.selection ){
		if(this.isIE){
			// select the text for IE (and take care of \r caracters)			
			nbLineStart= this.textarea.value.substr(0, this.textarea.selectionStart).split("\n").length - 1;
			nbLineEnd= this.textarea.value.substr(0, this.textarea.selectionEnd).split("\n").length - 1;
			var range = document.selection.createRange();
			range.moveToElementText( this.textarea );
			range.setEndPoint( 'EndToStart', range );
			
			range.moveStart('character', this.textarea.selectionStart - nbLineStart);
			range.moveEnd('character', this.textarea.selectionEnd - nbLineEnd - (this.textarea.selectionStart - nbLineStart)  );
			range.select();
		}	
	};
	
	
	EditArea.prototype.area_getSelection= function(){
		var text="";
		if( document.selection ){
			var range = document.selection.createRange();
			text=range.text;
		}else{
			text= this.textarea.value.substring(this.textarea.selectionStart, this.textarea.selectionEnd);
		}
		return text;			
	};
	
		
	EditArea.prototype.startResizeArea= function(e){
		editArea.before_resize_infos= editArea.getSelectionInfos();
		
		document.onmouseup= editArea.endResizeArea;
		document.onmousemove= editArea.resizeArea;
		editArea.isResizing=true;
		editArea.resize_start_mouse_x= getMouseX(e);
		editArea.resize_start_mouse_y= getMouseY(e);
		editArea.resize_start_width=document.getElementById("edit_area").offsetWidth;
		editArea.resize_start_height=document.getElementById("edit_area").offsetHeight;
		//editArea.resize_start_inner_height= document.getElementById("result").offsetHeight +2;
		
		if(!editArea.isIE){	// remove border width
			editArea.resize_start_height-=2;
			editArea.resize_start_width-=2;	
		}else if(!editArea.back_compat_mode){
			editArea.resize_start_height-=2;
			editArea.resize_start_width-=2;
		}
				
		editArea.resize_new_width= editArea.resize_start_width;
		editArea.resize_new_height= editArea.resize_start_height;
		//alert(resize_start_width);
		document.getElementById("edit_area").style.display="none";
		document.getElementById("cursor_pos").style.display="none";
		document.getElementById("end_bracket").style.display="none";
		
		document.getElementById("resize_hidden_field").style.width= editArea.resize_start_width+"px";
		document.getElementById("resize_hidden_field").style.height= editArea.resize_start_height+"px";
		document.getElementById("resize_hidden_field").style.display="block";
		setTimeout("editArea.scrollBody()", 50);
		return false;
	};
	
	EditArea.prototype.endResizeArea= function(e){
		editArea.isResizing=false;
		document.onmouseup="";
		document.onmousemove="";		
		document.getElementById("resize_hidden_field").style.display="none";
		
		document.getElementById("edit_area").style.display="block";
		editArea.resize_new_width= Math.max(editArea.min_width, editArea.resize_new_width);
		
		var w= editArea.resize_new_width;
		if(editArea.isIE && editArea.back_compat_mode)
			w=w-2;
			
		document.getElementById("result").style.width= w+"px";
		document.getElementById("edit_area").style.width= w+"px";
		resize_new_height= Math.max(editArea.min_height, editArea.resize_new_height);
		
	//	var h =editArea.resize_new_height  - (editArea.resize_start_height - editArea.resize_start_inner_height) ;
		var h =editArea.resize_new_height  - editArea.toolbars_height -2;
		/*if(editArea.isGecko)
			editArea.resize_new_height-=4;*/
	/*	if(editArea.isFirefox)
			h+=4;*/
			
		document.getElementById("result").style.height= h+"px";
		document.getElementById("edit_area").style.height= editArea.resize_new_height+"px";
			
		document.getElementById("cursor_pos").style.display="block";
		document.getElementById("end_bracket").style.display="block";
		editArea.textarea.focus();
		editArea.area_select( editArea.before_resize_infos["indexOfCursor"], 0);
		editArea.before_resize_infos["indexOfCursor"]= new Array();
		
		return false;
	};
	
	// can take an event or direct mouse coordinates
	EditArea.prototype.resizeArea= function(e, new_x, new_y){
		if(new_x && new_y){
			editArea.resize_mouse_x= new_x;
			editArea.resize_mouse_y= new_y;
		}else{
			editArea.resize_mouse_x= getMouseX(e);
			editArea.resize_mouse_y= getMouseY(e);
		}	
		if(editArea.allow_resize=="both" || editArea.allow_resize=="x"){
			editArea.resize_new_width= Math.max(editArea.min_width, editArea.resize_start_width + editArea.resize_mouse_x - editArea.resize_start_mouse_x);
			document.getElementById("resize_hidden_field").style.width= editArea.resize_new_width+"px";
		}
		if(editArea.allow_resize=="both" || editArea.allow_resize=="y"){
			editArea.resize_new_height= Math.max(editArea.min_height, editArea.resize_start_height + editArea.resize_mouse_y - editArea.resize_start_mouse_y);
			document.getElementById("resize_hidden_field").style.height= editArea.resize_new_height+"px";
		}
		return false;
	};
	
	EditArea.prototype.scrollBody= function(){	// don't work for IE with back_compat_mode == false (if there is a doctype)
		if(!editArea.isResizing)
			return;
		var scroll_top=0;
		var scroll_left=0;
		var new_x=editArea.resize_mouse_x;
		var new_y=editArea.resize_mouse_y;
		var diff_top= 500;
		if(this.isIE){
			if(this.back_compat_mode){
				scroll_top= document.body.scrollTop;
				scroll_left= document.body.scrollLeft;
				
			}else{
				scroll_top= document.documentElement.scrollTop;
				scroll_left= document.documentElement.scrollLeft;
			}
			diff_top= document.body.clientHeight + scroll_top - editArea.resize_mouse_y;
		}else{
			scroll_top=window.pageYOffset;
			scroll_left=window.pageXOffset;
			diff_top= window.innerHeight + scroll_top - editArea.resize_mouse_y;
		}
		
		
		if(diff_top < 25){
			var add_top=Math.ceil((25- diff_top)/2);
			if(this.back_compat_mode)
				document.body.scrollTop=scroll_top+ add_top;
			else
				document.documentElement.scrollTop=scroll_top+ add_top;
			new_y=editArea.resize_mouse_y+ add_top;
		}
		
		var diff_left= document.body.clientWidth + scroll_left - editArea.resize_mouse_x;
		if(diff_left < 25){
			var add_left=Math.ceil((25- diff_left)/2);
			if(this.back_compat_mode)
				document.body.scrollLeft=scroll_left+ add_left;
			else
				document.documentElement.scrollLeft=scroll_left+ add_left;
			new_x=editArea.resize_mouse_x+ add_left;
		}
		
		/*window.status="x: "+editArea.resize_mouse_x+" y: "+editArea.resize_mouse_y+" b_h: "+document.body.clientHeight+" s_t: "+scroll_top+ " rest: "+(document.body.clientHeight + scroll_top -editArea.resize_mouse_y)+""
					 +" ("+window.innerHeight +"+"+ scroll_top +"-"+editArea.resize_mouse_y+")"+""+ " rest left: "+(document.body.clientWidth + scroll_left -editArea.resize_mouse_x)+""
					 +" ("+document.body.clientWidth +"+"+ scroll_left +"-"+editArea.resize_mouse_x+")"+""
					 +" d_h: "+document.body.scrollHeight+ " diff_top: "+diff_top+""
					 +" new_y: "+new_y+" prev_y "+editArea.resize_mouse_y+ "scrool_h: "+document.body.scrollHeight;
		*/
		if(new_x!=editArea.resize_mouse_x || new_y!=editArea.resize_mouse_y){
			editArea.resizeArea("", new_x, new_y);			
		}
		
		setTimeout("editArea.scrollBody()", 30);
	};

	
	
	
	EditArea.prototype.set_font= function(family, size){
	//	document.getElementById("test_area").style.font="10pt courier";
	//	document.getElementById("test_area").style.backgroundColor="#987654";
		//setAttribute(document.getElementById("test_area"), "style", "font: 10pt courier;");
		var elems= new Array(this.id, "content_highlight", "edit_area_test_font_size", "cursor_pos", "end_bracket", "selection_field", "line_number");
		if(family && family!="")
			this.font_family= family;
		if(size && size>0)
			this.font_size=size;
		if(!this.isIE && !this.isNS){	
			var elem_font=document.getElementById("area_font_size");		
			for(var i=0; i < elem_font.length; i++){
				if(elem_font.options[i].value && elem_font.options[i].value == this.font_size)
						elem_font.options[i].selected=true;
			}
		}
				
		//this.lineHeight= Math.floor(this.font_size*1.6);
		/*if(this.isNS && !this.isIE)
			this.lineHeight= Math.floor((this.font_size)*1.5)-1;*/
		/*else*/
		if(this.isOpera || this.isSafari){	// bad solution!!!!
			this.lineHeight= Math.floor(this.font_size*1.6);
			this.charWidth=Math.ceil(this.lineHeight/2);
			if(size==8)
				this.charWidth++;		
		}
		for( var i in elems){
			var elem=	document.getElementById(elems[i]);	
			if(this.isOpera || this.isSafari){	// opera doesn't support style modification for textarea by elem.style...= value;
				//alert("set");
				setAttribute(elem, "style", getAttribute(elem, "style") +";font-size: "+this.font_size+"pt;font-family: "+this.font_family+";line-height: "+this.lineHeight+"px;");	
				//setAttribute(elem, "style", getAttribute(elem, "style") +";font-size: "+this.font_size+"pt;font-family: "+this.font_family+"; ");	
			}else{
				document.getElementById(elems[i]).style.fontFamily= ""+this.font_family;
				document.getElementById(elems[i]).style.fontSize= this.font_size+"pt";
				//document.getElementById(elems[i]).style.lineHeight= this.lineHeight+"px";
				//document.getElementById(elems[i]).style.lineHeight= "14pt";
			}
		}
		
		//alert(	getAttribute(document.getElementById("edit_area_test_font_size"), "style"));
		if(!this.isOpera){
			document.getElementById("edit_area_test_font_size").innerHTML="0";	
			this.charWidth= document.getElementById('edit_area_test_font_size').offsetWidth;
			this.lineHeight= document.getElementById("edit_area_test_font_size").offsetHeight;
		}
		if(this.isIE){
			// IE have a fixed size for tabulation and not a given number of caracters
		/*	document.getElementById("edit_area_test_font_size").innerHTML="\t0";		
			this.charWidth= (document.getElementById("edit_area_test_font_size").offsetWidth / this.charWidth) -1;*/
		}
		//alert("font "+this.textarea.style.font);
		// force update of selection field
		this.last_line_selected=-1;
		if(this.state=="loaded"){
			this.textarea.focus();
			this.textareaFocused=true;
		}
		this.last_selection["indexOfCursor"]=-1;
		this.last_selection["curr_pos"]=-1;					
		//alert("line_h"+ this.lineHeight + " char width: "+this.charWidth+ " this.id: "+this.id+ "(size: "+size+")");
	};
	
	EditArea.prototype.change_font_size= function(){
		var size=document.getElementById("area_font_size").value;
		if(size>0)
			this.set_font("", size);
			
	};
	
	
	EditArea.prototype.open_inline_popup= function(popup_id){
		this.close_all_inline_popup();
		var popup= document.getElementById(popup_id);		
		var area= document.getElementById("edit_area");
		
		// search matching icon
		for(var i in this.inlinePopup){
			if(this.inlinePopup[i]["popup_id"]==popup_id){
				var icon= document.getElementById(this.inlinePopup[i]["icon_id"]);
				if(icon){
					this.switchClassSticky(icon, 'editAreaButtonSelected', true);			
					break;
				}
			}
		}
		if(!popup.postionned){
			var new_left= calculeOffsetLeft(area) + area.offsetWidth /2 - popup.offsetWidth /2;
			var new_top= calculeOffsetTop(area) + area.offsetHeight /2 - popup.offsetHeight /2;
			//var new_top= area.offsetHeight /2 - popup.offsetHeight /2;
			//var new_left= area.offsetWidth /2 - popup.offsetWidth /2;
			//alert("new_top: ("+new_top+") = calculeOffsetTop(area) ("+calculeOffsetTop(area)+") + area.offsetHeight /2("+ area.offsetHeight /2+") - popup.offsetHeight /2("+popup.offsetHeight /2+") - scrollTop: "+document.body.scrollTop);
			popup.style.left= new_left+"px";
			popup.style.top= new_top+"px";
			popup.postionned=true;
		}
		popup.style.visibility="visible";
		
		//popup.style.display="block";
	}

	EditArea.prototype.close_inline_popup= function(popup_id){
		var popup= document.getElementById(popup_id);		

		// search matching icon
		for(var i in this.inlinePopup){
			if(this.inlinePopup[i]["popup_id"]==popup_id){
				var icon= document.getElementById(this.inlinePopup[i]["icon_id"]);
				if(icon){
					this.switchClassSticky(icon, 'editAreaButtonNormal', false);			
					break;
				}
			}
		}
		
		popup.style.visibility="hidden";	
	}
	
	EditArea.prototype.close_all_inline_popup= function(e){
		for(var i in this.inlinePopup){
			this.close_inline_popup(this.inlinePopup[i]["popup_id"]);		
		}
		this.textarea.focus();
	};
	
	EditArea.prototype.show_help= function(){
		this.open_inline_popup("edit_area_help");
	};
			
	EditArea.prototype.new_document= function(){
		this.textarea.value="";
		this.area_select(0,0);
	};
	
	EditArea.prototype.get_all_toolbar_height= function(){
		var area= document.getElementById("edit_area");
		var results=getChildren(area, "div", "class", "area_toolbar", "all", "0");	// search only direct children
		results= results.concat(getChildren(area, "table", "class", "area_toolbar", "all", "0"));
		var height=0;
		for(var i in results){			
			height+= results[i].offsetHeight;
		}
		//alert("toolbar height: "+height);
		return height;
	};
	
	EditArea.prototype.go_to_line= function(){		
		var icon= document.getElementById("go_to_line_icon");
		if(icon != null){
			this.restoreClass(icon);
			this.switchClassSticky(icon, 'editAreaButtonSelected', true);
		}
		
		var line= prompt(this.getLang("go_to_line_prompt"), "");
		if(line && line!=null && line.search(/^[0-9]+$/)!=-1){
			var start=0;
			var lines= this.textarea.value.split("\n");
			if(line > lines.length)
				start= this.textarea.value.length;
			else{
				for(var i=0; i< Math.min(line-1, lines.length); i++)
					start+= lines[i].length + 1;
			}
			this.area_select(start, 0);
		}
		if(icon != null)
			this.switchClassSticky(icon, 'editAreaButtonNormal', false);
		
	};
	
	
	EditArea.prototype.change_line_selection_mode= function(setTo){
		//alert("setTo: "+setTo);
		if(setTo != null){
			if(setTo === false)
				this.line_selection=true;
			else
				this.line_selection=false;
		}
		var icon= document.getElementById("change_line_selection_icon");
		this.textarea.focus();
		if(this.line_selection===true){
			//setAttribute(icon, "class", getAttribute(icon, "class").replace(/ selected/g, "") );
			/*setAttribute(icon, "oldClassName", "editAreaButtonNormal" );
			setAttribute(icon, "className", "editAreaButtonNormal" );*/
			//this.restoreClass(icon);
			//this.restoreAndSwitchClass(icon,'editAreaButtonNormal');
			this.switchClassSticky(icon, 'editAreaButtonNormal', false);
			
			this.line_selection=false;
			document.getElementById("selection_field").style.display= "none";
			document.getElementById("cursor_pos").style.display= "none";
			document.getElementById("end_bracket").style.display= "none";
		}else{
			//setAttribute(icon, "class", getAttribute(icon, "class") + " selected");
			//this.switchClass(icon,'editAreaButtonSelected');
			this.switchClassSticky(icon, 'editAreaButtonSelected', false);
			this.line_selection=true;
			document.getElementById("selection_field").style.display= "block";
			document.getElementById("cursor_pos").style.display= "block";
			document.getElementById("end_bracket").style.display= "block";
		}	
	};
	
	// the auto scroll of the textarea has some lacks when it have to show cursor in the visible area when the textarea size change
	EditArea.prototype.scroll_to_view= function(){
		if(!this.line_selection)
			return;
		if(this.isOpera){
			//alert(this.textarea.scrollLeft);
			/*res=document.getElementById("result");
			document.getElementById("line").value="offsetLeft: "+calculeOffsetLeft(this.textarea)+" scroll"+this.textarea.scrollLeft+ " width: "+ this.textarea.offsetWidth;
			res.scrollLeft=0;
			this.textarea.scrollTop=0;
			this.textarea.scrollLeft=0;
			this.textarea.style.left="0px";*/
		}
	/*	if(this.isIE){
			
			this.textarea.scrollTop=0;
			this.textarea.scrollLeft=0;
		}*/
		var zone= document.getElementById("result");
		
		//var cursor_pos_top= parseInt(document.getElementById("cursor_pos").style.top.replace("px",""));
		var cursor_pos_top= document.getElementById("cursor_pos").cursor_top;
		var max_height_visible= zone.clientHeight + zone.scrollTop;
		var miss_top= cursor_pos_top + this.lineHeight - max_height_visible;
		if(miss_top>0){
			zone.scrollTop= zone.scrollTop + miss_top;
		}else if( zone.scrollTop > cursor_pos_top){
			// when erase all the content -> does'nt scroll back to the top
			zone.scrollTop= cursor_pos_top;	 
		}
		//var cursor_pos_left= parseInt(document.getElementById("cursor_pos").style.left.replace("px",""));
		var cursor_pos_left= document.getElementById("cursor_pos").cursor_left;
		var max_width_visible= zone.clientWidth + zone.scrollLeft;
		var miss_left= cursor_pos_left + this.charWidth - max_width_visible;
		if(miss_left>0){			
			zone.scrollLeft= zone.scrollLeft + miss_left+ 50;
		}else if( zone.scrollLeft > cursor_pos_left){
			zone.scrollLeft= cursor_pos_left;
		}else if( zone.scrollLeft == 45){
			// show the line numbers if textarea align to it's left
			zone.scrollLeft=0;
		}
		//if(miss_top> 0 || miss_left >0)
			//alert("miss top: "+miss_top+" miss left: "+miss_left);
	};
	
	EditArea.prototype.check_undo= function(){
		if(this.textareaFocused){
			var text=this.textarea.value;
			if(this.previous.length<1)
				this.switchClassSticky(document.getElementById("undo_icon"), 'editAreaButtonDisabled', true);
			/*var last= 0;
			for( var i in this.previous){
				last=i;
			}*/
			if(this.previous[this.previous.length-1] != text){
				this.previous.push(text);
				if(this.previous.length > this.max_undo+1)
					this.previous.shift();
			}
			if(this.previous.length == 2)
				this.switchClassSticky(document.getElementById("undo_icon"), 'editAreaButtonNormal', false);
		}
			//if(this.previous[0] == text)	
		if(this.state=="loaded")		
			setTimeout("editArea.check_undo()", 1000);
	};
	
	EditArea.prototype.undo= function(){
		//alert("undo"+this.previous.length);
		if(this.previous.length > 0){
			var pos_cursor=this.getSelectionInfos()["indexOfCursor"];
			this.next.push(this.textarea.value);
			var text= this.previous.pop();
			if(text==this.textarea.value && this.previous.length > 0)
				text=this.previous.pop();						
			this.textarea.value= text;
			this.area_select(pos_cursor, 0);
			this.switchClassSticky(document.getElementById("redo_icon"), 'editAreaButtonNormal', false);
			//alert("undo"+this.previous.length);
		}
	};
	
	EditArea.prototype.redo= function(){
		if(this.next.length > 0){
			var pos_cursor=this.getSelectionInfos()["indexOfCursor"];
			var text= this.next.pop();
			this.previous.push(this.textarea.value);
			this.textarea.value= text;
			this.area_select(pos_cursor, 0);
			this.switchClassSticky(document.getElementById("undo_icon"), 'editAreaButtonNormal', false);
			
		}
		if(	this.next.length == 0)
			this.switchClassSticky(document.getElementById("redo_icon"), 'editAreaButtonDisabled', true);
	};
	
	EditArea.prototype.switchClass = function(element, class_name, lock_state) {
		var lockChanged = false;
	
		if (typeof(lock_state) != "undefined" && element != null) {
			element.classLock = lock_state;
			lockChanged = true;
		}
	
		if (element != null && (lockChanged || !element.classLock)) {
			element.oldClassName = element.className;
			element.className = class_name;
		}
	};
	
	EditArea.prototype.restoreAndSwitchClass = function(element, class_name) {
		if (element != null && !element.classLock) {
			this.restoreClass(element);
			this.switchClass(element, class_name);
		}
	};
	
	EditArea.prototype.restoreClass = function(element) {
		if (element != null && element.oldClassName && !element.classLock) {
			element.className = element.oldClassName;
			element.oldClassName = null;
		}
	};
	
	EditArea.prototype.setClassLock = function(element, lock_state) {
		if (element != null)
			element.classLock = lock_state;
	};
	
	EditArea.prototype.switchClassSticky = function(element, class_name, lock_state) {
		var lockChanged = false;
	
	/*	// Performance issue
		if (!this.stickyClassesLookup[element_name])
			this.stickyClassesLookup[element_name] = document.getElementById(element_name);
	
	//	element = document.getElementById(element_name);
		element = this.stickyClassesLookup[element_name];*/
	
		if (typeof(lock_state) != "undefined" && element != null) {
			element.classLock = lock_state;
			lockChanged = true;
		}
	
		if (element != null && (lockChanged || !element.classLock)) {
			element.className = class_name;
			element.oldClassName = class_name;
	
			// Fix opacity in Opera
			if (this.isOpera) {
				if (class_name == "mceButtonDisabled") {
					var suffix = "";
	
					if (!element.mceOldSrc)
						element.mceOldSrc = element.src;
	
					if (this.operaOpacityCounter > -1)
						suffix = '?rnd=' + this.operaOpacityCounter++;
	
					element.src = this.baseURL + "/images/opacity.png" ;
					element.style.backgroundImage = "url('" + element.mceOldSrc + "')";
				} else {
					if (element.mceOldSrc) {
						element.src = element.mceOldSrc;
						element.parentNode.style.backgroundImage = "";
						element.mceOldSrc = null;
					}
				}
			}
		}
	};	// need to redifine this functiondue to IE problem
	function getAttribute( elm, aname ) {
		try{
			var avalue = elm.getAttribute( aname );
		}catch(exept){
		
		}
		if ( ! avalue ) {
			for ( var i = 0; i < elm.attributes.length; i ++ ) {
				var taName = elm.attributes [i] .name.toLowerCase();
				if ( taName == aname ) {
					avalue = elm.attributes [i] .value;
					return avalue;
				}
			}
		}
		return avalue;
	}
	
	// need to redifine this functiondue to IE problem
	function setAttribute( elm, attr, val ) {
		if(attr=="class"){
			elm.setAttribute("className", val);
			elm.setAttribute("class", val);
		}else{
			elm.setAttribute(attr, val);
		}
	}
	
	/* return a child element
		elem: element we are searching in
		elem_type: type of the eleemnt we are searching (DIV, A, etc...)
		elem_attribute: attribute of the searched element that must match
		elem_attribute_match: value that elem_attribute must match
		option: "all" if must return an array of all children, otherwise return the first match element
		depth: depth of search (-1 or no set => unlimited)
	*/
	function getChildren(elem, elem_type, elem_attribute, elem_attribute_match, option, depth){
		
		if(!option)
			var option="single";
		if(depth ==null)
			var depth=-1;
	//	alert("depth:"+depth);
		if(elem){
			var children= elem.childNodes;
			var result=null;
			var results= new Array();
		//	alert("level: "+level+" elem: "+elem+" nb_child: "+children.length);
			for (var x=0;x<children.length;x++) {
		//		alert("level: "+level+" "+x+"/"+children.length+": elem: "+children[x]+" nb_child: "+children.length);
				strTagName = new String(children[x].tagName);
				children_class="?";
				if(strTagName!= "undefined"){
					children_class= getAttribute(children[x],elem_attribute);
				//	alert("tag: "+strTagName+" chidl: "+children[x]);
					if(strTagName.toLowerCase()==elem_type.toLowerCase() && (elem_attribute=="" ||children_class==elem_attribute_match)){
				//		alert("level: "+level+" "+"found "+children[x]);
						if(option=="all"){
							results.push(children[x]);
						}else{
							return children[x];
						}
					}
					if(option=="all" && depth!=0){
						//alert("search Child For: "+strTagName+ " class: "+children_class+" depth: "+depth);
						result=getChildren(children[x], elem_type, elem_attribute, elem_attribute_match, option, depth-1);
						if(option=="all"){
							if(result.length>0){
							//	alert("found2 "+result);							
								results= results.concat(result);
							}
						}else if(result!=null){												
							return result;
						}
					}
				}
				//alert("not match tag: "+strTagName+ " class: "+children_class);
			}
			if(option=="all")
				return results;
		}
	//	alert("level: "+level+" "+" not found in : "+elem+" nb_child: "+children.length);					
		return null;
	}	
	
	function isChildOf(elem, parent){
		if(elem){
			if(elem==parent)
				return true;
			while(elem.parentNode != 'undefined'){
				return isChildOf(elem.parentNode, parent);
			}
		}
		return false;
	}
	
	function getMouseX(e){
		/*if(document.all)
			return event.x + document.body.scrollLeft;
		else
			return e.pageX;*/
		return (navigator.appName=="Netscape") ? e.pageX : event.x + document.body.scrollLeft;
	}
	
	function getMouseY(e){
		/*if(document.all)
			return event.y + document.body.scrollTop;
		else
			return e.pageY;*/
		return (navigator.appName=="Netscape") ? e.pageY : event.y + document.body.scrollTop;
	}
	
	function calculeOffsetLeft(r){
	  return calculeOffset(r,"offsetLeft")
	}
	
	function calculeOffsetTop(r){
	  return calculeOffset(r,"offsetTop")
	}
	
	function calculeOffset(element,attr){
	  var offset=0;
	  while(element){
		offset+=element[attr];
		element=element.offsetParent
	  }
	  return offset;
	}
	
	
	var move_current_element="";
	function start_move_element(e, id){
		var elem_id=(e.target || e.srcElement).id;
		if(id)
			elem_id=id;		
		//alert(e.toString()+ (e.target || e.srcElement).id);
		move_current_element= document.getElementById(elem_id);
		move_current_element.onmousemove= move_element;
		move_current_element.onmouseup= end_move_element;
		
		mouse_x= getMouseX(e);
		mouse_y= getMouseY(e);
		move_current_element.start_pos_x = mouse_x - (move_current_element.style.left.replace("px","") || calculeOffsetLeft(move_current_element));
		move_current_element.start_pos_y = mouse_y - (move_current_element.style.top.replace("px","") || calculeOffsetTop(move_current_element));
		//alert("startmove" +move_current_element.style.top.replace("px",""));
	}
	
	function end_move_element(e){
		move_current_element.onmousemove= "";
		move_current_element.onmouseup= "";
		move_current_element="";
	}
	
	function move_element(e){
		var mouse_x=getMouseX(e);
		var mouse_y=getMouseY(e);
		var new_top= mouse_y - move_current_element.start_pos_y;
		var new_left= mouse_x - move_current_element.start_pos_x;
		move_current_element.style.top= new_top+"px";
		move_current_element.style.left= new_left+"px";		
		return false;
	}
	
	
	/* for debug purpose*/
	
	function count_children(elem, level){
		//elem.normalize();
		children= elem.childNodes;
		if(!level)
			level=0;
		if(level==0)		
			return children.length;
		else{
			var count= children.length;
			for(i in children)
				count+= count_children(elem, level-1);
			return count;
		}
	}
	
	function count_child_type(elem, level){

		var new_node= elem.cloneNode(false);
		var types= new Array();
		children= elem.childNodes;
	//	var error="\nerror for "+elem;
		if(children){
			error+="\n\t nb child before:"+ elem.childNodes.length;
			for(i=0; i< children.length; i++){
				if(children[i].nodeType && children[i].nodeType>=1 && children[i].nodeType <= 12 ){
					//if(children[i].nodeType>0)
					
					if(types[children[i].nodeType])
						types[children[i].nodeType]++;	
					else
						types[children[i].nodeType]=1;
					//new_node.appendChild(children[i]);
					// clone the "hiddenContent" element and assign it to the "newContent" variable
					newContent = children[i].cloneNode(true);
					// clear the contents of your destination element.
					//so_clearInnerHTML(document.getElementById("mContainer"));
					// append the cloned element to the destination element
					new_node.appendChild(newContent);
				}else{
					error+="\nbad children: ";
					new_node.removeChild(children[i]);			
				}
				
					//elem.removeChild(children[i]);
			}
			error+= "  nb child after :"+ new_node.childNodes.length+ " level: "+level;
			if(!level)
				level=0;
			if(level!=0){
				
				for(var i=0; i<new_node.childNodes.length; i++){
					count_child_type(new_node.childNodes[i], level-1);
				//	if(new_node.childNodes[i].nodeType>0){
					//	types= types.concat(count_child_type(new_node.childNodes[i], level-1));
							
				//	}
				}
			}			
		}
	//	elem= new_node;
		document.getElementById("src").value+=error;
		
		
		// insert the cloned object into the DOM before the original one
		elem.parentNode.insertBefore(new_node,elem);
		// remove the original object
		//elem.parentNode.removeChild(elem);
		
		//new_node.parentNode= elem.parentNode;
		//elem.parentNode.replaceChild(new_node, elem);
		//alert(error);
		return types;
	}
	
	
	/*isMSIE = (navigator.appName == "Microsoft Internet Explorer");
		isMSIE5 = this.isMSIE && (ua.indexOf('MSIE 5') != -1);
		this.isMSIE5_0 = this.isMSIE && (ua.indexOf('MSIE 5.0') != -1);
		this.isGecko = ua.indexOf('Gecko') != -1;
		this.isSafari = ua.indexOf('Safari') != -1;
		this.isOpera = ua.indexOf('Opera') != -1;
	//	this.isMac = ua.indexOf('Mac') != -1;
		this.isNS7 = ua.indexOf('Netscape/7') != -1;
		this.isNS71 = ua.indexOf('Netscape/7.1') != -1;
		
		var date= new Date();
		var dend= date.getTime();
		if(dend-d1 >100)
			document.getElementById("line").value="end "+ (dend - d1)+ " middle a "+ (dmiddle- d1)+ " middle b "+ (dend-dmiddle);
		
		
		
		
		*/	EditArea.prototype.getRegExp= function(tab_text){
		//res="( |=|\\n|\\r|\\[|\\(|µ|)(";
		res="(\\b)(";
		for( i=0; i<tab_text.length; i++){
			if(i>0)
				res+="|";
			//res+="("+ tab_text[i] +")";
			//res+=tab_text[i].replace(/(\.|\?|\*|\+|\\|\(|\)|\[|\]|\{|\})/g, "\\$1");
			res+=this.get_reg_exp_str(tab_text[i]);
		}
		//res+=")( |\\.|:|\\{|\\(|\\)|\\[|\\]|\'|\"|\\r|\\n|\\t|$)";
		res+=")(\\b)";
		reg= new RegExp(res);
		
		return res;
	}
	
	EditArea.prototype.get_reg_exp_str= function(string){
		return string.replace(/(\.|\?|\*|\+|\\|\(|\)|\[|\]|\}|\{|\$)/g, "\\$1");
	}
	
	EditArea.prototype.initRegExp= function(){
		// get CSS rules
		var styles = this.isMSIE ? document.styleSheets : document.styleSheets;		
		var theRules = new Array();
		var style_index= document.styleSheets.length-1;
		if (document.styleSheets[style_index].cssRules)
			theRules = document.styleSheets[style_index].cssRules;
		else if (document.styleSheets[style_index].rules)
			theRules = document.styleSheets[style_index].rules;
			
		
		/*var style="";
		for(var i in theRules)
			style+="\n"+i+":"+ theRules[i];
		alert(theRules.length+"\n" +style);*/
			
		for(var lang in EditArea_language_data){
			this.code[lang]= new Array();
			this.code[lang]["keywords_reg_exp"]= new Array();
			this.keywords_reg_exp_nb=0;
			
			if(EditArea_language_data[lang]['KEYWORDS']){
				for(var i in EditArea_language_data[lang]['KEYWORDS']){
					this.code[lang]["keywords_reg_exp"][i]= new RegExp(this.getRegExp( EditArea_language_data[lang]['KEYWORDS'][i] ),"g");
					this.keywords_reg_exp_nb++;
				}
			}
			
			if(EditArea_language_data[lang]['OPERATORS']){
				var str="";
				for(var i in EditArea_language_data[lang]['OPERATORS']){
					str+=EditArea_language_data[lang]['OPERATORS'][i];
				}
				this.code[lang]["operators_reg_exp"]= new RegExp("(["+str+"])","g");
			}
			
			if(EditArea_language_data[lang]['DELIMITERS']){
				var str="";
				var nb=0;
				for(var i in EditArea_language_data[lang]['DELIMITERS']){
					if(nb>0)
						str+="|";
					str+=this.get_reg_exp_str(EditArea_language_data[lang]['DELIMITERS'][i]);
					nb++;
				}
				this.code[lang]["delimiters_reg_exp"]= new RegExp("("+str+")","g");
			}
			
			
	//		/(("(\\"|[^"])*"?)|('(\\'|[^'])*'?)|(//(.|\r|\t)*\n)|(/\*(.|\n|\r|\t)*\*/)|(<!--(.|\n|\r|\t)*-->))/gi
			this.code[lang]["quotes_list"]=new Array();
			var quote_tab= new Array();
			if(EditArea_language_data[lang]['QUOTEMARKS']){
				for(var i in EditArea_language_data[lang]['QUOTEMARKS']){				
					var x=this.get_reg_exp_str(EditArea_language_data[lang]['QUOTEMARKS'][i]);
					this.code[lang]["quotes_list"][this.code[lang]["quotes_list"].length]= x;
					quote_tab[quote_tab.length]="("+x+"(\\\\"+x+"|[^"+x+"])*"+x+"?)";				
				}			
			}
					
		
			if(EditArea_language_data[lang]['COMMENT_SINGLE']){
				for(var i in EditArea_language_data[lang]['COMMENT_SINGLE']){							
					var x=this.get_reg_exp_str(EditArea_language_data[lang]['COMMENT_SINGLE'][i]);
					quote_tab[quote_tab.length]="("+x+"(.|\\r|\\t)*\\n?)";
				}			
			}		
			// (/\*(.|[\r\n])*?\*/)
			if(EditArea_language_data[lang]['COMMENT_MULTI']){
				for(var i in EditArea_language_data[lang]['COMMENT_MULTI']){							
					var start=this.get_reg_exp_str(i);
					var end=this.get_reg_exp_str(EditArea_language_data[lang]['COMMENT_MULTI'][i]);
					quote_tab[quote_tab.length]="("+start+"(.|\\n|\\r)*?"+end+")";
				}			
			}		
			if(quote_tab.length>0)
				this.code[lang]["comment_or_quote_reg_exp"]= new RegExp("("+quote_tab.join("|")+")","gi");
				
			if(EditArea_language_data[lang]['SCRIPT_DELIMITERS']){
				this.code[lang]["script_delimiters"]= new Array();
				for(var i in EditArea_language_data[lang]['SCRIPT_DELIMITERS']){							
					this.code[lang]["script_delimiters"][i]= EditArea_language_data[lang]['SCRIPT_DELIMITERS'];
				}			
			}
			
			this.code[lang]["custom_regexp"]= new Array();
			if(EditArea_language_data[lang]['REGEXPS']){
				for(var i in EditArea_language_data[lang]['REGEXPS']){
					var val= EditArea_language_data[lang]['REGEXPS'][i];
					if(!this.code[lang]["custom_regexp"][val['execute']])
						this.code[lang]["custom_regexp"][val['execute']]= new Array();
					this.code[lang]["custom_regexp"][val['execute']][i]={'regexp' : new RegExp(val['search'], val['modifiers'])
																		, 'class' : val['class']};
				}
			}
			
			if(EditArea_language_data[lang]['STYLES']){							
				this.lang_style[lang]= new Array();

				for(var i in EditArea_language_data[lang]['STYLES']){
					if(typeof(EditArea_language_data[lang]['STYLES'][i]) != "string"){
						for(var j in EditArea_language_data[lang]['STYLES'][i]){
							this.lang_style[lang][j]= EditArea_language_data[lang]['STYLES'][i][j];
						}
					}else{
						this.lang_style[lang][i]= EditArea_language_data[lang]['STYLES'][i];
					}
				}
			}
			// load styles
			for(var i in this.lang_style[lang]){
				if(this.isIE){
					styles[style_index].addRule("."+i.toLowerCase()+" span", this.lang_style[lang][i],0);
					styles[style_index].addRule("."+i.toLowerCase(), this.lang_style[lang][i],0);
				}else{
					styles[style_index].insertRule("."+ i.toLowerCase() +" span{"+this.lang_style[lang][i]+"}", 0);
					styles[style_index].insertRule("."+ i.toLowerCase() +"{"+this.lang_style[lang][i]+"}", 0);
				}

			}
	
		}
	
				
	};
		/*EditArea.prototype.comment_or_quotes= function(v0, v1, v2, v3, v4,v5,v6,v7,v8,v9, v10){
		new_class="quotes";
		if(v6 && v6 != undefined && v6!="")
			new_class="comments";
		return "µ__"+ new_class +"__µ"+v0+"µ_END_µ";

	};*/
	
/*	EditArea.prototype.htmlTag= function(v0, v1, v2, v3, v4,v5,v6,v7,v8,v9, v10){
		res="<span class=htmlTag>"+v2;
		alert("v2: "+v2+" v3: "+v3);
		tab=v3.split("=");
		attributes="";
		if(tab.length>1){
			attributes="<span class=attribute>"+tab[0]+"</span>=";
			for(i=1; i<tab.length-1; i++){
				cut=tab[i].lastIndexOf("&nbsp;");				
				attributes+="<span class=attributeVal>"+tab[i].substr(0,cut)+"</span>";
				attributes+="<span class=attribute>"+tab[i].substr(cut)+"</span>=";
			}
			attributes+="<span class=attributeVal>"+tab[tab.length-1]+"</span>";
		}		
		res+=attributes+v5+"</span>";
		return res;		
	};*/
	
	EditArea.prototype.comment_or_quote= function(){
		var new_class="comments";
		for(var i in editArea.code[editArea.current_code_lang]["quotes_list"]){
			if(EditArea.prototype.comment_or_quote.arguments[0].indexOf(editArea.code[editArea.current_code_lang]["quotes_list"][i])==0)
				new_class="quotesmarks";
		}		
		return "µ__"+ new_class +"__µ"+EditArea.prototype.comment_or_quote.arguments[0]+"µ_END_µ";
	};
	
	EditArea.prototype.custom_highlight= function(){
		res= EditArea.prototype.custom_highlight.arguments[1]+"µ__"+ editArea.reg_exp_span_tag +"__µ" + EditArea.prototype.custom_highlight.arguments[2]+"µ_END_µ";
		if(EditArea.prototype.custom_highlight.arguments.length>5)
			res+= EditArea.prototype.custom_highlight.arguments[ EditArea.prototype.custom_highlight.arguments.length-3 ];
		return res;
	};
	
	EditArea.prototype.smartTab= function(){
		val="                   ";
		return EditArea.prototype.smartTab.arguments[2] + EditArea.prototype.smartTab.arguments[3] + val.substr(0, editArea.tab_nb_char - (EditArea.prototype.smartTab.arguments[3].length)%editArea.tab_nb_char);
	};
	
	
	EditArea.prototype.colorizeText= function(text){
		//text="<div id='result' class='area' style='position: relative; z-index: 4; height: 500px; overflow: scroll;border: solid black 1px;'> ";
	  	if(this.doSmartTab)
			text= text.replace(/((\n?)([^\t\n]*)\t)/gi, this.smartTab);		// slower than simple replace...
		else
			text= text.replace(/\t/gi,"        ");
		text= " "+text; // for easier regExp
		/*if(this.do_html_tags)
			text= text.replace(/(<[a-z]+ [^>]*>)/gi, '[__htmlTag__]$1[_END_]');*/
		for(var lang in this.code){
			text=this.apply_language(text, lang);
		}
		
		text= text.replace(/&/g,"&amp;");
		text= text.replace(/</g,"&lt;");
		text= text.replace(/>/g,"&gt;");		
		text= text.substr(1);	// remove the first space added
		text= text.replace(/ /g,"&nbsp;");
		text= text.replace(/µ_END_µ/g,"</span>");
		text= text.replace(/µ__([a-zA-Z0-9]+)__µ/g,"<span class='$1'>");
		
		
		//text= text.replace(//gi, "<span class='quote'>$1</span>");
		//alert("text: \n"+text);
		
		return text;
	};
	
	EditArea.prototype.apply_language= function(text, lang){
		this.current_code_lang=lang;
		/*alert(typeof(text)+"\n"+text.length);
		
		var parse_index=0;
		for(var script_start in this.code[lang]["script_delimiters"]){
			var pos_start= text.indexOf(script_start);
			var pos_end= text.length;	// MUST BE SET TO CORRECT VAL!!!
			if(pos_start!=-1){
				var start_text=text.substr(0, pos_start);
				var middle_text= text.substring(pos_start, pos_end);
				var end_text= text.substring(pos_end);
				if(this.code[lang]["comment_or_quote_reg_exp"]){
					//setTimeout("document.getElementById('debug_area').value=editArea.comment_or_quote_reg_exp;", 500);
					middle_text= middle_text.replace(this.code[lang]["comment_or_quote_reg_exp"], this.comment_or_quote);
				}
				
				if(this.code[lang]["keywords_reg_exp"]){
					for(var i in this.code[lang]["keywords_reg_exp"]){	
						this.reg_exp_span_tag=i;
						middle_text= middle_text.replace(this.code[lang]["keywords_reg_exp"][i], this.custom_highlight);			
					}			
				}
				
				if(this.code[lang]["delimiters_reg_exp"]){
					middle_text= middle_text.replace(this.code[lang]["delimiters_reg_exp"], 'µ__delimiters__µ$1µ_END_µ');
				}		
				
				if(this.code[lang]["operators_reg_exp"]){
					middle_text= middle_text.replace(this.code[lang]["operators_reg_exp"], 'µ__operators__µ$1µ_END_µ');
				}
			}
			text= start_text+ middle_text + end_text;
		}*/
		
		if(this.code[lang]["custom_regexp"]['before']){
			for( var i in this.code[lang]["custom_regexp"]['before']){
				var convert="$1µ__"+ this.code[lang]["custom_regexp"]['before'][i]['class'] +"__µ$2µ_END_µ$3";
				text= text.replace(this.code[lang]["custom_regexp"]['before'][i]['regexp'], convert);			
			}
		}
		
		if(this.code[lang]["comment_or_quote_reg_exp"]){
			//setTimeout("document.getElementById('debug_area').value=editArea.comment_or_quote_reg_exp;", 500);
			text= text.replace(this.code[lang]["comment_or_quote_reg_exp"], this.comment_or_quote);
		}
		
		if(this.code[lang]["keywords_reg_exp"]){
			for(var i in this.code[lang]["keywords_reg_exp"]){	
				this.reg_exp_span_tag=i;
				text= text.replace(this.code[lang]["keywords_reg_exp"][i], this.custom_highlight);			
			}			
		}
		
		if(this.code[lang]["delimiters_reg_exp"]){
			text= text.replace(this.code[lang]["delimiters_reg_exp"], 'µ__delimiters__µ$1µ_END_µ');
		}		
		
		if(this.code[lang]["operators_reg_exp"]){
			text= text.replace(this.code[lang]["operators_reg_exp"], 'µ__operators__µ$1µ_END_µ');
		}
		
		if(this.code[lang]["custom_regexp"]['after']){
			for( var i in this.code[lang]["custom_regexp"]['after']){
				var convert="$1µ__"+ this.code[lang]["custom_regexp"]['after'][i]['class'] +"__µ$2µ_END_µ$3";
				text= text.replace(this.code[lang]["custom_regexp"]['after'][i]['regexp'], convert);			
			}
		}
		
		return text;
	};
		
	EditArea.prototype.changeHighlight= function(){
			
		if(this.isIE)
			this.getIESelection();
		var pos_start= this.textarea.selectionStart;
		var pos_end= this.textarea.selectionEnd;
		
		
		if(this.do_highlight===true)
			this.disableHighlight();
		else
			this.enableHighlight();
			
		this.textarea.selectionStart = pos_start;
		this.textarea.selectionEnd = pos_end;
		if(this.isIE)
			this.setIESelection();
				
	};
	
	EditArea.prototype.disableHighlight= function(displayOnly){		
		document.getElementById("selection_field").innerHTML="";
		var contain=document.getElementById("content_highlight");
		contain.style.visibility="hidden";
		contain.innerHTML="";
		var new_class=getAttribute(this.textarea,"class").replace(/hidden/,"");
		this.textarea.setAttribute("className", new_class);
		this.textarea.setAttribute("class", new_class);
		
		//var icon= document.getElementById("highlight_icon");
		//setAttribute(icon, "class", getAttribute(icon, "class").replace(/ selected/g, "") );
		//this.restoreClass(icon);
		//this.switchClass(icon,'editAreaButtonNormal');
		this.switchClassSticky(document.getElementById("highlight_icon"), 'editAreaButtonNormal', false);
		this.switchClassSticky(document.getElementById("reset_highlight_icon"), 'editAreaButtonDisabled', true);
		//area.onkeyup = formatArea;
		if(!displayOnly){
			this.do_highlight=false;
			this.formatArea();
			if(this.state=="loaded")
				this.textarea.focus();
		}
	};

	EditArea.prototype.enableHighlight= function(displayOnly){		
		
		var selec=document.getElementById("selection_field");
		selec.style.visibility="visible";		
		var contain=document.getElementById("content_highlight");
		contain.style.visibility="visible";
		var new_class=getAttribute(this.textarea,"class")+" hidden";
		this.textarea.setAttribute("className", new_class);
		this.textarea.setAttribute("class", new_class);
		
		//var icon= document.getElementById("highlight_icon");
		//setAttribute(icon, "class", getAttribute(icon, "class") + " selected");
		//this.switchClass(icon,'editAreaButtonSelected');
		this.switchClassSticky(document.getElementById("highlight_icon"), 'editAreaButtonSelected', false);
		this.switchClassSticky(document.getElementById("reset_highlight_icon"), 'editAreaButtonNormal', false);
		
		//area.onkeyup="";
		if(!displayOnly){
			this.do_highlight=true;
			this.reSync();
			if(this.state=="loaded")
				this.textarea.focus();
		}
	};
	
	
	EditArea.prototype.majArea= function(reload_all){
		if(!reload_all)
			reload_all=false;
		this.is_updating=true;
		
		date= new Date();
		tps1=date.getTime();
		date= new Date();
		tps_middle_opti=date.getTime();
				
		
		var infos= this.getSelectionInfos();
		var text= infos["full_text"];
		var start_line_pb=-1;	// for optimisation process
		var end_line_pb=-1;		// for optimisation process
		var stay_begin_array=new Array();	// for optimisation process
		var stay_end_array=new Array();	// for optimisation process
		var update_text=text;			
		if(text=="")
			text="\n ";
					
		/***  optmisation: will search to update only changed lines ***/
		if(reload_all){
			this.previous_content= new Array();
			this.previous_text="";
		}else{
			if(text== this.previous_text || (this.last_highlight_line_selected== infos["line_start"] && this.previous_content.length== infos["nb_line"])){
				this.is_updating=false;
				this.last_highlight_line_selected= infos["line_start"];
				return;
			}
						
			// search for lines that have changed
			var tab_text=text.split("\n");
			var previous_tab_text= this.previous_text.split("\n");
			i=0;
			for(; i< tab_text.length && i<previous_tab_text.length && start_line_pb == -1; i++){
				if(previous_tab_text[i] != tab_text[i]){
					start_line_pb=i;
				}
			}
			
			nb_end_line_ok=0;
			if(start_line_pb==-1){
				start_line_pb=i;
			}else{
				j=previous_tab_text.length-1;
				i=tab_text.length-1;
				while(i>=0 && j>=0 && previous_tab_text[j] == tab_text[i]){
					i--;
					j--;
					nb_end_line_ok++;
				}				
			}				
	
			update_text="";
			stop_modif=start_line_pb;
			if(previous_tab_text.length<tab_text.length)
				stop_modif=tab_text.length - nb_end_line_ok - (previous_tab_text.length - (start_line_pb+1 + nb_end_line_ok));
			else if(previous_tab_text.length>tab_text.length)
				stop_modif=tab_text.length - nb_end_line_ok - (tab_text.length - (start_line_pb+1 + nb_end_line_ok));
			else
				stop_modif=tab_text.length - nb_end_line_ok;
			
			// get changed lines content
			for(i=start_line_pb; i< Math.min(tab_text.length, stop_modif);  i++){
				if(i>start_line_pb)
					update_text+="\n";
				update_text+=tab_text[i];				
			}
			date= new Date();
			tps_middle_opti=date.getTime();
			end_line_pb=this.previous_content.length - (tab_text.length - stop_modif);
			
			// get the unchanged lines content (thoses lines are already highlighted
			
			stay_begin_array= this.previous_content.slice(0, start_line_pb);		
			stay_end_array= this.previous_content.slice(end_line_pb);
						
			if(this.debug){
				nb_line_pb= this.previous_content.length - nb_end_line_ok;
				document.getElementById("line").value="previous_nb_line: "+this.previous_content.length+" nb_end_line_unchanged: "+nb_end_line_ok+" Line pb: "+ start_line_pb+ "fin pb: "+ end_line_pb+ " nbLine: "+nb_line_pb+"\n";
			}
	
			/*** END optmisation ***/
		}
		date= new Date();
		tps_end_opti=date.getTime();	
				
		// apply highlight
		new_text=this.colorizeText(update_text);		

		// get the new highlight content
		var middle=new_text.split("\n");
		var tab_text=stay_begin_array.concat(middle).concat(stay_end_array);		
			
		date= new Date();
		tps2=date.getTime();
		var hightlight_content=tab_text.join("<br>");	// take 200 ms for 60Ko text => very slow...
		//this.previous_hightlight_content= tab_text.join("<br>");
		
		date= new Date();
		inner1=date.getTime();		
					
		// update the content of the highlight div by first updating a clone node(as there is no display in the same time for this node it's quite faster (5*))
		var prev_Obj=document.getElementById("content_highlight")
		var new_Obj = prev_Obj.cloneNode(false);
		new_Obj.innerHTML=hightlight_content;			
		prev_Obj.parentNode.insertBefore(new_Obj,prev_Obj);
		prev_Obj.parentNode.removeChild(prev_Obj);			
	
		date= new Date();
		tps3=date.getTime();
		
		if(this.debug){
			tot1=tps_end_opti-tps1;
			tot_middle=tps_end_opti- tps_middle_opti;
			tot2=tps2-tps_end_opti;
			tps_join=inner1-tps2;			
			tps_td2=tps3-inner1;
			//lineNumber=tab_text.length;
			//document.getElementById("line").value+=" \nNB char: "+document.getElementById("src").value.length+" Nb line: "+ lineNumber;
			document.getElementById("line").value+=" \nTps optimisation "+tot1+" (second part: "+tot_middle+") | tps reg exp: "+tot2+" | tps join: "+tps_join;
			document.getElementById("line").value+=" | tps update highlight content: "+tps_td2+"\n"+ update_text;
		}
		
		
		if(this.previous_content.length != infos["nb_line"])
			this.formatArea();
		// store datas
		this.last_highlight_line_selected= infos["line_start"];
		this.previous_content= tab_text;
		this.previous_text= text;
		//document.getElementById("debug_area").value= hightlight_content;
		this.is_updating=false;
	};
	
	EditArea.prototype.reSync= function(){
		this.previous_content= new Array();
		this.previous_text="";
		this.textarea.scrollLeft=0;
		this.textarea.scrollTop=0;
		this.startMajArea(true);
	}
	
	var clavier_cds=new Array(146);
	clavier_cds[8]="Retour arriere";
	clavier_cds[9]="Tabulation";
	clavier_cds[12]="Milieu (pave numerique)";
	clavier_cds[13]="Entrer";
	clavier_cds[16]="Shift";
	clavier_cds[17]="Ctrl";
	clavier_cds[18]="Alt";
	clavier_cds[19]="Pause";
	clavier_cds[20]="Verr Maj";
	clavier_cds[27]="Echap";
	clavier_cds[32]="Espace";
	clavier_cds[33]="Page precedente";
	clavier_cds[34]="Page suivante";
	clavier_cds[35]="Fin";
	clavier_cds[36]="Debut";
	clavier_cds[37]="Fleche gauche";
	clavier_cds[38]="Fleche haut";
	clavier_cds[39]="Fleche droite";
	clavier_cds[40]="Fleche bas";
	clavier_cds[44]="Impr ecran";
	clavier_cds[45]="Inser";
	clavier_cds[46]="Suppr";
	clavier_cds[91]="Menu Demarrer Windows / touche pomme Mac";
	clavier_cds[92]="Menu Demarrer Windows";
	clavier_cds[93]="Menu contextuel Windows";
	clavier_cds[112]="F1";
	clavier_cds[113]="F2";
	clavier_cds[114]="F3";
	clavier_cds[115]="F4";
	clavier_cds[116]="F5";
	clavier_cds[117]="F6";
	clavier_cds[118]="F7";
	clavier_cds[119]="F8";
	clavier_cds[120]="F9";
	clavier_cds[121]="F10";
	clavier_cds[122]="F11";
	clavier_cds[123]="F12";
	clavier_cds[144]="Verr Num";
	clavier_cds[145]="Arret defil";



	function keyDown(e){
		//alert("keydown");
		if(!e){	// if IE
			e=event;
		}
		var target_id=(e.target || e.srcElement).id;
		var use=false;
		/*if((e.keyCode<=40 && e.keyCode!=32) || (e.keyCode>90 && e.keyCode!=113))
			return true;*/
		if (clavier_cds[e.keyCode])
			letter=clavier_cds[e.keyCode];
		else
			letter=String.fromCharCode(e.keyCode);
		var low_letter= letter.toLowerCase();
				
		if(letter=="Tabulation" && target_id==editArea.id){			
			if(ShiftPressed(e))
				editArea.invertTabSelection();
			else
				editArea.tabSelection();
			
			use=true;
			if(editArea.isOpera)	// opera can't cancel keydown events...
				setTimeout("editArea.textarea.focus()", 1);
		}else if(letter=="Entrer" && target_id==editArea.id){
			//alert("enter");
			if(editArea.pressEnter())
				use=true;
		}else if(CtrlPressed(e)){
			//alert(letter+" | "+low_letter);
			switch(low_letter){
				case "f":				
					editArea.area_search();
					use=true;
					break;
				case "r":
					editArea.area_replace();
					use=true;
					break;
				case "q":
					editArea.close_all_inline_popup(e);
					use=true;
					break;
				case "h":
					if(editArea.isOpera){ // opera fire 2 times this event o_O
						date= new Date();
						if(!editArea.opera_last_fire_highlight)
							editArea.opera_last_fire_highlight=0;
						if(editArea.opera_last_fire_highlight < date.getTime() - 1000){							
							editArea.opera_last_fire_highlight= date.getTime();
							editArea.changeHighlight();							
						}				
					}else
						editArea.changeHighlight();
					use=true;
					break;
				case "g":
					setTimeout("editArea.go_to_line();", 5);	// the prompt stop the return false otherwise
					use=true;
					break;
				case "e":
					editArea.show_help();
					use=true;
					break;
				default:
					break;			
			}		
		}		
		
		if(use){
			//alert(letter);
			// in case of a control that sould'nt be used by IE but that is used => THROW a javascript error that will stop key action
			if(editArea.isIE)
				e.keyCode=0;
			/*if(e.preventDefault)
				e.preventDefault();*/
			return false;
		}
		if(editArea.next.length > 0){
			editArea.next= new Array();	// undo the ability to use "redo" button
			editArea.switchClassSticky(document.getElementById("redo_icon"), 'editAreaButtonDisabled', true);
		}
		//alert("Test: "+ letter + " ("+e.keyCode+") ALT: "+ AltPressed(e) + " CTRL "+ CtrlPressed(e) + " SHIFT "+ ShiftPressed(e));
		
		return true;
		
	}


	// return true if Alt key is pressed
	function AltPressed(e) {
	  if (window.event) {
	    return (window.event.altKey);
	  } else {
	  	if(e.modifiers)
	    	return (e.altKey || (e.modifiers % 2));
	    else
	    	return e.altKey;
	  }
	} 

	// return true if Ctrl key is pressed
	function CtrlPressed(e) {
	  if (window.event) {
	    return (window.event.ctrlKey);
	  } else {
	    return (e.ctrlKey || (e.modifiers==2) || (e.modifiers==3) || (e.modifiers>5));
	  }
	}

	// return true if Shift key is pressed
	function ShiftPressed(e) {
	  if (window.event) {
	    return (window.event.shiftKey);
	  } else {
	    return (e.shiftKey || (e.modifiers>3));
	  }
	} 

		EditArea.prototype.show_search = function(){
		if(document.getElementById("area_search_replace").style.visibility=="visible"){
			this.hidden_search();
		}else{
			this.open_inline_popup("area_search_replace");
			var text= this.area_getSelection();
			var search= text.split("\n")[0];
			document.getElementById("area_search").value= search;
			document.getElementById("area_search").focus();
		}
	};
	
	EditArea.prototype.hidden_search= function(){
		/*document.getElementById("area_search_replace").style.visibility="hidden";
		this.textarea.focus();
		var icon= document.getElementById("search_icon");
		setAttribute(icon, "class", getAttribute(icon, "class").replace(/ selected/g, "") );*/
		this.close_inline_popup("area_search_replace");
	};
	
	EditArea.prototype.area_search= function(mode){
		if(!mode)
			mode="search";
		document.getElementById("area_search_msg").innerHTML="";
		var search=document.getElementById("area_search").value;		
		var infos= this.getSelectionInfos();		
		var start= infos["indexOfCursor"];
		var pos=-1;
		var pos_begin=-1;
		var length=search.length;
		
		if(document.getElementById("area_search_replace").style.visibility!="visible"){
			this.show_search();
			return;
		}
		if(search.length==0){
			document.getElementById("area_search_msg").innerHTML="Search field empty";
			return;
		}
		// advance to the next occurence if no text selected
		if(mode!="replace" && this.area_getSelection().length>0){
				if(document.getElementById("area_search_reg_exp").checked)
					start++;
				else
					start+= search.length;
		}
		//search
		if(document.getElementById("area_search_reg_exp").checked){
			// regexp search
			var opt="mg";
			if(!document.getElementById("area_search_match_case").checked)
				opt+="i";
			var reg= new RegExp(search, opt);
			pos= infos["full_text"].substr(start).search(reg);
			pos_begin= infos["full_text"].search(reg);
			if(pos!=-1){
				pos+=start;
				length=infos["full_text"].substr(start).match(reg)[0].length;
			}else if(pos_begin!=-1){
				length=infos["full_text"].match(reg)[0].length;
			}
		}else{
			if(document.getElementById("area_search_match_case").checked){
				pos= infos["full_text"].indexOf(search, start); 
				pos_begin= infos["full_text"].indexOf(search); 
			}else{
				pos= infos["full_text"].toLowerCase().indexOf(search.toLowerCase(), start); 
				pos_begin= infos["full_text"].toLowerCase().indexOf(search.toLowerCase()); 
			}		
		}
		
		// interpret result
		if(pos==-1 && pos_begin==-1){
			document.getElementById("area_search_msg").innerHTML="<strong>"+search+"</strong> not found.";
			return;
		}else if(pos==-1 && pos_begin != -1){
			begin= pos_begin;
			document.getElementById("area_search_msg").innerHTML="End of area reached. Restart at begin";
		}else
			begin= pos;
		
		//document.getElementById("area_search_msg").innerHTML+="<strong>"+search+"</strong> found at "+begin+" strat at "+start+" pos "+pos+" curs"+ infos["indexOfCursor"]+".";
		if(mode=="replace" && pos==infos["indexOfCursor"]){
			var replace= document.getElementById("area_replace").value;
			var new_text="";			
			if(document.getElementById("area_search_reg_exp").checked){
				var opt="m";
				if(!document.getElementById("area_search_match_case").checked)
					opt+="i";
				var reg= new RegExp(search, opt);
				new_text= infos["full_text"].substr(0, begin) + infos["full_text"].substr(start).replace(reg, replace);
			}else{
				new_text= infos["full_text"].substr(0, begin) + replace + infos["full_text"].substr(begin + length);
			}
			this.textarea.value=new_text;
			this.area_select(begin, length);
			this.area_search();
		}else
			this.area_select(begin, length);
	};
	
	
	
	
	EditArea.prototype.area_replace= function(){		
		this.area_search("replace");
	};
	
	EditArea.prototype.area_replace_all= function(){
	/*	this.area_select(0, 0);
		document.getElementById("area_search_msg").innerHTML="";
		while(document.getElementById("area_search_msg").innerHTML==""){
			this.area_replace();
		}*/
	
		var base_text= this.textarea.value;
		var search= document.getElementById("area_search").value;		
		var replace= document.getElementById("area_replace").value;
		if(search.length==0){
			document.getElementById("area_search_msg").innerHTML="Search field empty";
			return ;
		}
		
		var new_text="";
		var nb_change=0;
		if(document.getElementById("area_search_reg_exp").checked){
			// regExp
			var opt="mg";
			if(!document.getElementById("area_search_match_case").checked)
				opt+="i";
			var reg= new RegExp(search, opt);
			nb_change= infos["full_text"].match(reg).length;
			new_text= infos["full_text"].replace(reg, replace);
			
		}else{
			
			if(document.getElementById("area_search_match_case").checked){
				var tmp_tab=base_text.split(search);
				nb_change= tmp_tab.length -1 ;
				new_text= tmp_tab.join(replace);
			}else{
				// case insensitive
				var lower_value=base_text.toLowerCase()
				var lower_search=search.toLowerCase();
				
				var start=0;
				var pos= lower_value.indexOf(lower_search);				
				while(pos!=-1){
					nb_change++;
					new_text+= this.textarea.value.substring(start , pos)+replace;
					start=pos+ search.length;
					pos= lower_value.indexOf(lower_search, pos+1);
				}
				new_text+= this.textarea.value.substring(start);				
			}
		}			
		if(new_text==base_text){
			document.getElementById("area_search_msg").innerHTML="<strong>"+search+"</strong> not found.";
		}else{
			this.textarea.value= new_text;
			document.getElementById("area_search_msg").innerHTML="<strong>"+nb_change+"</strong> occurences replaced.";
		}
	};