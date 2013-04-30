<?php
class frm_tabs{
	private $actions = array('salir');
	
	public function OnPaint(){
		$frm = new bas_htm_form("TESTEANDO",$this->actions);
		$tabs = new bas_htm_tabs();
		$tabs->addtab('tab01');
		$tabs->p('Contenido del tab uno');
		$tabs->addtab('tab01.5');
		$tabs->p('Contenido del tab uno punto cinco');
		$tabs->addtab('tab02');
		$tabs->p('Contenido del tab dos');
		$tabs->addtab('tab03');
		$tabs->p('Contenido del tab tres');
		$tabs->addtab('tab04');
		$tabs->p('Contenido del tab cuatro');
		$frm->add($tabs);
		$frm->printme();
	}

	public function OnAction($action, $data){
		switch($action) {
			case 'salir': return(array('close'));
		}
	}

	
}
?>