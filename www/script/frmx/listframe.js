function bas_frmx_listframe(form, id){
	
	if (arguments.length == 0) return; // to be able to inherit from this

	
	bas_frmx_frame.apply(this, arguments);
	this.scroll = 0;
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
}


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

	$("#" + this.id).find( ".ia_Colums_dinamic" ).sortable({handle: ".header_columDinamic"});//.disableSelection();
	
	$("#" + this.id).find( ".ia_Colums_fixed" ).sortable({handle: ".header_columStatic"});//.disableSelection();
	

/*----------------------------------------------------------------
		###		Realización del resize de las distintas columnas.
 ----------------------------------------------------------------*/
 
	$("#" + this.id).find( ".columStatic" ).resizable({
		handles: "e",
		stop: function( event, ui ) {
			var inc = $(ui.element).width() - ui.originalSize.width;
			//alert($(".ia_Colums_fixed").width()+inc);
			$(".ia_Colums_fixed").width($(".ia_Colums_fixed").width()+inc);
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
	
}

bas_frmx_listframe.prototype.setSelected = function(pos){
	//alert("row_" + $(event.target).attr("id"));

	$("#" + this.id).find(".ui-selected").toggleClass("ui-selected");
	$("#" + this.id).find(".ui-icon-play").toggleClass("ui-icon");
	$("#" + this.id).find(".ui-icon-play").toggleClass("ui-icon-play");
	$("#" + this.id).find(".ia_selected_box").toggleClass("ia_selected_box").toggleClass("ia_select_box");

	$("#" + this.id).find(".row_" + pos).toggleClass("ui-selected");
	$("#" + this.id).find("#"+pos).toggleClass("ia_select_box").toggleClass("ia_selected_box").toggleClass("ui-icon ui-icon-play");
	
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
        
}






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