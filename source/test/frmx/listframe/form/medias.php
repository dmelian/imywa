<?php
class test_frmx_listframe_form_medias extends bas_frmx_listframe{
	
	public $listado= 1;
	public $desde= '2013-5-20';
	public $hasta= '2013-5-30';

    public function __construct($id,$title, $tabs='', $grid=array('width'=>4,'height'=>4)) {
    	global $_LOG;
		parent::__construct($id,$title);
	//	$this->query = new bas_sqlx_querydef();
		$this->query->add("movLocal",'seguimiento');
		$this->query->addcol("local", "Local","movLocal" ,true,'seguimiento');
			
		$this->query->addrelated('grupoConcepto','concepto','movLocal','seguimiento');	
		$this->query->addcol("grupo", "Concepto","conceptoGrupo" ,true, 'seguimiento');
		
	
		$this->query->addcol("valor", "Valor","movLocal" ,false,'seguimiento');
		$this->query->setAttColum('valor', 'expression', 'sum(movLocal.importe)*grupoConcepto.signo');
		
		$this->query->addrelated('columna', 'grupo', 'grupoConcepto', false, 'seguimiento');
		
		$this->query->addCondition("movLocal.fecha between '{$this->desde}' and '{$this->hasta}'", 'fecha'); //Ver las fechas iniciales y finales de la semana.
		$this->query->addCondition("columna.listado = {$this->listado}", 'listado');
		
		$this->query->setkey(array('local'));
		
	// 	$this->query->db = "seguimiento";
		$this->query->order= array("local"=>"asc",'columna.orden'=>'asc');
		$_LOG->log("medias.query = " . $this->query->query());
		
		$x=$y= $height = 1;
		$width = 80;
		
	// 	$this->setFixed(2);
		
		// field type:   ($id,$type,$name, $caption, $editable, $value,$visible)
		
		$this->addComponent($width, $height,"local");
		
		$qry = "select grupo from columna where listado = {$this->listado} order by orden";
		
		foreach(array(array("grupo"=>'GVTA'),array("grupo"=>'GECPA'),array("grupo"=>'GEOTRGTS'),array("grupo"=>'GRDO'))
				as $rec){
			$this->query->addcol($rec['grupo'],$rec['grupo'],"valordetalle",false,"temp","number");
			$this->setAttr($rec['grupo'],"selected",false);
			$this->addComponent($width,$height,$rec['grupo']);
			$pivotValues[]= $rec['grupo'];
		}
	
		$this->query->setGroup('local'); $this->query->setGroup('grupo');
		$_LOG->log("medias.grouped-query = " . $this->query->query());
		$this->createRecord();
		$this->setPivot("concepto","importe");
		//pivot values =$pivotValues;
		
		$proc = new bas_sql_myextprocedure("seguimiento");
		if ($proc->success){ 
			$proc->call('calcularConceptos', array(null, null, null,null),"seguimiento"); 
			$this->Reload(false,$proc->connection);
			$proc->commit();
		}
		
    }	
    
}