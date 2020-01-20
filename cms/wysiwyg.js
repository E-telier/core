/////////////
/* WYSIWYG */
/////////////
var gEditorControl = '';
var NO_CLOSING = true;
var lastSelectedImg = null;
var pWysiwyg = {};

function Wysiwyg(name, lang) {
			
	this.pName = name;
	this.pEditorControl = '';
	this.timeout = 0;
	this.interval = 0;
	this.maxLines = 0;
	this.maxCharsPerLine = 0;
	this.maxChars = 0;
	this.lastValue = '';
	this.pImgList = null;
	this.pLastCaret = 0;
	this.pLang = lang;
		
	// DELETE IMAGE DIV //
	$('#wysiwyg_'+this.pName).keyup(function(e){
		//alert(e.keyCode);
		if (lastSelectedImg!=null) {
			if(e.keyCode == 46 || e.keyCode == 8) {
				pattern = new RegExp("<div class=\"img[^\"]*\"[^>]*><br><\/div>");
				replacement = "";	
				var html = $(this).html();	
				html = html.replace(pattern, replacement, html);	
				$(this).html(html);	
				//alert(html);
				lastSelectedImg = null;
			}
		}
	})
									
	this.init = init;
	function init() {
		this.setEditorControls('code');
		var self = this;	
		
		$('#wysiwyg_'+this.pName + ', #'+this.pName).on('keyup', function() {			
			clearTimeout(self.timeout);
			self.timeout = setTimeout(function() { self.update(); }, '1000');
		});
		$('#wysiwyg_'+this.pName + ', #'+this.pName).on('blur', function() {
			clearTimeout(self.timeout);
			self.update();
			clearTimeout(self.interval);
			//self.timeout = setTimeout(function() { self.update(); }, '50');
		});
		$('#wysiwyg_'+this.pName + ', #'+this.pName).on('focus', function() {
			clearTimeout(self.interval);			
			self.interval = setInterval(function() { self.update_caret(); }, 100);
		});
		$('.style-table').on('click', function() {
			clearTimeout(self.timeout);
			self.timeout = setTimeout(function() { self.update(); }, '100');
		});
		
		
			
		$('#wysiwyg_'+this.pName).click(function() { lastSelectedImg = null; self.update(); });
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
				
				$('#wysiwyg_'+this.pName+' .img img.delete_img_btn').remove();
				
			} else {
				this.update_source();
				//alert($('.wysiwyg'));				
				$('#wysiwyg_'+this.pName).attr('contenteditable', true);
				
				$('#code_btn_'+this.pName).removeClass('selected_control_editor');
				$('#wysiwyg_btn_'+this.pName).addClass('selected_control_editor');
				$('#wysiwyg_'+this.pName).addClass('selected_editor');
				$('#'+this.pName).removeClass('selected_editor');
				
				// Add onclick event on img //
				$('#wysiwyg_'+this.pName+' .img').attr('onclick', 'lastSelectedImg=this; event.stopPropagation();')
					.append('<img src="'+CMSRootPath+'cms/design/picto_delete.jpg" width="22" height="22" style="position:absolute;top:0px;right:0px;" onclick="$(this).parent(\'.img\').remove(); lastSelectedImg=null; event.stopPropagation();" alt="Delete" contenteditable="false" />');
					
				
			}
			
		}
	}		
	
	this.simplifyHTML = simplifyHTML;
	function simplifyHTML(tDatas) {
	
		var sel, range;
		
		if (window.getSelection) {
			sel = window.getSelection();
			if (sel.rangeCount) {
				range = sel.getRangeAt(0);
			}
		}
			
		
		var tOldDatas = tDatas;
		
		// Delete all unpermitted STYLE //
		tDatas = tDatas.replace(/\[[^\]]+\]/gmi, '');
		
		// Keep selection //
		tDatas = tDatas.replace(/<span class="selection">((?!<\/span>).*?)<\/span>(<!-- END OF SELECTION -->)*/gmi, '[selection]$1[/selection][$2]');
		tDatas = tDatas.replace(/\[<!-- END OF SELECTION -->\]/gmi, '[selection_comment]');
		tDatas = tDatas.replace(/\[\]/gmi, '');
		
		// Keep permitted styles //
		tDatas = tDatas.replace(/<([\/]{0,1})b>/gmi, '[$1b]');
		tDatas = tDatas.replace(/<([\/]{0,1})i>/gmi, '[$1i]');
		tDatas = tDatas.replace(/<([\/]{0,1})u>/gmi, '[$1u]');
		
		tDatas = tDatas.replace(/<([\/]{0,1})a([^>]*)>/gmi, '[$1a$2]');
		
		tDatas = tDatas.replace(/<([\/]{0,1})ul>/gmi, '[$1ul]');
		tDatas = tDatas.replace(/<([\/]{0,1})ol>/gmi, '[$1ol]');
		tDatas = tDatas.replace(/<([\/]{0,1})li>/gmi, '[$1li]');
		
		// Keep returns //		
		tDatas = tDatas.replace(/<br([\/ ]{0,2})>/gmi, '[br]');		
		tDatas = tDatas.replace(/<div>/gmi, '[div]');
		tDatas = tDatas.replace(/<\/div>/gmi, '[/div]');
		tDatas = tDatas.replace(/<p>/gmi, '[p]');
		tDatas = tDatas.replace(/<\/p>/gmi, '[/p]');
		
		
		// Delete style //
		tDatas = tDatas.replace(/ style="[^"]*"/gmi, '');
		tDatas = tDatas.replace(/ id="[^"]*"/gmi, '');
		tDatas = tDatas.replace(/ class="[^"]*"/gmi, '');
		
		// Delete all unpermitted HTML //
		tDatas = tDatas.replace(/<[^>]+>/gmi, '');
		
		// Restore styles //
		tDatas = tDatas.replace(/\[selection\]((?!\[\/selection\]).*?)\[\/selection\]/gmi, '<span class="selection">$1</span>');
		tDatas = tDatas.replace(/\[selection_comment\]/gmi, '<!-- END OF SELECTION -->');
		tDatas = tDatas.replace(/\[([^\]]+)\]/gmi, '<$1>');		
		//tDatas = tDatas.replace(/<br>/gmi, '<br />');
		
				
		if (tDatas!=tOldDatas) {
			$('#wysiwyg_'+this.pName).html(tDatas);
					
			if (window.getSelection) {				
				if (sel.rangeCount) {					
					sel.addRange(range);
				}
			}			
		}
		
		return tDatas;
		
	}
	
	this.update_caret = function() {
		if ($('#wysiwyg_'+this.pName)[0] === document.activeElement) {
			this.pLastCaret = getCaretCharacterOffsetWithin($('#wysiwyg_'+this.pName)[0]);
		}
	}
	
	this.update_source = update_source;
	function update_source() {
	
		var tDatas = $('#wysiwyg_'+this.pName).html();
		//alert(tDatas);
		
		if ($('#wysiwyg_'+this.pName).closest('.simple_editor').length>0) {
			tDatas = this.simplifyHTML(tDatas);
		}
		/*
		if ($('#wysiwyg_'+this.pName)[0] === document.activeElement) {
			this.pLastCaret = getCaretCharacterOffsetWithin($('#wysiwyg_'+this.pName)[0]);
		}	
		*/		
		if (this.lastValue==tDatas) {
			return false;
		}
		this.lastValue=tDatas;
		
		//alert(tDatas);
				
		var self = this;
		var ajaxRq = $.ajax({
				'url': CMSRootPath+'cms/_translate_style.php?lang='+self.pLang, 
				'method': 'POST',
				'dataType': 'text', 										
				'data': {
					'fromHTML': tDatas
				}
			,
				'success': function(data) {
				if (self.pEditorControl=='wysiwyg') {					
					$('#'+self.pName).val(data);		
				}
			}, 
				'error': function(xrh) {
				if (xrh.status != 0) {alert('error update_source'); }
			}
		});
		
		$(window).bind("beforeunload", function() { 
		  ajaxRq.abort();
		});
	}
			
	this.update_preview = update_preview;
	function update_preview(force) {
	
		var tDatas = $('#'+this.pName).val();
		
		if (this.lastValue==tDatas) {
			return false;
		}
		this.lastValue=tDatas;
		
		var self = this;
		self.force = force;
					
		var ajaxRq = $.ajax({
			'url': CMSRootPath+'cms/_translate_style.php?lang='+self.pLang, 
			'method': 'POST',
			'dataType': 'text', 										
			'data': {
				'toHTML': tDatas
			},
			'success': function(data) {			
				if (self.pEditorControl=='code' || self.force==true) {					
					$('#wysiwyg_'+self.pName).html(data);			
				}
			}, 
			'error': function(xrh, error, msg) {
				if (xrh.status != 0) {alert('error update_preview '+xrh.status+' '+error+' '+msg); }
			}
		});	

		$(window).bind("beforeunload", function() { 
		  ajaxRq.abort();
		});
		
	}
	
	this.update = update;
	function update() {
			
		if (this.pEditorControl=='wysiwyg' && $('#wysiwyg_'+this.pName).is(':focus')) {
			
			// NODE DETECT //		
			var parentsInArray = new Array();
			$(getSelectedNode()).parents().each(function() {			
				if ($(this)[0].tagName=='DIV') {
					 return false;
				} else {
					//alert($(this)[0].tagName);
					parentsInArray.push($(this)[0].tagName);
				}
			});			
			var currentTagName = getSelectedNode().tagName;
			parentsInArray.push(currentTagName);
			console.log(parentsInArray);
			
			var tTools = $('#wysiwyg_'+this.pName).closest('.version').find('.tools.style');
			console.log(tTools.length);
			if (tTools.length>0) {
				if (parentsInArray.indexOf('B')>=0) {
					tTools.find('.add_bold').css({'font-weight':'bold'});
				} else {
					tTools.find('.add_bold').css({'font-weight':'normal'});
				}
				if (parentsInArray.indexOf('I')>=0) {
					tTools.find('.add_italic').css({'font-style':'italic'});
				} else {
					tTools.find('.add_italic').css({'font-style':'normal'});
				}
				if (parentsInArray.indexOf('U')>=0) {
					tTools.find('.add_underlined').css({'text-decoration':'underline'});
				} else {
					tTools.find('.add_underlined').css({'text-decoration':'none'});
				}
			}
			
			if ($('.'+this.pName+' .simple_styles').length>0) {
				
				var oldSRC, newSRC;
				$('.'+this.pName+' .simple_styles a .currentNode').each(function() {
					var this_img = $(this);
					oldSRC = this_img.attr('src');
					newSRC=oldSRC.substring(0, oldSRC.lastIndexOf('-'))+'-off.gif';
					this_img.attr('src', newSRC).removeClass('currentNode');
				});
				
				var button;
				if (parentsInArray.indexOf('B')>=0) {			
					button = $('.'+this.pName+' .simple_styles a[href="bold"] img');		
					button.addClass('currentNode');
					oldSRC =button.attr('src');
					newSRC=oldSRC.substring(0, oldSRC.lastIndexOf('-'))+'-on.gif';
					button.attr('src', newSRC);
				}
				if (parentsInArray.indexOf('I')>=0) {
					button = $('.'+this.pName+' .simple_styles a[href="italic"] img');
					button.addClass('currentNode');
					oldSRC = button.attr('src');
					newSRC=oldSRC.substring(0, oldSRC.lastIndexOf('-'))+'-on.gif';
					button.attr('src', newSRC);
				}
				if (parentsInArray.indexOf('U')>=0) {
					button = $('.'+this.pName+' .simple_styles a[href="underlined"] img');
					button.addClass('currentNode');
					oldSRC = button.attr('src');
					newSRC=oldSRC.substring(0, oldSRC.lastIndexOf('-'))+'-on.gif';
					button.attr('src', newSRC);
				}
			}
		}
		
		/*
		// MANAGE LIMITS //
		change=false;
		
		if (this.maxChars>0) {
			var html = $('#wysiwyg_'+this.pName).html();
			html = html.replace(/[\n\r]/gim, '');
			html = html.replace(/<[^>]*>/gim, '');
			
			$('.'+this.pName+' .max_chars').html(html.length+'/'+this.maxChars+' max chars');
			
			if (html.length>this.maxChars) {
				$('.'+this.pName+' .max_chars').css({'color':'#ff0000'});
			} else {
				$('.'+this.pName+' .max_chars').css({'color':'#999999'});
			}
			
		}
		if (this.maxCharsPerLine > 0) {
			var html = $('#wysiwyg_'+this.pName).html();
			html = html.replace(/[\n\r]/gim, '');
			html = html.split('<br>');
			//alert(html);
			var nbLines = html.length;
			//alert(nbLines);
			for (var i=0;i<nbLines;i++) {
				var line = html[i];
				var length = line.length;
				//alert(line.indexOf('<'));
				while(line.indexOf('<')>=0) {
					length = length - (line.indexOf('>')-line.indexOf('<')+1);
					line = line.substring(line.indexOf('>')+1);
					//alert(line);
				}
								
				if (length>this.maxCharsPerLine) {
					//alert(length);
					line = html[i];
					var diff = length-this.maxCharsPerLine;
					
					//if (line.length-line.lastIndexOf('>')>=diff) {
						line = line.substring(0, line.length-diff)+'<br>'+line.substring(line.length-diff);
					//}
					
					change=true;
					
				}
				html[i] = line;
			}
			
			if (change) {
				html = html.join('<br>');
				$('#wysiwyg_'+this.pName).html(html);
			}
		}		
		if (this.maxLines > 0) {
			var html = $('#wysiwyg_'+this.pName).html();
			html = html.replace(/[\n\r]/gim, '');
			if (html.lastIndexOf('<br>')==html.length-4) { html = html.substring(0, html.length-4); }
			html = html.split('<br>');
			//alert(html);
			var nbLines = html.length;
			
			//alert('.'+this.pName+' .max_lines'+' '+$('.'+this.pName+' .max_lines').length);
			
			$('.'+this.pName+' .max_lines').html((nbLines)+'/'+this.maxLines+' max lines');
			
			if (nbLines>this.maxLines) {
								
				$('.'+this.pName+' .max_lines').css({'color':'#ff0000'});
			} else {
				$('.'+this.pName+' .max_lines').css({'color':'#999999'});
			}
		}
		*/
		// UPDATE CONTENT //
		if ($('#wysiwyg_'+this.pName).length<1) {
			// WYSIWYG has been deleted
			clearTimeout(self.timeout);
		} else {
		
			if (this.pEditorControl=='code') {
				this.update_preview();
			} else {
				this.update_source();
			}
		}
				
	}
		
	this.showPreview = showPreview;
	function showPreview(tBool) {
		if (tBool) {
			
			$('#wysiwyg_'+this.pName).css({'min-height':'160px', 'height':'auto', 'padding':'8px'});
			var tHeight = $('#wysiwyg_'+this.pName).outerHeight();
			$('#wysiwyg_'+this.pName).css({'min-height':'0px', 'height':'0px', 'padding':'2px'});
			
			$('#wysiwyg_'+this.pName).animate({'height':tHeight+'px', 'padding':'8px'}, 1000, 'swing', function() { 
				$('#wysiwyg_'+this.pName).css({'height':'auto'});
			});
		} else {
			$('#wysiwyg_'+this.pName).animate({'min-height':'0px', 'height':'0px', 'padding':'2px'}, 1000);
		}
	}
	
	this.showCode = showCode;
	function showCode(tBool) {
		if (tBool) {
			$('#'+this.pName).animate({'height':'260px', 'padding':'8px'}, 1000);
		} else {
			$('#'+this.pName).animate({'min-height':'0px', 'height':'0px', 'padding':'2px'}, 1000);
		}
	}
				
} // END OF OBJECT

