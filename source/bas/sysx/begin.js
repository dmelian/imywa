function bas_sysx_begin(){
	//this.dialogs= {};
}

bas_sysx_begin.prototype= new bas_frmx_form();
bas_sysx_begin.prototype.constructor= bas_sysx_begin; 


bas_sysx_begin.prototype.OnLoad= function(){

	this.sendAction('start');

};

/*
bas_sysx_begin.prototype.dialogAction= function(dialog, action){
	
	var params= $("#"+dialog).serializeArray();
	params["dialog"]= dialog;
	this.sendAction(action, params);
	$("#" + dialog).dialog("destroy");
	$("#dlg_" + dialog).remove();

};


bas_sysx_begin.prototype.sendAction= function(action, params){
	var data={"action": action};
	for (var param in params){
		if (params[param].name == undefined) data[param]= params[param];
		else data[params[param].name]= params[param].value;
	}
	$.ajax({"type":'POST'
		, "data": data //TODO: include session data.
		, "dataType": "json"
		, "context": this
		, "success": this.recvActionResponse
		, "error": this.actionError
	});
};

bas_sysx_begin.prototype.recvActionResponse= function(data, textStatus, jqXHR){
	switch(data.command){
	
	case "reload": 
		$(data.selector).html(data.content);
		break;
		
	case "load":
		for (var content=0; content < data.contents.length; content++){
			$(data.contents[content].selector).html(data.contents[content].content);
		}
		var currentFormClass= window[data.currentForm]; 
		currentForm= new currentFormClass();
		bas_copyAttributes(JSON.parse(data.currentFormAttributes), currentForm);
		currentForm.OnLoad();
		break;
		
	case "dialog":
		//this.dialogs[data.id]= data;
		$("body").append(data.content);
		var buttons={};
		for (var action=0; action < data.actions.length; action++){
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
		break;
		
	case "alert":
		alert(data.message);
		break;
		
	case "submit":
		break;
	}
};

bas_sysx_begin.prototype.actionError= function(error, textStatus, jqXHR){
	switch(textStatus){
	case "parsererror":
		alert("Parser Error: " + error.responseText);
		break;
		
	default:
		alert(textStatus);
	}
	
};
*/