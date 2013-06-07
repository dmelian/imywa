function bas_frmx_cardframe(form, id){
	
	if (arguments.length == 0) return; // to be able to inherit from this
	
	this.form= form;
	this.id= id;
}

bas_frmx_cardframe.prototype= new bas_frmx_frame();
bas_frmx_cardframe.prototype.constructor= bas_frmx_cardframe;

function change(id){
	test(id);	
}

bas_frmx_cardframe.prototype.OnLoad= function(){

	bas_frmx_frame.prototype.OnLoad.call(this);
  
	//Aquí tendría que ir la clase ia_resize. Así añadimos la posibilidad de permitir o no el resize
	$("#" + this.id).find(".ia_frame_content").resizable({handles:"s"});
	
	/*
	$("#" + this.id).addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
	.find( ".ia_frame_header" )
		.addClass( "ui-widget-header ui-corner-all" )
		.append("<span class=\'bt_maximize ui-icon ui-icon-circle-triangle-n\'style=\"float:right;margin-top:12px;margin-right:5px;\"></span>")
		.append("<span class=\'bt_collapse ui-icon ui-icon-circle-minus colapso\'style=\"float:right;margin-top:12px;\"></span>")
		.end()
	.find( ".ia_frame_content" );*/
	
    	
	/*$("#" + this.id).find(".bt_collapse").on("click",function(e){
			$(e.target).toggleClass( "ui-icon-circle-minus" ).toggleClass( "ui-icon-circle-plus" );
			$(e.target).parent().next(".ia_frame_content").slideToggle();
	});*/
	
	
	$("#" + this.id).find("form").on('submit',function(e){
		e.preventDefault();
	});
	//################################
	//     Funcionalidad extendida.
	//################################
	var tabs = $("#" + this.id).find( ".ia_tabs" ).tabs();
	$("#" + this.id).find(".ia_listtab").sortable({handle: ".ia_tab_item"}).disableSelection();
	
	$("#" + this.id).find(".ia_labelfield").on("click", function(e){
		$(e.target).next().focus();
// 		$(".ia_statusbar").html($(e.target).html()+ ": "+	$(e.target).next().attr('value'));
	});
	
	$("#" + this.id).find(".ia_inputfield").on("focus", function(e){
		$(".ia_statusbar").html($(e.target).prev().html()	+ ": "+	$(e.target).attr('value'));
	});
	
	$("#" + this.id).find(".ia_inputfield").on("blur", function(e){
		$(".ia_statusbar").html('');
	});
	
	
	//################################
	//     Cambio de tema.
	//################################
	/*$.ajax({
	url: "script/frmx/test.js",
	dataType: "script",
	async: false
	});

	$.ajax({
	url: "script/frmx/test2.js",
	dataType: "script",
	async: false
	});

	$.ajax({
	url: "script/frmx/test3.js",
	dataType: "script",
	async: false
	});
	change(this.id);*/
		
};

