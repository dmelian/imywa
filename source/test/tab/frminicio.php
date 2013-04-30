<?php
class frm_inicio{

	private $menu;
	private $toolbar=array('salir');
	
	public function __construct(){
		$this->menu = new bas_htm_menu();
		$this->menu->add('Tabs', 'tabs');
		$this->menu->add('Extensions', 'extension');
	}
	
	
	public function OnPaint(){
		$frm = new bas_htm_form('TEST - MENÚ PRINCIPAL',$this->toolbar);
		$frm->add($this->menu);
		$frm->printme();
	}
	
	public function OnAction($action, $data){
		switch ($action){
			case 'salir': return array('close');
			default: return array('open', "frm_$action");
		}
	}
}
?>