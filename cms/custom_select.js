$('head').append('<link href="'+CMSRootPath+'cms/custom_select.css?date=201810171110" rel="stylesheet" type="text/css" />');

$(document).ready(function() {
	mySelectsManager = new CustomSelectsManager();
});

var mySelectsManager = null;

function CustomSelectsManager() {

	//alert('CustomSelectsManager');
	this.interval = 0;
	this.pCustomSelectsList = new Array();
	
	this.init = init;
	function init() {
		var self = this;
		$('.custom_select').each(function(s) {	
			var tID = $(this).attr('id');
			self.pCustomSelectsList[tID] = new CustomSelect(this);
		});
	}
	
	this.getByID = getByID;
	function getByID(tID) {
		return this.pCustomSelectsList[tID];
	}
	
	var tSelf = this;
	$('body').append('<div class="my_select dummy"> </div>');
	this.interval = setInterval(function() {
		console.log($('.my_select').css('display'));
		if ($('.my_select').css('display')=='inline-block') {
			clearInterval(tSelf.interval);
			$('.my_select').remove();
			// INIT OBJECT //
			tSelf.init();
		}
	}, 200);	

}

function CustomSelect(tSelect) {

	//alert('CustomSelect');
	
	this.pOptions = new Array();
	this.pID;
	
	this.init = init;
	function init(tSelect) {
		var tID = $(tSelect).attr('id');
		this.pID = tID;
		var tClass = $(tSelect).attr('class');
		tClass = tClass.replace(/custom_select"/, '');
		var tOptions = '';
		var tSelected = '';
		
		var self = this;
		$(tSelect).children('option').each(function(o) {
		
			var tStyle = $(this).attr('style');
			
			if (typeof tStyle=='undefined') { tStyle=''; }
			tStyle = tStyle.replace(/"/g, "'"); // IE QUOTE FIX
			var tOptionClass = $(this).attr('class');
			if (typeof tOptionClass=='undefined') { tOptionClass=''; }
			var tOptionID = $(this).attr('id');
			if (typeof tOptionID=='undefined') { tOptionID=''; }
			
			self.pOptions[o] = { 'val':$(this).val(), 'html':$(this).html() }
		
			var tOption = '<div id="'+tOptionID+'" class="my_option '+tOptionClass+'" style="'+tStyle+'" onclick="mySelectsManager.getByID(\''+tID+'\').setSelectValue(\''+tID+'\', \''+o+'\', \''+$(this).val()+'\');">'+$(this).html()+'</div>\n';
			
			if (tSelected=='' || this.selected) {
				tSelected = tOption;
				tSelected = tSelected.replace(/onclick="[^"]+"/, '');
			}
			tOptions += tOption;
		});		
		
		$(tSelect).after('<div class="my_select '+tClass+'" tabindex="'+0+'" id="select_'+tID+'" onblur="mySelectsManager.getByID(\''+tID+'\').showOptions(\''+tID+'\', true);"></div>\n');
		$(tSelect).css({'display':'none'});
		$('#select_'+tID).append('\n<div class="my_selected" onclick="mySelectsManager.getByID(\''+tID+'\').showOptions(\''+tID+'\')">'+tSelected+'</div>');		
		$('#select_'+tID).append('\n<div class="my_options">\n'+tOptions+'</div>\n');
				
		// ADD SCROLL WIDTH //
		var tOptionsBlock = $('#select_'+tID).children('.my_options');
		var tMaxHeight = tOptionsBlock.css('max-height');
				
		var tOptionsTotalHeight = this.getOptionsTotalHeight(tID);					
		tOptionsBlock.css({'width':'auto'});
		if (tOptionsTotalHeight>=parseInt(tMaxHeight)) {
			var tWidth = tOptionsBlock.innerWidth();																		
			tOptionsBlock.css({'width':(tWidth+scrollbarWidth())+'px'});						
		}
						
		// SET SAME WIDTH FOR MY_SELECTED & MY_OPTIONS //
		var tOptionsOuterWidth = parseInt($('#select_'+tID+' .my_options').outerWidth());					
		var tBtnOuterWidth = $('#select_'+tID+' .my_selected').outerWidth();					
				
		if (tOptionsOuterWidth>tBtnOuterWidth) {
			var tBtnWidth = $('#select_'+tID+' .my_selected').width();
			var tBtnDiffWidth = tBtnOuterWidth-tBtnWidth;
		
			$('#select_'+tID+' .my_selected').css({'width':tOptionsOuterWidth-tBtnDiffWidth+'px'});
		} else {
			var tOptionsWidth = $('#select_'+tID+' .my_options').width();
			var tOptionDiffWidth = tOptionsOuterWidth-tOptionsWidth;						
			$('#select_'+tID+' .my_options').css({'width':tBtnOuterWidth-tOptionDiffWidth+'px'});
		}
				
	}
	
	this.getOptionsTotalHeight = getOptionsTotalHeight;
	function getOptionsTotalHeight(tID) {
		var tOptionsBlock = $('#select_'+tID).children('.my_options');
			
		//alert($(window).height()+' '+tOptionsBlock.offset().top+' '+$(window).scrollTop());
		var tMaxHeight = $(window).height()-(tOptionsBlock.offset().top - $(window).scrollTop()) -20;//tOptionsBlock.css('max-height');
		//var tPosition = $('#select_'+tID).position();
		var tSelectHeight = $('#select_'+tID).children('.my_selected').outerHeight();
		var tOptionsTotalHeight = tOptionsBlock.css({'height':'auto', 'max-height':'none', 'top':parseInt(tSelectHeight)+'px', 'left':'0px'}).outerHeight();
		tOptionsBlock.css({'height':'0px', 'max-height':tMaxHeight});
		
		return tOptionsTotalHeight;
	}

	this.showOptions = showOptions;
	function showOptions(tID, tForceClose) {
					
		var tOptionsBlock = $('#select_'+tID).children('.my_options');
						
		if (tOptionsBlock.height()>0 || tForceClose==true) {					
			tOptionsBlock.animate({'height':'0px', 'opacity':'0.0'}, 250);
		} else {			
			var tOptionsTotalHeight = this.getOptionsTotalHeight(tID);					
			tOptionsBlock.animate({'height':tOptionsTotalHeight+'px', 'opacity':'1.0'}, 500);		
		}
	}

	this.setSelectValue = setSelectValue;
	function setSelectValue(tID, tOptionNum, tValue) {

		var tSelect = $('#select_'+tID);
		
		// SET MY_SELECTED VALUE //
		var tOption = $('<div>').append(tSelect.find('.my_options .my_option:eq('+(tOptionNum)+')').clone()).html();
		tOption = tOption.replace(/onclick="[^"]+"/, '');				
		tSelect.children('.my_selected').html(tOption);				
		
		// SET SELECT VALUE //
		$('#'+tID).val(tValue);
		$('#'+tID).change();
		
		// CHECK IF SELECT VALUE HAS BEEN CHANGED //
				
		if ($('#'+tID).val()!=tValue) {
										
			//console.time('someFunction');			
			for (var o=0;o<this.pOptions.length;o++) {				
				if (this.pOptions[o].val==$('#'+tID).val()) {					
					tOption = $('<div>').append(tSelect.find('.my_options .my_option:eq('+o+')').clone()).html();
					tOption = tOption.replace(/onclick="[^"]+"/, '');				
					tSelect.children('.my_selected').html(tOption);					
					break;
				}
			}			
			//console.timeEnd('someFunction');
						
		}
		
		showOptions(this.pID, true);
										
	}
	
	// INIT OBJECT //
	this.init(tSelect);
	
}

function scrollbarWidth() {
	var div = $('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div>');
	// Append our div, do our calculation and then remove it
	$('body').append(div);
	var w1 = $('div', div).innerWidth();
	div.css('overflow-y', 'scroll');
	var w2 = $('div', div).innerWidth();
	$(div).remove();
	return (w1 - w2);
}