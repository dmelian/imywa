function bas_frmx_listframe(form, id){
	
	if (arguments.length == 0) return; // to be able to inherit from this

	
	bas_frmx_frame.apply(this, arguments);
	this.scroll = 0;
	this.fieldSelect = "";
}

bas_frmx_listframe.prototype= new bas_frmx_frame();
bas_frmx_listframe.prototype.constructor= bas_frmx_listframe;

bas_frmx_listframe.prototype.CheckWidth= function(){
	if ($("#" + this.id).find(".ia_listFixed").width() > $("#" + this.id).find(".ia_Colums_fixed").width()){
		var ancho = $("#" + this.id).find(".ia_listDinamic").width();
		var diferencia = ($("#" + this.id).find(".ia_listFixed").width() - $("#" + this.id).find(".ia_Colums_fixed").width());
		$("#" + this.id).find(".ia_listDinamic").width(ancho + diferencia);
		$("#" + this.id).find(".ia_listFixed").width($("#" + this.id).find(".ia_Colums_fixed").width());
		
	}	
};


bas_frmx_listframe.prototype.OnLoad= function(){

	bas_frmx_frame.prototype.OnLoad.call(this);

// 	this.Customer();
	var mainThis = this;
	var frameID= this.id ;
	var target_scroll = $("#" + this.id).find(".scroll_List"); 
	target_scroll.scroll(function () {
		var current_top = target_scroll.scrollTop();
		if(current_top != this.scroll){
			var heigthRow = 18;
			var topRow = 4;
			var posPt = ((3*current_top)/4); // transformacion de px a pt
			var pos = Math.round(posPt/(heigthRow+topRow	)); //magic number ??
			currentForm.sendAction('scroll_move',{"frameid":frameID,"pos": pos});
		}
	});
	
	if ($("#" + this.id).find(".ia_listFixed").width() > $("#" + this.id).find(".ia_Colums_fixed").width()){
		var ancho = $("#" + this.id).find(".ia_listDinamic").width();
		var diferencia = ($("#" + this.id).find(".ia_listFixed").width() - $("#" + this.id).find(".ia_Colums_fixed").width());
// 		$("#" + this.id).find(".ia_listDinamic").width(ancho + diferencia);
		$("#" + this.id).find(".ia_listFixed").width($("#" + this.id).find(".ia_Colums_fixed").width());
// 		alert($("#" + this.id).find(".ia_listDinamic").attr("left"));
// 		$("#" + this.id).find(".ia_listDinamic").width($("#" + this.id).find(".ia_Colums_fixed").width());

		$("#" + this.id).find(".ia_listDinamic").css("left", $("#" + this.id).find(".ia_Colums_fixed").width()+"px");
		
// 		alert($("#" + this.id).find(".ia_listDinamic").attr("left"));
		
	}

/*----------------------------------------------------------------
		###		Realización del sortable de las distintas columnas.
 ----------------------------------------------------------------*/

	$("#" + this.id).find( ".ia_Colums_dinamic" ).sortable({
		handle: ".header_columDinamic",
		beforeStop: function( event, ui ) {
			mainThis.setOrderCol("dinamic");
		}
	});//.disableSelection();
	
	$("#" + this.id).find( ".ia_Colums_fixed" ).sortable({
		handle: ".header_columStatic",
		beforeStop: function( event, ui ) {
			mainThis.setOrderCol("static");
		}
	});//.disableSelection();
	

/*----------------------------------------------------------------
		###		Realización del resize de las distintas columnas.
 ----------------------------------------------------------------*/
 
	$("#" + this.id).find( ".columStatic" ).resizable({
		handles: "e",
		stop: function( event, ui ) {
			var inc = $(ui.element).width() - ui.originalSize.width;
			$(".ia_Colums_fixed").width($(".ia_Colums_fixed").width()+inc);

			mainThis.setResize(this.attributes.field.value,$(ui.element).width());
		},
		//  minHeight: 150,
		minWidth: 50,
		ghost: true
	});
	
	$("#" + this.id).find( ".columDinamic" ).resizable({
		handles: "e",
		stop: function( event, ui ) {
			var inc = $(ui.element).width() - ui.originalSize.width;
			$(".ia_Colums_dinamic").width($(".ia_Colums_dinamic").width()+inc);
			
			mainThis.setResize(this.attributes.field.value,$(ui.element).width());
		},
		//  minHeight: 150,
		minWidth: 50,
		ghost: true
	});

/*----------------------------------------------------------------
		###		Realización del marcado en las filas seleccionadas. (selected)
 ----------------------------------------------------------------*/	
	
	$("#" + this.id).on("click",".selector_row",function(event){
		//alert("row_" + $(event.target).attr("id"));
		
		var pos = $(event.target).attr("id");

		mainThis.setSelected(pos);
	});
	
	
	
	$("#" + this.id).on("click",".list_row",function(event){
		var pos = $(event.target).attr("pos");
		mainThis.setSelected(pos);
		mainThis.getSelected();
	});
	
	$("#" + this.id).on("contextmenu",".ia_header_colum",function(event){
		var w = event.pageX;
		var h = event.pageY;
		event.preventDefault();
// 		alert("X: "+w+" Y: "+h);
		mainThis.showContextMenu();

		
		w = w - $("#ia_cotextMenu_header").width()/2;
		h = h - $("#ia_cotextMenu_header").height()/2;
		$("#ia_cotextMenu_header").css("left",w + "px");
		$("#ia_cotextMenu_header").css("top",h + "px");
		
// 		alert($(event.target).attr("field"));
		if (event.target.tagName == "LABEL") mainThis.fieldSelect = $(event.target).parent().attr("field");
		else mainThis.fieldSelect = $(event.target).attr("field");
		
		$('#ia_cotextMenu_header').fadeIn(1000);
		
		
	});

	if (!$('#ia_cotextMenu_header').length){
		$(".ia_menuContainer").append("<div id='ia_cotextMenu_header' idFrame='' style='position: absolute;width:90pt;border-color: black;border-style: solid;'></div>");
		$('#ia_cotextMenu_header').hide();
		$("body").on("click",function(event){
// 			$('#ia_cotextMenu_header').menu("destroy");

// 			$('#ia_cotextMenu_header').fadeOut(100);
			$('#ia_cotextMenu_header').hide();
			$('#ia_cotextMenu_header').html("");
		});
	}
	
	if (this.dbClick){
// 		$("#" + this.id).find(".list_row").dblclick(function(event){
		$("#" + this.id).on("dblclick",".list_row",function(event){
			var pos = $(event.target).attr("pos");
			var field = $(event.target).attr("field");
			var id = String(mainThis.id);
	// 		mainThis.setSelected(pos);
			currentForm.sendAction('dbclickList',{"frameID":id,"posDbClick": pos,"field":field});
		});
	}
	
};

