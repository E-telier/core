$(document).ready(function() {
	// LOADER//

	$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/basics/slideshow.css?d=201602031554" type="text/css" />');
	$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/basics/slideshow-custom.css?d=201602031554" type="text/css" />');

	eSlideshow.timer = setInterval(function() {
		// WAIT FOR CSS AND IMAGE LOADED //
		console.log($('.loading').css('z-index'));
		if ($('.loading').css('z-index')=='101' && $('#slider_content').find('img:eq(0)')[0].complete) {
			clearInterval(eSlideshow.timer);
			eSlideshow.initSlideshow();
		}
	}, 100);
	
	$(window).resize(function() {		
		eSlideshow.resizeImages();
	});
	
});

var eSlideshow = new eSlideshowClass();
function eSlideshowClass() {
	
	// PROPERTIES //				
	this.nb_img = 0;
	this.current=0;
	this.moving_nb = 0;
	this.interval = 5000;
	this.timer = 0;
	this.baseIndex = 10;
	this.maxWidth = 0;
	this.maxHeight = 0;
	this.img_list = new Array();
	this.nextEndState = { 'left':'150%' };
	this.nextStartState = { 'left':'50%', 'transition':'' };
	this.previousEndState = { 'left':'50%' };
	this.previousStartState = { 'left':'150%', 'transition':'' };

	this.initSlideshow = function() {
		
		var self = this;

		this.current=0;
		
		$('#slideshow').addClass('loaded');
				
		$('#slider_content .slider img').each(function() {
			var tName = $(this).attr('src');
			tName = tName.substring(tName.lastIndexOf('/'));
			self.img_list.push({'name':tName, 'size':[$(this).attr('width'), $(this).attr('height')], 'description':$(this).attr('alt')});
		});
		
		this.nb_img = this.img_list.length;				
		if (this.nb_img>1) {
			// ACTIVATE SLIDE //
			this.timer = setTimeout(function() { self.navigateSlide(1) }, this.interval);
		} else {
			// ONE IMAGE SELECTED //
			$("#arrows").css({'display':'none'});
		}
		
		// PILE //
		$('.slider').each(function(i) {
			$(this).css({'z-index': self.baseIndex+self.nb_img-i-1});
		});
			
		// SHOW DESCRIPTION //
		var description = this.img_list[this.current]['description'];	
		if (description!=undefined) {
			$('#slideshow .description').html(description);
		}
		
		// SET MENU //
		$('.selector').click(function() {
			var tIndex = $(this).index('.selector');
			self.navigateToSlide(tIndex);
		});
		$('#arrows div').click(function() {
			var step = 1;
			if (this.id=='previous') {
				step = -1;
			}
			self.navigateSlide(step);
		});
		
		this.resizeImages();
			
	}

	this.resizeImages = function() {
		
		var self = this;
		
		this.maxWidth = $('.slider').innerWidth();
		this.maxHeight = $('.slider').innerHeight();
			
		var tRatio = this.maxWidth/this.maxHeight;
		console.log(this.maxWidth+' '+this.maxHeight);
		
		$('.slider').each(function(i) {
			
			var tImgWidth = self.img_list[i]['size'][0];
			var tImgHeight = self.img_list[i]['size'][1];
			var tImgRatio = tImgWidth/tImgHeight;
			
			if (tRatio<tImgRatio && tImgWidth>self.maxWidth) {
				tImgWidth = self.maxWidth;
				tImgHeight = tImgWidth/tImgRatio;
			} else if (tRatio>=tImgRatio && tImgHeight>self.maxHeight) {
				tImgHeight = self.maxHeight;
				tImgWidth = tImgHeight * tImgRatio;
			}
			
			console.log(tImgWidth+' '+tImgHeight);
			$(this).find('img').css({'width':tImgWidth+'px', 'height':tImgHeight+'px'});
			
		});
	}

	this.navigateToSlide = function(num) {
		var step = num-this.current;
		if (step!=0) {
			this.navigateSlide(step);
		}
	}

	this.getSliderNum = function(num) {
		if (num<0) { num = this.nb_img-1; }
		else if (num>=this.nb_img) { num=0; }
		return num;
	}

	this.navigateSlide = function(step) {
		
		var self = this;
		
		if (this.timer) { clearTimeout(this.timer); }
		this.timer = setTimeout(function() { self.navigateSlide(1) }, this.interval);
				
		var destination = this.maxWidth;
		var diff=1;
		if (step<0) {
			//destination = -maxWidth;
			diff=-1;
		}
		for (let i=0;i<Math.abs(step);i++) {
					
			let moving = this.current;
					
			this.moving_nb++;
			var maxMoving = this.nb_img-1;
			if (this.moving_nb>maxMoving) { this.moving_nb--; return false; } // WAIT FOR END LOOP
			
			this.current+=diff;
			this.current = this.getSliderNum(this.current);		
			//console.log('this.current'+this.current);
			if (step<0) { moving = this.current; }
			
			let stepBack = Math.abs(step);
			
			setTimeout(function() {
				//console.log('moving'+moving+' '+self.moving_nb);
				
				if (diff>0) {
					// NEXT //
					$('#slider_'+moving).stop().addClass('moving').css(self.nextStartState).animate(self.nextEndState, 1000, 'easeInSine', function() { 
						// PUT OLD IN BACK AFTER ANIMATION //
						self.moving_nb--;
						var tOrigin = JSON.parse(JSON.stringify(self.nextStartState));
						tOrigin['z-index'] = self.baseIndex+self.moving_nb;
						tOrigin['transition'] = 'none';
						$(this).css(tOrigin).removeClass('moving'); 						
					});	
					
				} else {
					// PREVIOUS //
					var tOrigin = JSON.parse(JSON.stringify(self.previousStartState));
					tOrigin['z-index'] = self.baseIndex+self.nb_img-stepBack+i+1;
					$('#slider_'+moving).stop().addClass('moving').css(tOrigin).animate(self.previousEndState, 1000, 'easeOutSine', function() { 
						// PUT OLD IN BACK AFTER ANIMATION //
						self.moving_nb--;
						$(this).removeClass('moving'); 	
					});	
				}
				
			}, i*100);
			
			// PILE //
			$('.slider').each(function() {
				var tIndex = parseInt($(this).css('z-index'));
				//if (tIndex==moving && diff<0) { return true; }
				$(this).css({'z-index': tIndex+diff});
			});
			
		}

		// UPDATE MENU //
		$('.selector').removeClass('selected');
		$('.selector:eq('+this.current+')').addClass('selected');
			
		// UPDATE DESCRIPTION //
		var description = this.img_list[this.current]['description'];	
		if (description!=undefined) {
			$('#slideshow .description').html(description);
		}
			
	}
}
