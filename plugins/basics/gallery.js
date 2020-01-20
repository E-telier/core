function Gallery(maxImgWidth, maxImgHeight) {
	this.currentImg = 0;
	this.isMoving = false;
				
	this.screenHeight = $(window).height();
	this.screenWidth = $(window).width();
	
	this.maxImgWidth = maxImgWidth;
	this.maxImgHeight = maxImgHeight;	
}
Gallery.prototype.prepare = function() {
	var html_content = '';
	$('#gallery .img a').each(function(i) {
		var img_html = $(this).html();							
		$(this).closest('.img').html(img_html);		
	});		
							
	$('#gallery .img img').each(function(i) {
		$(this).css({cursor:'pointer'}).click(function() { myGallery.openGallery(i); });
	});	
	
	$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/basics/gallery.css?d=201803131501" type="text/css" />');	
	
	this.loadCSSListener = setInterval(function(){
		if($(".gallery_legend").css("display")==="none"){
			clearInterval(myGallery.loadCSSListener);
			// What you want to do after it loads
			myGallery.init();
		}
	},50);
		
}
Gallery.prototype.init = function() {	

	console.log('galerie init');
	
	$('#gallery').addClass('loaded');
		
	$('#gallery_bg').detach().appendTo('body');
		
	$(window).resize(function() {
		myGallery.resize();
	});
					
	var browser=navigator.appName;
	if (browser=="Netscape") {
		// Firefox //
		document.addEventListener("keyup", function(e) { myGallery.browseByKeyup(e); }, false);
	} else {
		// IE //
		document.attachEvent("onkeyup", function(e) { myGallery.browseByKeyup(e); });
	}
}

Gallery.prototype.resize = function() {
	
	this.screenHeight = $(window).height();
	this.screenWidth = $(window).width();
	
	this.responsiveGallery();
}

Gallery.prototype.openGallery = function(num) {
	$('html, body').stop().animate({'scrollTop':'0px'}, 500, 'swing');
				
	// background //
	$('#gallery_bg').css({'display':'block', top:(this.screenHeight*0.5)+'px', left:(this.screenWidth*0.5)+'px'});				
	var tTiming = 750;
	var tEase = 'swing';
	$("#gallery_bg").stop().animate({width:'100%', height:'100%', top:0, left:0}, tTiming, tEase, function() {
		
		myGallery.responsiveGallery();
					
		var tTiming = 750;
		var tEase = 'swing';
		$("#gallery_slideshow").stop().animate({opacity:1.0}, tTiming, tEase, function() { } );
		
		$("#gallery_closebg").css({width:myGallery.screenWidth+'px', height:myGallery.screenHeight+'px', top:0, left:0});
		$("#gallery_closebg").click(function() { myGallery.closeGallery(); });
		
		$("#gallery_slideshow").click(function(e) { 
			if(e.target !== e.currentTarget) return;
			myGallery.closeGallery(); 
		});
		
	});
	
	$("#gallery_slideshow .img").each(function(i) {
		// set CLICK //
		var tLegend = $(this).children('img').click( function() { myGallery.slideGallery(i); }).attr('alt');
		// set LEGEND //
		$(this).children('.gallery_legend').html(tLegend);
	});
	
	this.slideGallery(num);
}

Gallery.prototype.responsiveGallery = function(animate) {
	
	var greatestHeight = 0;
	
	var tTiming = 500;
	var tEase = 'swing';
	
	var gallery_pos = this.screenWidth/2;
	var gallery_width = 0;
	
	var upDownMargins = 2*40;
									
	$("#gallery_slideshow img").each(function(i) {
		
		var img_block = $(this).closest('.img');
				
		// GET MAX IMAGE SIZE FROM RATIO
		//var legendHeight = img_block.children('.gallery_legend').outerHeight();
		var legendHeight = (2*30) + parseInt(img_block.children('.gallery_legend').css('margin-top'));
		if (img_block.children('.gallery_legend').is(':hidden')) { legendHeight = 0; }
		
		var sideMargins = parseInt(img_block.css('margin-left')) + parseInt(img_block.css('margin-right'));
				
		var availableHeight = myGallery.screenHeight - upDownMargins;
		if (i==myGallery.currentImg) {
			// SELECTED //
			availableHeight -= legendHeight;
		}
		
		var cMaxImgWidth = Math.min(myGallery.maxImgWidth, myGallery.screenWidth - sideMargins );
		var cMaxImgHeight = Math.min(myGallery.maxImgHeight, availableHeight);
							
		// APPLI SIZE COEF TO ORIGIN //
		var defaultWidth = $(this).attr('width')
		var defaultHeight = $(this).attr('height');
		
		var availableZoneRatio = cMaxImgWidth/cMaxImgHeight;
		var imgRatio = defaultWidth/defaultHeight;
		
		var sizeRatio;
		if (imgRatio > availableZoneRatio) {
			sizeRatio = cMaxImgWidth/defaultWidth;
		} else {
			sizeRatio = cMaxImgHeight/defaultHeight;
		}				
		sizeRatio = Math.min(1, sizeRatio);
						
		var newWidth = defaultWidth*sizeRatio;
		var newHeight = defaultHeight*sizeRatio;
											
		if (i!=myGallery.currentImg) {
			newWidth/=2;
			newHeight/=2;
			legendHeight = 0;
		} else {
			var tSrc = $(this).attr('src');
			$(this).unbind('click');
			$(this).click(function() { myGallery.openBig(tSrc); });
			console.log('selected '+cMaxImgHeight+' '+legendHeight+' '+newHeight);
		}
		
		greatestHeight = Math.max(greatestHeight, cMaxImgHeight + legendHeight);
		
		if (animate) {			
			$(this).stop().animate({'width': newWidth+'px','height': newHeight+'px'}, tTiming, tEase);		
			img_block.stop().animate({'margin-top': ((cMaxImgHeight-newHeight)/2)+'px'}, tTiming, tEase, function() { myGallery.isMoving = false; } );
			img_block.children('.gallery_legend').css({'opacity':'0.0'}).stop().animate({'max-width':newWidth+'px'}, tTiming, tEase, function() { img_block.children('.selected .gallery_legend').css({'opacity':'1.0'}); } );
		} else {
			$(this).css({'width': newWidth+'px','height': newHeight+'px'});		
			img_block.css({'margin-top': ((cMaxImgHeight-newHeight)/2)+'px'});
			img_block.children('.gallery_legend').css({'max-width':newWidth+'px', 'opacity':'1.0'});
		}
								
		if (i<myGallery.currentImg) {
			gallery_pos -= newWidth + parseInt(img_block.css('margin-left')) + parseInt(img_block.css('margin-right'));
		} else if (i==myGallery.currentImg) {
			gallery_pos -= (newWidth/2) + parseInt(img_block.css('margin-left'));
		}
		
		gallery_width += newWidth + sideMargins + 5; // ADD SECURITY MARGIN
		
	});	
				
	if (animate) {
		$("#gallery_slideshow").animate({'width': gallery_width+'px', 'left': gallery_pos+'px', top:((this.screenHeight-greatestHeight)/2)+'px'}, tTiming, tEase);
	} else {
		$("#gallery_slideshow").css({'width': gallery_width+'px', 'left': gallery_pos+'px', top:((this.screenHeight-greatestHeight)/2)+'px'});
	}
	
	$('.left_arrow, .right_arrow').css({'top':(this.screenHeight-70)+'px'});
		
}

