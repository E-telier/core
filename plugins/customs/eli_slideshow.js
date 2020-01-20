	var minMargin = 100;
	var maxMargin;
	var direction='left';
	var timing;
	var cursortimer;
	var speed;
	var sliderWidth;
	
	var currentPositionRatio;		
	
	var loadCSSListener;
	
	var resizeTimer = 0;
	
	var dragging = false;
	var dragFrom;
	
	function resizeSlideshow() {
				
		var tContainerWidth = $('#creations').width();
		if (tContainerWidth>240) {
					
			minMargin = 0.1*tContainerWidth;
			maxMargin = -sliderWidth + tContainerWidth - minMargin;
			
			updateCursorPos();
			
			var SliderPos = (currentPositionRatio*(maxMargin-minMargin))+minMargin;
			$("#slideshow").css({'margin-left':SliderPos+'px'});
			
			var tTargetMargin = maxMargin;
			if (direction=='right') {
				tTargetMargin = minMargin;
			}									
			animateTo(tTargetMargin);
			
			//cursortimer = setInterval(function() { updateCursorPos() }, 80);
			
			var tControlPos = $("#controls").offset();
			tControlPos.top+=1;
			tControlPos.left+=1;
			$('#position').draggable({
				containment: [ tControlPos.left, tControlPos.top, tControlPos.left+$("#controls").width()-$('#position').width(), tControlPos.top ]
			});
		}
	}
	
	var images_loaded = false;
	$(window).on('load', function() {
		
		//$('body').prepend('<div>loaded</div>');
		
		// IMAGES LOADED //
		images_loaded = true;
		console.log('images_loades');
	});

	$(document).ready(function() {
		
		//$('body').prepend('<div>ready</div>');
		
		$('#creations').detach().prependTo('.middle');
				
		$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/customs/eli_slideshow.css?d=201912031749" type="text/css" />');	
	
		loadCSSListener = setInterval(function(){
			//$('body').prepend('<div>loadCSSListener '+$("#creations").css("overflow")+'</div>');
			if ($("#creations").css("overflow")==="hidden" && images_loaded===true){
				//$('body').prepend('<div>loadCSSListener loaded</div>');
				clearInterval(loadCSSListener);
				// What you want to do after it loads
				setTimeout(function() { initSlideshow(); }, 50);
			}
		}, 100);
													
	});
	
	function initSlideshow() {
		
		//$('body').prepend('<div>initSlideshow</div>');
		
		$(window).resize(function() {
			
			//$('body').prepend('<div>resize</div>');
		
			console.log('resize');
			$("#slideshow").stop(true, false);			
			clearTimeout(resizeTimer);
					
			resizeTimer = setTimeout(function() { resizeSlideshow(); }, 100);
					
		});

		var imgTransition = $('#creations a img').css('transition');
		$('#creations a img').css({'transition':'none'});
		setTimeout(function() { $('#creations a img').css({'transition':imgTransition}); }, 1);
		
		$('#creations').css({'opacity':'1.0'});
								
		var tNbRea = $('#creations a').length;
		var tReaWidth = $('#creations a').outerWidth(true);
		//console.log('tReaWidth '+tReaWidth);
		var tContainerWidth = $('#creations').width();
		sliderWidth = tNbRea*tReaWidth;
		$('#slideshow').css({'width':sliderWidth+'px'});
		
		minMargin = 0.1*tContainerWidth;
		$("#slideshow").css({'margin-left': minMargin+'px'});
		
		maxMargin = -sliderWidth + tContainerWidth - minMargin;
		speed = tReaWidth/10000; // PIXELS PER MILLISECONDS
		timing = sliderWidth/speed;
								
		animateTo(maxMargin);
																												
		cursortimer = setInterval(function() { updateCursorPos() }, 80);
		
		// Controls bar //	
		setTimeout(function() {
			var tControlPos = $("#controls").offset();
			tControlPos.top+=1;
			tControlPos.left+=1;
			$('#position').draggable({
				containment: [ tControlPos.left, tControlPos.top, tControlPos.left+$("#controls").width()-$('#position').width(), tControlPos.top ],
				start: function(e, ui) { dragging = true; dragFrom = ui.position.left; },
				stop: function(e, ui) { 
					dragging = false; 
					
					var tMargin = maxMargin;
					if (dragFrom>ui.position.left) {
						tMargin = minMargin;
					}
					animateTo(tMargin); 
				}
			});
		}, 500);
		
	}
	
	function animateTo(toMargin, tTiming) {
		
		//console.log(toMargin+' '+tTiming);
		
		if (typeof tTiming === 'undefined') {
			var tDistance = toMargin - parseInt($("#slideshow").css('margin-left'));
			
			tTiming = Math.round(Math.abs(tDistance/speed)); 
			//console.log(tDistance+' '+tTiming);
		}
		if (toMargin < parseInt($("#slideshow").css('margin-left')) ) {
			direction='left';
		} else {
			direction='right';
		}
											
		$("#slideshow").stop(true, false).animate({marginLeft: toMargin+'px'}, tTiming, 'linear', function() { after_func(); });
	}		
	function after_func() {
		
		//console.log($("#slideshow").css('margin-left'));
												
		if (direction=='left') {
			direction='right';
			animateTo(minMargin);
		} else if (direction=='right') {
			direction='left';
			animateTo(maxMargin);
		}
	}
	
	function updateCursorPos() {			
		if (dragging) {
			currentPositionRatio = $('#position').position().left / ($('#controls').width()-$('#position').width());
			var tNewPos = (currentPositionRatio * (maxMargin - minMargin)) + minMargin;
			$("#slideshow").stop(true, false).css({'marginLeft': tNewPos+'px'});
		} else {
			currentPositionRatio = -(parseInt($("#slideshow").css("marginLeft")) - minMargin) / (-maxMargin + minMargin);	
			//console.log(currentPositionRatio);
			if (currentPositionRatio<0) { currentPositionRatio=0; }
			$('#position').css({'left': (currentPositionRatio*($('#controls').width()-$('#position').width()))+'px'});	
		}
			
		
	}