bas_frmx_listframe.prototype.getHidenCols = function(){
// 	alert(this.fieldSelect);
	var ocultos = [];
	var pos = 0;
	var items = $("#"+this.id).find(".ia_header_colum").filter("[hide='yes']");
	items.each(function(){
		ocultos[pos] = this.attributes.field.value;
		pos++;
// 		alert(this.attributes.field.value);
	});
// 	alert(ocultos);
	return ocultos;
	
}

bas_frmx_listframe.prototype.hidenCol = function(hide){
	var column = $("#"+this.id).find(".ia_header_colum").filter("[field="+this.fieldSelect+"]");
	if (hide == undefined)	column.attr("hide","yes");
	else column.attr("hide","no");
	column = column.parent();
// 	column.css("display","none");
	column.slideToggle();
}

bas_frmx_listframe.prototype.showCol = function(field){
	this.fieldSelect = field;
	this.hidenCol("no");
}

bas_frmx_listframe.prototype.showAllCol = function(){
	var hiddenCols = this.getHidenCols();
	for(var ind=0; ind< hiddenCols.length; ind++){
		this.showCol(hiddenCols[ind]);
	}
}

bas_frmx_listframe.prototype.setResize = function(field, width){
	var ancho_pt= (3 * width ) / 4;			
	currentForm.sendAction('setColWidth',{
		'width': ancho_pt
		, 'field': field
		, 'frameid': this.id
	});
	
}

