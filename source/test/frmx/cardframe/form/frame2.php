<?php
class test_frmx_cardframe_form_frame2 extends bas_frmx_cardframe{
	
    public function __construct($id, $tabs='', $grid=array('width'=>4,'height'=>5)) {
		parent::__construct($id,$tabs,$grid,$this->query);
		$this->query = new bas_sqlx_querydef();
		$this->query->add("prueba_record",'test');
		$this->query->setkey(array('N_Factura','N_Linea'));
			
		$this->query->addcol("N_Factura", "Numero de Factura","prueba_record" ,true,'test');	
		$this->query->addcol("N_Linea", "Numero de linea","prueba_record" ,true,'test');	
		$this->query->addcol("id_Cliente", "Cliente","prueba_record" ,false,'test');
		$this->query->addcol("cantidad", "cantidad","prueba_record" ,false,'test');
		
	//	$this->query->addcol($id, $caption='', $table='',$pk='',$aliasof='',$type='');
		
		$this->query->order= array("id_Cliente"=>"asc", "cantidad"=>"desc");
		
		$this->query->addcondition("prueba_record.N_Factura > 1");
		
		
		
		//addComponent($tab, $x, $y, $width, $height, $field_id);
		$x=$y=$width= $height = 1;
		
		$this->tabs = array("Primera solapa","Segunda solapa","Tercera","cuarta");
		
		
		$this->addComponent("Primera solapa", 1, 1, 2, 1, "N_Factura");
		$this->addComponent("Primera solapa", 3, 1, 2, 1, "N_Linea");
		$this->addComponent("Primera solapa", 1, 2, 2, 1,"cantidad");
		$this->addComponent("Primera solapa", 3, 2, 2, 1,"id_Cliente");
		$this->setRecord();
		/*$this->addComponent("Primera solapa", 1, 1, 1, 1, new bas_sqlx_fieldtext("N_Factura","text","fact","Numero de Factura",false,"val",true));
		$this->addComponent("Primera solapa", 2, 1, 3, 1, new bas_sqlx_fieldtext("N_Linea","text","fact","Numero de linea",false,"val",true));
		$this->addComponent("Primera solapa", 1, 2, 2, 2,new bas_sqlx_fieldtext("cantidad","text","fact","cantidad",true,"val",true));
		$this->addComponent("Primera solapa", 3, 2, 1, 1,new bas_sqlx_fieldtext("id_Cliente","text","fact","Cliente ",true,"val",true));
*/
    }
}