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
