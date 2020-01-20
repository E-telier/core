var gParams;
var gFrames = 0;
var gRegisteredForAnimation = new Array();
var mySpritesManager = new SpritesManager();
var myGame = null;
var gScene;
var gMousePos = new Object();
var myPreloader = new Preloader();
var gInactiveTimeout;
var gFramerate = 66;
var gTime = 0;
var gFrameTime = Math.round(1000/gFramerate);

var gPause = false;

var gRestartFunction = function() { gRegisteredForAnimation.push(mySpritesManager); init(); };
var gGameMouseupFunction = function(e) { myGame.mouseUp(e); };

//var gLastGroundSpeed = 0;

window.onload = function() { preload(); };

function preload() {
	
	console.log('startMovie');
				
	gScene = document.getElementById('scene')
	console.log('gScene '+gScene);
	
	// Load font //
	ajaxCall('fonts/BebasNeue Bold.otf', 
		function(result) {						
			// Load Parameters //
			loadJSON('params.txt?date=201503101538', function(result) {
				gParams = result;		
				console.log('gParams '+gParams);
				
				preloadAssets();

				startAnimation();
				
			}, 
			function() { alert('error while loading parameters'); }
			);
		}, 
	function() { alert('error while loading font'); });
	
}

function preloadAssets() {
				
	// Preload images //	
	for (var b=0;b<gParams.backgrounds.length;b++) {
		var tBG = gParams.backgrounds[b];
		var tExt = '.png';
		if (b==0) { tExt = '.jpg'; }
		myPreloader.preloadImg(tBG.name, tExt);
	}
			
	myPreloader.preloadImg('viewfinder');
	myPreloader.preloadImg('scorerect');
	myPreloader.preloadImg('picture-bg');
	for (var o=0;o<gParams.objects.length;o++) {
		var tObject = gParams.objects[o];
		for (var n=1;n<=tObject.frames;n++) {
			var tNum = n;
			if (tNum<10) { tNum = '0'+tNum; }
			myPreloader.preloadImg(tObject.name+'_'+tNum);
		}
	}
	// Preload sounds //
	//myPreloader.preloadSound('sleep');
					
	
}

function startAnimation() {
	
	// Set framerate //
	if (gParams.showFPS==true) { setInterval(fps, 1000); }
	setInterval(animate, gFrameTime);
}
function init() {
	
	myPreloader = null;
		
	// Capture mouse position //
	window.onmousemove = function(e){
		updateMousePos(e);
	};
	// Capture mouse position //
	window.onmousedown = function(e){
		updateMousePos(e);
	};
		
	// Create game //			
	console.log('Create game');						
	myGame = new Photography();
	
	// Remove loading screen //
	document.getElementById('loader').className = 'loader';
		
}

function updateMousePos(e) {
	gMousePos = {			
		left: e.pageX - gScene.parentNode.offsetLeft,
		top: e.pageY - gScene.parentNode.offsetTop
	};
						
	clearTimeout(gInactiveTimeout);
	gInactiveTimeout = setTimeout(endGame, gParams.timer*1000);
}

function endGame() {
	
	console.log('endGame');
	
	window.onmousemove = function(e){};	
	window.onmousedown = function(e){};
	
	gScene.removeEventListener('click', gGameMouseupFunction);
	gScene.addEventListener('click', gRestartFunction);
	
	gRegisteredForAnimation.splice(indexOfObj(gRegisteredForAnimation, mySpritesManager), 1);
	mySpritesManager.draw();
	
	clearTimeout(gInactiveTimeout);
	
	var tScore = myGame.score;
	
	myGame.destroy();
	myGame = null;
				
	// Write Score //
	var tText = ' medal';
	if (tScore>=gParams.medals.gold) {
		tText = 'Gold'+tText;
	} else if (tScore>=gParams.medals.silver) {
		tText = 'Silver'+tText;
	} else if (tScore >= gParams.medals.bronze) {
		tText = 'Bronze'+tText;
	} else {
		tText = 'Game Over';
	}
	
	var context = gScene.getContext('2d');
	context.font = "64px 'BebasNeue'";
	context.textBaseline = 'alphabetic';
	context.fillStyle = '#000000';
	context.textAlign = 'center';
	/*
	context.lineWidth = 5;			
	context.strokeStyle = '#ff0000';	
	context.strokeText(tText, gScene.width/2, (gScene.height/2)-100);	
	*/
	context.fillText(tText, gScene.width/2, (gScene.height/2)+32);
		
	for (var s in mySpritesManager.sprites) {
		if (mySpritesManager.sprites[s]!=0) {
			console.log(mySpritesManager.sprites[s].name);
		}
	}
	
}

function animate() {
	
	if (gPause==false) {
	
		var d = new Date();
		var n = d.getTime();
		
		if (gTime==0) { gTime = n-gFrameTime; }
		
		var tDeltaTime = (n-gTime)/gFrameTime;	
			
		gTime = n;
		
		gPause = true;
		
		// Call animate every registered object //
		for (var r=0;r<gRegisteredForAnimation.length;r++) {
			gRegisteredForAnimation[r].animate(tDeltaTime);		
		}
		
		// Delete every object set to null //
		while(gRegisteredForAnimation.indexOf(null)>=0) {
			gRegisteredForAnimation.splice(gRegisteredForAnimation.indexOf(null), 1);
		}
		
		gPause = false;
		
	} else {
		//alert('paused');
	}
	gFrames++;			
		
}
function fps() {
	if (myGame!=null) {
		myGame.fps.DOM.innerHTML = gFrames;
	}
	gFrames=0;
}