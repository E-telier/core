$(document).ready(function() {	
	eCMS.init();
});

var eCMS = new eCMSClass();
function eCMSClass() {
	
	this.standard_menu = '';
	this.translations = {};
	
	this.init = function() {
		// Manual redirection //
		var redirect = $('.redirect');	
		if (redirect.length>0) {
			window.location.href=redirect.attr('href');
		}
		
		// Set switch Img //
		$('body').on('mouseenter ', '.switch', function() { setImgStatus(this, 'on'); $(this).parent('a').hover(function() { $(this).css({'background-color':'transparent'}); }); });
		$('body').on('mouseleave', '.switch', function() { setImgStatus(this, 'off'); });

		// Set responsive menu //
		/*
		this.standard_menu = $('.menu').html();	
		this.responsiveSpecial();
		var self = this;
		$(window).resize(function() {	
			self.responsiveSpecial();
		});
		*/
		
		// Add links to menu //
		$('.menu_link').each(function(i) {
			var href = $(this).attr('href');		
			$(this).attr('href', CMSRootPath+href);
			$('.menu ul').append('<li><div class="menu_link_'+i+' menu_linked"></div></li>');
			$(this).appendTo('.menu_link_'+i);
		});
		
		// Select text on focus //
		$('input[type="text"]').focus(function() { $(this).select(); });
		
		// Anchors position for combined pages //
		var tSelf = this;
		setTimeout(function() { tSelf.manageAnchorScroll(); }, 40);
	}
	
	this.manageAnchorScroll = function (tURL) {
		//alert('1');
		if (tURL==null) { tURL = (window.location+''); }	
			
		if (tURL.indexOf('#')>=0) {	

			var reference = tURL.substring(tURL.indexOf('#')+1);
			//alert(reference);
			if (reference=='') { return false; }
			
			var tJQElem = $("#"+reference+' .middle');
			
			if (tJQElem.length>0) {
						
				var tTop = 0;
				if ($('.middle').index(tJQElem)>0) {
					tTop = (tJQElem.offset().top)-50;
				}
				
				console.log(tTop);
				
				var tDiffTop = $(window).scrollTop()-tTop;
						
				if (tDiffTop!=0) {				
					$('html, body').stop(true, false);
					$('html, body').animate({
						scrollTop: tTop
					}, Math.abs(tDiffTop)*0.5, 'swing', function() { console.log($(window).scrollTop()); });
				}
			}
			
		}
		//alert('2');
	}

	this.responsiveSpecial = function () {
		var window_width = $(window).width();
		
		var menu_html = $('#header .menu').html();
			
		if (window_width<1024) {															
			if (/ul>/i.test(menu_html)==true) {
													
				menu_html = menu_html.substr(menu_html.toLowerCase().indexOf('<ul>'));
				menu_html = menu_html.substr(0, menu_html.toLowerCase().lastIndexOf('</ul>')+5);
			
				menu_html = menu_html.replace(/<ul>/gim, '<select onchange="window.location.href = this.value;">');
				menu_html = menu_html.replace(/<li class="[^"]*?selected">[^<]*?<a[^>]*?href=/gim, '<option selected="selected" value=');
				menu_html = menu_html.replace(/<li[^>]*?>[^<]*?<a[^>]*?href=/gim, '<option value=');			
				menu_html = menu_html.replace(/<\/a><\/li>/gim, '</option>');
				menu_html = menu_html.replace(/<\/ul>/gim, '</select>');
													
				$('#header .menu').html(menu_html);
			}
													
		} else {
			console.log(menu_html);
			if (menu_html.indexOf('<ul')<0) {
				//alert(standard_menu);
				console.log(this.standard_menu);
				$('#header .menu').html(this.standard_menu);
			}
		}	
		
	}
	
	this.translate = function(request, callback) {
		
		if (typeof eCMS.translations[request] !== 'undefined') {
			callback();
		}
		
		$.ajax({
			
			'url': CMSRootPath+'_eCore/eMain.php',
			'type': 'POST',
			'data': {'get_translation':1,'request':request,'transform':'ucfirst'},
			'dataType': 'json',
			
			'success': function(json) {
				
				var html = json[request];
				eCMS.translations[request] = html;
				
				if (typeof callback !== 'undefined') {
					callback();
				}
				
			}, 
			'error': function (xhr, ajaxOptions, thrownError) {						
				alert('translate error '+xhr.status+' '+ajaxOptions+' '+thrownError);
			  }
			
		});
	}

}