<?php
class test_frmx_listframe_form_frame1 extends bas_frmx_listframe{

    public function __construct($id,$title, $tabs='', $grid=array('width'=>4,'height'=>4)) {
	parent::__construct($id,$title);
//	$this->query = new bas_sqlx_querydef();
	$this->query->add("prueba_record",'test');
	$this->query->setkey(array('N_Factura','N_Linea'));
		
	$this->query->addcol("N_Factura", "Numero de Factura","prueba_record" ,true,'test');	
	$this->query->addcol("N_Linea", "Numero de linea","prueba_record" ,true,'test');	
	$this->query->addcol("id_Cliente", "Cliente","prueba_record" ,false,'test');
	$this->query->addcol("cantidad", "cantidad","prueba_record" ,false,'test');
	
//	$this->query->addcol($id, $caption='', $table='',$pk='',$aliasof='',$type='');
	$this->query->db = "test";
	$this->query->order= array("id_Cliente"=>"asc", "cantidad"=>"desc");
	/*$this->query->key= array("N_Factura"=>"asc", "N_Linea"=>"desc");
	$this->query->selectStr = " * ";
	$this->query->fromStr = " prueba_record ";
	$this->query->whereStr = " N_Factura > 1 ";*/
	
	$this->query->addcondition("prueba_record.N_Factura > 1");	
	
	
	//addComponent($tab, $x, $y, $width, $height, $field);
	$x=$y= $height = 1;
	$width = 80;
	
	$this->setFixed(2);
	
	// field type:   ($id,$type,$name, $caption, $editable, $value,$visible)
	
	$this->addComponent($width, $height,"N_Factura");
	$this->addComponent($width, $height,"N_Linea");
	$this->addComponent($width, $height,"cantidad");
	$this->addComponent($width, $height,"id_Cliente");
	$this->setRecord();
//	$this->addComponent($width, $height,new bas_sqlx_fieldtext("fecha","text","fact","fecha",true,"val",false));
//	$this->addComponent($width, $height, new bas_sqlx_fieldtext("telefono","text","fact","telefono",false,"val",false));

    }	
}