<script type="text/javascript">

	var fullHeader;
	var smallHeader;
	var headerPadding;

	$(document).ready(function() {
		
		fullHeader = $('#header').height();
		smallHeader = $('#header #menu').height();
		
		headerPadding = {'top':parseInt($('#header').css('padding-top')), 'bottom':parseInt($('#header').css('padding-bottom'))};
		
		//alert(fullHeader+' '+smallHeader+' '+headerPadding.top+' '+headerPadding.bottom);
		$(window).scroll(function() {
			changeMenuSize();
		});
		changeMenuSize();
	});
		function changeMenuSize() {
			if ($(window).width()>800) {
				if ($(window).scrollTop()>=fullHeader-smallHeader) {
					$('#header').css({'position': 'fixed', 'width': '100%', 'top':(smallHeader-fullHeader)+'px'});
					$('#site').css({'padding-top':fullHeader+headerPadding.top+headerPadding.bottom+'px'});
					$('#logo').css({'visibility': 'hidden'});
				} else {
					$('#header').css({'position': 'relative', 'width': 'auto', 'top':' 0'});
					$('#site').css({'padding-top':'0px'});		
					$('#logo').css({'visibility': 'visible'});					
				}
			}
		}
</script>