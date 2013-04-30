<?php

class test_frmx_cardframe_form extends bas_frmx_form{
	
	public function OnLoad(){
		parent::OnLoad();
		$this->toolbar= new bas_frmx_toolbar('pdf,csv,config,close');
		$this->title= 'Card Frame Test';
		$this->loadFrame('test_frmx_cardframe_form_frame1');
		$this->loadFrame('test_frmx_cardframe_form_frame2');
		//$this->addFrame(new test_frmx_cardframe_form_frame1('frame1','Testing Card Frame 1'));
		//$this->addFrame(new test_frmx_cardframe_form_frame1('frame1','Testing Card Frame 2-0'));
		//$this->addFrame(new test_frmx_cardframe_form_frame2('frame2','Testing Card Frame 2'));
	/*	$this->addFrame(new test_frmx_cardframe_form_frame1('frame1','Testing Card Frame 1-2'));
		$this->addFrame(new test_frmx_cardframe_form_frame1('frame1','Testing Card Frame 1-3'));
		$this->addFrame(new test_frmx_cardframe_form_frame3('frame3','Testing Card Frame 3-0'));
		$this->addFrame(new test_frmx_cardframe_form_frame4('frame4','Testing Card Frame 4-0'));*/
		$this->buttonbar = new bas_frmx_buttonbar();
		$this->buttonbar->addAction("aceptar");
		$this->buttonbar->addAction("eliminar");
		$this->buttonbar->addAction("nuevo");
		$this->buttonbar->addAction("salir");
		
		$menu = new bas_frmx_menubox("prueba");
		$menu->addElement("primero");
		$menu->addElement("segundo");
		$menu->addElement("tercero");
		$aux = new bas_frmx_menubox("sub");
		$aux->addElement("primero");
		$aux->addElement("segundo");
		$aux->addElement("tercero");
		
		$menu->addElement("cuarto","",$aux);
		
		$this->buttonbar->addMenu("prueba",$menu);
// 		$this->buttonbar->addMenu("eliminar",array("primero","segundo","tercero"));
// 		$this->buttonbar->addMenu("eliminar > primero",array("A","B","C"));
	}
	
	public function OnAction($action, $data=""){
		parent::OnAction($action,$data);
		switch($action){
			case 'salir': case 'close': return array('close');
			case 'edit':
// 					echo '{"command": "void",'. substr(json_encode($this),1);
			break;
		}
	}
	
}

?>
