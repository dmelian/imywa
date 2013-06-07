<?php
class frm_extension{
	private $actions = array('salir', 'subir', 'bajar');
	private $dataset;
	private $tabledef;
	private $extension;
	private $values=array('Domingo','Borito','Yoly','Daniel','Elena','Andrea','Juan','Pedro');
	private $id;
	
	function OnLoad(){
		$this->tabledef = new bas_dat_tabledef();
		$this->tabledef->addcol('Código');
		$this->tabledef->addcol('Nombre');
		
		$this->dataset = new bas_dat_arraydataset();
		$this->dataset->add(array('codigo'=>'1','nombre'=>'UNO'));
		$this->dataset->add(array('codigo'=>'2','nombre'=>'DOS'));
		$this->dataset->add(array('codigo'=>'3','nombre'=>'TRES'));
		$this->dataset->add(array('codigo'=>'4','nombre'=>'CUATRO'));
		$this->dataset->add(array('codigo'=>'5','nombre'=>'CINCO'));
		$this->dataset->add(array('codigo'=>'6','nombre'=>'SEIS'));
		$this->dataset->add(array('codigo'=>'7','nombre'=>'SIETE'));
		$this->dataset->add(array('codigo'=>'8','nombre'=>'OCHO'));
		$this->dataset->add(array('codigo'=>'9','nombre'=>'NUEVE'));
		$this->dataset->add(array('codigo'=>'10','nombre'=>'DIEZ'));
		$this->dataset->add(array('codigo'=>'11','nombre'=>'ONCE'));
		$this->dataset->add(array('codigo'=>'12','nombre'=>'DOCE'));
		
		$this->extension = new dat_extension1();
		
		
	}
	
	function OnPaint()
	{
		$frm = new bas_htm_form('EXTENSION TEST', $this->actions);
		$tb = new bas_htm_tableex($this->tabledef, $this->dataset);
		$tb->preextend($this->extension);
		$tb->postextend($this->extension);
		$frm->add($tb);
		$frm->printme();
	}
	
	function OnAction($action, $data){
		switch ($action){
			case 'salir': return array('close');
			case 'subir':
				$this->id++; 
				if($this->id >= count($this->values)) $this->id = count($this->values)-1;
				$this->extension->yo = $this->values[$this->id];
				break; 
			case 'bajar':
				$this->id--; 
				if($this->id < 0) $this->id = 0;
				$this->extension->yo = $this->values[$this->id];
				break; 
		}
	}
	
}
?>