<?php
class frm_doblekeylookup extends bas_frm_list{

	public function onLoad(){
		array_push($this->toolbar, 'aceptar', 'cancelar');
		$this->tbdef = new bas_sql_querydef('Doblekey');
		
		$this->tbdef->add('doblekey');
		$this->tbdef->addcol('k1');
		$this->tbdef->addcol('k2');
		$this->tbdef->addcol('data');
		
		$this->tbdef->setkey('k1,k2');
		$this->tbdef->setautokeyselect('k1,k2');
		
		
	}
	
	public function OnAction($action, $data){
		$ret = parent::OnAction($action,$data);
		if (isset($ret)) return $ret;
		
		switch ($action){
			
			case 'cancelar':
				return(array('close'));
				
			case 'aceptar':
				return(array('return', 'setvalues', $this->tbdef->getautokeyfilter()));
		}
	}
	
	
	
}
?>