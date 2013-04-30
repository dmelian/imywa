function bas_frmx_markdownframe(form, id){
	
	if (arguments.length == 0) return; // to be able to inherit from this


	bas_frmx_htmlframe.apply(this, arguments);
}

bas_frmx_markdownframe.prototype= new bas_frmx_htmlframe();
bas_frmx_markdownframe.prototype.constructor= bas_frmx_markdownframe;
