<?php
/*
	Copyright 2009-2012 Domingo Melian

	This file is part of imywa.

	imywa is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	imywa is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with imywa.  If not, see <http://www.gnu.org/licenses/>.
*/
class bas_frmx_form extends bas_html_page{
	public $frames= array();
	public $toolbar;
	public $buttonbar;
	
	public $sessionno;
	public $installationid;
	public $sequenceno;
	
	public $sessionId;
	
	protected $logo;
	
	public function OnLoad($permission=array('permission'=>'allow')){
		global $_SESSION;
		
		$this->jsClass= 'bas_frmx_form';
//		$this->addStyle("style/frmx/form.css");
//		$this->addStyle("style/frmx/frame.css");

		$defstyle=  isset($_SESSION->theme) ? $_SESSION->theme : 'amedita'; 
		if (!isset($this->theme)) $this->setTheme($defstyle);
		$this->addThemeStyle();
		
//		$this->addScript("script/frmx/frmx.js");
		$this->addScript("script/frmx/lib.js");
		$this->addScript("script/frmx/form.js");
		$this->addScript("script/frmx/frame.js");
		$this->addScript("script/frmx/htmlframe.js");
		$this->addScript("script/frmx/paragraphframe.js");
		$this->addScript("script/frmx/markdownframe.js");
		$this->addScript("script/frmx/cardframe.js");
		$this->addScript("script/frmx/listframe.js");
		$this->addScript("script/frmx/breadcrumbs.js");
		$this->addScript("script/frmx/menuframe.js");
		$this->addjqueryui();
		
		
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
	
	function getContents(){
		global $_LOG;
		$ret= array();
		$re = array();
		if (isset($this->logo)){
			//$ret ('.ia_logo')
			$ret['.ia_logo'] =  "<img id='ia_img_logo' src='{$this->logo}' height='120px' width='300px' border='0' >";
		}
		else $ret['.ia_logo'] =  "";

		
		ob_start();
		$ret['.ia_breadcrumb']= getBreadCrumbStamp($this);
		if (isset($this->toolbar)){
			$ret['.ia_toolbar']= $this->toolbar->stamp();
			$ret['.ia_menuContainer'] = $this->toolbar->OnPaintMenu();
		}
		else $ret['.ia_toolbar']= "";
// 		$frames='';
		
		foreach($this->frames as $frame) $frame->OnPaint($this);
		$ret['.ia_framecontainer']= ob_get_contents();
		if (isset($this->buttonbar)){
			ob_clean();
			$this->buttonbar->OnPaint();
			$ret['.ia_buttonbar']= ob_get_contents();
			ob_clean();
			if (isset($ret['.ia_menuContainer']))	{$ret['.ia_menuContainer'] .= $this->buttonbar->OnPaintMenu();
				$_LOG-log("ya existe el menuContainer");
			}
			else $ret['.ia_menuContainer'] = $this->buttonbar->OnPaintMenu();
		}
		else $ret['.ia_buttonbar']= "";
		
		$ret[".ia_statusbar"] = "";
		
		if (method_exists($this, 'myscript')) {
			ob_clean();
			$this->myscript();
			$ret['#myscript']= ob_get_contents();
		}
		ob_end_clean();
		return $ret;
		
	}	
	
	function OnPaint($mode= 'html', $dash=NULL){
		
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
