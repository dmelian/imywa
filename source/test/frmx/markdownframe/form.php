<?php
class test_frmx_markdownframe_form extends bas_frmx_form{

	public function OnLoad(){
		parent::OnLoad();
		$this->toolbar= new bas_frmx_toolbar('pdf,csv,close');
		$this->title= 'MarkDown Frame Test';
		$this->addFrame(new test_frmx_markdownframe_form_frame1('frame1','Frame 1'));
		$this->addFrame(new test_frmx_markdownframe_form_frame2('frame2','Frame 2'));
		$this->addFrame(new test_frmx_markdownframe_form_frame3('frame3','Frame 3'));
		$this->addFrame(new test_frmx_markdownframe_form_frame2('frame2bis','Frame 2(Copia)'));
	}
	
}
?>