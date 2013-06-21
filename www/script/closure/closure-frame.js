(function($, undefined) {	$.extend( {'currentFrame':{
	
	// Attributes	
	form: '',
	id: null,
	
	// Methods
	OnCreate: function(form, id){
		this.form= form;
		this.id= id;
	},

	Specific: function(){
	},

	OnLoad: function(){
		//Error: El selector en jquery es diferente. primero se selecciona el id y luego se hace un filtro.
		//$("#" + this.id).children(".resize").resizable({handles:"s"});
		$("#" + this.id).find(".ia_frame_content").resizable({handles:"s"});

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
	}

}});}) (jQuery);

