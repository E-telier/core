var ePopup = new ePopupClass();
function ePopupClass() {
	
	this.popupURLs={};
	this.interval = 0;
	
	$(window).resize(function() {
		if(typeof window.orientation === 'undefined'){
			$(".popup" ).each(function() {
				var name = $(this).find('.content')[0].id.replace('popup_', '');
				console.log('$(window).resize >> openPopup('+name+')');
				openPopup(name);
			});
		}
	});
	
	if (history!=null && typeof history.pushState=='function') {
		// BACK BUTTON CLOSE POPUP //
		
		var url = window.location.href;
		if (url.indexOf('glid')>=0) {
			url = url.split('?')[0];
			history.replaceState(null, null, url);
		}
		history.pushState(null, null, url);
		console.log(url);
		
		window.addEventListener('popstate', function (e) {				
			//alert(e.state.val+' '+typeof ePopup.popupURLs[e.state.val]);				
			if (typeof ePopup.popupURLs[e.state.val] !== 'undefined') {
				ePopup.closePopup($('#popup_'+e.state.val).find('div'), 'popstate');
				e.preventDefault();
				return false;
			}
		});
	}
	
	this.closePopup = function (JQElem, params) {
		JQElem = JQElem.closest('.popup');	
		if (JQElem.length==0) { JQElem = $('.popup'); }
		
		var tPopupID = JQElem.find('.content')[0].id;	
		var popupName = tPopupID.replace('popup_', '');
		
		//alert(tPopupID);
		
		delete this.popupURLs[popupName];
		
		JQElem.animate({'opacity':'0.0'}, 500, 'swing', 
		function() {					
			JQElem.remove();
		});
		$('html, body').animate({'scrollTop':'0px'}, 500);
		
		if (params!='popstate') {
			// FORCE BACK to cancel pushState
			history.go(-1);
		}
	}
	
	this.openPopup = function (popupName) {
		
		console.log('openPopup '+popupName);
							
		var windowWidth = $(window).width(); //retrieve current window width
		var windowHeight = $(window).height(); //retrieve current window height	
		var vScrollPosition = $(document).scrollTop(); //retrieve the document scroll ToP position
		var hScrollPosition = 0;//$(document).scrollLeft(); //retrieve the document scroll Left position
		
		var popup_container = $('#popup_'+popupName).css({'visibility':'visible'}).parent('.popup_container');
		var popup_size = { 'width': popup_container.outerWidth(), 'height': popup_container.outerHeight() }
						
		var left = Math.max(0, ((windowWidth-popup_size.width)*0.5)+hScrollPosition);
		var top  = Math.max(vScrollPosition, ((windowHeight-popup_size.height)*0.5)+vScrollPosition);
			
		popup_container.css({'left':left+'px', 'top':top+'px'});	
		$('.popup').css({'width':'0px', 'height':'0px'});
		
		var documentWidth = Math.max(0, $(document).width()); //retrieve current document width
		var documentHeight = Math.max(0, $(document).height()); //retrieve current document height
						
		$('.popup').css({'width':documentWidth+'px', 'height':documentHeight+'px'});	
		$('.popup').animate({'opacity':'1.0'}, 750);
		
		if(typeof setCheckboxes == 'function') {
			setCheckboxes();
		}
		if(typeof setRadios == 'function') {
			setRadios();
		}
	}

	this.loadingPopup = function(popupName, tClass) {
		// SET LOADING POPUP //
		
		if (typeof tClass==='undefined') {
			tClass = '';
		}
		
		var html = '<div style="text-align:center; padding:100px;"><img src="'+CMSRootPath+'design/load.gif" width="42" height="21" alt="loading" /></div>';	
		$('body').append('<div class="popup"><div class="popup_container '+tClass+'"><div class="content" id="popup_'+popupName+'" style="visibility:hidden;">'+html+'</div></div></div>');
				
		if ($('head').html().indexOf('popup.css')<0) {
			$.ajax({
				url:CMSRootPath+'css/popup.css?d=201810081118',
				dataType:'text',
				success: function(data) {
					console.log('popup.css loaded');
					$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'css/popup.css?d=201810081118" type="text/css" />');
					
					ePopup.interval = setInterval(function() { 
						if ($('.popup').css('position')=='absolute') { 
							console.log('popup.css ready');
							clearInterval(ePopup.interval);
							ePopup.openPopup(popupName); 
						}
					}, 10);
					
				},
				error: function(xhr, a, b) {
					ePopup.openPopup(popupName);
				}
			});			
		} else {
			this.openPopup(popupName);
		}
	}
	
	this.createPopup = function (popupName, params) {
			
		var popupRef = popupName;
		
		var tNum = 2;
		while($('#popup_'+popupName).length>0) {
			popupName = popupRef+'_'+tNum;
			tNum++;
		} 
				
		if (params==null) {
			params = {};
		}
		
		if (history!=null && typeof history.pushState=='function') {
			// BACK BUTTON CLOSE POPUP //
			history.replaceState({'val':popupName}, 'clean', cleaned_url);
			history.pushState({'val':popupName}, 'popup', cleaned_url);
		}
								
		this.loadingPopup(popupName, params['class']);
			
		// LOAD CONTENT //
		if (typeof params['url']==='undefined') {
			if (popupRef==='message') {
				params['url'] = CMSRootPath+'plugins/basics/_popup-message.php?default=1';
			} else {
				params['url'] = '?&p=form_'+popupRef;
			}
			
		} else if (params['url'].indexOf('?')<0) {
			if (params['url'].indexOf('.')<0) {
				params['url']+='/';
			}
			params['url']+='?url=rewrite';
		}
		
		this.popupURLs[popupName] = params['url'];
		delete params['url'];
		delete params['class'];
		for (var key in params) {					
			this.popupURLs[popupName]+='&'+key+'='+params[key]; 
		}		
		this.popupURLs[popupName]+='&popup=1';
		
		console.log('popup URL = '+this.popupURLs[popupName]);
									
		$.ajax({
			
			contentType:"text/html; charset=utf-8",
			url: this.popupURLs[popupName], 
			dataType:'html',
							
			'success': function(html) {
											
				html = html.substr(html.indexOf('<!-- START OF PAGE -->'));
				html = html.substr(0, html.indexOf('<!-- END OF PAGE -->'));
						
				//alert(html);
				
				
						
				$('#popup_'+popupName).html(html);
				if (params['add_cancel']===true) {
					$('#popup_'+popupName).find('.submit').last().after(' <div class="button cancel" onclick="'+params['cancel']+'">CANCEL</div>');
				}
				
				setTimeout(function() { 
					console.log('ajax.success >> openPopup('+popupName+')'); 
					ePopup.openPopup(popupName);
					
					// CLOSE POPUP ON OUTSIDE CLICK //
					if (popupRef!='message' || params['cancel']!=false) {
						$('#popup_'+popupName).closest('.popup').click(function(e) { 
							if(e.target==e.currentTarget) {
								ePopup.closePopup($('#popup_'+popupName)); 
							}
						});
					}
					
				}, 100);
				
						
			}, 
			'error': function(xhr, error, message) {
				alert('error'+xhr.status+' '+error+' '+message);
				$('.popup_container').each(function() {
					ePopup.closePopup($(this));
				});
			}
		});
		
	}
}