function bas_frmx_form(){}

bas_frmx_form.prototype.onload= function(){

	

	//Toolbar
	for (var action in this.toolbar.actions){
		// Create the button
		$("#toolbar_" + action).button( {
			text: false
			, icons:{primary: this.toolbar.actions[action]['icon']}
			, disabled: !this.toolbar.actions[action]['enabled']
		});
		
		// Link the menu.
		if (this.toolbar.actions[action].type == 'menu'){
			$("#toolbar_" + action).click(function() {
				var menu=$("#toobar_menu_" + action).show()
					.position({my: "right top", at: "right bottom", of: this });

	            $( document ).one( "click", function() {
	                menu.hide();
	            });

				return false;
			});
			
			$("#toolbar_menu_" + action).hide()
			.menu({ position: { my: "right top", at: "left top" }
			, icons: { submenu: "ui-icon-carat-1-w" } })
			;
		}
	}

/*	
			
		//ButtonBar
	
		$("#buttonbar").children().button();
		

		$("#customer").click(function() {
				var menu=$("#menu_customer").show()
					.position({my: "right bottom", at: "right top", of: this });

                $( document ).one( "click", function() {
                    menu.hide();
                });

				return false;
			});

		$("#menu_customer").hide()
			.menu({ position: { my: "right bottom", at: "left bottom" }
			, icons: { submenu: "ui-icon-carat-1-w" } })
			;

	
*/	
	
	
	$(".framecontainer").sortable({handle: ".ia_frame_header"}).disableSelection();
	
	for (var frameid in this.frames){
		if (this.frames[frameid].jsClass in window) {
			var frame= new window[this.frames[frameid].jsClass]();
			bas_copyAttributes(this.frames[frameid], frame);
			frame.onload();
			
		} else {
			if (this.frames[frameid].jsClass != '') {
				alert('The javascript <'+ this.frames[frameid].jsClass +'> class is undefined.');
			}
		}
	}
	
};

function bas_frmx_frame(form, id){
	
	if (arguments.length == 0) return; // to be able to inherit from this
	
	this.form= form;
	this.id= id;
}
	
bas_frmx_frame.prototype.onload= function(){
	
	//Error: El selector en jquery es diferente. primero se selecciona el id y luego se hace un filtro.
	$("#" + this.id).children(".resize").resizable({handles:"s"});

	// ### Nota: No se puede eliminar la inserción de clases de momento. Al mirar las clases el form.js lanza un error.
	// Posible solucion: Insertar nuestras clases (frmx_frame) en primer lugar y en el momento de leer las clases buscar hasta el primer espacio.
	// En el caso de las funciones (maximizar, colapsar, etc) utilizar la clase funciones y el form.js omita su carga de fichero especiales.
	
	$("#" + this.id).addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
	.find( ".ia_frame_header" )
		.addClass( "ui-widget-header ui-corner-all" )
		.prepend( "<span class='bt_maximize ui-icon ui-icon-circle-triangle-n'></span>")
		.prepend( "<span class='bt_collapse ui-icon ui-icon-circle-minus colapso'></span>")
		.end()
	.find( ".ia_frame_content" );
	
	$("#" + this.id).find(".ia_frame_header .bt_maximize" ).on("click",function() {
		if ($(this).hasClass("ui-icon-circle-triangle-n")) {
			$(this).parents( ".framecontainer:first" ).sortable("option", "disabled", true);
			$(this).parent().parent().siblings().css("visibility", "hidden");
		} else {
			$(this).parents( ".framecontainer:first" ).sortable("option", "disabled", false);
			$(this).parent().parent().siblings().css("visibility", "visible");	
		}
		$(this).toggleClass( "ui-icon-circle-triangle-n").toggleClass("ui-icon-circle-triangle-s")
				.parent().parent().toggleClass("maximized");			
	});
	
/*	$("#" + this.id).find(".colapso").click( function(e){
		$(e.target).toggleClass( "ui-icon-circle-minus" ).toggleClass( "ui-icon-circle-plus" );
		$(e.target).parent().next().slideToggle();// El contenido siempre estara en el siguiente div
		// El peligro esta en la posición del boton, puede añadirse mas botones. Solucion ponerle una clase generica "header" y buscarlo cm padre.

			});
		*/	
//Codigo optimizado para la expansion


	$("#" + this.id).find(".colapso").on("click", function(e){
		
		$(e.target).toggleClass( "ui-icon-circle-minus" ).toggleClass( "ui-icon-circle-plus" );
		
		if ($(e.target).parent().parent().hasClass("maximized")){
			$(this).parents( ".framecontainer:first" ).sortable("option", "disabled", false);
			$(this).parent().parent().siblings().css("visibility", "visible");	
			$(this).next().toggleClass( "ui-icon-circle-triangle-n").toggleClass("ui-icon-circle-triangle-s");
			$(this).parent().parent().toggleClass("maximized");
		}
		$(e.target).parent().next().slideToggle();// El contenido siempre estara en el siguiente div
		// El peligro esta en la posición del boton, puede añadirse mas botones. Solucion ponerle una clase generica "header" y buscarlo cm padre.
	});
		
};		

function bas_frmx_cardframe(form, id){
	
	if (arguments.length == 0) return; // to be able to inherit from this


	bas_frmx_frame.apply(this, arguments);
}

bas_frmx_cardframe.prototype= new bas_frmx_frame();
bas_frmx_cardframe.prototype.constructor= bas_frmx_frame;


function bas_copyAttributes(src, dst){
	for (var attr in src) dst[attr]= src[attr];
};
