
function frmxlist(form, div, theme){
	
	if (arguments.length == 0) return; // to be able to inherit from this
	
	// Attributes
	this.form= form;
	this.div= div;
	this.id= div.id;
	this.rowix= 0;
	this.rowsxpage= 10;
	this.config= {
		width: 800, 
		height: 600, 
		rowHeight: 25,
		charToPixelWidthScale: 10,
		cols: {}};
	this.mouseEvt= {
		downPoint: {x:0, y:0, pos:0},
		upPoint: {x:0, y:0},
		dragging: {status:false, originId:''}
	};
	this.scrollPos= {left:0, top:0};
	this.relativePosAdjustment= {left:0, top:0};

	//TODO Tomar el rowHeight, y los scrollwidths de su definición en el tema.
	
	this.sendCommand("gettabledef");

/*

	this.rowHeight= 25;
	this.scrollBarWidth= 20;
	this.scrollSizeWidth= 2;

*/
	
}		

frmxlist.prototype.resize= function (deltax, deltay, create){
	if (create == undefined) create= false;
	
	//TODO: Despues de recolocar uno, hay que volver a recolocar todos los objetos que hayan por debajo. (Y por la derecha).
	this.config.width+= deltax; if (this.config.width < 0) this.config.width= 0;
	this.config.height+= deltay; if (this.config.height < 0) this.config.height= 0;

	var lastBodyRows= this.config.bodyRows;
	this.config.bodyRows= parseInt(this.config.height / this.config.rowHeight) - 1;

	var ruleCount= this.scrollRuleIx;
	
	// Views
	var viewWidth= this.config.width < this.config.colsWidth ? this.config.colsWidth : this.config.width;
	this.styleSheet.insertRule(
		"div#" + this.id + " div.listview {"
		+ " width: " + viewWidth  + "px;"
		+ "}", ruleCount++);
	if (!create) this.styleSheet.deleteRule(ruleCount);

	if (lastBodyRows < this.config.bodyRows){
		var mybody= this.div.getElementsByClassName('body')[0];	
		var voidCell= document.createElement("div"); 
		voidCell.className= 'cell void';
		
		for (var irow=lastBodyRows; irow < this.config.bodyRows; irow++){
			var row= document.createElement("div");
			row.className= irow % 2 == 0 ? "row" : "row alternate";
			row.id= "row" + irow;
			for (var icol= 0; icol < this.tabledef.cols.length; icol++){
				var cell= document.createElement("div");
				cell.className= "cell";
				cell.id= this.tabledef.cols[icol].id;
				cell.innerHTML= icol==0 ? irow : "-";
				row.appendChild(cell);
			}
			row.appendChild(voidCell.cloneNode(false));
			mybody.appendChild(row);
		}
		
	} else if (lastBodyRows > this.config.bodyRows) {
		var mybody= this.div.getElementsByClassName('body')[0];	
		for (var irow=lastBodyRows-1; irow >= this.config.bodyRows; irow--){
			mybody.removeChild(mybody.childNodes[irow]);
		}
		
	}
	
	this.styleSheet.insertRule(
		"div#" + this.id + " div.listcont {"
		+ " width: " + this.config.width + "px;" 
		// + " height: " + this.config.height + "px;" -- La altura se la da su contenido, en este caso div.bodycount 
		+ "}", ruleCount++);
	if (!create) this.styleSheet.deleteRule(ruleCount);
	
	var bodyHeight= this.config.bodyRows * this.config.rowHeight;
	this.styleSheet.insertRule(
		"div#" + this.id + " div.bodycont {"
		+ " height: " + bodyHeight + "px;"
		+ "}", ruleCount++);
	if (!create) this.styleSheet.deleteRule(ruleCount);

	// Scroll Bars
	this.styleSheet.insertRule(
			"div#" + this.id + " div.scrollx {"
//				+ " left: 0px;  top: " + this.config.topAdjustment + "px;"
			+ " width: " + this.config.width + "px;"
			+ "}"
		, ruleCount++);
	if (!create) this.styleSheet.deleteRule(ruleCount);

	this.styleSheet.insertRule(
		"div#" + this.id + " div.sizescrollx {"
		+ " width: " + this.config.colsWidth + "px;"
		+ "}", ruleCount++);
	if (!create) this.styleSheet.deleteRule(ruleCount);

	var mytop= -20/*scrollbarwidth*/-bodyHeight/*-footerHeight*/;
	var myheight= bodyHeight;
	this.styleSheet.insertRule(
		"div#" + this.id + " div.scrolly {"
		+ " left: " + this.config.width + "px; top: " + mytop + "px; height: " + myheight + "px;"
		+ "}", ruleCount++);
	if (!create) this.styleSheet.deleteRule(ruleCount);
	this.relativePosAdjustment.top= mytop;
	
	this.styleSheet.insertRule(
		"div#" + this.id + " div.sizescrolly {"
		+ " height: " + this.config.rowHeight * 100 + "px;"
		+ "}", ruleCount++);
	if (!create) this.styleSheet.deleteRule(ruleCount);

	// Resize Handle
	this.styleSheet.insertRule(
		"div#" + this.id + " div.resizeviewhandle {"
		+ " left: " + this.config.width + "px; top: " + mytop + "px;"
		+ "}", ruleCount++);
	if (!create) this.styleSheet.deleteRule(ruleCount);
	//this.config.topAdjustment-= this.scrollBarWidth; // Last Height

	this.rowsxpage= this.config.bodyRows;
	this.form.resize();
	this.sendCommand("getdata", {"rowix":this.rowix, "rowsxpage":this.rowsxpage});
	
};
	
