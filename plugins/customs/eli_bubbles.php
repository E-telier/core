	
	<script src="<?php echo eMain::root_url(); ?>js/html5_engine/_core.js?date=201510051357"></script>
	<script src="<?php echo eMain::root_url(); ?>js/html5_engine/Sprite.js?date=201509011642"></script>	
	
	<script src="<?php echo eMain::root_url(); ?>js/html5_engine/Engine.js?date=201510051355"></script>
	<script src="<?php echo eMain::root_url(); ?>js/html5_engine/Preloader.js?date=201503101747"></script>
	
	<script src="<?php echo eMain::root_url(); ?>plugins/customs/Bubbles.js?date=201911271037"></script>
		
	<script type="text/javascript">
		
		var myBubbles;
		var timeout = 0;
		
		$(window).scroll(function() {						
			myBubbles.scroll();			
		});
		
		$(window).resize(function() {
			myBubbles.resize();			
		});
		$(document).ready(function() { 				
			$('#bubbles').detach().appendTo('body');		
			myBubbles = new Bubbles(); 
		});
		
	</script>
	
	<div id="bubbles" style="width:1024px; height:768px; position:fixed; top:0px;left:0px; overflow:hidden; z-index:0; opacity:0.1; display:none;">
		<canvas id="scene" width="1024" height="768"></canvas>
		<div id="assets"></div>
	</div>