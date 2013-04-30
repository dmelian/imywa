<?php
class test_inicio extends bas_frmx_form{

	public function __construct(){
		$menu = new bas_frmx_menu("menu_principal");
		$menu->add('Prueba html-frame', 'frmx_htmlframe_form');
		$menu->add('Prueba paragraph-frame', 'frmx_paragraphframe_form');
		$menu->add('Prueba MarkDown-frame', 'frmx_markdownframe_form');
		$menu->add('Prueba Card-frame', 'frmx_cardframe_form');
		$menu->add('Prueba List-frame', 'frmx_listframe_form');
		$menu->add('Prueba Concepto','frmx_concepto_form');

		$submenu_otros= new bas_frmx_menu("menu_otros");
		$submenu_otros->add('Prueba pdf', 'pdf_prueba');
		$submenu_otros->add('Prueba Dialogo', 'frmx_dialog_dialog');
		$submenu_otros->add('Prueba csv','frmx_csv_prueba');
// 		$submenu_otros->add('Prueba Concepto','frmx_concepto_form');
		$menu->addmenu($submenu_otros, 'otros');
		$this->addFrame($menu);
	}
	
	
	public function OnAction($action, $data){
		switch ($action){
			case 'salir': return array('close');
			
			case 'noimp':
				return array('open', 'bas_dlg_msgbox', 'warning', 'Acción no implementada');	
				break;
				
			default: return array('open', "test_$action");
		}
	}
	
	public function getBreadCrumbCaption(){ return "MENU"; }
	
}
?>
