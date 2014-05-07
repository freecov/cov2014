	EditArea.prototype.getRegExp= function(tab_text){
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
	