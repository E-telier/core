function Preloader(tEngine) {
	
	this.engine = tEngine;
	this.percent = 0;
	this.preloadAssets = { 'toLoad':0, 'loaded':0 };
	this.assets = new Array();
	
	this.engine.registeredForAnimation.push(this);
	
	this.preloadImg = preloadImg;
	function preloadImg(tFile) {
		
		if (this.assets.indexOf(tFile)<0) {
			this.assets.push(tFile);
				
			// create DOM //
			var i = document.createElement('img'); // or new Image()
			// Set DOM attributes //
			i.id = tFile.replace(new RegExp('\/', 'gim'), '-');
			
			var self = this;
			i.onload = function() {
				// Add to assets //
				var container = document.getElementById('assets');
				container.appendChild(this);
				
				console.log('loaded'+i.src);
				
				// Update progress //
				self.preloadAssets.loaded++;
				self.updateLoading();
			};
			//alert(CMSRootPath+tFile);
			i.src = CMSRootPath+tFile;
			console.log(tFile+' '+i.src);
			this.preloadAssets.toLoad++;
		}
	}

	this.preloadSound = preloadSound;
	function preloadSound(tFile, tLoop, tVolume, tAutoplay) {
		
		if (this.assets.indexOf(tFile)<0) {
			this.assets.push(tFile);
					
			if (!tLoop) { tLoop = 'true'; }
			if (!tVolume) { tVolume = '0.5'; }
			if (!tAutoplay) { tAutoplay = 'false'; }
			
			// create DOM //
			var i = document.createElement('audio'); // or new Image()
			// Set DOM attributes //
			i.loop = tLoop;
			i.autoplay = tAutoplay;
			i.volume = tVolume;
			i.id = tFile.replace(new Regexp('\/', gim), '-');
			i.preload = 'auto';
			
			// create child DOM //
			var s = document.createElement('source');
			// Set child DOM attributes //
			s.type = 'audio/'+tExt.substring(1);
			s.src = CMSRootPath+tFile;	
			i.appendChild(s);
			
			var self = this;
			i.onloadeddata = function() {
				// Add to assets //
				var container = document.getElementById('assets');
				container.appendChild(this);
				
				this.play();
				
				console.log('loaded'+s.src);
				
				// Update progress //				
				self.preloadAssets.loaded++;
				self.updateLoading();
			};
				
			console.log(s.src);
			
			this.preloadAssets.toLoad++;
		
		}
	}

	this.updateLoading = updateLoading;
	function updateLoading() {
		this.percent = this.preloadAssets.loaded/this.preloadAssets.toLoad;
				
		if (this.percent>=1) {						
			
			// show 100% //
			this.animate();
			
			// delete from animation //
			this.engine.registeredForAnimation.splice(indexOfObj(this.engine.registeredForAnimation, this), 1);
			
			// call init global //
			this.engine.init();
			
		}
	}
	
	this.animate = animate;
	function animate() {
		
		// Draw progress line //
		var lineWidth = 10;
		var lineTop = (this.engine.scene.height/2)-(lineWidth/2);
		var context = this.engine.scene.getContext('2d');
		context.clearRect(0, 0, this.engine.scene.width, this.engine.scene.height);
		context.beginPath();
		context.moveTo(0, lineTop);
		context.lineTo(this.engine.scene.width*this.percent, lineTop);
		context.lineWidth = 10;	
		context.strokeStyle = '#ff0000';
		context.stroke();
		
		// Write progress txt //
		context.font = "20px 'BebasNeue'";
		context.textBaseline = 'top';
		context.fillStyle = '#000000';
		context.textAlign = 'center';
		context.fillText('Loading '+Math.round(this.percent*100)+' %', 512, lineTop-30);
		
		
		//document.getElementById('percent').innerHTML = Math.round(this.percent*100)+' %';
		
	}
	
}