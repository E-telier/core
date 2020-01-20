<script type="text/javascript">
	$(document).ready(function() {
		$('.content h2').each(function(i) {
			var tTitle = this;
			var tTitleTxt = $(this).html();
			//alert(tTitleTxt+' '+tTitleTxt.match(/^[0-9]+\. /)+' '+tTitleTxt.match(/^[0-9]+\. (.*)$/));
			tTitleTxt = tTitleTxt.replace(/^[0-9]+\. (.*)$/, '$1');
			var new_link = $('<li><a>'+tTitleTxt+'</a></li>');
			new_link.children('a').click(function() {
				var tDiffTop = $(window).scrollTop()-(($(tTitle).offset().top)-60);
								
				if (tDiffTop!=0) {				
					$('html, body').stop(true, false);
					$('html, body').animate({
						scrollTop: ($(tTitle).offset().top)-60
					}, Math.abs(tDiffTop)*0.5);
				}
			}).attr('href', '#');
			$('.auto_anchor').append(new_link);
		});
		
	});
</script>
<ol class="auto_anchor">

</ol>