function saveSelection() {
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            return sel.getRangeAt(0);
        }
    } else if (document.selection && document.selection.createRange) {
        return document.selection.createRange();
    }
    return null;
}

function restoreSelection(range) {
    if (range) {
        if (window.getSelection) {
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (document.selection && range.select) {
            range.select();
        }
    }
}

function convertStyletoHTML(tOldStyle) {
	
	tNewStyle = '';
	
	tOldStyle = tOldStyle.replace(/title/gi, 'h');
	
	tOldStyle = tOldStyle.replace(new RegExp('link=\'(.*)\'', 'gi'), 'a href=\"../'+current_lang+'/$1\"');		
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
		case 'red':
		case 'green':
		case 'gray':
			tNewStyle = 'span class=\"'+tOldStyle+'\"';
			break;
			
		case 'left':
		case 'right':
		case 'center':
		case 'justify':
			tNewStyle = 'div class=\"align_'+tOldStyle+'\"';
			break;
			
		case 'dotlist':
			tNewStyle = 'ul><li';
			break;
		case 'numlist':
			tNewStyle = 'ol><li';
			break;
										
		default: 
			tNewStyle = tOldStyle;
	}
	
	return tNewStyle;

}

var getSelectedNode = function() {
    var node,selection;
    if (window.getSelection) {
      selection = getSelection();
      node = selection.anchorNode;
    }
    if (!node && document.selection) {
        selection = document.selection
        var range = selection.getRangeAt ? selection.getRangeAt(0) : selection.createRange();
        node = range.commonAncestorContainer ? range.commonAncestorContainer :
               range.parentElement ? range.parentElement() : range.item(0);
    }
    if (node) {
      return (node.nodeName == "#text" ? node.parentNode : node);
    }
}