frmxlist.prototype.setColStyles= function (create){
	if (create == undefined) create= false;
	if (create) var ruleCount= this.styleSheet.cssRules.length;

	var left= 0; var top= 0; var colWidth= 0; var voidWidth= window.innerWidth;
	this.config.colsWidth= 0;
	for (var icol= 0; icol < this.tabledef.cols.length; icol++){
		//TODO poner la propiedad visible y width en el tabledef export y descomentar la línea
		//colWidth= this.tabledef.cols[icol].visible ? this.tabledef.cols[icol].width : 0;
		colWidth= this.tabledef.cols[icol].width * this.config.charToPixelWidthScale;
		voidWidth-= colWidth;
		var myrule= "div#" + this.id + " div#" + this.tabledef.cols[icol].id + " {"
			+ " top: " + top + "px; left: " + left + "px; width: " + colWidth + "px;"
			+ "}";
		if (create) {
			this.styleSheet.insertRule(myrule, ruleCount);
			this.config.cols[this.tabledef.cols[icol].id]= {'colIx': icol,'ruleIx': ruleCount++};
		} else {
			this.styleSheet.deleteRule(this.config.cols[this.tabledef.cols[icol].id].ruleIx);
			this.styleSheet.insertRule(myrule, this.config.cols[this.tabledef.cols[icol].id].ruleIx);
		}
		left += colWidth;
		this.config.colsWidth += colWidth;
		top -= this.config.rowHeight;
	}
	var myrule= "div#" + this.id + " div.void {"
	+ " top: " + top + "px; left: " + left + "px; width: " + voidWidth + "px;}";
	if (create){
		this.styleSheet.insertRule(myrule, ruleCount);
		this.config.cols['void-cell']= {'colIx': icol,'ruleIx': ruleCount++};
	} else {
		this.styleSheet.deleteRule(this.config.cols['void-cell'].ruleIx);
		this.styleSheet.insertRule(myrule, this.config.cols['void-cell'].ruleIx);
	}
	
};

	
frmxlist.prototype.stretchCol= function (colId, widthStretch){
	var colWidth= this.tabledef.cols[this.config.cols[colId].colIx].width * this.config.charToPixelWidthScale + widthStretch;
	if (colWidth < 2 * this.config.charToPixelWidthScale) colWidth= 2 * this.config.charToPixelWidthScale; //Minimal col width
	this.tabledef.cols[this.config.cols[colId].colIx].width= parseInt(colWidth / this.config.charToPixelWidthScale);
	this.setColStyles();
	this.resize(0,0);
	//TODO: send the new tabledef to server.

};

