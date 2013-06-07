<?php

class bas_html_filterBox{
	public $id;
	public $title;
	public $content;
	protected $query;
	protected $actions;
	
	public function __construct(&$qry, $id, $actions='guardar'){
			$this->id= 'filterbox';
			$this->title=$id; 
// 			$this->message= $texts;	
			$this->query = $qry;
			$this->actions= new bas_sysx_actions($actions);
	}
	
	private function stamp(){

		$ret= "<div class=\"ia_dialogbox\" id=\"dlg_{$this->id}\">"
			. "<form class=\"ia_filterbox\" name =\"form_{$this->id}\" id=\"{$this->id}\" method= \"post\" enctype=\"multipart/form-data\">"
			. '<table style="width: 100%;">';
		$fields = $this->query->getAllfield();
		ob_start();
		global $_LOG;
		foreach($fields as $id => $field){
			$_LOG->log("#### filter:stamp. el filtro actual de $id es: ".$this->query->getfilter($id));
			$field->Show($this->query->getfilter($id));
			$input = ob_get_contents();	
			ob_clean();
			$ret.= '<tr><td>'
				. $input
				. "</td></tr>"; //modificado inma
		}
		ob_end_clean();
		
		$ret.= '</table></form></div>';
		
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