function HTML2str(html) {
	var before, after;
		
	while(html.indexOf('<')>=0) {
		
		before = html.substr(0, html.indexOf('<'));
		after = html.substr(html.indexOf('>')+1+before.length);
				
		html = before+after;
	}
	return html;
}

function xml2Str(xmlNode) {
  try {
    // Gecko-based browsers, Safari, Opera.
    return (new XMLSerializer()).serializeToString(xmlNode);
  }
  catch (e) {
    try {
      // Internet Explorer.
      return xmlNode.xml;
    }
    catch (e)
    {//Strange Browser ??
     //alert('Xmlserializer not supported');
    }
  }
  return false;
}

function doGetCaretPosition(ctrl) {
	var CaretPos = 0; // IE Support
	if (document.selection) {
		ctrl.focus();
		var Sel = document.selection.createRange();
		Sel.moveStart('character', -ctrl.value.length);
		CaretPos = Sel.text.length;
	}
	// Firefox support
	else if (ctrl.selectionStart || ctrl.selectionStart == '0')
		CaretPos = ctrl.selectionStart;
	return CaretPos;
}

function setCaretPosition(el, sPos)
{

	var charIndex = 0, range = document.createRange();
	range.setStart(el, 0);
	range.collapse(true);
	var nodeStack = [el], node, foundStart = false, stop = false;

	while (!stop && (node = nodeStack.pop())) {
		if (node.nodeType == 3) {
			var nextCharIndex = charIndex + node.length;
			if (!foundStart && sPos >= charIndex && sPos <= nextCharIndex) {
				range.setStart(node, sPos - charIndex);
				foundStart = true;
			}
			if (foundStart && sPos >= charIndex && sPos <= nextCharIndex) {
				range.setEnd(node, sPos - charIndex);
				stop = true;
			}
			charIndex = nextCharIndex;
		} else {
			var i = node.childNodes.length;
			while (i--) {
				nodeStack.push(node.childNodes[i]);
			}
		}
	}
	selection = window.getSelection();
	selection.removeAllRanges();
	selection.addRange(range);
} 

