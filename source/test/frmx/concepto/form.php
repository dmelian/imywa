<?php

class test_frmx_concepto_form extends bas_frmx_form{
	
	public function OnLoad(){
		parent::OnLoad();
		$this->toolbar= new bas_frmx_toolbar('pdf,csv,close');
		$this->title= 'List Frame Test';
		$this->addFrame(new test_frmx_concepto_form_frame2('frame1','Testing List Frame'));
		
		$this->buttonbar= new bas_frmx_buttonbar();
		$this->buttonbar->addAction('filtro');
		$this->buttonbar->addAction('salir');

		//$this->addFrame(new frmx_cardframe_form_frame1('frame1','Testing Card Frame 2-0'));
	//	$this->addFrame(new frmx_cardframe_form_frame2('frame2','Testing Card Frame 2'));
	/*	$this->addFrame(new frmx_cardframe_form_frame1('frame1','Testing Card Frame 1-2'));
		$this->addFrame(new frmx_cardframe_form_frame1('frame1','Testing Card Frame 1-3'));
		$this->addFrame(new frmx_cardframe_form_frame3('frame3','Testing Card Frame 3-0'));
		$this->addFrame(new frmx_cardframe_form_frame4('frame4','Testing Card Frame 4-0'));*/
	}
	
	public function OnAction($action, $data){
		global $_LOG;
		if ($ret = parent::OnAction($action, $data)) return $ret;
		if (isset($data['selected'])){
			$this->list->setSelected($data['selected']);	
		}
// 		$this->list->setSelected(3);	
		switch($action){
				case 'salir': return array('close');
				
				case 'filtro': 
					$save[] =  array('id'=> "save", 'type'=>'command', 'caption'=>"guardar", 'description'=>"guardar");

					$save[] =  array('id'=> "cancel", 'type'=>'command', 'caption'=>"cancelar", 'description'=>"Cancelar");
					
					$login= new bas_html_filterBox($this->frames["frame1"]->query, "Filtros",$save);
					echo $login->jscommand();
// 						 return array('close');
					break;
				case 'lookup': //El usuario ha picado en el botón de lookup de alguno de los campos.
				//$this->card->savedata($data);  //### Completar.
				//return (array('open',"${data['lookup']}lookup", 'seek', array()));//$this->data['comercializadora']));
				return (array('open','test_frmx_cardframe_form','edit',array()));
				break;
				
				case 'cancel':
					echo '{"command": "void",'. substr(json_encode($this),1);
					break;
		}
	}
	
}

?>