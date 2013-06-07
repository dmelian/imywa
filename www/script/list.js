
function loadform(config){
	
	createcolstyles(config);
	adjustmenus();
	adjustform();
	
	//Events
	var formtable = document.getElementById('formtable');
	formtable.getElementsByClassName('vscroll')[0].onscroll = vscrollme;
	formtable.getElementsByClassName('hscroll')[0].onscroll = hscrollme;
	window.onresize = adjustform;
	window.onmousedown = mousedownevent;
	window.onmouseup = mouseupevent;
	window.ondragstart = returnfalse;
	window.onselecUnexpectedtstart = returnfalse;

}

var downpoint= {x:0, y:0, pos:0};
var uppoint= {x:0, y:0};
var dragging= {status:false, originid:''};


function returnfalse(){
	return false;
}


function mousedownevent(event){
	if (event.explicitOriginalTarget.className == 'widthPull'){
		var colNode = event.explicitOriginalTarget.parentNode;
		dragging.status = true;
		dragging.originid = colNode.id;
		downpoint.x = event.clientX;
		downpoint.y = event.clientY;
		document.childNodes[0].style.cursor = 'ew-resize';
	}
	return false;
}


function mouseupevent(event){
	if (dragging.status) {
		document.childNodes[0].style.cursor = 'auto';
		uppoint.x = event.clientX;
		uppoint.y = event.clientY;
		dragging.status = false;
		var colNode = document.getElementById(dragging.originid);
		var newwidth = colNode.offsetWidth + uppoint.x - downpoint.x;
		if (newwidth < 16) newwidth = 16;
		colNode.style.width = newwidth;
		adjustandsendcolwidths();
	}
	return false;
}

function sendxhr(data){
	var rq = new XMLHttpRequest();
	rq.open('POST','xhrindex.php', true);
	rq.onreadystatechange = function(aEvt){
		if (rq.readyState == 4 && rq.status == 200) {} //rq.responseText contiene la respuesta.
	}
/* 	------ METODO POST
    ------ Para los nuevos Firefox
 	var data = new FormData();
	data.append('Nombre','Domingo');
	data.append('Apellidos', 'Melián Cárdenes');
	------ Para los antiguos
*/
	
	var post = encodeURI("sessionno=" + document.getElementsByName('sessionno')[0].value
			+ "&installationid=" + document.getElementsByName('installationid')[0].value
			+ "&sequenceno=" + document.getElementsByName('sequenceno')[0].value
			+ "&" + data
			);
	rq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	rq.setRequestHeader("Content-length", post.length);
	rq.setRequestHeader("Connection", "close");
	
	rq.send(post);
}

function adjustandsendcolwidths(){
	// get the new column widths.
	var coldefs = new Array();
	var headercols = document.getElementById('formtable').getElementsByClassName('tableHeader')[0].getElementsByClassName('tableCell');
	for (var icol = 0, left = 0; icol < headercols.length; icol++ ) {
		coldefs[icol] = {left: left, width: headercols[icol].offsetWidth};
		left+= headercols[icol].offsetWidth;
	}
	// set the widths and lefts
	var rules = document.styleSheets[0].cssRules;
	for (var irule=0; irule < rules.length; irule++){
		
		var divid=/div#col\d+/.exec(rules[irule].selectorText);
		if (divid != null) {
			rules[irule].style.left = coldefs[divid[0].substring(7)].left;
			rules[irule].style.width = coldefs[divid[0].substring(7)].width;
		}
	}
	
	var strcoldefs = "colwidths="; var sep = "";
	for (var icol = 0; icol < coldefs.length; icol++) { strcoldefs += sep + coldefs[icol].width; sep = ","; }
	sendxhr(strcoldefs);
	
	// Adjusts the void cols
	adjustform();
}

