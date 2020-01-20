/////////////////////////////////////////////////////////////////////////////////////////////////////////
/* Objects are moving parts, with or without shooting value */
/* Their speed is relative to their background */
/* Their value is used for counting points */
/////////////////////////////////////////////////////////////////////////////////////////////////////////

function GameObject(tParams) {
		
	this.params = tParams;
	
	this.status = 'pending';
	this.sprite = null;
					
	this.init = init;
	function init() {
		this.framecount=1;
		this.pictureTaken = false;

		this.status = 'used';
		
		this.currentframe = 0;
		this.time = 0;
		
		this.loop = true;
		if (this.params.hasOwnProperty('loop')) {
			this.loop = this.params.loop;
		}		
		
		// Define object speed based on its background level //		
		this.speed = 0;
		
		var tBgSpeed = 0;
		for (var b in gParams.backgrounds) {
			var tBG = gParams.backgrounds[b];
			if (tBG.name==this.params.background) {
				tBgSpeed = tBG.speed;
			}
		}
		
		// Find speed >=1 //
		var limit = 0;
		while(Math.abs(this.speed)<0.5) {
			var dY = this.params.max_y-this.params.min_y;
			var tY = this.params.min_y+Math.round(Math.random()*dY);
			var tRatio = (tY-this.params.min_y)/dY;

			// set scale to proportional distance : from 100% to 50% //
			var tZ = -50+(50*tRatio);

			// set speed to proportional distance //	
			var tMinSpeed = this.params.min_speed;
			var tMaxSpeed = this.params.max_speed;
			var tProportionalSpeed = tMinSpeed+(tRatio*(tMaxSpeed-tMinSpeed));
						
			this.speed = (gParams.speed*tBgSpeed)+tProportionalSpeed;
						
			if (Math.abs(this.speed)<0.5*gParams.speed) {
				console.log('TOO SLOW : '+this.params.name+' '+this.speed);
			}
			
			limit++;
			if (limit>=3) {
				// Speed not found -> Recycle object //
				this.speed = 0;
				myGame.myObjectsManager.pendingObject(this);
				break;
			}
		}		

		if (this.speed!=0) {
						
			var tX = gScene.width+(this.params.width/2);
			if (this.speed<0) {
				tX = -(this.params.width/2);
			}
			tX = Math.round(tX);
						
			gRegisteredForAnimation.push(this);
							
			if (this.sprite==null) {
				//console.log('new sprite');
				this.sprite = mySpritesManager.addSprite(this.params.name, document.getElementById(this.params.name+'_01'), tX, tY, this.params.width, this.params.height, 20, tZ, 1.0, false);
			} else {
				//console.log('recycle sprite');
				this.sprite.x = tX;
				this.sprite.y = tY;
				this.sprite.z = tZ;
			}
						
		} 
	}
	
	this.animate = animate;
	function animate(tDeltaTime) {
						
		tPlayAnim = false;
		
		if (this.loop===false) {
			if (this.currentframe<this.params.frames) {
				if (this.params.hasOwnProperty('start')) {
					this.time += tDeltaTime*(1000/gFramerate);
										
					if (this.time >= this.params.start || this.currentframe==0) {
						// START ANIM AT DUE TIME //
						tPlayAnim = true;
					}
					
				} else {
					tPlayAnim = true;
				}
			}
		}		
						
		if (this.loop==true || tPlayAnim==true) {						
			// Animate 12 FPS //
			if (this.framecount>=gFramerate/12 && this.params.frames>1) {
								
				var cNum = this.currentframe;
				
				if (cNum<this.params.frames) {
					cNum++;
				} else {					
					cNum = 1;				
				}
				
				this.currentframe = cNum;
				
				if (cNum<10) { cNum = '0'+cNum; }
							
				this.sprite.DOM = document.getElementById(this.params.name+'_'+cNum);
					
				this.framecount=1;
					
			}
			
		}
		
		// Move sprite //		
		this.applySpeed(tDeltaTime);
		
		// Count frames for animation //
		this.framecount++;
				
	}
	
	this.destroy = destroy;
	function destroy() {
		gRegisteredForAnimation.splice(indexOfObj(gRegisteredForAnimation, this), 1);
		mySpritesManager.removeSprite(this.sprite.layer);		
	}
	
	this.applySpeed = applySpeed;
	function applySpeed(tDeltaTime) {
		this.sprite.x -= tDeltaTime*this.speed;
		/*
		if (this.speed==0) {
			//console.log('obj '+tDeltaTime+' '+(tDeltaTime*this.speed));
			if (gLastGroundSpeed!=tDeltaTime*this.speed) {
				alert(this.x);
			}
		}
		*/
		var tZWidth = this.sprite.getZWidth();
		if ((this.sprite.x<-tZWidth/2 && this.speed>0) || (this.sprite.x>gScene.width+(this.sprite.tZWidth/2) && this.speed<0)) {
			// Sprite is out of screen -> Recycle //			
			myGame.myObjectsManager.pendingObject(this);
		}
	}
	
}

