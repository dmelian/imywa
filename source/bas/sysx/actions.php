<?php

/*
	ACTION PROPERTIES.
		type. (action, command, procedurecall, menu)
		caption.
		description.
	
	ACTIONS TYPES.
	action - A submit action recharge all the current form.
	command - An ajax command that request some data from the server.
*/		


class bas_sysx_actions implements Iterator{
	public $actions;

	private $pos;
	
	public function __construct($actions, $module=''){
		$this->pos= 0;
		if (is_array($actions)) $this->actions=$actions; 
		else { 
			$this->actions= array(); 
			foreach(explode(',', $actions) as $actionId) {
				$actionId= trim($actionId);
				$caption= T(get_class(), $actionId);
				if (substr($caption,0,5) == 'TEXT[') $caption= T($module, $actionId);
				$this->actions[]= array('id'=> $actionId, 'type'=>'command', 'caption'=>$caption, 'description'=>TD());
			}
		}
	}

	
	// Iterator Interface
	public function current()	{ return $this->actions[$this->pos]; }
	public function key()		{ return $this->pos; }
	public function next()		{ $this->pos++; }
	public function rewind()	{ $this->pos= 0; }
	public function valid()		{ return isset($this->actions[$this->pos]); }		
	
	public function json(){
		$ret= '['; $sep= '';
		foreach($this->actions as $action) {
			$ret.= $sep . json_encode($action);
			$sep=',';
		}
		return "$ret]";
	}
}

?>