Gallery.prototype.stepGallery = function(step) {
	var num = this.currentImg+step;
	
	if (num>=0 && num<$("#gallery_slideshow .img").length) {
		this.slideGallery(num);
	}
}

Gallery.prototype.slideGallery = function(num) {
	console.log('slideGallery '+num);
	if (!this.isMoving) {
		
		var gallery_pos = 0;
									
		// reset old selected //
		var selected = $("#gallery_slideshow .selected");
		var selected_img = selected.children('img');
						
		selected_img.off('click');
		var dest_num = this.currentImg;
		selected_img.click( function() { myGallery.slideGallery(dest_num); });
							
		selected.removeClass('selected');
												
		// set new selected //					
		selected = $("#gallery_slideshow .img").eq((num)).addClass('selected');		
		selected_img = selected.children('img');
				
		// set SRC //	
		var this_src = selected_img.attr('src');	
		if (this_src.indexOf('_full.')<0) {
			
			selected_img.on('load', function() {
				
				myGallery.loadClosestImg(num);
								
			});
			
			this_src = this_src.replace(/([\s\S]+)\.([^\.]+)$/gim, '$1_full.$2');	
			selected_img.attr('src', this_src);	
		} else {
			myGallery.loadClosestImg(num);
		}
		
		selected_img.off('click');
		selected_img.click(function() { myGallery.openBig(this_src); });
									
		this.currentImg = num;
		
		if (parseInt($('#gallery_bg').css('top'))===0) {
			this.responsiveGallery(true);
			this.isMoving = true;
		}		
		
		this.showArrows();
	
	}
}

Gallery.prototype.loadClosestImg = function(num) {
	// previous //
	var previous_img = $("#gallery_slideshow .img").eq((num-1));				
	if (previous_img.length>0) {
		var prev_src = previous_img.find('img').attr('src');					
		if (prev_src.indexOf('_full.')<0) {
			prev_src = prev_src.replace(/([\s\S]+)\.([^\.]+)$/gim, '$1_full.$2');	
			previous_img.find('img').attr('src', prev_src);	
		}
	}
	
	// next //
	var next_img = $("#gallery_slideshow .img").eq((num+1));
	if (next_img.length>0) {
		var next_src = next_img.find('img').attr('src');
		if (next_src.indexOf('_full.')<0) {
			next_src = next_src.replace(/([\s\S]+)\.([^\.]+)$/gim, '$1_full.$2');	
			next_img.find('img').attr('src', next_src);	
		}
	}
}

Gallery.prototype.showArrows = function() {
	var tDisplay;
		
	tDisplay = 'block';
	if (this.currentImg==0) {
		tDisplay = 'none';
	}
	$('.left_arrow').css({'display':tDisplay});
	
	tDisplay = 'block';
	if (this.currentImg==$("#gallery_slideshow .img").length-1) {
		tDisplay = 'none';
	}
	$('.right_arrow').css({'display':tDisplay});
}
Gallery.prototype.closeGallery = function() {

	var tTiming = 250;				
	var tEase = 'swing';				
	$("#gallery_slideshow").stop().animate({opacity:0.0}, tTiming, tEase, function() { } );		
				
	tTiming = 1000;				
	$("#gallery_bg").stop().animate({width:'1%', height:'1%', top:(this.screenHeight*0.5)+'px', left:(this.screenWidth*0.5)+'px'}, tTiming, tEase, function() { $("#gallery_bg").css({'display':'none'}); } );
	
	$("#gallery_closebg").unbind("click");
}
Gallery.prototype.openBig = function(url) {
	if (!this.isMoving) {
		window.open(url, '_blank');
	}
}
			
Gallery.prototype.browseByKeyup = function(evenement) {

	var touche = window.event ? evenement.keyCode : evenement.which;
	
	if ($("#gallery_bg").width()>100) {
		if ((touche==37) || (touche==39)) {		
			
			var step;
			
			if (touche==37) { step = -1; } 
			else if (touche==39) { step = 1; $(window).scrollLeft(0); }
						
			this.stepGallery(step);
			
		}
	}
}