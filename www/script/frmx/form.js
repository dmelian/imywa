
function bas_frmx_form(){}

bas_frmx_form.prototype.OnLoad= function(){

	

	
$( ".ia_menuframe" ).menu();
$( ".ia_menubar" ).menu({ position: { my: "right bottom", at: "left bottom" }, icons: { submenu: "ui-icon-carat-1-w" } }).hide();

$(".ia_submenubar_button").on("click",function(e){
// 	alert("entramos!!!!");
	var id = $(e.target).attr("id");
	if ($(".submenu_Show").length != 0){
		if (!($("#submenu_"+id).hasClass("submenu_Show"))){
			$(".submenu_Show").hide().toggleClass("submenu_Show");
		}
	}
	var menu= $("#submenu_"+id).fadeToggle("fast", "linear").position({my: "right bottom", at: "right top", of: this }).toggleClass("submenu_Show");
// 	alert("visaulizamos el id: "+id);

});

$(".ia_menutool_button").on("click",function(e){
// 	alert("entramos!!!!");
	var id =  e.target.parentElement.id;
	if ($(".submenu_Show").length != 0){
		if (!($("#submenu_"+id).hasClass("submenu_Show"))){
			$(".submenu_Show").hide().toggleClass("submenu_Show");
		}
	}
	$("#submenu_"+id).fadeToggle("fast", "linear").position({my: "right bottom", at: "right top", of: this }).toggleClass("submenu_Show");
});

// #### Control del hover en el BreadCrumb.
$(".ia_bread_item").hover(function(e){
	if (!$(e.target).hasClass("ia_bread_fixed")){
		if (e.target.tagName == "A")$(e.target).toggleClass("ia_bread_hover");
	}
});


// #### Ocultación de los submenus al haces click fuera de ellos.
$(":not(.ia_menu_item)").on("click",function(e){
	if (!($(e.target).hasClass("ia_submenubar_button"))){
		if ($(".submenu_Show").length != 0){
			$(".submenu_Show").hide().toggleClass("submenu_Show");
		}
	}
});

	//Toolbar
	if (this.toolbar != undefined){
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

			$( document ).on( "click", function() {
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
	}

	$(".dashboard_loadevents").on("change",function(evt){
		currentForm.sendAction("changePartition"
				,{"db": $(evt.target).attr('id'), "partitionId": $(evt.target).attr('value')}
				,"SessionAction");
	}).toggleClass("dashboard_loadevents");


	$(".dash_element").on("click",function(){
// 		currentForm.sendAction("changeApp", {'app':$(this).parent().attr("id")},SessionAction);
		if (!$(this).parent().hasClass("selected_dash")){
			currentForm.sendAction("changeApp", {'app':$(this).parent().attr("id")},"SessionAction");
 			$(".selected_dash").toggleClass("selected_dash");
 			$(this).parent().addClass("selected_dash");
// 			alert($( "#accordion" ).accordion( "option", "active" ));
		}
	});
/////////////accordion
	currentForm.loadDashboard();

	if (! $("body").hasClass("evnt_lookup")){
		
// 		$("body").on("click",":input",function(e){
// 			$(e.target).focus();
// 		});
		
		$("body").toggleClass("evnt_lookup");
		$("body").on("click",".lookup",function(e){
			var accion=this.name;
			var parent = $(this).parents(".ia_cardframe").attr("id");
			if ($(this).parents(".ui-dialog-content").length) {
				e.preventDefault();
				$(".ui-dialog-content").dialog('close');
			}
			submitlookup(parent,accion);
		});		
	}
	

// ############################################################################
// ######################   		buttonBar		############################
// ###########################################################################

// 	$( ".ia_menubar" ).menu().hide();
// 	$( ".ia_menubar_button" ).live("click",function(e){
// 		$(e.target).next().slideToggle();	
// 	})
	
			
	$(".ia_framecontainer").sortable({handle: ".ia_frame_header"});//.disableSelection();
	

	
	for (var frameid in this.frames){
		if (this.frames[frameid].jsClass in window) {
			var frame= new window[this.frames[frameid].jsClass]();
			bas_copyAttributes(this.frames[frameid], frame);
			this.frames[frameid] = frame;
			frame.OnLoad();
			frame.Specific();
			
		} else {
			if (this.frames[frameid].jsClass != '') {
				alert('The javascript <'+ this.frames[frameid].jsClass +'> class is undefined.');
			}
		}
	}
	
};

bas_frmx_form.prototype.loadDashboard= function(){
	if($(".group").hasClass("selected_dash_first")){
		var size_accodion = $("#accordion").children().size();
		var pos = $(".selected_dash_first").nextAll().size()+1;
		pos = (size_accodion - pos)%size_accodion;
		$(".selected_dash_first").toggleClass("selected_dash").toggleClass("selected_dash_first");//.toggleClass("selected_dash");
		$( "#accordion" ).accordion({
				heightStyle: "content",
				header: "> div > h3",
				active: pos
			}).sortable({
				axis: "y",
				handle: "h3"
			});
	}
	
};


 bas_frmx_form.prototype.sendAction= function(action, params, type){//frameid,action, params){
	var data=  new FormData();
// 	alert("sendAction");

	// what is type: undefined or ???. Like a class of destination or destination type of the action?
	if (type == undefined)data.append("action",action);//data = {"action": action};
	else data.append("SessionAction", action);//data={"SessionAction": action};
	
	var row_selected = select_item();
	if ( row_selected != undefined){
		data.append("selected", parseInt(row_selected));
		data.append("selected_ext", this.selectedItems());
// 		data['selected']= parseInt(row_selected);
	}
	for (var param in params){
		if (params[param] != undefined){
			if ((params[param].name == undefined)||(params[param].type != undefined)) data.append(param,params[param]);//data[param]= params[param];
			else data.append(params[param].name,params[param].value);  //  data[params[param].name]= params[param].value;
		}
	}
// 	alert("la accion a realizar es: "+action);

	data.append("XHR", 1);   // data['XHR']=1;
	data.append("sessionId", this.sessionId);   // data['sessionId']= this.sessionId;
// 	if (console) console.debug('ajax:', data);
	$.ajax({type:'POST'
		, dataType: "json"
		, contentType:false
		, data: data //TODO: include session data.
        , processData:false
        , cache:false
		, context: this
//		, success: this.recvActionResponse
//		, error: this.actionError
	}).done(this.recvActionResponse).fail(this.actionError);			
}; 

bas_frmx_form.prototype.recvActionResponse= function(data, textStatus, jqXHR){
	currentForm.executeJsCommand(data);
};

bas_frmx_form.prototype.executeJsCommand= function(data){
	if (data != null) switch(data.command){
	
	case "changeAttr":
		for (var i=0; i < data.attrs.length; i++){
			$(data.attrs[i].selector).attr(data.attrs[i].attr, data.attrs[i].value);
			//alert("selector:"+data.attrs[i].selector+" attr:"+data.attrs[i].attr+" value:"+data.attrs[i].value);
		}
		
	
	case "reload": 
		$(data.selector).html(data.content);
// 		this.frames[data.frameid].Customer();
		break;
		
	case "refreshDashboard":
		$("#accordion").accordion("destroy");
		$("#accordion").html(data.content);
		currentForm.loadDashboard();
		break;
		
	case "load":
		for (var content=0; content < data.contents.length; content++){
			if (data.contents[content].selector == ".ia_menuContainer")
				$(data.contents[content].selector).append(data.contents[content].content);
			else
				$(data.contents[content].selector).html(data.contents[content].content);

// 			alert("selector: "+data.contents[content].selector);
// 			alert("contenido:"+data.contents[content].content);
		}
		var currentFormClass= window[data.currentForm]; 
		currentForm= new currentFormClass();
		bas_copyAttributes(JSON.parse(data.currentFormAttributes), currentForm);
		currentForm.OnLoad();
		break;
		
	case "reloadList":
		currentForm.frames[data.frameid].Reload(data.data, data.selected, data.size, data.reset);
		break;
		
	case "dialog":
		//this.dialogs[data.id]= data;
// 		alert(data.content);
		
		$("body").append(data.content);
		var buttons={};
		for (var action=0; action < data.actions.length; action++){
// 			alert("caption: "+data.actions[action].caption +" id: "+data.id  +" action: "+data.actions[action].id );
			buttons[data.actions[action].caption]= 
				new Function("currentForm.dialogAction(\"" + data.id + "\",\"" + data.actions[action].id + "\");");
		}
		$("#" + data.id).dialog({
			"modal": true, "width": 400
			, "title": data.title
			, "id": data.id
			, "close": new Function("currentForm.dialogAction(\"" + data.id + "\",\"cancel\");")
			, "buttons": buttons
		}).data("dialog", data);
		
// 		$("input:focus").blur();
		
// 		alert($("#" + data.id).html());
		break;
		
	case "alert":
		alert(data.message);
		break;
		
	case "void":
		break;
		
// 	case "file":
// 		alert(data.contents);
// 		myWindow=window.open('','_blank','width=200,height=100',false);
// // 		myWindow.document.write(data.contents);
// // 		window.open();
// 		break;
		
	case "download":
		submitaction(data.action);
		break;
	
	case "compound":
		for (var command=0; command < data.commands.length; command++){
			currentForm.executeJsCommand(data.commands[command]);
		}
		break;
	}	
};


bas_frmx_form.prototype.actionError= function(error, textStatus, jqXHR){
	switch(textStatus){
	case "parsererror":
		alert("Parser Error: " + error.responseText);
// 		$("body").append(error.responseText);
		break;
		
	default:
		alert(textStatus);
	}
	
};

bas_frmx_form.prototype.dialogAction= function(dialogId, action, actionParams){
	
// 	var params= $("#"+dialogId+" :input").serializeArray();  // ### TODO: Posible problema con los tipos checkbox
	var params={};
	var a=2;
	params["dialog"]= dialogId;
	var $inputs = $('form[name="form_'+dialogId+'"] :input');//.filter('input[type="text"]');
		$inputs.each(function() {
			switch (this.type){
				case "checkbox":
	// 				alert(this.checked);
					params[this.name] = this.checked;
					break;
				case "file":
					params[this.name] = this.files[0];
					break;
				case "select-one":
					params[this.name] = this.value;
				break;
				case "textarea":
				case "password":
				case "text":
					params[this.name] = this.value;
					break;			
			}
		});
	for (var param in actionParams) params[param]= actionParams[param];
	
// 	$("body").delay(81100);

	this.sendAction(action, params);	
	$("#" + dialogId).dialog("destroy");
	$("#dlg_" + dialogId).remove();

};

bas_frmx_form.prototype.selectedItems= function(){
	var selectedRows= $(".ia_selected_box");
	var ret= ""; var sep= "";
	for (var sel= 0; sel < selectedRows.length; sel++){
		var frames= $(selectedRows[sel]).parents(".ia_frame");
		ret+= sep + frames[0].id + ":" + selectedRows[sel].id;
		sep= ",";
	}
	return ret;
};

bas_frmx_form.prototype.sendEclive= function(){
	$("#eclive_cups").attr("value",currentForm.frames["cups"].getSelectedField("cups"));
	$("#eclive_form").submit();
};

// -------------------------------------------------

function select_item(){  
  var row_selected = document.getElementsByClassName("ia_selected_box");
  //¿Que frame? se busca en sus padres .ia_frame y se toma su id
  //alert(row_selected);
    if (row_selected.length != 0){    
		return row_selected[0].id;
// 		addhidden("selected",row_selected[0].id);//row_selected.id);
   // alert(row_selected[0].id);
  }
  else
	  return undefined;
  
};


function frameAction(action,form,lookup){
	

	var values = {};
	if (form != "ALLFRAME"){
		var $inputs = $('form[name="form_'+form+'"] :input');//.filter('input[type="text"]');
		if (lookup != undefined) values["lookup"] = lookup;
		$inputs.each(function() {
			switch (this.type){
				case "checkbox":
	// 				alert(this.checked);
					values[this.name] = this.checked;
					break;
				case "file":
					values[this.name] = this.files[0];
					break;
				case "select-one":
					values[this.name] = this.value;
				break;
				case "textarea":
				case "password":
				case "text":
					values[this.name] = this.value;
					break;			
			}
		});
	}
	else{ // debemos recorrer todos los formularios con cla clase ia_Form
		var $forms = $(".ia_Form");
		$forms.each(function(){
			for (var elem=0;elem<this.length;elem++){
				switch (this[elem].type){
					case "checkbox":
		// 				alert(this.checked);
						values[this[elem].name] = this[elem].checked;
						break;
					case "file":
						values[this[elem].name] = this[elem].files[0];
						break;
					case "select-one":
						values[this[elem].name] = this[elem].value;
					break;
					case "password":
					case "text":
						values[this[elem].name] = this[elem].value;
						break;			
				}
			}

		});
		
	}
// 		if (this.type == "file"){	values.append([this.name],this.files[0]);		}
// 		else	{				values.append([this.name],this.value);		}
	currentForm.sendAction(action,values); 
}

function ajaxaction(action,params){ 
//  	alert($('form[name="form_ficha_incidencia"] > input[type="text"]').size());
// 	alert($('form[name="form_ficha_incidencia"] :input').filter('input[type="text"]').size());
	currentForm.sendAction(action,params); 
};
	
	
// function submitlookup(node,lookupForm){
// 	
// 	var idForm= $(node).parents(".ia_cardframe").attr("id");
// 	if ($(".ia_filterbox").length == 0){
// 		frameAction("lookup",idForm,lookupForm);
// 		if (console != undefined) console.log('lookup.NO.dialog');
// 		
// 	} else {
// 		var dialogId = $(".ia_filterbox").attr("id");
// 		currentForm.dialogAction(dialogId, 'lookup', {lookup: lookupForm});
// 		if (console != undefined) console.log('lookup.dialog');
// 	}
// 	
// };


function submitlookup(idForm,lookupForm){
	
	if ($(".ia_filterbox").length == 0){
		frameAction("lookup",idForm,lookupForm);
		if (console != undefined) console.log('lookup.NO.dialog');
		
	} else {
		var dialogId = $(".ia_filterbox").attr("id");
		currentForm.dialogAction(dialogId, 'lookup', {lookup: lookupForm});
		if (console != undefined) console.log('lookup.dialog');
	}
	
};



function addhidden(hidname, hidvalue, hidform){
	var newinput = document.createElement("input");
	
	newinput.setAttribute("type", "hidden");
	newinput.setAttribute("name", hidname);
	newinput.setAttribute("value", hidvalue);
	if (hidform == undefined)	document.forms["form_"+hidvalue].appendChild(newinput);
	else	document.forms["form_"+hidform].appendChild(newinput);
// 	$("#form_"+hidvalue).appendChild(newinput);
};

function submitaction(action){
	addhidden('action', action);
	var item = select_item();
	if (item != undefined) addhidden('selected', item,action);
// 	$("#form_"+action).submit();
	document.forms["form_"+action].submit();
	
};