bas_frmx_listframe.prototype.setOrderCol = function(typeCol){
		var static = $("#" + this.id).find( ".ia_Colums_fixed" ).sortable( "toArray", {attribute:"field"} );
		var sorted = $("#" + this.id).find( ".ia_Colums_dinamic" ).sortable( "toArray", {attribute:"field"} );
		
		if (typeCol == "dinamic"){
			if ($.isArray(static)){
				sorted = static.concat(sorted);
			}
		}else{
			if ($.isArray(sorted)){
				sorted = static.concat(sorted);
			}
			else{
				sorted = static;
			}			
		}
// 		alert("STOOOP: "+sorted);
		var frameID = this.id;
		currentForm.sendAction('setColOrder',{
				'order': sorted
				, 'frameid': frameID
		});
		
}


bas_frmx_listframe.prototype.showContextMenu = function(){
	var hiddenCols = this.getHidenCols();
	var ocultos = "";
	if (hiddenCols.length != 0){
		ocultos = "<ul>";
		for(var ind=0; ind< hiddenCols.length; ind++){
			ocultos += "<li ><a onclick=\"currentForm.frames['"+this.id+"'].showCol('"+hiddenCols[ind]+"');\" >"+hiddenCols[ind]+"   </a> </li>";
		}
		ocultos += "</ul>";
	}
	var acciones = "<ul id='ia_header_ul'name='actions'>";
	acciones += "<li><a onclick=\"currentForm.frames['"+this.id+"'].hidenCol();\" >Ocultar</a></li>";
	acciones += "<li><a onclick=\"currentForm.frames['"+this.id+"'].getHidenCols();\">Ordenar por..</a></li>";
	if (hiddenCols.length != 0){
		acciones += "<li><a >Mostrar</a>" + ocultos +" </li>";
		acciones += "<li><a onclick=\"currentForm.frames['"+this.id+"'].showAllCol();\">Mostrar todas</a></li>";
	}
	acciones += "</ul>";
		
	$('#ia_cotextMenu_header').html(acciones);
	$('#ia_cotextMenu_header').children().menu({icons : {
			submenu : "ui-icon-carat-1-w"
			}});
}

bas_frmx_listframe.prototype.setSelected = function(pos){
	//alert("row_" + $(event.target).attr("id"));
	
	if (this.multiSelected){
		$("#" + this.id).find(".row_" + pos).toggleClass("ui-selected");
		$("#" + this.id).find("#"+pos).toggleClass("ia_select_box").toggleClass("ia_selected_box").toggleClass("ui-icon ui-icon-play");
	}
	else{
		$("#" + this.id).find(".ui-selected").toggleClass("ui-selected");
		$("#" + this.id).find(".ui-icon-play").toggleClass("ui-icon");
		$("#" + this.id).find(".ui-icon-play").toggleClass("ui-icon-play");
		$("#" + this.id).find(".ia_selected_box").toggleClass("ia_selected_box").toggleClass("ia_select_box");

		$("#" + this.id).find(".row_" + pos).toggleClass("ui-selected");
		$("#" + this.id).find("#"+pos).toggleClass("ia_select_box").toggleClass("ia_selected_box").toggleClass("ui-icon ui-icon-play");
	}
};


bas_frmx_listframe.prototype.unSelected = function(pos){

	$("#" + this.id).find(".row_" + pos).toggleClass("ui-selected");
	$("#" + this.id).find("#"+pos).toggleClass("ia_select_box").toggleClass("ia_selected_box").toggleClass("ui-icon ui-icon-play");
	
};


