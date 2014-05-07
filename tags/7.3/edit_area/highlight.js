		
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
	
	