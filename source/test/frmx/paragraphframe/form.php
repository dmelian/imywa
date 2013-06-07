<?php

class frmx_paragraphframe_form extends bas_frmx_form{
	
	public function OnLoad(){
		parent::OnLoad();
		$this->toolbar= new bas_frmx_toolbar('pdf,csv,close');
		$this->title= 'Paragraph Frame Test';
		$this->addFrame(new frmx_paragraphframe_form_frame1('frame1','Frame 1'));
		$this->addFrame(new frmx_paragraphframe_form_frame1('frame1b','Frame 1 Bis'));
	}
	
}

?>