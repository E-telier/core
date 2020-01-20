/////////////////////////////////////////////////////////////////////////////////////////////////////////
/* Texts are game objects that add animation to text sprites */
/* They can have a speed and a duration */
/* They are killed by moving out of the screen or fading out */
/////////////////////////////////////////////////////////////////////////////////////////////////////////

function Text(tID, tX, tY, tText, tFormat, tTimer, tSpeed) {
		
	var tName = 'text_'+tID;
	
	this.DOM = document.createElement('div');
	this.DOM.id = tName;	
	this.DOM.setAttribute('style', tFormat);
	this.DOM.innerHTML = tText;
	document.getElementById('assets').appendChild(this.DOM);
	
	this.timer = tTimer;
	this.speed = tSpeed;
	this.opacity = 1.0;
	if (tFormat.indexOf('opacity')>=0) {
		var tOpacity = tFormat.substring(tFormat.indexOf('opacity'));
		tOpacity = tOpacity.substring(0, tOpacity.indexOf(';'));
		tOpacity = tOpacity.replace(':', '');
		tOpacity = tOpacity.trim();
		tOpacity = parseFloat(tOpacity);
		this.opacity = tOpacity;
	}
	
	this.sprite = mySpritesManager.addSprite('text', this.DOM, tX, tY, gScene.width, gScene.height, 70);	
	this.sprite.opacity = this.opacity;
	
	gRegisteredForAnimation.push(this);
	
	this.destroy = destroy;
	function destroy() {		
		gRegisteredForAnimation.splice(indexOfObj(gRegisteredForAnimation, this), 1);
		mySpritesManager.removeSprite(this.sprite.layer);	
		document.getElementById('assets').removeChild(this.DOM);		
	}
	
	this.animate = animate;
	function animate(tDeltaTime) {
		
		var tKill = false;
		
		if (this.timer!=undefined) {
			this.timer -= 20;
			
			// Fade out the last 10 frames //
			if (this.timer<20*10) {
				this.opacity -= 0.1; 
				this.sprite.opacity = this.opacity;
			}
			
			// Kill at ended duration //
			if (this.timer<=0) {				
				tKill = true;		
			}
		}
		
		if (this.speed!=undefined) {
			this.sprite.x += this.speed*tDeltaTime;
			
			// Kill if out of screen //
			if (this.sprite.x<-gScene.width || this.sprite.x>=gScene.width) {			
				tKill = true;
			}
		}
		
		if (tKill==true) {
			myGame.myTextsManager.removeText(this);
		}
		
	}
	
}

function TextsManager() {
	
	this.texts = new Array();
	this.lastID = 0;
	
	this.destroy = destroy
	function destroy() {
		while (this.texts.length>0) {			
			this.removeText(this.texts[0]);			
		}
		this.texts = null;
	}
	
	this.addText = addText;
	function addText(tX, tY, tText, tFormat, tTimer, tSpeed) {
		this.lastID++;
		var tNewText = new Text(this.lastID, tX, tY, tText, tFormat, tTimer, tSpeed);
		this.texts.push(tNewText);
		return tNewText;
	}
	
	this.removeText = removeText;
	function removeText(tText) {
		tText.destroy();
		var tIndex = indexOfObj(this.texts, tText);				
		if (tIndex>=0) {
			this.texts.splice(tIndex, 1);
		}
	}
	
	this.hide = hide;
	function hide() {
		for (var t in this.texts) {			
			this.texts[t].sprite.opacity = 0.0;
		}
	}	
	this.show = show;
	function show() {
		for (var t in this.texts) {
			// Restore current opacity //
			this.texts[t].sprite.opacity = this.texts[t].opacity;
		}
	}
	
}

