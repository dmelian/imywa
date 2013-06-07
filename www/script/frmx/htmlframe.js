function bas_frmx_htmlframe(form, id){
	
	if (arguments.length == 0) return; // to be able to inherit from this


	bas_frmx_frame.apply(this, arguments);
}

bas_frmx_htmlframe.prototype= new bas_frmx_frame();
bas_frmx_htmlframe.prototype.constructor= bas_frmx_htmlframe; 
