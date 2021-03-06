function setImgStatus(elem, status) {
	var name = $(elem).attr('src');
	var ext = name.substr(name.lastIndexOf('.'));
	name = name.substr(0, name.lastIndexOf('-'));
	name += '-'+status;
	name += ext;
	//alert(name);
	$(elem).attr('src', name);	
}

/////////////////////////////////////////////////////////////////////

// STRING //
function escapeHTML(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

function date2Str(tDate, tFormat) {
	
	var dd = tDate.getDate();
	var mm = tDate.getMonth()+1; //January is 0!

	var yyyy = tDate.getFullYear();
	if(dd<10){dd='0'+dd} 
	if(mm<10){mm='0'+mm} 
	
	var tDateStr = '';
	if (tFormat.indexOf('d/m/Y')>=0) {
		tDateStr = dd+'/'+mm+'/'+yyyy;
	} else {
		tDateStr = yyyy+'-'+mm+'-'+dd;
	}
	
	if (tFormat.indexOf('H:i:s')>=0) {
		var hh = tDate.getHours();
		if (hh<10) { hh = '0'+hh; }
		var mi = tDate.getMinutes();
		if (mi<10) { mi = '0'+mi; }
		var ss = tDate.getSeconds();
		if (ss<10) { ss = '0'+ss; }
		
		tDateStr += ' '+hh+':'+mi+':'+ss;
	}
	
	return tDateStr;
}
function formatDate(tDate, tFormat) {
	
	if (tDate.indexOf('-')>=0) {
		tDate = tDate.split('-');
		var dd = tDate[2];
		var mm = tDate[1];
		var yyyy = tDate[0];
	} else {
		tDate = tDate.split('/');
		var dd = tDate[0];
		var mm = tDate[1];
		var yyyy = tDate[2];
	}
	
	tFormat = tFormat.replace('d', dd);
	tFormat = tFormat.replace('m', mm);
	tFormat = tFormat.replace('Y', yyyy);
	
	return tFormat; 
}

function encode_utf8(s) {
  return unescape(encodeURIComponent(s));
}
function decode_utf8(s) {
  return decodeURIComponent(escape(s));
}

/////////////////////////////////////////////////////////////////////////////

// NUMBERS //
var nb_decimals = 4;
function computeNumber(tNumber) {
		
	tNumber = ''+tNumber;
	
	tNumber = tNumber.replace(/[^0-9,\.-]+/gim, '');
			
	// Remove dotted thousands //
	tNumber = tNumber.replace(/^([\-]{0,1})([0-9]{0,3})[\.]{0,1}([0-9]{0,3})[\.]{0,1}([0-9]{1,3}),([0-9]{1,4})$/, '$1$2$3$4.$5');
		
	// Remove comma thousands //
	tNumber = tNumber.replace(/^([\-]{0,1})([0-9]{0,3})[,]{0,1}([0-9]{0,3})[,]{0,1}([0-9]{1,3})\.([0-9]{1,4})$/, '$1$2$3$4.$5');
	//alert('computeNumber '+tNumber);
	tNumber = parseFloat(tNumber);
	//alert('computeNumber '+tNumber);
	return tNumber;
}
function formatToDecimal(value, noEmptyDecimals) {
	// TEXT -> COMPUTER NUMBER : 10000.01 //
	
	if (noEmptyDecimals==undefined) { noEmptyDecimals = false; }
	
	var newValue = computeNumber(value);
		
	if (isNaN(newValue)) {
		newValue = '0.';
		for(var i=0;i<nb_decimals;i++) { newValue+='0'; }
	} else if (noEmptyDecimals==false) {
						
		// REMOVE SIGN TO AVOID WRONG ROUNDING
		var negative = false;
		if (newValue<0) { negative = true; }
		newValue = Math.abs(newValue);
		newValue = Number(Math.round(newValue+'e'+nb_decimals)+'e-'+nb_decimals);
		if (negative) { newValue = -newValue; }
		
		newValue = newValue.toFixed(nb_decimals);
	} else {
		newValue = ''+newValue;
	}
			
	return newValue;
}

function formatNumber(number, noEmptyDecimals) {
	// COMPUTER -> TEXT NUMBER : 10.000,01 //
	
	if (noEmptyDecimals==undefined) { noEmptyDecimals = false; }
		
	number = formatToDecimal(number, noEmptyDecimals);
		
	// NO DECIMALS = NO DOTTED THOUSANDS //
	if (number.indexOf('.')<0) { return number ;}
	
	// COMMA DECIMAL //
	number = number.replace('.', ',');
		
	// DOTTED THOUSANDS //	
	x = number.split(',');
	x1 = x[0];
	x2 = x.length > 1 ? ',' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + '.' + '$2');
	}
		
	return x1 + x2;
}
function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////

////////////
// Window //
////////////
function getWindowHeight() {
	var windowHeight = 0;
	if (typeof(window.innerHeight) == 'number') {
		windowHeight = window.innerHeight;
	}
	else {
		if (document.documentElement && document.documentElement.clientHeight) {
			windowHeight = document.documentElement.clientHeight;
		}
		else {
			if (document.body && document.body.clientHeight) {
				windowHeight = document.body.clientHeight;
			}
		}
	}
	return windowHeight;
}
function getWindowWidth() {
	var windowWidth = 0;
	if (typeof(window.innerWidth) == 'number') {
		windowWidth = window.innerWidth;
	}
	else {
		if (document.documentElement && document.documentElement.clientWidth) {
			windowWidth = document.documentElement.clientWidth;
		}
		else {
			if (document.body && document.body.clientWidth) {
				windowWidth = document.body.clientWidth;
			}
		}
	}
	return windowWidth;
}

function popitup(link, target, params) {		
	var w = window.open(link,
		target||"_blank",
		params);
	return w?false:true; // allow the link to work if popup is blocked
}
//////////////////////////////////////////////////////////////////////////////////////////////////////