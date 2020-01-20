
$(document).ready(function() {
	if ($('head').html().indexOf('form_contact.css')<0) {
		$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'plugins/basics/form_contact.css?date=201603011638" type="text/css" />');
	}
});	

function sendContactForm(dom_btn) {
	
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
	
	var text = $('#contact_txt').val();
	
	for (var i=0;i<forbidden.length;i++) {		
		if (forbidden[i].test(text)) {
			alert('language not supported');
			return 0;
		}
	}
		
	var error = false;
	
	var formID = 'form_contact';
	
	eForm.submit(dom_btn);

}