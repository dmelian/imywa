<?php

class bas_html_inputBox{
	public $id;
	public $title;
	protected $module;
	protected $actions;
	protected $contents;
	
	public function __construct($module, $id){
		$this->actions= new bas_sysx_actions('ok,cancel');
		if (is_object($module)) $module= get_class($module);
		$this->title= T($module, $id);
		$this->module= $module; $this->id= $id;
		$this->contents= array();
	}
	
	public function addText($id, $type='input'){
		$this->contents[]=array('type'=>$type, 'id'=>$id);
	}
	
	public function addPassword($id){ $this->addText($id, 'password'); }
	
	public function stamp(){
		$ret= "<div class=\"ia_dialogbox\" id=\"dlg_{$this->id}\">"
			. "<form class=\"ia_filterbox\" name =\"form_{$this->id}\" id=\"{$this->id}\" method= \"post\" enctype=\"multipart/form-data\">"
			. '<table>';
		
		foreach($this->contents as $content){
			$ret.= '<tr><td>'
				. T($this->module, $content['id'])
				. "</td><td><input class='focus' type=\"{$content['type']}\" name=\"{$content['id']}\"></td></tr>"; //modificado inma
			
		}
		
		$ret.= '</table></form></div>';
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
