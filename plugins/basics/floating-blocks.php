	<script type="text/javascript">
		var blockTop;
		var blockMargin = 20;
		var originTop = 0;
		var blockHeight;
		var middleHeight;
		var timeout = 0;
				
		function setBlocksPos() {
						
			var tScrollTop = $(window).scrollTop();
			var tTop = originTop;
					
			if (tScrollTop+blockMargin+originTop >= blockTop 
				&& blockHeight < $(window).height()-blockMargin-originTop) {
				tTop = (tScrollTop+blockMargin+originTop)-blockTop;
			}
			/*
			if (tTop + blockHeight > middleHeight - 20) {
				tTop = middleHeight - blockHeight - 20;
			}
			*/
			$('.blocks').stop().animate({'top':tTop+'px'}, 500);
		}
		$(document).ready(function() { 
		
			timeout = setTimeout(function() { 
				if ($(window).width()>1024) {
				
					$('.blocks').css({'position':'absolute'});
			
					blockTop = $('.blocks').offset().top;				
					blockHeight = $('.blocks').height();
					middleHeight = $('#site .middle').height();
															
					$(window).scroll(function() {				
						clearTimeout(timeout);
						timeout = setTimeout(function() { setBlocksPos(); }, 20);
					});
					
					setBlocksPos();
				
				}
			}, 500);
					
		});
			
	</script>