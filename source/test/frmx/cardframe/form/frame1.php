<?php
class test_frmx_cardframe_form_frame1 extends bas_frmx_cardframe{
    public function __construct($id, $tabs='', $grid=array('width'=>4,'height'=>4)) {
	parent::__construct($id,$tabs,$grid);
	/*$this->query = new bas_sqlx_querydef();
	$this->query->order= array("id_Cliente"=>"asc", "cantidad"=>"desc");
	$this->query->key= array("N_Factura"=>"asc", "N_Linea"=>"desc");
	$this->query->selectStr = " * ";
	$this->query->fromStr = " prueba_record ";
	$this->query->whereStr = " N_Factura > 1 ";
	*/
	
	$this->query = new bas_sqlx_querydef();
	$this->query->add("prueba_record",'test');
	$this->query->setkey(array('N_Factura','N_Linea'));
		
	$this->query->addcol("N_Factura", "Numero de Factura","prueba_record" ,true,'test');	
	$this->query->addcol("N_Linea", "Numero de linea","prueba_record" ,true,'test');	
	$this->query->addcol("id_Cliente", "Cliente","prueba_record" ,false,'test');
	$this->query->addcol("cantidad", "cantidad","prueba_record" ,false,'test');
	$this->query->addcol("image", "Imagen!!!","prueba_record" ,false,'test',"image");
	$this->setAttr("N_Factura","lookup","prueba");
	
//	$this->query->addcol($id, $caption='', $table='',$pk='',$aliasof='',$type='');
	
	$this->query->order= array("id_Cliente"=>"asc", "cantidad"=>"desc");
	
	$this->query->addcondition("prueba_record.N_Factura > 1");
	
	
	
	//addComponent($tab, $x, $y, $width, $height, $field_id);
	$x=$y= $height = 1;
	$width = 2;
	
	$this->tabs = array("Primera","Segunda","Tercera","cuarta");
	
	
	$this->addComponent("Primera", 1, 2, $width, $height, "N_Factura");
	$this->addComponent("Segunda", 2, 2, $width, $height, "N_Linea");
	$this->addComponent("Segunda", 1, 2, $width, $height,"cantidad");
	$this->addComponent("Primera", 2, 1, $width, $height,"id_Cliente");
	$this->addComponent("Tercera", 1, 1, 3, 3,"image",false);
	$this->setRecord();

    }
}