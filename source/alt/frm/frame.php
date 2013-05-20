<?php

class alt_frm_frame{
	public $id;
	public $title;
	public $jsClass= 'bas_frmx_frame';
	public $permission;
	public $actions=array();
	public $paintWrapper= true;
	
	public function __construct($id,$title=""){ 
		$this->id= $id;
		if ($title == "")	$this->title= $id; 
		else $this->title= $title;
	}
	
	public function OnLoad($permission= array('permission'=>'allow')){
		$this->permission= $permission;
		$this->checkActionsPermissions();
	}
	
	public function checkActionsPermissions(){
		switch ($this->permission['permission']){
			case 'except':
			case 'only':			
		}
	}
	
	public function OnPaint($page){
		$page->addDiv('ia_frame', $this->id);
		$page->addDiv('ia_frame_header'); echo $this->title;
		$page->nextDiv('ia_frame_content');
		$this->OnPaintContent($page);
		$page->closeDiv('ia_frame');
	}
}

?>
