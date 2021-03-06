$(document).ready(function() {
		
	$('body').on('keypress', 'input', function(event) {
		
		//console.log('key '+event.which);
		
		if ( event.which == 13 ) {
			console.log('CONNECT ENTER KEY');			
			
			var tForm = $(this).closest('form');
			console.log(this.tagName+' '+tForm.length+' '+tForm.find('.submit').length);
			if (tForm.hasClass('no_enter_submit')) {
				console.log('no_enter_submit');
				event.preventDefault();
			} else {
			
				var submit_btn = tForm.find('.submit');
				if (submit_btn.length>0) {
					console.log('trigger click');
					submit_btn.click();		
					event.preventDefault();
				}	
			}
		}
	});
	
	$('body').on('click', '.submit', function(event) { if (event.target.onclick==null) { eForm.submit(event.target); } });
				
});

// STATIC CLASS //
var eForm = new eFormClass();
function eFormClass() {

	this.formSent = false;
	
	this.submittedBtn = {};	
	this.submit = function(tDom) {
				
		this.submittedBtn.jq = $(tDom);
		
		if (this.submittedBtn.jq[0].tagName=='INPUT') {
			this.submittedBtn.txt = this.submittedBtn.jq.val();
			this.submittedBtn.jq.val('loading...');
		} else {
			this.submittedBtn.txt = this.submittedBtn.jq.html();
			this.submittedBtn.jq.html('<img src="'+CMSRootPath+'design/loader.gif" width="32" height="32" alt="loading..." />');
		}
		
		this.submittedBtn.jq.after('<div class="submitted"><img src="'+CMSRootPath+'design/loader.gif" width="80" height="80" alt="loading..." /><br /><span>Envoi...</span></div>');
		this.submittedBtn.jq.css({'display':'none'});
		
		$('body').off('click', '.submit');
		
		if ($(tDom).closest('.popup').length>0) {
			this.asyncSubmitForm($(tDom).closest('form')[0].id);
		} else {
			this.checkForm($(tDom).closest('form')[0].id);
		}
	}
	this.failedSubmit = function() {
		
		if (typeof this.submittedBtn.jq == 'undefined') {
			return false;
		}
		
		this.submittedBtn.jq.parent().find('.submitted').remove();
		this.submittedBtn.jq.css({'display':'inline-block'});
		
		if (this.submittedBtn.jq[0].tagName=='INPUT') {
			this.submittedBtn.jq.val(this.submittedBtn.txt);			
		} else {
			this.submittedBtn.jq.html(this.submittedBtn.txt);			
		}
		$('body').on('click', '.submit', function(event) { eForm.submit(event.target); });
		
		if (typeof submitCallBack !== 'undefined') {
			submitCallBack(0);
		}
		
	}
	this.successSubmit = function(formID) {
		
		if (typeof this.submittedBtn.jq == 'undefined') {
			document.getElementById(tFormID).submit();
			return false;
		}
		
		if (this.submittedBtn.jq[0].tagName=='INPUT') {
			this.submittedBtn.jq.val($("<div>").html(' &nbsp; &#10003; &nbsp; ').text());			
		} else {
			this.submittedBtn.jq.html(' &nbsp; &#10003; &nbsp; ');			
		}
				
		$('.submitted').html('<img src="'+CMSRootPath+'design/loaded.gif" width="80" height="80" alt="loaded" /><br /><span>Envoi réussi !<br />Le '+date2Str(new Date(), 'd/m/Y H:i:s')+'</span>');
		
		var tFormID = formID;
		setTimeout(function() { document.getElementById(tFormID).submit(); }, 1000);
		
		if (typeof submitCallBack !== 'undefined') {
			submitCallBack(1);
		}
		
	}
	
	this.checkFields = function(container, hideResult) {
		var error='';
		
		var self = this;
			
		$(container).find('input, select, textarea').not('.button').removeClass('invalid').addClass('valid');
		
		console.log('PASSWORD');
		// PASSWORD CONFIRMATION //
		if ($(container).find('#password_2').length>0) {
			if ($(container).find('#password').val()!=$(container).find('#password_2').val()) {
				$(container).find('#password_2').addClass('invalid').removeClass('valid');
				error='error : incorrect password confirmation';
			}
		}
		if ($(container).find('#old_password').length>0 && $(container).find('#password').length>0) {
			if ($(container).find('#old_password').val()=='' && $(container).find('#password').val()!='') {
				$(container).find('#old_password').addClass('invalid').removeClass('valid');
				error='error : incorrect old password';
			}
		}
		
		console.log('NEEDED');
		// NEEDED //		
		$(container).find('.needed').each(function(i) {
			
			var fieldtype = $(this).attr('type');
					
			if (fieldtype=='checkbox') {
				if ($(this).is(':checked')==false) {	
					$(this).addClass('invalid').removeClass('valid');
					if (typeof errorCheckBox == 'function') { errorCheckBox($(this).attr('name'), formID); }
					error='error : missing checkbox';
				}
			} else {
				if ($(this).val()=='') {	
					$(this).addClass('invalid').removeClass('valid');
					error='error : missing field';					
				}
			}
						
		});
		
		console.log('FORBIDDEN');
		// FORBIDDEN //
		$(container).find('.forbidden').each(function(i) {
			if ($(this).val()!='') {	
				$(this).addClass('invalid').removeClass('valid');
				error='error : field is forbidden';
			}
		});
		
		console.log('LIMITS');
		// LIMITS //
		$(container).find('.limits').each(function(i) {
			var classes = $(this).attr('class');
			var text = $(this).prev('div').html();
			text = text.replace(/[\n\r]/gim, '');
			if (text.lastIndexOf('<br>')==text.length-4) { text = text.substring(0, text.length-4); }
			
			//alert(classes);
			
			var max_lines_pos = classes.indexOf(' max_lines_');
			//alert(max_lines_pos);
			if (max_lines_pos>0) {
				var max_lines = parseInt(classes.substring(max_lines_pos+11, classes.indexOf(' ', max_lines_pos+1)));
				var lines = text.split('<br>');
				//alert(lines.length+' '+max_lines);
				if (lines.length>max_lines) {
					error = 'error : too many lines';
					$(this).prev('div').addClass('invalid').removeClass('valid');
				}
			}
					
			text = text.replace(/[\n\r]/gim, '');
			text = text.replace(/<[^>]*>/gim, '');
			text = text.replace(/&nbsp;/gim, ' ');	
			text = text.replace(/&[^ ]*;/gim, '');
					
			var max_chars_pos = classes.indexOf(' max_chars_');
			if (max_chars_pos>0) {
				var max_chars = parseInt(classes.substring(max_chars_pos+11, classes.indexOf(' ', max_chars_pos+1)));
				if (text.length>max_chars) {
					error = 'error : too many characters';
					$(this).prev('div').addClass('invalid').removeClass('valid');
				}
			}
					
		});
		
		console.log('TAX');
		// TAX ID //
		var tCountries = new Array('Belgium', 'France', 'Luxembourg', 'Nederlands', 'Germany', 'Spain');
		var tCountriesParams = {
			'Belgium'   : {'prefix':'BE', 'num':10},
			'France'    : {'prefix':'FR', 'num':11},
			'Luxembourg': {'prefix':'LU', 'num':8},		
			'Germany'   : {'prefix':'DE', 'num':9},
			'Spain'     : {'prefix':'ES', 'num':9},
			
			'Nederlands': {'regex':new RegExp('^NL[0-9]{9}[A-Z]{1}[0-9]{2}$')}
		}
		var tCountry = $('select[id^="country"]').val();
		var tImposeFormat = false;
		if (tCountries.indexOf(tCountry)>=0) { 
			tImposeFormat=true; 
			var regexTaxID;
			if (tCountriesParams[tCountry]['regex']!=undefined) {
				regexTaxID = tCountriesParams[tCountry]['regex'];
			} else {
				regexTaxID	= new RegExp('^'+tCountriesParams[tCountry]['prefix']+'[0-9]{'+tCountriesParams[tCountry]['num']+'}$');
			}
		}
						
		$(container).find('.tax_id').each(function(i) {
			var tValue = $(this).val();
			if (tValue!='') {
				tValue = tValue.replace(/ /g, '');
				tValue = tValue.replace(/\./g, '');
				tValue = tValue.toUpperCase();
				if ((tImposeFormat && !regexTaxID.test(tValue)) || (!tImposeFormat && tValue.indexOf(':')>=0)) {			
					$(this).addClass('invalid').removeClass('valid');
					error='error : { '+tValue+' } is not a valid tax id';
				} else {
					if (tCountry=='Belgium') {
						tNewValue = tValue.substr(0,2)+' '+tValue.substr(2,4)+' '+tValue.substr(6, 3)+' '+tValue.substr(9,3);
					} else {
						tNewValue = tValue.substr(0,2)+' '+tValue.substr(2);
					}
					$(this).val(tNewValue);
				}
			}
		});
		
		console.log('EMAIL');
		// EMAIL //
		var regexEmail = /^[a-z0-9_\-\.]+@[a-z0-9_\-]+\.[a-z0-9_\-]+$/;
		$(container).find('.email').each(function(i) {
			var tValue = $(this).val().toLowerCase();
			tValue = tValue.replace(/[ ]+/gim, '');
			$(this).val(tValue);
			if (tValue!='') {
				if (!regexEmail.test(tValue)) {			
					$(this).addClass('invalid').removeClass('valid');
					error='error : { '+tValue+' } is not a valid e-mail address';
				}
			}
		});
		
		console.log('NUMBER');
		// FORCE NUMBER //
		$(container).find('.number').each(function(i) {
			if (isNaN(parseInt($(this).val()))) {
				$(this).addClass('invalid').removeClass('valid');
				error = 'error : { '+$(this).val()+' } is not a number';
			}
		});
		
		console.log('DECIMALS');
		// DECIMALS //
		$(container).find('.decimals').each(function(i) {
			var value = formatToDecimal($(this).val());
			$(this).val(value);
			//alert(value+' '+Math.round(value));
		});
		
		console.log('WYSIWYG');			
		// WYSIWYG CLEAN //
		if (!error) {
			$(container).find('div[id^="wysiwyg_"]').each(function() {
				var tName = this.id.replace('wysiwyg_', '');
							
				clearInterval(pWysiwyg[tName].interval);
				pWysiwyg[tName] = null;
				
				var tTextAreaJQ = $('textarea[name="'+tName+'"]');
				self.cleanWysiwyg(tTextAreaJQ);
				
			});
		}
		
		if (hideResult) { $(container).find('input, select, textarea').not('.button').removeClass('invalid').removeClass('valid'); }
		
		return error;
	}
	
	this.cleanWysiwyg = function(tTextAreaJQ) {
		var tText = tTextAreaJQ.val();
				
		var tSelection = /<span class="selection">(.*?)<\/span>(<\!-- END OF SELECTION -->)?/gim;	
		tText = tText.replace(tSelection, '$1');
				
		var tZeroWidth = new RegExp("[\u200B\u200C\u200D\uFEFF]", "gim");
		tText = tText.replace(tZeroWidth, '');
		
		var tEmptyTag = /\[([^ ]+)[^\]]*\][ \n\r\t]*\[\/\1\]/gim;	
		while((/\[([^ ]+)[^\]]*\][ \n\r\t]*\[\/\1\]/gim).test(tText)) {			
			tText = tText.replace(tEmptyTag, '');
		}
						
		tTextAreaJQ.val(tText);
	}
	
	this.checkForm = function (formID, parameters) {
			
		if (formID==null) { formID = 'main_form'; }		
		
		var error = this.checkFields($('#'+formID)[0]);
		
		console.log(error+' '+parameters+' '+this.formSent);
		if (!error) {
			
			$('#'+formID).parent().find('.error').remove();
			
			if (parameters=='NO_SUBMIT') {
				return true;
			} else if (this.formSent==false) {
				// FORBID DOUBLE SUBMIT //
				this.formSent = true;
							
				// NO CONFIRM ON CLOSE
				$(window).off('beforeunload');
				
				// SCROLL TOP AND SUBMIT
				//$('html, body').animate({'scrollTop':$('#'+formID).offset().top+'px'}, 500, 'swing', function() { document.getElementById(formID).submit(); });
				this.successSubmit(formID);
								
				// LOADING POPUP //
				/*
				if (typeof ePopup !== 'undefined') {
					ePopup.loadingPopup('empty');
				}
				*/
			} 
		} else {
						
			this.manageError(error, formID, parameters);
			
			$('html, body').animate({'scrollTop':$('#'+formID).offset().top+'px'}, 500, 'swing', function() { });
			return false;
		}
		
	}

	this.manageError = function (error, formID, parameters) {
		
		if (parameters!='NO_SUBMIT' && $('#'+formID+' .submitted').length>0) {
			this.failedSubmit();
		}
		
		this.formSent = false;
		$('.formsent').remove();
				
		var error_message = error.replace(/\{ [^\}]+ \}/gmi, '{ data }');
		var data_message = error.replace(/[^\{]*\{ ([^\}]+) \}[^\}]*/gmi, '$1');
			
		console.log(CMSRootPath+'_eCore/eLang.php');
		console.log(error+'-'+error_message);
					
		$.ajax({
			
			'url': CMSRootPath+'_eCore/eMain.php',
			'type': 'POST',
			'data': {'get_translation':1,'request':error_message,'transform':'ucfirst'},
			'dataType': 'json',
			
			'success': function(json) {
								
				var html = json[error_message];
								
				html = html.replace('{ data }', data_message);
				html = html.replace('\"', '"');
				
				if ($('.error').length==0) {
					$('#'+formID).before('<div class="error">'+html+'</div>');
				} else {
					$('.error').html(html).css({'display':'block'});
				}
				
				$('.success').remove();
				
			}, 
			'error': function (xhr, ajaxOptions, thrownError) {			
				alert('translate error '+xhr.status+' '+ajaxOptions+' '+thrownError);
			  }
			
		});
	}

	this.getFormDatas = function (formID, tDataType) {

		var datas = new Object();
		$('#'+formID+' input').each(function() {
			var ok = true;
			var this_input = this;
			if ((this.type=='radio') || (this.type=='checkbox')) {
				if (this.checked!=true) {
					ok=false;
				}
			}
			if (ok) {
				datas[this.name]=this.value;
			}
		});
		$('#'+formID+' select').each(function() {
			datas[this.name]=this.value;
		});
		$('#'+formID+' textarea').each(function() {
			datas[this.name]=this.value;
		});
		
		if (tDataType=='text') {
			var datasTxt = '';
			for (key in datas) {
				if (datasTxt!='') { datasTxt += '&'; }
				datasTxt += key+'='+datas[key];
			}
			return datasTxt;
		}	
		
		return datas;
	}
	this.asyncSubmitForm = function (formID) {
		
		console.log('asyncSubmitForm '+this.formSent);
		
		if (this.formSent==false) {
		
			this.formSent = true;
			//$('#'+formID).append('<div class="formsent center"><img src="'+CMSRootPath+'design/load.gif" width="32" height="32" alt="loading..." /></div>');
			
			$(window).unbind('beforeunload');

			var tPopupID = formID;
			var tPopup = $('#'+formID).closest('.content');
			if (tPopup.length>0) {
				tPopupID = tPopup[0].id;	
			}
			var popupName = tPopupID.replace('popup_', '');
			
			var datas;
			var params = {};
			if(typeof FormData !== 'undefined') {
				datas = new FormData( $('#'+formID)[0] );
				datas.append('ajax', '1');
				params.processData = false;
				params.contentType = false;
				params.dataType = 'text';
			} else {
				// IE 8-9
				datas = getFormDatas(formID, 'text');
				datas += '&ajax=1';
				
				params.processData = true;
				params.contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
				params.dataType = 'text';
			}
			
			$.ajax({
				
				url: ePopup.popupURLs[popupName],
				'type': 'POST',
				data: datas,
				processData: params.processData,
				contentType: params.contentType,
				'dataType': params.dataType
				
				,
				'success': function(html) {
														
					var error = false;
					var result = html;
					if (html.indexOf('<!-'+'- AJAX RESULT -'+'->')>0) {
						var regex = new RegExp('[\\s\\S]+<!-'+'- AJAX RESULT -'+'->([\\s\\S]+)<!-'+'- END OF AJAX RESULT -'+'->[\\s\\S]+', 'gim');	
						result = html.replace(regex, '$1');
						
						if (result.toUpperCase().indexOf('[ERROR] : ')>=0) {
							//var noHTML = $('<div/>').html(result).text();
							result = result.replace(/\[ERROR\] : /gim, '');		
							eForm.manageError(result, formID);
							
							error = true;
						}
					} else {	
						console.log('no AJAX return');
					}
					
					eForm.formSent = false;
					$('.formsent').remove();
					
					if (!error) {
						if(typeof asyncFormSubmitted == 'function') {			
							asyncFormSubmitted(result);
						} else {			
							window.location.href = window.location.href+'';
						}
					}
				}, 
				'error': function(xhr, ajaxOptions, thrownError) {
					alert('error');
					alert(xhr.status);
					alert(thrownError);
					
					eForm.formSent = false;
					$('.formsent').remove();
					
				}
				
			});
		}
	}
}


