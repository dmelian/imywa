
function frmxlist(form, div){
	
	this.createStructure= function(tabledef){
		var myp= document.createElement("p");
		myp.innerHTML= "Mi identificador es " + this.id;
		this.div.appendChild(myp);

		// la tabla
		var mytable= document.createElement("table");
		for (var irow=0; irow<=this.rowsxpage; irow++){
			var myrow= document.createElement("tr");
			for (var icol= 0; icol < tabledef.cols.length; icol++){
				var mycell= document.createElement(irow == 0 ? "th" : "td");
				mycell.innerHTML= irow == 0 ? tabledef.cols[icol].caption : "--";
				myrow.appendChild(mycell);
			}
			mytable.appendChild(myrow);
		}
		this.div.appendChild(mytable);
		this.tabledef= tabledef;
		this.sendCommand("getdata", {"rowix":this.rowix, "rowsxpage":this.rowsxpage});

		// Los botones de navegación.
		// Next, Previous.
		// Resize.
		// First, Last.

		var myactions= ['previous','next'];
		var mydivbuttons= document.createElement("div");
		for (actionix=0; actionix<myactions.length; actionix++){
			var mybutton= document.createElement("input");
			mybutton.type= "button";
			mybutton.value= myactions[actionix];
			mybutton.parentFrame= this;
			mybutton.onclick= function(evt){
				//alert(this.value);
				this.parentFrame.sendAction(this.value);
			};
			mydivbuttons.appendChild(mybutton);
		}
		this.div.appendChild(mydivbuttons);
		
	}
	
	this.setData= function(pageData){
		var mytable= this.div.getElementsByTagName('table')[0];
		for (var irow=1; irow<mytable.childNodes.length; irow++){
			var myrow= mytable.childNodes[irow];
			for (var icol=0; icol<myrow.childNodes.length; icol++){
				var mycell= myrow.childNodes[icol];
				if (pageData[irow-1] == undefined) mycell.innerHTML= "-";
				else mycell.innerHTML= pageData[irow-1][this.tabledef.cols[icol].id];
			}
		}		
	}
	
	// Ejecución de acciones
	this.executeCommand= function(serverCommand){
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
		case "setInitialRow":
			//¿A donde apunta el this ahora?
			this.old_recarga(command[1]);
			break;
			
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
	
	this.sendAction= function(action){
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
	this.sendCommand= function(command, args){

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
	
	// Attributes
	this.form= form;
	this.div= div;
	this.id= div.id;
	this.rowix= 0;
	this.rowsxpage= 10;
	
	//this.old_createStructure();
	
	this.sendCommand("gettabledef");


}

