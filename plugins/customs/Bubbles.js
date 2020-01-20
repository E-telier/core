function Bubbles() {
		
	// VARIABLES //		
	this.bubbles = new Array();
	this.engine = new Engine(this);
			
	this.engine.loadAndStart(document.getElementById('scene'), CMSRootPath+'plugins/customs/bubbles.txt?d=201911271038');
	
	this.scrollTop = 0;
	
	this.timeout = 0;		
		
	// CONSTRUCTOR //
	this.init = init;
	function init() {	
		console.log('init');
		$('#bubbles').css({'width':getWindowWidth()+'px', 'height':getWindowHeight()+'px', 'display':'block'}).animate({'opacity':'1.0'}, 1000).find('canvas').attr('width', getWindowWidth()).attr('height', getWindowHeight());
		this.createBubbles();		
		this.engine.registeredForAnimation.push(this);
	}
				
	this.destroy = destroy;
	function destroy() {
		console.log('destroy');
		
		var tIndex = indexOfObj(this.engine.registeredForAnimation, this);
		if (tIndex>=0) {		
			this.engine.registeredForAnimation.splice(tIndex, 1);
		}
					
		for (var p in this.bubbles) {			
			this.engine.mySpritesManager.removeSprite(this.bubbles[p][0].layer);
			this.bubbles[p][0] = null;
		}		
		this.bubbles = null;
	}	
		
	this.animate = animate;
	function animate(tDeltaTime) {
		//console.log(this.bubbles.length);
		for (var p in this.bubbles) {
			
			if (this.bubbles==null) { return 0; }
			
			var tBubble = this.bubbles[p];
			var tSprite = this.bubbles[p][0];
			
			var tBottom = tSprite.y + (tSprite.height/2);			
			if (tBottom<=0) {
				tSprite.y = getWindowHeight() + (tSprite.height/2) + tBottom;
			}
			
			var tTop = tSprite.y - (tSprite.height/2);			
			if (tTop>=getWindowHeight()) {
				tSprite.y = - (tSprite.height/2) + (tTop - getWindowHeight());
			}
			
			tSprite.y -= tDeltaTime * tBubble[1];
			//console.log(this.bubbles[p][0].y);
		}
		//console.log(this.bubbles.length);
	}
	
	////////////////////////////////////////////
	
	this.resize = resize;
	function resize() {
		clearTimeout(this.timeout);		
		this.destroy();
		$('#bubbles').stop(true, false).css({'opacity':'0.0'});
		
		var self = this;
		
		this.timeout = setTimeout(function() { self.init() }, 1000);
	}
	
	this.scroll = scroll;
	function scroll() {
		
		var tDiff = $(window).scrollTop()-this.scrollTop
		this.scrollTop = $(window).scrollTop();
		
		for (var p in this.bubbles) {
			
			var tBubble = this.bubbles[p];
			var tSprite = this.bubbles[p][0];
						
			tSprite.y -= tDiff * tBubble[1] * 10;
		}
	}
	
	this.createBubbles = createBubbles;
	function createBubbles() {

		this.bubbles = new Array();
		this.engine.scene.getContext('2d').clearRect(0, 0, this.engine.scene.width, this.engine.scene.height);
		
		var nb = parseInt(getWindowWidth()/100);
										
		for (var i=0;i<nb;i++) {
		
			var page_width = $('.page').outerWidth();
			var margin = (getWindowWidth()-page_width)/2;
		
			var size = 25+(((i+1)/nb)*325);
			//console.log('size '+size);
			var opacity = (40+((i/nb)*40))/100;
							
			var left = parseInt(Math.random()*getWindowWidth())-parseInt(size/2); 
			var top = parseInt(Math.random()*getWindowHeight())-parseInt(size/2);
									
			var tSpeed = (size/20) * (this.engine.frameTime/1000);
			
			var tSprite = this.engine.mySpritesManager.addSprite('bubble_'+this.bubbles.length, document.getElementById('plugins-customs-bubble.png'), left+(size/2), top+(size/2), size, size, this.bubbles.length, 0, opacity, false, '#CEE7F2');
			this.bubbles[this.bubbles.length] = new Array(tSprite, tSpeed);
		}
	}
		
}