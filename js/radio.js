$(document).ready(function() {	
	setRadios();	
});
$('body').change(function() { setRadios(); });

function setRadios() {
	$('input[type="radio"]').each(function(i) {
	
		if ($(this).css('display')!='none') {	
			var name = $(this).attr('name');
			var id = 'r'+i+'-'+$(this.form).attr('id')+'___'+name;
			var needed = $(this).hasClass('needed');
		
			var status = '';
			if ($(this).is(':checked')==false) {
				status = 'off';
			} else {
				status = 'on';			
			}
			
			//alert(id);
						
			$(this).after('<div class="radio_container"><img src="'+CMSRootPath+'design/radio-'+status+'.png" class="'+status+' radio" id="img_'+id+'" width="15" height="15" onclick="setRadio(this);" /></div>');
			
			$(this).addClass('radio_id_'+id).css({'display':'none'});
			
			var existing_change = $(this).attr('onchange');
			if (existing_change==undefined) { existing_change = ''; }
			$(this).attr('onchange', existing_change+' updateAllRadios();');
		}
	});
	
	$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'css/radio.css" type="text/css" />');
}

function setRadio(image) {

	var status = '';
	var id = $(image).attr('id').substr(4);
	var name = id.substr(id.indexOf('___')+3);
		
	if ($(image).hasClass('off')) {		
		$(image).removeClass('off');
		status = 'on';
		$('.radio_id_'+id).prop('checked', true);
	} else {
		/*
		$(image).removeClass('on');		
		status = 'off';
		$('.radio_id_'+id).prop('checked', false);
		*/
		return false;
	}
			
	$(image).addClass(status);	
	$(image).attr('src', ''+CMSRootPath+'design/radio-'+status+'.png');
			
	$('.radio_id_'+id).trigger('change');
				
}
function updateAllRadios() {
	$('input[type="radio"]').each(function() {
		updateRadio(this);
	});
}
function updateRadio(radio) {

	var classes = $(radio).attr('class');
	var id = classes.substr(classes.indexOf('radio_id_')+9);
	if (id.indexOf(' ')>0) { id = id.substr(0, id.indexOf(' ')); }
	var image = $('#img_'+id);
	var status = '';
		
	if ($(radio).is(':checked')==false) {
		status = 'off';
		if (image.hasClass('on')) {
			image.removeClass('on');		
			image.addClass(status);				
		}
	} else {
		status = 'on';
		if (image.hasClass('off')) {
			image.removeClass('off');		
			image.addClass(status);				
		}
	}
		
	//alert(id+' '+$(radio).is(':checked')+' '+status+' '+image.attr('class'));
		
	image.attr('src', ''+CMSRootPath+'design/radio-'+status+'.png');
	
}