frmxlist.prototype.scroll= function (){
	var ruleCount= this.scrollRuleIx;
	
	var viewWidth= this.config.width < this.config.colsWidth ? this.config.colsWidth : this.config.width;
	this.styleSheet.insertRule(
		"div#" + this.id + " div.listview {"
		+ " width: " + viewWidth  + "px;"
		+ " left: " + -this.scrollPos.left + "px"
		+ "}", ruleCount++);
	this.styleSheet.deleteRule(ruleCount);
	//TODO: EL SCROLL VERTICAL SE HACE POR LLAMADAS DE PETICIONES DE DATOS (-THIS.SCROLLPOS.TOP)
/*		this.styleSheet.insertRule('div#%ID% div.CellsView {position: relative; left: %LEFT%px; top: %TOP%px;}'
		.replace('%ID%',this.id).replace('%LEFT%', 0).replace('%TOP%', -this.scrollPos.top), ruleCount++);
		if (!this.creatingStyle) this.styleSheet.deleteRule(ruleCount);
*/		
};
	
frmxlist.prototype.scrollWheel= function(axis, delta){
	switch (axis){
		case 1: // X axis
			var myLeft= this.scrollPos.left + delta;
			if (myLeft < 0) myLeft= 0;
			//if (myLeft > 1000) myLeft= 1000;
			this.div.getElementsByClassName('scrollbar scrollx')[0].scrollLeft =  myLeft;
			break;

		case 2: // Y axis
			var myTop= this.scrollPos.top + delta;
			if (myTop < 0) myTop= 0;
			//if (myTop > 1000) myTop= 1000;
			this.div.getElementsByClassName('scrollbar scrolly')[0].scrollTop =  myTop;
			break;
	}
};	

frmxlist.prototype.handleEvent= function (event){
	switch(event.type) {
		case 'mousedown':
			if (event.target.className == 'resizecolhandle'){
				var colNode= event.target.parentNode;
				this.mouseEvt.dragging.status= true;
				this.mouseEvt.dragging.originId= colNode.id;
				this.mouseEvt.downPoint.x= event.clientX;
				this.mouseEvt.downPoint.y= event.clientY;
				document.childNodes[0].style.cursor= 'ew-resize';
				event.preventDefault();
				
			} else if (event.target.className == 'resizeviewhandle'){
				this.mouseEvt.dragging.status= true;
				this.mouseEvt.dragging.originId= 'resizeviewhandle';
				this.mouseEvt.downPoint.x= event.clientX;
				this.mouseEvt.downPoint.y= event.clientY;
				document.childNodes[0].style.cursor= 'se-resize';
				event.preventDefault();					
			}
			break;

		case 'mouseup':
			if (this.mouseEvt.dragging.status) {
				document.childNodes[0].style.cursor= 'auto';
				this.mouseEvt.upPoint.x= event.clientX;
				this.mouseEvt.upPoint.y= event.clientY;
				this.mouseEvt.dragging.status= false;
				if (this.mouseEvt.dragging.originId == 'resizeviewhandle'){
					this.resize(
						this.mouseEvt.upPoint.x - this.mouseEvt.downPoint.x
						, this.mouseEvt.upPoint.y - this.mouseEvt.downPoint.y);
				} else {
					this.stretchCol(
						this.mouseEvt.dragging.originId
						, this.mouseEvt.upPoint.x - this.mouseEvt.downPoint.x);
				}
				event.preventDefault();
			}
			break;

		case 'dragstart': case 'selectstart': 
			event.preventDefault();
			break;

		case 'scroll':
			if (event.target.className == 'scrollbar scrollx') {
				this.scrollPos.left= event.target.scrollLeft;
				this.scroll();
			} else if (event.target.className == 'scrollbar scrolly') {
				this.scrollPos.top= event.target.scrollTop;
				this.rowix= parseInt(event.target.scrollTop/this.config.rowHeight);
				this.sendCommand("getdata", {"rowix":this.rowix, "rowsxpage":this.rowsxpage});
			}
			break;

		case 'DOMMouseScroll':
			// Firefox: event.axis= 1-x,2-y; event.detail= ticks.
			this.scrollWheel(event.axis, event.detail*4);
			event.preventDefault();
			break;

		case 'mousewheel':
			// event.wheelDelta= ticks*(-120); event.wheelDeltaX= ticksX*(-120); event.wheelDeltaY= ticksY*(-120);
			this.scrollWheel(event.wheelDeltaX != 0? 1 : 2, -event.wheelDelta/15);
			event.preventDefault();
			break;				
			
	}
};
	
