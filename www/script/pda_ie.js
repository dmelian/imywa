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

	switch (cdad) {
		case '1':
			document.getElementById(showdivcdadid).innerHTML = '1'; 
			submitform = false;
			break;

		case 'C':
			document.getElementById(showdivcdadid).innerHTML = '1'; 
			submitform = true;
			break;

		default: 
			var text = new String();
			text = document.getElementById(showdivcdadid).innerHTML;
			if (text == '...') {
				if (cdad == '0') cdad = '10';
				text = cdad; 
			}else text = text.concat(cdad);
			document.getElementById(showdivcdadid).innerHTML = text;
			submitform = true;
			break;
	}
	
	if (submitform) {
		var input = document.getElementById(inputcdadid);
		input.value = document.getElementById(showdivcdadid).innerHTML;
		document.forms[0].submit(); 
	}
}

function concatkeyorsubmitaction(keyevent, inputid, action){
	var inputvalue = new String();
	inputvalue = document.getElementById(inputid).value;
	if (keyevent.keyCode == 13) submitaction(action);
	else document.getElementById(inputid).value = inputvalue.concat(String.fromCharCode(keyevent.keyCode));
}

