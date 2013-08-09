<?php

class bas_html_frameBox{
	public $id;
	public $title;
	public $content;
	protected $frame;
	protected $actions;
	
	public function __construct(&$frame, $id, $actions='guardar'){
			$this->id= 'frameBox';
			$this->title=$id; 
// 			$this->message= $texts;	
			$this->frame = $frame;
			$this->actions= new bas_sysx_actions($actions);
	}
	
	private function stamp(){

		$ret= "<div style=\"width:100%;height:100%;position: relative;\" class=\"ia_dialogbox\" id=\"dlg_{$this->id}\">"
			. "<form style=\"width:100%;height:100%;min-height: 900pt;position: relative;\" class=\"ia_filterbox\" name =\"form_{$this->id}\" id=\"{$this->id}\" method= \"post\" enctype=\"multipart/form-data\">"
			. '<div name="ia_frameBox" style=" position: absolute; left: 10px; right: 10px; bottom: 10px; top: 10px;">';
		ob_start();
			$this->frame->OnPaint();
			$input = ob_get_contents();	
		ob_end_clean();
		
		$ret.= $input.'</div></form></div>';
		
		$this->content = $ret;
		return $ret;
		
	}
	
	public function jscommand(){
		return '{"command": "dialog", "content":"'	. addcslashes($this->stamp(),'"\\/') .'"'
			. ',"actions":'. $this->actions->json()
			. ',' . substr(json_encode($this),1);
	}
}

?>