function getSelectionHtml() {
    var html = "";
    if (typeof window.getSelection != "undefined") {
        var sel = window.getSelection();
        if (sel.rangeCount) {
            var container = document.createElement("div");
            for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                container.appendChild(sel.getRangeAt(i).cloneContents());
            }
            html = container.innerHTML;
        }
    } else if (typeof document.selection != "undefined") {
        if (document.selection.type == "Text") {
            html = document.selection.createRange().htmlText;
        }
    }
    return html;
}

function cleanSelection(selectionStr, baliseOn, baliseOff, tBaliseOpen, tBaliseClose, tBaliseType) {

	var tDeleteStyle = false
	if (tBaliseOpen=='[' && selectionStr.indexOf(baliseOn)==0 && selectionStr.lastIndexOf(baliseOff)==selectionStr.length-baliseOff.length) {
		// GLOBAL IDENTICAL TAG //
		tDeleteStyle = true;
	}

	var tSafeBaliseOpen = tBaliseOpen.replace('[', '\\\[');
	var tSafeBaliseClose = tBaliseClose.replace(']', '\\\]');
	
	if (tBaliseType!='span') {
		// REMOVE ALL IDENTICAL TAG //		
		
		var colors = new Array('red', 'blue', 'green', 'gray');
		if (colors.indexOf(tBaliseType)>=0) {
			for (var i=0;i<colors.length;i++) {
				tSimilarBaliseType = colors[i];
				tRegEmptyTxt = tSafeBaliseOpen+'[\/]*'+tSimilarBaliseType+'[^'+tSafeBaliseClose+']*'+tSafeBaliseClose;				
				tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');	
				selectionStr = selectionStr.replace(tRegEmpty, '');
			}
		} else {
			tRegEmptyTxt = tSafeBaliseOpen+'[\/]*'+tBaliseType+'[^'+tSafeBaliseClose+']*'+tSafeBaliseClose;
			//alert(tRegEmptyTxt);
			tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');	
			selectionStr = selectionStr.replace(tRegEmpty, '');
		}
		
	} else {
		// REMOVE CLASS ATTR FROM SPAN //
		tRegEmptyTxt = baliseOn.replace(/class="[^"]+"/gim, 'class="[^"]+"');
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');	
		selectionStr = selectionStr.replace(tRegEmpty, '');
		
		tRegEmptyTxt = baliseOff.replace(/class="[^"]+"/gim, 'class="[^"]+"');
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');	
		selectionStr = selectionStr.replace(tRegEmpty, '');	
	}
	
		
	if (tDeleteStyle==true) {
		// Double style to delete //
		selectionStr = baliseOn + selectionStr + baliseOff;
	}
		
	return selectionStr;
}

function addStyle(f, wysiwygName, tNo_closing) {
	
	console.log('addStyle '+f+' '+wysiwygName+' '+tNo_closing);

	var tWhere = wysiwygName;	  			 			  
	if (tWhere==null) {	tWhere = "contenu"; }
	
	var tBaliseOpen = '[';
	var tBaliseClose = ']';
									
	if (gEditorControl=='wysiwyg') { 
		tWhere = 'wysiwyg_'+tWhere; 
		tBaliseOpen = '<';
		tBaliseClose = '>';
		
		if (f.indexOf('img')==0) {
			// Force focus for custom img select //	
			$('#'+tWhere)[0].focus();
			console.log(pWysiwyg[wysiwygName].pLastCaret);
			setCaretPosition($('#'+tWhere)[0], pWysiwyg[wysiwygName].pLastCaret);
		}
		
		f = convertStyletoHTML(f);	
		
	}	
								
	// Balises entrée et sortie //				
	var baliseOn  = tBaliseOpen + f + tBaliseClose;
	
	tBaliseType = f;
	if (f.indexOf(' ')>0) {			
		tBaliseType = f.substring(0, f.indexOf(' '));
	} else if (f.indexOf('=')>0) {		
		tBaliseType = f.substring(0, f.indexOf('='));
	}
	
	if (tNo_closing!=true) {
		var baliseOff = tBaliseOpen+'/' + tBaliseType + tBaliseClose;
		
		if (tBaliseType=='span' || tBaliseType=='div') {
			baliseOff+='<!-- END OF '+f+' -->';
		}		
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
		tBaliseType = 'ul';
	}
	if (f.substring(0,6)=='ol><li') {
		var baliseOff = '</li></ol>';
		tBaliseType = 'ol';
	}
	
	if (baliseOn=='[list]') {
		baliseOn += '\n';
		baliseOff = '\n'+baliseOff+' \n';
	}
	
	console.log(baliseOn+' '+baliseOff);
		
	var HTML = '';
	var caretPos = 0;		
	if (gEditorControl=='wysiwyg') { 
		var sel, range;
		
		if (window.getSelection) {
			sel = window.getSelection();
			console.log(sel.rangeCount);
			if (sel.rangeCount) {
			
				if (lastSelectedImg!=null) {
					// SELECTION ON IMG //
					if (baliseOff=='</a>') {
						if ($(lastSelectedImg).parent().is( "a" ) ) {
							$(lastSelectedImg).unwrap();
						}
						$(lastSelectedImg).wrap(baliseOn + baliseOff);		
					}
				} else {
			
					range = sel.getRangeAt(0);
					
					console.log(range);	
					console.log(range.cloneContents());
					
					selectionHTML=xml2Str(range.cloneContents());
					console.log(selectionHTML);
					if (range!='' && selectionHTML=='') {
						// if XMLSerializer doesn' work //
						selectionHTML=getSelectionHtml();
					}		
					console.log(selectionHTML);					
					
					selectionHTML = selectionHTML.replace(new RegExp(' xmlns="http://www.w3.org/1999/xhtml"', 'gim'), '');
					selectionHTML = selectionHTML.replace(new RegExp('<span class="selection">', 'gim'), '');
					selectionHTML = selectionHTML.replace(new RegExp('</span><!-'+'- END OF SELECTION -'+'->', 'gim'), '');	
					selectionHTML = selectionHTML.replace(new RegExp('</span>(?!<!--)', 'gim'), '');
																		
					if (range!='' && selectionHTML=='') {
						// IE DOESNT SUPPORT SERIALIZE XML //
						selectionHTML = range;
					} else {
										
						var firstBaliseOnPos = selectionHTML.indexOf(baliseOn);	
						var lastBaliseOffPos = selectionHTML.lastIndexOf(baliseOff);	
						var originHTML = selectionHTML;	
												
						// Clean all existing tags in range //
						selectionHTML = cleanSelection(selectionHTML, baliseOn, baliseOff, tBaliseOpen, tBaliseClose, tBaliseType);
						
						if (selectionHTML=='') { selectionHTML='&#8203;'; caretPos = 1; }
					
					}
					
					console.log(selectionHTML);												
					replacementText = selectionHTML;
													
					// Add single tag to all range //				
					var firstTag = false;
					if (firstBaliseOnPos>=0) {
						front = HTML2str(originHTML.substr(0, firstBaliseOnPos));							
						if (front.length<=0) { firstTag = true; }							
					}
					
					var lastTag = false;
					if (lastBaliseOffPos>0 && lastBaliseOffPos<=originHTML.length-baliseOff.length) {
						back = HTML2str(originHTML.substr(lastBaliseOffPos+baliseOff.length));		
						if (back.length<=0) { lastTag = true; }				
					}
											
					if (baliseOn.indexOf('li')>=0) {
						// DELETE ALL LIST ELEMENT //
						
						var newReplacementText = replacementText;
						newReplacementText = newReplacementText.replace(/<li[^>]*>/gim, '');
						newReplacementText = newReplacementText.replace(/<\/li[^>]*>/gim, '<br />');
						
						if (newReplacementText!=replacementText) {
							// Force incorrect
							lastTag = true;
							firstTag=true;
							replacementText = newReplacementText;			
						}
					} 
										
					var selectionText = '<span class="selection_bis"></span><!-'+'- END OF SELECTION BIS -'+'->';
					if (!(firstTag==true && lastTag==true)) {	
						// ADD TAG ONLY WHEN NOT FIRST AND LAST
						selectionText = baliseOn + selectionText + baliseOff;
					}
							
					console.log(selectionText);
					range.deleteContents();  
					
					// Range.createContextualFragment() would be useful here but is
					// non-standard and not supported in all browsers (IE9, for one)					
										
					var el = document.createElement(tBaliseType);
					el.innerHTML = selectionText;
					var frag = document.createDocumentFragment(), node, lastNode;
					while ( (node = el.firstChild) ) {
						lastNode = frag.appendChild(node);
					}
					range.insertNode(frag);
					console.log(range);
										
					sel.removeAllRanges();
									
					var result = $('#'+tWhere).html();
					console.log(tWhere+' '+result);
																			
					result = result.replace(' xmlns="http://www.w3.org/1999/xhtml"', '');
					
					result = result.replace(new RegExp(' xmlns="http://www.w3.org/1999/xhtml"', 'gim'), '');		
					result = result.replace(new RegExp('<span class="selection">', 'gim'), '');
					result = result.replace(new RegExp('</span><!-'+'- END OF SELECTION -'+'->', 'gim'), '');
					
					result = result.replace('selection_bis', 'selection');
					result = result.replace('SELECTION BIS', 'SELECTION');
															
					$('#'+tWhere).html(result);
										
					HTML = result.replace('<span class="selection">', '<span class="selection">'+selectionHTML);
					
					//alert('HTML '+HTML);
					
				}						
			}
		} else if (document.selection && document.selection.createRange) {
		
			// IE //			
			range = document.selection.createRange();	
			replacementText = baliseOn + range.text + baliseOff;	
						
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
			str = '[selection]'+str+'[/selection]';
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
			str = '[selection]'+str+'[/selection]';
																	
			mess.value 			= chaine.substring(0, startPos) + baliseOn + str + baliseOff + chaine.substring(endPos, chaine.length);
			mess.selectionStart = Number(startPos);
			mess.selectionEnd   = Number(endPos)  + Number(baliseOn.length) + Number(baliseOff.length) + Number(('[selection][/selection]').length);
			mess.focus();
			
			var textareaStart = mess.selectionStart;
			var textareaEnd   = mess.selectionEnd - + Number(('[selection][/selection]').length);
			
		} else {		
			// Selection vide
			
			if (f=='a') {
				baliseOn = tBaliseOpen + f + ' href=\'\' target=\'_blank\''+tBaliseClose;
			}
							
			mess.value += baliseOn + '' + baliseOff;
			mess.focus();
		}
	}
			
	// CLEAN CODE //
	
	if (HTML=='') {
		if (gEditorControl=='wysiwyg') { 
			HTML = $('#'+tWhere).html();	
		} else {
			HTML = $('#'+tWhere).val();	
		}
	}
	
	//alert(HTML);
		
	// Remove similar inside selection //
	var selectionStartTag = '[selection]';
	var selectionStart = HTML.indexOf(selectionStartTag);
	if (selectionStart<0) { 
		selectionStartTag = '<span class="selection">'; 
		selectionStart = HTML.indexOf(selectionStartTag)
	}
		
	var selectionEndTag = '[/selection]';	
	var selectionEnd = HTML.indexOf(selectionEndTag);
	if (selectionEnd<0) { 
		selectionEndTag = '</span><!-'+'- END OF SELECTION -'+'->';	
		selectionEnd = HTML.indexOf(selectionEndTag);
	}
		
	if (selectionStart>=0 && selectionEnd>=0) {
	
		var selectionStr = HTML.substring(selectionStart+selectionStartTag.length, selectionEnd);
		selectionStr = cleanSelection(selectionStr, baliseOn, baliseOff, tBaliseOpen, tBaliseClose, tBaliseType);
		
		HTML = HTML.substring(0, selectionStart+selectionStartTag.length) + selectionStr + HTML.substring(selectionEnd);		
		HTML = HTML.replace(/\[[\/]*selection\]/gim, '');
	}	
			
	// Check if node already exists > Exemple //
	// <b>1<b>2</b>3</b> 
	// BECOMES 
	// <b>1</b>2<b>3</b>
	
	var oldHTML = HTML;	
				
	var newHTML = '';
	while(oldHTML.length>0) {
	
		var firstOpen = oldHTML.indexOf(baliseOn);		
		var firstClose = oldHTML.indexOf(baliseOff, firstOpen);
		//if (firstClose>=0) { firstClose += firstOpen+baliseOn.length; }
		
		var secondOpen = oldHTML.indexOf(baliseOn, firstOpen+1);
		//if (secondOpen>=0) { secondOpen+=firstOpen+baliseOn.length; }
		
		var secondClose = oldHTML.indexOf(baliseOff, firstClose+1);
		//if (secondClose>=0) { secondClose+=firstClose+baliseOff.length; }
				
		if (secondOpen<0 || firstOpen<0) {
			// NO DOUBLE OPENING //
			newHTML += oldHTML;
			oldHTML = '';
		} else if (firstClose>secondOpen) {
			// open before close <b>...<b>...</b> //
			
			// Close before secondOpen //
			newHTML += oldHTML.substr(0, secondOpen);
			newHTML += baliseOff;
			
			// Open after firstClose //
			newHTML += oldHTML.substr(secondOpen+baliseOn.length, firstClose-(secondOpen+baliseOn.length));
			newHTML += baliseOn;
						
			newHTML += oldHTML.substr(firstClose+baliseOff.length, secondClose-(firstClose+baliseOff.length));
			newHTML += baliseOff;
						
			oldHTML = oldHTML.substr(secondClose+baliseOff.length);		
			
			
		} else {
			
			newHTML += oldHTML.substr(0, firstClose+baliseOff.length);
			oldHTML = oldHTML.substr(firstClose+baliseOff.length);
			
		}
			
	}
				
	if (newHTML!='') {			
		HTML = newHTML;		
	}
					
	// EMPTY LINK //
	tRegEmptyTxt = '<a[^>]*?><\/a>';
	tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');	
	HTML = HTML.replace(tRegEmpty, '');	
				
	// INCLUDED different Link //
	// <a=x>1<a=y>2</a>3</a> //
	// BECOMES //
	// <a=x>1</a><a=y>2</a><a=x>3</a>
				
	tRegEmptyTxt = '<a href="([^"]*?)"([^>]*?)>((?!<\/a>).)*?<a href="([^"]*?)"([^>]*?)>((?!<\/a>).)*?<\/a>((?!<\/a>).)*?<\/a>';
	tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');	
	HTML = HTML.replace(tRegEmpty, '<a href="$1"$2>$3</a><a href="$4"$5>$6</a><a href="$1"$2>$7</a>');
				
	HTML = HTML.replace(/\[link='([^\]]+)'\]((?!\[\/link\]).)*?\[link='([^\]]+)'\]((?!\[\/link\]).)*?\[\/link\]((?!\[\/link\]).)*?\[\/link\]/gi, '[link=\'$1\']$2[/link][link=\'$3\']$4[/link][link=\'$1\']$4[/link]');	
	HTML = HTML.replace(/\[url='([^\]]+)'\]((?!\[\/url\]).)*?\[url='([^\]]+)'\]((?!\[\/url\]).)*?\[\/url\]((?!\[\/url\]).)*?\[\/url\]/gi, '[url=\'$1\']$2[/url][url=\'$3\']$4[/url][url=\'$1\']$4[/url]');	
					
	// Empty node //	
	if (baliseOff!='') {
	
		var tSafeBaliseOpen = tBaliseOpen.replace(/\[/g, '\\[');
		var tSafeBaliseClose = tBaliseClose.replace(/\]/g, '\\]');;
		var tSimilarBaliseOn = tSafeBaliseOpen+tBaliseType+'[^'+tSafeBaliseClose+']*?'+tSafeBaliseClose;
		
		tRegEmptyTxt = baliseOff; // Exemple : <b></b>
		tRegEmptyTxt = tRegEmptyTxt.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
		tRegEmptyTxt = tRegEmptyTxt.replace(/\//g, '\\/').replace(/\=/g, '\\=');
		tRegEmptyTxt = tSimilarBaliseOn+tRegEmptyTxt;
		tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');		
		
		HTML = HTML.replace(tRegEmpty, '');
	}
		
	// CLEAN LIST //	
	tRegEmptyTxt = '<li><br[ \/]*></li>';
	tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');	
	HTML = HTML.replace(tRegEmpty, '');
		
	tRegEmptyTxt = '<li>[ \n\r]*</li>';
	tRegEmpty = new RegExp(tRegEmptyTxt, 'gi');	
	HTML = HTML.replace(tRegEmpty, '');
	
	// NO <li> in <ul></ul>
	var oldHTML = HTML;
	var newHTML='';
	while(oldHTML.indexOf('<ul>')>=0) {
		var startpos = oldHTML.indexOf('<ul>');
		var endpos = oldHTML.indexOf('</ul>');
				
		if (endpos<0) {
			endpos = oldHTML.length;
		}
		
		var before = oldHTML.substr(0, startpos);
		var after = oldHTML.substr(endpos+5);
		
		var content = oldHTML.substr(startpos+4, endpos-(startpos+4));
		if (content.indexOf('<li>')>=0) {
			before += '<ul>';
			content += '</ul>';	
		}
		newHTML += before + content;
		oldHTML = after;		
		
	}
	if (newHTML!='') { HTML = newHTML+oldHTML; }
	
	// NO <li> in <ol></ol>
	var oldHTML = HTML;
	var newHTML='';
	while(oldHTML.indexOf('<ol>')>=0) {
		var startpos = oldHTML.indexOf('<ol>');
		var endpos = oldHTML.indexOf('</ol>');
				
		if (endpos<0) {
			endpos = oldHTML.length;
		}
		
		var before = oldHTML.substr(0, startpos);
		var after = oldHTML.substr(endpos+5);
		
		var content = oldHTML.substr(startpos+4, endpos-(startpos+4));
		if (content.indexOf('<li>')>=0) {
			before += '<ol>';
			content += '</ol>';	
		}
		newHTML += before + content;
		oldHTML = after;		
		
	}
	if (newHTML!='') { HTML = newHTML+oldHTML; }
	
	if (gEditorControl=='wysiwyg') { 
		$('#'+tWhere).html(HTML);
	} else {
		$('#'+tWhere).val(HTML);
		
		if (textareaStart!=undefined) {
			mess.selectionStart = textareaStart;
			mess.selectionEnd = textareaEnd;
		}
		
	}
				
	if (sel) { 
		// SET SELECTION ON SELECTION NODE FIRST CHILD //		
		if ($('#'+tWhere+' .selection').length>0) {
			//alert('SET SELECTION ON SELECTION NODE FIRST CHILD');
			range.selectNodeContents($('#'+tWhere+' .selection')[0]);
			sel.addRange(range);
		}
	}
				
	if (caretPos>0) {	
		// SHOW CURSOR AT FALSE EMPTY RANGE //				
		var pos = getCaretCharacterOffsetWithin($('#'+tWhere)[0]);	
			//alert(pos);
		setSelectionRange( $('#'+tWhere)[0], pos, pos);	
	}
	
	// Images //		
	$('.temp_img').each(function() {
		var ref = $(this).attr('src');
										
		var fileInfos = pWysiwyg[wysiwygName].pImgList[ref];
		
		var new_image = '<div class="img '+fileInfos['align']+' img_'+fileInfos['align']+' '+ref+'" onclick="lastSelectedImg=this; event.stopPropagation();" contenteditable="false"><img src="'+(CMSRootPath+'/images/'+fileInfos['filename'])+'" width="'+fileInfos['width']+'" height="'+fileInfos['height']+'" title="'+fileInfos['description']+'" alt="'+fileInfos['description']+'" /><img src="'+CMSRootPath+'cms/design/picto_delete.jpg" width="22" height="22" style="position:absolute;top:0px;right:0px;" alt="Delete" onclick="$(this).parent(\'.img\').remove(); lastSelectedImg=null; event.stopPropagation();" /></div>';
		
		if (fileInfos['align']=='center') {
			new_image+='<br />\n';
		} else {
			new_image+='&nbsp;\n';
		}
		
		$(this).before(new_image);						
	});
	$('.temp_img').remove();
	
	document.getElementById(tWhere).focus();
	
	pWysiwyg[wysiwygName].update();
		
}

function getCaretCharacterOffsetWithin(element) {
	var caretOffset = 0;
	var doc = element.ownerDocument || element.document;
	var win = doc.defaultView || doc.parentWindow;
	var sel;
	if (typeof win.getSelection != "undefined") {
		if (win.getSelection().rangeCount > 0) {
			var range = win.getSelection().getRangeAt(0);
			var preCaretRange = range.cloneRange();
			preCaretRange.selectNodeContents(element);
			preCaretRange.setEnd(range.endContainer, range.endOffset);
			caretOffset = preCaretRange.toString().length;
		}
	} else if ( (sel = doc.selection) && sel.type != "Control") {
		var textRange = sel.createRange();
		var preCaretTextRange = doc.body.createTextRange();
		preCaretTextRange.moveToElementText(element);
		preCaretTextRange.setEndPoint("EndToEnd", textRange);
		caretOffset = preCaretTextRange.text.length;
	}
	return caretOffset;
}

function getTextNodesIn(node) {
    var textNodes = [];
    if (node.nodeType == 3) {
        textNodes.push(node);
    } else {
        var children = node.childNodes;
        for (var i = 0, len = children.length; i < len; ++i) {
            textNodes.push.apply(textNodes, getTextNodesIn(children[i]));
        }
    }
    return textNodes;
}
function setSelectionRange(el, start, end) {
    if (document.createRange && window.getSelection) {
        var range = document.createRange();
        range.selectNodeContents(el);
        var textNodes = getTextNodesIn(el);
        var foundStart = false;
        var charCount = 0, endCharCount;
		
        for (var i = 0, textNode; textNode = textNodes[i++]; ) {
				
            endCharCount = charCount + textNode.length;
            if (!foundStart && start >= charCount
                    && (start < endCharCount ||
                    (start == endCharCount && i <= textNodes.length))) {
                range.setStart(textNode, start - charCount);
                foundStart = true;
            }
						
            if (foundStart && end <= endCharCount) {
                range.setEnd(textNode, end - charCount);
                break;
            }
			/*
			if (!foundStart && (i == textNodes.length)) {					
				range.setStart(textNode, 1);
				range.setEnd(textNode, textNode.length);
				//alert('start '+1+' end '+textNode.length);
                break;	
			}
			*/
			
            charCount = endCharCount;
        }

        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (document.selection && document.body.createTextRange) {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(true);
        textRange.moveEnd("character", end);
        textRange.moveStart("character", start);
        textRange.select();
    }
}

function addTag(tTag, tInput) {
	var mess  = document.getElementById(tInput);
	
	tTag = '{ '+tTag+' }';
			
	if (gEditorControl=='wysiwyg') { 
		var sel, range;
		if (window.getSelection) {
			sel = window.getSelection();
			if (sel.rangeCount) {
				range = sel.getRangeAt(0);
										
				replacementText = tTag + range;
				
				range.deleteContents();  

				// Range.createContextualFragment() would be useful here but is
				// non-standard and not supported in all browsers (IE9, for one)
				/*				
				var el = document.createElement(tBaliseType);
				el.innerHTML = replacementText;
				var frag = document.createDocumentFragment(), node, lastNode;
				while ( (node = el.firstChild) ) {
					lastNode = frag.appendChild(node);
				}
				range.insertNode(frag);
				*/
				range.insertNode(document.createTextNode(replacementText));
									
			}
		} else if (document.selection && document.selection.createRange) {
		
			// IE //			
			range = document.selection.createRange();	
			replacementText = tTag + range.text;	
			
			range.text = '';
			range.pasteHTML(replacementText);
		}
		mess = document.getElementById('wysiwyg_'+tInput);
		mess.focus();
	} else {			
		if (document.selection) {	
			// IE		
			mess.focus();		
			sel = document.selection.createRange();
									
			sel.text = tTag;		
			mess.focus();
		} 				
		else if (mess.selectionStart || mess.selectionStart == "0") {
			// OTHERS		
			var startPos = mess.selectionStart;
			var endPos   = mess.selectionEnd;
			var chaine   = mess.value;
																	
			mess.value 			= chaine.substring(0, startPos) + tTag + chaine.substring(endPos, chaine.length);
			mess.selectionStart = Number(startPos);
			mess.selectionEnd   = Number(endPos);
			mess.focus();	
		}	  
		else {		
			// EMPTY Selection									
			mess.value += tTag;
			mess.focus();
		}
	}	
}

function addURL(tWysiwygName) {							
	var url = document.getElementById(tWysiwygName+'_lien').value;													
	if (url.match(/^[0-9\.\/ \+]+$/)==null && url.indexOf('@')<0 && url.indexOf('http://')<0 && url.indexOf('https://')<0) {
		url = 'http://'+url;
	}								
	addStyle('url'+'=\''+url+'\'', tWysiwygName);
}
