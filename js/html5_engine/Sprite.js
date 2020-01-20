/////////////////////////////////////////////////////////////////////////////////////////////////////////
/* Sprites are core 2D objects to be drawn on each frame */
/* Their properties can be changed on the fly */
/* Z axis is simulated as % from -100=0% to n=(100+n)% */
/* Texts don't support transformation properties like Z axis, width or height */
/* Texts style is defined by their DOM style */
/////////////////////////////////////////////////////////////////////////////////////////////////////////

function Sprite(tSpritesManager, name, tDOM, x, y, width, height, layer, z, opacity, border, bgColor, rotation) {
		
	this.spritesManager = tSpritesManager;
		
	if (!z) { z = 0; }
	if (!opacity) { opacity = 1.0; }
	if (!border) { border=false; }
	if (!bgColor) { bgColor=false; }
	if (!rotation) { rotation=0; }
							
	this.name = name;
	this.DOM = tDOM;
	this.x = x;
	this.y = y;
	this.z = z;
	this.width = width;				
	this.height = height;
	this.layer = layer;
	this.opacity = opacity;
	this.border = border;
	this.bgColor = bgColor;
	this.rotation = rotation*Math.PI/180;
		
	this.draw = draw;
	function draw() {
					
		var canvas = this.spritesManager.engine.scene;
		var context = canvas.getContext('2d');
		
		context.globalAlpha = this.opacity;
		
		if (this.DOM.tagName=='IMG') {
			// IMG //
			var zWidth = Math.round(this.width+(this.z*this.width/100));						
			var zHeight = Math.round(this.height+(this.z*this.height/100));
			if (zWidth<=0 || zHeight<=0) { return false; }
			var tLeft = Math.round(this.x-(zWidth/2));
			var tTop = Math.round(this.y-(zHeight/2));
					
			if (this.border===true) {	
				context.fillStyle = "#000000";
				context.fillRect(tLeft-1,tTop-1,zWidth+2,zHeight+2);		
			}
			
			if (this.bgColor!=false) {
				context.fillStyle = this.bgColor;
				context.fillRect(tLeft,tTop,zWidth,zHeight);		
			}
			
			if (this.rotation!=0) {
				// save the current co-ordinate system 
				// before we screw with it
				context.save(); 
			 
				// move to the middle of where we want to draw our image
				context.translate(this.x, this.y);
			 
				// rotate around that point, converting our 
				// angle from degrees to radians 
				context.rotate(this.rotation);
			 
				// draw it up and to the left by half the width
				// and height of the image 
				context.drawImage(this.DOM, -(zWidth/2), -(zWidth/2), zWidth, zHeight);
			 
				// and restore the co-ords to how they were when we began
				context.restore(); 
				
			} else {
				context.drawImage(this.DOM, tLeft, tTop, zWidth, zHeight);
			}
			
			
		} else if (this.DOM.tagName=='DIV') {
			// TEXT //
					
			// Style Object for supported styles //
			var tStyleObj = this.DOM.style;
			context.font = tStyleObj.fontSize+' \''+tStyleObj.fontFamily+'\'';
			context.textBaseline = 'alphabetic';
			context.fillStyle = tStyleObj.color;
			context.textAlign = tStyleObj.textAlign;
				
			// Parsing Style attribute for unsupported styles //
			var tStyleAttr = this.DOM.getAttribute('style');
			var tStroke = tStyleAttr.indexOf('text-stroke');
			if (tStroke>=0) {						
				tStroke = tStyleAttr.substring(tStroke+('text-stroke').length);
				tStroke = tStroke.substring(0, tStroke.indexOf(';'));
				tStroke = tStroke.replace(':', '');
				tStroke = tStroke.trim();
				context.lineWidth = parseInt(tStroke.split(' ')[0], 10);			
				context.strokeStyle = tStroke.split(' ')[1];
				context.strokeText(this.DOM.innerHTML, this.x, this.y);					
			} 								
			
			context.fillText(this.DOM.innerHTML, this.x, this.y);
			
		}
	}
	
	this.getZWidth = getZWidth;
	function getZWidth() {
		// zWidth = width * ( 1 + ( z/100 ) ) //
		return Math.round(this.width+(this.z*this.width/100));	
	}
	
	this.getZHeight = getZHeight;
	function getZHeight() {
		return Math.round(this.height+(this.z*this.height/100));	
	}
	
	this.getRect = getRect;
	function getRect() {
		var zWidth = this.getZWidth();
		var zHeight = this.getZHeight();
		return { 'left':this.x-(zWidth/2), 'top': this.y-(zHeight/2), 'right': this.x+(zWidth/2), 'bottom': this.y+(zHeight/2) };
	}
	
}
function SpritesManager(tEngine) {

	this.engine = tEngine;

	// prepare sprites layer //
	this.maxSprites = 150;

	this.sprites = new Array();
	for (var i=0;i<this.maxSprites;i++) {
		this.sprites[i] = 0;			
	}
		
	this.engine.registeredForAnimation.push(this);

	this.addSprite = addSprite;
	function addSprite(name, tDOM, x, y, width, height, layer, z, opacity, border, bgColor, rotation) {
		
		if (layer===undefined) {
			layer = 0;
		}
		// Find empty channel starting from asked //
		while(this.sprites[layer]!=0 && layer<this.maxSprites) {
			layer++;
		}		
		var tNewSprite = new Sprite(this, name, tDOM, x, y, width, height, layer, z, opacity, border, bgColor, rotation);
		this.sprites[layer] = tNewSprite;
				
		return this.sprites[layer];
	}

	this.removeSprite = removeSprite;
	function removeSprite(layer) {		
		this.sprites[layer] = 0;		
	}
	
	this.getSpriteNum = getSpriteNum;
	function getSpriteNum(tSprite) {
		for (var i=0;i<this.maxSprites;i++) {
			if (this.sprites[i] == tSprite) {
				return i;
			}			
		}
	}
	
	this.changeZIndex = changeZIndex;
	function changeZIndex(tFrom, tTo) {
		
		//console.log('from '+tFrom+' '+this.sprites[tFrom].name);
		//console.log('to '+tTo+' '+this.sprites[tTo].name);
		
		// Copy at index //
		var tSpriteFrom = this.sprites[tFrom];		
		this.sprites.splice(tTo, 0, tSpriteFrom);
		
		// Delete old index //
		var tDeleteIndex = tFrom;
		if (tFrom>tTo) { tDeleteIndex += 1; }
		this.sprites.splice(tDeleteIndex, 1);	
		
		// Re-index all indexes between //
		var tMax = Math.max(tTo, tFrom);
		var tMin = Math.min(tTo, tFrom);				
		for (var p=tMin;p<=tMax;p++) {
			var tSp = this.sprites[p];
			if (tSp!=0) {				
				tSp.layer = p;
			}
		}
				
	}
	
	this.draw = draw;
	function draw() {		
		for (var i=0;i<this.maxSprites;i++) {
			var tSp = this.sprites[i];
			if (tSp!=0) {				
				tSp.draw();						
			}
		}
	}
	
	this.animate = animate;
	function animate() {
		
		var context = this.engine.scene.getContext('2d');		
		context.clearRect(0, 0, this.engine.scene.width, this.engine.scene.height);
		
		this.draw();
		
	}
	
}