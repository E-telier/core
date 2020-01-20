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
	return $(window).width();
}
//////////////////////////////////////////////////////////////////////////////////////////////////////

///////////
// Array //
///////////
function indexOfObj(tArray, tObject) {
	for (var i=0;i<tArray.length;i++) {
		if (tArray[i]===tObject) {
			/*
			if (tObject.constructor==GameObject) {
				alert(i+' '+tArray[i].params.name+' '+tArray[i].sprite.x+' - '+tObject.params.name+' '+tObject.sprite.x);
			} 
			*/
			return i;
			
		}
	}	
	return -1;
};
//////////////////////////////////////////////////////////////////////////////////////////////////////

//////////
// Rect //
//////////
function intersectRect(r1, r2) {
  return !(r2.left > r1.right || 
           r2.right < r1.left || 
           r2.top > r1.bottom ||
           r2.bottom < r1.top);
}

function overlapRect(r1, r2) {
	x_overlap = Math.max(0, Math.min(r1.right,r2.right) - Math.max(r1.left,r2.left));
	y_overlap = Math.max(0, Math.min(r1.bottom,r2.bottom) - Math.max(r1.top,r2.top));
	overlapArea = x_overlap * y_overlap;
	
	return overlapArea;
}
function areaRect(tRect) {
	var width = tRect.right-tRect.left;
	var height = tRect.bottom-tRect.top;
	
	return width*height;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////

//////////
// Ajax //
//////////
function loadJSON(path, success, error) {
   ajaxCall(path, success, error, true);
}
function ajaxCall(path, success, error, json) {
	
	//alert(path);
	
	var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                if (success)
					var tResult = xhr.responseText;
					if (json) { tResult = JSON.parse(tResult.replace(/\/\*[^\*]+\*\//gim, '')); }
                    success(tResult);
            } else {
                if (error)
                    error(xhr);
            }
        }
    };
    xhr.open("GET", path, true);
    xhr.send();
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
