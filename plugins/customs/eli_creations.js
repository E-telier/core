	var doc_ready = false;
	var win_loaded = false;
	
	$(document).ready(function() {	
		console.log('doc_ready');
		doc_ready = true;
		
		$('.creations .load').css({'background-image':'url('+CMSRootPath+'design/load.gif)', 'background-repeat':'no-repeat', 'background-position':'center center', 'background-size':'24px 24px'});
				
		start_creations();
	});
	
	window.onload = function() {
		console.log('win_loaded');
		win_loaded = true;		
		start_creations();
	}
	
	function start_creations() {
		//return false;
		if (!doc_ready || !win_loaded) { return false }
		
		$('.creations').css({'opacity':'1.0'});
		$('.creations .load').css({'display':'none'});
		
		gElements = $('.element');	
		gImgNumMax = $('.element').length;			
				
		$('.content .block .element').css({position:'absolute', width:'auto', height:'auto', 'opacity':'0.0', 'margin':'0px'}).each(function(p) {
			$(this).find('img').removeAttr('title').css({'vertical-align':'top', 'width':gMinSize.width+'px', 'height':gMinSize.height+'px'});
			$(this).find('.imgblock a').css({'display':'block'});
			$(this).find('.title').css({'font-size':'12px', 'opacity':'0.0', 'position':'absolute', 'top':'120px', 'left':'0px', 'display':'block', 'background-color':'rgba(0,0,0,0.6)', 'width':'150px', 'color':'#ffffff', 'text-align':'center', 'padding':'5px'});
		});
						
		$(window).resize(function() {
			setElemPos();
		});
		
		setElemPos();
		
		if (typeof(noAnim)=='undefined') {
			setTimeout(function() { gInterval = setInterval(animImg, 40); } , 300);
		} else {
			activateButtons();	
			$('.element').css({'opacity':'1.0'});
		}
		
	}
	
	function setElemPos() {
			
		var containerWidth = $('.content .block .creations').width();
		var margin = 22;		
		var colWidth = $('.content .block .element').outerWidth();
		var rowHeight =  $('.content .block .element').outerHeight();
		
		var tLeft = 0;
		var tTop = 0;
		$('.content .block .element').each(function(p) {
						
			if (tLeft+colWidth>containerWidth) {
				tLeft = 0;
				tTop += rowHeight+margin;
			}
			
			$(this).css({'top':tTop+'px', 'left':tLeft+'px'});
			
			tLeft += colWidth+margin;
			
		});
					
		$('.content .block .creations').css({'height':tTop+rowHeight+'px'});
		
	}
							
	var gImgNum=0;	
	var gInterval;
	var gElements = null;	
	var gImgNumMax = 0;	
	var gMinSize = {'width':100, 'height':75};
	var gMaxSize = {'width':160, 'height':120};
		
	function activateButtons() {
		
		console.log('activateButtons');
		
		$('.element').mouseenter(function() {
			var img = $(this).find('img');
			var tTitle = $(this).children('.title');
			
			var tTiming = 200;
			var tEase = "easeOutBack";							
			img.stop().animate({width:gMaxSize.width+'px', height:gMaxSize.height+'px'}, tTiming, tEase, function() { 
				// After anim, show title //
				tTitle.css({'display':'block', 'marginTop':'0px', 'opacity':'0.0', 'z-index':251});
				var marginTop = tTitle.outerHeight(true);
				//alert('marginTop'+marginTop);
				tTitle.css({'marginTop': -marginTop+'px'});
				var tTiming = 250;
				var tEase = "easeOutBack";
				tTitle.animate({'opacity':'0.99'}, tTiming, tEase, function() { });		
					
			} );
			$(this).css({'z-index':200});
			
			var tLeft = (-(gMaxSize.width-gMinSize.width)/2)+'px';
			var tTop = (-(gMaxSize.height-gMinSize.height)/2)+'px';					
			$(this).css({'box-shadow': '2px 3px 10px 2px #262627'}).stop().animate({'margin-left':tLeft, 'margin-top':tTop}, tTiming, tEase, function() {} );
		}).mouseleave(function() {
		
			var tTitle = $(this).children('.title');
			tTitle.css({'display':'none', 'opacity':'0.0'});
			
			var img = $(this).find('img');			
			var tTiming = 300;
			var tEase = "easeOutBack";
			img.stop().animate({width:gMinSize.width+'px', height:gMinSize.height+'px'}, tTiming, tEase, function() {} );
			$(this).css({'z-index':'1'});
						
			var tLeft = 0+'px';
			var tTop = 0+'px';
			$(this).css({'box-shadow': '2px 3px 10px 2px #C7D2D9'}).stop().animate({'margin-left':tLeft, 'margin-top':tTop}, tTiming, tEase, function() {} );
		});
	}
	
	function animImg() {
		if (gImgNum==gImgNumMax) {
			clearInterval(gInterval);				
			activateButtons();				
		} else {
			var tRandIndex = Math.floor(Math.random()*gElements.length);
			var elem = gElements[tRandIndex];
			gElements.splice(tRandIndex, 1);
			gImgNum++;
			
			var tTiming = 200;
			var tEase = "easeOutBack";
			
			var tTop = parseInt($(elem).css('top'));
			
			$(elem).css({'top':(tTop+200)+'px'}).animate({top:tTop+'px', 'opacity':'1.0'}, tTiming, tEase);
		}
	}