function ObjectsManager(tObjects) {
	
	this.objectsParams = tObjects;
	this.objects = new Array();
	
	this.nextObjects = new Array();
	this.newObjects = new Array();
	
	gRegisteredForAnimation.push(this);
	
	for (o in this.objectsParams) {		
		var tType = this.objectsParams[o];
		var tTime = tType.min_frequency+(Math.random()*(tType.max_frequency-tType.min_frequency));		
		var self = this;
		
		// Add to creation list //
		this.nextObjects.push({ 'time':tTime, 'type':tType });
			
	}
	
	this.destroy = destroy;
	function destroy() {
		
		gRegisteredForAnimation.splice(indexOfObj(gRegisteredForAnimation, this), 1);
		
		while(this.objects.length>0) {
			this.removeObject(this.objects[0]);			
		}
		this.objects = null;	
		this.nextObjects = null;
		this.newObjects = null;
	}
	
	this.createObject = createObject;
	function createObject(tType) {
		
		var tNewObj = null;
		for (o in this.objects) {
			var tObject = this.objects[o];
			if (tObject.status=='pending' && tObject.params.name==tType.name) {
				//console.log('recycle '+this.objects[o].sprite.layer+' '+tType.name);
				tNewObj = tObject;
				break;
			}
		}
		if (tNewObj==null) {
			tNewObj = new GameObject(tType);
			this.objects.push(tNewObj);		
		}
		tNewObj.init();		
		
		var tTime = tType.min_frequency+(Math.random()*(tType.max_frequency-tType.min_frequency));		
				
		// Add to newly created list //
		this.newObjects.push(tNewObj);
			
		// Add to creation list //
		this.nextObjects.push({ 'time':tTime, 'type':tType });
		
	}
	
	this.animate = animate;
	function animate() {
		// Check depth of newly created //
		var tNb = this.newObjects.length;
		for (var n=0;n<tNb;n++) {
			this.checkDepth(this.newObjects[n]);
		}
		// Reset newly created list //
		this.newObjects = new Array();
		
		// Check timer of pending object for creation //
		tNb = this.nextObjects.length;
		for (var n=0;n<tNb;n++) {
			
			var tNextObj = this.nextObjects[n];
			
			// Decrease timer with theorical frame time //
			tNextObj.time -= 1000/gFramerate;
			
			// Create object with ended timer //
			if (tNextObj.time<=0) {
				this.createObject(tNextObj.type);
				this.nextObjects.splice(n, 1);
			}
		}
		
	}
	
	this.pendingObject = pendingObject;
	function pendingObject(tObject) {
		tObject.status = 'pending';
		
		this.newObjects.splice(indexOfObj(this.newObjects, tObject), 1);
		
		//alert(indexOfObj(gRegisteredForAnimation, tObject)+' '+tObject.params.name+' '+tObject.sprite.x);
		
		gRegisteredForAnimation[indexOfObj(gRegisteredForAnimation, tObject)] = null;
	}
	
	this.removeObject = removeObject;
	function removeObject(tObject) {
		
		tObject.destroy();
		var tIndex = indexOfObj(this.objects, tObject);				
		if (tIndex>=0) {
			this.objects.splice(tIndex, 1);
		}
	}
		
	this.checkDepth = checkDepth;
	function checkDepth(tObj) {
				
		var tObjLayers = new Array();
		var tNb = this.objects.length;
		for (var s=0;s<tNb;s++) {
			var tObject = this.objects[s];
			if (tObject.status=='used') {
				tObjLayers.push(tObject.sprite.layer);
			}
		}
		tObjLayers.sort();
				
		var tNewSpriteNum = 0;
						
		var zHeight = tObj.sprite.getZHeight();
		var tBottom = Math.round(tObj.sprite.y+(zHeight/2));
		var tLayer = tObj.sprite.layer;
				
		// Check back //
		for (var s=tObjLayers[0];s<tLayer;s++) {
													
			var tSprite = mySpritesManager.sprites[s];
			
			if (tSprite!=0) {			
				var tThisZHeight = tSprite.getZHeight();
				var tThisBottom = Math.round(tSprite.y+(tThisZHeight/2));
				
				// IF this bottom is greater than new Bottom -> should be greater layer too //
				if (tThisBottom>tBottom && tSprite.layer<tLayer) {
					tNewSpriteNum = s;
					break;
				}	
			}
		}
		// Check Front //
		tNb = tObjLayers.length;
		for (var s=tObjLayers[tNb-1];s>tLayer;s--) {
												
			var tSprite = mySpritesManager.sprites[s];
			
			if (tSprite!=0) { 
			
				var tThisZHeight = tSprite.getZHeight();
				var tThisBottom = Math.round(tSprite.y+(tThisZHeight/2));
							
				// IF this bottom is lower than new Bottom -> should be lower layer too //
				if (tThisBottom<tBottom && tSprite.layer>tLayer) {
					tNewSpriteNum = s+1;
					break;
				}		
			}
		}
						
		if (tNewSpriteNum>0 && tNewSpriteNum!=tLayer) {			
			mySpritesManager.changeZIndex(tLayer,tNewSpriteNum);
		}
		
	}
	
	// Function to sort objects by sprite layer //
	function sortByLayer(a,b) {
	  if (a.sprite.layer < b.sprite.layer)
		 return -1;
	  if (a.sprite.layer > b.sprite.layer)
		return 1;
	  return 0;
	}
	
	this.checkIntersect = checkIntersect;
	function checkIntersect(tCameraRect) {
		
		var tTotalIntersect = 0;
		
		// Sort objects by sprite layer //
		this.objects.sort(sortByLayer);
		
		// Set covering rects list //
		var tCoverRect = new Array();
		
		var tCameraX = tCameraRect.left + ((tCameraRect.right - tCameraRect.left)/2);
		var tCameraY = tCameraRect.top + ((tCameraRect.bottom - tCameraRect.top)/2);
		
		var tNb = this.objects.length;
		for (var b=tNb-1;b>=0;b--) {
			
			var tObject = this.objects[b];									
			var tObjectRect = tObject.sprite.getRect();
			
			// First check if intersect with camera //
			if (intersectRect(tObjectRect, tCameraRect)) {			
			
				// 0 value = covering object //
				if (tObject.params.value==0) {
					
					// FULL camera covering -> quit looking for object //
					if (tObjectRect.left<=tCameraRect.left && tObjectRect.right>=tCameraRect.right) {
						if (tObjectRect.top<=tCameraRect.top && tObjectRect.bottom>=tCameraRect.bottom) {								
							break;
						}
					}
					
					// Inside camera more than 65% area -> quit looking for object //
					if (overlapRect(tObjectRect, tCameraRect)>=areaRect(tCameraRect)*0.65) {
						console.log('>=65% coverage -> stop counting points');
						break;
					}
					
					tCoverRect.push(tObjectRect);					
					
				}
				
				if (tObject.params.value>0) {
														
					var tIntersect = 0;
					var tValue = tObject.params.value
										
					// Check every covering objects //
					var tBreak = false;
					var tObjArea = areaRect(tObjectRect);
					var tNb = tCoverRect.length;
					for (var c=0;c<tNb;c++) {
						var tThisCover = tCoverRect[c];
						if (intersectRect(tObjectRect, tThisCover)) {							
							var tOverlap = overlapRect(tObjectRect, tThisCover);						
							if (tOverlap>=tObjArea*0.75) {
								// COVERING >=75% object -> stop counting points //
								tBreak = true;
								break;								
							} else if (tOverlap>=tObjArea*0.15) {
								// COVERING <75% >15% object -> value/2 //
								tValue = tValue/2
							}
						}
					}
					if (tBreak==true) {
						break;
					}
					
					// FULL //
					if (tObjectRect.left>=tCameraRect.left && tObjectRect.right<=tCameraRect.right) {
						if (tObjectRect.top>=tCameraRect.top && tObjectRect.bottom<=tCameraRect.bottom) {
							
							var tObjectX = tObject.sprite.x;
							var tObjectY = tObject.sprite.y;
							
							//alert(tObjectX+' '+tCameraX+' '+tObjectY+' '+tCameraY);
							//alert(Math.abs(tObjectX-tCameraX)+' '+Math.abs(tObjectY-tCameraY));
							//alert((Math.abs(tObjectX-tCameraX)+Math.abs(tObjectY-tCameraY)));
							
							var tMaxDistance = gParams.radius;
							var dx = tObjectX-tCameraX;
							var dy = tObjectY-tCameraY
							var tDist = dx * dx + dy * dy;
							if (tDist <= tMaxDistance*tMaxDistance) {
							//if (Math.abs(tObjectX-tCameraX)+Math.abs(tObjectY-tCameraY)<=40) {
								// Perfect Centered Shot //
								tValue = tValue*3;
							}
							
							tIntersect = tValue;
						}
					}
					
					// In Width + intersect half height //
					if (tIntersect==0) {			
						if (tObjectRect.left>=tCameraRect.left && tObjectRect.right<=tCameraRect.right) {			
							if ((tObject.sprite.y>=tCameraRect.top && tObjectRect.top<tCameraRect.top) || (tObject.sprite.y<=tCameraRect.bottom && tObjectRect.bottom>tCameraRect.bottom)) {
								//console.log('in width only -> value/2');
								tIntersect = 0.5*tValue;
							}				
						}
					}
					
					// In Height + intersect half width //
					if (tIntersect==0) {
						if (tObjectRect.top>=tCameraRect.top && tObjectRect.bottom<=tCameraRect.bottom) {								
							if ((tObject.sprite.x>=tCameraRect.left && tObjectRect.left<tCameraRect.left) || (tObject.sprite.x<=tCameraRect.right && tObjectRect.right>tCameraRect.right)) {
								//console.log('in height only -> value/2');
								tIntersect = 0.5*tValue;
							}
						}
					}
					
					// Intersect At least half width & half height //					
					if (tIntersect==0) {
						if ((tObject.sprite.y>=tCameraRect.top && tObjectRect.top<tCameraRect.top) || (tObject.sprite.y<=tCameraRect.bottom && tObjectRect.bottom>tCameraRect.bottom)) {
							if ((tObject.sprite.x>=tCameraRect.left && tObjectRect.left<tCameraRect.left) || (tObject.sprite.x<=tCameraRect.right && tObjectRect.right>tCameraRect.right)) {
								//console.log('intersect both incomplete -> value/4');
								tIntersect = 0.25*tValue;
							}
						}
					}
					
					if (tObject.pictureTaken==true) {
						tIntersect = tIntersect * gParams.doubleTake;
					}
					
					tTotalIntersect+=tIntersect;
					
					if (tIntersect>0) {
						tObject.pictureTaken = true;
					}
				}
			}
		}
				
		return tTotalIntersect;
		
		
	}
	
}