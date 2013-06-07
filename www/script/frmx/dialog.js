
function bas_frmx_dialog(){}


bas_frmx_dialog.prototype.OnLoad= function(){

	var buttons={};
	for (var i=0; i < this.actions.length; i++){
		buttons[this.actions[i]]= 
			function() { $("#login_form").submit();	};
	}
	
	
	$(".ia_dialog").dialog({resizable: this.resizable
		,width: this.width
		,modal: this.modal
		,title: this.title
		,buttons: buttons
	});
	
	
};