function createcolstyles(config){
	style = document.styleSheets[0];
	rulecount = style.cssRules.length;
	
	// Header
	
	var fixedselector = 'div.tableContainer div.tableHeader';
	var rule = '%fs div.tableRow { height: %1px; overflow: hidden; color: white; background-color: silver; background-image: url(\'../image/titleback2.png\'); }';
	style.insertRule(rule.replace('%fs',fixedselector).replace('%1', config.heights[0]), rulecount++);
	
	var left = 0; var top = 0;
	for (var col=0; col<config.widths.length; col++){
		rule = '%fs div#col%1 { position: relative;	top: %2px; left: %3px; width: %4px; height: %5px;}'
		style.insertRule(rule.replace('%fs',fixedselector).replace('%1',col)
			.replace('%2',top).replace('%3',left).replace('%4',config.widths[col]).replace('%5',config.heights[0])
			, rulecount++);
		left += config.widths[col];
		top -= config.heights[0];
	}

	// Content
	
	fixedselector = 'div.tableContainer div.tableContent';
	rule = '%fs div.tableRow { height: %1px; overflow: hidden; color: black; background-color: white; }';
	style.insertRule(rule.replace('%fs',fixedselector).replace('%1', config.heights[1]), rulecount++);
	rule = '%fs div.tableRowAlt { height: %1px; overflow: hidden; color: black; background-color: whitesmoke; }';
	style.insertRule(rule.replace('%fs',fixedselector).replace('%1', config.heights[1]), rulecount++);
	
	var left = 0; var top = 0;
	for (var col=0; col<config.widths.length; col++){
		rule = '%fs div#col%1 { position: relative;	top: %2px; left: %3px; width: %4px; height: %5px;}'
		style.insertRule(rule.replace('%fs',fixedselector).replace('%1',col)
			.replace('%2',top).replace('%3',left).replace('%4',config.widths[col]).replace('%5',config.heights[1])
			, rulecount++);
		left += config.widths[col];
		top -= config.heights[1];
		}

	// Footer
	
	fixedselector = 'div.tableContainer div.tableFooter';
	var rule = '%fs div.tableRow { height: %1px; overflow: hidden; color: black; background-color: whitesmoke; background-image: url(\'../image/titleback2.png\'); }';
	style.insertRule(rule.replace('%fs',fixedselector).replace('%1', config.heights[2]), rulecount++);
	
	var left = 0; var top = 0;
	for (var col=0; col<config.widths.length; col++){
		rule = '%fs div#col%1 { position: relative;	top: %2px; left: %3px; width: %4px; height: %5px;}'
		style.insertRule(rule.replace('%fs',fixedselector).replace('%1',col)
			.replace('%2',top).replace('%3',left).replace('%4',config.widths[col]).replace('%5',config.heights[2])
			, rulecount++);
		left += config.widths[col];
		top -= config.heights[2];
	}

	
}

