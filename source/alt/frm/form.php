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
	
	public $sessionno;
	public $installationid;
	public $sequenceno;
	
	public $sessionId;
	
	protected $logo;
	
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
	
	function OnCommand($command, $data){
	
	}
	
	function OnHeader(){ }
	function OnFooter(){}
	
	function OnPaint($media){
		$media= ('screen' => with events, 'paper')
	}
	
	function getContents($paper){
		
		// Global settings		
		$paper->setLogo($this->logo); //This must be included in ha header section.
		
		$paper->setBreadCrumb(getBreadCrumbStamp($this));//This is an session job.
		$paper->setToolBar($this->toolbar);
		$paper->setButtonBar,statusBar($); // and all others global settings for the paper 

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
	
	function OnPaint($paper, $mode, $dash=NULL){
		
		$paper->newForm();
		
		switch($mode){
			case 'html': $this->OnHtml(); break;
			case 'jscommand': $this->jscommand($dash); break; 
		}
	}

	public function jscommand($dash){
		global $_LOG;
		echo '{"command":"load","contents":[';
		$sep='';
		foreach ($this->getContents() as $selector => $content){
			$content= json_encode($content);
			echo "$sep{\"selector\":\"$selector\",\"content\":$content}";
			$sep=',';
		} 
		
		if (isset($dash)) echo "$sep{\"selector\":\".ia_dashboardcontainer\",\"content\":\"$dash\"}";
		echo ']';
		echo ",\"currentForm\":\"{$this->jsClass}\"";
		echo ',"currentFormAttributes":"'. addcslashes(json_encode($this),'"\\/') .'"';
		echo '}';
	}
	
	
	public function OnHtml(){
		global $_SESSION;
		global $_LOG;
		$this->beginHtml();

		$this->addDiv('ia_form');
		$this->addDiv('ia_logo');
		
		$this->nextDiv('ia_formheader'); 
		echo $this->OnHeader();
		//$this->addDiv('ia_logo');
		$this->addDiv('ia_breadcrumb'); echo getBreadCrumbStamp($this);
		
		$this->nextDiv('ia_toolbar'); 
		if(isset($this->toolbar))echo $this->toolbar->stamp();
		  
		$this->closeDiv('ia_formheader');
		
		$this->addDiv('ia_formcontent');
		$this->addDiv('ia_dashboardcontainer'); 
		$this->nextDiv('ia_framecontainer');		
		foreach($this->frames as $frame) $frame->OnPaint($this);
		$this->closeDiv('ia_formcontent');
		
		$this->addDiv('ia_formfooter');
		echo $this->OnFooter();
		$this->addDiv('ia_statusbar');
		$this->nextDiv('ia_buttonbar');
	    if(isset($this->buttonbar))$this->buttonbar->OnPaint();
		
		$this->closeDiv('ia_formfooter');
		
		echo "<div style=\"position:absolute;\" class=\"ia_menuContainer\" >";
		echo "</div>";

		$this->endHtml();
	}
	
	function OnPdf(){
		$pdf= new bas_pdf_form();
		$pdf->beginDoc();
// 		global $_LOG;
// 		$_LOG->log("OnPdf::lista de frames del formulario ". count($this->frames));
		foreach($this->frames as $frame) $frame->OnPdf($pdf);
		$pdf->endparagraph();
		$pdf->endDoc();
	}
	
	function OnCsv(){
		//$form= new_csv_form();
		$csv = new bas_csv_form('documentocsv.csv');
		$csv->open();
		foreach($this->frames as $frame) $frame->Oncsv($csv);
		$csv->close();
	}
	
	function OnAction($action, $data){
		global $_LOG;
		
		if ($action == 'xhrcommand'){
			
			if (isset($data['frameid'])) {
				if (isset($this->frames[$data['frameid']])){
					$this->frames[$data['frameid']]->OnCommand($data['command'], $data);
				} else {/* LOG. No se encuentra el frame frameid*/}
				
			} else $this->OnCommand($data['command'], $data);
			
		} else switch($action){
			case "changeApp":
				return array("changeApp",$data["app"]);
			case "close":
				$jump= isset($data['jump']) ? $data['jump'] : 1; 
				return array('close', $jump); 
				break; 
			case "pdf":
// 				$_LOG->log("OnAction:: lista de frames del formulario ". count($this->frames));
				$this->OnPdf(); 
				return array($action);
			break;
			case "csv": $this->OnCsv(); return array($action); break;
			case "ajax_previous": case "ajax_next": case "first": case "previous": case "next": case "last": 
				foreach($this->frames as $key=>$value){
					$this->frames[$key]->OnAction($action, $data);
				}
				$this->OnPaint("jscommand");
			break;			
			case "scroll_move":
				$ret= $this->frames[$data['frameid']]->OnAction($action, $data);
				if ($ret) return $ret; else break;
			
			 
		}
	}
		
}
?>
