<?php

class bas_html_messageBox{
	public $id;
	public $title;
	public $message;
	protected $actions;
	
	public function __construct($module, $id, $texts=false, $actions='ok'){
		if ($module !== false){
			if (is_object($module)) $module= get_class($module);
			$this->id= $id;
			$this->title= T($module, $id, $texts);
			$this->message= TD();
			$this->actions= new bas_sysx_actions($actions, $module);
			
		} else {
			$this->id= 'messagebox';
			$this->title=$id; 
			$this->message= $texts;	
			$this->actions= new bas_sysx_actions($actions);
		}
	}
	
	private function stamp(){
		$ret= "<div class=\"ia_messagebox\" id=\"dlg_{$this->id}\"><p id=\"{$this->id}\">";
		$ret.= "{$this->message}</p></div>";
		return $ret;
	}
	
	public function jscommand(){
		return '{"command": "dialog", "content":"'
			. addcslashes($this->stamp(),'"\\/') .'"'
			. ',"actions":'. $this->actions->json()
			. ',' . substr(json_encode($this),1);
	}
}
?>
