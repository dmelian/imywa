 <?php
// 
// class frmx_listframe_form extends bas_frmx_form{
// 	
// 	public function OnLoad(){
// 		parent::OnLoad();
// 		$this->toolbar= new bas_frmx_toolbar('pdf,csv,close');
// 		$this->title= 'List Frame Test';
// 		$this->addFrame(new test_frmx_listframe_form_frame1('frame1','Test List Frame 1'));
// 	}
// 	
// }




class test_frmx_listframe_form extends bas_frmx_form {
	private $list;
	public function OnLoad(){
		parent::OnLoad();
		
		$this->title = 'Lista';
		
		$this->toolbar = new bas_frmx_toolbar('pdf,csv,close');
		$this->buttonbar= new bas_frmx_buttonbar();
		$this->buttonbar->addAction('nuevo');
		$this->buttonbar->addAction('editar');
		$this->buttonbar->addAction('borrar');

		$this->buttonbar->addAction('salir');

		$list = new bas_frmx_listframe('frame1','Test List Frame 1');
				
		$width= 100;
		$height = 1;

		$list->query->add("prueba_record",'test');
		$list->query->setkey(array('N_Factura','N_Linea'));
			
		$list->query->addcol("N_Factura", "Numero de Factura","prueba_record" ,true,'test');	
		$list->query->addcol("N_Linea", "Numero de linea","prueba_record" ,true,'test');	
		$list->query->addcol("id_Cliente", "Cliente","prueba_record" ,false,'test');
		$list->query->addcol("cantidad", "cantidad","prueba_record" ,false,'test');
		
	//	$this->query->addcol($id, $caption='', $table='',$pk='',$aliasof='',$type='');
		$list->query->db = "test";
		$list->query->order= array("id_Cliente"=>"asc", "cantidad"=>"desc");
		/*$this->query->key= array("N_Factura"=>"asc", "N_Linea"=>"desc");
		$this->query->selectStr = " * ";
		$this->query->fromStr = " prueba_record ";
		$this->query->whereStr = " N_Factura > 1 ";*/
		
		$list->query->addcondition("prueba_record.N_Factura > 1");	
		
		// ### Codigo de prueba
// 		$union = new bas_sqlx_unionquery($list->query);
// 		$union->addQuery($list->query);
// 		$list->query = $union;
		
		// ### Codigo de prueba
		
		$list->setFixed(2);
		$list->setFooter(15);
	
	// field type:   ($id,$type,$name, $caption, $editable, $value,$visible)
	
		$list->addComponent($width, $height,"N_Factura");
		$list->addComponent($width, $height,"N_Linea");
		$list->addComponent($width, $height,"cantidad");
		$list->addComponent($width, $height,"id_Cliente");
		
		$list->query->setGroup("N_Factura");
		$list->query->setGroup("N_Linea");
		
		$list->setRecord();	
		
		$this->addFrame($list);
		$this->addFrame(new test_frmx_listframe_form_medias('frame21','Testing List Frame'));
		
		
		$this->list = $list;
	}
	
	
	public function OnAction($action, $data){
		global $_LOG;
		if ($ret = parent::OnAction($action, $data)) return $ret;
		if (isset($data['selected'])){
			$this->list->setSelected($data['selected']);	
		}
		$_LOG->debug("DAtos enviados:",$data);
		
		if (isset($data['selected_ext'])){
		$_LOG->debug("Decodee enviados:",json_decode($data["selected_ext"]));
		}
		

// 		$this->list->setSelected(3);	
		switch($action){
				case 'salir': return array('close');
				
				case 'nuevo': 
					$save[] =  array('id'=> "save", 'type'=>'command', 'caption'=>"guardar", 'description'=>"guardar");

					$save[] =  array('id'=> "cancel", 'type'=>'command', 'caption'=>"cancelar", 'description'=>"Cancelar");
					
					$login= new bas_html_filterBox($this->frames["frame1"]->query, "Filtros",$save);
					echo $login->jscommand();
// 						 return array('close');
					break;
				case "save":
					foreach($data as $id => $value){
						$_LOG->log("###############   elemento actual id: $id  valor: $value");
						$this->frames["frame1"]->query->setfilter($value,$id);					
					}
					echo '{"command": "void",'. substr(json_encode($this),1);
					break;
				case 'cancel':
					echo '{"command": "void",'. substr(json_encode($this),1);
					break;
					
				case 'observaciones':
					if (isset($data['selected'])) {
						return array('open', 'document_observacionlista', 'seek', $this->list->getSelected());
						
					} else return array('open', 'bas_dlg_msgbox', 'warning', 'Seleccione la incidencia.');
					
				case 'borrar':
					if (isset($data['selected'])) {
						$sel = $this->list->query->getautokeyfilter();
						$proc = new bas_sql_myprocedure('incidencia_delete', array($sel['incidencia']));
						if (! $proc->success) { return array('open', 'bas_dlg_msgbox', 'error', $proc->errormsg); }
					} else return array('open', 'bas_dlg_msgbox', 'warning', 'Seleccione la incidencia.'); 
					break;
					
				case 'terminar':
					if (isset($data['selected'])) {
						$sel = $this->list->query->getautokeyfilter();
						$proc = new bas_sql_myprocedure('incidencia_terminar', array($sel['incidencia']));
						if (! $proc->success) { return array('open', 'bas_dlg_msgbox', 'error', $proc->errormsg); }
					} else return array('open', 'bas_dlg_msgbox', 'warning', 'Seleccione la incidencia.'); 
					break;
					
				case 'seek':
					// Este formulario se va a ver como detalle de documentos. 
					// Así que en el seek se requiere un tipo de referencia y una referencia para ponerla como filtro obligado.
					// Lo mismo con nuevo o editar.
					if (!isset($data['tiporef']) || $data['tiporef'] == 'general') {
						$data['tiporef'] = 'general'; $data['referencia']='*';
					}
					if (isset($data['tiporef']) && isset($data['referencia'])){
						$this->list->query->setfilter($data['tiporef'], 'tiporef');
						$this->list->query->setfilter($data['referencia'], 'referencia');
					}
		}
	}
// 	
}
?>