frmxlist.prototype.createStructure= function(tabledef){

	this.tabledef= tabledef;
	
	var myp= document.createElement("p");
	myp.innerHTML= "Mi identificador es " + this.id;
	this.div.appendChild(myp);

	// la tabla dinámica
	
	// STYLES
	////////////////////////////////
	var css= document.createElement('style');
	css.type= 'text/css';
	css.id= this.id + "_style";
	this.styleSheet= document.getElementsByTagName("head")[0].appendChild(css).sheet;

	this.setColStyles(true);
	this.scrollRuleIx= this.styleSheet.cssRules.length;
	var ruleCount= this.scrollRuleIx;

	// ELEMENTS
	/////////////////////////////
	var lvCont= document.createElement("div");
	lvCont.className= "listcont container";
	var lv= document.createElement("div");
	lv.className= "listview view";

	var voidCell= document.createElement("div"); 
	voidCell.className= 'cell void';
	
	var hd= document.createElement("div");
	hd.className= "header";

	var row= document.createElement("div");
	row.className= "row";
	for (var icol= 0; icol < tabledef.cols.length; icol++){
		var cell= document.createElement("div");
		cell.className= "cell";
		cell.id= tabledef.cols[icol].id;
		cell.innerHTML= tabledef.cols[icol].caption;
		var rh= document.createElement("div");
		rh.className= 'resizecolhandle';
		rh.addEventListener('mousedown', this, false);
		rh.addEventListener('dragstart', this, false);
		rh.addEventListener('selectstart', this, false);
		cell.insertBefore(rh, cell.firstChild);
		row.appendChild(cell);
	}
	row.appendChild(voidCell.cloneNode(false));
	hd.appendChild(row);
	lv.appendChild(hd);
	
	var bd= document.createElement("div");
	bd.className= "body";
	
	this.config.bodyRows= parseInt(this.config.height / this.config.rowHeight) - 1;
	for (var irow=0; irow<this.config.bodyRows; irow++){
		var row= document.createElement("div");
		row.className= irow % 2 == 0 ? "row" : "row alternate";
		row.id= "row" + irow;
		for (var icol= 0; icol < tabledef.cols.length; icol++){
			var cell= document.createElement("div");
			cell.className= "cell";
			cell.id= tabledef.cols[icol].id;
			cell.innerHTML= icol==0 ? irow : "-";
			row.appendChild(cell);
		}
		row.appendChild(voidCell.cloneNode(false));
		bd.appendChild(row);
	}
	bd.addEventListener('DOMMouseScroll', this, false);
	bd.addEventListener('mousewheel', this, false);
	lv.appendChild(bd);

	
	/* FOOTER NOT IMPLEMENTED YET
		<div class="Footer">
			<div class="Row"><div class="Cell" id="selected"></div><div class="Cell" id="noaveria">F NA</div><div class="Cell" id="sala">F Sala</div><div class="Cell" id="maquina">F Maquina</div><div class="Cell" id="modelo">F Modelo</div><div class="Cell" id="descripcion">F Descrip</div><div class="Cell" id="maquinaparada">F MP</div><div class="Cell" id="prioridad">F Priori</div></div>
		</div>
	*/

	lvCont.appendChild(lv);
	this.div.appendChild(lvCont);
	
	var sx= document.createElement("div");
	sx.className= "scrollbar scrollx";
	sx.addEventListener('scroll', this, false);
	var ssx= document.createElement("div");
	ssx.className= "sizescrollbar sizescrollx";
	sx.appendChild(ssx);
	this.div.appendChild(sx);
	
	var sy= document.createElement("div");
	sy.className= "scrollbar scrolly";
	sy.addEventListener('scroll', this, false);
	var ssy= document.createElement("div");
	ssy.className= "sizescrollbar sizescrolly";
	sy.appendChild(ssy);
	this.div.appendChild(sy);

	var rh= document.createElement("div");
	rh.className= "resizeviewhandle";
	rh.addEventListener('mousedown', this, false);
	rh.addEventListener('dragstart', this, false);
	rh.addEventListener('selectstart', this, false);
	this.div.appendChild(rh);

	window.addEventListener('mouseup', this, false);
	
	this.resize(0,0,true);
	
}

