	
	function clearOutput(divname) {
		my_div = document.getElementById(divname);
		while (my_div.firstChild) {
			my_div.removeChild(my_div.firstChild);
		}
		/*var len=my_div.childNodes.length;
		for (var i = 0; i < len; i++) {
			if (my_div.childNodes[i] != null) {
			  my_div.removeChild(my_div.childNodes[i]);
			}
		}*/
	}

	function outputText(divname, warningstring) {
		my_div = document.getElementById(divname);
		var spanelement = document.createElement("span");
		spanelement.appendChild(document.createTextNode(warningstring));
		spanelement.appendChild(document.createElement("br"));
		my_div.appendChild(spanelement);
		/*my_div.appendChild(document.createTextNode(warningstring));
		my_div.appendChild(document.createElement("br"));*/
	}
	
	function outputWarning(divname, warningstring) {
		my_div = document.getElementById(divname);
		var spanelement = document.createElement("span");
		spanelement.style.color = "red";
		var splitwarning = warningstring.split('|'); 
		var correctinchi="";
		for(i = 0; i < splitwarning.length; i++){
			if (splitwarning[i].substr(0,8)=="onclick:") {
				var a = document.createElement("a");
				onclickfunction=splitwarning[i].substr(8, splitwarning[i].indexOf(":a:")-8);
				a.setAttribute("onClick", onclickfunction+divname.replace("outputdiv","")+"();");
				a.appendChild(document.createTextNode(splitwarning[i].substr(splitwarning[i].indexOf(":a:")+3)));
				spanelement.appendChild(a);
			} else {
				if (splitwarning[i].substr(0,5)=="href:") {
					var a = document.createElement("a");
					a.href=splitwarning[i].substr(5, splitwarning[i].indexOf(":a:")-5);
					a.appendChild(document.createTextNode(splitwarning[i].substr(splitwarning[i].indexOf(":a:")+3)));
					spanelement.appendChild(a);
				} else {
					spanelement.appendChild(document.createTextNode(splitwarning[i]));
				}
			}
		}
		spanelement.appendChild(document.createElement("br"));
		my_div.appendChild(spanelement);
		/*my_div.appendChild(document.createElement("br"));*/
	}
	
	function outputSearchResult(divname, linkname) {
		my_div = document.getElementById(divname);
		var a = document.createElement("a");
		a.href="/learn-chemistry/wiki/"+linkname;
		a.appendChild(document.createTextNode(linkname));
		a.appendChild(document.createElement("br"));
		my_div.appendChild(a);
		/*my_div.appendChild(document.createElement("br"));*/
	}
	
	function processInChICodeWarnings(warningarray, divname) {
	     for (var i = 0; i < warningarray.length; i++) {
			if (warningarray[i] != null) {
				if (warningarray[i].firstChild != null) {
					//if (warningarray[i].data != null)
					outputWarning(divname, "Warning: " + warningarray[i].firstChild.data)
				}
			}
		 }
	}
	
/* 
 * Kills an event's propagation and default action
 */
function killEvent(eventObject) {
    if (eventObject && eventObject.stopPropagation) {
        eventObject.stopPropagation();
    }
    if (window.event && window.event.cancelBubble ) {
        window.event.cancelBubble = true;
    }
    
    if (eventObject && eventObject.preventDefault) {
        eventObject.preventDefault();
    }
    if (window.event) {
        window.event.returnValue = false;
    }
}

/* 
 * Cross-browser event handling, by Scott Andrew
 */
function addEvent(element, eventType, lamdaFunction, useCapture, inputparameters) {
    if (element.addEventListener) {
		if (inputparameters==null)
			element.addEventListener(eventType, lamdaFunction, useCapture);
		else
			element.addEventListener(eventType, function(event) {lamdaFunction(event, inputparameters); }, useCapture);
        return true;
    } else if (element.attachEvent) {
		if (inputparameters==null)
			var r = element.attachEvent('on' + eventType, lamdaFunction);
		else
			var r = element.attachEvent('on' + eventType, function() {lamdaFunction(event, inputparameters); });
        return r;
    } else {
        return false;
    }
}