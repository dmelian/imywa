function bas_frmx_menuframe(form, id){
	
	if (arguments.length == 0) return; // to be able to inherit from this


	bas_frmx_frame.apply(this, arguments);
}

bas_frmx_menuframe.prototype= new bas_frmx_frame();
bas_frmx_menuframe.prototype.constructor= bas_frmx_menuframe;


bas_frmx_menuframe.prototype.OnLoad= function(){

      bas_frmx_frame.prototype.OnLoad.call(this);
      $( ".ia_menuframe" ).menu();
		
};