bas_frmx_listframe.prototype.getSelected = function(){
	var selected = "";
	var items = $("#" + this.id).find(".ui-icon-play");
	var sep = "";
	if (items.length == 0) return undefined;
	items.each(function(event){
		selected = selected + sep + this.id;
		sep =",";
	});
	return selected;
}

bas_frmx_listframe.prototype.getSelectedFIeld = function(field){
	var selected = "";
	var items = $("#" + this.id).find(".ui-selected").filter("[field = "+field+"]");
	var sep = "";
	if (items.length == 0) return undefined;
	items.each(function(event){
		selected = selected + sep + this.field;
		sep =",";
	});
	return selected;
}

bas_frmx_listframe.prototype.Reload = function(data, selected, size, reset){
	var pos =1;
	var nelem = data.length;

	var row;  
    for (var i=1;i<=size;i++){ // ### Este for es un parche temporarl. Lo ideal es recorrer de forma independiente los campos o que se envien siempre todos los campos del registro, existan o no.
            row = $("#" + this.id).find(".row_"+i); 
    //                 row = $("#" + this.id).find(".row_1"); 
            row.each(function( index ) {
                $(this).html(" ");
              
            }); 
        }
    
    
    if (nelem != 0){
        for (var i=1;i<=size;i++){ // ### Este for es un parche temporarl. Lo ideal es recorrer de forma independiente los campos o que se envien siempre todos los campos del registro, existan o no.
                row = $("#" + this.id).find(".row_"+i); 
                for (var field in data[0]){
                    row.filter("."+field).html(" ");
                }
        }
        $("#" + this.id).find(".scroll_List")[0].childNodes[0].style.height = reset+"pt";

        for (var i=0;i<nelem;i++){ // Recorremos los datos secuencialmente, insertando el contenido de cada fila en su columna correspondiente.
            row = $("#" + this.id).find(".row_"+pos); 
            for (var field in data[i]){
                row.filter("."+field).html(data[i][field]);
            }
            pos++;		
        }

    }
//     else{
//         for (var i=1;i<=size;i++){ // ### Este for es un parche temporarl. Lo ideal es recorrer de forma independiente los campos o que se envien siempre todos los campos del registro, existan o no.
//             row = $("#" + this.id).find(".row_"+i); 
//     //                 row = $("#" + this.id).find(".row_1"); 
//             row.each(function( index ) {
//                 $(this).html(" ");
//               
//             }); 
//         }
//           
//     }
    this.setSelected(selected);
        
};






// bas_frmx_listframe.prototype.Reload = function(data, selected, size, reset){
// 	var pos =1;
// 	var nelem = data.length;
// // 	var tam = data.size;
// 	var row;
// 	
// 	$("#" + this.id).find(".scroll_List")[0].childNodes[0].style.height = reset+"pt";
// // 	var aux = $("#" + this.id).find(".scroll_List")[0].childNodes[0].style.height;//offsetHeight;
// 	for (var i=0;i<nelem;i++){ // Recorremos los datos secuencialmente, insertando el contenido de cada fila en su columna correspondiente.
// 		row = $("#" + this.id).find(".row_"+pos); 
// 		for (var field in data[i]){
// 			row.filter("."+field).html(data[i][field]);
// 		}
// 		pos++;		
// 	}
// 	if (size > nelem){ // Si se envia un número de elementos menor que el tamaño de la ventana se tendrá que borrar el contenido de los X faltantes.
// // 		alert("estoy");
// 
// 		pos = nelem+1;
// 		for (var i=nelem+1;i<=size;i++){
// 			row = $("#" + this.id).find(".row_"+pos); 
// 			for (var field in data[0]){
// 				row.filter("."+field).html(" ");
// 			}
// 			pos++;		
// 		}
// 	}
// 
// 	this.setSelected(selected);
// }