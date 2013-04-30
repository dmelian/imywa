function submit(){
	document.forms[0].submit();
}

/**
*	A�ade un hidden al primer formulario que se encuentre en la p�gina
*/
function addhidden(hidname, hidvalue){
	var newinput = document.createElement("input");
	
	newinput.setAttribute("type", "hidden");
	newinput.setAttribute("name", hidname);
	newinput.setAttribute("value", hidvalue);

	document.forms[0].appendChild(newinput);
}


/**
*	Crea los campos action y lookup necesarios para pedir un lookup de una columna. Y realiza el submit.
*	Como �nico parametro tiene el identificador de la columna.
*/
function submitlookup(id){
	addhidden('action', 'lookup');
	addhidden('lookup', id);
	submit();
}

/**
*	Para lanzar un action desde un <a> (los men�s)
*	Crea un campo action y realiza el submit
*/
function submitaction(action,params){
	if (action != undefined){
		addhidden('action', action);
		if (params != undefined){
			for (var param in params){
				if (params.hasOwnProperty(param)) addhidden(param, params[param]);
			}
		}
	}
	//addhidden('action', id);
	submit();
}


/**
*	Muestra el tab seleccionado y oculta todos los dem�s. (por medio de las clases css)
*
*/
function showtab(id, selected){
	var tabs_container = document.getElementById("tab_" + id);
	var tabs = tabs_container.getElementsByTagName("li");
	tabs[selected].className = "selected"; 
	var content = document.getElementById("tabcontent"+id+selected);  
	content.className = "tabcontentSelected";
	for (var i=0; i<tabs.length; i++){
		if (i != selected) {
			tabs[i].className = ''; 
			content =  document.getElementById("tabcontent"+id+i);
			content.className = "tabcontent";
		}
	}
	var hidden_selected = document.getElementById("tabselected_" + id);
	hidden_selected.setAttribute("value", selected);
}


function adjustmenus(){
	var submenu = document.getElementById('mainMenu').getElementsByTagName("ul");
	
	for (var i=0; i<submenu.length; i++){

		if (submenu[i].parentNode.parentNode.id == 'mainMenu') {
			submenu[i].style.top = submenu[i].parentNode.offsetHeight+"px";
		} else if (i>0) {
			submenu[i].style.left = submenu[i-1].getElementsByTagName("a")[0].offsetWidth+"px";
		}
		
		 submenu[i].parentNode.onmouseover = function(){
			this.getElementsByTagName("ul")[0].style.visibility="visible";
		};
		
		submenu[i].parentNode.onmouseout = function(){
			this.getElementsByTagName("ul")[0].style.visibility="hidden";
		};
	}
	
}