function adjustform(){
	var heightadjust = 21;
	var widthadjust = 0;
	
	var tablecontainer = document.getElementById('formtable');
	var tableview = tablecontainer.getElementsByClassName('tableView')[0];
	
	var colswidth = 0;
	var colcount = 0;
	var rowheight = 0;
	var voidwidth = 0;
		
	var headerheight = 0;
	var rows = tableview.getElementsByClassName('tableHeader')[0].getElementsByClassName('tableRow');
	for (var i=0; i<rows.length; i++) {
		headerheight += rows[i].offsetHeight;
		var voidcell = rows[i].lastChild;
		if (voidcell.className != 'voidCol') {
			rows[i].innerHTML += '<div class="voidCol"></div>';
			voidcell = rows[i].lastChild;
			voidcell.style.position = 'relative';
			voidcell.style.overflow = 'hidden';
		}
		if (i == 0){
			var cells = rows[i].getElementsByClassName('tableCell');
			var widthpulls = cells[0].getElementsByClassName('widthPull').length;
			for (var j=0; j<cells.length; j++) {
				colswidth += cells[j].offsetWidth;
				if (widthpulls == 0) {
					cells[j].innerHTML = '<div class="widthPull"></div><div class="cellContent">%1</div>'.replace('%1',cells[j].innerHTML);
				}
			}
			rowheight = cells[0].offsetHeight;
			colcount = cells.length;
			voidwidth = window.innerWidth - widthadjust - colswidth;
			if (voidwidth < 0) voidwidth = 0;
		}
		voidcell.style.top = -(colcount * rowheight) +'px';
		voidcell.style.left = colswidth +'px';
		voidcell.style.height = rowheight +'px' ;
		voidcell.style.width = voidwidth +'px';
		//document.getElementById('statusbar').innerHTML = "window.innerWidth = " + window.innerWidth + ", style.width =" + voidcell.style.width + ", voidwidth =" + voidwidth;
		voidcell.style.backgroundColor = 'silver';
		voidcell.style.color = 'silver';
	}
	
	var footerheight = 0;
	var rows = tableview.getElementsByClassName('tableFooter')[0].getElementsByClassName('tableRow');
	for (i=0; i<rows.length; i++) {
		footerheight += rows[i].offsetHeight;
		var voidcell = rows[i].lastChild;
		if (voidcell.className != 'voidCol') {
			rows[i].innerHTML += '<div class="voidCol"></div>';
			voidcell = rows[i].lastChild;
			voidcell.style.position = 'relative';
			voidcell.style.overflow = 'hidden';
		}
		if (i == 0){
			var rowheight = rows[i].getElementsByClassName('tableCell')[0].offsetHeight;
		}
		voidcell.style.top = -(colcount * rowheight) +'px';
		voidcell.style.left = colswidth +'px';
		voidcell.style.height = rowheight +'px' ;
		voidcell.style.width = voidwidth +'px';
		voidcell.style.backgroundColor = 'whitesmoke';
		voidcell.style.color = 'whitesmoke';
	}
	
	var contentheight = 0;
	var rows = tableview.getElementsByClassName('tableContent')[0].childNodes;
	var rowheight = 0;
	for (i=0; i<rows.length; i++) {
		if (rows[i].className != null && rows[i].className.substring(0,8) == 'tableRow') {
			contentheight += rows[i].offsetHeight;
			var voidcell = rows[i].lastChild;
			if (voidcell.className != 'voidCol') {
				rows[i].innerHTML += '<div class="voidCol"></div>';
				voidcell = rows[i].lastChild;
				voidcell.style.position = 'relative';
				voidcell.style.overflow = 'hidden';
			}
			if (rowheight == 0){
				rowheight = rows[i].getElementsByClassName('tableCell')[0].offsetHeight;
			}
			voidcell.style.top = -(colcount * rowheight) +'px';
			voidcell.style.left = colswidth +'px';
			voidcell.style.height = rowheight +'px' ;
			voidcell.style.width = voidwidth +'px';
			voidcell.style.color = 'white';
		}
	}
	
	var contentspace = window.innerHeight
		- document.getElementById('titlebar').offsetHeight
		- document.getElementById('menubar').offsetHeight
		- document.getElementById('toolbar').offsetHeight
		- document.getElementById('statusbar').offsetHeight
		- document.getElementById('header').offsetHeight
		- document.getElementById('footer').offsetHeight
		- headerheight - footerheight
		- heightadjust
		;
	
	if (contentspace > 0 ){
		if (contentspace >= contentheight) {
			//Recolocar el status bar
			document.getElementById('statusbar').style.top = contentspace - contentheight; 
			contentspace = contentheight;
		}
		tableview.getElementsByClassName('scrollTableContent')[0].style.height = contentspace;
		tablecontainer.getElementsByClassName('scrollTableView')[0].style.width = (window.innerWidth - 20) + 'px';
		tableview.style.height = (headerheight + footerheight + contentspace) + 'px';
		if (window.innerWidth > colswidth) tableview.style.width = window.innerWidth +'px';  else tableview.style.width = colswidth +'px';
		tablecontainer.style.height = (headerheight + footerheight + contentspace + 20) + 'px';
		tablecontainer.style.width = (window.innerWidth) + 'px';
		
		
		
		var vscroll = tablecontainer.getElementsByClassName('vscroll')[0];
		vscroll.style.width = '20px';
		vscroll.style.left = (window.innerWidth - 20)+'px';
		vscroll.style.top = -(headerheight+footerheight+contentspace)+'px';
		vscroll.style.height = (headerheight+footerheight+contentspace)+'px';
		vscroll.getElementsByClassName('vscrollsize')[0].style.height = headerheight + footerheight + contentheight;

		var hscroll = tablecontainer.getElementsByClassName('hscroll')[0];
		hscroll.style.height = '20px';
		hscroll.style.left = '0px';
		hscroll.style.top = -(headerheight+footerheight+contentspace)+'px';
		hscroll.style.width = (window.innerWidth - 20)+'px';
		hscroll.getElementsByClassName('hscrollsize')[0].style.width = colswidth;

/*		document.getElementById('statusbar').innerHTML = 
			"top = " + vscroll.style.top
			+ ", left =" + vscroll.style.left
			+ ", height =" + vscroll.style.height
			+ ", width =" + vscroll.style.width
			;
*/
/*
 		var hscroll = tablecontainer.getElementsByClassName('hscroll')[0];
		hscroll.style.top = -(headerheight + footerheight + contentspace)+'px';
		hscroll.style.width = window.innerWidth - 16;
*/	}
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


function vscrollme(event){
	var tablecontainer = document.getElementById("formtable");
	tablecontainer.getElementsByClassName('tableContent')[0].style.top =  "-" + event.target.scrollTop + "px";
}

function hscrollme(event){
	var tablecontainer = document.getElementById("formtable");
	var tableview = tablecontainer.getElementsByClassName('tableView')[0]
	tableview.style.left =  "-" + event.target.scrollLeft + "px";
	tableview.style.width = tableview.style.width + event.target.scrollLeft + "px";
	
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

