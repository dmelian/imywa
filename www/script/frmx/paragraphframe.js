function bas_frmx_paragraphframe(form, div, theme){
	
	if (arguments.length == 0) return; // to be able to inherit from this


	bas_frmx_frame.apply(this, arguments);
}

bas_frmx_paragraphframe.prototype= new bas_frmx_frame();
bas_frmx_paragraphframe.prototype.constructor= bas_frmx_paragraphframe;

