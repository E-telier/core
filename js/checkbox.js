$(document).ready(function() {	
	setCheckboxes();	
});

function setCheckboxes() {
	$('input[type="checkbox"]').each(function(i) {
		
		if ($(this).css('display')!='none') {	
			var name = $(this).attr('name');
			var id = $(this.form).attr('id')+'___'+name;
			var needed = $(this).hasClass('needed');
		
			var status = '';
			if ($(this).is(':checked')==false) {
				status = 'off';
			} else {
				status = 'on';			
			}
						
			$(this).after('<div class="checkbox_container"><img src="'+CMSRootPath+'design/checkbox-'+status+'.png" class="'+status+' checkbox" id="img_'+id+'" width="17" height="15" onclick="setCheckbox(this);" /></div>');
			
			$(this).addClass('checkbox_id_'+id).css({'display':'none'});
			
			var existing_change = $(this).attr('onchange');
			if (existing_change==undefined) { existing_change = ''; }
			$(this).attr('onchange', existing_change+' updateAllCheckboxes();');
		}
	});
	
	$('head').append('<link rel="stylesheet" href="'+CMSRootPath+'css/checkbox.css" type="text/css" />');
	
}

function setCheckbox(image) {

	var status = '';
	var id = $(image).attr('id').substr(4);
	var name = id.substr(id.indexOf('___')+3);
	
	if ($(image).hasClass('off')) {		
		$(image).removeClass('off');
		status = 'on';
		$('.checkbox_id_'+id).prop('checked', true);
	} else {
		$(image).removeClass('on');		
		status = 'off';
		$('.checkbox_id_'+id).prop('checked', false);
	}
			
	$(image).addClass(status);	
	$(image).attr('src', CMSRootPath+'design/checkbox-'+status+'.png');
			
	$('.checkbox_id_'+id).trigger('change');
				
}
function updateAllCheckboxes() {
	$('input[type="checkbox"]').each(function() {
		updateCheckbox(this);
	});
}
function updateCheckbox(checkbox) {

	var classes = $(checkbox).attr('class');
	var id = classes.substr(classes.indexOf('checkbox_id_')+12);
	var image = $('#img_'+id);
	var status = '';
	
	if ($(checkbox).is(':checked')==false) {
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
		
	image.attr('src', CMSRootPath+'design/checkbox-'+status+'.png');
	
}

function errorCheckBox(name, formID) {
	var id = formID+'___'+name;
	$('#img_'+id).parent('.checkbox_container').css({'border':'1px solid #ff0000', 'padding-left':'3px'});
}
