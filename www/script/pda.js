function addhidden(hidname, hidvalue){
	var newinput = document.createElement("input");
	newinput.setAttribute("type", "hidden");
	newinput.setAttribute("name", hidname);
	newinput.setAttribute("value", hidvalue);
	document.forms[0].appendChild(newinput);
}

function submitaction(id){
	addhidden('action', id);
	document.forms[0].submit();
}

function submitcdad(cdad, showdivcdadid, inputcdadid){
	/* cdad - Dígito pulsado
	 * showdivcdadid - identificador de la división que muestra la cantidad en pantalla	
	 * inputdadid - identificador del campo de texto (oculto) donde se almacena la cantidad que se envía al servidor.
	 */
	
	var submitform;
	var display = display = document.getElementById(showdivcdadid);

	switch (cdad) {
		case '1':
			var child;
			while ((child = display.firstChild) != null) display.removeChild(child);

			var node = document.createTextNode('1');
			display.appendChild(node); 
			submitform = false;
			break;

		case 'C':
			var child;
			while ((child = display.firstChild) != null) display.removeChild(child);

			var node = document.createTextNode('1');
			display.appendChild(node); 
			submitform = true;
			break;

		default: 
			var textcdad = new String();
			textcdad = display.firstChild.wholeText;
			if (textcdad == '...') {
				if (cdad == '0') cdad = '10';
				textcdad = cdad; 
			} else textcdad = textcdad.concat(cdad);
			
			var child;
			while ((child = display.firstChild) != null) display.removeChild(child);

			var node = document.createTextNode(textcdad);
			display.appendChild(node); 
			submitform = true;
			break;
	}
	
	if (submitform) {
		var textcdad = new String();
		var input = document.getElementById('inputcdad');
		textcdad = display.firstChild.wholeText;
		input.value = textcdad;
		document.forms[0].submit(); 
	}	
}

function concatkeyorsubmitaction(keyevent, inputid, action){
	var inputvalue = new String();
	inputvalue = document.getElementById(inputid).value;
	if (keyevent.keyCode == 13) submitaction(action);
	else document.getElementById(inputid).value = inputvalue.concat(String.fromCharCode(keyevent.charCode));
}
