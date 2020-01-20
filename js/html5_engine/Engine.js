function Engine(tProject) {
	
	var self = this;
	
	this.project = tProject;
	
	this.params;
		
	this.scene;
	this.mousePos = new Object();
	
	this.registeredForAnimation = new Array();
	
	this.myPreloader = new Preloader(this);
	this.mySpritesManager = new SpritesManager(this);
	
	this.inactiveTimeout;
	this.framerate = 66;
	this.time = 0;
	this.frameTime = Math.round(1000/this.framerate);
	this.frames = 0;
	

	this.pause = false;

	//var gLastGroundSpeed = 0;

	this.loadAndStart = loadAndStart;
	function loadAndStart(tScene, tParamsPath) {
		
		console.log('startMovie');
		//alert('loadAndStart '+this.registeredForAnimation.length);
					
		this.scene = tScene;
		console.log('scene '+scene);
				
		var tMe = this;
				
		// Load Parameters //
		loadJSON(tParamsPath, function(result) {
			tMe.params = result;		
			console.log('params '+tMe.params);
			
			//alert('loadAndStart '+tMe.registeredForAnimation.length);
			
			tMe.preloadAssets();

			tMe.startAnimation();
			
		}, 
		function() { alert('error while loading parameters'); }
		);

	}

	this.preloadAssets = preloadAssets;
	function preloadAssets() {					
		
		for (var tFile in this.params.assets.images) {	
			// Preload images //
			this.myPreloader.preloadImg(this.params.assets.images[tFile]);
		}
		for (var tFile in this.params.assets.sounds) {	
			// Preload sounds //
			var tSound = this.params.assets.sounds[tFile];
			this.myPreloader.preloadSound(tSound.path, tSound.loop, tSound.volume, tSound.autoplay);
		}	
	}

	this.startAnimation = startAnimation;
	function startAnimation() {		
		// Set framerate //
		if (this.params.showFPS==true) { setInterval(this.fps, 1000); }
		
		setInterval(this.animate, this.frameTime);
	}
	this.init = init;
	function init() {
		
		myPreloader = null;
			
		var tMe = this;
			
		// Capture mouse position //
		window.onmousemove = function(e){
			tMe.updateMousePos(e);
		};
		// Capture mouse position //
		window.onmousedown = function(e){
			tMe.updateMousePos(e);
		};
		
		// Remove loading screen //
		//document.getElementById('loader').className = 'loader';
		
		this.project.init();
			
	}

	this.updateMousePos = updateMousePos;
	function updateMousePos(e) {
		mousePos = {			
			left: e.pageX - scene.parentNode.offsetLeft,
			top: e.pageY - scene.parentNode.offsetTop
		};
		/*					
		clearTimeout(this.inactiveTimeout);
		this.inactiveTimeout = setTimeout(endGame, this.params.timer*1000);
		*/
	}

	this.animate = animate;
	function animate() {
		
		//console.log('animate '+this.registeredForAnimation.length);
		
		if (self.pause==false) {
		
			var d = new Date();
			var n = d.getTime();
			
			if (self.time==0) { self.time = n-self.frameTime; }
			
			var tDeltaTime = (n-self.time)/self.frameTime;	
				
			self.time = n;
			
			self.pause = true;
			
			// Call animate every registered object //
			for (var r=0;r<self.registeredForAnimation.length;r++) {
				self.registeredForAnimation[r].animate(tDeltaTime);		
			}
			
			// Delete every object set to null //
			while(self.registeredForAnimation.indexOf(null)>=0) {
				self.registeredForAnimation.splice(self.registeredForAnimation.indexOf(null), 1);
			}
			
			self.pause = false;
			
		} else {
			alert('paused');
		}
		self.frames++;			
			
	}
	this.fps = fps;
	function fps() {
		if (myGame!=null) {
			myGame.fps.DOM.innerHTML = frames;
		}
		this.frames=0;
	}
}