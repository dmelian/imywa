<?php
/*
	Copyright 2009-2012 Domingo Melian

	This file is part of imywa.

	imywa is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	imywa is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with imywa.  If not, see <http://www.gnu.org/licenses/>.
*/
class frm_itemnuevo{
	private $toolbar;
	private $datadef;
	private $data;
	
	function OnLoad(){
		$this->toolbar = array('aceptar','cancelar');
		$this->datadef = new bas_dat_tabledef();
		$this->datadef->addcol('item');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		
		$this->datadef->settemplate('input', array(), '*');
		$this->data = $this->datadef->getemptyrec();
	}

	public function OnPaint(){
		$frm = new bas_htm_dialog("NUEVO item",'dialog',$this->toolbar);
		$card = new bas_htm_card($this->datadef, $this->data);
		$frm->add($card);
		$frm->printme();
	}

	private function savedata($data){
		foreach (array_keys($this->data) as $id) $this->data[$id] = $data[$id];
	}
	
	
	public function OnAction($action, $data){
		switch($action) {
			
			case 'aceptar':
				$this->savedata($data);
				$proc = new bas_sql_myprocedure('item_insert', array($data['item'],$data[],$data[],$data[],$data[],$data[],$data[],$data[]));
				if ($proc->success){ return array('return', 'setvalues', array('item' =>$proc->result['item'])); }
				else { return array('open', 'bas_dlg_msgbox', 'error', $proc->errormsg); }
				
			case 'cancelar':
				return(array('close'));
				
			case 'lookup': //El usuario ha picado en el botón de lookup de alguno de los campos.
				$this->savedata($data);
				return (array('open',"frm_${data['lookup']}lookup"));
				
			case 'loadvalues':	case 'setvalues': //Se retorna del lookup y se cargan todas los datos anteriormente guardados.
				$this->data = array_merge($this->data, $data);
				break;
				
		}
	}
}
?>
