<?php
/*
	ALT_FRM_FORM
	
	We want separate the html(css-js) functionality apart from the definition of the form.
	Now this class (copy of bas_frmx_form) doesn't extends from bas_html_page and doesn't have
		any html functionality.
		
*/
class alt_frm_form {
	public $frames= array();
	public $toolbar;
	public $buttonbar;
	
	public function OnLoad($permission=array('permission'=>'allow')){
		
//		$this->jsClass= 'bas_frmx_form'; // this is the js behavior. how to do it??
		
	}

	function loadFrame($frameName, $frameid=''){
		global $_SESSION;
		
		if (!$frameid) $frameid= 'frame'.(count($this->frames)+1);
		$permission= $_SESSION->apps[$_SESSION->currentApp]->classPermission($frameName); 		
		if ($permission['permission'] != 'deny'){
			$frame= new $frameName($frameid);
			$frame->id= $frameid;
			if (method_exists($frame, 'OnLoad')) $frame->OnLoad($permission);
			$this->addFrame($frame);
		}
	}

	function addFrame($frame){
		$this->frames[$frame->id]= $frame;
	}
	
	function getBreadCrumbCaption(){return $this->title;}
	
	function OnPaint($media){
		$media= array('screen' => 'with events', 'paper');
	}
	
	function getContents($paper){
		
		// Global settings		
		$paper->setLogo($this->logo); //This must be included in ha header section.
		
		$paper->setBreadCrumb(getBreadCrumbStamp($this));//This is an session job.
		$paper->setToolBar($this->toolbar);
		$paper->setButtonBar_statusBar($s); // and all others global settings for the paper 

		// Begin
		$paper->open();
		
		foreach($this->frames as $frame) {
			$paper->openFrame($frame->type, $frame->id);
			$frame->getContents($paper);
			$paper->closeFrame();
		}
		
		$paper->close();
		
		// The form events are executed by the session.
		// ¿ The data ? getCurrent getValue(id) next nextPage setPageLenght
	}	
	
	function OnPaint(){
	}

	function OnAction($action, $data){
	}
		
}
?>
