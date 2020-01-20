function Background(tParams) {
	
	this.params = tParams;
	this.speed = gParams.speed*tParams.speed;
	
	this.securityOverlap = 1;
	
	this.backgrounds = new Array();	
	//this.motionBlurs = new Array();
	
	var tLeft = 0;
	// Repeat the backgrounds as much as needed to loop //
	while(tLeft-this.params.width<gScene.width || this.backgrounds.length<2) {		
		this.backgrounds.push(mySpritesManager.addSprite(this.params.name, document.getElementById(this.params.name),Math.round(tLeft+(this.params.width/2)),this.params.y,this.params.width,this.params.height, 0, 0, 1.0, false));
		
		tLeft += this.params.width-this.securityOverlap;
	}
	/*
	for (var m=0;m<this.backgrounds.length;m++) {
		var tBlur = mySpritesManager.addSprite(this.params.name, document.getElementById(this.params.name), this.backgrounds[m].x, this.params.y, this.params.width,this.params.height, 0, 0, 0.3, false);
		
		mySpritesManager.changeZIndex(tBlur.layer, this.backgrounds[m].layer);
		
		this.motionBlurs.push(tBlur);
	}
	*/
	gRegisteredForAnimation.push(this);
	
	this.destroy = destroy;
	function destroy() {
		gRegisteredForAnimation.splice(indexOfObj(gRegisteredForAnimation, this), 1);
		
		for (var b in this.backgrounds) {
			mySpritesManager.removeSprite(this.backgrounds[b].layer);
			this.backgrounds[b] = null;
		}
		this.backgrounds = null;
	}
	
	this.animate = animate;
	function animate(tDeltaTime) {
		
		//console.log(tDeltaTime);
		
		var tChanged = false;
		var tNb = this.backgrounds.length;
		for (var s=0;s<tNb;s++) {
			
			var tSprite = this.backgrounds[s];
			
			//this.motionBlurs[s].x = tSprite.x+1;
			
			//console.log(tDeltaTime*this.speed);
			tSprite.x -= tDeltaTime*this.speed;
			/*
			if (this.params.name=='2_sol') {
				//console.log('bg '+tDeltaTime+' '+(tDeltaTime*this.speed));
				gLastGroundSpeed = tDeltaTime*this.speed;
			}
			*/
			var tDeltaX = (tSprite.width/2)+tSprite.x;
						
			if (tDeltaX<=0) {
				// Repeat background when no more visible //				
				tSprite.x = (((tSprite.width-this.securityOverlap)*(this.backgrounds.length-1))-this.securityOverlap+(tSprite.width/2))+tDeltaX;				
				tChanged = true;
			}
		}
				
	}
	
}