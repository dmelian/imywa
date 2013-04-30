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
class frm_itemmodify{
	private $toolbar;
	private $datadef;
	private $data;
	private $item;
	private $success;
	private $errormsg;
	
	function OnLoad(){
		$this->toolbar = array('aceptar','cancelar');
		$this->datadef = new bas_dat_tabledef();
		$this->datadef->addcol('Item');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->addcol('');
		$this->datadef->settemplate('input', array(), '*');
	}

	public function OnPaint(){
		if ($this->success) {
			$frm = new bas_htm_dialog("ITEM: $this->item - {$this->data['nombre']}",'dialog',$this->toolbar);
			$card = new bas_htm_card($this->datadef, $this->data);
			$frm->add($card);
			$frm->printme();
		} else {
			$dlg = new bas_htm_dialog('Error', 'error', array('ok'), 'icon_dlgerror.png');
			$dlg->p($qry->errormsg);
			$dlg->printme();
		}
	}

	private function savedata($data){
		foreach (array_keys($this->data) as $id) $this->data[$id] = $data[$id];
	}
	
	private function loaddata(){
		$qry = new bas_sql_myquery("select item,..."
			. " from item where item = '$this->item'");
		$this->success = $qry->success;
		if ($qry->success) $this->data = $qry->result;
		else $this->errormsg = $qry->errormsg;
	}
	
	public function OnAction($action, $data){
		switch($action) {
			
			case 'aceptar':
				$this->savedata($data);
				$proc = new bas_sql_myprocedure('item_modify', array($this->item, $data['item'],$data[''],$data[''],$data[''],$data[''],$data[''],$data['']));
				if ($proc->success){ return array('return', 'setvalues', array('item' =>$proc->result['item'])); }
				else { return array('open', 'bas_dlg_msgbox', 'error', $proc->errormsg); }
				
			case 'cancelar': case 'ok':
				return(array('close'));
				
			case 'loadvalues':	case 'setvalues':
				$this->data = array_merge($this->data, $data);
				break;
				
			case 'lookup': //El usuario ha picado en el botón de lookup de alguno de los campos.
				$this->savedata($data);
				return (array('open',"frm_${data['lookup']}lookup"));
				
			case 'seek':
				$this->item = $data;
				$this->loaddata();
				break;
		}
	}
}
?>
