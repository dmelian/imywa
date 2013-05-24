<?php
class test_frmx_listframe_form_medias extends bas_frmx_listframe{

    public function __construct($id,$title, $tabs='', $grid=array('width'=>4,'height'=>4)) {
	parent::__construct($id,$title);
//	$this->query = new bas_sqlx_querydef();
	$this->query->add("calcularConceptos_medias",'seguimiento');
	$this->query->setkey(array('local'));
		
	$this->query->addcol("local", "Recinto","calcularConceptos_medias" ,true,'seguimiento');	
	$this->query->addcol("concepto", "concepto","calcularConceptos_medias" ,true,'seguimiento');	
	$this->query->addcol("importe", "importe","calcularConceptos_medias" ,false,'seguimiento');
	
// 	$this->query->db = "seguimiento";
	$this->query->order= array("local"=>"asc");
	
	$x=$y= $height = 1;
	$width = 80;
	
// 	$this->setFixed(2);
	
	// field type:   ($id,$type,$name, $caption, $editable, $value,$visible)
	
	$this->addComponent($width, $height,"local");
	
	
// 	$this->query->addcol("$i","Expresion Periodo $i","valordetalle",false,"temp","textarea");		$this->setAttr("$i","selected",false);
// 	$this->addComponent(300,300,"$i");

	$this->createRecord();
	$this->setPivot("concepto","importe");
	
	$proc = new bas_sql_myextprocedure("seguimiento");
	if ($proc->success){ 
		$proc->call('calcularConceptos', array(null, null, null,null),"seguimiento"); 
		$this->Reload(false,$proc->connection);
		$proc->commit();
	}
	
    }	
    
}