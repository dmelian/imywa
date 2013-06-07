window.onresize = adjustform;

function loadform(){
	adjustform();
	adjustmenus();
}

function adjustform(){
	var tablecontainer = document.getElementById('tableContainer');
		
	var headerheight = 0;
	var rows = tablecontainer.getElementsByClassName('tableHeader')[0].getElementsByClassName('tableRow');
	for (i=0; i<rows.length; i++) headerheight += rows.offsetHeight;
	
	var footerheight = 0;
	var rows = tablecontainer.getElementsByClassName('tableFooter')[0].getElementsByClassName('tableRow');
	for (i=0; i<rows.length; i++) footerheight += rows.offsetHeight;
	
	var contentheight = window.innerHeight
		- document.getElementById('titlebar').offsetHeight
		- document.getElementById('menubar').offsetHeight
		- document.getElementById('toolbar').offsetHeight
		- document.getElementById('statusbar').offsetHeight
		- document.getElementById('listheader').offsetHeight
		- headerheight - footerheight; 
		;
	
	if (contentheight > 0){
		tablecontainer.style.height = headerheight + footerheight + contentheight;
		tablecontainer.getElementsByClassName('tableContentContainer')[0].style.height = contentheight;
		tablecontainer.getElementsByClassName('vscroll')[0].style.height = headerheight + footerheight + contentheight;
	}
	
}

function adjustmenus(){
	var lastleft = 100;
	var menus = document.getElementById('menus');
	if (menus != null) {
		var uls = menus.getElementsByTagName('UL');
		for (var i=0; i<uls.length; i++){
			if (uls[i].id.substring(0,4) == 'menu') {
				var anchor = document.getElementById('linkto' + uls[i].id.substring(4));
				uls[i].style.left = anchor.offsetLeft;
				document.getElementById(anchor.id).onmouseover = showmenu;
				document.getElementById(anchor.id).onmouseout = hidemenu;
				document.getElementById(uls[i].id).onmouseover = showmenu;
				document.getElementById(uls[i].id).onmouseout = hidemenu;
			} else {
				uls[i].parentNode.onmouseover = showmenu;
				uls[i].parentNode.onmouseout = hidemenu;
				uls[i].style.top = 0;
				uls[i].style.left = uls[i].parentNode.offsetWidth;
			}
		}
	}	
}

function showmenu(event){
	var element = event.currentTarget;
	
	if (element.tagName == "A") {
		if (element.id.substring(0,6) == "linkto") {
			document.getElementById('menu' + element.id.substring(6)).style.visibility = "visible";
		}
	} else if (element.tagName == "UL") { 
		element.style.visibility = "visible";
	} else if (element.tagName == 'LI') {
		element.getElementsByTagName('UL')[0].style.visibility = "visible";
	}
}

function hidemenu(event){
	var element = event.currentTarget;
	
	if (element.tagName == "A") {
		if (element.id.substring(0,6) == "linkto") {
			document.getElementById('menu' + element.id.substring(6)).style.visibility = "hidden";
		}
	} else if (element.tagName == "UL") { 
		element.style.visibility = "hidden";
	} else if (element.tagName == 'LI') {
		element.getElementsByTagName('UL')[0].style.visibility = "hidden";
	}
}

function submit(){ 
	document.forms[0].submit();
}

function addhidden(hidname, hidvalue){
	var newinput = document.createElement("input");
	
	newinput.setAttribute("type", "hidden");
	newinput.setAttribute("name", hidname);
	newinput.setAttribute("value", hidvalue);

	document.forms[0].appendChild(newinput);
}

function submitlookup(id){
	addhidden('action', 'lookup');
	addhidden('lookup', id);
	submit();
}

function submitaction(id){
	addhidden('action', id);
	submit();
}

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