frmxlist.prototype.setData= function(pageData){
	var mybody= this.div.getElementsByClassName('body')[0];
	for (var irow=0; irow < mybody.childNodes.length; irow++){
		var myrow= mybody.childNodes[irow];
		for (var icol= 0; icol < myrow.childNodes.length-1; icol++){
			var myCell= myrow.childNodes[icol];
			myCell.innerHTML= (pageData[irow] == undefined)? '-' : pageData[irow][myCell.id];
		}
	}
}

frmxlist.prototype.executeCommand= function(serverCommand){
/*	CUIDADÍN CON EL JSON. 
	Si el texto json no está bien construido, se aborta la ejecución sin más. 
	como el eval. 
*/
	
	if (serverCommand.substring(0,5) != 'JSON:') {
		alert(serverCommand);
		return;
	}
	var command= JSON.parse(serverCommand.substring(5));
	switch (command[0]){
	case "setTableDef":
		this.createStructure(command[1]);
		break;
		
	case "setData":
		this.setData(command[1]);
		break;
		
	default:
		alert(command[0]+ " not implemented.");	
	}		
}

frmxlist.prototype.sendAction= function(action){
	switch(action){
	case 'next':
		this.rowix+= this.rowsxpage;
		this.sendCommand('getdata', {"rowix":this.rowix, "rowsxpage":this.rowsxpage}); 
		break;
	case 'previous': 
		this.rowix-= this.rowsxpage;
		this.sendCommand('getdata', {"rowix":this.rowix, "rowsxpage":this.rowsxpage}); 
		break;
	}
	
}

// Métodos para la comunicación con el servidor
frmxlist.prototype.sendCommand= function(command, args){

	var argString= '';
	for(var arg in args){
		argString+="&" + arg + "=" + args[arg]; 
	}
	
	var rq= new XMLHttpRequest();
	rq.parentFrame= this;
	rq.open('POST','xhrindex.php', true);
	rq.onreadystatechange = function(evt) {
/*	-------- USO DEL THIS
 * Mucho cuidadín con el uso del this. En el caso de un evento, this se cambia por el 
 * propietario del evento. El elemento html que generó el evento, o en este
 * caso el objeto xmlhttpresponse que generó el evento.
 * Por eso nos creamos una propiedad en este evento y copiamos el this de nuestro objeto.	
 * (Un poco extraño para viejos programadores como yo.) Pero funciona.
 * 	--------
 */		
		if (this.readyState == 4 && this.status == 200) {
			if (this.responseText != '') {
				//alert('getting response:('+ this.responseText +')');
				this.parentFrame.executeCommand(this.responseText);
			} //else alert('No response for this.');
		} //rq.responseText contiene la respuesta.			
	};
	
/* 	------ METODO POST
    ------ Para los nuevos Firefox
 	var data = new FormData();
	data.append('Nombre','Domingo');
	data.append('Apellidos', 'Melián Cárdenes');
	------ Para los antiguos
*/
	var postString= "sessionno=" + document.getElementsByName('sessionno')[0].value
	+ "&installationid=" + document.getElementsByName('installationid')[0].value
	+ "&sequenceno=" + document.getElementsByName('sequenceno')[0].value
	+ "&action=xhrcommand&command=" + command + "&frameid=" + this.id;
	if (argString!= "") postString+= "&" + argString;
	
	var post= encodeURI(postString);
	rq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//		rq.setRequestHeader("Content-length", post.length);
//		rq.setRequestHeader("Connection", "close");
	
	rq.send(post);
}


