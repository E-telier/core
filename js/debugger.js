$(document).ready(function() {
	var html = document.documentElement.innerHTML;//$(document).html();
		
	var error = html.match(/<b>(?:Notice|Warning|Fatal error|Parse error)<\/b>:.*? on line <b>[0-9]+<\/b>/gim);
	if (error) {
		//alert('error '+error);
				
		$.ajax({
			  method: "POST",
			  url: window.location.href,
			  data: { 'php_error': error.toString(), 'url': window.location.href },
			  
			  success: function (result) { /*alert('erreur envoyée '+result);*/ }
		});
		
	}
	
	
});