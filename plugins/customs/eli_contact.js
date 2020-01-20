function submitForm() {	

	// FALSE EMAIL TRAP //
	if ($('#email').val()!='') {
		return 0;
	}
	
	// FORBIDDEN CHARS //
	var forbidden = new Array();
	forbidden.push(/[\u0400-\u04FF]/gim); // Cyrillic
	forbidden.push(/[\u4e00-\u9fff]/gim); // Chinese
	forbidden.push(/[\u0600-۾]/gim); // Arabic
	forbidden.push(/\.ru\//gim); // Russian link
	forbidden.push(/penis/gim); // Spam
	forbidden.push(/income/gim); // Spam
	forbidden.push(/ sex /gim); // Spam
	forbidden.push(/bit\.ly/gim); // Spam
	forbidden.push(/\$/gim); // Spam
	forbidden.push(/prostate/gim); // Spam
	
	var text = $('#contact_txt').val();
	
	for (var i=0;i<forbidden.length;i++) {		
		if (forbidden[i].test(text)) {
			alert('language not supported');
			return 0;
		}
	}
	
	var formID = 'estimate';
	eForm.submit($('#'+formID+' .submit')[0]);
	
}

function updateCost() {
						
	var cost = 0;
	var rate = 50;
	
	var type = document.forms['estimate'].elements['type'].value;
	switch(type) {
		case 'app': 
			cost=16*rate;
			break;
		case 'site': 
			cost=8*rate;
			break;
		default:
			cost=4*rate;
			break;
	} 
								
	if (document.forms['estimate'].elements['delivery'][1].checked) {
		cost = cost*1.5;
		
	}
								
	var languages = document.forms['estimate'].elements['languages'].value;
	
	switch(languages) {
		case '2': 
			cost=cost*1.1;
			cost += 4*rate;
			break;
		case '6': 									
			cost=cost*1.5;
			cost += 8*rate;
			break;
		default:									
			break;
	}
	
	if (document.forms['estimate'].elements['talents'].value=='all') {
		cost = cost*1.6;
	}
	
	if (document.forms['estimate'].elements['php'].checked) {
		cost = cost*1.1;
	}
	if (document.forms['estimate'].elements['responsive'].checked) {
		cost = cost*1.5;
	}
	if (document.forms['estimate'].elements['javascript'].checked) {
		cost = cost*1.5;
	}						
	if (document.forms['estimate'].elements['flash'].checked) {								
		cost = cost*1.6;
		cost += 8*rate;
	}
	if (document.forms['estimate'].elements['ecom'].checked) {
		cost = cost*1.6;
		cost += 16*rate;
	}
	
	if (document.forms['estimate'].elements['cms'].checked) {
		cost += 8*rate;
	}
	
	if (document.forms['estimate'].elements['3D'].checked) {								
		cost = cost*1.6;
		cost += 8*rate;
	}
								
	cost = parseInt(cost*0.02)*50;
								
	var ratio = cost/10000;							
	if (cost>7500) {
		var ratio = 7500/10000;
		cost = "&gt; "+7500;
	}
	
	var tCost = $('#cost').html();	
	tCost = tCost.replace(/&gt; /, '');	
	tCost = tCost.replace(/<b>[0-9]+<\/b>/, '0');	
	tCost = tCost.replace('0', '<b>'+cost+'</b>');	
	$('#cost').html(tCost);
	
	var position = ratio*$('#jauge').width();
	$('#cursor').stop();
	$('#cursor').animate({'marginLeft': position}, 750);

}

$(document).ready(function() {						
	updateCost();
});