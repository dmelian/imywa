<?php
class frm_inicio extends bas_frm_card{
	
	public function onLoad(){
		array_push($this->toolbar, 'save', 'replace', 'salir');
		$this->tbdef = new bas_sql_querydef('Masteres');
		
		$this->tbdef->add('master');
		
		$this->tbdef->addcol('master'); $this->tbdef->select(false);
		$this->tbdef->addcol('fk1'); $this->tbdef->settemplate('input');
		
		$this->tbdef->addcol('fk2'); 
		$this->tbdef->setproperty('lookup','doblekey'); $this->tbdef->setproperty('lookupfields', array('k1'=>'fk1', 'k2'=>'fk2')); 
		$this->tbdef->settemplate('input');
		
		$this->tbdef->addcol('data'); $this->tbdef->settemplate('upload');
		$this->tbdef->addcol('uploadkey'); $this->tbdef->settemplate('input');		
		
		$this->tbdef->setkey('master');
		
		$this->initrecs($this->tbdef->getemptyrec());
		
		
	}
	
	public function onAction($action, $data){
		$ret = parent::OnAction($action,$data);
		if (isset($ret)) return $ret;
		switch ($action){
			
			case 'salir':
				return(array('close'));
				
			case 'save':
				$upload = new bas_dat_upload();
				if (!$upload->save('data')) {
					return array('open', 'bas_dlg_msgbox', 'error', $upload->errormsg);
				}
				break;
				
			case 'replace':
				$upload = new bas_dat_upload();
				if (!$upload->replace($data['uploadkey'] , 'data')){
					return array('open', 'bas_dlg_msgbox', 'error', $upload->errormsg);
				}
				break;
				
		}
		
	}
}
?>