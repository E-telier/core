	
	function getCaretCharacterOffsetWithin(element) {
		var caretOffset = 0;
		var doc = element.ownerDocument || element.document;
		var win = doc.defaultView || doc.parentWindow;
		var sel;
		if (typeof win.getSelection != "undefined") {
			var range = win.getSelection().getRangeAt(0);
			var preCaretRange = range.cloneRange();
			preCaretRange.selectNodeContents(element);
			preCaretRange.setEnd(range.endContainer, range.endOffset);
			caretOffset = preCaretRange.toString().length;
		} else if ( (sel = doc.selection) && sel.type != "Control") {
			var textRange = sel.createRange();
			var preCaretTextRange = doc.body.createTextRange();
			preCaretTextRange.moveToElementText(element);
			preCaretTextRange.setEndPoint("EndToEnd", textRange);
			caretOffset = preCaretTextRange.text.length;
		}
		return caretOffset;
	}
	
	function convertStyletoHTML(tOldStyle) {
	
		tNewStyle = '';
		
		tOldStyle = tOldStyle.replace(/title/gi, 'h');
		
		tOldStyle = tOldStyle.replace(new RegExp('link=\'(.*)\'', 'gi'), 'a href=\"../$1\"');		
		tOldStyle = tOldStyle.replace(new RegExp('url=\'(.*)\'', 'gi'), 'a href=\"$1\" target=\"_blank\"');
				
		tOldStyle = tOldStyle.replace(/img=(.*) \/img/gi, 'img src="$1" class="temp_img" /');
		
		tOldStyle = tOldStyle.replace(new RegExp('module=(.*) /module', 'gi'), 'div class=\"module-$1\"></div');
					
		switch(tOldStyle) {
			case 'bold':
			case 'italic':
			case 'underlined':
			case 'paragraph':
				tNewStyle = tOldStyle.substring(0,1);
				break;
				
			case 'blue':
				tNewStyle = 'span style=\"color:#36365C;\"';
				break;
			case 'red':
				tNewStyle = 'span style=\"color:#CC3300;\"';
				break;
			case 'green':
				tNewStyle = 'span style=\"color:#336600;\"';
				break;
			case 'gray':
				tNewStyle = 'span style=\"color:#999999;\"';
				break;
				
			case 'list':
				tNewStyle = 'ul><li';
				break;
											
			default: 
				tNewStyle = tOldStyle;
		}
	
		return tNewStyle;
	
	}
	
	var NO_CLOSING = true;
	var gEditorControl = '';	
	function ajoutStyle(f, tWhere, tNo_closing) {
	  			  
		alert(f); 
				  
	  	if (tWhere==null) {	tWhere = "contenu"; }
		
		var tBaliseOpen = '[';
		var tBaliseClose = ']';
										
		if (gEditorControl=='wysiwyg') { 
			tWhere = 'wysiwyg_'+tWhere; 
			tBaliseOpen = '<';
			tBaliseClose = '>';
			
			f = convertStyletoHTML(f);			
		}
							
		// Balises entrée et sortie //				
		var baliseOn  = tBaliseOpen + f + tBaliseClose;
		
		tBaliseType = f;
		if (f.indexOf(' ')>0) {			
			tBaliseType = f.substring(0, f.indexOf(' '));
		}
		
		if (tNo_closing!=true) {
			var baliseOff = tBaliseOpen+'/' + tBaliseType + tBaliseClose;
		}
		else  {
			var baliseOff = '';
		}
								
		// Exceptions //
		if (f.substring(0,3)=='url') {
			var baliseOff = '[/url]';
		}
		if (f.substring(0,4)=='link') {
			var baliseOff = '[/link]';
		}
		if (f.substring(0,6)=='ul><li') {
			var baliseOff = '</li></ul>';
		}
				
		if (baliseOn=='[list]') {
			baliseOn += '\n';
			baliseOff = '\n'+baliseOff+' \n';			
		}
		
		//alert(baliseOn+' '+baliseOff);
		
		if (gEditorControl=='wysiwyg') { 
			var sel, range;
			if (window.getSelection) {
				sel = window.getSelection();
				if (sel.rangeCount) {
					range = sel.getRangeAt(0);
											
					replacementText = baliseOn + range + baliseOff;
					
					html=new XMLSerializer().serializeToString(range.cloneContents());								
					if (html.indexOf(baliseOn.substr(0,2))==0 && html.indexOf(baliseOff)==html.length-4) {
						replacementText = range+'';					
					}
					
					range.deleteContents();  

					// Range.createContextualFragment() would be useful here but is
					// non-standard and not supported in all browsers (IE9, for one)
					
					//alert(tBaliseType);
					
					var el = document.createElement(tBaliseType);
					el.innerHTML = replacementText;
					var frag = document.createDocumentFragment(), node, lastNode;
					while ( (node = el.firstChild) ) {
						lastNode = frag.appendChild(node);
					}
					range.insertNode(frag);
										
				}
			} else if (document.selection && document.selection.createRange) {
			
				// IE //			
				range = document.selection.createRange();				
				replacementText = baliseOn + range.text + baliseOff;				
				//alert(replacementText);
				
				//range.collapse(false);
				range.text = '';
				range.pasteHTML(replacementText);
			}
		} else {
			
			// Ajout du style à la sélection //
			var mess  = document.getElementById(tWhere);
				
			if (document.selection) {	
				// IE
				//alert('ie');
				var str = document.selection.createRange().text;
				mess.focus();		
				sel = document.selection.createRange();
										
				sel.text = baliseOn + str + baliseOff;		
				mess.focus();
			} 				
			else if (mess.selectionStart || mess.selectionStart == "0") {
				// AUTRES
				//alert('other');			
				var startPos = mess.selectionStart;
				var endPos   = mess.selectionEnd;
				var chaine   = mess.value;
				var str      = chaine.substring( mess.selectionStart, mess.selectionEnd ); 
																		
				mess.value 			= chaine.substring(0, startPos) + baliseOn + str + baliseOff + chaine.substring(endPos, chaine.length);			
				mess.selectionStart = Number(startPos);
				mess.selectionEnd   = Number(endPos)  + Number(baliseOn.length) + Number(baliseOff.length);
				mess.focus();	
			}	  
			else {		
				// Selection vide
				
				if (f=='a') {
					baliseOn = tBaliseOpen + f + ' href=\'\' target=\'_blank\''+tBaliseClose;
				}
								
				mess.value += baliseOn + '' + baliseOff;
				mess.focus();
			}
		}
		
		// CLEAN CODE //
		
		// Check if node already exists > Exemple <b>1<b>2</b>3</b> //
		if (gEditorControl=='wysiwyg') { 
			var oldHTML = $('#'+tWhere).html();	
		} else {
			var oldHTML = $('#'+tWhere).val();	
		}
		//alert(oldHTML);
		var newHTML = '';
		while(oldHTML.length>0) {
			var tLastOpen = oldHTML.lastIndexOf(baliseOn);
			var tAfterClose = tLastOpen+oldHTML.substr(tLastOpen).indexOf(baliseOff);
			var tBeforeClose = oldHTML.substr(0, tLastOpen).lastIndexOf(baliseOff);
			var tBeforeOpen = oldHTML.substr(0, tLastOpen).lastIndexOf(baliseOn);
			
			if (tBeforeOpen>tBeforeClose) {
				var tNewString = '';
				tNewString += oldHTML.substr(tBeforeOpen, tLastOpen-tBeforeOpen) 
				//alert('1 '+tNewString);
				tNewString += baliseOff 
				//alert('2 '+tNewString);
				tNewString += oldHTML.substr(tLastOpen+baliseOn.length, tAfterClose-(tLastOpen+baliseOn.length)) 
				//alert('3 '+tNewString);
				tNewString += baliseOn 
				//alert('4 '+tNewString);
				tNewString += oldHTML.substr(tAfterClose+baliseOff.length);
				//alert('5 '+tNewString);
				
				oldHTML = oldHTML.substr(0, tBeforeOpen);
				newHTML = tNewString +newHTML;
			} else {
				newHTML = oldHTML +newHTML;
				oldHTML = '';
			}							
		}
		if (newHTML!='') {
				
			$('#'+tWhere).html(newHTML);
			if (gEditorControl=='wysiwyg') { 
				$('#'+tWhere).html(newHTML);
			} else {
				$('#'+tWhere).val(newHTML);
			}			
		}
		
		// Empty node //
		if (gEditorControl=='wysiwyg') { 
			var HTML = $('#'+tWhere).html();	
		} else {
			var HTML = $('#'+tWhere).val();	
		}
				
		var tRegEmptyTxt = baliseOn+baliseOff+baliseOn+'(.*)'+baliseOff; // Exemple : <b></b><b>exemple</b>
		tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
		tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
		var tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');		
		HTML = HTML.replace(tRegEmpty, '$1');
		
		if (baliseOff!='') {
			tRegEmptyTxt = baliseOn+baliseOff; // Exemple : <b></b>
			tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
			tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
			tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');				
			HTML = HTML.replace(tRegEmpty, '');
		}
		
		tRegEmptyTxt = '[img= /img]';
		tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
		tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');			
		HTML = HTML.replace(tRegEmpty, '');	

		tRegEmptyTxt = '<div class="img.*"><br></div>';
		/*tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]');*/
		tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');				
		HTML = HTML.replace(tRegEmpty, '');	
		
		tRegEmptyTxt = '<div class="img.*">&nbsp;</div>';
		/*tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]');*/
		tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');				
		HTML = HTML.replace(tRegEmpty, '');	

		/*
		tRegEmptyTxt = '<div class="img.*">^(?!<img)+.*</div>';
		tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
		tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');				
		HTML = HTML.replace(tRegEmpty, '$1');	
		*/
		
		
		// Double Link //		
		tRegEmptyTxt = '<a href="(.*)"(.*)>(.*)<a href=".*".*>(.*)</a>(.*)</a>';
		tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]')
		tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');				
		HTML = HTML.replace(tRegEmpty, '<a href="$1"$2>$3$4$5</a>');
		
		tRegEmptyTxt = '[link=\'(.*)\'](.*)[link=\'.*\'](.*)[/link](.*)[/link]';
		tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]')
		tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');				
		HTML = HTML.replace(tRegEmpty, '[link=\'$1\']$2$3$4[/link]');
				
		tRegEmptyTxt = '[url=\'(.*)\'](.*)[url=\'.*\'](.*)[/url](.*)[/url]';
		tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]')
		tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');				
		HTML = HTML.replace(tRegEmpty, '[url=\'$1\']$2$3$4[/url]');		
		//alert('link\n'+HTML)
				
		//alert(HTML);				
		if (gEditorControl=='wysiwyg') { 
			$('#'+tWhere).html(HTML);
		} else {
			$('#'+tWhere).val(HTML);
		}
		
		// Images //
		$('.temp_img').each(function() {
			var ref = $(this).attr('src');			
			var fileInfos = gImgList[ref];			
			$(this).before('<div class="img '+fileInfos['align']+'"><img src="'+('../images/'+fileInfos['filename'])+'" width="'+fileInfos['width']+'" height="'+fileInfos['height']+'" title="'+fileInfos['description']+'" alt="'+fileInfos['description']+'" /></div>');									
		});
		$('.temp_img').remove();
		
		//alert($('#'+tWhere).html());
		
	}
	
	function Wysiwyg(name) {
			
		this.pName = name;
		this.pEditorControl = '';
										
		this.init = init;
		function init() {			
			this.setEditorControls('code')
			var self = this;
			setInterval(function() { self.update(); }, '100');
		}
						
		this.setEditorControls = setEditorControls;			
		function setEditorControls(tStrValue) {
			if (this.pEditorControl!=tStrValue) {	
				this.pEditorControl = tStrValue;
				if (tStrValue=='code') {
					this.update_preview();
					$('#wysiwyg_'+this.pName).attr('contenteditable', false);
					$('#code_btn_'+this.pName).addClass('selected_control_editor');
					$('#wysiwyg_btn_'+this.pName).removeClass('selected_control_editor');
					$('#wysiwyg_'+this.pName).removeClass('selected_editor');
					$('#'+this.pName).addClass('selected_editor');
				} else {
					this.update_source();
					$('#wysiwyg_'+this.pName).attr('contenteditable', true);
					$('#code_btn_'+this.pName).removeClass('selected_control_editor');
					$('#wysiwyg_btn_'+this.pName).addClass('selected_control_editor');
					$('#wysiwyg_'+this.pName).addClass('selected_editor');
					$('#'+this.pName).removeClass('selected_editor');
				}
				
			}
		}		

		this.update_source = update_source;
		function update_source() {
		
			var tDatas = $('#wysiwyg_'+this.pName).html();			
			var self = this;								
			var ajaxRq = $.ajax({
					'url': '_translate_style.php', 
					'type': 'POST',
					'dataType': 'text', 										
					'data': {
						'fromHTML': tDatas
					}
				,
					'success': function(data) {	
					//alert('code\n1'+data+'2');
					if (self.pEditorControl=='wysiwyg') {
						data = data.replace(/\r/, '');
						data = data.replace(/\n/, '');
						$('#'+self.pName).val(data);							
					}
				}, 
					'error': function(xhr) {
					if (xhr.status != 0) { alert('error'); }
				}
			});
			
			$(window).bind("beforeunload", function() { 
			  ajaxRq.abort();
			});
		}

		this.update_preview = update_preview;		
		function update_preview() {
		
			var tDatas = $('#'+this.pName).val();
			var self = this;
										
			var ajaxRq = $.ajax({
				'url': '_translate_style.php', 
				'type': 'POST',
				'dataType': 'text', 										
				'data': {
					'toHTML': tDatas
				}
			,
				'success': function(data) {
				//alert('code\n1'+data+'2');
				if (self.pEditorControl=='code') {
					//alert(data);
					
					data = data.replace(/\r/, '');
					data = data.replace(/\n/, '');
					
					$('#wysiwyg_'+self.pName).html(data);						
				}
			}, 
				'error': function(xhr) {
				if (xhr.status != 0) { alert('error'); }
			}
			});	

			$(window).bind("beforeunload", function() { 
			  ajaxRq.abort();
			});
			
		}

		this.update = update;			
		function update() {			
			if (this.pEditorControl=='code') {
				this.update_preview();
			} else {
				this.update_source();
			}												
		}

		this.showPreview = showPreview;
		function showPreview(tBool) {
			if (tBool) {
				$('#wysiwyg_'+this.pName).animate({'min-height':'160px', 'max-height':'360px', 'padding':'8px'}, 1000);
			} else {
				$('#wysiwyg_'+this.pName).animate({'min-height':'0px', 'max-height':'0px', 'padding':'2px'}, 1000);
			}
		}
		this.showCode = showCode;
		function showCode(tBool) {
			if (tBool) {
				$('#'+this.pName).animate({'height':'260px', 'padding':'8px'}, 1000);
			} else {
				$('#'+this.pName).animate({'height':'0px', 'padding':'2px'}, 1000);
			}
		}
	}
	
	